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

/** /program/languages/pt/pt_manifest.php - description of the Portugese translation
 *
 * This file defines the Portugese language package ('pt'). 
 * This file is used when this package is installed.
 *
 * @author Rita Valente Ribeiro da Silva <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam <info@websiteatschool.eu>
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_pt
 * @version $Id: pt_manifest.php,v 1.3 2013/06/14 19:59:57 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$manifests['pt'] = array(
    'language_key'        => 'pt',
    'language_name'       => 'Português',
    'parent_language_key' => 'es',
    'description'         => 'This is the Portugese translation based on Website@School release 0.90.4',
    'author'              => 'Rita Valente Ribeiro da Silva',
    'version'             => 2013061400,
    'release'             => '0.90.4',
    'release_date'        => '2013-06-14',
    'is_core'             => FALSE
   );

?>