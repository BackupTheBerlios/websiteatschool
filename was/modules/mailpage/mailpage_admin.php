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
 * @version $Id: mailpage_admin.php,v 1.3 2013/07/02 18:13:03 pfokker Exp $
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
function mailpage_disconnect(&$output, $area_id, $node_id, $module) {
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
function mailpage_connect(&$output, $area_id, $node_id, $module) {
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
 * place where the form should be POST'ed (but see the notes below).
 *
 * Note that the mailpage module uses two different configuration tables.
 *
 * The first is 'mailpages' which holds the configuration for a single mailpage in a single record.
 * The second one is 'mailpages_addresses' which holds 0, 1 or more addresses per mailpage, and
 * thus has more records per mailpage. The way to deal with this 1-on-N situation (imho) is
 * to actually have two dialogs that the user can interact with.
 *
 * The first dialog looks something like this:
 * <pre>
 * _Add an address_
 * _Address 1_
 * _Address 2_
 * ...
 * Header:          __________
 * Introduction:    __________
 * Default message: __________
 * * [Save] [Cancel]
 * </pre>
 *
 * where the clickable links _Add an address_,...,_Address 1_ link to the second dialog:
 * <pre>
 * Name:         __________
 * E-mail:       __________
 * Description:  __________
 * Thank-you:    __________
 * Sort order:   __________
 * [Save] [Cancel] [Delete]
 * </pre>
 *
 * This dialog allows for manipulating addresses to go with the mailpage.
 * The clickable link that leads to dialog #2 can be distinguished from the
 * main dialog via the GET-parameter 'address' which holds the unique pkey
 * 'mailpage_address_id' of the address, or 0 in case of a new address to add.
 *
 * Since the module interface is mostly designed to work with a single dialog
 * (via <modulename>_show_edit() and <modulename>_save()) there is a challenge
 * to get two dialogs working from those two routines.
 *
 * The best I could think of is to add the parameter 'address' to the URLs:
 * if it is there we need to deal with a dialog #2 and if it isn't we deal with
 * dialog #1. We need to take care that the buttons in dialog #2 only take the
 * user back to dialog #1 rather than end the editing of the dialog alltogether.
 * The buttons in dialog #1 should end the editing, however. Mmm, tricky.
 *
 * The trick is to 'abuse' the return value of 'edit_again': if that parameter
 * is TRUE, the <module>_save() routine eventually yields a call to <module>_show_edit.
 * If we test for 'edit_again' AND GET['address'] we can isolate the case where
 * dialog #2 returns 'edit_again' even though the data in dialog #2 was valid
 * (as determined by checking the dialog again). So, it is a little complicated
 * (understatement) but it could work, at least from the POV of the user.
 *
 * There is one other thing: we only get the $href to POST to. It _should_
 * be used 'as-is', but we know (from Page Manager) that it is in fact a link
 * to admin.php with a 'job', 'task' and 'node' parameter. We can simply add
 * an extra parameter by appending '&address=nnn' to $href, but that does not
 * give us access to the task TASK_NODE_EDIT_CONTENT but only to the task
 * TASK_SAVE_CONTENT. Aaarrgghhhh, it seems we need to violate the interface
 * and construct our own $href via
 * <code>
 * $href=href($WAS_SCRIPT_NAME,array('job'=>JOB_PAGEMANAGER,'task'=>TASK_XXX_YYY,'node'=>$node_id));
 * </code>
 * rather than using the $href provided. Ugly. I'm sorry about that.
 *
 * The dialog should be added to the $output object. Useful routines are:
 * <code>
 * $output->add_content($content): add $content to the content area
 * $output->add_message($message): add $message to the message area (feedback to the user)
 * $output->add_popup_bottom($message): make $message popup in the browser after loading the page (uses JS)
 * $output->add_popup_top($message): make $message popup in the browser before loading the page (uses JS)
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
function mailpage_show_edit(&$output, $area_id, $node_id, $module, $viewonly, $edit_again, $href) {
    global $WAS_SCRIPT_NAME;
    $records = mailpage_get_addresses($node_id);
    $sort_order = 10 * (1 + sizeof($records)); // $records are always renumbered so this is the first largest sord_order
    //
    // 1 -- determine which dialog to show based on GET['address'], $edit_again and validate($dialog2)
    //
    $address_id = get_parameter_int('address',NULL);
    if ((is_null($address_id)) || (($address_id != 0) && (!isset($records[$address_id])))) {
        $dialogdef = mailpage_get_dialogdef_config($output,$viewonly,$node_id);
        if (($edit_again) && (!isset($_POST['button_delete']))) { // retrieve and validate POSTed values
            mailpage_dialog_validate($dialogdef,$node_id,$records); // don't show messages; alrady did that
        }
        $dialog = 1;
    } else { // $address_id is either 0 (new address to add) OR is a valid existing address_id
        $dialogdef = mailpage_get_dialogdef_address($output,$viewonly,$node_id, $address_id, $sort_order);
        if ($edit_again) {
	    if (dialog_validate($dialogdef)) {
	        $dialogdef = mailpage_get_dialogdef_config($output,$viewonly,$node_id);
                $dialog = 1;
            } else {
                $dialog = 2;
            }
        } else {
            $dialog = 2;
        }
    }
    //
    // 2 -- actually show the selected dialog
    //
    if ($dialog == 1) {
        $output->add_content('<h2>'.t('mailpage_content_header','m_mailpage').'</h2>');
        $output->add_content(t('mailpage_content_explanation','m_mailpage'));
        $params = array('job' => JOB_PAGEMANAGER,
                        'task' => TASK_NODE_EDIT_CONTENT,
                        'node' => $node_id,
                        'address' => '0');
        $output->add_content('<p>');
        if (!$viewonly) { // cannot add records in view-only mode
            $attributes = array('title' => t('add_new_address_title','m_mailpage'));
            $anchor = t('add_new_address_label','m_mailpage');
            $output->add_content(html_a($WAS_SCRIPT_NAME,$params,$attributes,$anchor).'<br>');
        }
        foreach($records as $record) {
            $params['address'] = $record['mailpage_address_id'];
            $aparams = array('{NAME}' => $record['name'],
                             '{EMAIL}' => $record['email'],
                             '{SORT_ORDER}' => $record['sort_order'],
                             '{ADDRESS_ID}' => $record['mailpage_address_id']);
            $anchor = t('edit_address_label','m_mailpage',$aparams);
            $attributes = array('title' => t('edit_address_title','m_mailpage',$aparams));
            $output->add_content(html_a($WAS_SCRIPT_NAME,$params,$attributes,$anchor).'<br>');
        }
        $output->add_content('</p>');
        $output->add_content(dialog_quickform($href,$dialogdef));
    } else {
        if ($address_id != 0) {
            $output->add_content('<h2>'.t('mailpage_edit_address_header','m_mailpage').'</h2>');
            $output->add_content(t('mailpage_edit_address_explanation','m_mailpage'));
        } else {
            $output->add_content('<h2>'.t('mailpage_add_address_header','m_mailpage').'</h2>');
            $output->add_content(t('mailpage_add_address_explanation','m_mailpage'));
        }
        // Alas, we need to construct our own href because of adding $address_id
        $href = href($WAS_SCRIPT_NAME, array(
            'job'=>JOB_PAGEMANAGER,
            'task'=>TASK_SAVE_CONTENT,
            'node'=>$node_id,
            'address' => strval($address_id)));
        $output->add_content(dialog_quickform($href,$dialogdef));
    }
    return TRUE;
} // mailpage_show_edit()


/** save the modified content data of this module linked to node $node_id
 *
 * this validates and saves the data that was submitted by the user.
 *
 * See also {@see mailpage_show_edit()} for the complications of having a single
 * routine to deal with two different dialogs.
 
 * If validation of dialog 1 fails, or storing the data doesn't work,
 * the flag $edit_again is set to TRUE and the return value is FALSE.
 * Validation and storage of data from dialog 2 _always_ returns $edit_again
 * TRUE because we want to return in dialog #1 after finishing dialog #2.
 *
 * If the user has cancelled the operation, the flag $edit_again is set to FALSE
 * and the return value is also FALSE.
 *
 * If the modified data is stored successfully, the return value is TRUE (and
 * the value of $edit_again is a don't care). Note that this also only applies
 * to the main dialoag (dialog #1).
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
function mailpage_save(&$output, $area_id, $node_id, $module, $viewonly, &$edit_again) {
    global $USER;
    $node_id = intval($node_id);
    $addresses = mailpage_get_addresses($node_id);
    $sort_order = 10 * (1 + sizeof($addresses)); // $addresses are always renumbered so this is the first largest sord_order

    $address_id = get_parameter_int('address',NULL);
    if (is_null($address_id)) { // main config needs to be saved
        $edit_again = FALSE; // assume we do NOT need to edit again
        // 1 -- bail out if cancelled or viewonly
        if ((isset($_POST['button_cancel'])) || ($viewonly)) {
            return FALSE;
        }
        // 2 -- redo if invalid data was submitted
        $dialogdef = mailpage_get_dialogdef_config($output,$viewonly,$node_id);
        if (!mailpage_dialog_validate($dialogdef, $node_id, $addresses)) {
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
        $table = 'mailpages';
        $fields = array('header'   => trim($dialogdef['header']['value']),
                        'introduction' => trim($dialogdef['introduction']['value']),
                        'message' => trim($dialogdef['message']['value']),
                        'mtime' => $now,
                        'muser_id' => $USER->user_id);
        $where = array('node_id' => $node_id);
        if (db_update($table,$fields,$where) === FALSE) {
            logger(sprintf('%s(): error saving config values: %s',__FUNCTION__,db_errormessage()));
            $edit_again = TRUE;
            $retval = FALSE;
            $output->add_message(t('error_saving_data','m_mailpage'));
        }
        return $retval;
    }
    //
    // At this point we need to either save a new record, update an existing record,
    // delete an existing record or simply cancel and return to the main config dialog.
    // The logic depends on the submit button that was used and the value of $address_id.
    //
    $dialogdef = mailpage_get_dialogdef_address($output,$viewonly,$node_id, $address_id, $sort_order);
    if (!dialog_validate($dialogdef, $node_id, $addresses)) {
        // there were errors, show them to the user and do it again
        foreach($dialogdef as $k => $item) {
            if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                $output->add_message($item['error_messages']);
            }
        }
        $edit_again = TRUE;
        return FALSE;
    }
    $edit_again = TRUE; // we abuse this flag to return to the main config dialog instead of page mgr
    if ((isset($_POST['button_cancel'])) || ($viewonly)) {
        return FALSE;
    }
    $table = 'mailpages_addresses';
    $fields = array(
        'node_id' => $node_id,
        'sort_order' => intval($dialogdef['sort_order']['value']),
        'name' => trim($dialogdef['name']['value']),
        'email' => trim($dialogdef['email']['value']),
        'description' => trim($dialogdef['description']['value']),
        'thankyou' => trim($dialogdef['thankyou']['value'])
        );
    if ($address_id <= 0) { // new record needs to be saved.
        if (db_insert_into($table,$fields) === FALSE) {
            logger(sprintf('%s(): error adding address: %s',__FUNCTION__,db_errormessage()));
            $output->add_message(t('error_saving_data','m_mailpage'));
        }
    } elseif (isset($addresses[$address_id])) { // OK, that is an existing record
        $where = array('mailpage_address_id' => $address_id);
        if (isset($_POST['button_save'])) { // Go save the record
            if (db_update($table,$fields,$where) === FALSE) {
                logger(sprintf('%s(): error updating address: %s',__FUNCTION__,db_errormessage()));
                $output->add_message(t('error_saving_data','m_mailpage'));
            }
        } elseif (isset($_POST['button_delete'])) { // Go delete this record
            if (db_delete($table,$where) === FALSE) {
                logger(sprintf('%s(): error deleting address: %s',__FUNCTION__,db_errormessage()));
                $output->add_message(t('error_deleting_data','m_mailpage'));
            }
        }
    }
    return FALSE; // Dirty trick to return to the main config dialog
} // mailpage_save()


/** validate the data entered by the user
 *
 * Other than the standard validation we check for at least 1 destination
 *
 * @param object &$dialogdef defines the dialog and will hold POSTed values
 * @param int $node_id which page?
 * @param array $addresses holds the currently defined addresses
 * @return bool TRUE if valid, else FALSE + messages added to dialogdef
 */
function mailpage_dialog_validate(&$dialogdef, $node_id, $addresses) {
    $retval = TRUE; // assume success
    // 1 -- check the usual suspects (side-effect: fetch POSTed values in dialogdef)
    if (!dialog_validate($dialogdef)) {
        $retval = FALSE;
    }

    // 2 -- special case: there must be at least 1 destination
    if (sizeof($addresses) < 1) {
        $retval = FALSE;
        ++$dialogdef['button_save']['errors'];
        $params = array('{NODE}' => $node_id);
        $dialogdef['button_save']['error_messages'][]=t('error_retrieving_addresses','m_mailpage',$params);
    }
    return $retval;
} // mailpage_dialog_validate()


/** construct a dialog definition for the main mailpage configuration
 *
 * @param object &$output collects the html output (if any)
 * @param int $viewonly if TRUE the Save button is not displayed and config values cannot be changed
 * @param int $node_id identifies the current mailpage
 * @return array dialog definition
 */
function mailpage_get_dialogdef_config(&$output, $viewonly, $node_id) {
    //
    // 1 -- generic configuration parameters
    //
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
            'columns' => 40,
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
            ),
        'message' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'message',
            'minlength' => 0,
            'maxlength' => 32768, // arbitrary; 32 kB
            'columns' => 50,
            'rows' => 10,
            'label' => t('default_message_label','m_mailpage'),
            'title' => t('default_message_title','m_mailpage'),
            'viewonly' => $viewonly,
            'value' => $message,
            'old_value' => $message
            )
        );
    //
    // 2 -- maybe add a save button but always a cancel button
    //
    if (!$viewonly) {
        $dialogdef['button_save'] = dialog_buttondef(BUTTON_SAVE);
    }
    $dialogdef['button_cancel'] = dialog_buttondef(BUTTON_CANCEL);
    return $dialogdef;
} // mailpage_get_dialogdef_config()


/** construct a dialog definition for a mailpage address configuration
 *
 * @param object &$output collects the html output (if any)
 * @param int $viewonly if TRUE the Save button is not displayed and config values cannot be changed
 * @param int $node_id identifies the current mailpage
 * @param int $address_id identifies the address, 0 for a new one
 * @param int $sort_order is the next available sort order for new records
 * @return array dialog definition
 */
function mailpage_get_dialogdef_address(&$output, $viewonly, $node_id, $address_id, $sort_order=10) {
    //
    // 1 -- maybe get existing data from table
    //
    $values = array(
        'name'        => '',
        'sort_order'  => $sort_order,
        'email'       => '',
        'description' => '',
        'thankyou'    => '',
        'message'     => ''
        );
    if ($address_id > 0) {
        $table = 'mailpages_addresses';
        $keyfield = 'mailpage_address_id';
        $fields = '*';
        $where = array('node_id' => intval($node_id), $keyfield => intval($address_id));
        if (($record = db_select_single_record($table, $fields, $where)) === FALSE) {
            logger(sprintf('%s(): error retrieving configuration: %s',__FUNCTION__,db_errormessage()));
            $output->add_message(t('error_retrieving_data','admin'));
        } else {
            $values = $record;
        }
    }
    //
    // 2 -- define the dialog
    //
    $dialogdef = array(
        'name' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'name',
            'minlength' => 0,
            'maxlength' => 80,
            'columns' => 40,
            'label' => t('address_name_label','m_mailpage'),
            'title' => t('address_name_title','m_mailpage'),
            'viewonly' => $viewonly,
            'value' => $values['name'],
            'old_value' => $values['name'],
            ),
        'email' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'email',
            'minlength' => 0,
            'maxlength' => 255,
            'columns' => 40,
            'label' => t('address_email_label','m_mailpage'),
            'title' => t('address_email_title','m_mailpage'),
            'viewonly' => $viewonly,
            'value' => $values['email'],
            'old_value' => $values['email']
            ),
        'description' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'description',
            'minlength' => 0,
            'maxlength' => 240,
            'columns' => 40,
            'label' => t('address_description_label','m_mailpage'),
            'title' => t('address_description_title','m_mailpage'),
            'viewonly' => $viewonly,
            'value' => $values['description'],
            'old_value' => $values['description']
            ),
        'thankyou' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'thankyou',
            'minlength' => 0,
            'maxlength' => 240,
            'columns' => 40,
            'label' => t('address_thankyou_label','m_mailpage'),
            'title' => t('address_thankyou_title','m_mailpage'),
            'viewonly' => $viewonly,
            'value' => $values['thankyou'],
            'old_value' => $values['thankyou']
            ),
        'sort_order' => array(
            'type' => F_INTEGER,
            'name' => 'sort_order',
            'minlength' => 0,
            'maxlength' => 10,
            'columns' => 7,
            'minvalue' => 0,
            'label' => t('address_sort_order_label','m_mailpage'),
            'title' => t('address_sort_order_title','m_mailpage'),
            'viewonly' => $viewonly,
            'value' => $values['sort_order'],
            'old_value' => $values['sort_order']
            )
        );
    //
    // 3 -- add buttons: save (mabye), cancel and delete (maybe)
    //
    if (!$viewonly) {
        $dialogdef['button_save'] = dialog_buttondef(BUTTON_SAVE);
    }
    $dialogdef['button_cancel'] = dialog_buttondef(BUTTON_CANCEL);

    if ((!$viewonly) && ($address_id != 0)){
        $dialogdef['button_delete'] = dialog_buttondef(BUTTON_DELETE);
    }
    return $dialogdef;
} // mailpage_get_dialogdef_address()


/** retrieve current list of addresses in an array (could be empty)
 *
 * this retrieves the addresses associated with $node_id from the
 * database. As an important side effect all entries' sort order values
 * are renumbered. This means that we always have a neat set of sort
 * order numbers 10, 20, ...
 *
 * On error we return an empty array as a sort of best effort.
 * The error is logged though.
 *
 * @param int $node_id indicates page
 * @return array records from the database with sort order renumbered OR an empty array
 */
function mailpage_get_addresses($node_id) {
    $table = 'mailpages_addresses';
    $keyfield = 'mailpage_address_id';
    $fields = '*';
    $where = array('node_id' => intval($node_id));
    $order = array('sort_order',$keyfield);
    if (($records = db_select_all_records($table,$fields,$where,$order,$keyfield)) === FALSE) {
        logger(sprintf('%s(): error retrieving addresses: %s',__FUNCTION__,db_errormessage()));
        return array();
    }
    // side-effect: renumber the sort order of the addresses if not a nice list of 10, 20, ...
    $sort_order = 0;
    foreach($records as $k => $record) {
        $sort_order += 10;
        if ($record['sort_order'] == $sort_order) {
            continue;
        }
        $records[$k]['sort_order'] = $sort_order;
        $where = array($keyfield => intval($record[$keyfield]));
        $fields = array('sort_order' => $sort_order);
        if (db_update($table,$fields,$where) === FALSE) {
            logger(sprintf('%s(): error renumbering addresses: %s',__FUNCTION__,db_errormessage()));
        }
    }
    return $records;
} // mailpage_get_addresses()

?>