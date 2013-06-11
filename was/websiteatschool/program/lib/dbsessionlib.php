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

/** /program/lib/dbsessionlib.php - functions to keep PHP-sessions in the database
 *
 * This file provides the functions to handle sessions via the database
 * rather than via files or other standard PHP-mechanisms.
 *
 * Useful information about storing sessions can be found in these user
 *  comments: http://php.net/manual/en/function.session-set-save-handler.php
 *
 * Important issues:
 *  - there is an issue with writing session data when using
 *    a database class because the database class is already destroyed
 *    when PHP tries to write the session data. Workaround: call
 *    session_commit() (alias for session_write_close()) near the end
 *    of the script.
 *  - there is a security risk when the script simply accepts any
 *    session id that is presented via a cookie; a session should
 *    exist/be created before data is written and the session key
 *    should have been created in a previous call en thus be present
 *    in the database
 *  - different versions of PHP handle callback parameters differently
 *    where object methods are involved. The most generic way to work
 *    around these incompatibilities is to use global functions for
 *    open, close, etc. rather than methods in some session class.
 *  - session keys should be sanitised before manipulating the database
 *    in order to prevent an SQL injection.
 *  - session.auto_start may make it impossible to substitute our own
 *    session handlers if it is set in php.ini
 *  - how about locking a session? 
 *
 * There is a difference between the maximum session duration and the session
 * time out. A session could last for 'duration' seconds but only if there
 * is still activity at least every timeout seconds. There should be a maximum
 * session lifetime of say 24 hours. There also should be a timeout of say
 * 60 minutes.
 *
 * Sessions are stored in a table called 'sessions'. This table is
 * defined as follows:
 * <pre>
 * session_id       serial
 * session_key      varchar(172)
 * session_data     longtext
 * user_id          int unsigned 
 * user_information varchar(255)
 * ctime            datetime
 * atime            datetime
 * primary key(session_id)
 * foreign key(user_id) references users(user_id)
 * unique index(session_key)
 * </pre>
 *
 * Note: the size of the session_key was reduced from 255 to 172 after version 2011051100
 * to prevent database problems (see {@link update_core_2011092100()}).

 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: dbsessionlib.php,v 1.6 2013/06/11 11:26:05 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** setup database based handlers for session management
 *
 * this is basically shorthand for session_set_save_handler()
 * this routine replaces the existing session handlers
 * with the handlers specified below in this file.
 *
 * @param string the name of the session (usually 'PHPSESSID' in generic PHP-applications) 
 * @return bool FALSE on failure, TRUE otherwise
 */
function dbsession_setup($session_name) {
    session_name($session_name);
    return session_set_save_handler(
        'dbsession_open',
        'dbsession_close',
        'dbsession_read',
        'dbsession_write',
        'dbsession_destroy',
        'dbsession_garbage_collection');
} // dbsession_setup()


/** create a new session in the session table, return the unique sessionkey
 *
 * this creates generates a new unique session key and stores it in a new record
 * in the sessions table. Additional information is recorded in the new record too:
 * the user_id and auxiliary information. This information makes that a session
 * can always be linked to a particular user (which is handy when dealing with
 * locked pages, etc.). This routine attempts to create a unique session key
 * a number of times. If it doesn't work out, the routine returns FALSE.
 *
 * the optional parameter $user_information can be used to store additional
 * information about this user, e.g. the IP-address. This is useful for generating
 * messages like 'Node xxx is currently locked by user YYYY logged in from ZZZZ'.
 *
 * Note that the generation of a unique session key is salted with both the main
 * url of this website and the special salt that was recorded once during installation
 * time. Also, pseudo-random data is added via rand(). Hopefully this will be
 * hard to guess, even though we use md5() to condense this (semi-)random information
 * into only 128 bits.
 *
 * @param int link to the users table, identifies the user that started the session
 * @param string (optional) auxiliary information about the user, e.g. the IP-address
 * @return bool|string FALSE on error, the unique session key ('token') on success
 * @uses $CFG
 * @uses $DB
 * @todo should we also record the IP-address of the user in the session record?
 *       In a way this is a case of information leak, even though it is only between
 *       authenticated users. Mmmm...
 */
function dbsession_create($user_id,$user_information='') {
    global $CFG,$DB;
    $salt = $CFG->www;
    if (isset($CFG->salt)) { $salt .= $CFG->salt; }
    $current_time = strftime('%Y-%m-%d %T'); // current date/time as string yyyy-mm-dd hh:mm:ss
    for ($tries = 5; ($tries > 0); --$tries) {
        $new_sessionkey = md5($salt.uniqid('',TRUE).rand());
        $retval = db_insert_into('sessions',array(
            'session_key' => $new_sessionkey,
            'session_data' => '',
            'user_id' => $user_id,
            'user_information' => $user_information,
            'ctime' => $current_time,
            'atime' => $current_time));
        if ($retval !== FALSE) {
            return $new_sessionkey;
        }
    }
    return FALSE;
} // dbsession_create()


/** check to see if $session_key exists in the session table
 *
 * This checks the existence of a session in the sessions table.
 * Session keys are only generated from {@link dbsession_create()}. 
 * This prevents us accepting spurious session keys via a 
 * manipulated cookie. If the session key does not exist,
 * the call fails and FALSE is returned.
 *
 * @param string the unique session_key that identifies the session
 * @return bool FALSE on failure or non-existing, TRUE otherwise
 *
 */
function dbsession_exists($session_key) {
    $retval = db_select_single_record('sessions','session_id',array('session_key' => $session_key));
    return ($retval === FALSE) ? FALSE : TRUE;
} // dbsession_exists()


/** retrieve the session_id (pkey) that corresponds with session_key
 *
 * this is very similar to {@link dbsession_exists()}. This routine returns
 * the actual session_id integer, whereas dbsession_exists() only returns TRUE.
 *
 * @param string the unique session_key that identifies the session
 * @return int|bool FALSE on failure or non-existing, session_id (primary key) otherwise
 */
function dbsession_get_session_id($session_key) {
    $retval = db_select_single_record('sessions','session_id',array('session_key' => $session_key));
    return ($retval === FALSE) ? FALSE : intval($retval['session_id']);
} // dbsession_get_session_id()


/** 'open' a session
 *
 * this 'opens' a session. note that this function is unable to identify the
 * session because it is only presented with
 *  - the $save_path (which is relevant only with file-based session handlers)
 *  - the $session_name (which is the _name_ of the session, but not the _token_)
 * there is no way to let this function do anything useful, so it boils down
 * to a dummy always returning TRUE.
 * The function {@link dbsession_close()} has the same uselessness, so they are
 * a perfect pair.
 *
 * @param string (unused) pathname relevant for file based session handler
 * @param string (unused) the non-unique session_name that identifies the cookie in the user's browser
 * @return bool always returns TRUE
 *
 */
function dbsession_open($save_path, $session_name) {
    return TRUE;
} // dbsession_open()


/** 'close' a session that was opened with dbsession_open() before
 *
 * Since this function has no way to tell _which_ session should be closed,
 * it is utterly useless (but it has to exist to satisfy session_set_save_handler())
 * The function {@link dbsession_open()} has the same uselessness, so they are
 * a perfect pair.
 *
 * @return bool always TRUE because this is really a dummy function
 * @todo should we do something with locking the session record from
 *       dbsession_open() until dbsession_close()? For now, the session record
 *       is not locked in any way, so the latest call gets to keep its changes
 *       Mmmm....
 *
 */
function dbsession_close() {
    return TRUE;
} // dbsesseion_close()


/** read the (serialised) session data from the database
 *
 * @param string the unique session_key that identifies the session
 * @return string empty string on failure, existing session data otherwise
 */
function dbsession_read($session_key) {
    $retval = db_select_single_record('sessions','session_data',array('session_key' => $session_key));
    if ($retval === FALSE) {
        return '';
    }
    // updating the atime of the session extends the session lifetime,
    // but a failure while updating will be ignored (it's not _that_ important)
    $current_time = strftime('%Y-%m-%d %T');
    db_update('sessions',array('atime' => $current_time),array('session_key' => $session_key));
    return (string) $retval['session_data'];
} // dbsession_read()


/** write the (serialised) data to the database
 *
 *
 * @param string the unique session_key that identifies the session
 * @param string the string with (serialised) session variables
 * @return bool FALSE on failure, TRUE otherwise
 */
function dbsession_write($session_key,$session_data) {
    $current_time = strftime('%Y-%m-%d %T');
    $retval = db_update('sessions',
                        array('atime' => $current_time,'session_data' => $session_data),
                        array('session_key' => $session_key));
    if ($retval === FALSE) {
        return FALSE;
    } elseif ($retval != 1) {
        return FALSE;
    } else {
        return TRUE;
    }
} // dbsession_write()


/** remove a session record from the sessions table (it should still exist)
 *
 * remove the specified record from the sessions table. it is an error if
 * the record does not exist.
 *
 * @param string the unique session_key that identifies the session
 * @return bool FALSE on failure, TRUE otherwise
 */
function dbsession_destroy($session_key) {
    $retval = db_delete('sessions',array('session_key' => $session_key));
    if ($retval === FALSE) {
        return FALSE;
    } elseif ($retval != 1) {
        return FALSE;
    } else {
        return TRUE;
    }
} // dbsession_destroy()


/** remove all sessions that are last accessed more than $time_out seconds ago
 *
 * @param int the time-out value to automatically expire sessions (in seconds)
 * @return bool FALSE on failure, TRUE otherwise
 * @uses dbsession_remove_obsolete_sessions()
 */
function dbsession_garbage_collection($time_out) {
    global $CFG;
    $retval = dbsession_remove_obsolete_sessions($time_out,'atime');
    if ($retval) {
        $seconds = max($time_out,intval($CFG->session_expiry));
        $retval = dbsession_remove_obsolete_sessions($seconds,'ctime');
    }
    return $retval;
} // dbsession_garbage_collection()


/** remove all sessions that were created more than $max_life seconds ago
 *
 * not only are sessions terminated when there is no more activity for
 * $time_out seconds (@see dbsession_garbage_collection()) but also the total
 * lifetime of a session is limited to $life_time seconds. This routine is
 * not part of the required session handlers but it can be called periodically
 * (@see cron.php}.
 *
 * @param int the lifetime value to automatically expire sessions (in seconds)
 * @return bool FALSE on failure, TRUE otherwise
 * @uses dbsession_remove_obsolete_sessions()
 */
function dbsession_expire($max_lifetime) {
    return dbsession_remove_obsolete_sessions($max_lifetime,'ctime');
} // dbsession_expire()


/** workhorse for removing obsolete sessions from the database
 *
 * this logs and subsequently removes obsolete sessions from the sessions table
 * It is a workhorse function for both {@link dbsession_garbage_collection()}
 * and {@link dbsession_expire()}.
 *
 * Session records are removed when the $time_field in the sessions table
 * contains a date/time that is older than $seconds seconds ago. Before
 * the records are removed, we retrieve them and log pertinent information from
 * each one via logger(), for future reference.
 *
 * Note that we try to continue with deleting records, even if the logging appears
 * to have generated errors.
 *
 * @param int $seconds the period of time after which the session is obsolete
 * @param string $time_field the field to use for time comparisons: either 'atime' or 'ctime'
 * @return bool TRUE if everything went well, FALSE otherwise
 *
 */
function dbsession_remove_obsolete_sessions($seconds,$time_field) {
    global $DB;
    $retval = TRUE; // assume success

    $xtime = strftime('%Y-%m-%d %T',time()-intval($seconds));
    $table_users = $DB->prefix.'users';
    $table_sessions = $DB->prefix.'sessions';
    $sql = "SELECT s.session_id, s.user_id, u.username, s.user_information, s.ctime, s.atime ".
           "FROM $table_sessions AS s LEFT JOIN $table_users AS u ON s.user_id = u.user_id ".
           "WHERE s.$time_field < ".db_escape_and_quote($xtime)." ".
           "ORDER BY s.$time_field";
    $DBResult = $DB->query($sql);
    if ($DBResult === FALSE) {
        if ($DB->debug) { trigger_error($DB->errno.'/\''.$DB->error.'\''); }
        $retval = FALSE;
    }
    if ($retval !== FALSE) {
        $records = $DBResult->fetch_all_assoc('session_id');
        $DBResult->close();
        if ($records === FALSE) {
            if ($DB->debug) { trigger_error($DB->errno.'/\''.$DB->error.'\''); }
            $retval = FALSE;
        }
    }
    if ($retval === FALSE) {
        logger('dbsession_remove_obsolete_sessions(): errors retrieving obsolete sessions',WLOG_DEBUG);
    } elseif (sizeof($records) < 1) {
        logger('dbsession_remove_obsolete_sessions(): nothing to do',WLOG_DEBUG);
    } else {
        foreach($records as $session_id => $record) {
            $msg = sprintf("session %d %s (%d seconds) [login %s(%d) from %s on %s, last access %s]",
                   $record['session_id'],
                   ($time_field == 'ctime') ? 'terminated' : 'timed out',
                   $seconds,
                   empty($record['username']) ? '?' : $record['username'],
                   $record['user_id'],
                   $record['user_information'],
                   $record['ctime'],
                   $record['atime']);
            logger($msg);
        }
        logger('dbsession_remove_obsolete_sessions(): number of removed sessions: '.sizeof($records),WLOG_DEBUG);
    }
    unset($records);
    $where = $time_field.' < '.db_escape_and_quote($xtime);
    if (db_delete('sessions',$where) === FALSE) {
        return FALSE;
    } else {
        return $retval;
    }
} // dbsession_remove_obsolete_sessions()

?>