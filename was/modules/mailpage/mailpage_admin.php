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

/** /program/modules/mailpage/mailpage_admin.php - management interface for mailpage-module
 *
 * This file defines the administrative interface to this module.
 * The interface consists of the following four functions.
 *
 * <code>
 * mailpage_disconnect(&$output,$area_id,$node_id,$module)
 * mailpage_connect(&$output,$area_id,$node_id,$module)
 * mailpage_show_edit(&$output,$area_id,$node_id,$module,$viewonly,$edit_again,$href)
 * mailpage_save(&$output,$area_id,$node_id,$module,$viewonly,&$edit_again)
 * </code>
 *
 * These functions are called from pagemanagerlib.php whenever necessary.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_mailpage
 * @version $Id: mailpage_admin.php,v 1.1 2013/06/20 14:41:33 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** disconnect this module from a node
 *
 * this breaks the link between the node $node_id in area $area_id and
 * this module. For now we simply delete the records in 'mailpages' and
 * 'mailpages_addresses' linked to node node_id.
 * 
 * @param object &$output collects the html output (if any)
 * @param int $area_id the area in which $node_id resides
 * @param int $node_id the node from which we need to disconnect
 * @param array $module the module record straight from the database
 * @return bool TRUE on success, FALSE otherwise
 */
function mailpage_disconnect(&$output,$area_id,$node_id,$module) {
    $where = array('node_id' => intval($node_id));
    $retval = db_delete('mailpages',$where);
    if (db_delete('mailpages_addresses',$where) === FALSE) {
        $retval = FALSE;
    }
    return ($retval === FALSE) ? FALSE : TRUE;
} // mailpage_disconnect()


/** connect this module to a node
 *
 * this makes the link between the node $node_id in area $area_id and this module.
 * In this case we create a single 'mailpages' record linked to node_id.
 * Any addresses can be added later as desired and records in 'mailpages_addresses'
 * will be created as necessary.
 *
 * @param object &$output collects the html output (if any)
 * @param int $area_id the area in which $node_id resides
 * @param int $node_id the node to which we need to connect
 * @param array $module the module record straight from the database
 * @return bool TRUE on success, FALSE otherwise
 */
function mailpage_connect(&$output,$area_id,$node_id,$module) {
    global $USER;
    $now = strftime('%Y-%m-%d %T');
    $fields = array(
        'node_id' => intval($node_id),
        'header' => '',
        'introduction' => '',
        'ctime' => $now,
        'cuser_id' => $USER->user_id,
        'mtime' => $now,
        'muser_id' => $USER->user_id);
    $retval = db_insert_into('mailpages',$fields);
    if ($retval !== 1) {
        logger(sprintf('%s(): cannot connect to node \'%d\': %s',__FUNCTION__,$node_id,db_errormessage()));
        $retval = FALSE;
    } else {
        $retval = TRUE;
    }
    return $retval;
} // mailpage_connect()


/** present the user with a dialog to modify the mailpage that is connected to node $node_id
 *
 * this prepares a dialog for the user filled with existing data (if any), possibly allowing
 * the user to modify the content. If the flag $viewonly is TRUE, this routine should only
 * display the content rather than let the user edit it. If the flag $edit_again is TRUE,
 * the routine should use the data available in the $_POST array, otherwise it should read
 * the data from the database (or wherever the data comes from). The parameter $href is the
 * place where the form should be POST'ed.
 *
 * The dialog should be added to the $output object. Useful routines are:
 * <code>
 * $output->add_content($content): add $content to the content area
 * $output->add_message($message): add $message to the message area (feedback to the user)
 * $output->add_popup_bottom($message): make $message popup in the browser after loading the page (uses javascript)
 * $output->add_popup_top($message): make $message popup in the browser before loading the page (uses javascript)
 * </code>
 * 
 * @param object &$output collects the html output (if any)
 * @param int $area_id the area in which $node_id resides
 * @param int $node_id the node to which this module is connected
 * @param array $module the module record straight from the database
 * @param bool $viewonly if TRUE, editing is not allowed (but simply showing the content is allowed)
 * @param bool $edit_again if TRUE start with data from $_POST, else use data from database
 * @param string $href the action property of the HTML-form, the place where data will be POST'ed
 * @return bool TRUE on success + output stored via $output, FALSE otherwise
 */
function mailpage_show_edit(&$output,$area_id,$node_id,$module,$viewonly,$edit_again,$href) {
    $dialogdef = mailpage_get_dialogdef($output,$viewonly,$node_id);
    if ($edit_again) { // retrieve and validate the POSTed values
        mailpage_dialog_validate($dialogdef); // don't show messages; we did that in mailpage_save() below
    }
    $output->add_content('<h2>'.t('mailpage_content_header','m_mailpage').'</h2>');
    $output->add_content(t('mailpage_content_explanation','m_mailpage'));
    $output->add_content(dialog_quickform($href,$dialogdef));
    return TRUE;
} // mailpage_show_edit()


/** save the modified content data of this module linked to node $node_id
 *
 * this validates and saves the data that was submitted by the user.
 * If validation fails, or storing the data doesn't work, the flag $edit_again
 * is set to TRUE and the return value is FALSE.
 *
 * If the user has cancelled the operation, the flag $edit_again is set to FALSE
 * and the return value is also FALSE.
 *
 * If the modified data is stored successfully, the return value is TRUE (and
 * the value of $edit_again is a don't care).
 *
 * Here is a summary of return values.
 *
 *  - retval = TRUE ==> data saved successfully
 *  - retval = FALSE && edit_again = TRUE ==> re-edit the data, show the edit dialog again
 *  - retval = FALSE && edit_again = FALSE ==> cancelled, do nothing
 *
 * @param object &$output collects the html output (if any)
 * @param int $area_id the area in which $node_id resides
 * @param int $node_id the node to which the content is connected
 * @param array $module the module record straight from the database
 * @param bool $viewonly if TRUE, editing and hence saving is not allowed
 * @param bool &$edit_again set to TRUE if we need to edit the content again, FALSE otherwise
 * @return bool TRUE on success + output stored via $output, FALSE otherwise
 */
function mailpage_save(&$output,$area_id,$node_id,$module,$viewonly,&$edit_again) {
    global $USER;

    $edit_again = FALSE; // assume we do NOT need to edit again
    $node_id = intval($node_id);

    // 1 -- bail out if cancelled or viewonly
    if ((isset($_POST['button_cancel'])) || ($viewonly)) {
        return FALSE;
    }

    // 2 -- redo if invalid data was submitted
    $dialogdef = mailpage_get_dialogdef($output,$viewonly,$node_id);
    if (!mailpage_dialog_validate($dialogdef)) {
        // there were errors, show them to the user and do it again
        foreach($dialogdef as $k => $item) {
            if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                $output->add_message($item['error_messages']);
            }
        }
        $edit_again = TRUE;
        return FALSE;
    }

    // 3 -- actually save the settings
    $retval = TRUE; // assume success
    $now = strftime('%Y-%m-%d %T');
    // 3A -- main config
    $table = 'mailpages';
    $fields = array('header'   => trim($dialogdef['header']['value']),
                    'introduction' => trim($dialogdef['introduction']['value']),
                    'message' => trim($dialogdef['message']['value']),
                    'mtime' => $now,
                    'muser_id' => $USER->user_id);
    $where = array('node_id' => $node_id);
    $retval = db_update($table,$fields,$where);
    if (db_update($table,$fields,$where) === FALSE) {
        logger(sprintf('%s(): error saving config values: %s',__FUNCTION__,db_errormessage()));
        $edit_again = TRUE;
        $retval = FALSE;
    }
    // 3B -- step through all destinations
    $table = 'mailpages_addresses';
    for ($i=1; $i <= $dialogdef['names']['old_value']; ++$i) {
        $pkey = (isset($dialogdef['name'.$i]['pkey'])) ? $dialogdef['name'.$i]['pkey'] : 0;
        $where = array('mailpage_address_id' => $pkey);
        $name = trim($dialogdef['name'.$i]['value']);
        if ((empty($name)) && ($pkey > 0)) { // empty name in existing record: DELETE
            if (db_delete($table,$where) === FALSE) {
                logger(sprintf('%s(): error deleting address: %s',__FUNCTION__,db_errormessage()));
                $edit_again = TRUE;
                $retval = FALSE;
            }
        } elseif (!empty($name)) {
            $fields = array(
                'node_id' => $node_id,
                'sort_order' => intval($dialogdef['sort_order'.$i]['value']),
                'name' => trim($dialogdef['name'.$i]['value']),
                'email' => trim($dialogdef['email'.$i]['value']),
                'description' => trim($dialogdef['description'.$i]['value']),
                'thankyou' => trim($dialogdef['thankyou'.$i]['value'])
                );
            if ($pkey > 0) { // non-empty name in existing record: UPDATE
                if (db_update($table,$fields,$where) === FALSE) {
                    logger(sprintf('%s(): error updating address: %s',__FUNCTION__,db_errormessage()));
                    $edit_again = TRUE;
                    $retval = FALSE;
                }
            } else { // non-empty name in new record: INSERT
                if (db_insert_into($table,$fields) === FALSE) {
                    logger(sprintf('%s(): error adding address: %s',__FUNCTION__,db_errormessage()));
                    $edit_again = TRUE;
                    $retval = FALSE;
                }
            }
        }
    }
    if (!$retval) {
        $output->add_message(t('error_saving_data','m_mailpage'));
    }
    return $retval;
} // mailpage_save()


/** construct a dialog definition for the mailpage configuration
 *
 * @param object &$output collects the html output (if any)
 * @param int $viewonly if TRUE the Save button is not displayed and config values cannot be changed
 * @param int $node_id identifies the current mailpage
 * @return array dialog definition
 */
function mailpage_get_dialogdef(&$output, $viewonly, $node_id) {

    // 1 -- generic configuration parameters
    $table = 'mailpages';
    $fields = array('header','introduction', 'message');
    $where = array('node_id' => intval($node_id));
    if (($record = db_select_single_record($table, $fields, $where)) === FALSE) {
        logger(sprintf('%s(): error retrieving configuration: %s',__FUNCTION__,db_errormessage()));
        $output->add_message(t('error_retrieving_data','admin'));
        $header = '';
        $introduction = '';
        $message = '';
    } else {
        $header = $record['header'];
        $introduction = $record['introduction'];
        $message = $record['message'];
    }
    $dialogdef = array(
        'header' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'header',
            'minlength' => 0,
            'maxlength' => 240,
            'columns' => 30,
            'label' => t('header_label','m_mailpage'),
            'title' => t('header_title','m_mailpage'),
            'viewonly' => $viewonly,
            'value' => $header,
            'old_value' => $header,
            ),
        'introduction' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'introduction',
            'minlength' => 0,
            'maxlength' => 32768, // arbitrary; 32 kB
            'columns' => 50,
            'rows' => 10,
            'label' => t('introduction_label','m_mailpage'),
            'title' => t('introduction_title','m_mailpage'),
            'viewonly' => $viewonly,
            'value' => $introduction,
            'old_value' => $introduction
            )
        );

    // 2 -- step through existing destination addresses + add at least 1 extra
    $table = 'mailpages_addresses';
    $keyfield = 'mailpage_address_id';
    $fields = '*';
    $where = array('node_id' => intval($node_id));
    $order = array('sort_order',$keyfield);
    $i = 0; // counts # of names and doubles as the index
    if (($records = db_select_all_records($table,$fields,$where,$order,$keyfield)) === FALSE) {
        logger(sprintf('%s(): error retrieving addresses: %s',__FUNCTION__,db_errormessage()));
        $output->add_message(t('error_retrieving_data','admin'));
    } else {
        // always add an empty destination address at the bottom
        $records[0] = array(
            'name' => '',
            'sort_order' => 0,
            'email' => '',
            'description' => '',
            'thankyou' => '',
            'message' => ''
             );
        foreach($records as $id => $record) {
            $params = array('{INDEX}' => strval(++$i));
            $dialogdef['name'.$i] = array(
                'type' => F_ALPHANUMERIC,
                'name' => 'name'.$i,
                'minlength' => 0,
                'maxlength' => 80,
                'columns' => 30,
                'label' => t('name_label','m_mailpage',$params),
                'title' => t('name_title','m_mailpage',$params),
                'viewonly' => $viewonly,
                'value' => $record['name'],
                'old_value' => $record['name'],
                'pkey' => intval($id)
                );
            $dialogdef['email'.$i] = array(
                'type' => F_ALPHANUMERIC,
                'name' => 'email'.$i,
                'minlength' => 0,
                'maxlength' => 255,
                'columns' => 30,
                'label' => t('email_label','m_mailpage',$params),
                'title' => t('email_title','m_mailpage',$params),
                'viewonly' => $viewonly,
                'value' => $record['email'],
                'old_value' => $record['email']
                );
            $dialogdef['description'.$i] = array(
                'type' => F_ALPHANUMERIC,
                'name' => 'description'.$i,
                'minlength' => 0,
                'maxlength' => 240,
                'columns' => 30,
                'label' => t('description_label','m_mailpage',$params),
                'title' => t('description_title','m_mailpage',$params),
                'viewonly' => $viewonly,
                'value' => $record['description'],
                'old_value' => $record['description']
                );
            $dialogdef['thankyou'.$i] = array(
                'type' => F_ALPHANUMERIC,
                'name' => 'thankyou'.$i,
                'minlength' => 0,
                'maxlength' => 240,
                'columns' => 30,
                'label' => t('thankyou_label','m_mailpage',$params),
                'title' => t('thankyou_title','m_mailpage',$params),
                'viewonly' => $viewonly,
                'value' => $record['thankyou'],
                'old_value' => $record['thankyou']
                );
            $dialogdef['sort_order'.$i] = array(
                'type' => F_INTEGER,
                'name' => 'sort_order'.$i,
                'minlength' => 0,
                'maxlength' => 10,
                'columns' => 5,
                'minvalue' => 0,
                'label' => t('sort_order_label','m_mailpage',$params),
                'title' => t('sort_order_title','m_mailpage',$params),
                'viewonly' => $viewonly,
                'value' => 10 * $i, // automatically renumber all destinations
                'old_value' => $record['sort_order'],
                );
        } // foreach
    } // else
    $dialogdef['names'] = array(
        'type' => F_INTEGER,
        'name' => 'names',
        'hidden' => TRUE,
        'value' => $i,
        'old_value' => $i // this is the actual value to use; the POSTed $value cannot be trusted
        );
    $dialogdef['message'] = array(
        'type' => F_ALPHANUMERIC,
        'name' => 'message',
        'minlength' => 0,
        'maxlength' => 32768, // arbitrary; 32 kB
        'columns' => 50,
        'rows' => 10,
        'label' => t('message_label','m_mailpage'),
        'title' => t('message_title','m_mailpage'),
        'viewonly' => $viewonly,
        'value' => $message,
        'old_value' => $message
        );

    // 3 -- maybe add a save button
    if (!$viewonly) {
        $dialogdef['button_save'] = dialog_buttondef(BUTTON_SAVE);
    }
    $dialogdef['button_cancel'] = dialog_buttondef(BUTTON_CANCEL);
    return $dialogdef;
} // mailpage_get_dialogdef()


/** validate the data entered by the user
 *
 * Other than the standard validation we check for at least 1 non-empty name
 *
 * @param object &$dialogdef defines the dialog and will hold POSTed values
 * @return bool TRUE if valid, else FALSE + messages added to dialogdef
 */
function mailpage_dialog_validate(&$dialogdef) {
    $retval = TRUE; // assume success
    // 1 -- check the usual suspects (side-effect: fetch POSTed values in dialogdef)
    if (!dialog_validate($dialogdef)) {
        $retval = FALSE;
    }

    // 2 -- special case: there must be at least 1 destination
    $n = 0;
    for ($i=1; $i <= $dialogdef['names']['old_value']; ++$i) {
        $name = 'name'.$i;
        if (isset($dialogdef[$name]['value'])) {
            $value = trim($dialogdef[$name]['value']);
            if (!empty($value)) {
                ++$n;
            }
        }
    }
    if ($n <= 0) {
        $retval = FALSE;
        ++$dialogdef['name1']['errors'];
        $params = array('{FIELD}' => str_replace('~','',$dialogdef['name1']['label']),'{MIN}' => 1);
        $dialogdef['name1']['error_messages'][] = t('validate_too_short','',$params);
    }
    return $retval;
} // mailpage_dialog_validate()


?>