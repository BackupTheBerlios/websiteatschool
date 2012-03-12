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

/** /program/themes/rosalina/languages/en/rosalina.php - translated messages for theme (English)
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wastheme_rosalina
 * @version $Id: rosalina.php,v 1.2 2012/03/12 06:56:13 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['title'] = 'Rosalina Theme';
$string['description'] = 'This theme implements HV Menu (Javascript-based)';
$string['translatetool_title'] = 'Rosalina';
$string['translatetool_description'] = 'This file contains translations for the Rosalina theme';

$string['you_are_here'] = 'You are here:';
$string['alt_logo'] = 'image of logo';
$string['jumpmenu_area'] = 'Select area';
$string['jumpmenu_area_title'] = 'Select an area and press [Go]';

$string['jumpmenu_areas'] = 'Areas';
$string['lastupdated'] = 'last updated: {UPDATE_YEAR}-{UPDATE_MONTH}-{UPDATE_DAY}';
$string['copyright'] = '&copy;{COPYRIGHT_YEAR} {SITENAME}';

$comment['quicktop_section_id_label'] = 
"Here are the translations for the dialog to edit the properties of the Rosalina-theme.
Because there *many* properties, it is impossible to give all properties their own hotkey.

This means that it is not possible to navigate to a specified property via a hotkey.
However, it is still possible to submit (~Save) the changes via the default hotkey
for the Save-button or to discard the changes (~Cancel) via the default hotkey for the
Cancel-button.

We start with the settings that Rosaline inherits from the built-in theme (Frugal).
Note that you can re-use the existing translations: simply copy them from the
translation of the Frugal theme.
";

$string['quicktop_section_id_label'] = 'Quicklinks section top';
$string['quicktop_section_id_title'] = 'Number of section containing links at top of every page (0 for none)';
$string['quickbottom_section_id_label'] = 'Quicklinks section bottom';
$string['quickbottom_section_id_title'] = 'Number of section containing links at bottom of every page (0 for none)';
$string['show_breadcrumb_trail_label'] = 'Breadcrumb trail';
$string['show_breadcrumb_trail_title'] = 'Check the box to show a breadcrumb trail';
$string['show_breadcrumb_trail_option'] = 'Show breadcrumb trail';
$string['logo_image_label'] = 'Logo';
$string['logo_image_title'] = 'URL of the logo image file';
$string['logo_height_label'] = 'Logo height';
$string['logo_height_title'] = 'Height of the logo in pixels';
$string['logo_width_label'] = 'Logo width';
$string['logo_width_title'] = 'Width of the logo in pixels';
$string['stylesheet_label'] = 'Static stylesheet';
$string['stylesheet_title'] = 'URL of the static stylesheet file (empty for none)';
$string['style_usage_static_label'] = 'Static stylesheet usage';
$string['style_usage_static_option'] = 'Use static stylesheet file';
$string['style_usage_static_title'] = 'Check the box to include the static stylesheet on every page';
$string['style_label'] = 'Extra style at area level';
$string['style_title'] = 'Additional area-wide style information';
$string['style_usage_area_label'] = 'Extra style usage (area)';
$string['style_usage_area_option'] = 'Use extra style at area level';
$string['style_usage_area_title'] = 'This applies the style information from \'Extra style\' above';
$string['style_usage_node_label'] = 'Extra style usage (node)';
$string['style_usage_node_option'] = 'Allow node level style information (\'Bazaar Style Style\')';
$string['style_usage_node_title'] = 'This allows \'Bazaar Style Style\': a different style for every page/section';

$comment['logo_title_label'] = 'Here starts the list of additional texts for Rosalina dealing with the logo and hotspots and all';
$string['logo_title_label'] = 'Logo title';
$string['logo_title_title'] = 'The text the visitor sees when hovering over the logo';
$string['logo_alt_label'] = 'Logo alternative text';
$string['logo_alt_title'] = 'Text displayed when the image is not available or images are disabled in the browser';
$string['logo_hotspots_label'] = 'Hotspots';
$string['logo_hotspots_title'] = 'The number of hotspots defined for the logo';
$string['logo_hotspot_1_label'] = 'Hotspot 1';
$string['logo_hotspot_1_title'] = 'hotspot definition: shape;coords;href;text;alt_href;alt_text;target';
$string['logo_hotspot_2_label'] = 'Hotspot 2';
$string['logo_hotspot_2_title'] = 'hotspot definition: shape;coords;href;text;alt_href;alt_text;target';
$string['logo_hotspot_3_label'] = 'Hotspot 3';
$string['logo_hotspot_3_title'] = 'hotspot definition: shape;coords;href;text;alt_href;alt_text;target';
$string['logo_hotspot_4_label'] = 'Hotspot 4';
$string['logo_hotspot_4_title'] = 'hotspot definition: shape;coords;href;text;alt_href;alt_text;target';
$string['logo_hotspot_5_label'] = 'Hotspot 5';
$string['logo_hotspot_5_title'] = 'hotspot definition: shape;coords;href;text;alt_href;alt_text;target';
$string['logo_hotspot_6_label'] = 'Hotspot 6';
$string['logo_hotspot_6_title'] = 'hotspot definition: shape;coords;href;text;alt_href;alt_text;target';
$string['logo_hotspot_7_label'] = 'Hotspot 7';
$string['logo_hotspot_7_title'] = 'hotspot definition: shape;coords;href;text;alt_href;alt_text;target';
$string['logo_hotspot_8_label'] = 'Hotspot 8';
$string['logo_hotspot_8_title'] = 'hotspot definition: shape;coords;href;text;alt_href;alt_text;target';

$comment['hvmenu_LowBgColor_label'] = 'Here are the texts dealing with the configuration of HV-menu.
Note that there are a few texts that are currently un-used in the Rosalina theme (see below)';

$string['hvmenu_LowBgColor_label'] = 'Background colour topmenu';
$string['hvmenu_LowBgColor_title'] = 'Background colour when mouse is not hovering over toplevel menu items';

$string['hvmenu_LowSubBgColor_label'] = 'Background colour submenu';
$string['hvmenu_LowSubBgColor_title'] = 'Background colour when mouse is not hovering over submenu items';

$string['hvmenu_HighBgColor_label'] = 'Background colour active topmenu';
$string['hvmenu_HighBgColor_title'] = 'Background colour when mouse is hovering over toplevel menu item';

$string['hvmenu_HighSubBgColor_label'] = 'Background colour active submenu';
$string['hvmenu_HighSubBgColor_title'] = 'Background colour when mouse is hovering over submenu item';

$string['hvmenu_FontLowColor_label'] = 'Font colour topmenu';
$string['hvmenu_FontLowColor_title'] = 'Font colour when mouse is not hovering over toplevel menu item';

$string['hvmenu_FontSubLowColor_label'] = 'Font colour submenu';
$string['hvmenu_FontSubLowColor_title'] = 'Font colour when mouse is not hovering over submenu item';

$string['hvmenu_FontHighColor_label'] = 'Font colour active topmenu';
$string['hvmenu_FontHighColor_title'] = 'Font colour when mouse is hovering over toplevel menu item';

$string['hvmenu_FontSubHighColor_label'] = 'Font colour active submenu';
$string['hvmenu_FontSubHighColor_title'] = 'Font colour when mouse is hovering over submenu item';

$string['hvmenu_BorderColor_label'] = 'Border colour topmenu';
$string['hvmenu_BorderColor_title'] = 'Border colour for toplevel menu items';

$string['hvmenu_BorderSubColor_label'] = 'Border colour submenu';
$string['hvmenu_BorderSubColor_title'] = 'Border colour for submenu items';

$string['hvmenu_BorderWidth_label'] = 'Border width';
$string['hvmenu_BorderWidth_title'] = 'Border width (in pixels)';

$string['hvmenu_BorderBtwnElmnts_label'] = 'Separate menu items';
$string['hvmenu_BorderBtwnElmnts_option'] = 'Show border between menu items';
$string['hvmenu_BorderBtwnElmnts_title'] = 'Check the box to separate menu items from each other with a border';

$string['hvmenu_FontFamily_label'] = 'Fonts';
$string['hvmenu_FontFamily_title'] = 'Comma-separated list of font families to use for menu items';

$string['hvmenu_FontSize_label'] = 'Fontsize';
$string['hvmenu_FontSize_title'] = 'Size of the font to use, e.g. 8.5 or 10.0';

$string['hvmenu_FontBold_label'] = 'Bold';
$string['hvmenu_FontBold_option'] = 'Use bold text for menu items';
$string['hvmenu_FontBold_title'] = 'Check the box to show all menu items in bold';

$string['hvmenu_FontItalic_label'] = 'Italics';
$string['hvmenu_FontItalic_option'] = 'Use italic text for menu items';
$string['hvmenu_FontItalic_title'] = 'Check the box to show all menu items in italics';

$string['hvmenu_MenuTextCentered_label'] = 'Menu item alignment';
$string['hvmenu_MenuTextCentered_left_option'] = 'left aligned';
$string['hvmenu_MenuTextCentered_center_option'] = 'centered';
$string['hvmenu_MenuTextCentered_right_option'] = 'right aligned';
$string['hvmenu_MenuTextCentered_title'] = 'Select the alignment of the menu items';

$string['hvmenu_MenuCentered_label'] = 'Horizontal menu alignment';
$string['hvmenu_MenuCentered_left_option'] = 'left aligned';
$string['hvmenu_MenuCentered_center_option'] = 'centered';
$string['hvmenu_MenuCentered_right_option'] = 'right aligned';
$string['hvmenu_MenuCentered_title'] = 'Select the horizontal alignment of the complete menu';

$string['hvmenu_MenuVerticalCentered_label'] = 'Vertical menu alignment';
$string['hvmenu_MenuVerticalCentered_top_option'] = 'top';
$string['hvmenu_MenuVerticalCentered_middle_option'] = 'middle';
$string['hvmenu_MenuVerticalCentered_bottom_option'] = 'bottom';
$string['hvmenu_MenuVerticalCentered_static_option'] = 'static';
$string['hvmenu_MenuVerticalCentered_title'] = 'Select the vertical alignment of the complete menu';

$string['hvmenu_ChildOverlap_label'] = 'Horizontal overlap';
$string['hvmenu_ChildOverlap_title'] = 'Horizontal overlap child/parent (a number between -1.00 and +1.00)';

$string['hvmenu_ChildVerticalOverlap_label'] = 'Vertical overlap';
$string['hvmenu_ChildVerticalOverlap_title'] = 'Vertical overlap child/parent (a number between -1.00 and +1.00)';

$string['hvmenu_StartTop_label'] = 'Menu offset X';
$string['hvmenu_StartTop_title'] = 'Menu offset X-coordinate (pixels)';

$string['hvmenu_StartLeft_label'] = 'Menu offset Y';
$string['hvmenu_StartLeft_title'] = 'Menu offset Y-coordinate (pixels)';


$string['hvmenu_LeftPaddng_label'] = 'Left padding';
$string['hvmenu_LeftPaddng_title'] = 'Left padding (pixels)';

$string['hvmenu_TopPaddng_label'] = 'Top padding';
$string['hvmenu_TopPaddng_title'] = 'Top padding (pixels)';

$string['hvmenu_FirstLineHorizontal_label'] = 'Horizontal menu';
$string['hvmenu_FirstLineHorizontal_option'] = 'Start with a horizontal toplevel menu';
$string['hvmenu_FirstLineHorizontal_title'] = 'Check the box for a horizontal toplevel menu, uncheck for vertical toplevel menu';

$string['hvmenu_DissapearDelay_label'] = 'Delay';
$string['hvmenu_DissapearDelay_title'] = 'Delay (in ms) before a menu folds in';

$string['hvmenu_MenuWrap_label'] = 'Menu wrap';
$string['hvmenu_MenuWrap_option'] = 'Enable menu wrap';
$string['hvmenu_MenuWrap_title'] = 'Check the box to enable menu wrap around';

$string['hvmenu_RightToLeft_label'] = 'Right-to-left';
$string['hvmenu_RightToLeft_option'] = 'Enable right-to-left menu unfold';
$string['hvmenu_RightToLeft_title'] = 'Check the box to unfold the menu from right to left';

$string['hvmenu_UnfoldsOnClick_label'] = 'Unfold on click';
$string['hvmenu_UnfoldsOnClick_option'] = 'Unfold menu on mouse click';
$string['hvmenu_UnfoldsOnClick_title'] = 'Check the box to unfold on mouse click, uncheck to use mouse hovering';

$string['hvmenu_ShowArrow_label'] = 'Show arrows';
$string['hvmenu_ShowArrow_option'] = 'Show small arrows to indicate submenus';
$string['hvmenu_ShowArrow_title'] = 'Check the box to display small triangles to indicate submenus';

$string['hvmenu_KeepHilite_label'] = 'Keep highlight';
$string['hvmenu_KeepHilite_option'] = 'Keep the selected menu path highlighted';
$string['hvmenu_KeepHilite_title'] = 'Check the box to keep the menu path highlighted';

$string['hvmenu_Arrws_label'] = 'Arrow-images';
$string['hvmenu_Arrws_title'] = 'Comma-delimited list of filenames and dimensions: arrow,width,height,arrowdown,width,height,arrowleft,width,height';

$string['menu_top_label'] = 'Toplevel menu limits';
$string['menu_top_title'] = 'Comma-delimited list: min_width,char_width,max_width,height in px (default: 120,8,300,20)';
$string['menu_sub_label'] = 'Submenu limits';
$string['menu_sub_title'] = 'Comma-delimited list: min_width,char_width,max_width,height in px (default: 150,8,500,20)';

$comment['hvmenu_VerCorrect_label'] = 'Below are the labels of a few parameters that are currently unused in the Rosalina-theme. If you, the translator, are pressed for time, you may want to skip the texts below.';
$string['hvmenu_VerCorrect_label'] = 'Frame correction X';
$string['hvmenu_VerCorrect_title'] = 'Multiple frame correction X-coordinate (pixels)';
$string['hvmenu_HorCorrect_label'] = 'Frame correction Y';
$string['hvmenu_HorCorrect_title'] = 'Multiple frame correction Y-coordinate (pixels)';
$string['hvmenu_MenuFramesVertical_label'] = 'Frames in columns';
$string['hvmenu_MenuFramesVertical_option'] = 'Use frames in columns';
$string['hvmenu_MenuFramesVertical_title'] = 'Check the box to use frames in columns, uncheck for frames in rows';
$string['hvmenu_TakeOverBgColor_label'] = 'Frame background colour';
$string['hvmenu_TakeOverBgColor_option'] = 'Menu frame takes over background colour submenu item frame';
$string['hvmenu_TakeOverBgColor_title'] = 'Check the box to take over the background colour';
$string['hvmenu_FirstLineFrame_label'] = 'Top level frame';
$string['hvmenu_FirstLineFrame_title'] = 'Name of the toplevel frame (use \'self\' if no frames are used)';
$string['hvmenu_SecLineFrame_label'] = 'Submenu frame';
$string['hvmenu_SecLineFrame_title'] = 'Name of the submenu frame (use \'self\' if no frames are used)';
$string['hvmenu_DocTargetFrame_label'] = 'Content frame';
$string['hvmenu_DocTargetFrame_title'] = 'Name of the content frame (use \'self\' if no frames are used)';
$string['hvmenu_TargetLoc_label'] = 'Target ID';
$string['hvmenu_TargetLoc_title'] = 'Name of the element used for relative positioning';
$string['hvmenu_HideTop_label'] = 'Hide topmenu';
$string['hvmenu_HideTop_option'] = 'Hide topmenu while loading new content';
$string['hvmenu_HideTop_title'] = 'Check the box to hide the toplevel menu while loading a new page';
$string['hvmenu_WebMasterCheck_label'] = 'Webmaster check';
$string['hvmenu_WebMasterCheck_option'] = 'Check the menu tree';
$string['hvmenu_WebMasterCheck_title'] = 'This option is used for checking menu integrity during development';

$comment['demo_logo_title'] = 'Below are the few strings used to configure the public area with demo data';
$string['demo_logo_title'] = 'Hint: the jigsaw pieces are clickable';
$string['demo_logo_alt'] = 'School logo';
$string['demo_admin_php_title'] = 'Website@School (admin.php)';
$string['demo_index_php_title'] = 'Home';
$string['demo_index_php_login_title'] = 'Login';
$string['demo_index_php_logout_title'] = 'Logout';
$string['demo_websiteatschool_eu_title'] = 'Visit the Website@School project site';

?>