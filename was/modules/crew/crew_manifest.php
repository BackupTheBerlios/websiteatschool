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

/** /program/modules/crew/crew_manifest.php - description of the crew module
 *
 * This file defines the various components of the crew module such as
 * the names of the various scripts and version information. This file is
 * used when this module is installed.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_crew
 * @version $Id: crew_manifest.php,v 1.1 2013/05/30 15:38:20 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$manifests['crew'] = array(
    'name' => 'crew',
    'description' => 'This is the Collaborative Remote Educational Workshop module',
    'author' => 'Peter Fokker',
    'version' => 2013052900,
    'release' => '0.90.5',
    'release_date' => '2013-05-29',
    'is_core' => TRUE,
    'has_acls' => TRUE,
    'tabledefs' => 'install/crew_tabledefs.php',
    'install_script' => 'crew_install.php',
    'view_script' => 'crew_view.php',
    'admin_script' => 'crew_admin.php',
    'search_script' => 'crew_search.php',
    'cron_script' => 'crew_cron.php',
    'cron_interval' => 0
    );

?>
