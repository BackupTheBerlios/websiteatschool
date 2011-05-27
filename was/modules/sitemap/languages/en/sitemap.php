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

/** /program/modules/sitemap/languages/en/sitemap.php - translated messages for module (English)
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_sitemap
 * @version $Id: sitemap.php,v 1.1 2011/05/27 22:02:19 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['title'] = 'Sitemap';
$string['description'] = 'This module shows a small, medium or large sitemap';
$string['translatetool_title'] = 'Sitemap';
$string['translatetool_description'] = 'This file contains translations for the Sitemap-module';

$string['sitemap_content_header'] = 'Sitemap configuration';
$string['sitemap_content_explanation'] = 'Here you can configure the sitemap. Use \'area\' for a simple area map, \'limited\' for an area map followed by a list of links to other areas and \'full\' for a complete overview of all areas.';

$string['scope_label'] = 'Select the scope of the sitemap:';
$string['scope_title'] = 'Select one of the options to set the scope of the sitemap';
$string['scope_small_label'] = '~Area';
$string['scope_small_title'] = 'Simple area map';
$string['scope_medium_label'] = '~Limited';
$string['scope_medium_title'] = 'Area map followed by list of other available areas';
$string['scope_large_label'] = '~Full';
$string['scope_large_title'] = 'Full overview of all available areas';

$string['sitemap_available_areas'] = 'Available areas';
?>