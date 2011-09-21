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

/** /program/main_index.php - workhorse for visitor interface
 *
 * This file deals with the visitor interface.
 * It is included and called from /index.php.
 *
 * The work is done in {@link main_index()}.
 *
 * Parameters are passed like index.php?parm=val. Here is an overview of
 * recognised global parameters that are handled here (and not in a module).
 *
 *  - logout - used to end a user's session (no need for a value, just the param is enough)
 *  - login=i - step i of the login procedure
 *  - area=a - indicates which area to access; if specified it should match the node, if that is specified
 *  - node=n - indicates which node to access; if specified it should match the area, if that is specified
 *  - language=xx - indicates the language to use; xx is a valid language code like 'en', 'de', 'fr' or 'nl'
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: main_index.php,v 1.5 2011/09/21 18:54:19 pfokker Exp $
 * @todo add the performance results in a HTML-comment if not CFG->debug, in sight otherwise
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** main program for visitors
 *
 * this routine is called from /index.php. It is the main program for visitors.
 *
 * @return void page sent to the browser
 * @todo cleanup login/logout-code
 */
function main_index() {
    global $USER;
    global $CFG;
    global $LANGUAGE;

    /** initialise the program, setup database, read configuration, etc. */
    require_once($CFG->progdir.'/init.php');
    initialise();
    was_version_check(); // this never returns if versions don't match

    // TODO: cleanup like in main_admin()
    // handle login/logout/continuation so we quickly find out which user is calling
    if (isset($_GET['logout'])) {
        /** loginlib.php contains both login- and logout-routines */
        require_once($CFG->progdir.'/lib/loginlib.php');
        was_logout(); // may or may not return here
    } elseif (isset($_GET['login'])) {
        /** loginlib.php contains both login- and logout-routines */
        require_once($CFG->progdir.'/lib/loginlib.php');
        was_login(magic_unquote($_GET['login'])); // may or may not return here
    } elseif (isset($_COOKIE[$CFG->session_name])) {
        /** dbsessionlib.php contains our own database based session handler */
        require_once($CFG->progdir.'/lib/dbsessionlib.php');
        dbsession_setup($CFG->session_name);
        if (dbsession_exists(magic_unquote($_COOKIE[$CFG->session_name]))) {
            session_start();
        }
    }

    // At this point we either have a valid session with a logged-in user
    // (indicated via existence of $_SESSION) or we are dealing with an anonymous
    // visitor with non-existing $_SESSION. Keep track of the number of calls
    // this user makes (may be logged lateron on logout).

    if (isset($_SESSION)) {
        if (!isset($_SESSION['session_counter'])) { // first time after login, record start time of session
            $_SESSION['session_counter'] = 1;
            $_SESSION['session_start'] = strftime("%Y-%m-%d %T");
        } else {
            $_SESSION['session_counter']++;
        }
    }

    // Now is the time to create a USER object, even when the visitor is just a passerby
    // because we can then determine easily if a visitor is allowed certain things, e.g.
    // view a protected area or something

    /** useraccount.class.php is used to define the USER object */
    require_once($CFG->progdir.'/lib/useraccount.class.php');

    if ((isset($_SESSION)) && (isset($_SESSION['user_id']))) {
        $USER = new Useraccount($_SESSION['user_id']);
        $USER->is_logged_in = TRUE;
        $_SESSION['language_key'] = $LANGUAGE->get_current_language(); // remember language set via _GET or otherwise
    } else {
        $USER = new Useraccount();
        $USER->is_logged_in = FALSE;
    }

    // Check for the special preview-mode
    // This allows a webmaster to preview a page in the correct environment (theme)
    // even when the page is under embargo. Note that the node_id and area_id are
    // retrieved from the session; the user only has a cryptic preview-code.
    // See pagemanagerlib.php for more information (function task_page_preview()).

    $in_preview_mode = FALSE;
    if ($USER->is_logged_in) {
        $preview_code_from_url = get_parameter_string('preview');
        if ((!is_null($preview_code_from_url)) && 
            (isset($_SESSION['preview_salt'])) && 
            (isset($_SESSION['preview_node']))) {
            $hash = md5($_SESSION['preview_salt'].$_SESSION['preview_node']);
            if ($hash === $preview_code_from_url) {
                $node_id = intval($_SESSION['preview_node']);
                $area_id = intval($_SESSION['preview_area']);
                $area = db_select_single_record('areas','*',array('area_id' => $area_id));
                if ($area === FALSE) {
                    logger("Fatal error 070: cannot preview node '$node_id' in area '$area_id'");
                    error_exit('070');
                } else {
                    $tree = tree_build($area_id);
                    $in_preview_mode = TRUE;
                }
            }
        }
    }

    if ($in_preview_mode == FALSE) {
        $requested_area = get_requested_area();
        $requested_node = get_requested_node();
        $req_area_str = (is_null($requested_area)) ? "NULL" : strval($requested_area);
        $req_node_str = (is_null($requested_node)) ? "NULL" : strval($requested_node);

        if (($area = calculate_area($requested_area,$requested_node)) === FALSE) {
            logger("Fatal error 080: no valid area (request: area='$req_area_str', node='$req_node_str')");
            error_exit('080'); // no such area
        }
        $area_id = intval($area['area_id']);

        // If $USER has no permission to view area $area_id, we simply bail out.
        // Rationale: if the user is genuine, she knows about logging in first.
        // If the user is NOT logged in and tries to view a protected area, I'd consider
        // it malicious, and in that case I won't even confirm the existence of
        // the requested area. (If a cracker simply tries areas 0,1,.. and sometimes is greeted
        // with 'please enter credentials' and sometimes with 'area does not exist', this
        // provides information to the cracker. I don't want that). Note that the error code
        // is the same as the one for non-existing area. In other words: for an unauthorised
        // visitor an existing private area is just as non-existent as a non-existing public area.
        if ((db_bool_is(TRUE,$area['is_private'])) &&
            (!$USER->has_intranet_permissions(ACL_ROLE_INTRANET_ACCESS,$area_id))) {
            logger(sprintf("Fatal error 080: no view permissions for area '%d' (request: area='%s', node='%s')",
                            $area_id,$req_area_str,$req_node_str));
            error_exit('080'); // no such area
        }
        // still here?
        // then we've got a valid $area_id and corresponding $area record.
        // now we need to figure out which $node_id to use

        $tree = tree_build($area_id);
        if (($node_id = calculate_node_id($tree,$area_id,$requested_node)) === FALSE) {
            logger(sprintf("Fatal error 080: no valid node within area '%d' (request: area='%s', node='%s')",
                           $area_id,$req_area_str,$req_node_str));
            error_exit('080'); // no such area
        }
    }

    // At this point we have the following in our hands
    // - a valid $area_id
    // - a valid $node_id
    // - the complete tree from area $area_id in $tree
    // - the area record from database in $area
    // - the node record from database in $tree[$node_id]['record']
    // - a flag that signals preview mode in $in_preview_mode

    /** themelib contains the theme factory */
    require_once($CFG->progdir.'/lib/themelib.php');

    // And now we know about the $area, we can carry on determining which $theme to use.
    //
    $theme = theme_factory($area['theme_id'],$area_id,$node_id);
    if ($theme === FALSE) {
        logger("Fatal error 090: cannot setup theme '{$area['theme_id']}' in area '$area_id'");
        error_exit('090');
    }

    // Tell the theme about the preview mode
    $theme->set_preview_mode($in_preview_mode);

    // Now all we need to do is let the module connected to node $node_id generate output
    $module_id = $tree[$node_id]['record']['module_id'];
    module_view($theme,$area_id,$node_id,$module_id);

    // Remember this visitor
    update_statistics($node_id);

    // Finally, send output to user
    $theme->send_output();

    if (isset($_SESSION)) {
        session_write_close();
    }

    // done!
    exit;
} // main_index();


/** try to retrieve a valid area record based on values of requested area and requested node
 *
 * this determines which area to use. If the user specifies nothing (no area, no node), we
 * simply go for the default area or the first available area. If the user does specify an
 * area and/or a node, we use that information to get to the area. Note that if the user specifies
 * both area and node, the two should match. That is: you cannot specify a node from area X
 * and also request area Y: that yields no results. If only a node is specified, the area is
 * calculated from the area to which the node belongs.
 *
 * We let the database do most of the work by constructing and executing an appropriate SQL-statement.
 *
 * @param int|null $requested_area the area the user specified or NULL if no area specifically requested
 * @param int|null $requested_node the node the user specified or NULL if no node specifically requested
 * @return bool|array FALSE on error/not found, otherwise an array with a complete area record from database
 * @uses $DB
 */
function calculate_area($requested_area,$requested_node) {
    global $DB;
    $tbl_areas = $DB->prefix.'areas';
    $tbl_nodes = $DB->prefix.'nodes';
    if (is_null($requested_node)) {
        if (is_null($requested_area)) {
            $where = array('is_active' => TRUE);
            $order = array('CASE WHEN (is_default = '.SQL_TRUE.') THEN 0 ELSE 1 END','sort_order');
            $sql = db_select_sql('areas','*',$where,$order);
        } else {
            $where = array('is_active' => TRUE,'area_id' => intval($requested_area));
            $sql = db_select_sql('areas','*',$where);
        }
    } else {
        $sql = "SELECT a.* FROM $tbl_areas AS a INNER JOIN $tbl_nodes AS n ON a.area_id = n.area_id ".
               "WHERE (a.is_active = ".SQL_TRUE.") AND (n.node_id = ".intval($requested_node).")";
        if (!is_null($requested_area)) {
            $sql .= " AND (a.area_id = ".intval($requested_area).")";
        }
    }
    if (($DBResult = $DB->query($sql,1)) === FALSE) {
        logger("DEBUG [$sql]: error {$DB->errno}/'{$DB->error}'",WLOG_DEBUG);
        return FALSE;
    } elseif ($DBResult->num_rows != 1) {
        $DBResult->close();
        return FALSE;
    } else {
        $record = $DBResult->fetch_row_assoc();
        $DBResult->close();
        return $record;
    }
} // calculate_area()


/** calculate and validate the node_id to display
 *
 * this tries to determine a valid node to display based on the node the user
 * requested and the area that the user may or may not have requested.
 *
 * Basic assumption is that the visitor has indeed view access to
 * area $area_id. This means that the user is allowed to see the nodes
 * in this area that are not under embargo (and not expired).
 * We do have a complete overview of all nodes in this area in the array $tree.
 * (See {@link tree_build()} for more information about the tree structure)
 *
 * The parameter $requested_node is either an integer, indicating the user
 * explicitly specified a node number in the page request, or null, indicating
 * that the user did not explicitly specify a node. In the latter case
 * the user may or may not have explicitly requested an area.
 *
 * There are several cases we need to handle
 * - if no node is explicitly requested, we need to identify the default page in the area
 * - if the node is under embargo the node does not exist (from the POV of the user)
 * - if the requested node is a section, we need to identify the default page in that section
 *
 * @param array &$tree a reference to the complete tree in area $area_id
 * @param int $area_id the area where we are looking for a node
 * @param int|null $requested_node the node_id the user requested or NULL if none was specified
 * @return bool|int FALSE if no suitable node found or a valid $node_id
 */
function calculate_node_id(&$tree,$area_id,$requested_node) {
    if (is_null($requested_node)) {
        return calculate_default_page($tree,$tree[0]['first_child_id']);
    }

    $node_id = intval($requested_node);
    if (!isset($tree[$node_id])) {
        logger("calculate_node_id(): weird: node '$node_id' not set in tree for area '$area_id'",WLOG_DEBUG);
        return FALSE;
    }

    if ((is_under_embargo($tree,$node_id)) || is_expired($node_id,$tree)) {
        return FALSE;
    }

    return ($tree[$node_id]['is_page']) ? $node_id : calculate_default_page($tree,$tree[$node_id]['first_child_id']);
} // calculate_node_id()


/** try to find a default page within a subtree of pages and sections
 *
 * this walks the tree $tree starting at $subtree_id looking for a default page.
 * We give it three tries. First we look for a default node in the section
 * of which $subtree_id is the first node. If we find a page, we're done, if we
 * find a section we descend into that subsubtree. If there still is no default page,
 *  we go look for any page in the initial set of nodes. If that too doesn't yield
 * a page, we descend into the subsubtrees. If THAT doesn't yield a page we give up
 * and return FALSE, indicating no page to be found.
 *
 * @param array &$tree a reference to the complete tree in the area of interest
 * @param int $subtree_id the place where we need to start looking (usually the first_child_id of the parent)
 * @return bool|int FALSE if no page is to be found, the $node_id of the page otherwise
 */
function calculate_default_page(&$tree,$subtree_id) {
    $now = strftime("%Y-%m-%d %T");

    // 1 -- try to find a node with is_default flag set

    for ($next_id = $subtree_id; ($next_id != 0); $next_id = $tree[$next_id]['next_sibling_id']) {
        if ($tree[$next_id]['is_default']) {
            if ((!$tree[$next_id]['is_hidden']) &&
                ($tree[$next_id]['record']['embargo'] <= $now) && ($now <= $tree[$next_id]['record']['expiry'])) {
                if ($tree[$next_id]['is_page']) {
                    return $next_id;
                } else {
                    if (($node_id = calculate_default_page($tree,$tree[$next_id]['first_child_id'])) !== FALSE) {
                        return $node_id;
                    }
                }
            } // hidden/embargo/expiry
        } // is_default
    } // for

    // 2 -- no joy, now try the first unhidden page, any unhidden page, at this level
    for ($next_id = $subtree_id; ($next_id != 0); $next_id = $tree[$next_id]['next_sibling_id']) {
        if ($tree[$next_id]['is_page']) {
            if ((!$tree[$next_id]['is_hidden']) &&
                ($tree[$next_id]['record']['embargo'] <= $now) && ($now <= $tree[$next_id]['record']['expiry'])) {
                return $next_id;
            } //  hidden/embargo/expiry
        } // is_page
    } // for

    // 3 -- still no joy, now try to descend into subsections
    for ($next_id = $subtree_id; ($next_id != 0); $next_id = $tree[$next_id]['next_sibling_id']) {
        if ((!($tree[$next_id]['is_page'])) && (!($tree[$next_id]['is_default']))) {
            if ((!$tree[$next_id]['is_hidden']) &&
                ($tree[$next_id]['record']['embargo'] <= $now) && ($now <= $tree[$next_id]['record']['expiry'])) {
                if (($node_id = calculate_default_page($tree,$tree[$next_id]['first_child_id'])) !== FALSE) {
                    return $node_id;
                }
            } // hidden/embargo/expiry
        } // !is_page and !is_default
    } // for
    return FALSE;
} // calculate_default_page()


/** call the routine that generates the view (content) of module $module_id
 *
 * this loads the file containing the visitor interface for module $module_id
 * in core and subsequently calls the routine responsible for displaying the
 * content (function modulename_view()). The routine module_view() is supposed
 * to deposit any output into the $theme via the appropriate methods such
 * as $theme->add_content().
 *
 * @param array &$theme a reference to the output object
 * @param int $area_id the area where we are looking for a node
 * @param int $node_id the node we are working with
 * @param int $module_id the module connected to the node we are working with
 * @return bool FALSE on error, return value of modulename_view() otherwise
 */ 
function module_view(&$theme,$area_id,$node_id,$module_id) {
    $module = module_load_view($module_id);
    if ($module === FALSE) {
        $params = array('{MODULE}' => $module_id, '{NODE}' => $node_id);
        $msg = t('problem_with_module','',$params);
        $theme->add_content($msg);
        $theme->add_message($msg);
        $theme->add_popup_top($msg);
        $retval = FALSE;
    } else {
        $module_view = $module['name'].'_view';
        if (!function_exists($module_view)) {
            logger("module_view(): weird: function '$module_view' does not exist. Huh?");
            $params = array('{MODULE}' => $module_id, '{NODE}' => $node_id);
            $msg = t('problem_with_module','',$params);
            $theme->add_content($msg);
            $theme->add_message($msg);
            $theme->add_popup_top($msg);
            $retval = FALSE;
        } else {
            $retval = $module_view($theme,$area_id,$node_id,$module);
        }
    }
} // module_view()


/** load the visitor/view interface of a module in core
 *
 * this includes the 'view'-part of a module via 'require_once()'. This routine
 * first figures out if the view-script file actually exists before the
 * file is included. Also, we look at a very specific location, namely:
 * /program/modules/<modulename>/<module_view_script> where <modulename> is retrieved
 * from the modules table in the database.
 *
 * Note that if modulename would somehow be something like "../../../../../../etc/passwd\x00",
 * we could be in trouble...
 *
 * @param int $module_id indicates which module to load
 * @return bool|array FALSE on error or an array with the module record from the modules table
 * @todo should we sanitise the modulename here? It is not user input, but it comes from the modules
 *       table in the database. However, if a module name would contain sequences of "../" we might
 *       be in trouble
 */
function module_load_view($module_id) {
    global $CFG;
    $module_record = db_select_single_record('modules','*',array('module_id' => intval($module_id),'is_active' => TRUE));
    if ($module_record === FALSE) {
        logger("module_load_view(): weird: module '$module_id' is not there. Is it de-activated?");
        return FALSE;
    }
    $module_view_script = $CFG->progdir.'/modules/'.$module_record['name'].'/'.$module_record['view_script'];
    if (!file_exists($module_view_script)) {
        logger("module_load_view(): weird: file '$module_view_script' does not exist. Huh?");
        return FALSE;
    }
    require_once($module_view_script);
    return $module_record;
} // module_load_view()


/** update all statistics for the view of page $node_id
 *
 * this is a place for future extension. This routine is called once for every
 * page view. It can be used to record relevant data in a table, for future
 * reference, e.g. 
 *  - the IP-address of the visitor
 *  - the $node_id
 *  - the current date/time
 *  - the number of views of node $node_id from the visitor's IP-address 
 *  - etc. etc.
 *
 * Note that the table holding this information can quickly become very large.
 * That requires some form of logrotate or condensing the data. This feature
 * has yet to be developed.
 * 
 * @param int $node_id the page (node) that was viewed
 * @return void we assume that everything goes well and if it doesn't: too bad.
 * @todo maybe extend this routine to actually store more statistics information
 *       in a separate table
 */
function update_statistics($node_id) { 
    update_view_count($node_id);
    //
    // additional statistics code can go here
    //
} // update_statistics()


/** update the view count for page $node_id
 *
 * @param int $node_id the page (node) that need its view_count incremented with 1
 * @return void we assume that everything goes smooth, and if it doesn't: too bad.
 */
function update_view_count($node_id) {
    global $DB;
    $datim = db_escape_and_quote(strftime("%Y-%m-%d %T"));
    $sql = 'UPDATE '.$DB->prefix.'nodes '.
           'SET view_count = view_count + 1, atime = '.$datim.' '.
           'WHERE node_id = '.intval($node_id);
    return $DB->exec($sql);
} // update_view_count()

?>