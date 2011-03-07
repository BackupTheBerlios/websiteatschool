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
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: updatelib.php,v 1.6 2011/03/07 14:14:20 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/* This is the list of recognised update tasks */

define('TASK_UPDATE_OVERVIEW', 'overview'        );
define('TASK_UPDATE_CORE',     'core'            );

define('TASK_INSTALL_LANGUAGE','install_language');
define('TASK_UPDATE_LANGUAGE', 'update_language' );

define('TASK_INSTALL_MODULE',  'install_module'  );
define('TASK_UPDATE_MODULE',   'update_module'   );

define('TASK_INSTALL_THEME',   'install_theme'   );
define('TASK_UPDATE_THEME',    'update_theme'    );


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
 * Note that we give a core update high priority: if the core
 * is not up to date, nothing will work, except updating the core.
 *
 * @param object &$output collects the html output
 * @return void results are returned as output in $output
 */
function job_update(&$output) {
    global $CFG,$WAS_SCRIPT_NAME,$USER;
    $output->set_helptopic('update');
    $task = get_parameter_string('task',TASK_UPDATE_OVERVIEW);

    if ($task == TASK_UPDATE_OVERVIEW) {
        update_show_overview($output);
    } elseif ($task == TASK_UPDATE_CORE) {
        update_core($output);
        update_show_overview($output);
    } elseif (intval($CFG->version) != intval(WAS_VERSION)) {
        $output->add_message(t('update_core_warnning_core_goes_first','admin'));
        update_show_overview($output);
    } else {
        $key = get_parameter_string('key','');
        switch($task) {
        case TASK_INSTALL_LANGUAGE:
            install_language($output,$key);
            update_show_overview($output);
            break;

        case TASK_UPDATE_LANGUAGE:
            update_language($output,$key);
            update_show_overview($output);
            break;

        case TASK_INSTALL_MODULE:
            install_module($output,$key);
            update_show_overview($output);
            break;

        case TASK_UPDATE_MODULE:
            update_module($output,$key);
            update_show_overview($output);
            break;

        case TASK_INSTALL_THEME:
            install_theme($output,$key);
            update_show_overview($output);
            break;

        case TASK_UPDATE_THEME:
            update_theme($output,$key);
            update_show_overview($output);
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
            update_show_overview($output);
            break;
        }
    }
} // job_update()


/** display an introductory text for update + status overview
 *
 * @param object &$output collects the html output
 * @return void results are returned as output in $output
 */
function update_show_overview(&$output) {
    global $CFG;

    // 0 -- title and introduction
    $output->add_content('<h2>'.t('update_header','admin').'</h2>');
    $output->add_content(t('update_intro','admin'));

    // 1 -- show core status in an HTML-table
    update_status_table_open($output);
    $class = 'odd';
    $attributes = array('class' => $class);
    $output->add_content('  '.html_table_row($attributes));
    $output->add_content('    '.html_table_cell($attributes,t('update_core','admin')));
    $output->add_content('    '.html_table_cell($attributes,$CFG->version));
    $core_version_release_date = sprintf('%s (%s) %s',WAS_VERSION,WAS_RELEASE_DATE,WAS_RELEASE);
    $output->add_content('    '.html_table_cell($attributes,$core_version_release_date));
    if (intval($CFG->version) == intval(WAS_VERSION)) {
        $output->add_content('    '.html_table_cell($attributes,t('update_status_ok','admin')));
    } else {
        $output->add_content('    '.html_table_cell($attributes,update_status_anchor(TASK_UPDATE_CORE)));
    }
    $output->add_content('  '.html_table_row_close());
    update_status_table_close($output);

    // 2 -- subsystem status
    $subsystems = array(
        'languages'    => array(
            'table'    => 'languages',
            'fields'   => array('language_key','version'),
            'keyfield' => 'language_key',
            'path'     => $CFG->progdir.'/languages',
            'install'  => TASK_INSTALL_LANGUAGE,
            'update'   => TASK_UPDATE_LANGUAGE
            ),
        'modules' => array(
            'table'    => 'modules',
            'fields'   => array('name','version'),
            'keyfield' => 'name',
            'path'     => $CFG->progdir.'/modules',
            'install'  => TASK_INSTALL_MODULE,
            'update'   => TASK_UPDATE_MODULE
            ),
        'themes' => array(
            'table'    => 'themes',
            'fields'   => array('name','version'),
            'keyfield' => 'name',
            'path'     => $CFG->progdir.'/themes',
            'install'  => TASK_INSTALL_THEME,
            'update'   => TASK_UPDATE_THEME
            ),
        );
    foreach($subsystems as $subsystem => $data) {

        // 2A -- retrieve all manifests (including un-installed subsystems)
        $manifests = get_manifests($data['path']);

        // 2B -- retrieve all installed subsystems by consulting the database
        $where = '';
        $order = $data['keyfield'];
        $records = db_select_all_records($data['table'],$data['fields'],$where,$order,$data['keyfield']);
        if ($records === FALSE) {
            logger(sprintf('%s(): error retrieving subsystems \'%s\'; continuing nevertheless: %s',
                            __FUNCTION__,$subsystem,db_errormessage()));
            $records = array(); 
        }

        // 2C -- open an HTML-table for status overview
        $title = t('update_subsystem_'.$subsystem,'admin');
        update_status_table_open($output,$title);
        $class = 'even';

        // 2D -- step through all available manifests and show diff's (if any)
        foreach($manifests as $key => $manifest) {
            $version_manifest = (isset($manifest['version'])) ? $manifest['version'] : NULL;
            $version_database = (isset($records[$key]['version'])) ? $records[$key]['version'] : NULL;
            /*
             * At this point there are several possibilities for version_manifest (M) and version_database (D)
             * - both M and D are integers AND M == D: subsystem is up to date: show 'OK'
             * - both M and D are integers AND M > D: subsystem upgrade required: show 'Update' link
             * - both M and D are integers AND M < D: huh, subsystem downgrade?: show 'ERROR'
             * - M is an integer and D is NULL: subsystem apparently not yet installed: show 'Install' link
             * - M is NULL (and D is don't care): not a valid manifest, skip (but log) this one
             */
            if (is_null($version_manifest)) {
                logger(sprintf('%s(): subsystem \'%s/%s\' has no internal version; skipping this manifest',
                                __FUNCTION__,$subsystem,$key));
                continue;
            } elseif (is_null($version_database)) {
                $version_database = '-';
                $status = update_status_anchor($data['install'],$key,t('update_status_install','admin'));
            } elseif (intval($version_manifest) == intval($version_database)) {
                $status = t('update_status_ok','admin');
            } elseif (intval($version_manifest) > intval($version_database)) {
                $status = update_status_anchor($data['update'],$key,t('update_status_update','admin'));
            } else {
                $status = t('update_status_error','admin');
                logger(sprintf('%s(): weird: \'%s/%s\' database version (%d) is greater than manifest version (%d)?',
                               __FUNCTION__,$subsystem,$key,intval($version_database),intval($version_manifest)));
            }
            $class = ($class == 'odd') ? 'even' : 'odd';
            $attributes = array('class' => $class);
            if ((isset($manifest['release_date'])) && (!empty($manifest['release_date']))) {
                $version_manifest .= ' ('.$manifest['release_date'].')';
            }
            if ((isset($manifest['release'])) && (!empty($manifest['release']))) {
                $version_manifest .= ' '.$manifest['release'];
            }
            $output->add_content('  '.html_table_row($attributes));
            $output->add_content('    '.html_table_cell($attributes,htmlspecialchars($key)));
            $output->add_content('    '.html_table_cell($attributes,htmlspecialchars($version_database)));
            $output->add_content('    '.html_table_cell($attributes,htmlspecialchars($version_manifest)));
            $output->add_content('    '.html_table_cell($attributes,$status));
            $output->add_content('  '.html_table_row_close());
        }

        // 2E -- now check for orphans (database records without matching manifest)
        foreach($records as $key => $record) {
            if (isset($manifests[$key])) { // already dealt with in the foreach loop over all manifests
                continue;
            }
            // this should NOT happen!
            $version_database = (isset($record['version'])) ? $record['version'] : 'NULL';
            $status = t('update_status_error','admin');
            $class = ($class == 'odd') ? 'even' : 'odd';
            $attributes = array('class' => $class);
            $output->add_content('  '.html_table_row($attributes));
            $output->add_content('    '.html_table_cell($attributes,htmlspecialchars($key)));
            $output->add_content('    '.html_table_cell($attributes,htmlspecialchars($version_database)));
            $output->add_content('    '.html_table_cell($attributes,'?'));
            $output->add_content('    '.html_table_cell($attributes,$status));
            $output->add_content('  '.html_table_row_close());
            logger(sprintf('%s(): weird: \'%s/%s\' database version (%s) exists without corresponding manifest?',
                           __FUNCTION__,$subsystem,$key,strval($version_database)));
        }
        update_status_table_close($output);
    }
} // update_show_overview()


/** install an additional language pack
 *
 * this routine attempts to insert the information from the
 * manifest of language $language_key into the database.
 * The routine displays the result (error or success) in a
 * message in $output. Details can be found in the logs.
 *
 * The language_key is validated by reading all existing manifests.
 * This is quite expensive, but that is not important because we
 * do not use this routine very often anyway.
 *
 * Note that we assume that the actual translations of the
 * language pack are already unpacked into the correct directories.
 * The corresponding manifest should exist in the directory
 * /program/languages/$language_key.
 *
 * @param object &$output collects the html output
 * @param string $lanuage_key primary key for language record in database AND name of the /program/languages subdirectory
 * @return void results are returned as output in $output
 */
function install_language(&$output,$language_key) {
    global $CFG;
    $retval = TRUE; // assume success
    $language_key = strval($language_key);
    $progdir_languages = $CFG->progdir.'/languages';
    $datadir_languages = $CFG->datadir.'/languages';
    $manifests = get_manifests($progdir_languages);
    $params = array('{LANGUAGE}' => $language_key);
    if (!isset($manifests[$language_key])) {
        logger(sprintf('%s(): manifest for language \'%s\' not found; nothing installed',__FUNCTION__,$language_key));
        $retval = FALSE;
    } else {
        $manifest = $manifests[$language_key];
        if ((!is_dir($datadir_languages.'/'.$language_key)) &&
            (!@mkdir($datadir_languages.'/'.$language_key,0700))) {
            logger(sprintf('%s(): could not create directory %s/%s',__FUNCTION__,$datadir_languages,$language_key));
            $retval = FALSE;
        }
        @touch($datadir_languages.'/'.$language_key.'/index.html'); // try to "protect" directory never mind errors
        $table = 'languages';
        $fields = array(
            'language_key'        => $language_key,
            'language_name'       => (isset($manifest['language_name'])) ?
                                         strval($manifest['language_name']) : '('.$language_key.')',
            'parent_language_key' => (isset($manifest['parent_language_key'])) ? 
                                         strval($manifest['parent_language_key']) : '',
            'version'             => intval($manifest['version']),
            'manifest'            => $manifest['manifest'],
            'is_core'             => ((isset($manifest['is_core'])) && ($manifest['is_core'])) ? TRUE : FALSE,
            'is_active'           => TRUE,
            'dialect_in_database' => FALSE,
            'dialect_in_file'     => FALSE
            );
        if (db_insert_into($table,$fields) === FALSE) {
            logger(sprintf('%s(): cannot install language \'%s\': %s',__FUNCTION__,$language_key,db_errormessage()));
            $retval = FALSE;
        }
    }
    if ($retval) {
        logger(sprintf('%s(): success installing language \'%s\'',__FUNCTION__,$language_key));
        $output->add_message(t('update_subsystem_language_success','admin',$params));
    } else {
        $output->add_message(t('update_subsystem_language_error','admin',$params));
    }
} // install_language()


/** update a language in the database
 *
 * this routine tries to update the information in the database with the
 * information in the language manifest of the selected language $language_key.
 * The event is logged via logger().
 *
 * Note that an upgrade of a language is not at all interesting because there
 * is nothing to do except to update the data in the databse with that from
 * the manifest. However, we still do it this way in order for the user to
 * grow accustomed to it so we can complexicate this routine in the future
 * without the user having to learn new tricks.
 *
 * @param object &$output collects the html output
 * @param string $lanuage_key primary key for language record in database AND name of the /program/languages subdirectory
 * @return void results are returned as output in $output
 */
function update_language(&$output,$language_key) {
    global $CFG;
    $retval = TRUE; // assume success
    $language_key = strval($language_key);
    $progdir_languages = $CFG->progdir.'/languages';
    $datadir_languages = $CFG->datadir.'/languages';
    $manifests = get_manifests($progdir_languages);
    $params = array('{LANGUAGE}' => $language_key);
    if (!isset($manifests[$language_key])) {
        logger(sprintf('%s(): manifest for language \'%s\' not found; nothing updated',__FUNCTION__,$language_key));
        $retval = FALSE;
    } else {
        $manifest = $manifests[$language_key];
        // the directory _should_ already exist, but better safe than sorry
        if ((!is_dir($datadir_languages.'/'.$language_key)) &&
            (!@mkdir($datadir_languages.'/'.$language_key,0700))) {
            logger(sprintf('%s(): could not create directory %s/%s',__FUNCTION__,$datadir_languages,$language_key));
            $retval = FALSE;
        }
        @touch($datadir_languages.'/'.$language_key.'/index.html'); // try to "protect" directory never mind errors
        $table = 'languages';
        $fields = array(
            'language_key'        => $language_key,
            'language_name'       => (isset($manifest['language_name'])) ?
                                         strval($manifest['language_name']) : '('.$language_key.')',
            'parent_language_key' => (isset($manifest['parent_language_key'])) ? 
                                         strval($manifest['parent_language_key']) : '',
            'version'             => intval($manifest['version']),
            'manifest'            => $manifest['manifest'],
            'is_core'             => ((isset($manifest['is_core'])) && ($manifest['is_core'])) ? TRUE : FALSE,
            'is_active'           => TRUE
            );
        $where = array('language_key' => $language_key);
        if (db_update($table,$fields,$where) === FALSE) {
            logger(sprintf('%s(): cannot update language \'%s\': %s',__FUNCTION__,$language_key,db_errormessage()));
            $retval = FALSE;
        }
    }
    if ($retval) {
        logger(sprintf('%s(): success updating language \'%s\'',__FUNCTION__,$language_key));
        $output->add_message(t('update_subsystem_language_success','admin',$params));
    } else {
        $output->add_message(t('update_subsystem_language_error','admin',$params));
    }
} // update_language()


/** install an additional module
 *
 * this routine attempts to insert the information from the
 * manifest of module $module_key into the database.
 * The routine displays the result (error or success) in a
 * message in $output. Details can be found in the logs.
 *
 * The module_key is validated by reading all existing module manifests.
 * This is quite expensive, but that is not important because we
 * do not use this routine very often anyway.
 *
 * Note that we assume that the actual modules are already
 * unpacked into the correct directories.
 * The corresponding manifest should exist in the directory
 * /program/modules/$module_key.
 *
 * @param object &$output collects the html output
 * @param string $module_key primary key for module record in database AND name of the /program/modules subdirectory
 * @return void results are returned as output in $output
 */
function install_module(&$output,$module_key) {
    return FALSE; // stub
} // install_module()


/** call the module-specific upgrade routine
 *
 * this routine tries to execute the correct upgrade script/function for
 * module $module_key. If all goes well, a success message is written to $output
 * (and the update is performed), otherwise an error message is written to $output
 * Either way the event is logged via logger().
 *
 * Note that we take care not to load spurious files and execute non-existing functions.
 * However, at some point we do have to have some trust in the file system...
 *
 * @param object &$output collects the html output
 * @param string $module_key unique secondary key for module record in modules table in database
 * @return void results are returned as output in $output
 */
function update_module(&$output,$module_key) {
    global $CFG;
    $module_key = strval($module_key);
    $params = array('{MODULE}' => $module_key);
    $progdir_modules = $CFG->progdir.'/modules';
    $manifests = get_manifests($progdir_modules);

    // 1 -- validate input: module must exist in available manifests
    if (!isset($manifests[$module_key])) {
        logger(sprintf('%s(): manifest for module \'%s\' not found; nothing updated',__FUNCTION__,$module_key));
        $output->add_message(t('update_subsystem_module_error','admin',$params));
        return;
    }
    $manifest = $manifests[$module_key];

    // 2 -- get the primary key of the installed module (required for {$module}_update() function)
    $table = 'modules';
    $fields = array('module_id','name');
    $where = array('name' => $module_key);
    $record = db_select_single_record($table,$fields,$where);
    if ($record === FALSE) {
        logger(sprintf('%s(): error retrieving module_id for \'%s\': %s',__FUNCTION__,$module_key,db_errormessage()));
        $output->add_message(t('update_subsystem_module_error','admin',$params));
        return;
    }
    $name = $record['name'];
    $module_id = intval($record['module_id']);

    // 3 -- let the module update code do its thing
    $retval = FALSE; // assume failure
    $messages = array(); // collects error messages from call-back routine
    if ((isset($manifest['install_script'])) && (!empty($manifest['install_script']))) {
        $filename = sprintf('%s/%s/%s',$progdir_modules,$name,$manifest['install_script']);
        if (file_exists($filename)) {
            @include_once($filename);
            $module_upgrade = $name.'_upgrade';
            if (function_exists($module_upgrade)) {
                if ($module_upgrade($messages,$module_id)) {
                    logger(sprintf('%s(): %s(): success upgrading module \'%s\' (new version is %d)',
                                   __FUNCTION__,$module_upgrade,$name,$manifest['version']));
                    $retval = TRUE; // so far so good; remember this success
                } else {
                    logger(sprintf('%s(): %s() returned an error',__FUNCTION__,$module_upgrade));
                }
                foreach($messages as $message) { // remember messages, either good or bad
                    logger($message);
                }
            } else {
                logger(sprintf('%s(): function %s() does not exist?',__FUNCTION__,$module_upgrade));
            }
        } else {
            logger(sprintf('%s(): file %s does not exist?',__FUNCTION__,$filename));
        }
    } else {
        logger(sprintf('%s(): no install script specified in manifest for module \'%s\'',__FUNCTION__,$name));
    }

    // 4 -- on success update the relevant record in the database (and make it active)
    if ($retval) {
        $table = 'modules';
        $fields = array(
            'version'       => intval($manifest['version']),
            'manifest'      => $manifest['manifest'],
            'is_active'     => TRUE,
            'has_acls'      => ((isset($manifest['has_acls'])) && ($manifest['has_acls'])) ? TRUE : FALSE,
            'view_script'   => (isset($manifest['view_script']))   ? $manifest['view_script']           : NULL,
            'admin_script'  => (isset($manifest['admin_script']))  ? $manifest['admin_script']          : NULL,
            'search_script' => (isset($manifest['search_script'])) ? $manifest['search_script']         : NULL,
            'cron_script'   => (isset($manifest['cron_script']))   ? $manifest['cron_script']           : NULL,
            'cron_interval' => (isset($manifest['cron_interval'])) ? intval($manifest['cron_interval']) : NULL
        );
        $where = array('module_id' => $module_id);
        if (db_update($table,$fields,$where) === FALSE) {
            logger(sprintf('%s(): cannot update module data for \'%s\': %s',__FUNCTION__,$module_key,db_errormessage()));
            $retval = FALSE;
        }
    }

    // 5 -- inform the user about the final outcome
    if ($retval) {
        $output->add_message(t('update_subsystem_module_success','admin',$params));
    } else {
        $output->add_message(t('update_subsystem_module_error','admin',$params));
    }
} // update_module()


/** install an additional theme
 *
 * this routine attempts to insert the information from the
 * manifest of theme $theme_key into the database.
 * The routine displays the result (error or success) in a
 * message in $output. Details can be found in the logs.
 *
 * The theme_key is validated by reading all existing module manifests.
 * This is quite expensive, but that is not important because we
 * do not use this routine very often anyway.
 *
 * Note that we assume that the actual thenes are already
 * unpacked into the correct directories.
 * The corresponding manifest should exist in the directory
 * /program/themes/$theme_key.
 *
 * @param object &$output collects the html output
 * @param string $theme_key primary key for theme record in database AND name of the /program/themes subdirectory
 * @return void results are returned as output in $output
 */
function install_theme(&$output,$theme_key) {
    $params = array('{THEME}' => $theme_key);
    $output->add_message(t('update_subsystem_theme_error','admin',$params));
} // install_theme()


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
 * @param string $theme_key unique secondary key for theme record in themes table in database
 * @return void results are returned as output in $output
 */
function update_theme(&$output,$theme_key) {
    global $CFG;
    $theme_key = strval($theme_key);
    $params = array('{THEME}' => $theme_key);
    $progdir_themes = $CFG->progdir.'/themes';
    $manifests = get_manifests($progdir_themes);

    // 1 -- validate input: theme must exist in available manifests
    if (!isset($manifests[$theme_key])) {
        logger(sprintf('%s(): manifest for theme \'%s\' not found; nothing updated',__FUNCTION__,$theme_key));
        $output->add_message(t('update_subsystem_theme_error','admin',$params));
        return;
    }
    $manifest = $manifests[$theme_key];

    // 2 -- get the primary key of the installed theme (required for {$theme}_update() function)
    $table = 'themes';
    $fields = array('theme_id','name');
    $where = array('name' => $theme_key);
    $record = db_select_single_record($table,$fields,$where);
    if ($record === FALSE) {
        logger(sprintf('%s(): error retrieving theme_id for \'%s\': %s',__FUNCTION__,$theme_key,db_errormessage()));
        $output->add_message(t('update_subsystem_theme_error','admin',$params));
        return;
    }
    $name = $record['name'];
    $theme_id = intval($record['theme_id']);

    // 3 -- let the theme update code do its thing
    $retval = FALSE; // assume failure
    $messages = array(); // collects error messages from call-back routine
    if ((isset($manifest['install_script'])) && (!empty($manifest['install_script']))) {
        $filename = sprintf('%s/%s/%s',$progdir_themes,$name,$manifest['install_script']);
        if (file_exists($filename)) {
            @include_once($filename);
            $theme_upgrade = $name.'_upgrade';
            if (function_exists($theme_upgrade)) {
                if ($theme_upgrade($messages,$theme_id)) {
                    logger(sprintf('%s(): %s(): success upgrading theme \'%s\' (new version is %d)',
                                   __FUNCTION__,$theme_upgrade,$name,$manifest['version']));
                    $retval = TRUE; // so far so good; remember this success
                } else {
                    logger(sprintf('%s(): %s() returned an error',__FUNCTION__,$theme_upgrade));
                }
                foreach($messages as $message) { // remember messages, either good or bad
                    logger($message);
                }
            } else {
                logger(sprintf('%s(): function %s() does not exist?',__FUNCTION__,$theme_upgrade));
            }
        } else {
            logger(sprintf('%s(): file %s does not exist?',__FUNCTION__,$filename));
        }
    } else {
        logger(sprintf('%s(): no install script specified in manifest for theme \'%s\'',__FUNCTION__,$name));
    }

    // 4 -- on success update the relevant record in the database (and make it active)
    if ($retval) {
        $table = 'themes';
        $fields = array(
            'version' => intval($manifest['version']),
            'manifest' => $manifest['manifest'],
            'is_active' => TRUE,
            'class' => (isset($manifest['class'])) ? $manifest['class'] : NULL,
            'class_file' => (isset($manifest['class_file'])) ? $manifest['class_file'] : NULL
        );
        $where = array('theme_id' => $theme_id);
        if (db_update($table,$fields,$where) === FALSE) {
            logger(sprintf('%s(): cannot update theme data for \'%s\': %s',__FUNCTION__,$theme_key,db_errormessage()));
            $retval = FALSE;
        }
    }

    // 5 -- inform the user about the final outcome
    if ($retval) {
        $output->add_message(t('update_subsystem_theme_success','admin',$params));
    } else {
        $output->add_message(t('update_subsystem_theme_error','admin',$params));
    }
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
    if (!update_core_2011020100($output)) { return; }
    // if (!update_core_2011mmdd00($output)) { return; }
    // ...
} // update_core()


// ==================================================================
// =========================== UTILITIES ============================
// ==================================================================

/** return an anchor tag with link to the specific update function
 *
 * This utility routine returns a ready to user HTML anchor tag.
 *
 * @param string $task which update task do we need to do?
 * @param string||null $key which module/theme/etc. (NULL for core)
 * @param string $anchor text to show in link
 * @return array ready to use HTML-code
 */
function update_status_anchor($task=NULL,$key=NULL,$anchor=NULL) {
    global $WAS_SCRIPT_NAME;
    $parameters = array('job' => JOB_UPDATE);
    if (!is_null($task)) {
        $parameters['task'] = $task;
    }
    if (!is_null($key)) {
        $parameters['key'] = strval($key);
    }
    if (is_null($anchor)) {
        $anchor = t('update_status_update','admin');
    }
    return html_a($WAS_SCRIPT_NAME,$parameters,NULL,$anchor);
} // update_status_anchor()


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


/** open a status overview HTML-table including column headers
 *
 * this routine opens an HTML-table in prepration for a status
 * overview of a subsystem (languages, modules, themes). The optional
 * title is used as the header of the first column.
 *
 * Because we display the release and release_date in the 3rd column,
 * the columns have different widths: 20% 20% 40% 20%.
 *
 * @param object &$output collects the html output
 * @param string $title is the header of the first column
 * @return void results are returned as output in $output
 */
function update_status_table_open(&$output,$title='') {
    $output->add_content('<p>');
    $output->add_content(html_table(array('width' => '98%')));
    $attributes = array('class' => 'header');
    $output->add_content('  '.html_table_row($attributes));
    $attributes['align'] = 'left';
    $attributes['width'] = '20%';
    $output->add_content('    '.html_table_head($attributes,$title));
    $output->add_content('    '.html_table_head($attributes,t('update_version_database','admin')));
    $attributes['width'] = '40%';
    $output->add_content('    '.html_table_head($attributes,t('update_version_manifest','admin')));
    $attributes['width'] = '20%';
    $output->add_content('    '.html_table_head($attributes,t('update_status','admin')));
    $output->add_content('  '.html_table_row_close());
} // update_status_table_open()

/** close the status overview HTML-table we opened before
 *
 * this is the companion routine for {@link update_status_table_open()};
 * it closes the open HTML-table
 *
 * @param object &$output collects the html output
 * @return void results are returned as output in $output
 */
function update_status_table_close(&$output) {
    $output->add_content(html_table_close());
} // update_status_table_close()


/** retrieve an array of manifests for modules, themes or languages
 *
 * this examines the file system starting in the directory $path,
 * looking for manifest files. These manifest files are named after
 * the subdirectory they are in as follows.
 * Example:
 * If $path is /program/modules, this routine steps through that directory
 * and may find subdirectories 'htmlpage', 'guestbook' and 'forum'.
 * Eventually these manfest files are include()'d:
 * /program/modules/htmlpage/htmlpage_manifest.php,
 * /program/modules/guestbook/guestbook_manifest.php and
 * /program/modules/forum/forum_manifest.php.
 *
 * Every manifest file must describe the module (or language or theme)
 * via the following construct:
 * <code>
 * $manifests['htmlpage'] = array('name' => 'htmlpage', ...., 'cron_interval' => 0);
 * </code>
 *
 * After processing all the subdirectories of $path, the resulting array $manifests is
 * returned. Note that pseudo-directories like '.' and '..' are not considered. Also,
 * subdirectories 'foo' without the file 'foo_manifest.php' are also ignored.
 *
 * Note that the name of the manifest file itself is also stored in the array,
 * but excluding the subdirectory name.
 *
 * Note: a similar routine is used in the installation script {@link install.php}.
 * 
 * @param string $path top directory for the search for manifest files
 * @return array zero or more arrays comprising manifests
 */
function get_manifests($path) {
    $manifests = array();
    if (is_dir($path)) {
        if (($dp = opendir($path)) !== FALSE) {
            while (($item = readdir($dp)) !== FALSE) {
                $item_path = $path.'/'.$item;
                $item_manifest = $item_path.'/'.$item.'_manifest.php';
                if ((substr($item,0,1) != '.') && (is_dir($item_path)) && (is_file($item_manifest))) {
                    @include($item_manifest);
                    $manifests[$item]['manifest'] = $item.'_manifest.php';
                }
            }
            closedir($dp);
        }
    }
    return $manifests;
} // get_manifests()






// ==================================================================
// =========================== WORKHORSES ===========================
// ==================================================================

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


/** perform actual update to version 2010122100
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


/** perform actual update to version 2011020100
 *
 * @param object &$output collects the html output
 * @return bool TRUE on success, FALSE otherwise
 */
function update_core_2011020100(&$output) {
    global $CFG;
    if ($CFG->version >= 2011020100) {
        return TRUE;
    }
    // ...
    // necessary updates go here (e.g. modify table def, etc. etc.)
    // ..
    // If all is well, we update the version number in the database AND in $CFG->version
    return update_core_version($output,2011020100);
} // update_core_2011020100()

?>