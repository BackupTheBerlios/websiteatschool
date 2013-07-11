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
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: updatelib.php,v 1.26 2013/07/11 10:40:31 pfokker Exp $
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
 * program and modules, themes, etc. It is called automagically whenever
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
            $s = (utf8_strlen($task) <= 50) ? $task : utf8_substr($task,0,44).' (...)';
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

    // 1 -- show core status in a 6-column HTML-table
    update_status_table_open($output);
    $class = 'odd';
    $attributes = array('class' => $class);
    $output->add_content('  '.html_table_row($attributes));
    $output->add_content('    '.html_table_cell($attributes,t('update_core','admin')));
    $output->add_content('    '.html_table_cell($attributes,$CFG->version));
    $output->add_content('    '.html_table_cell($attributes,htmlspecialchars(WAS_VERSION)));
    $output->add_content('    '.html_table_cell($attributes,htmlspecialchars(WAS_RELEASE_DATE)));
    $output->add_content('    '.html_table_cell($attributes,htmlspecialchars(WAS_RELEASE)));
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
            'fields'   => array('language_key','version','manifest'),
            'keyfield' => 'language_key',
            'path'     => $CFG->progdir.'/languages',
            'install'  => TASK_INSTALL_LANGUAGE,
            'update'   => TASK_UPDATE_LANGUAGE
            ),
        'modules' => array(
            'table'    => 'modules',
            'fields'   => array('name','version','manifest'),
            'keyfield' => 'name',
            'path'     => $CFG->progdir.'/modules',
            'install'  => TASK_INSTALL_MODULE,
            'update'   => TASK_UPDATE_MODULE
            ),
        'themes' => array(
            'table'    => 'themes',
            'fields'   => array('name','version','manifest'),
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

        // 2C -- open a 6-column HTML-table for status overview
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
            $release_date_manifest = (isset($manifest['release_date'])) ? $manifest['release_date'] : '';
            $release_manifest = (isset($manifest['release'])) ? $manifest['release'] : '';
            $output->add_content('  '.html_table_row($attributes));
            $output->add_content('    '.html_table_cell($attributes,htmlspecialchars($key)));
            $output->add_content('    '.html_table_cell($attributes,htmlspecialchars($version_database)));
            $output->add_content('    '.html_table_cell($attributes,htmlspecialchars($version_manifest)));
            $output->add_content('    '.html_table_cell($attributes,htmlspecialchars($release_date_manifest)));
            $output->add_content('    '.html_table_cell($attributes,htmlspecialchars($release_manifest)));
            $output->add_content('    '.html_table_cell($attributes,$status));
            $output->add_content('  '.html_table_row_close());
        }

        // 2E -- now check for orphans (database records without matching manifest)
        foreach($records as $key => $record) {
            if (isset($manifests[$key])) { // already dealt with in the foreach loop over all manifests
                continue;
            }
            // Realisticly speaking there are two possibilities here:
            //  1. a new language was added locally but no 'official' language pack was installed, or
            //  2. a language was once installed but the manifest is lost in the mist of time (very unlikely)
            // The former case is perfectly possible, the latter is a real error.
            // Note, however, that case 1 is very unlikely for modules and themes: the user cannot simply
            // add a record to the modules or themes table like she can via 'Add a language' in the Translate Tool.
            //
            // The trigger for the error (case 2) is: a manifest name is mentioned in the $record but
            // we haven't seen that one before (in the foreach loop over all manifests).
            // This error condition yields question marks for the external version and release/release date
            // and 'ERROR' for status. Case 1 above yields dashes instead, with a status of OK.
            //
            $version_database = (isset($record['version'])) ? $record['version'] : '0';
            if ((isset($record['manifest'])) && (!empty($record['manifest']))) { // Case 2 -- ERROR
                $version_release_date = '?';
                $status = t('update_status_error','admin');
                logger(sprintf('%s(): weird: \'%s/%s\' database version (%s) exists without corresponding manifest?',
                               __FUNCTION__,$subsystem,$key,strval($version_database)));
            } else { // Case 1 - locally added language
                $version_release_date = '-';
                $status = t('update_status_ok','admin');
            }
            $class = ($class == 'odd') ? 'even' : 'odd';
            $attributes = array('class' => $class);
            $output->add_content('  '.html_table_row($attributes));
            $output->add_content('    '.html_table_cell($attributes,htmlspecialchars($key)));
            $output->add_content('    '.html_table_cell($attributes,htmlspecialchars($version_database)));
            $output->add_content('    '.html_table_cell($attributes,$version_release_date));
            $output->add_content('    '.html_table_cell($attributes,$version_release_date));
            $output->add_content('    '.html_table_cell($attributes,$version_release_date));
            $output->add_content('    '.html_table_cell($attributes,$status));
            $output->add_content('  '.html_table_row_close());
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
 * @uses $CFG
 * @todo we should refactor and combine install_theme() and install_module()
 */
function install_module(&$output,$module_key) {
    global $CFG;
    $retval = TRUE; // assume success
    $module_key = strval($module_key);
    $progdir_modules = $CFG->progdir.'/modules';
    $manifests = get_manifests($progdir_modules);
    $params = array('{MODULE}' => $module_key);

    // 0 -- sanity check
    if (!isset($manifests[$module_key])) {
        logger(sprintf('%s(): manifest for module \'%s\' not found; nothing installed',__FUNCTION__,$module_key));
        $retval = FALSE;
    } else {
        // 1 -- preliminary creation of a new module record (we need the pkey in the installer)
        $manifest = $manifests[$module_key];
        $table = 'modules';
        $fields = array(
            'name'          => $module_key,
            'version'       => 0,
            'manifest'      => $manifest['manifest'],
            'is_core'       => ((isset($manifest['is_core'])) && ($manifest['is_core'])) ? TRUE : FALSE,
            'is_active'     => FALSE,
            'has_acls'      => ((isset($manifest['has_acls'])) && ($manifest['has_acls'])) ? TRUE : FALSE,
            'view_script'   => (isset($manifest['view_script'])) ? $manifest['view_script'] : NULL,
            'admin_script'  => (isset($manifest['admin_script'])) ? $manifest['admin_script'] : NULL,
            'search_script' => (isset($manifest['search_script'])) ? $manifest['search_script'] : NULL,
            'cron_script'   => (isset($manifest['cron_script'])) ? $manifest['cron_script'] : NULL,
            'cron_interval' => (isset($manifest['cron_interval'])) ? intval($manifest['cron_interval']):NULL,
            'cron_next' => NULL
            );
        $key_field = 'module_id';
        if (($module_id = db_insert_into_and_get_id($table,$fields,$key_field)) === FALSE) {
            logger(sprintf('%s(): cannot install module \'%s\': %s',__FUNCTION__,$module_key,db_errormessage()));
            $retval = FALSE;
        } else {
            // 2A -- maybe insert tables for module
            if ((isset($manifest['tabledefs'])) && (!empty($manifest['tabledefs']))) {
                $filename = sprintf('%s/%s/%s',$progdir_modules,$module_key,$manifest['tabledefs']);
                if (file_exists($filename)) {
                    if (!update_create_tables($filename)) {
                        $retval = FALSE;
                    }
                }
            }
            // 2B -- call the installation routine
            $messages = array(); // collects error messages from call-back routine
            if ((isset($manifest['install_script'])) && (!empty($manifest['install_script']))) {
                $filename = sprintf('%s/%s/%s',$progdir_modules,$module_key,$manifest['install_script']);
                if (file_exists($filename)) {
                    @include_once($filename);
                    $module_install = $module_key.'_install';
                    if (function_exists($module_install)) {
                        if ($module_install($messages,$module_id)) {
                            logger(sprintf('%s(): %s(): success installing module \'%s\' (version is %d)',
                                           __FUNCTION__,$module_install,$module_key,$manifest['version']));
                        } else {
                            $retval = FALSE;
                            logger(sprintf('%s(): %s() returned an error',__FUNCTION__,$module_install));
                        }
                        foreach($messages as $message) { // remember messages, either good or bad
                            logger($message);
                        }
                    } else {
                        $retval = FALSE;
                        logger(sprintf('%s(): function %s() does not exist?',__FUNCTION__,$module_install));
                    }
                } else {
                    $retval = FALSE;
                    logger(sprintf('%s(): file %s does not exist?',__FUNCTION__,$filename));
                }
            } else {
                $retval = FALSE;
                logger(sprintf('%s(): no install script in manifest for module \'%s\'',__FUNCTION__,$module_key));
            }
            // 2C -- if all went well, we make the module active
            if ($retval) {
                $where = array($key_field => $module_id);
                $fields = array(
                    'is_active' => TRUE, 
                    'version'   => intval($manifest['version'])
                    );
                if (db_update($table,$fields,$where) === FALSE) {
                    logger(sprintf('%s(): cannot activate module \'%s\': %s',__FUNCTION__,$module_key,db_errormessage()));
                    $retval = FALSE;
                }
            }
        }
    }
    if ($retval) {
        logger(sprintf('%s(): success installing module \'%s\'',__FUNCTION__,$module_key));
        $output->add_message(t('update_subsystem_module_success','admin',$params));
    } else {
        $output->add_message(t('update_subsystem_module_error','admin',$params));
    }
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
 * @uses $CFG
 * @todo we should refactor and combine install_theme() and install_module()
 */
function install_theme(&$output,$theme_key) {
    global $CFG;
    $retval = TRUE; // assume success
    $theme_key = strval($theme_key);
    $progdir_themes = $CFG->progdir.'/themes';
    $manifests = get_manifests($progdir_themes);
    $params = array('{THEME}' => $theme_key);

    // 0 -- sanity check
    if (!isset($manifests[$theme_key])) {
        logger(sprintf('%s(): manifest for theme \'%s\' not found; nothing installed',__FUNCTION__,$theme_key));
        $retval = FALSE;
    } else {
        // 1 -- preliminary creation of a new theme record (we need the pkey in the installer)
        $manifest = $manifests[$theme_key];
        $table = 'themes';
        $fields = array(
            'name'       => $theme_key,
            'version'    => 0,
            'manifest'   => $manifest['manifest'],
            'is_core'    => ((isset($manifest['is_core'])) && ($manifest['is_core'])) ? TRUE : FALSE,
            'is_active'  => FALSE,
            'class'      => (isset($manifest['class'])) ? $manifest['class'] : NULL,
            'class_file' => (isset($manifest['class_file'])) ? $manifest['class_file'] : NULL
            );
        $key_field = 'theme_id';
        if (($theme_id = db_insert_into_and_get_id($table,$fields,$key_field)) === FALSE) {
            logger(sprintf('%s(): cannot install theme \'%s\': %s',__FUNCTION__,$theme_key,db_errormessage()));
            $retval = FALSE;
        } else {
            // 2A -- maybe insert tables for theme
            if ((isset($manifest['tabledefs'])) && (!empty($manifest['tabledefs']))) {
                $filename = sprintf('%s/%s/%s',$progdir_themes,$theme_key,$manifest['tabledefs']);
                if (file_exists($filename)) {
                    if (!update_create_tables($filename)) {
                        $retval = FALSE;
                    }
                }
            }
            // 2B -- call the installation routine
            $messages = array(); // collects error messages from call-back routine
            if ((isset($manifest['install_script'])) && (!empty($manifest['install_script']))) {
                $filename = sprintf('%s/%s/%s',$progdir_themes,$theme_key,$manifest['install_script']);
                if (file_exists($filename)) {
                    @include_once($filename);
                    $theme_install = $theme_key.'_install';
                    if (function_exists($theme_install)) {
                        if ($theme_install($messages,$theme_id)) {
                            logger(sprintf('%s(): %s(): success installing theme \'%s\' (version is %d)',
                                           __FUNCTION__,$theme_install,$theme_key,$manifest['version']));
                        } else {
                            $retval = FALSE;
                            logger(sprintf('%s(): %s() returned an error',__FUNCTION__,$theme_install));
                        }
                        foreach($messages as $message) { // remember messages, either good or bad
                            logger($message);
                        }
                    } else {
                        $retval = FALSE;
                        logger(sprintf('%s(): function %s() does not exist?',__FUNCTION__,$theme_install));
                    }
                } else {
                    $retval = FALSE;
                    logger(sprintf('%s(): file %s does not exist?',__FUNCTION__,$filename));
                }
            } else {
                $retval = FALSE;
                logger(sprintf('%s(): no install script in manifest for theme \'%s\'',__FUNCTION__,$theme_key));
            }
            // 2C -- if all went well, we make the theme active
            if ($retval) {
                $where = array($key_field => $theme_id);
                $fields = array(
                    'is_active' => TRUE, 
                    'version'   => intval($manifest['version'])
                    );
                if (db_update($table,$fields,$where) === FALSE) {
                    logger(sprintf('%s(): cannot activate theme \'%s\': %s',__FUNCTION__,$theme_key,db_errormessage()));
                    $retval = FALSE;
                }
            }
        }
    }
    if ($retval) {
        logger(sprintf('%s(): success installing theme \'%s\'',__FUNCTION__,$theme_key));
        $output->add_message(t('update_subsystem_theme_success','admin',$params));
    } else {
        $output->add_message(t('update_subsystem_theme_error','admin',$params));
    }
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
    if (!update_core_2011051100($output)) { return; }
    if (!update_core_2011093000($output)) { return; }
    if (!update_core_2012041900($output)) { return; }
    if (!update_core_2013071100($output)) { return; }
    // if (!update_core_2012mmdd00($output)) { return; }
    // ...

    // finally: check for obsolete files (list is hardcoded in update_remove_obsolete_files())
    update_remove_obsolete_files($output);
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
        logger(sprintf('%s(): core upgraded to version %s',__FUNCTION__,strval($CFG->version)));
    }
    return $retval;
} // update_core_version()


/** open a status overview HTML-table including column headers
 *
 * this routine opens an HTML-table in prepration for a status
 * overview of the system or a subsystem (languages, modules, themes).
 * The optional title is used as the header of the first column.
 *
 * The width of the first column is 25% and the remaining 5 columns
 * area 15% each which creates an orderly display of name, internal
 * version, external version, releasedate, release and status.
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
    $attributes['width'] = '25%';
    $output->add_content('    '.html_table_head($attributes,$title));
    $attributes['width'] = '15%';
    $output->add_content('    '.html_table_head($attributes,t('update_version_database','admin')));
    $output->add_content('    '.html_table_head($attributes,t('update_version_manifest','admin')));
    $output->add_content('    '.html_table_head($attributes,t('update_release_date_manifest','admin')));
    $output->add_content('    '.html_table_head($attributes,t('update_release_manifest','admin')));
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


/** create tables in database via include()'ing a file with tabledefs
 *
 * @param string $filename contains the table definitions
 * @return bool TRUE on success, FALSE otherwise + messages written to logger
 */
function update_create_tables($filename) {
    $retval = TRUE; // assume success
    if (!file_exists($filename)) {
        logger(sprintf('%s(): cannot include tabledefs: file \'%s\' not found',__FUNCTION__,$filename));
        $retval = FALSE;
    } else {
        $tabledefs = array();
        include($filename);
        foreach($tabledefs as $tabledef) {
            if (!update_create_table($tabledef)) {
                $retval = FALSE;
            }
        }
    }
    return $retval;
} // update_create_tables()


/** create table in database from an individual tabledef
 *
 * @param array $tabledef contains the definition of a single table
 * @return bool TRUE on success, FALSE otherwise + message written to logger
 * @uses $DB
 */
function update_create_table($tabledef) {
    global $DB;
    $retval = TRUE; // assume success
    if ($DB->create_table($tabledef) === FALSE) { // oops, but we continue anyway
        logger(sprintf('%s(): cannot create \'%s\': %s',__FUNCTION__,$tabledef['name'],db_errormessage()));
        $retval = FALSE;
    } else {
        logger(sprintf('%s(): success creating table \'%s\'',__FUNCTION__,$tabledef['name']),WLOG_DEBUG);
    }
    return $retval;
} // update_create_table()


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


/** perform actual update to version 2011051100
 *
 * this is a substantial change in the database: we (finally) standardise on UTF-8
 * including the database. Up until now we still only have a choice of exactly one
 * database driver: MySQL. Therefore the upgrade we do here can be more or less
 * MySQL-specific. (So much for database-independency).
 *
 * What needs to be done here?
 *
 * The most important task (in fact: the only task) is to change the collation (and
 * implicitly the default charset) to utf8_unicode_ci (4.1.x <= MySQL < 5.5.2) or
 * utf8mb4_unicode_ci (MySQL 5.5.3+). See {@link mysql.class.php} for more information
 * on these UTF-8 & MySQL issues.
 *
 * Strategy here is as follows.
 * <code>
 * for all 'our' tables (ie. "LIKE '{$prefix}%'" do
 *    if table default charset is already utf8 (or utf8mb4)
 *        continue;
 *    for appropriate columns in this table
 *        change column type to binary
 *        change column type back to non-binary with correct charset and collation
 *    if no trouble sofar
 *        change default charset/collation of the table too
 *    else
 *        return failure
 * return success
 * </code>
 *
 * This way we _might_ be able to work our way through huge tables: if the PHP
 * max processing time kicks in, we can rerun the upgrade and start (again) with
 * the table we had in our hands the previous time. I don't expect this to happen,
 * but it still the way to do it IMHO.
 *
 * Note that I assume that I cannot change the default charset of the DATABASE
 * for the same reason the Installation Wizard expects the database to be ready
 * before installation commences. (I cannot be sure that I have the privilege to
 * execute 'ALTER DATABASE $db_name DEFAULT CHARSET utf8 COLLATE utf8_unicode_ci').
 *
 * A useful reference for solving this problem of converting to utf8 can be found
 * here: http://codex.wordpress.org/Converting_Database_Character_Sets.
 *
 * In the case of W@S we do not have to deal with enum-fields because those are not used
 * at this time. In fact it boils down to changing char, varchar, text and longtext.
 *
 * Let goforit...
 *
 * @param object &$output collects the html output
 * @return bool TRUE on success, FALSE otherwise
 */
function update_core_2011051100(&$output) {
    global $CFG,$DB;

    // 0 -- get outta here when already upgraded
    $version = 2011051100;
    if ($CFG->version >= $version) {
        return TRUE;
    }

    // 1 -- can we do anything at all with upgrading to UTF-8 and all?
    $utf8_level = (isset($DB->utf8_support)) ? $DB->utf8_support : FALSE;

    if ($utf8_level === FALSE) {
        $msg = sprintf('%s(): cannot determine UTF-8 support in MySQL: utf8_level is not 0, 3 or 4',__FUNCTION__);
        logger($msg);
        $output->add_message(htmlspecialchars($msg));
        $output->add_message(t('update_core_error','admin',array('{VERSION}' => strval($version))));
        return FALSE;
    } elseif ($utf8_level == 0) {
        $db_version = mysql_get_server_info();
        logger(sprintf('%s(): MySQL \'%s\' too old for UTF-8 update',__FUNCTION__,$db_version));
        $params = array('{VERSION}' => $db_version);
        $output->add_message(t('warning_mysql_obsolete','i_install',$params));
        return update_core_version($output,$version);
    }

    // 2A -- prepare to step through all tables and all columns changing collation (and implicit charset)
    $charset = ($utf8_level == 3) ? 'utf8' : 'utf8mb4';
    $collation = $charset.'_unicode_ci';
    $pattern = str_replace(array('_','%'),array('\_','\%'),$DB->escape($DB->prefix)).'%'; // e.g. 'was\_%'
    $sql = sprintf("SHOW TABLE STATUS LIKE '%s'",$pattern);
    if (($DBResult = $DB->query($sql)) === FALSE) {
        $msg = sprintf('%s(): cannot show tables: %d/%s',__FUNCTION__,$DB->errno,$DB->error);
        logger($msg);
        $output->add_message(htmlspecialchars($msg));
        $output->add_message(t('update_core_error','admin',array('{VERSION}' => strval($version))));
        return FALSE;
    }
    $tables = $DBResult->fetch_all_assoc('Name');
    $DBResult->close();
    $overtime = max(intval(ini_get('max_execution_time')),30); // request this additional processing time after a table

    // 2B -- visit all tables
    foreach($tables as $table) {
        if ((isset($table['Collation'])) && (utf8_strcasecmp($table['Collation'],$collation) == 0)) {
            logger(sprintf('%s(): table \'%s\' is already \'%s\'',__FUNCTION__,$table['Name'],$collation),WLOG_DEBUG);
            continue; // somehow someone already changed this table; carry on with the next one
        }
        // 3A -- prepare to step through all columns of this table
        $sql = sprintf('SHOW FULL COLUMNS FROM `%s`',$table['Name']);
        if (($DBResult = $DB->query($sql)) === FALSE) {
            $msg = sprintf('%s(): cannot show columns from %s: %d/%s',__FUNCTION__,$table['Name'],$DB->errno,$DB->error);
            logger($msg);
            $output->add_message(htmlspecialchars($msg));
            $output->add_message(t('update_core_error','admin',array('{VERSION}' => strval($version))));
            return FALSE;
        }
        $columns = $DBResult->fetch_all_assoc('Field');

        // 3B -- visit all columns in this table
        foreach($columns as $column) {
            if (isset($column['Collation'])) {
                if (is_null($column['Collation'])) {
                    continue; // nothing to do; no collation to change
                } elseif (utf8_strcasecmp($column['Collation'],$collation) == 0) {
                    logger(sprintf('%s(): column \'%s.%s\' is already converted to \'%s\'',
                                    __FUNCTION__,$table['Name'],$column['Field'],$collation),WLOG_DEBUG);
                    continue; // somehow someone already changed this column; carry on with the next one
                }
            } else {
                continue; // this field has no collation whatsoever. Weird but we'll let it pass.
            }
            $sql1 = sprintf('ALTER TABLE `%s` CHANGE `%s` `%s` ',$table['Name'],$column['Field'],$column['Field']);
            $sql2 = $sql1;
            $vtype = explode("(",$column['Type']); // split 'varchar(n)' into components
            switch(strtolower($vtype[0])) {
            case 'varchar':
                $len = intval($vtype[1]);
                $sql1 .= sprintf('VARBINARY(%d)',$len);
                $sql2 .= sprintf('VARCHAR(%d)',$len);
                break;
            case 'char':
                $len = intval($vtype[1]);
                $sql1 .= sprintf('BINARY(%d)',$len);
                $sql2 .= sprintf('CHAR(%d)', $len);
                break;
            case 'text':
                $sql1 .= 'BLOB';
                $sql2 .= 'TEXT';
                break;
            case 'longtext':
                $sql1 .= 'LONGBLOB';
                $sql2 .= 'LONGTEXT';
                break;
            default:
                logger(sprintf('%s(): cannot handle \'%s.%s\' type \'%s\'; skipping',
                                __FUNCTION__,$table['Name'],$column['Field'],$vtype[0]),WLOG_DEBUG);
                continue;
                break;
            }
            $sql2 .= sprintf(' CHARACTER SET %s COLLATE %s',$charset,$collation);
            $sql2 .= ($column['Null'] == 'YES') ? ' NULL' : ' NOT NULL';
            $sql2 .= (is_null($column['Default'])) ? '' : sprintf(' DEFAULT \'%s\'',$DB->escape($column['Default']));
            $sql2 .= (empty($column['Comment'])) ? '' : sprintf(' COMMENT \'%s\'',$DB->escape($column['Comment']));

            // Do back-and-forth in one go so we don't write to 'log_messages' while that table is being converted
            // We report any errors/do logging after we're back on track again
            $retval1 = $DB->exec($sql1); $errno1 = $DB->errno; $error1 = $DB->error; // text (latin1) -> binary
            $retval2 = $DB->exec($sql2); $errno2 = $DB->errno; $error2 = $DB->error; // binary -> text (utf8)
            if (($retval1 === FALSE) || ($retval2 === FALSE)) { // oops, failure! gotta get out of this place!
                $msg = sprintf('%s(): cannot change \'%s.%s\': \'%s\': \'%d/%s\', \'%s\': \'%d/%s\'; bailing out',
                               __FUNCTION__,$table['Name'],$column['Field'],$sql1,$errno1,$error1,$sql2,$errno2,$error2);
                logger($msg);
                $output->add_message(htmlspecialchars($msg));
                $output->add_message(t('update_core_error','admin',array('{VERSION}' => strval($version))));
                return FALSE;
            } else {
                logger(sprintf('%s(): alter column \'%s.%s\': changed collation from \'%s\' to  \'%s\'',
                               __FUNCTION__,$table['Name'],$column['Field'],$column['Collation'],$collation),WLOG_DEBUG);
            }

        }
        // Only if all columns went OK, we change the table; this is a quick and dirty sentinel to work
        // through a long converstion (at worst we do 1 table per round) Eventually the conversion will be complete...
        $sql = sprintf('ALTER TABLE `%s` DEFAULT CHARSET %s COLLATE %s',$table['Name'],$charset,$collation);
        if ($DB->exec($sql) === FALSE) {
            $msg = sprintf('%s(): cannot alter \'%s\' with \'%s\': %d/%s; bailing out',
                           __FUNCTION__,$table['Name'],$sql,$DB->errno,$DB->error);
            logger($msg);
            $output->add_message(htmlspecialchars($msg));
            $output->add_message(t('update_core_error','admin',array('{VERSION}' => strval($version))));
            return FALSE;
        } else {
            logger(sprintf('%s(): alter table \'%s\': changed collation from \'%s\' to  \'%s\'',
                           __FUNCTION__,$table['Name'],$table['Collation'],$collation),WLOG_DEBUG);
        }
        @set_time_limit($overtime); // try to get additional processing time after every processed table
    }
    return update_core_version($output,$version);
} // update_core_2011051100()


/** perform actual update to version 2011093000
 *
 * this is yet another substantial change in the database: after we (finally)
 * standardised on UTF-8 the last time (see {@link update_core_2011051100()}
 * a number of problems occurred with new installations.
 *
 * This specifically occurs with MySQL (currently the only supported database).
 * In all their wisdom Oracle decided to change the default database engine from
 * MyISAM to InnoDB in MySQL version 5.5.5. Bad move to do that somewhere in a
 * sub-sub-release. Anyway. New installations with the default InnoDB engine
 * AND with the 4-byte utf8mb4 character set  (available since sub-sub-release 5.5.3) 
 * now generate serious trouble, because
 *
 *  - there is a hard-coded limit of 767 bytes for a key (index) in InnoDB, and
 *  - every utf8mb4 character counts as four bytes never mind the actual content.
 *
 * Note: the limit of 767 bytes stems from a utf8 (or utf8mb3 as it is now called)
 * string of max. 255 characters and 1 16-bit string length. 255 * 3 + 2 = 767 bytes.
 * I wonder why UTF-8 wasn't implemented correctly (ie. with 1 to 4 bytes) to begin with and
 * the key limit increased to 4 * 255 + 2 = 1022 bytes. The limited UTF-8 support
 * (only the BMP) now poses substantial problems. Yet another reason to start
 * looking for an alternative database solution. BTW: the key limit in MyISAM
 * is 1000 bytes.
 *
 * These two conditions (InnoDB and utf8mb4) limit the length of a key (index) to
 * 767 bytes / 4 bytes-per-char = 191 utf8mb4 characters. As it happens, some
 * tables in WebsiteAtSchool used keyfields like varchar(240) and even varchar(255).
 * These key sizes fail in InnoDB/utf8mb4 and the latter even fails with
 * MyISAM/utf8mb4 because 255 * 4 + 2 = 1022 bytes > 1000 bytes. What a mess...
 *
 * So there you have it: all keys MUST be shortened to 191 characters max. in order
 * to prevent stupid error messages about key too long. The alternative (forcing
 * another character set such as 'ascii' or 'latin1' for some fields) doesn't cut
 * it IMHO.
 *
 * *sigh*
 *
 * We still have a choice of exactly one database driver: MySQL.
 * Therefore the upgrade we do here can be more or less
 * MySQL-specific (so much for database-independency), as it has to be,
 * because the syntax of ALTER TABLE is -- unsuprisingly -- MySQL-specific.
 *
 * The good news is that we are still in beta, so a major change in the data
 * definition is less painful than with hundreds of production servers...
 *
 * Another issue is the use of foreign keys. We used to have a FK in the
 * nodes tabledef along the lines of this construct:
 * FOREIGN KEY parentnode (parent_id) REFERENCES nodes (node_id);
 * Upto now this could not possibly have worked with InnoDB because
 * adding a node would at the top level of an area would not satisfy this
 * constraint. Since MyISAM silently ignores any foreign key definition
 * it 'simply works' in that case. So, because this FK must be removed from
 * earlier installations we need to DROP the FOREIGN KEY. However, since
 * the whole program never installed using InnoDB, there is no need to drop
 * this foreign key that wasn't even recorded (in a MyISAM database) in the
 * first place. The same applies to a number of other FK's too: these are
 * now removed from the various tabledefs but do no need to be DROPped in
 * this update routine.
 *
 * What needs to be done here?
 *
 * For existing tables some fields must be shortened from varchar(255) or
 * varchar(240) to something like varchar(191) or even less. This MUST be
 * done for key (index) fields. However, while we are at it some more fields
 * SHOULD (or COULD) be shortened too. Here is what we do.
 *
 * <code>
 * for all affected table.fields do
 *    if a record exists with current data length > proposed new length then
 *        tell the user about it
 *    endif
 * next
 * if there were data length errors then
 *     tell the user about manually fixing it
 *     bail out with result FALSE (= not upgraded)
 * endif
 * for all affected table.fields do
 *     change field definition to new length
 *     if errors
 *         tell the user about it (but carry on)
 *     endif
 * next
 * return results (TRUE on success, FALSE on 1 or more errors)
 * </code>
 *
 * Below is a discussion of all affected fields and the rationale for
 * picking the new lengths less than 191 characters.
 *
 * <code>
 * config.name: varchar(240) => varchar(80)
 * modules_properties.name: varchar(240) => varchar(80)
 * themes_properties.name: varchar(240) => varchar(80)
 * themes_areas_properties.name: varchar(240) => varchar(80)
 * users_properties.name: varchar(240) => varchar(80)
 * users_properties.section: varchar(240) => varchar(80)
 * </code>
 * 
 * Currently the longest parameter name in use is 27 characters, so I
 * have to admit that the arbitrary size of 240 is a little bit too much.
 * I'll reduce these fields to a size of 80, which seems a little more
 * realistic. As an additional bonus, this allows for a compound key
 * using 'section' and 'name' in users_properties while staying within
 * the limit of 767 bytes or 191 characters.
 *
 * <code>
 * areas.path: varchar(240) => varchar(60)
 * groups.path: varchar(240) => varchar(60)
 * users.path: varchar(240) => varchar(60)
 * </code>
 *
 * and
 *
 * <code>
 * groups.groupname: varchar(255) => varchar(60)
 * users.username: varchar(255) => varchar(60)
 * </code>
 * 
 * The length of username or groupname was arbitrary set to 255.
 * Different systems have different limits, e.g. 8, 14, 15, 16,
 * 20, 32, 64 or 128. Since W@S is a stand-alone system we are more
 * or less free to choose whatever we want (as long as it is less
 * than 191 of course).
 *
 * Since a username or groupname is only used to distinguish one user
 * from another but at the same time giving at least some readability,
 * a length of 255 is way too long. An arbitrary but hopefully more
 * realistic choice is 60 characters. 
 *
 * The path for a user or group is derived from the corresponding
 * name so it makes sense to make both fields the same length.
 *
 * <code>
 * log_messages.remote_addr: varchar(255) => varchar(150)
 * login_failures.remote_addr: varchar(255) => varchar(150)
 * </code>
 * 
 * A remote address of type IPv4 generally looks like this: 'ddd.ddd.ddd.ddd' => length 15
 * It is not so easy to determine the length of an IPv6 address, because many valid variants exist.
 * 'xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx' => length 39
 * '0000:0000:0000:0000:0000:0000:ddd.ddd.ddd.ddd' => length 45
 * '[0000:0000:0000:0000:0000:0000:ddd.ddd.ddd.ddd'] => length 47 (RFC3989)
 * 
 * Adding to the complexity and confusion are link-local addresses with
 * zone indices: a percent-sign followed by an interface number
 * (e.g. '%1') or interface name (e.g. '%eth0') appended to the raw
 * address. This adds 2 or 5 or even more characters to the address.
 * And then we of course have the reverse DNS-variant like
 * 'x.x.x.x.x.x.x.x.x.x.x.x.x.x.x.x.x.x.x.x.x.x.x.x.x.x.x.x.x.x.x.x.ip6.arpa.' => length 73
 * or the special Microsoft trick to shoehorn a literal address in a UNC path:
 * 'xxxx-xxxx-xxxx-xxxx-xxxx-xxxx-xxxx-xxxx.ipv6-literal.net' => length 56 or
 * 'xxxx-xxxx-xxxx-xxxx-xxxx-xxxx-xxxx-xxxxs1.ipv6-literal.net' => length 58+ (with zone index)
 * 
 * Of course there several 'simplifications' such as omitting leading
 * zeros in the hexquads and replacing the longest sequece of 0-hexquads
 * with '::' that add to the confusion. RFC5952 adds the definition of a 'canonical
 * representation' of IPv6 addresses to the party. Mmmm, see http://xkcd.com/927
 * 
 * My conclusion is: this whole IPv6-idea suffers from the Second System Syndrome
 * (see F. Brooks' Mythical Man Month) and unfortunately we have to deal with it.
 * 
 * *sigh*
 * 
 * I will reduce the length of these fields from 255 to 150 for no other
 * reason than that it is 10 times the length of a dotted-decimal IPv4
 * address and sufficient to accomodate a reverse DNS address twice (2 x
 * 73 = 146).
 * 
 * <code>
 * sessions.session_key: varchar(255) => varchar(172)
 * </code>
 * 
 * This field stores a session key, currently constructed using md5()
 * which yields a string with 32 (lowercase) hexadecimal characters.  In
 * the future a different digest could be used to provice a session_key,
 * e.g. SHA-1 (40 hexdigits) or SHA-512 (128 hexdigits). Another option
 * would be to use a UUID: 128 bits represented in 32 hexdigits in the
 * form xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx (string of 36 bytes).
 * Alternatively, the SHA-512 could be encoded in base64 yielding a
 * string of 512 / 6 = 86 bytes. In this context, a field of size 255
 * seems a little over the top, not to mention problematic with 4-byte
 * UTF-8 characters combined with the infamous MySQL / InnoDB-limit of
 * 767 bytes for keyfields. I guess I will settle for a field size of
 * 172 characters which is not too much for InnoDB keys + utf8mb4 and
 * exactly enough to store a 1024 bit number in base64.
 *
 * @param object &$output collects the html output
 * @return bool TRUE on success, FALSE otherwise
 */
function update_core_2011093000(&$output) {
    global $CFG,$DB;

    // 0 -- get outta here when already upgraded
    $version = 2011093000;
    if ($CFG->version >= $version) {
        return TRUE;
    }

    // List of column definitions keyed by 'tablename:fieldname' copied from (new) tabledefs
    $alterdefs = array(
        'areas:path' => array(
            'name' => 'path',
            'type' => 'varchar',
            'length' => 60,
            'notnull' => TRUE,
            'comment' => 'the place to store user uploaded files etc., relative to CFG->datadir/areas'
            ),
        'config:name' => array(
            'name' => 'name', 
            'type' => 'varchar', 
            'length' => 80,
            'notnull' => TRUE,
            'comment' => 'the name of the global configuration parameter'
            ),
        'groups:groupname' => array(
            'name' => 'groupname',
            'type' => 'varchar',
            'length' => 60,
            'notnull' => TRUE,
            'comment' => 'the short groupname, must be unique too'
            ),
        'groups:path' => array(
            'name' => 'path',
            'type' => 'varchar',
            'length' => 60,
            'notnull' => TRUE,
            'comment' => 'the place (subdirectory) to store files for this group, relative to CFG->datadir/groups'
            ),
        'log_messages:remote_addr' => array(
            'name' => 'remote_addr',
            'type' => 'varchar',
            'length' => 150,
            'notnull' => TRUE,
            'comment' => 'IP-address of the visitor'
            ),
        'login_failures:remote_addr' => array(
            'name' => 'remote_addr',
            'type' => 'varchar',
            'length' => 150,
            'notnull' => TRUE,
            'comment' => 'IP-address of the visitor that failed the login attempt/is blocked'
            ),
        'modules_properties:name' => array(
            'name' => 'name', 
            'type' => 'varchar', 
            'length' => 80, 
            'notnull' => TRUE,
            'comment' => 'the name of the configuration parameter'
            ),
        'sessions:session_key' =>array(
            'name' => 'session_key',
            'type' => 'varchar',
            'length' => 172,
            'default' => '',
            'comment' => 'contains the unique identifier (\'token\') which is stored in the user\'s cookie'
            ),
        'themes_areas_properties:name' => array(
            'name' => 'name', 
            'type' => 'varchar', 
            'length' => 80,
            'notnull' => TRUE,
            'comment' => 'the name of the configuration parameter'
            ),
        'themes_properties:name' => array(
            'name' => 'name', 
            'type' => 'varchar', 
            'length' => 80, 
            'notnull' => TRUE,
            'comment' => 'the name of the configuration parameter'
            ),
        'users:path' => array(
            'name' => 'path',
            'type' => 'varchar',
            'length' => 60,
            'notnull' => TRUE,
            'comment' => 'the place (subdirectory) to store files for this user, relative to CFG->datadir/users'
            ),
        'users:username' => array(
            'name' => 'username',
            'type' => 'varchar',
            'length' => 60,
            'notnull' => TRUE,
            'comment' => 'the account name, must be unique too'
            ),
        'users_properties:name' => array(
            'name' => 'name', 
            'type' => 'varchar', 
            'length' => 80, 
            'notnull' => TRUE,
            'comment' => 'the name of the configuration parameter'
            ),
        'users_properties:section' => array(
            'name' => 'section',
            'type' => 'varchar',
            'length' => 80,
            'notnull' => TRUE,
            'comment' => 'keeps related properties grouped together, e.g. in a separate tab'
            ),
        'nodes:module_id' => array(
            'name' => 'module_id',
            'type' => 'int',
            'notnull' => FALSE,
            'default' => NULL,
            'comment' => 'this connects to the module generating actual node content; NULL for sections'
            )
        );

    //
    // 1 -- check existing data for strings that are too long (only the varchar fields)
    //
    $errors = 0;
    foreach($alterdefs as $table_field => $fielddef) {
        if ($fielddef['type'] != 'varchar') {
            continue;
        }
        list($table,$field) = explode(':',$table_field);
        $length = $fielddef['length'];
        $where = sprintf('CHAR_LENGTH(%s) > %d',$field,$length);
        if (($records = db_select_all_records($table,$field,$where)) === FALSE) {
            $msg = sprintf('%s(): cannot retrieve data from table \'%s\' field \'%s\': %s',
                           __FUNCTION__,$table,$field,db_errormessage());
            logger($msg);
            $output->add_message(htmlspecialchars($msg));
            $output->add_message(t('update_core_error','admin',array('{VERSION}' => strval($version))));
            return FALSE;
        }
        if (sizeof($records) <= 0) {
            continue;
        }
        $params = array('{TABLE}' => $table,'{FIELD}' => $field,'{LENGTH}' => $length,'{CONTENT}' => '');
        foreach($records as $record) {
            ++$errors;
            logger(sprintf('%s(): content of table \'%s\' field \'%s\' longer than %d: \'%s\'',
                           __FUNCTION__,$table,$field,$length,$record[$field]));
            $params['{CONTENT}'] = $record[$field];
            $output->add_message(t('update_field_value_too_long','admin',$params));
        }
    }
    if ($errors > 0) {
        logger(sprintf('%s(): number of errors encoutered: %d; bailing out for manual correction',__FUNCTION__,$errors));
        $output->add_message(t('update_please_correct_field_value_manually','admin',array('{ERRORS}' => $errors)));
        $msg = t('update_core_error','admin',array('{VERSION}' => strval($version)));
        $output->add_message($msg);
        $output->add_popup_bottom($msg); // attract some more attention
        return FALSE;
    }

    //
    // 2 -- actually change the table definitions (both varchar and int)
    //
    $overtime = max(intval(ini_get('max_execution_time')),30); // additional processing time in seconds
    foreach($alterdefs as $table_field => $fielddef) {
        list($table,$field) = explode(':',$table_field);
        $sql = sprintf('ALTER TABLE `%s%s` CHANGE %s %s',$DB->prefix,$table,$field,$DB->column_definition($fielddef));
        if ($DB->exec($sql) === FALSE) {
            $msg = sprintf('%s(): cannot alter \'%s\' with \'%s\': %d/%s; bailing out',
                           __FUNCTION__,$table,$sql,$DB->errno,$DB->error);
            logger($msg);
            $output->add_message(htmlspecialchars($msg));
            $output->add_message(t('update_core_error','admin',array('{VERSION}' => strval($version))));
            return FALSE;
        } else {
            if ($fielddef['type'] == 'varchar') {
                $msg = sprintf('changed type to varchar(%d)',$fielddef['length']);
            } else {
                $msg = 'changed \'notnull\' and \'default\' properties'; // there is only nodes.modules_id here...
            }
            logger(sprintf('%s(): alter table \'%s\' field \'%s\': %s',__FUNCTION__,$table,$field,$msg),WLOG_DEBUG);
        }
        @set_time_limit($overtime); // try to get additional processing time after every processed table
    }

    //
    // 3 -- adjust existing data for nodes.module_id
    //
    $table = 'nodes';
    $fields = array('module_id' => NULL);
    $where = array('module_id' => 0);
    if (($retval = db_update($table,$fields,$where)) === FALSE) {
        $msg = sprintf('%s(): cannot update \'%s\': %d/%s; bailing out',__FUNCTION__,$table,$sql,$DB->errno,$DB->error);
        logger($msg);
        $output->add_message(htmlspecialchars($msg));
        $output->add_message(t('update_core_error','admin',array('{VERSION}' => strval($version))));
        return FALSE; 
    } else {
        logger(sprintf('%s(): update field \'nodes.module_id\': %d rows affected',__FUNCTION__,$retval),WLOG_DEBUG);
    }

    //
    // 4 -- add new config option and attempt to fix existing data suffering from sort_order bug in page manager
    //

    // 4A -- add a new sort option to the CFG
    $retval = TRUE; // assume success
    if (!isset($CFG->pagemanager_at_end)) {
        $table = 'config';
        $fields = array(
            'name' => 'pagemanager_at_end',
            'type' => 'b',
            'value' => '0',
            'sort_order' => 240,
            'extra' => '',
            'description' => 'sort order position within section for new nodes: TRUE is at the end  - USER-defined'
            );
        if (db_insert_into($table,$fields) === FALSE) {
            $msg = sprintf("%s(): cannot add config option 'pagemanager_at_end': %s",__FUNCTION__,db_errormessage());
            logger($msg);
            $output->add_message(htmlspecialchars($msg));
            $output->add_message(t('update_core_error','admin',array('{VERSION}' => strval($version))));
            return FALSE; 
        } else {
            logger(sprintf("%s(): success adding option 'pagemanager_at_end' to configuration table",__FUNCTION__));
        }
    } else {
        logger(sprintf("%s(): option 'pagemanager_at_end' already set in configuration table",__FUNCTION__));
    }

    // 4B -- attempt to update sort_orders in nodes that are obviously wrong
    $table = 'nodes';
    $fields = array('area_id','CASE WHEN node_id=parent_id THEN 0 ELSE parent_id END AS section','node_id','sort_order');
    $where = '';
    $order = array('area_id','CASE WHEN node_id = parent_id THEN 0 ELSE parent_id END','sort_order');
    $keyfield = 'node_id';
    if (($records = db_select_all_records($table,$fields,$where,$order,$keyfield)) === FALSE) {
        $msg = sprintf('%s(): cannot retrieve sort orders in nodes; skipping: %s',__FUNCTION__,db_errormessage());
        logger($msg);
        $output->add_message(htmlspecialchars($msg));
        $output->add_message(t('update_core_error','admin',array('{VERSION}' => strval($version))));
        return FALSE; 
    }
    $count = 0;
    $area_id = 0;
    $section = 0;
    foreach ($records as $node_id => $record) {
        if (($area_id != $record['area_id']) || ($section != $record['section'])) {
            $area_id    = $record['area_id'];
            $section    = $record['section'];
            $sort_order = $record['sort_order'] + 10;
        } else {
            if ($sort_order != $record['sort_order']) {
                $fields = array('sort_order' => intval($sort_order));
                $where = array('node_id' => intval($node_id));
                if (db_update($table,$fields,$where) === FALSE) {
                    $msg = sprintf("%s(): sort order error in node '%d': %s",__FUNCTION__,$node_id,db_errormessage());
                    logger($msg);
                    $output->add_message(htmlspecialchars($msg));
                    $output->add_message(t('update_core_error','admin',array('{VERSION}' => strval($version))));
                    return FALSE; 
                }
                logger(sprintf('%s(): success updating sort_order from %d => %d in area %d, section %d, node %d',
                                       __FUNCTION__,$record['sort_order'],$sort_order,$area_id,$section,$node_id));
                ++$count;
            }
            $sort_order += 10;
        }
    }
    logger(sprintf('%s(): success updating sort orders in nodes table; count = %d',__FUNCTION__,$count));


    //
    // 5 -- all done: bump version in database
    //
    return update_core_version($output,$version);
} // update_core_2011093000()


/** attempt to remove or at least flag obsolete files
 *
 * this routine can grow bigger on every update when perhaps more files are obsoleted.
 * We always check all files (even the older ones) because the user might not have removed
 * them yet. If we can delete the files, we do so. If not, we log it and also show a message
 * to the user via $output.
 *
 * @param object &$output collects output
 * @return bool TRUE on success, FALSE otherwise
 */
function update_remove_obsolete_files(&$output) {
    global $CFG;
    // This array holds the filenames and the version where the file was obsoleted
    $obsolete_files = array(
        '/lib/node.class.php'     => '0.90.3 / 2011093000',
        '/lib/modulelib.php'      => '0.90.3 / 2011093000',
        '/lib/area.class.php'     => '0.90.3 / 2011093000',
        '/lib/module.class.php'   => '0.90.3 / 2011093000',
        '/graphics/blank16.gif'             => '0.90.4 / 2012041900',
        '/styles/admin_high_visibility.css' => '0.90.4 / 2012041900'
        );

    $retval = TRUE; // assume success
    foreach($obsolete_files as $filename => $version) {
        $full_path = $CFG->progdir.$filename;
        $path = '/program'.$filename;
        if (!is_file($full_path)) {
            logger(sprintf("%s(): file '%s' (obsolete since %s) no longer exists, good!",__FUNCTION__,$path,$version));
        }elseif (@unlink($full_path)) {
            logger(sprintf("%s(): success unlinking file '%s' (obsolete since %s)",__FUNCTION__,$full_path,$version));
        } else {
            logger(sprintf("%s(): cannot unlink file '%s' (obsolete since %s)",__FUNCTION__,$full_path,$version));
            $params = array('{FILENAME}' => $path, '{VERSION}' => $version);
            $output->add_message(t('update_warning_obsolete_file','admin',$params));
            $retval = FALSE;
        }
    }
    return $retval;
} // update_remove_obsolete_files()


/** perform actual update to version 2012041900
 *
 * Changes between 2011093000 and 2012041900:
 *  - addition of the ckeditor-option in the site configuration table
 *
 * @param object &$output collects the html output
 * @return bool TRUE on success, FALSE otherwise
 */
function update_core_2012041900(&$output) {
    global $CFG,$DB;
    $version = 2012041900;
    if ($CFG->version >= $version) {
        return TRUE;
    }
    //
    // 1 -- maybe change default editor to CKEditor
    //
    $table = 'config';
    $fields = array('extra' => 'options=ckeditor,fckeditor,plain',
                    'description' => 'Default rich text editor - USER-defined, default ckeditor');
    $where = array('name' => 'editor');
    if ((!isset($CFG->editor)) || ($CFG->editor == 'fckeditor')) {
        $fields['value'] = 'ckeditor';
    }
    if (db_update($table,$fields,$where) === FALSE) {
        $msg = sprintf("%s(): cannot update editor configuration: %s",__FUNCTION__,db_errormessage());
        logger($msg);
        $output->add_message(htmlspecialchars($msg));
        $output->add_message(t('update_core_error','admin',array('{VERSION}' => strval($version))));
        return FALSE; 
    }
    //
    // 2 -- maybe add some additional fields to nodes, users
    //
    $addfielddefs = array(
        'nodes:style' => array(
            'name' => 'style', 
            'type' => 'text', 
            'notnull' => TRUE,
            'comment' => 'additional style information to add AFTER static and area-level style'
            ),
        'users:skin' =>  array(
            'name' => 'skin',
            'type' => 'varchar',
            'length' => 20,
            'notnull' => TRUE,
            'default' => 'base',
            'comment' => 'preferred skin'
            )
        );
    foreach($addfielddefs as $table_field => $fielddef) {
        list($table, $field) = explode(':',$table_field);
        if (($DBResult = $DB->query(db_select_sql($table,$field),1)) !== FALSE) {
            $DBResult->close();
            logger(sprintf("%s(): field '%s' already exists in '%s', skipping ALTER TABLE", __FUNCTION__, $field, $table));
        } else {
            $sql = sprintf('ALTER TABLE %s%s ADD COLUMN (%s)', $DB->prefix, $table, $DB->column_definition($fielddef));
            if (($retval = $DB->exec($sql)) === FALSE) {
                $msg = sprintf("%s(): cannot add field '%s' to table '%s': %s", __FUNCTION__, $field, $table, db_errormessage());
                logger($msg);
                $output->add_message(htmlspecialchars($msg));
                $output->add_message(t('update_core_error','admin',array('{VERSION}' => strval($version))));
                return FALSE; 
            } else {
                logger(sprintf("%s(): success adding field '%s' to table '%s'",__FUNCTION__, $field, $table));
            }
        }
    }
    //
    // 3 -- maybe get rid of old field 'high_visibility'
    //
    if (($DBResult = $DB->query(db_select_sql('users','high_visibility'),1)) === FALSE) {
            logger(sprintf("%s(): field 'high_visibility' no longer exists in 'users', skipping ALTER TABLE", __FUNCTION__));
    } else {
        $DBResult->close();
        $changes = array('base' => FALSE, 'textonly' => TRUE);
        foreach ($changes as $newval => $oldval) {
            if (($retval = db_update('users',array('skin' => $newval), array('high_visibility' => $oldval))) === FALSE) {
                $msg = sprintf("%s(): cannot update field 'users.skin' to '%s': %s", __FUNCTION__, $newval, db_errormessage());
                logger($msg);
                $output->add_message(htmlspecialchars($msg));
                $output->add_message(t('update_core_error','admin',array('{VERSION}' => strval($version))));
                return FALSE; 
            } else {
                logger(sprintf("%s(): success setting 'users.skin' to '%s' in %d record(s)",__FUNCTION__, $newval, $retval));
            }
        }
        $sql = sprintf('ALTER TABLE %susers DROP COLUMN high_visibility',$DB->prefix);
        if (($retval = $DB->exec($sql)) === FALSE) {
            $msg = sprintf("%s(): cannot drop field 'high_visibility' from table 'users': %s", __FUNCTION__, db_errormessage());
            logger($msg);
            $output->add_message(htmlspecialchars($msg));
            $output->add_message(t('update_core_error','admin',array('{VERSION}' => strval($version))));
            return FALSE; 
        } else {
            logger(sprintf("%s(): success dropping field 'high_visibility' from table 'users'",__FUNCTION__));
        }
    }
    //
    // 4 -- If all is well, we update the version number in the database AND in $CFG->version
    //
    return update_core_version($output,$version);
} // update_core_2012041900()


/** perform actual update to version 2013071100
 *
 * Changes between 2012041900 and 2013071100:
 *  - addition of a new core table 'tokens'
 *
 * @param object &$output collects the html output
 * @return bool TRUE on success, FALSE otherwise
 */
function update_core_2013071100(&$output) {
    global $CFG,$DB;
    $version = 2013071100;
    $retval = TRUE; // assume success
    if ($CFG->version >= $version) {
        return $retval;
    }
    $filename = $CFG->progdir.'/install/tabledefs.php';
    if (!file_exists($filename)) {
        logger(sprintf('%s(): cannot include tabledefs: file \'%s\' not found',__FUNCTION__,$filename));
        $retval = FALSE;
    } else {
        $tabledefs = array();
        include($filename);
        $table = 'tokens';
        if ($DB->table_exists($table)) {
            logger(sprintf("%s(): table '%s' already exists, skipping CREATE TABLE",__FUNCTION__, $table));
        } else {
            $retval = update_create_table($tabledefs[$table]);
        }
    }
    if ($retval) {
        $retval = update_core_version($output,$version);
    }
    return $retval;
} // update_core_2013071100()

?>