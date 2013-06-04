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

/** /program/modules/crew/crew_admin.php - management interface for crew-module
 *
 * This file defines the administrative interface to this module.
 * The interface consists of the following four functions.
 *
 * <code>
 * crew_disconnect(&$output,$area_id,$node_id,$module)
 * crew_connect(&$output,$area_id,$node_id,$module)
 * crew_show_edit(&$output,$area_id,$node_id,$module,$viewonly,$edit_again,$href)
 * crew_save(&$output,$area_id,$node_id,$module,$viewonly,&$edit_again)
 * </code>
 *
 * These functions are called from pagemanagerlib.php whenever necessary.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_crew
 * @version $Id: crew_admin.php,v 1.2 2013/06/04 09:56:13 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

define('CREW_PERMISSION_READ', 1);
define('CREW_PERMISSION_WRITE',2);
define('CREW_ACL_ROLE_READONLY', CREW_PERMISSION_READ);
define('CREW_ACL_ROLE_READWRITE',CREW_PERMISSION_READ | CREW_PERMISSION_WRITE);


/** disconnect this module from a node
 *
 * this breaks the link between the node $node_id in area $area_id and this module.
 * For now we simply delete the relevant record from the workshops table. Also
 * we remove any record which might exist in the ACL table
 * acls_modules_nodes for this particular node.
 * 
 * @param object &$output collects the html output (if any)
 * @param int $area_id the area in which $node_id resides
 * @param int $node_id the node from which we need to disconnect
 * @param array $module the module record straight from the database
 * @return bool TRUE on success, FALSE otherwise
 */
function crew_disconnect(&$output,$area_id,$node_id,$module) {
    $where = array('node_id' => intval($node_id));
    $retval = db_delete('workshops',$where);
    $where['module_id'] = intval($module['module_id']);
    if (db_delete('acls_modules_nodes',$where) === FALSE) {
        $retval = FALSE;
    }
    return ($retval === FALSE) ? FALSE : TRUE;
} // crew_disconnect()


/** connect this module to a node
 *
 * this makes the link between the node $node_id in area $area_id and this module.
 * In this case we simply link a single workshops record to node $node_id in a
 * 1-to-1 relation. Any permissions (ACL) can be stored in the acls_modules_node
 * table later but by default we start afresh with just a bare workshops record.
 *
 * Note that we set the parameter 'visibility' to 0. This implies a 
 * workshop that is only visible for the individual accounts that are
 * explicitly allowed to view and/or edit. It is up to the user to
 * configure the workshop in a different way, e.g allow all accounts
 * to view the workshop results (visibility=1) or allow the world
 * to see the results (visibility=2).
 * 
 * @param object &$output collects the html output (if any)
 * @param int $area_id the area in which $node_id resides
 * @param int $node_id the node to which we need to connect
 * @param array $module the module record straight from the database
 * @return bool TRUE on success, FALSE otherwise
 */
function crew_connect(&$output,$area_id,$node_id,$module) {
    global $USER;
    $now = strftime('%Y-%m-%d %T');
    $fields = array(
        'node_id' => intval($node_id),
        'header' => '',
        'introduction' => '',
        'visibility' => 0,
        'document' => '',
        'ctime' => $now,
        'cuser_id' => $USER->user_id,
        'mtime' => $now,
        'muser_id' => $USER->user_id);
    $retval = db_insert_into('workshops',$fields);
    if ($retval !== 1) {
        logger(sprintf('%s(): cannot connect workshop to node \'%d\': %s',__FUNCTION__,$node_id,db_errormessage()));
        $retval = FALSE;
    } else {
        $retval = TRUE;
    }
    return $retval;
} // crew_connect()


/** present the user with a dialog to modify the workshop that is connected to node $node_id
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
function crew_show_edit(&$output,$area_id,$node_id,$module,$viewonly,$edit_again,$href) {
    global $USER;
    $module_id = intval($module['module_id']);
    $dialogdef = crew_get_dialogdef($output,$viewonly,$module_id,$area_id,$node_id,$USER->user_id);
    if ($edit_again) { // retrieve and (again) validate the POSTed values
        dialog_validate($dialogdef); // no need to show messages; we did that alread in crew_save() below
    }
    $output->add_content('<h2>'.t('crew_content_header','m_crew').'</h2>');
    $output->add_content(t('crew_content_explanation','m_crew'));

    // Manually construct the form because of embedded HTML-table
    $in_table = FALSE;
    $postponed = array();
    $oddeven = 'even';
    $output->add_content(html_form($href));
    foreach($dialogdef as $name => $item) {
        // this always works because the last item is not an acl field
        if (($in_table) && (substr($name,0,3) != 'acl')) {
            $output->add_content(html_table_close());
            $in_table = FALSE;
        }
        if ((!$in_table) && (substr($name,0,3) == 'acl')) {
            $output->add_content(html_table(array('class' => 'acl_form')));
            $in_table = TRUE;
        }
        if (substr($name,0,3) == 'acl') {
            $oddeven = ($oddeven == 'even') ? 'odd' : 'even';
            $attributes = array('class' => $oddeven);
            $output->add_content('  '.html_table_row($attributes));
            $output->add_content('    '.html_table_cell($attributes,dialog_get_label($item)));
            $widget = dialog_get_widget($item);
            if (is_array($widget)) {
                $output->add_content('    '.html_table_cell($attributes));
                // add every radio button on a separate line
                $postfix = ($item['type'] == F_RADIO) ? '<br>' : '';
                foreach ($widget as $widget_line) {
                    $output->add_content('      '.$widget_line.$postfix);
                }
                $output->add_content('    '.html_table_cell_close());
            } else {
                $output->add_content('    '.html_table_cell($attributes,$widget));
            }
            $output->add_content('  '.html_table_row_close());
        } else {
            if ($item['type'] == F_SUBMIT) {
                $postponed[$name] = $item;
            } else {
                $output->add_content('<p>');
                $output->add_content(dialog_get_label($item).'<br>');
                $widget = dialog_get_widget($item);
                if (is_array($widget)) {
                    // add every radio button on a separate line
                    $postfix = ($item['type'] == F_RADIO) ? '<br>' : '';
                    foreach ($widget as $widget_line) {
                        $output->add_content($widget_line.$postfix);
                    }
                } else {
                    $output->add_content($widget);
                }
            }
        }
    }
    foreach($postponed as $item) {
        $output->add_content(dialog_get_widget($item));
    }
    $output->add_content('<p>');
    $output->add_content(html_form_close());
    return TRUE;
} // crew_show_edit()


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
function crew_save(&$output,$area_id,$node_id,$module,$viewonly,&$edit_again) {
    global $USER;
    $retval = TRUE; // assume success
    $module_id = intval($module['module_id']);
    $node_id = intval($node_id);

    // 1 -- bail out if cancelled or viewonly
    if ((isset($_POST['button_cancel'])) || ($viewonly)) {
        $edit_again = FALSE;
        return FALSE;
    }

    // 2 -- redo if invalid data was submitted
    $dialogdef = crew_get_dialogdef($output,$viewonly,$module_id,$area_id, $node_id,$USER->user_id);
    if (!dialog_validate($dialogdef)) {
        // there were errors, show them to the user and ask caller to do it again
        foreach($dialogdef as $k => $item) {
            if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                $output->add_message($item['error_messages']);
            }
        }
        $edit_again = TRUE;
        return FALSE;
    }

    // 3 -- actually save the new (plain) settings (always)
    $now = strftime('%Y-%m-%d %T');
    $table = 'workshops';
    $fields = array(
        'header'       => $dialogdef['header']['value'],
        'introduction' => $dialogdef['introduction']['value'],
        'visibility'   => $dialogdef['visibility']['value'],
        'mtime'        => $now,
        'muser_id'     => $USER->user_id);
    $where = array('node_id' => intval($node_id));
    if (db_update($table,$fields,$where) === FALSE) {
        logger(sprintf('%s(): error saving config value: %s',__FUNCTION__,db_errormessage()));
        $edit_again = TRUE;
        $retval = FALSE;
    }
    // 4 -- save or delete the changed ACLs and maybe add new ones too
    $table = 'acls_modules_nodes';
    $where = array('acl_id' => 0, 'node_id' => $node_id, 'module_id' => $module_id);
    $fields = $where;
    foreach($dialogdef as $k => $item) {
        if (!isset($item['acl_id'])) {
            continue;
        }
        $acl_id = intval($item['acl_id']);
        $value = intval($item['value']);
        $old_value = intval($item['old_value']);
        $dbretval = TRUE; // assume success
        if ($value != 0) {
            if (is_null($item['old_value'])) { // need to add a new record
                $fields['permissions_modules'] = $value;
                $fields['acl_id'] = $acl_id;
                $dbretval = db_insert_into($table,$fields);
            } else if ($value != $old_value) { // need to update existing record
                $where['acl_id'] = $acl_id;
                $dbretval = db_update($table,array('permissions_modules' => $value),$where);
            }
        } else if (!is_null($item['old_value'])) { // delete existing record because the value is now 0
            $where['acl_id'] = $acl_id;
            $dbretval = db_delete($table,$where);
        }
        if ($dbretval === FALSE) {
            $messages[] = __FUNCTION__.'(): '.db_errormessage();
            $edit_again = TRUE;
            $retval = FALSE;
        }
    }
    return $retval;
} // crew_save()


/** construct a dialog definition for the workshop configuration
 *
 * this generates an array which defines the dialog for workshop configuration.
 * There are a few plain fields that simply go into the appropriate workshops
 * record and the save and cancel button. However, there may also be items
 * related to ACLs. These fields are used to define the user's roles and the
 * should be presented in a table. We abuse the field names for this purpose:
 * if the first 3 characters are 'acl' we put the widgets in an HTML-table, otherwise
 * it is just an ordinary widget.
 *
 * Note that in the case of a single simple user without any aquaintances (ie. a user
 * that is not member of any group) the user is not able to add herself to the list
 * of authorised users. What is the point of having a _collaborative_ workshop when
 * you are the only one to collaborate with? (However, it would be fairly easy to force/add
 * an entry for this user if the tmp table would turn out empty. Maybe later....?)

 * @param object &$output collects the html output (if any)
 * @param int $viewonly if TRUE the Save button is not displayed and values cannot be changed
 * @param int $module_id indicates the id of the crew module in the database (needed for ACL)
 * @param int $area_id indicates the area where node_id lives (needed for ACL)
 * @param int $node_id indicates which page we are loooking at (needed for ACL)
 * @param int $user_id indicates the current user (needed for ACL)
 * @return array dialog definition
 */
function crew_get_dialogdef(&$output,$viewonly,$module_id,$area_id,$node_id,$user_id) {
    global $DB, $USER;

    static $dialogdef = NULL;
    if (!is_null($dialogdef)) { // recycle
        return $dialogdef;
    }
    $visibilities = array(
        '2' => array('option' => t('visibility_world_label',  'm_crew'),
                     'title'  => t('visibility_world_title',  'm_crew')),
        '1' => array('option' => t('visibility_all_label',    'm_crew'),
                     'title'  => t('visibility_all_title',    'm_crew')),
        '0' => array('option' => t('visibility_workers_label','m_crew'),
                     'title'  => t('visibility_workers_title','m_crew')));
    $roles = array(
        ACL_ROLE_NONE => array(
                'option' => t('acl_role_none_option','admin'),
                'title' => t('acl_role_none_title','admin')
                ),
        CREW_ACL_ROLE_READONLY => array(
                'option' => t('crew_acl_role_readonly_option','m_crew'),
                'title' => t('crew_acl_role_readonly_title','m_crew')
                ),
        CREW_ACL_ROLE_READWRITE => array(
                'option' => t('crew_acl_role_readwrite_option','m_crew'),
                'title' => t('crew_acl_role_readwrite_title','m_crew')
                ),
        ACL_ROLE_GURU => array(
                'option' => t('acl_role_guru_option','admin'),
                'title' => t('acl_role_guru_title','admin')
                )
        );

    // 1 -- plain & simple fields
    // make a fresh start with data from the database
    $dialogdef = array(
        'header' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'header',
            'minlength' => 0,
            'maxlength' => 240,
            'columns' => 30,
            'label' => t('header_label','m_crew'),
            'title' => t('header_title','m_crew'),
            'viewonly' => $viewonly,
            'value' => '',
            'old_value' => ''
            ),
        'introduction' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'introduction',
            'minlength' => 0,
            'maxlength' => 32768, // arbitrary; 32 kB
            'columns' => 50,
            'rows' => 10,
            'label' => t('introduction_label','m_crew'),
            'title' => t('introduction_title','m_crew'),
            'viewonly' => $viewonly,
            'value' => '',
            'old_value' => ''
            ),
        'visibility' => array(
            'type' => F_RADIO,
            'name' => 'visibility',
            'value' => 0,
            'old_value' => 0,
            'options' => $visibilities,
            'viewonly' => $viewonly,
            'title' => t('visibility_title','m_crew'),
            'label' => t('visibility_label','m_crew')
            )
        );
    $table = 'workshops';
    $fields = array('header','introduction','visibility');
    $where = array('node_id' => intval($node_id));
    if (($record = db_select_single_record($table, $fields, $where)) === FALSE) {
        logger(sprintf('%s(): error retrieving CREW configuration: %s',__FUNCTION__,db_errormessage()));
        $output->add_message(t('error_retrieving_data','admin'));
    } else {
        foreach($record as $name => $value) {
            $dialogdef[$name]['value'] = $dialogdef[$name]['old_value'] = $value;
        }
    }

    $sql = sprintf('DROP TEMPORARY TABLE IF EXISTS %screw_tmp',$DB->prefix);
    $retval = $DB->exec($sql);
    if ($USER->has_job_permissions(JOB_PERMISSION_ACCOUNTMANAGER)) {
        // Allow $USER to set/edit any user's permission because she is already able
        // to manipulate useraccounts, _all_ useraccounts. We are sure that $USER is a
        // valid user with at least JOB_PERMISSION_STARTCENTER or else we would not be here.
        $sql = sprintf('CREATE TEMPORARY TABLE %screw_tmp '.
                       'SELECT u.acl_id, u.username, u.full_name, amn.permissions_modules '.
                       'FROM %susers u '.
                       'LEFT JOIN %sacls_modules_nodes amn '.
                           'ON amn.acl_id = u.acl_id AND amn.module_id = %d AND amn.node_id = %d '.
                       'ORDER BY u.full_name',
                       $DB->prefix, $DB->prefix, $DB->prefix, $module_id, $node_id);
    } else {
        // Only allow $USER to set permissions for all her acquaintances, ie. all users
        // that are members of the group(s) that $USER is a also member of.
        $sql = sprintf('CREATE TEMPORARY TABLE %screw_tmp '.
                       'SELECT DISTINCT u.acl_id, u.username, u.full_name, amn.permissions_modules '.
                       'FROM %susers u '.
                       'INNER JOIN %susers_groups_capacities ugc1 USING(user_id) '.
                       'INNER JOIN %susers_groups_capacities ugc2 USING(group_id) '.
                       'LEFT JOIN %sacls_modules_nodes amn '.
                           'ON amn.acl_id = u.acl_id AND amn.module_id = %d AND amn.node_id = %d '.
                       'WHERE ugc2.user_id = %d '.
                       'ORDER BY u.full_name',
                       $DB->prefix, $DB->prefix, $DB->prefix, $DB->prefix, $DB->prefix,
                       $module_id, $node_id, $user_id);
    }
    $retval = $DB->exec($sql);
    // at this point we have a temporary table with all 'editable' accounts
    // we first add those to the dialogdef.
    $table = 'crew_tmp';
    $fields = '*';
    $where = '';
    $order = array('full_name','username');
    if (($records = db_select_all_records($table,$fields,$where,$order)) === FALSE) {
        logger(sprintf('%s(): error retrieving elegible CREW-members: %s',__FUNCTION__,db_errormessage()));
        $output->add_message(t('error_retrieving_data','admin'));
    } else {
        foreach($records as $record) {
            $acl_id = intval($record['acl_id']);
            $name = 'acl_rw_'.$acl_id;
            $dialogdef[$name] = array(
                'type' => F_LISTBOX,
                'name' => $name,
                'value' => (is_null($record['permissions_modules'])) ? 0 : $record['permissions_modules'],
                'old_value' => $record['permissions_modules'],
                'acl_id' => $acl_id, // for future reference (ie. when saving changed data)
                'options' => $roles,
                'viewonly' => $viewonly,
                'title' => $record['username'],
                'label' => $record['full_name']
                );
        }
    }
    // the next step is to generate a list of any OTHER accounts that happen to have
    // permissions for this module on this node other than ACL_ROLE_NONE.
    // This list consists of a few UNIONs that effectively yields all accounts that
    // somehow have a non-0 permissions_modules, either global (acls), any node for
    // this module (acls_modules), any node within this area (acls_modules_area),
    // any node that is an ancestor of node_id (acls_modules_nodes) OR this specific
    // node for a user that is NOT an acquaintance (ie. who is not in the temp table).
    // Note that we don't check the ancestors (parents) when node happens to be at
    // the top level within the area, ie. when parent is 0. We also peek inside
    // 'acls_areas' and 'acls_nodes'. Pfew, complicated...
    // All these OTHER accounts cannot be manipulated by $USER because all accounts
    // would then be in the temp table, so there.
    // Since there may be more records for the same user (or rather acl_id), we need
    // to drill down the results. As all permissions are additive we can simply OR
    // these together per acl_id/user which yields a single combined role for that user.

    $tree = tree_build($area_id);
    $ancestors = array();
    for ($next_id = $tree[$node_id]['parent_id']; ($next_id); $next_id = $tree[$next_id]['parent_id']) {
        $ancestors[] = $next_id;
    }
    unset($tree);
    $sql = (empty($ancestors)) ? '' :
            sprintf('SELECT u.acl_id, u.username, u.full_name, amn.permissions_modules  '.
                    'FROM %susers u INNER JOIN %sacls_modules_nodes amn USING (acl_id) '.
                    'WHERE amn.permissions_modules <> 0 AND amn.module_id = %d AND amn.node_id IN (%s)',
                     $DB->prefix, $DB->prefix, $module_id, join(',',$ancestors)).
            ' UNION '.
            sprintf('SELECT u.acl_id, u.username, u.full_name, an.permissions_modules  '.
                    'FROM %susers u INNER JOIN %sacls_nodes an USING (acl_id) '.
                    'WHERE an.permissions_modules <> 0 AND amn.node_id IN (%s)',
                     $DB->prefix, $DB->prefix, join(',',$ancestors)).
            ' UNION ';
    $sql .= sprintf('SELECT u.acl_id, u.username, u.full_name, a.permissions_modules '.
                    'FROM %susers u INNER JOIN %sacls a USING (acl_id) '.
                    'WHERE a.permissions_modules <> 0',
                     $DB->prefix, $DB->prefix).
            ' UNION '.
            sprintf('SELECT u.acl_id, u.username, u.full_name, am.permissions_modules  '.
                    'FROM %susers u INNER JOIN %sacls_modules am USING (acl_id) '.
                    'WHERE am.permissions_modules <> 0 AND am.module_id = %d',
                     $DB->prefix, $DB->prefix, $module_id).
            ' UNION '.
            sprintf('SELECT u.acl_id, u.username, u.full_name, aa.permissions_modules  '.
                    'FROM %susers u INNER JOIN %sacls_areas aa USING (acl_id) '.
                    'WHERE aa.permissions_modules <> 0 AND aa.area_id = %d',
                     $DB->prefix, $DB->prefix, $area_id).
            ' UNION '.
            sprintf('SELECT u.acl_id, u.username, u.full_name, ama.permissions_modules  '.
                    'FROM %susers u INNER JOIN %sacls_modules_areas ama USING (acl_id) '.
                    'WHERE ama.permissions_modules <> 0 AND ama.module_id = %d AND ama.area_id = %d',
                     $DB->prefix, $DB->prefix, $module_id, $area_id).
            ' UNION '.
            sprintf('SELECT u.acl_id, u.username, u.full_name, an.permissions_modules  '.
                    'FROM %susers u INNER JOIN %sacls_nodes an USING (acl_id) '.
                    'WHERE an.permissions_modules <> 0 AND an.node_id = %d',
                     $DB->prefix, $DB->prefix, $node_id).
            ' UNION '.
            sprintf('SELECT u.acl_id, u.username, u.full_name, amn.permissions_modules  '.
                    'FROM %susers u INNER JOIN %sacls_modules_nodes amn USING (acl_id) '.
                    'LEFT JOIN %screw_tmp tmp USING(acl_id) '.
                    'WHERE amn.permissions_modules <> 0 AND amn.module_id = %d AND amn.node_id = %d '.
                        'AND tmp.acl_id IS NULL ',
                     $DB->prefix, $DB->prefix, $DB->prefix, $module_id, $node_id).
            'ORDER BY full_name, acl_id';
    if (($result = $DB->query($sql)) === FALSE) {
        logger(sprintf('%s(): error retrieving other account names: %s',__FUNCTION__,db_errormessage()));
        $output->add_message(t('error_retrieving_data','admin'));
    } else if ($result->num_rows > 0) {
        $records = array();
        while (($record = $result->fetch_row_assoc()) !== FALSE) {
            $acl_id = intval($record['acl_id']);
            if (isset($records[$acl_id])) {
                $records[$acl_id]['permissions_modules'] |= intval($record['permissions_modules']);
            } else {
                $records[$acl_id] = $record;
            }
        }
        $result->close();
        foreach($records as $acl_id => $record) {
            $name = 'acl_ro_'.$acl_id;
            $dialogdef[$name] = array(
                'type' => F_LISTBOX,
                'name' => $name,
                'value' => (is_null($record['permissions_modules'])) ? 0 : $record['permissions_modules'],
                'options' => $roles,
                'viewonly' => TRUE,
                'title' => $record['username'],
                'label' => $record['full_name']
                );
        }
    }
    if (!$viewonly) {
        $dialogdef['button_save'] = dialog_buttondef(BUTTON_SAVE);
    }
    $dialogdef['button_cancel'] = dialog_buttondef(BUTTON_CANCEL);
    return $dialogdef;
} // crew_get_dialogdef()


?>