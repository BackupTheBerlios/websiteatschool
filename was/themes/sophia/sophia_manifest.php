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

/** /program/themes/sophia/sophia_manifest.php - description of the sophia theme
 *
 * This file defines the sophia theme.
 * It is used when this theme is installed.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wastheme_sophia
 * @version $Id: sophia_manifest.php,v 1.3 2013/06/11 11:25:56 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$manifests['sophia'] = array(
    'name' => 'sophia',
    'description' => 'This is a theme using big buttons for top level navigation',
    'author' => 'Peter Fokker',
    'version' => 2012062200,
    'release' => '0.90.4',
    'release_date' => '2012-06-22',
    'is_core' => TRUE,
    'tabledefs' => 'NULL',
    'install_script' => 'sophia_install.php',
    'class' => 'ThemeSophia',
    'class_file' => 'sophia.class.php'
   );

?>