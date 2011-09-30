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

/** /program/languages/en/en_manifest.php - description of the main language/translation (English)
 *
 * This file defines the English language package ('en'). 
 * This file is used when this essential (core) package is installed.
 *
 * @author Peter Fokker <peter@berestijn.nl>o
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_en
 * @version $Id: en_manifest.php,v 1.6 2011/09/30 14:03:56 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$manifests['en'] = array(
    'language_key'        => 'en',
    'language_name'       => 'English',
    'parent_language_key' => '',
    'description'         => 'This is the principal language/translation (basis for all others)',
    'author'              => 'Peter Fokker',
    'version'             => 2011093000,
    'release'             => '0.90.3',
    'release_date'        => '2011-09-30',
    'is_core'             => TRUE
   );

?>