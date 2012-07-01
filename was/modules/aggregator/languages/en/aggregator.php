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

/** /program/modules/aggregator/languages/en/aggregator.php - translated messages for module (English)
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_aggregator
 * @version $Id: aggregator.php,v 1.1 2012/07/01 18:45:40 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['title'] = 'Aggregator';
$string['description'] = 'This module aggregates selected nodes';
$string['translatetool_title'] = 'Aggregator';
$string['translatetool_description'] = 'This file contains translations for the Aggregator-module';

$comment['aggregator_content_header'] = 'Here is the aggregator configuration dialog:

~Header
~Introduction
~List of nodes
~Number of nodes to show
~Reverse order pages within sections
~Text length in paragraphs (htmlpage)
~Width in pixels (snapshots)
H~eight in pixels (snapshots)
~Visible images (snapshots)
~Pause between images in seconds (snapshots)
[~Save] [~Cancel]

Please make sure your translation has a comparable set of hotkeys (indicated via the tildes \'~\').';

$string['aggregator_content_header'] = 'Aggregator configuration';
$string['aggregator_content_explanation'] = 'Here you can configure the aggregator module.
You can add an optional header and an optional introduction to the aggregator.
You should specify a comma-delimited list of page or section numbers. The specified
pages will be aggregated in the output of this module. If you specify a section number,
all individual pages within that section are aggregated, in natural order or in reversed order.
<p>
If a specified page is linked to the snapshots module, the specified
number of images is displayed and rotated after the pause specified
below. If the page is linked to the htmlpage-module, the first few
paragraphs will be displayed.
<p>
Note:<br>
Pages linked to an unrecognised module are not aggregated.';
$string['header_label'] = '~Header';
$string['header_title'] = 'Header for the aggregator';
$string['introduction_label'] = '~Introduction';
$string['introduction_title'] = 'Introduction text for the aggregator';
$string['node_list_label'] = '~List of pages and sections';
$string['node_list_title'] = 'A comma delimited list of page and section numbers';

$string['items_label'] = '~Number of pages to aggregate';
$string['items_title'] = 'This is the maximum number of pages to aggregate';
$string['reverse_order_check'] = '~Reverse the sort order';
$string['reverse_order_label'] = '';
$string['reverse_order_title'] = 'Check the box to reverse the sort order of pages within a specified section';
$string['htmlpage_length_label'] = '~Text length in paragraphs (htmlpage)';
$string['htmlpage_length_title'] = 'The length of the extracted text from the page (in paragraphs)';
$string['snapshots_width_label'] = '~Width in pixels (snapshots)';
$string['snapshots_width_title'] = 'The total width available for displaying snapshots';
$string['snapshots_height_label'] = 'H~eight in pixels (snapshots)';
$string['snapshots_height_title'] = 'The height available for displaying snapshots';
$string['snapshots_visible_label'] = '~Visible images (snapshots)';
$string['snapshots_visible_title'] = 'The number of visible snapshots';
$string['snapshots_showtime_label'] = '~Pause between images in seconds (snapshots)';
$string['snapshots_showtime_title'] = 'The time (in seconds) before the next image is displayed';

$string['invalid_node'] = '{FIELD}: invalid page/section number \'{VALUE}\'';

?>