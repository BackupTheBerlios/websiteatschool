<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker <peter@berestijn.nl>
#
# This program is free software: you can redistribute it and/or modify it under
# the terms of the GNU Affero General Public License version 3 as published by
# the Free Software Foundation supplemented with the Additional Terms, as set
# forth in the License Agreement for Website@School (see /program/license.html).
#
# This program is distributed in the hope that it will be useful, but
# WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
# FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License
# for more details.
#
# You should have received a copy of the License Agreement for Website@School
# along with this program. If not, see http://websiteatschool.eu/license.html

/** /program/modules/mailpage/mailpage_view.php - interface to the view-part of the mailpage module
 *
 * This file defines the interface with the mailpage-module for viewing content.
 * The interface consists of this function:
 *
 * <code>
 * mailpage_view(&$output,$area_id,$node_id,$module)
 * </code>
 *
 * This function is called from /index.php when the node to display is connected
 * to this module.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_mailpage
 * @version $Id: mailpage_view.php,v 1.6 2013/07/03 12:55:16 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

define('MAILPAGE_REFERENCE',sha1(__FILE__.':'.__LINE__));
require_once($CFG->progdir.'/lib/tokenlib.php');

/** display the content of the mailpage linked to node $node_id
 *
 * @param object &$theme collects the (html) output
 * @param int $area_id identifies the area where $node_id lives
 * @param int $node_id the node to which this module is connected
 * @param array $module the module record straight from the database
 * @return bool TRUE on success + output via $theme, FALSE otherwise
 */
function mailpage_view(&$theme,$area_id,$node_id,$module) {
    //
    // 0 -- basic sanity checks
    //
    if (($config = mailpage_view_get_config($node_id)) === FALSE) {
        $theme->add_message(t('error_retrieving_config','m_mailpage'));
        return FALSE;
    } elseif (sizeof($config['addresses']) <= 0) {
        logger(sprintf('%s(): no addresses at node %d: is mailpage unconfigured?',__FUNCTION__, $node_id));
        $msg = t('error_retrieving_addresses','m_mailpage',array('{NODE}' => strval($node_id)));
        $theme->add_message($msg);
        $theme->add_content($msg);
        return FALSE;
    }
    //
    // 1 -- do we have a token already?
    //
    $t0 = $t1 = 0;
    $ip_addr = '';
    $data = FALSE;
    $token_id = FALSE;
    if (isset($_POST['token'])) { // lookup valid UTF8 key (or fail with substitute U+FFFD instead)
        $token_key = (utf8_validate($_POST['token'])) ? magic_unquote($_POST['token']) : "\xEF\xBF\xBD";
        $token_id = token_lookup(MAILPAGE_REFERENCE,$token_key,$t0,$t1,$ip_addr,$data);
    }
    //
    // 2 -- handle cases of expired tokens and Cancel first
    //
    $now = time();
    if (($token_id !== FALSE) && (isset($_POST['button_cancel']))) { // visitor pressed [Cancel]
        $theme->add_message(t('cancelled','admin'));
        token_destroy($token_id);
        $token_id = FALSE;
    }
    if (($token_id !== FALSE) && ($t1 < $now)) { // token expired
	$theme->add_message(t('error_token_expired','m_mailpage'));
        token_destroy($token_id);
        $token_id = FALSE;
    }
    //
    // 3 -- handle the three remaining buttons from the two dialogs
    //
    if ($token_id !== FALSE) {
        if (isset($_POST['button_preview'])) {
            //
            // 3A -- Preview button
            //
            $dialogdef = mailpage_view_get_dialogdef($config, $token_key);
            if (!mailpage_view_dialog_validate($dialogdef)) {
                foreach($dialogdef as $k => $item) {
                    if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                        $theme->add_message($item['error_messages']);
                    }
                }
                mailpage_show_form($theme, $config, $dialogdef);
            } else {
                if (!token_store($token_id, $dialogdef)) {
                    $theme->add_message(t('error_storing_data','m_mailpage'));
                    logger(sprintf('%s(): token store error in page %d: %s',
                                   __FUNCTION__,$node_id,db_errormessage()));
                    return FALSE;
                }
                mailpage_show_preview($theme, $config, $dialogdef, $ip_addr);
            }
        } elseif (isset($_POST['button_edit'])) {
            //
            // 3B -- Edit button
            //
            if ($data === FALSE) {
                $theme->add_message(t('error_retrieving_data','m_mailpage'));
                logger(sprintf('%s(): no data after token_lookup()? (page=%d)',__FUNCTION__,$node_id));
                $data = mailpage_view_get_dialogdef($config, $token_key);
            }
            mailpage_show_form($theme, $config, $data);
        } elseif (isset($_POST['button_send'])) {
            //
            // 3C -- Send button
            //
            if ($data === FALSE) {
                $theme->add_message(t('error_retrieving_data','m_mailpage'));
                logger(sprintf('%s(): no data after token_lookup()? (page=%d)',__FUNCTION__,$node_id));
                $data = mailpage_view_get_dialogdef($config, $token_key);
            }
            if ($now < $t0) { // the window of opportunity is still closed; go back to form a la Edit
                $msg = t('error_too_fast','m_mailpage');
                $theme->add_message($msg);
                $theme->add_popup_top($msg);
                mailpage_show_form($theme, $config, $data);
                logger(sprintf('%s(): reply too fast (%ds) from %s',__FUNCTION__,$t0-$now,$ip_addr));
            } elseif (!mailpage_send_message($config, $data, $ip_addr, $now-$t0)) {
                $theme->add_message(t('error_sending_message'));
                mailpage_show_form($theme, $config, $data);
            } else {
                token_destroy($token_id);
                mailpage_show_thankyou($theme, $config, $data, $ip_addr);
            }
        } else {
            //
            // 3D -- catch all: initiate a new round (shouldn't happen)
            //
            token_destroy($token_id);
            $token_id = FALSE;
        }
    }
    //
    // 4 -- Start with a clean slate
    //
    if ($token_id === FALSE) {
        $token_key = '';
        if (($token_id = token_create(MAILPAGE_REFERENCE,$token_key,20)) === FALSE) { // 20s delay
            $msg = t('error_creating_token','m_mailpage',array('{NODE}' => strval($node_id)));
            $theme->add_message($msg);
            $theme->add_content($msg);
            return FALSE;
        }
        $dialogdef = mailpage_view_get_dialogdef($config, $token_key);
        mailpage_show_form($theme, $config, $dialogdef);
    }
    return TRUE;
} // mailpage_view()


/** retrieve all configuration data for this mailpage
 *
 * this retrieves all configuration data for this mailpage,
 * i.e. both the general parameters (header/intro/etc.) and
 * the full list of configured destination addresses.
 * Here is a quick reminder of the structure in $config.
 * <pre>
 * 'node_id'
 * 'header'
 * 'introduction'
 * 'message'
 * 'addresses' = ['mailpage_address_id','node_id','sort_order','name','email','description','thankyou'],...
 * </pre>
 *
 * @param int $node_id identifies the page
 * @return bool|array configuration data in a (nested) array or FALSE on error
 */
function mailpage_view_get_config($node_id) {
    // 1 -- generic configuration
    $table = 'mailpages';
    $fields = array('node_id','header','introduction', 'message');
    $where = array('node_id' => intval($node_id));
    if (($config = db_select_single_record($table, $fields, $where)) === FALSE) {
        logger(sprintf('%s(): error retrieving configuration: %s',__FUNCTION__,db_errormessage()));
        return FALSE;
    }
    // 2 -- fetch all configured destinations
    $table = 'mailpages_addresses';
    $fields = '*';
    $where = array('node_id' => intval($node_id));
    $order = array('sort_order','mailpage_address_id');
    if (($records = db_select_all_records($table,$fields,$where,$order)) === FALSE) {
        logger(sprintf('%s(): error retrieving addresses: %s',__FUNCTION__,db_errormessage()));
        return FALSE;
    }
    $config['addresses'] = $records; // simple 0-based array, not keyed by mailpage_address_id
    return $config;
} // mailpage_view_get_config()


/** construct a dialog definition for the visitor's mail form
 *
 * this defines the contact form. If there is but one destination
 * we don't even add the listbox because it makes no sense to tell
 * the user to select an option from a listbox with a single item.
 *
 * @param array $config mailpage configuration including addresses
 * @param string $token_key
 * @return array datadefinition
 */
function mailpage_view_get_dialogdef($config, $token_key) {
    $options = array();
    foreach($config['addresses'] as $index => $address) {
        $options[$index] = array(
            'option'     => $address['name'],
            'title'      => $address['description'],
            'address_id' => $address['mailpage_address_id']);
    }
    $dialogdef = array();
    if (1 < sizeof($options)) {
        $dialogdef['destination'] = array(
            'type' => F_LISTBOX,
            'name' => 'destination',
            'id' => 'mailpage_destination',
            'value' => 0,
            'options' => $options,
            'viewonly' => (sizeof($options) < 2) ? TRUE : FALSE,
            'label' => t('destination_label','m_mailpage'),
            'title' => (sizeof($options) < 2) ? $options[0]['title'] : t('destination_title','m_mailpage')
            );
    }
    $dialogdef = array_merge($dialogdef,array(
        'token' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'token',
	    'hidden' => TRUE,
            'value' => $token_key
            ),
        'fullname' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'fullname',
            'minlength' => 1,
            'maxlength' => 255,
            'columns' => 44,
            'label' => t('fullname_label','m_mailpage'),
            'title' => t('fullname_title','m_mailpage'),
            'value' => '',
            ),
        'email' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'email',
            'minlength' => 3,
            'maxlength' => 255,
            'columns' => 44,
            'label' => t('email_label','m_mailpage'),
            'title' => t('email_title','m_mailpage'),
            'value' => '',
            ),
        'subject' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'subject',
            'minlength' => 0,
            'maxlength' => 80,
            'columns' => 44,
            'label' => t('subject_label','m_mailpage'),
            'title' => t('subject_title','m_mailpage'),
            'value' => '',
            ),
        'message' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'message',
            'minlength' => 1,
            'maxlength' => 32768, // arbitrary; 32 kB
            'columns' => 50,
            'rows' => 10,
            'label' => t('message_label','m_mailpage'),
            'title' => t('message_title','m_mailpage'),
            'value' => $config['message']
            ),
        'button_preview' => dialog_buttondef('button_preview',t('button_preview','m_mailpage')),
        'button_cancel' => dialog_buttondef(BUTTON_CANCEL)
        )
    );
    return $dialogdef;
} // mailpage_view_get_dialogdef()


/** validate the data entered by the visitor
 *
 * Other than the standard validation we check for at least 1 @-sign in
 * the email address. (It is non-trivial to validate an email address,
 * it may not even be possible to do it in a single regular expression.
 * See http://stackoverflow.com/questions/201323 for more information.)
 *
 * @param object &$dialogdef defines the dialog and will hold POSTed values
 * @return bool TRUE if valid, else FALSE + messages added to dialogdef
 */
function mailpage_view_dialog_validate(&$dialogdef) {
    $retval = TRUE; // assume success
    // 1 -- check the usual suspects (side-effect: fetch POSTed values in dialogdef)
    if (!dialog_validate($dialogdef)) {
        $retval = FALSE;
    }

    // 2 -- special case: there must be at least 1 @-sign in the email field
    if (strpos($dialogdef['email']['value'],'@') === FALSE) { // no @-sign in mail address
        $retval = FALSE;
        ++$dialogdef['email']['errors'];
        $params = array('{FIELD}' => str_replace('~','',$dialogdef['email']['label']));
        $dialogdef['email']['error_messages'][] = t('validate_invalid','',$params);
    }
    return $retval;
} // mailpage_view_dialog_validate()


/** display the contact form
 *
 * this displays the contact form. Every destination gets a
 * separate DIV just below the listbox, with the additional
 * information for that destination. If JavaScript is NOT
 * enabled, all DIVs are displayed, otherwise only the
 * currently selected destination is displayed and the
 * others are not. IOW: this form is still usable even
 * without JS enabled AND it is screenreader-friendly.
 *
 * If there is only a single destination, the listbox is
 * not defined in the dialogdef and hence not rendered at all:
 * there is no point in showing a list of options
 * if there is nothing to choose from. This means that the
 * necessary javascript is NOT added and also no DIVs are
 * shown. So: a clean, uncluttered form in case of a single
 * destination.
 *
 * @param object &$theme collects the (html) output
 * @param array mailpage configuration data in a (nested) array
 * @param array $dialogdef array that defines the input fields
 * @return void output writted to $theme
 */
function mailpage_show_form(&$theme, $config, $dialogdef) {
    //
    // 1 -- maybe output a header and an introduction
    //
    $header = trim($config['header']);
    if (!empty($header)) {
        $theme->add_content(html_tag('h2','',$header));
    }
    $introduction = trim($config['introduction']);
    if (!empty($introduction)) {
        $theme->add_content($introduction);
    }
    $href = was_node_url($theme->node_record);
    //
    // 2 -- Render the dialog (maybe including the additional DIVs)
    //
    $postponed = array();
    $theme->add_content(html_form($href));
    foreach($dialogdef as $name => $item) {
        if (($item['type'] == F_SUBMIT) || ((isset($item['hidden'])) && ($item['hidden']))) {
            $postponed[$name] = $item;
        } else {
            $theme->add_content('<p>');
            $theme->add_content(dialog_get_label($item).'<br>');
            $widget = dialog_get_widget($item);
            if (is_array($widget)) {
                // add every radio button on a separate line
                $postfix = ($item['type'] == F_RADIO) ? '<br>' : '';
                foreach ($widget as $widget_line) {
                    $theme->add_content($widget_line.$postfix);
                }
            } else {
                $theme->add_content($widget);
            }
        }
        if ($name == 'destination') {
            foreach($item['options'] as $index => $option) {
                $theme->add_content(sprintf('<div class="%s" id="%s%d">%s: %s</div>',
					    'mailpage_destination_option',
					    'mailpage_destination_', $index,
                                            htmlspecialchars($option['option']),
                                            htmlspecialchars($option['title'])));
            }
            // This suppresses the DIVs that correspond to currently
            // not selected options in the listbox
            $js="<script><!--\n".
                "var sel=document.getElementById('mailpage_destination');\n".
                "sel.onchange=function() {\n".
                "  var div;\n".
                "  for(var i=0; i<this.length; ++i) {\n".
                "    div=document.getElementById('mailpage_destination_'+this.options[i].value);\n".
                "    div.style.display=(this.options[i].selected)?'block':'none';\n".
                "  }\n".
                "}\n".
                "sel.onchange();\n".
                "--></script>\n";
            $theme->add_content($js);
        }
    }
    $theme->add_content('<p>');
    foreach($postponed as $item) {
        $theme->add_content(dialog_get_widget($item));
    }
    $theme->add_content(html_form_close());
} // mailpage_show_form()


/** show a preview of the message to the visitor
 *
 * this shows a preview of the message to visitor.
 * Nothing is editable, it is view-only. The only option
 * is to either press the Send-button to actually send
 * the messate OR to press the Edit button to go back
 * to the editable form.
 *
 * Sending a message is a two-step procedure by design.
 *
 * @param object &$theme collects the (html) output
 * @param array mailpage configuration data in a (nested) array
 * @param array $dialogdef array that defines the input fields
 * @param string $ip_addr the originating IP-address
 * @return void output writted to $theme
 */
function mailpage_show_preview(&$theme, $config, $dialogdef, $ip_addr) {
    $destinations = 
    //
    // 1 -- prepare the information to show
    //
    $forbidden = array(chr(10),chr(13),chr(34),'\\');
    $mailfrom = sprintf('&quot;%s&quot; &lt;%s&gt;',
                        htmlspecialchars(str_replace($forbidden,'',trim($dialogdef['fullname']['value']))),
                        htmlspecialchars(str_replace($forbidden,'',trim($dialogdef['email']['value']))));
    $subject = htmlspecialchars(trim($dialogdef['subject']['value']));
    $message = nl2br(htmlspecialchars(trim($dialogdef['message']['value'])));
    $remote_addr = htmlspecialchars($ip_addr);
    //
    // 2 -- actually output the preview
    //
    $theme->add_content(html_tag('h2','',t('preview_header','m_mailpage')));
    $theme->add_content('<div>');
    $theme->add_content(sprintf('<strong>%s</strong>: %s<br>',t('from','m_mailpage'),$mailfrom));
    if (1 < sizeof($config['addresses'])) { // only show destination if there is more than 1
        $index = (isset($dialogdef['destination'])) ? $dialogdef['destination']['value'] : 0;
        $destination = $config['addresses'][$index]['name'];
        $sendto = htmlspecialchars('"'.str_replace($forbidden,'',trim($destination)).'"');
        $theme->add_content(sprintf('<strong>%s</strong>: %s<br>',t('to','m_mailpage'),$sendto));
    }
    $theme->add_content(sprintf('<strong>%s</strong>: %s<br>',t('subject','m_mailpage'),$subject));
    $theme->add_content(sprintf('<strong>%s</strong>: %s<br>',t('date','m_mailpage'),date('r')));
    $theme->add_content(sprintf('<strong>%s</strong>: %s<br>',t('ip_addr','m_mailpage'),$remote_addr));
    $theme->add_content(sprintf("<strong>%s</strong>:<br>\n%s<br>",t('message','m_mailpage'),$message));
    $theme->add_content('</div>');
    //
    // 3 -- finish with navigation for the visitor
    //
    $previewdef = array(
        'token' => $dialogdef['token'],
        'button_edit' => dialog_buttondef(BUTTON_EDIT),
        'button_send' => dialog_buttondef('button_send',t('button_send','m_mailpage')),
        'button_cancel' => dialog_buttondef(BUTTON_CANCEL)
        );
    $href = was_node_url($theme->node_record);
    $theme->add_content(dialog_quickform($href,$previewdef));
} // mailpage_show_preview()


/** actually send the visitor's message to the selected destination
 *
 * In order to get a feeling for the time a visitor needs, we also
 * record the delay (in seconds) next to the visitor's IP address.
 *
 * @param array mailpage configuration data in a (nested) array
 * @param array $dialogdef array that defines the data fields including values
 * @param string $ip_addr the originating IP-address
 * @param int $delay the # of seconds since time=t0
 * @return bool FALSE on error, TRUE on success + message sent
 * @todo extra validation of set_mailreplyto and set_subject?
 * @todo more available parameters in subject_line?
 * @todo make body of mail configuratble?
 */
function mailpage_send_message($config, $dialogdef, $ip_addr, $delay) {
    global $CFG;
    $mailfrom = sprintf('(%s) %s',trim($dialogdef['fullname']['value']),
                                  trim($dialogdef['email']['value']));
    $index = (isset($dialogdef['destination'])) ? $dialogdef['destination']['value'] : 0;
    $sendto = trim($config['addresses'][$index]['name']);
    $subject = trim($dialogdef['subject']['value']);
    $message = trim($dialogdef['message']['value']);
    $remote_addr = $ip_addr;
    $body = sprintf("%s: %s\n",t('from','m_mailpage'),$mailfrom).
            sprintf("%s: %s\n",t('to','m_mailpage'),$sendto).
            sprintf("%s: %s\n",t('subject','m_mailpage'),$subject).
            sprintf("%s: %s\n",t('date','m_mailpage'),date('r')).
            sprintf("%s: %s (%d)\n",t('ip_addr','m_mailpage'),$remote_addr,$delay).
            sprintf("%s:\n%s\n",t('message','m_mailpage'),$message);
    $email = $config['addresses'][$index]['email'];
    $name = $config['addresses'][$index]['name'];
    $params = array(
        '{NODE}' => strval($config['node_id']),
        '{SUBJECT}' => $subject,
        '{IP_ADDR}' => $remote_addr);
    $subject_line = t('subject_line','m_mailpage',$params);
    include_once($CFG->progdir.'/lib/email.class.php');
    $mailer = new Email;
    $mailer->set_mailto($email,$name);
    $mailer->set_mailreplyto(trim($dialogdef['email']['value']),trim($dialogdef['fullname']['value']));
    $mailer->set_subject($subject_line);
    $mailer->set_message($body);
    return $mailer->send();
} // mailpage_send_message()


/** thank the visitor for the message and show a text copy too
 *
 * Almost the same as {@see mailpage_show_preview()}.
 *
 * @param object &$theme collects the (html) output
 * @param array mailpage configuration data in a (nested) array
 * @param array $dialogdef array that defines the input fields
 * @param string $ip_addr the originating IP-address
 * @return void output writted to $theme
 * @todo should we have an OK button at all???
 */
function mailpage_show_thankyou(&$theme, $config, $dialogdef, $ip_addr) {
    //
    // 1 -- prepare the information to show
    //
    $forbidden = array(chr(10),chr(13),chr(34),'\\');
    $mailfrom = sprintf('&quot;%s&quot; &lt;%s&gt;',
                        htmlspecialchars(str_replace($forbidden,'',trim($dialogdef['fullname']['value']))),
                        htmlspecialchars(str_replace($forbidden,'',trim($dialogdef['email']['value']))));

    $index = (isset($dialogdef['destination'])) ? $dialogdef['destination']['value'] : 0;
    $subject = htmlspecialchars(trim($dialogdef['subject']['value']));
    $message = nl2br(htmlspecialchars(trim($dialogdef['message']['value'])));
    $remote_addr = htmlspecialchars($ip_addr);
    $thankyou = trim($config['addresses'][$index]['thankyou']);
    //
    // 2 -- actually output the text
    //
    $theme->add_content(html_tag('h2','',t('thankyou_header','m_mailpage')));
    if (!empty($thankyou)) {
        $theme->add_content(html_tag('p','',$thankyou));
    }
    $theme->add_content(html_tag('p','',t('here_is_a_copy','m_mailpage')));
    $theme->add_content('<div>');
    $theme->add_content(sprintf('<strong>%s</strong>: %s<br>',t('from','m_mailpage'),$mailfrom));
    if (1 < sizeof($config['addresses'])) { // only show destination if there is more than 1
        $destination = $config['addresses'][$index]['name'];
        $sendto = htmlspecialchars('"'.str_replace($forbidden,'',trim($destination)).'"');
        $theme->add_content(sprintf('<strong>%s</strong>: %s<br>',t('to','m_mailpage'),$sendto));
    }
    $theme->add_content(sprintf('<strong>%s</strong>: %s<br>',t('subject','m_mailpage'),$subject));
    $theme->add_content(sprintf('<strong>%s</strong>: %s<br>',t('date','m_mailpage'),date('r')));
    $theme->add_content(sprintf('<strong>%s</strong>: %s<br>',t('ip_addr','m_mailpage'),$remote_addr));
    $theme->add_content(sprintf("<strong>%s</strong>:<br>\n%s<br>",t('message','m_mailpage'),$message));
    $theme->add_content('</div>');
} // mailpage_show_thankyou()

?>