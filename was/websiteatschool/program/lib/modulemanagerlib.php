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
error_reporting(-1);
/** /program/lib/modulemanagerlib.php - modulemanager
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: modulemanagerlib.php,v 1.7 2013/06/11 11:26:06 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

define('TASK_MODULEMANAGER_INTRO','intro');
define('TASK_MODULEMANAGER_SAVE','save');
define('TASK_MODULEMANAGER_EDIT','edit');

/** main entry point for modulemanager (called from /program/main_admin.php)
 *
 * this routine dispatches the tasks, If the specified task
 * is not recognised, the default task TASK_MODULEMANAGER_INTRO
 * is executed.
 *
 * @param object &$output collects the html output
 * @return void results are returned as output in $output
 */
function job_modulemanager(&$output) {
    $output->set_helptopic('modulemanager');
    $task = get_parameter_string('task',TASK_MODULEMANAGER_INTRO);
    switch($task) {
    case TASK_MODULEMANAGER_INTRO:
        modulemanager_show_intro($output);
        modulemanager_show_menu($output);
        break;

    case TASK_MODULEMANAGER_EDIT:
    case TASK_MODULEMANAGER_SAVE:
        modulemanager_process($output,$task);
        break;

    default:
        $s = (utf8_strlen($task) <= 50) ? $task : utf8_substr($task,0,44).' (...)';
        $message = t('task_unknown','admin',array('{TASK}' => htmlspecialchars($s)));
        $output->add_message($message);
        logger('modulemanager: unknown task: '.htmlspecialchars($s));
        modulemanager_show_intro($output);
        modulemanager_show_menu($output);
        break;
    }
} // job_modulemanager()


/** display an introductory text for the module manager
 *
 * @param object &$output collects the html output
 * @return void results are returned as output in $output
 */
function modulemanager_show_intro(&$output) {
        $output->add_content('<h2>'.t('modulemanager_header','admin').'</h2>');
        $output->add_content(t('modulemanager_intro','admin'));
} // modulemanager_show_intro()


/** display the module manager menu
 *
 * @param object &$output collects the html output
 * @param string $current_module indicates the current menu selection (if any)
 * @return void results are returned as output in $output
 */
function modulemanager_show_menu(&$output,$current_module=NULL) {
    global $WAS_SCRIPT_NAME;
    $modules = modulemanager_get_modules();
    $output->add_menu('<h2>'.t('menu','admin').'</h2>');
    $output->add_menu('<ul>');
    if (sizeof($modules) <= 0) {
        $output->add_menu('  <li>'.t('modulemanager_no_modules','admin'));
    } else {
        foreach($modules as $module_id => $module) {
            $parameters = array('job' => JOB_MODULEMANAGER,
                                'task' => TASK_MODULEMANAGER_EDIT,
                                'module' => $module_id);
            $attributes = array('title' => '('.$module['name'].') '.$module['description']);
            $anchor = $module['title'];
            if ($current_module == $module_id) {
                $attributes['class'] = 'current';
            }
            $output->add_menu('  <li>'.html_a($WAS_SCRIPT_NAME,$parameters,$attributes,$anchor));
        }
    }
    $output->add_menu('</ul>');
} // modulemanager_show_menu()


/** retrieve a list of modules that should appear in the module manager
 *
 * this routine returns an array with id, name, title and
 * description of all active modules that have at least
 * one parameter in the modules_properties table.
 * If there are no modules available (or an error occurs)
 * an empty array is returned. The modules in the list is ordered
 * by the translated name (title) of the module, i.e. the order
 * depends on the current translation language.
 *
 * @param bool $forced if TRUE a fresh trip to the database is forced
 * @return array list of modules sorted by (translated) title
 */
function modulemanager_get_modules($forced=FALSE) {
    global $DB;
    static $modules=NULL;
    if (($modules === NULL) || ($forced)) {
        $sql = sprintf('SELECT DISTINCT m.module_id,m.name '.
                       'FROM %smodules m '.
                       'INNER JOIN %smodules_properties mp USING(module_id) '.
                       'WHERE (is_active)',$DB->prefix,$DB->prefix);
        if (($result = $DB->query($sql)) === FALSE) {
            logger('modulemanager: '.db_errormessage());
            return array();
        }
        $records = $result->fetch_all_assoc('module_id');
        $result->close();
        // we now have an array with id's and (short) names keyed by id.
        // now pickup translated names
        foreach($records as $module_id => $module) {
            $name = $module['name'];
            $records[$module_id]['title'] = t('title','m_'.$name);
            $records[$module_id]['description'] = t('description','m_'.$name);
        }
        uasort($records,'modulemanager_cmp');
        $modules = $records;
    }
    return $modules;
} // modulemanager_get_modules()

/** compare two arrays by the title member (for sorting modules)
 *
 * @param array $a
 * @param array $b
 * $return int indicating the ordering of $a and $b like strcmp()
 */
function modulemanager_cmp($a,$b) {
    return utf8_strcasecmp($a['title'],$b['title']);
} // modulemanager_cmp()


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
function modulemanager_process(&$output, $task) {
        global $CFG,$WAS_SCRIPT_NAME;

        // 0 -- sanity check
        $modules = modulemanager_get_modules();
        $module_id = get_parameter_int('module',0);
        if (!isset($modules[$module_id])) {
            logger(sprintf('%s(): unknown module; id = %d',__FUNCTION__,$module_id));
            $output->add_message(t('error_invalid_parameters','admin'));
            modulemanager_show_intro($output);
            modulemanager_show_menu($output);
            return;
        }

        // 1 -- prepare
        include_once($CFG->progdir.'/lib/configassistant.class.php');
        $table = 'modules_properties';
        $keyfield = 'module_property_id';
        $prefix = 'config_';
        $domain = 'm_'.$modules[$module_id]['name'];
        $where = array('module_id' => $module_id);
        $assistant = new ConfigAssistant($table,$keyfield,$prefix,$domain,$where);
        $href = href($WAS_SCRIPT_NAME,array('job' => JOB_MODULEMANAGER,
                                            'task' => TASK_MODULEMANAGER_SAVE,
                                            'module' => $module_id));
        // 2 -- what do we need to do?
        if ($task == TASK_MODULEMANAGER_SAVE) { // save data (or cancel if they want to cancel) 
            if (isset($_POST['button_save'])) {
                if ($assistant->save_data($output)) {
                    modulemanager_show_intro($output);
                    modulemanager_show_menu($output,$module_id);
                } else {
                    $output->add_content('<h2>'.t($prefix.'header',$domain).'</h2>');
                    $output->add_content(t($prefix.'explanation',$domain));
                    $assistant->show_dialog($output,$href);
                }
            } else {
                $output->add_message(t('cancelled','admin'));
                modulemanager_show_intro($output);
                modulemanager_show_menu($output,$module_id);
            }
        } else { // no save yet, simply show dialog
            $output->add_content('<h2>'.t($prefix.'header',$domain).'</h2>');
            $output->add_content(t($prefix.'explanation',$domain));
            $assistant->show_dialog($output,$href);
            modulemanager_show_menu($output,$module_id);
        }
} // modulemanager_process()


?>