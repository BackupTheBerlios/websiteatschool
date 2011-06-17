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
 * This script is an entry point; it can be called directly.
 * It is also linked to from /program/admin.php, via the help button, implementing
 * a context-sensitive help function. The following parameters are recognised:
 *
 *  - language: a language key, e.g. 'nl' (Dutch) or 'es' (Spanish). Default is 'en' (English)
 *  - topic: one of the recognised topics, e.g. 'tools' or 'pagemanager'. Default is 'toc' (Table of contents)
 *  - subtopic: one of the subtopics relevant in this topic, e.g. 'license' in the 'install' topic. Default is '' (None).
 *
 * The actual work is done in the function {@link show_manual()} below.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: manual.php,v 1.5 2011/06/17 18:19:10 pfokker Exp $
 */

/** This global defines the mapping between topics/subtopics and manual files/filefragments
 *
 * This array is keyed by topic. The value is either
 *
 *  - the name of a manual file (e.g. 'filemanager.html'),
 *  - a pointer to a fragment within a file (e.g. 'install.html#h2.3'), or
 *  - an array with a mapping between subtopics and file fragments.
 *
 * This array defines the valid topics and subtopics. Anything else yields the 'toc'
 * topic (i.e. the index.html file). Note that this mapping may grow in future versions
 * whenever new context sensitive help is added to the main program.
 */
$TOPICS = array(
    'toc'                  => 'index.html', 
    'accountmanager'       => 'accountmanager.html',
    'areamanager'          => 'configurationmanager.html#h4',
    'backuptool'           => 'tools.html#h4',
    'configurationmanager' => 'configurationmanager.html',
    'filemanager'          => 'filemanager.html',
    'groupmanager'         => 'accountmanager.html#h4',
    'install'              => array(
        ''                 => 'install.html',
        'language'         => 'install.html#h2.1',
        'installtype'      => 'install.html#h2.2',
        'license'          => 'install.html#h2.3',
        'database'         => 'install.html#h2.4',
        'website'          => 'install.html#h2.5',
        'user'             => 'install.html#h2.6',
        'compatibility'    => 'install.html#h2.7',
        'confirm'          => 'install.html#h2.8',
        'finish'           => 'install.html#h2.9'
        ),
    'logview'              => 'tools.html#h5',
    'pagemanager'          => 'pagemanager.html',
    'translatetool'        => 'tools.html#h3',
    'tools'                => 'tools.html',
    'usermanager'          => 'accountmanager.html#h3',
    'update'               => 'tools.html#h6'
    );


$language = (isset($_GET['language'])) ? magic_unquote($_GET['language']) : 'en';
$topic    = (isset($_GET['topic']))    ? magic_unquote($_GET['topic'])    : 'toc';
$subtopic = (isset($_GET['subtopic'])) ? magic_unquote($_GET['subtopic']) : '';

show_manual($language,$topic,$subtopic);
exit;

// ==================================================================
// =========================== WORKHORSES ===========================
// ==================================================================

/** redirect the user to a specific place in the manual OR show helpful message about downloading the manual
 *
 * There is a Website@School Users' Guide available, in English. This is
 * a separate download from the project's website. That means that it is
 * optional to have the (English) manual installed. If it is installed,
 * it is installed under /program/manuals/en/. There might also be translations
 * available, say the Dutch version of the manual. That one would be
 * installed in /program/manuals/nl/ which allows for peaceful co-existence of
 * multiple translations of the manual. This script manual.php is designed to:
 *
 *  - redirect the user to the correct translation of the manual (if installed), and
 *  - possibly use deep links to create context-sensitive help.
 *
 * If the manual is not available in the requested language, the user is redirected
 * to the English version (if that one IS installed). If no manual is installed at
 * all, the user is shown a simple HTML-page which provides a link to the location
 * where the manual(s) can be downloaded.
 *
 * @param string $language indicates the desired manual language
 * @param string $topic is the topic of interest to which we deep link
 * @param string $subtopic is a subtopic to allow for an even deeper link
 * @return void this function never returns
 */
function show_manual($language='en',$topic='toc',$subtopic='') {
    global $TOPICS;
    $manuals = get_available_manuals(dirname(__FILE__).'/manuals');

    // 1 -- Sanitise input
    // 1A -- topic must be defined in $TOPICS; go to ToC if not
    if (!isset($TOPICS[$topic])) {
        $topic = 'toc';
        $subtopic = '';
    }
    // 1B -- a subtopic should also exist; go to first subtopic if it doesn't
    if (is_array($TOPICS[$topic])) {
        if (isset($TOPICS[$topic][$subtopic])) {
            $file_fragment = $TOPICS[$topic][$subtopic];
        } else {
            $file_fragment = current($TOPICS[$topic]);
            if ($file_fragment === FALSE) {
                $file_fragment = $TOPICS['toc'];
            }
        }
    } else {
        $file_fragment = $TOPICS[$topic];
    }

    // 2A -- prepare to look for requested topic/subtopic OR ToC if requested topic is not found
    $file_fragments = array($file_fragment);
    if ($file_fragment != $TOPICS['toc']) {
        $file_fragments[] = $TOPICS['toc'];
    }
    // 2B -- prepare to look for requested language OR English if requested language is not found (and English is)
    $languages = array();
    if (isset($manuals[$language])) {
        $languages[$language] = $manuals[$language];
    }
    if (($language != 'en') && (isset($manuals['en']))) {
        $languages['en'] = $manuals['en'];
    }

    // 3A -- try requested topic/subtopic and subsequently the ToC
    foreach($file_fragments as $file_fragment) {
        if (strpos($file_fragment,'#') === FALSE) {
            $file = $file_fragment;
            $fragment = '';
        } else {
            list($file,$fragment) = explode('#',$file_fragment);
        }
        // 3B -- try the requested language and subsequently (maybe) English
        foreach($languages as $language_key => $manual_path) {
            if (file_exists($manual_path.'/'.$file)) {
                $url = sprintf('manuals/%s/%s',$language_key,$file_fragment);
                header('Location: '.$url);
                echo "<a href=\"{$url}\">$url</a>\n";
                exit;
            }
        }
    }
    // 4 -- still here? Then there's no manual installed. Hint at downloading it.
    echo <<<EOT
<html>
    <head>
        <title>Website@School User's Guide not installed</title>
    </head>

    <body bgcolor="#FFFFDD">
        <script type="text/javascript">
        <!--
            document.write('<input type="button" value="Close" onclick="window.close();">');
        -->
        </script>
        <h3>Website@School Users' Guide not installed</h3>

        It looks like the Website@School Users' Guide is currently not installed on this website or server.
        <p>
        Please take the following steps to install it.
        <ol>
        <li>Download the latest version of the Website@School Users' Guide
            archive (either the <tt>.zip</tt>-file or the <tt>.tar.gz</tt>-file) from
            <strong><a href="http://download.websiteatschool.eu"
                       target="_blank">download.websiteatschool.eu</a></strong>
        <li>Unpack the downloaded archive in the <em>CMS Root Folder</em>.
            This is the directory where <tt>config.php</tt> resides.
        <li>The Website@School Users' Guide is now available on your website.
            You can subsequently refresh this screen in your browser by pressing the appropriate key
            (usually <tt>[F5]</tt>).
        </ol>
        <p>
        <div style="background: #CFCFCF">
            <a href="about.html" target="_blank"><img
               src="graphics/poweredby.png" alt="Powered by Website@School" width="280" height="35" border="0"
               title="The Website@School logo is a registered trademark of Vereniging Website At School"></a>
        </div>
    </body>
</html>
EOT;
    exit;
} // show_manual()


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

/** construct a list of 0 or more languages of available manuals
 *
 * This routine examines the directory $path to see which subdirectories
 * exist. Each subdirectory indicates a possible language. An array
 * keyed with these languages and the full path to the directory containing
 * the manual's index.html is returned (but it could be empty).
 *
 * @param string $path is the directory where to look for manuals (usually /program/manuals).
 * @return array contains a list of available manual directories keyed by language_key
 */
function get_available_manuals($path) {
    $manuals = array();
    if (!is_dir($path)) {
        return $manuals;
    } elseif (($handle = @opendir($path)) == FALSE) {
        return $manuals;
    }
    while (($entry = @readdir($handle)) !== FALSE) {
        if (($entry == '.') || ($entry == '..')) {
            continue;
        }
        if (file_exists($path.'/'.$entry.'/index.html')) { // at least the ToC should exist in an installed manual
            $manuals[$entry] = $path.'/'.$entry;
        }
    }
    @closedir($handle);
    return $manuals;
} // get_available_manuals()

?>