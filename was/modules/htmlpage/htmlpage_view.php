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

/** /program/modules/htmlpage/htmlpage_view.php - interface to the view-part of the htmlpage module
 *
 * This file defines the interface with the htmlpage-module for viewing content.
 * The interface consists of this function:
 *
 * <code>
 * htmlpage_view(&$output,$area_id,$node_id,$module)
 * </code>
 *
 * This function is called from /index.php when the node to display is connected
 * to this module.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_htmlpage
 * @version $Id: htmlpage_view.php,v 1.2 2011/02/03 14:04:00 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** display the content of the htmlpage linked to node $node_id
 *
 * @param object &$theme collects the (html) output
 * @param int $area_id identifies the area where $node_id lives
 * @param int $node_id the node to which this module is connected
 * @param array $module the module record straight from the database
 * @return bool TRUE on success + output via $theme, FALSE otherwise
 */
function htmlpage_view(&$theme,$area_id,$node_id,$module) {
    $record = db_select_single_record('htmlpages','page_data',array('node_id' => intval($node_id)));
    if ($record === FALSE) {
        $msg = "Oops. Something went wrong with the htmlpage linked to node '$node_id'";
        $theme->add_message($msg);
        $theme->add_popup_top($msg);
        $retval = FALSE;
    } else {
//        if ((10 <= $node_id) && ($node_id <= 11)) {
//            $msg = 'STUB to demonstrate the message area and popup-function (only pages 10 and 11): <b>'.
//                  __FUNCTION__."</b>(\$theme,$area_id,$node_id,{$module['module_id']})";
//            $theme->add_message($msg);
//            $theme->add_popup_bottom(strip_tags($msg));
//        }
        $theme->add_content($record['page_data']);
        $retval = TRUE;
    }
    return $retval;
} // htmlpage_view()

?>