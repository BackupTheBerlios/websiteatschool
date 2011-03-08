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

/** /program/themes/frugal/frugal_manifest.php - description of the frugal theme
 *
 * This file defines the frugal theme. This file is
 * used when this theme is installed.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wastheme_frugal
 * @version $Id: frugal_manifest.php,v 1.3 2011/03/08 17:19:24 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$manifests['frugal'] = array(
    'name' => 'frugal',
    'description' => 'This is a minimalistic theme',
    'author' => 'Peter Fokker',
    'version' => 2011020100,
    'release' => '0.90.0',
    'release_date' => '2011-02-01',
    'is_core' => TRUE,
    'tabledefs' => 'NULL',
    'install_script' => 'frugal_install.php',
    'class' => 'ThemeFrugal',
    'class_file' => 'frugal.class.php'
   );

?>