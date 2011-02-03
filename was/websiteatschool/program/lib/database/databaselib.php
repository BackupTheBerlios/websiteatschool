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

/** /program/lib/database/databaselib.php - database factory and database access routines
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: databaselib.php,v 1.2 2011/02/03 14:04:04 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** manufacture a database object
 *
 * This loads (includes) a specific database access class based
 * on the parameter $db_type. Currently 'mysql' is
 * the only option, but support for PostgreSQL or other databases
 * could be added in the future, see the code that is commented out
 *
 * Because Website@School is not meant to be an 'enterprisy
 * application', I decided against using an abstract class that
 * would be extended by a specific driver class which would
 * be instantiated via yet another factory type class; I'd like to
 * keep this as simple as possible while retaining the necessary
 * flexibility (and the option to add support for other databases).
 * Toolkits like Adodb seem overkill for this application program.
 *
 * This routine is called at a fairly early stage in the process.
 * It does not rely on any regular libraries which may be include()'ed
 * lateron. If no valid database type is specified, the function
 * returns FALSE, otherwise a database object is returned.
 *
 * Note that I did not use a singleton because I think that that
 * pattern is simply a fancy word for a global variable. YMMV.
 *
 * Note:
 * This file can be safely included from the {@link install.php} script,
 * allowing for database-manipulations via this abstraction layer rather
 * than directly going to the database. There are no dependencies on other
 * include()'s other than the actual database class files such as
 * mysql.class.php. Also, this file does not rely on the global variable $CFG,
 * which is also very convenient in the installer (where no CFG is available).
 *
 * @param string $prefix the tablename prefix
 * @param string $db_type (optional) which database to use, default 'mysql'
 * @param bool $debug if TRUE extra information is displayed (handy for debugging the code)
 * @return bool|object FALSE on error, or an instance of the $db_type database class
 * @todo perhaps add postgresql in a future version
 */
function database_factory($prefix,$db_type='mysql',$debug=FALSE) {
    switch($db_type) {
    case 'mysql':
        include_once(dirname(__FILE__).'/mysql.class.php');
        $o = new DatabaseMysql($prefix,$debug);
        break;

#   case 'postgresql':
#       include_once(dirname(__FILE__).'/postgresql.class.php');
#       $o = new DatabasePostgresql($prefix,$debug);
#       break;

    default:
        $o = FALSE;
        break;
    }
    return $o;
} // database_factory()


/** generate the necessary SQL-code for an INSERT INTO statement
 *
 * Construct an SQL-statement that inserts data into the
 * speficied table. This routine takes care of properly escaping
 * strings and also handles the addition of the table prefix
 *
 * @param string the name of the table to insert into (without prefix)
 * @param array an associative array with fieldnames and fieldvalues
 * @return string the constructed SQL-statement
 * @uses db_escape_and_quote
 */
function db_insert_into_sql($tablename,$fields) {
    global $DB;
    $field_names = '';
    $field_values = '';
    $glue = '';
    foreach($fields as $field => $value) {
        $field_names .= $glue.$field;
        $field_values .= $glue.db_escape_and_quote($value);
        $glue = ',';
    }
    $sql = 'INSERT INTO '.$DB->prefix.$tablename.'('.$field_names.') VALUES('.$field_values.')';
    return $sql;
} // db_insert_into_sql()


/** execute the necessary SQL-code for an INSERT INTO statement
 *
 * This excutes the SQL-statement created by {@link db_insert_into_sql()}.
 *
 * @param string the name of the table to insert into (without prefix)
 * @param array an associative array with fieldnames and fieldvalues
 * @return bool FALSE on error, # of affected rows otherwise
 * @uses $DB
 *
 */
function db_insert_into($tablename,$fields) {
    global $DB;
    return $DB->exec(db_insert_into_sql($tablename,$fields));
} // db_insert_into()


/** execute the necessary SQL-code for an INSERT INTO statement and return the last_insert_id
 *
 * This excutes the SQL-statement created by {@link db_insert_into_sql()}.
 * If all goes well, the value of the last inserted id is returned.
 *
 * @param string $tablename the name of the table to insert into (without prefix)
 * @param array $fields an associative array with fieldnames and fieldvalues
 * @param string $key_fieldname the name of the field that holds the primary key/serial
 * @return bool FALSE on error, last_insert_id on success
 * @uses $DB
 */
function db_insert_into_and_get_id($tablename,$fields,$key_fieldname='') {
    global $DB;
    $retval = $DB->exec(db_insert_into_sql($tablename,$fields));
    if (($retval !== FALSE) && ($retval == 1)) {
        $retval = db_last_insert_id($tablename,$key_fieldname);
    }
    return $retval;
} // db_insert_into_and_get_id()


/** generate the necessary SQL-code for a simple SELECT statement
 *
 * Construct an SQL-statement of the form:
 * SELECT field_list FROM table WHERE where_expression ORDER BY orderby_list
 *
 * The parameter $fields can be either a simple string, indicating
 * a single field or an array when more fields are to be selected
 *
 * The optional parameter $where is either a simple string with an appropriate
 * expression (without the keyword WHERE) or an array with fieldname/value-pairs.
 * In the latter case the clauses fieldname=value are AND'ed together.
 * If the specified values are string-like, they are properly quoted.
 * Boolean values are treated properly too.
 *
 * The optional parameter $order is either a simple string with an appropriate
 * list or expression (without the keyword ORDER BY) or an array with fieldnames
 * which will be used to create a comma-delimited string.
 *
 * Examples:
 * 1. db_select_sql('areas','title') yields 'SELECT title FROM was_areas'
 *
 * 2. db_select_sql('areas',array('title','theme_id'),array('is_visible' => TRUE),'sort_order') yields
 * 'SELECT title,theme_id FROM was_areas WHERE is_visible = 1 ORDER BY sort_order' (if SQL_TRUE is '1')
 *
 * @param string name of the table to select from (without prefix)
 * @param mixed fieldname or array with fieldnames to select
 * @param mixed a single clause or an array with fieldnames => values ((without the WHERE keyword)
 * @param mixed fieldname or array with fieldnames to determine sort order (without ORDER BY keyword)
 * @return string the constructed SQL-statement
 * @uses db_escape_and_quote
 */
function db_select_sql($tablename,$fields,$where='',$order='') {
    global $DB;

    $field_list = '';
    if (is_string($fields)) {
        $field_list = $fields;
    } elseif (is_array($fields)) {
        $field_list = '';
        $glue = '';
        foreach($fields as $field) {
            $field_list .= $glue.$field;
            $glue = ',';
        }
    }

    $where_clause = db_where_clause($where);

    $orderby_clause = '';
    if (!empty($order)) {
        if (is_string($order)) {
            $orderby_clause = ' ORDER BY '.$order;
        } elseif (is_array($order)) {
            $orderby_clause = ' ORDER BY ';
            $glue = '';
            foreach($order as $field) {
                $orderby_clause .= $glue.$field;
                $glue = ',';
            }
        }
    }
    $sql = 'SELECT '.$field_list.' FROM '.$DB->prefix.$tablename.$where_clause.$orderby_clause;
    return $sql;
} // db_select_sql()


/** fetch a single record from the database
 *
 * @param string name of the table to select from (without prefix)
 * @param mixed fieldname or array with fieldnames to select
 * @param mixed a single clause or an array with fieldnames => values ((without the WHERE keyword)
 * @param mixed fieldname or array with fieldnames to determine sort order (without ORDER BY keyword)
 * @return bool|array the selected record as an associative array or FALSE on error or not found
 * @uses db_select_sql
 */
function db_select_single_record($tablename,$fields,$where='',$order='') {
    global $DB;
    $sql = db_select_sql($tablename,$fields,$where,$order);
    if (($DBResult = $DB->query($sql,1)) === FALSE) {
        if ($DB->debug) { trigger_error($DB->errno.'/\''.$DB->error.'\''); }
        return FALSE;
    } elseif ($DBResult->num_rows != 1) {
        $DBResult->close();
        return FALSE;
    } else {
        $record = $DBResult->fetch_row_assoc();
        $DBResult->close();
        return $record;
    }
} // db_select_single_record()


/** fetch all selected records from the database in one array
 *
 * @param string $tablename name of the table to select from (without prefix)
 * @param mixed $fields fieldname or array with fieldnames to select
 * @param mixed $where a single clause or an array with fieldnames => values ((without the WHERE keyword)
 * @param mixed $order fieldname or array with fieldnames to determine sort order (without ORDER BY keyword)
 * @param string $keyfield field to use as the key in the returned array or empty for 0-based numeric array key
 * @param int $limit the maximum number of records to retrieve
 * @param int $offset the number of records to skip initially
 * @return bool|array the selected records as an array of associative arrays or FALSE on error or not found
 * @uses db_select_sql
 */
function db_select_all_records($tablename,$fields,$where='',$order='',$keyfield='',$limit='',$offset='') {
    global $DB;
    $sql = db_select_sql($tablename,$fields,$where,$order);
    if (($DBResult = $DB->query($sql,$limit,$offset)) === FALSE) {
        if ($DB->debug) { trigger_error($DB->errno.'/\''.$DB->error.'\''); }
        return FALSE;
    } else {
        $records = $DBResult->fetch_all_assoc($keyfield);
        $DBResult->close();
        return $records;
    }
} // db_select_all_records()


/** conditionally quote and escape values for use with a database table
 *
 * If $value is a string, it is escaped and single quotes are added at begin and end.
 * If $value is a boolean, it is converted into the correct value for the database using SQL_FALSE/SQL_TRUE
 * If $value is NULL, it is converted into the string 'NULL' (without quotes)
 * Otherwise the value is not changed.
 *
 * @param mixed string, boolean, null or other value to escape and quote
 * @return string|mixed quoted value
 * @see http://xkcd.com/327
 * @uses $DB
 */
function db_escape_and_quote($value) {
    global $DB;
    if (is_string($value)) {
        $quoted_value = '\''.$DB->escape($value).'\'';
    } elseif (is_bool($value)) {
        $quoted_value = ($value) ? SQL_TRUE : SQL_FALSE;
    } elseif (is_null($value)) {
        $quoted_value = 'NULL';
    } else {
        $quoted_value = $value;
    }
    return $quoted_value;
} // db_escape_and_quote()


/** update one or more fields in a table
 *
 * @param string the name of the table to update (without prefix)
 * @param array an associative array with fieldnames and fieldvalues
 * @param mixed a single clause or an array with fieldnames => values ((without the WHERE keyword)
 * @return bool|int FALSE on failure or the number of affected rows
 * @uses db_update_sql()
 */
function db_update($tablename,$fields,$where='') {
    global $DB;
    return $DB->exec(db_update_sql($tablename,$fields,$where));
} // db_update()


/** generate sql to update one or more fields in a table
 *
 * @param string the name of the table to update (without prefix)
 * @param array an associative array with fieldnames and fieldvalues
 * @param mixed a single clause or an array with fieldnames => values ((without the WHERE keyword)
 * @return string the constructed SQL-statement
 * @uses $DB
 */
function db_update_sql($tablename,$fields,$where='') {
    global $DB;
    $sql = 'UPDATE '.$DB->prefix.$tablename.' SET ';
    if (is_array($fields)) {
        $glue = '';
        foreach($fields as $field => $value) {
            $sql .= $glue.$field.' = '.db_escape_and_quote($value);
            $glue = ', ';
        }
    }
    $where_clause = db_where_clause($where);
    return $sql.$where_clause;
} // db_update_sql()


/** delete zero or more rows in a table
 *
 * @param string the name of the table to delete from (without prefix)
 * @param mixed a single clause or an array with fieldnames => values ((without the WHERE keyword)
 * @return bool|int FALSE on failure or the number of affected rows
 * @uses db_delete_sql()
 */
function db_delete($tablename,$where='') {
    global $DB;
    return $DB->exec(db_delete_sql($tablename,$where));
} // db_delete()


/** generate SQL to delete zero or more rows in a table
 *
 * @param string the name of the table to delete from (without prefix)
 * @param mixed a single clause or an array with fieldnames => values ((without the WHERE keyword)
 * @return string the constructed SQL statement
 */
function db_delete_sql($tablename,$where='') {
    global $DB;

    $sql = 'DELETE FROM '.$DB->prefix.$tablename;

    $where_clause = db_where_clause($where);
    return $sql.$where_clause;
} // db_delete_sql()


/** check boolean field in a database-independent way
 *
 * Various databases have different ways to indicate
 * TRUE or FALSE in boolean type of fields. MySQL uses a tinyint(1)
 * with values NULL, 0 and 1. PostgreSQL uses a lowercase 't'
 * or 'f' etc. We already have two database-specific definitions
 * for TRUE and FALSE: SQL_TRUE and SQL_FALSE. This routine
 * 'converts' the database-specific boolean values back to 
 * a form that is useable in PHP. This routine is able to test for
 * either TRUE or FALSE. Any other value is returned as NULL.
 *
 * Typical use:
 * <pre>
 * $user = db_select_single_record('users','is_active','user_id = 13');
 * if (db_bool_is(TRUE,$user['is_active'])) {
 *    ...
 * }
 * </pre>
 * @param bool|mixed value to test for, could be TRUE, FALSE or anything else
 * @param mixed the value of the variable to check
 *
 */
function db_bool_is($value,$variable_to_check) {
    if ($value === TRUE) {
        return ($variable_to_check == SQL_TRUE) ? TRUE : FALSE;
    } elseif ($value === FALSE) {
        return ($variable_to_check == SQL_FALSE) ? TRUE : FALSE;
    } else {
        return NULL;
    }
} // db_bool_is()


/** wrapper for DB->last_insert_id()
 *
 * This calls $DB->last_insert_id() in a way that should be
 * compatible with a future PostgreSQL database class.
 * Note that MySQL doesn't care about this. You can get away
 * with leaving table and field parameters empty (as is the default), but
 * for compatibility and documentation purposes you should use
 * the correct values.
 *
 * Typical use:
 * db_insert_into('users',$fields_array);
 * $user_id = db_last_insert_id('users','user_id');
 *
 * @param string name of the table (without prefix) in which a record was inserted
 * @param string name of the serial field to examine
 * @return int|bool FALSE on error, otherwise an integer identifying the inserted record
 * @uses $DB
 */
function db_last_insert_id($tablename='',$fieldname='') {
    global $DB;
    return $DB->last_insert_id($tablename,$fieldname);
} // db_last_insert_id()


/** construct a where clause from string/array, including the word WHERE
 *
 * this constructs a where clause including the word 'WHERE' based on
 * the string or array $where. 
 *
 * The optional parameter $where is either a simple string with an appropriate
 * expression (without the keyword WHERE) or an array with fieldname/value-pairs.
 * In the latter case the clauses fieldname=value are AND'ed together.
 * If the specified values are string-like, they are properly quoted.
 * Boolean values are treated properly too. NULL-values yield a standard 'IS NULL'
 * type of expression.
 *
 *
 * @param mixed a single clause or an array with fieldnames => values ((without the WHERE keyword)
 * @return string empty string or a WHERE-clause with leading space and the word 'WHERE'
 */
function db_where_clause($where='') {
    $where_clause = '';
    if (!empty($where)) {
        if (is_string($where)) {
            $where_clause = ' WHERE '.$where;
        } elseif (is_array($where)) {
            $where_clause = ' WHERE ';
            $glue = '';
            foreach($where as $field => $value) {
                if (is_null($value)) {
                    $where_clause .=  $glue.'('.$field.' IS NULL)';
                } else {
                    $where_clause .= $glue.'('.$field.' = '.db_escape_and_quote($value).')';
                }
                $glue = ' AND ';
            }
        }
    }
    return $where_clause;
} // db_where_clause()


/** retrieve the latest database error from $DB
 *
 * @return string the error number and the error message of the latest error from $DB or empty string if no error
 * @uses $DB;
 */
function db_errormessage() {
    global $DB;
    $errno = $DB->errno;
    if ($errno != 0) {
        $errormessage = strval($errno).'/'.$DB->error;
    } else {
        $errormessage = '';
    }
    return $errormessage;
} // db_errormessage()

?>