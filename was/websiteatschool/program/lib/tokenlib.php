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

/** /program/lib/tokenlib.php - functions to manipulate unique tokens via the database
 *
 * This file provides the functions to manipulate tokens stored in the database.
 * These tokens are used to create unique instances of dialogs (forms) making it
 * impossible to POST data to a form without first retrieving the individual
 * (blank) form to start with.
 *
 * Tokens are stored in a table called 'tokens' which is defined as follows:
 * <pre>
 * +--------------+--------------+------+-----+---------+----------------+
 * | Field        | Type         | Null | Key | Default | Extra          |
 * +--------------+--------------+------+-----+---------+----------------+
 * | token_id     | int(11)      |      | PRI | NULL    | auto_increment |
 * | token_key    | varchar(60)  |      | MUL |         |                |
 * | token_ref    | varchar(60)  |      |     |         |                |
 * | token_start  | int(11)      |      |     | 0       |                |
 * | token_end    | int(11)      |      |     | 0       |                |
 * | token_expire | int(11)      |      |     | 0       |                |
 * | remote_addr  | varchar(150) |      |     |         |                |
 * | data         | longtext     | YES  |     | NULL    |                |
 * +--------------+--------------+------+-----+---------+----------------+
 * </pre>
 *
 * Note: this table was added in v0.90.5 (June 2013), initially for the mailpage module.
 *
 * The following functions are defined.
 * <pre>
 * $token_id = token_create($reference, &$token_key, $delay_start, $delay_end, $remote_addr);
 * $token_id = token_lookup($reference, $token_key, &$timer_start, &$timer_end, &$remote_addr);
 * $retval   = token_store($token_id, $data);
 * $retval   = token_fetch($token_id, &$data);
 * $retval   = token_destroy($token_id);
 * $retval   = token_garbage_collect();
 * </pre>
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: tokenlib.php,v 1.1 2013/06/27 13:35:22 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** create a new record in the tokens table, return the unique token_id
 *
 * this creates generates a new unique token_key and stores it in a new record
 * in the tokens table. Additional information is recorded in the new record too:
 *  - the dialog/form reference
 *  - the earliest unix timestamp POST'ed data will be accepted (based on $delay_start)
 *  - the latest unix timestamp POST'ed data will be accepted (based on $delay_end)
 *  - the unix timestamp after which this record can be deleted (via garbage collection)
 *  - the IP-address of the current caller (from $_SERVER['REMOTE_ADDR'])
 *
 * This routine creates a unique token_key. This key consists of the hexadecimal
 * representation of the token_id (which is the unique serial of the record) followed
 * by a quasi-random string of hexadecimal characters. There is no way there will be
 * a repeat ever, unless the serial wraps round. I assume (dangerous, I know) that
 * this is very unlikely to happen any time soon. In order to further obfuscate the
 * generated key the (sequential) serial number is xor'ed with 878133331 before the
 * random string is appended.
 *
 * The parameter $reference is used to further limit the chance that POST'ing a
 * rogue token_key could influence another, valid $token_key. This $reference
 * could be a number identifying a particular dialog definition, e.g. by using
 * the filename and a line number in a define().
 *
 * Finally, the IP-address of the visitor is recorded. This can be used in the
 * future to limit brute force attacks on a form. Currently it is not used other
 * than to add to the mail that is sent via the mailpage module. Note that by default
 * we use the IP-address from $_SERVER but the caller is free to substitute something
 * else, perhaps a canonical IPv6-address.
 *
 * We call the garbage collector from here. This keeps the table clean. Furthermore,
 * we have got the time: the user has to wait at least $delay_start seconds before
 * she can submit anything, so I think there is no rush. YMMV.
 *
 * @param string $reference is an identifier of the dialog requesting a token
 * @param string &$token_key a generated unique identifier based on the pkey of the token
 * @param int $delay_start seconds to wait before POST'ed data will be considered valid
 * @param int $delay_end seconds after which the dialog no longer accepts POST'ed data
 * @param string $remote_addr the IP-address this visitor is calling from
 * @return bool|int FALSE on error or primary key of the generated token in the tokens table.
 * @todo Should we enforce valid UTF8 in $reference and $ip_addr? We might have substr() trouble...
 */
function token_create($reference, &$token_key, $delay_start=10, $delay_end=14400, $ip_addr=NULL) {
    token_garbage_collect(); // always cleanup before we start something new
    $obfuscate    = 878133331;
    $delta_t0     = min(max(intval($delay_start),0        ), 3600); // make sure 0 <= $dt0 <= 3600
    $delta_t1     = min(max(intval($delay_end),  $delta_t0),86400); // make sure $dt0 <= $dt1 <= 86400
    $token_start  = time()       + $delta_t0;                       // 10 seconds from now
    $token_end    = $token_start + $delta_t1;                       // 10 + 14400 seconds from now (~4h)
    $token_expire = $token_end   + $delta_t1;                       // 10 + 28800 seconds from now (~8h)
    $token_ref    = substr($reference,0,60);
    if (is_null($ip_addr)) {
         $remote_addr = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '';
    } else {
         $remote_addr = substr($ip_addr,0,150);
    }
    $table       = 'tokens';
    $fields      = array(
        'token_ref'    => $token_ref,
        'token_start'  => $token_start,
        'token_end'    => $token_end,
        'token_expire' => $token_expire,
        'remote_addr'  => $remote_addr,
        'data'         => ''
        );
    $keyfield    = 'token_id';
    if (($token_id = db_insert_into_and_get_id($table,$fields,$keyfield)) === FALSE) {
        logger(sprintf('%s(): cannot create token \'%s\': %s',__FUNCTION__,$reference,db_errormessage()));
        return FALSE;
    }
    $token_key = sprintf('%08x%s', $token_id ^ $obfuscate, md5(quasi_random_string(32)));
    $fields = array('token_key' => $token_key);
    $where = array('token_id' => $token_id);
    return (db_update($table,$fields,$where) === FALSE) ? FALSE : $token_id;
} // token_create()


/** lookup $reference + $token_key in the table and retrieve token information
 *
 * This checks the existence of a token record in the tokens table.
 * This $token_key can only generated via {@link token_create()}.
 * Furthermore, we require that the $reference matches the one in
 * the record. This prevents us accepting spurious token keys via
 * manipulated requests. If the key does not exist, the call fails
 * and FALSE is returned, otherwise the token_id is returned +
 * data in the other parameters (timers, remote_addr, etc).
 *
 * Typical use:
 *
 * <code>
 * if (($token_id = token_lookup($ref, $key, $t0, $t1, $ip_addr)) === FALSE) {
 *   logger('error: no such token');
 * } elseif (time() < $0) {
 *   logger('error: whoa, not so fast there!');
 * } elseif ($t1 < time()) {
 *   logger('error: too late');
 * } else {
 *   logger('welcome visitor from '.$ip_addr);
 * }
 * </code>
 *
 * This allows for accepting POST'ed information only within the time window
 * defined by $t0 and $t1 and denying access otherwise with a precise cause
 * (no token, too early, too late).
 *
 * @param string $reference is an identifier of the dialog requesting a token
 * @param string $token_key a unique identifier based on the pkey of the token
 * @param int &$time_start unix timestamp indicating start of the valid interval
 * @param int &$time_end unix timestamp indicating end of the valid interval
 * @param string &$remote_addr the IP-address this visitor that created this token
 * @return bool FALSE on failure/non-existing, token_id otherwise and values in parameters
 */
function token_lookup($reference, $token_key, &$token_start, &$token_end, &$remote_addr) {
    $token_ref = substr($reference,0,60);
    $table = 'tokens';
    $fields = array('token_id', 'token_start', 'token_end', 'remote_addr');
    $where = array('token_key' => strval($token_key), 'token_ref' => $token_ref);
    if (($record = db_select_single_record($table,$fields,$where)) === FALSE) {
        return FALSE; 
    }
    $token_start = intval($record['token_start']);
    $token_end = intval($record['token_end']);
    $remote_addr = $record['remote_addr'];
    return intval($record['token_id']);
} // token_lookup()


/** retrieve the (unserialised) data from the database
 *
 * @param int $token_id the unique token_id (pkey) that identifies the token record
 * @param string &$data receives the unserialised data from the database
 * @return bool TRUE on success, FALSE otherwise
 */
function token_fetch($token_id, &$data) {
    $table = 'tokens';
    $fields = array('data');
    $where = array('token_id' => intval($token_id));
    if (($record = db_select_single_record($table, $fields, $where)) === FALSE) {
        return FALSE;
    }
    $data = unserialize($record['data']);
    return ($data === FALSE) ? FALSE : TRUE;
} // token_fetch()


/** write the (serialised) data to the database
 *
 * serialise and store $data in the database
 *
 * @param int $token_id the unique token_id (pkey) that identifies the token record
 * @param string $data holds the unserialised data to store
 * @return bool TRUE on success, FALSE otherwise
 */
function token_store($token_id, $data) {
    $table = 'tokens';
    $fields = array('data' => serialize($data));
    $where = array('token_id' => intval($token_id));
    if (($retval = db_update($table, $fields, $where)) !== FALSE) {
        $retval = ($retval == 1) ? TRUE : FALSE;
    }
    return $retval;
} // token_store()


/** remove a token record from the tokens table (it should still exist)
 *
 * remove the specified record from the table. it is an error if
 * the record does not exist.
 *
 * @param string $token_id the unique token_id (pkey) that identifies the record
 * @return bool FALSE on failure, TRUE otherwise
 */
function token_destroy($token_id) {
    $table = 'tokens';
    $where = array('token_id' => intval($token_id));
    if (($retval = db_delete($table, $where)) !== FALSE) {
        $retval = ($retval == 1) ? TRUE : FALSE;
    }
    return $retval;
} // token_destroy()


/** remove all expired tokens
 *
 * this removes all records that are expired. Since this
 * routine is also called from token_create() we have a big chance
 * to keep the tokens table clean. However: logging every delete()
 * may be a little too much so it is commented out.
 *
 * This way, the worst that can happen is that someone keeps GET'ting
 * a form that uses tokens every second for 8 hours in a row and
 * subsequently nobody ever visits that form again. In that case we
 * eventually have 8 x 3600 x 1 = 28800 garbage records. Oh well.
 *
 * OTOH: with all those crawlers and spiders on the WWW there is bound
 * to be a 'bot' visiting that form the next day, effectively cleaning
 * up for us. (Robots are a feature, not a bug...)
 *
 * @return bool FALSE on failure, TRUE otherwise
 * @todo This routine should be called from cron.php every once in a while
 */
function token_garbage_collect() {
    $table = 'tokens';
    $where = sprintf('token_expire < %d', time());
    if (($retval = db_delete($table,$where)) === FALSE) {
        logger(sprintf('%s(): error: %s',__FUNCTION__,db_errormessage()));
    } /* elseif ($retval > 0) {        
        logger(sprintf('%s(): # of records deleted: %d',__FUNCTION__,$retval),WLOG_DEBUG);
    } */
    return $retval;
} // token_garbage_collect()

?>