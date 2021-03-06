<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker <peter@berestijn.nl>
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

/** /program/themes/axis/axis_manifest.php - description of the axis theme
 *
 * This file defines the 'axis' theme.
 * This file is used when this theme is installed or upgraded.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wastheme_axis
 * @version $Id: axis_manifest.php,v 1.4 2013/07/11 10:40:29 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$manifests['axis'] = array(
    'name' => 'axis',
    'description' => 'This is a simple theme with a vertical hierarchical menu',
    'author' => 'Peter Fokker',
    'version' => 2012041200,
    'release' => '0.90.5',
    'release_date' => '2013-07-11',
    'is_core' => FALSE,
    'tabledefs' => 'NULL',
    'install_script' => 'axis_install.php',
    'class' => 'ThemeAxis',
    'class_file' => 'axis.class.php'
   );

?>