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

/** /program/main_file.php - workhorse for serving files
 *
 * This file deals with serving files
 * It is included and called from /file.php.
 *
 * The work is done in {@link main_file()}.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: main_file.php,v 1.3 2011/09/21 18:54:19 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** main program for serving files
 *
 * this routine is called from /file.php.
 *
 * This routine is responsible for serving files to the visitor.
 * These files are stored in a (virtual) file hierarchy that looks
 * like this.
 *
 * <pre>
 * /areas/areaname
 *       /another
 *       /stillmore
 *       ...
 * /users/username
 *       /another
 *       /stillmore
 *       ...
 * /groups/groupname
 *        /another
 *        /stillmore
 *        ...
 * /websiteatschool/program
 *                 /manual
 *                 /languages
 * </pre>
 *
 * This structure maps to the real file system as follows.  The (virtual)
 * directories /areas, /users and /groups correspond to the fysical
 * directories {$CFG->datadir}/areas, {$CFG->datadir}/users and
 * {$CFG->datadir}/groups respectively. The subdirectories correspond to
 * a (unique) area, user or group and serve as a file repository for that
 * area, user or group.
 *
 * The (virtual) top-level directory /websiteatschool is a special case.
 * It is used to serve the currently running website program code and the
 * user-defined translations of active languages.
 *
 * Before any file is transmitted to the visitor the access privileges
 * are checked.  The following rules apply.
 *
 * Access control for the /areas subdirectory
 *
 *  - an area must be active before any files are served
 *  - the visitor must have access to the private area if files are to be served
 *  - non-existing files yield a 404 Not Found error
 *  - non-existing areas also yield a 404 Not Found error
 *  - if the visitor has no access to the private area, also a 404 Not Found error is returned
 *
 * Access control for /users and /groups
 *
 *  - a user/group must be active before any files are served
 *  - non-existing users/groups yield 404 Not Found
 *  - non-existing files in existing directories also yield 404 Not Found
 *
 * Access control for /websiteatschool
 *
 *  - there is no limit on downloading the currently active program code or user-defined translations of active languages
 *
 * @todo the check on '/../' is inconclusive if the $path is encoded in UTF-8: the overlong
 *       sequence 2F C0 AE 2E 2F eventually yields 2F 2E 2E 2F or '/../'. Reference: RFC3629 section 10.
 * @return void file sent to the browser OR 404 not found on error
 */
function main_file() {
    global $USER;
    global $CFG;
    global $WAS_SCRIPT_NAME;
    global $LANGUAGE;

    /** initialise the program, setup database, read configuration, etc. */
    require_once($CFG->progdir.'/init.php');
    initialise();
    was_version_check(); // this never returns if versions don't match

    /** utility routines for manipulating files */
    require_once($CFG->progdir.'/lib/filelib.php');

    $filename = get_requested_filename();
    if (is_null($filename)) {
        error_exit404();
    }

    // 0 -- is the visitor logged in
    if (isset($_COOKIE[$CFG->session_name])) {
        /** dbsessionlib.php contains our own database based session handler */
        require_once($CFG->progdir.'/lib/dbsessionlib.php');
        dbsession_setup($CFG->session_name);
        if (dbsession_exists(magic_unquote($_COOKIE[$CFG->session_name]))) {
            session_start();
            if (!isset($_SESSION['session_counter'])) { // first time after login, record start time of session
                $_SESSION['session_counter'] = 1;
                $_SESSION['session_start'] = strftime("%Y-%m-%d %T");
            } else {
                $_SESSION['session_counter']++;
            }
        }
    }
    /** useraccount.class.php is used to define the USER object */
    require_once($CFG->progdir.'/lib/useraccount.class.php');

    if ((isset($_SESSION)) && (isset($_SESSION['user_id']))) {
        $USER = new Useraccount($_SESSION['user_id']);
        $USER->is_logged_in = TRUE;
        $_SESSION['language_key'] = $LANGUAGE->get_current_language(); // remember language set via _GET or otherwise
        session_write_close(); // we no longer need this here, everything relevant is now in $USER
    } else {
        $USER = new Useraccount();
        $USER->is_logged_in = FALSE;
    }

    //
    // 1 -- does the visitor want to download the source code
    //
    $path_components = explode('/',trim(strtr($filename,'\\','/'),'/'));
    if (strtolower($path_components[0]) == 'websiteatschool') {
        $source = (isset($path_components[1])) ? strtolower($path_components[1]) : 'program';
        download_source($source);
        exit;
    }
   
    //
    // 2 -- no. check out regular files
    //
    $path = '/'.implode('/',$path_components); // construct clean pathname


    // 2A -- check the 1st and 2nd component of the requested file
    switch ($path_components[0]) {
    case 'areas':
        $area_path = (isset($path_components[1])) ? $path_components[1] : '';
        $fields = array('area_id','is_private');
        $where = array('is_active' => TRUE, 'path' => $area_path);
        $table = 'areas';
        if (($record = db_select_single_record($table,$fields,$where)) === FALSE) {
            logger(sprintf("%s(): access denied for file '%s': non-existing or inactive area: return 404 Not Found",
                           __FUNCTION__,$path),WLOG_DEBUG);
            error_exit404($path);
        }
        $area_id = intval($record['area_id']);
        if ((db_bool_is(TRUE,$record['is_private'])) &&
            (!$USER->has_intranet_permissions(ACL_ROLE_INTRANET_ACCESS,$area_id))) {
            logger(sprintf("%s(): access denied for file '%s' in private area '%d': return 404 Not Found",
                            __FUNCTION__,$path,$area_id),WLOG_DEBUG);
            error_exit404($path);
        }
        break;        

    case 'users':
        $user_path = (isset($path_components[1])) ? $path_components[1] : '';
        $fields = array('user_id');
        $where = array('path' => $user_path,'is_active' => TRUE);
        $table = 'users';
        if (($record = db_select_single_record($table,$fields,$where)) === FALSE) {
            logger(sprintf("%s(): access denied for file '%s': non-existing or inactive user: return 404 Not Found",
                           __FUNCTION__,$path),WLOG_DEBUG);
            error_exit404($path);
        }
        break;

    case 'groups':
        $group_path = (isset($path_components[1])) ? $path_components[1] : '';
        $fields = array('group_id');
        $where = array('path' => $group_path,'is_active' => TRUE);
        $table = 'groups';
        if (($record = db_select_single_record($table,$fields,$where)) === FALSE) {
            logger(sprintf("%s(): access denied for file '%s': non-existing or inactive group: return 404 Not Found",
                           __FUNCTION__,$path),WLOG_DEBUG);
            error_exit404($path);
        }
        break;

    default:
        logger(sprintf("%s(): access denied for file '%s': subdirectory '%s' not recognised: return 404 Not Found",
                        __FUNCTION__,$path,$path_components[0]),WLOG_DEBUG);
        error_exit404($path);
        break;
    }

    // 2B -- still here? 1st and 2nd components are good but are there tricks furter down the line?
    if (!is_file($CFG->datadir.$path)) {
        logger(sprintf("%s(): access denied for file '%s': file does not exist: return 404 Not Found",
                       __FUNCTION__,$path),WLOG_DEBUG);
        error_exit404($path);
    }
    if (strpos($path,'/../') !== FALSE) {
        logger(sprintf("%s(): access denied for file '%s': no tricks with '/../': return 404 Not Found",
                       __FUNCTION__,$path),WLOG_DEBUG);
        error_exit404($path);
    }

    


    //
    // At this point we confident that the file exists within the data directory and also that
    // the visitor is allowed access to the file. Now send the file to the visitor.
    //
    $name = basename($path);
    if (($bytes_sent = send_file_from_datadir($path,$name)) === FALSE) {
        logger(sprintf("Failed to send '%s' using filename '%s'",$path,$name));
        $retval = FALSE;
    } else {
        logger(sprintf("Success sending '%s' using filename '%s', size = %d bytes",$path,$name,$bytes_sent));
        $retal = TRUE;
    }

    exit;
} // main_file()


/** generate an RFC1123-compliant date/time stamp
 *
 * This constructs a date/time stamp that is a fixed-length
 * subset of RFC1123. This is the preferred format in HTTP
 * (see RFC2616 section 3.3).
 *
 * The format is as follows:
 * <pre>
 * rfc1123-date = wkday "," SP date SP time SP "GMT"
 * date         = 2DIGIT SP month SP 4DIGIT             ; day month year (e.g., 02 Jun 1982)
 * time         = 2DIGIT ":" 2DIGIT ":" 2DIGIT          ; 00:00:00 - 23:59:59
 * wkday        = "Mon" | "Tue" | "Wed" | "Thu" | "Fri" | "Sat" | "Sun"
 * month        = "Jan" | "Feb" | "Mar" | "Apr" | "May" | "Jun" |
 *                "Jul" | "Aug" | "Sep" | "Oct" | "Nov" | "Dec"
 * </pre>
 *
 * If $timevalue is less or equal to zero, the current time is
 * used, otherwies $timevalue is interpreted as a standard unix timestamp.
 *
 * @param int $t the date/time value to use, or 0 for current time
 * @return string date/time stamp formatted according to RFC1123 (fixed length = 29)
 */
function rfc1123date($t=0) {
    $timevalue = ($t <= 0) ? time() : $t;
    return gmdate('D, d M Y H:i:s',$timevalue).' GMT';
} // rfc1123date()


/** the designated file is sent to the visitor
 *
 * This transmits the file {$CFG->datadir}$file from
 * the data directory to the visitor's browser, suggesting
 * the name $name. The file is transmitted in chunks 
 * (see {@link readfile_chunked()}).
 *
 * Several different variations are possible.
 *
 *  - by specifying a Time To Live of 0 seconds, this routine
 *    tries hard to defeat any caching by proxies
 *
 *  - if the download flag is TRUE, this routine tries to
 *    prevent the visitor's browser to render the file in-line
 *    suggesting downloading instead
 *
 * Quirks
 *
 *  - There appears to be a problem with Internet Explorer and https://
 *    and caching which requires a specific workaround. We simply check
 *    for 'https:' or 'http'.
 *
 *  - Adobe Acrobat Reader has a bad track record of infecting
 *    user's computers with malware when PDF's are rendered in-line.
 *    Therefore we force download for that kind of files.
 *
 *  - It is not easy to determine the exact mime type of files
 *    without resorting to a complex shadow-filesystem or a metadata
 *    table in the database. Therefore we 'guess' the mime type, either
 *    based on the information provided by the fileinfo PHP-module, or
 *    simply based on the extension of $file (which is not very reliable,
 *    but we have to do _something_). See {@link get_mimetype()} for details.
 *
 * @param string $file name of the file to send relative to $CFG->datadir
 * @param string $name filename to suggest to the visitor/visitor's browser
 * @param string $mimetype the mime type of the file; if not specified we use an educated guess
 * @param int $ttl time to live (aka maximum age) in seconds, 0 implies file is not cacheable
 * @param bool $download if TRUE we try to force a download
 * @uses get_mimetype()
 */
function send_file_from_datadir($file,$name,$mimetype='',$ttl=86400,$download=FALSE) {
    global $CFG;

    $path = $CFG->datadir.$file;
    $mtime = filemtime($path);
    $fsize = filesize($path);
    if (empty($mimetype)) {
        $mimetype = get_mimetype($path);
    }

    // Try to prevent inline rendering of PDF because of bugs in Adobe Reader
    $ext = strtolower(pathinfo($path,PATHINFO_EXTENSION));
    if (($mimetype == 'application/pdf') || ($ext == 'pdf')) {
        $download = TRUE;
        $ttl = 0;
    }

    $headers = array();
    $headers['Last-Modified'] = rfc1123date($mtime);
    $headers['Content-Disposition'] = sprintf('%s; filename=%s',
                                              ($download) ? 'attachment' : 'inline',
                                              urlencode($name));
    $headers['Content-Type'] = $mimetype;
    $headers['Content-Length'] = $fsize;
    $headers['Accept-Ranges'] = 'none';
    if ($ttl > 0) {
        $headers['Cache-Control'] = sprintf('max-age=%d',$ttl);
        $headers['Expires'] = rfc1123date(time() + $ttl);
        $headers['Pragma'] = '';
    } else {
        if (strtolower(substr($CFG->www,0,6)) == 'https:') {
            $ttl = 10;
            $headers['Cache-Control'] = sprintf('max-age=%d',$ttl);
            $headers['Expires'] = rfc1123date(time() - 86400); // 24h in the past
            $headers['Pragma'] = '';
        } else {
            $headers['Cache-Control'] = 'private, must-revalidate, max-age=0';
            $headers['Expires'] = rfc1123date(time() - 86400); // 24h in the past
            $headers['Pragma'] = 'no-cache';
        }
    }
    foreach($headers as $k => $v) {
        @header(trim($k.': '.$v));
    }
    $bytes = readfile_chunked($path);
    return $bytes;
} // send_file_from_datadir()


/** exit with a 404 not found error
 *
 * @param string $filename the file we were looking for and could not find
 * @return void this routine never returns
 */
function error_exit404($filename='') {
    header('HTTP/1.0 404 Not Found');
    echo t('file_not_found','',array('{FILE}' => htmlspecialchars($filename)));
    die();
} // error_exit404()


/** construct a zipfile with the current source and stream it to the visitor
 *
 * this routine streams a ZIP-archive to the visitor with either the current
 * websiteatschool program code or the selected manual. This routine is necessary
 * to comply with the provisions of the program license which basically says that the
 * source code of the running program must be made available.
 *
 * Note that it is not strictly necessary to also provide the manual, but this
 * routine can do that nevertheless.
 *
 * Note that we take special care not to download the (private) data directory
 * $CFG->datadir. Of course the datadirectory should live outside the document
 * root and certainly outside the /program directory tree, but accidents will
 * happen and we don't want to create a gaping security hole.
 *
 * If there are severe errors (e.g. no manual is available for download or an invalid
 * component was specified) the program exist immediately with a 404 not found error.
 * Otherwise the ZIP-archive is streamed to the user. If all goes well, we return TRUE,
 * if there were errors we immediately return TRUE (without finishing the task at hand
 * other than a perhasp futile attempt to properly close the  ZIP-archive). The last
 * error message from the Zip is logged.
 *
 * @param string $component either 'program' or 'manual' or 'languages'
 * @return void|bool Exit with 404 not found, OR TRUE and generated ZIP-file sent to user OR FALSE on error
 * @uses download_source_tree()
 */
function download_source($component) {
    global $CFG;
    global $LANGUAGE;
    $time_start = microtime();

    //
    // Step 0 -- decide what needs to be done
    //
    switch($component) {
    case 'program':
        $files = array('index.php','admin.php','cron.php','file.php','config-example.php');
        $directories = array('program' => $CFG->progdir);
        $excludes = array(realpath($CFG->datadir),realpath($CFG->progdir.'/manuals'));
        $archive = 'websiteatschool-program.zip';
        break;

    case 'manual':
        $language = $LANGUAGE->get_current_language();
        $manuals = $CFG->progdir.'/manuals';
        if (!is_dir($manuals.'/'.$language)) {
            if (!is_dir($manuals.'/en')) {
                logger(sprintf("Failed download 'websiteatschool/manual/%s': 404 Not Found",$language));
                error_exit404("websiteatschool/$component");
            } else {
                $language = 'en';
            }
        }
        $files = array();
        $directories = array('program/manuals/'.$language => $manuals.'/'.$language);
        $excludes = array(realpath($CFG->datadir));
        $archive = sprintf('websiteatschool-manual-%s.zip',$language);
        break;

    case 'languages':
        $files = array();
        $directories = array();
        $excludes = array();
        $archive = 'websiteatschool-languages.zip';
        $languages = $LANGUAGE->retrieve_languages();
        foreach($languages as $language_key => $language) {
            if ((db_bool_is(TRUE,$language['is_active'])) && (db_bool_is(TRUE,$language['dialect_in_file']))) {
                $directories['languages/'.$language_key] = $CFG->datadir.'/languages/'.$language_key;
            }
        }
        if (sizeof($directories) < 1) {
            logger(sprintf("Failed download websiteatschool/%s': 404 Not Found",$component));
            error_exit404('websiteatschool/'.$component);
        }
        break;

    default:
        logger(sprintf("Failed download websiteatschool/%s': 404 Not Found",$component));
        error_exit404('websiteatschool/'.$component);
        break;
    }

    //
    // Step 1 -- setup Ziparchive
    //
    include_once($CFG->progdir.'/lib/zip.class.php');
    $zip = new Zip;
    $comment = $CFG->www;
    if (!$zip->OpenZipstream($archive,$comment)) {
        $elapsed = diff_microtime($time_start,microtime());
        logger(sprintf("Failed download '%s' (%0.2f seconds): %s",$archive,$elapsed,$zip->Error));
        return FALSE;
    }

    //
    // Step 2 -- add files in the root directory (if any)
    //
    if (sizeof($files) > 0) {
        foreach($files as $file) {
            $path = $CFG->dir.'/'.$file;
            if (!file_exists($path)) {
                logger(sprintf("%s(): missing file '%s' in archive '%s'",__FUNCTION__,$path,$archive),WLOG_DEBUG);
                $data = sprintf('<'.'?'.'php echo "%s: file was not found"; ?'.'>',$file);
                $comment = sprintf('%s: missing',$file);
                if (!$zip->AddData($data,$file,$comment)) {
                    $elapsed = diff_microtime($time_start,microtime());
                    logger(sprintf("Failed download '%s' (%0.2f seconds): %s",$archive,$elapsed,$zip->Error));
                    $zip->CloseZip();
                    return FALSE;
                }
            } else {
                if (!$zip->AddFile($path,$file)) {
                    $elapsed = diff_microtime($time_start,microtime());
                    logger(sprintf("Failed download '%s' (%0.2f seconds): %s",$archive,$elapsed,$zip->Error));
                    $zip->CloseZip();
                    return FALSE;
                }
            }
        }
    }

    //
    // Step 3 -- add directories to archive
    //
    foreach($directories as $vpath => $path) {
        if (!download_source_tree($zip,$path,$vpath,$excludes)) {
            $elapsed = diff_microtime($time_start,microtime());
            logger(sprintf("Failed download '%s' (%0.2f seconds): %s",$archive,$elapsed,$zip->Error));
            $zip->CloseZip();
            return FALSE;
        }
    }

    //
    // Step 4 -- we're done
    //
    if (!$zip->CloseZip()) {
        $elapsed = diff_microtime($time_start,microtime());
        logger(sprintf("Failed download '%s' (%0.2f seconds): %s",$archive,$elapsed,$zip->Error));
        return FALSE;
    }
    logger(sprintf("Download '%s' (%0.2f seconds): success",$archive,diff_microtime($time_start,microtime())));
    return TRUE;
} // download_source()


/** workhorse function to recursively add most of a tree to a ZIP-archive
 *
 * this routine recursively adds the tree starting at $path to the opened archive $zip.
 * If a directory is in the list of excluded directories in $excludes it is skipped.
 *
 * @param object &$zip Zip-archive
 * @param string $path physical directory to add to archive
 * @param string $vpath virtual pathname for this physical directory
 * @param array &$excludes array with 'forbidden' subdirectories
 * @return bool TRUE on success, FALSE otherwise
 * @uses download_source_tree()
 */
function download_source_tree(&$zip,$path,$vpath,&$excludes) {
    if (in_array(realpath($path),$excludes)) {
        logger(sprintf("%s(): skipping excluded directory '%s'",__FUNCTION__,$path),WLOG_DEBUG);
        return TRUE;
    }

    if (($handle = opendir($path)) === FALSE) {
        logger(sprintf("%s(): cannot open directory '%s'",__FUNCTION__,$path),WLOG_DEBUG);
        return FALSE;
    }

    while (($file = readdir($handle)) !== FALSE) {
        if (($file == '.') || ($file == '..')) {
            continue;
        }
        if (is_file($path.'/'.$file)) {
            if (!$zip->AddFile($path.'/'.$file,$vpath.'/'.$file)) {
                logger(sprintf("%s(): cannot add file '%s/%s': %s",__FUNCTION__,$path,$file,$zip->Error),WLOG_DEBUG);
                closedir($handle);
                return FALSE;
            }
        } elseif (is_dir($path.'/'.$file)) {
            if (!download_source_tree($zip,$path.'/'.$file,$vpath.'/'.$file,$excludes)) {
                closedir($handle);
                return FALSE;
            }
        }
    }
    closedir($handle);
    return TRUE;
} // download_source_tree()


/** send a file to the visitor's browser in chunks
 *
 * This sends the file $path to the browser in manageable chunks.
 *
 * @param string $path fully qualified path of the file to send
 * @return bool|int FALSE on error, otherwise the number of bytes transmitted
 */
function readfile_chunked($path) {
    $chunk_size = 1048576; // we do 1024 kB per chunk
    $add_execution_time = max(30,intval(ini_get('max_execution_time')));
    $buffer = '';
    $count = 0;
    if (($fp = @fopen($path, 'rb')) == FALSE) {
        return FALSE;
    }
    while (!feof($fp)) {
        if (($buffer = @fread($fp, $chunk_size)) === FALSE) {
            logger(sprintf("%s(): error reading from '%s' (did %d bytes sofar)",__FUNCTION__,$path,$count),WLOG_DEBUG);
            @fclose($fp);
            return FALSE;
        }
        echo $buffer;
        $bytes = strlen($buffer);
        $count += $bytes;
        if ($bytes >= $chunk_size) { // we just read a full chunk, probably more to come; request more time...
            logger(sprintf("%s(): need more time for '%s' (%d bytes sofar)",__FUNCTION__,$path,$count),WLOG_DEBUG);
            set_time_limit($add_execution_time);
        }
        ob_flush();
        flush();
    }
    @fclose($fp);
    return $count;
} // readfile_chunked()

?>