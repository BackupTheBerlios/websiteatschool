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

/** /program/lib/updatelib.php - update wizard
 *
 * This file handles all system updates. The basic idea is as follows.
 *
 * We assume that a previous version of Website@School is/was already
 * correctly installed using the code in /program/{@link install.php}.
 * Using this version the school has added many, many hours of work in
 * entering data.
 *
 * Now a new version is installed, i.e. the new files (or just the updated
 * files) are copied/uploaded to the webserver, including the file {@link version.php}
 * and perhaps also updated versions of modules, themes, etc. This yields
 * an error message for visitors (the infamous 'error 050') because the
 * database version and the file version no longer match. The user logging
 * in into admin.php is forced to attend to the job 'update', arriving here
 * in {@link job_update()}.
 *
 * There an overview is presented of the core version and versions of all subsystems,
 * with the option to upgrade those that qualify. The actual work for the
 * core version is done in {@link update_core()} in this file. The actual work
 * for modules and themes are done via the code in those subsystems. However,
 * these are called from here.
 *
 * The user is more or less _forced_ to perform the upgrade: all ways lead to
 * job_upgrade() while the internal (database) version does not match the file
 * version.
 *
 * The upgrade should perform all necessary steps to upgrade, ending with
 * updating the internal version number to match the file version. After that
 * the error message '050' for visitors is gone, and admin.php no longer
 * forces the user to come here. It is possible, however, to manually arrive
 * here to check the updates of modules, themes, etc. but I consider that less
 * importent. That is: an upgrade of a module or theme will NOT be forced upon
 * the user. It is wise, though to upgrade, but that is up to the user.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.org/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: updatelib.php,v 1.1 2011/02/01 13:00:33 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/* This is the list of recognised update tasks */

define('TASK_UPDATE_OVERVIEW','overview');
define('TASK_UPDATE_CORE','core');
define('TASK_UPDATE_MODULE','module');
define('TASK_UPDATE_THEME','theme');


/** main entry point for update wizard (called from /program/main_admin.php)
 *
 * This routine takes care of executing update routines for both the core
 * program and modeles, themes, etc. It is called automagically whenever
 * the core program version in the database is different from the version
 * in the file {@lnk version.php} (see also {@link main_admin()}).
 *
 * It can also be called manually via 'job=update'. When no specific
 * task is specified, this routine shows the overview of versions for
 * core, modules, themes, etc. Whenever a component is NOT up to date,
 * an [Update] button is displayed. If a component IS up to date, we
 * simply display the word 'OK'. This implies that when everything is up
 * to date, the overview simply displays a list of OK's and the user
 * is 'free to go'.
 *
 * The actual updates for modules, themes, etc. is done via the various
 * subsystems themselves, e.g. by calling htmlpage_upgrade() in the file
 * /program/modules/htmlpage/htmlpage_install.php. The updates for the
 * core program are actually performed from this file right here, see
 * {@link update_core_2010120800()} below for an example.
 *
 * @param object &$output collects the html output
 * @return void results are returned as output in $output
 */
function job_update(&$output) {
    global $CFG,$WAS_SCRIPT_NAME,$USER;
    $output->set_helptopic('update');
    $task = get_parameter_string('task',TASK_UPDATE_OVERVIEW);
    switch($task) {
    case TASK_UPDATE_OVERVIEW:
        show_update_overview($output);
        break;

    case TASK_UPDATE_CORE:
        update_core($output);
        show_update_overview($output);
        break;

    case TASK_UPDATE_MODULE:
        $module_id = get_parameter_int('id',0);
        update_module($output,$module_id);
        show_update_overview($output);
        break;

    case TASK_UPDATE_THEME:
        $theme_id = get_parameter_int('id',0);
        update_theme($output,$theme_id);
        show_update_overview($output);
        break;

    default:
        if (strlen($task) > 50) {
            $s = substr($task,0,44).' (...)';
        } else {
            $s = $task;
        }
        $message = t('task_unknown','admin',array('{TASK}' => htmlspecialchars($s)));
        $output->add_message($message);
        logger('tools: unknown task: '.htmlspecialchars($s));
        show_update_overview($output);
        break;
    }
} // job_update()


/** display an introductory text for update + status overview
 *
 * @param object &$output collects the html output
 * @return void results are returned as output in $output
 */
function show_update_overview(&$output) {
    global $CFG;

    // 0 -- title and introduction
    $output->add_content('<h2>'.t('update_header','admin').'</h2>');
    $output->add_content(t('update_intro','admin'));

    // 1 -- make a start with 4-col HTML-table with status overview
    $class = 'header';
    $output->add_content('<p>');
    $output->add_content(html_table());
    $output->add_content('  '.html_table_row(array('class' => $class)));
    $output->add_content('    '.html_table_head(NULL,t('update_subsystem','admin')));
    $output->add_content('    '.html_table_head(NULL,t('update_version_database','admin')));
    $output->add_content('    '.html_table_head(NULL,t('update_version_manifest','admin')));
    $output->add_content('    '.html_table_head(NULL,t('update_status','admin')));
    $output->add_content('  '.html_table_row_close());

    // 2 -- core status
    $class = ($class == 'odd') ? 'even' : 'odd';
    $attributes = array('class' => $class);
    $output->add_content('  '.html_table_row($attributes));
    $output->add_content('    '.html_table_cell($attributes,t('update_core','admin')));
    $output->add_content('    '.html_table_cell($attributes,$CFG->version));
    $output->add_content('    '.html_table_cell($attributes,WAS_VERSION));
    if (intval($CFG->version) == intval(WAS_VERSION)) {
        $output->add_content('    '.html_table_cell($attributes,t('update_status_ok','admin')));
    } else {
        $output->add_content('    '.html_table_cell($attributes,update_status_update(TASK_UPDATE_CORE)));
    }
    $output->add_content('  '.html_table_row_close());

    // 3 -- subsystem status
    $subsystems = array(
        'modules' => array(
            'table' => 'modules',
            'fields' => array('module_id','name','version'),
            'keyfield' => 'module_id',
            'path' => $CFG->progdir.'/modules',
            'task' => TASK_UPDATE_MODULE),
        'themes' => array(
            'table' => 'themes',
            'fields' => array('theme_id','name','version'),
            'keyfield' => 'theme_id',
            'path' => $CFG->progdir.'/themes',
            'task' => TASK_UPDATE_THEME)
        );
    foreach($subsystems as $subsystem => $data) {
        //
        // 3A -- subheader spread over all columns
        $class = ($class == 'odd') ? 'even' : 'odd';
        $attributes = array('class' => $class);
        $output->add_content('  '.html_table_row(array('class' => 'header')));
        $attributes['colspan'] = '4';
        $output->add_content('    '.html_table_head($attributes,t('update_subsystem_'.$subsystem,'admin')));
        $output->add_content('  '.html_table_row_close());
        //
        // 3B -- iterate through all installed modules, themes, etc.
        $where = '';
        $order = $data['keyfield'];
        $records = db_select_all_records($data['table'],$data['fields'],$where,$order,$data['keyfield']);
        if ($records === FALSE) {
            logger(sprintf('%s(): error retrieving subsystems \'%s\': %s',__FUNCTION__,$subsystem,db_errormessage()));
            continue;
        }
        foreach($records as $id => $record) {
            $name = $record['name'];
            $version_database = $record['version'];
            $manifests = array();
            $item_manifest = sprintf('%s/%s/%s_manifest.php',$data['path'],$name,$name);
            if (is_file($item_manifest)) {
                @include($item_manifest);
            }
            $version_manifest = (isset($manifests[$name]['version'])) ? $manifests[$name]['version'] : 0;
            $class = ($class == 'odd') ? 'even' : 'odd';
            $attributes = array('class' => $class);
            $output->add_content('  '.html_table_row($attributes));
            $output->add_content('    '.html_table_cell($attributes,htmlspecialchars($name)));
            $output->add_content('    '.html_table_cell($attributes,htmlspecialchars($version_database)));
            $output->add_content('    '.html_table_cell($attributes,htmlspecialchars($version_manifest)));
            if (intval($version_database) == intval($version_manifest)) {
                $output->add_content('    '.html_table_cell($attributes,t('update_status_ok','admin')));
            } else {
                $output->add_content('    '.html_table_cell($attributes,update_status_update($data['task'],$id)));
            }
            $output->add_content('  '.html_table_row_close());
        }
    }
    $output->add_content(html_table_close());
} // show_update_overview()


/** return an anchor tag with link to the specific update function
 *
 * This utility routine returns a ready to user HTML anchor tag.
 *
 * @param string $task which update task do we need to do?
 * @param int|null $id which module/theme/etc. (NULL for core)
 * @return array ready to use HTML-code
 */
function update_status_update($task=NULL,$id=NULL) {
    global $WAS_SCRIPT_NAME;
    $parameters = array('job' => JOB_UPDATE);
    if (!is_null($task)) {
        $parameters['task'] = $task;
    }
    if (!is_null($id)) {
        $parameters['id'] = strval($id);
    }
    return html_a($WAS_SCRIPT_NAME,$parameters,NULL,t('update_status_update','admin'));
} // update_status_update()


/** record the specified version number in the config table AND in $CFG->version
 *
 * This utility routine records the new version number in the config table
 * and also adjusts the version number already in core (in $CFG->version).
 *
 * @param object &$output collects the html output
 * @param int $version the new version number to store in config table
 * @return bool TRUE on success, FALSE otherwise
 */ 
function update_core_version(&$output,$version) {
    global $CFG;
    $table = 'config';
    $fields = array('value' => intval($version));
    $where = array('name' => 'version');
    if (($retval = db_update($table,$fields,$where)) === FALSE) {
        $output->add_message(t('update_core_error','admin',array('{VERSION}' => strval($version))));
        logger(sprintf('%s(): core upgrade to version %s failed: %s',__FUNCTION__,strval($version),db_errormessage()));
    } else {
        $CFG->version = $version;
        $output->add_message(t('update_core_success','admin',array('{VERSION}' => strval($CFG->version))));
        logger(sprintf('%s(): core upgraded to version %s',__FUNCTION__,strval($CFG->version)),LOG_DEBUG);
    }
    return $retval;
} // update_core_version()


/** call the module-specific upgrade routine
 *
 * this routine tries to execute the correct upgrade script/function for
 * module $module_id. If all goes well, a success message is written to $output
 * (and the update is performed), otherwise an error message is written to $output
 * Either way the event is logged via logger().
 *
 * Note that we take care not to load spurious files and execute non-existing functions.
 * However, at some point we do have to have some trust in the file system...
 *
 * @param object &$output collects the html output
 * @param int $module_id primary key for module record in modules table in database
 * @return void results are returned as output in $output
 */
function update_module(&$output,$module_id) {
    global $CFG;
    $messages = array(); // collect messages here (including those from $name_upgrade())
    //
    // 1 -- translate module_id -> name
    $module_id = intval($module_id);
    $table = 'modules';
    $keyfield = 'module_id';
    $fields = array($keyfield,'name');
    $where = array($keyfield => $module_id);
    $record = db_select_single_record($table,$fields,$where);
    if ($record === FALSE) {
        logger(sprintf('%s(): error retrieving data for module \'%d\': %s',__FUNCTION__,$module_id,db_errormessage()));
        $output->add_message(t('update_subsystem_module_error','admin',array('{MODULE}' => strval($module_id))));
        return;
    }
    $name = $record['name'];
    //
    // 2A -- try to load $name_manifest
    $manifests = array();
    $item_manifest = sprintf('%s/modules/%s/%s_manifest.php',$CFG->progdir,$name,$name);
    if (is_file($item_manifest)) {
        @include($item_manifest);
    }
    if ((isset($manifests[$name]['install_script'])) && (!empty($manifests[$name]['install_script']))) {
        $filename = sprintf('%s/modules/%s/%s',$CFG->progdir,$name,$manifests[$name]['install_script']);
        //
        // 2B try to load $name_install
        if (file_exists($filename)) {
            @include_once($filename);
            $item_upgrade = $name.'_upgrade';
            //
            // 2C -- finally try to execute $name_upgrade()
            if (function_exists($item_upgrade)) {
                if ($item_upgrade($messages,$module_id)) {
                    //
                    // 2D -- All is well, get outta here
                    logger(sprintf('%s(): success updating module %s',__FUNCTION__,$name));
                    $messages[] = t('update_subsystem_module_success','admin',array('{MODULE}' => $name));
                    $output->add_message($messages);
                    return;
                } else {
                    logger(sprintf('%s(): %s() returned an error',__FUNCTION__,$item_upgrade));
                }
            } else {
                logger(sprintf('%s(): function %s() does not exist?',__FUNCTION__,$item_upgrade));
            }
        } else {
            logger(sprintf('%s(): file %s does not exist?',__FUNCTION__,$filename));
        }
    } else {
        logger(sprintf('%s(): no install script specified in manifest for %s',__FUNCTION__,$name));
    }
    $messages[] = t('update_subsystem_module_error','admin',array('{MODULE}' => $name));
    $output->add_message($messages);
} // update_module()


/** call the theme-specific upgrade routine
 *
 * this routine tries to execute the correct upgrade script/function for
 * theme $theme_id. If all goes well, a success message is written to $output
 * (and the update is performed), otherwise an error message is written to $output
 * Either way the event is logged via logger().
 *
 * Note that we take care not to load spurious files and execute non-existing functions.
 * However, at some point we do have to have some trust in the file system...
 *
 * @param object &$output collects the html output
 * @param int $theme_id primary key for theme record in themes table in database
 * @return void results are returned as output in $output
 */
function update_theme(&$output,$theme_id) {
    global $CFG;
    $messages = array(); // collect messages here (including those from $name_upgrade())
    //
    // 1 -- translate theme_id -> name
    $theme_id = intval($theme_id);
    $table = 'themes';
    $keyfield = 'theme_id';
    $fields = array($keyfield,'name');
    $where = array($keyfield => $theme_id);
    $record = db_select_single_record($table,$fields,$where);
    if ($record === FALSE) {
        logger(sprintf('%s(): error retrieving data for theme \'%d\': %s',__FUNCTION__,$theme_id,db_errormessage()));
        $output->add_message(t('update_subsystem_theme_error','admin',array('{THEME}' => strval($theme_id))));
        return;
    }
    $name = $record['name'];
    //
    // 2A -- try to load $name_manifest
    $manifests = array();
    $item_manifest = sprintf('%s/themes/%s/%s_manifest.php',$CFG->progdir,$name,$name);
    if (is_file($item_manifest)) {
        @include($item_manifest);
    }
    if ((isset($manifests[$name]['install_script'])) && (!empty($manifests[$name]['install_script']))) {
        $filename = sprintf('%s/themes/%s/%s',$CFG->progdir,$name,$manifests[$name]['install_script']);
        //
        // 2B try to load $name_install
        if (file_exists($filename)) {
            @include_once($filename);
            $item_upgrade = $name.'_upgrade';
            //
            // 2C -- finally try to execute $name_upgrade()
            if (function_exists($item_upgrade)) {
                if ($item_upgrade($messages,$theme_id)) {
                    //
                    // 2D -- All is well, get outta here
                    logger(sprintf('%s(): success updating theme %s',__FUNCTION__,$name));
                    $messages[] = t('update_subsystem_theme_success','admin',array('{THEME}' => $name));
                    $output->add_message($messages);
                    return;
                } else {
                    logger(sprintf('%s(): %s() returned an error',__FUNCTION__,$item_upgrade));
                }
            } else {
                logger(sprintf('%s(): function %s() does not exist?',__FUNCTION__,$item_upgrade));
            }
        } else {
            logger(sprintf('%s(): file %s does not exist?',__FUNCTION__,$filename));
        }
    } else {
        logger(sprintf('%s(): no install script specified in manifest for %s',__FUNCTION__,$name));
    }
    $messages[] = t('update_subsystem_theme_error','admin',array('{THEME}' => $name));
    $output->add_message($messages);
} // update_theme()


/** update the core version in the database to the version in the version.php file (the 'manifest' version)
 *
 *
 * @param object &$output collects the html output
 * @return void results are returned as output in $output
 */
function update_core(&$output) {
    global $CFG;
    if ($CFG->version < 2010092700) {
        logger(sprintf('%s(): version %s is too old; cannot upgrade automatically',__FUNCTION__,strval($CFG->version)));
        $output->add_message(t('update_version_database_too_old','admin',array('{VERSION}' => $CFG->version)));
        return;
    }
    if (!update_core_2010120800($output)) { return; }
    if (!update_core_2010122100($output)) { return; }
    // if (!update_core_2010123100($output)) { return; }
    // ...
} // update_core()


/** perform actual update to version 2010120800
 *
 * @param object &$output collects the html output
 * @return bool TRUE on success, FALSE otherwise
 */
function update_core_2010120800(&$output) {
    global $CFG;
    if ($CFG->version >= 2010120800) {
        return TRUE;
    }
    // ...
    // necessary updates go here (e.g. modify table def, etc. etc.)
    // ..
    // If all is well, we update the version number in the database AND in $CFG->version
    return update_core_version($output,2010120800);
} // update_core_2010120800()


/** perform actual update to version 2010120800
 *
 * @param object &$output collects the html output
 * @return bool TRUE on success, FALSE otherwise
 */
function update_core_2010122100(&$output) {
    global $CFG;
    if ($CFG->version >= 2010122100) {
        return TRUE;
    }
    // ...
    // necessary updates go here (e.g. modify table def, etc. etc.)
    // ..
    // If all is well, we update the version number in the database AND in $CFG->version
    return update_core_version($output,2010122100);
} // update_core_2010122100()


?>