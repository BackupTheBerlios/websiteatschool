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

/** /program/modules/guestbook/guestbook_admin.php - management interface for module
 *
 * This file defines the administrative interface to this module.
 * The interface consists of the following four functions.
 *
 * <code>
 * guestbook_disconnect(&$output,$area_id,$node_id,$module)
 * guestbook_connect(&$output,$area_id,$node_id,$module)
 * guestbook_show_edit(&$output,$area_id,$node_id,$module,$viewonly,$edit_again,$href)
 * guestbook_save(&$output,$area_id,$node_id,$module,$viewonly,&$edit_again)
 * </code>
 *
 * These functions are called from pagemanagerlib.php whenever necessary.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_guestbook
 * @version $Id: guestbook_admin.php,v 1.4 2013/06/11 11:25:19 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** disconnect this module from a node
 *
 * this breaks the link between the node $node_id in area $area_id and this module.
 * It depends on the module what this function should do. Often it simply boils
 * down to a no-op returning TRUE to indicate success.
 * 
 * @param object &$output collects the html output (if any)
 * @param int $area_id the area in which $node_id resides
 * @param int $node_id the node from which we need to disconnect
 * @param array $module the module record straight from the database
 * @return bool TRUE on success, FALSE otherwise
 */
function guestbook_disconnect(&$output,$area_id,$node_id,$module) {
    $output->add_message('STUB: '.__FUNCTION__.'() in '.__FILE__.' ('.__LINE__.')');
    // foreach($module as $k => $v) {$output->add_message("module[$k] => $v"); }
    return TRUE;
} // guestbook_disconnect()


/** connect this module to a node
 *
 * this makes the link between the node $node_id in area $area_id and this module.
 * It depends on the module what this function should do. Often it simply boils
 * down to a no-op returning TRUE to indicate success.
 * 
 * @param object &$output collects the html output (if any)
 * @param int $area_id the area in which $node_id resides
 * @param int $node_id the node to which we need to connect
 * @param array $module the module record straight from the database
 * @return bool TRUE on success, FALSE otherwise
 */
function guestbook_connect(&$output,$area_id,$node_id,$module) {
    $output->add_message('STUB: '.__FUNCTION__.'() in '.__FILE__.' ('.__LINE__.')');
    // foreach($module as $k => $v) {$output->add_message("module[$k] => $v"); }
    return TRUE;
} // guestbook_connect()


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
function guestbook_show_edit(&$output,$area_id,$node_id,$module,$viewonly,$edit_again,$href) {
    $output->add_content('<h2>STUB - Edit Content Dialog ('.$module['name'].')</h2>');
    $output->add_content('STUB: '.__FUNCTION__.'() in '.__FILE__.' ('.__LINE__.')'.
    "<br>viewonly = $viewonly<br>edit_again = $edit_again<br>href = $href<p>\n".
    "Note that this is just a stub with two buttons. It really does nothing but exercise the module interface.<p>\n");

    $dialogdef = array(
        array(
            'type' => F_CHECKBOX,
            'name' => 'fail',
            'options' => array(1 => 'STUB: Check the bo~x to let the save routine fail'),
            'title' => 'STUB - check the box to test failure of the save routine()',
            ),

        dialog_buttondef(BUTTON_SAVE),
        dialog_buttondef(BUTTON_CANCEL)
        );
    $output->add_content(dialog_quickform($href,$dialogdef));
    return TRUE;
} // guestbook_show_edit()


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
 * @param int $node_id the node to which we need to connect
 * @param array $module the module record straight from the database
 * @param bool $viewonly if TRUE, editing and hence saving is not allowed
 * @param bool $edit_again set to TRUE if we need to edit the content again, FALSE otherwise
 * @return bool TRUE on success + output stored via $output, FALSE otherwise
 */
function guestbook_save(&$output,$area_id,$node_id,$module,$viewonly,&$edit_again) {
    if (isset($_POST['button_cancel'])) {
        $edit_again = FALSE;
        $retval = FALSE;
    } else {
        $edit_again = TRUE;
        $retval = (!isset($_POST['fail']));
        $message = ($retval) ? 'STUB: SUCCESS!' : 'STUB: FAIL!';
        $message .= ' ('.$module['name'].')';
        $output->add_message($message);
    }
    return $retval;
} // guestbook_save()


?>