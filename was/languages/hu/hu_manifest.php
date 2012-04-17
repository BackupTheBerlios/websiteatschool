<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2012 Vereniging Website At School, Amsterdam <info@websiteatschool.eu>
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

/** /program/languages/hu/hu_manifest.php - description of the Hungarian translation
 *
 * This file defines the Hungarian language package ('hu'). 
 * This file is used when this package is installed.
 *
 * @author Erika Swiderski, Gergely Sipos <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2012 Vereniging Website At School, Amsterdam <info@websiteatschool.eu>
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_hu
 * @version $Id: hu_manifest.php,v 1.1 2012/04/17 15:20:13 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$manifests['hu'] = array(
    'language_key'        => 'hu',
    'language_name'       => 'Magyar',
    'parent_language_key' => 'en',
    'description'         => 'This is the Hungarian translation based on Website@School release 0.90.3',
    'author'              => 'Erika Swiderski, Gergely Sipos',
    'version'             => 2012041700,
    'release'             => '0.90.3',
    'release_date'        => '2012-04-17',
    'is_core'             => FALSE
   );

?>