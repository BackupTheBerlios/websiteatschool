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
# along with this program. If not, see http://websiteatschool.org/license.html

/** /program/lib/modulelib.php - module factory
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.org/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: modulelib.php,v 1.1 2011/02/01 13:00:24 pfokker Exp $
 * @todo we probably need to get rid of this file because it is not used (2010-12-07/PF)
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** module class is used as a base class from which others are derived */
require_once($CFG->progdir.'/lib/module.class.php');

/** manufacture a module object
 *
 * This loads (includes) a specific module based on the parameter
 * $module_id. Relevant data is read from the database. If no
 * module can be found, the function returns FALSE;
 *
 * Note that the base Module-class is always included so it is there if
 * other modules need it.
 *
 * @param int $module_id which module to retrieve from database via primary key
 * @return bool|object FALSE on error, or an instance of the specified module class
 * @uses $CFG
 * @todo what if the module is not found? Currently no alternative is loaded but FALSE is returned.
 */
function module_factory($module_id=0,$node_id=NULL) {
    global $CFG;
    $o = FALSE; // assume failure
    $record = db_select_single_record('modules','*',array('module_id' => intval($module_id),'is_active' => TRUE));
    if ($record !== FALSE) {
        /* We have an existing and active module. We know that we can find the
         * module's files in $CFG->progdir.'/modules/'.$module_name.
         * The file to include is called $module_filename and the class
         * is $module_class. We will now try to include the relevant file
         * and instantiate the module.
         */
        $module_name = $record['name']; // e.g. 'htmlpage'
        $module_class = $record['class']; // e.g. 'ModuleHtmlpage'
        $module_filename = $record['class_file']; // e.g. 'htmlpage.class.php'

        $module_directory = $CFG->progdir.'/modules/'.$module_name;
        if (is_file($module_directory.'/'.$module_filename)) {
            include_once($module_directory.'/'.$module_filename);
            $o = new $module_class($record,$node_id);
        }
    }
    return $o;
}

?>