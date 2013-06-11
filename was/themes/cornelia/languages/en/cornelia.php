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

/** /program/themes/cornelia/languages/en/cornelia.php - translated messages for theme (English)
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wastheme_cornelia
 * @version $Id: cornelia.php,v 1.2 2013/06/11 11:25:42 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['title'] = 'Cornelia Theme';
$string['description'] = 'This is a 2- or 3-column theme using inverted L for navigation';
$string['translatetool_title'] = 'Cornelia';
$string['translatetool_description'] = 'This file contains translations for the Cornelia theme';

$string['you_are_here'] = 'You are here:';
$string['alt_logo'] = 'go to start page';
$string['menu'] = 'menu';
$string['menu_menu'] = 'menu {MENU}';
$string['print'] = 'print';
$string['print_title'] = 'printer-friendly version of this page';

$comment['quicktop_section_id_label'] = 
"Here are the translations for the dialog to edit the properties of a theme.
All properties have a distinct hotkey, identified
by the tilde in front of the hotkey letter. However,
there may not be enough letters to give every prompt a distinct one
in your translation. This is the list.

~Quicklink section Top
Quicklin~k section Bottom
~Logo
~Width of logo
~Height of logo
~Breadcrumb trail
Static st~ylesheet
Additional stylesheet for ~2-columns
Pr~inter friendly stylesheet
~Use static stylesheet file
E~xtra style at area level
Use extra style at ~area level
Allow ~node level style information
Additional t~ext in header
~Path to banner file directory
Banner ~rotate interval
A~dditional html before menu
Additional h~tml after menu
Sidebar pa~ge list
Sidebar t~op html
Sidebar bottom ht~ml <---!
Additional ~footer text
~Save
~Cancel
";

$string['quicktop_section_id_label'] = '~Quicklinks section top';
$string['quicktop_section_id_title'] = 'Number of section containing links at top of every page (0 for none)';

$string['quickbottom_section_id_label'] = 'Quic~klinks section bottom';
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

$string['stylesheet2_label'] = 'Additional stylesheet for ~2-columns';
$string['stylesheet2_title'] = 'URL of the additional static stylesheet file (empty for none)';

$string['stylesheet_print_label'] = 'Pr~inter friendly stylesheet';
$string['stylesheet_print_title'] = 'URL of the additional stylesheet file for print version (empty for none)';

$string['style_usage_static_label'] = 'Static stylesheet usage';
$string['style_usage_static_option'] = '~Use static stylesheet file';
$string['style_usage_static_title'] = 'Check the box to include the static stylesheet on every page';

$string['style_label'] = 'E~xtra style at area level';
$string['style_title'] = 'Additional area-wide style information';

$string['style_usage_area_label'] = 'Extra style usage (area)';
$string['style_usage_area_option'] = 'Use extra style at ~area level';
$string['style_usage_area_title'] = 'This applies the style information from \'Extra style\' above';

$string['style_usage_node_label'] = 'Extra style usage (node)';
$string['style_usage_node_option'] = 'Allow ~node level style information (\'Bazaar Style Style\')';
$string['style_usage_node_title'] = 'This allows \'Bazaar Style Style\': a different style for every page/section';

$string['header_text_label'] = 'Additional t~ext in header';
$string['header_text_title'] = 'This additional text is added to the header of the page';

$string['header_banners_directory_label'] = '~Path to directory containing banner image files';
$string['header_banners_directory_title'] = 'Location of the background banners for the header';

$string['header_banners_interval_label'] = 'Banner ~rotate interval in minutes (0 = no banners at all)';
$string['header_banners_interval_title'] = 'This is the time after which another banner will be used for a page view';

$string['left_top_html_label'] = 'A~dditional HTML before menu';
$string['left_top_html_title'] = 'This additional free-form HTML appears above the menu in 1st column';

$string['left_bottom_html_label'] = 'Additional H~TML after menu';
$string['left_bottom_html_title'] = 'This additional free-form HTML appears below the menu in 1st column';

$string['sidebar_nodelist_label'] = 'Comma-delimited list of pa~ges to display in sidebar';
$string['sidebar_nodelist_title'] = 'Use \'0\' for empty page or \'-\' to suppress sidebar';

$string['right_top_html_label'] = 'Additional HTML at t~op of sidebar';
$string['right_top_html_title'] = 'This additional free-form HTML appears at the top of the 3rd column';

$string['right_bottom_html_label'] = 'Additional HT~ML at bottom of sidebar';
$string['right_bottom_html_title'] = 'This additional free-form HTML appears at the bottom of the 3rd column';

$string['footer_text_label'] = 'Additional text in ~footer';
$string['footer_text_title'] = 'This additional text is added to the footer of the page';

?>