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

/** /program/init.php - setup database connection, sessions, configuration, etc.
 *
 * This file is included from one of the main entry points.
 * The following subsystems and global variables are initialised:
 *  - connection to the database
 *  - session handler
 *  - $CFG
 *  - etc.
 *
 * This file is included at a fairly early stage in the process.
 * It does not rely on any regular libraries which are include()'ed lateron.
 * That is: all relevant libraries (such asl{@link waslib.php} are included
 * when necessary from within the function {@link initialise()}.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: init.php,v 1.3 2011/05/09 13:29:08 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** initialise the program, setup database, read configuration, etc.
 * @return void
 */
function initialise() {
    if (!defined('E_NONE')) {
        /** consistency in PHPs defined error levels requires E_NONE too, imho */
        define('E_NONE',0);
    }
    if (!function_exists('microtime')) {
        /** Kludge for environments without microtime()
         * @return string microseconds always 0.0 and current timestamp (seconds)
         */
        function microtime() { return "0.0 ".time(); }
    }
    /** This global keeps track of the script performance
     *
     * We record the start time of the script execution as soon as we are able to.
     * When all is said and done, we can do calculations about elapsed time, etc..
     * @global object $PERFORMANCE
     */
    global $PERFORMANCE;
    $PERFORMANCE->time_start = microtime();

    /** the maximum number of iterations in database loops (prevent circular reference) */
    define('MAXIMUM_ITERATIONS',50);

    /** the name of the script (entrypoint) that is currently running */
    global $WAS_SCRIPT_NAME;
    $WAS_SCRIPT_NAME = $_SERVER['SCRIPT_NAME'];

    /** This global variable holds all configuration parameters
     *
     * The following essential parameters are defined in /config.php
     * (see {@link config-example.php} for more information):
     * 
     *  - $CFG->db_type defines the database type.
     *  - $CFG->db_server defines the name of the database server.
     *  - $CFG->db_username holds the username to use when connecting to the server.
     *  - $CFG->db_password holds the password to use when connecting to the server.
     *  - $CFG->db_name holds the name of the database to use.
     *  - $CFG->prefix holds the tablename prefix.
     *  - $CFG->dir is the absolute directory path of 'index.php' and 'config.php'.
     *  - $CFG->www is the URI which corresponds with the directory $CFG->dir.
     *  - $CFG->progdir is the absolute path to the program directory
     *  - $CFG->progwww is the URI which corresponds with the directory $CFG->progdir.
     *  - $CFG->datadir is the absolute path to a private directory outside the document root.
     *
     * There is one optional parameter than can be specified in /config.php:
     *
     *  - $CFG->debug is a parameter to switch debugging ON
     *
     * Two additional parameters are derived from $CFG->www and $CFG->progwww
     * (see {@link calculate_uri_shortcuts()} for more information):
     *
     *  - $CFG->www_short (uri without scheme/authority when identical with progwww)
     *  - $CFG->progwww_short (uri without scheme/authority when identical with www)
     *
     * All other configuration parameters are retrieved from the database.
     *
     * @global object $CFG
     */
    global $CFG;

    /** this global object is associated with the logged in user
     *
     * @global object $USER
     */
    global $USER;

    /* keep error messages to ourselves; don't leak information while not debugging */
    error_reporting(E_NONE);

    if (!isset($CFG->debug)) {
        $CFG->debug = FALSE;
    }
    if ($CFG->debug) {
        error_reporting(E_ALL);
    }

    /** 'version.php' defines internal and external version numbers */
    require_once($CFG->progdir.'/version.php');

    /** 'utf8lib.php' contains essential routines for manipulating UTF-8 string */
    require_once($CFG->progdir.'/lib/utf8lib.php');

    /** This global object is used to access the database
     *
     * @global object $DB
     */
    global $DB;

    /** Manufacture an object of the database class corresponding with '$CFG->db_type' */
    include_once($CFG->progdir.'/lib/database/databaselib.php');
    $DB = database_factory($CFG->prefix,$CFG->db_type);

    if ($DB === FALSE) {
        error_exit('020');
    }
    if (!$DB->connect($CFG->db_server,$CFG->db_username,$CFG->db_password,$CFG->db_name)) {
        trigger_error($DB->errno.'/'.$DB->error);
        error_exit('030');
    }

    /* Trying to get rid of the magic (see also magic_unquote()) asap, ie. before reading from database */
    if (ini_get('magic_quotes_sybase') == 1) {
        error_exit('060');
    }
    if (get_magic_quotes_runtime() == 1) {
        set_magic_quotes_runtime(0);
    }

    /** utility routines, including shortcuts for database manupulation */
    require_once($CFG->progdir.'/lib/waslib.php');

    /** utility routines for generating HTML-code */
    require_once($CFG->progdir.'/lib/htmllib.php');

    /** utility routines for generating HTML-dialogs */
    require_once($CFG->progdir.'/lib/dialoglib.php');

    /* retrieve all configuration settings from the database, including the internal database version */
    $properties = get_properties();
    if ($properties !== FALSE) {
        foreach ($properties as $name => $value) {
            if (!isset($CFG->$name)) {
                $CFG->$name = $value;
            }
        }
        unset($name);
        unset($value);
    } else {
        $CFG->version = '?';
    }
    unset($properties);

    // Maybe calculate a shortcut for 'www' and 'progwww' (needed in appropriate_legal_notices() called in error_exit())
    list($CFG->www_short,$CFG->progwww_short) = calculate_uri_shortcuts($CFG->www,$CFG->progwww);

    /** this global array holds all valid and active languages
     *
     * @global object $LANGUAGE;
     */
    global $LANGUAGE;

    /** Load the code for the global LANGUAGE object */
    include_once($CFG->progdir.'/lib/language.class.php');
    $LANGUAGE = new Language;
} // initialise()


/** check version of PHP-files against version stored in database
 *
 * this checks the main WAS_VERSION (of files) against $CFG->version (database).
 * if all is well, we return TRUE indicating both version numbers match. If there
 * is a discrepancy it is logged and depending on parameter $exit_on_error we either
 * exit alltogether OR we return FALSE to indicate the version mismatch.
 *
 * Typical use is to call this routine near the start as follows
 * (e.g. in {@link main_index.php} or {@link main_file.php}):
 * <code>
 * ...
 * initialise();
 * was_version_check();
 * // Still here? Then version is OK
 * ...
 * </code>
 *
 * This forces an exit for the interfaces at the 'visitor' side. For the
 * webmaster it is different: even if the versions do not match, we want to
 * be able to login and do something about it via some sort of upgrade routine, e.g:
 * <code>
 * ...
 * initialise();
 * if (!was_version_check(FALSE)) {
 *   do_upgrade();
 * } else {
 *   do_regular_admin();
 * }
 * ...
 * </code>
 *
 * @param bool $exit_on_error if TRUE, this routine only returns if versions match, if FALSE a mismatch is fatal
 * @return bool|void TRUE if versions match, FALSE if mismatch and not $exit_on_error, no return at all otherwise
 */
function was_version_check($exit_on_error=TRUE) {
    global $CFG;
    $retval = TRUE; // assume success
    if ($CFG->version !=  WAS_VERSION) {
        $retval = FALSE;
        if ($exit_on_error) {
            logger("Fatal error 050: database version ({$CFG->version}) does not match code version (".WAS_VERSION.")");
            error_exit('050');
        } else {
            logger("Warning 050: database version ({$CFG->version}) does not match code version (".WAS_VERSION.
                   ") but continuing nevertheless; we might be on our way to an upgrade");
        }
    }
    return $retval;
} // was_version_check()


/** emergency exit of program in case there is something really, really wrong
 *
 * This routine outputs a short message and a 'cryptic' condition code
 * and exits the program. It is called when something goes horribly wrong
 * during the early stages of running the program, e.g. the database cannot
 * be opened or there is a version mismatch between the program code (the
 * .php-files) and the database. The complete condition code is the WAS
 * release number followed by a slash followed by the WAS version number
 * followed by a slash and the bare condition code. The message ends with
 * a link to about.html with 'Powered by' or 'Based on', depending on the
 * WAS original flag. Note that we try to show graphics (including logo) but
 * that we switch back to text-only if it is too early, ie. 
 * before {@link waslib.php} is included.
 *
 * Here is an overview of meaning of the condition codes used.
 *
 *  - 010: cannot find config.php, is W@S installed at all?
 *  - 015: cannot find program/main_XXXXX.php, is W@S installed at all?
 *  - 020: configuration error, invalid database type
 *  - 030: cannot connect to database, busy or configuration error?
 *  - 040: error accessing the database, is W@S installed at all?
 *  - 050: version mismatch, update to new version necessary
 *  - 060: magic_quotes_sybase is On
 *  - 070: there is no (default) node available in this (default) area
 *  - 080: there is no area available
 *  - 090: there is no valid theme available
 *
 * The condition code is numeric because it is easier to report for
 * non-English speaking users than a complicated English sentence.
 * (The language files are not yet loaded when error_exit() is called).
 *
 * @param string the bare condition code to report
 * @param string the title to show in the generated HTML-page
 * @return void this function never returns
 * @uses WAS_VERSION indicate internal version in 'cryptic' message
 * @uses $CFG
 * @todo do we really want to 'leak' a link to the main site?
 */
function error_exit($bare_condition_code,$page_title='Fatal Error') {
    global $CFG;
    $was_release = htmlspecialchars(WAS_RELEASE);
    $was_version = htmlspecialchars(WAS_VERSION);
    $anchor_self = '';
    if ((isset($CFG->www)) && (!empty($CFG->www))) {
        $anchor_self = "\n    <p>\n    <a href=\"{$CFG->www}\">{$CFG->www}</a>";
    }
    if (function_exists('appropriate_legal_notices')) {
        $poweredby = appropriate_legal_notices(FALSE,'');
    } else {
        $poweredby = sprintf('%s <a href="%s/about.html" target="_blank">Website@School<a>',
                         (WAS_ORIGINAL) ? 'Powered by' : 'Based on',$CFG->progwww);
    }
    echo <<<EOT
<html>
  <head>
    <title>$page_title</title>
  </head>
  <body>
    <h1>$page_title</h1>
    <b>There is a problem with this site.</b>
    <p>
    Please contact the site owner mentioning the following condition code:
    <p>
    <b>$was_release / $was_version / $bare_condition_code</b>
    <p>
    Thank you for your cooperation!$anchor_self
    <p>
    $poweredby
  </body>
</html>
EOT;
    exit(1);
} // error_exit()

/** Calculate the difference between two microtimes
 *
 * @param string starting time as a string (fractional seconds, space, seconds) 
 * @param string ending time as a string (fractional seconds, space, seconds)
 * @return double interval between the two times (in seconds)
 */
function diff_microtime($time_start, $time_stop) {
    list($msec,$sec) = explode(' ',$time_start);
    $t0 = (double) $msec + (double) $sec;
    list($msec,$sec) = explode(' ',$time_stop);
    $t1 = (double) $msec + (double) $sec;
    return $t1 - $t0;
} // diff_microtime()

?>