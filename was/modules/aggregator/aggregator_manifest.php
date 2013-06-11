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

/** /program/modules/aggregator/aggregator_manifest.php - description of the aggregator module
 *
 * This file defines the various components of the aggregator module such as
 * the names of the various scripts and version information. This file is
 * used when this module is installed.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_aggregator
 * @version $Id: aggregator_manifest.php,v 1.2 2013/06/11 11:25:17 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$manifests['aggregator'] = array(
    'name' => 'aggregator',
    'description' => 'This module aggregates selected htmlpages and snapshots',
    'author' => 'Peter Fokker',
    'version' => 2012070100,
    'release' => '0.90.4',
    'release_date' => '2012-07-01',
    'is_core' => TRUE,
    'has_acls' => FALSE,
    'tabledefs' => 'install/aggregator_tabledefs.php',
    'install_script' => 'aggregator_install.php',
    'view_script' => 'aggregator_view.php',
    'admin_script' => 'aggregator_admin.php',
    'search_script' => 'aggregator_search.php',
    'cron_script' => 'aggregator_cron.php',
    'cron_interval' => 0
    );

?>