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

/** /program/modules/redirect/redirect_manifest.php - description of the redirect module
 *
 * This file defines the various components of the redirect module such as
 * the names of the various scripts and version information. This file is
 * used when this module is installed.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_redirect
 * @version $Id: redirect_manifest.php,v 1.3 2013/07/11 10:40:28 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$manifests['redirect'] = array(
    'name' => 'redirect',
    'description' => 'This module allows redirection of nodes to other (external) URLs',
    'author' => 'Peter Fokker',
    'version' => 2013071100,
    'release' => '0.90.5',
    'release_date' => '2013-07-11',
    'is_core' => TRUE,
    'has_acls' => FALSE,
    'install_script' => 'redirect_install.php',
    'view_script' => 'redirect_view.php',
    'admin_script' => 'redirect_admin.php',
    'search_script' => 'redirect_search.php',
    'cron_script' => 'redirect_cron.php',
    'cron_interval' => 0
    );

?>