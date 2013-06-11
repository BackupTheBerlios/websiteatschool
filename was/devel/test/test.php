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

/** test.php - the main entrypoint for testing
 *
 * this is to 'play' with the system during development. It gives opportunity
 * to excercise various parts of the system and test things. This file could
 * be located anywhere, as long as there is a corresponding config.php in
 * the same directory which defines the necessary parameters in $CFG.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wastest
 * @version $Id: test.php,v 1.4 2013/06/11 11:25:08 pfokker Exp $
 */
/**
 * Valid entry points define WASENTRY; prevents direct access to include()'s.
 */
define('WASENTRY',__FILE__);

if (file_exists(dirname(__FILE__).'/config.php')) {
    unset($CFG); /* just to be sure that we are not tricked via stray globals */
    require_once(dirname(__FILE__).'/config.php');
} else {
    wastest_bailout();
    die;
}
/** this creates a working environment where we can conduct our tests */
require_once($CFG->progdir.'/init.php');


$tests = array(
    'wastest_dump_cfg' => 'Dump the $CFG object (including database password)',
    'wastest_dump_db'  => 'Dump the contents of the $DB object (also including passwords)',
    'wastest_drop_table' => 'Unconditionally drop the table \'foo\'',
    'wastest_create_table' => 'Create table \'foo\' (only a few fields)',
    'wastest_fill_table' => 'Add some records to \'foo\'',
    'wastest_dump_table' => 'Display all records from \'foo\'',
    'wastest_show_tables' => 'Result of MySQL SHOW TABLES',
    'wastest_tabledef_dump' => 'Raw dump of a big table definition which excercises create_table() in $Database',
    'wastest_tabledef_sql' => 'SQL-version of this big table definition',
    'wastest_table_create' => 'Actually create the big table via $DB->create_table()',
    'wastest_drop_big_table' => 'Drop the big table again',
    'wastest_describe_table' => 'Result of MySQL DESCRIBE tablename (using the big table)',
    'wastest_dump_table_description' => 'A neat HTML-table describing tabledefs in a generic way',
);

echo <<<EOT
<html>
<head>
<title>WAS Test</title>
</head>
<body>
EOT;
/* strategy: if they specified a test to do, do it. Then print the test menu (again) */
if (isset($_GET['test'])) {
    $current_test = $_GET['test'];
    perform_test($current_test);
    }
else {
    $current_test = '';
}
show_testmenu($current_test);
echo <<<EOT
</body>
</html>
EOT;
die;

/*
 *==================================================================
 */

function perform_test($test_to_do) {
    global $tests;
    if (isset($tests[$test_to_do])) {
        $test_to_do("<h2>{$tests[$test_to_do]}</h2>\n");
    } else {
        echo "<h2>Error</h2>\nunknown test to do: '".htmlspecialchars($test_to_do)."'\n";
    }
} // perform_test()

function show_testmenu($default_test) {
    global $tests,$PERFORMANCE,$DB;
    $s = "\n<p>\n<hr>\n<b>WAS TEST MENU</b> ".WAS_RELEASE.' ('.WAS_VERSION.")\n<hr>\n".
         "<form method=\"get\" action=\"{$_SERVER['PHP_SELF']}\">\n".
         "<input type=\"submit\" name=\"b\" value=\"OK\">\n<p>\n";
    foreach($tests as $k => $v) {
        $checked = ($k == $default_test) ? ' checked' : '';
        $s .= '<input type="radio" name="test" value="'.htmlspecialchars($k)."\"$checked>".htmlspecialchars($v)."<br>\n";
    }
    $s .= "<p>\nExtra 1: <input type=\"text\" name=\"extra1\" value=\"".
          htmlspecialchars($_GET['extra1'])."\" size=\"60\">\n";
    $s .= "<p>\n<input type=\"submit\" name=\"b\" value=\"OK\">\n</form>\n<hr>\n";
    // statistics
    $PERFORMANCE->time_stop = microtime();
    $s .= "Script execution time: <b>".
          diff_microtime($PERFORMANCE->time_start,$PERFORMANCE->time_stop).
          "</b> seconds.\n".
          "Number of database queries: <b>".$DB->query_counter.'</b>. '.
          'Current date: <b>'.strftime('%Y-%m-%d %T')."</b>\n<hr>\n";
    echo $s;
}

function wastest_bailout() {
    global $_SERVER;
    $cfgdir = dirname(__FILE__);
    $cfgfile = $cfgdir.'/config.php';
    $cfgwww = 'http://'.$_SERVER['HTTP_HOST'].'/devel/test';
    $cfgprogdir = $_SERVER['DOCUMENT_ROOT'].'/websiteatschool/program';
    $cfgprogwww = 'http://'.$_SERVER['HTTP_HOST'].'/websiteatschool/program';
    $cfgdatadir = $_SERVER['DOCUMENT_ROOT'].'/data';
    $cfgself = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
    header('Content-type: text/plain');
    echo <<<EOT
Oops. Apparently the file '$cfgfile' is not present.
You need to create one first, and then you can come back here.
For your convenience here's a template (use cut and paste).

<?php
// File: $cfgfile

\$CFG->db_type     = 'mysql';
\$CFG->db_server   = 'localhost';
\$CFG->db_username = 'your_database_username_here';
\$CFG->db_password = 'your_database_password_here';
\$CFG->db_name     = 'was';
\$CFG->prefix      = 'test_';

\$CFG->www         = '$cfgwww';
\$CFG->progwww     = '$cfgprogwww';
\$CFG->dir         = '$cfgdir';
\$CFG->progdir     = '$cfgprogdir';
\$CFG->datadir     = '$cfgdatadir';

\$CFG->debug       = TRUE;
?>

Remember that the file config.php should end with '?>' with no
trailing spaces, newlines and whatnot. Good luck.
EOT;
} // wastest_bailout()


/*
 * WORKHORSE ROUTINES FOLLOW
 */
function wastest_dump_cfg($title) {
    global $CFG;
    echo $title;
    echo "<pre>\n";
    print_r($CFG);
    echo "</pre>\n";
}

function wastest_dump_db($title) {
    global $DB;
    echo $title;
    echo "<pre>\n";
    print_r($DB);
    echo "</pre>\n";
}

function wastest_drop_table($title) {
    global $DB;
    echo $title;
    if ($DB->table_exists('foo')) {
        echo "Table 'foo' does exist. That's good, let's drop the table.<br>\n";
    } else {
        echo "Table 'foo' does NOT exist. Oh well, we'll see what happens if we drop it nevertheless.<br>\n";
    }
    echo "Dropping table 'foo'. If all is well, retval = '0' and error = 0/''<br>\n";
    $retval = $DB->drop_table('foo');
    echo "retval = ";
    echo ($retval === FALSE) ? "FALSE" : "'$retval'";
    echo ", error = ".$DB->errno.'/\''.$DB->error."'<br>\n";
}

function wastest_create_table($title) {
    global $DB;
    echo $title;
    if ($DB->table_exists('foo')) {
        echo "Table 'foo' does exist. Oh well, we'll see what happens if we create it nevertheless.<br>\n";
    } else {
        echo "Table 'foo' does NOT exist. That's good, let's create the table (via direct MySQL statement).<br>\n";
    }
    echo "Creating table 'foo'. If all is well, retval = '0' and error = 0/''<br>\n";
    $sql = <<<EOT
        CREATE TABLE {$DB->prefix}foo (
            id   int primary key auto_increment,
            bar  varchar(80),
            baz  datetime,
            t    text
        ) COMMENT = 'This is a MySQL-specific CREATE TABLE'
EOT;
    echo "<pre>\n$sql\n</pre>\n";
    $retval = $DB->exec($sql);
    echo "retval = ";
    echo ($retval === FALSE) ? "FALSE" : "'$retval'";
    echo ", error = ".$DB->errno.'/\''.$DB->error."'<br>\n";
}

function wastest_fill_table($title) {
    global $DB;
    echo $title;
    if ($DB->table_exists('foo')) {
        echo "Table 'foo' does exist. That's good, let's add data to the table.<br>\n";
    } else {
        echo "Table 'foo' does NOT exist. Oh well, we'll see what happens if we add to it nevertheless.<br>\n";
    }
    echo "Adding data to table 'foo'. If all is well, retval = '1' and error = 0/''<br>\n";

    $bar = $DB->quote("Peter's \"quotetest\"");
    $t = $DB->quote("Just a little \'more\' quoted text to see what's happening...");
    $sql = "
        INSERT INTO {$DB->prefix}foo (bar, t)
        VALUES (
            '$bar',
            '$t'
        )";
    echo "<pre>\n$sql\n</pre>\n";
    $retval = $DB->exec($sql);
    echo "retval = ";
    echo ($retval === FALSE) ? "FALSE" : "'$retval'";
    echo ", error = ".$DB->errno.'/\''.$DB->error."'<br>\n";

    echo "<br>\nAdd some more data...<br>";
    for ($i=0; $i<3; ++$i) {
        $bar = $DB->quote("Test \"".($i+2)."\" ");
        $t = $DB->quote("Generic ISO 8601 time in baz...");
        $now = strftime("%Y-%m-%d %T",time()+$i); // ISO 8601 date
        $sql = "
        INSERT INTO {$DB->prefix}foo (bar, baz, t)
        VALUES (
            '$bar',
            '$now',
            '$t'
        )";
        echo "<pre>\n$sql\n</pre>\n";
        $retval = $DB->exec($sql);
        echo "retval = ";
        echo ($retval === FALSE) ? "FALSE" : "'$retval'";
        $seq = $DB->prefix.'foo_id_seq';
        echo ", error = ".$DB->errno.'/\''.$DB->error."', last_insert_id('$seq') returns ".
             $DB->last_insert_id($seq)."\n";
    }
}

function wastest_dump_table($title) {
    global $DB;
    echo $title;

    echo "We will now try to show the complete contents of the table 'foo' using \$DBResult->fetch_all_assoc()<br>\n";
    $sql = "SELECT * FROM {$DB->prefix}foo";
    echo "<pre>\n$sql\n</pre>\n";
    $retval = $DB->query($sql);
    if ($retval === FALSE) {
        echo "retval = FALSE, error = ".$DB->errno.'/\''.$DB->error."'<br>\n";
    } else {
        echo "Explanation: the result is an object representing a result set.\n".
             "We now will retrieve all rows from the result set as associative arrays\n".
             "with the command print_r(\$retval->fetch_all_assoc())\n\n";
        $all_records = $retval->fetch_all_assoc();
        echo "<pre>\n";
        print_r($all_records);
        echo "\n</pre>\n";
        echo "As an added bonus we'll show the same data in a table format too...\n<p>\n";
        echo dump_array_as_html_table($all_records);
   }
}

function dump_array_as_html_table($array_to_dump) {
    $s = "<table border=\"1\" cellpadding=\"3\">\n";
    $first = 1;
    foreach($array_to_dump as $record) {
        if ($first) {
            $first = 0;
            $s .= " <tr>\n";
            foreach($record as $k => $v) {
                $s .=  "  <th bgcolor=\"#FFCCCC\">".htmlspecialchars($k)."</th>\n";
                }
            $s .= " </tr>\n";
        }
        $s .= " <tr>\n";
        foreach($record as $k => $v) {
            $s .= (empty($v)) ? "  <td>&nbsp;</td>\n" : "  <td>".htmlspecialchars($v)."</td>\n";
            }
        $s .= " </tr>\n";
    }
    $s .= "</table>\n";
    return $s;
}



function wastest_show_tables($title) {
    global $DB;
    echo $title;
    echo "Quick and dirty overview of tables in \$DB...<p>\n";
    $retval = $DB->query("SHOW TABLES");
    echo dump_array_as_html_table($retval->fetch_all_assoc());
}

function wastest_tabledef_dump($title) {
    echo $title;
    include_once(dirname(__FILE__).'/test_tabledef.php');
    echo "<pre>\n";
    print_r($test_tabledefs);
    echo "</pre>";
}

function wastest_tabledef_sql($title) {
    global $DB;
    echo $title;
    include_once(dirname(__FILE__).'/test_tabledef.php');

    $sql = $DB->create_table_sql($test_tabledefs[$tablename]);
    if ($sql === FALSE) {
        echo "create_table_sql() returned an error";
    } else {
        echo "Here is the necessary SQL-statement.\n<p>\n<pre>\n".$sql."\n</pre>\n";
    }
}
function wastest_table_create($title) {
    global $DB;
    echo $title;
    include_once(dirname(__FILE__).'/test_tabledef.php');
    $retval = $DB->create_table($test_tabledefs[$tablename]);
    echo "retval = ";
    echo ($retval === FALSE) ? "FALSE" : "'$retval'";
    echo ", error = ".$DB->errno.'/\''.$DB->error."'<br>\n";
}

function wastest_drop_big_table($title) {
    global $DB;
    echo $title;
    include_once(dirname(__FILE__).'/test_tabledef.php');
    $retval = $DB->drop_table($tablename);
    echo "retval = ";
    echo ($retval === FALSE) ? "FALSE" : "'$retval'";
    echo ", error = ".$DB->errno.'/\''.$DB->error."'<br>\n";
}

function wastest_describe_table($title) {
    global $DB;
    echo $title;
    include_once(dirname(__FILE__).'/test_tabledef.php');
    $sql = 'DESCRIBE '.$DB->prefix.$tablename;

    $dbresult = $DB->query($sql);
    if ($dbresult === FALSE) {
        echo "Oops, could not let the database describe the table via <code>'$sql'</code><br>\n";
        echo $DB->errno.'/'.$DB->error."<br>\n";
    } else {
        echo dump_array_as_html_table($dbresult->fetch_all_assoc());
        $dbresult->close();
    }
}

function wastest_dump_table_description($title) {
    echo $title;
    include_once(dirname(__FILE__).'/'.$_GET['extra1']);
    foreach($tabledefs as $tabledef) {
        $s = "<table width=\"100%\" border=\"0\" cellpadding=\"3\">\n";
        $s .= "<tr><th bgcolor=\"#FFCCCC\">".htmlspecialchars($tabledef['name'])."</th></tr>\n";
        $s .= "<tr><td bgcolor=\"#FFFFCC\">".htmlspecialchars($tabledef['comment'])."</td></tr>\n";
        $s .= "<tr><td>\n".
              "  <table width=\"100%\" border=\"1\" cellpadding=\"3\">\n".
              "  <tr>\n".
              "    <th bgcolor=\"#CCCCFF\" width=\"12%\">Field</th>\n".
              "    <th bgcolor=\"#CCCCFF\" width=\"12%\">Type</th>\n".
              "    <th bgcolor=\"#CCCCFF\" width=\"12%\">Null</th>\n".
              "    <th bgcolor=\"#CCCCFF\" width=\"12%\">Default</th>\n".
              "    <th bgcolor=\"#CCCCFF\" width=\"12%\">Extra</th>\n".
              "    <th bgcolor=\"#CCCCFF\" width=\"40%\">Comment</th>\n".
              "  </tr>\n";
        foreach($tabledef['fields'] as $field) {
            $s .= "  <tr>\n";
            $s .= "    <td>".htmlspecialchars($field['name'])."</td>\n";

            $s .= "    <td>".htmlspecialchars($field['type']);
            if ((isset($field['length'])) || (isset($field['decimals']))) {
                $s .= "(".htmlspecialchars($field['length']);
                if (isset($field['decimals'])) {
                    $s .= ",".htmlspecialchars($field['decimals']);
                }
                $s .= ")";
            }
            if (isset($field['unsigned'])) {
                $s .= " unsigned";
            }
            $s .= "</td>\n";
            if (isset($field['notnull'])) {
                $s .= ($field['notnull']) ? "    <td>not null</td>\n" : "    <td>null</td>\n";
            } else {
                $s .= "    <td>&nbsp;</td>\n";
            }
            if (isset($field['default'])) {
                if ($field['default'] === FALSE) {
                    $s .= "    <td>FALSE</td>\n";
                } elseif ($field['default'] === TRUE) {
                    $s .= "    <td>TRUE</td>\n";
                } else {
                    $s .= "    <td>".htmlspecialchars($field['default'])."</td>\n";
                }
            } else {
                $s .= "    <td>&nbsp;</td>\n";
            }
            if (isset($field['enum_values'])) {
                $s .= "    <td>";
                $comma = "";
                foreach($field['enum_values'] as $v) {
                    $s .= $comma.htmlspecialchars($v);
                    $comma = ", ";
                }
                $s .= "</td>\n";
            } else {
                $s .= "    <td>&nbsp;</td>\n";
            }
            $s .= (isset($field['comment'])) ? "    <td>".htmlspecialchars($field['comment'])."</td>\n" : "    <td>&nbsp;</td>\n";

            $s .= "  </tr>\n";
        }
        $s .= "  </table>\n".
              "</td></tr>\n";

        // keys
        $s .= "<tr><td>\n".
              "  <table width=\"100%\" border=\"1\" cellpadding=\"3\">\n".
              "  <tr>\n".
              "    <th bgcolor=\"#CCCCFF\" width=\"12%\">Key</th>\n".
              "    <th bgcolor=\"#CCCCFF\" width=\"12%\">Type</th>\n".
              "    <th bgcolor=\"#CCCCFF\" width=\"18%\">Fields</th>\n".
              "    <th bgcolor=\"#CCCCFF\" width=\"18%\">References</th>\n".
              "    <th bgcolor=\"#CCCCFF\" width=\"40%\">Comment</th>\n".
              "  </tr>\n";
        foreach($tabledef['keys'] as $key) {
            $s .= "  <tr>\n";
            if (isset($key['name'])) {
                $s .= "    <td>".htmlspecialchars($key['name'])."</td>\n";
            } else {
                $s .= "    <td>&nbsp;</td>\n";
            }
            $s .= "    <td>";
            if (isset($key['unique'])) {
                $s .= "unique ";
            }
            $s .= htmlspecialchars($key['type'])."</td>\n";
            if (isset($key['fields'])) {
                $s .= "    <td>";
                $comma = "";
                foreach($key['fields'] as $v) {
                    $s .= $comma.htmlspecialchars($v);
                    $comma = ", ";
                }
                $s .= "</td>\n";
            } else {
                $s .= "    <td>&nbsp;</td>\n";
            }
            if (isset($key['reftable'])) {
                $s .= "    <td>".htmlspecialchars($key['reftable'])."(";
                $comma = "";
                foreach($key['reffields'] as $v) {
                    $s .= $comma.htmlspecialchars($v);
                    $comma = ", ";
                }
                $s .= ")</td>\n";
            } else {
                $s .= "    <td>&nbsp;</td>\n";
            }
            $s .= (isset($key['comment'])) ? "    <td>".htmlspecialchars($key['comment'])."</td>\n" : "    <td>&nbsp;</td>\n";

            $s .= "  </tr>\n";
        }
        $s .= "  </table>\n".
              "</td></tr>\n";


        $s .= "</table>\n";
        echo $s."<p>\n&nbsp;<p>\n";
    }

//echo "<pre>\n";
//    print_r($tabledefs);
//echo "</pre>\n";
}

/*****
function wastest_($title) {
    echo $title;

}
****/

?>