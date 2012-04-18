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

/** /program/lib/groupmanager.class.php - taking care of group management
 *
 * This file defines a class for dealing with groups.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: groupmanager.class.php,v 1.11 2012/04/18 07:57:36 pfokker Exp $
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

    /** @var array used to cache group records keyed by group_id */
    var $groups = array();

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
        $this->groups = array();

        $task = get_parameter_string('task',TASK_GROUPS);
        switch($task) {
        case TASK_GROUPS:                     $this->groups_overview();      break;
        case TASK_GROUP_ADD:                  $this->group_add();            break;
        case TASK_GROUP_SAVE_NEW:             $this->group_savenew();        break;
        case TASK_GROUP_EDIT:                 $this->group_edit();           break;
        case TASK_GROUP_SAVE:                 $this->group_save();           break;
        case TASK_GROUP_DELETE:               $this->group_delete();         break;
        case TASK_GROUP_CAPACITY_OVERVIEW:    $this->capacity_overview();    break;
        case TASK_GROUP_CAPACITY_INTRANET:    $this->capacity_intranet();    break;
        case TASK_GROUP_CAPACITY_ADMIN:       $this->capacity_admin();       break;
        case TASK_GROUP_CAPACITY_PAGEMANAGER: $this->capacity_pagemanager(); break;
        case TASK_GROUP_CAPACITY_SAVE:        $this->capacity_save();        break;
        case TASK_GROUP_CAPACITY_MODULE:
            $this->output->add_message("STUB: task '$task' not yet implemented");
            $this->output->add_message('group = '.get_parameter_string('group','(unset)'));
            $this->output->add_message('capacity = '.get_parameter_string('capacity','(unset)'));
            $this->output->add_message('module = '.get_parameter_string('module','(unset)'));
            $this->groups_overview();
            break;
        default:
            $s = (utf8_strlen($task) <= 50) ? $task : utf8_substr($task,0,44).' (...)';
            $message = t('task_unknown','admin',array('{TASK}' => htmlspecialchars($s)));
            $output->add_message($message);
            logger(sprintf('%s.%s(): unknown task: \'%s\'',__CLASS__,__FUNCTION__,htmlspecialchars($s)));
            $this->groups_overview();
            break;
        }
    } // GroupManager()

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
        if (!$this->output->text_only) {
            $icon_blank = '    '.$this->output->skin->get_icon('blank');
            for ($i=0; $i<2; ++$i) {
                $this->output->add_content($icon_blank);
            }
        } // else
            // don't clutter the text-only interface with superfluous layout fillers
        $a_attr = array('title'=> t('groupmanager_add_a_group_title','admin'));
        $a_params = $this->a_params(TASK_GROUP_ADD);
        $this->output->add_content('    '.html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,t('groupmanager_add_a_group','admin')));

        // 3 -- Construct a list of existing groups if any
        $records = $this->get_group_capacity_records();
        if ($records === FALSE) {
            $this->output->add_message(t('error_retrieving_data','admin'));
            logger(sprintf('%s.%s(): cannot retrieve list of groups+capacities: %s',
                           __CLASS__,__FUNCTION__,db_errormessage()));
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
        if (($new_group_id = db_insert_into_and_get_id('groups',$fields,'group_id')) === FALSE) {
            logger(sprintf("%s.%s(): saving new group '%s' failed: %s",
                           __CLASS__,__FUNCTION__,$groupname,db_errormessage()));
            if ($groupdata_directory_created) { // Only get rid of the directory _we_ created
                @unlink($groupdata_full_path.'/index.html');
                @rmdir($groupdata_full_path);
            }
            $this->output->add_message(t('groupmanager_savenew_group_failure','admin'));
            $this->groups_overview();
            return;
        }
        // 3B -- create 0, 1 or more group/capacities
        $capacity_sort_order = 0;
        for ($i=1; $i <= GROUPMANAGER_MAX_CAPACITIES; ++$i) {
            $k = 'group_capacity_'.$i;
            if (($capacity_code = intval($dialogdef[$k]['value'])) != CAPACITY_NONE) {
                if ($this->add_group_capacity($new_group_id,$capacity_code,++$capacity_sort_order) === FALSE) {
                    if ($groupdata_directory_created) { // Only get rid of the directory _we_ created
                        @unlink($groupdata_full_path.'/index.html');
                        @rmdir($groupdata_full_path);
                    }
                    $this->output->add_message(t('groupmanager_savenew_group_failure','admin'));
                    $this->groups_overview();
                    return;
                }
            }
        }

        // 4 -- tell user about success
        $params = array('{GROUP}' => $groupname,'{GROUP_FULL_NAME}' => $group_fullname);
        $this->output->add_message(t('groupmanager_savenew_group_success','admin',$params));
        logger(sprintf("%s.%s(): success saving new group '%d' %s (%s) and datadir /groups/%s",
                       __CLASS__,__FUNCTION__,$new_group_id,$groupname,$group_fullname,$groupdata_directory));
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
            logger(sprintf("%s.%s(): unspecified parameter group",__CLASS__,__FUNCTION__));
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
        global $WAS_SCRIPT_NAME,$USER;

        // 0 -- sanity check
        $group_id = get_parameter_int('group',NULL);
        if (is_null($group_id)) {
            logger(sprintf("%s.%s(): unspecified parameter group",__CLASS__,__FUNCTION__));
            $this->output->add_message(t('error_invalid_parameters','admin'));
            $this->groups_overview();
            return;
        }

        // 1 -- bail out if the user pressed cancel button
        if (isset($_POST['button_cancel'])) {
            $this->output->add_message(t('cancelled','admin'));
            $this->groups_overview();
            return;
        }

        // 2 -- validate the data
        $dialogdef = $this->get_dialogdef_edit_group($group_id);
        if ($dialogdef === FALSE) {
            $this->output->add_message(t('error_retrieving_data','admin'));
            $this->groups_overview();
            return;
        }
        // 2A -- remember generic errors (string too short, number too small, etc)
        $invalid = FALSE;
        if (!dialog_validate($dialogdef)) {
            $invalid = TRUE;
        }
        // 2B -- check out the groupname: this field should be unique
        $record = db_select_single_record('groups','group_id',array('groupname' => $dialogdef['group_name']['value']));
        if (($record !== FALSE) && (intval($record['group_id']) != $group_id)) { // Another group exists with this name
            ++$dialogdef['group_name']['errors'];
            $fname = (isset($dialogdef['group_name']['label'])) ? str_replace('~','',$dialogdef['group_name']['label'])
                                                                : $dialogdef['group_name']['name'];
            $dialogdef['group_name']['error_messages'][] = t('validate_not_unique','',array('{FIELD}'=>$fname));
            $invalid = TRUE;
        }
        // 2C -- watch out for ACLs marked for deletion that are related to this $USER
        $new = array();
        for ($i=1; $i <= GROUPMANAGER_MAX_CAPACITIES; ++$i) {
            if (($capacity_code = intval($dialogdef['group_capacity_'.$i]['value'])) != CAPACITY_NONE) {
                $new[$capacity_code] = $capacity_code;
            }
        }
        for ($i=1; $i <= GROUPMANAGER_MAX_CAPACITIES; ++$i) {
            $k = 'group_capacity_'.$i;
            if ((($capacity_code = intval($dialogdef[$k]['old_value'])) != CAPACITY_NONE) &&
                (!isset($new[$capacity_code]))) { // this existing capacity code is marked for deletion
                $acl_id = intval($dialogdef[$k]['acl_id']);
                if (isset($USER->related_acls[$acl_id])) { // Houston we have a problem: user's membership at stake
                    ++$dialogdef[$k]['errors'];
                    $params = $this->get_group_capacity_names($group_id,$capacity_code);
                    $params['{FIELD}'] = (isset($dialogdef[$k]['label'])) ? str_replace('~','',$dialogdef[$k]['label'])
                                                                          : $dialogdef[$k]['name'];
                    $dialogdef[$k]['error_messages'][] = t('usermanager_delete_group_capacity_not_self','admin',$params);
                    $invalid = TRUE;
                }
            }
        }
        // 2D -- if there were any errors go redo dialog while keeping data already entered
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
        $errors = 0;
        // 3A -- maybe update group record in table 'groups'
        $fields = array();
        if ($dialogdef['group_name']['value'] != $dialogdef['group_name']['old_value']) {
            $fields['groupname'] = $dialogdef['group_name']['value'];
        }
        if ($dialogdef['group_fullname']['value'] != $dialogdef['group_fullname']['old_value']) {
            $fields['full_name'] = $dialogdef['group_fullname']['value'];
        }
        if ($dialogdef['group_is_active']['value'] != $dialogdef['group_is_active']['old_value']) {
            $fields['is_active'] = ($dialogdef['group_is_active']['value'] == 1) ? TRUE : FALSE;
        }
        $table = 'groups';
        if (sizeof($fields) > 0) {
            $where = array('group_id' => $group_id);
            if (db_update($table,$fields,$where) === FALSE) {
                ++$errors;
                logger(sprintf("%s.%s(): error saving data group '%d' (%s) in table '%s': %s",__CLASS__,__FUNCTION__,
                               $group_id,$dialogdef['group_name']['value'],$table,db_errormessage()));
            } else {
                logger(sprintf("%s.%s(): success saving changes to group '%d' (%s) in table '%s'",
                               __CLASS__,__FUNCTION__,$group_id,$dialogdef['group_name']['value'],$table),WLOG_DEBUG);
            }
        } else {
            logger(sprintf("%s.%s(): no changes to save for group '%d' (%s) in table '%s'",
                           __CLASS__,__FUNCTION__,$group_id,$dialogdef['group_name']['value'],$table),WLOG_DEBUG);
        }

        // 4 -- maybe add, change or delete child records for this group (ie. links to capacities etc.)
        // 4A -- collect the lists of old capacities (with sort order and acl) and new capacities (with new sort order)
        $new = array();
        $old = array(); 
        $sort_order_new = 0;
        for ($i=1; $i <= GROUPMANAGER_MAX_CAPACITIES; ++$i) {
            $k = 'group_capacity_'.$i;
            if ((($capacity_code = intval($dialogdef[$k]['value'])) != CAPACITY_NONE) && (!isset($new[$capacity_code]))){
                $new[$capacity_code] = ++$sort_order_new;
            }
            if (($capacity_code = intval($dialogdef[$k]['old_value'])) != CAPACITY_NONE) {
                 $old[$capacity_code] = array('sort_order' => intval($dialogdef[$k]['sort_order']),
                                              'acl_id'     => intval($dialogdef[$k]['acl_id']));
            }
        }
        // 4B -- handle deletions and updates
        foreach($old as $capacity_code => $v) {
            $acl_id     = $v['acl_id'];
            $sort_order = $v['sort_order'];
            $where_group_capacity = array('group_id' => $group_id, 'capacity_code' => $capacity_code);
            if (!isset($new[$capacity_code])) {
                //
                // 4B1 -- get rid of this group/capacity and acl (in correct order due to FK constraints)
                $where_acl_id = array('acl_id' => $acl_id);
                $table_wheres = array(
                    'acls_areas'              => $where_acl_id,
                    'acls_nodes'              => $where_acl_id,
                    'acls_modules'            => $where_acl_id,
                    'acls_modules_areas'      => $where_acl_id,
                    'acls_modules_nodes'      => $where_acl_id,
                    'users_groups_capacities' => $where_group_capacity,
                    'groups_capacities'       => $where_group_capacity,
                    'acls'                    => $where_acl_id);
                $message = sprintf("%s.%s(): del group/capacity=%d/%d:",__CLASS__,__FUNCTION__,$group_id,$capacity_code);
                foreach($table_wheres as $table => $where) {
                    if (($rowcount = db_delete($table,$where)) === FALSE) {
                        ++$errors;
                        logger(sprintf("%s.%s(): delete from '%s' failed: %s",
                                       __CLASS__,__FUNCTION__,$table,db_errormessage()));
                    } else {
                        $message .= sprintf(" '%s':%d",$table,$rowcount);
                    }
                }
                logger($message,WLOG_DEBUG);
            } elseif ($sort_order != $new[$capacity_code]) {
                //
                // 4B2 -- save changed sort order of this group/capacity
                $fields = array('sort_order' => $new[$capacity_code]);
                if (db_update('groups_capacities',$fields,$where_group_capacity) === FALSE) {
                    ++$errors;
                    logger(sprintf("%s.%s(): cannot update sort order in  group/capacity '%d/%d': %s",
                                   __CLASS__,__FUNCTION__,$group_id,$capacity_code,db_errormessage()));
                } else {
                    logger(sprintf("%s.%s(): success changing sort order '%d' -> '%d' for group/capacity '%d/%d'",
                                   __CLASS__,__FUNCTION__,
                                   $sort_order,$new[$capacity_code],$group_id,$capacity_code),WLOG_DEBUG);
                }
            } //  else
                // no changes
        }

        // 4C -- add new group/capacities when necessary
        foreach($new as $capacity_code => $sort_order) {
            if (!isset($old[$capacity_code])) {
                if ($this->add_group_capacity($group_id,$capacity_code,$sort_order) === FALSE) {
                    ++$errors;
                }
            }
        }
        // 5 -- all done
        $params = array(
            '{GROUP}'           => $dialogdef['group_name']['value'],
            '{GROUP_FULL_NAME}' => $dialogdef['group_name']['value'],
            '{ERRORS}'          => $errors);
        if ($errors == 0) {
            $this->output->add_message(t('groupmanager_edit_group_success','admin',$params));
        } else {
            $this->output->add_message(t('errors_saving_data','admin',$params));
        }
        logger(sprintf("%s.%s(): %s saving group '%d' '%s' (%s)",
                       __CLASS__,__FUNCTION__,
                       ($errors == 0) ? "success" : "failure",
                       $group_id,$params['{GROUP}'],$params['{GROUP_FULL_NAME}']));
        $this->groups_overview();
        return;
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
     * @todo since multiple tables are involved, shouldn't we use transaction/rollback/commit?
     *       Q: How well is MySQL suited for transactions? A: Mmmmm.... Which version? Which storage engine?
     */
    function group_delete() {
        global $WAS_SCRIPT_NAME,$DB,$USER;
        //
        // 0 -- sanity check
        //
        $group_id = get_parameter_int('group',NULL);
        if (is_null($group_id)) {
            logger(sprintf("%s.%s(): unspecified parameter group",__CLASS__,__FUNCTION__));
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

        // 2A -- do not allow the user to remove a group associated with the user
        $where = array('user_id' => intval($USER->user_id),'group_id' => $group_id);
        if (($record = db_select_single_record('users_groups_capacities','capacity_code',$where)) !== FALSE) {
            // Oops, the current user happens to be a member of this group
            $params = $this->get_group_capacity_names($group_id,$record['capacity_code']);
            logger(sprintf("%s.%s(): user attempts to remove group '%s' ('%d', '%s') but she is a '%s'",
                            __CLASS__,__FUNCTION__,$params['{GROUP}'],$group_id,
                            $params['{GROUP_FULL_NAME}'],$params['{CAPACITY}']));
            $this->output->add_message(t('usermanager_delete_group_not_self','admin',$params));
            $this->groups_overview();
            return;
        }

        // 2B -- are there any files left in this user's private storage $CFG->datadir.'/groups/'.$path?
        if (($group = $this->get_group_record($group_id)) === FALSE) {
            $this->groups_overview();
            return;
        }
        $path = '/groups/'.$group['path'];
        if (!userdir_is_empty($path)) {
            // At this point we know there are still files associated with this
            // group in the data directory. This is a show stopper; it is up to the
            // admin requesting this delete to get rid of the files first (eg via File Manager)
            logger(sprintf("%s.%s(): data directory '%s' not empty",__CLASS__,__FUNCTION__,$path));
            $params = $this->get_group_capacity_names($group_id);
            $this->output->add_message(t('usermanager_delete_group_dir_not_empty','admin',$params));
            $this->groups_overview();
            return;
        }

        //
        // 3 -- user has confirmed delete?
        //
        if ((isset($_POST['button_delete'])) &&
            (isset($_POST['dialog'])) && ($_POST['dialog'] == GROUPMANAGER_DIALOG_DELETE)) {
            $params = $this->get_group_capacity_names($group_id); // pick up name before it is gone
            if ((userdir_delete($path)) &&  $this->delete_group_capacities_records($group_id)) {
                $this->output->add_message(t('groupmanager_delete_group_success','admin',$params));
                $retval = TRUE;
            } else {
                $this->output->add_message(t('groupmanager_delete_group_failure','admin',$params));
                $retval = FALSE;
            }
            logger(sprintf("%s.%s(): %s deleting group '%d' %s (%s)",
                           __CLASS__,__FUNCTION__,($retval === FALSE) ? 'failure' : 'success',
                           $group_id,$params['{GROUP}'],$params['{GROUP_FULL_NAME}']));
            $this->groups_overview();
            return;
        }

        //
        // 4 -- no delete yet, first show confirmation dialog
        //
        // Dialog is very simple: a simple text showing
        // - the name of the group
        // - the names of the associated capacities with number of users associated
        // - a Delete and a Cancel button
        //
        $dialogdef = array(
            'dialog' => array('type'=>F_INTEGER,'name'=>'dialog','value'=>GROUPMANAGER_DIALOG_DELETE,'hidden'=>TRUE),
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
            logger(sprintf('%s.%s(): database error: %s',__CLASS__,__FUNCTION__,db_errormessage()));
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
            logger(sprintf("%s.%s(): weird: 'dialog' not set",__CLASS__,__FUNCTION__),WLOG_DEBUG);
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
            logger(sprintf("%s.%s(): weird: dialog='%d'. Huh?",__CLASS__,__FUNCTION__,$dialog),WLOG_DEBUG);
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


    /** show a menu for a group capacity with options to modify privileges, etc. etc.
     *
     * @param int $group_id the group of interest
     * @param int $capacity_code identifies the capacity to manage
     * @param string|null $current_task the name of the task to emphasise in the menu (underlined)
     * @param string|null $current_capacity_code the name of the capacity to emphasise in the menu (underlined)
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME;
     */ 
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
     * @param int $group_id the group of interest
     * @param string|null $current_task the name of the task to emphasise in the menu (underlined)
     * @param string|null $current_capacity_code the name of the capacity to emphasise in the menu (underlined)
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
                'maxlength' => 60,
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
        if (($group = $this->get_group_record($group_id))=== FALSE) {
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
                'maxlength' => 60,
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
                'maxlength' => 60,
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

    /** construct a simple option list with all available capacity names keyed by capacity code
     *
     * @return array ready to use array with capacity names keyed by capacity code
     */
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
        $alt = t('icon_group_delete_alt','admin');
        $text = t('icon_group_delete_text','admin');
        $anchor = $this->output->skin->get_icon('delete', $title, $alt, $text);

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
        $alt = t('icon_group_edit_alt','admin');
        $text = t('icon_group_edit_text','admin');
        $anchor = $this->output->skin->get_icon('edit', $title, $alt, $text);

        // 2 -- construct the A tag
        $a_params = $this->a_params(TASK_GROUP_EDIT,$group_id);
        $a_attr = array('title' => $title);
        return html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
    } // get_icon_edit()


    /** shorthand to get the name of a group
     *
     * @param int $group_id the group of interest
     * @return string the name of the group
     */
    function get_groupname($group_id) {
        $record = $this->get_group_record($group_id);
        return ($record === FALSE) ? "($group_id)" : $record['groupname'];
    } // get_groupname()

    /** retrieve the acl_id for a particular group/capacity from the database
     *
     * @param int $group_id the group to examine
     * @param int $capacity_code the capacity to examine
     * @return int|bool FALSE on error, acl_id on success
     */
    function calc_acl_id($group_id,$capacity_code) {
        $where = array('group_id' => $group_id, 'capacity_code' => $capacity_code);
        $record = db_select_single_record('groups_capacities','acl_id',$where);
        if ($record === FALSE) {
            logger(sprintf("%s.%s(): cannot retrieve acl_id: group='%d' and capacity='%d': %s",
                           __CLASS__,__FUNCTION__,$group_id,$capacity_code,db_errormessage()));
            $this->output->add_message(t('error_retrieving_data','admin'));
            $this->groups_overview();
            return FALSE;
        }
        return intval($record['acl_id']);
    } // calc_acl_id()

    /** shorthand to test the validity of a particular group/capacity
     *
     * @param int $group_id the group to examine
     * @param int $capacity_code the capacity to examine
     * @return bool TRUE if valid combination, FALSE otherwise
     */
    function valid_group_capacity($group_id,$capacity_code) {
        if ((is_null($group_id)) || (is_null($capacity_code))) {
            logger(sprintf("%s.%s(): invalid parameters: group='%s', capacity='%s'",
                            __CLASS__,__FUNCTION__,
                            is_null($group_id) ? 'NULL' : strval($group_id),
                            is_null($capacity_code) ? 'NULL' : strval($capacity_code)));
            $this->output->add_message(t('error_invalid_parameters','admin'));
            return FALSE;
        }
        return TRUE;
    } // valid_group_capacity()


    /** shortcut to retrieve the name and full name of the selected group and optionally a capacity name
     *
     * @param int $group_id identifies the group of interest
     * @param int $capacity_code identifies the capacity (optional)
     * @return array with ready-to-use name/full_name/capacity name
     */
    function get_group_capacity_names($group_id,$capacity_code=0) {
        if (($record = $this->get_group_record($group_id)) === FALSE) {
            $record = array('groupname' => strval($group_id),'full_name' => strval($group_id));
        }
        return array('{GROUP}' => $record['groupname'],
                     '{GROUP_FULL_NAME}' => $record['full_name'],
                     '{CAPACITY}' => capacity_name($capacity_code));
    } // get_group_capacity_names()


    /** add a group/capacity and corresponding acl to the database
     *
     * @param int $group_id group of interest
     * @param int $capacity_code the capacity
     * @param int $sort_order
     * @return bool TRUE on success, FALSE otherwise
     */
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
            logger(sprintf("%s.%s(): adding new acl for group/capacity '%d/%d' failed: %s",
                            __CLASS__,__FUNCTION__,$group_id, $capacity_code, db_errormessage()));
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
            logger(sprintf("%s.%s(): adding new record for group/capacity '%d/%d' failed: %s",
                            __CLASS__,__FUNCTION__,$group_id, $capacity_code, db_errormessage()));
            $retval = db_delete('acls',array('acl_id' => $new_acl_id));
            logger(sprintf("%s.%s(): removing freshly created acl '%d': %s",
                            __CLASS__,__FUNCTION__,$new_acl_id, 
                           ($retval !== FALSE) ? 'success' : 'failure: '.db_errormessage()));
            return FALSE;
        }
        logger(sprintf("%s.%s(): success adding group/capacity '%d/%d' with acl_id='%d'",
                        __CLASS__,__FUNCTION__,$group_id, $capacity_code,$new_acl_id),WLOG_DEBUG);
        return TRUE;
    } // add_group_capacity()


    /** actually remove a group and all associated records
     *
     * this actually deletes the group $group_id and associated records, in a specific order,
     * to satisfy the FK constraints. ACL's are deleted last if there are any at all.
     *
     * Note
     * This routine looks a lot like the corresponding one in the UserManager. However, we don't
     * know in advance how many ACLs are associated with this group whereas a user record always
     * has exactly 1 ACL. This explains the logic in step 1 below.
     *
     * @param int $group_id the group to delete
     * @return bool FALSE if there were errors, TRUE if delete was completely successful
     *
     * @todo since multiple tables are involved, shouldn't we use transaction/rollback/commit?
     *       Q: How well is MySQL suited for transactions? A: Mmmmm.... Which version? Which storage engine?
     */
    function delete_group_capacities_records($group_id) {
        $group_id = intval($group_id);

        // 1 -- prepare a todo list for deletes related to group_id (order is important because of FK constraints)
        $where_group_id = array('group_id' => $group_id);
        $table = 'groups_capacities';
        $fields = 'acl_id';
        $sort_order = 'sort_order';
        if (($records = db_select_all_records($table,$fields,$where_group_id,$sort_order)) === FALSE) {
            logger(sprintf("%s.%s(): cannot retrieve acls of group '%d': %s",
                            __CLASS__,__FUNCTION__,$group_id,db_errormessage()));
            return FALSE;
        }
        if (sizeof($records) > 0) {
            $where_acl_id = '(';
            $count = 0;
            foreach($records as $record) {
                $where_acl_id .= sprintf('%s(acl_id = %d)',($count++) ? ' OR ' : '',$record['acl_id']);
            }
            $where_acl_id .= ')';
            $table_wheres = array(
                'acls_areas'              => $where_acl_id,
                'acls_nodes'              => $where_acl_id,
                'acls_modules'            => $where_acl_id,
                'acls_modules_areas'      => $where_acl_id,
                'acls_modules_nodes'      => $where_acl_id,
                'users_groups_capacities' => $where_group_id,
                'groups_capacities'       => $where_group_id,
                'acls'                    => $where_acl_id,
                'groups'                  => $where_group_id);
        } else {
            $table_wheres = array(
                'users_groups_capacities' => $where_group_id,
                'groups_capacities'       => $where_group_id,
                'groups'                  => $where_group_id);
        }

        // 2 -- actually process the todo list
        $message = sprintf("%s.%s(): group_id=%d:",__CLASS__,__FUNCTION__,$group_id);
        // start transaction
        foreach($table_wheres as $table => $where) {
            if (($rowcount = db_delete($table,$where)) === FALSE) {
                // rollback transaction
                $message .= sprintf(" '%s': FAILED. I'm outta here (%s)",$table,db_errormessage());
                logger($message);
                return FALSE;
            } else {
                $message .= sprintf(" '%s':%d",$table,$rowcount);
            }
        }
        // commit transaction
        logger($message,WLOG_DEBUG);
        return TRUE;
    } // delete_group_capacities_records()


    /** retrieve a single group's record possibly from the cache
     *
     * @param int $group_id identifies the group record
     * @param bool $forced if TRUE unconditionally fetch the record from the database 
     * @return bool|array FALSE if there were errors, the group record otherwise
     */
    function get_group_record($group_id,$forced=FALSE) {
        $group_id = intval($group_id);
        if ((!isset($this->groups[$group_id])) || ($forced)) {
            $table = 'groups';
            $fields = '*';
            $where = array('group_id' => $group_id);
            if (($record = db_select_single_record($table,$fields,$where)) === FALSE) {
                logger(sprintf("%s.%s(): cannot retrieve record for group '%d': %s",
                               __CLASS__,__FUNCTION__,$group_id,db_errormessage()));
                $this->output->add_message(t('error_retrieving_data','admin'));
                return FALSE;
            } else {
                $this->groups[$group_id] = $record;
            }
        }
        return (isset($this->groups[$group_id])) ? $this->groups[$group_id] : FALSE;
    } // get_group_record()

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
            logger(sprintf('%s.%s(): cannot retrieve areas. Mmmm...',__CLASS__,__FUNCTION__),WLOG_DEBUG);
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