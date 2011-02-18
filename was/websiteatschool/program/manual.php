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

/** /program/manual.php - a kickstarter for the documentation
 *
 * This script is also an entry point; it can be called directly.
 * It is called from /program/admin.php via the help button.
 * Recognised parameters:
 *
 * language
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: manual.php,v 1.3 2011/02/18 14:53:15 pfokker Exp $
 * @todo guess what? we need to replace this stub with real documentation
 * @todo How about adding an extra parameter to manual.php in order to 'deep link' into the manual?
 */

/** this circumvents the 'magic' in magic_quotes_gpc() by conditionally stripping slashes
 *
 * This routine borrowed from {@link waslib.php}.
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

$language = (isset($_GET['language'])) ? magic_unquote($_GET['language']) : 'en';
$topic = (isset($_GET['topic'])) ? magic_unquote($_GET['topic']) : 'toc';
$subtopic = (isset($_GET['subtopic'])) ? magic_unquote($_GET['subtopic']) : '';

// STUBS
$languages = array('en','nl');
$topics = array('toc'                  => 'index.html', 
                'accountmanager'       => 'accountmanager.html',
                'areamanager'          => 'areamanager.html',
                'groupmanager'         => 'groupmanager.html',
                'configurationmanager' => 'configurationmanager.html',
                'pagemanager'          => 'pagemanager.html',
                'filemanager'          => 'filemanager.html',
                'usermanager'          => 'usermanager.html',
                'install'              => 'install.html'
                );

// Sanitise
// Language MUST be one of the defined languages
if (!in_array($language,$languages)) {
    $language = 'en';
}
// Topic MUST be one of the known topics
if (!isset($topics[$topic])) {
    $topic = 'toc';
    $subtopic = '';
}
$filename = 'manuals/'.$language.'/'.$topics[$topic];

// Subtopic (if any) MUST contain only letters, digits, underscore or dot
if ((!empty($subtopic)) && ($subtopic == preg_replace('/[^A-Za-z0-9_\.]/','',$subtopic))) { 
    $url = $filename.'#'.$subtopic;
} else {
    $url = $filename;
    $subtopic = htmlspecialchars($subtopic);
}

if (file_exists($filename)) {
    header('Location: '.$url);
    echo "<a href=\"{$url}\">$url</a>\n";
    exit;
}

// No redirect, we're stuck here...

echo <<<EOT
<html>
<head>
<title>STUB: Manual</title>
</head>

<body bgcolor="#FFFFDD">
<script type="text/javascript">
<!--
    document.write('<input type="button" value="Close" onclick="window.close();">');
-->
</script>
<h1>Site@School Manual ($language)</h1>

At this time there is no manual available. This is a stub.
<p>
Additional information:
<ul>
<li>Language: <b>$language</b>
<li>Topic: <b>$topic</b>
<li>Subtopic: <b>$subtopic</b>
<li>Filename: <b>$filename</b>
<li>URL: <b>$url</b>
</ul>

We were looking for the file <b>$filename</b> but it could not be found.
<p>
The following topics are recognised and they link to actual files as follows.
<ul>
EOT;
foreach ($topics as $name => $value) {
    echo "<li><b>{$name}</b> =&gt; <tt>manuals/{$language}/$value</tt>\n";
}
echo <<<EOT
</ul>

Byebye now...

EOT;
?>