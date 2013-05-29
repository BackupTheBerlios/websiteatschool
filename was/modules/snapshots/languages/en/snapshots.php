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

/** /program/modules/snapshots/languages/en/snapshots.php - translated messages for module (English)
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_snapshots
 * @version $Id: snapshots.php,v 1.3 2013/05/29 15:25:26 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['title'] = 'Snapshots';
$string['description'] = 'This module is a quick image viewer';
$string['translatetool_title'] = 'Snapshots';
$string['translatetool_description'] = 'This file contains translations for the Snapshots-module';

$comment['snapshots_content_header'] = 'Here is the snapshots configuration dialog:

~Header
~Introduction
~Location
Select the ~variant
[X] ~Thumbnails
[ ] ~First
[ ] Slidesho~w
~Box
[~Save] [~Cancel]

Please make sure your translation has a comparable set of hotkeys (indicated via the tildes \'~\').';

$string['snapshots_content_header'] = 'Snapshots configuration';
$string['snapshots_content_explanation'] = 'Here you can configure the snapshots module.
You can add an optional header and an optional introduction to the snapshots.
You can also change the initial display of the snapshots.
Use one of the following options:
\'thumbnails\' to start with the introductory text and an overview of all snapshots,
\'first\' to start with the first snapshot in the series, or
\'slideshow\' for an automatic slideshow (javascript-based).';

$string['header_label'] = '~Header';
$string['header_title'] = 'Header for the snapshots';
$string['introduction_label'] = '~Introduction';
$string['introduction_title'] = 'Introduction text for the snapshots';
$string['snapshots_path_label'] = '~Location';
$string['snapshots_path_title'] = 'Data folder holding the snapshots';
$string['variant_label'] = 'Select the ~variant of the snapshots initial display';
$string['variant_title'] = 'Select one of the options to choose the variant to use';
$string['variant_thumbs_label'] = '~Thumbnails';
$string['variant_thumbs_title'] = 'Start with the introduction and all thumbnails';
$string['variant_first_label'] = '~First';
$string['variant_first_title'] = 'Start with the first snapshot of the series';
$string['variant_slideshow_label'] = 'Slidesho~w';
$string['variant_slideshow_title'] = 'Show all snapshots, one by one';
$string['dimension_label'] = '~Box size (in pixels)';
$string['dimension_title'] = 'Enter the dimension of the box containing the full-size snapshots';

$string['no_snapshots_available'] = 'No snapshots are available';
$string['warning_no_such_snapshot'] = 'Warning: cannot find snapshot \'{SNAPSHOT}\'';

$string['move_first_title'] = 'First';
$string['move_first_alt'] = 'first';
$string['move_prev_title'] = 'Previous';
$string['move_prev_alt'] = 'previous';
$string['move_up_title'] = 'Overview';
$string['move_up_alt'] = 'thumbnails';
$string['move_next_title'] = 'Next';
$string['move_next_alt'] = 'next';
$string['move_last_title'] = 'Last';
$string['move_last_alt'] = 'last';
$string['move_current_title'] = 'Current';
$string['move_current_alt'] = 'current';
$string['slideshow_title'] = 'Slideshow (opens in a pop-up window)';
$string['slideshow_alt'] = 'slideshow';
$string['snapshot_status'] = '{SNAPSHOT}/{SNAPSHOTS} - {CAPTION}';
$string['warning_different_area'] = 'Warning: you selected snapshots located in another area ({AREANAME})';
$string['warning_personal_directory'] = 'Warning: you selected snapshots located in your personal directory';
$comment['js_loading'] = 'The messages below are used to show an error/warning from the javascript slideshow.';
$string['js_loading'] = 'loading...';
$string['js_no_images'] = 'No snapshots to show';
$comment['snapshots0_title'] = 'Below are the strings that are used in the demodata';
$string['snapshots0_title'] = 'This section holds the main photo album of the school';
$string['snapshots0_link_text'] = 'Pictures';
$string['snapshots1_title'] = 'Pictures of our latest field trip';
$string['snapshots1_link_text'] = 'Field trip {LAST_WEEK}';
$string['snapshots1_header'] = 'Field trip to the botanical garden ({LAST_WEEK})';
$string['snapshots1_introduction'] = 'Here are the photos of the field trip to the botanical garden the seniors made on {LAST_WEEK}.<p>{LOREM} {IPSUM} {DOLOR}';

?>