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

/** /program/lib/usermanager.class.php - taking care of user management
 *
 * This file defines a class for dealing with users.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: usermanager.class.php,v 1.15 2013/06/11 11:26:06 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** this value is used to select all users rather than users from a specific group */
define('GROUP_SELECT_ALL_USERS',-1);

/** this value is used to select the users that are not associated with any group */
define('GROUP_SELECT_NO_GROUP',0);

/** User management
 *
 * @todo Perhaps this class should be merged with the GroupManager class because there
 *       is a lot of overlap. Mmmmm.... maybe in a future refactoring operation.
 */
class UserManager {
    /** @var object|null collects the html output */
    var $output = NULL;

    /** @var bool if TRUE the calling routing is allowed to use the menu area (e.g. show account mgr menu) */
    var $show_parent_menu = FALSE;

    /** @var array used to cache user records keyed by user_id */
    var $users = array();

    /** construct a UserManager object
     *
     * This initialises the UserManager and also dispatches the task to do.
     * This also loads the loginlib: we need that in order to manipulate the user password.
     *
     * @param object &$output collects the html output
     */
    function UserManager(&$output) {
        global $CFG;
        $this->output = &$output;
        $this->output->set_helptopic('usermanager');
        $this->users = array();

        require_once($CFG->progdir.'/lib/loginlib.php');

        $task = get_parameter_string('task',TASK_USERS);
        switch($task) {
        case TASK_USERS:            $this->users_overview();   break;
        case TASK_USER_ADD:         $this->user_add();         break;
        case TASK_USER_SAVE_NEW:    $this->user_savenew();     break;
        case TASK_USER_DELETE:      $this->user_delete();      break;
        case TASK_USER_EDIT:        $this->user_edit();        break;
        case TASK_USER_SAVE:        $this->user_save();        break;
        case TASK_USER_GROUPS:      $this->user_groups();      break;
        case TASK_USER_GROUPADD:    $this->user_groupadd();    break;
        case TASK_USER_GROUPSAVE:   $this->user_groupsave();   break;
        case TASK_USER_GROUPDELETE: $this->user_groupdelete(); break;
        case TASK_USER_INTRANET:    $this->user_intranet();    break;
        case TASK_USER_ADMIN:       $this->user_admin();       break;
        case TASK_USER_PAGEMANAGER: $this->user_pagemanager(); break;
        case TASK_USER_ADVANCED:
        case TASK_USER_TREEVIEW:
        case TASK_USER_MODULE:
            $this->output->add_message('STUB: not implemented: '.$task); 
            $this->output->add_message('user = '.get_parameter_string('user','(unset)'));
            $this->show_parent_menu = TRUE; // STUB
            break;

        default:
            $s = (utf8_strlen($task) <= 50) ? $task : utf8_substr($task,0,44).' (...)';
            $message = t('task_unknown','admin',array('{TASK}' => htmlspecialchars($s)));
            $output->add_message($message);
            logger('usermanager: unknown task: '.htmlspecialchars($s));
            $this->users_overview();
            break;
        }
    } // UserManager()

    function show_parent_menu() {
        return $this->show_parent_menu;
    }

    // ==================================================================
    // =========================== WORKHORSES ===========================
    // ==================================================================

    /** display a list of existing users and an option to add a user
     *
     * This constructs the heart of the user manager: a link to add
     * a user, followed by a list of links for deleting an modifying 
     * selected (see below) users. The list of users is ordered as follows.
     * First the active users are displayed, an after that the inactive
     * users are displayed. The sort order is based on the short name of
     * the user.
     *
     * Note that a selection is made of all user accounts, based on a
     * choice the user makes from the menu (see {@link show_menu_overview()}).
     * This list to show is selected as follows:
     *
     *  - if the parameter 'group' is NOT set in $_GET[] and this is the 1st time,
     *    all users are listed (equivalent with GROUP_SELECT_ALL_USERS). If
     *    we are returning, the $_SESSION may contain another default group
     *    selectiond
     *    
     *  - if the parameter 'group' is set to GROUP_SELECT_ALL_USERS (-1),
     *    all users are listed
     *
     *  - if the parameter 'group' is set to GROUP_SELECT_NO_GROUP (zero),
     *    all users without a group are listed
     *
     *  - if the parameter 'group' has another value, the users of that
     *    group are listed
     *
     * The list of existing users is paginated, ie. if there are more than a
     * screenfull, an additional paginator is displayed at the end of the list.
     * The screen always starts with an add a user link though.
     *
     * Note that the list of existing users shows the full name and
     * the username in parenthese. If a 'real' group is selected (ie. not
     * the collection of users without a group or all users), the capacity
     * of that user in that group is also displayed.
     *
     * Example: 
     * Amelia Cackle, a 'Principal' in the 'faculty' group, is displayed
     * like this in the faculty group: Amelia Cackle (acackl) (Principal)
     *
     * @return void output is returned in $this->output
     */
    function users_overview() {
        global $WAS_SCRIPT_NAME,$CFG;

        //
        // 0 -- calculate the subset (if any) to display + remember last choice in session
        //
        $group_id = get_parameter_int('group',NULL);
        if (is_null($group_id)) {
            if (!isset($_SESSION['users_overview_group_id'])) {
                // first call this session: show first screen of all users, start at first record
                $group_id = GROUP_SELECT_ALL_USERS;
                $limit = $CFG->pagination_height;
                $offset = 0;
            } else {
                $group_id = $_SESSION['users_overview_group_id'];
                $limit = $_SESSION['users_overview_limit'];
                $offset = $_SESSION['users_overview_offset'];
            }
        } else {
            $limit = get_parameter_int('limit',$CFG->pagination_height);
            $offset = get_parameter_int('offset',0);
        }
        $num_users = $this->get_num_user_records($group_id);
        // correct 'insane' values
        $offset = max(min($num_users-1,$offset),0); // 0 <= $offset < $num_users
        $limit = max($limit,1); // make sure 1 <= $limit
        $_SESSION['users_overview_group_id'] = $group_id;
        $_SESSION['users_overview_limit'] = $limit;
        $_SESSION['users_overview_offset'] = $offset;
        $_SESSION['aclmanager_open_areas'] = FALSE; // reset the expanded/collapsed area feature to default: all closed

        //
        // 1 -- Start content and UL-list
        //
        if (($num_users <= $limit) && ($offset == 0)) { // listing fits on a single screen
            $header = '<h2>'.t('menu_users','admin').'</h2>';
        } else { // we need paginating; show where we are
            $param = array('{FIRST}' => strval($offset+1),
                           '{LAST}' => strval(min($num_users,$offset+$limit)),
                           '{TOTAL}' => strval($num_users));
            $header = '<h2>'.t('menu_users','admin').' '.t('pagination_count_of_total','admin',$param).'</h2>';
            $parameters = $this->a_params(TASK_USERS);
            $parameters['group'] = $group_id;
            $this->output->add_pagination($WAS_SCRIPT_NAME,$parameters,$num_users,$limit,$offset,$CFG->pagination_width);
        }
        $this->output->add_content($header);
        $this->output->add_content('<ul>');

        //
        // 2 -- Add an 'add a user' option
        //
        $this->output->add_content('  <li class="list">');
        // line up the prompt with links to existing users below (if any)
        if (!$this->output->text_only) {
            $icon_blank = '    '.$this->output->skin->get_icon('blank');
            for ($i=0; $i<2; ++$i) {
                $this->output->add_content($icon_blank);
            }
        } // else
            // don't clutter the high-visiblity interface with superfluous layout fillers
        $a_attr = array('title'=> t('usermanager_add_a_user_title','admin'));
        $a_params = $this->a_params(TASK_USER_ADD);
        $this->output->add_content('    '.html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,t('usermanager_add_a_user','admin')));

        //
        // 3 -- Show a list of existing users (if any)
        //
        $records = $this->get_user_records($group_id,$limit,$offset);
        if ($records === FALSE) {
            $this->output->add_message(t('error_retrieving_data','admin'));
            logger('usermanager: cannot retrieve list of users: '.db_errormessage());
        } elseif (sizeof($records) > 0) {
            if (($group_id == GROUP_SELECT_ALL_USERS) || ($group_id == GROUP_SELECT_NO_GROUP)) {
                $show_capacity = FALSE;
            } else {
                $show_capacity =  TRUE;
            }
            $inactive = t('inactive','admin');
            foreach($records as $user_id => $record) {
                $this->output->add_content('  <li class="list">');
                $this->output->add_content('    '.$this->get_icon_delete($user_id));
                $this->output->add_content('    '.$this->get_icon_edit($user_id));
                $a_params = $this->a_params(TASK_USER_EDIT,$user_id);
                $params = array('{USERNAME}' => $record['username'],'{FULL_NAME}' => $record['full_name']);
                $a_attr = array('title' => t('usermanager_user_edit_title','admin', $params));
                $anchor = t('usermanager_user_edit','admin',$params);
                if (db_bool_is(TRUE,$record['is_active'])) {
                     if ($show_capacity) {
                         $capacity_inactive = sprintf(" (%s)",capacity_name($record['capacity_code']));
                     } else {
                         $capacity_inactive = '';
                     }
                } else {
                     if ($show_capacity) {
                        $capacity_inactive = sprintf(" (%s, %s)",capacity_name($record['capacity_code']),$inactive);
                    } else {
                        $capacity_inactive = sprintf(" (%s)",$inactive);
                    }
                }
                $this->output->add_content('    '.html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor).$capacity_inactive);
            }
            $this->output->add_content('</ul>');
        }

        //
        // 4 -- End with the menu of groups of users
        //
        $this->show_menu_overview($group_id);
    } // users_overview()


    /** present 'add user' dialog where the user can enter minimal properties for a new user
     *
     * this displays a dialog where the user can enter the minimal necessary properties
     * of a new user. These properties are: 
     *  - name (e.g. 'hparkh')
     *  - full name (e.g. 'Helen Parkhurst')
     *  - a password
     *  - an e-mail address
     *  - the active flag
     * Other properties will be set to default values and can be edited lateron
     * by editing the user account.
     *
     * The new user is saved via performing the task TASK_USER_SAVE_NEW
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME
     */
    function user_add() {
        global $WAS_SCRIPT_NAME;
        $this->output->add_content('<h2>'.t('usermanager_add_user_header','admin').'</h2>');
        $this->output->add_content(t('usermanager_add_user_explanation','admin'));
        $href = href($WAS_SCRIPT_NAME,$this->a_params(TASK_USER_SAVE_NEW));
        $dialogdef = $this->get_dialogdef_add_user();
        $this->output->add_content(dialog_quickform($href,$dialogdef));
        $this->show_breadcrumbs_adduser();
    } // user_add()


    /** save a new user to the database
     *
     * this saves a new user to the database. This involves at least two tables:
     * a record in the users table with basic information and also a record with
     * access control in the acls table.
     *
     * @todo maybe we should find a more elegant way to check a field for uniqueness
     * @todo shouldn't we end with the edit-user dialog rather than the users overview?
     *       that might make more sense...
     * @return data saved to the database, directory created, output created via users_overview()
     * @uses $CFG
     * @uses $WAS_SCRIPT_NAME
     */
    function user_savenew() {
        global $WAS_SCRIPT_NAME,$CFG;
        //
        // 1 -- bail out if the user pressed cancel button
        //
        if (isset($_POST['button_cancel'])) {
            $this->output->add_message(t('cancelled','admin'));
            $this->users_overview();
            return;
        }

        //
        // 2 -- validate the data
        //
        $invalid = FALSE;
        $dialogdef = $this->get_dialogdef_add_user();
        //
        // 2A -- check for generic errors (string too short, number too small, etc)
        if (!dialog_validate($dialogdef)) {
            $invalid = TRUE;
        }
        // 2B -- additional check: unique username
        $fname = $this->get_fname($dialogdef['username']);
        $params = array('{FIELD}' => $fname);
        $username = $dialogdef['username']['value'];
        $record = db_select_single_record('users','user_id',array('username' => $username));
        if ($record !== FALSE) {
            // Oops, a record with that username already exists. Go flag error
            ++$dialogdef['username']['errors'];
            $dialogdef['username']['error_messages'][] = t('validate_not_unique','',$params);
            $invalid = TRUE;
        }

        // 2C -- additional check: unique userdata subdirectory relative to {$CFG->datadir}/users/
        $userdata_directory = strtolower(sanitise_filename($username));
        $userdata_full_path = $CFG->datadir.'/users/'.$userdata_directory;
        $userdata_directory_created = @mkdir($userdata_full_path,0700);
        if ($userdata_directory_created) {
            @touch($userdata_full_path.'/index.html'); // "protect" the newly created directory from prying eyes
        } else {
            // Mmmm, failed; probably already exists then. Oh well. Go flag error.
            ++$dialogdef['username']['errors'];
            $params['{VALUE}'] = '/users/'.$userdata_directory;
            $dialogdef['username']['error_messages'][] = t('validate_already_exists','',$params);
            $invalid = TRUE;
        }

        // 2D -- additional check: valid password
        $password1 = $dialogdef['user_password1']['value'];
        $password2 = $dialogdef['user_password2']['value'];
        if ((!empty($password1)) || (!empty($password2))) {
            if ($password1 != $password2) {
                $params = array('{FIELD1}' => $this->get_fname($dialogdef['user_password1']),
                                '{FIELD2}' => $this->get_fname($dialogdef['user_password2']));
                ++$dialogdef['user_password1']['errors'];
                ++$dialogdef['user_password2']['errors'];
                $dialogdef['user_password1']['error_messages'][] = t('validate_different_passwords','',$params);
                $dialogdef['user_password1']['value'] = '';
                $dialogdef['user_password2']['value'] = '';
                $invalid = TRUE;
            } elseif (!acceptable_new_password($password1,$password2)) {
                $params = array('{MIN_LENGTH}' => MINIMUM_PASSWORD_LENGTH,
                                '{MIN_LOWER}' => MINIMUM_PASSWORD_LOWERCASE,
                                '{MIN_UPPER}' => MINIMUM_PASSWORD_UPPERCASE,
                                '{MIN_DIGIT}' => MINIMUM_PASSWORD_DIGITS,
                                '{FIELD}'=> $this->get_fname($dialogdef['user_password1']));
                ++$dialogdef['user_password1']['errors'];
                $dialogdef['user_password1']['error_messages'][] = t('validate_bad_password','',$params);
                ++$dialogdef['user_password2']['errors'];
                $dialogdef['user_password1']['value'] = '';
                $dialogdef['user_password2']['value'] = '';
                $invalid = TRUE;
            }
        }
        // 2E -- if there were any errors go redo dialog while keeping data already entered
        if ($invalid) {
            if ($userdata_directory_created) { // Get rid of the directory _we_ created
                @unlink($userdata_full_path.'/index.html');
                @rmdir($userdata_full_path);
            }
            // show errors messages
            foreach($dialogdef as $k => $item) {
                if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                    $this->output->add_message($item['error_messages']);
                }
            }
            $this->output->add_content('<h2>'.t('usermanager_add_user_header','admin').'</h2>');
            $this->output->add_content(t('usermanager_add_user_explanation','admin'));
            $href = href($WAS_SCRIPT_NAME,$this->a_params(TASK_USER_SAVE_NEW));
            $this->output->add_content(dialog_quickform($href,$dialogdef));
            return;
        }
        //
        // 3 -- store the data
        //
        // At this point we have a validated new user dialog in our hands
        // We now need to convert the data from the dialog to sensible
        // values for the database fields and store the data.
        // Note that the userdata directory already exists at this point

        $fields_acl = array(
            'permissions_intranet' => ACL_ROLE_NONE,
            'permissions_modules' => ACL_ROLE_NONE,
            'permissions_jobs' => ACL_ROLE_NONE,
            'permissions_nodes' => ACL_ROLE_NONE
            );
        //
        // 3A -- create an acl (with no permissions whatsoever) and remember the new acl_id
        //
        $errors = 0;
        $new_acl_id = db_insert_into_and_get_id('acls',$fields_acl,'acl_id');
        if ($new_acl_id === FALSE) {
            logger(sprintf("user_savenew(): adding new acl for new user failed: %s",db_errormessage()));
            ++$errors;
        } else {
            //
            // 3B -- subsequently add a new user and remember the new user_id
            //
            $new_username = $dialogdef['username']['value'];
            $new_user_fullname = $dialogdef['user_fullname']['value'];
            $new_salt = password_salt();
            $fields = array(
                'username' => $new_username,
                'salt' => $new_salt,
                'password_hash' => password_hash($new_salt,$dialogdef['user_password1']['value']),
                'bypass_mode' => FALSE,
                'bypass_hash' => NULL,
                'bypass_expiry' => NULL,
                'full_name' => $new_user_fullname,
                'email' => $dialogdef['user_email']['value'],
                'is_active' => ($dialogdef['user_is_active']['value'] == 1) ? TRUE : FALSE,
                'redirect' => '',
                'language_key' => $CFG->language_key,
                'path' => $userdata_directory,
                'acl_id' => $new_acl_id,
                'editor' => $CFG->editor,
                'skin' => 'base'
                );
            $new_user_id = db_insert_into_and_get_id('users',$fields,'user_id');
            if ($new_user_id === FALSE) {
                logger(sprintf("usermanager: saving new user %s (%s) failed: %s".
                               $new_username, $new_user_fullname,db_errormessage()));
                ++$errors;
            }
        }
        if ($errors > 0) {
            if ($userdata_directory_created) { // Get rid of the directory _we_ created
                @unlink($userdata_full_path.'/index.html');
                @rmdir($userdata_full_path);
            }
            $this->output->add_message(t('usermanager_savenew_user_failure','admin'));
        } else {
            $params = array('{USERNAME}' => $new_username,'{FULL_NAME}' => $new_user_fullname);
            $this->output->add_message(t('usermanager_savenew_user_success','admin',$params));
            logger(sprintf("usermanager: success saving new user '%d' %s (%s) and datadir /users/%s",
                           $new_user_id,$new_username,$new_user_fullname,$userdata_directory));
        }
        $this->users_overview();
    } // user_savenew()


    /** present an 'edit user' dialog filled with existing data
     *
     * @todo maybe it is better to call this routine with $user_id as a parameter?
     *       that allows for moving vrom adduser() -> savenew() -> edituser($user_id).
     *       Mmmm, food for thought
     * @output void output via $this->output
     */
    function user_edit() {
        global $WAS_SCRIPT_NAME;
        $user_id = get_parameter_int('user',NULL);
        if (is_null($user_id)) {
            logger("usermanager->user_edit(): unspecified parameter user");
            $this->output->add_message(t('error_invalid_parameters','admin'));
            $this->users_overview();
            return;
        }
        $params = $this->get_user_names($user_id);
        $this->output->add_content('<h2>'.t('usermanager_edit_user_header','admin',$params).'</h2>');
        $this->output->add_content(t('usermanager_edit_user_explanation','admin',$params));
        $href = href($WAS_SCRIPT_NAME,$this->a_params(TASK_USER_SAVE,$user_id));
        $dialogdef = $this->get_dialogdef_edit_user($user_id);
        if ($dialogdef !== FALSE) {
            $this->output->add_content(dialog_quickform($href,$dialogdef));
        } else {
            $this->output->add_message(t('error_retrieving_data','admin'));
        }
        $this->show_menu_user($user_id,TASK_USER_EDIT);
    } // user_edit()


    /** save edited user data to the database
     *
     * @output void work done and output via $this->output
     */
    function user_save() {
        global $WAS_SCRIPT_NAME,$CFG;

        // 0 -- sanity check
        $user_id = get_parameter_int('user',NULL);
        if (is_null($user_id)) {
            logger("usermanager->user_save(): unspecified parameter user");
            $this->output->add_message(t('error_invalid_parameters','admin'));
            $this->users_overview();
            return;
        }

        // 1 -- bail out if the user pressed cancel button
        $dialog = (isset($_POST['dialog'])) ? intval($_POST['dialog']) : 0;
        if (isset($_POST['button_cancel'])) {
            $this->output->add_message(t('cancelled','admin'));
            if  ($dialog != USERMANAGER_DIALOG_EDIT) {
                $this->user_edit();
            } else {
                $this->users_overview();
            }
            return;
        }
        if ($dialog == USERMANAGER_DIALOG_EDIT) {
            $this->user_save_basic($user_id);
            return;
        }
        // What remains are the acl-type save actions.

        // 2 -- which acl to use?
        if (($acl_id = $this->calc_acl_id($user_id)) === FALSE) {
            $this->user_edit();
            return;
        }

        // 3A -- construct necessary parameters for dialog
        $related_acls = calc_user_related_acls($user_id);
        $a_params = $this->a_params(TASK_USER_SAVE,$user_id);
        $params = $this->get_user_names($user_id);

        // 3B -- save data (and perhaps redo dialog if an error occurred)
        switch($dialog) {
        case USERMANAGER_DIALOG_INTRANET:
            include_once($CFG->progdir.'/lib/aclmanager.class.php');
            $acl = new AclManager($this->output,$acl_id,ACL_TYPE_INTRANET);
            $acl->set_related_acls($related_acls);
            $acl->set_action($a_params);
            $acl->set_header(t('usermanager_intranet_header','admin',$params));
            $acl->set_intro(t('usermanager_intranet_explanation','admin',$params));
            $acl->set_dialog($dialog);
            if (!$acl->save_data()) {
                $acl->show_dialog(); // redo dialog, but without a distracting menu this time
                return;
            }
            break;

        case USERMANAGER_DIALOG_ADMIN:
            include_once($CFG->progdir.'/lib/aclmanager.class.php');
            $acl = new AclManager($this->output,$acl_id,ACL_TYPE_ADMIN);
            $acl->set_related_acls($related_acls);
            $acl->set_action($a_params);
            $acl->set_header(t('usermanager_admin_header','admin',$params));
            $acl->set_intro(t('usermanager_admin_explanation','admin',$params));
            $acl->set_dialog($dialog);
            if (!$acl->save_data()) {
                $acl->show_dialog(); // redo dialog, but without a distracting menu this time
                return;
            }
            break;

        case USERMANAGER_DIALOG_PAGEMANAGER:
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
            $acl->set_related_acls($related_acls);
            $acl->set_action($a_params);
            $acl->set_header(t('usermanager_pagemanager_header','admin',$params));
            $acl->set_intro(t('usermanager_pagemanager_explanation','admin',$params));
            $acl->set_dialog($dialog);

            // Enable pagination for this one: the list of nodes can be very very long so split up in smaller screens.
            $a_params = $this->a_params(TASK_USER_PAGEMANAGER,$user_id);
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
            logger(sprintf("usermanager->save_data(): weird: dialog='%d'. Huh?",$dialog),WLOG_DEBUG);
            break;
        }
        // 4 -- we always end up in the basic properties screen (if we're still here)
        $this->user_edit();
    } // user_save()


    /** save basic properties of user account
     *
     * @param int $user_id the account to save (pkey in users table)
     * @uses $WAS_SCRIPT_NAME
     */
    function user_save_basic($user_id) {
        global $WAS_SCRIPT_NAME;
        $user_id = intval($user_id);
        //
        // 2 -- validate the data
        //
        $invalid = FALSE;
        $dialogdef = $this->get_dialogdef_edit_user($user_id);
        //
        // 2A -- check for generic errors (string too short, number too small, etc)
        if (!dialog_validate($dialogdef)) {
            $invalid = TRUE;
        }
        // 2B -- additional check: unique username
        $record = db_select_single_record('users','user_id',array('username' => $dialogdef['username']['value']));
        if (($record !== FALSE) && (intval($record['user_id']) != $user_id)) {
            // Oops, a record with that username already exists and it's not us. Go flag error
            ++$dialogdef['username']['errors'];
            $fname = $this->get_fname($dialogdef['username']);
            $dialogdef['username']['error_messages'][] = t('validate_not_unique','',array('{FIELD}'=>$fname));
            $invalid = TRUE;
        }
        // 2C -- additional check: valid password
        $password1 = $dialogdef['user_password1']['value'];
        $password2 = $dialogdef['user_password2']['value'];
        if ((!empty($password1)) || (!empty($password2))) {
            if ($password1 != $password2) {
                $params = array('{FIELD1}' => $this->get_fname($dialogdef['user_password1']),
                                '{FIELD2}' => $this->get_fname($dialogdef['user_password2']));
                ++$dialogdef['user_password1']['errors'];
                ++$dialogdef['user_password2']['errors'];
                $dialogdef['user_password1']['error_messages'][] = t('validate_different_passwords','',$params);
                $dialogdef['user_password1']['value'] = '';
                $dialogdef['user_password2']['value'] = '';
                $invalid = TRUE;
            } elseif (!acceptable_new_password($password1,$password2)) {
                $params = array('{MIN_LENGTH}' => MINIMUM_PASSWORD_LENGTH,
                                '{MIN_LOWER}' => MINIMUM_PASSWORD_LOWERCASE,
                                '{MIN_UPPER}' => MINIMUM_PASSWORD_UPPERCASE,
                                '{MIN_DIGIT}' => MINIMUM_PASSWORD_DIGITS,
                                '{FIELD}'=> $this->get_fname($dialogdef['user_password1']));
                ++$dialogdef['user_password1']['errors'];
                $dialogdef['user_password1']['error_messages'][] = t('validate_bad_password','',$params);
                $params['{FIELD}'] = $this->get_fname($dialogdef['user_password2']);
                ++$dialogdef['user_password2']['errors'];
                $dialogdef['user_password1']['value'] = '';
                $dialogdef['user_password2']['value'] = '';
                $invalid = TRUE;
            }
        }
        // 2D -- if there were any errors go redo dialog while keeping data already entered
        if ($invalid) {
            foreach($dialogdef as $k => $item) {
                if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                    $this->output->add_message($item['error_messages']);
                }
            }
            $params = $this->get_user_names($user_id);
            $this->output->add_content('<h2>'.t('usermanager_edit_user_header','admin',$params).'</h2>');
            $this->output->add_content(t('usermanager_edit_user_explanation','admin',$params));
            $href = href($WAS_SCRIPT_NAME,$this->a_params(TASK_USER_SAVE,$user_id));
            if ($dialogdef !== FALSE) {
                $this->output->add_content(dialog_quickform($href,$dialogdef));
                $this->show_breadcrumbs_user($user_id);
            } else {
                $this->output->add_message(t('error_retrieving_data','admin'));
                $this->show_menu_user($user_id,TASK_USER_EDIT);
            }
            return;
        }
        // 3 -- Now actually save the data which we just validated
        $fields = array(
            'username' => $dialogdef['username']['value'],
            'bypass_mode' => FALSE,
            'bypass_hash' => NULL,
            'bypass_expiry' => NULL,
            'full_name' => $dialogdef['user_fullname']['value'],
            'email' => $dialogdef['user_email']['value'],
            'is_active' => ($dialogdef['user_is_active']['value'] == 1) ? TRUE : FALSE,
            'redirect' => $dialogdef['user_redirect']['value'],
            'language_key' => $dialogdef['user_language_key']['value'],
            'editor' => $dialogdef['user_editor']['value'],
            'skin' => $dialogdef['user_skin']['value']
            );
        if (!empty($password1)) {
            $new_salt = password_salt();
            $new_password = $password1;
            $fields['salt'] = $new_salt;
            $fields['password_hash'] = password_hash($new_salt,$new_password);
        }

        $params = array('{USERNAME}' => $dialogdef['username']['value'],
                        '{FULL_NAME}' => $dialogdef['user_fullname']['value']);
        if (db_update('users',$fields,array('user_id' => $user_id)) === FALSE) {
            $this->output->add_message(t('usermanager_save_user_failure','admin',$params));
            logger("usermanager->user_save(): error saving data user '$user_id': ".db_errormessage());
        } else {
            $this->output->add_message(t('usermanager_save_user_success','admin',$params));
            logger("usermanager->user_save(): success saving changes to '$user_id' in 'users'",WLOG_DEBUG);
        }
        $this->users_overview();
        return;
    } // user_save_basic()


    /** delete a user after confirmation
     *
     * after some basic tests this either presents a confirmation dialog to the user OR
     * deletes a user with associated acls and other records.
     *
     * Note that this routine could have been split into two routines, with the
     * first one displaying the confirmation dialog and the second one 'saving the changes'.
     * However, I think it is counter-intuitive to perform a deletion of data under
     * the name of 'saving'. So, I decided to use the same routine for both displaying
     * the dialog and acting on the dialog.
     *
     * Note that the (user)files should be removed before the account can be removed,
     * see {@link userdir_is_empty()}. It is up to the user or the admin to remove those files.
     *
     * A special test is performed to prevent users from killing their own account (which would
     * immediately kick them out of admin.php never to be seen again). 
     *
     * @return void results are returned as output in $this->output
     * @todo since multiple tables are involved, shouldn't we use transaction/rollback/commit?
     *       Q: How well is MySQL suited for transactions? A: Mmmmm.... Which version? Which storage engine?
     */
    function user_delete() {
        global $WAS_SCRIPT_NAME,$DB,$USER;
        //
        // 0 -- sanity check
        //
        $user_id = get_parameter_int('user',NULL);
        if (is_null($user_id)) {
            logger(sprintf("%s.%s(): unspecified parameter user",__CLASS__,__FUNCTION__));
            $this->output->add_message(t('error_invalid_parameters','admin'));
            $this->users_overview();
            return;
        }

        //
        // 1 -- bail out if the user pressed cancel button
        //
        if (isset($_POST['button_cancel'])) {
            $this->output->add_message(t('cancelled','admin'));
            $this->users_overview();
            return;
        }

        // 2A -- do not allow the user to commit suicide
        if (intval($USER->user_id) == intval($user_id)) {
            logger(sprintf("%s.%s(): user attempts to kill her own user account",__CLASS__,__FUNCTION__));
            $this->output->add_message(t('usermanager_delete_user_not_self','admin'));
            $this->users_overview();
            return;
        }


        // 2B -- are there any files left in this user's private storage $CFG->datadir.'/users/'.$path?
        if (($user = $this->get_user_record($user_id)) === FALSE) {
            $this->users_overview();
            return;
        }
        $path = '/users/'.$user['path'];
        if (!userdir_is_empty($path)) {
            // At this point we know there are still files associated with this
            // user in the data directory. This is a show stopper; it is up to the
            //  admin requesting this delete to get rid of the files first (eg via File Manager)
            logger(sprintf("%s.%s(): data directory '%s' not empty",__CLASS__,__FUNCTION__,$path));
            $params = $this->get_user_names($user_id); // pick up username
            $this->output->add_message(t('usermanager_delete_user_dir_not_empty','admin',$params));
            $this->users_overview();
            return;
        }

        //
        // 3 -- user has confirmed delete?
        //
        if ((isset($_POST['button_delete'])) &&
            (isset($_POST['dialog'])) && (intval($_POST['dialog']) == USERMANAGER_DIALOG_DELETE)) {
            $params = $this->get_user_names($user_id); // pick up name before it is gone
            if ((userdir_delete($path)) && ($this->delete_user_records($user_id))) {
                $this->output->add_message(t('usermanager_delete_user_success','admin',$params));
                $retval = TRUE;
            } else {
                $this->output->add_message(t('usermanager_delete_user_failure','admin',$params));
                $retval = FALSE;
            }
            logger(sprintf("%s.%s(): %s deleting user '%d' %s (%s)",
                           __CLASS__,__FUNCTION__,($retval === FALSE) ? 'failure' : 'success',
                           $user_id,$params['{USERNAME}'],$params['{FULL_NAME}']));
            $this->users_overview();
            return;
        }

        //
        // 4 -- no delete yet, first show confirmation dialog
        //
        // Dialog is very simple: a simple text showing
        // - the name of the user
        // - a Delete and a Cancel button
        //
        $dialogdef = array(
            array('type' => F_INTEGER,'name' => 'dialog','value' => USERMANAGER_DIALOG_DELETE,'hidden' => TRUE),
            dialog_buttondef(BUTTON_DELETE),
            dialog_buttondef(BUTTON_CANCEL)
            );

        $params = $this->get_user_names($user_id);
        $header = t('usermanager_delete_user_header','admin',$params);
        $this->output->add_content('<h2>'.$header.'</h2>');
        $this->output->add_content(t('usermanager_delete_user_explanation','admin'));
        $this->output->add_content('<ul>');
        $this->output->add_content('  <li class="level0">'.t('usermanager_delete_user_user','admin',$params));
        $this->output->add_content('</ul>');
        $this->output->add_content(t('delete_are_you_sure','admin'));

        $a_params = $this->a_params(TASK_USER_DELETE,$user_id);
        $href = href($WAS_SCRIPT_NAME,$a_params);
        $this->output->add_content(dialog_quickform($href,$dialogdef));
        $this->show_menu_user($user_id,TASK_USER_DELETE);
        $this->output->add_breadcrumb(
            $WAS_SCRIPT_NAME,
            $a_params,
            array('title' => $header),
            t('usermanager_delete_user_breadcrumb','admin'));
    } // user_delete()


    /** present an overview of group memberships for the specified user
     *
     * this constructs a link to add a membership to the user account and
     * a list of existing memberships, if any, including a delete button
     * per membership.
     *
     * The SQL-query retrieves the list of existing memberships from the
     * database, ordered by the short groupname. The data is validated
     * by joining to the table groups_capacities. If for some reason there
     * exists an invalid combination of group_id and capacity_code in
     * users_groups_capacities table, it will not show up in the list here.
     *
     * Note that it is currently not possible to change a users' group
     * membership, i.e. you cannot promote a user from 'Member' to 'Chair'
     * for a group: you have to delete the group membership first, and
     * subsequently add it again with the correct capacity.
     *
     * @return void results are returned as output in $this->output
     */
    function user_groups() {
        global $WAS_SCRIPT_NAME,$CFG,$DB;

        // 0 -- sanity check
        $user_id = get_parameter_int('user',NULL);
        if (is_null($user_id)) {
            logger("usermanager->user_groups(): unspecified parameter user");
            $this->output->add_message(t('error_invalid_parameters','admin'));
            $this->users_overview();
            return;
        }

        // 1 -- start memberships list 
        $params = $this->get_user_names($user_id);
        $this->output->add_content('<h2>'.t('usermanager_user_groups_header','admin',$params).'</h2>');
        $this->output->add_content(t('usermanager_user_groups_explanation','admin',$params));
        $this->output->add_content('<ul>');

        // 2 -- add an 'add a membership' option
        $this->output->add_content('  <li class="list">');
        // line up the prompt with links to existing areas below (if any)
        if (!$this->output->text_only) {
            $icon_blank = '    '.$this->output->skin->get_icon('blank');
            for ($i=0; $i<1; ++$i) {
                $this->output->add_content($icon_blank);
            }
        } // else
            // don't clutter the high-visiblity interface with superfluous layout fillers
        $a_attr = array('title'=> t('usermanager_user_groups_add_title','admin'));
        $a_params = $this->a_params(TASK_USER_GROUPADD,$user_id);
        $anchor = t('usermanager_user_groups_add','admin');
        $this->output->add_content('    '.html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor));

        // 3 -- List existing memberships (if any)
        $sql = sprintf("SELECT ugc.user_id, ugc.group_id, ugc.capacity_code, g.groupname, g.full_name ".
                       "FROM %susers_groups_capacities ugc ".
                       "INNER JOIN %sgroups_capacities gc USING (group_id, capacity_code) ".
                       "INNER JOIN %sgroups g USING (group_id) ".
                       "WHERE ugc.capacity_code <> 0 AND ugc.user_id = %d ".
                       "ORDER BY g.groupname",
                       $DB->prefix,$DB->prefix,$DB->prefix,$user_id);
        if (($DBResult = $DB->query($sql)) !== FALSE) {
            $records = $DBResult->fetch_all_assoc('group_id');
            $DBResult->close();
            foreach($records as $group_id => $record) {
                $param = array('{CAPACITY}' => capacity_name($record['capacity_code']),
                               '{GROUP}' => $record['groupname'],
                               '{GROUP_FULL_NAME}' => $record['full_name']);
                $this->output->add_content('  <li class="list">');
                $this->output->add_content('    '.$this->get_icon_groupdelete($user_id,$group_id));
                $this->output->add_content('    '.t('usermanager_user_groups','admin',$param));
            }
        }

        // 4 -- close the list...
        $this->output->add_content('</ul>');

        // 5 -- ...and finish with showing the user menu (including breadcrumbtrail)
        $this->show_menu_user($user_id,TASK_USER_GROUPS);
    } // user_groups()


    /** present 'add membership' dialog
     *
     * this displays a simple dialog where the user can add a membership to a
     * user account, one at a time. Basically we show a picklist with all
     * available group/capacity-combinations. Here "available" means:
     *  - only groups of which the user is currently NOT a member
     *  - only non-0 group/capacity-combinations that occur in the groups_capacities_table
     *    (capacity 0 implies: no capacity)
     *
     * An additional feature is that the user can become member of inactive groups.
     * However, these groups are sorted at the end of the picklist.
     */
    function user_groupadd() {
        global $WAS_SCRIPT_NAME;

        // 0 -- sanity check
        $user_id = get_parameter_int('user',NULL);
        if (is_null($user_id)) {
            logger("usermanager->user_groupadd(): unspecified parameter user");
            $this->output->add_message(t('error_invalid_parameters','admin'));
            $this->users_overview();
            return;
        }
        $params = $this->get_user_names($user_id);
        $this->output->add_content('<h2>'.t('usermanager_user_groupadd_header','admin',$params).'</h2>');
        $this->output->add_content(t('usermanager_user_groupadd_explanation','admin',$params));
        $href = href($WAS_SCRIPT_NAME,$this->a_params(TASK_USER_GROUPSAVE,$user_id));
        $dialogdef = $this->get_dialogdef_add_usergroup($user_id);
        $this->output->add_content(dialog_quickform($href,$dialogdef));

        $this->show_menu_user($user_id,TASK_USER_GROUPS);

        $parameters = $this->a_params(TASK_USER_GROUPADD,$user_id);
        $anchor = t('usermanager_user_groups_add','admin');
        $attributes = array('title' => t('usermanager_user_groups_add_title','admin'));
        $this->output->add_breadcrumb($WAS_SCRIPT_NAME,$parameters,$attributes,$anchor);

    } // user_groupadd()


    /** save the new group/capacity for the selected user
     *
     * this adds a record to the users_groups_capacities table, indicating
     * the group membership and the corresponding capacity for the user.
     *
     * @return void output written to browser via $this->output
     * @uses $WAS_SCRIPT_NAME
     */
    function user_groupsave() {
        global $WAS_SCRIPT_NAME;

        //
        // 0 -- sanity check
        //
        $user_id = get_parameter_int('user',NULL);
        if (is_null($user_id)) {
            logger("usermanager->user_groupsave(): unspecified parameter user");
            $this->output->add_message(t('error_invalid_parameters','admin'));
            $this->users_overview();
            return;
        }

        //
        // 1 -- bail out if the user pressed cancel button
        //
        if (isset($_POST['button_cancel'])) {
            $this->output->add_message(t('cancelled','admin'));
            $this->user_groups();
            return;
        }

        //
        // 2 -- make sure the data is valid
        //
        $dialogdef = $this->get_dialogdef_add_usergroup($user_id);
        if (!dialog_validate($dialogdef)) {
            foreach($dialogdef as $k => $item) {
                if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                    $this->output->add_message($item['error_messages']);
                }
            }
            $params = $this->get_user_names($user_id);
            $this->output->add_content('<h2>'.t('usermanager_user_groupadd_header','admin',$params).'</h2>');
            $this->output->add_content(t('usermanager_user_groupadd_explanation','admin',$params));
            $href = href($WAS_SCRIPT_NAME,$this->a_params(TASK_USER_GROUPSAVE,$user_id));
            $this->output->add_content(dialog_quickform($href,$dialogdef));
            $this->show_menu_user($user_id,TASK_USER_GROUPS);
            $parameters = $this->a_params(TASK_USER_GROUPADD,$user_id);
            $anchor = t('usermanager_user_groups_add','admin');
            $attributes = array('title' => t('usermanager_user_groups_add_title','admin'));
            $this->output->add_breadcrumb($WAS_SCRIPT_NAME,$parameters,$attributes,$anchor);
            return;
        }

        //
        // 3 -- save the selected group/capacity to this user account
        //
        $key = $dialogdef['user_group_capacity']['value'];
        list($group_id,$capacity_code) = explode(':',$key);
        $group_id = intval($group_id);
        $capacity_code = intval($capacity_code);
        if (($group_id == 0) || ($capacity_code == 0)) {
            // the key '0:0' is used to indicate 'no more groups'; pretend that the user cancelled add group membership
            $this->output->add_message(t('cancelled','admin'));
            $this->user_groups();
            return;
        }
        $errors = 0; // assume all goes well, for now...
        $fields = array('user_id' => $user_id, 'group_id' => $group_id, 'capacity_code' => $capacity_code);
        $table = 'users_groups_capacities';
        if (db_insert_into($table,$fields) === FALSE) {
            // Mmmm, weird. Perhaps there was already a record for $user_id,$group_id with another $capacity_code?
            logger("usermanager: weird: add membership for user '$user_id' and group '$group_id' failed: ".
                   db_errormessage());
            ++$errors;
            $where = array('user_id' => $user_id,'group_id' => $group_id);
            if (db_delete($table,$where) === FALSE) {
                logger("usermanager: add membership double-fault, giving up: ".db_errormessage());
                ++$errors;
            } elseif (db_insert_into($table,$fields) === FALSE) {
                logger("usermanager: add membership failed again, giving up: ".db_errormessage());
                ++$errors;
            } else {
                logger("usermanager: add membership for user '$user_id' and group '$group_id' succeeded, finally");
                $errors = 0; // OK. Forget the initial error.
            }
        }
        if ($errors == 0) {
            $this->output->add_message(t('success_saving_data','admin'));
        } else {
            $this->output->add_message(t('errors_saving_data','admin',array('{ERRORS}' => $errors)));
        }
        $this->user_groups();
    } // user_groupsave()


    /** end the group membership for the selected user
     *
     * @uses $DB
     */
    function user_groupdelete() {
        global $DB;
        $user_id = get_parameter_int('user',NULL);
        $group_id = get_parameter_int('group',NULL);
        if ((is_null($user_id)) || (is_null($user_id))) {
            logger("usermanager->user_groupsave(): unspecified parameter user and/or group");
            $this->output->add_message(t('error_invalid_parameters','admin'));
            $this->users_overview();
            return;
        }
        $sql = sprintf("SELECT g.groupname, g.full_name, ugc.capacity_code ".
                       "FROM %susers_groups_capacities ugc ".
                       "INNER JOIN %sgroups g USING (group_id) ".
                       "WHERE ugc.user_id = %d AND ugc.group_id = %d",
                       $DB->prefix,$DB->prefix,$user_id,$group_id);

        if (($DBResult = $DB->query($sql,1)) !== FALSE) {
            $record = $DBResult->fetch_row_assoc();
            $DBResult->close();
            $param = array('{GROUP}' => $record['groupname'],
                           '{GROUP_FULL_NAME}' => $record['full_name'],
                           '{CAPACITY}' => capacity_name($record['capacity_code']));
        } else {
            $param = array('{GROUP}' => $group_id,
                           '{GROUP_FULL_NAME}' => $group_id,
                           '{CAPACITY}' => '?');
            logger('usermanager->user_groupdelete(): cannot retrieve groupname etc. Huh? '.db_errormessage());
        }

        $table = 'users_groups_capacities';
        $where = array('user_id' => $user_id,'group_id' => $group_id);
        if (db_delete($table,$where) === FALSE) {
            $this->output->add_message(t('usermanager_delete_usergroup_failure','admin',$param));
        } else {
            $this->output->add_message(t('usermanager_delete_usergroup_success','admin',$param));
        }
        $this->user_groups();
    } // user_groupdelete()


    /** show a dialog for modifying intranet permissions for a user
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME
     * @uses $CFG
     */
    function user_intranet() {
        global $WAS_SCRIPT_NAME,$CFG;

        //
        // 0 -- sanity check
        //
        $user_id = get_parameter_int('user',NULL);
        if (is_null($user_id)) {
            logger("usermanager->user_intranet(): unspecified parameter user");
            $this->output->add_message(t('error_invalid_parameters','admin'));
            $this->users_overview();
            return;
        }

        // 1 -- which acl to use?
        if (($acl_id = $this->calc_acl_id($user_id)) === FALSE) {
            $this->user_edit();
            return;
        }

        // 2 -- setup the AclManager to do the dirty work
        include_once($CFG->progdir.'/lib/aclmanager.class.php');
        $acl = new AclManager($this->output,$acl_id,ACL_TYPE_INTRANET);
        $acl->set_related_acls(calc_user_related_acls($user_id));
        $acl->set_action($this->a_params(TASK_USER_SAVE,$user_id));
        $params = $this->get_user_names($user_id);
        $acl->set_header(t('usermanager_intranet_header','admin',$params));
        $acl->set_intro(t('usermanager_intranet_explanation','admin',$params));
        $acl->set_dialog(USERMANAGER_DIALOG_INTRANET);

        // 3 -- show the dialog and the menu
        $acl->show_dialog();
        $this->show_menu_user($user_id,TASK_USER_INTRANET);
    } // user_intranet()


    /** show a dialog for modifying admin permissions for a user
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME
     * @uses $CFG
     */
    function user_admin() {
        global $WAS_SCRIPT_NAME,$CFG;
        //
        // 0 -- sanity check
        //
        $user_id = get_parameter_int('user',NULL);
        if (is_null($user_id)) {
            logger("usermanager->user_admin(): unspecified parameter user");
            $this->output->add_message(t('error_invalid_parameters','admin'));
            $this->users_overview();
            return;
        }

        // 1 -- which acl to use?
        if (($acl_id = $this->calc_acl_id($user_id)) === FALSE) {
            $this->user_edit();
            return;
        }

        // 2 -- setup the AclManager to do the dirty work
        include_once($CFG->progdir.'/lib/aclmanager.class.php');
        $acl = new AclManager($this->output,$acl_id,ACL_TYPE_ADMIN);
        $acl->set_related_acls(calc_user_related_acls($user_id));
        $acl->set_action($this->a_params(TASK_USER_SAVE,$user_id));
        $params = $this->get_user_names($user_id);
        $acl->set_header(t('usermanager_admin_header','admin',$params));
        $acl->set_intro(t('usermanager_admin_explanation','admin',$params));
        $acl->set_dialog(USERMANAGER_DIALOG_ADMIN);

        // 3 -- show the dialog and the menu
        $acl->show_dialog();
        $this->show_menu_user($user_id,TASK_USER_ADMIN);
    } // user_admin()


    /** show a dialog for modifying page manager permissions for a user
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME
     * @uses $CFG
     */
    function user_pagemanager() {
        global $WAS_SCRIPT_NAME,$CFG;
        //
        // 0 -- sanity check
        //
        $user_id = get_parameter_int('user',NULL);
        if (is_null($user_id)) {
            logger("usermanager->user_pagemanager(): unspecified parameter user");
            $this->output->add_message(t('error_invalid_parameters','admin'));
            $this->users_overview();
            return;
        }

        //
        // 1 -- maybe change the state of the open/closed areas
        //
        if (!isset($_SESSION['aclmanager_open_areas'])) {
            $_SESSION['aclmanager_open_areas'] = FALSE; // default: everything is closed
        }
        $area_id = get_parameter_int('area',NULL);
        $_SESSION['aclmanager_open_areas'] = $this->areas_expand_collapse($_SESSION['aclmanager_open_areas'],$area_id);

        //
        // 2 -- which acl to use?
        //
        if (($acl_id = $this->calc_acl_id($user_id)) === FALSE) {
            $this->user_edit();
            return;
        }

        //
        // 3A -- construct necessary parameters for dialog
        //
        $related_acls = calc_user_related_acls($user_id);
        $a_params = $this->a_params(TASK_USER_SAVE,$user_id);
        $params = $this->get_user_names($user_id);
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
        $acl->set_related_acls($related_acls);
        $acl->set_action($a_params);
        $acl->set_header(t('usermanager_pagemanager_header','admin',$params));
        $acl->set_intro(t('usermanager_pagemanager_explanation','admin',$params));
        $acl->set_dialog(USERMANAGER_DIALOG_PAGEMANAGER);

        // Enable pagination for this one: the list of nodes can be very very long so split up in smaller screens.
        $a_params = $this->a_params(TASK_USER_PAGEMANAGER,$user_id);
        $acl->enable_pagination($a_params,$limit,$offset);

        // Also enable the expand/collapse feature
        $acl->enable_area_view($a_params,$_SESSION['aclmanager_open_areas']);

        //
        // 4 -- show dialog + menu
        //
        $acl->show_dialog();
        $this->show_menu_user($user_id,TASK_USER_PAGEMANAGER);
    } // user_pagemanager()


    /** construct a dialogdef for selecting a group/capacity
     *
     * @param int $user_id limit the options to groups this user is NOT already a member of
     * @return array ready-to-use dialog definition
     */
    function get_dialogdef_add_usergroup($user_id) {
        $dialogdef = array(
            'user_group_capacity' => array(
                'type' => F_LISTBOX,
                'name' => 'user_group_capacity',
                'options' => $this->get_options_available_groups_capacities($user_id),
                'label' => t('usermanager_user_groupadd_groupcapacity_label','admin'),
                'title' => t('usermanager_user_groupadd_groupcapacity_title','admin'),
                'value' => '',
                ),
            'button_save' => dialog_buttondef(BUTTON_SAVE),
            'button_cancel' => dialog_buttondef(BUTTON_CANCEL)
        );
        return $dialogdef;
    } // get_dialogdef_add_usergroup()


    /** construct a list of groups still available for this user
     *
     * this constructs an array with available groups/capacities for the user $user_id
     * If the user is already a member of all available groups or there are no groups at all,
     * the list consists of a single option 'No groups available'.
     *
     * The values in this list are constructed from the primary key values of the
     * underlying groups_capacities table. These two numbers (group_id and capacity_code)
     * are separated with a colon ':' to make it easier to parse once we are to save
     * the values (in the table users_groups_capacities).
     *
     * The SQL-statement looks quite complex. What it does is using the table groups_capacities
     * as a starting point for _all_ valid (ie capacity_id != CAPACITY_NONE) combinations of
     * group and capacity. By left-joining the table users_groups_capacities with a very specific
     * ON-clause, and leaving out the column capacity_code, the resulting list consists of
     * all combinations of group and capacity buy without any entries that have a group of
     * which the user is already a member, no matter what capacity.
     * In other words: if a user is already a member of a group with capacity A, this user
     * cannot be member of the same group with capacity B. Finally, the table groups is used
     * to retrieve the group information such as the groupname and the active-flag.
     *
     * The resulting list is ordered by groupname and subsequently by the sort_order of the
     * capacity_code. However, inactive groups are sorted after the active groups so they
     * appear near the bottom of the list.
     *
     * @param int $user_id the user to which this list of available groups applies
     * @return array with available groups/capacities ready-to-use in a F_LISTBOX
     */
    function get_options_available_groups_capacities($user_id) {
        global $DB;
        $options = array();
        $sql = sprintf("SELECT gc.group_id, gc.capacity_code, g.groupname, g.full_name, g.is_active ".
                       "FROM %sgroups_capacities gc ".
                       "INNER JOIN %sgroups g USING (group_id) ".
                       "LEFT JOIN %susers_groups_capacities ugc ON (gc.group_id = ugc.group_id AND ugc.user_id = %d) ".
                       "WHERE gc.capacity_code <> 0 AND ugc.user_id IS NULL ".
                       "ORDER BY CASE WHEN (g.is_active) THEN 0 ELSE 1 END, g.groupname, gc.sort_order",
                       $DB->prefix,$DB->prefix,$DB->prefix,intval($user_id));
        if (($DBResult = $DB->query($sql)) !== FALSE) {
            $records = $DBResult->fetch_all_assoc();
            $DBResult->close();
            foreach($records as $record) {
                $key = $record['group_id'].":".$record['capacity_code'];
                $is_inactive = (db_bool_is(TRUE,$record['is_active'])) ? '' : sprintf(' (%s)',t('inactive','admin'));
                $options[$key] = array(
                    'option' => $record['groupname'].$is_inactive." / ".capacity_name($record['capacity_code']),
                    'title' => $record['full_name']
                    );
            }
        }
        if (empty($options)) {
            $options = array('0:0' => t('usermanager_user_groupadd_groupcapacity_none_available','admin'));
        }
        return $options;
    } // get_options_available_groups_capacities()


    /** remove all records relating to a single acl_id from various acl-tables
     *
     * this bluntly removes all records from the various user-related  tables for user $user_id.
     * Whenever there's an error deleting records, the routine bails out immediately and returns FALSE.
     * If all goes well, TRUE is returned. Any errors are logged, success is logged to DEBUG-log.
     *
     * Note that the order of deletion is important: we must first get rid of the foreign key constraints.
     *
     * @param int $user_id the key to the user account to delete
     * @return bool TRUE on success, FALSE on failure
     */
    function delete_user_records($user_id) {
        $user_id = intval($user_id);

        // 1 -- which ACL to delete?
        if (($acl_id = $this->calc_acl_id($user_id)) === FALSE) {
            return FALSE;
        }

        // 2 -- make list of tables and where-clauses to process (order is important due to FK constraints)...
        $where_acl_id = array('acl_id' => intval($acl_id));
        $where_user_id = array('user_id' => $user_id);
        $table_wheres = array(
            'sessions'                => $where_user_id,
            'users_properties'        => $where_user_id,
            'users_groups_capacities' => $where_user_id,
            'acls_areas'              => $where_acl_id,
            'acls_nodes'              => $where_acl_id,
            'acls_modules_areas'      => $where_acl_id,
            'acls_modules_nodes'      => $where_acl_id,
            'acls_modules'            => $where_acl_id,
            'users'                   => $where_user_id,
            'acls'                    => $where_acl_id);

        // 3 -- ...and step through the list
        $message = sprintf('%s.%s(): user_id=%d,acl_id=%d:',__CLASS__,__FUNCTION__,$user_id,$acl_id);
        // start transaction here
        foreach($table_wheres as $table => $where) {
            if (($rowcount = db_delete($table,$where)) === FALSE) {
                // rollback transaction here
                $message .= sprintf(" '%s': FAILED. I'm outta here (%s)",$table,db_errormessage());
                logger($message);
                return FALSE;
            } else {
                $message .= sprintf(" '%s':%d",$table,$rowcount);
            }
        }
        // commit transaction here
        logger($message,WLOG_DEBUG);
        return TRUE;
    } // delete_user_records()


    /** show the user menu with current option highlighted
     *
     * this constructs the user menu. Only the relevant options
     * are displayed (eg. if the user is not an admin, no pagemanager
     * option is displayed).
     *
     * @param int $user_id identifies the user
     * @param string $current_task the task to show highlighted
     * @param int $current_module_id the current module to show highlighted
     * @return void output generated in $this->output
     */
    function show_menu_user($user_id,$current_task=NULL,$current_module_id=NULL) {
        global $WAS_SCRIPT_NAME;
        $user_id = intval($user_id);
        $menu_items = array(
            array(
                'parameters' => $this->a_params(TASK_USER_EDIT,$user_id),
                'anchor' => t('menu_user_basic','admin'),
                'title' => t('menu_user_basic_title','admin')
            ),
            //
            // Currently there is no 'advanced' user properties dialog
            //
            //array(
            //    'parameters' => $this->a_params(TASK_USER_ADVANCED,$user_id),
            //    'anchor' => t('menu_user_advanced','admin'),
            //    'title' => t('menu_user_advanced_title','admin')
            //),
            //
            array(
                'parameters' => $this->a_params(TASK_USER_GROUPS,$user_id),
                'anchor' => t('menu_user_groups','admin'),
                'title' => t('menu_user_groups_title','admin')
            ),
            array(
                'parameters' => $this->a_params(TASK_USER_INTRANET,$user_id),
                'anchor' => t('menu_user_intranet','admin'),
                'title' => t('menu_user_intranet_title','admin')
            ),

// *** Commented out because Dirk needs to create realistic screenshots for the manual (2011-01-10/PF) ***
//            array(
//                'parameters' => $this->a_params(TASK_USER_MODULE,$user_id,1),
//                'anchor' => "Agenda (stub)",
//                'title' => t('menu_user_module_title','admin')
//            ),
//            array(
//                'parameters' => $this->a_params(TASK_USER_MODULE,$user_id,1),
//                'anchor' => "Chat (stub)",
//                'title' => t('menu_user_module_title','admin')
//            ),
//            array(
//                'parameters' => $this->a_params(TASK_USER_MODULE,$user_id,1),
//                'anchor' => "Forum (stub)",
//                'title' => t('menu_user_module_title','admin')
//            ),
// ***

            array(
                'parameters' => $this->a_params(TASK_USER_ADMIN,$user_id),
                'anchor' => t('menu_user_admin','admin'),
                'title' => t('menu_user_admin_title','admin')
            )
        );
        if ($this->has_job_permission($user_id,JOB_PERMISSION_PAGEMANAGER)) {
            $menu_items[] = array(
                'parameters' => $this->a_params(TASK_USER_PAGEMANAGER,$user_id),
                'anchor' => t('menu_user_pagemanager','admin'),
                'title' => t('menu_user_pagemanager_title','admin')
                );
        }
        $this->show_breadcrumbs_user($user_id);

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
    } // show_menu_user()





    /** display a menu showing groups of users (if any) + corresponding breadcrumb trail
     *
     * this constructs a list of links allowing for a quick selection of a subset of users
     * This looks a little like this:
     *
     * <pre>
     * All users (66)
     * No group (5)
     * faculty (14)
     * grade12 (7)
     * ...
     * webmasters (2)
     * </pre>
     *
     * The indication of the current selection in the menu is based on $group_id. Most of
     * the time this is a genuine group_id. However, 'All users' and 'No group' are special cases:
     * 
     *  - The value GROUP_SELECT_ALL_USERS (-1) cannot be a genuine group_id because these are always > 0.
     *  - The value GROUP_SELECT_NO_GROUP (0) cannot be a genuine group_id because these are always > 0.
     *
     * @param int|null $group_id identifies the current selection
     * @return void output stored via $this->output.
     * @uses $DB;
     * @uses #AS_SCRIPT_NAME
     */
    function show_menu_overview($group_id) {
        global $WAS_SCRIPT_NAME,$DB;

        $this->show_breadcrumbs_overview($group_id);
        $this->output->add_menu('<h2>'.t('menu','admin').'</h2>');
        $this->output->add_menu('<ul>');

        //
        // 1 -- are there any users at all? (must be since we're logged in ourselves, oh well)
        //
        $users = $this->get_num_user_records(GROUP_SELECT_ALL_USERS);
        $parameters = $this->a_params(TASK_USERS);
        $parameters['group'] = GROUP_SELECT_ALL_USERS;
        $attributes = array('title' => t('usermanager_all_users_title','admin'));
        if ((is_null($group_id)) || ($group_id == -1)) {
            $anchor = t('usermanager_all_users','admin');
            $this->output->add_breadcrumb($WAS_SCRIPT_NAME,$parameters,$attributes,$anchor);
            $attributes['class'] = 'current';
        }
        $anchor = t('usermanager_all_users_count','admin',array('{COUNT}' => $users));
        $this->output->add_menu('  <li>'.html_a($WAS_SCRIPT_NAME,$parameters,$attributes,$anchor));

        //
        // 2 -- are there any users without a group?
        //
        $users = $this->get_num_user_records(GROUP_SELECT_NO_GROUP);
        if ($users > 0) {
            $parameters = $this->a_params(TASK_USERS);
            $parameters['group'] = GROUP_SELECT_NO_GROUP;
            $attributes = array('title' => t('usermanager_users_nogroup_title','admin'));
            if ((!is_null($group_id)) && ($group_id == 0)) {
                $anchor = t('usermanager_users_nogroup','admin');
                $this->output->add_breadcrumb($WAS_SCRIPT_NAME,$parameters,$attributes,$anchor);
                $attributes['class'] = 'current';
            }
            $anchor = t('usermanager_users_nogroup_count','admin',array('{COUNT}' => $users));
            $this->output->add_menu('  <li>'.html_a($WAS_SCRIPT_NAME,$parameters,$attributes,$anchor));
        }

        //
        // 3 -- now iterate through all groups WITH users (if any)
        //
        $group_id = intval($group_id); // we no longer need to deal with NULL or 0 so make group_id an int now
        $sql = sprintf("SELECT g.groupname, g.full_name, g.is_active, g.group_id, COUNT(ugc.user_id) AS users ".
                       "FROM %susers_groups_capacities ugc ".
                       "INNER JOIN %sgroups g USING (group_id) ".
                       "GROUP BY g.groupname, g.full_name, g.is_active, g.group_id",
                       $DB->prefix,$DB->prefix);
        if (($DBResult = $DB->query($sql)) !== FALSE) {
            $records = $DBResult->fetch_all_assoc('group_id');
            $DBResult->close();
            foreach($records as $record) {
                $parameters = $this->a_params(TASK_USERS);
                $parameters['group'] = $record['group_id'];
                $param = array('{COUNT}' => intval($record['users']),
                               '{GROUP}' => $record['groupname'],
                               '{GROUP_FULL_NAME}' => $record['full_name']);
                $attributes = array('title' => t('usermanager_users_group_title','admin',$param));
                if (($group_id == intval($record['group_id']))) {
                    $anchor = t('usermanager_users_group','admin',$param);
                    $this->output->add_breadcrumb($WAS_SCRIPT_NAME,$parameters,$attributes,$anchor);
                    $attributes['class'] = 'current';
                }
                $anchor = t('usermanager_users_group_count','admin',$param);
                $this->output->add_menu('  <li>'.html_a($WAS_SCRIPT_NAME,$parameters,$attributes,$anchor));
            }
        }

        //
        // 4 -- done, close the list
        //
        $this->output->add_menu('<ul>');
    } // show_menu_overview()


    /** display breadcrumb trail that leads to users overview screen
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME;
     */
    function show_breadcrumbs_overview() {
        global $WAS_SCRIPT_NAME;
        $breadcrumbs = array(
            array(
                'parameters' => array('job' => JOB_ACCOUNTMANAGER),
                'anchor' => t('name_accountmanager','admin'),
                'title' => t('description_accountmanager','admin')
            ),
            array(
                'parameters' => array('job' => JOB_ACCOUNTMANAGER, 'task' => TASK_USERS),
                'anchor' => t('menu_users','admin'),
                'title' => t('menu_users_title','admin')
            )
        );
        foreach($breadcrumbs as $b) {
            $this->output->add_breadcrumb($WAS_SCRIPT_NAME,$b['parameters'],array('title' => $b['title']),$b['anchor']);
        }
    } // show_breadcrumbs_overview()


    /** display breadcrumb trail that leads to the edit user dialog
     *
     * @param int $user_id the user of interest
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME;
     * @uses show_breadcrumbs_overview()
     */
    function show_breadcrumbs_user($user_id) {
        global $WAS_SCRIPT_NAME;
        $this->show_breadcrumbs_overview();
        $parameters = $this->a_params(TASK_USER_EDIT,$user_id);
        $attributes = array('title' => t('menu_user_basic_title','admin'));
        $params = $this->get_user_names($user_id);
        $anchor = $params['{USERNAME}'];
        $this->output->add_breadcrumb($WAS_SCRIPT_NAME,$parameters,$attributes,$anchor);
    } // show_breadcrumbs_user()


    /** display breadcrumb trail that leads to the add new user dialog
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME;
     * @uses show_breadcrumbs_overview()
     */
    function show_breadcrumbs_adduser() {
        global $WAS_SCRIPT_NAME;
        $this->show_breadcrumbs_overview();
        $parameters = array('job' => JOB_ACCOUNTMANAGER, 'task' => TASK_USER_ADD);
        $anchor = t('usermanager_add_a_user','admin');
        $attributes = array('title' => t('usermanager_add_a_user_title','admin'));
        $this->output->add_breadcrumb($WAS_SCRIPT_NAME,$parameters,$attributes,$anchor);
    } // show_breadcrumbs_adduser()


    /** shorthand for the anchor parameters that lead to the user manager
     *
     * @param string|null $task the next task to do or NULL if none
     * @param int|null $user_id the user of interest or NULL if none
     * @param int|null $module_id the module of interest or NULL if none
     * @return array ready-to-use array with parameters for constructing a-tag
     */
    function a_params($task=NULL,$user_id=NULL,$module_id=NULL) {
        $parameters = array('job' => JOB_ACCOUNTMANAGER);
        if (!is_null($task)) {
            $parameters['task'] = $task;
        }
        if (!is_null($user_id)) {
            $parameters['user'] = strval($user_id);
        }
        if (!is_null($module_id)) {
            $parameters['module'] = strval($module_id);
        }
        return $parameters;
    } // a_params()

    /** retrieve (a selection of) all user records from the database
     *
     * this retrieves a subset of all existing user accounts from the
     * database. The selection depends on the value of $group_id:
     *
     *  - $group_id == GROUP_SELECT_ALL_USERS (-1): all users ordered by active, username
     *  - $group_id == GROUP_SELECT_NO_GROUP (0): all users without a group, ordered by active, username
     *  - otherwise: users in group $group_id, ordered by active, username
     *
     * Note that in the first two cases there is no capacity, in the third
     * case every user has capacity relating to the specified group.
     *
     * @param int $group_id selection for users
     * @param int $limit maximum number of records to retrieve
     * @param int $offset number of records to skip in result set
     * @return array|bool FALSE on error or else an array with data of selected users
     *
     */
    function get_user_records($group_id,$limit,$offset) {
        global $DB;
        $records = array();
        if ($group_id == GROUP_SELECT_ALL_USERS) { // all users
            $fields = array('user_id','username','full_name','is_active');
            $sort_order = 'CASE WHEN (is_active) THEN 0 ELSE 1 END, username, user_id';
            $records = db_select_all_records('users',$fields,'',$sort_order,'user_id',$limit,$offset);
        } elseif ($group_id == GROUP_SELECT_NO_GROUP) {
            $sql = sprintf("SELECT u.user_id, u.username, u.full_name, u.is_active ".
                           "FROM %susers u ".
                           "LEFT JOIN %susers_groups_capacities ugc USING (user_id) ".
                           "WHERE ugc.user_id IS NULL ".
                           "ORDER BY CASE WHEN (is_active) THEN 0 ELSE 1 END, username, user_id",
                           $DB->prefix,$DB->prefix);
            if (($DBResult = $DB->query($sql,$limit,$offset)) !== FALSE) {
                $records = $DBResult->fetch_all_assoc('user_id');
                $DBResult->close();
            }
        } else {
            $sql = sprintf("SELECT u.user_id, u.username, u.full_name, u.is_active, ugc.capacity_code  ".
                           "FROM %susers u ".
                           "INNER JOIN %susers_groups_capacities ugc USING (user_id) ".
                           "WHERE ugc.group_id = %d ".
                           "ORDER BY CASE WHEN (is_active) THEN 0 ELSE 1 END, username, user_id",
                           $DB->prefix,$DB->prefix,intval($group_id));
            if (($DBResult = $DB->query($sql,$limit,$offset)) !== FALSE) {
                $records = $DBResult->fetch_all_assoc('user_id');
                $DBResult->close();
            } else echo db_errormessage();
        }
        return $records;
    } // get_user_records()


    /** calculate the total number of users in a specific group
     *
     * this calculates the total number of users in group $group_id.
     * If $group_id equates to GROUP_SELECT_ALL_USERS, the grand total
     * is returned, if it equates to GROUP_SELECT_NO_GROUP the number
     * of users without a group is calculated.
     *
     * @param int $group_id which group needs to be counted
     * @return int the number of users in the specified group (could be 0)
     * @uses $DB
     */
    function get_num_user_records($group_id) {
        global $DB;
        $group_id = intval($group_id);
        $num_users = 0;
        if ($group_id == GROUP_SELECT_ALL_USERS) {
            $record = db_select_single_record('users','COUNT(user_id) AS users');
            $num_users = ($record !== FALSE) ? intval($record['users']) : 1; // ignore failure; simply substitute 1 (us)
        } elseif ($group_id == GROUP_SELECT_NO_GROUP) {
            $sql = sprintf("SELECT COUNT(u.user_id) AS users ".
                           "FROM %susers u ".
                           "LEFT JOIN %susers_groups_capacities ugc USING (user_id) ".
                           "WHERE ugc.user_id IS NULL",
                           $DB->prefix,$DB->prefix);
            if (($DBResult = $DB->query($sql,1)) !== FALSE) {
                $record = $DBResult->fetch_row_assoc();
                $num_users = intval($record['users']);
                $DBResult->close();
            } else {
                logger("get_num_user_records(): cannot count users in group '$group_id: ".$db_errormessage());
            }
        } else {
            $where = sprintf('group_id = %d AND capacity_code > 0',$group_id);
            $record = db_select_single_record('users_groups_capacities','COUNT(user_id) AS users',$where);
            $num_users = ($record !== FALSE) ? intval($record['users']) : 0; // ignore failure; simply substitute 0
        }
        return $num_users;
    } // get_num_user_records()


    /** construct a clickable icon to delete this user
     *
     * @param int $user_id the user to delete
     * @return string ready-to-use A-tag
     * @uses $CFG
     * @uses $WAS_SCRIPT_NAME
     */
    function get_icon_delete($user_id) {
        global $CFG,$WAS_SCRIPT_NAME;

        // 1 -- construct the icon (image or text)
        $title = t('icon_user_delete','admin');
        $alt = t('icon_user_delete_alt','admin');
        $text = t('icon_user_delete_text','admin');
        $anchor = $this->output->skin->get_icon('delete', $title, $alt, $text);

        // 2 -- construct the A tag
        $a_params = $this->a_params(TASK_USER_DELETE,$user_id);
        $a_attr = array('title' => $title);
        return html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
    } // get_icon_delete()


    /** construct a clickable icon to edit the properties of this user
     *
     * @param int $user_id the user to edit
     * @return string ready-to-use A-tag
     * @uses $CFG
     * @uses $WAS_SCRIPT_NAME
     */
    function get_icon_edit($user_id) {
        global $CFG,$WAS_SCRIPT_NAME;

        // 1 -- construct the icon (image or text)
        $title = t('icon_user_edit','admin');
        $alt = t('icon_user_edit_alt','admin');
        $text = t('icon_user_edit_text','admin');
        $anchor = $this->output->skin->get_icon('edit', $title, $alt, $text);

        // 2 -- construct the A tag
        $a_params = $this->a_params(TASK_USER_EDIT,$user_id);
        $a_attr = array('title' => $title);
        return html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
    } // get_icon_edit()


    /** construct a clickable icon to delete a membership from this user
     *
     * @param int $user_id the useraccount from which to delete
     * @param int $user_id the group to delete
     * @return string ready-to-use A-tag
     * @uses $CFG
     * @uses $WAS_SCRIPT_NAME
     */
    function get_icon_groupdelete($user_id,$group_id) {
        global $CFG,$WAS_SCRIPT_NAME;

        // 1 -- construct the icon (image or text)
        $title = t('icon_membership_delete','admin');
        $alt = t('icon_membership_delete_alt','admin');
        $text = t('icon_membership_delete_text','admin');
        $anchor = $this->output->skin->get_icon('delete', $title, $alt, $text);

        // 2 -- construct the A tag
        $a_params = $this->a_params(TASK_USER_GROUPDELETE,$user_id);
        $a_params['group'] = $group_id;
        $a_attr = array('title' => $title);
        return html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
    } // get_icon_groupdelete()


    /** construct the add userdialog
     *
     * @return array contains the dialog definition
     */
    function get_dialogdef_add_user() {
        $params = array(
                    '{MIN_LENGTH}' => MINIMUM_PASSWORD_LENGTH,
                    '{MIN_LOWER}' => MINIMUM_PASSWORD_LOWERCASE,
                    '{MIN_UPPER}' => MINIMUM_PASSWORD_UPPERCASE,
                    '{MIN_DIGIT}' => MINIMUM_PASSWORD_DIGITS);
        $dialogdef = array(
            'dialog' => array(
                'type' => F_INTEGER,
                'name' => 'dialog',
                'value' => USERMANAGER_DIALOG_ADD,
                'hidden' => TRUE
            ),
            'username' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'username',
                'minlength' => 1,
                'maxlength' => 60,
                'columns' => 30,
                'label' => t('usermanager_add_username_label','admin'),
                'title' => t('usermanager_add_username_title','admin'),
                'value' => '',
                ),
            'user_password1' => array(
                'type' => F_PASSWORD,
                'name' => 'user_password1',
                'minlength' => MINIMUM_PASSWORD_LENGTH,
                'maxlength' => 255,
                'columns' => 30,
                'label' => t('usermanager_add_user_password1_label','admin',$params),
                'title' => t('usermanager_add_user_password1_title','admin',$params),
                'value' => '',
                ),
            'user_password2' => array(
                'type' => F_PASSWORD,
                'name' => 'user_password2',
                'minlength' => MINIMUM_PASSWORD_LENGTH,
                'maxlength' => 255,
                'columns' => 30,
                'label' => t('usermanager_add_user_password2_label','admin',$params),
                'title' => t('usermanager_add_user_password2_title','admin',$params),
                'value' => '',
                ),
            'user_fullname' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'user_fullname',
                'minlength' => 1,
                'maxlength' => 255,
                'columns' => 50,
                'label' => t('usermanager_add_user_fullname_label','admin'),
                'title' => t('usermanager_add_user_fullname_title','admin'),
                'value' => '',
                ),
            'user_email' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'user_email',
                'minlength' => 0,
                'maxlength' => 255,
                'columns' => 50,
                'label' => t('usermanager_add_user_email_label','admin'),
                'title' => t('usermanager_add_user_email_title','admin'),
                'value' => '',
                ),
            'user_is_active' => array(
                'type' => F_CHECKBOX,
                'name' => 'user_is_active',
                'options' => array(1 => t('usermanager_add_user_is_active_check','admin')),
                'label' => t('usermanager_add_user_is_active_label','admin'),
                'title' => t('usermanager_add_user_is_active_title','admin'),
                'value' => '1', // default is active
                ),

            'button_save' => dialog_buttondef(BUTTON_SAVE),
            'button_cancel' => dialog_buttondef(BUTTON_CANCEL)
            );
        return $dialogdef;
    } // get_dialogdef_add_user()


    /** construct the edit user dialog
     *
     * @param int $user_id indicates which user to edit
     * @return bool|array FALSE on error or array with dialog definition and existing data from database
     * @uses $LANGUAGE
     */
    function get_dialogdef_edit_user($user_id) {
        global $LANGUAGE;

        $user_id = intval($user_id);
        // 1 -- retrieve data from users-record
        if (($user = $this->get_user_record($user_id)) === FALSE) {
            return FALSE;
        }
        $params = array(
                    '{MIN_LENGTH}' => MINIMUM_PASSWORD_LENGTH,
                    '{MIN_LOWER}' => MINIMUM_PASSWORD_LOWERCASE,
                    '{MIN_UPPER}' => MINIMUM_PASSWORD_UPPERCASE,
                    '{MIN_DIGIT}' => MINIMUM_PASSWORD_DIGITS);
        // 2 -- construct dialog definition including current values from database
        $dialogdef = array(
            'dialog' => array(
                'type' => F_INTEGER,
                'name' => 'dialog',
                'value' => USERMANAGER_DIALOG_EDIT,
                'hidden' => TRUE
            ),
            'username' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'username',
                'minlength' => 1,
                'maxlength' => 60,
                'columns' => 30,
                'label' => t('usermanager_edit_username_label','admin'),
                'title' => t('usermanager_edit_username_title','admin'),
                'value' => $user['username'],
                'old_value' => $user['username'],
                ),
            'user_password1' => array(
                'type' => F_PASSWORD,
                'name' => 'user_password1',
                'minlength' => 0,
                'maxlength' => 255,
                'columns' => 30,
                'label' => t('usermanager_edit_user_password1_label','admin',$params),
                'title' => t('usermanager_edit_user_password1_title','admin',$params),
                'value' => '',
                ),
            'user_password2' => array(
                'type' => F_PASSWORD,
                'name' => 'user_password2',
                'minlength' => 0,
                'maxlength' => 255,
                'columns' => 30,
                'label' => t('usermanager_edit_user_password2_label','admin',$params),
                'title' => t('usermanager_edit_user_password2_title','admin',$params),
                'value' => '',
                ),
            'user_fullname' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'user_fullname',
                'minlength' => 1,
                'maxlength' => 255,
                'columns' => 50,
                'label' => t('usermanager_edit_user_fullname_label','admin'),
                'title' => t('usermanager_edit_user_fullname_title','admin'),
                'value' => $user['full_name'],
                'old_value' => $user['full_name'],
                ),
            'user_email' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'user_email',
                'minlength' => 0,
                'maxlength' => 255,
                'columns' => 50,
                'label' => t('usermanager_edit_user_email_label','admin'),
                'title' => t('usermanager_edit_user_email_title','admin'),
                'value' => $user['email'],
                'old_value' => $user['email'],
                ),
           'user_is_active' =>  array(
                'type' => F_CHECKBOX,
                'name' => 'user_is_active',
                'options' => array(1 => t('usermanager_edit_user_is_active_check','admin')),
                'label' => t('usermanager_edit_user_is_active_label','admin'),
                'title' => t('usermanager_edit_user_is_active_title','admin'),
                'value' => (db_bool_is(TRUE,$user['is_active'])) ? '1' : '',
                'old_value' => (db_bool_is(TRUE,$user['is_active'])) ? '1' : ''
                ),
            'user_redirect' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'user_redirect',
                'minlength' => 0,
                'maxlength' => 255,
                'columns' => 50,
                'label' => t('usermanager_edit_user_redirect_label','admin'),
                'title' => t('usermanager_edit_user_redirect_title','admin'),
                'value' => $user['redirect'],
                'old_value' => $user['redirect'],
                ),
            'user_language_key' => array(
                'type' => F_LISTBOX,
                'name' => 'user_language_key',
                'options' => $LANGUAGE->get_active_language_names(),
                'label' => t('usermanager_edit_user_language_label','admin'),
                'title' => t('usermanager_edit_user_language_title','admin'),
                'value' => $user['language_key'],
                'old_value' => $user['language_key']
                ),
            'user_editor' => array(
                'type' => F_LISTBOX,
                'name' => 'user_editor',
                'options' => $this->get_editor_names(),
                'label' => t('usermanager_edit_user_editor_label','admin'),
                'title' => t('usermanager_edit_user_editor_title','admin'),
                'value' => $user['editor'],
                'old_value' => $user['editor']
                ),
            'user_skin' => array(
                'type' => F_LISTBOX,
                'name' => 'user_skin',
                'options' => $this->get_skin_names(),
                'label' => t('usermanager_edit_user_skin_label','admin'),
                'title' => t('usermanager_edit_user_skin_title','admin'),
                'value' => $user['skin'],
                'old_value' => $user['skin']
                ),
            'user_path' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'user_path',
                'minlength' => 1,
                'maxlength' => 60,
                'columns' => 30,
                'label' => t('usermanager_edit_user_path_label','admin'),
                'title' => t('usermanager_edit_user_path_title','admin'),
                'value' => $user['path'],
                'old_value' => $user['path'],
                'viewonly' => TRUE
                ),
            'button_save' => dialog_buttondef(BUTTON_SAVE),
            'button_cancel' => dialog_buttondef(BUTTON_CANCEL)
            );
        return $dialogdef;
    } // get_dialogdef_edit_user()


    /** shortcut to retrieve the username and full name of the selected user
     *
     * @param int $user_id identifies the user of interest
     * @return array with ready-to-use information
     */
    function get_user_names($user_id) {
        if (($record = $this->get_user_record($user_id)) === FALSE) {
            $record = array('username' => strval($user_id),'full_name' => strval($user_id));
        }
        return array('{USERNAME}' => $record['username'],'{FULL_NAME}' => $record['full_name']);
    } // get_user_names()


    /** prepare a list of available editors
     *
     * this routine returs a hardcoded list of available editors: we do not
     * expect to be adding or removing editors to/from the CMS soon, even
     * though CKEditor was added in March 2012.
     *
     * Anyway, it might be cleaner to do generate this list elsewhere.
     * A picklist of available editors is available in the 'editor'
     * parameter in the table 'config'. The actual implementation of editors
     * is done in {@link dialog_get_widget_richtextinput()} in 
     * {@link in dialoglib.php}.
     *
     * Here we (re-)use the translations for the (short) editor option
     * and (long) editor name from the site config dialogs, e.g. via a
     * constructed key 'site_config_editor_{$editor}_option'.
     *
     * @return array list of available editors
     * @todo retrieve this list from 'config'-table?
     */
    function get_editor_names() {
        $options = array();
        foreach( array('ckeditor','fckeditor','plain') as $editor) {
            $options[$editor] = array(
                'option' => t("site_config_editor_{$editor}_option",'admin'),
                'title' =>  t("site_config_editor_{$editor}_title",'admin'));
        }
        return $options;
    } // get_editor_names()


    /** prepare a list of available skins
     *
     * this routine returs a hardcoded list of available skins: we do not
     * expect to be adding or removing skins to/from the CMS any time soon.
     *
     * @return array list of available skins
     */
    function get_skin_names() {
        $options = array();
        foreach( array('base','textonly','braille', 'big', 'lowvision') as $skin) {
            $options[$skin] = array(
                'option' => t("usermanager_edit_user_skin_{$skin}_option",'admin'),
                'title' =>  t("usermanager_edit_user_skin_{$skin}_title",'admin'));
        }
        return $options;
    } // get_skin_names()


    /** determine whether a user has permissions for a particular job
     *
     * this determines whether this user has permissions to access the
     * specified job, e.g. do they have access to the page manager. If so, we can
     * display the menu option, otherwise we can suppress it and keep the menu clean(er).
     *
     * @param int $user_id group to check
     * @param int job a bitmask indicating a particular job
     * @return bool TRUE if the group/capacity has the permission, FALSE otherwise
     */
    function has_job_permission($user_id,$job) {
       if (($acl_id = $this->calc_acl_id($user_id)) === FALSE) {
           return FALSE;
       }
       if (( $jobs = db_select_single_record('acls','permissions_jobs',array('acl_id' => $acl_id))) === FALSE) {
           return FALSE;
       }
       return (($jobs['permissions_jobs'] & $job) == $job) ? TRUE : FALSE;
    } // has_job_permission()


    /** determine the acl_id for user user_id
     *
     * @param int $user_id identifies the user record of interest
     * @return bool|int acl_id on success, FALSE on error
     */
    function calc_acl_id($user_id) {
        if (($record = $this->get_user_record($user_id)) === FALSE) {
            return FALSE;
        }
        return intval($record['acl_id']);
    } // calc_acl_id()


    /** shorthand for the first readable name in a dialogdef item
     *
     * @param array $item contains definition of a single field in a dialog
     * @return string either the label or the name of the item, without tildes
     */
    function get_fname($item) {
        $fname = (isset($item['label'])) ? str_replace('~','',$item['label']) : $item['name'];
        return $fname;
    } // get_fname()


    /** retrieve a single user's record possibly from the cache
     *
     * @param int $user_id identifies the user record
     * @param bool $forced if TRUE unconditionally fetch the record from the database 
     * @return bool|array FALSE if there were errors, the user record otherwise
     */
    function get_user_record($user_id,$forced=FALSE) {
        $user_id = intval($user_id);
        if ((!isset($this->users[$user_id])) || ($forced)) {
            $table = 'users';
            $fields = '*';
            $where = array('user_id' => $user_id);
            if (($record = db_select_single_record($table,$fields,$where)) === FALSE) {
                logger(sprintf("%s.%s(): cannot retrieve record for user '%d': %s",
                               __CLASS__,__FUNCTION__,$user_id,db_errormessage()));
                $this->output->add_message(t('error_retrieving_data','admin'));

                return FALSE;
            } else {
                $this->users[$user_id] = $record;
            }
        }
        return (isset($this->users[$user_id])) ? $this->users[$user_id] : FALSE;
    } // get_user_record()

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
            logger('areas_expand_collapse(): cannot retrieve areas. Mmmm...',WLOG_DEBUG);
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

} // UserManager
?>