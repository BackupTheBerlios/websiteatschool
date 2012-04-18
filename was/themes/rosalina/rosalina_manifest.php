<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker <peter@berestijn.nl>
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

/** /program/themes/rosalina/rosalina_manifest.php - description of the rosalina theme
 *
 * This file defines the rosalina theme. This file is
 * used when this theme is installed.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wastheme_rosalina
 * @version $Id: rosalina_manifest.php,v 1.3 2012/04/18 07:57:29 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$manifests['rosalina'] = array(
    'name' => 'rosalina',
    'description' => 'This is a theme patterned after the Site@School theme of the same name',
    'author' => 'Peter Fokker',
    'version' => 2011060300,
    'release' => '0.90.3',
    'release_date' => '2011-09-30',
    'is_core' => FALSE,
    'tabledefs' => 'NULL',
    'install_script' => 'rosalina_install.php',
    'class' => 'ThemeRosalina',
    'class_file' => 'rosalina.class.php'
   );

?>