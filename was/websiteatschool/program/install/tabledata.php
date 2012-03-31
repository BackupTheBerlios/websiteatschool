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

/** /program/install/tabledata.php defines core data in a generic way
 *
 * This file contains the essential data for a new installation, i.e. the
 * items in the configuration table that should exist, etc. This is all
 * done in a generic (database-independent) way.

 * This file defines an array called '$tabledata' which contains one or more
 * arrays with a tablename and yet another array with fieldname/fieldvalue
 * pairs. This construction with nested arrays is converted to an actual
 * SQL-statement in a function. That function also takes care of the table
 * prefix, so we can simple refer to the bare tablenames here.
 *
 * The reason to use this nested array construction is that it is easier to see
 * which field gets which value compared to a (possibly very long) SQL-statement.
 * Furthermore you don't need to worry about prefixing the table name and it is
 * almost impossible to mismatch the number of fields and the number of values
 * because they are combined in a $key => $value pair. Finally, all strings are
 * automagically escaped with $DB->escape() in the function that constructs the
 * actual SQL-statement.
 *
 * This definition file uses the PHP variable types, i.e. if you want to insert
 * a number, you can specify a number (without quotes) and for a boolean you can
 * use the PHP-values TRUE or FALSE. Here's an example.
 * <pre>
 * $tabledata = array();
 * $tabledata[] = array('table' => 'tablename_without_prefix',
 *                      'fields' => array(
 *                          'string_field' => 'This is a string, even with unescaped "quotes"',
 *                          'boolean_field' => TRUE,
 *                          'integer_field' => 123,
 *                          'date_field' => '2008-02-01 23:34:45',
 *                          'double_field' => 1.234567,
 *                          'field_with_null_value' => NULL
 *                         )
 *                     );
 * </pre>
 * Note that a date field is handled like a string field.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasinstall
 * @version $Id: tabledata.php,v 1.5 2012/03/31 15:18:54 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

if (!isset($tabledata)) {
    $tabledata = array();
}

$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'version',
        'type' => 'i',
        'value' => '2009010100',
        'sort_order' => 10,
        'extra' => 'minvalue=2009010100;maxvalue=2147123199;viewonly=1',
        'description' => 'Database version (read-only); must match /program/version.php - INSTALLER-defined'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'salt',
        'type' => 's',
        'value' => '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
        'sort_order' => 20,
        'extra' => 'minlength=8;maxlength=255',
        'description' => 'This global salt is used when generating session keys - USER-defined'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'session_name',
        'type' => 's',
        'value' => 'WASSESSION',
        'sort_order' => 30,
        'extra' => 'minlength=1;maxlength=255',
        'description' => 'The name of the session cookie  - USER-defined, default WASSESSION'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'session_expiry',
        'type' => 'i',
        'value' => '86400',
        'sort_order' => 40,
        'extra' => 'minvalue=120;maxvalue=31536000',
        'description' => 'The session expiry time in seconds - USER-defined, default 86400 (24h), min 120 (2m), max 31536000 (365d)'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'login_max_failures',
        'type' => 'i',
        'value' => '10',
        'sort_order' => 50,
        'extra' => 'minvalue=0',
        'description' => 'The maximum number of allowable login-failures - USER-defined, default 10 tries'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'login_failures_interval',
        'type' => 'i',
        'value' => '12',
        'sort_order' => 60,
        'extra' => 'minvalue=1',
        'description' => 'The sliding interval (minutes) during which failed attempts are counted - USER-defined, default 12'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'login_bypass_interval',
        'type' => 'i',
        'value' => '30',
        'sort_order' => 70,
        'extra' => 'minvalue=1',
        'description' => 'The interval (minutes) during which the bypass is valid  - USER-defined, default 30'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'login_blacklist_interval',
        'type' => 'i',
        'value' => '8',
        'sort_order' => 80,
        'extra' => 'minvalue=1',
        'description' => 'The duration (in minutes) of blacklisting the user - USER-defined, default 8 minutes'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'title',
        'type' => 's',
        'value' => '',
        'sort_order' => 90,
        'extra' => 'maxlength=255',
        'description' => 'This is the title of the website - USER defined, initially set via Install Wizard'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'website_from_address',
        'type' => 's',
        'value' => '',
        'sort_order' => 100,
        'extra' => 'maxlength=255',
        'description' => 'This is used as the website sender address - USER-defined, initially set via Install Wizard'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'website_replyto_address',
        'type' => 's',
        'value' => '',
        'sort_order' => 110,
        'extra' => 'maxlength=255',
        'description' => 'This is used as the website reply address - USER-defined, initially set via Install Wizard'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'language_key',
        'type' => 's',
        'value' => 'en',
        'sort_order' => 120,
        'extra' => 'minlength=2;maxlength=10',
        'description' => 'This is the language of the site - USER-defined, initially set via Install Wizard'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'pagination_height',
        'type' => 'i',
        'value' => '20',
        'sort_order' => 130,
        'extra' => 'minvalue=1',
        'description' => 'Preferred number of items/screen in long listings - USER-defined, default 20'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'pagination_width',
        'type' => 'i',
        'value' => '7',
        'sort_order' => 140,
        'extra' => 'minvalue=5',
        'description' => 'Maximum number of screens in pagination navigation (odd number) - USER-defined, default 7'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'editor',
        'type' => 'l',
        'value' => 'ckeditor',
        'sort_order' => 150,
        'extra' => 'options=ckeditor,fckeditor,plain',
        'description' => 'Default rich text editor - USER-defined, default ckeditor'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'friendly_url',
        'type' => 'b',
        'value' => '0',
        'sort_order' => 160,
        'extra' => '',
        'description' => 'use proxy-friendly paths, not query strings - USER-defined, initially set via Install Wizard'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'clamscan_path',
        'type' => 's',
        'value' => '',
        'sort_order' => 170,
        'extra' => 'maxlength=240',
        'description' => 'Fully qualified path to clamscan executable  - USER-defined, initially set via Install Wizard'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'clamscan_mandatory',
        'type' => 'b',
        'value' => '0',
        'sort_order' => 180,
        'extra' => '',
        'description' => 'If TRUE virusscan is required on every upload - USER-defined, initially set via Install Wizard'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'upload_max_files',
        'type' => 'i',
        'value' => '8',
        'sort_order' => 190,
        'extra' => 'minvalue=1;maxvalue=512',
        'description' => 'Maximum number of files to upload in the simple (non-Java) file upload dialog - USER-defined, default 8, min 1, max 512'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'thumbnail_dimension',
        'type' => 'i',
        'value' => '100',
        'sort_order' => 200,
        'extra' => 'minvalue=16;maxvalue=2048',
        'description' => 'Maximum dimension (width and height) of thumbnails generated on upload - USER-defined, default 100, min 16, max 2048'
        )
    );

// Define three ordered lists of allowable extensions (as a shorthand for filetype)
$tmp_filemanager_images = array('bmp','gif','jpg','jpe','jpeg','png','tif','tiff');
$tmp_filemanager_flash = array('swf');
$tmp_filemanager_files = array_merge(
    $tmp_filemanager_images,
    $tmp_filemanager_flash,
    array(
        'avi','csv','doc','html','htm','mp3','mpeg','mpg','odf','odp','ods','odt','pdf','ppt','rtf','txt','xls','zip'
        )
    );
sort($tmp_filemanager_files);

$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'filemanager_files',
        'type' => 's',
        'value' => implode(',',$tmp_filemanager_files),
        'sort_order' => 210,
        'extra' => 'maxlength=65535',
        'description' => 'Comma-delimited list of allowable/uploadable file extensions (shorthand for filetype) - USER-defined'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'filemanager_images',
        'type' => 's',
        'value' => implode(',',$tmp_filemanager_images),
        'sort_order' => 220,
        'extra' => 'maxlength=65535',
        'description' => 'Comma-delimited list of allowable/uploadable image extensions - USER-defined'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'filemanager_flash',
        'type' => 's',
        'value' => implode(',',$tmp_filemanager_flash),
        'sort_order' => 230,
        'extra' => 'maxlength=65535',
        'description' => 'Comma-delimited list of allowable/uploadable flash extensions - USER-defined'
        )
    );
$tabledata[] = array(
    'table' => 'config',
    'fields' => array(
        'name' => 'pagemanager_at_end',
        'type' => 'b',
        'value' => '0',
        'sort_order' => 240,
        'extra' => '',
        'description' => 'sort order position within section for new nodes: TRUE is at the end  - USER-defined'
        )
    );

?>