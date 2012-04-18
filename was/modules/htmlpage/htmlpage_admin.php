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

/** /program/modules/htmlpage/htmlpage_admin.php - management interface for htmlpage-module
 *
 * This file defines the administrative interface to this module.
 * The interface consists of the following four functions.
 *
 * <code>
 * htmlpage_disconnect(&$output,$area_id,$node_id,$module)
 * htmlpage_connect(&$output,$area_id,$node_id,$module)
 * htmlpage_show_edit(&$output,$area_id,$node_id,$module,$viewonly,$edit_again,$href)
 * htmlpage_save(&$output,$area_id,$node_id,$module,$viewonly,&$edit_again)
 * </code>
 *
 * These functions are called from pagemanagerlib.php whenever necessary.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_htmlpage
 * @version $Id: htmlpage_admin.php,v 1.3 2012/04/18 07:57:24 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** disconnect this module from a node
 *
 * this breaks the link between the node $node_id in area $area_id and this module.
 * For now we simply delete the record with page data. In a future version
 * we might want to retain the page data, 'for future reference' (but: what do
 * we do with it? Oh well).
 * 
 * @param object &$output collects the html output (if any)
 * @param int $area_id the area in which $node_id resides
 * @param int $node_id the node from which we need to disconnect
 * @param array $module the module record straight from the database
 * @return bool TRUE on success, FALSE otherwise
 */
function htmlpage_disconnect(&$output,$area_id,$node_id,$module) {
    $where = array('node_id' => intval($node_id));
    $retval = db_delete('htmlpages',$where);
    return ($retval == 1) ? TRUE : FALSE;
} // htmlpage_disconnect()


/** connect this module to a node
 *
 * this makes the link between the node $node_id in area $area_id and this module.
 * In this case we simply link a data container to node $node_id in a 1-to-1 relation.
 * Note that we might decide lateron to keep versions of pages around, e.g. by inserting
 * new records every save rather than updating the existing.
 * 
 * @param object &$output collects the html output (if any)
 * @param int $area_id the area in which $node_id resides
 * @param int $node_id the node to which we need to connect
 * @param array $module the module record straight from the database
 * @return bool TRUE on success, FALSE otherwise
 */
function htmlpage_connect(&$output,$area_id,$node_id,$module) {
    global $USER;
    $now = strftime('%Y-%m-%d %T');
    $fields = array(
        'node_id' => intval($node_id),
        'version' => 1,
        'page_data' => '',
        'ctime' => $now,
        'cuser_id' => $USER->user_id,
        'mtime' => $now,
        'muser_id' => $USER->user_id);
    $retval = db_insert_into('htmlpages',$fields);
    return ($retval == 1) ? TRUE : FALSE;
} // htmlpage_connect()


/** present the user with a dialog to modify the content that is connected to node $node_id
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
 * @param string $href the action property of the HTML-form, the placa where data will be POST'ed
 * @return bool TRUE on success + output stored via $output, FALSE otherwise
 */
function htmlpage_show_edit(&$output,$area_id,$node_id,$module,$viewonly,$edit_again,$href) {
    if ($edit_again) {
        $value = magic_unquote($_POST['htmlpage_content']);
    } else {
        $where = array('node_id' => intval($node_id));
        $order = array('version DESC');
        $retval = db_select_single_record('htmlpages','*',$where,$order);
        if ($retval === FALSE) {
            $value = '??error??';
            $version = 0;
        } else {
            $value = $retval['page_data'];
            $version = $retval['version'];
        }
    }

    $dialogdef = array(
        'htmlpage_content' => array(
            'type' => F_RICHTEXT,
            'name' => 'htmlpage_content',
            'value' => $value,
            'rows' => 20,
            'columns' => 80,
            'maxlength' => 655360, // arbitrary limit, 640kB is enough for anyone (famous last words)
            'viewonly' => $viewonly,
            'alt' => 'STUB: alttext goes here',
            'title' => 'STUB: mouse-over goes here',
            )
        );
    if (!$viewonly) {
        $dialogdef['button_save'] = dialog_buttondef(BUTTON_SAVE);
    }
    $dialogdef['button_cancel'] = dialog_buttondef(BUTTON_CANCEL);
    $output->add_content(dialog_quickform($href,$dialogdef));
    return TRUE;
} // htmlpage_show_edit()


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
function htmlpage_save(&$output,$area_id,$node_id,$module,$viewonly,&$edit_again) {
    global $USER;
    if ((isset($_POST['button_cancel'])) || ($viewonly)) {
        $edit_again = FALSE;
        $retval = FALSE;
    } else {
        $edit_again = TRUE;
        if (!($viewonly)) {
            $where = array('node_id' => intval($node_id));
            $order = array('version DESC');
            $record = db_select_single_record('htmlpages','*',$where,$order);
            if ($record !== FALSE) {
                $now = strftime('%Y-%m-%d %T');
                $fields = array(
                    'version' => intval($record['version']) + 1,
                    'page_data' => magic_unquote($_POST['htmlpage_content']),
                    'mtime' => $now,
                    'muser_id' => $USER->user_id);
                $where = array('htmlpage_id' => $record['htmlpage_id']);
                $retval = db_update('htmlpages',$fields,$where);
            } else {
                $retval = FALSE;
            }
        } else {
            $retval = FALSE;
        }
    }
    return $retval;
} // htmlpage_save()


?>