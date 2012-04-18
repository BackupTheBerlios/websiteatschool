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

/** /program/modules/htmlpage/htmlpage_manifest.php - description of the htmlpage module
 *
 * This file defines the various components of the htmlpage-module such as
 * the names of the various scripts and version information. This file is
 * used when this module is installed.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_htmlpage
 * @version $Id: htmlpage_manifest.php,v 1.8 2012/04/18 10:09:16 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$manifests['htmlpage'] = array(
    'name' => 'htmlpage',
    'description' => 'This module is able to display/edit a simple HTML-page',
    'author' => 'Peter Fokker',
    'version' => 2011020100,
    'release' => '0.90.4',
    'release_date' => '2012-04-19',
    'is_core' => TRUE,
    'has_acls' => FALSE,
    'tabledefs' => 'install/htmlpage_tabledefs.php',
    'install_script' => 'htmlpage_install.php',
    'view_script' => 'htmlpage_view.php',
    'admin_script' => 'htmlpage_admin.php',
    'search_script' => 'htmlpage_search.php',
    'cron_script' => 'htmlpage_cron.php',
    'cron_interval' => 0
    );

?>