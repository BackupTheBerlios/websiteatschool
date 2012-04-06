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

/** /program/lib/filemanager.class.php - filemanager
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: filemanager.class.php,v 1.7 2012/04/06 18:47:26 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** utility routines for manipulating files */
require_once($CFG->progdir.'/lib/filelib.php');

/* This is the list of recognised tasks in File Manager */
define('TASK_LIST_DIRECTORY','ls');
define('TASK_CHANGE_DIRECTORY','cd');
define('TASK_PREVIEW_FILE','preview');
define('TASK_REMOVE_FILE','rm');
define('TASK_REMOVE_DIRECTORY','rmdir');
define('TASK_ADD_FILE','upload');
define('TASK_ADD_DIRECTORY','mkdir');
define('TASK_REMOVE_MULTIPLE_FILES','batchrm');

/** This constant is used to construct the fieldname used for deleting files */
define('PARAM_FILENAME','filename_');

/** This constant is used to construct the fieldname counting the number of files to delete */
define('PARAM_FILENAMES','filenames');

define('PARAM_PATH','path');
define('PARAM_SORT','sort');
define('SORTBY_NONE',0);
define('SORTBY_FILE_ASC',1);
define('SORTBY_FILE_DESC',-1);
define('SORTBY_SIZE_ASC',2);
define('SORTBY_SIZE_DESC',-2);
define('SORTBY_DATE_ASC',3);
define('SORTBY_DATE_DESC',-3);

/** File Manager
 *
 * This class implements the File Manager.
 *
 * This class is also used to browse files and images from FCKEditor.
 * Distinction is made via the $job parameter in the constructor.
 *
 * All the work is directed from the constructor, so it is enough to
 * simply instantiate a new object and let the constructor do the work.
 * The only thing needed is an output object (see {@link AdminOutput}).
 */
class FileManager {
    /** @var object|null $output collects the html output */
    var $output = NULL;

    /** @var string $job indicates how we are called (eg. as 'filemanager' or as 'filebrowser' or 'imagebrowser') */
    var $job = '';

    /** @var null|array $areas holds all area records (for future reference) or NULL if not yet set */
    var $areas = NULL;

    /** @var null|array $usergroups holds all $USER's group records (for future reference) or NULL if not yet set */
    var $usergroups = NULL;

    /** @var array $vpaths is a cache of virtual paths (see {@link vpath()}) */
    var $vpaths = array();

    /** @var string $current_directory links to the session-variable that holds the current working directory */
    var $current_directory;

    /** @var int $sort holds the current sort order in directory listings (default SORTBY_FILE_ASC) */
    var $sort = SORTBY_FILE_ASC;

    /** @var bool|array $ext_allow_upload holds uploadable filename extensions (lowercase), FALSE (none) or TRUE (all) */
    var $ext_allow_upload = FALSE;

    /** @var bool|array $ext_allow_browse holds brwosable filename extensions (lowercase), FALSE (none) or TRUE (all) */
    var $ext_allow_browse = FALSE;

    /** @var bool $show_thumbnails if TRUE we display files graphically (as a thumbnail), otherwise in table format */
    var $show_thumbnails = FALSE;

    /** construct a FileManager object (called from /program/main_admin.php)
     *
     * This initialises the FileManager, checks user permissions and 
     * finally dispatches the tasks. If the specified task is not
     * recognised, the default task TASK_LIST_DIRECTORY is executed.
     *
     * Note that many commands act on the directory contained in the
     * SESSION-variable current_directory.
     *
     * @param object &$output collects the html output
     * @param string $job indicates the mode: filemanager, filebrowser (FCKEditor) or imagebrowser (FCKEditor)
     * @return void results are returned as output in $this->output
     * @todo a nice filter for JOB_IMAGEBROWSER and also an alternative user interface for browsing/selecting images
     */
    function FileManager(&$output,$job=JOB_FILEMANAGER) {
        global $USER,$CFG;

        $this->output = &$output;
        $this->job = $job;

        // Prepare lists of allowed filename extensions for browsing/uploading and set show thumbnail flag
        switch($this->job) {
        case JOB_FILEBROWSER:
            $this->output->add_stylesheet($CFG->progwww_short.'/styles/admin_no_navigation.css');
            $this->show_thumbnails = TRUE;
            $this->ext_allow_upload = $this->ext_allow_browse = $this->allowed_extensions($CFG->filemanager_files);
            break;

        case JOB_IMAGEBROWSER:
            $this->output->add_stylesheet($CFG->progwww_short.'/styles/admin_no_navigation.css');
            $this->show_thumbnails = TRUE;
            $this->ext_allow_upload = $this->ext_allow_browse = $this->allowed_extensions($CFG->filemanager_images);
            break;

        case JOB_FLASHBROWSER:
            $this->output->add_stylesheet($CFG->progwww_short.'/styles/admin_no_navigation.css');
            $this->show_thumbnails = TRUE;
            $this->ext_allow_upload = $this->ext_allow_browse = $this->allowed_extensions($CFG->filemanager_flash);
            break;

        case JOB_FILEMANAGER:
            $allowed_extensions_list = $CFG->filemanager_files;
            $this->show_thumbnails = FALSE;
            $this->ext_allow_upload = $this->allowed_extensions($CFG->filemanager_files);
            $this->ext_allow_browse = TRUE;
            break;

        default:
            logger(sprintf('%s.%s(): weird job \'%s\' so no allowed file extensions at all, sorry about that',
                           __CLASS__,__FUNCTION__,$this->job));
            $this->show_thumbnails = FALSE;
            $this->ext_allow_upload = FALSE;
            $this->ext_allow_browse = FALSE;
            break;
        }

        $this->output->set_helptopic('filemanager');
        $this->areas = get_area_records();
        $this->usergroups = get_user_groups($USER->user_id);
        $this->sort = SORTBY_FILE_ASC;

        // Make absolutely sure we do have a valid working directory (default to the user's 'My Files')
        if (!isset($_SESSION['current_directory'])) {
            $_SESSION['current_directory'] = '/users/'.$USER->path;
        } elseif (($_SESSION['current_directory'] = $this->valid_path($_SESSION['current_directory'])) === FALSE) {
            $_SESSION['current_directory'] = '/users/'.$USER->path;
        }
        $this->current_directory = &$_SESSION['current_directory'];

        $task = get_parameter_string('task',TASK_LIST_DIRECTORY);
        switch ($task) {
        case TASK_LIST_DIRECTORY:        $this->task_list_directory();        break;
        case TASK_CHANGE_DIRECTORY:      $this->task_change_directory();      break;
        case TASK_PREVIEW_FILE:          $this->task_preview_file();          break;
        case TASK_REMOVE_FILE:           $this->task_remove_file();           break;
        case TASK_REMOVE_DIRECTORY:      $this->task_remove_directory();      break;
        case TASK_REMOVE_MULTIPLE_FILES: $this->task_remove_multiple_files(); break;
        case TASK_ADD_FILE:              $this->task_add_file();              break;
        case TASK_ADD_DIRECTORY:         $this->task_add_directory();         break;
        default:
            $s = (utf8_strlen($task) <= 50) ? $task : utf8_substr($task,0,44).' (...)';
            $message = t('task_unknown','admin',array('{TASK}' => htmlspecialchars($s)));
            $this->output->add_message($message);
            logger(__FUNCTION__.'(): unknown task: '.htmlspecialchars($s));
            $this->task_list_directory();
            break;
        }
    } // FileManager()


    /** show a directory listing of the current working directory and links to add/delete files/directories etc.
     *
     * This is the main routine to show a list of subdirectories and files
     * in the current working directory ($_SESSION['current_directory']).
     *
     * @return void current directory listing sent to browser via $this->output
     */
    function task_list_directory() {
        global $CFG;
        $path = $this->current_directory;
        $title = $this->vname($path);
        $this->output->add_content(sprintf('<h2>%s</h2>',htmlspecialchars($title)));
        $this->output->add_content(sprintf('<h3>%s</h3>',htmlspecialchars($path)));
        $this->show_breadcrumbs($path);
        $this->show_menu($path);
        $this->show_list($path);
    } // task_list_directory()


    /** make another directory the current (working) directory and optionally change the sort order
     *
     * This changes the current working directory to the user-supplied path
     * (after thorough validation, naturally). The new current directory is
     * stored in $this->current_directory and via that reference in the
     * $_SESSION array, for future reference. If a valid directory was specified,
     * we also take a look at the optional sort order parameter and set the
     * sort order of the directory listing accordingly.
     *
     * After (perhaps) changing the current directory, and perhaps changing the sort order,
     * the contents of that directory is displayed via {@link task_list_directory()}.
     *
     * @return void current directory changed and directory listing sent to browser via $this->output
     */
    function task_change_directory() {
        $newdir = get_parameter_string(PARAM_PATH,$this->current_directory);
        if (($path = $this->valid_path($newdir)) !== FALSE) {
            $this->current_directory = $path;
            $this->sort = get_parameter_int(PARAM_SORT,SORTBY_FILE_ASC);
        } else {
            $this->output->add_message(t('invalid_path','admin',array('{PATH}' => htmlspecialchars($newdir))));
        }
        $this->task_list_directory();
    } // task_change_directory()


    /** preview a file via file.php
     *
     * After validation of the specified path, the user is redirected
     * to {@link file.php} in order to show the selected file.
     *
     * @return void user is redirected to file.php
     */
    function task_preview_file() {
        if (($path = $this->valid_path(get_parameter_string(PARAM_PATH,''))) !== FALSE) {
            $url = $this->file_url($path);
            header('Location: '.$url);
            die();
        }
    } // task_preview_file()


    /** show confirmation dialog for multiple file delete OR perform actual file delete
     *
     * this routine either shows a list of files to be deleted, asking the user for confirmation
     * or actually deletes the specified files if the user did confirm the delete.
     * We bail out if the user pressed the cancel button in the confirmation dialog.
     * The real work is done in workhorse routines in order to combine the single-file-delete
     * and the batch-delete into a single confirmation routine. For actual deletion, however,
     * we always return here and not in the single file delete (see {$link task_remove_file()}).
     *
     * @uses show_dialog_confirm_delete_files()
     * @return void output sent to browser via $this->output and perhaps files deleted
     */
    function task_remove_multiple_files() {
        // 0 -- essential sanity check alias change directory
        $newdir = get_parameter_string(PARAM_PATH,$this->current_directory);
        if (($path = $this->valid_path($newdir)) === FALSE) {
            $this->output->add_message(t('invalid_path','admin',array('{PATH}' => htmlspecialchars($newdir))));
            $this->task_list_directory();
            return;
        }
        $this->current_directory = $path;
        $this->sort = get_parameter_int(PARAM_SORT,SORTBY_FILE_ASC);

        // 1 -- do they want to bail out?
        if (isset($_POST['button_cancel'])) {
            $this->output->add_message(t('cancelled','admin'));
            $this->task_list_directory();
            return;
        }
        // 2 -- construct a list of files to delete
        $entries = $this->get_entries($path);
        $n = (isset($_POST[PARAM_FILENAMES])) ? intval($_POST[PARAM_FILENAMES]) : 0;
        $entries_to_delete = array();
        for ($i=0; $i<$n; ++$i) {
            $fieldname = sprintf('%s%d',PARAM_FILENAME,$i);
            if (isset($_POST[$fieldname])) {
                $filename = magic_unquote($_POST[$fieldname]);
                if ((isset($entries[$filename])) && ($entries[$filename]['is_file'])) {
                    $entries_to_delete[$filename] = $entries[$filename];
                } else {
                    logger(sprintf('%s.%s(): weird attempt to delete %s/%s',__CLASS__,__FUNCTION__,$path,$filename));
                }
            }
        }

        // 3 -- what needs to be done?
        $n = count($entries_to_delete);
        // 3A -- nothing to do
        if ($n <= 0) {
            $this->output->add_message(t('filemanager_nothing_to_delete','admin'));
            $this->task_list_directory();
            return;
        }

        if ($n == 1) {
            $entry = reset($entries_to_delete);
            $params = array('{FILENAME}' => $entry['vpath']);
        } else {
            $params = array('{COUNT}' => strval($n));
        }

        // 3B -- confirmation dialog or actual deletion?
        if ((isset($_POST['confirm'])) && (intval($_POST['confirm']) != 0)) {
            if ($this->delete_files($path,$entries_to_delete) === FALSE) {
                $this->output->add_message(t(($n == 1) ? 'filemanager_failure_delete_file' : 
                                                         'filemanager_failure_delete_files','admin',$params));
            } else {
                $this->output->add_message(t(($n == 1) ? 'filemanager_success_delete_file' : 
                                                         'filemanager_success_delete_files','admin',$params));
            }
            $this->task_list_directory();
        } else {
            $this->show_dialog_confirm_delete_files($path,$entries_to_delete);
        }
    } // task_remove_multiple_files()


    /** show a confirmation dialog for deleting a single file
     *
     * This shows a confirmation dialog for deletion of a single file.
     * We reuse the code for deletion of multiple files, see {@link task_remove_multiple_files()}.
     *
     * @uses show_dialog_confirm_delete_files()
     * @return void confirmation dialog sent to browser via $this->output
     */
    function task_remove_file() {
        // 0A -- sanity check: the file to delete must be valid
        $path_parameter = get_parameter_string(PARAM_PATH,'');
        if (($filepath = $this->valid_path($path_parameter)) === FALSE) {
            $params = array('{PATH}' => htmlspecialchars($path_parameter));
            $this->output->add_message(t('invalid_path','admin',$params));
            $this->task_list_directory();
            return;
        }
        // 0B -- sanity check: the directory holding the file must be valid too
        if (($path = $this->valid_path(dirname($filepath))) === FALSE) {
            $params = array('{PATH}' => htmlspecialchars(dirname($filepath)));
            $this->output->add_message(t('invalid_path','admin',$params));
            $this->task_list_directory();
            return;
        }
        $this->current_directory = $path;
        $filename = basename($filepath);

        // 1 -- validate the supplied filename against the real directory contents
        $entries = $this->get_entries($path);
        $entries_to_delete = array();
        if ((isset($entries[$filename])) && ($entries[$filename]['is_file'])) {
            $entries_to_delete[$filename] = $entries[$filename];
        } else {
            logger(sprintf('%s.%s(): weird attempt to delete %s/%s',__CLASS__,__FUNCTION__,$path,$filename));
            $this->output->add_message(t('filemanager_nothing_to_delete','admin'));
            $this->task_list_directory();
            return;
        }

        // 2 -- everything looks OK, show dialog
        $this->show_dialog_confirm_delete_files($path,$entries_to_delete);
    } // task_remove_file()


    /** show a confirmation dialog for removing a single directory OR actually removes a directory
     *
     * This shows a confirmation dialog for removing of a single directory OR actually removes a directory.
     *
     * @return void confirmation dialog sent to browser via $this->output
     */
    function task_remove_directory() {
        // 0A -- sanity check: the directory to delete must be valid
        $path_parameter = get_parameter_string(PARAM_PATH,'');
        if (($directorypath = $this->valid_path($path_parameter)) === FALSE) {
            $params = array('{PATH}' => htmlspecialchars($path_parameter));
            $this->output->add_message(t('invalid_path','admin',$params));
            $this->task_list_directory();
            return;
        }
        // 0B -- sanity check: the directory holding the subdirectory must be valid too
        if (($path = $this->valid_path(dirname($directorypath))) === FALSE) {
            $params = array('{PATH}' => htmlspecialchars(dirname($directorypath)));
            $this->output->add_message(t('invalid_path','admin',$params));
            $this->task_list_directory();
            return;
        }
        $this->current_directory = $path;
        $directoryname = basename($directorypath);

        // 1 -- do they want to bail out?
        if (isset($_POST['button_cancel'])) {
            $this->output->add_message(t('cancelled','admin'));
            $this->task_list_directory();
            return;
        }
        // 2A -- validate the supplied directoryname against the real directory contents
        $entries = $this->get_entries($path);
        $entries_to_delete = array();
        if ((isset($entries[$directoryname])) && (!($entries[$directoryname]['is_file']))) {
            $entries_to_delete[$directoryname] = $entries[$directoryname];
        } else {
            logger(sprintf('%s.%s(): weird attempt to delete %s/%s',__CLASS__,__FUNCTION__,$path,$directoryname));
            $this->output->add_message(t('filemanager_nothing_to_delete','admin'));
            $this->task_list_directory();
            return;
        }
        $params = array('{DIRECTORY}' => $entries_to_delete[$directoryname]['vpath']);

        // 2B -- validate the directory to delete: it should be empty (except index.html and thumbnails)
        $entries = $this->get_entries($directorypath);
        if (count($entries) != 0) {
            logger(sprintf('%s.%s(): cannot delete non-empty %s/%s',__CLASS__,__FUNCTION__,$path,$directorypath));
            $this->output->add_message(t('filemanager_directory_not_empty','admin',$params));
            // as a service we descend into the directory
            $this->current_directory = $directorypath;
            $this->task_list_directory();
            return;
        }

        // 3 -- what needs to be done: confirmation dialog or actual rmdir?
        if ((isset($_POST['confirm'])) && (intval($_POST['confirm']) != 0)) {
            if ($this->delete_directory($path,$entries_to_delete) === FALSE) {
                $this->output->add_message(t('filemanager_failure_delete_directory','admin',$params));
            } else {
                $this->output->add_message(t('filemanager_success_delete_directory','admin',$params));
            }
            $this->task_list_directory();
        } else {
            $this->show_dialog_confirm_delete_directory($path,$entries_to_delete);
        }
    } // task_remove_directory()


    /** add one or more new files to a directory
     *
     * This routine either shows a dialog where the user can specify the names of
     * one or more (maximum $CFG->upload_max_files) files OR processes the dialog.
     *
     * Various checks are performed before the files are actually saved, e.g.
     * checks for viruses (via ClamAV), resolve name clashes, allowed filetypes
     * and extensions, etc. This is all done in a separate worker routine.
     *
     * @return void output returned via $this->output
     */
    function task_add_file() {
        global $WAS_SCRIPT_NAME,$CFG;

        // 1A -- bail out if user pressed cancel button
        if (isset($_POST['button_cancel'])) {
            $this->output->add_message(t('cancelled','admin'));
            $this->task_list_directory();
            return;
        }
        // 1B -- Check validity of working directory, maybe bail out
        $newdir = get_parameter_string(PARAM_PATH,$this->current_directory);
        if (($path = $this->valid_path($newdir)) === FALSE) {
            $this->output->add_message(t('invalid_path','admin',array('{PATH}' => htmlspecialchars($newdir))));
            $this->task_list_directory();
            return;
        }
        $this->current_directory = $path; // this is where we will create the new subdirectory

        // 2 -- prepare dialog (either to show it or to validate it)
        $dialogdef = $this->get_dialogdef_add_files($CFG->upload_max_files);
        $a_params = array('job' => $this->job, 'task' => TASK_ADD_FILE, PARAM_PATH => $path);
        $href = href($WAS_SCRIPT_NAME,$a_params);

        // 3 -- show dialog or validate + process?
        if (!isset($_POST['MAX_FILE_SIZE'])) {
            $params = array('{DIRECTORY}' => $this->vpath($path),
                            '{MAX_FILE_SIZE}' => strval(ini_get_int('upload_max_filesize')),
                            '{POST_MAX_SIZE}' => strval(ini_get_int('post_max_size')));
            $this->output->add_content('<h2>'.t('filemanager_add_files_header','admin',$params).'</h2>');
            $this->output->add_content(t('filemanager_add_files_explanation','admin',$params));
            $method = 'post';
            $attributes = array('enctype' => 'multipart/form-data');
            $this->output->add_content(dialog_quickform($href,$dialogdef,$method,$attributes));
            $this->show_breadcrumbs($path);
            $this->show_menu($path);
            return;
        }
        // 4 -- validate and save data
        $files_saved = 0;
        $files_skipped = 0;
        foreach($dialogdef as $name => $item) {
            // 4A - is there a fila at all?
            if (($item['type'] != F_FILE) ||
                (!isset($_FILES[$name])) ||
                ($_FILES[$name]['error'] == UPLOAD_ERR_NO_FILE)) {
                continue;
            }

            // 4B - was there an upload error
            $upload_err = $_FILES[$name]['error'];
            $params = array(
                '{FIELD}' => str_replace('~','',$item['label']),
                '{FILENAME}' => htmlspecialchars($_FILES[$name]['name']),
                '{ERROR}' => strval($upload_err),
                '{MAX_FILE_SIZE}' => strval(ini_get_int('upload_max_filesize')),
                '{POST_MAX_SIZE}' => strval(ini_get_int('post_max_size')));
            if (($upload_err == UPLOAD_ERR_INI_SIZE) || ($upload_err == UPLOAD_ERR_FORM_SIZE)) {
                $this->output->add_message(t('filemanager_add_files_upload_size_error','admin',$params));
                ++$files_skipped;
                continue;
            } elseif (($upload_err != UPLOAD_ERR_OK) || (!is_uploaded_file($_FILES[$name]['tmp_name']))) {
                // combine these two conditions because of the very low probability of !is_uploaded_file()
                $this->output->add_message(t('filemanager_add_files_upload_error','admin',$params));
                ++$files_skipped;
                continue;
            }

            // 4C - does the uploaded file contain a virus?
            $retval = $this->virusscan($_FILES[$name]['tmp_name'],$_FILES[$name]['name']);
            if ($retval != 0) {
                if ($retval == 1) { // virusscanner worked and the file is infected
                    $this->output->add_message(t('filemanager_add_files_virus_found','admin',$params));
                } else { // problem with the virusscanner itself
                    $params['{ERROR}'] = strval($retval);
                    $this->output->add_message(t('filemanager_add_files_virusscan_failed','admin',$params));
                }
                ++$files_skipped;
                continue;
            }

            // 4D -- 'forbidden' characters in filename?
            $sanitised_1 = sanitise_filename($_FILES[$name]['name']);
            if (substr($sanitised_1,0,strlen(THUMBNAIL_PREFIX)) == THUMBNAIL_PREFIX) {
                $params = array('{FILENAME}' => htmlspecialchars($_FILES[$name]['name']),
                                '{PATH}' => htmlspecialchars($this->vpath($path)),
                                '{TARGET}' => htmlspecialchars($sanitised_1));
                $this->output->add_message(t('filemanager_add_files_forbidden_name','admin',$params));
                ++$files_skipped;
                continue;
            }

            // 4E - does the (sanitised) filename match the mimetype?
            $sanitised_2 = $this->sanitise_filetype($_FILES[$name]['tmp_name'],$sanitised_1,$_FILES[$name]['type']);
            $mimetype = get_mimetype($_FILES[$name]['tmp_name'],$sanitised_1);
            if ($sanitised_1 != $sanitised_2) {
                $params = array('{FILENAME}' => htmlspecialchars($_FILES[$name]['name']),
                                '{FILETYPE}' => $mimetype,
                                '{TARGET}' => htmlspecialchars($sanitised_2));
                $this->output->add_message(t('filemanager_add_files_filetype_mismatch','admin',$params));
                ++$files_skipped;
                continue;
            }

            // Sane extension?
            if (!$this->has_allowed_extension($sanitised_2,$this->ext_allow_upload)) {
                $params = array('{FILENAME}' => htmlspecialchars($_FILES[$name]['name']),
                                '{FILETYPE}' => $mimetype,
                                '{TARGET}' => htmlspecialchars($sanitised_2));
                $this->output->add_message(t('filemanager_add_files_filetype_banned','admin',$params));
                ++$files_skipped;
                continue;
            }

            // 4F - circumvent name clashes by constructing a unique filename (don't overwrite existing files)
            $target_name = $this->unique_filename($path,$sanitised_2);

            // 4Ga - actually save file...
            $target_path = sprintf('%s/%s',$path,$target_name);
            $params = array('{FILENAME}' => htmlspecialchars($_FILES[$name]['name']),
                            '{PATH}' => htmlspecialchars($this->vpath($path)),
                            '{TARGET}' => htmlspecialchars($target_name));
            if (@move_uploaded_file($_FILES[$name]['tmp_name'],$CFG->datadir.$target_path)) {
                ++$files_saved;
                logger(sprintf('%s.%s(): success uploading %s to %s',
                        __CLASS__,__FUNCTION__,$_FILES[$name]['name'],$target_path),WLOG_DEBUG);
                $this->output->add_message(t('filemanager_add_files_success','admin',$params));
                // 4Gb - ...and on success try to create a thumbnail too
                $this->make_thumbnail($path,$target_name);
            } else {
                ++$files_skipped;
                logger(sprintf('%s.%s(): error moving uploaded file %s to %s',
                        __CLASS__,__FUNCTION__,$_FILES[$name]['name'],$target_path));
                $this->output->add_message(t('filemanager_add_files_error','admin',$params));
            }
        }
        // 5 -- all done, report results and show directory listing
        $params = array('{SAVECOUNT}' => strval($files_saved), '{SKIPCOUNT}' => strval($files_skipped));
        logger(sprintf('%s.%s(): added: %d, skipped: %d',__CLASS__,__FUNCTION__,$files_saved,$files_skipped),WLOG_DEBUG);
        $this->output->add_message(t('filemanager_add_files_results','admin',$params));
        $this->task_list_directory();
    } // task_add_file()


    /** create a new subdirectory
     *
     * This routine either shows a dialog where the user can specify the name of a new
     * directory to add OR processes the dialog.
     *
     * In case of directory name too short, already exists, etc. the user is
     * returned to the dialog to try again. If all goes well the new directory
     * is created and at the same time the empty file 'index.html' is created
     * to "protect" the directory from prying eyes.
     *
     * @return void output returned via $this->output
     */
    function task_add_directory() {
        global $WAS_SCRIPT_NAME,$CFG;

        // 1A -- bail out if user pressed cancel button
        if (isset($_POST['button_cancel'])) {
            $this->output->add_message(t('cancelled','admin'));
            $this->task_list_directory();
            return;
        }
        // 1B -- Check validity of working directory, maybe bail out
        $newdir = get_parameter_string(PARAM_PATH,$this->current_directory);
        if (($path = $this->valid_path($newdir)) === FALSE) {
            $this->output->add_message(t('invalid_path','admin',array('{PATH}' => htmlspecialchars($newdir))));
            $this->task_list_directory();
            return;
        }
        $this->current_directory = $path; // this is where we will create the new subdirectory

        // 2 -- prepare dialog (either to show it or to validate it)
        $dialogdef = array(
            'subdirectory' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'subdirectory',
                'minlength' => 1,
                'maxlength' => 240,
                'columns' => 30,
                'label' => t('filemanager_add_subdirectory_label','admin'),
                'title' => t('filemanager_add_subdirectory_title','admin'),
                'value' => ''
                ),
            'button_save' => dialog_buttondef(BUTTON_SAVE),
            'button_cancel' => dialog_buttondef(BUTTON_CANCEL)
            );
        $a_params = array('job' => $this->job, 'task' => TASK_ADD_DIRECTORY, PARAM_PATH => $path);
        $href = href($WAS_SCRIPT_NAME,$a_params);

        // 3 -- show dialog or validate + process?
        if (!isset($_POST['subdirectory'])) {
            $this->output->add_content('<h2>'.t('filemanager_add_subdirectory_header','admin').'</h2>');
            $this->output->add_content(t('filemanager_add_subdirectory_explanation','admin'));
            $this->output->add_content(dialog_quickform($href,$dialogdef));
            $this->show_breadcrumbs($path);
            $this->show_menu($path);
            return;
        }

        // 4 -- validate user input and maybe create directory

        // 4A -- check for generic errors
        $invalid = FALSE;
        if (!dialog_validate($dialogdef)) {
            $invalid = TRUE;
        }
        // 4B -- check for additional errors: sane filename
        $subdirectory = $dialogdef['subdirectory']['value'];
        $sanitised_subdirectory = sanitise_filename($subdirectory);
        if (($subdirectory != $sanitised_subdirectory) ||
            (substr($sanitised_subdirectory,0,strlen(THUMBNAIL_PREFIX)) == THUMBNAIL_PREFIX)) {
            ++$dialogdef['subdirectory']['errors'];
            $params = array(
                '{FIELD}' => str_replace('~','',$dialogdef['subdirectory']['label']),
                '{VALUE}' => htmlspecialchars($subdirectory));
            $dialogdef['subdirectory']['error_messages'][] = t('validate_bad_filename','',$params);
            $invalid = TRUE;
        }
        // 4C -- check for additional errors: directory should not exist already
        $subdirectory_full_path = $CFG->datadir.$path.'/'.$sanitised_subdirectory;
        if (file_exists($subdirectory_full_path)) {
            ++$dialogdef['subdirectory']['errors'];
            $params = array(
                '{FIELD}' => str_replace('~','',$dialogdef['subdirectory']['label']),
                '{VALUE}' => $this->vpath($path).'/'.$sanitised_subdirectory);
            $dialogdef['subdirectory']['error_messages'][] = t('validate_already_exists','',$params);
            $invalid = TRUE;
        }
        // 4D -- redo dialog if there were errors
        if ($invalid) {
            // shortcut: only the subdirectory field can (and does) yield errors
            $this->output->add_message($dialogdef['subdirectory']['error_messages']);
            $dialogdef['subdirectory']['value'] = $sanitised_subdirectory;
            $this->output->add_content('<h2>'.t('filemanager_add_subdirectory_header','admin').'</h2>');
            $this->output->add_content(t('filemanager_add_subdirectory_explanation','admin'));
            $this->output->add_content(dialog_quickform($href,$dialogdef));
            $this->show_breadcrumbs($path);
            // note that we do NOT display the menu here; let the user concentrate (or press Cancel) this time
            return;
        }

        // 5 -- all set, go create subdir
        $params = array('{PATH}' => htmlspecialchars($this->vpath($path)),
                        '{DIRECTORY}' => htmlspecialchars($sanitised_subdirectory));
        if (@mkdir($subdirectory_full_path,0700)) {
            @touch($subdirectory_full_path.'/index.html'); // "protect" the newly created directory from prying eyes
            $this->output->add_message(t('filemanager_add_subdirectory_success','admin',$params));
            logger(sprintf('%s.%s(): success with mkdir %s',__CLASS__,__FUNCTION__,$path.'/'.$subdirectory),WLOG_DEBUG);
        } else {
            $this->output->add_message(t('filemanager_add_subdirectory_failure','admin',$params));
            logger(sprintf('%s.%s(): cannot mkdir %s',__CLASS__,__FUNCTION__,$path.'/'.$subdirectory));
        }
        $this->task_list_directory();
    } // task_add_directory()


    // ========================================================================
    // ============================== WORKHORSES ==============================
    // ========================================================================


    /** access control and validation for selected directory or file
     *
     * This routine checks to see if the current user has access to the
     * specified file or directory. If not, FALSE is returned, otherwise
     * the valid path is returned, with a starting slash. If $path doesn't
     * start with a slash a slash is assumed nevertheless. The path is
     * relative to $CFG->datadir.
     *
     * It is not allowed to reference parent directories in the path.
     * This prevents tricks like '../../../etc/passwd' (leaking the system passwd file)
     * or '/users/foo/../bar' (access to bar's userdata with permissions for just foo's files).
     * Furthermore, symbolic links are NOT acceptable as part of the path, i.e.
     * symlinks like '/users/foo/etc -> /etc' or '/users/foo/passwd -> /etc/passwd' are
     * considered invalid.
     *
     * Required permissions for access are:
     *  - areas: $USER must have the admin_pagemanager permissions for that area.
     *  - groups: $USER must be a member of that group OR $USER must have access to the Account Manager.
     *  - users: $USER must be that user OR $USER must have access to the Account Manager.
     *
     * @param string $path the path (file or directory) to check, relative to $CFG->datadir
     * @return string|bool a clean version of $path starting with a slash, FALSE otherwise
     * @uses $CFG
     * @uses $USER
     * @todo the check on '/../' is inconclusive if the $path is encoded in UTF-8: the overlong
     *       sequence 2F C0 AE 2E 2F eventually yields 2F 2E 2E 2F or '/../'. Reference: RFC3629 section 10.
     */
    function valid_path($path) {
        global $CFG,$USER;

        // 0 -- prepare for easy access to the $n different path components
        $path_components = $this->explode_path($path);
        $n = count($path_components);


        // 1 -- always disallow attempts to escape from tree via parent directory tricks
        if (in_array('..',$path_components)) {
            logger(sprintf("%s.%s(): no .. allowed in path '%s'",__CLASS__,__FUNCTION__,$path),WLOG_DEBUG);
            return FALSE;
        }

        // 2 -- check access to subtrees /areas, /groups or /users
        switch($path_components[0]) {
        case '':
            break;

        case 'areas':
            if ($n > 1) {
                $area_path = $path_components[1];
                foreach($this->areas as $area_id => $area) {
                    if ($area['path'] == $area_path) {
                        if (!$USER->is_admin_pagemanager($area_id)) {
                            return FALSE;
                        } else {
                            break;
                        }
                    }
                }
            }
            break;

        case 'groups':
            if (($n > 1) && (!$USER->has_job_permissions(JOB_PERMISSION_ACCOUNTMANAGER))) {
                $group_path = $path_components[1];
                $is_member = FALSE;
                foreach($this->usergroups as $group_id => $usergroup) {
                    if ($usergroup['path'] == $group_path) {
                        $is_member = TRUE;
                        break;
                    }
                }
                if (!$is_member) {
                    return FALSE;
                }
            }
            break;

        case 'users':
            if (($n > 1) && (!$USER->has_job_permissions(JOB_PERMISSION_ACCOUNTMANAGER))) {
                $user_path = $path_components[1];
                if ($user_path != $USER->path) {
                    return FALSE;
                }
            }
            break;

        default:
            return FALSE;
            break;
        }

        // 3 -- check existence of physical path, do not allow symlinks
        $full_path = $CFG->datadir;
        for ($i=0; $i < $n-1; ++$i) {
            $full_path .= '/'.$path_components[$i];
            if ((is_link($full_path)) || (!is_dir($full_path))) {
                return FALSE;
            }
        }
        $full_path .= '/'.$path_components[$n-1];
        if ((is_link($full_path)) || ((!is_dir($full_path)) && (!is_file($full_path)))) {
            return FALSE;
        }

        // 4 -- success: return the complete path with leading '/' and no trailing '/'
        return '/'.implode('/',$path_components);
    } // valid_path()


    /** display a list of directories and files in $path
     *
     * This yields a list of directories (for $path is '/', '/areas', '/groups'
     * or '/users') or a list of directories and files ($path is anything else).
     * In the latter case, if the current user has sufficient permissions,
     * various additional links are added such as upload a file or create directory.
     * The actual work is done in two separate workhorses.
     *
     * @parameter string $path the (virtual) path to the directory to list
     * @return void output displayed via $this->output
     * @uses show_directories()
     * @uses show_directories_and_files()
     */
    function show_list($path) {
        $path_components = $this->explode_path($path);
        if (count($path_components) <= 1) {
            $entries = array();
            $parent = FALSE;
            switch($path_components[0]) {
            case '':
                $entries = $this->get_entries_root();
                break;
            case 'areas':
                $entries = $this->get_entries_areas();
                $parent = '/';
                break;
            case 'groups':
                $entries = $this->get_entries_groups();
                $parent = '/';
                break;
            case 'users':
                $entries = $this->get_entries_users();
                $parent = '/';
                break;
            }
            $this->show_directories($entries,$parent);
        } else {
            $this->show_directories_and_files($path,$this->show_thumbnails);
        }
    } // show_list()


    /** display a list of subdirectories and files in directory $path
     *
     * This long routine displays the following items to the user
     *  - (optional) navigation link to add (upload) a file
     *  - (optional) navigation link to add (create) a directory
     *  - a 4, 5 or 6 column table with
     *    . navigation link to the parent directory
     *    . 0, 1 or more rows with delete and navigation links to subdirectories (if any)
     *    . 0, 1 or more rows with a checkbox and delete and preview links to files (if any)
     *    . (optional) a 'select all' checkbox
     *  - (optional) Delete-button to mass-delete files 
     *
     * The table can be ordered in various ways: by name, by size and by date,
     * either ascending or descending. Clicking the relevant column header yields
     * another sort order. This toggles between ascending and descending.
     * Default sort order is by name ascending.
     *
     * The checkbox 'select all' works with Javascript in the most simple way:
     * an ad-hoc script connected to the onclick attribute of the select all checkbox.
     * However, the select all checkbox itself is rendered via Javascript.
     * The effect is that this feature is only available if Javascript is enabled
     * in the browser. If it isn't, no select all is visible so it can not distract
     * the user. This is part of the attempt to make this CMS usable even without
     * Javascript.
     *
     * If the flag $show_thumbnails is set we display file entries as thumbnails.
     * This is done mostly to cater for the visual interactieve selection of images from FCK Editor.
     *
     * @param string $path the directory to display
     * @param bool $show_thumbnails if TRUE files are displayed as thumbnails, table rows otherwise
     * @return void output generated via $this->output
     * @uses $USER
     * @uses $CFG
     * @uses $WAS_SCRIPT_NAME
     * @todo This routine is way too long, it should be split up into smaller subroutines
     */
    function show_directories_and_files($path,$show_thumbnails=TRUE) {
        global $USER,$WAS_SCRIPT_NAME,$CFG;
        $path_components = $this->explode_path($path);
        $n = count($path_components);
        $branch = $path_components[0];
        $entries = $this->get_entries($path);
        $this->sort_entries($entries,$this->sort);

        // 1 -- check out permissions for add/delete file/directory
        $parent = FALSE;
        $add_file = FALSE;
        $add_directory = FALSE;
        $delete_file = FALSE;
        $delete_directory = FALSE;
        if (($n == 2) && ($branch == 'users') && ($path_components[1] == $USER->path)) {
            $parent = '/';
            $add_file = TRUE;
            $add_directory = TRUE;
            $delete_file = TRUE;
            $delete_directory = TRUE;
        } else {
            $parent = '';
            for ($i=0; $i < $n-1; ++$i) {
                $parent .= '/'.$path_components[$i];
            }
            if ($branch == 'areas') {
                $area_path = $path_components[1];
                foreach($this->areas as $area_id => $area) {
                    if ($area['path'] == $area_path) {
                        $perm = PERMISSION_NODE_ADD_PAGE | PERMISSION_AREA_ADD_PAGE;
                        $add_file = $USER->has_area_permissions($perm,$area_id);
                        $perm = PERMISSION_NODE_ADD_SECTION | PERMISSION_AREA_ADD_SECTION;
                        $add_directory = $USER->has_area_permissions($perm,$area_id);
                        $perm = PERMISSION_NODE_DROP_PAGE | PERMISSION_AREA_DROP_PAGE;
                        $delete_file = $USER->has_area_permissions($perm,$area_id);
                        $perm = PERMISSION_NODE_DROP_SECTION | PERMISSION_AREA_DROP_SECTION;
                        $delete_directory = $USER->has_area_permissions($perm,$area_id);
                        break;
                    }
                }
            } else { // branch = groups or users: always allowed to add/delete
                $add_file = TRUE;
                $add_directory = TRUE;
                $delete_file = TRUE;
                $delete_directory = TRUE;
            }
        }

        // 2 -- maybe show add file/directory links (before and outside the table with dirs/files)
        if (($add_file) || ($add_directory)) {
            $this->output->add_content('<ul>');
            $html_tag_li = '  '.html_tag('li',array('class' => 'level0'));
            $a_params = array('job' => $this->job, 'task' => TASK_ADD_FILE, PARAM_PATH => $path);
            if ($add_file) {
                $a_attr = array('title' => t('filemanager_add_file_title','admin'));
                $anchor = t('filemanager_add_file','admin');
                $this->output->add_content($html_tag_li.html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor));
            }
            if ($add_directory) {
                $a_params['task'] = TASK_ADD_DIRECTORY;
                $a_attr = array('title' => t('filemanager_add_directory_title','admin'));
                $anchor = t('filemanager_add_directory','admin');
                $this->output->add_content($html_tag_li.html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor));
            }
            $this->output->add_content('</ul>');
        }

        // 3A -- maybe open form (for multiple file manipulation)
        if ($delete_file) {
            $a_params = array('job' => $this->job, 'task' => TASK_REMOVE_MULTIPLE_FILES, PARAM_PATH => $path);
            $this->output->add_content(html_form(href($WAS_SCRIPT_NAME,$a_params)));
        }
        // 3B -- open 4, 5 or 6 column table including clickable headers to sort by file/size/date
        $this->output->add_content(html_table());
        $this->output->add_content('  '.html_table_row(array('class'=>'header')));
        if ($this->output->text_only) {
            $spacer = '';
        } else { // quick&dirty minimum column width
            $img_attr = array('width' => 16, 'height' => 16, 'title' => '', 'alt' => t('spacer','admin'));
            $spacer = html_img($CFG->progwww_short.'/graphics/blank16.gif',$img_attr);
        }
        // 3Ba -- column with checkboxes (only if not in thumbnail view mode)
        if (($delete_file) && (!($show_thumbnails))) {
            $this->output->add_content('    '.html_table_head('',$spacer));
        }
        // 3Bb - column with delete icons
        if ((($delete_file) && (!($show_thumbnails))) || ($delete_directory)) {
            $this->output->add_content('    '.html_table_head('',$spacer));
        }
        // 3Bc - column with folder/preview icons
        $this->output->add_content('    '.html_table_head('',$spacer));

        // 3Bd - column with filename
        $a_params = array('job' => $this->job,'task' => TASK_CHANGE_DIRECTORY,PARAM_PATH => $path);
        $th_attr = array('align' => 'left');
        $sort = ($this->sort == SORTBY_FILE_ASC) ? SORTBY_FILE_DESC : SORTBY_FILE_ASC;
        $a_params['sort'] = $sort;
        $a_attr = array('title' => t(($sort > 0) ? 'filemanager_sort_asc' : 'filemanager_sort_desc','admin'));
        $anchor = t('filemanager_column_file','admin');
        $this->output->add_content('    '.html_table_head($th_attr,html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor)));

        // 3Be - column with size
        $th_attr = array('align' => 'right');
        $sort = ($this->sort == SORTBY_SIZE_ASC) ? SORTBY_SIZE_DESC : SORTBY_SIZE_ASC;
        $a_params['sort'] = $sort;
        $a_attr = array('title' => t(($sort > 0) ? 'filemanager_sort_asc' : 'filemanager_sort_desc','admin'));
        $anchor = t('filemanager_column_size','admin');
        $this->output->add_content('    '.html_table_head($th_attr,html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor)));

        // 3Bf - column with datim
        $th_attr = array('align' => 'left');
        $sort = ($this->sort == SORTBY_DATE_ASC) ? SORTBY_DATE_DESC : SORTBY_DATE_ASC;
        $a_params['sort'] = $sort;
        $a_attr = array('title' => t(($sort > 0) ? 'filemanager_sort_asc' : 'filemanager_sort_desc','admin'));
        $anchor = t('filemanager_column_date','admin');
        $this->output->add_content('    '.html_table_head($th_attr,html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor)));
        $this->output->add_content('  '.html_table_row_close());


        // 4 -- prepare to iterate through all directory entries
        $oddeven = 'even';

        // 4A -- always add a link to the parent directory ($parent should not be FALSE)
        if ($parent !== FALSE) {
            $a_params = array('job' => $this->job,'task' => TASK_CHANGE_DIRECTORY,PARAM_PATH => $parent);
            $title = t('filemanager_parent_title','admin');
            $a_attr = array('title' => $title);
            $anchor = t('filemanager_parent','admin');
            if ($this->output->text_only) {
                $icon = html_tag('span','class="icon"','['.t('icon_open_directory_text','admin').']');
            } else {
                $img_attr = array('height'=>16,'width'=>16,'title'=>$title,'alt'=>t('icon_open_directory_alt','admin'));
                $icon = html_img($CFG->progwww_short.'/graphics/folder_closed.gif',$img_attr);
            }
            $oddeven = ($oddeven == 'even') ? 'odd' : 'even';
            $this->output->add_content('  '.html_table_row(array('class' => $oddeven)));
            if (($delete_file) && (!($show_thumbnails))) {
                $this->output->add_content('    '.html_table_cell('','')); // a: column with checkboxes empty
            }
            if ((($delete_file) && (!($show_thumbnails))) || ($delete_directory)) {
                $this->output->add_content('    '.html_table_cell('','')); // b: column with delete icons empty
            }
            // c: column with folder icon
            $this->output->add_content('    '.html_table_cell('',html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$icon)));
            // d: column with filename
            $this->output->add_content('    '.html_table_cell('',html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor)));
            $this->output->add_content('    '.html_table_cell('','')); // e: column with size empty
            $this->output->add_content('    '.html_table_cell('','')); // f: column with date empty
            $this->output->add_content('  '.html_table_row_close());
        }

        // 4B -- step through the entries
        $count_directories = 0;
        $count_files = 0;
        $files = array();
        foreach ($entries as $name => $entry) {
            $index = ($entry['is_file']) ? $count_files++ : $count_directories++;
            // Maybe keep the files for later processing (do all directories first)
            if (($show_thumbnails) && ($entry['is_file'])) {
                $files[$name] = $entry;
                continue;
            }
            $oddeven = ($oddeven == 'even') ? 'odd' : 'even';
            $attributes = array('class' => $oddeven);
            // flag 'forbidden' files (ie. files which would be banned from uploading with the current settings)
            $file_forbidden = ((isset($entry['is_allowed'])) && (!$entry['is_allowed'])) ? TRUE : FALSE;
            if ($file_forbidden) {
                $attributes['class'] .= ' error';
            }
            $this->output->add_content('  '.html_table_row($attributes));

            // 4Ba: checkbox (for files only)
            if (($delete_file) && (!($show_thumbnails))) {
                if ($entry['is_file']) {
                    $checkbox_def = array(
                        'type' => F_CHECKBOX,
                        'name' => sprintf('%s%d',PARAM_FILENAME,$index),
                        'options' => array($entry['name'] => ' '),
                        'title' => t('filemanager_select_file_entry_title','admin'),
                        'value' => '' // default is UNchecked
                        );
                    $widget = dialog_get_widget($checkbox_def);
                    if (is_array($widget)) {
                        $this->output->add_content('    '.html_table_cell($attributes));
                        $this->output->add_content($widget);
                        $this->output->add_content('    '.html_table_cell_close());
                    } else {
                        $this->output->add_content('    '.html_table_cell($attributes,$widget));
                    }
                } else {
                    $this->output->add_content('    '.html_table_cell($attributes,''));
                }
            }
            // 4Bb: delete icon
            if ((($delete_file) && (!($show_thumbnails))) || ($delete_directory)) {
                if (($delete_file) && ($entry['is_file'])) {
                    $title = t('filemanager_delete_file','admin',array('{FILENAME}'=>htmlspecialchars($entry['vname'])));
                    $a_params = array('job' => $this->job,
                                      'task' => TASK_REMOVE_FILE,
                                      PARAM_PATH => $entry['path']);
                    $a_attr = array('title' => $title);
                    if ($this->output->text_only) {
                        $anchor = html_tag('span','class="icon"','['.t('icon_delete_file_text','admin').']');
                    } else {
                        $img_attr = array('height'=>16,'width'=>16,'title'=>$title,'alt'=>t('icon_delete_file_alt','admin'));
                        $anchor = html_img($CFG->progwww_short.'/graphics/delete.gif',$img_attr);
                    }
                    $cell = html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
                } elseif (($delete_directory) && (!($entry['is_file']))) {
                    $title = t('filemanager_delete_directory','admin',
                               array('{DIRECTORY}'=>htmlspecialchars($entry['vname'])));
                    $a_params = array('job' => $this->job,
                                      'task' => TASK_REMOVE_DIRECTORY,
                                      PARAM_PATH => $entry['path']);
                    $a_attr = array('title' => $title);
                    if ($this->output->text_only) {
                        $anchor = html_tag('span','class="icon"','['.t('icon_delete_directory_text','admin').']');
                    } else {
                        $img_attr = array('height'=>16,'width'=>16,'title'=>$title,'alt'=>t('icon_delete_directory_alt','admin'));
                        $anchor = html_img($CFG->progwww_short.'/graphics/delete.gif',$img_attr);
                    }
                    $cell = html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
                } else {
                    $cell = '';
                }
                $this->output->add_content('    '.html_table_cell($attributes,$cell));
            } // else suppress this column completely
            if ($entry['is_file']) {
                // 4Bc (file): preview file icon
                $title = $entry['title'];
                if ($this->output->text_only) {
                    $anchor = html_tag('span','class="icon"','['.t('icon_preview_file_text','admin').']');
                } else {
                    $img_attr=array('height'=>16,'width'=>16,'alt'=>t('icon_preview_file_alt','admin'));
                    if (!$file_forbidden) {
                        $img_attr['title'] = $title;
                    }
                    $anchor = html_img($CFG->progwww_short.'/graphics/view.gif',$img_attr);
                }
                if ($file_forbidden) {
                    // Show a 'dead' preview link with icon and a dead link with the filename;
                    // prevent that the user accidently displays a rogue file.
                    // Note the '[' and ']': this makes it visible even without stylesheets
                    $this->output->add_content('    '.html_table_cell($attributes,$anchor));
                    $anchor = '['.htmlspecialchars($entry['vname']).']';
                    $this->output->add_content('    '.html_table_cell($attributes,$anchor));
                } else {
                    // OK. Acceptable file, carry on.
                    // Now construct the A tag for the preview button.
                    // This is tricky, because we want to present the preview in a separate
                    // window/popup. We don't want to double-escape html special chars, so we
                    // construct the url + params + attr manually here. The javascript routine is
                    // added to the output page in /program/main_admin.php.
                    //
                    $a_params = sprintf('job=%s&task=%s&%s=%s',
                                         $this->job,
                                         TASK_PREVIEW_FILE,
                                         PARAM_PATH,rawurlencode($entry['path']));
                    $url = $WAS_SCRIPT_NAME.'?'.htmlspecialchars($a_params);
                    $a_attr = sprintf('title="%s" target="_blank" onclick="popup(\'%s\'); return false;"',$title,$url);
                    $this->output->add_content('    '.html_table_cell($attributes,
                                                                  html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor)));
                    // 4Bd (file): another A tag but now with the filename as anchor
                    // However, the action is different if we are in file browsing mode: in that case we select the
                    // file (for the FCK editor).
                    $anchor = htmlspecialchars($entry['vname']);
                    if (($this->job == JOB_FILEBROWSER) || 
                        ($this->job == JOB_IMAGEBROWSER) || 
                        ($this->job == JOB_FLASHBROWSER)) {
                        // Note: we depend on Javascript here, but since FCK Editor is also a Javascript application...
                        // In other words: we would not be here in the first place if Javascript wasn't enabled.
                        // (The file preview option does not depend on Javascript, see task_preview_file().)
                        $url = $this->file_url($entry['path']);
                        $title = t('filemanager_select','admin',array('{FILENAME}' => htmlspecialchars($entry['name'])));
                        $a_attr = sprintf('title="%s" onclick="select_url(\'%s\'); return false;"',$title,$url);
                        $this->output->add_content('    '.html_table_cell($attributes,html_a("#",NULL,$a_attr,$anchor)));
                    } else {
                        $this->output->add_content('    '.html_table_cell($attributes,
                                                                  html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor)));
                    }
                }
                // 4Be (file): filesize (right aligned)
                $attributes['align'] = 'right';
                $size = $this->human_readable_size($entry['size']);
                $this->output->add_content('    '.html_table_cell($attributes,$size));
            } else { // directory
                // 4Bc (dir): open directory icon
                $a_params = array('job' => $this->job,
                                  'task' => TASK_CHANGE_DIRECTORY,
                                  PARAM_PATH => $entry['path']);
                $title = $entry['title'];
                $a_attr = array('title' => $title);
                if ($this->output->text_only) {
                    $anchor = html_tag('span','class="icon"','['.t('icon_open_directory_text','admin').']');
                } else {
                    $img_attr=array('height'=>16,'width'=>16,'title'=>$title,'alt'=>t('icon_open_directory_alt','admin'));
                    $anchor = html_img($CFG->progwww_short.'/graphics/folder_closed.gif',$img_attr);
                }
                $this->output->add_content('    '.html_table_cell($attributes,
                                                                  html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor)));
                // 4Bd (dir): another A tag but now with the directory name as anchor
                $anchor = htmlspecialchars($entry['vname']);
                $this->output->add_content('    '.html_table_cell($attributes,
                                                                  html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor)));

                // 4Be (dir): skip 'size' of the directory (makes no sense)
                $this->output->add_content('    '.html_table_cell($attributes,''));
            }
            // 4Bf: mtime (as yyyy-mm-dd hh:mm:ss)
            $datim = strftime('%Y-%m-%d %T',$entry['mtime']);
            $attributes = array('class' => $oddeven);
            $this->output->add_content('    '.html_table_cell($attributes,$datim));
            // close the table row
            $this->output->add_content('  '.html_table_row_close());
        }
        // at this point we have shown all directory entries and maybe all file entries
        // Now we may want to add a 'select all' checkbox + Delete button
        if (($delete_file) && ($count_files > 0)) {
            $oddeven = ($oddeven == 'even') ? 'odd' : 'even';
            $attributes = array('class' => $oddeven);
            // Generate ad hoc javascript to check/uncheck all checkboxes
            $onclick = sprintf("for(var i=0; i<%d; ++i) {document.forms[0].elements['%s'+i].checked=this.checked;}",
                               $count_files,PARAM_FILENAME);
            // Manually construct widget because we cannot squeeze in the onclick attribute with dialog_get_widget()
            $widget = html_tag('input',array(
                'name' => sprintf('%s%s',PARAM_FILENAME,'all'),
                'type' => 'checkbox',
                'value' => '1',
                'title' => t('filemanager_select_file_entries_title','admin'),
                'onclick' => $onclick));
            $label = t('filemanager_select_file_entries','admin');
            // Now conditionally write the last row in the table
            // This is done with Javascript generating HTML which in turn adds Javascript in the onclick attribute.
            // Mmmm, overly complicated, but the effect is: only when Javascript is enabled a 'select all'
            // checkbox is rendered. If Javascript is disabled, we don't show this stuff at all (no distractions).
            $this->output->add_content('  <script type="text/javascript"><!--');
            $this->output->add_content('    document.write("'.addslashes(html_table_row($attributes)).'");');
            $this->output->add_content('    document.write("  '.addslashes(html_table_cell($attributes,$widget)).'");');
            $attributes['colspan'] = '5';
            $this->output->add_content('    document.write("  '.addslashes(html_table_cell($attributes,$label)).'");');
            $this->output->add_content('    document.write("'.addslashes(html_table_row_close()).'");');
            $this->output->add_content('  //--></script>');
        }
        $this->output->add_content(html_table_close());
        if ($delete_file) {
            if ($count_files > 0) {
                $this->output->add_content(html_tag('input',array(
                                                        'type' => 'hidden',
                                                        'name' => PARAM_FILENAMES,
                                                        'value' => strval($count_files))));
                $widget = dialog_get_widget(dialog_buttondef(BUTTON_DELETE));
                $this->output->add_content('<p>');
                $this->output->add_content($widget);
            }
        }
        if (($show_thumbnails) && ($count_files > 0)) {
            $index = 0;
            foreach ($files as $name => $entry) {
                $this->show_file_as_thumbnail($path,$entry,$delete_file,$index++);
            }
        }
        if ($delete_file) {
            $this->output->add_content(html_form_close());
        }
    } // show_directories_and_files()


    /** output a simple list of directories (for navigation only)
     *
     * This outputs a simple list of subdirectories based on 
     * information in the array $entries. The subdirectories can
     * not be deleted and no files or subdirectories can be added.
     * (because this is either '/', 'Areas','Groups' or 'Users')
     *
     * @param array &$entries ready to use data describing all subdirectories to show
     * @param bool|string $parent suppress link to parent if FALSE otherwise path of parent
     * @return void output generated via $this->output
     * @uses $CFG
     * @uses $WAS_SCRIPT_NAME
     */
    function show_directories(&$entries,$parent) {
        global $CFG,$WAS_SCRIPT_NAME;

        $a_params = array('job' => $this->job,'task' => TASK_CHANGE_DIRECTORY);
        $this->output->add_content('<ul>');

        // maybe display a link to the parent directory
        if ($parent !== FALSE) {
            $title = t('filemanager_parent_title','admin');
            $a_params[PARAM_PATH] = $parent;
            $a_attr = array('title' => $title);
            if ($this->output->text_only) {
                $icon = html_tag('span','class="icon"','['.t('icon_open_directory_text','admin').']');
            } else {
                $img_attr=array('height'=>16,'width'=>16,'title'=>$title,'alt'=>t('icon_open_directory_alt','admin'));
                $icon = html_img($CFG->progwww_short.'/graphics/folder_closed.gif',$img_attr);
            }
            $anchor = t('filemanager_parent','admin');
            $this->output->add_content('  '.html_tag('li',array('class' => 'level0')));
            $this->output->add_content('      '.html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$icon));
            $this->output->add_content('      '.html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor));
        }

        // iterate through all directories
        foreach($entries as $entry) {
            $title = $entry['title'];
            $a_params[PARAM_PATH] = $entry['path'];
            $a_attr = array('title' => $title);
            if ($this->output->text_only) {
                $icon = html_tag('span','class="icon"','['.t('icon_open_directory_text','admin').']');
            } else {
                $img_attr=array('height'=>16,'width'=>16,'title'=>$title,'alt'=>t('icon_open_directory_alt','admin'));
                $icon = html_img($CFG->progwww_short.'/graphics/folder_closed.gif',$img_attr);
            }
            $anchor = htmlspecialchars($entry['vname']);
            $this->output->add_content('  '.html_tag('li',array('class' => 'level0')));
            $this->output->add_content('    '.html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$icon));
            $this->output->add_content('    '.html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor));
        }
        $this->output->add_content('</ul>');
    } // show_directories()


    /** display a clickable path to the directory $path
     *
     * @param string $path the directory path to show
     * @return void output stored via $this->output
     */
    function show_breadcrumbs($path) {
        global $WAS_SCRIPT_NAME, $USER;
        $path_components = $this->explode_path($path);;
        $n = count($path_components);
        if (($n > 1) && ($path_components[0] == 'users') && ($path_components[1] == $USER->path)) {
            $i = 1;
            $directory = '/users';
        } else {
            $i = 0;
            $directory = '';
        }
        $href = $WAS_SCRIPT_NAME;
        $a_params = array('job' => $this->job, 'task' => TASK_CHANGE_DIRECTORY);

        // If we are NOT in the root directory, add a breadcrumb link to the root directory.
        // If we are, this breadcrumb will be added via the regular while loop below anyway.
        if ($path_components[0] != '') {
            $name = $this->vname('/');
            $a_params[PARAM_PATH] = '/';
            $attributes = array('title' => t('filemanager_navigate_to','admin',array('{DIRECTORY}' => $name)));
            $anchor = strtolower($name);
            $this->output->add_breadcrumb($href,$a_params,$attributes,$anchor);
        }

        $title_path = '';
        while ($i < $n) {
            $directory .= '/'.$path_components[$i];
            $name = $this->vname($directory);
            $a_params[PARAM_PATH] = $directory;
            $title_path .= (empty($title_path)) ? $name : '/'.$name;
            $attributes = array('title' => t('filemanager_navigate_to','admin',array('{DIRECTORY}' => $title_path)));
            $anchor = strtolower($name);
            $this->output->add_breadcrumb($href,$a_params,$attributes,$anchor);
            ++$i;
        }
    } // show_breadcrumbs()


    /** show a menu that is equivalent with the root directory
     *
     * @param string $current_path indicator for highlighting the current directory subtree
     * @return void menu is displayed via $this->output
     */
    function show_menu($current_path='') {
        global $WAS_SCRIPT_NAME,$USER;
        $path_components = $this->explode_path($current_path);
        $current = '/'.$path_components[0];
        if ((count($path_components) > 1) && ($path_components[0] == 'users') && ($path_components[1] == $USER->path)) {
            $current .= '/'.$USER->path;
        }
        $this->output->add_menu('<h2>'.t('menu','admin').'</h2>');
        $this->output->add_menu('<ul>');
        $a_params = array('job' => $this->job,'task' => TASK_CHANGE_DIRECTORY);
        $entries = $this->get_entries_root();

        foreach($entries as $name => $entry) {
            $attributes = array('title' => $entry['title']);
            $anchor = $entry['vname'];
            $a_params[PARAM_PATH] = $entry['path'];
            if ($entry['path'] == $current) {
                $attributes['class'] = 'current';
            }
            $this->output->add_menu('  <li>'.html_a($WAS_SCRIPT_NAME,$a_params,$attributes,$anchor));
        }
        $this->output->add_menu('</ul>');
    } // show_menu()


    /** show a dialog that ask the user to confirm a mass file delete
     *
     * Show a list of files from $entries_to_delete and ask the user to confirm
     * with [Delete] button or to cancel with [Cancel] button. The names of the files
     * to delete are communicated via hidden fields. Once the form is submitted the
     * data is validated against the existing files in the directory $path.
     *
     * @param string $path the working directory
     * @param array $entries_to_delete an array with directory entries identifying the files to delete keyed by name
     * @return dialog is displayed via $this->output
     */
    function show_dialog_confirm_delete_files($path,$entries_to_delete) {
        global $WAS_SCRIPT_NAME;
        $dialogdef = array(
            'confirm' => array(
                'type' => F_INTEGER,
                'name' => 'confirm',
                'value' => '1',
                'hidden' => TRUE
                )
            );
        $n = count($entries_to_delete);
        $params = array('{COUNT}' => strval($n),'{PATH}' => htmlspecialchars($this->vpath($path)));
        $this->output->add_content('<h2>'.t('filemanager_delete_file_header','admin').'</h2>');
        $this->output->add_content(t(($n == 1) ? 'filemanager_delete_file_explanation' :
                                                 'filemanager_delete_files_explanation','admin',$params));
        $this->output->add_content('<ul>');
        $index = 0;
        foreach ($entries_to_delete as $filename => $entry) {
            $name = sprintf('%s%d',PARAM_FILENAME,$index++);
            $dialogdef[$name] = array(
                'type' => F_ALPHANUMERIC,
                'name' => $name,
                'value' => $entry['name'],
                'hidden' => TRUE);
            $this->output->add_content('  <li class="level0">'.htmlspecialchars($entry['vname']));
        }
        $this->output->add_content('</ul>');
        $this->output->add_content(t('delete_are_you_sure','admin'));
        $dialogdef[PARAM_FILENAMES] = array(
                'type' => F_INTEGER,
                'name' => PARAM_FILENAMES,
                'value' => $index,
                'hidden' => TRUE);
        $dialogdef['button_delete'] = dialog_buttondef(BUTTON_DELETE);
        $dialogdef['button_cancel'] = dialog_buttondef(BUTTON_CANCEL);
        $href = href($WAS_SCRIPT_NAME,array('job' => $this->job, 
                                            'task' => TASK_REMOVE_MULTIPLE_FILES,
                                            PARAM_PATH => $path));
        $this->output->add_content(dialog_quickform($href,$dialogdef));
        $this->show_breadcrumbs($path);
    } // show_dialog_confirm_delete_files()


    /** show a dialog that ask the user to confirm the removal of a directory
     *
     * Show the first directory from $entries_to_delete and ask the user to confirm
     * with [Delete] button or to cancel with [Cancel] button. The name of the directory
     * to delete is part of the href rather than a POSTed field. We only allow a single
     * directory to be removed at a time.
     *
     * @param string $path the working directory
     * @param array $entries_to_delete an array with directory entries identifying the files to delete keyed by name
     * @return dialog is displayed via $this->output
     */
    function show_dialog_confirm_delete_directory($path,$entries_to_delete) {
        global $WAS_SCRIPT_NAME;
        $dialogdef = array(
            'confirm' => array(
                'type' => F_INTEGER,
                'name' => 'confirm',
                'value' => '1',
                'hidden' => TRUE
                ),
            'button_delete' => dialog_buttondef(BUTTON_DELETE),
            'button_cancel' => dialog_buttondef(BUTTON_CANCEL)
            );

        $params = array('{PATH}' => htmlspecialchars($this->vpath($path)));
        $this->output->add_content('<h2>'.t('filemanager_delete_directory_header','admin').'</h2>');
        $this->output->add_content(t('filemanager_delete_directory_explanation','admin',$params));
        $this->output->add_content('<ul>');
        $entry = reset($entries_to_delete);
        $this->output->add_content('  <li class="level0">'.htmlspecialchars($entry['vname']));
        $this->output->add_content('</ul>');
        $this->output->add_content(t('delete_are_you_sure','admin'));
        $href = href($WAS_SCRIPT_NAME,array('job' => $this->job, 
                                            'task' => TASK_REMOVE_DIRECTORY,
                                            PARAM_PATH => $entry['path']));
        $this->output->add_content(dialog_quickform($href,$dialogdef));
        $this->show_breadcrumbs($path);
    } // show_dialog_confirm_delete_directory()


    /** workhorse function that actually deletes files, and possibly the corresponding thumbnails
     *
     * This routine deletes the files specified in the array $entries from directory $path.
     * If a thumbnail-file exists (ie. a file with a similar name but with the THUMBNAIL_PREFIX prepended),
     * it is deleted too.
     *
     * @param string $path the directory containing the files to delete
     * @param array $entries list of files to delete
     * @return bool TRUE on success, FALSE on error (+ messages written to log)
     * @uses $CFG
     */
    function delete_files($path,$entries) {
        global $CFG;
        $errors = 0; // assume success
        foreach($entries as $entryname => $entry) {
            $entrypath = sprintf('%s/%s',$path,$entryname);
            $full_entrypath = $CFG->datadir.$entrypath;
            if (@unlink($full_entrypath)) {
                logger(sprintf("%s.%s(): success unlinking '%s'",__CLASS__,__FUNCTION__,$entrypath),WLOG_DEBUG);
            } else {
                logger(sprintf("%s.%s(): cannot unlink '%s'",__CLASS__,__FUNCTION__,$entrypath));
                ++$errors;
            }
            $thumbpath = sprintf('%s/%s%s',$path,THUMBNAIL_PREFIX,$entryname);
            $full_thumbpath = $CFG->datadir.$thumbpath;
            if (is_file($full_thumbpath)) {
                if (@unlink($full_thumbpath)) {
                    logger(sprintf("%s.%s(): success unlinking '%s'",__CLASS__,__FUNCTION__,$thumbpath),WLOG_DEBUG);
                } else {
                    logger(sprintf("%s.%s(): cannot unlink '%s'",__CLASS__,__FUNCTION__,$thumbpath));
                    ++$errors;
                }
            } else {
                logger(sprintf("%s.%s(): no thumbnail '%s' exists",__CLASS__,__FUNCTION__,$thumbpath),WLOG_DEBUG);
            }
        }
        return ($errors == 0) ? TRUE : FALSE;
    } // delete_files()


    /** workhorse function that actually removes directories
     *
     * This routine first deletes the files "index.html" and "THUMBNAIL_PREFIX*" in the directory to remove
     * and subsequently the directory itself. This is more or less the reverse of the mkdir function
     * (see {@link task_add_directory()}) but with a twist: we consider any remaining thumbnails as trash
     * and we will happily delete those without further ado.
     *
     * If anything goes wrong, the routine returns FALSE and some details are written to the logfile.
     * Note that if the directory somehow contains symlinks or devices or named pipes we bail out: we
     * cannot handle those kinds of directory entries.
     *
     * Note that the routine is able to delete an array of directories, even though
     * it is currently called/used with only a single entry. We don't want to make it too easy to
     * remove many directories at once (an attempt to protect the user against herself).
     *
     * @param string $path the directory containing the directories to delete
     * @param array $entries list of subdirectories to delete, keyed by subdirectory name
     * @return bool TRUE on success, FALSE on error (+ messages written to log)
     * @uses $CFG
     */
    function delete_directory($path,$directories) {
        global $CFG;
        $errors = 0; // assume success

        foreach($directories as $directoryname => $directory) {
            $directorypath = sprintf('%s/%s',$path,$directoryname);
            $full_directorypath = $CFG->datadir.$directorypath;
            if (($handle = @opendir($full_directorypath)) === FALSE) {
                logger(sprintf("%s.%s(): cannot open directory '%s'",__CLASS__,__FUNCTION__,$directorypath));
                ++$errors;
                continue;
            }
            $spurious_entries = array();
            while (($filename = readdir($handle)) !== FALSE) {
                if (($filename == '.') || ($filename == '..')) { // skip uninteresting housekeeping files
                    continue;
                }
                $filepath = sprintf('%s/%s',$directorypath,$filename);
                $full_filepath = $CFG->datadir.$filepath;
                if (is_file($full_filepath)) {
                    if ((($filename == 'index.html') && (filesize($full_filepath) == 0)) || 
                        (substr($filename,0,strlen(THUMBNAIL_PREFIX)) == THUMBNAIL_PREFIX)) {
                        if (@unlink($full_filepath)) {
                            logger(sprintf("%s.%s(): success unlinking '%s'",__CLASS__,__FUNCTION__,$filepath),WLOG_DEBUG);
                        } else {
                            logger(sprintf("%s.%s(): cannot unlink '%s'",__CLASS__,__FUNCTION__,$filepath));
                            ++$errors;
                        }
                    } else {
                        $spurious_entries[] = $filepath;
                    }
                } else {
                    $spurious_entries[] = $filepath;
                }
            }
            @closedir($handle);
            if (count($spurious_entries) > 0) {
                foreach ($spurious_entries as $filepath) {
                    logger(sprintf("%s.%s(): spurious entry '%s' remains",__CLASS__,__FUNCTION__,$filepath));
                }
                logger(sprintf("%s.%s(): cannot remove non-empty '%s/'",__CLASS__,__FUNCTION__,$directorypath));
                ++$errors;
            } else {
                if (@rmdir($full_directorypath)) {
                    logger(sprintf("%s.%s(): success removing '%s/'",__CLASS__,__FUNCTION__,$directorypath),WLOG_DEBUG);
                } else {
                    logger(sprintf("%s.%s(): cannot remove '%s/'",__CLASS__,__FUNCTION__,$directorypath));
                    ++$errors;
                }
            }
        }
        return ($errors == 0) ? TRUE : FALSE;
    } // delete_directory()


    /** construct the (possibly translated) name of the last directory in the path
     *
     * This examines $path and returns a string with the last directory component.
     * There are a few special cases:
     *
     *  - the empty string indicates the root directory
     *  - /users/<userpath> maps to a (translated) string t('filemanager_personal')
     *  - /areas maps to a (translated) string t('filemanager_areas')
     *  - /groups maps to a (translated) string t('filemanager_groups')
     *  - /users maps to a (translated) string t('filemanager_users')
     *
     * All other variations yield the last component in the list of components.
     *
     * @param string $path the path to examine
     * @return string the (possibly translated) name of the last directory component
     */
    function vname($path) {
        global $USER;
        $vname = ''; // assume nothing

        $path_components = $this->explode_path($path);;
        $n = count($path_components);
        if ($n <= 1) {
            switch($path_components[0]) {
            case 'areas':  $vname = t('filemanager_areas', 'admin'); break;
            case 'users':  $vname = t('filemanager_users', 'admin'); break;
            case 'groups': $vname = t('filemanager_groups','admin'); break;
            case '':       $vname = t('filemanager_root',  'admin'); break;
            }
        } elseif ($n == 2) {
            $name = strval($path_components[1]);
            switch($path_components[0]) {
            case 'areas':
                $vname = $name; // fall back
                foreach($this->areas as $area_id => $area) {
                    if ($area['path'] == $name) {
                        $vname = $area['title'];
                        break;
                    }
                }
                break;

            case 'users':
                if ($name == $USER->path) {
                    $vname = t('filemanager_personal','admin');
                } else {
                    $table = 'users';
                    $fields = array('full_name');
                    $where = array('path' => $name);
                    if (($record = db_select_single_record($table,$fields,$where)) === FALSE) {
                        $vname = $name; // fall back
                    } else {
                        $vname = $record['full_name'];
                    }
                }
                break;

            case 'groups':
                $table = 'groups';
                $fields = array('full_name');
                $where = array('path' => $name);
                if (($record = db_select_single_record($table,$fields,$where)) === FALSE) {
                    $vname = $name; // fall back
                } else {
                    $vname = $record['full_name'];
                }
                break;
            }
        } else {
            $vname = $path_components[$n-1]; // last component of the path
        }
        return $vname;
    } // vname()


    /** shorthand for splitting a path into an array with path components
     *
     * This routine splits $path into components. Path components are
     * supposed to be delimited with a forward slash '/', but if somehow a
     * backslash '\' is encountered, it is translated to a forward slash first.
     * This means that it is impossible to have backslashes as part of a
     * path name, even though the underlying filesystem would happily accept
     * a component (filename or directoryname) with an embedded backslash.
     *
     * @param string $path the path to split
     * @return array broken down path
     */
    function explode_path($path) {
        return explode('/',trim(strtr($path,'\\','/'),'/'));
    } // explode_path()


    /** generate a list of (virtual) directories at the root level
     *
     * This generates a list of up to 4 'directories' which are
     * equivalent to 'My Files', 'Areas', 'Groups' and 'Users'.
     * Permissions and group memberships are taken into account, i.e.
     * if a user has no group memberships (and is not an account manager),
     * the 'Groups' directory is suppressed.
     *
     * @return array list of directories at the root level
     * @uses $USER
     * @uses $CFG
     */
    function get_entries_root() {
        global $USER,$CFG;
        // 1 -- My Files
        $myfiles = 'users/'.$USER->path;
        $entries = array(
            $myfiles => array(
                'name' => $USER->path,
                'path' => '/'.$myfiles,
                'vname' => t('filemanager_personal','admin'),
                'vpath' => t('filemanager_personal','admin'),
                'mtime' => filemtime($CFG->datadir.'/'.$myfiles),
                'size' => 0,
                'is_file' => FALSE,
                'title' => t('filemanager_personal_title','admin')
                )
            );

        // 2 -- Areas (if any are allowed)
        foreach($this->areas as $area_id => $area) {
            if ($USER->is_admin_pagemanager($area_id)) {
                $entries['areas'] = array(
                    'name' => 'areas',
                    'path' => '/areas',
                    'vname' => t('filemanager_areas','admin'),
                    'vpath' => t('filemanager_areas','admin'),
                    'mtime' => filemtime($CFG->datadir.'/areas'),
                    'size' => 0,
                    'is_file' => FALSE,
                    'title' => t('filemanager_areas_title','admin')
                    );
                break;
            }
        }

        // 3 - Groups (if any)
        if (($USER->has_job_permissions(JOB_PERMISSION_ACCOUNTMANAGER)) || (count($this->usergroups) > 0)) {
            $entries['groups'] = array(
                'name' => 'groups',
                'path' => '/groups',
                'vname' => t('filemanager_groups','admin'),
                'vpath' => t('filemanager_groups','admin'),
                'mtime' => filemtime($CFG->datadir.'/groups'),
                'size' => 0,
                'is_file' => FALSE,
                'title' => t('filemanager_groups_title','admin')
                );
        }
        // 4 - Users (always, because at least access to our own 'My Files')
        $entries['users'] = array(
            'name' => 'users',
            'path' => '/users',
            'vname' => t('filemanager_users','admin'),
            'vpath' => t('filemanager_users','admin'),
            'mtime' => filemtime($CFG->datadir.'/users'),
            'size' => 0,
            'is_file' => FALSE,
            'title' => t('filemanager_users_title','admin')
            );
        return $entries;
    } //get_entries_root()


    /** generate a list of (virtual) directories for areas the user can access
     *
     * This generates a list of (virtual) area directories for which the
     * user has access permissions. The list is ordered based on the
     * sort order (in the areas table).
     *
     * @return array list of available areas for this user
     * @uses $USER;
     * @uses $CFG;
     */
    function get_entries_areas() {
        global $USER,$CFG;
        $entries = array();
        if (count($this->areas) > 0) {
            foreach($this->areas as $area_id => $area) {
                if ($USER->is_admin_pagemanager($area_id)) {
                    $name = $area['path'];
                    $path = '/areas/'.$name;
                    $vname = $area['title'];
                    $vpath = t('filemanager_areas','admin').'/'.$vname;
                    $entries[$name] = array(
                        'name' => $name,
                        'path' => $path,
                        'vname' => $vname,
                        'vpath' => $vpath,
                        'mtime' => filemtime($CFG->datadir.$path),
                        'size' => 0,
                        'is_file' => FALSE,
                        'title' => t('filemanager_navigate_to','admin',array('{DIRECTORY}' => $vpath))
                        );
                }
            }
        }
        return $entries;
    } // get_entries_areas()


    /** generate a list of (virtual) directories for groups the user can access
     *
     * This generates a list of (virtual) group directories for which the
     * user has access permissions. The list is ordered by groupname.
     *
     * @return array list of available group directories for this user
     * @uses $USER;
     * @uses $CFG;
     */
    function get_entries_groups() {
        global $USER,$CFG;
        $entries = array();
        if ($USER->has_job_permissions(JOB_PERMISSION_ACCOUNTMANAGER)) {
            $table = 'groups';
            $fields = '*';
            $where = '';
            $order = array('full_name','groupname');
            if (($groups = db_select_all_records($table,$fields,$where,$order,'group_id')) === FALSE) {
                logger(sprintf('%s.%s(): cannot retrieve groups list: %s',__CLASS__,__FUNCTION__,db_errormessage()));
                $groups = array();
            }
            // uasort($groups,array("FileManager","cmp_groups")); // no necessay: the database did that already
        } else {
            $groups = $this->usergroups;
            uasort($groups,array("FileManager","cmp_groups"));
        }
        if (count($groups) > 0) {
            foreach($groups as $group_id => $group) {
                $name = $group['path'];
                $path = '/groups/'.$name;
                $vname = $group['full_name'];
                $vpath = t('filemanager_groups','admin').'/'.$vname;
                $entries[$name] = array(
                    'name' => $name,
                    'path' => $path,
                    'vname' => $vname,
                    'vpath' => $vpath,
                    'mtime' => filemtime($CFG->datadir.$path),
                    'size' => 0,
                    'is_file' => FALSE,
                    'title' => t('filemanager_navigate_to','admin',array('{DIRECTORY}' => $vpath))
                    );
            }
        }
        return $entries;
    } // get_entries_groups()


    /** generate a list of (virtual) directories for users this user can access
     *
     * This generates a list of (virtual) user directories for which this
     * user has access permissions. The list is ordered by full name.
     *
     * @return array list of available user directories for this user
     * @uses $USER;
     * @uses $CFG;
     */
    function get_entries_users() {
        global $USER,$CFG;
        $entries = array();
        if ($USER->has_job_permissions(JOB_PERMISSION_ACCOUNTMANAGER)) {
            $table = 'users';
            $fields = array('user_id','username','full_name','is_active','path');
            $where = '';
            $order = array('full_name','username');
            if (($users = db_select_all_records($table,$fields,$where,$order,'user_id')) === FALSE) {
                logger(sprintf('%s.%s(): cannot retrieve users list: %s',__CLASS__,__FUNCTION__,db_errormessage()));
                $users = array();
            }
        } else {
            $users = array(array(
                    'user_id' => $USER->user_id,
                    'username' => $USER->username,
                    'full_name' => $USER->full_name,
                    'is_active' => TRUE,
                    'path' => $USER->path
                    )
                );
        }
        if (count($users) > 0) {
            foreach($users as $user_id => $user) {
                $name = $user['path'];
                $path = '/users/'.$name;
                $vname = $user['full_name'];
                $vpath = t('filemanager_users','admin').'/'.$vname;
                $entries[$name] = array(
                    'name' => $name,
                    'path' => $path,
                    'vname' => $vname,
                    'vpath' => $vpath,
                    'mtime' => filemtime($CFG->datadir.$path),
                    'size' => 0,
                    'is_file' => FALSE,
                    'title' => t('filemanager_navigate_to','admin',array('{DIRECTORY}' => $vpath))
                    );
            }
        }
        return $entries;
    } // get_entries_users()


    /** generate a list of selected files and subdirectories in $path
     *
     * This creates an array containing a (filtered) listing of the directory $path,
     * keyed by filename.
     * we items are suppressed:
     *  - current directory '.'
     *  - parent directory '..'
     *  - index.html if it has size 0 (used to 'protect' directory against prying eyes)
     *  - THUMBNAIL_PREFIX* the thumbnails of images
     *  - symbolic links
     *
     * @param string $path the directory to list, e.g. '/areas/exemplum' or '/groups/faculty'
     * @return array list of available files and subdirectories
     * @uses $CFG;
     */
    function get_entries($path) {
        global $CFG,$WAS_SCRIPT_NAME;
        $directories = array();
        $files = array();
        $full_path = $CFG->datadir.$path;
        $vpath = $this->vpath($path);
        if (($handle = @opendir($full_path)) === FALSE) {
            logger(sprintf("%s.%s(): cannot open directory '%s'",__CLASS__,__FUNCTION__,$path));
            return array();
        }
        while (($entryname = readdir($handle)) !== FALSE) {
            $full_entryname = $full_path.'/'.$entryname;
            if (($entryname == '.') || ($entryname == '..')) {
                continue;  // skip the uninteresting housekeeping files
            } elseif (is_link($full_entryname)) {
                continue; // skip symlinks (they could be abused to trick us into leaking information)
            } elseif (is_file($full_entryname)) {
                $filesize = filesize($full_entryname);
                if (($entryname == 'index.html') && ($filesize == 0)) {
                    continue; // skip uninteresting empty "protection" file
                } elseif (substr($entryname,0,strlen(THUMBNAIL_PREFIX)) == THUMBNAIL_PREFIX) {
                    continue; // skip uninteresting thumbnail files
                } elseif (!$this->has_allowed_extension($entryname,$this->ext_allow_browse)) {
                    continue; // only files with allowable extensions for browsing are retrieved
                }
                // Even when the file can be browsed based on extension, doesn't mean it should be
                // there in first place. We allow the caller to identify existing files with 'forbidden' extensions
                $files[$entryname] = array(
                    'name' => $entryname,
                    'path' => $path.'/'.$entryname,
                    'vname' => $entryname,
                    'vpath' => $vpath.'/'.$entryname,
                    'mtime' => filemtime($full_entryname),
                    'size' => $filesize,
                    'is_file' => TRUE,
                    'is_allowed' => $this->has_allowed_extension($entryname,$this->ext_allow_upload),
                    'title' => t('filemanager_preview','admin',array('{FILENAME}' => htmlspecialchars($entryname)))
                    );
            } elseif (is_dir($full_entryname)) {
                $directories[$entryname] = array(
                    'name' => $entryname,
                    'path' => $path.'/'.$entryname,
                    'vname' => $entryname,
                    'vpath' => $vpath.'/'.$entryname,
                    'mtime' => filemtime($full_entryname),
                    'size' => 0,
                    'is_file' => FALSE,
                    'title' => t('filemanager_navigate_to','admin',array('{DIRECTORY}' => $entryname))
                    );
            } // else nothing to see here
        }
        closedir($handle);
        return $directories + $files;
    } // get_entries()


    /** translate a path to the corresponding virtual path
     *
     * This translates a path like '/users/webmaster/foo' into 'My Files/foo'
     * and '' into 'All Files', etc. The result of the translation is cached
     * in $this->vpaths, for future reference.
     *
     * @param string $path the path to translate
     * @return string the translated path
     */
    function vpath($path) {
        global $USER;
        if (isset($this->vpaths[$path])) {
            return $this->vpaths[$path];
        }
        $path_components = $this->explode_path($path);
        $n = count($path_components);

        // 1 -- root path (special case)
        if (($n <= 1) && ($path_components[0] == '')) {
            $path = '';
            $vpath = t('filemanager_root','admin');
            $this->vpaths[$path] = $vpath;
            $this->vpaths['/'] = $vpath;
            return $vpath;
        }
        // 2 -- handle paths starting with 'My Files', 'Areas', 'Groups' and 'Users'
        if (($n > 1) && ($path_components[0] == 'users') && ($path_components[1] == $USER->path)) {
            $path = '/users/'.$USER->path;
            if (isset($this->vpaths[$path])) {
                $vpath = $this->vpaths[$path];
            } else {
                $vpath = t('filemanager_personal','admin');
                $this->vpaths[$path] = $vpath;
            }
            $i = 2; // start at the first 'real' subdirectory after 'My Files', if any
        } else {
            switch($path_components[0]) {
            case 'areas':
            case 'groups':
            case 'users':
                $path = '/'.$path_components[0];
                if (isset($this->vpaths[$path])) {
                    $vpath = $this->vpaths[$path];
                } else {
                    $vpath = $this->vname($path);
                    $this->vpaths[$path] = $vpath;
                }
                $i = 1; // start at first 'real' subdirectory after 'Areas', 'Groups' or 'Users'
                break;
                
            default:
                return ''; // should not happen (only /areas, /groups or /users is allowed to start path)
                break;
            }
        }
        while ($i < $n) {
            $path .= '/'.$path_components[$i];
            $vpath .= '/'.$path_components[$i];
            if (!isset($this->vpaths[$path])) {
                $this->vpaths[$path] = $vpath;
            }
            ++$i;
        }
        return $vpath;
    } // vpath()


    /** convert an integer filesize to a human readable form
     *
     * This routine displays a file size in bytes, kilobytes, megabytes
     * or gigabytes or bytes with space-delimited groups of 3 digits
     * depending on the size. No decimals are used.
     *
     * @param int $size value to convert
     * @return string readable form of $size
     */
    function human_readable_size($size) {
        if ($size < 10000) {
            return strval($size);
        } elseif ($size < (10000 << 10)) {
            return sprintf("%d kB",$size >> 10);
        } elseif ($size < (10000 << 20)) {
            return sprintf("%d MB",$size >> 20);
        } elseif ($size < (10000 << 30)) {
            return sprintf("%d GB",$size >> 30);
        } else {
            return number_format(floatval($size),0,'.',' ');
        }
    } // human_readable_size()


    /** callback for comparing two group records
     *
     * This routine is used to order a list of groups by full_name, groupname.
     *
     * @param array $a first array with groupdata (straight copy from database record)
     * @param array $b second array with groupdata (straight copy from database record)
     * @return int 0 if equal, negative if $a < $b, positive if $a > $b
     */
    function cmp_groups($a,$b) {
        $a_full_name = (isset($a['full_name'])) ? $a['full_name'] : '';
        $b_full_name = (isset($b['full_name'])) ? $b['full_name'] : '';
        $retval = strnatcasecmp($a_full_name,$b_full_name);
        if ($retval == 0) {
            $a_groupname = (isset($a['groupname'])) ? $a['groupname'] : '';
            $b_groupname = (isset($b['groupname'])) ? $b['groupname'] : '';
            $retval = strnatcasecmp($a_groupname,$b_groupname);
        }
        return $retval;
    } // cmp_groups()


    /** sort directory entries
     *
     * @param array &$entries array with directory entries
     * @param int $sortorder
     * @return void entries array sorted
     * @todo it is a pity I cannot reference $this->sort from within the 6 cmp-functions...
     */
    function sort_entries(&$entries,$sort) {
        switch($sort) {
        case SORTBY_NONE:      return; break; // unsorted
        case SORTBY_FILE_ASC:  $cmp_function = "cmp_entries_byfile_asc";  break;
        case SORTBY_FILE_DESC: $cmp_function = "cmp_entries_byfile_desc"; break;
        case SORTBY_SIZE_ASC:  $cmp_function = "cmp_entries_bysize_asc";  break;
        case SORTBY_SIZE_DESC: $cmp_function = "cmp_entries_bysize_desc"; break;
        case SORTBY_DATE_ASC:  $cmp_function = "cmp_entries_bydate_asc";  break;
        case SORTBY_DATE_DESC: $cmp_function = "cmp_entries_bydate_desc"; break;
        default:               $cmp_function = "cmp_entries_byfile_asc";  break;
        }
        uasort($entries,array("FileManager",$cmp_function));
    } // sort_entries()


    /** callback for comparing two directory entries by filename
     *
     * Comparison between a file and a directory always shows directories first.
     *
     * @param array $a first entry
     * @param array $b second entry
     * @return int 0 if equal, negative if $a comes before $b, positive otherwise
     */
    function cmp_entries_byfile_asc($a,$b) {
        $a_is_file = (isset($a['is_file'])) ? $a['is_file'] : FALSE;
        $b_is_file = (isset($b['is_file'])) ? $b['is_file'] : FALSE;
        if ($a_is_file != $b_is_file) {
            return ($b_is_file) ? -1 : 1;
        }
        $a_vname = (isset($a['vname'])) ? $a['vname'] : '';
        $b_vname = (isset($b['vname'])) ? $b['vname'] : '';
        return strnatcasecmp($a_vname,$b_vname);
    } // cmp_entries_byfile_asc()


    /** callback for comparing two directory entries by filename (descending)
     *
     * Comparison between a file and a directory always shows directories first,
     * nevermind that we are sorting in descending order.
     *
     * @param array $a first entry
     * @param array $b second entry
     * @return int 0 if equal, negative if $a comes before $b, positive otherwise
     */
    function cmp_entries_byfile_desc($a,$b) {
        $a_is_file = (isset($a['is_file'])) ? $a['is_file'] : FALSE;
        $b_is_file = (isset($b['is_file'])) ? $b['is_file'] : FALSE;
        if ($a_is_file != $b_is_file) {
            return ($b_is_file) ? -1 : 1;
        }
        $a_vname = (isset($a['vname'])) ? $a['vname'] : '';
        $b_vname = (isset($b['vname'])) ? $b['vname'] : '';
        return strnatcasecmp($b_vname,$a_vname);
    } // cmp_entries_byfile_desc()


    /** callback for comparing two directory entries by size
     *
     * Comparison between a file and a directory always shows directories first.
     *
     * @param array $a first entry
     * @param array $b second entry
     * @return int 0 if equal, negative if $a comes before $b, positive otherwise
     */
    function cmp_entries_bysize_asc($a,$b) {
        $a_is_file = (isset($a['is_file'])) ? $a['is_file'] : FALSE;
        $b_is_file = (isset($b['is_file'])) ? $b['is_file'] : FALSE;
        if ($a_is_file != $b_is_file) {
            return ($b_is_file) ? -1 : 1;
        }
        $a_size = (isset($a['size'])) ? $a['size'] : 0;
        $b_size = (isset($b['size'])) ? $b['size'] : 0;
        if ($a_size != $b_size) {
            return $a_size - $b_size;
        }
        $a_vname = (isset($a['vname'])) ? $a['vname'] : '';
        $b_vname = (isset($b['vname'])) ? $b['vname'] : '';
        return strnatcasecmp($a_vname,$b_vname);
    } // cmp_entries_bysize_asc()


    /** callback for comparing two directory entries by size (descending)
     *
     * Comparison between a file and a directory always shows directories first,
     * nevermind that we are sorting in descending order.
     *
     * @param array $a first entry
     * @param array $b second entry
     * @return int 0 if equal, negative if $a comes before $b, positive otherwise
     */
    function cmp_entries_bysize_desc($a,$b) {
        $a_is_file = (isset($a['is_file'])) ? $a['is_file'] : FALSE;
        $b_is_file = (isset($b['is_file'])) ? $b['is_file'] : FALSE;
        if ($a_is_file != $b_is_file) {
            return ($b_is_file) ? -1 : 1;
        }
        $a_size = (isset($a['size'])) ? $a['size'] : 0;
        $b_size = (isset($b['size'])) ? $b['size'] : 0;
        if ($a_size != $b_size) {
            return $b_size - $a_size;
        }
        $a_vname = (isset($a['vname'])) ? $a['vname'] : '';
        $b_vname = (isset($b['vname'])) ? $b['vname'] : '';
        return strnatcasecmp($b_vname,$a_vname);
    } // cmp_entries_bysize_desc()


    /** callback for comparing two directory entries by mtime
     *
     * Comparison between a file and a directory always shows directories first.
     *
     * @param array $a first entry
     * @param array $b second entry
     * @return int 0 if equal, negative if $a comes before $b, positive otherwise
     */
    function cmp_entries_bydate_asc($a,$b) {
        $a_is_file = (isset($a['is_file'])) ? $a['is_file'] : FALSE;
        $b_is_file = (isset($b['is_file'])) ? $b['is_file'] : FALSE;
        if ($a_is_file != $b_is_file) {
            return ($b_is_file) ? -1 : 1;
        }
        $a_date = (isset($a['mtime'])) ? $a['mtime'] : 0;
        $b_date = (isset($b['mtime'])) ? $b['mtime'] : 0;
        if ($a_date != $b_date) {
            return $a_date - $b_date;
        }
        $a_vname = (isset($a['vname'])) ? $a['vname'] : '';
        $b_vname = (isset($b['vname'])) ? $b['vname'] : '';
        return strnatcasecmp($a_vname,$b_vname);
    } // cmp_entries_bydate_asc()


    /** callback for comparing two directory entries by date (descending)
     *
     * Comparison between a file and a directory always shows directories first,
     * nevermind that we are sorting in descending order.
     *
     * @param array $a first entry
     * @param array $b second entry
     * @return int 0 if equal, negative if $a comes before $b, positive otherwise
     */
    function cmp_entries_bydate_desc($a,$b) {
        $a_is_file = (isset($a['is_file'])) ? $a['is_file'] : FALSE;
        $b_is_file = (isset($b['is_file'])) ? $b['is_file'] : FALSE;
        if ($a_is_file != $b_is_file) {
            return ($b_is_file) ? -1 : 1;
        }
        $a_date = (isset($a['mtime'])) ? $a['mtime'] : 0;
        $b_date = (isset($b['mtime'])) ? $b['mtime'] : 0;
        if ($a_date != $b_date) {
            return $b_date - $a_date;
        }
        $a_vname = (isset($a['vname'])) ? $a['vname'] : '';
        $b_vname = (isset($b['vname'])) ? $b['vname'] : '';
        return strnatcasecmp($b_vname,$a_vname);
    } // cmp_entries_bydate_desc()


    /** construct a dialog definition for adding (uploading) files
     *
     * this constructs an array which defines a file(s) upload dialog.
     * Note that we make a subtle difference between a single-file upload and
     * a multifile upload: I think it looks stupid to start numbering a list
     * of files to upload when there is in fact only a list of exactly 1 file(s).
     * The cost is minimal: two extra strings in the translation file.
     *
     * @param int $num_files the maximum number of file upload fields to add to the dialog (default 1)
     * @return array with dialog definition keyed on field name
     */
    function get_dialogdef_add_files($num_files=1) {
        $num_files = max(1,min(512,intval($num_files))); // sane limits: $num_files between 1 and (arbitrary) 512
        $upload_max_filesize = ini_get_int('upload_max_filesize');
        $dialogdef = array(
            'MAX_FILE_SIZE' => array(
                'type' => F_INTEGER,
                'name' => 'MAX_FILE_SIZE',
                'value' => $upload_max_filesize,
                'hidden' => TRUE
                )
            );
        if ($num_files == 1) {
            $dialogdef['filename'] = array(
                'type' => F_FILE,
                'name' => 'filename',
                'columns' => 50,
                'label' => t('filemanager_add_file_label','admin'),
                'title' => t('filemanager_add_file_title','admin'),
                'value' => ''
                );
        } else {
            for ($i=1 ; $i <= $num_files; ++$i) {
                $field = sprintf('filename%d',$i);
                $params = array('{INDEX}' => strval($i));
                $dialogdef[$field] = array(
                    'type' => F_FILE,
                    'name' => $field,
                    'columns' => 50,
                    'label' => t('filemanager_add_files_label','admin',$params),
                    'title' => t('filemanager_add_files_title','admin',$params),
                    'value' => ''
                    );
            }
        }
        $dialogdef['button_save'] = dialog_buttondef(BUTTON_SAVE);
        $dialogdef['button_cancel'] = dialog_buttondef(BUTTON_CANCEL);
        return $dialogdef;
    } // get_dialogdef_add_files()


    /** scan a file for viruses
     *
     * this scans $path for viruses, returns 0 if file considerd clean, 1 for infected file,
     * or 2 if something else went wrong.
     *
     * If the flag $CFG->clamscan_mandatory is set, we consider the file infected if we are
     * not able to run the virus scanner (better safe than sorry). However, if no virusscanner
     * is configured at all ($CFG->clamscan_path is empty), we indicate a 'clean' file even
     * though we did not scan it. Rationale: it doesn't make sense to make scanning mandatory
     * and at the same time NOT configuring a scanner at all.
     *
     * If scanning succeeds and a virus is found we send an alert to the website owner address
     * (or the reply-to-address) immediately. Furthermore everything is logged.
     *
     * @param string $path the path of the file to scan
     * @param string $name the name of the file as provided by the uploader (from $_FILES)
     * @return int return 0 if clean, 1 if infected, 2 if other error
     * @uses $CFG
     * @uses $USER
     * @todo This routine is quite *nix-centric. I'm not sure how this would work other server platforms.
     *       Should we do something about that?
     * @todo maybe use MIME for sending alert if not 7bit message?
     */
    function virusscan($path,$name='') {
        global $CFG,$USER;
        $clamscan = $CFG->clamscan_path;
        $mandatory = $CFG->clamscan_mandatory;

        if ((empty($clamscan)) && (!($mandatory))) {
            logger(sprintf('%s.%s(): file %s (%s) unconditionally accepted because virusscanner is unconfigured',
                            __CLASS__,__FUNCTION__,$path,$name),WLOG_DEBUG);
            return 0;
        }

        // Make sure that the virusscanner can actually read this file
        if (!@chmod($path,0644)) {
            logger(sprintf('%s.%s(): chmod() %s (%s) to 0644 failed',__CLASS__,__FUNCTION__,$path,$name),WLOG_DEBUG);
        }

        // Construct the command to execute including redirecting stderr to stdout (a quirk in libclamav), see @todo 1
        $command = sprintf('%s %s 2>&1',$clamscan,escapeshellarg($path));
        $exit_code = 0;
        $lines = array();
        $dummy = @exec($command,$lines,$exit_code);

        if ($exit_code == 0) { // Pfew! File appears to be clean
            logger(sprintf('%s.%s(): %s (%s) considered clean',__CLASS__,__FUNCTION__,$path,$name),WLOG_DEBUG);
            return 0;
        }

        // Still here? Must have been something wrong.
        $forbidden = array(chr(10),chr(13),'\'');
        $sitename = str_replace($forbidden,'',$CFG->title);
        $params = array(
            '{OUTPUT}' => implode("\n",$lines),
            '{PATH}' => $path,
            '{FILENAME}' => $name,
            '{USERNAME}' => $USER->username,
            '{FULL_NAME}' => $USER->full_name,
            '{SITENAME}' => $sitename
            );
        if ($exit_code == 1) { // Darn. We have a virus
            $retval = 1;
            logger(sprintf('%s.%s(): %s (%s) infected: %s',
                            __CLASS__,__FUNCTION__,$path,$name,$params['{OUTPUT}']),WLOG_WARNING);
            $subject = t('filemanager_virus_mailsubject1','admin',$params);
            $message = t('filemanager_virus_mailmessage1','admin',$params);
        } elseif ($mandatory) { // we were not able to scan it and scanning is mandatory: consider the file infected
            $retval = 2;
            logger(sprintf('%s.%s(): virusscan of %s (%s) failed and scanning is mandatory: %s',
                            __CLASS__,__FUNCTION__,$path,$name,$params['{OUTPUT}']),WLOG_WARNING);
            $subject = t('filemanager_virus_mailsubject2','admin',$params);
            $message = t('filemanager_virus_mailmessage2','admin',$params);
        } else {
            logger(sprintf('%s.%s(): accepted file %s (%s) even though the (optional) virusscanning failed: %s',
                            __CLASS__,__FUNCTION__,$path,$name,$params['{OUTPUT}']));
            return 0; // if not mandatory, pretend the file is clean even if clamscan totally failed
        }
        // Still here? Then we have an alert to send. Here we go.

        /** make sure utility routines for creating/sending email messages are available */
        require_once($CFG->progdir.'/lib/email.class.php');

        $email = new Email;
        $mailto = (empty($CFG->website_replyto_address)) ? $CFG->website_from_address : $CFG->website_replyto_address;
        $email->set_mailto($mailto,$CFG->title);
        $email->set_subject($subject);
        $email->set_message($message);
        // inferred from RFC2156 that these are the right words to use... 
        $email->set_header('Priority','urgent'); // RFC2156: "normal" | "non-urgent" | "urgent"
        $email->set_header('Importance','high'); // RFC2156: "low" | "normal" | "high"
        $email->set_header('X-Priority','1 (Highest)'); // "1 (Highest)" | "3 (Normal)" | "5 (Lowest)"

        if ($email->send()) { // success, mail was accepted for delivery
            logger(sprintf('%s.%s(): success sending \'%s\' to %s',__CLASS__,__FUNCTION__,$subject,$mailto),WLOG_DEBUG);
        } else {
            logger(sprintf('%s.%s(): failure sending \'%s\' to %s',__CLASS__,__FUNCTION__,$subject,$mailto));
        }
        return $retval;
    } // virusscan()


    /** try to make sure that the extension of file $name makes sense or matches the actual filetype
     *
     * this checks or changes the $name of the file in line with the
     * mimetype of the actual file (as established by get_mimetype()).
     *
     * The reason to do this is to make it harder to 'smuggle in' files
     * with deceptive filenames/extensions. Quite often the extension is
     * used to determine the type of the file, even by browsers that should
     * know better. By uploading a malicious .PDF using an innocuous extension
     * like .TXT, a browser may be tricked into rendering that .PDF inline.
     * By changing the extension from .TXT to .PDF we can mitigate that risk,
     * at least a little bit. (People somehow trust an extension even though
     * they should know better and file(1) says so...)
     *
     * Strategy is as follows. If the mimetype based on the $name matches the
     * actual mimetype, we can simply allow the name provided.
     *
     * If there is a difference, we try to find an extension that maps to the
     * same mimetype as that of the actual file. IOW: we put more trust in the
     * mimetype of the actual file than we do in the mimetype suggested by the
     * extension.
     *
     * @param string $path full path to the actual file (from $_FILES[$i]['tmp_name'])
     * @param string $name the requested name of the file to examine (from $_FILES[$i]['name'])
     * @param string $type the suggested filetype of the file (from $_FILES[$i]['type'])
     * @return string the sanitised name and extension based on the file type
     */
    function sanitise_filetype($path,$name,$type) {
        // 0 -- initialise: isolate the $filename and $ext
        if (strpos($name,'.') === FALSE) { // not a single dot -> filename without extension
            $filename = $name;
            $extension = '';
        } else {
            $components = explode('.',$name);
            $extension = array_pop($components);
            $filename = implode('.',$components);
            unset($components);
        }

        // 1 -- does actual file mimetype agree with the file extension?
        $type_path = get_mediatype(get_mimetype($path,$name));
        $ext = strtolower($extension);
        $mimetypes = get_mimetypes_array();
        $type_name = (isset($mimetypes[$ext])) ? get_mediatype($mimetypes[$ext]) : 'application/octet-stream';
        if (strcmp($type_path,$type_name) == 0) {
            return $name;
        }

        // 2 -- No, we change the extension based on the actual mimetype of the file
        // 2A - lookup the first extension matching type, or use '' (which implies application/octet-stream)
        $new_extension = array_search($type_path,$mimetypes);
        if (($new_extension === FALSE) || (is_null($new_extension))) {
            $new_extension = '';
            logger(sprintf('%s.%s(): mimetype \'%s\' not recognised; using \'%s\' instead',
                            __CLASS__,__FUNCTION__,$type_path,$mimetypes[$new_extension]));
        }
        // 2B - avoid tricks with double extensions (eg. upload of "malware.exe.txt")
        if ($new_extension == '') {
            if ($type_name == 'application/octet-stream') {
                // preserve original extension and case because the original
                // extension will yield 'application/octet-stream' when served via file.php,
                // i.e. there is no need to lose the extension if it yields the same mimetype anyway
                $new_name = $name;
            } elseif (strpos($filename,'.') === FALSE) {
                // filename has no dot => 
                // no part of existing filename can be mistaken for an extension =>
                // don't add anything at all
                $new_name = $filename;
            } else {
                // bare $filename already contains an extension =>
                // add '.bin' to force 'application/octet-stream'
                $new_name = $filename.'.bin';
            }
        } else {
            $new_name = $filename.'.'.$new_extension;
        }
        logger(sprintf('%s.%s(): namechange %s -> %s (%s)',__CLASS__,__FUNCTION__,$name,$new_name,$type_path),WLOG_DEBUG);
        return $new_name;
    } // sanitise_filetype()


    /** construct a unique filename taking existing files into account
     *
     * this constructs a filename that is unique within the target directory.
     * If a file of the name $name already exists, a new name is constructed
     * from the basename of $name, an integer sequence number and the extension
     * of $name.
     *
     * There is no guarantee that this name will stay uniqe between the moment
     * we test for it and the moment the file is actually moved into place (a
     * classical race condition). However, the chance that this will be happening
     * is small enough I guess.
     *
     * @param string $directory the target directory for the file upload
     * @param string $name the (sanitised) name of the file
     * @return string a filename that is not yet existing in $directory
     * @todo Should we take care of the race condition in this routine?
     *       Should we already create an empty file or is that clutter?
     * @uses $CFG
     */
    function unique_filename($directory,$name) {
        global $CFG;
        if (!file_exists($CFG->datadir.$directory.'/'.$name)) {
            return $name;
        }
        if (strpos($name,'.') === FALSE) { // not a single dot -> filename without extension
            $filename = $name;
            $ext = '';
        } else {
            $components = explode('.',$name);
            $ext = '.'.array_pop($components);
            $filename = implode('.',$components);
            unset($components);
        }
        for ($i=0; $i<1024; ++$i) { // eventually we will get out of this loop
            $name = sprintf('%s-%d%s',$filename,$i,$ext);
            if (!file_exists($CFG->datadir.$directory.'/'.$name)) {
                return $name;
            }
        }
        logger(sprintf('%s.%s(): failed to create a unique sequential name (last try: %s); now use time of day',
                       __CLASS__,__FUNCTION__,$name));
        $name = sprintf('%s-%d%s',$filename,time(),$ext);
        return $name;
    } // unique_filename()


    /** construct a url that links to a file via /file.php
     *
     * This constructs a URL that links to a file, either
     * <code>
     * /file.php/path/to/file.txt
     * </code>
     * or
     * <code>
     * /file.php?file=/path/to/file.txt
     * </code>
     * depending on the global setting for proxy-friendly urls.
     * Note that we try to make the link as short as possible,
     * eg. by omitting the http:// part if possible (see $CFG->www_short).
     *
     * @parameter string $path the name of the file including path
     * @return string ready to use URL
     */
    function file_url($path) {
        global $CFG;
        if ($CFG->friendly_url) {
            $url = $CFG->www_short.'/file.php'.$path;
        } else {
            $url = $CFG->www_short.'/file.php?file='.$path;
        }
        return $url;
    } // file_url()


    /** try to create a thumbnail of the image in file $filename (best effort)
     *
     * this routine attempts to create a thumbnail of file $filename. First we
     * determine whether this is actually a graphics file and whether GD is available.
     * Then we decide if scaling is needed at all. If the file is of one of the
     * currently supported image formats, we load the file, resample it and write
     * the resulting (smaller) image to a file which name is prepended with the
     * thumbnail prefix.
     *
     * Design considerations:
     *
     *  - we use a square box (of $thumb_dimension x $thumb_dimension) to fit the images
     *  - we do not create thumbnails for images smaller than that square box
     *  - the aspect ratio is preserved
     *  - we use default quality settings for write jpeg and png thumbnails
     *  - all errors are logged, successes are logged to WLOG_DEBUG
     *  - this routine totally relies on GD
     *  - we try to extend our stay with set_time_limit() every time a thumbnail
     *    is created because image processing is quite time-consuming
     *
     * Note that this is a best effort: it something goes wrong, we do not try very hard
     * to correct the error; creating a thumbnail is considered 'nice to have'. As a matter
     * of fact we leave immediately on error (after cleaning up memory hogs, naturally).
     *
     * Note that we only use GD to create thumbnails. I did consider Imagick, but
     * eventually I decided against it because it appears that support for that extension
     * requires PHP 5.1.3. Perhaps we can add support in a later version of the FileManager.
     *
     * @param string $directory the working directory (relative to $CFG->datadir)
     * @param string $filename the name of the image file (including extension if any)
     * @return void
     */
    function make_thumbnail($directory,$filename) {
        global $CFG;
        $image_path = sprintf('%s%s/%s',$CFG->datadir,$directory,$filename);
        $thumb_path = sprintf('%s%s/%s%s',$CFG->datadir,$directory,THUMBNAIL_PREFIX,$filename);
        if (($image_info = @getimagesize($image_path)) === FALSE) {
            logger(sprintf('%s.%s(): file \'%s\' is not an image',__CLASS__,__FUNCTION__,$filename),WLOG_DEBUG);
            return;
        }

        // 1 -- do we have GD support?
        if ((!function_exists('imagecreatetruecolor')) ||
            (!function_exists('imagecopyresampled')) ||
            (!function_exists('imagedestroy'))) {
            logger(sprintf('%s.%s(): no GD-support (file=\'%s\')',__CLASS__,__FUNCTION__,$filename),WLOG_DEBUG);
            return;
        }

        // 2 -- is this file a supported image format anyway?
        $image_type = $image_info[2]; // one of the IMAGETYPE_XXX constants
        $supported_formats = imagetypes(); // bitmap of supported formats using IMG_XXX constants
        if ((($image_type == IMAGETYPE_GIF)  && (!($supported_formats & IMG_GIF))) ||
            (($image_type == IMAGETYPE_JPEG) && (!($supported_formats & IMG_JPG))) ||
            (($image_type == IMAGETYPE_PNG)  && (!($supported_formats & IMG_PNG)))) {
            logger(sprintf('%s.%s(): unsupported imagetype %d (file=\'%s\', mimetype=\'%s\')',
                           __CLASS__,__FUNCTION__,$image_type,$filename,$image_info['mime']),WLOG_DEBUG);
            return;
        }

        // 3 -- do we have to scale the image at all?
        $thumb_dimension = $CFG->thumbnail_dimension;
        $image_w = $image_info[0];
        $image_h = $image_info[1];
        $image_dimension = max($image_w,$image_h);
        if (($image_dimension <= $thumb_dimension)) {
            logger(sprintf('%s.%s(): no scaling necessary for file \'%s\', dimensions are: %dx%d',
                           __CLASS__,__FUNCTION__,$filename,$image_w,$image_h),WLOG_DEBUG);
            return; // nothing to do, image is already smaller than a thumbnail
        }

        // 4 -- prepare for thumbnail creation
        set_time_limit(30); // grad some more processing time (if possible)
        $thumb_w = intval(($image_w * $thumb_dimension) / $image_dimension);
        $thumb_h = intval(($image_h * $thumb_dimension) / $image_dimension);
        if (($thumb_img = @imagecreatetruecolor($thumb_w,$thumb_h)) === FALSE) {
            logger(sprintf('%s.%s(): imagecreatetruecolor(%d,%d) failed',__CLASS__,__FUNCTION__,$thumb_w,$thumb_h));
            return;
        }

        // 5 -- load image, resample/scale, store thumbnail for supported formats
        switch($image_type) {
        case IMAGETYPE_GIF:
            if (($image_img = @imagecreatefromgif($image_path)) === FALSE) {
                logger(sprintf('%s.%s(): imagecreatefromgif(\'%s\') failed',__CLASS__,__FUNCTION__,$filename));
                imagedestroy($thumb_img);
                return;
            }
            if (@imagecopyresampled($thumb_img,$image_img,0,0,0,0,$thumb_w,$thumb_h,$image_w,$image_h) === FALSE) {
                logger(sprintf('%s.%s(): imagecopyresampled() failed for \'%s\'',__CLASS__,__FUNCTION__,$filename));
                imagedestroy($thumb_img);
                imagedestroy($image_img);
                return;
            }
            if (!@imagegif($thumb_img,$thumb_path)) {
                logger(sprintf('%s.%s(): imagegif(\'%s%s\') failed',__CLASS__,__FUNCTION__,THUMBNAIL_PREFIX,$filename));
                imagedestroy($thumb_img);
                imagedestroy($image_img);
                return;
            }
            break;

        case IMAGETYPE_JPEG:
            if (($image_img = @imagecreatefromjpeg($image_path)) === FALSE) {
                logger(sprintf('%s.%s(): imagecreatefromjpeg(\'%s\') failed',__CLASS__,__FUNCTION__,$filename));
                imagedestroy($thumb_img);
                return;
            }
            if (@imagecopyresampled($thumb_img,$image_img,0,0,0,0,$thumb_w,$thumb_h,$image_w,$image_h) === FALSE) {
                logger(sprintf('%s.%s(): imagecopyresampled() failed for \'%s\'',__CLASS__,__FUNCTION__,$filename));
                imagedestroy($thumb_img);
                imagedestroy($image_img);
                return;
            }
            if (!@imagejpeg($thumb_img,$thumb_path)) { // use default quality (about 75 according to the PHP manual)
                logger(sprintf('%s.%s(): imagejpeg(\'%s%s\') failed',__CLASS__,__FUNCTION__,THUMBNAIL_PREFIX,$filename));
                imagedestroy($thumb_img);
                imagedestroy($image_img);
                return;
            }
            break;

        case IMAGETYPE_PNG:
            if (($image_img = @imagecreatefrompng($image_path)) === FALSE) {
                logger(sprintf('%s.%s(): imagecreatefrompng(\'%s\') failed',__CLASS__,__FUNCTION__,$filename));
                imagedestroy($thumb_img);
                return;
            }
            if (@imagecopyresampled($thumb_img,$image_img,0,0,0,0,$thumb_w,$thumb_h,$image_w,$image_h) === FALSE) {
                logger(sprintf('%s.%s(): imagecopyresampled() failed for \'%s\'',__CLASS__,__FUNCTION__,$filename));
                imagedestroy($thumb_img);
                imagedestroy($image_img);
                return;
            }
            if (!@imagepng($thumb_img,$thumb_path)) { // use default quality (compression)
                logger(sprintf('%s.%s(): imagepng(\'%s%s\') failed',__CLASS__,__FUNCTION__,THUMBNAIL_PREFIX,$filename));
                imagedestroy($thumb_img);
                imagedestroy($image_img);
                return;
            }
            break;

        default:
            imagedestroy($thumb_img);
            logger(sprintf('%s.%s(): unknown imagetype %d (\'%s\')',__CLASS__,__FUNCTION__,$image_type,$filename));
            return;
            break;
        }
        // 6 -- success
        imagedestroy($thumb_img);
        imagedestroy($image_img);
        logger(sprintf('%s.%s(): success creating %dx%d thumbnail \'%s/%s%s\'',__CLASS__,__FUNCTION__,
                               $thumb_w,$thumb_h,$directory,THUMBNAIL_PREFIX,$filename),WLOG_DEBUG);
        return;
    } // make_thumbnail()


    /** show a thumbnail of a single (image) file perhaps including clickable links for selection in FCK Editor
     *
     * This constructs a single clickable image with either a selection of the file (for FCK Editor, in
     * file/image browser mode) or a link to the file preview. If a file is not an image or otherwise no
     * suitable thumbnail is found, a large question mark is displayed (unknown.gif). Otherwise the existing
     * thumbnail is shown, maintaining the original aspect ratio. Either way the image is scaled to the currently
     * specified thumbail dimension so the image fits the corresponding DIV-tag.
     *
     * The strategy for finding a thumbnail is as follows:
     * - is the file to show an image at all? If not, show unknown.gif
     * - if the file zz_thumb_{filename.ext} exists, use that, otherwise
     * - if not AND the original file is smaller than a thumbnailm use the original file, otherwise
     * - use 'unknown.gif' after all.
     *
     * If the flag $delete_file is set, we also generate a checkbox and a delete icon. This means that
     * even in file/image browser mode files can be deleted by the user. In fact the file/image browser
     * is basically the same old filemanager.
     *
     * @param string $directory the current working directory (necessary to construct (full) paths)
     * @param array $entry information about the file to show, see {@link get_entries()} for the format
     * @param bool $delete_file if TRUE, user is allowed to delete the file (used for generating delete icon)
     * @param int $index a counter used to generate a unique field name for the checkbox
     * @param string $m optional margin for better code readability
     * @return output generated via $this->output
     * @uses $WAS_SCRIPT_NAME
     * @uses $CFG
     */
    function show_file_as_thumbnail($directory,$entry,$delete_file,$index,$m='') {
        global $WAS_SCRIPT_NAME,$CFG;


        // 1A -- prepare the clickable thumbnail (or 'unknown') image
        $filename = $entry['name'];
        $params = array('{FILENAME}' => $entry['vname'],
                        '{SIZE}' => $this->human_readable_size($entry['size']),
                        '{DATIM}' => strftime('%Y-%m-%d %T',$entry['mtime']));
        $image_path = sprintf('%s%s/%s',$CFG->datadir,$directory,$filename);
        if (($image_info = @getimagesize($image_path)) === FALSE) { // not an image, show 'unknown'
            $thumb_url = $CFG->progwww_short.'/graphics/unknown.gif';
            $thumb_width = $CFG->thumbnail_dimension;
            $thumb_height = $CFG->thumbnail_dimension;
            $properties = t('filemanager_title_thumb_file','admin',$params);
        } else {
            $image_width = $image_info[0];
            $image_height = $image_info[1];
            $image_dimension = max($image_width,$image_height);
            $thumb_path = sprintf('%s%s/%s%s',$CFG->datadir,$directory,THUMBNAIL_PREFIX,$filename);
            if (file_exists($thumb_path)) {
                $thumb_url = $this->file_url(sprintf('%s/%s%s',$directory,THUMBNAIL_PREFIX,$filename));
                $thumb_width = intval(($image_width * $CFG->thumbnail_dimension) / $image_dimension);
                $thumb_height = intval(($image_height * $CFG->thumbnail_dimension) / $image_dimension);
            } elseif ($image_dimension <= $CFG->thumbnail_dimension) {
                $thumb_url = $this->file_url(sprintf('%s/%s',$directory,$filename));
                $thumb_width = $image_width;
                $thumb_height = $image_height;
            } else {
                $thumb_url = $CFG->progwww_short.'/graphics/unknown.gif';
                $thumb_width = $CFG->thumbnail_dimension;
                $thumb_height = $CFG->thumbnail_dimension;
            }
            $params['{WIDTH}'] = strval($image_width);
            $params['{HEIGHT}'] = strval($image_height);
            $properties = t('filemanager_title_thumb_image','admin',$params);
        }
        $title = $properties;
        $img_attr = array('width' => $thumb_width, 'height' => $thumb_height, 'title' => $title);
        $anchor = html_img($thumb_url,$img_attr);

        // 1B -- choose between a file select (for FCK Editor) or file preview (generic file manager)
        if (($this->job == JOB_FILEBROWSER) || 
            ($this->job == JOB_IMAGEBROWSER) || 
            ($this->job == JOB_FLASHBROWSER)) {
            // Note: we depend on Javascript here, but since FCK Editor is also a Javascript application...
            // In other words: we would not be here in the first place if Javascript wasn't enabled.
            // (The file preview option does not depend on Javascript, see task_preview_file().)
            $url = $this->file_url($entry['path']);
            $title = t('filemanager_select','admin',array('{FILENAME}' => htmlspecialchars($entry['name'])));
            $a_attr = sprintf('title="%s" onclick="select_url(\'%s\'); return false;"',$title,$url);
            $html_a_tag = html_a("#",NULL,$a_attr,$anchor);
        } else {
            $a_params = sprintf('job=%s&task=%s&%s=%s',
                                     $this->job,
                                     TASK_PREVIEW_FILE,
                                     PARAM_PATH,rawurlencode($entry['path']));
            $url = $WAS_SCRIPT_NAME.'?'.htmlspecialchars($a_params);
            $a_attr = sprintf('title="%s" target="_blank" onclick="popup(\'%s\'); return false;"',$title,$url);
            $html_a_tag = html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
        }

        // 2 -- prepare checkbox and delete icon (if file deletion is allowed)
        if (($delete_file) && ($entry['is_file'])) {
            $checkbox_def = array(
                'type' => F_CHECKBOX,
                'name' => sprintf('%s%d',PARAM_FILENAME,$index),
                'options' => array($entry['name'] => ' '),
                'title' => t('filemanager_select_file_entry_title','admin'),
                'value' => '' // default is UNchecked
                );
            $widget = dialog_get_widget($checkbox_def);

            $title = t('filemanager_delete_file','admin',array('{FILENAME}'=>htmlspecialchars($entry['vname'])));
            $a_params = array('job' => $this->job,
                              'task' => TASK_REMOVE_FILE,
                              PARAM_PATH => $entry['path']);
            $a_attr = array('title' => $title);
            if ($this->output->text_only) {
                $anchor = html_tag('span','class="icon"','['.t('icon_delete_file_text','admin').']');
            } else {
                $img_attr = array('height'=>16,'width'=>16,'title'=>$title,'alt'=>t('icon_delete_file_alt','admin'));
                $anchor = html_img($CFG->progwww_short.'/graphics/delete.gif',$img_attr);
            }
            $icon = html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
        } else {
            $icon = '';
            $widget = '';
        }

        // 3 -- place all prepared items in a separate DIV
        $this->output->add_content($m.'<div class="thumbnail_container">');
        $this->output->add_content($m.'  <div class="thumbnail_image">');
        $this->output->add_content($m.'    '.$html_a_tag);
        $this->output->add_content($m.'  </div>');
        $this->output->add_content($m.'  <div class="thumbnail_delete">');
        $this->output->add_content($widget);
        $this->output->add_content($m.'    '.$icon);
        $this->output->add_content($m.'  </div>');
        $this->output->add_content($m.'  <div class="thumbnail_description">');
        $this->output->add_content($m.'    '.html_tag('span',
                                                      array('title' => $properties),
                                                      htmlspecialchars($entry['vname'])));
        $this->output->add_content($m.'  </div>');
        $this->output->add_content($m.'</div>');
    } // show_file_as_thumbnail()


    /** see if the filename extension is allowed
     *
     * Note that an 'empty' extension could be acceptable.
     *
     * @param string $filename the filename to examine
     * @param bool|array &$extensions array with allowable extensions or FALSE for none or TRUE for all
     * @return bool TRUE if allowed, FALSE otherwise
     */
    function has_allowed_extension($filename,&$extensions) {
        if (is_bool($extensions)) { // FALSE = none allowed, TRUE = all allowed
            return $extensions;
        } elseif (!is_array($extensions)) {
            return FALSE; // better safe than sorry, not allowed (shouldn't happen)
        }
        if (strpos($filename,'.') === FALSE) { // not a single dot -> filename without extension
            $ext = '';
        } else {
            $components = explode('.',$filename);
            $ext = array_pop($components);
            unset($components);
        }
        return (array_search($ext,$extensions) === FALSE) ? FALSE : TRUE;
    } // has_allowed_extension()


    /** convert a comma-delimited list of allowable extensions to an array (or FALSE if none are allowed)
     *
     * this converts the comma-delimited list of allowable filename extensions
     * to an array with one element per allowable extension OR to a boolean with value FALSE
     * if no allowable extensions are specified. The input is converted to lower case and also
     * spaces and dots are removed (in anticipation of users entering the bare extension with
     * the dot while we use the bare extension here. Also, it appears very natural to specify a
     * list including spaces (which we don't want), so there.
     *
     * @param string $allowed_extensions_list comma-delimited string with allowable extensions (or empty string)
     * @return bool|array array with acceptable extensions or FALSE if none are acceptable
     */
    function allowed_extensions($allowed_extensions_list) {
        $allowed_extensions_list = str_replace('.','',$allowed_extensions_list);
        $allowed_extensions_list = str_replace(' ','',$allowed_extensions_list);
        return (empty($allowed_extensions_list)) ? FALSE : explode(',',strtolower($allowed_extensions_list));
    } // allowed_extensions()

} // FileManager

?>