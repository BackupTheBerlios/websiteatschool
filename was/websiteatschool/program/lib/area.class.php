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
# along with this program. If not, see http://websiteatschool.org/license.html

/** /program/lib/area.class.php - taking care of areas
 *
 * This file defines a class for dealing with areas.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.org/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @todo we probably need to get rid of this file because it is not used (2010-12-07/PF)
 * @version $Id: area.class.php,v 1.1 2011/02/01 13:00:34 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** the bare name of the areas table (without prefix), should be a class constant but PHP4 doesn't do that  */
define('AREA_CONST_TABLE_AREAS','areas');

/** the bare name of the nodes table (without prefix), should be a class constant but PHP4 doesn't do that  */
define('AREA_CONST_TABLE_NODES','nodes');


/** Methods to access properties of an area
 *
 * @todo refactor/change the way the default node and area are calculated from
 *       the requested node and area. We now go the the database too often.
 */
class Area {
    /** @var int|null the requested area or NULL if not specified */
    var $requested_area = NULL;

    /** @var int|null the requested node or NULL if not specified */
    var $requested_node = NULL;

    /** @var int the actual area id, calculated from $requested_area and $requested_node */
    var $area_id = NULL;

    /** @var int the actual node id, calculated from $requested_area and $requested_node */
    var $node_id = NULL;

    /** @var array list of cached node records keyed with node_id */
    var $nodes = array();

    /** @var array list of arrays acting as a tree structure of all nodes in the area */
    var $tree = array();

    /** @var array cached area record from database */
    var $area_record = FALSE;
 
    /** @var bool FALSE if area uninitialised/not found, otherwise TRUE */
    var $area_exists = FALSE;

    /** @var string the name of the areas table including table prefix */
    var $table_areas_prefix = '';

    /** @var string the name of the nodes table including table prefix */
    var $table_nodes_prefix = '';


    /** @var object the module that actually provides the content */
    var $module = NULL;

    /** construct an Area object
     *
     * This initialises the Area by calculating the correct node_id (of a page to show) and area_id.
     * If all goes well, initialisation continues with retrieving _all_ node records in an (ordered)
     * array $this->nodes and subsequently constructing the complete navigation tree from the retrieved
     * node records, taking 'hidden' pages/sections into account. If all is well,
     * the module referenced by the node $node_id is initialised.
     * Success is indicated by setting the variable $this->exists to TRUE.
     *
     * @param int|null $area_id the number of the area the user requested (or NULL if none specified)
     * @param int|null $node_id the number of the node to display (or NULL if none specified)
     * @return void the constructor returns nothing but $this->exists is set to TRUE on success
     * @uses $DB
     */
    function Area($requested_area=NULL,$requested_node=NULL) {
        global $DB;
        $this->requested_area = $requested_area;
        $this->requested_node = $requested_node;
        $this->table_areas_prefix = $DB->prefix.AREA_CONST_TABLE_AREAS;
        $this->table_nodes_prefix = $DB->prefix.AREA_CONST_TABLE_NODES;

        $this->nodes = array();
        $retval = $this->calculate_node_id($this->requested_area,$this->requested_node,$this->nodes);
        if ($retval === FALSE) { // no valid node found, bail out
            logger('DEBUG '.__FILE__.'('.__LINE__.'): no valid node found',LOG_DEBUG);
            return;
        }
        $this->node_id = intval($retval);
        $this->area_id = intval($this->nodes[$this->node_id]['area_id']);
        $this->area_record = db_select_single_record(AREA_CONST_TABLE_AREAS,'*',array('area_id' => intval($this->area_id)));
        if ($this->area_record === FALSE) { // failure reading area record, bail out
            logger('DEBUG '.__FILE__.'('.__LINE__.'): failure reading area record '.$this->area_id,LOG_DEBUG);
            return;
        }

        $this->nodes = $this->retrieve_nodes_from_database($this->area_id);
        if ($this->nodes === FALSE) { // failure reading all node records into cache, bail out
            logger('DEBUG '.__FILE__.'('.__LINE__.'): failure reading nodes in area '.$this->area_id,LOG_DEBUG);
            return;
        }
        $this->tree = $this->build_tree_of_nodes($this->nodes);

        $this->module = module_factory(intval($this->nodes[$this->node_id]['module_id']),$this->node_id);
        if ($this->module === FALSE) { // failure setting up module, bail out
            logger('DEBUG '.__FILE__.'('.__LINE__.'): failure constructing module for node '.$this->node_id,LOG_DEBUG);
            return;
        }
        $this->area_exists = TRUE; // finally indicate success
    } // Area()


    /** determine existence of area
     *
     * @return bool FALSE if area does not exist or object not yet initialised, TRUE otherwise
     */
    function exists() {
        return $this->area_exists;
    }


    /** determine the theme to use
     *
     * @return int theme_id for this area
     */
    function get_theme_id() {
        return intval($this->area_record['theme_id']);
    }


    /** determine if an area is private or public
     *
     * @return bool TRUE if area is private, FALSE otherwise
     */
    function area_is_private() {
        return (db_bool_is(TRUE,$this->area_record['is_private'])) ? TRUE : FALSE;
    }


    /** fetch the title to be used in a HTML-title-tag in the head section
     *
     * This tries to retrieve the area title. If that title
     * is not defined (i.e. empty), the global title of the
     * site is used.
     *
     * @return string title of area or global site title
     * @uses $CFG
     */
    function get_area_title() {
        global $CFG;
        $title = $this->area_record['title'];
        if (empty($title)) {
            $title = $CFG->title;
        }
        return $title;
    } // get_area_title()


    /** fetch the title of a node
     *
     * This tries to retrieve the title of the node.
     * The result could be an empty string; there is no 'built-in' title
     * such as 'page $node_id' or something. 
     *
     * @param int $node_id indicates which node, NULL implies the default node
     * @return bool|string FALSE on error or title of the node
     */
    function get_node_title($node_id = NULL) {
        $record = $this->get_node_record($node_id);
        if ($record !== FALSE) {
            $title = $record['title'];
        } else {
            $title = FALSE;
        }
        return $title;
    } // get_node_title()


    /** get a node record, maybe from the cache
     *
     * This retrieves a node record, either from the cache or otherwise from the database.
     * If data is retrieved from the database it is added to the cache. The possible embargo
     * is taken into account.
     *
     * @param int $node_id indicates the node record to retrieve
     * @return bool|array FALSE on error or an array with the node record
     */
    function get_node_record($node_id = NULL) {
        global $DB,$CFG;
        if (empty($node_id)) {
            $node_id = $this->node_id;
        }
        $node_id = intval($node_id);

        if (isset($this->nodes[$node_id])) {
            $record = $this->nodes[$node_id];
        } else {
            $datim = db_escape_and_quote(strftime('%Y-%m-%d %T')); // current date/time between quotes, ready for SQL-use
            $sql = 'SELECT n.* '.
                   'FROM '.$this->table_nodes_prefix.' AS n '.
                   'WHERE n.node_id = '.$node_id.' AND (n.embargo <= '.$datim.') AND ('.$datim.' <= n.expiry)';
            if (($DBResult = $DB->query($sql,1)) === FALSE) {
                if ($CFG->debug) { trigger_error($DB->errno.'/\''.$DB->error.'\''); }
                return FALSE;
            } elseif ($DBResult->num_rows != 1) {
                $DBResult->close();
                return FALSE;
            } else {
                $record = $DBResult->fetch_row_assoc();
                $DBResult->close();
                $this->nodes[$node_id] = $record;
            }
        }
        return $record;
    } // get_node_record()



    /** fetch breadcrumb trail for a node
     *
     * @param int $node_id indicates which node, NULL means the default node
     * @param array|null $attributes NULL or an array of key-value-pairs to add to the anchors
     * @return bool|array FALSE on error, an ordered array with link information otherwise
     */
    function get_breadcrumb_anchors($node_id = NULL,$attributes = NULL) {
        $anchors = array();
        for ($i = 0; $i < MAXIMUM_ITERATIONS; ++$i) {
            $record = $this->get_node_record($node_id);
            if ($record === FALSE) {
                return FALSE;
            }
            // insert anchor; build orderd list from right to left
            $anchor = $this->node2anchor($record,$attributes,TRUE); // TRUE = textonly
            $anchors = array_merge(array($anchor),$anchors);
            $parent_id = intval($record['parent_id']);
            $node_id = intval($record['node_id']);
            if ($node_id == $parent_id) {
                // we've reached the top level, return to caller
                return $anchors;
            } else {
                $node_id = $parent_id; // check out the parent of this node in next iteration
            }
        }
        // too many iterations (endless loop?)
        logger('DEBUG '.__FILE__.'('.__LINE__.'): too many iterations (endless loop) in node '.$node_id,LOG_DEBUG);
        return FALSE;
    } // get_breadcrumb_anchors()


    /** get an ordered array of node records with parent equal to $node_id
     *
     * This returns an ordered array of all childeren (pages and sections) of node $node_id.
     * If $node_id is itself a page, then an empty array is returned.
     * Note that only the direct descendants are returned, not the grandchildren.
     *
     * @param int $node_id the direct parent of the nodes that will be returned
     * @param int $show_hidden_too if TRUE also the 'hidden' childeren are returned
     * @return array an ordered array with node records (can be empty)
     */
    function get_childeren($node_id,$show_hidden_too = FALSE) {
        $childeren = array();
        if ($this->exists()) {
            $next_id = $this->tree[$node_id]['first_child_id'];
            while ($next_id != 0) {
                if (!($this->tree[$next_id]['is_hidden']) || ($show_hidden_too)) {
                    $childeren[$next_id] = $this->nodes[$next_id];
                }
                $next_id = $this->tree[$next_id]['next_sibling_id'];
            }
        }
        return $childeren;
    }

    /** get the link_href property of a node
     *
     * This retrieves the link_href property of the specified $node_id or
     * the default node.
     *
     * @param int|null $node_id indicates the node to look at or the default node if NULL
     * @return string contents of link_href property
     */
    function get_node_link_href($node_id = NULL) {
        $link_href = '';
        $record = $this->get_node_record($node_id);
        if ($record !== FALSE) {
            $link_href = $record['link_href'];
        }
        return $link_href;
    }


    /** construct an anchor from a node record
     *
     * This constructs an array with key-value-pairs that can be used to
     * construct an HTML anchor tag. At least the following keys are created
     * in the resulting array: 'href', 'title' and 'anchor'. The latter is either
     * the text or a referenct to an image that is supposed to go between the
     * opening tag and closing tag. Furtermore an optional key is created: target.
     * The contents of the input array $attributes is merged into the result.
     *
     * If the parameter $textonly is TRUE the key 'anchor' is always text.
     * If $textonly is NOT TRUE, the 'anchor' may refer to an image.
     *
     * Note that the link text is always non-empty. If the node record has an
     * empty link_text, the word 'node' followed by the node_id is returned.
     * (Otherwise it will be hard to make an actual clickable link).
     *
     * @param array $node_record the node record to convert
     * @param array $attributes optional attributes to add to the HTML A-tag
     * @param bool $textonly if TRUE, no clickable images will be returned
     * @return string an HTML A-tag that links to the node OR to the external link (if any)
     */
    function node2anchor($node_record,$attributes=NULL,$textonly=FALSE) {
        global $WAS_SCRIPT_NAME;
        $anchor = (is_array($attributes)) ? $attributes : array();

        $node_id = intval($node_record['node_id']);
        $link_text= (empty($node_record['link_text'])) ? '(node='.$node_id.')' :  $node_record['link_text'];
        $title = (empty($node_record['title'])) ? $link_text : $node_record['title'];

        if (empty($node_record['link_href'])) {
            $anchor['href'] = $WAS_SCRIPT_NAME . '?node=' . $node_id;

        } else {
            $anchor['href'] = $node_record['link_href'];
        }
        $anchor['title'] = $title;
        if (!empty($node_record['link_target'])) {
            $anchor['target'] = $node_record['link_target'];
        }
        if (($textonly) || (empty($node_record['image']))) {
            $anchor['anchor'] = $link_text;
        } else {
            $img = '<img src="'.$node_record['image'].'"'.
                       ' alt="'.$link_text.'"'.
                       ' title="'.$title.'"';
            if (!empty($node_record['link_image_width'])) {
                $img .= ' width="'.intval($node_record['link_image_width']).'"';
            }
            if (!empty($node_record['link_image_height'])) {
                $img .= ' height="'.intval($node_record['link_image_height']).'"';
            }
            $img .= '>';
            $anchor['anchor'] = $img;
        }
        return $anchor;
    } // node2anchor()


    // ====== every function below should be a private or protected function but PHP4 does not do that =====


    /** determine which node in which area to show
     *
     *
     * @param $requested_area integer|null the requested node number or null if none specified
     * @param $requested_node integer|null the requested area number or null if none specified
     * @param $node_cache array this referenced variable holds the node records retrieved from the database
     * @return bool|int the node_id of the node to show or FALSE if none available
     */
    function calculate_node_id($requested_area,$requested_node,&$node_cache) {
        $node_id = FALSE;
        if (empty($requested_node)) {
            $node_id = $this->calculate_validate_default_node_id($requested_area,$node_cache);
        } else {
            $node_id = $this->calculate_validate_node_id($requested_area,$requested_node,$node_cache);
        }
        return $node_id;
    }


    /** calculate and validate the default node from an area or the default area
     *
     * This determines the default node in the specified area or in the default area
     * if no area was explicitly requested. Returns the node_id OR FALSE if no suitable
     * node can be found.
     *
     * As a side-effect the node-records that are retrieved from the database in the process
     * are cached in $node_cache, with the corresponding node_id as the key.
     *
     * @param $requested_area integer|null the requested area number or null if none specified
     * @param $node_cache array this referenced variable holds the node records retrieved from the database
     * @return bool|int the node_id of the node to show or FALSE if none available
     */
    function calculate_validate_default_node_id($requested_area,&$node_cache) {
        global $DB,$CFG;
        $node_id = FALSE;
        //$DB->debug = 1;
        $datim = db_escape_and_quote(strftime('%Y-%m-%d %T')); // current date/time between quotes, ready for SQL-use

        if (empty($requested_area)) {
            // nothing specified (no node, no area), first get default area
            // we look at the first active area, preferably one that has 'is_default' set to TRUE
            // we need the CASE-construct because it is not easy to sort on BOOLean values and
            // we really want to check out the default areas before we consider other areas
            $sql = 'SELECT area_id FROM '.$this->table_areas_prefix.' WHERE (is_active) '.
                   'ORDER BY CASE WHEN (is_default) THEN 0 ELSE 1 END, sort_order';

            // the first record is the one we want, so use the $limit parameter to get max 1 record
            // we leave immediately if there is no suitable area to be found
            if (($DBResult = $DB->query($sql,1)) === FALSE) {
                if ($CFG->debug) { trigger_error($DB->errno.'/\''.$DB->error.'\''); }
                return FALSE;
            } elseif ($DBResult->num_rows != 1) {
                if ($CFG->debug) { logger('DEBUG '.__FILE__.'('.__LINE__.'): no available area',LOG_DEBUG); }
                $DBResult->close();
                return FALSE;
            } else {
                $record = $DBResult->fetch_row_assoc();
                $area_id = intval($record['area_id']);
                $DBResult->close();
            }
        } else {
            $area_id = intval($requested_area);
        }

        // we have an area (possibly freshly calculated above). now get default page within that area.
        $node_id = $this->calculate_default_descendant_node_id($area_id,NULL,$node_cache);
        return $node_id;
    } // calculate_validate_default_node_id()


    /** calculate and validate the node to display based on a node and an area or the default area
     *
     * This determines whether the specified node (and area if specified) is a valid node.
     * If the specified node is not a page (but a section), we descend into the subtree starting
     * at that node until we find a (default) node that is also a page.
     *
     * If no suitable node can be found, this routine returns FALSE. Otherwise the valid node_id
     * of the page is returned, even if the requested node was a section. In other words: this routine
     * may return another node_id that the one that was requested.
     *
     * 
     *
     * @param $requested_area integer|null the requested node number or null if none specified
     * @param $requested_node integer|null the requested area number or null if none specified
     * @param $node_cache array this referenced variable holds the node records retrieved from the database
     * @return bool|int the node_id of the node to show or FALSE if none available
     */
    function calculate_validate_node_id($requested_area,$requested_node,&$node_cache) {
        global $DB,$CFG;
        //$DB->debug = 1;
        $local_node_cache = array(); // used to detect circular references AND to cache node records
        $datim = db_escape_and_quote(strftime('%Y-%m-%d %T')); // current date/time between quotes, ready for SQL-use

        $node_id = intval($requested_node);
        if (empty($requested_area)) {
            // only 'node' specified but not area, so check area via node information
            $sql = 'SELECT n.* '.
                   'FROM '.$this->table_nodes_prefix.' AS n INNER JOIN '.$this->table_areas_prefix.' AS a '.
                         'ON n.area_id = a.area_id '.
                   'WHERE (n.node_id = '.$node_id.') AND (n.embargo <= '.$datim.') AND ('.$datim.' <= n.expiry) AND'.
                         '(a.is_active)';
        } else {
            // both parameters are specified so we need them to match
            $area_id = intval($requested_area);
            $sql = 'SELECT n.* '.
                   'FROM '.$this->table_nodes_prefix.' AS n INNER JOIN '.$this->table_areas_prefix.' AS a '.
                         'ON n.area_id = a.area_id '.
                   'WHERE (n.node_id = '.$node_id.') AND (n.embargo <= '.$datim.') AND ('.$datim.' <= n.expiry) AND'.
                         '(a.area_id = '.$area_id.') AND (a.is_active)';
        }
        if (($DBResult = $DB->query($sql,1)) === FALSE) {
            if ($CFG->debug) { trigger_error($DB->errno.'/\''.$DB->error.'\''); }
            return FALSE;
        } elseif ($DBResult->num_rows != 1) {
            if ($CFG->debug) { logger('DEBUG '.__FILE__.'('.__LINE__.'): no such node',LOG_DEBUG); }
            $DBResult->close();
            return FALSE;
        } else {
            $record = $DBResult->fetch_row_assoc();
            $node_id = intval($record['node_id']);
            $area_id = intval($record['area_id']);
            $DBResult->close();
        }

        // At this point we know $node_id and $area_id for certain.
        // There are now 2 x 2 = 4 possibilities
        // $node_id is a toplevel page => done!
        // $node_id is a page but not at the toplevel => check all parents
        // $node_id is a section but not at toplevel => check all parents AND descend while looking for (default) page
        // $node_id is a toplevel section => descend while looking for (default) page

        $local_node_cache[$node_id] = $record;
        $parent_id = intval($record['parent_id']);

        if ($node_id != $parent_id) {
            // not at toplevel, check out all parent sections; they must exist and not be under embargo
            // note that $node_id is not changed; it still points to the requested node 
            for ($i = 0; $i < MAXIMUM_ITERATIONS; ++$i) {
                $sql = 'SELECT n.* '.
                       'FROM '.$this->table_nodes_prefix.' AS n INNER JOIN '.$this->table_areas_prefix.' AS a '.
                             'ON n.area_id = a.area_id '.
                       'WHERE (a.area_id = '.$area_id.') AND (a.is_active) AND '.
                           '(n.node_id = '.$parent_id.') AND(n.embargo <= '.$datim.') AND ('.$datim.' <= n.expiry)'.
                           ' AND NOT (n.is_page)';
                if (($DBResult = $DB->query($sql,1)) === FALSE) {
                    if ($CFG->debug) { trigger_error($DB->errno.'/\''.$DB->error.'\''); }
                    return FALSE;
                } elseif ($DBResult->num_rows != 1) {
                    if ($CFG->debug) { logger('DEBUG '.__FILE__.'('.__LINE__.'): no such parent '.$parent_id,LOG_DEBUG); }
                    $DBResult->close();
                    return FALSE;
                }
                $record = $DBResult->fetch_row_assoc();
                $DBResult->close();
                $local_node_cache[$parent_id] = $record; // cache parent node
                if ($record['node_id'] == $record['parent_id']) {
                    break; // we've reached the top level; we're done here
                } else {
                    $parent_id = intval($record['parent_id']);
                    if (isset($local_node_cache[$parent_id])) { // Deja vu! This circular reference shouldn't happen
                        logger(__FILE__.'('.__LINE__.'): circular reference in area '.$area_id.' node '.$node_id);
                        return FALSE;
                    }
                }
            }
            if (MAXIMUM_ITERATIONS <= $i) {
                logger('DEBUG '.__FILE__.'('.__LINE__.'): endless loop in node '.$node_id,LOG_DEBUG);
                return FALSE;
            }
        }

        // At this point we have checked the ancestors all the way to the top.
        // They all exist and none of the ancestors is under embargo
        if (db_bool_is(FALSE,$local_node_cache[$node_id]['is_page'])) {
            // Search section for suitable default page
            $node_id = $this->calculate_default_descendant_node_id($area_id,$node_id,$local_node_cache);
        }
        $node_cache += $local_node_cache;
        return $node_id;
    } // calculate_validate_node_id()


    /** calculate the default page in the subtree $subtree_id in area $area_id
     *
     * This searches for the first default page in the subtree starting at node (of
     * type section) $subtree_id. If $subtree_id is empty, the whole area is searched for
     * a default page.
     *
     * @param int $area_id a valid indicator of the area to look at
     * @param int|null $subtree_id the id of the starting node of the subtree to search OR null if searching whole area
     * @param array $node_cache contains cached records, used to identify circular reference
     * @return bool|int FALSE when no node found, the id of the node otherwise
     * @uses MAXIMUM_ITERATIONS limits the number of levels to try to a sensibele maximum (no endless loops)
     */
    function calculate_default_descendant_node_id($area_id,$subtree_id,&$node_cache) {
        global $CFG,$DB;
        $datim = db_escape_and_quote(strftime('%Y-%m-%d %T')); // current date/time between quotes, ready for SQL-use
        $local_node_cache = array(); // used to detect circular references AND to cache node records

        if (empty($subtree_id)) {
            $node_id_clause = 'n.parent_id = n.node_id'; // start with criterion for a top level node within an area
        } else {
            $node_id_clause = 'n.parent_id = '.intval($subtree_id); // start with criterion for requested subtree
        }

        for ($i = 0; $i < MAXIMUM_ITERATIONS; ++$i) {
            $sql = 'SELECT n.* '.
                   'FROM '.$this->table_nodes_prefix.' AS n INNER JOIN '.$this->table_areas_prefix.' AS a '.
                         'ON n.area_id = a.area_id '.
                   'WHERE '.$node_id_clause.' AND (n.embargo <= '.$datim.') AND ('.$datim.' <= n.expiry) AND '.
                          '(a.area_id = '.$area_id.') AND (a.is_active) '.
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
            $node_id = intval($record['node_id']);
            if (isset($local_node_cache[$node_id])) { // Deja vu! This circular reference shouldn't happen
                logger(__FILE__.'('.__LINE__.'): circular reference in area '.$area_id.' node '.$node_id,LOG_DEBUG);
                return FALSE;
            } else {
                $local_node_cache[$node_id] = $record; // cache this node record (for detecting circular reference)
            }
            if (db_bool_is(TRUE,$record['is_page'])) { // found (default) page in 0 or more (default) sections
                $node_cache += $local_node_cache; // add records we retrieved to caller's cache, we're done here now
                return $node_id;
            } else { // no joy yet, continue the search in this section, look for (default) page/section among childeren
                $node_id_clause = 'n.parent_id = '.$node_id;
            }
        }
        logger(__FILE__.'('.__LINE__.'): too many iterations (endless loop) in area '.$area_id,LOG_DEBUG);
        return FALSE; // if we arrive here, we have spent MAXIMUM_ITERATIONS without conclusive answer, ergo: not found
    } // calculate_default_descendant_node_id()


    /** get an array of all available node records in the selected area as assoc arrays
     *
     * This yields an array of all available (i.e. visible and hidden) nodes
     * in the area. The expired nodes and nodes under embargo are not retrieved;
     * these are considered non-existing.
     *
     * The array with nodes is ordered by parent_id and sort_order and keyed with
     * the node_id. This sort order helps to create an ordered list of nodes per level.
     *
     * @param int $area_id the area for which all nodes are to be retrieved
     * @result mixed FALSE on error or an ordered array with nodes
     */
    function retrieve_nodes_from_database($area_id) {
        $datim = db_escape_and_quote(strftime('%Y-%m-%d %T')); // current date/time between quotes, ready for SQL-use
        $where = '(embargo <= '.$datim.') AND ('.$datim.' <= expiry) AND '.'(area_id = '.intval($area_id).')';
        $order = array('CASE WHEN (parent_id = node_id) THEN 0 ELSE parent_id END', 'sort_order','node_id');
        return db_select_all_records('nodes','*',$where,$order,'node_id');
    }


    /** construct a complete tree from node records (including 'hidden' nodes)
     *
     * This iterates through all node records $records and constructs a tree.
     * Nodes/sections without childeren are automatically set to 'hidden'.
     * If all childeren of a node/section are hidden, that node/section is also hidden.
     *
     * The resulting tree is actually an array keyed with the node_id (and with an
     * additional 'node' 0, see notes below). The order of the array is not relevant
     * because any node can be accessed directly via the key. However, the order is
     * the same as the order of the incoming $records array but with 'node' 0
     * prepended.
     *
     * Note 1
     * It is essential that the incoming array $record is properly ordered, i.e.
     * all nodes first grouped by parent_id and after that sorted by sort_order. This
     * way it is possible to easily construct a linked list of siblings in the correct
     * sort order.
     *
     * Note 2
     * The root node of the whole tree has node_id 0. The value 0 is also used to
     * indicate the end of a linked list. Note that there is no node with node_id 0
     * in the database; the first node has node_id = 1 so there are no conflicts here.
     *
     * Note 3
     * The root node in the tree structure has node_id 0. In order to have the top level
     * nodes (ie. nodes directly under the root node) have their parent_id's set to 0
     * instead of to their own node_id. The reason to use parent_id = node_id is that
     * referential integrity requires that a the parent_id field should have a valid
     * node_id and 0 is not a valid node_id in the database.
     *
     * @param array $records pointer to ordered array of node records (pointer to save stack space)
     * @return bool TRUE on success, or FALSE on failure
     */
    function build_tree_of_nodes(&$records) {

        // Start with 'special' node 0 is root of the tree

        $tree = array(0 => array(
          'node_id' => 0,
          'parent_id' => 0,
          'prev_sibling_id' => 0,
          'next_sibling_id' => 0,
          'first_child_id' => 0,
          'is_hidden' => FALSE,
          'is_page' => FALSE)
          );

        // step through all node records and copy the relevant fields

        foreach($records as $record) {
            $node_id = intval($record['node_id']);
            $parent_id = intval($record['parent_id']);
            $is_hidden = db_bool_is(TRUE,$record['is_hidden']);
            $is_page = db_bool_is(TRUE,$record['is_page']);
            if ($parent_id == $node_id) { // top level
                $parent_id = 0;
            }
            $tree[$node_id] = array(
                'node_id' => $node_id,
                'parent_id' => $parent_id,
                'prev_sibling_id' => 0,
                'next_sibling_id' => 0,
                'first_child_id' => 0,
                'is_hidden' => $is_hidden,
                'is_page' => $is_page);
        }

        // step through all collected records and add links to childeren and siblings

        $prev_node_id = 0;
        foreach ($tree as $node_id => $node) {
            $parent_id = $node['parent_id'];
            if ($parent_id == $tree[$prev_node_id]['parent_id']) {
                $tree[$prev_node_id]['next_sibling_id'] = $node_id;
                $tree[$node_id]['prev_sibling_id'] = $prev_node_id;
            } else {
                $tree[$parent_id]['first_child_id'] = $node_id;
            }
            $prev_node_id = $node_id;
        }

        // node 0 is a special case, the top level nodes are in fact childeren, not siblings

        $tree[0]['first_child_id'] = $tree[0]['next_sibling_id'];
        $tree[0]['next_sibling_id'] = 0;

        // finally adjust the 'hidden' properties for sections without unhidden childeren

        $tree[0]['is_hidden'] |= $this->subtree_is_hidden(0,$tree);

        // done!

        return $tree;
    } // build_tree_of_nodes()


    /** recursively determine whether all childeren of a node are hidden
     *
     * This walks the subtree starting at $node_id and returns TRUE if
     * all childeren of this node are hidden or FALSE if at least 1 is
     * not hidden. As a side-effect, a section/node is made hidden if all childeren
     * are hidden, i.e. the results are recorded in the nodes in the $tree.
     *
     * This recursive routine can be called at most MAXIMUM_ITERATIONS times,
     * preventing endless loops. When this limit is reached, a message is logged
     * (but only once).
     *
     * Note that the results of subtrees are OR'ed with the is_hidden property
     * of the current node. This means that IF a subtree has all nodes hidden,
     * the node will be made hidden too. Hoever, if a subtree has at least one
     * non-hidden node, the node will not be forced to be unhidden: if it was
     * already hidden it stays that way.
     *
     * @param int $node_id start of the subtree
     * @param array $tree a pointer to the tree (pointer to save stack space)
     * @return bool TRUE if subtree is hidden, FALSE otherwise
     */
    function subtree_is_hidden($node_id,&$tree) {
        static $level = 0;
        static $loop_reported = FALSE;
        $is_hidden = TRUE; // assume the worst

        ++$level;
        if ($level > MAXIMUM_ITERATIONS) {
            if (!$loop_reported) {
                logger(__FILE__.'('.__LINE__.'): too many iterations (endless loop) in subtree '.$node_id);
                $loop_reported = TRUE;
            }
        } elseif ($tree[$node_id]['is_page']) {
            $is_hidden = $tree[$node_id]['is_hidden'];
        } else {
            $next_id = $tree[$node_id]['first_child_id'];
            // note: a section without childeren never enters this while loop so will automatically be hidden
            while ($next_id != 0) {
                $is_hidden &= $this->subtree_is_hidden($next_id,$tree);
                $next_id = $tree[$next_id]['next_sibling_id'];
            }
            $tree[$node_id]['is_hidden'] |= $is_hidden;
        }
        --$level;
        return $is_hidden;
    } // subtree_is_hidden()







} // Area


?>