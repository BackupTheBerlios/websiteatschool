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

/** /program/themes/frugal/languages/en/frugal.php - translated messages for theme (English)
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wastheme_frugal
 * @version $Id: frugal.php,v 1.5 2013/06/11 11:25:44 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['title'] = 'Frugal Theme';
$string['description'] = 'This theme is the most basic theme';
$string['translatetool_title'] = 'Frugal';
$string['translatetool_description'] = 'This file contains translations for the Frugal theme';

$string['you_are_here'] = 'You are here:';
$string['alt_logo'] = 'image of logo';
$string['jumpmenu_area'] = 'Select area';
$string['jumpmenu_area_title'] = 'Select an area and press [Go]';


$comment['quicktop_section_id_label'] = 
"Here are the translations for the dialog to edit the properties of a theme.
All properties have a distinct hotkey, identified
by the tilde in front of the hotkey letter. This is the list.

Quicklink section ~Top
Quicklink section Botto~m
~Logo
~Width of logo
~Height of logo
~Breadcrumb trail
Static st~ylesheet
Use ~static stylesheet file
E~xtra style at area level
Use extra style at ~area level
Allow ~node level style information

~Save
~Cancel
";

$string['quicktop_section_id_label'] = 'Quicklinks section ~top';
$string['quicktop_section_id_title'] = 'Number of section containing links at top of every page (0 for none)';

$string['quickbottom_section_id_label'] = 'Quicklinks section botto~m';
$string['quickbottom_section_id_title'] = 'Number of section containing links at bottom of every page (0 for none)';

$string['logo_image_label'] = '~Logo';
$string['logo_image_title'] = 'URL of the logo image file';

$string['logo_height_label'] = 'Logo ~height';
$string['logo_height_title'] = 'Height of the logo in pixels';

$string['logo_width_label'] = 'Logo ~width';
$string['logo_width_title'] = 'Width of the logo in pixels';

$string['show_breadcrumb_trail_label'] = 'Breadcrumb trail';
$string['show_breadcrumb_trail_title'] = 'Check the box to show a breadcrumb trail';
$string['show_breadcrumb_trail_option'] = 'Show ~breadcrumb trail';

$string['stylesheet_label'] = 'Static st~ylesheet';
$string['stylesheet_title'] = 'URL of the static stylesheet file (empty for none)';

$string['style_usage_static_label'] = 'Static stylesheet usage';
$string['style_usage_static_option'] = 'Use ~static stylesheet file';
$string['style_usage_static_title'] = 'Check the box to include the static stylesheet on every page';

$string['style_label'] = 'E~xtra style at area level';
$string['style_title'] = 'Additional area-wide style information';

$string['style_usage_area_label'] = 'Extra style usage (area)';
$string['style_usage_area_option'] = 'Use extra style at ~area level';
$string['style_usage_area_title'] = 'This applies the style information from \'Extra style\' above';

$string['style_usage_node_label'] = 'Extra style usage (node)';
$string['style_usage_node_option'] = 'Allow ~node level style information (\'Bazaar Style Style\')';
$string['style_usage_node_title'] = 'This allows \'Bazaar Style Style\': a different style for every page/section';



?>