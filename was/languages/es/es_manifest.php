<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2011 Vereniging Website At School, Amsterdam <info@websiteatschool.eu>
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

/** /program/languages/es/es_manifest.php - description of the Spanish translation
 *
 * This file defines the Spanish language package ('es'). 
 * This file is used when this package is installed.
 *
 * @author Anouk Coumans, Hanna Tulleken, Margot Molier <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2011 Vereniging Website At School, Amsterdam <info@websiteatschool.eu>
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_es
 * @version $Id: es_manifest.php,v 1.3 2011/09/29 18:58:52 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$manifests['es'] = array(
    'language_key'        => 'es',
    'language_name'       => 'Español',
    'parent_language_key' => 'en',
    'description'         => 'This is the Spanish translation based on Website@School release 0.90.2',
    'author'              => 'Anouk Coumans, Hanna Tulleken, Margot Molier',
    'version'             => 2011092900,
    'release'             => '0.90.2',
    'release_date'        => '2011-09-29',
    'is_core'             => FALSE
   );

?>