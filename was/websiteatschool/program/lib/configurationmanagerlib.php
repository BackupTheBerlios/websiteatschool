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

/** /program/lib/configurationmanagerlib.php - configurationmanager
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: configurationmanagerlib.php,v 1.2 2011/02/03 14:04:03 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/* This is the list of recognised tasks in Configuration Manager */

define('TASK_CONFIGURATION_INTRO','intro');
define('TASK_AREAS','areas');
define('TASK_SITE','site');
define('TASK_ALERTS','alerts');

define('CHORE_SAVE','save');


/** main entry point for configurationmanager (called from /program/main_admin.php)
 *
 * this routine dispatches the tasks, If the specified task
 * is not recognised, the default task TASK_CONFIGURATION_INTRO
 * is executed.
 *
 * @param object &$output collects the html output
 * @return void results are returned as output in $output
 */
function job_configurationmanager(&$output) {
    global $CFG,$WAS_SCRIPT_NAME,$USER;
    $output->set_helptopic('configurationmanager');
    $task = get_parameter_string('task',TASK_CONFIGURATION_INTRO);
    switch($task) {
    case TASK_CONFIGURATION_INTRO:
        show_configuration_intro($output);
        show_configuration_menu($output);
        break;

    case TASK_AREAS:
        include($CFG->progdir.'/lib/areamanager.class.php');
        $mgr = new AreaManager($output);
        if ($mgr->show_parent_menu()) {
            show_configuration_menu($output,$task);
        }
        break;

    case TASK_SITE:
        if ($USER->has_site_permissions(PERMISSION_SITE_EDIT_SITE)) {
            process_task_site($output);
        } else {
            $output->add_content("<h2>".t('access_denied','admin')."</h2>");
            $output->add_content(t('task_access_denied','admin'));
            $output->add_message(t('task_access_denied','admin'));
            show_configuration_menu($output,TASK_SITE);
            logger("configurationmanager: user '{$USER->username}' tried to access site config without permission");
        }
        break;

    case TASK_ALERTS:
        $output->add_message("STUB: handling for '$task' not implemented");
        show_configuration_intro($output);
        show_configuration_menu($output,$task);
        break;

    default:
        if (strlen($task) > 50) {
            $s = substr($task,0,44).' (...)';
        } else {
            $s = $task;
        }
        $message = t('task_unknown','admin',array('{TASK}' => htmlspecialchars($s)));
        $output->add_message($message);
        logger('configurationmanager: unknown task: '.htmlspecialchars($s));
        show_configuration_intro($output);
        show_configuration_menu($output);
        break;
    }
} // job_configurationmanager()


/** display an introductory text for the configuration manager + menu
 *
 * @param object &$output collects the html output
 * @return void results are returned as output in $output
 */
function show_configuration_intro(&$output) {
        $output->add_content('<h2>'.t('configurationmanager_header','admin').'</h2>');
        $output->add_content(t('configurationmanager_intro','admin'));
} // task_configuration_intro()


/** display the configuration manager menu
 *
 * @param object &$output collects the html output
 * @param string $current_task indicate the current menu selection (if any)
 * @return void results are returned as output in $output
 */
function show_configuration_menu(&$output,$current_task=NULL) {
    global $WAS_SCRIPT_NAME;
    $menu_items = array(
        array(
            'task' => TASK_AREAS,
            'anchor' => t('menu_areas','admin'),
            'title' => t('menu_areas_title','admin')
        ),
        array(
            'task' => TASK_SITE,
            'anchor' => t('menu_site','admin'),
            'title' => t('menu_site_title','admin')
        ),
        array(
            'task' => TASK_ALERTS,
            'anchor' => t('menu_alerts','admin'),
            'title' => t('menu_alerts_title','admin')
        )
    );
    $output->add_menu('<h2>'.t('menu','admin').'</h2>');
    $output->add_menu('<ul>');
    foreach($menu_items as $item) {
        $parameters = array('job' => JOB_CONFIGURATIONMANAGER, 'task' => $item['task']);
        $attributes = array('title' => $item['title']);
        if ($current_task == $item['task']) {
            $attributes['class'] = 'current';
        }
        $output->add_menu('  <li>'.html_a($WAS_SCRIPT_NAME,$parameters,$attributes,$item['anchor']));
    }
    $output->add_menu('</ul>');
} // show_configuration_menu()


/** handle the editing/saving of the main configuration information
 *
 * this routine handles editing of the main configuration parameters.
 * It either displays the edit dialog or saves the modified data and
 * shows the configuration manager introduction screen.
 *
 * Note that we do NOT try to redirect the user via a header() after
 * a succesful save. It would be handy because this particular
 * save action may have had impact on the global configuration,
 * which is already read at this point. By redirecting we would
 * make a fresh start, with the new parameters.
 * However, we lose the easy ability to tell the user that the data
 * was saved (via $output->add_message()). So, either no feedback
 * or obsolete global config in core. Hmmmm. I settle for the feedback
 * and the 'wrong' settings.
 *
 * @param object &$output collects the html output
 * @return void results are returned as output in $output
 * @uses ConfigAssistant()
 */
function process_task_site(&$output) {
        global $CFG,$WAS_SCRIPT_NAME;

        // 1 -- prepare
        include_once($CFG->progdir.'/lib/configassistant.class.php');
        $table = 'config';
        $keyfield = 'name';
        $prefix = 'site_config_';
        $domain = 'admin';
        $where = '';
        $assistant = new ConfigAssistant($table,$keyfield,$prefix,$domain,$where);
        $href = href($WAS_SCRIPT_NAME,array('job' => JOB_CONFIGURATIONMANAGER,
                                            'task' => TASK_SITE,
                                            'chore' => CHORE_SAVE));
        // 2 -- what do we need to do?
        $chore = get_parameter_string('chore');
        if ($chore == CHORE_SAVE) { // save data (or cancel if they want to cancel) 
            if (isset($_POST['button_save'])) {
                if ($assistant->save_data($output)) {
                    //if (!headers_sent()) {
                    //    header('Location: '.href($WAS_SCRIPT_NAME,array('job' => JOB_CONFIGURATIONMANAGER)));
                    //    exit;
                    //} else {
                        show_configuration_intro($output);
                        show_configuration_menu($output);
                    //}
                } else {
                    $output->add_content('<h2>'.t($prefix.'header','admin').'</h2>');
                    $output->add_content(t($prefix.'explanation','admin'));
                    $assistant->show_dialog($output,$href);
                }
            } else {
                $output->add_message(t('cancelled','admin'));
                show_configuration_intro($output);
                show_configuration_menu($output);
            }
        } else { // no save yet, simply show dialog
            $output->add_content('<h2>'.t($prefix.'header','admin').'</h2>');
            $output->add_content(t($prefix.'explanation','admin'));
            $assistant->show_dialog($output,$href);
            show_configuration_menu($output,TASK_SITE);
        }
} // process_task_site()


?>