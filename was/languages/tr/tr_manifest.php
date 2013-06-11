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

/** /program/languages/tr/tr_manifest.php - description of the Turkish translation
 *
 * This file defines the Turkish language package ('tr'). 
 * This file is used when this package is installed.
 *
 * @author Ülkü Gaga <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam <info@websiteatschool.eu>
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_tr
 * @version $Id: tr_manifest.php,v 1.3 2013/06/11 11:25:16 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$manifests['tr'] = array(
    'language_key'        => 'tr',
    'language_name'       => 'Türkçe',
    'parent_language_key' => 'nl',
    'description'         => 'This is the Turkish translation based on Website@School release 0.90.3',
    'author'              => 'Ülkü Gaga',
    'version'             => 2012041700,
    'release'             => '0.90.3',
    'release_date'        => '2012-04-17',
    'is_core'             => FALSE
   );

?>