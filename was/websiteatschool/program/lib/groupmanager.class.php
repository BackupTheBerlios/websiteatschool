<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker <peter@berestijn.nl>
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

/** /program/lib/groupmanager.class.php - taking care of group management
 *
 * This file defines a class for dealing with groups.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: groupmanager.class.php,v 1.2 2011/02/03 14:04:04 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** this defines the maximum number of capacities a group can have (keep this below 10 because of dialog hotkeys) */
define('GROUPMANAGER_MAX_CAPACITIES',8);

/** Group management
 *
 * @todo Perhaps this class should be merged with the UserManager class because there
 *       is a lot of overlap. Mmmmm.... maybe in a future refactoring operation.
 */
class GroupManager {
    /** @var object|null collects the html output */
    var $output = NULL;

    /** @var array|null caches the list of group-capacity-combinations */
    var $group_capacity_records = NULL;

    /** @var bool if TRUE the calling routing is allowed to use the menu area (e.g. show account mgr menu) */
    var $show_parent_menu = FALSE;

    /** construct a GroupManager object
     *
     * This initialises the GroupManager and also dispatches the task to do.
     *
     * @param object &$output collects the html output
     */
    function GroupManager(&$output) {
        global $CFG;
        $this->output = &$output;
        $this->output->set_helptopic('groupmanager');

        $task = get_parameter_string('task',TASK_GROUPS);
        switch($task) {
        case TASK_GROUPS:
            $this->groups_overview();
            break;

        case TASK_GROUP_ADD:
            $this->group_add();
            break;

        case TASK_GROUP_SAVE_NEW:
            $this->group_savenew();
            break;

        case TASK_GROUP_EDIT:
            $this->group_edit();
            break;

        case TASK_GROUP_SAVE:
            $this->group_save();
            break;

        case TASK_GROUP_DELETE:
            $this->group_delete();
            break;

        case TASK_GROUP_CAPACITY_OVERVIEW:
            $this->capacity_overview();
            break;

        case TASK_GROUP_CAPACITY_INTRANET:
            $this->capacity_intranet();
            break;

        case TASK_GROUP_CAPACITY_ADMIN:
            $this->capacity_admin();
            break;

        case TASK_GROUP_CAPACITY_PAGEMANAGER:
            $this->capacity_pagemanager();
            break;

        case TASK_GROUP_CAPACITY_SAVE:
            $this->capacity_save();
            break;

        case TASK_GROUP_CAPACITY_MODULE:
            $this->output->add_message("STUB: task '$task' not yet implemented");
            $this->output->add_message('group = '.get_parameter_string('group','(unset)'));
            $this->output->add_message('capacity = '.get_parameter_string('capacity','(unset)'));
            $this->output->add_message('module = '.get_parameter_string('module','(unset)'));
            $this->groups_overview();
            break;

        default:
            $s = (strlen($task) <= 50) ? $task : substr($task,0,44).' (...)';
            $message = t('task_unknown','admin',array('{TASK}' => htmlspecialchars($s)));
            $output->add_message($message);
            logger('groupmanager: unknown task: '.htmlspecialchars($s));
            $this->groups_overview();
            break;
        }
    }

    function show_parent_menu() {
        return $this->show_parent_menu;
    }


    // ==================================================================
    // ====================== WORKHORSES (GROUPS) =======================
    // ==================================================================


    /** display list of existing groups and an option to add a group
     *
     * this constructs the heart of the group manager: a link to add a
     * group followed by a list of links for all existing groups and
     * additional links per capacity per group.
     *
     * This list of groups is ordered as follows. All active groups
     * come first, the inactive groups follow. The sort order is based
     * on the (short) name of the group.
     *
     * Example:
     *
     * <pre>
     * Add a group
     * [D] [E] faculty (Member, Principal)
     * [D] [E] grade12 (Pupil, Teacher)
     * ...
     * [D] [E] zebra (Member, Project lead)
     * [D] [E] aardvark (inactive)
     * [D] [E] grade45 (inactive)
     * </pre>
     *
     * Note that both the links '[E]' and 'faculty' lead to edit of group properties
     * The links 'Member' and 'Principal' lead to the group-capacity overview screen
     * The link '[D]' leads to a group delete confirmation screen
     *
     * @return void results are returned as output in $this->output
     * @uses $USER
     * @uses $WAS_SCRIPT_NAME
     * @uses $CFG
     */
    function groups_overview() {
        global $USER,$WAS_SCRIPT_NAME,$CFG;

        // 1 -- Start content and UL-list
        $this->output->add_content('<h2>'.t('menu_groups','admin').'</h2>');
        $this->output->add_content('<ul>');

        // 2 -- Add an 'add a group' option
        $this->output->add_content('  <li class="list">');
        // line up the prompt with links to existing areas below (if any)
        if (!$USER->high_visibility) {
            $img_attr = array('width' => 16, 'height' => 16, 'title' => '', 'alt' => t('spacer','admin'));
            $icon_blank = '    '.html_img($CFG->progwww_short.'/graphics/blank16.gif',$img_attr);
            for ($i=0; $i<2; ++$i) {
                $this->output->add_content($icon_blank);
            }
        } // else
            // don't clutter the high-visiblity interface with superfluous layout fillers
        $a_attr = array('title'=> t('groupmanager_add_a_group_title','admin'));
        $a_params = $this->a_params(TASK_GROUP_ADD);
        $this->output->add_content('    '.html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,t('groupmanager_add_a_group','admin')));

        // 3 -- Construct a list of existing groups if any
        $records = $this->get_group_capacity_records();
        if ($records === FALSE) {
            $this->output->add_message(t('error_retrieving_data','admin'));
            logger('groupmanager: cannot retrieve list of groups+capacities: '.db_errormessage());
        } elseif (sizeof($records) > 0) {
            $prev_group = FALSE;
            $line = '';
            $end_of_line = '';
            foreach($records as $record) {
                $group_id = $record['group_id'];
                if ($group_id != $prev_group) {
                    //
                    // 3A -- finish previous group...
                    if (!empty($end_of_line)) {
                        $line .= $end_of_line;
                    }
                    if (!empty($line)) {
                        $this->output->add_content('    '.$line);
                    }
                    $line = '';
                    $glue = '';
                    $end_of_line = '';
                    $prev_group = $group_id;
                    //
                    // 3B -- ...and start the next one
                    $this->output->add_content('  <li class="list">');
                    $this->output->add_content('    '.$this->get_icon_delete($group_id));
                    $this->output->add_content('    '.$this->get_icon_edit($group_id));
                    $a_params = $this->a_params(TASK_GROUP_EDIT,$group_id);
                    $params = array('{FULL_NAME}' => $record['full_name']);
                    $a_attr = array('title' => t('groupmanager_group_edit_title','admin', $params));
                    $anchor = $record['groupname'];
                    $this->output->add_content('    '.html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor));

                    //
                    // 3C -- for active groups: start a list with links to capacity overview, if any
                    if (db_bool_is(TRUE,$record['is_active'])) {
                        $capacity_code = intval($record['capacity_code']);
                        if ($capacity_code > 0) {
                            $anchor = capacity_name($capacity_code);
                            $a_params = $this->a_params(TASK_GROUP_CAPACITY_OVERVIEW,$group_id,$capacity_code);
                            $a_attr = array('title' => t('groupmanager_group_capacity_edit_title','admin'));
                            $line = '('.html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
                            $end_of_line = ')';
                            $glue = ',';
                        }
                    } else {
                        $this->output->add_content('    ('.t('inactive','admin').')');
                    }
                } else {
                    // 4 -- for active groups: carry on with the list; inactive groups alread had '(inactive)' printed
                    if (db_bool_is(TRUE,$record['is_active'])) {
                        $line .= $glue;
                        $this->output->add_content('    '.$line);
                        $line = '';
                        $capacity_code = intval($record['capacity_code']);
                        if ($capacity_code > 0) {
                            $anchor = capacity_name($capacity_code);
                            $a_params = $this->a_params(TASK_GROUP_CAPACITY_OVERVIEW,$group_id,$capacity_code);
                            $a_attr = array('title' => t('groupmanager_group_capacity_edit_title','admin'));
                            $line = html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
                        }
                    } // else
                        // skip additional group-capacities for inactive groups
                }
            }
            if (!empty($end_of_line)) {
                $line .= $end_of_line;
            }
            if (!empty($line)) {
                $this->output->add_content('    '.$line);
            }
        }

        // 4 -- close the list and allow caller to show the account manager menu too
        $this->output->add_content('</ul>');
        $this->show_parent_menu = TRUE;
    } // groups_overview()


    /** present 'add group' dialog where the user can enter minimal properties for a new group
     *
     * this displays a dialog where the user can enter the minimal necessary properties
     * of a new group. These properties are: 
     *  - name (e.g. 'grade7')
     *  - full name (e.g. 'Pupils of grade 7')
     *  - the active flag
     *  - the allowable capacities for this group (e.g. 'Pupil' and 'Teacher')
     * Other properties (if any) will be set to default values and can be edited lateron
     * by editing the group.
     *
     * The new group is saved via performing the task TASK_GROUP_SAVE_NEW
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME
     */
    function group_add() {
        global $WAS_SCRIPT_NAME;
        $this->output->add_content('<h2>'.t('groupmanager_add_group_header','admin').'</h2>');
        $this->output->add_content(t('groupmanager_add_group_explanation','admin'));
        $href = href($WAS_SCRIPT_NAME,$this->a_params(TASK_GROUP_SAVE_NEW));
        $dialogdef = $this->get_dialogdef_add_group();
        $this->output->add_content(dialog_quickform($href,$dialogdef));
        $this->show_breadcrumbs_addgroup();
    } // group_add()


    /** save a new group to the database
     *
     * this saves a new group to the database. This quite a complex task because of
     * the number of tables involved.
     *
     * First we have the table 'groups' which stores the basic group information.
     * Then there is the table 'groups_capacities'. For every combination of group
     * and capacity requested by the user a record must be added to this table.
     * Then there is also a separate acl for every group_capacity, so there.
     * 
     * The strategy should be something like this.
     * new_group_id = insert_new_group_into_groups()
     * for all GROUPMANAGER_MAX_CAPACITIES do
     *     if capacity != CAPACITY_NONE && capacity_not_added_yet()
     *         prepare_new_acl_record();
     *         new_acl_id = insert_new_acl_in_acls();
     *         prepare_new_groups_capacities_record();
     *         insert_new_group_capacity_in_table()
     *   
     * @todo maybe we should find a more elegant way to check a field for uniqueness
     * @todo should we delete the datadirectory if something goes wrong?
     * @return data saved to the database, output created via groups_overview()
     */
    function group_savenew() {
        global $WAS_SCRIPT_NAME,$CFG;
        //
        // 1 -- bail out if the user pressed cancel button
        //
        if (isset($_POST['button_cancel'])) {
            $this->output->add_message(t('cancelled','admin'));
            $this->groups_overview();
            return;
        }

        //
        // 2 -- validate the data
        //
        $invalid = FALSE;
        $dialogdef = $this->get_dialogdef_add_group();

        // 2A -- check for generic errors (string too short, number too small, etc)
        if (!dialog_validate($dialogdef)) {
            $invalid = TRUE;
        }

        // 2B -- check out the groupname: this field should be unique
        $groupname = $dialogdef['group_name']['value'];
        $fname = (isset($dialogdef['group_name']['label'])) ? $dialogdef['group_name']['label'] : 'group_name';
        $params = array('{FIELD}' => str_replace('~','',$fname));
        if (db_select_single_record('groups','group_id',array('groupname' => $groupname)) !== FALSE) {
            // Oops, a record with that groupname already exists. Go flag error
            ++$dialogdef['group_name']['errors'];
            $dialogdef['group_name']['error_messages'][] = t('validate_not_unique','',$params);
            $invalid = TRUE;
        }

        // 2C -- additional check: unique groupdata subdirectory relative to {$CFG->datadir}/groups/
        $groupdata_directory = strtolower(sanitise_filename($groupname));
        $groupdata_full_path = $CFG->datadir.'/groups/'.$groupdata_directory;
        $groupdata_directory_created = @mkdir($groupdata_full_path,0700);
        if ($groupdata_directory_created) {
            @touch($groupdata_full_path.'/index.html'); // "protect" the newly created directory from prying eyes
        } else {
            // Mmmm, failed; probably already exists then. Oh well. Go flag error.
            ++$dialogdef['group_name']['errors'];
            $params['{VALUE}'] = '/groups/'.$groupdata_directory;
            $dialogdef['group_name']['error_messages'][] = t('validate_already_exists','',$params);
            $invalid = TRUE;
        }

        // 2D -- if there were any errors go redo dialog while keeping data already entered
        if ($invalid) {
            if ($groupdata_directory_created) { // Only get rid of the directory _we_ created
                @unlink($groupdata_full_path.'/index.html');
                @rmdir($groupdata_full_path);
            }
            // show errors messages
            foreach($dialogdef as $k => $item) {
                if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                    $this->output->add_message($item['error_messages']);
                }
            }
            $this->output->add_content('<h2>'.t('groupmanager_add_group_header','admin').'</h2>');
            $this->output->add_content(t('groupmanager_add_group_explanation','admin'));
            $href = href($WAS_SCRIPT_NAME,$this->a_params(TASK_GROUP_SAVE_NEW));
            // $dialogdef = $this->get_dialogdef_add_group();
            $this->output->add_content(dialog_quickform($href,$dialogdef));
            return;
        }
        //
        // 3 -- store the data
        //
        // At this point we have a validated new group dialog in our hands
        // We now need to convert the data from the dialog to sensible
        // fields and store the data.
        //
        // 3A -- insert new group record and remember the new group_id
        $group_fullname = $dialogdef['group_fullname']['value'];
        $fields = array(
            'groupname' => $groupname,
            'full_name' => $group_fullname,
            'is_active' => ($dialogdef['group_is_active']['value'] == 1) ? TRUE : FALSE,
            'path'      => $groupdata_directory
            );
        $new_group_id = db_insert_into_and_get_id('groups',$fields,'group_id');
        if ($new_group_id === FALSE) {
            logger("groupmanager: saving new group failed: ".db_errormessage());
            $this->output->add_message(t('groupmanager_savenew_group_failure','admin'));
            $this->groups_overview();
            if ($groupdata_directory_created) { // Only get rid of the directory _we_ created
                @unlink($groupdata_full_path.'/index.html');
                @rmdir($groupdata_full_path);
            }
            return;
        }

        // 3B collect the specified capacities and calculate sort order
        $capacity_codes = array();
        $capacity_sort_order = 0;
        foreach($dialogdef as $k => $item) {
            if ((isset($item['name'])) && (substr($item['name'],0,15) == 'group_capacity_')) {
                $value = intval($item['value']);
                if ($value != CAPACITY_NONE) {
                    $capacity_codes[$value] = ++$capacity_sort_order;
                }
            }
        }

        // 3C -- create 0, 1 or more acls + group-capacity-records
        foreach($capacity_codes as $capacity_code => $sort_order) {
            if ($this->add_group_capacity($new_group_id,$capacity_code,$sort_order) === FALSE) {
                $this->output->add_message(t('groupmanager_savenew_group_failure','admin'));
                $this->groups_overview();
                if ($groupdata_directory_created) { // Only get rid of the directory _we_ created
                    @unlink($groupdata_full_path.'/index.html');
                    @rmdir($groupdata_full_path);
                }
                return;
            }
        }

        // 4 -- tell user about success
        $params = array('{GROUP}' => $groupname,'{GROUP_FULL_NAME}' => $group_fullname);
        $this->output->add_message(t('groupmanager_savenew_group_success','admin',$params));
        logger(sprintf("groupmanager: success saving new group '%d' %s (%s) and datadir /groups/%s",
                         $new_group_id,$groupname,$group_fullname,$groupdata_directory));
        $this->groups_overview();
    } // group_savenew()


    /** show a dialog with the basic properties of a group
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME
     */
    function group_edit() {
        global $WAS_SCRIPT_NAME;
        $group_id = get_parameter_int('group',NULL);
        if (is_null($group_id)) {
            logger("groupmanager->group_edit(): unspecified parameter group");
            $this->output->add_message(t('error_invalid_parameters','admin'));
            $this->groups_overview();
            return;
        }
        $this->output->add_content('<h2>'.t('groupmanager_edit_group_header','admin').'</h2>');
        $this->output->add_content(t('groupmanager_edit_group_explanation','admin'));
        $href = href($WAS_SCRIPT_NAME,$this->a_params(TASK_GROUP_SAVE,$group_id));
        $dialogdef = $this->get_dialogdef_edit_group($group_id);
        if ($dialogdef !== FALSE) {
            $this->output->add_content(dialog_quickform($href,$dialogdef));
        } else {
            $this->output->add_message(t('error_retrieving_data','admin'));
        }
        $this->show_menu_group($group_id,TASK_GROUP_EDIT);
    } // group_edit()


    /** save an edited group to the database, including adding/modifying/deleting group/capacity-records
     *
     * Note: no error checking when inserting new capacity because we more or less know
     * that that capacity does not exist already or it would have been in the array already.
     * (But what if there are more than GROUPMANAGER_MAX_CAPACITIES in the database? Mmmm....
     *
     * @output void work done and output via $this->output
     * @uses $WAS_SCRIPT_NAME;
     */
    function group_save() {
        global $WAS_SCRIPT_NAME;

        //
        // 0 -- sanity check
        //
        $group_id = get_parameter_int('group',NULL);
        if (is_null($group_id)) {
            logger("groupmanager->group_save(): unspecified parameter group");
            $this->output->add_message(t('error_invalid_parameters','admin'));
            $this->groups_overview();
            return;
        }

        //
        // 1 -- bail out if the user pressed cancel button
        //
        if (isset($_POST['button_cancel'])) {
            $this->output->add_message(t('cancelled','admin'));
            $this->groups_overview();
            return;
        }

        //
        // 2 -- validate the data
        //
        $dialogdef = $this->get_dialogdef_edit_group($group_id);
        if ($dialogdef === FALSE) {
            $this->output->add_message(t('error_retrieving_data','admin'));
            $this->groups_overview();
            return;
        }
        //
        // 2A -- remember generic errors (string too short, number too small, etc)
        $invalid = FALSE;
        if (!dialog_validate($dialogdef)) {
            $invalid = TRUE;
        }
        // 2B -- check out the groupname: this field should be unique
        foreach($dialogdef as $k => $item) {
            if (isset($item['name'])) {
                switch($item['name']) {
                case 'group_name':
                    $record = db_select_single_record('groups','group_id',array('groupname' => $item['value']));
                    if (($record !== FALSE) && (intval($record['group_id']) != $group_id)) {
                        // Oops, a record with that groupname already exists and it's not us. Go flag error
                        ++$dialogdef[$k]['errors'];
                        $fname = (isset($item['label'])) ? str_replace('~','',$item['label']) : $item['name'];
                        $dialogdef[$k]['error_messages'][] = t('validate_not_unique','',array('{FIELD}'=>$fname));
                        $invalid = TRUE;
                    }
                    break;
                }
            }
        }
        // 2C -- if there were any errors go redo dialog while keeping data already entered
        if ($invalid) {
            foreach($dialogdef as $k => $item) {
                if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                    $this->output->add_message($item['error_messages']);
                }
            }
            $this->output->add_content('<h2>'.t('groupmanager_edit_group_header','admin').'</h2>');
            $this->output->add_content(t('groupmanager_edit_group_explanation','admin'));
            $href = href($WAS_SCRIPT_NAME,$this->a_params(TASK_GROUP_SAVE,$group_id));
            $this->output->add_content(dialog_quickform($href,$dialogdef));
            // note: we suppress the distracting menu this time: user should focus on entering correct data
            // However, we do show the breadcrumb trail
            $this->show_breadcrumbs_group();
            $this->output->add_breadcrumb($WAS_SCRIPT_NAME,
                  $this->a_params(TASK_GROUP_EDIT,$group_id),
                  array('title' => t('groupmanager_group_menu_edit_title','admin')),
                  t('groupmanager_group_menu_edit','admin'));
            return;
        }

        // 3 -- store update group data

        // At this point we have a validated group dialog in our hands
        // We now need to convert the data from the dialog to sensible
        // fields and store the data.

        // 3A -- update group record AND (as a side effect) collect the specified capacities
        $fields = array('groupname' => '','full_name' => '','is_active' => TRUE);
        $capacities_old = array();
        $capacities_new = array();
        $new_sort_order = 0;
        $new_group_name = '';
        $new_group_fullname = '';
        $modified = FALSE; // assume no changes
        foreach($dialogdef as $k => $item) {
            if (isset($item['name'])) {
                switch ($item['name']) {
                case 'group_name':
                    $new_group_name = $item['value'];
                    $fields['groupname'] = $new_group_name;
                    if ($new_group_name != $item['old_value']) {
                        $modified = TRUE;
                    }
                    break;
                case 'group_fullname':
                    $new_group_fullname = $item['value'];
                    $fields['full_name'] = $new_group_fullname;
                    if ($new_group_fullname != $item['old_value']) {
                        $modified = TRUE;
                    }
                    break;
                case 'group_is_active':
                    $fields['is_active'] = ($item['value'] == 1) ? TRUE : FALSE;
                    if ($item['value'] != $item['old_value']) {
                        $modified = TRUE;
                    }
                    break;
                default:
                    if (substr($item['name'],0,15) == 'group_capacity_') {
                        $old_value = intval($item['old_value']);
                        $value = intval($item['value']);
                        if ($old_value != CAPACITY_NONE) {
                            $capacities_old[$old_value] = array(
                                'sort_order' => intval($item['sort_order']),
                                'acl_id' => intval($item['acl_id']));
                        }
                        if (($value != CAPACITY_NONE) && (!isset($capacities_new[$value]))) {
                            $capacities_new[$value] = ++$new_sort_order;
                        }
                    }
                    break;
                }
            }
        }
        $errors = 0;
        if ($modified) {
            if (db_update('groups',$fields,array('group_id' => $group_id)) === FALSE) {
                ++$errors;
                logger("groupmanager->group_save(): error saving data group '$group_id': ".db_errormessage());
            } else {
                logger("groupmanager->group_save(): success saving changes to '$group_id' in 'groups'",LOG_DEBUG);
            }
        } else {
            logger("groupmanager->group_save(): no changes to '$group_id' in 'groups'",LOG_DEBUG);
        }

        //
        // 4 -- delete/update/add group-capacities (if necessary)
        //
        // At this point we have two arrays with capacities: the old and the new situation.
        // We need to do the following:
        // Step through the OLD list and 
        //  - remove/deactivate all capacities that are not in the NEW list (step 4A)
        //  - update the records that have a different sort order in the NEW list (step 4B)
        // Step through the NEW list and
        //  - insert records that do not occur in the OLD list (step 4C).
        //
        foreach($capacities_old as $capacity_code => $v) {
            $acl_id = $v['acl_id'];
            $sort_order = $v['sort_order'];
            if (!isset($capacities_new[$capacity_code])) {
                //
                // 4A -- get rid of this group/capacity's acl and also the group/capacity itself
                if ($this->acl_delete($acl_id) === FALSE) {
                    ++$errors;
                    logger(sprintf("group_save(): error removing acl '%d'; skipping delete of group/capacity '%d/%d'",
                                   $acl_id,$group_id,$capacity_code));
                } else {
                    logger(sprintf("group_save(): success removing acl_id '%d' from group/capacity '%d/%d'",
                                   $acl_id,$group_id,$capacity_code),LOG_DEBUG);
                    $where = array('group_id' => $group_id, 'capacity_code' => $capacity_code);
                    $tables = array('groups_capacities','users_groups_capacities');
                    foreach ($tables as $table) {
                        if (($retval = db_delete($table,$where)) === FALSE) {
                            ++$errors;
                            logger(sprintf("group_save(): error removing group/capacity '%d/%d' from '%s': %s",
                                           $group_id,$capacity_code,$table,db_errormessage()));
                        } else {
                            logger(sprintf("group_save(): success removing group/capacity '%d/%d' from '%s', %d records",
                                           $group_id,$capacity_code,$table,$retval),LOG_DEBUG);
                        }
                    }
                }
            } elseif ($sort_order != $capacities_new[$capacity_code]) {
                //
                // 4B -- change sort order
                $where = array('group_id' => $group_id, 'capacity_code' => $capacity_code);
                $fields = array('sort_order' => $capacities_new[$capacity_code]);
                if (db_update('groups_capacities',$fields,$where) === FALSE) {
                    ++$errors;
                    logger(sprintf("groupmanager->group_save(): cannot update sort order in  group/capacity '%d/%d': %s",
                                   $group_id,$capacity_code,db_errormessage()));
                } else {
                    logger(sprintf("group_save(): success changing sort order '%d' -> '%d' for group/capacity '%d/%d'",
                                   $sort_order,$capacities_new[$capacity_code],$group_id,$capacity_code),LOG_DEBUG);
                }
            } //  else
                // no changes
        }
        //
        // 4C -- add new group/capacities when necessary
        foreach($capacities_new as $capacity_code => $sort_order) {
            if (!isset($capacities_old[$capacity_code])) {
                if ($this->add_group_capacity($group_id,$capacity_code,$sort_order) === FALSE) {
                    ++$errors;
                }
            }
        }
        if ($errors == 0) {
            $params = array('{GROUP}' => $new_group_name,'{GROUP_FULL_NAME}' => $new_group_fullname);
            $this->output->add_message(t('groupmanager_edit_group_success','admin',$params));
        } else {
            $this->output->add_message(t('errors_saving_data','admin',array('{ERRORS}' => $errors)));
        }
        logger(sprintf("groupmanager: %s saving modified group '%d' %s (%s)",
                       ($errors == 0) ? "success" : "failure",$group_id,$new_group_name,$new_group_fullname));
        $this->groups_overview();
    } // group_save()


    /** delete a group after confirmation
     *
     * this either presents a confirmation dialog to the user OR deletes a group with
     * associated capacities and acls.
     *
     * Note that this routine could have been split into two routines, with the
     * first one displaying the confirmation dialog and the second one 'saving the changes'.
     * However, I think it is counter-intuitive to perform a deletion of data under
     * the name of 'saving'. So, I decided to use the same routine for both displaying
     * the dialog and acting on the dialog.
     *
     * @return void results are returned as output in $this->output
     * @todo should we also require the user to delete any files associated with the group before we even consider
     *       deleting it? Or is is OK to leave the files and still delete the group. Food for thought.
     * @todo since multiple tables are involved, shouldn't we use transaction/rollback/commit?
     *       Q: How well is MySQL suited for transactions? A: Mmmmm.... Which version? Which storage engine?
     */
    function group_delete() {
        global $WAS_SCRIPT_NAME,$DB;
        //
        // 0 -- sanity check
        //
        $group_id = get_parameter_int('group',NULL);
        if (is_null($group_id)) {
            logger("groupmanager->group_save(): unspecified parameter group");
            $this->output->add_message(t('error_invalid_parameters','admin'));
            $this->groups_overview();
            return;
        }

        //
        // 1 -- bail out if the user pressed cancel button
        //
        if (isset($_POST['button_cancel'])) {
            $this->output->add_message(t('cancelled','admin'));
            $this->groups_overview();
            return;
        }

        //
        // 2 -- user has confirmed delete?
        //
        if ((isset($_POST['button_delete'])) &&
            (isset($_POST['dialog'])) && ($_POST['dialog'] == GROUPMANAGER_DIALOG_DELETE)) {
            $params = $this->get_group_capacity_names($group_id); // pick up name before it is gone
            if (($retval = $this->delete_group_capacities_acls($group_id)) === FALSE) {
                $this->output->add_message(t('groupmanager_delete_group_failure','admin',$params));
            } else {
                $this->output->add_message(t('groupmanager_delete_group_success','admin',$params));
            }
            logger(sprintf("groupmanager: %s deleting group '%d' %s (%s)",
                       ($retval === FALSE) ? 'failure' : 'success',
                       $group_id,
                       $params['{GROUP}'],
                       $params['{GROUP_FULL_NAME}']));
            $this->groups_overview();
            return;
        }

        //
        // 3 -- no delete yet, first show confirmation dialog
        //
        // Dialog is very simple: a simple text showing
        // - the name of the group
        // - the names of the associated capacities with number of users associated
        // - a Delete and a Cancel button
        //
        $dialogdef = array(
            'dialog' => array('type' => F_INTEGER,'name' => 'dialog','value' => GROUPMANAGER_DIALOG_DELETE,'hidden' => TRUE),
            'button_save' => dialog_buttondef(BUTTON_DELETE),
            'button_cancel' => dialog_buttondef(BUTTON_CANCEL)
            );

        $params = $this->get_group_capacity_names($group_id);
        $header = t('groupmanager_delete_group_header','admin',$params);
        $this->output->add_content('<h2>'.$header.'</h2>');
        $this->output->add_content(t('groupmanager_delete_group_explanation','admin'));
        $this->output->add_content('<ul>');
        $this->output->add_content('  <li class="level0">'.t('groupmanager_delete_group_group','admin',$params));
        $sql = sprintf("SELECT gc.capacity_code, COUNT(ugc.user_id) AS users ".
                       "FROM %sgroups_capacities gc LEFT JOIN %susers_groups_capacities ugc ".
                       "ON gc.group_id = ugc.group_id AND gc.capacity_code = ugc.capacity_code ".
                       "WHERE gc.group_id = %d ".
                       "GROUP BY gc.capacity_code ".
                       "ORDER BY gc.sort_order",
                       $DB->prefix,$DB->prefix,$group_id);
        if (($DBResult = $DB->query($sql)) !== FALSE) {
            $records = $DBResult->fetch_all_assoc();
            $DBResult->close();
            foreach($records as $record) {
                $params['{CAPACITY}'] = capacity_name($record['capacity_code']);
                $params['{COUNT}'] = $record['users'];
                $line = t('groupmanager_delete_group_capacity','admin',$params);
                $this->output->add_content('  <li class="level0">'.$line);
            }
        }
        $this->output->add_content('</ul>');
        $this->output->add_content(t('delete_are_you_sure','admin'));

        $a_params = $this->a_params(TASK_GROUP_DELETE,$group_id);
        $href = href($WAS_SCRIPT_NAME,$a_params);
        $this->output->add_content(dialog_quickform($href,$dialogdef));
        $this->show_menu_group($group_id,TASK_GROUP_DELETE);
        $this->output->add_breadcrumb(
            $WAS_SCRIPT_NAME,
            $a_params,
            array('title' => $header),
            t('groupmanager_delete_group_breadcrumb','admin'));
    } // group_delete()


    // ==================================================================
    // ================ WORKHORSES (GROUP-CAPACITIES) ===================
    // ==================================================================


    /** display an overview of all members of a group with a particular capacity 
     *
     * this constructs a clickable list of users that are associated with a
     * particular combination of group_id and capacity_code. The name of the
     * user is a link to the usermanager for that user. Users are sorted by name.
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME
     * @uses $DB
     */
    function capacity_overview() {
        global $DB,$WAS_SCRIPT_NAME;
        $group_id = get_parameter_int('group',NULL);
        $capacity_code = get_parameter_int('capacity',NULL);

        // 1 -- sanity check
        if (!$this->valid_group_capacity($group_id,$capacity_code)) {
            $this->groups_overview();
            return;
        }

        // 2 -- go for a list of users that are members of this group/capacity
        $sql = sprintf('SELECT u.user_id, u.username, u.full_name, u.is_active '.
                       'FROM %susers u INNER JOIN %susers_groups_capacities ugc ON u.user_id = ugc.user_id '.
                       'WHERE ugc.group_id = %d AND ugc.capacity_code = %d '.
                       'ORDER BY u.username',
                       $DB->prefix,$DB->prefix,$group_id,$capacity_code);
        if (($DBResult = $DB->query($sql)) === FALSE) {
            logger('groupmanager->capacity_overview(): database error: '.$DB->errno.'/\''.$DB->error.'\'');
            $this->output->add_message(t('error_retrieving_data','admin'));
            $this->groups_overview();
            return;
        }

        // 3 -- Start with the actual overview
        $params = $this->get_group_capacity_names($group_id,$capacity_code);
        $this->output->add_content('<h2>'.t('groupmanager_capacity_overview_header','admin',$params).'</h2>');

        // 4 -- Iterate through all members (or say there are none)
        if ($DBResult->num_rows <= 0) {
            $this->output->add_content(t('groupmanager_capacity_overview_no_members','admin',$params));
        } else {
            $records = $DBResult->fetch_all_assoc('user_id');
            $this->output->add_content(t('groupmanager_capacity_overview_explanation','admin',$params));
            $this->output->add_content('<ul>');
            foreach($records as $user_id => $record) {
                $a_params = array('job' => JOB_ACCOUNTMANAGER,'task' => TASK_USER_EDIT,'user' => $user_id);
                $attributes = array('title' => t('usermanager_user_edit_title','admin',
                                                 array('{FULL_NAME}' => $record['full_name'])));
                $anchor = sprintf('%s (%s)',$record['full_name'],$record['username']);
                $inactive = (db_bool_is(TRUE,$record['is_active'])) ? '' : sprintf(' (%s)',t('inactive'.'admin'));
                $this->output->add_content('  <li class="list">'.
                                                  html_a($WAS_SCRIPT_NAME,$a_params,$attributes,$anchor).$inactive);
            }
            $this->output->add_content('</ul>');
        }
        $this->show_menu_groupcapacity($group_id,$capacity_code,TASK_GROUP_CAPACITY_OVERVIEW);
    } // capacity_overview()


    /** save data from a dialog for a group/capacity
     *
     * @return void data saved and results are returned as readable output in $this->output
     * @uses $WAS_SCRIPT_NAME
     * @uses $CFG
     */
    function capacity_save() {
        global $WAS_SCRIPT_NAME,$CFG;
        $group_id = get_parameter_int('group',NULL);
        $capacity_code = get_parameter_int('capacity',NULL);

        // 1 -- sanity check
        if (!$this->valid_group_capacity($group_id,$capacity_code)) {
            $this->groups_overview();
            return;
        }

        // 2 -- user wants to bail out?
        if (isset($_POST['button_cancel'])) {
            $this->output->add_message(t('cancelled','admin'));
            $this->capacity_overview();
            return;
        }

        // 3 -- which acl to use?
        if (($acl_id = $this->calc_acl_id($group_id,$capacity_code)) === FALSE) {
            $this->capacity_overview();
            return;
        }

        // 4 -- did they specify a dialog?
        if (!isset($_POST['dialog'])) {
            logger("groupmanager->capacity_save(): weird: 'dialog' not set",LOG_DEBUG);
            $this->capacity_overview();
            return;
        }
        $dialog = intval($_POST['dialog']);

        // 5 -- prepare necessary parameters and process dialog
        $a_params = $this->a_params(TASK_GROUP_CAPACITY_SAVE,$group_id,$capacity_code);
        $params = $this->get_group_capacity_names($group_id,$capacity_code);

        switch ($dialog) {
        case GROUPMANAGER_DIALOG_CAPACITY_INTRANET:
            include_once($CFG->progdir.'/lib/aclmanager.class.php');
            $acl = new AclManager($this->output,$acl_id,ACL_TYPE_INTRANET);
            $acl->set_action($a_params);
            $acl->set_header(t('groupmanager_capacity_intranet_header','admin',$params));
            $acl->set_intro(t('groupmanager_capacity_intranet_explanation','admin',$params));
            $acl->set_dialog($dialog);
            if (!$acl->save_data()) {
                $acl->show_dialog(); // redo dialog, but without a distracting menu this time
                return;
            }
            break;

        case GROUPMANAGER_DIALOG_CAPACITY_ADMIN:
            include_once($CFG->progdir.'/lib/aclmanager.class.php');
            $acl = new AclManager($this->output,$acl_id,ACL_TYPE_ADMIN);
            $acl->set_action($a_params);
            $acl->set_header(t('groupmanager_capacity_admin_header','admin',$params));
            $acl->set_intro(t('groupmanager_capacity_admin_explanation','admin',$params));
            $acl->set_dialog($dialog);
            if (!$acl->save_data()) {
                $acl->show_dialog(); // redo dialog, but without a distracting menu this time
                return;
            }
            break;

        case GROUPMANAGER_DIALOG_CAPACITY_PAGEMANAGER:
            $limit = get_parameter_int('limit',$CFG->pagination_height);
            $offset = get_parameter_int('offset',0);
            if ($limit != $CFG->pagination_height) {
                $a_params['limit'] = $limit;
            }
            if ($offset != 0) {
                $a_params['offset'] = $offset;
            }
            include_once($CFG->progdir.'/lib/aclmanager.class.php');
            $acl = new AclManager($this->output,$acl_id,ACL_TYPE_PAGEMANAGER);
            $acl->set_action($a_params);
            $acl->set_header(t('groupmanager_capacity_pagemanager_header','admin',$params));
            $acl->set_intro(t('groupmanager_capacity_pagemanager_explanation','admin',$params));
            $acl->set_dialog($dialog);

            // Enable pagination for this one: the list of nodes can be very very long so split up in smaller screens.
            $a_params = $this->a_params(TASK_GROUP_CAPACITY_PAGEMANAGER,$group_id,$capacity_code);
            $acl->enable_pagination($a_params,$limit,$offset);

            // Also enable the expand/collapse feature
            if (!isset($_SESSION['aclmanager_open_areas'])) {
                $_SESSION['aclmanager_open_areas'] = FALSE; // default: everything is closed
            }
            $acl->enable_area_view($a_params,$_SESSION['aclmanager_open_areas']);
            if (!$acl->save_data()) {
                $acl->show_dialog(); // redo dialog, but without a distracting menu this time
                return;
            }
            break;

        default:
            logger(sprintf("groupmanager->save_data(): weird: dialog='%d'. Huh?",$dialog),LOG_DEBUG);
            break;
        }

        // 6 -- we always end up in the capacity overview screen
        $this->capacity_overview();
    } // capacity_save()


    /** show a dialog for modifying intranet permissions for a group/capacity
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME
     * @uses $CFG
     */
    function capacity_intranet() {
        global $WAS_SCRIPT_NAME,$CFG;
        $group_id = get_parameter_int('group',NULL);
        $capacity_code = get_parameter_int('capacity',NULL);

        // 1 -- sanity check
        if (!$this->valid_group_capacity($group_id,$capacity_code)) {
            $this->groups_overview();
            return;
        }
        // 2 -- which acl to use?
        if (($acl_id = $this->calc_acl_id($group_id,$capacity_code)) === FALSE) {
            $this->capacity_overview();
            return;
        }

        // 3 -- setup the AclManager to do the dirty work
        include_once($CFG->progdir.'/lib/aclmanager.class.php');
        $acl = new AclManager($this->output,$acl_id,ACL_TYPE_INTRANET);
        $acl->set_action($this->a_params(TASK_GROUP_CAPACITY_SAVE,$group_id,$capacity_code));
        $params = $this->get_group_capacity_names($group_id,$capacity_code);
        $acl->set_header(t('groupmanager_capacity_intranet_header','admin',$params));
        $acl->set_intro(t('groupmanager_capacity_intranet_explanation','admin',$params));
        $acl->set_dialog(GROUPMANAGER_DIALOG_CAPACITY_INTRANET);

        // 4 -- show the dialog and the menu
        $acl->show_dialog();
        $this->show_menu_groupcapacity($group_id,$capacity_code,TASK_GROUP_CAPACITY_INTRANET);
    } // capacity_intranet()


    /** show a dialog for modifying admin permissions for a group/capacity
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME
     * @uses $CFG
     */
    function capacity_admin() {
        global $WAS_SCRIPT_NAME,$CFG;
        $group_id = get_parameter_int('group',NULL);
        $capacity_code = get_parameter_int('capacity',NULL);

        // 0 -- sanity check
        if (!$this->valid_group_capacity($group_id,$capacity_code)) {
            $this->groups_overview();
            return;
        }
        // 1 -- which acl to use?
        if (($acl_id = $this->calc_acl_id($group_id,$capacity_code)) === FALSE) {
            $this->capacity_overview();
            return;
        }

        // 2 -- setup the AclManager to do the dirty work
        include_once($CFG->progdir.'/lib/aclmanager.class.php');
        $acl = new AclManager($this->output,$acl_id,ACL_TYPE_ADMIN);
        $acl->set_action($this->a_params(TASK_GROUP_CAPACITY_SAVE,$group_id,$capacity_code));
        $params = $this->get_group_capacity_names($group_id,$capacity_code);
        $acl->set_header(t('groupmanager_capacity_admin_header','admin',$params));
        $acl->set_intro(t('groupmanager_capacity_admin_explanation','admin',$params));
        $acl->set_dialog(GROUPMANAGER_DIALOG_CAPACITY_ADMIN);

        // 3 -- show the dialog and the menu
        $acl->show_dialog();
        $this->show_menu_groupcapacity($group_id,$capacity_code,TASK_GROUP_CAPACITY_ADMIN);
    } // capacity_admin()


    /** show a dialog for modifying page manager permissions for a group/capacity
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME
     * @uses $CFG
     */
    function capacity_pagemanager() {
        global $WAS_SCRIPT_NAME,$CFG;
        $group_id = get_parameter_int('group',NULL);
        $capacity_code = get_parameter_int('capacity',NULL);

        // 0 -- sanity check
        if (!$this->valid_group_capacity($group_id,$capacity_code)) {
            $this->groups_overview();
            return;
        }

        // 1 -- maybe change the state of the open/closed areas
        if (!isset($_SESSION['aclmanager_open_areas'])) {
            $_SESSION['aclmanager_open_areas'] = FALSE; // default: everything is closed
        }
        $area_id = get_parameter_int('area',NULL);
        $_SESSION['aclmanager_open_areas'] = $this->areas_expand_collapse($_SESSION['aclmanager_open_areas'],$area_id);

        // 2 -- which acl to use?
        if (($acl_id = $this->calc_acl_id($group_id,$capacity_code)) === FALSE) {
            $this->capacity_overview();
            return;
        }

        //
        // 3A -- construct necessary parameters for dialog
        //
        $a_params = $this->a_params(TASK_GROUP_CAPACITY_SAVE,$group_id,$capacity_code);
        $limit = get_parameter_int('limit',$CFG->pagination_height);
        $offset = get_parameter_int('offset',0);
        if ($limit != $CFG->pagination_height) {
            $a_params['limit'] = $limit;
        }
        if ($offset != 0) {
            $a_params['offset'] = $offset;
        }

        //
        // 3B -- setup Aclmanager to do the dirty work
        //
        include_once($CFG->progdir.'/lib/aclmanager.class.php');
        $acl = new AclManager($this->output,$acl_id,ACL_TYPE_PAGEMANAGER);
        $acl->set_action($a_params);
        $params = $this->get_group_capacity_names($group_id,$capacity_code);
        $acl->set_header(t('groupmanager_capacity_pagemanager_header','admin',$params));
        $acl->set_intro(t('groupmanager_capacity_pagemanager_explanation','admin',$params));

        $acl->set_dialog(GROUPMANAGER_DIALOG_CAPACITY_PAGEMANAGER);

        // Enable pagination for this one: the list of nodes can be very very long so split up in smaller screens.
        $a_params = $this->a_params(TASK_GROUP_CAPACITY_PAGEMANAGER,$group_id,$capacity_code);
        $acl->enable_pagination($a_params,$limit,$offset);

        // Also enable the expand/collapse feature
        $acl->enable_area_view($a_params,$_SESSION['aclmanager_open_areas']);

        //
        // 4 -- show dialog + menu
        //
        $acl->show_dialog();
        $this->show_menu_groupcapacity($group_id,$capacity_code,TASK_GROUP_CAPACITY_PAGEMANAGER);
    } // capacity_pagemanager()









    /** display breadcrumb trail that leads to group capacity overview screen
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME;
     */
    function show_breadcrumbs_groupcapacity($group_id,$capacity_code) {
        global $WAS_SCRIPT_NAME;
        $breadcrumbs = array(
            array(
                'parameters' => $this->a_params(TASK_GROUPS),
                'anchor' => t('name_accountmanager','admin'),
                'title' => t('description_accountmanager','admin')
                ),
            array(
                'parameters' => $this->a_params(TASK_GROUPS),
                'anchor' => t('menu_groups','admin'),
                'title' => t('menu_groups_title','admin')
                ),
            array(
                'parameters' => $this->a_params(TASK_GROUP_EDIT,$group_id),
                'anchor' => $this->get_groupname($group_id),
                'title' => t('groupmanager_group_menu_edit_title','admin')
                 ),
            array(
                'parameters' => $this->a_params(TASK_GROUP_CAPACITY_OVERVIEW,$group_id,$capacity_code),
                'anchor' => capacity_name($capacity_code),
                'title' => t('groupmanager_group_capacity_edit_title','admin')
                )
        );
        foreach($breadcrumbs as $b) {
            $this->output->add_breadcrumb($WAS_SCRIPT_NAME,$b['parameters'],array('title' => $b['title']),$b['anchor']);
        }
    } // show_breadcrumbs_groupcapacity()


    function show_menu_groupcapacity($group_id,$capacity_code,$current_task=NULL,$current_module_id=NULL) {
        global $WAS_SCRIPT_NAME;
        $group_id = intval($group_id);
        $capacity_code = intval($capacity_code);
        $menu_items = array(
            array(
                'parameters' => $this->a_params(TASK_GROUP_CAPACITY_OVERVIEW,$group_id,$capacity_code),
                'anchor' => t('menu_groupcapacity_overview','admin'),
                'title' => t('menu_groupcapacity_overview_title','admin')
            ),
            array(
                'parameters' => $this->a_params(TASK_GROUP_CAPACITY_INTRANET,$group_id,$capacity_code),
                'anchor' => t('menu_groupcapacity_intranet','admin'),
                'title' => t('menu_groupcapacity_intranet_title','admin')
            ),

// *** Commented out because Dirk needs to create realistic screenshots for the manual (2011-01-10/PF) ***
//            array(
//                'parameters' => $this->a_params(TASK_GROUP_CAPACITY_MODULE,$group_id,$capacity_code,1),
//                'anchor' => "Agenda (stub)",
//                'title' => t('menu_groupcapacity_module_title','admin')
//            ),
//            array(
//                'parameters' => $this->a_params(TASK_GROUP_CAPACITY_MODULE,$group_id,$capacity_code,2),
//                'anchor' => "Chat (stub)",
//                'title' => t('menu_groupcapacity_module_title','admin')
//            ),
//            array(
//                'parameters' => $this->a_params(TASK_GROUP_CAPACITY_MODULE,$group_id,$capacity_code,3),
//                'anchor' => "Forum (stub)",
//                'title' => t('menu_groupcapacity_module_title','admin')
//            ),
// ***

            array(
                'parameters' => $this->a_params(TASK_GROUP_CAPACITY_ADMIN,$group_id,$capacity_code),
                'anchor' => t('menu_groupcapacity_admin','admin'),
                'title' => t('menu_groupcapacity_admin_title','admin')
            )
        );
        if ($this->has_job_permission($group_id,$capacity_code,JOB_PERMISSION_PAGEMANAGER)) {
            $menu_items[] = array(
                'parameters' => $this->a_params(TASK_GROUP_CAPACITY_PAGEMANAGER,$group_id,$capacity_code),
                'anchor' => t('menu_groupcapacity_pagemanager','admin'),
                'title' => t('menu_groupcapacity_pagemanager_title','admin')
                );
        }
        $this->show_breadcrumbs_groupcapacity($group_id,$capacity_code);

        $this->output->add_menu('<h2>'.t('menu','admin').'</h2>');
        $this->output->add_menu('<ul>');
        foreach($menu_items as $item) {
            $attributes = array('title' => $item['title']);
            if ($current_task == $item['parameters']['task']) {
                // STUB. Need to take $current_module_id into account too!
                $this->output->add_breadcrumb($WAS_SCRIPT_NAME,$item['parameters'],$attributes,$item['anchor']);
                $attributes['class'] = 'current';
            }
            $this->output->add_menu('  <li>'.html_a($WAS_SCRIPT_NAME,$item['parameters'],$attributes,$item['anchor']));
        }
        $this->output->add_menu('</ul>');
    } // show_menu_groupcapacity()


    /** determine whether a group/capacity has permissions for a particular job
     *
     * this determines whether this group/capacity has permissions to access the
     * specified job, e.g. do they have access to the page manager. If so, we can
     * display the menu option, otherwise we can suppress it and keep the menu clean(er).
     *
     * @param int $group_id group to check
     * @param int $capacity_code capacity of this group to check
     * @param int job a bitmask indicating a particular job
     * @return bool TRUE if the group/capacity has the permission, FALSE otherwise
     */
    function has_job_permission($group_id,$capacity_code,$job) {
       if (($acl_id = $this->calc_acl_id($group_id,$capacity_code)) === FALSE) {
           return FALSE;
       }
       if (( $jobs = db_select_single_record('acls','permissions_jobs',array('acl_id' => $acl_id))) === FALSE) {
           return FALSE;
       }
       return (($jobs['permissions_jobs'] & $job) == $job) ? TRUE : FALSE;
    } // has_job_permission()

    /** display breadcrumb trail that leads to groups overview screen
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME;
     */
    function show_breadcrumbs_group() {
        global $WAS_SCRIPT_NAME;
        $breadcrumbs = array(
            array(
                'parameters' => array('job' => JOB_ACCOUNTMANAGER),
                'anchor' => t('name_accountmanager','admin'),
                'title' => t('description_accountmanager','admin')
            ),
            array(
                'parameters' => array('job' => JOB_ACCOUNTMANAGER, 'task' => TASK_GROUPS),
                'anchor' => t('menu_groups','admin'),
                'title' => t('menu_groups_title','admin')
            )
        );
        foreach($breadcrumbs as $b) {
            $this->output->add_breadcrumb($WAS_SCRIPT_NAME,$b['parameters'],array('title' => $b['title']),$b['anchor']);
        }
    } // show_breadcrumbs_group()


    /** show a menu for a group including links to the group's capacity overview screens
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME;
     */
    function show_menu_group($group_id,$current_task=NULL,$current_capacity_code=NULL) {
        global $WAS_SCRIPT_NAME;
        $group_id = intval($group_id);
        $menu_items = array(
            array(
                'parameters' => $this->a_params(TASK_GROUP_EDIT,$group_id),
                'anchor' => t('groupmanager_group_menu_edit','admin'),
                'title' => t('groupmanager_group_menu_edit_title','admin')
            )
        );
        $this->show_breadcrumbs_group();
        $this->output->add_menu('<h2>'.t('menu','admin').'</h2>');
        $this->output->add_menu('<ul>');
        foreach($menu_items as $item) {
            $attributes = array('title' => $item['title']);
            if ($current_task == $item['parameters']['task']) {
                $anchor = $this->get_groupname($group_id);
                $this->output->add_breadcrumb($WAS_SCRIPT_NAME,$item['parameters'],$attributes,$anchor);
                $attributes['class'] = 'current';
            }
            $this->output->add_menu('  <li>'.html_a($WAS_SCRIPT_NAME,$item['parameters'],$attributes,$item['anchor']));
        }
        $where = array('group_id' => $group_id);
        $records = db_select_all_records('groups_capacities','capacity_code',$where,'sort_order');
        if ($records !== FALSE) {
            foreach($records as $record) {
                $capacity_code = $record['capacity_code'];
                if ($capacity_code > CAPACITY_NONE) {
                    $a_params = $this->a_params(TASK_GROUP_CAPACITY_OVERVIEW,$group_id,$capacity_code);
                    $attributes = array('title' => t('groupmanager_group_capacity_edit_title','admin'));
                    if (($current_task == TASK_GROUP_CAPACITY_OVERVIEW) && ($current_capacity_code == $capacity_code)) {
                        $attributes['class'] = 'current';
                    }
                    $anchor = capacity_name($capacity_code);
                    $this->output->add_menu('  <li>'.html_a($WAS_SCRIPT_NAME,$a_params,$attributes,$anchor));
                }                    
            }
        }
        $this->output->add_menu('</ul>');
    } // show_menu_group()


    /** display breadcrumb trail that leads to the add new group dialog
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME;
     */
    function show_breadcrumbs_addgroup() {
        global $WAS_SCRIPT_NAME;

        $breadcrumbs = array(
            array(
                'parameters' => array('job' => JOB_ACCOUNTMANAGER),
                'anchor' => t('name_accountmanager','admin'),
                'title' => t('description_accountmanager','admin')
            ),
            array(
                'parameters' => array('job' => JOB_ACCOUNTMANAGER, 'task' => TASK_GROUPS),
                'anchor' => t('menu_groups','admin'),
                'title' => t('menu_groups_title','admin')
            ),
            array(
                'parameters' => array('job' => JOB_ACCOUNTMANAGER, 'task' => TASK_GROUP_ADD),
                'anchor' => t('groupmanager_add_a_group','admin'),
                'title' => t('groupmanager_add_a_group_title','admin')
            )
        );
        foreach($breadcrumbs as $b) {
            $this->output->add_breadcrumb($WAS_SCRIPT_NAME,$b['parameters'],array('title' => $b['title']),$b['anchor']);
        }
    } // show_breadcrumbs_addgroup()


    /** construct the add group dialog
     *
     * @return array contains the dialog definition
     */
    function get_dialogdef_add_group() {
        $dialogdef = array(
            'dialog' => array(
                'type' => F_INTEGER,
                'name' => 'dialog',
                'value' => GROUPMANAGER_DIALOG_ADD,
                'hidden' => TRUE
            ),
            'group_name' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'group_name',
                'minlength' => 1,
                'maxlength' => 255,
                'columns' => 30,
                'label' => t('groupmanager_add_group_name_label','admin'),
                'title' => t('groupmanager_add_group_name_title','admin'),
                'value' => '',
                ),
            'group_fullname' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'group_fullname',
                'minlength' => 1,
                'maxlength' => 255,
                'columns' => 30,
                'label' => t('groupmanager_add_group_fullname_label','admin'),
                'title' => t('groupmanager_add_group_fullname_title','admin'),
                'value' => '',
                ),
            'group_is_active' => array(
                'type' => F_CHECKBOX,
                'name' => 'group_is_active',
                'options' => array(1 => t('groupmanager_add_group_is_active_check','admin')),
                'label' => t('groupmanager_add_group_is_active_label','admin'),
                'title' => t('groupmanager_add_group_is_active_title','admin'),
                'value' => '1', // default is active
                )
            );
        $options = $this->get_options_capacities();
        for ($i=1; $i <= GROUPMANAGER_MAX_CAPACITIES; ++$i) {
            $dialogdef['group_capacity_'.$i] = array(
                'type' => F_LISTBOX,
                'name' => 'group_capacity_'.$i,
                'value' => strval(CAPACITY_NONE),
                'label' => t('groupmanager_add_group_capacity_label','admin',array('{INDEX}' => strval($i))),
                'title' => t('groupmanager_add_group_capacity_title','admin'),
                'options' => $options
                );
        }
        $dialogdef['button_save'] = dialog_buttondef(BUTTON_SAVE);
        $dialogdef['button_cancel'] = dialog_buttondef(BUTTON_CANCEL);
        return $dialogdef;
    } // get_dialogdef_add_group()


    /** construct the edit group dialog
     *
     * @param int $group_id the group that will be edited
     * @return array|bool FALSE on errors retrieving data, otherwise array containing the dialog definition
     */
    function get_dialogdef_edit_group($group_id) {
        $group_id = intval($group_id);
        // 1A -- retrieve data from groups-record
        $group = db_select_single_record('groups','*',array('group_id' => $group_id));
        if ($group === FALSE) {
            return FALSE;
        }
        // 1B -- retrieve the available capacities for this group (could be 0)
        $capacities = db_select_all_records('groups_capacities','*',array('group_id' => $group_id),'sort_order');
        if ($capacities === FALSE) {
            return FALSE;
        }
        // 2 -- construct dialog definition including current values from database
        $dialogdef = array(
            'dialog' => array(
                'type' => F_INTEGER,
                'name' => 'dialog',
                'value' => GROUPMANAGER_DIALOG_EDIT,
                'hidden' => TRUE
            ),
            'group_name' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'group_name',
                'minlength' => 1,
                'maxlength' => 255,
                'columns' => 30,
                'label' => t('groupmanager_edit_group_name_label','admin'),
                'title' => t('groupmanager_edit_group_name_title','admin'),
                'value' => $group['groupname'],
                'old_value' => $group['groupname']
                ),
            'group_fullname' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'group_fullname',
                'minlength' => 1,
                'maxlength' => 255,
                'columns' => 30,
                'label' => t('groupmanager_edit_group_fullname_label','admin'),
                'title' => t('groupmanager_edit_group_fullname_title','admin'),
                'value' => $group['full_name'],
                'old_value' => $group['full_name']
                ),
            'group_is_active' => array(
                'type' => F_CHECKBOX,
                'name' => 'group_is_active',
                'options' => array(1 => t('groupmanager_edit_group_is_active_check','admin')),
                'label' => t('groupmanager_edit_group_is_active_label','admin'),
                'title' => t('groupmanager_edit_group_is_active_title','admin'),
                'value' => (db_bool_is(TRUE,$group['is_active'])) ? '1' : '',
                'old_value' => (db_bool_is(TRUE,$group['is_active'])) ? '1' : ''
                )
            );
        $options = $this->get_options_capacities();
        for ($i=1; $i <= GROUPMANAGER_MAX_CAPACITIES; ++$i) {
            if (isset($capacities[$i-1])) {
                $value = intval($capacities[$i-1]['capacity_code']);
                $sort_order = intval($capacities[$i-1]['sort_order']);
                $acl_id = intval($capacities[$i-1]['acl_id']);
            } else {
                $value = CAPACITY_NONE;
                $sort_order = 0;
                $acl_id = 0;
            }
            $dialogdef['group_capacity_'.$i] = array(
                'type' => F_LISTBOX,
                'name' => 'group_capacity_'.$i,
                'value' => $value,
                'old_value' => $value,
                'label' => t('groupmanager_edit_group_capacity_label','admin',array('{INDEX}' => strval($i))),
                'title' => t('groupmanager_edit_group_capacity_title','admin'),
                'options' => $options,
                'sort_order' => $sort_order,
                'acl_id' => $acl_id
                );
        }
        $dialogdef['group_path'] = array(
                'type' => F_ALPHANUMERIC,
                'name' => 'group_path',
                'minlength' => 1,
                'maxlength' => 240,
                'columns' => 30,
                'label' => t('groupmanager_edit_group_path_label','admin'),
                'title' => t('groupmanager_edit_group_path_title','admin'),
                'value' => $group['path'],
                'old_value' => $group['path'],
                'viewonly' => TRUE
                );

        $dialogdef['button_save'] = dialog_buttondef(BUTTON_SAVE);
        $dialogdef['button_cancel'] = dialog_buttondef(BUTTON_CANCEL);
        return $dialogdef;
    } // get_dialogdef_edit_group()



    function get_options_capacities() {
        $options = array();
        for ($i = CAPACITY_NONE; $i < CAPACITY_NEXT_AVAILABLE; ++$i) {
            $options[$i] = capacity_name($i);
        }
        return $options;
    } // get_options_capacities()


    // ==================================================================
    // ======================== UTILITY ROUTINES ========================
    // ==================================================================

    /** return an array of group-capacity records (possibly buffered)
     *
     * @return array|bool FALSE on error, array with records otherwise (could be empty)
     * @uses $DB
     */
    function get_group_capacity_records($force=FALSE) {
        global $DB;
        if ((is_null($this->group_capacity_records)) || ($force)) {
            $sql = sprintf('SELECT  g.group_id, gc.capacity_code, g.groupname, g.full_name,g.is_active '.
                           'FROM %sgroups g LEFT JOIN %sgroups_capacities gc ON g.group_id = gc.group_id '.
                           'ORDER BY CASE WHEN (g.is_active) THEN 0 ELSE 1 END, g.groupname,gc.sort_order',
                            $DB->prefix,$DB->prefix);
            if (($DBResult = $DB->query($sql)) === FALSE) {
                if ($DB->debug) { trigger_error($DB->errno.'/\''.$DB->error.'\''); }
                return FALSE;
            } else {
                $records = $DBResult->fetch_all_assoc();
                $DBResult->close();
                $this->group_capacity_records = $records;
            }
        }
        return $this->group_capacity_records;
    } // get_group_capacity_records()


    /** shorthand for the anchor parameters that lead to the group manager
     *
     * @param string|null $task the next task to do or NULL if none
     * @param int|null $group_id the group of interest or NULL if none
     * @param int|null $capacity_code the capacity of interest or NULL if none
     * @param int|null $module_id the module of interest or NULL if none
     * @return array ready-to-use array with parameters for constructing a-tag
     */
    function a_params($task=NULL,$group_id=NULL,$capacity_code=NULL,$module_id=NULL) {
        $parameters = array('job' => JOB_ACCOUNTMANAGER);
        if (!is_null($task)) {
            $parameters['task'] = $task;
        }
        if (!is_null($group_id)) {
            $parameters['group'] = strval($group_id);
        }
        if (!is_null($capacity_code)) {
            $parameters['capacity'] = strval($capacity_code);
        }
        if (!is_null($module_id)) {
            $parameters['module'] = strval($module_id);
        }
        return $parameters;
    }

    /** construct a clickable icon to delete this group
     *
     * @param int $group_id the group to delete
     * @return string ready-to-use A-tag
     * @uses $CFG
     * @uses $USER
     * @uses $WAS_SCRIPT_NAME
     */
    function get_icon_delete($group_id) {
        global $CFG,$WAS_SCRIPT_NAME,$USER;

        // 1 -- construct the icon (image or text)
        $title = t('icon_group_delete','admin');
        if ($USER->high_visibility) {
            $anchor = html_tag('span','class="icon"','['.t('icon_group_delete_text','admin').']');
        } else {
            $img_attr = array('height' => 16, 'width' => 16, 'title' => $title, 'alt' => t('icon_group_delete_alt','admin'));
            $anchor = html_img($CFG->progwww_short.'/graphics/delete.gif',$img_attr);
        }

        // 2 -- construct the A tag
        $a_params = $this->a_params(TASK_GROUP_DELETE,$group_id);
        $a_attr = array('title' => $title);
        return html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
    } // get_icon_delete()


    /** construct a clickable icon to edit the properties of this group
     *
     * @param int $group_id the group to edit
     * @return string ready-to-use A-tag
     * @uses $CFG
     * @uses $USER
     * @uses $WAS_SCRIPT_NAME
     */
    function get_icon_edit($group_id) {
        global $CFG,$WAS_SCRIPT_NAME,$USER;


        // 1 -- construct the icon (image or text)
        $title = t('icon_group_edit','admin');
        if ($USER->high_visibility) {
            $anchor = html_tag('span','class="icon"','['.t('icon_group_edit_text','admin').']');
        } else {
            $img_attr = array('height' => 16, 'width' => 16, 'title' => $title, 'alt' => t('icon_area_edit_alt','admin'));
            $anchor = html_img($CFG->progwww_short.'/graphics/edit.gif',$img_attr);
        }

        // 2 -- construct the A tag
        $a_params = $this->a_params(TASK_GROUP_EDIT,$group_id);
        $a_attr = array('title' => $title);
        return html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
    } // get_icon_edit()


    function get_groupname($group_id) {
        $group_id = intval($group_id);
        $record = db_select_single_record('groups','groupname',array('group_id'=>$group_id));
        return ($record === FALSE) ? "($group_id)" : $record['groupname'];
    }

    function calc_acl_id($group_id,$capacity_code) {
        $where = array('group_id' => $group_id, 'capacity_code' => $capacity_code);
        $record = db_select_single_record('groups_capacities','acl_id',$where);
        if ($record === FALSE) {
            logger(sprintf("groupmanager->calc_acl_id(): cannot retrieve acl_id: group='%d' and capacity='%d': %s",
			$group_id,$capacity_code),db_errormessage());
            $this->output->add_message(t('error_retrieving_data','admin'));
            $this->groups_overview();
            return FALSE;
        }
        return intval($record['acl_id']);
    } // calc_acl_id()

    function valid_group_capacity($group_id,$capacity_code) {
        if ((is_null($group_id)) || (is_null($capacity_code))) {
            logger(sprintf("groupmanager: invalid parameters: group='%s', capacity='%s'",
                            is_null($group_id) ? 'NULL' : strval($group_id),
                            is_null($capacity_code) ? 'NULL' : strval($capacity_code)));
            $this->output->add_message(t('error_invalid_parameters','admin'));
            return FALSE;
        }
        return TRUE;
    } // valid_group_capacity()

    function get_group_capacity_names($group_id,$capacity_code=0) {
        $record  = db_select_single_record('groups',array('groupname','full_name'),array('group_id' => $group_id));
        return array('{GROUP}' => $record['groupname'],
                     '{GROUP_FULL_NAME}' => $record['full_name'],
                     '{CAPACITY}' => capacity_name($capacity_code));
    }


    /** remove all records relating to 1 or more acl_id's from various acl-tables
     *
     * this bluntly removes all records from the various acls* tables for the specified acl_id's.
     * Whenever there's an error deleting records, the routine bails out immediately and returns FALSE.
     * If all goes well, TRUE is returned. Any errors are logged, success is logged to DEBUG-log.
     *
     * @param int|array $acl the key(s) to the ACL(s) to delete
     * @return bool TRUE on success, FALSE on failure
     * @todo should this routine be moved to an acl-object? Hmmm....
     */
    function acl_delete($acl) {
        $tables = array('acls_areas','acls_nodes','acls_modules_areas','acls_modules_nodes','acls_modules','acls');
        $message = 'acl_delete(acl_id=';
        if (is_array($acl)) {
            $glue = '';
            $comma = '';
            $where = '(';
            foreach($acl as $acl_id) {
                $where .= sprintf("%s(acl_id = %d)",$glue,intval($acl_id));
                $glue = ' OR ';
                $message .= $comma.$acl_id;
                $comma = ',';
            }
            $where .= ')';
        } else {
            $where = array('acl_id' => intval($acl));
            $message .= strval(intval($acl));
        }
        $message .= ') rows: ';
        foreach($tables as $table) {
            if (($rowcount = db_delete($table,$where)) === FALSE) {
                $message .= sprintf(" '%s': FAILED. I'm outta here (%s)",$table,db_errormessage());
                logger($message);
                return FALSE;
            } else {
                $message .= sprintf(" '%s':%d",$table,$rowcount);
            }
        }
        logger($message,LOG_DEBUG);
        return TRUE;
    } // acl_delete()

    function add_group_capacity($group_id,$capacity_code,$sort_order) {
        static $fields_acl = array(
            'permissions_intranet' => ACL_ROLE_NONE,
            'permissions_modules' => ACL_ROLE_NONE,
            'permissions_jobs' => ACL_ROLE_NONE,
            'permissions_nodes' => ACL_ROLE_NONE
            );
        //
        // 1 -- create an acl (with no permissions whatsoever) and remember the new acl_id
        //
        $new_acl_id = db_insert_into_and_get_id('acls',$fields_acl,'acl_id');
        if ($new_acl_id === FALSE) {
            logger(sprintf("add_group_capacity(): adding new acl for group/capacity '%d/%d' failed: %s",
                            $group_id, $capacity_code, db_errormessage()));
            return FALSE;
        }
        //
        // 2 -- subsequently add a new group-capacity record pointing to this new acl
        //
        $fields = array(
            'group_id' => intval($group_id),
            'capacity_code' => intval($capacity_code),
            'sort_order' => intval($sort_order),
            'acl_id' => $new_acl_id
            );
        if (db_insert_into('groups_capacities',$fields) === FALSE) {
            logger(sprintf("add_group_capacity(): adding new record for group/capacity '%d/%d' failed: %s",
                           $group_id, $capacity_code, db_errormessage()));
            $retval = db_delete('acls',array('acl_id' => $new_acl_id));
            logger(sprintf("add_group_capacity(): removing freshly created acl '%d': %s",
                           $new_acl_id, 
                           ($retval !== FALSE) ? 'success' : 'failure: '.db_errormessage()));
            return FALSE;
        }
        logger(sprintf("add_group_capacity(): success adding group/capacity '%d/%d' with acl_id='%d'",
                       $group_id, $capacity_code,$new_acl_id),LOG_DEBUG);
        return TRUE;
    } // add_group_capacity()

    /** actually remove a group and all associated data
     *
     * this actually deletes the group $group_id and associated data, in the following order:
     * First all acls associated with the group-capacities are deleted.
     * If that worked, we delete the group-capacity records.
     * If that worked, we delete the group record itself.
     *
     * @param int $group_id the group to delete
     * @return bool FALSE if there were errors, TRUE if delete was completely successful
     *
     * @todo should we also require the user to delete any files associated with the area before we even consider
     *       deleting it? Or is is OK to leave the files and still delete the area. We do require that nodes are
     *       removed from the area, but that is mainly because of maintaining referential integrity. Mmmmm... Maybe
     *       that applies to the files as well, especially in a private area. Food for thought.
     * @todo since multiple tables are involved, shouldn't we use transaction/rollback/commit?
     *       Q: How well is MySQL suited for transactions? A: Mmmmm.... Which version? Which storage engine?
     */
    function delete_group_capacities_acls($group_id) {
        $group_id = intval($group_id);
        $where = array('group_id' => $group_id);

        // FIXME: we need to do something with path
        // $record  = db_select_single_record('groups',array('path'),$where);
        // do_something_with_group_files_and_then_rmdir($record['path']);

        //
        // 1 -- first try to get rid of the acls associated with the associated capacities
        //
        $capacities = db_select_all_records('groups_capacities','acl_id',$where,'sort_order');
        if ($capacities === FALSE) {
            logger("delete_group_capacities_acls(): error retrieving acls group '$group_id': ".db_errormessage());
            return FALSE;
        }
        if (sizeof($capacities) > 0) {
            $acls = array();
            foreach($capacities as $capacity) {
                $acls[] = $capacity['acl_id'];
            }
            if ($this->acl_delete($acls) === FALSE) {
                logger("delete_group_capacities_acls(): error deleting group '$group_id'");
                return FALSE;
            }
        }

        //
        // 2 -- now remove group-capacities and the user associations and the group itself
        //
        $tables = array('users_groups_capacities','groups_capacities','groups');
        $message = sprintf("delete_group_capacities_acls(group_id=%d) rows: ",$group_id);
        foreach($tables as $table) {
            if (($rowcount = db_delete($table,$where)) === FALSE) {
                $message .= sprintf(" '%s': FAILED. I'm outta here (%s)",$table,db_errormessage());
                logger($message);
                return FALSE;
            } else {
                $message .= sprintf(" '%s':%d",$table,$rowcount);
            }
        }
        logger($message,LOG_DEBUG);
        return TRUE;
    } // delete_group_capacities_acls()


    /** manipulate the current state if indicator(s) for 'open' and 'closed' areas
     * 
     * this manipulates the current state of 'open' and 'closed' areas in $areas_open.
     * If $area_id is NULL, we don't have to do anything but simply return the current state.
     * If $area_id is 0 (zero), we need to toggle all areas at once (area_id = 0 implies the site level toggle)
     * If $area_id is an integer, it is assumed to be a valid area_id and that area should be toggled.
     *
     * @param array|bool $areas_open current state of indicator(s) for 'open' and 'closed' areas
     * @param int|null $area_id the area to expand/collapse or NULL if nothing needs to be done
     * @return array|bool new state of indicator(s) for 'open' and 'closed' areas
     */
    function areas_expand_collapse($areas_open,$area_id) {

        // 0 -- anything to do?
        if (!is_int($area_id)) {
            return $areas_open;
        }

        // 1 -- toggle site-level?
        if ($area_id == 0) {
            $areas_open = ((is_array($areas_open)) || ($areas_open === TRUE)) ? FALSE : TRUE;
            return $areas_open;
        }

        // 2 -- still here? must be individual area then
        // 2A -- old: every area closed; new: a single area opened
        if ($areas_open === FALSE) {
            $areas_open = array($area_id => TRUE);
            return $areas_open;
        }
        // 2B -- old: some open, some closed
        if (is_array($areas_open)) {
            $areas_open[$area_id] = ((isset($areas_open[$area_id])) && ($areas_open[$area_id])) ? FALSE : TRUE;
            // if this is the last one set to FALSE, all areas are now 'closed' and we should return FALSE and no array
            if ($areas_open[$area_id]) {
                return $areas_open;
            } else {
                foreach($areas_open as $k => $v) {
                    if ($v) {
                        return $areas_open; // there was at least 1 other area 'open', so stick to an array
                    }
                }
                // still here? then all areas were closed: return FALSE;
                return FALSE;
            }
        }
        // 2C -- old: all opened; new: a single area is closed
        // At this point we start with all areas opened, and only area area_id must be closed.
        // That means that we have to create an array of areas and set every area's value to TRUE,
        // except area area_id.
        $records = db_select_all_records('areas','area_id','','','area_id');
        if ($records === FALSE) {
            logger('areas_expand_collapse(): cannot retrieve areas. Mmmm...',LOG_DEBUG);
            return TRUE;
        }
        $open_areas = array();
        foreach($records as $k => $v) {
            $open_areas[$k] = TRUE;
        }
        $open_areas[$area_id] = FALSE;
        unset($records);
        return $open_areas;
    } // areas_expand_collapse()


} // GroupManager
?>