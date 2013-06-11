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

/** /program/modules/crew/crew_view.php - interface to the view-part of the crew module
 *
 * This file defines the interface with the crew-module for viewing content.
 * The interface consists of this function:
 *
 * <code>
 * crew_view(&$output,$area_id,$node_id,$module)
 * </code>
 *
 * This function is called from /index.php when the node to display is connected
 * to this module.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_crew
 * @version $Id: crew_view.php,v 1.4 2013/06/11 11:25:18 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

define('CREW_MAX_DOCUMENT_SIZE',65536); // arbitrary limit of about 64 kB

/** display the content of the workshop linked to node $node_id
 *
 *
 * @param object &$theme collects the (html) output
 * @param int $area_id identifies the area where $node_id lives
 * @param int $node_id the node to which this module is connected
 * @param array $module the module record straight from the database
 * @return bool TRUE on success + output via $theme, FALSE otherwise
 * @todo FixMe: we need to take parent node permissions into account
 *       as soon as we can assign crew permissions to sections.
 *       We now specificly look at permissions in table acls_modules_nodes
 *       OR at global (guru) permissions for modules in table acls. (June 2013).
 */
function crew_view(&$theme,$area_id,$node_id,$module) {
    global $USER;
    //
    // 1 -- initialise: check out the visibility of this page
    //
    $table = 'workshops';
    $fields = array('visibility');
    $where = array('node_id' => intval($node_id));
    $record = db_select_single_record($table,$fields,$where);
    if ($record === FALSE) {
        logger(sprintf('%s(): error retrieving configuration: %s',__FUNCTION__,db_errormessage()));
        $visibility = 0;
    } else {
        $visibility = intval($record['visibility']);
    }
    //
    // 2 -- compute permissions for the current user: 1=READ, 2=WRITE
    //
    $module_id = intval($module['module_id']);
    if (!(($visibility == 2) || // world
         (($visibility == 1) && ($USER->is_logged_in)) || // authenticated users only
         (($visibility == 0) && ($USER->has_module_node_permissions(1,$module_id,$area_id,$node_id))))) {
        $theme->add_content(t('crew_view_access_denied','m_crew'));
        return TRUE;
    }
    // OK. Reading is permitted. How about writing?
    $writer = $USER->has_module_node_permissions(2,$module_id,$area_id,$node_id);

    //
    // 3 -- what do we need to do here?
    //

    // If we are still here, we have at least read access.
    // That means that we always can show the current version of the document
    // via crew_view_show_view(). Anything else requires the $writer permissions.
    // If, for some reason, someone attempts to save the document without
    // having write permissions, we simply can/will discard the POST'ed
    // document and pretend that the visitor just wants to read the current
    // version of the document.
    //
    // Note that we have a total of 5 variations here.
    // 1. Initial visit: a simple GET for the current version of the page (for starters)
    // 2. The user pressed [Edit] from 1. and now sees the popup JS Workshop
    // 3. The user submitted via [Save] which saves the document and shows 1. again
    // 4. The user submitted via [SaveEdit] which saves the document and shows 2. again without popup
    // 5. The user submitted via [Cancel] and now sees 1. again.
    // Here we go. 

    if ($writer) {
        if (isset($_POST['button_edit'])) {
            $retval = crew_view_show_edit($theme,$module_id,TRUE); // TRUE means 1st time; generate pop-up
        } elseif (isset($_POST['button_saveedit'])) {
            crew_view_save_document($theme,$node_id); 
            $retval = crew_view_show_edit($theme,$module_id,FALSE); // FALSE means do not generate pop-up
        } else {
            if (isset($_POST['button_save'])) {
                crew_view_save_document($theme,$node_id);
            } elseif (isset($_POST['button_cancel'])) {
                $theme->add_message(t('cancelled','admin'));
            }
            $retval = crew_view_show_view($theme,$writer,$node_id);
        }
    } else {
        $retval = crew_view_show_view($theme,$writer,$node_id);
    }
    return $retval;
} // crew_view()


/** display the current version of the document and maybe an Edit button
 *
 * this fetches a fresh version of the document from the database
 * and displays it, with the header and the introduction (if any).
 *
 * If the writer flag is TRUE, we also display the date/time/user 
 * of last update to the document plus we generate an Edit button
 * which allows this user to actually edit the document via CREW.
 *
 * Note
 * Since CREW requires JavaScript, we use a trick: we initially
 * hide the Edit button (and the Skin listbox) by setting the
 * display-parameter of the surrounding DIV to none. Subsequently
 * we set that parameter to 'block' using JavaScript. That means
 * if JaveScript is not enabled, the user will not see the DIV
 * and hence the button. If JS is enabled there is no problem.
 * As an added bonus we also have a single line of text warning
 * the user about JavaScript via a NOSCTIPT-tag. The only thing
 * that can happen now is that the browser does not support the
 * Websocket protocl. This is dealt with in the actual CREW code.
 *
 * @param object &$theme collects the (html) output
 * @param bool $writer if TRUE we add an Edit button to the display
 * @param int $node_id key to the page we're generating
 * @return bool TRUE on success+output generated via $theme, FALSE otherwise
 */
function crew_view_show_view(&$theme,$writer,$node_id) {
    global $CFG;
    $theme->add_stylesheet($CFG->progwww_short.'/modules/crew/crew_view.css');
    // 1 -- get the necessary information we need from database
    if (($record = crew_view_get_workshop_data($node_id)) === FALSE) {
        $theme->add_message(t('error_retrieving_workshop_data','m_crew'));
        return FALSE;
    }

    // 2 -- massage and display the data
    $header = trim($record['header']);
    $introduction = trim($record['introduction']);
    if (!empty($header)) {
        $attr = array('class' => 'crew_header');
        $theme->add_content(html_tag('h2',$attr,$header));
    }
    if (!empty($introduction)) {
        $attr = array('class' => 'crew_introduction');
        $theme->add_content(html_tag('div',$attr,$introduction));
    }
    $attr = array('class' => 'crew_document');
    $document = nl2br(htmlspecialchars($record['document']));
    $theme->add_content(html_tag('div',$attr,$document));

    // 3A -- maybe display the document time stamp (only if user qualifies as a writer)
    if ($writer) {
        $params = array(
            '{USERNAME}' => (is_null($record['username'])) ? $record['muser_id'] : $record['username'],
            '{FULL_NAME}' => (is_null($record['full_name'])) ? $record['muser_id'] : $record['full_name'],
            '{DATIM}' => $record['mtime']
            );
        $attr = array('class' => 'crew_datim');
        $theme->add_content(html_tag('div',$attr,t('last_updated_by','m_crew',$params)));
    }

    // 3B -- maybe display an Edit button (only if user qualifies)
    if ($writer) { // only writer get to see the Edit button (if Javascript AND Websockets available)
        $theme->add_content(html_tag('noscript','',t('crew_requires_js_and_ws','m_crew')));
        $attr = array('id' => 'crew_start_edit', 'style' => 'display: none;');
        $theme->add_content(html_tag('div',$attr));
        $dialogdef = crew_view_dialogdef();
        $href = was_node_url($theme->node_record);
        $theme->add_content(dialog_quickform($href,$dialogdef));
        $theme->add_content(html_tag_close('div'));
        $js = "document.getElementById('crew_start_edit').style.display='block';";
        $theme->add_content(html_tag('script','',$js));
    }
} // crew_view_show_view()


/** construct an option to select a skin and start an Edit session
 *
 * this defines a dialog where the user can pick a skin from a list
 * of existing skins and subsequently press [Edit] to actually edit
 * the document.
 *
 * @return array dialogdefinition
 */
function crew_view_dialogdef() {
    $options = array(
        '0' => array(
            'option' => t('skin_standard_option','m_crew'),
            'title' => t('skin_standard_title','m_crew'),
            'css' => 'crew.css'),
        '1' => array(
            'option' => t('skin_bw_option','m_crew'),
            'title' => t('skin_bw_title','m_crew'),
            'css' => 'crew_bw.css'),
        '2' => array(
            'option' => t('skin_rb_option','m_crew'),
            'title' => t('skin_rb_title','m_crew'),
            'css' => 'crew_rb.css'),
        '3' => array(
            'option' => t('skin_by_option','m_crew'),
            'title' => t('skin_by_title','m_crew'),
            'css' => 'crew_by.css')
        );
    $dialogdef = array(
        'skin' => array(
            'type' => F_LISTBOX,
            'name' => 'skin',
            'value' => '0',
            'options' => $options,
            'title' => t('skin_title','m_crew'),
            'label' => t('skin_label','m_crew')
            ),
        'button_edit' => dialog_buttondef(BUTTON_EDIT)
        );
    return $dialogdef;
} // crew_view_dialogdef()


/** save the POST'ed version of the document to the workshop database
 *
 * this saves the document after validating it. The maximum length
 * is arbitrary but it should be enough for a 'normal' document.
 *
 * @param object &$theme collects the (html) output
 * @param int $node_id key to the page/workshop we are working in
 * @return bool TRUE on success+data saved, FALSE otherwise
 */
function crew_view_save_document(&$theme, $node_id) {
    global $USER;
    $dialogdef = array(
        'text' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'text',
            'rows' => 10,
            'columns' => 80,
            'value' => '',
            'maxlength' => CREW_MAX_DOCUMENT_SIZE // arbitrary limit on text length
            )
         );
    if (!dialog_validate($dialogdef)) {
	foreach($dialogdef['text']['error_messages'] as $msg) {
            $theme->add_message($msg);
        }
        $theme->add_message(t('error_saving_workshop_data','m_crew'));
        return FALSE;
    }
    $now = strftime('%Y-%m-%d %T');
    $table = 'workshops';
    $fields = array(
        'document' => $dialogdef['text']['value'],
        'mtime' => $now,
        'muser_id' => $USER->user_id);
    $where = array('node_id' => intval($node_id));
    if (($retval = db_update($table,$fields,$where)) === FALSE) {
        logger(sprintf('%s(): workshop %d data error: %s',__FUNCTION__,$node_id,db_errormessage()));
        $theme->add_message(t('error_saving_workshop_data','m_crew'));
    } else {
        $params = array(
            '{USERNAME}' => $USER->username,
            '{FULL_NAME}' => $USER->full_name,
            '{DATIM}' => $now
        );
        $theme->add_message(t('last_updated_by','m_crew',$params));
        $retval = TRUE;
    }
    return $retval;
} // crew_view_save_document()

/** show the (visually almost) empty page and load or continue with the JS popup window
 *
 * this routine is responsible for showing an 'empty' page and maybe for generating
 * a JS popup window (if $first==TRUE). The 'empty' page contains only a form with
 * single textarea. However, this textarea is not displayed (display:none) so the
 * casual user sees nothing (but obviously without CSS it is a different matter).
 * This textarea is used by the CREW code to store the edited document before
 * submitting the form. Since there are no buttons of any kind, it is completely
 * up to the JS code to generate the necessary DOM elements that are required to
 * successfully save the document.
 *
 * If $first is TRUE, we have to setup the popup window. This is quite complicated
 * because generate the necessary JS-code at runtime using JS. One of the reasons
 * is that I want to set the correct translations in the popup window. There may
 * be an easier way.
 *
 * The Websocket protocol is used to talk to the Websocket server which is configured
 * for this site. This setting can be manipulated using the Module Manager. In order
 * to authenticate ourselves against the websocket server we use the following mechanism.
 * There are a few important variables used in authenticating:
 *
 *  - $origin: this is the website's hostname as seen by the user's browser
 *  - $request_uri: a string that uniquely identifies the node within the origin
 *  - $full_name: the full name of the current user (ie. $USER->full_name) 
 *  - $username: the (short) name/userid of the curent user (ie. $USER->username)
 *  - $request_date: the current time (GMT) in the format "yyyy-mm-dd hh:mm:ss".
 *
 * and also
 *
 *  - $secret_key: a secret shared with the Websocket server
 *  - $location: the URL of the Websocket server
 *
 * The authentication works as follows. The variables $origin, $request_uri, $full_name,
 * $username and $request_date are concatenated in a $message. Then the $message and
 * the $secret_key are used to calculate a hashed message authentication code (HMAC)
 * according to RFC2104 (see function {@see hmac()} in waslib.php).
 *
 * When connecting to the Websocket server the parameters $request_uri, $full_name,
 * $username and $request_date are sent, together with the HMAC. The server then
 * calculates the HMAC too and if it matches the HMAC that was sent, access is
 * granted.
 *
 * Note that the variable $origin is only used here to calculate the HMAC; it is
 * not sent to the Websocket server like the other parameters. Instead we use the
 * Origin as seen by the user's web browser. Obviously the two should match or else
 * authentication fails. This way we check the browser's idea of where the web page
 * is located. Also note that we made the current date/time part of the HMAC. That
 * is done to prevent replay-attacks (the other variables are quasi-static between
 * CREW editing sessions). It is up to the Websocket server to determine if the
 * timestamp is (still) valid or not. This depends on a certain clock synchronisation
 * between the webserver and the Websocket server.
 *
 * Also note that the shared secret never leaves the webserver, only the hashed
 * message is sent from webserver to Websocket server. However, the secret has to
 * be the same on both ends.
 *
 * @param object &$theme collects the (html) output
 * @param int $module_id identifies the crew module (need that for getting module properties)
 * @param bool $first if TRUE we generate code to generate a popup
 * @return bool TRUE on success+output generated via $theme, FALSE otherwise
 */
function crew_view_show_edit(&$theme,$module_id,$first=FALSE) {
    global $USER,$WAS_SCRIPT_NAME,$CFG;

    // 1A -- fetch the latest version of the document (we always need that)...
    $node_id = intval($theme->node_record['node_id']);
    if (($record = crew_view_get_workshop_data($node_id)) === FALSE) {
        $theme->add_message(t('error_retrieving_workshop_data','m_crew'));
        return FALSE;
    }
    // 1B -- and tell the user the date/time/user of latest update in content area
    $params = array(
        '{USERNAME}' => (is_null($record['username'])) ? $record['muser_id'] : $record['username'],
        '{FULL_NAME}' => (is_null($record['full_name'])) ? $record['muser_id'] : $record['full_name'],
        '{DATIM}' => $record['mtime']
        );
    $attr = array('class' => 'crew_datim');
    $theme->add_content(html_tag('p',$attr,t('last_updated_by','m_crew',$params)));

    // 1C -- prepare a hidden textarea with the current document text
    /* <noscript>requires javascript</noscript>
     * <div>
     *   <form>
     *     <textarea>$document</textarea>
     *   </form>
     * </div>
     */
    $theme->add_content(html_tag('noscript','',t('crew_requires_js_and_ws','m_crew')));
    $attr = array('id' => 'crew_start_edit', 'style' => 'display: none;');
    $theme->add_content(html_tag('div',$attr));
    $href = was_node_url($theme->node_record);
    $attr = array('id' => 'frmEdit');
    $theme->add_content(html_form($href,'post',$attr));
    $attr = array('id' => 'txtText', 'rows' => 10, 'cols' => 80, 'name' => 'text');
    $theme->add_content(html_tag('textarea',$attr,htmlspecialchars($record['document'])));
    $theme->add_content(html_form_close());
    $theme->add_content(html_tag_close('div'));

    // At this point we're done IF this was a repeat call.
    // If it was the first call we need to do some more, like popping up the edit window
    if (!$first) {
        return TRUE;
    }

    // Still here, so this is the first time

    // 2 -- prepare all information for popup

    // 2A -- which skin?
    $dialogdef = crew_view_dialogdef();
    if (!dialog_validate($dialogdef)) { // somehow an error; default to first skin
        $value = '0'; 
    } else {
        $value = $dialogdef['skin']['value'];
    }
    $skin = $dialogdef['skin']['options'][$value]['css'];

    // 2B -- which location,origin,secret (from module_properties)
    $table = 'modules_properties';
    $fields = array('name', 'value');
    $where = array('module_id' => $module_id);
    $order = array('sort_order');
    $keyfield = 'name';
    if (($properties = db_select_all_records($table,$fields,$where,$order,$keyfield)) === FALSE) {
        logger(sprintf('%s(): module properties error: %s',__FUNCTION__,db_errormessage()));
        $theme->add_message(t('error_retrieving_workshop_data','m_crew'));
        return FALSE;
    }
    $org = $properties['origin']['value'];
    $loc = $properties['location']['value'];
    $secret = $properties['secret']['value'];

    // 2C -- prepare variables for and perform hmac calculation
    $workshop = trim($record['header']);
    if (empty($workshop)) {
        $workshop = trim($node_record['link_text']);
    }
    $uri = sprintf('%s/%d/%s',$WAS_SCRIPT_NAME, $node_id, friendly_bookmark($workshop));
    $name = $USER->full_name;
    $nick = $USER->username;
    $datim = gmstrftime('%Y-%m-%d %T');

    $hmac_key = $secret;
    $hmac_msg = $org.$uri.$name.$nick.$datim;
    $sig = hmac($hmac_key,$hmac_msg);
    $progcrew = $CFG->progwww_short.'/modules/crew';
    $css = $progcrew.'/'.$skin;
    if (($CFG->debug) || (!file_exists($CFG->progdir.'/modules/crew/crew.min.js'))) {
        $js = $progcrew.'/crew.js';
    } else {
        $js = $progcrew.'/crew.min.js';
    }
    $theme->add_content(html_tag('script'));
    $theme->add_content(crew_screen($loc,$nick,$name,$uri,$workshop,$org,$datim,$sig,$css,$js,$progcrew));
    $theme->add_content(html_tag_close('script'));
    return TRUE;
} // crew_view_show_edit()


/** construct triple-indirect edit screen in pop-up
 *
 * this routine constructs the necessary HTML-code to show in the CREW
 * editor screen. We use this dynamic construction in order to be able
 * to simply plugin parameters and translated strings into the generated
 * pop-up screen. Perhaps there is a better way (without using a separate
 * javascript translation file for every language) but I haven't thought
 * of it yet.
 *
 * So here's the deal. We use PHP to generate HTML code which includes the
 * javascript configuration for the CREW editor. This generated HTML is
 * written to the regular page that is generated whenever the user presses
 * the Edit-button in the regular page in the form of a long Javascript
 * string which is used to pop-up a new window. Overly complicated, so
 * let me know if there is a better (cleaner) way.
 *
 */ 
function crew_screen($loc,$uid,$user,$uri,$shop,$org,$date,$hmac,$css,$jscript,$progcrew) {
  $vars = array('reqLocation'  => $loc,
		'reqUserId'    => $uid,
		'reqUserName'  => $user,
		'reqWorkshop'  => $uri,
		'reqShop'      => $shop,
		'reqOrigin'    => $org,
		'reqDate'      => $date,
		'reqSignature' => $hmac);
  $tras = array(t('crew_js_websocket_not_supported','m_crew'), 
		t('crew_js_initialised','m_crew'), 
		t('crew_js_connected','m_crew'), 
		t('crew_js_disconnected_clean','m_crew'), 
		t('crew_js_disconnected_unclean','m_crew'), 
		t('crew_js_unknown_msg','m_crew'), 
		t('crew_js_error','m_crew'), 
		t('crew_js_save_characters','m_crew'), 
		t('crew_js_saveedit_characters','m_crew'), 
		t('crew_js_cancel_characters','m_crew'), 
		t('crew_js_sound_off','m_crew'), 
		t('crew_js_sound_on','m_crew'), 
		t('crew_js_enters_workshop','m_crew'), 
		t('crew_js_leaves_workshop','m_crew'), 
		t('crew_js_malformed_message','m_crew'), 
		t('crew_js_unloading','m_crew'), 
		t('crew_js_authenticating','m_crew'), 
		t('crew_js_error_relocate','m_crew'), 
		t('crew_js_error_patchcount','m_crew'), 
		t('crew_js_error_context','m_crew'), 
		t('crew_js_error_usercount','m_crew'),
		t('crew_js_error_document_size','m_crew')
		);
  // massage a few parameters just in case
  // - str_replace to get rid of newlines because javascript wants string literals on a single line
  // - htmlspecialchars because embedded quotes must be converted to html-entities
  // - addslashes because any single quote has to be escaped in javascript to get through eventually
  // newlines in these pathnames are not supposed to be there; get rid of them completely
  $crew_css = addslashes(htmlspecialchars(str_replace("\n","",$css)));
  $crew_js =  addslashes(htmlspecialchars(str_replace("\n","",$jscript)));
  $crew_dir = addslashes(htmlspecialchars(str_replace("\n","",$progcrew)));

  // newlines in these translated button names/titles have no effect in html; a simple space will do
  $save_v =   addslashes(htmlspecialchars(str_replace("\n"," ",t('crew_button_save','m_crew'))));
  $save_t =   addslashes(htmlspecialchars(str_replace("\n"," ",t('crew_button_save_title','m_crew'))));
  $edit_v =   addslashes(htmlspecialchars(str_replace("\n"," ",t('crew_button_saveedit','m_crew'))));
  $edit_t =   addslashes(htmlspecialchars(str_replace("\n"," ",t('crew_button_saveedit_title','m_crew'))));
  $cancel_v = addslashes(htmlspecialchars(str_replace("\n"," ",t('crew_button_cancel','m_crew'))));
  $cancel_t = addslashes(htmlspecialchars(str_replace("\n"," ",t('crew_button_cancel_title','m_crew'))));
  $refresh_v= addslashes(htmlspecialchars(str_replace("\n"," ",t('crew_button_refresh','m_crew'))));
  $refresh_t= addslashes(htmlspecialchars(str_replace("\n"," ",t('crew_button_refresh_title','m_crew'))));
  $send_v =   addslashes(htmlspecialchars(str_replace("\n"," ",t('crew_button_send','m_crew'))));
  $send_t =   addslashes(htmlspecialchars(str_replace("\n"," ",t('crew_button_send_title','m_crew'))));
  $sound_v =  addslashes(htmlspecialchars(str_replace("\n"," ",t('crew_button_sound','m_crew'))));
  $sound_t =  addslashes(htmlspecialchars(str_replace("\n"," ",t('crew_button_sound_title','m_crew'))));

  // generate a piece of javascript that pops up an HTML-page filled with javascript (sorry)
  $js = <<<EOF
function crew_start() {
  var lt=0;
  var tp=0;
  var wd=screen.width-2*lt;
  var ht=screen.height-2*tp;
  var features = 'width='+wd+',height='+ht+',top='+tp+',left='+lt+',location=0,menubar=0,scrollbars=0,status=0,titlebar=0,toolbar=0';
  var html=
'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">\\n'+
'<html>\\n'+
'<head>\\n'+
' <title></title>\\n'+
' <link rel="stylesheet" type="text/css" href="$crew_css">\\n'+
'</head>\\n'+
'<body>\\n'+
' <div id="divCrewContainer0">\\n'+
'  <div id="divCrewContainer1">\\n'+
'   <div id="divCrewEdit" contenteditable="true"></div>\\n'+
'   <div id="divCrewMembers"></div>\\n'+
'   <input type="submit" id="btnCrewSave" name="button_save" value="$save_v" title="$save_t">\\n'+
'   <input type="submit" id="btnCrewSaveEdit" name="button_edit" value="$edit_v" title="$edit_t">\\n'+
'   <input type="submit" id="btnCrewCancel" name="button_cancel" value="$cancel_v" title="$cancel_t">\\n'+
'   <input type="submit" id="btnCrewRefresh" name="button_refresh" value="$refresh_v" title="$refresh_t">\\n'+
'  </div>\\n'+
'  <div id="divCrewContainer2">\\n'+
'   <input id="txtCrewMessage">\\n'+
'   <input type="submit" id="btnCrewSound" name="button_sound" value="$sound_v" title="$sound_t">\\n'+
'   <input type="submit" id="btnCrewMessage" name="button_message" value="$send_v" title="$send_t">\\n'+
'   <div id="divCrewMessages"></div>\\n'+
'  </div>\\n'+
' </div>\\n'+
' <s'+'cript type="text/javascript" src="$crew_js"></s'+'cript>\\n'+
' <sc'+'ript type="text/javascript">\\n'+

EOF;
  $js .= "'  var visibleCaret=\\'\\\\u2610\\';\\n'+\n"; // pilcrow='\u00b6' ballot-box='\u2610';
  $js .= "'  var logReverse=1;\\n'+\n"; // 1=insert latest at the top, 0=append latest at bottom
  $js .= sprintf("'  var maxDocumentSize=\\'%d\\';\\n'+\n",CREW_MAX_DOCUMENT_SIZE); // tell about limits
  $js .= sprintf("'  var crewDir=\\'%s\\';\\n'+\n",addslashes(addslashes($crew_dir))); // beep.* path

  // Once again, a nasty sequence of escaped escapes:
  // - addslashes to eventually escape quotes etc. in the generatED javascript
  // - substitute newlines with backslash n '\n' because javascript only wants single line string literals
  // - addslashes to escape escape characters in the generatING javascript.
  // really ugly... :-(
  foreach($vars as $k => $v) {
    if (is_bool($v)) {
      $js .= sprintf("'  var %s=%d;\\n'+\n",$k,($v) ? 1 : 0);
    } else {
      $js .= sprintf("'  var %s=\\'%s\\';\\n'+\n",$k,addslashes(str_replace("\n","\\n",addslashes($v))));
    }
  }
  $js .= "'  var tr=[\\'".addslashes(addslashes($tras[0]));
  for ($i=1; $i<sizeof($tras); ++$i) {
    $js .= "\\',\\n\\'".addslashes(str_replace("\n","\\n",addslashes($tras[$i])));
  }
  $js .= "\\'];\\n'+\n";
  $js .= <<<EOF
'  onLoad(crew_init);\\n'+
' </s'+'cript>\\n'+
'</body>\\n'+
'</html>\\n';
  var w = window.open('','',features);
  w.document.open();
  w.document.writeln(html);
  w.document.close();
  w.focus();
} // crew_start()
crew_start();

EOF;
 return $js;
} // crew_screen()


/** retrieve the current version of the document + other workshop data
 *
 * @param int $node_id which one are we looking at?
 * @return bool|array FALSE on error, array with record from db otherwise
 */
function crew_view_get_workshop_data($node_id) {
    global $DB;
    $sql = sprintf('SELECT w.*, u.username, u.full_name '.
                   'FROM %sworkshops w LEFT JOIN %susers u ON w.muser_id = u.user_id '.
                   'WHERE node_id = %d',
                   $DB->prefix, $DB->prefix, $node_id);
    if ((($db_result = $DB->query($sql,1)) === FALSE) || ($db_result->num_rows != 1)) {
        logger(sprintf('%s(): workshop %d data error: %s',__FUNCTION__,$node_id,db_errormessage()));
        return FALSE;
    }
    $record = $db_result->fetch_row_assoc();
    $db_result->close();
    return $record;
} // crew_view_get_workshop_data()


?>