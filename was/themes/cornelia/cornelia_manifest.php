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

/** /program/themes/cornelia/cornelia_manifest.php - description of the cornelia theme
 *
 * This file defines the cornelia theme.
 * It is used when this theme is installed.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wastheme_cornelia
 * @version $Id: cornelia_manifest.php,v 1.1 2012/11/01 09:06:09 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$manifests['cornelia'] = array(
    'name' => 'cornelia',
    'description' => 'This is a 2-column/3-column theme using inverted L for navigation',
    'author' => 'Peter Fokker',
    'version' => 2012110100,
    'release' => '0.90.4',
    'release_date' => '2012-11-01',
    'is_core' => FALSE,
    'tabledefs' => 'NULL',
    'install_script' => 'cornelia_install.php',
    'class' => 'ThemeCornelia',
    'class_file' => 'cornelia.class.php'
   );

?>