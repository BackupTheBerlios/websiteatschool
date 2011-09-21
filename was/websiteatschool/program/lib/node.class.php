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

/** /program/lib/node.class.php - taking care of nodes
 *
 * This file defines a class for dealing with nodes.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: node.class.php,v 1.3 2011/09/21 18:54:20 pfokker Exp $
 * @todo we probably need to get rid of this file because it is not used (2010-12-07/PF)
 */
if (!defined('WASENTRY')) { die('no entry'); }

class Node {

    var $node_exists = FALSE;

    var $node_path = array();

    function Node($requested_node=NULL,$requested_area=NULL) {
        $record = $this->calculate_node($requested_node,$requested_area,$this->node_path);
        if ($record !== FALSE) {
            foreach($record as $k => $v) {
                $this->$k = $v;
            }
            $this->node_exists = TRUE;
        }
    }

    function exists() {
        return $this->node_exists;
    }

    function get_node_path() {
        return $this->node_path;
    }

    function get_area_id() {
        return $this->area_id;
    }

/** determine which node should be displayed based on user's request
 *
 * In total there are 4 cases for node n and area a:
 *
 * 1. n defined, a defined
 *    return n if it is visible and it matches with a and a is visible, otherwise FALSE
 *
 * 2. n defined, a undefined
 *    return n if n is visible and the corresponding a is visible, otherwise FALSE
 *
 * 3. n undefined, a defined
 *    return the default n in the specified a if a is active or FALSE if not found
 *
 * 4. n undefined, a undefined
 *    return the default n in the default a or FALSE if no active default area exists
 *
 * Cases 1 and 2 might lead to a node that is not available (ie expired or
 * under embargo). In order to not 'give away' information about the possible
 * existence of the requested node, we simply return FALSE, indicating that
 * the node was not found.
 *
 * There is a potential problem with a request for a node that is of type 'section'
 * (i.e. not a page that eventually yields actual content). The problem is that in
 * that case we should look for a valid page-type node in that section-type node.
 * The question is whether we should look for a default page in a (default) subsection
 * or not (nested default). Currently the strategy is to look for the first suitable
 * page if available, and otherwise descend into the first suitable subsection and
 * try again repeatedly until we find a page or we reach the end of the tree.
 *
 * Note:
 * As a side effect of this function, the path from the area root to the final page
 * is calculated. This navigation path is returned in the variable reference
 * $node_path.
 *
 * Calculation of this navigation path is necessary in order to determine
 * the visibility of a node; if a section-type node higher in the tree is not
 * available, all underlying nodes should also not be available. IOW: not anly do
 * we have to check the actual node, we need to check all parent nodes too.
 *
 * The array returned in $node_path is keyed with the node_id, i.e. the array
 * element $node_path[$node_id] yields the database record for node $node_id.
 *
 * Note that we check the node embargo date and the expiry date in
 * two separate expressions rather than using the 'BETWEEN low AND high'
 * because you never can be sure if it is inclusive low/high or not and
 * the database might assume the low is always smaller than high.
 *
 * @param integer|null the requested node number or null if none specified
 * @param integer|null the requested area number or null if none specified
 * @param array this referenced variable receives the (valid) path to the node to show
 * @return bool|array an array with valid node record and $node_path filled or FALSE if no node could be found
 * @todo refactor into two different functions: one for specified node, other for unspecified node
 */
function calculate_node($node,$area,&$node_path) {
    global $DB,$CFG;
    // $DB->debug = 1;
    $tbl_areas = $DB->prefix.'areas';
    $tbl_nodes = $DB->prefix.'nodes';
    $datim = db_escape_and_quote(strftime('%Y-%m-%d %T')); // current date/time between quotes, ready for SQL-use

    if (empty($node)) {
        if (empty($area)) {
            // case 4: nothing specified, first get default area
            // we look at the first active area, preferably one that has 'is_default' set to TRUE
            // we need the CASE-construct because it is not easy to sort on BOOLean values and
            // we really want to check out the default areas before we consider other areas
            $sql = 'SELECT area_id FROM '.$tbl_areas.' WHERE (is_active) '.
                   'ORDER BY CASE WHEN (is_default) THEN 0 ELSE 1 END, sort_order';

            // the first record is the one we want, so use the $limit parameter to get max 1 record
            // we leave immediately if there is no suitable area
            if (($DBResult = $DB->query($sql,1)) === FALSE) {
                if ($CFG->debug) { trigger_error($DB->errno.'/\''.$DB->error.'\''); }
                return FALSE;
            } elseif ($DBResult->num_rows != 1) {
                if ($CFG->debug) { logger('DEBUG '.__FILE__.'('.__LINE__.'): no available area',WLOG_DEBUG); }
                $DBResult->close();
                return FALSE;
            } else {
                $record = $DBResult->fetch_row_assoc();
                $area = $record['area_id'];
                $DBResult->close();
            }
        }
        // case 3: no node, but we do have an area (possibly calculated from case 4 above). get default node.
        $node_id_clause = 'n.parent_id = n.node_id'; // start with criterion for a top level node within an area
        for ($i = 0; $i < MAXIMUM_ITERATIONS; ++$i) {
            $sql = 'SELECT n.* '.
                   'FROM '.$tbl_nodes.' AS n INNER JOIN '.$tbl_areas.' AS a ON n.area_id = a.area_id '.
                   'WHERE '.$node_id_clause.' AND (n.embargo <= '.$datim.') AND ('.$datim.' <= n.expiry) AND '.
                          '(a.area_id = '.$area.') AND (a.is_active) '.
                   'ORDER BY CASE WHEN (n.is_default) THEN 0 ELSE 1 END, '.
                            'CASE WHEN (n.is_page) THEN 0 ELSE 1 END, '.
                            'sort_order';
            if (($DBResult = $DB->query($sql,1)) === FALSE) {
                if ($CFG->debug) { trigger_error($DB->errno.'/\''.$DB->error.'\''); }
                return FALSE;
            } elseif ($DBResult->num_rows != 1) {
                if ($CFG->debug) { logger('DEBUG '.__FILE__.'('.__LINE__.'): no available node',WLOG_DEBUG); }
                $DBResult->close();
                return FALSE;
            }
            // Still here? Then we have got at least one (more) node
            $record = $DBResult->fetch_row_assoc();
            $DBResult->close();
            $node = intval($record['node_id']);
            if (isset($node_path[$node])) { // Deja vu! This circular reference shouldn't happen
                if ($CFG->debug) {
                    logger('DEBUG '.__FILE__.'('.__LINE__.'): circular reference in area '.$area.' node '.$node,WLOG_DEBUG);
                }
                return FALSE;
            } else {
                $node_path[$node] = $record; // record this node as part of the path
            }
            if ($record['is_page']) { // we have found a (default) page in 0 or more (default) sections, we're done
                return $record;
            } else { // no joy yet, continue the search in this section, look for a (default) page or section
                $node_id_clause = 'n.parent_id = '.$node;
            }
        }
        if ($CFG->debug) {
            logger('DEBUG '.__FILE__.'('.__LINE__.'): too many iterations (endless loop) in area '.$area,WLOG_DEBUG);
        }
        return FALSE; // if we arrive here, we have spent MAXIMUM_ITERATIONS without conclusive answer, ergo: not found
    } else {
        if (empty($area)) {
            // case 2: only 'node' specified, so check area via node information
            $sql = 'SELECT n.* '.
                   'FROM '.$tbl_nodes.' AS n INNER JOIN '.$tbl_areas.' AS a ON n.area_id = a.area_id '.
                   'WHERE (n.node_id = '.$node.') AND (n.embargo <= '.$datim.') AND ('.$datim.' <= n.expiry) AND'.
                         '(a.is_active)';
        } else {
            // case 1: both parameters are specified so we need them to match
            $sql = 'SELECT n.* '.
                   'FROM '.$tbl_nodes.' AS n INNER JOIN '.$tbl_areas.' AS a ON n.area_id = a.area_id '.
                   'WHERE (n.node_id = '.$node.') AND (n.embargo <= '.$datim.') AND ('.$datim.' <= n.expiry) AND'.
                         '(a.area_id = '.$area.') AND (a.is_active)';
        }
        if (($DBResult = $DB->query($sql,1)) === FALSE) {
            if ($CFG->debug) { trigger_error($DB->errno.'/\''.$DB->error.'\''); }
            return FALSE;
        } elseif ($DBResult->num_rows != 1) {
            if ($CFG->debug) { logger('DEBUG '.__FILE__.'('.__LINE__.'): no such node',WLOG_DEBUG); }
            $DBResult->close();
            return FALSE;
        } else {
            $record = $DBResult->fetch_row_assoc();
            $node = $record['node_id'];
            $area = $record['area_id'];
            $DBResult->close();
        }
        // At this point we know $node and $area for certain.
        // There are now 2 x 2 = 4 possibilities
        // $node is a toplevel page => done!
        // $node is a page but not at the toplevel => check all parents
        // $node is a section but not at the toplevel => check all parents AND descend while looking for (default) page
        // $node is a toplevel section => descend while looking for (default) page
        $node_path[$node] = $record;
        $parent_node = intval($record['parent_id']);

        if ($node != $parent_node) {
            // not at toplevel, check out all parents
            // note that $node is not changed; it still points to the requested node
            for ($i = 0; $i < MAXIMUM_ITERATIONS; ++$i) {
                $sql = 'SELECT n.* '.
                       'FROM '.$tbl_nodes.' AS n INNER JOIN '.$tbl_areas.' AS a ON n.area_id = a.area_id '.
                       'WHERE (a.area_id = '.$area.') AND (a.is_active) AND '.
                           '(n.node_id = '.$parent_node.') AND(n.embargo <= '.$datim.') AND ('.$datim.' <= n.expiry)'.
                           ' AND NOT (n.is_page)';
                if (($DBResult = $DB->query($sql,1)) === FALSE) {
                    if ($CFG->debug) { trigger_error($DB->errno.'/\''.$DB->error.'\''); }
                    return FALSE;
                } elseif ($DBResult->num_rows != 1) {
                    if ($CFG->debug) { logger('DEBUG '.__FILE__.'('.__LINE__.'): no such parent node',WLOG_DEBUG); }
                    $DBResult->close();
                    return FALSE;
                }
                $record = $DBResult->fetch_row_assoc();
                $DBResult->close();
                $node_path = array($parent_node => $record) + $node_path; // insert parent at _begin_ of array
                if ($record['node_id'] == $record['parent_id']) {
                    break; // we've reached the top level; we're done here
                } else {
                    $parent_node = intval($record['parent_id']);
                }
            }
            if (MAXIMUM_ITERATIONS <= $i) {
                if ($CFG->debug) {
                    logger('DEBUG '.__FILE__.'('.__LINE__.'): too many iterations (endless loop) in node '.$node,WLOG_DEBUG);
                }
                return FALSE;
            }
        }
        // At this point we have checked the node parents all the way to the top.
        // Now we _might_ need to descend and look for a default page, if node is not a page
        if ($node_path[$node]['is_page']) {
            return $node_path[$node];
        }
        // Apparently a section was originally requested. So, we descend into the tree, looking for a suitable page

        $node_id_clause = 'n.parent_id = '.$node; // start with criterion for nodes within this section
        for ($i = 0; $i < MAXIMUM_ITERATIONS; ++$i) {
            $sql = 'SELECT n.* '.
                   'FROM '.$tbl_nodes.' AS n INNER JOIN '.$tbl_areas.' AS a ON n.area_id = a.area_id '.
                   'WHERE '.$node_id_clause.' AND (n.embargo <= '.$datim.') AND ('.$datim.' <= n.expiry) AND '.
                          '(a.area_id = '.$area.') AND (a.is_active) '.
                   'ORDER BY CASE WHEN (n.is_default) THEN 0 ELSE 1 END, '.
                            'CASE WHEN (n.is_page) THEN 0 ELSE 1 END, '.
                            'sort_order';
            if (($DBResult = $DB->query($sql,1)) === FALSE) {
                if ($CFG->debug) { trigger_error($DB->errno.'/\''.$DB->error.'\''); }
                return FALSE;
            } elseif ($DBResult->num_rows != 1) {
                $DBResult->close();
                return FALSE;
            }
            // Still here? Then we have got at least one (more) node
            $record = $DBResult->fetch_row_assoc();
            $DBResult->close();
            $node = intval($record['node_id']);
            if (isset($node_path[$node])) { // Deja vu! This circular reference shouldn't happen
                if ($CFG->debug) {
                    logger('DEBUG '.__FILE__.'('.__LINE__.'): circular reference in area '.$area.' node '.$node,WLOG_DEBUG);
                }
                return FALSE;
            } else {
                $node_path[$node] = $record; // record this node as part of the path, at the end of the array
            }
            if ($record['is_page']) { // we have found a (default) page in 0 or more (default) sections, we're done
                return $record;
            } else { // no joy yet, continue the search in this section, look for a (default) page or section
                $node_id_clause = 'n.parent_id = '.$node;
            }
        }
        if ($CFG->debug) {
            logger('DEBUG '.__FILE__.'('.__LINE__.'): too many iterations (endless loop) in area '.$area,WLOG_DEBUG);
        }
        return FALSE; // if we arrive here, we have spent MAXIMUM_ITERATIONS without conclusive answer, ergo: not found
    }
} // calculate_node()

}
?>