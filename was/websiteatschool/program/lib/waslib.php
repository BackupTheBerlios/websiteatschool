<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker <peter@berestijn.nl>
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

/** /program/lib/waslib.php - core functions
 *
 * This file provides various utility routines.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: waslib.php,v 1.21 2013/06/03 12:16:33 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** The constants CAPACITY_* are used for group memberships (see {@link accountmanagerlib.php}). */
define('CAPACITY_NONE',           0);
define('CAPACITY_PUPIL',          1);
define('CAPACITY_TEACHER',        2);
define('CAPACITY_PRINCIPAL',      3);
define('CAPACITY_MEMBER',         4);
define('CAPACITY_PROJECTLEAD',    5);
define('CAPACITY_TREASURER',      6);
define('CAPACITY_SECRETARY',      7);
define('CAPACITY_CHAIR',          8);
define('CAPACITY_EDITOR',         9);
define('CAPACITY_PUBLISHER',     10);
define('CAPACITY_CUSTOM1',       11);
define('CAPACITY_CUSTOM2',       12);
define('CAPACITY_CUSTOM3',       13);
define('CAPACITY_CUSTOM4',       14);
define('CAPACITY_CUSTOM5',       15);
define('CAPACITY_CUSTOM6',       16);
define('CAPACITY_CUSTOM7',       17);
define('CAPACITY_CUSTOM8',       18);
define('CAPACITY_CUSTOM9',       19);
define('CAPACITY_NEXT_AVAILABLE',20); // This constant should always be 1 higher than the highest real capacity


/** this circumvents the 'magic' in magic_quotes_gpc() by conditionally stripping slashes
 *
 * Magic quotes are a royal pain for portability. If magic quotes
 * are enabled, this function reverses the effect.
 * There are three PHP-parameters in php.ini affecting the magic:
 *  - the directive 'magic_quotes_runtime'
 *  - the directive 'magic_quotes_gpc'
 *  - the directive 'magic_quotes_sybase'
 *
 * This routine deals with undoing the effect of the latter two.
 * The effect of magic_quotes_runtime can be undone via
 * set_magic_quotes_runtime(0). This is done once at
 * program start (See {@link initialise()} in {@link init.php}).
 *
 * This routine should be used to unquote strings from
 * $_GET[], $_POST[] and $_COOKIE whenever they are needed.
 *
 * Important note: because third party subsystems may deal with
 * magic quotes on their own, it is a Bad Idea[tm] to globally
 * replace the contents of $_GET[], $_POST[] and $_COOKIE with
 * the unescaped values once at program start. Any subsystem
 * would be confused if magic_quotes_gpc() indicates that the
 * magic is in effect whereas in reality the magic was already
 * undone at program start. Yes, this yields a performance
 * penalty, but this magic was a mess right from the start.
 * Hopefully PHP6 will get rid of this magic for once and for all...
 *
 * @param string a string value that is conditionally unescaped
 * @return string the unescaped string 
 */
function magic_unquote($value) {
    if (is_string($value)) {
        if (ini_get('magic_quotes_sybase') == 1) {
            $value = str_replace('\'\'','\'',$value);
        } elseif (get_magic_quotes_gpc() == 1) {
            $value = stripslashes($value);
        }
    }
    return $value;
} // magic_unquote()


/** retrieve typed properties (name-value-pairs) from a table
 *
 * this retrieves the fields 'name', 'value' and 'type' from all records from $tablename 
 * that satisfy the condition in $where. The values, which are stored as strings in the
 * database,  are converted to their proper value type and stored in the resulting array, 
 * keyed by name. The following types are recognised:
 *
 *  - b  = boolean
 *  - d  = date ('yyyy-mm'dd', handled like a string)
 *  - dt = datetime ('yyyy-mm-dd hh:mm:ss', handled like a string)
 *  - f  = float
 *  - i  = integer
 *  - s  = string
 *  - t  = time ('hh:mm:ss', handled like a string)
 *
 * Note that we currently do not validate these properties, the assumption is that the values are valid (or empty).
 *
 * @param string $tablename the name of the table holding the properties
 * @param array|string $where which records do we need to select
 * @return bool|array FALSE on error, or an array with name-value-pairs
 */
function get_properties($tablename='config',$where='') {
    $fields = array('name','value','type');
    if (($records = db_select_all_records($tablename,$fields,$where)) === FALSE) {
        return FALSE;
    }
    $properties = array();
    foreach ($records as $record) {
        $name = $record['name'];
        switch($record['type']) {
        case 'b':
            $value = ($record['value']) ? TRUE : FALSE;
            break;

        case 'd':
        case 'dt':
        case 't':
        case 's':
            $value = $record['value'];
            break;

        case 'i':
            $value = (is_numeric($record['value'])) ? intval($record['value']) : NULL;
            break;

        case 'f':
            $value = (is_numeric($record['value'])) ? floatval($record['value']) : NULL;
            break;

        default:
            $value = $record['value'];
            break;
        }
        $properties[$name] = $value;
    }
    unset($records);
    return $properties;
} // get_properties()


/** try to eliminate the scheme and authority from the two main uri's
 *
 * This tries to get rid of the scheme and the authority in 'www' and 'progwww',
 * If these two elements are the same, it becomes possible to use
 * a shorter form of the uri when referencing files in 'progwww' from 'www'.
 *
 * If the scheme and the authority of 'www' and 'progwww' are the same,
 * the returned strings contain only the path elements. If scheme and authority
 * differ, they contain the same as 'www' and 'progwww' respectively.
 *
 * Examples:
 * www = 'http://www.example.com/site' and progwww = 'http://www.example.com/site/program'
 * yields www_short = '' and wwwprog_short = '/program'.
 *
 * www = 'http://www.example.com' and progwww = 'http://common.example.com/program'
 * yields www_short idential to www and progwww_short identical to progwww.
 *
 * The purpose is to be able to generate relative links, e.g. an image in 
 * /program/graphics/foo.jpg can be referred to like this
 * <code>
 * <img src="{$CFG->progwww_short}/graphics/foo.jpg"> or
 * <img src="/program/graphics/foo.jpg"> rather than
 * <img src="http://www.example.com/program/graphics/foo.jpg">
 * </code>
 *
 * Note that the comparison in this routine is not very fancy, it can be
 * easily fooled to consider scheme+authority to be different. However, since
 * this routine is only used to compare two values from config.php, it's
 * not likely to cause trouble.
 *
 * @param string $www the uri (scheme / authority / path) of the directory holding config.php
 * @param string $progwww the uri (scheme / authority / path) corresponding with the program directory
 * @return array the two short versions of www and progwww, if possible
 */
function calculate_uri_shortcuts($www,$progwww) {
    $www_short = $www;
    $progwww_short = $progwww;

    $i = strpos($www,'://');
    if ($i !== FALSE) {
        $i += 3; // skip the '://'
        $i = strpos($www,'/',$i);
        if ($i === FALSE) { // must be scheme+authority without a path then
            $i = strlen($www);
        }
        if (strncasecmp($www,$progwww,$i) == 0) { // KISS-compare scheme + authority in both uri's
            $www_short = substr($www,$i);
            $progwww_short = substr($progwww,$i);
        }
    }
    return array($www_short,$progwww_short);
} // calculate_uri_shortcuts()


/** convert a string representation of a date/time to a timestamp
 *
 * this is a crude date/time parser. We collect digits and convert
 * to integers. With the integers we fill an array with at least 6 integers,
 * corresponding to year, month, day, hours, minutes and seconds.
 * If there are less than six numbers in the source string the value 0 is used.
 * for the remaining elements. Note that a number in this context is always
 * a non-negative number because a dash (or minus) is considered a delimiter.
 *
 * Note that valid date/time values are limited to how many seconds can be represented
 * in a signed long integer, where 0 equates to 1970-01-01 00:00:00 (the Unix epoch).
 * The upper limit for a 32-bit int is some date in 2038 (only 30 years from now).
 *
 * @param string date/time in the form yyyy-mm-dd hh:mm:ss
 * @return bool|long unix timestamp (second since epoch) or FALSE on error
 */
function string2time($timestring) {
    $ascii_zero = ord('0');
    $num = array(0,0,0,0,0,0);
    $in_number = FALSE;
    $index = 0;
    $length = strlen($timestring);
    for ($i = 0; $i < $length; ++$i) {
        $x = ord(substr($timestring,$i,1)) - $ascii_zero;
        if ($in_number) {
            if ((0 <= $x) && ($x <= 9)) {
                $num[$index] = 10 * $num[$index] + $x;
            } else {
                $in_number = FALSE;
                ++$index;
            }
        } else {
            if ((0 <= $x) && ($x <= 9)) {
                $num[$index] = $x;
                $in_number = TRUE;
            }
        }
    }
    //          hour    min     sec     mon     day     year
    $t = mktime($num[3],$num[4],$num[5],$num[1],$num[2],$num[0]);
    if ($t == -1) { $t = FALSE; } // make PHP4 return value compatible with PHP5
    return $t;
} // string2time()

define('QUASI_RANDOM_DIGITS',10);
define('QUASI_RANDOM_HEXDIGITS',16);
define('QUASI_RANDOM_DIGITS_UPPER',36);
define('QUASI_RANDOM_DIGITS_UPPER_LOWER',62);

/** generate a string with quasi-random characters
 *
 * This generates a string of $length quasi-random characters.
 * The optional parameter $candidates determines which characters
 * are elegible. Popular choices for $candidates are:
 *
 *  - 10 (minimum): use only digits from 0,...,9
 *  - 16: use digits 0,...9 or letters A,...F
 *  - 36 (default): use digits 0,...,9 or letters A,...,Z
 *  - 62: use digits 0,...,9 or letters A,...,Z or letters a,...,z
 *
 * If $candidates is smaller than 10, 10 is used, if
 * $candidates is greater than 62 62 is used.
 *
 * Note that this is an ASCII-centric routine: we only use
 * plain ASCII letters and digits and nothing of the 64000 other
 * UNicode characters in the Basic Multilingual Plane. The reason
 * is simple: 7-bit ASCII characters have the best chance of getting
 * through communiocation channels unmangled so there.
 *
 * @param int length of the string to generate
 * @param int number of candidate-characters to choose from
 * @retun string the generated string of $length characters
 */
function quasi_random_string($length,$candidates=36) {
    static $alphanumerics = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $s = '';
    $max = max(9,min(61,--$candidates)); // make sure that 9 <= $max <= 61
    for($i=0; $i<$length; ++$i) {
        $s .= $alphanumerics{rand(0,$max)};
    }
    return $s;
} // quasi_random_string()


/** massage a message and generate a javascript alert()
 *
 * @param string message to display
 * @return string javascript code with alert() function call with properly escaped message string
 */
function javascript_alert($message) {
    $newlines = array("\r\n","\n","\r");
    return "alert(\"".str_replace($newlines,"\\n",addslashes($message))."\");";
} // javascript_alert()


/** unfold a possible multiline string
 *
 * This removes all linefeeds and carriage returns from a string
 * Typical use would be to strip a subject line in a mailmessage
 * from newlines which might interfere with proper sending of mail
 * headers.
 *
 * @param string the multiline string to strip
 * @param string (optional) the string to replace newlines
 * @return string the string with offending characters replaced
 */
function replace_crlf($multiline_string,$replacement='') {
    return str_replace(array("\n","\r"),$replacement,$multiline_string);
} // replace_crlf()


/** translation of phrases via a function with a very short name
 *
 * This is only a wrapper function for $LANGUAGE->get_phrase()
 *
 * @param string $phrase_key indicates the phrase that needs to be translated
 * @param string $full_domain (optional) indicates the text domain (perhaps with a prefix)
 * @param array $replace (optional) an assoc array with key-value-pairs to insert into the translation
 * @param string $location_hint (optional) hints at a directory location of language files
 * @param string $language (optional) target language
 * @return string translated string with optional values from array 'replace' inserted
 * @uses $LANGUAGE
 */
function t($phrase_key,$full_domain='',$replace='',$location_hint='',$language='') {
    global $LANGUAGE;
    return $LANGUAGE->get_phrase($phrase_key,$full_domain,$replace,$location_hint,$language);
} // t()


/** a simple function to log information to the database 'for future reference'
 *
 * This adds a message to the table log_messages, including a time, the remote address
 * and (of course) a message. See also the standard PHP-function syslog(). We use the
 * existing symbolic constants for priority. Default value is WLOG_INFO.
 *
 * Note that messages with a priority WLOG_DEBUG are only written to the log
 * if the global parameter $CFG->debug is TRUE. All other messages are simply
 * logged, no further questions asked.
 *
 * If the caller does not provide a user_id, this routine attempts to
 * read the user_id from the global $_SESSION array, i.e. we try to link
 * events to a particular user if possible.
 *
 * Note that with a field definition of varchar(150) there is room to store either
 * an IPv4 address (max 15 bytes) or a full-blown IPv6 address (39-47 bytes, see RFC3989) or
 * even twice a complete reverse DNS address (see {@link update_core_2011092100()}).
 *
 * See also {@link task_logview()} for a rant on the difference between LOG_DEBUG and LOG_INFO.
 *
 * @param string $message the message to write to the log
 * @param int $priority loglevel, see PHP-function syslog() for a list of predefined constants
 * @return bool FALSE on error, TRUE on success
 * @uses $CFG
 * @todo should we make this configurable and maybe log directly to syslog 
 *       (with automatic logrotate) or do we want to keep this 'self-contained'
 *       (the webmaster can read the table, but not the machine's syslog)?
 */
function logger($message,$priority=WLOG_INFO,$user_id='') {
    global $CFG;

    if (($priority == WLOG_DEBUG) && (!($CFG->debug))) {
        return TRUE;
    }

    // Try to link this information to a particular user
    // if possible (only when a session exists) and if no
    // user_id was provided by the caller.
    if (empty($user_id)) {
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }
    }
    $fields = array(
        'datim' => strftime('%Y-%m-%d %T'),
        'remote_addr' => (string) $_SERVER['REMOTE_ADDR'],
        'priority' => intval($priority),
        'user_id' => (empty($user_id)) ? NULL : intval($user_id),
        'message' => (string) $message);
    $retval = (FALSE === db_insert_into('log_messages',$fields)) ? FALSE : TRUE;
    return $retval;
} // logger()


/** get the number of the node the user requested or NULL if not specified
 *
 * This routine exists because nodes and (to a lesser extent) areas
 * are so central to the whole idea of WAS.
 *
 * A specific node can be requested in two different ways, for example page 35
 * with an additional parameter 'photo' with value 7 is called either via
 * <code>
 * http://exemplum.eu/was/index.php/was/index.php/35/photo/5/Picture_of_our_field_trip.html
 * </code>
 * or
 * <code>
 * http://exemplum.eu/was/index.php/was/index.php?node=35&photo=5
 * </code>
 *
 * The routine get_parameter_int() with a default value of NULL yields 35 in both cases.
 *
 * A node can also be specified implicitly, e.g. via
 * <code>
 * http://exemplum.eu/was/index.php/was/index.php/area/1
 * </code>
 * or
 * <code>
 * http://exemplum.eu/was/index.php/was/index.php?area=1
 * </code>
 * which yields the default node for area 1, or simply
 * <code>
 * http://exemplum.eu/was/index.php/was/index.php
 * </code>
 * which yields the default node in the default area.
 *
 * Important note:
 * In previous versions of this routine (and {@link get_requested_area()}) we
 * also accepted constructs like
 * <code>
 * http://exemplum.eu/was/index.php/was/index.php/35
 * http://exemplum.eu/was/index.php/was/index.php/1/35
 * </code>
 * but this has a great disadvantage that the idea of an ever growing list of
 * integers (or more general: positional parameters) is not very handy in the
 * long run. For instance: how to convey that we want to see photo #5 on page #35?
 * 'index.php/35/5'? How is that to be interpreted different from page 5 in area 35?
 * I now think the better approach is to use key/value-pairs in the friendly url path,
 * and also get rid of the unnamed int indicating 'area'. The latter wasn't really
 * usefull anyway, because specifying a node IMPLIES an area and it could even
 * cause trouble if a bookmarked area+node would be moved to another area: the bookmark
 * would yield an error message rather than the node (in another area) or the default
 * node in the bookmarked area.
 * All in all this change makes this routine exetremely simple: it almost another
 * name for get_parameter_int().
 * It is still possible to specify both node AND area (allthough there is no need):
 * <code>
 * http://exemplum.eu/was/index.php/was/index.php/35/area/1
 * http://exemplum.eu/was/index.php/was/index.php/node/35/area/1
 * http://exemplum.eu/was/index.php/was/index.php/area/1/node/35
 * </code>
 *
 * Note that this routine does not validate the requested node in any way other
 * than making sure that IF it is specified, it is valid UTF-8 and it is an integer value.
 * For all we know it might even be a negative value.
 *
 * @return int|null integer indicating the node or NULL if none specified
 */
function get_requested_node() {
    return get_parameter_int('node',NULL);
} // get_requested_node()


/** get the number of the area the user requested or null if not specified
 *
 * See discussion of {@link get_requested_node()}.
 *
 * @return int|null integer indicating the area or null if none specified
 */
function get_requested_area() {
    return get_parameter_int('area',NULL);
} // get_requested_area()


/** get the name of the requested file
 *
 * See discussion of {@link get_requested_node()}. Files are served via
 * /file.php via a comparable mechanism: either
 *
 *     http://exemplum.eu/was/file.php/path/to/filename.ext
 *
 * OR
 *
 *    http://exemplum.eu/was/file.php?file=/path/to/filename.ext
 *
 * This routine extracts the '/path/to/filename.ext' part.
 *
 * Note that we require valid UTF-8. If the path is not UTF-8, we return NULL.
 *
 * @return string|null requested filename or null if none specified or invalid UTF-8
 */
function get_requested_filename() {
    global $CFG;
    $filename = NULL;
    if (($CFG->friendly_url) && (isset($_SERVER['PATH_INFO'])) && (utf8_validate($_SERVER['PATH_INFO']))) {
        $filename = $_SERVER['PATH_INFO'];
    } elseif ((isset($_GET['file'])) && (utf8_validate($_GET['file']))) {
        $filename = magic_unquote($_GET['file']);
    }
    return $filename;
} // get_requested_filename()


/** return an integer value specified in the page request or default value if none
 *
 * this routine first checks the friendly url to see of the requested parameter is
 * specified there. If it is, we will use it unless there is also a parameter in $_GET
 * that prevails. If the parameter is not specified at all, the $default_value is returned.
 * It is the responsability of the caller to provide a workable default value.
 *
 * Note that invalid UTF-8 is silently discarded.
 *
 * @param string $name the name of the parameter to retrieve the value of
 * @param mixed $default_value the value to return if parameter was not specified
 * @return mixed the value of the parameter or the default value if not specified
 */
function get_parameter_int($name,$default_value=NULL) {
    $value = get_friendly_parameter($name,NULL); // returns a string or a null if $name not in friendly url
    $value = (is_null($value)) ? $default_value : intval($value); // make sure the string becomes an int or use default
    if ((isset($_GET[$name])) && (utf8_validate($_GET[$name]))) {
        $value = intval($_GET[$name]);
    }
    return $value;
} // get_parameter_int()


/** return an (unquoted) string value specified in the page request or default value if none
 *
 * First check out the friendly url for the named parameter. If it exists, we use that,
 * otherwise we have the $default_value. After that the valid UTF-8 value may overwrite the
 * value found in the friendly url (or the default value).
 *
 * It is the responsability of the caller to provide a workable default value.
 *
 * Note that invalid UTF-8 is silently discarded.
 *
 * @param string $name the name of the parameter to retrieve the value of
 * @param mixed $default_value the value to return if parameter was not specified
 * @return mixed the value of the parameter or the default value if not specified
 */
function get_parameter_string($name,$default_value=NULL) {
    $value = get_friendly_parameter($name,$default_value); // first check out friendly URL...
    if ((isset($_GET[$name])) && (utf8_validate($_GET[$name]))) {
        $value = magic_unquote($_GET[$name]);              // ...but let valid UTF-8 GET[] value prevail (if any)
    }
    return $value;
} // get_parameter_string()


/** redirect to another url by sending an http header
 *
 * @param string $url the url to redirect to
 * @return nothing
 */
function redirect_and_exit($url,$message='') {
    $file = '';
    $line = 0;
    if (headers_sent($file,$line)) {
        // headers were already sent, log this strange event
        logger("headers were already sent in file $file($line)",WLOG_DEBUG);
    } else {
        header('Location: '.$url);
    }
    echo "<html>\n".
         "<head>\n".
         "  <title>redirect</title>\n".
         "</head>\n".
         "<body>\n".
         "  Redirect: <a href=\"$url\">".htmlspecialchars($url)."</a>\n";
         "  <p>$message\n".
         "</body>\n".
         "</html>\n";
    exit;
} // redirect_and_exit()


/** return the number of database queries that was executed
 *
 * @return int the number of queries
 * @uses $DB
 */
function performance_get_queries() {
  global $DB;
  return $DB->query_counter;
} // performance_get_queries()


/** return the script execution time
 *
 * @return double interval between begin execution and now
 * @todo maybe we should get rid of this $PERFORMANCE object,
 *       because it doesn't do that much anyway
 */
function performance_get_seconds() {
  global $PERFORMANCE;
  $time_stop = microtime();
  return diff_microtime($PERFORMANCE->time_start,$time_stop);
} // performance_get_seconds()


/** get record lock on a node
 *
 * this is a wrapper around {@link lock_record()} for locking nodes.
 *
 * @param int $node_id the primary key of the node to lock
 * @param array &$lockinfo returns information about the session that already locked this record
 * @return bool TRUE if locked succesfully, FALSE on error or already locked ; extra info returned in $lockinfo
 * @uses lock_record()
 */
function lock_record_node($node_id,&$lockinfo) {
    return lock_record($node_id,$lockinfo,'nodes','node_id','locked_by_session_id','locked_since');
} // lock_record_node()


/** release lock on a node
 *
 * this is a wrapper around {@link lock_release()} for unlocking nodes.
 *
 * @param int $node_id the primary key of the node record to unlock
 * @return bool TRUE if locked removed succesfully, FALSE on error or lock not found
 * @uses lock_record()
 */
function lock_release_node($node_id) {
    return lock_release($node_id,'nodes','node_id','locked_by_session_id','locked_since');
} // lock_release_node()


/** put a (co-operative) lock on a record
 *
 * this tries to set the co-operative) lock on the record with serial (pkey) $id
 * in table $tablename by setting the $locked_by field to our own session_id. This
 * is the companion routine of {@link lock_release()}.
 *
 * The mechanism of co-operative locking works as follows. Some tables (such as
 * the 'nodes' table) have an int field, e.g. 'locked_by_session_id'. This field
 * can either be NULL (indicating that the record is not locked) or hold the primary
 * key of a session (indicating that the record is locked and also by which session).
 * 
 * Obtaining a lock boils down to updating the table and setting that field to the
 * session_id. As long as the underlying database system guarantees that execution
 * of an UPDATE statement is not interrupted, we can use UPDATE as a 
 * 'Test-And-Set'-function. According to the docentation MySQL does this.
 * 
 * The procedure is as follows.
 *
 * 1. we try to set the locked_by-field to our session_id on the condition that
 *    the previous value of that field is NULL. If this succeeds, we have effectively
 *    locked the record.
 *
 * 2. If this fails, we retrieve the current value of the field to see which session has
 *    locked it. If this happens to be us, we had already locked the record before and
 *    we're done.
 *
 * 3. If another session_id holds the lock, we check for that session's existence. If it
 *    still exists, we're out of luck: we can't obtain the lock.
 *
 * 4. If that other session does no longer exist, we try to replace that other session's
 *    session_id with our own session_id, once again using a single UPDATE (avoiding another
 *    race condition). If that succeeds we're done and we have the lock; if it failes
 *    we're also done but without lock.
 *
 * If locking the record fails because the record is already locked by another session,
 * this routine returns information about that other session in $lockinfo. It is up to
 * the caller to use this information or not.
 *
 * Note.
 * A record can stay locked if the webbrowser of the locking session has crashed. Eventually
 * this will be resolved if the crashed session is removed from the sessions table. However,
 * the user may have restarted her browser while the record was locked. From the new session
 * it appears that the record is still locked. This may take a while. Mmmmm...
 * The other option is to lock on a per-user basis rather than per-session basis. Mmmm...
 * Should we ask the user to override the session if it happens to be the same user?
 * Mmm. put it on the todo list. (A small improvement might be to call the garbage collection
 * between step 2 and 3. Oh well).
 *
 * @todo perhaps we can save 1 trip to the database by checking for something like
 *       UPDATE SET locked_by = $session_id WHERE (id = $id) AND ((locked_by IS NULL) OR (locked_by = $session_id))
 *       but I don't know how many affected rows that would yield if we already had the lock and
 *       effectively nothing changes in the record. (Perhaps always update atime to force 1 affected row?)
 * @todo do we need a 'force lock' option to forcefully take over spurious locks?
 * @todo we need to resolve the problem of crashing browsers and locked records
 * @param int $id the primary key of the record to lock
 * @param array &$lockinfo returns information about the session that already locked this record
 * @param string $tablename the name of the table
 * @param string $pkey name of the field holding the serial (pkey)
 * @param string $locked_by name of the field to hold our session_id indicating we locked the record
 * @param string $locked_since name of the field holding the datetime when the lock was obtained
 * @return bool TRUE if locked succesfully, FALSE on error or already locked ; extra info returned in $lockinfo
 */
function lock_record($id,&$lockinfo,$tablename, $pkey,$locked_by,$locked_since) {
    global $DB;
    $now = strftime("%Y-%m-%d %T");
    $lockinfo = array();
    if (!isset($_SESSION['session_id'])) { // weird, we SHOULD have a session id
        logger('weird: no session_id in lock_record()',WLOG_DEBUG);
        return FALSE;
    } else {
        $session_id = $_SESSION['session_id'];
        if (!is_int($session_id)) {
            logger('weird: session_id in lock_record() is not an int',WLOG_DEBUG);
            return FALSE;
        }
    }
    //
    // 1 -- try to get a lock from scratch (ie: record is currently not locked by anyone)
    //
    $fields = array($locked_by => $session_id, $locked_since => $now);
    $where = array($pkey => intval($id), $locked_by => NULL);
    $retval = db_update($tablename,$fields,$where);
    if ($retval === FALSE) { // error
        return FALSE;
    } elseif ($retval == 1) { // exactly 1 row was updated, hurray, success!
        return TRUE;
    } // else record was probably locked by another session or by our session; go check it out

    //
    // 2 -- 1 didn't work, find out who holds the lock
    //
    $retval = db_select_single_record($tablename,$locked_by,array($pkey => $id));
    if ($retval === FALSE) {
        return FALSE; // error
    } elseif ($retval[$locked_by] == $session_id) {
        return TRUE; // we had the lock ourselves all along
    } // else record was definately locked by session $retval[$locked_by]

    //
    // 3 -- whoever holds the lock, it is not us. check out if 'their' session is still active
    //
    $locked_by_session_id = $retval[$locked_by];
    $prefixed_tablename = $DB->prefix.$tablename;
    $sessions_table = $DB->prefix.'sessions';
    $users_table = $DB->prefix.'users';
    $sql = "SELECT s.user_id, u.username, u.full_name, s.user_information, s.ctime, s.atime, x.$locked_since AS ltime ".
           "FROM $prefixed_tablename AS x INNER JOIN $sessions_table AS s ON x.$locked_by = s.session_id ".
           "LEFT JOIN $users_table AS u ON s.user_id = u.user_id ".
           "WHERE s.session_id = $locked_by_session_id";
    $DBResult = $DB->query($sql,1);
    if ($DBResult === FALSE) { // error
        return FALSE;
    } elseif ($DBResult->num_rows == 1) { // alas, already locked by another
        $lockinfo = $DBResult->fetch_row_assoc();

        $DBResult->close();
        return FALSE;
    } else {
        $DBResult->close();
    }

    //
    // 4 -- We still have a chance to obtain the lock because the current 'lock' appears
    //      to belong to a session that no longer exists
    //
    $fields = array($locked_by => $session_id, $locked_since => $now);
    $where = array($pkey => intval($id), $locked_by => $locked_by_session_id);
    $retval = db_update($tablename,$fields,$where);
    if ($retval === FALSE) { // error
        return FALSE;
    } elseif ($retval == 1) { // exactly 1 row was updated, hurray, success!
        return TRUE;
    } else {
        return FALSE; // no joy, give up
    }
} // lock_record()


/** unlock a record that was previously successfully locked
 *
 * this removes the co-operative) lock on the record with serial (pkey) $id
 * in table $tablename by setting the $locked_by field to NULL. This is the
 * companion routine of {@link lock_record()}.
 *
 * @param int $id the primary key of the record to unlock
 * @param string $tablename the name of the table
 * @param string $pkey name of the field holding the serial (pkey)
 * @param string $locked_by name of the field holding the session_id of the session that locked the record
 * @param string $locked_since name of the field holding the datetime when the lock was obtained
 * @return bool TRUE if locked removed succesfully, FALSE on error or lock not found
 */
function lock_release($id,$tablename, $pkey,$locked_by,$locked_since) {
    if (!isset($_SESSION['session_id'])) { // weird, we SHOULD have a session id
        logger('weird: no session_id in lock_release()',WLOG_DEBUG);
        return FALSE;
    } else {
        $session_id = $_SESSION['session_id'];
        if (!is_int($session_id)) {
            logger('weird: session_id in lock_release() is not an int',WLOG_DEBUG);
            return FALSE;
        }
    }
    $fields = array($locked_by => NULL,$locked_since => NULL);
    $where = array($pkey => intval($id), $locked_by => $session_id);
    $retval = db_update($tablename,$fields,$where);
    if ($retval === FALSE) { // error
        return FALSE;
    } elseif ($retval != 1) { // record not found
        return FALSE;
    } else {
        return TRUE; // exactly 1 row was updated, hurray, success!
    }
} // lock_release()


/** send pending messages/alerts
 *
 * this goes through all the alert accounts to see if any messages need
 * to be sent out by email. The strategy is as follows.
 * First we collect a maximum of $max_messages alerts in in core 
 * (1 trip to the database) Then we iterate through that collection
 * and for every alert we
 *  1. construct and send an email message
 *  2. update the record (reset the message buffer 
 *     and message count) (+1 trip to the database)
 *
 * Locking and unlocking would be even more expensive, especially when
 * chances of race conditions are not so big. (An earlier version of
 * this routine went to the database once for the list of all pending
 * alerts and subsequently twice for each alert but eventually I
 * considered that too expensive too).
 *
 * Assuming that an UPDATE is more or less atomic, we hopefully
 * can get away with an UPDATE with a where clause looking explicitly
 * for the previous value of the message count. If a message was added
 * after retrieving the alerts but before updating, the message count
 * would be incremented (by the other process) which would prevent us from
 * updating. The alert would be left unchanged but including
 * the added message. Worst case: the receiver gets the same list of
 * alerts again and again. I consider that a fair trade off, given the
 * low probability of it happening. (Mmmm, famous last words...)
 *
 * Bottom line, we don't do locking in this routine.
 *
 * Note that we add a small reminder to the message buffer about
 * us processing the alert and sending a message. However, we don't
 * set the number of messages to 1 because otherwise that would be
 * the signal to sent this message the next time. We don't want
 * sent a message every $cron_interval minutes basically saying 
 * that we didn't do anything since the previous run. (Or is this
 * a feature after all?)
 *
 * Failures are logged, success are logged as WLOG_DEBUG.
 *
 * @param int $max_messages do not send more than this number of messages
 * @return int the number of messages that were processed
 */
function cron_send_queued_alerts($max_messages=10) {
    global $CFG;
    //
    // 1 -- any work to do at all?
    //
    $now = strftime('%Y-%m-%d %T');
    $table = 'alerts';
    $fields = '*';
    $where = '(messages > 0) AND (is_active = '.SQL_TRUE.') AND (cron_next <= '.db_escape_and_quote($now).')';
    $order = 'cron_next';
    $keyfield = 'alert_id';
    $limit = max(1,intval($max_messages)); // at least go for 1 alert
    if (($alerts = db_select_all_records($table,$fields,$where,$order,$keyfield,$limit)) === FALSE) { // ignore error
        logger(sprintf('%s(): error retrieving alerts: %s',__FUNCTION__,db_errormessage()));
        return 0;
    } elseif (sizeof($alerts) < 1) { // nothing to do
        logger(sprintf('%s(): nothing to do',__FUNCTION__),WLOG_DEBUG);
        return 0;
    }

    //
    // 2 -- yes, work to do: iterate through until at most $max_messages are sent
    //
    $alert_messages_sent = 0;

    /** make sure utility routines for creating/sending email messages are available */
    require_once($CFG->progdir.'/lib/email.class.php');
    $email = new Email;

    foreach($alerts as $alert_id => $alert)  {
        $messages = intval($alert['messages']);
        $mailto = $alert['email'];
        $full_name = $alert['full_name'];
        $email->set_mailto($mailto,$full_name);
        $email->set_subject(t('alerts_mail_subject','',array('{ALERTS}' => $messages,'{SITENAME}' => $CFG->title)));
        $email->set_message(wordwrap($alert['message_buffer'],70));
        if ($email->send()) {
            // alert was accepted, reset our message buffer, counter
            $cron_next = strftime('%Y-%m-%d %T',time() + 60 * intval($alert['cron_interval']));
            $continuation_line = $now."\n".t('alerts_processed','',array('{ALERTS}' => $messages))."\n";
            $fields = array('cron_next' => $cron_next,
                            'messages' => 0, 
                            'message_buffer' => $continuation_line);
            $where = array('alert_id' => $alert_id,
                           'messages' => $messages); // don't update if another message was added while we were working
            if (($retval = db_update('alerts',$fields,$where)) !== FALSE) {
                logger(sprintf('%s(): %d message(s) for %s (%s) (id=%d) sent; %d record(s) updated',
                               __FUNCTION__,$messages,$mailto,$full_name,$alert_id,$retval),WLOG_DEBUG);
                ++$alert_messages_sent;
                if ($max_messages <= $alert_messages_sent) {
                    break;
                }
            } else {
                logger(sprintf('%s(): error with alert for %s (%s) (id=%d): '.
                               'mail was sent, but record not reset. '.
                               'Was another process updating this record while we were not looking?',
                               __FUNCTION__,$mailto,$full_name,$alert_id));
            }
        } else {
            logger(sprintf('%s(): error: %d message(s) for %s (%s) (id=%d) NOT sent',
                           __FUNCTION__,$messages,$mailto,$full_name,$alert_id));
        }
    }
    logger(sprintf('%s(): success processing %d alert(s)',__FUNCTION__,$alert_messages_sent));
    return $alert_messages_sent;
} // cron_send_queued_alerts()


/** construct a tree of nodes in memory
 *
 * this reads the nodes in the specified area from disk and
 * constructs a tree via linked lists (sort of).
 * If parameter $force is TRUE, the data is read from the database,
 * otherwise a cached version is returned (if available).
 *
 * Note that this routine also 'repairs' the tree when an orphan is
 * detected. The orphan is automagically moved to the top of the area.
 * Of course, it shouldn't happen, but if it does we are better off
 * with a magically _appearing_ orphan than a _disappearing_ node.
 *
 * A lot of operations in the page manager work with a tree of nodes in
 * some way, e.g. walking the tree and displaying it or walking the
 * tree and collecting the sections (but not the pages), etc.
 *
 * The tree starts with a 'root' with key 0 ($tree[0]). This is the starting
 * point of the tree. The nodes at the top level of an area are linked from
 * this root node via the field 'first_child_id'. If there are no nodes
 * in the area, this field 'first_child_id' is zero. The linked list is
 * constructed by using the node_id. All nodes in an area are collected in
 * an array. This array us used to construct the linked lists.
 *
 * Every node has a parent (via 'parent_id'), where the nodes at the top level
 * have a parent_id of zero; this points to the 'root'. The nodes within a section
 * or at the top level are linked forward via 'next_sibling_id' and backward
 * via 'prev_sibling_id'. A zero indicates the end of the list. Childeren start
 * with 'first_child_id'. A value of zero means: no childeren.
 *
 * The complete node record from the database is also stored in the tree.
 * This is used extensively throughout the pagemanager; it acts as a cache for
 * all nodes in an area. 
 *
 * Note that we cache the node records per area. If two areas are involved,
 * the cache doesn't work very well anymore. However, this doesn't happen very
 * often; only in case of moving nodes from one area to another (and even then).
 *
 * @param int $area_id the area to make the tree for
 * @param bool $force if TRUE forces reread from database (resets the cache)
 * @return array contains a 'root node' 0 plus all nodes from the requested area if any
 * @todo what if we need the trees of two different areas?
 *       should the static var here be an array, keyed by area_id?
 * @todo repairing a node doesn't really belong here, in this routine.
 *       we really should have a separate 'database repair tool' for this purpose.
 *       someday we'll fix this....
 */
function tree_build($area_id, $force = FALSE) {
    global $DB;
    static $tree = NULL;
    static $cached_area_id = 0;

    if (($tree !== NULL) && (!($force)) && ($area_id == $cached_area_id)) {
        return $tree;
    }

    // 1 -- Start with 'special' node 0 is root of the tree
    $tree = array(0 => array(
      'node_id' => 0,
      'parent_id' => 0,
      'prev_sibling_id' => 0,
      'next_sibling_id' => 0,
      'first_child_id' => 0,
      'is_hidden' => FALSE,
      'is_page' => FALSE,
      'record' => array())
      );


    $where = array('area_id' => intval($area_id));
    $order = array('CASE WHEN (parent_id = node_id) THEN 0 ELSE parent_id END', 'sort_order','node_id');
    $records = db_select_all_records('nodes','*',$where,$order,'node_id');

    // 2 -- step through all node records and copy the relevant fields + integral record too
    if ($records !== FALSE) {
        foreach($records as $record) {
            $node_id = intval($record['node_id']);
            $parent_id = intval($record['parent_id']);
            $is_hidden = db_bool_is(TRUE,$record['is_hidden']);
            $is_page = db_bool_is(TRUE,$record['is_page']);
            $is_default = db_bool_is(TRUE,$record['is_default']);
            if ($parent_id == $node_id) { // top level
                $parent_id = 0;
            }
            $tree[$node_id] = array(
                'node_id' => $node_id,
                'parent_id' => $parent_id,
                'prev_sibling_id' => 0,
                'next_sibling_id' => 0,
                'first_child_id' => 0,
                'is_hidden' => $is_hidden,
                'is_page' => $is_page,
                'is_default' => $is_default,
                'record' => $record);
        }
    }
    unset($records); // free memory

    // 3 -- step through all collected records and add links to childeren and siblings
    $prev_node_id = 0;
    $sort_order = 0;
    foreach ($tree as $node_id => $node) {
        $parent_id = $node['parent_id'];
        if (!isset($tree[$parent_id])) {
            // obviously this shouldn't happen but if it does, we DO log it and we do something about it!
            // Note: changes will be effective the NEXT time the tree is read from database.
            logger("pagemanager: node '$node_id' was orphaned because parent '$parent_id' does not exist; fixed");
            $sort_order -= 10; // insert orphans in reverse order at the top level
            $fields = array('parent_id' => $node_id, 'sort_order' => $sort_order);
            $where = array('node_id' => $node_id);
            $sql = db_update_sql('nodes',$fields,$where);
            logger("tree_build(): moved orphan '$node_id' (original parent '$parent_id') to top with '$sql'",WLOG_DEBUG);
            $DB->exec($sql);
        } elseif ($parent_id == $tree[$prev_node_id]['parent_id']) {
            $tree[$prev_node_id]['next_sibling_id'] = $node_id;
            $tree[$node_id]['prev_sibling_id'] = $prev_node_id;
        } else {
            $tree[$parent_id]['first_child_id'] = $node_id;
        }
        $prev_node_id = $node_id;
    }

    // 4 -- 'root node' 0 is a special case, the top level nodes are in fact childeren, not siblings
    $tree[0]['first_child_id'] = $tree[0]['next_sibling_id'];
    $tree[0]['next_sibling_id'] = 0;

    // 5 -- done!
    $cached_area_id = $area_id;
    return $tree;
} // tree_build()


/** calculate the visibility of the nodes in the tree
 *
 * this flags visible nodes as visible. Here 'visible' means that
 *
 *  - the node is not hidden, not expired and not under embargo
 *  - the section has at least 1 visible node (page or section)
 *
 * As a side effect, any subtree starting at a hidden/expired/embargo'ed
 * section is completely set to invisible so we don't risk the chance to
 * accidently show a page from an invisible section.
 * This routine walks through the tree recursively.
 *
 * @param int $subtree_id the starting point for the tree walking
 * @param array &$tree pointer to the current tree
 * @param bool $force_invisibility 
 * @return bool TRUE when there is at least 1 visible node, FALSE otherwise
 * @todo how about making all nodes under embargo visible when previewing a page
 *       or at least the path from the node to display?
 */
function tree_visibility($subtree_id,&$tree,$force_invisibility=FALSE) {
    $now = strftime("%Y-%m-%d %T");
    $visible_nodes = 0;
    for ($node_id = $subtree_id; ($node_id != 0); $node_id = $tree[$node_id]['next_sibling_id']) {
        if ($tree[$node_id]['is_page']) {
            if (($tree[$node_id]['record']['expiry'] < $now) ||
                ($now < $tree[$node_id]['record']['embargo']) ||
                ($force_invisibility) ||
                ($tree[$node_id]['is_hidden'])) {
                $tree[$node_id]['is_visible'] = FALSE;
            } else {
                $tree[$node_id]['is_visible'] = TRUE;
                ++$visible_nodes;
            }
        } else { //section
            if (($tree[$node_id]['record']['expiry'] < $now) || 
                ($now < $tree[$node_id]['record']['embargo']) ||
                ($force_invisibility)) {
                $tree[$node_id]['is_visible'] = FALSE;
                tree_visibility($tree[$node_id]['first_child_id'],$tree,TRUE);
            } elseif ($tree[$node_id]['is_hidden']) {
                $tree[$node_id]['is_visible'] = FALSE;
                tree_visibility($tree[$node_id]['first_child_id'],$tree);
            } else {
                if (tree_visibility($tree[$node_id]['first_child_id'],$tree)) {
                    $tree[$node_id]['is_visible'] = TRUE;
                    ++$visible_nodes;
                } else {
                    $tree[$node_id]['is_visible'] = FALSE;
                }
            }
        }
    }
    return ($visible_nodes > 0) ? TRUE : FALSE;
} // tree_visibility()


/** determine if any of the ancestors or $node_id itself is under embargo
 *
 * This climbs the tree upward, starting at $node_id, to see if any nodes
 * are under embargo. If an embargo'ed node is detected, TRUE is returned.
 * If none of the nodes are under embargo, then FALSE is returned.
 *
 * Note that this routine looks strictly at the embargo property, it is very
 * well possible that a node is expired, see {@link is_expired()}.
 *
 * Also note that this routine currently also tries to 'fix' the node database
 * when a circular reference is detected. This doesn't really belong here, but
 * for the time being it is convenient to have this auto-repair mechanism here.
 * The node that is fixed is the section we are looking at after MAXIMUM_ITERATIONS
 * tries, which is not necessarily the node we started with.
 *
 * @param array &$tree family tree
 * @param int $node_id 
 * @return bool TRUE if any ancestor (or node_id) is under embargo, otherwise FALSE
 * @todo this function also 'repairs' circular references. This should move to a separate
 *       tree-repair function but for the time being it is "convenient" to have automatic repairs...
 * @uses $DB
 */
function is_under_embargo(&$tree,$node_id) {
    global $DB;
    $tries = MAXIMUM_ITERATIONS;
    $now = strftime('%Y-%m-%d %T');
    $node_id = intval($node_id);
    for ($next_id = $node_id; (($next_id != 0) && (--$tries > 0)); $next_id = $tree[$next_id]['parent_id']) {
        if ($now < $tree[$next_id]['record']['embargo']) {
            return TRUE;
        }
    }
    if ($tries <= 0) { // circular reference detected, try to fix it
        // insert offending node before first node at the top level
        // this becomes visible the _next_ time the tree is read from the database
        $first_id = $tree[0]['first_child_id'];
        $sort_order = ($first_id != 0) ? intval($tree[$first_id]['record']['sort_order']) - 10 : 10;
        $fields = array('parent_id' => $next_id, 'sort_order' => $sort_order);
        $where = array('node_id' => $next_id);
        $sql = db_update_sql('nodes',$fields,$where);
        $parent_id = intval($tree[$node_id]['parent_id']);
        logger("is_under_embargo(): circular reference '$node_id' (parent '$parent_id') fixed with '$sql'",WLOG_DEBUG);
        $DB->exec($sql);
    }
    return FALSE;
} // is_under_embargo()


/** determine if any of the ancestors or $node_id itself is already expired
 *
 * This climbs the tree upward, starting at $node_id, to see if any nodes
 * are expired. If an expired node is detected, TRUE is returned.
 * If none of the nodes are expired, then FALSE is returned.
 *
 * Note that this routine looks strictly at the expiry property, it is very
 * well possible that a node is under embargo, see {@link is_under_embargo()}.
 *
 * Also note that this routine currently also tries to 'fix' the node database
 * when a circular reference is detected. This doesn't really belong here, but
 * for the time being it is convenient to have this auto-repair mechanism here.
 * The node that is fixed is the section we are looking at after MAXIMUM_ITERATIONS
 * tries, which is not necessarily the node we started with.
 *
 * @param int $node_id 
 * @param array &$tree family tree
 * @return bool TRUE if any ancestor (or node_id) is expired, otherwise FALSE
 * @todo this function also 'repairs' circular references. This should move to a separate
 *       tree-repair function but for the time being it is "convenient" to have automatic repairs...
 * @uses $DB
 */
function is_expired($node_id,&$tree) {
    global $DB;
    $tries = MAXIMUM_ITERATIONS;
    $now = strftime('%Y-%m-%d %T');
    $node_id = intval($node_id);
    for ($next_id = $node_id; (($next_id != 0) && (--$tries > 0)); $next_id = $tree[$next_id]['parent_id']) {
        if ($tree[$next_id]['record']['expiry'] < $now) {
            return TRUE;
        }
    }
    if ($tries <= 0) { // circular reference detected, try to fix it
        // insert offending node before first node at the top level
        // this becomes visible the _next_ time the tree is read from the database
        $first_id = $tree[0]['first_child_id'];
        $sort_order = ($first_id != 0) ? intval($tree[$first_id]['record']['sort_order']) - 10 : 10;
        $fields = array('parent_id' => $next_id, 'sort_order' => $sort_order);
        $where = array('node_id' => $next_id);
        $sql = db_update_sql('nodes',$fields,$where);
        $parent_id = intval($tree[$node_id]['parent_id']);
        logger("is_expired(): circular reference '$node_id' (parent '$parent_id') fixed with '$sql'",WLOG_DEBUG);
        $DB->exec($sql);
    }
    return FALSE;
} // is_expired()


/** retrieve a list of all available area records keyed by area_id
 *
 * this returns a list of area-records or FALSE if no areas are available
 * The list is cached via a static variable so we don't have to go to the
 * database more than once for this.
 * Note that the returned array is keyed with area_id and is sorted by sort_order.
 * Also note that this list may include areas for which the current user has
 * no permissions whatsoever and also areas that are inactive.
 *
 * @param bool $forced if TRUE forces reread from database (resets the cache)
 * @return array|bool FALSE if no areas available or an array with area-records
 */
function get_area_records($forced = FALSE) {
    static $records = NULL;
    if (($records === NULL) || ($forced)) {
        $tablename = 'areas';
        $fields = '*';
        $order = array('sort_order','area_id');
        $records = db_select_all_records($tablename,$fields,'',$order,'area_id');
    }
    return $records;
} // get_area_records()


/** retrieve a list of all available module records
 *
 * this returns a list of active module-records or FALSE if none are are available
 * The list is cached via a static variable so we don't have to go to the
 * database more than once for this.
 * Note that the returned array is keyed with module_id.
 *
 * @param bool $forced if TRUE forces reread from database (resets the cache)
 * @return array|bool FALSE if no modules available or an array with module-records
 */
function get_module_records($forced = FALSE) {
    static $records = NULL;
    if (($records === NULL) || ($forced)) {
        $tablename = 'modules';
        $fields = '*';
        $where = array('is_active' => TRUE);
        $order = array('module_id');
        $records = db_select_all_records($tablename,$fields,$where,$order,'module_id');
    }
    return $records;
} // get_module_records()


/** translate a numeric capacity code to a readable name
 *
 * this translates a capacity code into a readable name,
 * e.g. as an item in a dropdown list when dealing with group
 * memberships. The actual codes are defined as constants,
 * e.g. CAPACITY_NONE. 
 *
 * @param int $capacity numeric code of capacity
 * @return string readable name of capacity
 */
function capacity_name($capacity) {
    $capacity = intval($capacity);
    if (($capacity < CAPACITY_NONE) || (CAPACITY_NEXT_AVAILABLE <= $capacity)) {
        $name = t('capacity_name_unknown','',array('{CAPACITY}' => $capacity));
    } else {
        $name = t('capacity_name_'.$capacity);
    }
    return $name;
} // capacity_name()


/** calculate an array with acls related to user $user_id via group memberships
 *
 * this calculates the related acls for user $user_id. The results are returned
 * as an array keyed by acl_id. It can containt 0 or more elements. The values
 * of the array elements are groupname/capacity-pairs.
 * This routine is referenced from both {@link useraccount.class.php}
 * and {@link usermanager.class.php}.
 *
 * @param int $user_id the user we're looking at
 * @return array 0, 1 or more acl_id => groupname/capacity pairs
 * @ues $DB
 */
function calc_user_related_acls($user_id) {
    global $DB;
    $related_acls = array();
    $sql = sprintf("SELECT gc.acl_id, ugc.group_id, ugc.capacity_code, g.groupname, g.full_name ".
                   "FROM %susers_groups_capacities ugc ".
                   "INNER JOIN %sgroups_capacities gc USING (group_id, capacity_code) ".
                   "INNER JOIN %sgroups g USING (group_id) ".
                   "WHERE ugc.capacity_code <> 0 AND ugc.user_id = %d ".
                   "ORDER BY g.groupname",
                   $DB->prefix,$DB->prefix,$DB->prefix,$user_id);
    if (($DBResult = $DB->query($sql)) !== FALSE) {
        $records = $DBResult->fetch_all_assoc('acl_id');
        $DBResult->close();
        foreach($records as $acl_id => $record) {
            $related_acls[$acl_id] = $record['groupname']."/".capacity_name($record['capacity_code']);
        }
    } else {
        logger("calc_related_acls(): cannot retrieve acls for user '$user_id': ".db_errormessage());
    }
    return $related_acls;
} // calc_user_related_acls()


/** retrieve the records of the groups of which user $user_id is a member
 *
 * @param int $user_id the user we're looking at
 * @return array 0, 1 or more acl_id => $group_record pairs
 * @uses $DB
 */
function get_user_groups($user_id) {
    global $DB;
    $records = array();
    $sql = sprintf("SELECT g.* ".
                   "FROM %sgroups g ".
                   "INNER JOIN %susers_groups_capacities ugc ".
                   "USING (group_id) ".
                   "WHERE ugc.capacity_code <> 0 AND ugc.user_id = %d ".
                   "ORDER BY g.groupname",
                   $DB->prefix,$DB->prefix,$user_id);
    if (($DBResult = $DB->query($sql)) !== FALSE) {
        $records = $DBResult->fetch_all_assoc('group_id');
        $DBResult->close();
    } else {
        logger(sprintf("%s(): cannot retrieve groups for user '%d': %s",__FUNCTION__,$user_id,db_errormessage()));
    }
    return $records;
} // get_user_groups()


/** convert a string to another type (bool, int, etc.)
 *
 * @param string $type new type for $value: b=bool, i=integer, s=string, etc.
 * @param string $value the value to convert to tye $type
 * @return mixed the value $value casted to the proper type
 * @todo perhaps change the possible values of $type to full 
 *       strings rather than 'cryptic' single letter codes.
 *       Furthermore: what do we do with invalid dates, times
 *       and date/times? For now it is a stub, returning
 *       $value as-is. Oh well.
 */
function convert_to_type($type,$value) {
    switch($type) {
    case 'b':
        $value = (bool) $value;
        break;

    case 'd':
        // date stub
        break;

    case 'dt':
        // date/time stub
        break;

    case 'i':
        $value = intval($value);
        break;

    case 'f':
        $value = (double) $value;
        break;

    case 's':
        $value = (string) $value;
        break;

    case 't':
        // time stub
        break;
    default:
        logger('convert_to_type: unknown type \''.$type.'\' encountered');
        break;
    }
    return $value;
} // convert_to_type()


/** sanitise a string to make it acceptable as a filename/directoryname
 *
 * this routine analyses and maybe converts the input string as follows:
 *
 *  - all leading and trailing dots, spaces, dashes, underscores, backslashes and slashes are removed
 *  - all embedded spaces, backslashes and slashes are converted to underscores
 *  - only letters, digits, dots, dashes or underscores are retained
 *  - all sequences of 2 or more underscores are replaced with a single underscore
 *  - finally all 'forbidden' words (including empty string) get an underscore prefixed
 *
 * Note that this sanitising only satisfies the basic rules for filenames;
 * creating a new file with a sanitised name may still clash with an existing
 * file or subdirectory.
 *
 * Also note that a full pathname will yield something that looks
 * like a simple filename without directories or drive letter: 
 * C:\Program Files\Apache Group\htpasswd becomes
 * C_Program_Files_Apache_Group_htpasswd and /etc/passwd becomes
 * etc_passwd. Also this routine makes a URL look like a filename:
 * http://www.example.com becomes http_www.example.com.
 *
 * Finally note that we don't even attempt to transliterate utf8-characters
 * or any other characters between 128 and 255; these are simply removed.
 *
 * @param string $filename the string to sanitise
 * @return string sanitised filename which is never empty
 * @todo should we check for overlong UTF-8 encodings:
 *       C0 AF C0 AE C0 AE C0 AF equates to /../ or is that dealt with already
 *       by filtering on letters/digits and embedded dots/dashes/underscores?
 */
function sanitise_filename($filename)  {
    // get rid of all diacriticals etc.
    $s = utf8_strtoascii($filename);

    // strip leading space/dot/dash/underscore/backslash/slash
    $s = preg_replace('/^[ .\-_\\\\\\/]*/','',$s);

    // strip trailing space/dot/dash/underscore/backslash/slash
    $s = preg_replace('/[ .\-_\\\\\\/]*$/','',$s);

    // replace embedded spaces/backslashes/slashes/at-signs/colons with underscores
    $s = strtr($s,' \\/@:','_____');

    // keep only letters/digits and embedded dots/dashes/underscores
    $s = preg_replace('/[^0-9A-Za-z.\-_]/','',$s);

    // replace sequences of underscores with a single underscore
    $s = preg_replace('/__+/','_',$s);

    // 'forbidden' words
    $forbidden = array('','aux','com1','com2','com3','com4','con','lpt1','lpt2','lpt3','lpt4','nul','prn');
    if (in_array(utf8_strtolower($s),$forbidden)) {
        $s = '_'.$s;
    }
    return $s;
} // sanitise_filename()


/** return an integer (bytecount) value from PHP ini
 *
 *
 * @param string $variable name of the variable to retrieve, e.g. 'upload_max_filesize'
 * @return int value expressed in bytes
 */
function ini_get_int($variable) {
    $str = ini_get($variable);
    $value = intval($str);
    switch(substr($str,-1)) {
    case 'M':
    case 'm': $value <<= 20; break;

    case 'k':
    case 'K': $value <<= 10; break;

    case 'G':
    case 'g': $value <<= 30; break;
    }
    return $value;
} // ini_get_int()


/** convert string $s from native format to quoted printable (RFC2045)
 *
 * this converts the input string $s to quoted printable form as defined in
 * RFC2045 (see {@link http://www.ietf.org/rfc/rfc2045.txt}). By default this
 * routine assumes a line-oriented text input. This can be overruled by
 * calling the routine with the parameter $textmode set to FALSE: in that
 * case the input is considered to be a binary string with no embedded newlines.
 *
 * The routine assumes that the input lines are delimited with $newline.
 * By default this parameter is a LF (Linefeed) but it could be changed to
 * another delimiter using the function parameter $newline.
 *
 * According to RFC2045 the resulting output lines should be no longer than
 * 76 bytes, even though it is very well possible to use shorter lines. This
 * can be done by setting the parameter $max_length to the desired value.
 * Note that this value is forced to be in the range 4,...,76.
 *
 * The encoding is defined in section 6.7 of RFC2045 with these five rules.
 *
 * (1) General 8bit representation:
 *     any character may be represented as "=" followed by two uppercase
 *     hexadecimal digits.
 *
 * (2) Literal representation
 *     characters "!" to "~" but excluding the "=" may represent themselves.
 *
 * (3) White space
 *     Space " " and tab "\t" at the end of a line must use rule (1);
 *     in all other cases either rule (1) or (2) may be applied.
 *
 * (4) Line breaks 
 *     The (hard) line breaks in the input must be represented
 *     using "\r\n" in the output.
 *
 * (5) Soft line breaks
 *     Output lines may not be longer than 76 bytes. This can be enforced by
 *     inserting a soft line break (the string "=\r\n") in the output. This
 *     soft line break will disappear once the encoded string is decoded.
 *
 * The basic conversion algoritm is constructed using two important variables:
 *
 *  - an integer value ($remaining) indicating the number of bytes
 *    left in the current output line
 *
 *  - a boolean flag ($next_is_newline) indicating if the next input
 *    character is a $newline
 *
 * The variable $remaining keeps track of situations where the current
 * character (either as (1) General 8bit representation or (2) Literal
 * representation) might not fit on the current line (eg. 2 bytes left
 * requires an 8bit representation to be moved to the next output line).
 * The flag $next_is_newline is used to make the best posible use of the
 * available remaining space in the output, eg. if the current character
 * is exactly as long as the remaining space, we can output that character
 * on the current output line, because we are sure that it is the last
 * character on the current output line so there cannot be a soft return
 * next.
 *
 * Note that spaces (ASCII 32) and tabs (ASCII 9) are treated differently
 * depending on their position in the line. The rule is that both should
 * be represented as "=20" or "=09" at the end of an input line and that
 * it is allowed to use " " or "\t" when NOT at the end of an input line.
 * In the latter case, the output line will allways end with a soft line
 * break "=\r\n" which makes sure that there are not trailing spaces/tabs
 * in the output line anyway.
 *
 * Also note that the end of the input $s is also flagged via setting
 * $next_is_newline. This is an optimalisation which treats spaces and tabs
 * at the end of the input as if they were at the end of an input line,
 * ie. converting to "=20" or "=09". This means that the output will never
 * end with a space of a tab, even if the input does.
 *
 * Note that in case of a binary conversion the input character(s) that might
 * otherwise indicate a newline are to be considered as binary data. However,
 * if the data is completely binary, it probably doesn't make sense to use
 * Quoted-Printable in the first place (base64 would probably be a better
 * choice).
 *
 * Reference: see {@link http://www.ietf.org/rfc/rfc2045.txt}.
 *
 * @param string $s source string
 * @param bool $textmode TRUE means newlines count as hard line breaks, FALSE is binary data
 * @param string $newline native character indicating end of line
 * @param int $max_length indicates the limit for output lines (excluding the CRLF)
 * @return string encoded string according to RFC2045
 * @todo should we change the code to accomodate the canonical newline CRLF in the input?
 */
function quoted_printable($s,$textmode=TRUE,$newline="\n",$max_length=76) {
    static $crlf = "\r\n";
    static $soft_line_break = "=\r\n";
    if (($n = strlen($s)) == 0) {
        return '';
    }
    $max_length = max(4,min(76,intval($max_length))); // keep output lines between 4 and 76 chars
    $remaining = $max_length;
    $next_is_newline = (($textmode) && ($s{0} == $newline)) ? TRUE : FALSE;
    for ($t='',$i=0; $i<$n; ) {
        $c = ord($s{$i++});
        $curr_is_newline = $next_is_newline;
        // in textmode embedded newlines are flagged, but the end of the source is flagged in any mode
        $next_is_newline = ((($textmode) && ((($i < $n) && ($s{$i} == $newline)))) || ($i == $n)) ? TRUE : FALSE;
        if ($curr_is_newline) {
            $t .= $crlf;
            $remaining = $max_length;
        } elseif ($next_is_newline) {
            if (((33 <= $c) && ($c <= 126)) && ($c != 61)) {   // (2) Literal representation
                $t .= chr($c);
                --$remaining;                                  // could be 0, but next is newline, so no problem
            } elseif ($remaining >= 3) {                       // (1) General 8bit representation
                $t .= sprintf('=%02X',$c);
                $remaining -= 3;                               // could be 0, but next is newline, so no problem
            } else {                                           // (5) Soft return + (1) General 8bit representation
                $t .= $soft_line_break . sprintf('=%02X',$c);
                $remaining = $max_length - 3;
            }
        } else {
            if ((((33 <= $c) && ($c <= 126)) && ($c != 61)) || ($c == 32) || ($c == 9)) { // (3) Whitespace too
                if ($remaining > 1) {                          // (2) Literal representation
                    $t .= chr($c);
                    --$remaining;                              // at least 1, never 0
                } else {                                       // (5) Soft return + (2) Literal representation
                    $t .= $soft_line_break . chr($c);
                    $remaining = $max_length - 1;
                }
            } elseif ($remaining > 3) {                        // (1) General 8bit representation
                $t .= sprintf('=%02X',$c);
                $remaining -= 3;                               // at least 1, never 0
            } else {                                           // (5) Soft return + (1) General 8bit representation
                $t .= $soft_line_break . sprintf('=%02X',$c);
                $remaining = $max_length - 3;
            }
        }
    }
    return $t;
} // quoted_printable()


/** a small utility routine that returns a unique integer
 *
 * this generates a unique number (starting at 1). This number is guaranteed to be
 * unique during this http-request (or at least until the static variable $id overflows,
 * but that takes a while).
 * If the optional parameter $increment is FALSE, the latest id returned is
 * returned again.
 *
 * @param bool $increment optional indicates whether the static counter must be incremented
 * @return int a new unique value every time
 */
function get_unique_number($increment=TRUE) {
    static $id = 0;
    if ($increment) {
        ++$id;
    }
    return $id;
} // get_unique_number()


/** construct a link to appropriate legal notices as per AGPLv3 section 5
 *
 * This routine constructs ready-to-use HTML-code for a link to the
 * Appropriate Legal Notices, which are to be found in /program/about.html.
 * Depending on the highvisibility flag we either generate a text-based
 * link or a clickabel image.
 *
 * The actual text / image to use depends on the global constant WAS_ORIGINAL.
 * This constant is defined in /program/version.php and it should be TRUE for
 * the original version of Website@School and FALSE for modified versions.
 *
 * In the former case the anchor looks like 'Powered by Website@School', in the
 * latter case it will look like 'Based on Website@School', which is in line
 * with the requirements from the license agreement for Website@School, see
 * /program/license.html.
 *
 * IMPORTANT NOTE
 *
 * Please respect the license agreement and change the definition of
 *  WAS_ORIGINAL to FALSE if you modify this program (see /program/version.php).
 * You also should change the file '/program/about.html' and add a 'prominent
 * notice' of your modifications.
 *
 * Note: a comparable routine can be found in {@link install.php}.
 *
 * @param bool $text_only if TRUE we return a text-only link, otherwise a clickable image
 * @param string $m margin to improve readability of generated code
 * @return string ready-to-use HTML
 *
 */
function appropriate_legal_notices($text_only=FALSE, $m='') {
    global $CFG;
    if ($text_only) {
        $prefix = (WAS_ORIGINAL) ? 'Powered by ' : 'Based on ';
        $anchor = 'Website@School';
    } else {
        $prefix = '';
        $anchor = sprintf('<img src="%s" width="%d" height="%d" border="0" alt="%s" title="%s">',
                      (WAS_ORIGINAL) ? $CFG->progwww_short.'/graphics/poweredby.png' : 
                                       $CFG->progwww_short.'/graphics/basedon.png',
                      (WAS_ORIGINAL) ? 280 : 255, 
                      (WAS_ORIGINAL) ? 35 : 35,
                      (WAS_ORIGINAL) ? 'Powered by Website@School' : 'Based on Website@School',
                      'The Website@School logo is a registered trademark of Vereniging Website At School');
    }
    return sprintf('%s%s<a href="%s" target="_blank">%s</a>',$m,$prefix,$CFG->progwww_short.'/about.html',$anchor);
} // appropriate_legal_notices()


/** massage a possibly relative URL to make it more qualified
 *
 * Here we perform some heuristics: if $url looks like it is relative,
 * we prepend the correct path (from $CFG) to it.
 *
 * Here a URL is considered relative when it does NOT start with a slash and it does
 * NOT start with a scheme followed by '://'. Additionaly, we make a distinction
 * between a relative URL starting with 'program/' (which indicates a static file
 * somewhere in the program directory) and other relative URLs (which are assumed
 * to start in the CMS Root Directory (the directory where index.php, admin.php & friends live).
 *
 * Note: according to RFC3986 a scheme must start with a letter and can contain only
 * letters, digits, '+', '-' or '.'. Note: all string operations here are ASCII; no UTF-8 issues here.
 *
 * If $fully_qualified is TRUE we always make a relative URL fully qualified.
 *
 * If $url starts with a slash, we must assume that the caller means some file relative to the
 * document root, or rather: relative to the top level directory embedded in $CFG->www. If we
 * have $url starting with a slash AND $full_qualified is TRUE, we extract the scheme and
 * authority from $CFG->www and prepend that to $url. This is a heuristic approach.
 *
 * Example 1:
 * 'program/styles/base.css' becomes 
 * '/program/styles/base.css' OR
 * 'http://exemplum.eu/program/styles/base.css'
 *
 * Example 2:
 * 'file.php/areas/exemplum/logo.jpg' becomes
 * '/file.php/areas/exemplum/logo.jpg' OR
 * 'http://exemplum.eu/file.php/areas/exemplum/logo.jpg'
 *
 * Example 3:
 * '/path/to/foo/bar/logo.jpg' becomes
 * '/path/to/foo/bar/logo.jpg' OR
 * 'http://exemplum.eu/path/to/foo/bar/logo.jpg'
 *
 * @param string $url the (possibly) relative URL to massage
 * @param bool $fully_qualified if TRUE forces the URL to contain a scheme, authority etc., else use shortened form
 * @return string the qualified URL
 */
function was_url($url,$fully_qualified=FALSE) {
    global $CFG;
    if (substr($url,0,1) == '/') {
        if ($fully_qualified) {
            $www = $CFG->www;
            if (($i = strpos($www,'://')) !== FALSE) {
                $url = ((($i = strpos($www,'/',$i+3)) === FALSE) ? $www : substr($www,0,$i)).$url;
            }
        }
    } elseif (preg_match('/^[A-Za-z][A-Za-z0-9\-+.]*:\/\//',$url) != 1) { // relative; does not start with 'scheme://'
        if (substr($url,0,8) == 'program/') {
            $url = (($fully_qualified) ? $CFG->progwww : $CFG->progwww_short).substr($url,7);
        } else {
            $url = (($fully_qualified) ? $CFG->www : $CFG->www_short).'/'.$url;
        }
    }
    return $url;
} // was_url()


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
 *
 * Furthermore, if the flag $fully_qualified is TRUE, we
 * include scheme and authority in the resulting URL, ie.
 * <code>
 * http://exemplum.eu/file.php/path/to/file.txt
 * </code>
 *
 * @parameter string $path the name of the file including path
 * @param bool $fully_qualified if TRUE forces the URL to contain a scheme, authority etc., else use shortened form
 * @return string ready to use URL
 */
function was_file_url($path,$fully_qualified=FALSE) {
    global $CFG;
    $components = explode('/',$path);
    $path = '';
    foreach($components as $component) {
        if (!empty($component)) {
            $path .= '/'.rawurlencode($component);
        }
    }
    $url = sprintf('%s/file.php%s%s',($fully_qualified) ? $CFG->www : $CFG->www_short,
                                     ($CFG->friendly_url) ? '' : '?file=',
                                     $path);
    return $url;
} // was_file_url()


/** construct a ready-to-use href which links to the node $node via index.php
 *
 * this routine creates a ready-to-use href that links to node $node, taking these
 * options into account:
 *  - the href is replaced with a bare '#' if we area in preview mode
 *  - the href is either fully qualified or abbreviated, depending on $qualified
 *  - the node_id is conveyed either as a proxy-friendly url or a simple parameter ?node=$node_id
 *  - if we use friendly url, $node_id always comes first in the path (without the word 'node')
 *  - if we use friendly url, $bookmark is appended as the last item in the path
 *  - the additional parameters (if any) are sandwiched between the node_id and the bookmark
 *
 * This routine mainly deals with constructing a friendly url taking parameters into account in the
 * form of path components. The node_id is conveyed as the first parameter and it has no associated
 * name, ie. the url is shortened from '/was/index.php/node/35' to '/was/index.php/35'.
 * All other parameters from the array $parameters (if any) are added as pairs:
 * '/key1/value1/key2/value2/key3/value3' etc. The last parameter added to this path
 * is based on the $bookmark or, if that is empty the node's title. The purpose of this parameter
 * is to create a URL that looks like a descriptive filename, which makes it easier for the visitor
 * to bookmark this page and still have a clue as to what the page is about. Otherwise this
 * parameter is not used at all; the 'real' navigation information is in the node_id and the
 * additional parameters.

 * Example:
 * <code>
 * was_node_url($tree[35]['record'],array('photo'=>'5'),'Picture of our field trip')
 * </code>
 * yields the following URL (when friendly urls are used):
 * <code>
 * /was/index.php/35/photo/5/Picture_of_our_field_trip.html
 * </code>
 * or (when friendly urls are not used):
 * <code>
 * /was/index.php?node=35&photo=5
 * </code>
 * The interesting bits are the node_id (35) and the photo_id (5). The string alias filename
 * 'Picture_of_our_field_trip.html' is merely a suggestion to the browser and is not used by W@S.
 *
 * @param array $node record straight from the database (or $tree)
 * @param array|null $parameters additional parameters for the url (path components in friendly url mode)
 * @param string $bookmark the basis for a visual clue to identify the node (in friendly url mode only)
 * @param bool $preview if TRUE, the href is replaced with a bare '#' to obstruct navigation in preview mode
 * @param bool $qualified if TRUE use the scheme and authority, otherwise use the short(er) form without scheme/authority
 * @return string ready-to-use string holding the url
 * @uses $CFG
 * @uses friendly_bookmark()
 */
function was_node_url($node=NULL,$parameters=NULL,$bookmark='',$preview=FALSE,$qualified=FALSE) {
    global $CFG;
    if ($preview) {
        $href = '#';
    } else {
        $href = (($qualified) ? $CFG->www : $CFG->www_short).'/index.php';
        if ($CFG->friendly_url) {
            // 1 -- maybe add the (unnamed) node parameter
            if (!empty($node)) {
                $node_id = intval($node['node_id']);
                $title = $node['title'];
                $href .= '/'.strval($node_id);
            } else {
                $title = '';
            }
            // 2 -- maybe proceed with other, named parameters
            if (!empty($parameters)) {
                foreach($parameters as $k => $v) {
                    $k = strval($k);
                    $v = strval($v);
                    if (($k != '') && ($v != '')) {
                        $href .= '/'.rawurlencode(strtr($k,'/?+%','____')).'/'.rawurlencode(strtr($v,'/?+%','____'));
                    }
                }
            }
            // 3 -- maybe finish with the superfluous bookmark-filename
            if (($bookmark_filename = friendly_bookmark((empty($bookmark)) ? $title : $bookmark)) != '') {
                $href .= '/'.rawurlencode(strtr($bookmark_filename,'/?+%','____'));
            }
        } else {
            if (!empty($node)) {
                $node_id = intval($node['node_id']);
                if (!is_array($parameters)) {
                    $parameters = array();
                }
                $parameters = array_merge(array('node' => strval($node_id)),$parameters); // node goes first
            }
            $href = href($href,$parameters);
        }
    }
    return $href;
} // was_node_url()


/** construct an alphanumeric string from a (node) title yielding a readable bookmark filename
 *
 * this strips everything from $title except alphanumerics. Runs of other characters
 * are translated to a single underscore. Length of result is limited to
 * a length of $maxlen bytes (default 50). This includes the length of the extension $ext.
 *
 * Note that the $title is UTF-8 and may contain non-ASCII characters.
 * Ths routine deals with that situation by first converting the UTF-8
 * string to ASCII as much as possible (e.g. convert 'e-aigu' to plain 'e')
 * and subsequently converting all remaining non-letters/digits to a underscores.
 *
 * Finally the result is stripped from leading/trailing underscores. If this
 * yields a non-empty string, the extension $ext (default '.html') is appended.
 *
 * Note: this route works best with latin-like text; if $title is completely
 * written in Chinese (or other UTF-8 characters without a corresponding ASCII
 * replacement) we end up with a single underscore which is subsequently trim()'ed,
 * yielding an empty string and no $ext added. I am not sure what to do about that.
 *
 * Note: the extension is not checked for non-alphanumerics because this is the
 * responsability of the caller to provide a decent $ext if the default '.html' is
 * not used.
 *
 * @param string $title input text
 * @param int $maxlen the maximum length of the result
 * @param int $ext the filename extension added to a non-empty result
 * @return string string with only alphanumerics and underscores, max $maxlen chars
 */
function friendly_bookmark($title,$maxlen=50,$ext='.html') {
    $src = utf8_strtoascii($title);
    $tgt = '';
    $tgt_len = strlen($ext); // already count the extension length against maxlen
    $subst = FALSE;
    $n = utf8_strlen($src);
    for ($i = 0; (($i < $n) && ($tgt_len < $maxlen)); ++$i) {
        $c = utf8_substr($src,$i,1);
        if (ctype_alnum($c)) {
            $tgt .= $c;
            $tgt_len++;
            $subst = FALSE;
        } else {
            if (!$subst) {
                $tgt .= "_";
                $tgt_len++;
                $subst = TRUE;
            }
        }
    }
    if (($tgt = trim($tgt,'_')) != '') {
        $tgt .= $ext;
    }    
    return $tgt;
} // friendly_bookmark()


/** retrieve a named parameter from the friendly URL
 *
 * This routine attempts to parse the PATH_INFO server variable
 * and extract the parameters and values stored in the path components.
 * (see also {@link was_node_url()}).
 *
 * Example: the URL
 * <code>
 * /was/index.php/35/photo/5/Picture_of_our_field_trip.html
 * </code>
 * is broken down as follows:
 *  - /35 is the first non-empty parameter and also is completely numeric and hence
 *    interpreted as a node_id;
 *  - /photo/5 is considered a key-value-pair with key=photo and value=5;
 *  - /Picture_of_our_field_trip.html is the last component and is discarded
 * The static array which caches the results of the parsing will contain this:
 * $parameters = array('node' => 35, 'photo' => 5);
 *
 * Note that all parameters are checked for valid UTF-8.
 * If either key or value is NOT UTF-8, the pair is silently discarded.
 * This prevents tricks with overlong sequences and other UTF-8 black magic.
 *
 * Once the parsed friendly path is cached the parameter $name is looked up.
 * If found, the corresponding value is returned. If it is not found, $default_value
 * is returned.
 *
 * The cache is rebuilt if $force is TRUE (should never be necessary)
 *
 * Note: the parameter 'node' is a special case: if it is specified it is the first
 * parameter. This parameter otherwise is unnamed.
 *
 * @param string $name the parameter we need to look for
 * @param mixed $default_value is returned if the parameter was not found
 * @param bool $force if TRUE forces the parsing to be redone
 * @return mixed either the $default_value or the value of the named parameter
 *
 */
function get_friendly_parameter($name,$default_value=NULL,$force=FALSE) {
    global $CFG;
    static $parameters = NULL;
    if ((is_null($parameters)) || ($force)) {
        $parameters = array();
        if (($CFG->friendly_url) && (isset($_SERVER['PATH_INFO']))) {
            $raw_params = explode('/',$_SERVER['PATH_INFO']);
            $n = sizeof($raw_params);
            $index = 0;
            while (($index < $n) && (empty($raw_params[$index]))) {
                ++$index;
            }
            if (($index < $n) && (utf8_validate($raw_params[$index])) && (is_numeric($raw_params[$index]))) {
                $parameters['node'] = intval($raw_params[$index]);
                ++$index;
            }
            while ($index < $n - 1) {
                $key = (utf8_validate($raw_params[$index])) ? strval($raw_params[$index]) : '';
                ++$index;
                $val = (utf8_validate($raw_params[$index])) ? strval($raw_params[$index]) : '';
                ++$index;
                if ($key != '') {
                    $parameters[$key] = $val;
                }
            }
            // at this point there may be one last component ($raw_params[$n-1]); we'll ignore that unused parameter
        }
    }
    return (isset($parameters[$name])) ? $parameters[$name] : $default_value;
} // get_friendly_parameter()



/** determine whether a directory is empty (free from (user)files)
 *
 * this scans the directory $CFG->datadir.$path to see if it is empty,
 * i.e. does not contain any (user)files. Returns TRUE if empty, FALSE otherwise.
 * The (user) files we look at are those that are not filtered out:
 * - . and .. (directory housekeeping)
 * - index.html of 0 bytes ('protects' directory from prying eyes)
 * - symbolic links
 * - thumbnails (filenames starting with THUMBNAIL_PREFIX)
 * This filtering is the same as that in the file manager (see {@link filemanager.class.php}).  
 *
 * @param string $path the directory path relative to $CFG->datadir, e.g. '/areas/exemplum' or '/users/acackl'
 * @return bool TRUE if no (user)files or subdirectories exist in $path, FALSE otherwise
 */
function userdir_is_empty($path) {
    global $CFG;
    $retval = TRUE; // assume success
    $full_path = $CFG->datadir.$path;
    if (($handle = @opendir($full_path)) === FALSE) {
        logger(sprintf("%s(): cannot open directory '%s'",__FUNCTION__,$path));
        return FALSE;
    }
    while (($entryname = readdir($handle)) !== FALSE) {
        $full_entryname = $full_path.'/'.$entryname;
        if (($entryname == '.') || ($entryname == '..') || (is_link($full_entryname))) {
            continue;
        } elseif (is_file($full_entryname)) {
            $filesize = filesize($full_entryname);
            if ((($entryname == 'index.html') && ($filesize == 0)) ||
                (substr($entryname,0,strlen(THUMBNAIL_PREFIX)) == THUMBNAIL_PREFIX)) {
                continue;
            } else {
                $retval = FALSE;
                break;
            }
        } elseif (is_dir($full_entryname)) {
            $retval = FALSE;
            break;
        }
    }
    @closedir($handle);
    return $retval;
} // userdir_is_empty()


/** remove an 'empty' directory that used to contain (user)files
 *
 * this removes the left-over files in the directory $CFG->datadir.$path
 * and subsequently the directory itself. The allowable left-over files
 * are those that are skipped in {@link userdir_is_empty()}.
 * The (user) files we look at are those that are filtered out:
 * - . and .. (directory housekeeping)
 * - index.html of 0 bytes ('protects' directory from prying eyes)
 * - symbolic links
 * - thumbnails (filenames starting with THUMBNAIL_PREFIX)
 * This filtering is the same as that in the file manager (see {@link filemanager.class.php}).  
 *
 * Note that any symbolic links are deleted too.
 *
 * @param string $path the directory path relative to $CFG->datadir, e.g. '/areas/exemplum' or '/users/acackl'
 * @return bool TRUE on success, FALSE otherwise
 */
function userdir_delete($path) {
    global $CFG;
    $retval = TRUE; // assume success
    $full_path = $CFG->datadir.$path;

    // 1 -- setup shop
    if (($handle = @opendir($full_path)) === FALSE) {
        logger(sprintf("%s(): cannot open directory '%s'",__FUNCTION__,$path));
        return FALSE;
    }
    // 2 -- get rid of the left-over files
    while (($entryname = readdir($handle)) !== FALSE) {
        if (($entryname == '.') || ($entryname == '..')) { 
            continue;
        }
        $path_entryname = $path.'/'.$entryname;
        $full_entryname = $full_path.'/'.$entryname;
        if ((is_link($full_entryname)) ||
            ((is_file($full_entryname)) && ($entryname == 'index.html') && (filesize($full_entryname) == 0)) ||
            (substr($entryname,0,strlen(THUMBNAIL_PREFIX)) == THUMBNAIL_PREFIX)) {
            if (@unlink($full_entryname)) {
                logger(sprintf("%s(): success unlinking '%s'",__FUNCTION__,$path_entryname),WLOG_DEBUG);
            } else {
                logger(sprintf("%s(): cannot unlink '%s'",__FUNCTION__,$path_entryname));
                $retval = FALSE;
            }
        } else {
            logger(sprintf("%s(): weird: spurious directory entry '%s'; shouldn't happen",__FUNCTION__,$path_entryname));
            $retval = FALSE;
        }
    }
    @closedir($handle);

    // 3 -- get rid of the directory itself
    if (!@rmdir($full_path)) {
        $retval = FALSE;
    }
    if ($retval) {
        logger(sprintf("%s(): success removing '%s/'",__FUNCTION__,$path),WLOG_DEBUG);
    } else {
        logger(sprintf("%s(): cannot remove '%s/'",__FUNCTION__,$path));
    }
    return $retval;
} // userdir_delete()


/** calculate hmac according to RFC2104 (February 1997)
 *
 * Note: strings $opad and $ipad are created by simply copying $key
 * The contents are not important because we overwrite the contents
 * in the loop anyway.
 *
 * @param string $key (shared) secret key
 * @param string $message
 * @param bool $raw TRUE return binary hmac, FALSE hexadecimal 
 * @param function $hash either sha1 (default) or md5
 * @return string hashed message authentication code of $message
 */
function hmac($key, $message, $raw=FALSE, $hash="sha1") {
    $bs = 64;
    if (($n = strlen($key)) > $bs) {
        $n = strlen($key=pack('H*', $hash($key)));
    }
    if ($n < $bs) {
        $key .= str_repeat(chr(0), $bs-$n);
    }
    $opad = $ipad = $key; // quickly create strings of length $bs 
    for ($i=0; $i < $bs; ++$i) {
        $c = ord($key[$i]);
        $opad[$i] = chr(0x5C ^ $c);
        $ipad[$i] = chr(0x36 ^ $c);
    }
    $hmac = $hash($opad.pack('H*', $hash($ipad.$message)));
    return ($raw) ? pack('H*',$hmac) : $hmac;
} /* hmac() */

?>