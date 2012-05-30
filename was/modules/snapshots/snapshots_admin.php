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

/** /program/modules/snapshots/snapshots_admin.php - management interface for snapshots-module
 *
 * This file defines the administrative interface to this module.
 * The interface consists of the following four functions.
 *
 * <code>
 * snapshots_disconnect(&$output,$area_id,$node_id,$module)
 * snapshots_connect(&$output,$area_id,$node_id,$module)
 * snapshots_show_edit(&$output,$area_id,$node_id,$module,$viewonly,$edit_again,$href)
 * snapshots_save(&$output,$area_id,$node_id,$module,$viewonly,&$edit_again)
 * </code>
 *
 * These functions are called from pagemanagerlib.php whenever necessary.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_snapshots
 * @version $Id: snapshots_admin.php,v 1.1 2012/05/30 12:47:17 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** disconnect this module from a node
 *
 * this breaks the link between the node $node_id in area $area_id and this module.
 * For now we simply delete the record with the snapshots variant + introduction. 
 * 
 * @param object &$output collects the html output (if any)
 * @param int $area_id the area in which $node_id resides
 * @param int $node_id the node from which we need to disconnect
 * @param array $module the module record straight from the database
 * @return bool TRUE on success, FALSE otherwise
 */
function snapshots_disconnect(&$output,$area_id,$node_id,$module) {
    $where = array('node_id' => intval($node_id));
    $retval = db_delete('snapshots',$where);
    return ($retval === FALSE) ? FALSE : TRUE;
} // snapshots_disconnect()


/** connect this module to a node
 *
 * this makes the link between the node $node_id in area $area_id and this module.
 * In this case we simply link a single 'variant' parameter to node $node_id in a
 * 1-to-1 relation.
 *
 * Note that we set the parameter 'variant' to 1. This equates to the variant
 * where the visitor starts with the title, the optional introductory text and the
 * thumbnail overview. It is up to the user to configure the node to use other
 * variants, eg. start at the first picture full-size or display a slide show.
 * Also note that we start off with an (arbitrary) dimension for the full-size
 * snapshots. This is a per-node setting (as opposed to the systemwide setting
 * for thumbnail dimensions).
 * Finally, we do a little heuristics here by plugging in the current directory
 * from the filemanager. This is dirty, but we might assume that the user
 * uploaded the files to a directory just before adding this snapshots node.
 * In that case there is no need to change anything re: path.
 * If the user did NOT upload files, we plug in the name of the directory
 * which is associated with area $area_id.
 * 
 * @param object &$output collects the html output (if any)
 * @param int $area_id the area in which $node_id resides
 * @param int $node_id the node to which we need to connect
 * @param array $module the module record straight from the database
 * @return bool TRUE on success, FALSE otherwise
 */
function snapshots_connect(&$output,$area_id,$node_id,$module) {
    global $USER;
    $now = strftime('%Y-%m-%d %T');
    $areas = get_area_records();
    $path = (isset($_SESSION['current_directory'])) ? $_SESSION['current_directory'] : 
            ((isset($areas[$area_id])) ? '/areas/'.$areas[$area_id]['path'] : '');
    unset($areas);

    $fields = array(
        'node_id'        => intval($node_id),
        'header'         => '',
        'introduction'   => '',
        'snapshots_path' => $path,
        'variant'        => 1,
        'dimension'      => 512,
        'ctime'          => $now,
        'cuser_id'       => $USER->user_id,
        'mtime'          => $now,
        'muser_id'       => $USER->user_id);
    $retval = db_insert_into('snapshots',$fields);
    if ($retval !== 1) {
        logger(sprintf('%s(): cannot connect snapshots to node \'%d\': %s',__FUNCTION__,$node_id,db_errormessage()));
        $retval = FALSE;
    } else {
        $retval = TRUE;
    }
    return $retval;
} // snapshots_connect()


/** present the user with a dialog to modify the snapshots that are connected to node $node_id
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
 * @todo we might want to jazz up this dialog by adding some sort of 'directory browser'
 *       for the snapshots_path field using a pop-up window. Mmmmm.... future refinements...
 */
function snapshots_show_edit(&$output,$area_id,$node_id,$module,$viewonly,$edit_again,$href) {
    $dialogdef = snapshots_get_dialogdef($viewonly);
    if ($edit_again) {
        // retrieve and validate the POSTed values
        dialog_validate($dialogdef); // no need to show messages; we did that alread in snapshots_save() below
        snapshots_check_path($dialogdef['snapshots_path'],$area_id,$node_id);
    } else {
        // make a fresh start with data from the database
        $where = array('node_id' => intval($node_id));
        if (($record = db_select_single_record('snapshots','*',$where)) === FALSE) {
            logger(sprintf('%s(): error retrieving snapshots configuration: %s',__FUNCTION__,db_errormessage()));
        } else {
            foreach($dialogdef as $name => $item) {
                switch($name) {
                    case 'header':
                    case 'introduction':
                    case 'snapshots_path':
                    case 'variant':
                    case 'dimension':
                        $dialogdef[$name]['value'] = strval($record[$name]);
                        break;
                }
            }
        }
    }
    $output->add_content('<h2>'.t('snapshots_content_header','m_snapshots').'</h2>');
    $output->add_content(t('snapshots_content_explanation','m_snapshots'));
    $output->add_content(dialog_quickform($href,$dialogdef));
    return TRUE;
} // snapshots_show_edit()


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
function snapshots_save(&$output,$area_id,$node_id,$module,$viewonly,&$edit_again) {
    global $USER;

    // 1 -- bail out if cancelled or viewonly
    if ((isset($_POST['button_cancel'])) || ($viewonly)) {
        $edit_again = FALSE;
        return FALSE;
    }

    // 2 -- redo if invalid data was submitted
    $invalid = FALSE;
    $dialogdef = snapshots_get_dialogdef($viewonly);
    if (!dialog_validate($dialogdef)) {
        $invalid = TRUE;
    }
    if (!snapshots_check_path($dialogdef['snapshots_path'],$area_id,$node_id)) {
        $invalid = TRUE;
    }
    if ((isset($dialogdef['snapshots_path']['warnings'])) && ($dialogdef['snapshots_path']['warnings'] > 0)) {
        $output->add_message($dialogdef['snapshots_path']['warning_messages']);
    }
    // show errors to the user and redo
    if ($invalid) {
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
    $table = 'snapshots';
    $fields = array(
        'header'         => $dialogdef['header']['value'],
        'introduction'   => $dialogdef['introduction']['value'],
        'snapshots_path' => $dialogdef['snapshots_path']['value'],
        'variant'        => $dialogdef['variant']['value'],
        'dimension'      => intval($dialogdef['dimension']['value']),
        'mtime'          => $now,
        'muser_id'       => $USER->user_id);
    $where = array('node_id' => intval($node_id));
    if (db_update($table,$fields,$where) === FALSE) {
        logger(sprintf('%s(): error saving config value: %s',__FUNCTION__,db_errormessage()));
        $edit_again = TRUE;
        return FALSE;
    } else {
        return TRUE; // $edit_again is a don't care
    }
} // snapshots_save()


/** construct a dialog definition for the snapshots configuration data
 *
 * @param int $viewonly if TRUE the Save button is not displayed and config values cannot be changed
 * @return array dialog definition
 */
function snapshots_get_dialogdef($viewonly) {
    $options = array(
        '1' => array('option' => t('variant_thumbs_label', 'm_snapshots'),'title' => t('variant_thumbs_title', 'm_snapshots')),
        '2' => array('option' => t('variant_first_label','m_snapshots'),'title' => t('variant_first_title','m_snapshots')),
        '3' => array('option' => t('variant_slideshow_label', 'm_snapshots'),'title' => t('variant_slideshow_title', 'm_snapshots')));
    $dialogdef = array(
        'header' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'header',
            'minlength' => 0,
            'maxlength' => 240,
            'columns' => 30,
            'label' => t('header_label','m_snapshots'),
            'title' => t('header_title','m_snapshots'),
            'value' => '',
            ),
        'introduction' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'introduction',
            'minlength' => 0,
            'maxlength' => 32768, // arbitrary; 32 kB
            'columns' => 50,
            'rows' => 10,
            'label' => t('introduction_label','m_snapshots'),
            'title' => t('introduction_title','m_snapshots'),
            'value' => '',
            ),
        'snapshots_path' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'snapshots_path',
            'minlength' => 0,
            'maxlength' => 240,
            'columns' => 50,
            'label' => t('snapshots_path_label','m_snapshots'),
            'title' => t('snapshots_path_title','m_snapshots'),
            'value' => '',
            ),
        'variant' => array(
            'type' => F_RADIO,
            'name' => 'variant',
            'value' => 1,
            'options' => $options,
            'viewonly' => $viewonly,
            'title' => t('variant_title','m_snapshots'),
            'label' => t('variant_label','m_snapshots')
            ),
        'dimension' => array(
            'type' => F_INTEGER,
            'name' => 'dimension',
            'columns' => 10,
            'maxlength' => 10,
            'minvalue' => 10,
            'maxvalue' => 9999, // arbitrary but seems sane limit given current TFT's with 1920x1200 max
            'viewonly' => $viewonly,
            'title' => t('dimension_title','m_snapshots'),
            'label' => t('dimension_label','m_snapshots')
            )
        );
    if (!$viewonly) {
        $dialogdef['button_save'] = dialog_buttondef(BUTTON_SAVE);
    }
    $dialogdef['button_cancel'] = dialog_buttondef(BUTTON_CANCEL);
    return $dialogdef;
} // snapshots_get_dialogdef()


/** validate and massage the user-supplied data path
 *
 * this checks the directory path the user entered,
 * returns TRUE if the tests are passed.
 * 
 * There three places from which snapshots can be retrieved:
 *  - /areas/aaa
 *  - /users/uuu
 *  - /groups/ggg
 * 
 * That is: the path should at least contain 2 levels (and possibly more).
 * In other words: a bare '/' is not enough and neither are bare '/areas',
 * '/users' or '/groups'. And of course the directory should already exist
 * in the file systen under $CFG->datadir.
 * 
 * Various tests are done:
 * - the selected area directory must be active
 * - if the selected area is private,
 *     $USER must have intranet access for this area, OR
 *     the selected area must be the same as the area in which $node_id resides
 * - the selected user directory must be the $USER's, OR
 *   the $USER has access to the account manager (able to manipulate ALL users' directories)
 * - the selected group directory must be from a group the $USER is a member of, OR
 *   the $USER has access to the account manager (able to manipulate ALL groups' directories)
 * 
 * If all tests succeed, we may want to warn the user in the case that the
 * file location is in a different (and public) area than the node holding the snapshots module.
 * However, this is a warning only.
 *
 * Finally, we reconstruct the path in such a way that it starts with a slash
 * and does NOT end with a slash. This is done by changing the content of the $item parameter.
 * 
 * @param array &$item holds the field definition from the $dialogdef for the snapshots_path
 * @param int $area_id the area in which we are editing a snapshot module configuration
 * @param int $node_id the node to which the snapshot module is connected (unused)
 * @return bool TRUE if valid path, otherwise FALSE + messages in dialogdef
 * @todo should the user / group really be active here? If not, the images will fail in file.php
 *       but that may leak information about inactive users. Hmmm...
 * @todo we should use a different error message as soon as it is available in was.php,
 *       eg. 'validate_bad_directory' (much like 'validate_bad_filename').
 */
function snapshots_check_path(&$item,$area_id,$node_id) {
    global $USER,$CFG;
    $warning = '';
    $invalid = FALSE;
    $path_components = explode('/',trim(strtr($item['value'],'\\','/'),'/'));
    if ((sizeof($path_components) < 2) || (in_array('..',$path_components))) {
        $invalid = TRUE;
    } else {
        switch($path_components[0]) {
        case 'areas':
            $fields = array('area_id','is_private', 'title');
            $where = array('is_active' => TRUE, 'path' => $path_components[1]);
            $table = 'areas';
            if (($record = db_select_single_record($table,$fields,$where)) === FALSE) {
                // area doesn't exist or is inactive
                $invalid = TRUE;
            } elseif (db_bool_is(TRUE,$record['is_private'])) {
                // specified area is private
                if ((intval($record['area_id']) != $area_id) ||
                    (!$USER->has_intranet_permissions(ACL_ROLE_INTRANET_ACCESS,$record['area_id']))) {
                    // this private area is NOT the one where $node_id resides OR this user is denied access
                    $invalid = TRUE;
                }
            } else {
                // specified area is public
                if (intval($record['area_id']) != $area_id) {
                    // but it is not the same as the one where $node_id resides: go warn user eventually!
                    $params = array('{AREANAME}' => htmlspecialchars($record['title']));
                    $warning = t('warning_different_area','m_snapshots',$params);
                 }
            }
            break;
        case 'users':
            if ((!$USER->has_job_permissions(JOB_PERMISSION_ACCOUNTMANAGER)) && ($path_components[1] != $USER->path)) {
                $invalid = TRUE;
            }
            if ($path_components[1] == $USER->path) {
                $warning =  t('warning_personal_directory','m_snapshots');
            }
            break;
        case 'groups':
            if (!$USER->has_job_permissions(JOB_PERMISSION_ACCOUNTMANAGER)) {
                $usergroups = get_user_groups($USER->user_id);
                $is_member = FALSE;
                foreach($usergroups as $group_id => $usergroup) {
                    if ($usergroup['path'] == $path_components[1]) {
                        $is_member = TRUE;
                        break;
                    }
                }
                if (!$is_member) {
                    $invalid = TRUE;
                }
            }
            break;
        default:
             $invalid = TRUE;
             break;
        }
    }
    if (!$invalid) {
        $path = '/'.implode('/',$path_components);
        if (!is_dir($CFG->datadir.$path)) {
            $invalid = TRUE;
        }
    }
    if ($invalid) {
        $fname = str_replace('~','',$item['label']);
        $params = array('{PATH}' => htmlspecialchars($item['value']));
        $error_message = sprintf('%s: %s',$fname,t('invalid_path','admin',$params));
        ++$item['errors'];
        $item['error_messages'][] = $error_message;
        return FALSE;
    }
    if ($warning != '') {
        $item['warnings'] = 0;
        ++$item['warnings'];
        $item['warning_messages'] = $warning;
    }
    $item['value'] = $path;
    return TRUE;
} // snapshots_check_path()

?>