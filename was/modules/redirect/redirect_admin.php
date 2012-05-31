<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker <peter@berestijn.nl>
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

/** /program/modules/redirect/redirect_admin.php - management interface for redirect-module
 *
 * This file defines the administrative interface to this module.
 * The interface consists of the following four functions.
 *
 * <code>
 * redirect_disconnect(&$output,$area_id,$node_id,$module)
 * redirect_connect(&$output,$area_id,$node_id,$module)
 * redirect_show_edit(&$output,$area_id,$node_id,$module,$viewonly,$edit_again,$href)
 * redirect_save(&$output,$area_id,$node_id,$module,$viewonly,&$edit_again)
 * </code>
 *
 * These functions are called from pagemanagerlib.php whenever necessary.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_redirect
 * @version $Id: redirect_admin.php,v 1.1 2012/05/31 16:58:11 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** disconnect this module from a node
 *
 * this breaks the link between the node $node_id in area $area_id and
 * this module. For now we simply delete the content of the link_url
 * and link_target fields in the node record.
 * 
 * @param object &$output collects the html output (if any)
 * @param int $area_id the area in which $node_id resides
 * @param int $node_id the node from which we need to disconnect
 * @param array $module the module record straight from the database
 * @return bool TRUE on success, FALSE otherwise
 */
function redirect_disconnect(&$output,$area_id,$node_id,$module) {
    $now = strftime('%Y-%m-%d %T');
    $table = 'nodes';
    $fields = array(
        'link_href'   => '',
        'link_target' => '',
        'mtime'       => $now
        );
    $where = array('node_id' => intval($node_id));
    $retval = db_update($table,$fields,$where);
    return ($retval === FALSE) ? FALSE : TRUE;
} // redirect_disconnect()


/** connect this module to a node
 *
 * this makes the link between the node $node_id in area $area_id and this module.
 * Since all this module does is manipulating the node record, this is a no-op.
 *
 * @param object &$output collects the html output (if any)
 * @param int $area_id the area in which $node_id resides
 * @param int $node_id the node to which we need to connect
 * @param array $module the module record straight from the database
 * @return bool TRUE on success, FALSE otherwise
 */
function redirect_connect(&$output,$area_id,$node_id,$module) {
    return TRUE;
} // redirect_connect()


/** present the user with a dialog to modify the redirect that is connected to node $node_id
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
function redirect_show_edit(&$output,$area_id,$node_id,$module,$viewonly,$edit_again,$href) {
    $dialogdef = redirect_get_dialogdef($viewonly);
    if ($edit_again) {
        // retrieve and validate the POSTed values
        dialog_validate($dialogdef); // no need to show messages; we did that alread in redirect_save() bel0w
    } else {
        // make a fresh start with data from the database
        $table = 'nodes';
        $fields = array('link_href', 'link_target');
        $where = array('node_id' => intval($node_id));
        if (($record = db_select_single_record($table, $fields, $where)) === FALSE) {
            logger(sprintf('%s(): error retrieving node record for redirect configuration: %s',__FUNCTION__,db_errormessage()));
        } else {
            foreach($dialogdef as $name => $item) {
                switch($name) {
                    case 'link_href':
                    case 'link_target':
                        $dialogdef[$name]['value'] = strval($record[$name]);
                        break;
                }
            }
        }
    }
    $output->add_content('<h2>'.t('redirect_content_header','m_redirect').'</h2>');
    $output->add_content(t('redirect_content_explanation','m_redirect'));
    $output->add_content(dialog_quickform($href,$dialogdef));
    return TRUE;
} // redirect_show_edit()


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
function redirect_save(&$output,$area_id,$node_id,$module,$viewonly,&$edit_again) {
    global $USER;

    // 1 -- bail out if cancelled or viewonly
    if ((isset($_POST['button_cancel'])) || ($viewonly)) {
        $edit_again = FALSE;
        return FALSE;
    }

    // 2 -- redo if invalid data was submitted
    $dialogdef = redirect_get_dialogdef($viewonly);
    if (!dialog_validate($dialogdef)) {
        // there were errors, show them to the user and do it again
        foreach($dialogdef as $k => $item) {
            if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                $output->add_message($item['error_messages']);
            }
        }
        $edit_again = TRUE;
        return FALSE;
    }

    // 3 -- actually save the new setting
    $now = strftime('%Y-%m-%d %T');
    $table = 'nodes';
    $fields = array('link_href'   => trim($dialogdef['link_href']['value']),
                    'link_target' => trim($dialogdef['link_target']['value']),
                    'mtime'       => $now);
    $where = array('node_id' => intval($node_id));
    $retval = db_update($table,$fields,$where);
    if (db_update($table,$fields,$where) === FALSE) {
        logger(sprintf('%s(): error saving config value: %s',__FUNCTION__,db_errormessage()));
        $edit_again = TRUE;
        return FALSE;
    } else {
        return TRUE; // $edit_again is a don't care
    }
} // redirect_save()


/** construct a dialog definition for the redirect 'scope' value
 *
 * @param int $viewonly if TRUE the Save button is not displayed and config values cannot be changed by the user
 * @return array dialog definition
 */
function redirect_get_dialogdef($viewonly) {
    $dialogdef = array(
        'link_href' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'link_href',
            'maxlength' => 240,
            'minlength' => 1,
            'columns' => 50,
            'label' => t('link_href','m_redirect'),
            'title' => t('link_href_title','m_redirect'),
            'viewonly' => $viewonly
            ),
        'link_target' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'link_target',
            'maxlength' => 240,
            'columns' => 50,
            'label' => t('link_target','m_redirect'),
            'title' => t('link_target_title','m_redirect'),
            'viewonly' => $viewonly
            )
        );
    if (!$viewonly) {
        $dialogdef['button_save'] = dialog_buttondef(BUTTON_SAVE);
    }
    $dialogdef['button_cancel'] = dialog_buttondef(BUTTON_CANCEL);
    return $dialogdef;
} // redirect_get_dialogdef()


?>