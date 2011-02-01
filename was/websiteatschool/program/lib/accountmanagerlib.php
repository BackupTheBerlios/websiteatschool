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
# along with this program. If not, see http://websiteatschool.org/license.html

/** /program/lib/accountmanagerlib.php - accountmanager (users and groups)
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.org/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: accountmanagerlib.php,v 1.1 2011/02/01 13:00:13 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** default selection for account manager: show introduction + links to users and groups */
define('TASK_ACCOUNTS','overview');

/** TASK_USER* relate to user accounts */
define('TASK_USERS','users');
define('TASK_USER_ADD','useradd');
define('TASK_USER_DELETE','userdelete');
define('TASK_USER_EDIT','useredit');
define('TASK_USER_ADVANCED','useradvanced');
define('TASK_USER_GROUPS','usergroups');
define('TASK_USER_GROUPADD','usergroupadd');
define('TASK_USER_GROUPDELETE','usergroupdelete');
define('TASK_USER_GROUPSAVE','usergroupsave');
define('TASK_USER_INTRANET','userintranet');
define('TASK_USER_MODULE','usermodule');
define('TASK_USER_ADMIN','useradmin');
define('TASK_USER_PAGEMANAGER','userpagemanager');
define('TASK_USER_TREEVIEW','usertreeview');
define('TASK_USER_SAVE','usersave');
define('TASK_USER_SAVE_NEW','usersavenew');

/** TASK_GROUP* relate to plain groups */
define('TASK_GROUPS','groups');
define('TASK_GROUP_ADD','groupadd');
define('TASK_GROUP_DELETE','groupdelete');
define('TASK_GROUP_EDIT','groupedit');
define('TASK_GROUP_SAVE','groupsave');
define('TASK_GROUP_SAVE_NEW','groupsavenew');

/** TASK_GROUP_CAPACITY_* relate to group-capacity-combinations  */
define('TASK_GROUP_CAPACITY_OVERVIEW','capacityoverview');
define('TASK_GROUP_CAPACITY_INTRANET','capacityintranet');
define('TASK_GROUP_CAPACITY_MODULE','capacitymodule');
define('TASK_GROUP_CAPACITY_ADMIN','capacityadmin');
define('TASK_GROUP_CAPACITY_PAGEMANAGER','capacitypagemanager');
define('TASK_GROUP_CAPACITY_SAVE','capacitysave');

/** Distinguish between the various dialogs */
define('GROUPMANAGER_DIALOG_ADD',1);
define('GROUPMANAGER_DIALOG_EDIT',2);
define('GROUPMANAGER_DIALOG_DELETE',3);

define('GROUPMANAGER_DIALOG_CAPACITY_INTRANET',13);
define('GROUPMANAGER_DIALOG_CAPACITY_ADMIN',14);
define('GROUPMANAGER_DIALOG_CAPACITY_PAGEMANAGER',15);

define('USERMANAGER_DIALOG_ADD',21);
define('USERMANAGER_DIALOG_EDIT',22);
define('USERMANAGER_DIALOG_DELETE',23);


define('USERMANAGER_DIALOG_INTRANET',33);
define('USERMANAGER_DIALOG_ADMIN',34);
define('USERMANAGER_DIALOG_PAGEMANAGER',35);


/** main entry point for accountmanager (called from admin.php)
 *
 * this routing dispatches the tasks. If a specified task is not
 * recognised, the default task TASK_ACCOUNTS_OVERVIEW is
 * executed. Note that the User Manager and the Group Manager
 * are heavily interconnected. Therefore we use 1 common set
 * of tasks and distinguish between both managers via
 * sets of tasks, e.g. TASK_USER* point to the user manager
 * where TASK_GROUP* lead to the group manager.
 * 
 * @param object &$output collects the html output
 * @return void results are returned as output in $output
 */
function job_accountmanager(&$output) {
    global $CFG;
    $output->set_helptopic('accountmanager');
    $task = get_parameter_string('task',TASK_ACCOUNTS);

    switch ($task) {
    case TASK_ACCOUNTS:
        show_accounts_intro($output);
        show_accounts_menu($output);
        break;

    case TASK_USERS:
    case TASK_USER_ADD:
    case TASK_USER_DELETE:
    case TASK_USER_EDIT:
    case TASK_USER_ADVANCED:
    case TASK_USER_GROUPS:
    case TASK_USER_GROUPADD:
    case TASK_USER_GROUPDELETE:
    case TASK_USER_GROUPSAVE:
    case TASK_USER_INTRANET:
    case TASK_USER_MODULE:
    case TASK_USER_ADMIN:
    case TASK_USER_PAGEMANAGER:
    case TASK_USER_TREEVIEW:
    case TASK_USER_SAVE:
    case TASK_USER_SAVE_NEW:
        include($CFG->progdir.'/lib/usermanager.class.php');
        $mgr = new UserManager($output);
        if ($mgr->show_parent_menu()) {
            show_accounts_menu($output,TASK_USERS);
        }
        break;

    case TASK_GROUPS:
    case TASK_GROUP_ADD:
    case TASK_GROUP_DELETE:
    case TASK_GROUP_EDIT:
    case TASK_GROUP_SAVE:
    case TASK_GROUP_SAVE_NEW:
        include($CFG->progdir.'/lib/groupmanager.class.php');
        $mgr = new GroupManager($output);
        if ($mgr->show_parent_menu()) {
            show_accounts_menu($output,TASK_GROUPS);
        }
        break;

    case TASK_GROUP_CAPACITY_OVERVIEW:
    case TASK_GROUP_CAPACITY_INTRANET:
    case TASK_GROUP_CAPACITY_MODULE:
    case TASK_GROUP_CAPACITY_ADMIN:
    case TASK_GROUP_CAPACITY_PAGEMANAGER:
    case TASK_GROUP_CAPACITY_SAVE:
        include($CFG->progdir.'/lib/groupmanager.class.php');
        $mgr = new GroupManager($output);
        if ($mgr->show_parent_menu()) {
            show_accounts_menu($output,TASK_GROUPS);
        }
        break;

    default:
        if (strlen($task) > 50) {
            $s = substr($task,0,44).' (...)';
        } else {
            $s = $task;
        }
        $message = t('task_unknown','admin',array('{TASK}' => htmlspecialchars($s)));
        $output->add_message($message);
        logger('accountmanager: unknown task: '.htmlspecialchars($s));
        show_accounts_intro($output);
        show_accounts_menu($output);
        break;
    }
} // job_accountmanager()


/** display an introductory text for the account manager + menu
 *
 * @param object &$output collects the html output
 * @return void results are returned as output in $output
 */
function show_accounts_intro(&$output) {
        global $DB;
        $tables = array(
            'users'  => array('pkey' => 'user_id',  'active' => 0, 'inactive' => 0, 'total' => 0),
            'groups' => array('pkey' => 'group_id', 'active' => 0, 'inactive' => 0, 'total' => 0)
            );
        $output->add_content('<h2>'.t('accountmanager_header','admin').'</h2>');
        $output->add_content(t('accountmanager_intro','admin'));

        $class = 'header';
        $output->add_content('<p>');
        $output->add_content(html_table());
        $output->add_content('  '.html_table_row(array('class' => $class)));
        $output->add_content('    '.html_table_head(NULL,t('accountmanager_summary','admin')));
        $output->add_content('    '.html_table_head(NULL,t('accountmanager_active','admin')));
        $output->add_content('    '.html_table_head(NULL,t('accountmanager_inactive','admin')));
        $output->add_content('    '.html_table_head(NULL,t('accountmanager_total','admin')));
        $output->add_content('  '.html_table_row_close());
        foreach($tables as $table_name => $table) {
            $class = ($class == 'odd') ? 'even' : 'odd';
            $sql = sprintf("SELECT is_active, COUNT(%s) AS total FROM %s%s GROUP BY is_active",
                           $table['pkey'],$DB->prefix,$table_name);
            if (($DBResult = $DB->query($sql)) !== FALSE) {
                $records = $DBResult->fetch_all_assoc();
                $DBResult->close();
                foreach($records as $record) {
                    $key = (db_bool_is(TRUE,$record['is_active'])) ? 'active' : 'inactive';
                    $tables[$table_name][$key] += intval($record['total']);
                    $tables[$table_name]['total'] += intval($record['total']);
                }
            }
            $attributes = array('class' => $class);
            $output->add_content('  '.html_table_row($attributes));
            $output->add_content('    '.html_table_cell($attributes,t('accountmanager_'.$table_name,'admin')));
            $attributes['align'] = 'right';
            $output->add_content('    '.html_table_cell($attributes,$tables[$table_name]['active']));
            $output->add_content('    '.html_table_cell($attributes,$tables[$table_name]['inactive']));
            $output->add_content('    '.html_table_cell($attributes,$tables[$table_name]['total']));
            $output->add_content('  '.html_table_row_close());
        }
        $output->add_content(html_table_close());
} // show_accounts_intro()


/** display the account manager menu
 *
 * @param object &$output collects the html output
 * @param string $current_task indicate the current menu selection (if any)
 * @return void results are returned as output in $output
 */
function show_accounts_menu(&$output,$current_task=NULL) {
    global $WAS_SCRIPT_NAME;
    $menu_items = array(
        array(
            'task' => TASK_USERS,
            'anchor' => t('menu_users','admin'),
            'title' => t('menu_users_title','admin')
        ),
        array(
            'task' => TASK_GROUPS,
            'anchor' => t('menu_groups','admin'),
            'title' => t('menu_groups_title','admin')
        )
    );
    $output->add_breadcrumb($WAS_SCRIPT_NAME,
                            array('job' => JOB_ACCOUNTMANAGER),
                            array('title' => t('description_accountmanager','admin')),
                            strtolower(t('name_accountmanager','admin')));
    $output->add_menu('<h2>'.t('menu','admin').'</h2>');
    $output->add_menu('<ul>');
    foreach($menu_items as $item) {
        $parameters = array('job' => JOB_ACCOUNTMANAGER, 'task' => $item['task']);
        $attributes = array('title' => $item['title']);
        if ($current_task == $item['task']) {
            $output->add_breadcrumb($WAS_SCRIPT_NAME,$parameters,$attributes,$item['anchor']);
            $attributes['class'] = 'current';
        }
        $output->add_menu('  <li>'.html_a($WAS_SCRIPT_NAME,$parameters,$attributes,$item['anchor']));
    }
    $output->add_menu('</ul>');
} // show_accounts_menu()

?>