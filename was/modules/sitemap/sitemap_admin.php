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

/** /program/modules/sitemap/sitemap_admin.php - management interface for sitemap-module
 *
 * This file defines the administrative interface to this module.
 * The interface consists of the following four functions.
 *
 * <code>
 * sitemap_disconnect(&$output,$area_id,$node_id,$module)
 * sitemap_connect(&$output,$area_id,$node_id,$module)
 * sitemap_show_edit(&$output,$area_id,$node_id,$module,$viewonly,$edit_again,$href)
 * sitemap_save(&$output,$area_id,$node_id,$module,$viewonly,&$edit_again)
 * </code>
 *
 * These functions are called from pagemanagerlib.php whenever necessary.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_sitemap
 * @version $Id: sitemap_admin.php,v 1.4 2013/06/11 11:25:30 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** disconnect this module from a node
 *
 * this breaks the link between the node $node_id in area $area_id and this module.
 * For now we simply delete the record with the sitemap scope. 
 * 
 * @param object &$output collects the html output (if any)
 * @param int $area_id the area in which $node_id resides
 * @param int $node_id the node from which we need to disconnect
 * @param array $module the module record straight from the database
 * @return bool TRUE on success, FALSE otherwise
 */
function sitemap_disconnect(&$output,$area_id,$node_id,$module) {
    $where = array('node_id' => intval($node_id));
    $retval = db_delete('sitemaps',$where);
    return ($retval === FALSE) ? FALSE : TRUE;
} // sitemap_disconnect()


/** connect this module to a node
 *
 * this makes the link between the node $node_id in area $area_id and this module.
 * In this case we simply link a single 'scope' parameter to node $node_id in a
 * 1-to-1 relation.
 *
 * Note that we set the parameter 'scope' to 0. This implies a 'small' map.
 * It is up to the user to configure the node to use medium (scope=1) or large (scope=2) map.
 * 
 * @param object &$output collects the html output (if any)
 * @param int $area_id the area in which $node_id resides
 * @param int $node_id the node to which we need to connect
 * @param array $module the module record straight from the database
 * @return bool TRUE on success, FALSE otherwise
 */
function sitemap_connect(&$output,$area_id,$node_id,$module) {
    global $USER;
    $now = strftime('%Y-%m-%d %T');
    $fields = array(
        'node_id' => intval($node_id),
        'header' => '',
        'introduction' => '',
        'scope' => 0,
        'ctime' => $now,
        'cuser_id' => $USER->user_id,
        'mtime' => $now,
        'muser_id' => $USER->user_id);
    $retval = db_insert_into('sitemaps',$fields);
    if ($retval !== 1) {
        logger(sprintf('%s(): cannot connect sitemap to node \'%d\': %s',__FUNCTION__,$node_id,db_errormessage()));
        $retval = FALSE;
    } else {
        $retval = TRUE;
    }
    return $retval;
} // sitemap_connect()


/** present the user with a dialog to modify the sitemap that is connected to node $node_id
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
function sitemap_show_edit(&$output,$area_id,$node_id,$module,$viewonly,$edit_again,$href) {
    $dialogdef = sitemap_get_dialogdef($viewonly);
    if ($edit_again) {
        // retrieve and validate the POSTed values
        dialog_validate($dialogdef); // no need to show messages; we did that alread in sitemap_save() bel0w
    } else {
        // make a fresh start with data from the database
        $where = array('node_id' => intval($node_id));
        if (($record = db_select_single_record('sitemaps','*',$where)) === FALSE) {
            logger(sprintf('%s(): error retrieving sitemap configuration: %s',__FUNCTION__,db_errormessage()));
        } else {
            foreach($dialogdef as $name => $item) {
                switch($name) {
                    case 'header':
                    case 'introduction':
                    case 'scope':
                        $dialogdef[$name]['value'] = strval($record[$name]);
                        break;
                }
            }
        }
    }
    $output->add_content('<h2>'.t('sitemap_content_header','m_sitemap').'</h2>');
    $output->add_content(t('sitemap_content_explanation','m_sitemap'));
    $output->add_content(dialog_quickform($href,$dialogdef));
    return TRUE;
} // sitemap_show_edit()


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
function sitemap_save(&$output,$area_id,$node_id,$module,$viewonly,&$edit_again) {
    global $USER;

    // 1 -- bail out if cancelled or viewonly
    if ((isset($_POST['button_cancel'])) || ($viewonly)) {
        $edit_again = FALSE;
        return FALSE;
    }

    // 2 -- redo if invalid data was submitted
    $dialogdef = sitemap_get_dialogdef($viewonly);
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
    $table = 'sitemaps';
    $fields = array(
        'header'       => $dialogdef['header']['value'],
        'introduction' => $dialogdef['introduction']['value'],
        'scope'        => $dialogdef['scope']['value'],
        'mtime'        => $now,
        'muser_id'     => $USER->user_id);
    $where = array('node_id' => intval($node_id));
    if (db_update($table,$fields,$where) === FALSE) {
        logger(sprintf('%s(): error saving config value: %s',__FUNCTION__,db_errormessage()));
        $edit_again = TRUE;
        return FALSE;
    } else {
        return TRUE; // $edit_again is a don't care
    }
} // sitemap_save()


/** construct a dialog definition for the sitemap 'scope' value
 *
 * @param int $viewonly if TRUE the Save button is not displayed and config values cannot be changed by the user
 * @return array dialog definition
 */
function sitemap_get_dialogdef($viewonly) {
    $options = array(
        '0' => array('option' => t('scope_small_label', 'm_sitemap'),'title' => t('scope_small_title', 'm_sitemap')),
        '1' => array('option' => t('scope_medium_label','m_sitemap'),'title' => t('scope_medium_title','m_sitemap')),
        '2' => array('option' => t('scope_large_label', 'm_sitemap'),'title' => t('scope_large_title', 'm_sitemap')));
    $dialogdef = array(
        'header' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'header',
            'minlength' => 0,
            'maxlength' => 240,
            'columns' => 30,
            'label' => t('header_label','m_sitemap'),
            'title' => t('header_title','m_sitemap'),
            'value' => '',
            ),
        'introduction' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'introduction',
            'minlength' => 0,
            'maxlength' => 32768, // arbitrary; 32 kB
            'columns' => 50,
            'rows' => 10,
            'label' => t('introduction_label','m_sitemap'),
            'title' => t('introduction_title','m_sitemap'),
            'value' => '',
            ),
        'scope' => array(
            'type' => F_RADIO,
            'name' => 'scope',
            'value' => 0,
            'options' => $options,
            'viewonly' => $viewonly,
            'title' => t('scope_title','m_sitemap'),
            'label' => t('scope_label','m_sitemap')
            )
        );
    if (!$viewonly) {
        $dialogdef['button_save'] = dialog_buttondef(BUTTON_SAVE);
    }
    $dialogdef['button_cancel'] = dialog_buttondef(BUTTON_CANCEL);
    return $dialogdef;
} // sitemap_get_dialogdef()


?>