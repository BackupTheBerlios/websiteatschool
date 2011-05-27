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

/** /program/modules/sitemap/sitemap_view.php - interface to the view-part of the sitemap module
 *
 * This file defines the interface with the sitemap-module for viewing content.
 * The interface consists of this function:
 *
 * <code>
 * sitemap_view(&$output,$area_id,$node_id,$module)
 * </code>
 *
 * This function is called from /index.php when the node to display is connected
 * to this module.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_sitemap
 * @version $Id: sitemap_view.php,v 1.1 2011/05/27 22:02:18 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** display the content of the sitemap linked to node $node_id
 *
 * there are three different variations (depends on configuration parameter 'scope'):
 *
 *  - 0 (small): only show a map of the tree in the current area $area_id
 *  - 1 (medium): show a list of available areas followed by the map of the current area $area_id
 *  - 2 (large): show the maps of all available areas
 *
 * The default is 0 (small).
 *
 * @param object &$theme collects the (html) output
 * @param int $area_id identifies the area where $node_id lives
 * @param int $node_id the node to which this module is connected
 * @param array $module the module record straight from the database
 * @return bool TRUE on success + output via $theme, FALSE otherwise
 */
function sitemap_view(&$theme,$area_id,$node_id,$module) {
    global $USER,$WAS_SCRIPT_NAME;
    //
    // 1 -- determine scope of sitemap: 0=small, 1=medium, 2=large
    //
    $table = 'sitemaps';
    $fields = array('scope');
    $where = array('node_id' => intval($node_id));
    $record = db_select_single_record($table,$fields,$where);
    if ($record === FALSE) {
        logger(sprintf('%s(): error retrieving configuration: %s',__FUNCTION__,db_errormessage()));
        $scope = 0;
    } else {
        $scope = intval($record['scope']);
    }
    //
    // 2 -- compute a list of areas to process (could be just 1)
    //

    // 2A -- retrieve all areas, including those out of bounds for this user
    if (($all_areas = get_area_records()) === FALSE) {
        logger(sprintf('%s(): huh? cannot get area records: %s',__FUNCTION__,db_errormessage()));
        return FALSE; // shouldn't happen
    }

    // 2B -- narrow down the selection (active, (private) access allowed, within scope)
    $areas = array();
    foreach($all_areas as $id => $area) {
        if ((db_bool_is(TRUE,$area['is_active'])) &&
            ((db_bool_is(FALSE,$area['is_private'])) || 
             ($USER->has_intranet_permissions(ACL_ROLE_INTRANET_ACCESS,$id)))) {
            if (($scope == 2) || ($scope == 1) || (($scope == 0) && ($id == $area_id))) {
                $href   = ($theme->preview_mode) ? "#" : $WAS_SCRIPT_NAME;
                $params = ($theme->preview_mode) ?  NULL : array('area' => $id);
                $attributes = ($area_id == $id) ? array('class' => 'current') : NULL;
                $areas[$id] = html_a($href,$params,$attributes,$area['title']);
            }
        }
    }
    unset($all_areas);

    // $areas now holds all areas that we should to process
    if (sizeof($areas) <= 0) {
        logger(sprintf('%s(): weird, no areas to process; bailing out',__FUNCTION__));
        return FALSE; // shouldn't happen
    }

    //
    // 3 - Actually output a sitemap by walking the tree once for every elegible area
    //
    foreach($areas as $id => $area_anchor) {
        if (($scope == 1) && ($area_id != $id)) { // 1=medium only shows area map of $area_id (and an area list lateron)
            continue;
        }
        // 3A -- output a clickable area title
        $theme->add_content('<h2>'.$area_anchor.'</h2>');

        // 3B -- fetch the tree for this area...
        $tree = tree_build($id);
        tree_visibility($tree[0]['first_child_id'],$tree);

        // 3C -- ...and walk the tree
        sitemap_tree_walk($theme,$tree[0]['first_child_id'],$tree);
        unset($tree);
    }

    if ($scope == 1) {
        $theme->add_content('<h2>'.t('sitemap_available_areas','m_sitemap').'</h2>');
        $theme->add_content('<ul>');
        foreach($areas as $id => $area_anchor) {
            $theme->add_content('<li>'.$area_anchor);
        }
        $theme->add_content('</ul>');
    }
    return TRUE; // indicate success
} // sitemap_view()

function sitemap_tree_walk(&$theme,$subtree_id,&$tree,$m='') {
    static $level = 0;
    $class_level = 'level'.strval($level);
    $theme->add_content($m.'<ul>');
    for ($node_id = $subtree_id; ($node_id != 0); $node_id = $tree[$node_id]['next_sibling_id']) {
        if ($tree[$node_id]['is_visible']) {
            // 1 -- show this node
            $is_page        = $tree[$node_id]['is_page'];
            $class          = (($is_page) ? 'page ' : 'section ').$class_level;
            $theme->add_content($m.'  '.html_tag('li',array('class' => $class)).
                                        $theme->node2anchor($tree[$node_id]['record'],NULL,TRUE));
            // 2 -- maybe descend to subsection
            if (!$is_page) { 
                if (($subsubtree_id = $tree[$node_id]['first_child_id']) > 0) {
                    ++$level;
                    if ($level > MAXIMUM_ITERATIONS) {
                        logger(__FILE__.'('.__LINE__.') too many levels in node '.$node_id);
                    } else {
                        sitemap_tree_walk($theme,$subsubtree_id,$tree,$m.'  ');
                    }
                    --$level;
                }
            } // current subsection
        } // visible
    } // for
    $theme->add_content($m.'</ul>');
} // sitemap_tree_walk()

?>