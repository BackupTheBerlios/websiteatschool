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

/** /program/modules/crew/crew_view.php - interface to the view-part of the crew module
 *
 * This file defines the interface with the crew-module for viewing content.
 * The interface consists of this function:
 *
 * <code>
 * crew_view(&$output,$area_id,$node_id,$module)
 * </code>
 *
 * This function is called from /index.php when the node to display is connected
 * to this module.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_crew
 * @version $Id: crew_view.php,v 1.1 2013/05/30 15:38:20 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** display the content of the workshop linked to node $node_id
 *
 *
 * @param object &$theme collects the (html) output
 * @param int $area_id identifies the area where $node_id lives
 * @param int $node_id the node to which this module is connected
 * @param array $module the module record straight from the database
 * @return bool TRUE on success + output via $theme, FALSE otherwise
 */
function crew_view(&$theme,$area_id,$node_id,$module) {
    global $USER;
    //
    // 1 -- 
    //
    $table = 'workshops';
    $fields = array('header','introduction','visibility');
    $where = array('node_id' => intval($node_id));
    $record = db_select_single_record($table,$fields,$where);
    if ($record === FALSE) {
        logger(sprintf('%s(): error retrieving configuration: %s',__FUNCTION__,db_errormessage()));
        $visibility = 0;
        $header = '';
        $introduction = '';
    } else {
        $visibility = intval($record['visibility']);
        $header = trim($record['header']);
        $introduction = trim($record['introduction']);
    }
    //
    // 2 -- compute 
    //

    //
    // 3 -- maybe output a header and an introduction
    //
    if (!empty($header)) {
        $theme->add_content('<h2>'.$header.'</h2>');
    }
    if (!empty($introduction)) {
        $theme->add_content($introduction);
    }

    //
    // 4 - Actually output document
    //
    $theme->add_content('<h2>STUB!!!</h2>');
    $theme->add_content('STUB - this is work in progress');
    return TRUE; // indicate success
} // crew_view()

?>