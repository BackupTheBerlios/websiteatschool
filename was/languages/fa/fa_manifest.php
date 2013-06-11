<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam <info@websiteatschool.eu>
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

/** /program/languages/fa/fa_manifest.php - description of the Persian translation
 *
 * This file defines the Persian language package ('fa'). 
 * This file is used when this package is installed.
 *
 * @author A. Darvishi <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam <info@websiteatschool.eu>
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_fa
 * @version $Id: fa_manifest.php,v 1.2 2013/06/11 11:25:11 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$manifests['fa'] = array(
    'language_key'        => 'fa',
    'language_name'       => "\xE2\x80\xAB\xD9\x81\xD8\xA7\xD8\xB1\xD8\xB3\xDB\x8C",
    'parent_language_key' => 'nl',
    'description'         => 'This is the Persian translation based on Website@School release 0.90.3',
    'author'              => 'A. Darvishi',
    'version'             => 2012041700,
    'release'             => '0.90.3',
    'release_date'        => '2012-04-17',
    'is_core'             => FALSE
   );

?>