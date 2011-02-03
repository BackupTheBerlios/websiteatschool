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

/** /program/lib/themelib.php - theme factory
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: themelib.php,v 1.2 2011/02/03 14:04:04 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** theme class is used as a base class from which others can be derived */
require_once($CFG->progdir.'/lib/theme.class.php');


/** manufacture a theme object
 *
 * This loads (includes) a specific theme based on the parameter
 * $theme_id. Relevant data is read from the database.
 *
 * @param int $theme_id denotes which theme to retrieve from database via primary key
 * @param int $area_id the area we're working in
 * @param int $node_id the node that is to be displayed
 * @return bool|object FALSE on error, or an instance of the specified theme class
 * @todo should we massage the directory and file names of the included theme?
 * @todo what if the theme is not found? Currently no alternative is loaded but FALSE is returned.
 * @uses $CFG
 */
function theme_factory($theme_id,$area_id,$node_id) {
    global $CFG;
    $o = FALSE; // assume failure
    $theme_record = db_select_single_record('themes','*',array('theme_id' => intval($theme_id),'is_active' => TRUE));
    if ($theme_record !== FALSE) {
        /* We have an existing and active theme. We know that we can find the
         * theme's files in $CFG->progdir.'/themes/'.$theme_name.
         * The file to include is called $theme_filename and the class
         * is $theme_class. We will now try to include the relevant file
         * and instantiate the theme.
         */
        $theme_name = $theme_record['name']; // e.g. 'frugal'
        $theme_class = $theme_record['class']; // e.g. 'ThemeFrugal'
        $theme_filename = $theme_record['class_file']; // e.g. 'frugal.class.php'

        $theme_directory = $CFG->progdir.'/themes/'.$theme_name;
        if (is_file($theme_directory.'/'.$theme_filename)) {
             include_once($theme_directory.'/'.$theme_filename);
             $o = new $theme_class($theme_record,$area_id,$node_id);
        }
    }
    return $o;
} // theme_factory()

?>