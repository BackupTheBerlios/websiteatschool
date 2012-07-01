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

/** /program/modules/aggregator/aggregator_view.php - interface to the view-part of the module
 *
 * This file defines the interface with the aggregator-module for viewing content.
 * The interface consists of this function:
 *
 * <code>
 * aggregator_view(&$output,$area_id,$node_id,$module)
 * </code>
 *
 * This function is called from /index.php when the node to display is connected
 * to this module.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_aggregator
 * @version $Id: aggregator_view.php,v 1.1 2012/07/01 18:45:39 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
error_reporting(-1);

/** display the aggregated information from the nodes linked to this aggregator
 *
 *
 * @param object &$theme collects the (html) output
 * @param int $area_id identifies the area where $node_id lives
 * @param int $node_id the node to which this module is connected
 * @param array $module the module record straight from the database
 * @return bool TRUE on success + output via $theme, FALSE otherwise
 */
function aggregator_view(&$theme,$area_id,$node_id,$module) {
    global $USER,$CFG;
    // 1 -- retrieve configuration data
    $table = 'aggregator';
    $fields = array(
        'header',
        'introduction',
        'node_list',
        'items',
        'reverse_order',
        'htmlpage_length',
        'snapshots_width',
        'snapshots_height',
        'snapshots_visible',
        'snapshots_showtime');
    $where = array('node_id' => intval($node_id));
    $record = db_select_single_record($table,$fields,$where);
    if ($record === FALSE) {
        logger(sprintf('%s(): error retrieving configuration: %s',__FUNCTION__,db_errormessage()));
        $header              = '';
        $introduction        = '';
        $node_list           = '';
        $items               = 10;
        $reverse_order       = FALSE;
        $htmlpage_length     = 2;
        $snapshots_width     = 512;
        $snapshots_height    = 120;
        $snapshots_visible   = 3;
        $snapshots_showtime  = 5;
    } else {
        $header              = trim($record['header']);
        $introduction        = trim($record['introduction']);
        $node_list           = trim($record['node_list']);
        $items               = intval($record['items']);
        $reverse_order       = db_bool_is(FALSE,$record['node_list']) ? FALSE : TRUE;
        $htmlpage_length     = intval($record['htmlpage_length']);
        $snapshots_width     = intval($record['snapshots_width']);
        $snapshots_height    = intval($record['snapshots_height']);
        $snapshots_visible   = intval($record['snapshots_visible']);
        $snapshots_showtime  = intval($record['snapshots_showtime']);
    }
    if (!empty($header)) {
        $theme->add_content('<h2>'.$header.'</h2>');
    }
    if (!empty($introduction)) {
        $theme->add_content($introduction);
    }

    $theme->add_content('<p>STUB: page list: '.$node_list);
    $theme->add_content('<p><b>***** THIS IS JUST A PROOF OF CONCEPT *****</b>');

    $modules = db_select_all_records('modules','*','','','module_id');
//print_r($modules);
    $nodes = explode(',',$node_list);
    foreach($nodes as $k => $v) {
        $sub_node_id = intval($v);
	$node_record = db_select_single_record('nodes','*',array('node_id' => $sub_node_id));
        $module_id = $node_record['module_id'];
if (!isset($modules[$module_id])) {
  $theme->add_message("STUB: skipping unknown module page $sub_node_id here");
  continue;
}
        switch($modules[$module_id]['name']) {
        case 'htmlpage':
	    $theme->add_content('<h3>'.$node_record['title'].'</h3>');
	    $theme->add_content("STUB: plain html-page $sub_node_id goes here");
            for ($i=0; $i<20; ++$i) {
 	    	$theme->add_content("Just a little more text to see what is happening.");
		if ($i == 9) $theme->add_content("<p>");
	    }
	    $theme->add_content("<a href=\"#\">more...</a>");
            break;
        case 'snapshots':
	    $theme->add_content('<h3>'.$node_record['title'].'</h3>');
	    include_once($CFG->progdir.'/modules/snapshots/snapshots_view.php');
	    $snap = new SnapshotViewerInline($theme,$area_id,$sub_node_id,$modules[$module_id],
                        $snapshots_width,$snapshots_height,$snapshots_visible,$snapshots_showtime);
            $snap->run();
            unset($snap);
            break;
        }
    }
} // aggregator_view()

?>