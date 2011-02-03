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

# langdiff.php -- quick and dirty diff between old and current English language files
# Peter Fokker -- 2011-01-12
#
# $Id: langdiff.php,v 1.2 2011/02/03 14:03:59 pfokker Exp $
#
# See also langdiff.sh
#
# Usage: langdiff.php /path/to/old /path/to/new old_release new_release
#

/** Valid entry points define WASENTRY; prevents direct access to include()'s. */
define('WASENTRY',__FILE__);

$Oldpath = $_SERVER['argv'][1];
$Newpath = $_SERVER['argv'][2];
$Oldrelease = $_SERVER['argv'][3];
$Newrelease = $_SERVER['argv'][4];

# Handcrafted list of translations
#
$Files = array(
    'was' => 'languages/en/was.php',
    'loginlib'  => 'languages/en/loginlib.php',
    'admin' =>'languages/en/admin.php',
    'm_htmlpage' => 'modules/htmlpage/languages/en/htmlpage.php',
    't_frugal' => 'themes/frugal/languages/en/frugal.php',
    'i_install' => 'websiteatschool/program/install/languages/en/install.php',
    'i_demodata' => 'websiteatschool/program/install/languages/en/demodata.php'
    );

# Read all language files
#
$Old = array();
$New = array();
foreach($Files as $key => $filename) {
    $string = array();
    include $Newpath.'/'.$filename;
    $New[$key] = $string;

    $string = array();
    if (file_exists($Oldpath.'/'.$filename)) {
        include $Oldpath.'/'.$filename;
    }
    $Old[$key] = $string;
}

# Open the HTML-page
#
echo <<<EOT
<html>
<head>
<title>Source language changes $Oldrelease - $Newrelease</title>
</head>
<body>

<h1>Source language changes $Oldrelease - $Newrelease</h1>

Below is an overview of changes and additions in
release $Newrelease compared to release $Oldrelease.

EOT;

# Now step through all (new) files and (new) strings
#
$total_modified = 0;
$total_new = 0;
foreach($Files as $key => $filename) {
    $i = 0;
    $count_modified = 0;
    $count_new = 0;
    echo sprintf("\n<h2>%s (%s)</h2>\n",$New[$key]['translatetool_title'],$key);
    foreach($New[$key] as $k => $v) {
        ++$i;
        if (!isset($Old[$key][$k])) {
            ++$count_new;
            echo sprintf("<hr>\n".
                         "<span title=\"%s:%s\"><strong>%d</strong>: *** NEW ***</span><br>\n".
                         "<strong>new</strong>: %s<br>\n",$key,$k,$i,htmlspecialchars($v));
        } elseif ($Old[$key][$k] != $v) {
            ++$count_modified;
            echo sprintf("<hr>\n".
                         "<span title=\"%s:%s\"><strong>%d</strong>: *** MODIFIED ***</span><br>\n".
                         "<strong>old</strong>: %s<br>\n".
                         "<strong>new</strong>: %s<br>\n",
                          $key,$k,$i,htmlspecialchars($Old[$key][$k]),htmlspecialchars($v));
        }
    }
    if ($count_modified + $count_new == 0) {
        echo "No changes\n";
    } else {
        echo sprintf("<hr>\nModified: %d<br>\nNew: %d\n",$count_modified,$count_new);
    }
    $total_modified += $count_modified;
    $total_new += $count_new;
}
echo "\n<h1>Total</h1>\n";
if ($total_modified + $total_new == 0) {
    echo "No changes\n";
} else {
    echo sprintf("Modified: %d total<br>\n".
                 "New: %d total\n",$total_modified,$total_new);
}

# Finish HTML-page
#
echo "</body>\n</html>\n";

?>