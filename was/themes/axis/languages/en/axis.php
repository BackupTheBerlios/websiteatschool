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

/** /program/themes/axis/languages/en/axis.php - translated messages for theme (English)
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wastheme_axis
 * @version $Id: axis.php,v 1.4 2013/06/11 11:25:38 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['title'] = 'Axis Theme';
$string['description'] = 'A simple theme with a vertical hierarchical menu';
$string['translatetool_title'] = 'Axis';
$string['translatetool_description'] = 'This file contains translations for the Axis Theme';

$string['copyright'] = '©{COPYRIGHT_YEAR} {SITENAME}';
$string['logout_username'] = 'logout {USERNAME}';
$string['logout_username_title'] = 'end session for {FULL_NAME}';
$string['print'] = 'print';
$string['print_title'] = 'printer-friendly version of this page';

$comment['stylesheet_label'] = 
"Here are the translations for the dialog to edit the properties of a theme.
All properties have a distinct hotkey, identified
by the tilde in front of the hotkey letter. This is the list.

~Use static stylesheet
Static st~ylesheet
~Printer friendly stylesheet
~Use static stylesheet file
Use extra style at ~area level
E~xtra style at area level
Allow ~node level style information ('Bazaar Style Style')

~Save
~Cancel
";

$string['stylesheet_label'] = 'Static st~ylesheet';
$string['stylesheet_title'] = 'URL of the static stylesheet file (empty for none)';

$string['stylesheet_print_label'] = '~Printer friendly stylesheet';
$string['stylesheet_print_title'] = 'URL of the stylesheet file for print version (empty for none)';

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

?>