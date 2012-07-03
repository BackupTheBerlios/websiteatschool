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
 * @version $Id: aggregator_view.php,v 1.2 2012/07/03 20:34:35 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** display the aggregated information from the nodes linked to this aggregator
 *
 * his routine grabs information from other, existing pages, and
 * partially shows the this information followed by a 'read more...' link
 * to the corresponding full page. Two types of pages are currently
 * recognised:
 * 
 *  - htmlpage: a configurable # of paragraphs is displayed
 *  - snapshots: all images are rotated, with configurable number, time,
 *    dimensions  
 * 
 * The following showstoppers are taken into account.
 * 
 *  - a page must not be expired or under embargo
 *  - the corresponding area must be accessible to the user
 *  - database errors yield an empty list of aggregated pages
 *  - the number of aggregated pages is limited via a config option
 * 
 * Specifiying a section number rather than a page number is interpreted
 * as specifying the individual pages within that section (the above
 * showstoppers do apply, however). The combination of a limited
 * number of pages AND the ability to specify a section id as
 * shorthand for a collection of pages makes it easy to keep the
 * aggregator page up-to-date with a list of the N latest pages
 * within a section.
 *
 * Note about the styling of aggregated nodes
 *
 * The aggregator can be completely styled using a mix of id's and
 * classes. Here is a rough sketch, assuming an aggregator with
 * a non-empty header, non-empty introduction, a single snapshots
 * node and two htmlpage modules. This all takes place within the
 * #content div. Note that the various id's are numbered sequential
 * within the page.
 * 
 * <code>
 * h2 .aggregator_header
 * div .aggregator_introduction
 * div .aggregator_snapshots_outer #aggregator_outer_1
 *   h3 .aggregator_snapshots_header # aggregator_header_1
 *   div .aggregator_snapshots_inner #aggregator_inner_1
 *   div .aggregator_snapshots_more #aggregator_more_1
 *   div (clear:both)
 * div .aggregator_htmlpage_outer #aggregator_outer_2
 *   h3 .aggregator_htmlpage_header # aggregator_header_2
 *   div .aggregator_htmlpage_inner #aggregator_inner_2
 *   div .aggregator_htmlpage_more #aggregator_more_2
 *   div (clear:both)
 * div .aggregator_htmlpage_outer #aggregator_outer_3
 *   h3 .aggregator_htmlpage_header # aggregator_header_3
 *   div .aggregator_htmlpage_inner #aggregator_inner_3
 *   div .aggregator_htmlpage_more #aggregator_more_3
 *   div (clear:both)
 * ...
 * </code>
 *
 * @param object &$theme collects the (html) output
 * @param int $area_id identifies the area where $node_id lives
 * @param int $node_id the node to which this module is connected
 * @param array $module the module record straight from the database
 * @return bool TRUE on success + output via $theme, FALSE otherwise
 * @todo what to do with htmlpages containing h1's and h2's? Get rid
 *       of those? Mmmm....
 */
function aggregator_view(&$theme,$area_id,$node_id,$module) {
    global $USER,$CFG;

    // 1 -- retrieve configuration data
    $config = aggregator_view_get_config($node_id);

    // 2 -- start off with the header + introduction
    if (!empty($config['header'])) {
        $theme->add_content('<h2 class="aggregator_header">'.$config['header'].'</h2>');
        }
    if (!empty($config['introduction'])) {
        $theme->add_content('<div class="aggregator_introduction">');
        $theme->add_content($config['introduction']);
        $theme->add_content('</div>');
        }
    // 3 -- prepare for lookup of supported modules
    $modules = aggregator_view_get_modules();
    if (empty($modules)) {
        return FALSE; // shouldn't happen
    }

    // 4 -- expand the list of nodes
    $nodes = aggregator_view_get_nodes($config, $theme->tree, $modules);

    // 5 -- show at most N nodes in the specified order
    $counter = 0;
    foreach($nodes as $id => $node) {
        if (++$counter > $config['items']) {
            break;
        }
        $module_id = $node['module_id'];
        switch($modules[$module_id]['name']) {
        case 'htmlpage':
            aggregator_view_htmlpage($counter,$theme,$node,$config);
            break;
        case 'snapshots':
            aggregator_view_snapshots($counter,$theme,$node,$config,$modules);
            break;
        }
    }

    // 6 -- all done
    return TRUE;
} // aggregator_view()

/** retrieve the configuration information for this aggregator
 *
 * @param int $node_id identifies the aggregator page
 * @return array configuration record from db OR a handcrafted set of defaults
 */
function aggregator_view_get_config($node_id) {
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
    if (($record = db_select_single_record($table,$fields,$where)) === FALSE) {
        logger(sprintf('%s(): error retrieving configuration: %s',__FUNCTION__,db_errormessage()));
        $record = array(
            'header'             => '',
            'introduction'       => '',
            'node_list'          => '',
            'items'              => 10,
            'reverse_order'      => FALSE,
            'htmlpage_length'    => 2,
            'snapshots_width'    => 512,
            'snapshots_height'   => 120,
            'snapshots_visible'  => 3,
            'snapshots_showtime' => 5);
    } else {
        $record['header']             = trim($record['header']);
        $record['introduction']       = trim($record['introduction']);
        $record['node_list']          = trim($record['node_list']);
        $record['items']              = intval($record['items']);
        $record['reverse_order']      = db_bool_is(FALSE,$record['reverse_order']) ? FALSE : TRUE;
        $record['htmlpage_length']    = intval($record['htmlpage_length']);
        $record['snapshots_width']    = intval($record['snapshots_width']);
        $record['snapshots_height']   = intval($record['snapshots_height']);
        $record['snapshots_visible']  = intval($record['snapshots_visible']);
        $record['snapshots_showtime'] = intval($record['snapshots_showtime']);
    }
    return $record;
} // aggregator_view_get_config()


/** retrieve a list of modules suitable for aggregation keyed by module_id
 *
 * this selectively retrieves the module records for the modules we
 * support. The information is used to determine which nodes to
 * process and we also need a module record for the inline slideshow.
 *
 * @return array contains selected module records or empty array on db error
 */
function aggregator_view_get_modules() {
    $table = 'modules';
    $fields = '*';
    $where = "name = 'htmlpage' OR name = 'snapshots'";
    $order = '';
    $keyfield = 'module_id';
    $modules = db_select_all_records($table,$fields,$where,$order,$keyfield);
    if ($modules === FALSE) {
        logger(sprintf('%s: no modules found: %s',__FUNCTION__,db_errormessage()));
        $modules = array();
    }
    return $modules;
} // aggregator_view_get_modules()


/** construct an array with the node records to aggregate
 *
 * this routine converts the comma delimited list of node numbers
 * to an array of node records, ready for further processing.
 *
 * @param array &$config points to the aggregator configuration
 * @param array &$tree points to the (cached) tree of the current area
 * @param array &$modules points to array with supported modules
 * @return array ordered list of nodes to aggregate (could be empty)
 */
function aggregator_view_get_nodes(&$config, &$tree, &$modules) {
    $all_nodes = array();
    $node_ids = explode(',', $config['node_list']);
    foreach($node_ids as $node_id) {
        if (isset($tree[$node_id])) {
            $nodes = aggregator_view_get_node_from_tree($node_id,$config,$tree,$modules);
        } else {
            $nodes = aggregator_view_get_node_from_db($node_id,$config,$modules);
        }
        $all_nodes = array_merge($all_nodes,$nodes);
        if (sizeof($all_nodes) >= $config['items']) {
            return $all_nodes;
        }
    }
    return $all_nodes;
} // aggregator_view_get_nodes


/** construct an array with node records using cached tree in current area
 *
 * this routine constructs a list of 0, 1 or more node records based
 * on the $node_id provided by the caller. The node records are
 * retrieved from the cached tree in &$tree (from $theme->tree).
 *
 * This routine takes care of the showstoppers like embargo and
 * expiry but not the area because we already have access to this
 * area otherwise we would not be here in the aggregator module in
 * this area.
 *
 * @param int $node_id identifies page or section to evaluate
 * @param array &$config points to the aggregator configuration
 * @param array &$tree points to the (cached) tree of the current area
 * @param array &$modules points to array with supported modules
 * @return array ordered list of nodes to aggregate (could be empty)
 */
function aggregator_view_get_node_from_tree($node_id,&$config,&$tree,&$modules) {
    $nodes = array();
    $now = strftime("%Y-%m-%d %T");

    // don't show expired nodes or nodes under embargo
    if (($now < $tree[$node_id]['record']['embargo']) ||
        ($tree[$node_id]['record']['expiry'] < $now)) {
        return $nodes;
    }

    if ($tree[$node_id]['is_page']) {
        $module_id = $tree[$node_id]['record']['module_id'];
        if (isset($modules[$module_id])) {
            $nodes[] = $tree[$node_id]['record'];
        }
    } else {
        $next_id = $tree[$node_id]['first_child_id'];
        for ( ; ($next_id != 0); $next_id = $tree[$next_id]['next_sibling_id']) {
            if (($now < $tree[$next_id]['record']['embargo']) ||
                ($tree[$next_id]['record']['expiry'] < $now) ||
                (!$tree[$next_id]['is_page'])) {
                continue;
            }
            $module_id = $tree[$next_id]['record']['module_id'];
            if (isset($modules[$module_id])) {
                $nodes[] = $tree[$next_id]['record'];
            }
        }
    }

    // maybe reverse the result
    if ((sizeof($nodes) > 1) && ($config['reverse_order'])) {
        $nodes = array_reverse($nodes);
    }
    // all done
    return $nodes;
} // aggregator_view_get_node_from_tree()


/** retrieve an array with node records straight from the database
 *
 * this routine constructs a list of 0, 1 or more node records based
 * on the $node_id provided by the caller. The node records are
 * retrieved from the database.
 *
 * This routine takes care of the showstoppers like embargo and
 * expiry and also access permissions to the area. We can not
 * be sure if the user actually has access to a page until we
 * are have checked to area in which the node $node_id resides.
 * This is an extra test compared to 
 * {@link aggregator_view_get_node_from_tree()} above.
 *
 * @param int $node_id identifies page or section to evaluate
 * @param array &$config points to the aggregator configuration
 * @param array &$modules points to array with supported modules
 * @return array ordered list of nodes to aggregate (could be empty)
 */
function aggregator_view_get_node_from_db($node_id,&$config,&$modules) {
    global $USER;
    $nodes = array();
    $table = 'nodes';
    $fields = '*';
    $order = ($config['reverse_order']) ? 'sort_order DESC' : 'sort_order';
    $where = array('node_id' => intval($node_id));
    if (($record = db_select_single_record($table,$fields,$where)) === FALSE) {
        logger(sprintf('%s(): error retrieving node record: %s',__FUNCTION__,db_errormessage()));
        return $nodes;
    }
    $now = strftime("%Y-%m-%d %T");

    // don't show expired nodes or nodes under embargo
    if (($now < $record['embargo']) || ($record['expiry'] < $now)) {
        return $nodes;
    }

    // don't show private or inactive areas to random strangers
    $areas = get_area_records();
    $area_id = intval($record['area_id']);
    if ((db_bool_is(FALSE,$areas[$area_id]['is_active'])) ||
       ((db_bool_is(TRUE,$areas[$area_id]['is_private'])) &&
          (!$USER->has_intranet_permissions(ACL_ROLE_INTRANET_ACCESS,$area_id)))) {
        return $nodes;
    }

    // if it was but a plain page we're done (even if not htmlpage or snapshots)
    if (db_bool_is(TRUE,$record['is_page'])) {
        $module_id = intval($record['module_id']);
        if (isset($modules[$module_id])) {
            $nodes[] = $record;
        }
        return $nodes;
    }

    // mmm, must have been a section (but at least in the correct area)
    // go get the pages in this section in this area
    $where = array('parent_id' => $node_id, 'area_id' => $area_id, 'is_page' => TRUE);
    if (($records = db_select_all_records($table,$fields,$where,$order)) === FALSE) {
        logger(sprintf('%s(): error retrieving node records: %s',__FUNCTION__,db_errormessage()));
        return $nodes;
    }
    $counter = 0;
    foreach($records as $record) {
        // don't show expired nodes or nodes under embargo
        if (($now < $record['embargo']) || ($record['expiry'] < $now)) {
            continue;
        }
        $module_id = intval($record['module_id']);
        if (isset($modules[$module_id])) {
            $nodes[] = $record;
            if (++$counter >= $config['items']) {
                break;
            }
        }
    }
    return $nodes;
} // aggregator_view_get_node_from_db()


/** construct a title, summary and readmore prompt for an htmlpage page
 *
 * this routine uses a heuristic approach to snip N paragraphs from
 * the actual text in the html-page. Because we cannot be sure that
 * stripos() is available we resort to first changing any '<P' to
 * '<p ' and subsequently searching the string until the N+1'th '<p '.
 * The offset we calculate this way should contain exactly N
 * paragraphs. Obviously this assumes (dangerous...) that the htmlpage
 * page_data actually contains paragraphs. However, if not enough '<p
 * strings are found, the complete page is used. Heuristics...
 *
 * @param int $counter is a sequential number identifying the aggregated nodes
 * @param object &$theme points to theme where the output goes
 * @param array &$node points to the node record of this htmlpage
 * @param array &$config points to the aggregator configuration
 * @return array ordered list of nodes to aggregate (could be empty)
 */ 
function aggregator_view_htmlpage($counter,&$theme, &$node, &$config) {
    $id = strval($counter); // used to make all id's within this item unique

    // 1 -- outer div holds title+blurb+readmore...
    $attributes = array('class' => 'aggregator_htmlpage_outer','id' => 'aggregator_outer_'.$id);
    $theme->add_content(html_tag('div',$attributes));

    // 2A -- title
    $attributes = array('class' => 'aggregator_htmlpage_header','id' => 'aggregator_header_'.$id);
    $theme->add_content('  '.html_tag('h3',$attributes,$node['title']));

    // 2B -- blurb (enclosed in inner div)
    $attributes = array('class' => 'aggregator_htmlpage_inner','id' => 'aggregator_inner_'.$id);
    $theme->add_content('  '.html_tag('div',$attributes));

    // fetch actual content from database (requires knowledge of internals of htmlpage module/table)
    $table = 'htmlpages';
    $node_id = intval($node['node_id']);
    $where = array('node_id' => $node_id);
    $fields = array('page_data');
    if (($record = db_select_single_record($table,$fields,$where)) === FALSE) {
        logger(sprintf('%s: no pagedata (node=%d): %s',__FUNCTION__,$node_id,db_errormessage()));
        $pagedata = '';
    } else {
        // make SURE we only have lowercase <p followed by a space in the text (easy strpos'ing)
        $pattern = '/(<[pP])([^a-zA-Z0-9])/';
        $replace = '<p $2';
        $pagedata = preg_replace($pattern,$replace,$record['page_data']);
    }
    $offset = -1;
    $limit = $config['htmlpage_length'];
    for ($i=0; ($i <= $limit); ++$i) {
        if (($offset = strpos($pagedata,'<p ',$offset+1)) === FALSE) {
            break;
        }
    }
    if ($offset === FALSE) { // not enough '<p ' seen => show everything
        $theme->add_content($pagedata."\n");
    } else {
        $theme->add_content(substr($pagedata,0,$offset)."\n");
    }
    $theme->add_content('  </div>');

    // 2C -- readmore prompt
    $anchor = t('htmlpage_more','m_aggregator');
    $title = t('htmlpage_more_title','m_aggregator');
    $attr = array('title' => $title);
    $href = was_node_url($node,NULL,'',$theme->preview_mode);

    $attributes = array('class' => 'aggregator_htmlpage_more','id' => 'aggregator_more_'.$id);
    $theme->add_content('  '.html_tag('div',$attributes,html_a($href,NULL,$attr,$anchor)));
    $theme->add_content('  <div style="clear:both;"></div>');

    // 3 -- close outer div
    $theme->add_content('</div>'); 
} // aggregator_view_htmlpage()


/** construct a title, inline slideshow and readmore prompt for a snapshots page
 *
 * this routine uses the SnapshotViewerInline class to generate a
 * rotating inline slideshow. This leans very heavily on JavaScript.
 * If JavaScript is not enabled, that class has a fall-back showing
 * the first N images statically. Graceful degradation...
 *
 * @param int $counter is a sequential number identifying the aggregated nodes
 * @param object &$theme points to theme where the output goes
 * @param array &$node points to the node record of this htmlpage
 * @param array &$config points to the aggregator configuration
 * @param arrat &$modules points to list of records of supported modules
 * @return array ordered list of nodes to aggregate (could be empty)
 */ 
function aggregator_view_snapshots($counter, &$theme,&$node,&$config,&$modules) {
    global $CFG;
    $id = strval($counter); // used to make all id's within this item unique

    // 1 -- outer div holds title+slideshow+readmore...
    $attributes = array('class' => 'aggregator_snapshots_outer','id' => 'aggregator_outer_'.$id);
    $theme->add_content(html_tag('div',$attributes));

    // 2A -- title
    $attributes = array('class' => 'aggregator_snapshots_header','id' => 'aggregator_header_'.$id);
    $theme->add_content('  '.html_tag('h3',$attributes,$node['title']));

    // 2B -- slideshow (enclosed in inner div)
    $attributes = array('class' => 'aggregator_snapshots_inner','id' => 'aggregator_inner_'.$id);
    $theme->add_content('  '.html_tag('div',$attributes));

    include_once($CFG->progdir.'/modules/snapshots/snapshots_view.php');
    $snap = new SnapshotViewerInline($theme,
                intval($node['area_id']),
                intval($node['node_id']),
                $modules[intval($node['module_id'])],
                $config['snapshots_width'],
                $config['snapshots_height'],
                $config['snapshots_visible']);
    $snap->default_showtime = $config['snapshots_showtime'];
    $snap->run();
    unset($snap);
    $theme->add_content('  </div>');

    // 2C -- readmore prompt
    $anchor = t('snapshots_more','m_aggregator');
    $title = t('snapshots_more_title','m_aggregator');
    $attr = array('title' => $title);
    $href = was_node_url($node,NULL,'',$theme->preview_mode);

    $attributes = array('class' => 'aggregator_snapshots_more','id' => 'aggregator_more_'.$id);
    $theme->add_content('  '.html_tag('div',$attributes,html_a($href,NULL,$attr,$anchor)));
    $theme->add_content('  <div style="clear:both;"></div>');

    // 3 -- close outer div
    $theme->add_content('</div>');
} // aggregator_view_snapshots()

?>