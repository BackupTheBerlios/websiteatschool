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

/** /program/lib/pagemanager.class.php - pagemanager
 *
 * This file contains the Page Manager class, the core functionality of Website@School.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: pagemanager.class.php,v 1.13 2012/04/06 18:47:26 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/* This is the list of recognised tasks in Page Manager */
define('TASK_TREEVIEW','treeview');
define('TASK_TREEVIEW_SET','settreeview');
define('TASK_SUBTREE_EXPAND','expand');
define('TASK_SUBTREE_COLLAPSE','collapse');
define('TASK_SET_DEFAULT','setdefault');
define('TASK_ADD_PAGE','addpage');
define('TASK_ADD_SECTION','addsection');
define('TASK_NODE_DELETE','delete');
define('TASK_NODE_EDIT','edit');
define('TASK_NODE_EDIT_CONTENT','editcontent');
define('TASK_NODE_EDIT_ADVANCED','editadvanced');
define('TASK_PAGE_PREVIEW','preview');
define('TASK_SAVE_NODE','savenode');
define('TASK_SAVE_NEWPAGE','savenewpage');
define('TASK_SAVE_NEWSECTION','savenewsection');
define('TASK_SAVE_CONTENT','savecontent');

/* These constants are used with TASK_TREEVIEW_SET */
define('PARAM_TREEVIEW','treeview');
define('TREE_VIEW_MINIMAL',1);
define('TREE_VIEW_CUSTOM',2);
define('TREE_VIEW_MAXIMAL',3);

/* These constants help with interpretating the data in $_POST */
define('DIALOG_NODE_ADD',1);
define('DIALOG_NODE_EDIT',2);
define('DIALOG_NODE_EDIT_ADVANCED',3);
define('DIALOG_NODE_EDIT_CONTENT',4);
define('DIALOG_NODE_DELETE_CONFIRM',5);

/** Initial visibility of a new node: visible */
define('NODE_VISIBILIY_VISIBLE','1');
/** Initial visibility of a new node: hidden */
define('NODE_VISIBILIY_HIDDEN','2');
/** Initial visibility of a new node: under embargo */
define('NODE_VISIBILIY_EMBARGO','3');
/** Default initial visibility of a new node (see {@link get_dialogdef_add_node()}) */
define('NODE_VISIBILIY_DEFAULT',NODE_VISIBILIY_HIDDEN);
/** Default initial module of a new page (see {@link get_dialogdef_add_node()}) */
define('MODULE_NAME_DEFAULT','htmlpage');

/** Page Manager
 *
 * This class implements the Page Manager.
 *
 * All the work is directed from the constructor, so it is enough to
 * simply instantiate a new object and let the constructor do the work.
 * The only thing needed is an output object (see {@link AdminOutput}).
 */
class PageManager {
    /** @var object|null $output collects the html output */
    var $output = NULL;

    /** @var null|array $areas holds all area records (for future reference) or NULL if not yet set */
    var $areas = NULL;

    /** @var null|array $tree holds the complete tree for area $this->area_id or NULL if not yet set */
    var $tree = NULL;

    /** @var null|int $area_id indicates which tree is stored in $this-tree, or NULL if none yet */
    var $area_id = NULL;

    /** construct a PageManager object (called from /program/main_admin.php)
     *
     * This initialises the PageManager, checks user permissions and 
     * finally dispatches the tasks. If the specified task is not
     * recognised, the default task TASK_TREEVIEW is executed.
     *
     * Note that allmost all commands act on the area contained in the
     * SESSION-variable current_area_id. Also, we almost always need
     * the tree of nodes in that area, so we read it once, _before_
     * dispatching the task at hand. This means that the current tree
     * in the current area is ALWAYS available. This means that none of
     * the other routines should have to worry about which area or
     * reading the tree; this information is already available in
     * $this->area_id and $this->tree.
     *
     *
     * @param object &$output collects the html output
     * @return void results are returned as output in $this->output
     */
    function PageManager(&$output) {
        global $USER;

        $this->output = &$output;
        $this->output->set_helptopic('pagemanager');
        $this->areas = get_area_records();

        // Do not maintain a 'current area' if it is no longer there (it might have been deleted by us or another admin)
        if ((isset($_SESSION['current_area_id'])) && (!isset($this->areas[intval($_SESSION['current_area_id'])]))) {
            unset($_SESSION['current_area_id']);
        }

        // Do we have a valid working area? If not, try to calculate one
        if ((!isset($_SESSION['current_area_id'])) ||
            (!isset($_SESSION['tree_mode'])) ||
            (!isset($_SESSION['expanded_nodes']))) {

            // 1 -- Try the specified area aaa from command line (admin.php?area=aaa)
            $area_id = get_parameter_int('area',FALSE);
            if ($area_id !== FALSE) {
                if ((!$USER->is_admin_pagemanager($area_id)) || (!isset($this->areas[$area_id]))) {
                    logger(__FUNCTION__."(): weird: user '{$USER->username}' tried to access area '$area_id'");
                    $message = t('area_admin_access_denied','admin',array('{AREA}' => strval($area_id)));
                    $area_id = FALSE; // sentinel
                }
            } else {
                // 2 -- try use the first available area for which the user has permissions...
                $message = t('no_areas_available','admin'); // ... but assume the worst
                foreach($this->areas as $id => $area) {
                    if ($USER->is_admin_pagemanager($id)) {
                        $area_id = $id;
                        break;
                    }
                }
            }
            if ($area_id === FALSE) {
                $this->output->add_message($message);
                $this->output->add_content($message);
                $this->show_area_menu();
                return;
            } else {
                $_SESSION['current_area_id'] = $area_id;
                $_SESSION['expanded_nodes'] = array();
                $_SESSION['tree_mode'] = TREE_VIEW_MINIMAL;
            }
        } else {
            // Do we (still) have enough permissions for the current area?
            $area_id = intval($_SESSION['current_area_id']);
            if ((!$USER->is_admin_pagemanager($area_id)) || (!isset($this->areas[$area_id]))) {
                // this is completely weird: the session has an invalid area? tsk tsk tsk
                logger(__FUNCTION__."(): weird: user '{$USER->username}' can no longer access area '$area_id'?");
                $message = t('area_admin_access_denied','admin',array('{AREA}' => strval($area_id)));
                $this->output->add_message($message);
                $this->output->add_content($message);
                $this->show_area_menu();
                return;
            }
        }
        //
        // At this point we have 3 valid variables in $_SESSION indicating
        // the current working area and the open/closed state of sections.
        // We now should read the corresponding tree in core and look which
        // task we need to perform.
        //
        $this->build_cached_tree(intval($_SESSION['current_area_id']));
        $task = get_parameter_string('task',TASK_TREEVIEW);

        switch ($task) {
        case TASK_TREEVIEW:
            $this->task_treeview();
            break;

        case TASK_TREEVIEW_SET:
            $this->task_treeview_set();
            break;

        case TASK_SUBTREE_EXPAND:
            $this->task_subtree_expand();
            break;

        case TASK_SUBTREE_COLLAPSE:
            $this->task_subtree_collapse();
            break;

        case TASK_SET_DEFAULT:
            $this->task_set_default();
            break;

        case TASK_ADD_PAGE:
        case TASK_ADD_SECTION:
            $this->task_node_add($task);
            break;

        case TASK_NODE_DELETE:
            $this->task_node_delete();
            break;

        case TASK_NODE_EDIT:
        case TASK_NODE_EDIT_ADVANCED:
            $this->task_node_edit($task);
            break;

        case TASK_NODE_EDIT_CONTENT:
            $this->task_node_edit_content();
            break;

        case TASK_SAVE_CONTENT:
            $this->task_save_content();
            break;

        case TASK_PAGE_PREVIEW:
            $this->task_page_preview();
            break;

        case TASK_SAVE_NODE:
            $this->task_save_node();
            break;

        case TASK_SAVE_NEWPAGE:
        case TASK_SAVE_NEWSECTION:
            $this->task_save_newnode($task);
            break;

        default:
            $s = (utf8_strlen($task) <= 50) ? $task : utf8_substr($task,0,44).' (...)';
            $message = t('task_unknown','admin',array('{TASK}' => htmlspecialchars($s)));
            $this->output->add_message($message);
            logger(__FUNCTION__.'(): unknown task: '.htmlspecialchars($s));
            $this->task_treeview();
            break;
        }
    } // PageManager()


    /** maybe change the current area and then show the tree and the menu for the current area
     *
     * this routine switches to a new area if one is specified and subsequently
     * displays the tree of the new area or the existing current area.
     *
     * @return void results are returned as output in $this->output
     */
    function task_treeview() {
        global $USER;
        $area_id = get_parameter_int('area',FALSE);
        if ($area_id !== FALSE) {
            if ((!$USER->is_admin_pagemanager($area_id)) || (!isset($this->areas[$area_id]))) {
                logger(__FUNCTION__."(): weird: user '{$USER->username}' tried to access area '$area_id'");
                $message = t('area_admin_access_denied','admin',array('{AREA}' => strval($area_id)));
                $this->output->add_message($message);
                $this->output->add_content($message);
                $this->show_area_menu();
                return;
            } else {
                $_SESSION['current_area_id'] = $area_id;
                $_SESSION['expanded_nodes'] = array();
                $_SESSION['tree_mode'] = TREE_VIEW_MINIMAL;
                if ($area_id != $this->area_id) {
                    $this->build_cached_tree(intval($_SESSION['current_area_id']),TRUE); // TRUE means: force reread
                }
            }
        }
        $this->show_tree();
        $this->show_area_menu($this->area_id);
    } // task_treeview()


    /** this sets the tree view to the specified mode
     *
     * this is a simple routine to set the current view to
     * one of the three possible views. The problem that
     * sometimes 'custom' yields a view identical with 'maximal'
     * or 'minimal' is dealt with when constructing the links
     * to this routine task_treeview_set().
     * See {@link show_treeview_buttons()} for more information.
     *
     * @return void results are returned as output in $this->output
     */
    function task_treeview_set() {
        $new_mode = get_parameter_int(PARAM_TREEVIEW,TREE_VIEW_MINIMAL);
        switch ($new_mode) {
        case TREE_VIEW_MINIMAL:
        case TREE_VIEW_CUSTOM:
        case TREE_VIEW_MAXIMAL:
            $_SESSION['tree_mode'] = $new_mode;
            break;
        default:
            $_SESSION['tree_mode'] = TREE_VIEW_MINIMAL;
            break;
        }
        $this->show_tree();
        $this->show_area_menu($this->area_id);
    } // task_treeview_set()


    /** open the selected section and perhaps change the view mode
     *
     * this opens the selected node, i.e. unfold 1 level of the subtree
     * starting at the selected node. This should only happen when
     * the view mode is either minimal (all sections closed) or custom
     * (some sections opened and some sections closed). It should never
     * happen when mode is maximal.
     *
     * The status of a node (opened or closed) is remembered in
     * session variable 'expanded_nodes': an array keyed with node_id
     * If the corresponding value is TRUE, the section is considered open,
     * all other values (FALSE or element is non-existing) equate to closed.
     * See also {@link task_subtree_collapse()}.
     *
     * @return void results are returned as output in $this->output
     * @uses $USER
     */
    function task_subtree_expand() {
        global $USER;
        $node_id = get_parameter_int('node',0);
        switch($_SESSION['tree_mode']) {
        case TREE_VIEW_MINIMAL:
            $_SESSION['tree_mode'] = TREE_VIEW_CUSTOM;
            $_SESSION['expanded_nodes'] = array($node_id => TRUE);
            break;

        case TREE_VIEW_CUSTOM:
            $_SESSION['expanded_nodes'][$node_id] = TRUE;
            break;

        case TREE_VIEW_MAXIMAL:
            logger(__FUNCTION__."(): weird: {$USER->username} is expanding node '$node_id' in already maximised view");
            break;

        default:
            break;
        }
        $this->show_tree();
        $this->show_area_menu($this->area_id);
    } // task_subtree_expand()


    /** close the selected section and perhaps change the view mode
     *
     * this closes the selected node, i.e. fold in the subtree
     * starting at the selected node. This should only happen when
     * the view mode is either maximal (all sections closed) or custom
     * (some sections opened and some sections closed). It should never
     * happen when mode is minimal.
     *
     * The status of a node (opened or closed) is remembered in
     * session variable 'expanded_nodes': an array keyed with node_id
     * If the corresponding value is TRUE, the section is considered open,
     * all other values (FALSE or element is non-existing) equate to closed.
     * See also {@link task_subtree_expand()}.
     *
     * If the current mode is 'maximal', all sections are showed 'open'.
     * When one of the sections is closed (via this routine), we change
     * the mode to 'custom'. However, because the previous state was 'all
     * sections are opened', we need to remember all the sections in the
     * session variable 'expanded_nodes' and set them all to TRUE except
     * the section that needs to be closed. We do this by constructing the
     * complete tree of the area and adding an entry for every section and
     * setting the value to TRUE, except the node that needs to be closed.
     *
     * @return void results are returned as output in $this->output
     * @uses $USER
     */
    function task_subtree_collapse() {
        global $USER;
        $node_id = get_parameter_int('node',0);
        switch($_SESSION['tree_mode']) {
        case TREE_VIEW_MINIMAL:
            logger(__FUNCTION__."(): weird: {$USER->username} is collapsing node '$node_id' in already minimised view");
            break;

        case TREE_VIEW_CUSTOM:
            $_SESSION['expanded_nodes'][$node_id] = FALSE;
            break;

        case TREE_VIEW_MAXIMAL:
            $_SESSION['tree_mode'] = TREE_VIEW_CUSTOM;
            // first set every node of type section to expanded...
            $expanded_nodes = array();
            foreach ($this->tree as $k => $v) {
                if (!$v['is_page']) {
                    $expanded_nodes[$k] = TRUE;
                }
            }
            // ...except the node that needs to be collapsed
            $expanded_nodes[$node_id] = FALSE;
            $_SESSION['expanded_nodes'] = $expanded_nodes;
            break;

        default:
            break;
        }
        $this->show_tree();
        $this->show_area_menu($this->area_id);
    } // task_subtree_collapse()


    /** make the selected node the default for this level
     *
     * this sets a default node. First we make sure we have a valid
     * environment and a node that belongs to the current area
     * Then we check permissions and if the user is allowed to 
     *  - set the default bit on the target node, AND
     *  - reset the default bit on the current default node
     * we actually
     *  - reset the default bit from the current default (if there is one), AND
     *  - set the default bit for the selected node.
     *
     * Note: if the user sets the default node on the current default node,
     * the default is reset and subsequently set again (two trips to the database),
     * This also updates the mtime of the record.
     *
     * @return void results are returned as output in $this->output
     * @uses $USER
     */
    function task_set_default() {
        global $USER;

        // 1 -- do we have a sane value for node_id?
        $node_id = get_parameter_int('node',0);
        if (($node_id == 0) || (!isset($this->tree[$node_id]))) {
            // are they trying to trick us, specifying a node from another area?
            logger(__FUNCTION__."(): weird: user tried to make node '$node_id' default while in area '{$this->area_id}'");
            $this->output->add_message(t('invalid_node','admin',array('{NODE}' => strval($node_id))));
            $this->task_treeview();
            return;
        }

        // 2 -- are we allowed to change the default page?
        if (!$this->permission_set_default($node_id)) {
            $this->output->add_message(t('task_set_default_access_denied','admin',array('{NODE}' => $node_id)));
            $this->show_tree();
            $this->show_area_menu($this->area_id);
            return;
        }

        // 3A -- actually reset old default and set the new default in database
        $embargo = FALSE;
        $now = strftime('%Y-%m-%d %T');
        $fields = array('mtime' => $now);
        if (($home_id = $this->calc_home_id($node_id)) !== FALSE) {
            $fields['is_default'] = FALSE;
            $where = array('node_id' => $home_id);
            db_update('nodes',$fields,$where);
            $embargo = ($now < $this->tree[$home_id]['record']['embargo']);
        }
        $fields['is_default'] = TRUE;
        $where = array('node_id' => $node_id);
        db_update('nodes',$fields,$where);
        $embargo |= is_under_embargo($this->tree,$node_id); // TRUE when $home_id, $node_id or ancestor under embargo

        // 3B -- always log the event
        $parent_id = $this->tree[$node_id]['parent_id'];
        // log msg like "area 1: [page | section] 2 new default [in section 3 | at top] [(old was 4)] [(embargo)]"
        logger(sprintf(__CLASS__.': area %d: %s %d is new default %s%s%s',
                       $this->area_id,
                       ($this->tree[$node_id]['is_page']) ? 'page' : 'section',
                       $node_id,
                       ($parent_id != 0) ? 'in section '.$parent_id : ' at toplevel',
                       ($home_id != 0) ? ' (old was '.$home_id.')' : '',
                       ($embargo) ? ' (embargo)' : ''));

        // 3C -- maybe sent alerts about the event
        if (!$embargo) {
            $nodes = $this->get_node_id_and_ancestors($node_id);
            $params = array('{AREA}' => $this->area_id,'{NEW}' => $this->node_full_name($node_id));
            if ($parent_id != 0) {
                // msg: Area %d: new default page/section %d %s (%s) in section %d %s (%s)
                $params['{PARENT}'] = $this->node_full_name($parent_id);
                $message = t('new_default_node_in_section','admin',$params);
            } else {
                // msg: Area %d: new default page/section %d %s (%s)
                $message = t('new_default_node_in_area','admin',$params);
            }
            if ($home_id !== FALSE) {
                // additional msg: (old default page/section was %d %s (%s))
                $message .= ' '.t('old_default_node','admin',array('{OLD}' => $this->node_full_name($home_id)));
                $nodes[] = $home_id; // add the old home to the alert-list too
            }
            $this->queue_area_node_alert($this->area_id,$nodes,$message,$USER->full_name);
        }
        // 3D -- update tree in core and show it (not re-reading full tree saves an extra trip to database)
        $this->tree[$node_id]['is_default'] = TRUE;
        $this->tree[$node_id]['record']['is_default'] = TRUE;
        if ($home_id !== FALSE) {
            $this->tree[$home_id]['is_default'] = FALSE;
            $this->tree[$home_id]['record']['is_default'] = FALSE;
        }
        $this->show_tree();
        $this->show_area_menu($this->area_id); 
    } // task_set_default()


    /** display a dialog to add a new page or section to the current area
     *
     * this displays a dialog where the user can add
     * a node to the current area. If the user has no
     * permissions to add a node at all, the result is
     * an error message and the tree view
     *
     * The value of $task (which can be either TASK_ADD_PAGE or
     * TASK_ADD_SECTION) determines which dialog to show.
     *
     * Both dialogs are very similar (a page can have a module, a section cannot).
     * The actual dialog is constructed based on a dialogdef, see
     * the function {@link get_dialogdef_add_node()}.
     *
     * @param string $task identifies whether a page or a section should be added
     * @return void results are returned as output in $this->output
     */
    function task_node_add($task) {
        global $WAS_SCRIPT_NAME;
        $is_page = ($task == TASK_ADD_PAGE) ? TRUE : FALSE;

        // 1 -- are we allowed at all?
        if (!$this->permission_add_any_node($is_page)) {
            $msg = t('task_node_add_access_denied','admin');
            $this->output->add_message($msg);
            $this->output->add_popup_bottom($msg);
            $this->show_tree();
            $this->show_area_menu($this->area_id);
            return;
        }
        // 2 -- construct the dialog header
        if ($is_page) {
            $this->output->add_content('<h2>'.t('add_a_page_header','admin').'</h2>');
            $href = href($WAS_SCRIPT_NAME,array('job' => JOB_PAGEMANAGER, 'task' => TASK_SAVE_NEWPAGE));
            $this->output->add_content(t('add_page_explanation','admin'));
        } else {
            $this->output->add_content('<h2>'.t('add_a_section_header','admin').'</h2>');
            $href = href($WAS_SCRIPT_NAME,array('job' => JOB_PAGEMANAGER, 'task' => TASK_SAVE_NEWSECTION));
            $this->output->add_content(t('add_section_explanation','admin'));
        }

        // 3 -- construct the actual dialog
        $dialogdef = $this->get_dialogdef_add_node($is_page);
        $this->output->add_content(dialog_quickform($href,$dialogdef));
    } // task_node_add()


    /** delete one or more nodes from an area after user confirmation
     *
     * this deals with deleting nodes from an area. There are two stages.
     * Stage 1 is presenting the user with a list of selected nodes and
     * offering the user the choice to confirm the delete or cancel the operation.
     * Stage 2 is actually deleting the selected nodes (after the user confirmed
     * the delete in stage 1), including the disconnection of pages and modules.
     *
     * An important design decision was to limit the delete process to at most
     * 1 tree level. This means the following. If a user attempts to delete a page,
     * it is easy: after confirmation a single node is deleted from the database.
     * If a user attempts to delete a section, it can be different. If the
     * section is empty, i.e. there are no childeren, it is the same as deleting
     * a page: only a single node record has to be deleted.
     *
     * It becomes more dangerous if a section is filled, ie. has childeren.
     * If all childeren are pages (or empty subsections), it is still relatively
     * innocent because the worst case is that all pages in a section are deleted.
     * If, however, the section contains subsections which in turn contain
     * subsubsections, etc. the delete operation may become a little too
     * powerful. If it would work that way (deleting a section implies _all_
     * nodes in the subtree), it is possible to delete a complete area in only
     * a few keystrokes, no matter how many levels.
     *
     * In order to prevent this mass deletion, we decided to limit the delete
     * operation to at most a single level. In other words: the user can delete
     *  - a single page
     *  - a single empty section
     *  - a section with childeren but no grandchilderen
     * If a user attempts to delete a section with childeren and grandchilderen,
     * an error message is displayed and nothing is deleted.
     *
     * This forces the user to delete a complete tree a section at the time,
     * hopefully preventing a 'oh no! what have I done' user experience.
     *
     * We _always_ want the user to confirm the deletion of a node,
     * even if it is just a single page.
     *
     * Note that a page that is readonly will not be deleted.
     *
     * @return void results are returned as output in $this->output
     * @todo should we display trash can icons for sections with non-empty subsections in treeview?
     *       there really is no point, because we eventually will not accept deletion of sections
     *       with grandchilderen. Hmmmmm.....
     */
    function task_node_delete() {

        // 1 -- do we have a sane value for node_id?
        $node_id = get_parameter_int('node',0);
        if (($node_id == 0) || (!isset($this->tree[$node_id]))) {
            // are they trying to trick us, specifying a node from another area?
            logger(__FUNCTION__."(): weird: user tried to delete node '$node_id' working in area '{$this->area_id}'");
            $this->output->add_message(t('invalid_node','admin',array('{NODE}' => strval($node_id))));
            $this->task_treeview();
            return;
        }

        // 2 -- check out permissions and other preconditions for delete
        $access = TRUE; // assume we are allowed
        if (!$this->permission_delete_node($node_id,$this->tree[$node_id]['is_page'])) {
            $access = FALSE;
            $msg_key = 'task_delete_node_access_denied';
        } elseif (db_bool_is(TRUE,$this->tree[$node_id]['record']['is_readonly'])) {
            $access = FALSE;
            $msg_key = 'task_delete_node_is_readonly';
        } elseif ($this->node_has_grandchilderen($node_id)) {
            $access = FALSE;
            $msg_key = 'task_delete_node_limited';
        }
        if (!$access) {
            $params = array('{NODE}' => $node_id,'{NODE_FULL_NAME}' => $this->node_full_name($node_id));
            $msg = t($msg_key,'admin', $params);
            $this->output->add_message($msg);
            $this->output->add_popup_bottom($msg);
            $this->show_tree();
            $this->show_area_menu($this->area_id);
            return;
        }

        // 3 -- try to obtain a lock on this node
        $lockinfo = array();
        if (!lock_record_node($node_id,$lockinfo)) {
            // failed to get a lock, tell user about who DID obtain the lock + show tree again
            $msg = $this->message_from_lockinfo($lockinfo,$node_id,$this->tree[$node_id]['is_page']);
            $this->output->add_message($msg);
            $this->output->add_popup_bottom($msg);
            $this->show_tree();
            $this->show_area_menu($this->area_id);
            return;
        }
     
        // 4 -- now either show dialog (stage 1) or perform deletion (stage 2)
        if ((isset($_POST['dialog'])) && (intval($_POST['dialog']) == DIALOG_NODE_DELETE_CONFIRM)) {
            // stage 2 - do delete if user pressed delete button
            if (isset($_POST['button_delete'])) {
                if (!$this->delete_node($node_id)) {
                    lock_release_node($node_id);
                } // else
                    // nothing left to unlock
                $this->build_cached_tree($this->area_id,TRUE); // force re-read of tree after deletion
            } else { // user cancelled
                $this->output->add_message(t('cancelled','admin'));
                lock_release_node($node_id);
            }
            $this->show_tree();
            $this->show_area_menu($this->area_id);
        } else {
            $this->output->set_funnel_mode(TRUE);
            $this->show_dialog_delete_node_confirm($node_id);
        }
        return;
    } // task_node_delete()


    /** display a dialog where the user can edit basic or advanced properties of a node
     *
     * this constructs a dialog and a menu where the user can edit the properties of
     * a node. We check the user's permissions
     * and if that works out we try to obtain a lock on the record. If that succeeds,
     * we show the dialog (in funnel mode). If we don't get the lock, we inform the user about the other
     * user who holds the lock. In case of error (e.g. no permissions or no lock) we
     * fall back on displaying the area menu and the treeview.
     *
     * Note: the lock is released once the user saves the node OR cancels the edit operation.
     *
     * @param string $task identifies whether the basic of advanced properties should be edited
     * @return void results are returned as output in $this->output
     */
    function task_node_edit($task) {
        global $WAS_SCRIPT_NAME;

        // 1 -- do we have a sane value for node_id?
        $node_id = get_parameter_int('node',0);
        $anode = array('{NODE}' => strval($node_id));
        if (($node_id == 0) || (!isset($this->tree[$node_id]))) {
            // are they trying to trick us, specifying a node from another area?
            logger(__FUNCTION__."(): weird: user tried to edit node '$node_id' working in area '{$this->area_id}'");
            $this->output->add_message(t('invalid_node','admin',$anode));
            $this->task_treeview();
            return;
        }

        //  2 -- do we have permissions?
        $is_page = ($this->tree[$node_id]['is_page']) ? TRUE : FALSE;
        if (!$this->permission_edit_node($node_id,$is_page)) {
            $msg = t(($is_page) ? 'task_edit_page_access_denied' : 'task_edit_section_access_denied','admin',$anode);
            $this->output->add_message($msg);
            $this->output->add_popup_bottom($msg);
            $this->show_tree();
            $this->show_area_menu($this->area_id);
            return;
        }

        // 3 -- are wa able to lock the node in preparation for edit?
        $lockinfo = array();
        if (!lock_record_node($node_id,$lockinfo)) {
            // failed to get a lock, tell user about who DID obtain the lock + show tree again
            $msg = $this->message_from_lockinfo($lockinfo,$node_id,$is_page);
            $this->output->add_message($msg);
            $this->output->add_popup_bottom($msg);
            $this->show_tree();
            $this->show_area_menu($this->area_id);
            return;
        }
        // 4 -- Everything looks OK, go do it
        $viewonly = db_bool_is(TRUE,$this->tree[$node_id]['record']['is_readonly']);
        $anode['{NODE_FULL_NAME}'] = $this->node_full_name($node_id);
        if ($task == TASK_NODE_EDIT_ADVANCED) {
            $dialogdef = $this->get_dialogdef_edit_advanced_node($node_id,$is_page,$viewonly);
            $title = t(($is_page) ? 'edit_a_page_advanced_header' : 'edit_a_section_advanced_header','admin',$anode);
            $explain = t(($is_page)?'edit_page_advanced_explanation':'edit_section_advanced_explanation','admin',$anode);
        } else {
            $dialogdef = $this->get_dialogdef_edit_node($node_id,$is_page,$viewonly);
            $title = t(($is_page) ? 'edit_a_page_header' : 'edit_a_section_header','admin',$anode);
            $explain = t(($is_page) ? 'edit_page_explanation':'edit_section_explanation','admin',$anode);
        }
        $this->output->add_content('<h2>'.$title.'</h2>');
        $this->output->add_content($explain);
        $href = href($WAS_SCRIPT_NAME,array('job' => JOB_PAGEMANAGER, 'task' => TASK_SAVE_NODE, 'node' => $node_id));
        $this->get_dialog_data_node($dialogdef,$node_id);

        $this->output->set_funnel_mode(TRUE); // no distractions
        $this->output->add_content(dialog_quickform($href,$dialogdef));
        $this->show_edit_menu($node_id,$is_page,$task);
    } // task_node_edit()


    /** display a dialog where the user can edit the contents of a node via a module
     *
     * this effectively loads the module code associated with the specified node
     * and subsequently calls the corresponding code in the module to display an edit
     * dialog.
     *
     * Just like the other edit routine (see {@link task_node_edit()}) the node is
     * locked first. Also the user permissions are checked.
     * If we don't get the lock, we inform the user about the other
     * user who holds the lock. In case of error (e.g. no permissions or no lock or
     * an error loading the module) we fall back on displaying the area menu and 
     * the treeview. In that process the lock may be released.
     *
     * @return void results are returned as output in $this->output
     * @uses module_show_edit()
     */
    function task_node_edit_content() {

        // 1A -- do we have a sane value for node_id?
        $node_id = get_parameter_int('node',0);
        $anode = array('{NODE}' => strval($node_id));
        if (($node_id == 0) || (!isset($this->tree[$node_id]))) {
            // are they trying to trick us, specifying a node from another area?
            logger(__FUNCTION__."(): weird: user edits content of node '$node_id' working in area '{$this->area_id}'?");
            $this->output->add_message(t('invalid_node','admin',$anode));
            $this->task_treeview();
            return;
        }
        // 1B -- is it a page?
        if (!($this->tree[$node_id]['is_page'])) {
            logger(__CLASS__.": weird: cannot edit content of a section (section '$node_id')");
            $this->task_treeview();
            return;
        }

        // 2 -- are we allowed?
        if (!$this->permission_edit_node_content($node_id)) {
            $msg = t('task_edit_page_access_denied','admin',array('{NODE}' => $node_id));
            $this->output->add_message($msg);
            $this->output->add_popup_bottom($msg);
            $this->show_tree();
            $this->show_area_menu($this->area_id);
            return;
        }

        // 3 -- try to obtain a lock on this node
        $lockinfo = array();
        if (!lock_record_node($node_id,$lockinfo)) {
            // failed to get a lock, tell user about who DID obtain the lock + show tree again
            $msg = $this->message_from_lockinfo($lockinfo,$node_id,$this->tree[$node_id]['is_page']);
            $this->output->add_message($msg);
            $this->output->add_popup_bottom($msg);
            $this->show_tree();
            $this->show_area_menu($this->area_id);
            return;
        }

        // 4 -- Everything looks OK, go edit or view module content 
        $is_page = TRUE;
        $module_id = intval($this->tree[$node_id]['record']['module_id']);
        $viewonly = db_bool_is(TRUE,$this->tree[$node_id]['record']['is_readonly']);
        $edit_again = FALSE;
        $this->output->set_funnel_mode(TRUE);
        if ($this->module_show_edit($node_id,$module_id,$viewonly,$edit_again)) {
            $this->show_edit_menu($node_id,$is_page,TASK_NODE_EDIT_CONTENT);
        } else {
            lock_release_node($node_id);
            $this->output->set_funnel_mode(FALSE);
            $anode = array('{NODE_FULL_NAME}' => $this->node_full_name($node_id));
            $msg = t('error_editing_node_content','admin',$anode);
            $this->output->add_message($msg);
            $this->output->add_popup_bottom($msg);
            $this->show_tree();
            $this->show_area_menu($this->area_id);
        }
    } // task_node_edit_content()


    /** preview a page that is maybe still under embargo/already expired
     *
     * if the user has permissions to preview the specified page, she is
     * redirected to the regular site with a special one-time permission to view
     * a page, even if that page is under embargo or already expired (which
     * normally would prevent any user from viewing that page).
     *
     * There are several ways to implement such a one-off permit, e.g.
     * by setting a quasi-random string in the session and specifying that
     * string as a parameter to index.php. If (in index.php) the string provided matches
     * ths string in the session, the user is granted access. However, this
     * leaves room for the user to manually change the node id to _any_
     * number, even a node that that user is not supposed to see.
     *
     * Another solution might have been to simply include index.php.
     * I decided against that; I don't want to have to deal with a mix
     * of admin.php and index.php-code in the same run.
     *
     * I took a slightly different approach, as follows.
     * First I generate a quasi-random string of N (N=32) characters.
     * (The lenght of 32 is an arbitrary choice.)
     * This string is stored in the session variable.
     * Then I store the requested node in the session variable, too.
     * After that I calculate the md5sum of the combination of the
     * random string and the node id. This yields a hash.
     * This hash is passed on to index.php as the sole parameter.
     *
     * Note that the quasi-random key never leaves the server: it is
     * only stored in the session variables. Also, the node id is not
     * one of the parameters of index.php, this too is only stored in
     * the session variables.
     *
     * Once index.php is processed, the specified md5sum is retrieved
     * and a check is performed on the node id and the quasi-random string
     * in the session variables in order to see if the hashes match. If
     * this is the case, index.php can proceed to show the page preview.
     * Note that there is no way for the user to manipulate the node id,
     * because that number never travels to the user's browser in plain
     * text.
     *
     * Making a bookmark for the preview will use the hash, but the hash
     * depends on a quasi-random string stored in the session. It means
     * that when the session is terminated, the bookmarked page will no
     * longer be visible, which is good. Also, whenever another page
     * preview is requested, a new quasi-random string is generated,
     * which also invalidates the bookmarked page.
     *
     * The only thing that CAN happen is that the user saves the preview in
     * a place where it can be seen by others. Also, the page will probably
     * be cached in the user's browser.
     *
     * With respect to permissions: I consider the preview privilege equivalent
     * with edit permission: if the user is able to edit the node she can see
     * the content of the node anyway. However, maybe we should look at different
     * permissions. Put it on the todo-list.
     *
     * @return void results are returned as output in $this->output
     * @uses $CFG
     * @todo the check on permissions can be improved (is PERMISSION_XXXX_EDIT_NODE enough?)
     * @todo there is an issue with redirecting to another site:
     *       officially the url should be fully qualified (ie. $CFG->www).
     *       I use the shorthand, possibly without scheme and hostname
     *       ($CFG->www_short). This might pose a problem with picky browsers.
     *       See {@link calculate_uri_shortcuts} for more information.
     */
    function task_page_preview() {
        global $CFG;

        // 1A -- do we have a sane value for node_id?
        $node_id = get_parameter_int('node',0);
        $anode = array('{NODE}' => strval($node_id));
        if (($node_id == 0) || (!isset($this->tree[$node_id]))) {
            // are they trying to trick us, specifying a node from another area?
            logger(__FUNCTION__."(): weird: user previews node '$node_id' working in area '{$this->area_id}'?");
            $this->output->add_message(t('invalid_node','admin',$anode));
            $this->task_treeview();
            return;
        }
        // 1B -- is it a page?
        if (!($this->tree[$node_id]['is_page'])) {
            logger(__CLASS__.": weird: cannot preview content of a section (section '$node_id')");
            $this->task_treeview();
            return;
        }

        // 2 -- does the user have permission to edit and thus view this page at all?
        $user_has_permission = (($this->permission_edit_node_content($node_id)) ||
                                ($this->permission_edit_node($node_id,$this->tree[$node_id]['is_page'])));
        if ($user_has_permission) {
            $random_string = quasi_random_string(32);
            $_SESSION['preview_salt'] = $random_string;
            $_SESSION['preview_node'] = $node_id;
            $_SESSION['preview_area'] = $this->area_id;
            $hash = md5($_SESSION['preview_salt'].$_SESSION['preview_node']);
            session_write_close();
            redirect_and_exit($CFG->www_short.'/index.php?preview='.$hash);
            // we never reach this point
        } else {
            $msg = t('access_denied','admin');
            $this->output->add_message($msg);
            $this->output->add_popup_bottom($msg);
            $this->output->add_content('<h2>'.$msg.'</h2>');
            $this->output->add_content(t('access_denied_preview','admin'));
        }
    } // task_page_preview()


    /** save a newly added node to the database
     *
     * this validate and save the (minimal) data for a new node (section or page).
     * First we check which button press brought us here; Cancel means we're done,
     * else we need to validate the user input. This is done by setting up the same
     * dialog structure as we did when presenting the user with a dialog in the first
     * place. This ensures that WE determine which fields we need to look for in the
     * _POST data. (If we simply were to look for fieldnames in the _POST array, we
     * might be tricked in accepting random fieldnames. By starting from the dialog
     * structure we make sure that we only look at fields that are part of the dialog;
     * any other fields are ignored, minimising the risks of the user trying to trick
     * us.)
     *
     * The dialog structure is filled with the data POST'ed by the user and subsequently
     * the data  is validated against the rules in the dialog structure (eg. min length,
     * min/max numerical values, etc). If one or more fields fail the tests, we redo
     * the dialog, using the data from _POST as a new starting point. This makes that
     * the user doesn't lose all other field contents if she makes a minor mistake in
     * entering data for one field.
     *
     * If all data from the dialog appears to be valid, it is copied to an array that
     * will be used to actually insert a new record into the nodes table. This array
     * also holds various other fields (not part of the dialog) with sensible default
     * values. Interesting 'special' fields are 'sort_order' and 'is_hidden' and 'embargo'.
     *
     * 'sort_order' is calculated automatically from other sort orders in the same
     * parent section. There are two ways to do it: always add a node at the end or
     * the exact opposite: always add a node at the beginning. The jury is still out
     * on which of the two is the best choice (see comments in the code below).
     *
     * 'is_hidden' and 'embargo' are calculated from the dialog field 'node_visibility'.
     * The latter gives the user three options: 'visible', 'hidden' and 'embargo'.
     * This translates to the following values for 'is_hidden' and 'embargo' (note
     * that $now is the current time in the form 'yyyy-mm-dd hh:mm:ss'):
     *
     * visible: is_hidden = FALSE, 'embargo' = $now
     * hidden: is_hidden = TRUE, 'embargo' = $now
     * embargo: is_hidden = TRUE, 'embargo' = '9999-12-31 23:59:59'
     *
     * This makes sure that IF the user wants to create a 'secret' node, ie. under embargo
     * until some time in the future, the new node is never visible until the user edits
     * the node to make it visible. However, there is no need to manually add a date/time:
     * we simply plug in the maximum value for a date/time, which effectively means 'forever'.
     *
     * Finally, if the new node is saved, a message about this event is recorded in the logfile (even
     * for new nodes under embargo). Also, if the node is NOT under embargo, an alert message
     * is queued. Note that we do NOT send alerts on a page that is created under embargo.
     * (There is a slight problem with this: once a user edits the node and sets the embargo
     * to a more realistic value, e.g. next week, there is no practical way to inform the
     * 'alert-watchers' about that fact: we cannot send an alert at the time that the
     * embargo date is changed to 'next week' because the node is still under embargo. We don't
     * have a handy opportunity to send alerts because the embargo date will eventually come
     * around and the node will become visible automatically, without anyone being alerted to the
     * fact. Mmmm....
     *
     * @param string $task disinguishes between saving a page or a section
     * @return void results are returned as output in $this->output
     * @todo about 'sort_order': do we insert nodes at the end or the beginning of a parent section?
     * @todo how do we alert users that an embargo date has come around? Do we schedule alerts via cron?
     *
     */
    function task_save_newnode($task) {
        global $WAS_SCRIPT_NAME,$USER;
        $is_page = ($task == TASK_SAVE_NEWPAGE) ? TRUE : FALSE;

        // 1 -- do they want to bail out?
        if (isset($_POST['button_cancel'])) {
            $this->output->add_message(t('cancelled','admin'));
            $this->show_tree();
            $this->show_area_menu($this->area_id);
            return;
        }

        // 2 -- validate input
        $dialogdef = $this->get_dialogdef_add_node($is_page);
        if (!dialog_validate($dialogdef)) {
            // there were errors, show them to the user and do it again
            foreach($dialogdef as $k => $item) {
                if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                    $this->output->add_message($item['error_messages']);
                }
            }
            $href = href($WAS_SCRIPT_NAME,array('job' => JOB_PAGEMANAGER, 'task' => $task));
            $this->output->add_content('<h2>'.t(($is_page)?'add_a_page_header':'add_a_section_header','admin').'</h2>');
            $this->output->add_content(t(($is_page)?'add_page_explanation':'add_section_explanation','admin')); 
            $this->output->add_content(dialog_quickform($href,$dialogdef));
            return;
        }

        // 3A -- prepare for storing data into database
        $now = strftime('%Y-%m-%d %T');
        $parent_id = intval($dialogdef['node_parent_id']['value']);
        switch(intval($dialogdef['node_visibility']['value'])) {
        case NODE_VISIBILIY_VISIBLE: $embargo = FALSE; $hidden = FALSE; break;
        case NODE_VISIBILIY_HIDDEN:  $embargo = FALSE; $hidden = TRUE;  break;
        case NODE_VISIBILIY_EMBARGO: $embargo = TRUE;  $hidden = TRUE;  break;
        default:                     $embargo = FALSE; $hidden = FALSE; break; // same as visible
        }
        $fields = array(
            'area_id' => $this->area_id,
            'parent_id' => $parent_id,
            'is_page' => $is_page,
            'title' => $dialogdef['node_title']['value'],
            'link_text' => $dialogdef['node_link_text']['value'],
            'is_hidden' => $hidden,
            'embargo' => ($embargo) ? '9999-12-31 23:59:59' : $now,
            'sort_order' => $this->calculate_new_sort_order($this->tree,$this->area_id,$parent_id),
            'module_id' => ($is_page) ? intval($dialogdef['node_module_id']['value']) : NULL,
            'owner_id' => $USER->user_id,
            'ctime' => $now,
            'atime' => $now,
            'mtime' => $now);

        // 3B -- actually insert the new node
        $errors = 0;
        if (($new_node_id = db_insert_into_and_get_id('nodes',$fields,'node_id')) === FALSE) {
            ++$errors; // error retrieving the new node_id, shouldn't happen
        }
        if (($errors == 0) && ($parent_id == 0)) {
             if (db_update('nodes',array('parent_id' => $new_node_id),array('node_id' => $new_node_id)) === FALSE) {
                ++$errors;
            }
        }
        if (($errors == 0) && ($is_page)) {
            if ($this->module_connect($this->area_id, $new_node_id, $fields['module_id']) === FALSE) {
                ++$errors;
            }
        }
        // 3C -- maybe do alerts, too
        if ($errors > 0) {
            // something went wrong, tell user
            $message = t('error_adding_node','admin');
            $this->output->add_message($message);
        } else {
            // success! tell the world via an alert if not under embargo
            $nodes = $this->get_node_id_and_ancestors($parent_id);
            $nodes[] = $new_node_id;
            $message = t(($is_page) ? 'page_added' : 'section_added','admin',array(
                '{NODE}' => $new_node_id,
                '{AREA}' => $this->area_id,
                '{LINK}' => $fields['link_text'],
                '{TITLE}' => $fields['title']));
            $this->output->add_message($message);
            $embargo |= is_under_embargo($this->tree,$parent_id); // no alerts if new node or ancestors are embargo'ed
            logger(sprintf(__CLASS__.': area %d: added new %s %d %s (%s)%s',
                           $this->area_id,
                           ($is_page) ? 'page' : 'section',
                           $new_node_id,
                           $fields['link_text'],
                           $fields['title'],
                           ($embargo) ? ' (embargo)' : ''));
            if (!$embargo) {
                $this->queue_area_node_alert($this->area_id,$nodes,$message,$USER->full_name);
            }
            $this->build_cached_tree($this->area_id,TRUE); // TRUE means: force reread of tree
            // Finally a dirty trick: close the tree and re-open the path to the new node
            $_SESSION['tree_mode'] = TREE_VIEW_CUSTOM;
            $_SESSION['expanded_nodes'] = array();
            foreach ($nodes as $id) {
                $_SESSION['expanded_nodes'][$id] = TRUE;
            }
        }

        // 4 -- show tree again (maybe freshly read from database)
        $this->show_tree();
        $this->show_area_menu($this->area_id);
    } // task_save_newnode()


    /* save the modified data for a node
     *
     * this tries to update the node record in the database with the
     * data supplied by the user via either the basic or the advanced
     * properties dialog. If the data supplied are not valid, the
     * user is returned to the corresponding dialog (without losing
     * the data entered sofar), otherwise the user ends up in the
     * (treeview again.
     *
     * Note that the actual work is done in a workhorse routine,
     * here we merely check the basic assumptions and deal with the
     * user pressing the Cancel button.
     *
     * @return void results are returned as output in $this->output
     * @uses lock_release_node()
     * @uses save_node()
     */
    function task_save_node() {

        // 1 -- do we have a sane value for node_id?
        $node_id = get_parameter_int('node',0);
        if (($node_id == 0) || (!isset($this->tree[$node_id]))) {
            // are they trying to trick us, specifying a node from another area?
            logger(__FUNCTION__."(): weird: user tried to save node '$node_id' working in area '{$this->area_id}'");
            $this->output->add_message(t('invalid_node','admin',array('{NODE}' => strval($node_id))));
            $this->task_treeview();
            return;
        }

        // 2-- cancelled?
        if (isset($_POST['button_cancel'])) {
            $this->output->add_message(t('cancelled','admin'));
            lock_release_node($node_id);
            $this->show_tree();
            $this->show_area_menu($this->area_id);
            return;
        }

        // 3 -- permissions?
        $is_page = ($this->tree[$node_id]['is_page']) ? TRUE : FALSE;
        if (!$this->permission_edit_node($node_id,$is_page)) { // access denied
            $msg = t(($is_page) ? 'task_edit_page_access_denied' :
                                  'task_edit_section_access_denied','admin',array('{NODE}' => $node_id));
            $this->output->add_message($msg);
            $this->output->add_popup_bottom($msg);
            $this->show_tree();
            $this->show_area_menu($this->area_id);
            return;
        }

        // 4 -- prepare for save
        $lockinfo = array();
        if (!lock_record_node($node_id,$lockinfo)) {
            // failed to get a lock, tell user about who DID obtain the lock + show tree again
            $msg = message_from_lockinfo($lockinfo,$node_id,$is_page);
            $this->output->add_message($msg);
            $this->output->add_popup_bottom($msg);
            $this->show_tree();
            show_area_menu($this->area_id);
            return;
        }

        // 5 -- Now we've got everything: 
        // - permission to edit,
        // - a sane node_id and also 
        // - a record lock.
        // Let's go for it!
        $this->save_node($node_id);
    } // task_save_node()


    /* save the modified content of a node via a module
     *
     * this tries to save the possibly modified data of the node's content.
     * The actual work is delegated to the save-routine in the module's code.
     * however, we _do_ check the user's permissions for editing a node's 
     * content, etc.
     *
     * We have to trust the module when it tells us that saving data
     * was successful. If the module indicates that saving was NOT succesful,
     * the additional flag $edit_again indicates whether the data should be
     * edited again or that the operation should be cancelled. In the former case
     * we let the module display the edit dialog again. This allows for
     * re-editing the data instead of starting over from scratch or returning
     * to the treeview.
     *
     * Note that we currently only know if the save function went OK (retval TRUE)
     * or not OK (retval FALSE) and whether we need to edit again (via the $edit_again flag).
     * This makes it hard to distinguish between the following cases:
     *  - the user cancelled the operation (maybe via a [Cancel] button)
     *  - the user tried to save the data but the node is readonly and nothing was actually saved
     *  - the user tried to save the data, but the database had a problem
     *  - the user provided invalid data (e.g. an invalid date like 2008-02-31)
     *  - everything went smooth and the data was saved without a hitch
     * Maybe this can be refined in a future version.
     *
     * @return void results are returned as output in $this->output
     * @uses lock_release_node()
     * @uses lock_record_node()
     * @uses module_save()
     * @uses module_show_edit()
     * @uses $USER
     * @todo  should the module interface be refined to allow for different return codes, not just TRUE/FALSE???
     * @todo should the mtime of the NODE be updated when the CONTENT is modified? What is the meaning of a node's mtime?
     */
    function task_save_content() {
        global $USER;

        // 1A -- do we have a sane value for node_id?
        $node_id = get_parameter_int('node',0);
        $anode = array('{NODE}' => strval($node_id));
        if (($node_id == 0) || (!isset($this->tree[$node_id]))) {
            // are they trying to trick us, specifying a node from another area?
            logger(__FUNCTION__."(): weird: user saves content of node '$node_id' working in area '{$this->area_id}'?");
            $this->output->add_message(t('invalid_node','admin',$anode));
            $this->task_treeview();
            return;
        }
        // 1B -- is it a page?
        if (!($this->tree[$node_id]['is_page'])) {
            logger(__CLASS__.": weird: cannot save content of a section (section '$node_id')");
            $this->task_treeview();
            return;
        }

        // 2 -- are we allowed?
        if (!$this->permission_edit_node_content($node_id)) {
            $msg = t('task_edit_page_access_denied','admin',array('{NODE}' => $node_id));
            $this->output->add_message($msg);
            $this->output->add_popup_bottom($msg);
            $this->show_tree();
            $this->show_area_menu($this->area_id);
            return;
        }

        // 3 -- make certain we still have the lock
        $lockinfo = array();
        if (!lock_record_node($node_id,$lockinfo)) {
            // failed to get a lock, tell user about who DID obtain the lock + show tree again
            $is_page = TRUE;
            $msg = message_from_lockinfo($lockinfo,$node_id,$is_page);
            $this->output->add_message($msg);
            $this->output->add_popup_bottom($msg);
            $this->show_tree();
            $this->show_area_menu($this->area_id);
            return;
        }

        // 4 -- execute module save function
        $module_id = intval($this->tree[$node_id]['record']['module_id']);
        $viewonly = db_bool_is(TRUE,$this->tree[$node_id]['record']['is_readonly']);
        if ($this->module_save($node_id,$module_id,$viewonly,$edit_again)) { // success with save, bye now
            lock_release_node($node_id);
            $anode = array('{NODE_FULL_NAME}' => $this->node_full_name($node_id));
            $msg = t('page_saved','admin',$anode);
            $this->output->add_message($msg);
            $this->show_tree();
            $this->show_area_menu($this->area_id);
            $embargo = is_under_embargo($this->tree,$node_id);
            logger(sprintf(__CLASS__.": success saving content node '%d'%s",$node_id,($embargo) ? ' (embargo)':''));
            if (!$embargo) {
                $nodes = $this->get_node_id_and_ancestors($node_id);
                $anode['{AREA}'] = $this->area_id;
                $message = (t('page_content_edited','admin',$anode));
                $this->queue_area_node_alert($this->area_id,$nodes,$message,$USER->full_name);
            }
        } elseif ($edit_again) {
            if ($this->module_show_edit($node_id,$module_id,$viewonly,$edit_again)) {
                $this->output->set_funnel_mode(TRUE); // no distractions
                // Note that we also do NOT show the edit menu: we try to let the user concentrate
                // on the task at hand;  the only escape route is 'Cancel'...
                // Also note that we still have the record lock; that won't change because we
                // will be editing the page again. Cancel'ing will also release the lock.
            } else {
                lock_release_node($node_id);
                $anode = array('{NODE_FULL_NAME}' => $this->node_full_name($node_id));
                $msg = t('error_editing_node_content','admin',$anode);
                $this->output->add_message($msg);
                $this->output->add_popup_bottom($msg);
                $this->show_tree();
                $this->show_area_menu($this->area_id);
            }
        } else { // operation is cancelled
            lock_release_node($node_id);
            $this->output->add_message(t('cancelled','admin'));
            $this->show_tree();
            $this->show_area_menu($this->area_id);
        }
        return;
    } // task_save_content()


    // ==================================================================
    // =========================== WORKHORSES ===========================
    // ==================================================================


    /** construct a clickable list of available areas for the current user
     *
     * this iterates through all available areas in the areas table, and
     * constructs a list of areas (as LI's in a UL) for which the current user
     * has either administrative or view permissions.
     * The latter shows in 'dimmed' form, because it is not allowed to
     * view this area in pagemanager, but the area does exist and is available
     * to the user (as a visitor rather than an administrator) so it should
     * not be suppressed.
     * If a user has neither view or admin permission, the area is suppressed.
     * Note that every user has at least view permissions for a public area.
     *
     * The current area is determined by parameter $current_area_id. This area
     * gets the attribute 'class="current"' which makes it possible to emphasise
     * the current working area in the menu (via CSS).
     *
     * @param int|null $current_area_id the current area
     * @return void results are returned as output in $this->output
     */
    function show_area_menu($current_area_id=NULL) {
        global $USER,$WAS_SCRIPT_NAME;

        // 1 -- examine _all_ available areas and keep the ones we can access
        $selected_areas = array();
        foreach($this->areas as $area_id => $area) {
            if ($USER->is_admin_pagemanager($area_id)) {
                $selected_areas[$area_id] = $area + array('is_admin' => TRUE);
            } elseif ((db_bool_is(FALSE,$area['is_private'])) ||
                      ($USER->has_intranet_permissions(ACL_ROLE_INTRANET_ACCESS,$area_id))) {
                $selected_areas[$area_id] = $area + array('is_admin' => FALSE);
            } // else
                //  suppress this area because user has no access whatsoever
        }

        // 2 -- output selected areas
        $this->output->add_menu('<h2>'.t('menu','admin').'</h2>');
        $this->output->add_menu('<ul>');
        if (sizeof($selected_areas) > 0) {
            $a_params = array('job' => JOB_PAGEMANAGER,'task' => TASK_TREEVIEW,'area' => '0');
            foreach($selected_areas as $area_id => $area) {
                $a_params['area'] = strval($area_id);
                $title = sprintf("%s %d%s",
                                  t((db_bool_is(TRUE,$area['is_private'])) ? 'private_area' : 'public_area','admin'),
                                  $area_id,
                                  (db_bool_is(TRUE,$area['is_active'])) ? '' : ' ('.t('inactive','admin').')');
                $anchor = htmlspecialchars($area['title']);
                $attributes = array('title' => $title);
                if (!$area['is_admin']) {
                    $attributes['class'] = ($current_area_id == $area_id) ? 'dimmed current' : 'dimmed';
                } elseif ($current_area_id == $area_id) {
                    $attributes['class'] = 'current';
                }
                $this->output->add_menu('  <li>'.html_a($WAS_SCRIPT_NAME,$a_params,$attributes,$anchor));
            }
        } else {
            $this->output->add_menu('  <li>'.t('no_areas_available','admin'));
        }
        $this->output->add_menu('</ul>');
    } // show_area_menu()


    /** construct a clickable list of edit variants (basic, advanced and maybe content)
     *
     * this constructs a menu from where the user can navigate to edit basic
     * properties of a node, advanced properties or even the content (for pages).
     *
     * @param int $node_id the current node (the node being edited)
     * @param bool $is_page if TRUE display the link to edit content too (this is for pages only)
     * @param int $current_option the currently selected edit mode (basic, advanced or content)
     * @return void results are returned as output in $this->output
     * @uses $CFG
     * @uses $WAS_SCRIPT_NAME
     */
    function show_edit_menu($node_id,$is_page=FALSE,$current_option=NULL) {
        global $CFG,$WAS_SCRIPT_NAME;

        // 0 -- does the user have permission to edit this node at all?
        $user_has_permission = $this->permission_edit_node($node_id,$is_page);

        $this->output->add_menu('<h2>'.t('menu','admin').'</h2>');
        $this->output->add_menu('<ul>');
        $anode = array('{NODE}' => strval($node_id));

        // 1 -- Basic
        $title = t(($is_page) ? 'edit_basic_page_title' : 'edit_basic_section_title','admin',$anode);
        $a_params = array('job' => JOB_PAGEMANAGER,'task' => TASK_NODE_EDIT,'node' => strval($node_id));
        $a_attr = array('title' => $title);
        if ($current_option == TASK_NODE_EDIT) {
            $a_attr['class'] = 'current';
        }
        $anchor = t('edit_basic','admin');
        if (!$user_has_permission) {
            $a_attr['class'] = 'dimmed';
        }
        $this->output->add_menu('  <li>'.html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor));

        // 2 -- Advanced
        $title = t(($is_page) ? 'edit_advanced_page_title' : 'edit_advanced_section_title','admin',$anode);
        $a_params = array('job' => JOB_PAGEMANAGER,'task' => TASK_NODE_EDIT_ADVANCED,'node' => strval($node_id));
        $a_attr = array('title' => $title);
        if ($current_option == TASK_NODE_EDIT_ADVANCED) {
            $a_attr['class'] = 'current';
        }
        $anchor = t('edit_advanced','admin');
        if (!$user_has_permission) {
            $a_attr['class'] = 'dimmed';
        }
        $this->output->add_menu('  <li>'.html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor));

        // 3 -- Content (pages only)
        if ($is_page) {
            $title = t('edit_content_title','admin',$anode);
            $a_params = array('job' => JOB_PAGEMANAGER,'task' => TASK_NODE_EDIT_CONTENT,'node' => strval($node_id));
            $a_attr = array('title' => $title);
            if ($current_option == TASK_NODE_EDIT_CONTENT) {
                $a_attr['class'] = 'current';
            }
            $anchor = t('edit_content','admin');
            if (!$this->permission_edit_node_content($node_id)) {
                $a_attr['class'] = 'dimmed';
            }
            $this->output->add_menu('  <li>'.html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor));
        }
        $this->output->add_menu('</ul>');
    } // show_edit_menu()


    /** create a tree-like list of nodes in the content area of $this->output
     *
     * this constructs a tree-like view of the current area, with
     *  - a title
     *  - 0, 1 or 2 links to add a node
     *  - 0, 1 or 2 links to select a different tree view
     *  - all nodes that are currently show-able (depending on tree view mode)
     *
     * If the tree is empty, only the links to add a node are displayed (if
     * the user has permission to add). The individual nodes are displayed 
     * using recursion with {@link show_tree_walk()}.
     *
     * Note that the tree is constructed via nested UL's with LI's,
     * all in name of 'graceful degradation': this interface still works
     * if this program has no stylesheet whatsoever).
     *
     * @return void results are returned as output in $this->output
     * @uses $CFG
     * @uses $WAS_SCRIPT_NAME
     * @uses show_tree_walk()
     */
    function show_tree() {
        global $CFG,$WAS_SCRIPT_NAME;

        // 1 -- try to construct a title
        $area_title = (isset($this->areas[$this->area_id])) ? $this->areas[$this->area_id]['title'] : strval($this->area_id);
        $this->output->add_content(sprintf('<h2>%s</h2>',htmlspecialchars($area_title)));

        // 2A -- Check permissions for 'add a page' and 'add a section'
        $can_add_page = $this->permission_add_any_node(TRUE); // TRUE = check for add page
        $can_add_section = $this->permission_add_any_node(FALSE); // FALSE = check for add section

        // 2B -- add 'add a page' and/or 'add a section' as LI's in a UL
        if (($can_add_page) || ($can_add_section)) {
            // line up the "add a node" prompt with the other page links by prepending a few 'dummy' icon images
            // if NOT in text-only mode (for that it is better not to clutter the screen with
            // superfluous layout manipulation, KISS)
            if (!$this->output->text_only) {
                $img_attr = array('width' => 16, 'height' => 16, 'title' => '', 'alt' => t('spacer','admin'));
                $dummy = '    '.html_img($CFG->progwww_short.'/graphics/blank16.gif',$img_attr);
            }
            $this->output->add_content('<ul>');
            if ($can_add_page) {
                $this->output->add_content('  <li class="level0">');
                if (!$this->output->text_only) {
                    for ($i=0; $i<5; ++$i) {
                        $this->output->add_content($dummy);
                    }
                }
                $a_attr = array('name' => 'node_add','title'=> t('add_a_page_title','admin'));
                $a_params = array('job' => JOB_PAGEMANAGER,'task' => TASK_ADD_PAGE);
                $this->output->add_content('    '.html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,t('add_a_page','admin')));
            }
            if ($can_add_section) {
                $this->output->add_content('  <li class="level0">');
                if (!$this->output->text_only) {
                    for ($i=0; $i<5; ++$i) {
                        $this->output->add_content($dummy);
                    }
                }
                $a_attr = array('title'=> t('add_a_section_title','admin'));
                $a_params = array('job' => JOB_PAGEMANAGER,'task' => TASK_ADD_SECTION);
                $this->output->add_content('    '.html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,t('add_a_section','admin')));
            }
            $this->output->add_content('</ul>');
        }

        // 3 -- construct links to change treeview + complete tree OR message "no nodes to show"
        if (sizeof($this->tree) > 1) { // at least one node (apart from the pseudo-node 0)
            $this->show_treeview_buttons();
            $this->output->add_content('<ul>');
            $this->show_tree_walk($this->tree[0]['first_child_id'],'  ');
            $this->output->add_content('</ul>');
        } else {
            $this->output->add_content(t('no_nodes_yet','admin'));
        }
    } // show_tree()


    /** display the specified node, optionally all subtrees, and subsequently all siblings
     *
     * this routine displays the specified node, including clickable icons for
     * setting the default, editing the node etc. from the current tree.
     * After that, any subtrees of this node are displayed using recursion
     * (but only if the section is 'opened').
     * This continues for all siblings of the specified node until there are no more
     * (indicated by a sibling_id equal to zero).
     *
     * @param int $node_id the first node of this tree level to show
     * @param string $m left margin for increased readability
     * @return void results are returned as output in $this->output
     * @uses $CFG
     * @uses $WAS_SCRIPT_NAME
     * @uses show_tree_walk()
     */
    function show_tree_walk($node_id,$m='') {
        global $CFG,$WAS_SCRIPT_NAME;
        static $level = 0;
        $class = 'level'.intval($level);

        while ($node_id > 0) {
            //  1 -- display this node
            $this->output->add_content($m.html_tag('li',array('class' => $class)));
            $this->output->add_content($m.'  '.$this->get_icon_home($node_id));
            $this->output->add_content($m.'  '.$this->get_icon_delete($node_id));
            $this->output->add_content($m.'  '.$this->get_icon_edit($node_id));
            $this->output->add_content($m.'  '.$this->get_icon_invisibility($node_id));
            if ($this->tree[$node_id]['is_page']) {
                $this->output->add_content($m.'  '.$this->get_icon_page_preview($node_id));
            } else {
                $this->output->add_content($m.'  '.$this->get_icon_section($node_id));
            }
            $this->output->add_content($m.'  '.$this->get_link_node_edit($node_id));
            // 2 -- maybe descend tree and show recursively
            if ($this->section_is_open($node_id)) {
                $subtree_id = $this->tree[$node_id]['first_child_id'];
                if ($subtree_id > 0) {
                    if ($level >= MAXIMUM_ITERATIONS) {
                        $this->output->add_content(t('too_many_levels','admin',array('{NODE}' => strval($node_id))));
                        logger(__FUNCTION__.'(): too many levels in node '.$node_id,WLOG_DEBUG);
                    } else {
                        $this->output->add_content($m.'<ul>');
                        ++$level;
                        $this->show_tree_walk($subtree_id,$m.'  ');
                        --$level;
                        $this->output->add_content($m.'</ul>');
                    }
                }
            }

            // 3 -- bump pointer and continue with next node on this level
            $node_id = $this->tree[$node_id]['next_sibling_id'];
        }
        return;
    } // show_tree_walk()


    /** show one or two clickable links to change the view of the tree
     *
     * There are three different tree views:
     *  - minimal: all sections are closed, only the top level nodes are shown
     *  - custom: 1 or more sections are closed and 1 or more sections are opened
     *  - maximal: all sections are opened, all nodes are shown
     * There is a fourth option:
     *  - none: there are no sections at all
     * 
     * The view can be set to either TREE_VIEW_MINIMAL, TREE_VIEW_CUSTOM or
     * TREE_VIEW_MAXIMAL.  The current setting is remembered in session
     * variable 'tree_mode'. A list of customised nodes is kept in session
     * variable expanded_nodes[], an array keyed with the node number an a
     * value of either TRUE (section is 'open') or FALSE (section is
     * 'closed').  An empty array implies all nodes are closed (ie. default
     * value is FALSE).
     * 
     * In some cases TREE_VIEW_CUSTOM is equivalent to one of the other two,
     * e.g. when the user closes the last section, the effect looks exactly
     * like TREE_VIEW_MINIMAL. If the user manually opens all sections, the
     * effect is the same as TREE_VIEW_MAXIMAL.
     * 
     * In this routine we want to show 0, 1 or 2 buttons that allow the user
     * to switch to another viewmode, but only if the new mode(s) are different
     * from the current one.
     * 
     * The equivalency between modes can be determined by counting the number
     * of open and closed sections. Here is a truth table.
     * 
     * <pre>
     * | N | open | closed | description
     * +---+------+--------+------------
     * | 0 |   0  |   0    | no sections at all, show 0 buttons (al modes are equivalent)
     * | 1 |   0  |  >=1   | all sections are closed, 'custom' is equivalent with 'minimal'
     * | 2 |  >=1 |   0    | all sections are opened, 'custom' is equivalent with 'maximal'
     * | 3 |  >=1 |  >=1   | some open, some closed, 'custom' is distinct from the other two modes
     * </pre>
     * 
     * Case N=0
     * In this case there are no sections at all, so there is no point to show
     * any button at all because all views are equivalent: all available pages (if
     * any) live at the top level and they are always visible.
     * 
     * Case N=1
     * In this case 'minimal' and 'custom' are equivalent. That means that if the
     * current view is either 'minimal' or 'custom', the only viable option would
     * be to set the view to 'maximal'. If the current mode is 'maximal', the only
     * viable option is 'minimal'. Only 1 toggle-like button needs to be displayed.
     * 
     * Case N=2
     * In this case 'custom' and 'maximal' are equivalent. That means that if the
     * current view is either 'custom' or 'maximal', the only viable option would
     * be to set the view to 'minimal'. If the current mode is 'minimal', the only
     * viable option is 'maximal'. Only 1 toggle-like button needs to be displayed.
     * 
     * Case N=3
     * In this case 'custom' is a distinct mode somewhere between 'minimal' and 
     * 'maximal'. This means that there are always two other options to choose from:
     * if current mode is 'minimal' the choices are 'custom' and 'maximal',
     * if current mode is 'custom' the choices are 'maximal' and 'minimal',
     * if current mode is 'maximal' the choices are 'minimal' and 'custom'.
     * This means that two buttons need to be displayed.
     * 
     * Strategy:
     * First we step through the tree and we count the 'open' and 'closed'
     * sections. After that we determine whether N is 0,1,2 or 3 (see truthtable).
     * After that we calculate which of the three buttons need to be displayed,
     * depending on the current mode (obtained via the session variable 'tree_mode').
     * Subsequently the buttons are output to the 'content' area via $this->output.
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME
     */
    function show_treeview_buttons() {
        global $WAS_SCRIPT_NAME;

        // 1 -- count the open and closed sections
        $opened = 0;
        $closed = 0;
        foreach($this->tree as $node_id => $node) {
            if (!$node['is_page']) {
                if ((isset($_SESSION['expanded_nodes'][$node_id])) && ($_SESSION['expanded_nodes'][$node_id]) ) {
                    $opened++;
                } else {
                    $closed++;
                }
            }
        }

        // 2B -- no sections and hence no buttons at all, return zilch
        if ($opened + $closed == 0) {
            return;
        }

        // 2B -- determine which 1 or 2 buttons to show (and in what order)
        $buttons_to_show = array();
        switch ($_SESSION['tree_mode']) {
        case TREE_VIEW_MINIMAL:
            if (($opened > 0) && ($closed > 0)) { // 3 distinct modes (N=3), hence 2 buttons
                $buttons_to_show[] = TREE_VIEW_CUSTOM;
            }
            $buttons_to_show[] = TREE_VIEW_MAXIMAL; // always show 'maximal' if current == 'minimal'
            break;

        case TREE_VIEW_CUSTOM:
            if ($closed > 0) { // not all are opened: 'maximal' != 'custom'
                $buttons_to_show[] = TREE_VIEW_MAXIMAL;
            }
            if ($opened > 0) { // not all are closed: 'minimal' != 'custom'
                $buttons_to_show[] = TREE_VIEW_MINIMAL;
            }
            break;

        case TREE_VIEW_MAXIMAL:
            $buttons_to_show[] = TREE_VIEW_MINIMAL;
            if (($opened > 0) && ($closed > 0)) { // 3 distinct modes (N=3), hence 2 buttons
                $buttons_to_show[] = TREE_VIEW_CUSTOM;
            }
            break;
        }

        // 2C -- output a prompt and the the button(s)
        $this->output->add_content(t('set_tree_view','admin'));
        foreach ($buttons_to_show as $treeview) {
            $a_params = array('job' => JOB_PAGEMANAGER,'task' => TASK_TREEVIEW_SET,PARAM_TREEVIEW => $treeview);
            if ($treeview == TREE_VIEW_MINIMAL) {
                $anchor = t('set_view_minimal','admin');
                $title = t('set_view_minimal_title','admin');

            } elseif ($treeview == TREE_VIEW_CUSTOM) {
                $anchor = t('set_view_custom','admin');
                $title = t('set_view_custom_title','admin');

            } else {  // ($treeview == TREE_VIEW_MAXIMAL)
                $anchor = t('set_view_maximal','admin');
                $title = t('set_view_maximal_title','admin');
            }
            $attributes = array('title' => $title);
            $this->output->add_content(html_a($WAS_SCRIPT_NAME,$a_params,$attributes,$anchor));
        }
        return;
    } // show_treeview_buttons()


    /** display a list of 1 or more nodes to delete and ask user for confirmation of delete
     *
     * this displays a confirmation question with a list of nodes that
     * will be deleted. This list is either a single page or a single
     * (empty) section OR a section with childeren (but not grandchilderen).
     * See function {@link task_node_delete()} for more on this design decision.
     * If the user presses Delete button, the nodes will be deleted, if the user
     * presses Cancel then nothing is deleted.
     *
     * @param int $node_id the page or the section to delete
     * @return void results are returned as output in $this->output
     */
    function show_dialog_delete_node_confirm($node_id) {
        global $WAS_SCRIPT_NAME;
        $dialogdef = array(
            'dialog' => array(
                'type' => F_INTEGER,
                'name' => 'dialog',
                'value' => DIALOG_NODE_DELETE_CONFIRM,
                'hidden' => TRUE
            ),
            'button_delete' => dialog_buttondef(BUTTON_DELETE),
            'button_cancel' => dialog_buttondef(BUTTON_CANCEL)
            );
        $is_page = ($this->tree[$node_id]['is_page']) ? TRUE : FALSE;
        $anode = array('{NODE_FULL_NAME}' => $this->node_full_name($node_id));
        $page_header = t(($is_page) ? 'delete_a_page_header' : 'delete_a_section_header','admin',$anode);
        $this->output->add_content('<h2>'.$page_header.'</h2>');
        $this->output->add_content(t(($is_page) ? 'delete_page_explanation':'delete_section_explanation','admin'));
        $this->output->add_content('<ul>');
        $this->output->add_content('  <li class="level0">'.
                                          t(($is_page) ? 'page_full_name' : 'section_full_name','admin',$anode));
        if (!$is_page) {
            $next_id = $this->tree[$node_id]['first_child_id'];
            while ($next_id != 0) {
                $anode = array('{NODE_FULL_NAME}' => $this->node_full_name($next_id));
                if ($this->tree[$next_id]['is_page']) {
                    $this->output->add_content('  <li class="level0">'.t('page_full_name','admin',$anode));
                } else {
                    $this->output->add_content('  <li class="level0">'.t('section_full_name','admin',$anode));
                }
                $next_id = $this->tree[$next_id]['next_sibling_id'];
            }
        }
        $this->output->add_content('</ul>');
        $this->output->add_content(t('delete_are_you_sure','admin'));
        $href = href($WAS_SCRIPT_NAME,array('job' => JOB_PAGEMANAGER, 'task' => TASK_NODE_DELETE, 'node' => $node_id));
        $this->output->add_content(dialog_quickform($href,$dialogdef));
    } // show_dialog_delete_node_confirm()


    /** workhorse routine for deleting a node, including childeren
     *
     * This deletes the childeren (but not grandchilderen) of a section
     * and the section itself OR simply the node itself. See function 
     * {@link task_node_delete()} for more on this design decision.
     *
     * This routine actually deletes nodes from the database, but only
     * if these nodes do not have childeren AND if the nodes are not readonly.
     * Furthermore, just before the child nodes are deleted, a lock on that
     * node is obtained. This makes sure that a node that is currently being
     * edited by another user is not deleted under her nose. Also, we do not
     * delete nodes that have childeren because that would yield orphan nodes.
     *
     * Any problems with deleting childeren are reported in messages via $this->output.
     * If all childeren are deleted successfully, then $node_id is deleted.
     * Success of the whole operation is indicated by returning TRUE, otherwise
     * FALSE.
     *
     * @param int $node_id the page or the section to delete
     * @return bool TRUE if all nodes successfully deleted, FALSE otherwise
     */
    function delete_node($node_id) {
        global $USER;
        $alert_nodes = array();
        $alert_message = '';
        $error_count = 0;
        $anode = array('{AREA}' => $this->area_id);
        $next_id = ($this->tree[$node_id]['is_page']) ?  0 : $this->tree[$node_id]['first_child_id'];
        for ( ; ($next_id != 0); $next_id = $this->tree[$next_id]['next_sibling_id']) {
            $is_page = ($this->tree[$next_id]['is_page']) ? TRUE : FALSE;
            $lockinfo = array();
            if (!lock_record_node($next_id,$lockinfo)) {
                $msg = $this->message_from_lockinfo($lockinfo,$next_id,$is_page);
                $this->output->add_message($msg);
                $this->output->add_popup_bottom($msg);
                ++$error_count;
                continue;
            }
            $anode['{NODE_FULL_NAME}'] = $this->node_full_name($next_id);
            if (db_bool_is(TRUE,$this->tree[$next_id]['record']['is_readonly'])) {
                $msg = t('task_delete_node_is_readonly','admin',$anode);
                $this->output->add_message($msg);
                $this->output->add_popup_bottom($msg);
                ++$error_count;
                lock_release_node($next_id);
                continue;
            }
            if ($this->tree[$next_id]['is_page']) {
                $this->module_disconnect($this->area_id,$next_id,$this->tree[$next_id]['record']['module_id']);
            } elseif ($this->tree[$next_id]['first_child_id'] != 0) {
                logger(__FUNCTION__."(): weird: subsection '$next_id' of section '$node_id' is not empty, skip delete");
                $this->output->add_message(t('error_deleting_node','admin',$anode));
                ++$error_count;
                lock_release_node($next_id);
                continue;
            }
            $where = array('node_id' => $next_id);
            if (db_delete('nodes',$where) === FALSE) {
                logger(__CLASS__.": deletion of node '$node_id' failed: ".db_errormessage());
                $this->output->add_message(t('error_deleting_node','admin',$anode));
                ++$error_count;
                lock_release_node($next_id);
            } else {
                logger(__CLASS__.": successfully deleted node '$node_id'");
                $msg = t(($is_page) ? 'page_deleted' : 'section_deleted','admin',$anode);
                $this->output->add_message($msg);
                if (!is_under_embargo($this->tree,$next_id)) {
                    $alert_nodes[] = $next_id;
                    $alert_message .= $msg."\n";
                }
            }
        }

        // if all childeren deleted succesfully, delete section itself
        if ($error_count == 0) {
            $is_page = ($this->tree[$node_id]['is_page']) ? TRUE : FALSE;
            if ($is_page) {
                $this->module_disconnect($this->area_id,$node_id,$this->tree[$node_id]['record']['module_id']);
            }
            $anode['{NODE_FULL_NAME}'] = $this->node_full_name($node_id);
            $where = array('node_id' => $node_id);
            if (db_delete('nodes',$where) === FALSE) {
                logger(__CLASS__."(): deletion of node '$node_id' failed: ".db_errormessage());
                $this->output->add_message(t('error_deleting_node','admin',$anode));
                $retval = FALSE;
            } else {
                logger(__CLASS__.": successfully deleted node '$node_id'");
                $msg = t(($is_page) ? 'page_deleted' : 'section_deleted','admin',$anode);
                $this->output->add_message($msg);
                if (!is_under_embargo($this->tree,$node_id)) {
                    $alert_nodes[] = $node_id;
                    $alert_message .= $msg;
                }
                $retval = TRUE;
            }
        } else {
            logger(__CLASS__.": errors deleting section '$node_id': $error_count");
            $anode = array('{NODE_FULL_NAME}' => $this->node_full_name($node_id),'{COUNT}' => $error_count);
            $msg = t('errors_deleting_childeren','admin',$anode);
            $this->output->add_message($msg);
            $retval = FALSE;
        }
        if (!is_under_embargo($this->tree,$node_id) && !empty($alert_nodes)) {
            $alert_nodes = array_merge($alert_nodes,$this->get_node_id_and_ancestors($node_id));
            $this->queue_area_node_alert($this->area_id,$alert_nodes,$alert_message,$USER->full_name);
        }
        return $retval;
    } // delete_node()


    /** workhorse routing for saving modified node data to the database
     *
     * this is the 'meat' in saving the modified node data. There are a lot
     * of complicated things we need to take care of, including dealing with
     * the readonly property (if a node is currently readonly, nothing should
     * be changed whatsoever, except removing the readonly attribute) and with
     * moving a non-empty section to another area. Especially the latter is not
     * trivial to do, therefore it is being done in a separate routine
     * (see {@link save_node_new_area_mass_move()}).
     *
     * Note that we need to return the user to the edit dialog if the data
     * entered is somehow incorrect. If everything is OK, we simply display the
     * treeview and the area menu, as usual.
     *
     * Another complication is dealing with a changed module. If the user decides
     * to change the module, we need to inform the old module that it is no longer
     * connected to this page and is effectively 'deleted'. Subsequently we have to
     * tell the new module that it is in fact now added to this node. It is up to
     * the module's code to deal with these removals and additions (for some 
     * modules it could boil down to a no-op).
     *
     * Finally there is a complication with parent nodes and sort order.
     * The sort order is specified by the user via selecting the node AFTER which
     * this node should be positioned. However, this list of nodes is created
     * based on the OLD parent of the node. If the node is moved to elsewhere in
     * the tree, sorting after a node in another branch no longer makes sense.
     * Therefore, if both the parent and the sort order are changed, the parent
     * prevails (and the sort order information is discarded).
     *
     * @param int $node_id the node we have to change
     * @todo this routine could be improved by refactoring it; it is too long!
     * @return void results are returned as output in $this->output
     * @todo there is something wrong with embargo: should we check starting at parent or at node?
     *       this is not clear: it depends on basic/advanced and whether the embargo field changed.
     *       mmmm... safe choice: start at node_id for the time being
     */
    function save_node($node_id) {
        global $USER,$WAS_SCRIPT_NAME;

        // 0 -- prepare some useful information
        $is_advanced = (intval($_POST['dialog']) == DIALOG_NODE_EDIT_ADVANCED) ? TRUE : FALSE;
        $is_page = db_bool_is(TRUE,$this->tree[$node_id]['record']['is_page']);
        $viewonly = db_bool_is(TRUE,$this->tree[$node_id]['record']['is_readonly']);
        $embargo = is_under_embargo($this->tree,$node_id);

        // 1 -- perhaps make node read-write again + quit
        if ($viewonly) {
            $anode = array('{NODE_FULL_NAME}' => $this->node_full_name($node_id));
            if (($is_advanced) && (!isset($_POST['node_is_readonly']))) {
                $fields = array('is_readonly' => FALSE);
                $where = array('node_id' => $node_id);
                $retval  = db_update('nodes',$fields,$where);
                logger(sprintf(__CLASS__.': set node %s%s to readwrite: %s',
                                    $this->node_full_name($node_id),
                                    ($embargo) ? ' (embargo)' : '',
                                    ($retval === FALSE) ? 'failed: '.db_errormessage() : 'success'));
                $message = t('node_no_longer_readonly','admin',$anode);
                $this->output->add_message($message);
                if (!$embargo) {
                    $nodes = $this->get_node_id_and_ancestors($node_id);
                    $this->queue_area_node_alert($this->area_id,$nodes,$message,$USER->full_name);
                }
                // update our cached version of this node, saving a trip to the database
                $this->tree[$node_id]['record']['is_readonly'] = SQL_FALSE;
            } else {
                $this->output->add_message(t('node_still_readonly','admin',$anode));
            }
            lock_release_node($node_id);
            $this->show_tree();
            $this->show_area_menu($this->area_id);
            return;
        }

        // 2 -- validate data
        $dialogdef = ($is_advanced) ? $this->get_dialogdef_edit_advanced_node($node_id,$is_page,$viewonly) :
                                      $this->get_dialogdef_edit_node($node_id,$is_page,$viewonly);
        if (!dialog_validate($dialogdef)) {
            // errors? show them to the user and edit again
            foreach($dialogdef as $k => $item) {
                if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                    $this->output->add_message($item['error_messages']);
                }
            }
            $anode = array('{NODE}' => strval($node_id),'{NODE_FULL_NAME}' => $this->node_full_name($node_id));
            if ($is_advanced) {
                $title = t(($is_page) ? 'edit_a_page_advanced_header' : 'edit_a_section_advanced_header','admin',$anode);
                $expl=t(($is_page)?'edit_page_advanced_explanation':'edit_section_advanced_explanation','admin',$anode);
            } else {
                $title = t(($is_page) ? 'edit_a_page_header' : 'edit_a_section_header','admin',$anode);
                $expl=t(($is_page) ? 'edit_page_explanation':'edit_section_explanation','admin',$anode);
            }
            $this->output->add_content('<h2>'.$title.'</h2>');
            $this->output->add_content($expl);
            $href = href($WAS_SCRIPT_NAME,array('job' => JOB_PAGEMANAGER, 'task' => TASK_SAVE_NODE, 'node' => $node_id));
            $this->output->add_content(dialog_quickform($href,$dialogdef));
            $this->output->set_funnel_mode(TRUE); // no distractions
            // Note that we also do NOT show the edit menu: we try to let the user concentrate
            // on the task at hand;  the only escape route is 'Cancel'...
            // Also note that we still have the record lock; that won't change because we
            // will be editing the page again. Cancel'ing will also release the lock.
            return;
        }

        // 3A -- prepare for update of node record - phase 1
        $now = strftime("%Y-%m-%d %T");
        $fields = array('mtime' => $now);
        $changed_parent = FALSE;
        $changed_sortorder = FALSE;
        $changed_module = FALSE;
        $changed_area = FALSE;
        $new_area_mass_move = FALSE;
        foreach($dialogdef as $name => $item) {
            if ((isset($item['viewonly'])) && ($item['viewonly'])) {
                continue;
            }
            switch($name) {
            // basic fields
            case 'node_title':
                $fields['title'] = $item['value'];
                $this->tree[$node_id]['record']['title'] = $item['value']; // for full_name in message below
                break;
            case 'node_link_text':
                $fields['link_text'] = $item['value'];
                $this->tree[$node_id]['record']['link_text'] = $item['value']; // for full_name in message below
                break;
            case 'node_parent_id':
                if ($this->tree[$node_id]['parent_id'] != $item['value']) {
                    $parent_id = intval($item['value']); // could be 0, indicating top level node (see below)
                    $changed_parent = TRUE;
                }
                break;
            case 'node_module_id':
                if ($this->tree[$node_id]['record']['module_id'] != $item['value']) {
                    $fields['module_id'] = intval($item['value']);
                    $changed_module = TRUE;
                }
                break;
            case 'node_sort_after_id':
                if ($this->tree[$node_id]['prev_sibling_id'] != $item['value']) {
                    $node_sort_after_id = $item['value']; // deal with this after this foreach()
                    $changed_sortorder = TRUE;
                }
                break;
            // advanced fields
            case 'node_area_id':
                $new_area_id = intval($item['value']);
                if ($this->tree[$node_id]['record']['area_id'] != $new_area_id) {
                    if (($is_page) || ($this->tree[$node_id]['first_child_id'] == 0)) {
                        $fields['area_id'] = intval($item['value']);
                        $changed_area = TRUE;
                    } else {
                        $new_area_mass_move = TRUE;
                    }
                }
                break;
            case 'node_link_image':
                $fields['link_image'] = $item['value'];
                break;
            case 'node_link_image_width':
                $fields['link_image_width'] = intval($item['value']);
                break;
            case 'node_link_image_height':
                $fields['link_image_height'] = intval($item['value']);
                break;
            case 'node_link_target':
                $fields['link_target'] = $item['value'];
                break;
            case 'node_link_href':
                $fields['link_href'] = $item['value'];
                break;
            case 'node_is_hidden':
                $fields['is_hidden'] = ($item['value'] == 1) ? TRUE : FALSE;;
                break;
            case 'node_is_readonly':
                $fields['is_readonly'] = ($item['value'] == 1) ? TRUE : FALSE;
                break;
            case 'node_embargo':
                $fields['embargo'] = $item['value'];
                if ($now < $fields['embargo']) {
                    $embargo = TRUE;
                }
                break;
            case 'node_expiry':
                $fields['expiry'] = $item['value'];
                break;
            case 'node_style':
                $fields['style'] = $item['value'];
                break;
            } // switch
        } // foreach

        // 3B -- prepare for update - phase 2 ('simple exceptions')
        if ($changed_area) {
             // a single node will be moved to the top level of the new area
            $parent_id = 0;
            $newtree = tree_build($new_area_id);
            $fields['sort_order'] = $this->calculate_new_sort_order($newtree,$new_area_id,$parent_id);
            unset($newtree);
            $fields['parent_id'] = $node_id; // $parent_id == $node_id means: top level
            $fields['is_default'] = FALSE; // otherwise the target section might end up with TWO defaults...
        } elseif ($changed_parent) {
            // the node will be moved to another section (or the top level)
            $fields['sort_order'] = $this->calculate_new_sort_order($this->tree,$this->area_id,$parent_id);
            $fields['parent_id'] = ($parent_id == 0) ? $node_id : $parent_id;
            $fields['is_default'] = FALSE; // otherwise the target section might end up with TWO defaults...
        } elseif ($changed_sortorder) {
            // simply change the sort order
            $fields['sort_order'] = $this->calculate_updated_sort_order($node_id,$node_sort_after_id);
        }

        // 4A -- actually update the database for the pending 'simple' changes
        $errors = 0;
        if ($changed_module) {
            if (!($this->module_disconnect($this->area_id,$node_id,$this->tree[$node_id]['record']['module_id']))) {
                ++$errors;
            }
        }
        $where = array('node_id' => $node_id);
        if (db_update('nodes',$fields,$where) === FALSE) {
            logger(sprintf('%s.%s(): error saving node \'%d\'%s: %s',
                           __CLASS__,__FUNCTION__,$node_id,($embargo) ? ' (embargo)' : '',db_errormessage()));
            ++$errors;
        }
        if ($changed_module) {
            if (!($this->module_connect($this->area_id,$node_id,$fields['module_id']))) {
                ++$errors;
            }
        }
        if ($errors == 0) {
            logger(sprintf('%s.%s(): success saving node \'%d\'%s',
                           __CLASS__,__FUNCTION__,$node_id,($embargo) ? ' (embargo)' : ''),WLOG_DEBUG);
            $anode = array('{AREA}' => $this->area_id,'{NODE_FULL_NAME}' => $this->node_full_name($node_id));
            $this->output->add_message(t(($is_page) ? 'page_saved' : 'section_saved','admin',$anode));
            if (!$embargo) {
                $nodes = $this->get_node_id_and_ancestors($node_id);
                if ($changed_area) {
                    $areas = array($this->area_id,$fields['area_id']);
                    $anode['{NEWAREA}'] = $fields['area_id'];
                    $message = (t('node_was_edited_and_moved','admin',$anode));
                } else {
                    $areas = $this->area_id;
                    $message = (t('node_was_edited','admin',$anode));
                }
                $this->queue_area_node_alert($areas,$nodes,$message,$USER->full_name);
            }
        } else {
            $message = t('error_saving_node','admin');
            $this->output->add_message($message);
        }

        // 4B -- update the database for the special case of a mass-move
        if ($new_area_mass_move) {
            $this->save_node_new_area_mass_move($node_id,$new_area_id,$embargo);
        }

        // 5 -- clean up + show updated and re-read tree again
        lock_release_node($node_id);
        $this->build_cached_tree($this->area_id,TRUE); // force re-read of the tree structure
        $this->show_tree();
        $this->show_area_menu($this->area_id);
    } // save_node()


    /** workhorse routine for moving a complete subtree to another area
     *
     * this routine moves a subtree starting at section $node_id from area $this->area_id to area $new_area_id.
     * This is a complicated operation because
     *  - the subtree may be (very) large
     *  - nodes in the subtree may be locked by other users
     *  - we MUST take an all or nothing approach because either ALL of the nodes or NONE of the nodes
     *    in the subtree must change the area_id. If area_id's are only changed partly, we will end up
     *    with orphan nodes because areas differ between a parent and corresponding offspring.
     *
     * Here is my train of thoughts leading to my implementation.
     *
     * The best solution I can think of is to:
     *  - lock all individual nodes in the subtree, and if successful,
     *  - update all these records with the appropriate area_id and at the same time
     *    unlocking these records by writing a NULL to the lock field.
     * This way we postpone the actual move to the new area until we are certain that we
     * have all nodes involved in our hands.
     * If we don't succeed in obtaining all the necessary locks, we have to abandon the
     * operation, accept defeat, release all locks and return FALSE to our caller.
     * If we do succeed, well, we indicate success by returning TRUE.
     *
     * Mmmm....
     *
     * Note that the user might have two browser windows open in the same session.
     * This shouldn't happen, but there is no easy way to prevent the user to open more
     * windows in the same session. This may lead to an undesirable result: if the user
     * is editing another node in the same session in another window (totally unrelated
     * to the move of the current subtree), that node might also be moved to the new
     * area, introducing an orphan in the new area. Mmmmm. The best way to handle that
     * problem is to use a special helper field, say auxilary_id, in the nodes table.
     * That field could be used as follows (pseudo-code):
     *
     * set auxiliary_id of $node_id to $new_area_id
     * for all descendants of section $node_id do
     *    obtain lock on descendant
     *    set auxiliary_id to $new_area_id
     * update nodes set area_id = new_area_id, auxiliary_id = NULL, locked_by = NULL where
     * auxiliary_id = new_area_id AND locked_by = our_session_id
     * Then we once again concentrate the actual work in a single UPDATE-statement.
     *
     * It is a costly operation: at least 2 trips to the database per descendant.
     *
     * Mmmmm...
     *
     * Perhaps we can save (a lot) of trips to the database if we build on the assumption
     * that usually there are more childeren in every section AND that usually the childeren
     * are NOT locked. In that case the pseudo-code becomes:
     *
     * 
     * for all descendants of section $node_id do
     *    if is_section($descendant) then
     *        SET auxiliary_id = $new_area_id, locked_by = $our_session_id WHERE
     *            locked_by IS NULL AND parent_id = $descendant;
     *    endif
     * endfor
     * SET area_id = new_area_id, auxiliary_id = NULL, locked_by = NULL 
     * WHERE auxiliary_id = new_area_id AND locked_by = $our_session_id
     *
     * However, we might miss a descendant or two if it happens to be locked (by us,
     * or by another session). That's no good.
     *
     * Mmmmm...
     *
     * I'm sure there's a better way, but for the time being I'll simply use brute force
     * and my way through the subtree. If this really becomes a huge problem, we may want
     * to refactor this routine.
     *
     * @param int $node_id the node which we are going to move to $new_area_id
     * @param int $new_area_id the area to which we want to move the subtree $node_id
     * @param bool $embargo if TRUE, we cannot send alerts because the original tree is under embargo
     * @return bool FALSE on error, TRUE on success
     * @uses lock_records_subtree()
     */
    function save_node_new_area_mass_move($node_id,$new_area_id,$embargo) {
        global $USER;

        // 1A -- handy shorthand
        $anode = array('{AREA}' => $this->area_id,
                       '{NEWAREA}'=>$new_area_id,
                       '{NODE_FULL_NAME}'=>$this->node_full_name($node_id));

        // 1B -- prepare auxiliary field in $node_id itself
        if (db_update('nodes',array('auxiliary' => $new_area_id),array('node_id' => $node_id)) === FALSE) {
            logger(__FUNCTION__."(): cannot write new area_id $new_area_id to auxiliary field in node $node_id: ".
                                 db_errormessage(),WLOG_DEBUG);
            $msg = t('error_moving_subtree','admin',$anode);
            $this->output->add_message($msg);
            return FALSE;
        }
        // 1C -- lock all offspring + set auxiliary
        $subtree_id = $this->tree[$node_id]['first_child_id'];
        if (!$this->lock_records_subtree($subtree_id,$new_area_id)) {
            $msg = t('error_moving_subtree','admin',$anode);
            $this->output->add_message($msg);
            return FALSE;
        }

        // At this point we have locked the complete subtree AND we have inserted the
        // value for the new area in the auxiliary field. We can now mass-update the
        // selected records and change the area id in one go for all selected records.
        // However, first we want to move the section to the top level of the (old)
        // area because the parent of node_id will not be moved to $new_area_id.

        // 2 -- move section to top level if not already there
        if ($this->tree[$node_id]['parent_id'] != 0) {
            $sort_order = $this->calculate_new_sort_order($this->tree,$this->area_id,0); // 0 means: top level
            $fields = array('parent_id' => $node_id,'sort_order' => $sort_order);
            $where = array('node_id' => $node_id);
            if (db_update('nodes',$fields,$where) === FALSE) {
                logger(__FUNCTION__."(): error moving node $node_id to top level in (old) area {$this->area_id}: ".
                                     db_errormessage(),WLOG_DEBUG);
                $msg = t('error_moving_subtree','admin',$anode);
                $this->output->add_message($msg);
                return FALSE;
            }
        }

        // 3A -- calculate new sort order in new area
        $newtree = tree_build($new_area_id);
        $sort_order = $this->calculate_new_sort_order($newtree,$new_area_id,0); // 0 means: top level
        unset($newtree);

        // 3B -- execute the mass-move and unlock our locked records in a single go
        $fields = array('area_id' => intval($new_area_id),
                        'auxiliary' => NULL,
                        'locked_since' => NULL,
                        'locked_by_session_id' => NULL);
        $where = array('area_id' => $this->area_id,
                       'auxiliary' => $new_area_id,
                       'locked_by_session_id' => $_SESSION['session_id']);
        $retval = db_update('nodes',$fields,$where);
        if ($retval === FALSE) {
            logger(__FUNCTION__."(): error moving subtree $node_id to new area $new_area_id: ".
                                 db_errormessage(),WLOG_DEBUG);
            $msg = t('error_moving_subtree','admin',$anode);
            $this->output->add_message($msg);
            return FALSE;
        }

        // 3C -- update the sort order
        $retval = db_update('nodes',array('sort_order' => $sort_order),array('node_id' => $node_id));
        if ($retval === FALSE) {
            logger(__FUNCTION__."(): error adjusting sort order after moving tree $node_id to new area $new_area_id: ".
                                 db_errormessage(),WLOG_DEBUG);
            $msg = t('error_moving_subtree','admin',$anode);
            $this->output->add_message($msg);
            return FALSE;
        }

        // 4A -- tell user about success
        logger(sprintf(__CLASS__.": success moving %d nodes in subtree '%d' from area '%d' to area '%d'%s",
                       intval($retval),
                       $node_id,
                       $this->area_id,
                       $new_area_id,($embargo) ? ' (embargo)' : ''));
        $msg = t('success_moving_subtree','admin',$anode);
        $this->output->add_message($msg);

        // 4B -- maybe tell others too
        if (!$embargo) {
            $message = (t('subtree_was_moved','admin',$anode));
            $nodes = $this->get_node_id_and_ancestors($node_id);
            $areas = array($this->area_id,$new_area_id);
            $this->queue_area_node_alert($areas,$nodes,$message,$USER->full_name);
        }

        // 4C -- finally indicate success to caller
        return TRUE;
    } // save_node_new_area_mass_move()


    /** construct a clickable icon to set the home page/section on this tree level
     *
     * this constructs a clickable icon to change the default node on this level.
     * it requires PERMISSION_NODE_EDIT_PAGE or PERMISSION_NODE_EDIT_SECTION for
     * both the target default node AND the current default node (if any)
     *
     * @param int $node_id the node of interest
     * @return void results are returned as output in $this->output
     * @uses $CFG
     * @uses $WAS_SCRIPT_NAME
     */
    function get_icon_home($node_id) {
        global $CFG,$WAS_SCRIPT_NAME;

        // 1 -- check out permissions for node_id and also home_id if any
        $user_has_permission = $this->permission_set_default($node_id);

        if (!($user_has_permission)) {
            $title = t('icon_default_access_denied','admin');
        } elseif ($this->tree[$node_id]['is_default']) {
            $title = t('icon_is_default','admin');
        } else {
            $title = t('icon_default','admin');
        }

        // 2 -- construct the icon (image or text)
        if ($this->tree[$node_id]['is_default']) {
            if ($this->output->text_only) {
                $anchor = html_tag('span','class="icon"','['.t('icon_default_text','admin').']');
            } else {
                $img_attr = array('height'=>16,'width'=>16,'title'=>$title,'alt'=>t('icon_default_alt','admin'));
                $anchor = html_img($CFG->progwww_short.'/graphics/startsection.gif',$img_attr);
            }
        } else {
            if ($this->output->text_only) {
                $anchor = html_tag('span','class="icon"','['.t('icon_not_default_text','admin').']');
            } else {
                $img_attr = array('height'=>16,'width'=>16,'title'=>$title,'alt'=>t('icon_not_default_alt','admin'));
                $anchor = html_img($CFG->progwww_short.'/graphics/not_startsection.gif',$img_attr);
            }
        }

        // 3 -- construct the A tag
        $a_params = array('job' => JOB_PAGEMANAGER,'task' => TASK_SET_DEFAULT,'node' => strval($node_id));
        $a_attr = array('name' => 'node'.strval($node_id),'title' => $title);
        if (!$user_has_permission) {
            $a_attr['class'] = 'dimmed';
        }
        return html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
    } // get_icon_home()


    /** construct a clickable icon to delete this node (and underlying nodes too)
     *
     * @param int $node_id  the node to delete
     * @return void results are returned as output in $this->output
     * @uses $CFG
     * @uses $WAS_SCRIPT_NAME
     * @todo should we display trash can icons for sections with non-empty subsections?
     *       there really is no point, because we eventually will not accept deletion of sections
     *       with grandchilderen in task_node_delete. Hmmmmm..... For now I just added the condition
     *       that access is denied when a section has grandchilderen. Need to refine this, later.
     *       Also, how about readonly nodes? Surely those cannot be deleted... should it not show in the icon?
     */
    function get_icon_delete($node_id) {
        global $CFG,$WAS_SCRIPT_NAME;

        // 1 -- does the user have permission to delete this node at all?
        $user_has_permission = ($this->permission_delete_node($node_id,$this->tree[$node_id]['is_page'])) && 
                               (!$this->node_has_grandchilderen($node_id));

        // 2 -- construct the icon (image or text)
        $title = t(($user_has_permission) ? 'icon_delete' : 'icon_delete_access_denied','admin');
        if ($this->output->text_only) {
            $anchor = html_tag('span','class="icon"','['.t('icon_delete_text','admin').']');
        } else {
            $img_attr = array('height' => 16, 'width' => 16, 'title' => $title, 'alt' => t('icon_delete_alt','admin'));
            $anchor = html_img($CFG->progwww_short.'/graphics/delete.gif',$img_attr);
        }

        // 3 -- construct the A tag
        $a_params = array('job' => JOB_PAGEMANAGER,'task' => TASK_NODE_DELETE,'node' => strval($node_id));
        $a_attr = array('title' => $title);
        if (!$user_has_permission) {
            $a_attr['class'] = 'dimmed';
        }
        return html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
    } // get_icon_delete()


    /** construct a clickable icon to edit this node
     *
     * @param int $node_id the node to edit
     * @return void results are returned as output in $this->output
     * @uses $CFG
     * @uses $WAS_SCRIPT_NAME
     * @todo move permission check to a separate function permission_edit_node()
     */
    function get_icon_edit($node_id) {
        global $CFG,$WAS_SCRIPT_NAME;

        // 1 -- does the user have permission to edit this node at all?
        $user_has_permission = $this->permission_edit_node($node_id,$this->tree[$node_id]['is_page']);

        // 2 -- construct the icon (image or text)
        $title = t(($user_has_permission) ? 'icon_edit' : 'icon_edit_access_denied','admin');
        if ($this->output->text_only) {
            $anchor = html_tag('span','class="icon"','['.t('icon_edit_text','admin').']');
        } else {
            $img_attr = array('height' => 16, 'width' => 16, 'title' => $title, 'alt' => t('icon_edit_alt','admin'));
            $anchor = html_img($CFG->progwww_short.'/graphics/edit.gif',$img_attr);
        }

        // 3 -- construct the A tag
        $a_params = array('job' => JOB_PAGEMANAGER,'task' => TASK_NODE_EDIT,'node' => strval($node_id));
        $a_attr = array('title' => $title);
        if (!$user_has_permission) {
            $a_attr['class'] = 'dimmed';
        }
        return html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
    } // get_icon_edit()


    /** construct a clickable icon to edit the advanced properties of this node
     *
     * This icon has another purpose besides creating a link to the advanced
     * properties: it also indicates wheter a node is 'invisible' or not.
     * In this context 'invisible' means either
     *  - the node is under embargo until some time in the future, OR
     *  - the node is alreay expired some time in the past, OR
     *  - the node is hidden (ie. page is not visible in navigation but otherwise available).
     *
     * Depending on the visibility a different icon is displayed.
     *
     * @param int $node_id the node to edit
     * @return void results are returned as output in $this->output
     * @uses $CFG
     * @uses $WAS_SCRIPT_NAME
     */
    function get_icon_invisibility($node_id) {
        global $CFG,$WAS_SCRIPT_NAME;

        // 1 -- does the user have permission to edit this node at all?
        $user_has_permission = $this->permission_edit_node($node_id,$this->tree[$node_id]['is_page']);

        $datim_embargo = $this->tree[$node_id]['record']['embargo'];
        $datim_expiry = $this->tree[$node_id]['record']['expiry'];
        $datim_now = strftime('%Y-%m-%d %T');

        // 2 -- determine which icon to use + set title
        $icon_src = 'invisible.gif';
        $icon_text = t('icon_invisible_text','admin');
        $icon_alt = t('icon_invisible_alt','admin');
        if ($datim_now < $datim_embargo) {
            $title = t('icon_invisible_embargo','admin',array('{DATIM}' => $datim_embargo));
        } elseif ($datim_expiry < $datim_now) {
            $title = t('icon_invisible_expiry','admin',array('{DATIM}' => $datim_expiry));
        } elseif ($this->tree[$node_id]['is_hidden']) {
            $title = t('icon_invisible_hidden','admin');
        } else {
            $title = t('icon_visible','admin');
            $icon_src = 'visible.gif';
            $icon_text = t('icon_visible_text','admin');
            $icon_alt = t('icon_visible_alt','admin');
        }

        // 3A -- construct link parameters
        $a_params = array('job' => JOB_PAGEMANAGER,'task' => TASK_NODE_EDIT_ADVANCED,'node' => strval($node_id));
        $a_attr = array('title' => $title);
        if (!$user_has_permission) {
            $title = t('icon_visible_access_denied','admin');
            $a_attr['class'] = 'dimmed';
        }
        if ($this->output->text_only) {
            $anchor = html_tag('span','class="icon"','['.$icon_text.']');
        } else {
            $img_attr = array('height' => 16, 'width' => 16, 'title' => $title, 'alt' => $icon_alt);
            $anchor = html_img($CFG->progwww_short.'/graphics/'.$icon_src,$img_attr);
        }
        // 3B -- return complete icon
        return html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
    } // get_icon_invisiblity()


    /** construct a clickable icon to preview this node
     *
     * this constructs an icon to preview the page.
     * the user should have edit permissions OR edit content permissions,
     * because you can see the page when you can edit it, so there's no
     * point in preventing the preview in that case.
     * See {@link task_page_preview()} for more information.
     *
     * The preview is displayed in a separate window, either generated via a small
     * routing in javascript or (if javascript disabled) via a target="_blank".
     *
     * @param int $node_id the node to preview
     * @return void results are returned as output in $this->output
     * @uses $CFG
     * @uses $WAS_SCRIPT_NAME
     * @todo if this is a public area, the user can see every page, except the expired/embargo'ed ones
     *       should we take that into account too? I'd say that is way over the top. How about pages
     *       in an intranet where the user has view privilege? Complicated. KISS: only show preview to
     *       those that can edit or edit content.
     */
    function get_icon_page_preview($node_id) {
        global $CFG,$WAS_SCRIPT_NAME;

        // 1 -- does the user have permission to edit and thus view this page at all?
        $user_has_permission = (($this->permission_edit_node_content($node_id)) ||
                                ($this->permission_edit_node($node_id,$this->tree[$node_id]['is_page'])));

        if ($user_has_permission) {
            $title = t('icon_preview_page','admin');
        } else {
            $title = t('icon_preview_page_access_denied','admin');
        }

        // 2 -- construct the icon (image or text)
        if ($this->output->text_only) {
            $anchor = html_tag('span','class="icon"','['.t('icon_preview_page_text','admin').']');
        } else {
            $img_attr = array('height'=>16,'width'=>16,'title'=>$title,'alt'=>t('icon_preview_page_alt','admin'));
            $anchor = html_img($CFG->progwww_short.'/graphics/view.gif',$img_attr);
        }

        // 3 -- construct the A tag
        // This is tricky, because we want to present the preview in a separate
        // window/popup. We don't want to double-escape html special chars, so we
        // construct the url + params + attr manually here. The javascript routine is
        // added to the output page in /program/main_admin.php.
        //
        $a_params = sprintf('job=%s&task=%s&node=%d',JOB_PAGEMANAGER,TASK_PAGE_PREVIEW,$node_id);
        $url = $WAS_SCRIPT_NAME.'?'.htmlspecialchars($a_params);
        $a_attr = sprintf('title="%s" target="_blank" onclick="popup(\'%s\'); return false;"',$title,$url);
        if (!$user_has_permission) {
            $a_attr .= ' class="dimmed"';
        }
        return html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
    } // get_icon_page_preview()


    /** construct a clickable icon to open/close this node
     *
     * This is a toggle: if the node is closed the closed icon is shown,
     * but the action in the A-tag is to open the icon (and vice versa).
     *
     * @param int $node_id the section to open/close
     * @return void results are returned as output in $this->output
     * @uses $CFG
     * @uses $WAS_SCRIPT_NAME
     */
    function get_icon_section($node_id) {
        global $CFG,$WAS_SCRIPT_NAME;

        $img_attr = array('height' => 16, 'width' => 16);
        $a_params = array('job' => JOB_PAGEMANAGER,'node' => strval($node_id));

        if ($this->section_is_open($node_id)) {
            $title = t('icon_close_section','admin');
            $a_params['task'] = TASK_SUBTREE_COLLAPSE;
            $img_attr['title'] = $title;
            $img_attr['alt'] = t('icon_close_section_alt','admin');
            if ($this->output->text_only) {
                $anchor = html_tag('span','class="icon"','['.t('icon_close_section_text','admin').']');
            } else {
                $anchor = html_img($CFG->progwww_short.'/graphics/folder_open.gif',$img_attr);
            }
        } else {
            $title = t('icon_open_section','admin');
            $a_params['task'] = TASK_SUBTREE_EXPAND;
            $img_attr['title'] = $title;
            $img_attr['alt'] = t('icon_open_section_alt','admin');
            if ($this->output->text_only) {
                $anchor = html_tag('span','class="icon"','['.t('icon_open_section_text','admin').']');
            } else {
                $anchor = html_img($CFG->progwww_short.'/graphics/folder_closed.gif',$img_attr);
            }
        }
        $a_attr = array('title' => $title);
        return html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
    } // get_icon_section()


    /** construct a clickable link to edit this node showing the page's title or link-text
     *
     * this generates an A tag which leads to editing the properties (node == section)
     * or content (node == page). Additional information displayed via the title attribute
     * includes the node_id.
     *
     * @param int $node_id the node for which to make the link
     * @return void results are returned as output in $this->output
     * @uses $CFG
     * @uses $WAS_SCRIPT_NAME
     */
    function get_link_node_edit($node_id) {
        global $WAS_SCRIPT_NAME;

        // 1 -- does the user have permission to edit this node at all?
        $is_page = $this->tree[$node_id]['is_page'];
        $user_has_permission = ($is_page) ? ($this->permission_edit_node_content($node_id)) : 
                                            ($this->permission_edit_node($node_id,$is_page));

        // 2 -- construct the anchor text based on node title or link_text or a label indicating the node_id
        $anchor = '';
        $node_link_text = $this->tree[$node_id]['record']['link_text'];
        $node_title = $this->tree[$node_id]['record']['title'];
        if (!empty($node_link_text)) {
            $anchor .= $node_link_text;
        } elseif (!empty($node_title)) {
            $anchor .= $node_title;
        } else {
            $anchor .= t('node_has_no_name','admin',array('{NODE}' => strval($node_id)));
        }

        // this is more or less the same as the edit icon, reuse the translated prompt
        if ($user_has_permission) {
            $title = (!empty($node_title)) ? $node_title : t('icon_edit','admin');
            $title .= ' ('.strval($node_id).')'; // add node id to title to make it visible without display clutter
        } else {
            $title = t('icon_edit_access_denied','admin');
        }

        // 3 -- construct the A tag
        $a_params = array('job' => JOB_PAGEMANAGER,
                           'task' => ($is_page) ? TASK_NODE_EDIT_CONTENT : TASK_NODE_EDIT,
                           'node' => strval($node_id));
        $a_attr = array('title' => $title);
        if (!$user_has_permission) {
            $a_attr['class'] = 'dimmed';
        }
        return html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
    } // get_link_node_edit()


    /** construct a dialog definition for adding a node (page or section)
     *
     * the dialog for pages and sections are different in just a single field:
     * the page has an extra module field.
     *
     * Note that we set two default values: one for visibility and one for the
     * default module id. For now we set the initial visitibility to 2 (hidden).
     * The default module is 1, under the assumption that the first module in the
     * system is the one used most: a plain page. I didn't consider it worthy
     * enough to make this defaults configurable. However, the sort order in
     * the get_options_modules() doesn't guarantee that the plain page module is
     * the first in the list, so there.
     *
     * @param bool $is_page TRUE if the dialog is for a new page, FALSE is for a new section
     * @return array with dialog definition keyed on item name
     * @todo should we make the defaults in this routine configurable? (I'm not convinced they should)
     */
    function get_dialogdef_add_node($is_page) {
        if ($is_page) {
            $default_module_id = 1; // attempt to set the default module to a plain HTML-page, probably module #1
            $modules = $this->get_module_records();
            foreach ($modules as $module_id => $module) {
                if ($module['name'] == MODULE_NAME_DEFAULT) {
                    $default_module_id = $module_id;
                    break;
                }
            }
            unset($modules);
        }

        $dialogdef = array(
            'dialog' => array(
                'type' => F_INTEGER,
                'name' => 'dialog',
                'value' => DIALOG_NODE_ADD,
                'hidden' => TRUE
            ),
            'node_link_text' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'node_link_text',
                'minlength' => 1,
                'maxlength' => 240,
                'columns' => 30,
                'label' => t('add_node_linktext','admin'),
                'title' => t('add_node_linktext_title','admin'),
                'value' => '',
                ),
            'node_title' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'node_title',
                'minlength' => 1,
                'maxlength' => 240,
                'columns' => 50,
                'label' => t('add_node_title','admin'),
                'title' => t('add_node_title_title','admin'),
                'value' => ''
                ),
            'node_parent_id' => array(
                'type' => F_LISTBOX,
                'name' => 'node_parent_id',
                'value' => '',
                'label' => t('add_node_parent_section','admin'),
                'title' => t('add_node_parent_section_title','admin'),
                'options' => $this->get_options_parents($is_page),
                )
            );
        if ($is_page) {
            $dialogdef['node_module_id'] = array(
                'type' => F_LISTBOX,
                'name' => 'node_module_id',
                'value' => $default_module_id,
                'label' => t('add_node_module','admin'),
                'title' => t('add_node_module_title','admin'),
                'options' => $this->get_options_modules()
                );
        }
        $dialogdef['node_visibility'] = array(
                'type' => F_RADIO,
                'name' => 'node_visibility',
                'value' => NODE_VISIBILIY_DEFAULT,
                'label' => t('add_node_initial_visibility','admin'),
                'title' => t('add_node_initial_visibility_title','admin'),
                'options' => array(
                    NODE_VISIBILIY_VISIBLE => array('option' => t('add_node_visible','admin'),
                                                    'title'=>t('add_node_visible_title','admin')),
                    NODE_VISIBILIY_HIDDEN  => array('option'=>t('add_node_hidden','admin'),
                                                    'title'=>t('add_node_hidden_title','admin')),
                    NODE_VISIBILIY_EMBARGO => array('option'=>t('add_node_embargo','admin'),
                                                    'title'=>t('add_node_embargo_title','admin'))
                    )
                );
        $dialogdef['button_save'] = dialog_buttondef(BUTTON_SAVE);
        $dialogdef['button_cancel'] = dialog_buttondef(BUTTON_CANCEL);
        return $dialogdef;
    } // get_dialogdef_add_node()


    /** construct a dialog definition for editing basic properties of an existing node (page or section)
     *
     * the dialog for pages and sections is different in just a single field:
     * the page has an extra module field.
     *
     * Note that we return a keyed array using the name of the dialog field as a key.
     * This makes it easier to reference an incoming field in the save routine.
     *
     * @param int $node_id the node that is to be edited
     * @param bool $is_page TRUE if the dialog is for a page, FALSE is for a section
     * @param bool $viewonly if TRUE, all fields are 'dimmed' (uneditable) and there is no [Save] button
     * @return array with definition of the dialog keyed with fieldname
     */
    function get_dialogdef_edit_node($node_id,$is_page,$viewonly=FALSE) {
        $dialogdef = array(
            'dialog' => array(
                'type' => F_INTEGER,
                'name' => 'dialog',
                'value' => DIALOG_NODE_EDIT,
                'hidden' => TRUE
            ),
            'node_link_text' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'node_link_text',
                'minlength' => 1,
                'maxlength' => 240,
                'columns' => 30,
                'label' => t('edit_node_linktext','admin'),
                'title' => t('edit_node_linktext_title','admin'),
                'viewonly' => $viewonly,
                'value' => ''
                ),
            'node_title' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'node_title',
                'minlength' => 1,
                'maxlength' => 240,
                'columns' => 50,
                'label' => t('edit_node_title','admin'),
                'title' => t('edit_node_title_title','admin'),
                'viewonly' => $viewonly,
                'value' => ''
                ),
            'node_parent_id' => array(
                'type' => F_LISTBOX,
                'name' => 'node_parent_id',
                'value' => '',
                'label' => t('edit_node_parent_section','admin'),
                'title' => t('edit_node_parent_section_title','admin'),
                'options' => $this->get_options_parents($is_page,$node_id),
                'viewonly' => $viewonly
                ),
            'node_sort_after_id' => array(
                'type' => F_LISTBOX,
                'name' => 'node_sort_after_id',
                'value' => '',
                'label' => t('edit_node_sort_order','admin'),
                'title' => t('edit_node_sort_order_title','admin'),
                'options' => $this->get_options_sort_order($node_id),
                'viewonly' => $viewonly
                )
            );
        if ($is_page) {
            $dialogdef['node_module_id'] = array(
                'type' => F_LISTBOX,
                'name' => 'node_module_id',
                'value' => '',
                'label' => t('edit_node_module','admin'),
                'title' => t('edit_node_module_title','admin'),
                'options' => $this->get_options_modules(),
                'viewonly' => $viewonly
                );
        }
        if (!($viewonly)) {
            $dialogdef['button_save'] = dialog_buttondef(BUTTON_SAVE);
        }
        $dialogdef['button_cancel'] = dialog_buttondef(BUTTON_CANCEL);
        return $dialogdef;
    } // get_dialogdef_edit_node()


    /** construct a dialog definition for editing advanced properties of a node (page or section)
     *
     * this constructs a dialog to edit the advanced properties of a node.
     * There is a slight difference between pages and sections: a section can
     * have neither the 'target' property nor the 'href' property; that only makes
     * sense for a page, so these input fields are not displayed for a section.
     *
     * The readonly-property is a special case. Even if the parameter $viewonly is
     * TRUE, the readonly-field is displayed as 'editable'. This is because this
     * particular field is used to toggle the viewonly mode: if a node is readonly,
     * it cannot be edited, except the removal of the readonly attribute.
     * 
     * @param int $area_id the area in which the node lives
     * @param int $node_id the node that is to be edited
     * @param bool $is_page TRUE if the dialog is for a page, FALSE is for a section
     * @param bool $viewonly if TRUE, most fields are 'dimmed' (uneditable)
     * @return array with definition of the dialog
     */
    function get_dialogdef_edit_advanced_node($node_id,$is_page,$viewonly=FALSE) {
        $dialogdef = array(
            'dialog' => array(
                'type' => F_INTEGER,
                'name' => 'dialog',
                'value' => DIALOG_NODE_EDIT_ADVANCED,
                'hidden' => TRUE
            ),
            'node_is_readonly' => array(
                'type' => F_CHECKBOX,
                'name' => 'node_is_readonly',
                'options' => array(1 => t('edit_node_is_readonly_label','admin')),
                'label' => t('edit_node_is_readonly','admin'),
                'title' => t('edit_node_is_readonly_title','admin'),
                ),
            'node_area_id' => array(
                'type' => F_LISTBOX,
                'name' => 'node_area_id',
                'value' => '',
                'label' => t('edit_node_area_id','admin'),
                'title' => t('edit_node_area_id_title','admin'),
                'options' => $this->get_options_area($node_id,$is_page),
                'viewonly' => $viewonly
                ),
            'node_link_image' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'node_link_image',
                'maxlength' => 240,
                'columns' => 50,
                'label' => t('edit_node_link_image','admin'),
                'title' => t('edit_node_link_image_title','admin'),
                'viewonly' => $viewonly
                ),
            'node_link_image_width' => array(
                'type' => F_INTEGER,
                'name' => 'node_link_image_width',
                'columns' => 10,
                'maxlength' => 10,
                'minvalue' => 0,
                'maxvalue' => 9999, // arbitrary but seems sane limit given current TFT's with 1920x1200 max
                'label' => t('edit_node_link_image_width','admin'),
                'title' => t('edit_node_link_image_width_title','admin'),
                'viewonly' => $viewonly
                ),
            'node_link_image_height' => array(
                'type' => F_INTEGER,
                'name' => 'node_link_image_height',
                'columns' => 10,
                'maxlength' => 10,
                'minvalue' => 0,
                'maxvalue' => 9999,
                'label' => t('edit_node_link_image_height','admin'),
                'title' => t('edit_node_link_image_height_title','admin'),
                'viewonly' => $viewonly
                )
            );

        if ($is_page) {
            $dialogdef['node_link_target'] = array(
                'type' => F_ALPHANUMERIC,
                'name' => 'node_link_target',
                'maxlength' => 240,
                'columns' => 50,
                'label' => t('edit_node_link_target','admin'),
                'title' => t('edit_node_link_target_title','admin'),
                'viewonly' => $viewonly
                );
            $dialogdef['node_link_href'] = array(
                'type' => F_ALPHANUMERIC,
                'name' => 'node_link_href',
                'maxlength' => 240,
                'columns' => 50,
                'label' => t('edit_node_link_href','admin'),
                'title' => t('edit_node_link_href_title','admin'),
                'viewonly' => $viewonly
                );
        }

        $dialogdef['node_is_hidden'] = array(
                'type' => F_CHECKBOX,
                'name' => 'node_is_hidden',
                'options' => array(1 => t('edit_node_is_hidden_label','admin')),
                'label' => t('edit_node_is_hidden','admin'),
                'title' => t('edit_node_is_hidden_title','admin'),
                'viewonly' => $viewonly
                );
        $dialogdef['node_embargo'] = array(
                'type' => F_DATETIME,
                'name' => 'node_embargo',
                'maxlength' => 30,
                'columns' => 30,
                'minvalue' => '1000-01-01 00:00:00',
                'maxvalue' => '9999-12-31 23:59:59',
                'label' => t('edit_node_embargo','admin'),
                'title' => t('edit_node_embargo_title','admin'),
                'viewonly' => $viewonly
                );
        $dialogdef['node_expiry'] = array(
                'type' => F_DATETIME,
                'name' => 'node_expiry',
                'maxlength' => 30,
                'columns' => 30,
                'minvalue' => '1000-01-01 00:00:00',
                'maxvalue' => '9999-12-31 23:59:59',
                'label' => t('edit_node_expiry','admin'),
                'title' => t('edit_node_expiry_title','admin'),
                'viewonly' => $viewonly
                );
        $dialogdef['node_style'] = array(
                'type' => F_ALPHANUMERIC,
                'name' => 'node_style',
                'maxlength' => 65432,
                'columns' => 70,
                'rows' => 10,
                'label' => t('edit_node_style_label','admin'),
                'title' => t('edit_node_style_title','admin'),
                'viewonly' => $viewonly
                );
        $dialogdef['button_save'] = dialog_buttondef(BUTTON_SAVE);
        $dialogdef['button_cancel'] = dialog_buttondef(BUTTON_CANCEL);
        return $dialogdef;
    } // get_dialogdef_edit_advanced_node()


    /** fill the node dialog with data from the database
     *
     * this fills a node dialog with data from the database.
     * The routine takes care of some data conversions, e.g. manipulating a boolean
     * TRUE/FALSE so it fits in a checkbox type of widget, etc.
     *
     * Note that the data is NOT specifically validated. This means that
     * a dialog _could_ contain invalid values even when the user doesn't
     * change anything. Or, to put it a different way: if the database contains
     * garbage, the garbage is simply presented to the user. If the user
     * subsequently tries to save the "garbage" the validation will catch her.
     *
     * This routine is able to fill the values for both the 'basic' and the
     * 'advanced' dialogs.
     *
     * @param array &$dialogdef the dialog definition
     * @param int $node_id the node that needs to be edited
     * @return void $dialogdef is filled with data from the database
     */
    function get_dialog_data_node(&$dialogdef,$node_id) {
        $record = $this->tree[$node_id]['record'];
        foreach ($dialogdef as $name => $item) {
            switch ($name) {
            case 'node_link_text':
                $dialogdef[$name]['value'] = $record['link_text'];
                break;
            case 'node_title':
                $dialogdef[$name]['value'] = $record['title'];
                break;
            case 'node_parent_id':
                $dialogdef[$name]['value'] = ($record['parent_id'] == $record['node_id']) ? 0 : $record['parent_id'];
                break;
            case 'node_module_id':
                $dialogdef[$name]['value'] = $record['module_id'];
                break;
            case 'node_sort_after_id':
                $dialogdef[$name]['value'] = $this->tree[$node_id]['prev_sibling_id']; // 1st yields 0 which is perfect
                break;
            case 'node_is_readonly':
                $dialogdef[$name]['value'] = (db_bool_is(TRUE,$record['is_readonly'])) ? '1' : '';
                break;
            case 'node_area_id':
                $dialogdef[$name]['value'] = $record['area_id'];
                break;
            case 'node_link_image':
                $dialogdef[$name]['value'] = $record['link_image'];
                break;
            case 'node_link_image_width':
                $dialogdef[$name]['value'] = $record['link_image_width'];
                break;
            case 'node_link_image_height':
                $dialogdef[$name]['value'] = $record['link_image_height'];
                break;
            case 'node_link_target':
                $dialogdef[$name]['value'] = $record['link_target'];
                break;
            case 'node_link_href':
                $dialogdef[$name]['value'] = $record['link_href'];
                break;
            case 'node_is_hidden':
                $dialogdef[$name]['value'] = (db_bool_is(TRUE,$record['is_hidden'])) ? '1' : '';
                break;
            case 'node_embargo':
                $dialogdef[$name]['value'] = $record['embargo'];
                break;
            case 'node_expiry':
                $dialogdef[$name]['value'] = $record['expiry'];
                break;
            case 'node_style':
                $dialogdef[$name]['value'] = $record['style'];
                break;
            }
        }
        return;
    } // get_dialog_data_node()


    /** construct an options list of possible parent sections
     *
     * this constructs an array suitable for a radio field or a listbox.
     * If the user has the privilege, an option 'add to toplevel' is added too.
     *
     * If $forbidden_id is not NULL, it identifies the subtree that should be
     * excluded from the result. If it were not excluded, the user might choose
     * a child section as the parent for a section, which would introduce endless
     * loops or circular references. Excluding the 'own' subtree prevents that.
     *
     * Note that the list is constructed using recursion: the actual work is
     * is done in the routine {@link get_options_parents_walk()}.
     *
     * Also note that if $forbidden_id is not NULL, we interpret this as a request
     * to generate a picklist of parents for that node. We make sure that we always
     * add the current parent node to the list. This way the only option for a parent
     * might be to keep the current one, which obviously should be one of the options.
     *
     * @param bool $is_page if TRUE check page permissions, else check section permissions
     * @param mixed $forbidden_id identifies the subtree to EXclude from the results or NULL for all sections
     * @return array picklist of available sections
     * @uses get_options_parents_walk()
     */
    function get_options_parents($is_page,$forbidden_id=NULL) {
        global $USER;
        $options = array();
        $permissions = ($is_page) ? PERMISSION_AREA_ADD_PAGE : PERMISSION_AREA_ADD_SECTION;
        if (($USER->has_area_permissions($permissions,$this->area_id)) ||
            ((!is_null($forbidden_id)) && ($this->tree[$forbidden_id]['parent_id'] == 0))) {
            $options[0] = array('option' => t('options_parents_at_toplevel','admin'), 
                                'title' => t('options_parents_at_toplevel_title','admin'), 
                                'class' => 'level0');
        }
        $this->get_options_parents_walk($options,$is_page,$this->tree[0]['first_child_id'],$forbidden_id);
        return $options;
    } // get_options_parents();


    /** workhorse for construction an options list of possible parent sections
     *
     * This routine is called recursively in order to construct a list of possible
     * parent sections in the same order as the main tree display (see {@link show_tree()}),
     * but excluding the subtree starting at $forbidden_id.
     *
     * The list of parents is collected in $options. This variable is passed by
     * reference to save memory and also to keep the parents in the correct order.
     *
     * Note that the options in the output array all have a parameter 'class' which
     * can be used to detect how deep the nesting is. This can be visualised via
     * wellchosen CSS-parameters, eg.
     * <code>
     * option.level0 { margin-left: 0px; }
     * option.level1 { margin-left: 20px; }
     * option.level2 { margin-left: 40px; }
     * ...
     * </code>
     * which provides the illusion of a tree-like structure, even in a listbox.
     *
     * The current parent of node $forbidden_id is always included in the list
     * of allowable parents because a node should be able to keep the current parent,
     * always.
     *
     * @param array &$options resulting array, output of this routine
     * @param bool $is_page distinction between page (TRUE) or section (FALSE)
     * @param int $node_id the subtree where we should start
     * @param int|null $forbidden_id if not NULL the subtree to skip
     * @return void results are stored in &$options.
     * @uses get_options_parents_walk()
     */
    function get_options_parents_walk(&$options,$is_page,$node_id,$forbidden_id) {
        static $level = 0;
        $class = 'level'.intval($level);

        while ($node_id > 0) {
            if (!($this->tree[$node_id]['is_page'])) {
                if ((empty($forbidden_id)) || ($node_id != $forbidden_id)) {
                    if (($this->permission_add_node($node_id,$is_page)) ||
                        ((!is_null($forbidden_id)) && ($this->tree[$forbidden_id]['parent_id'] == $node_id))) {
                        $link_text = $this->tree[$node_id]['record']['link_text'];
                        $title = $this->tree[$node_id]['record']['title'];
                        if (empty($link_text)) {
                            $link_text = $title;
                        }
                        $anode = array('{NODE}' => "$node_id ($link_text)");
                        $options[$node_id] = array(
                            'option' => t('options_parents_section','admin',$anode),
                            'title' => $title,
                            'class' => $class);
                    }
                    $subtree_id = $this->tree[$node_id]['first_child_id'];
                    if ($subtree_id > 0) {
                        if ($level >= MAXIMUM_ITERATIONS) { // silently ignore but log it
                            logger(__FUNCTION__.'(): too many levels in node '.$node_id,WLOG_DEBUG);
                        } else {
                            ++$level;
                            $this->get_options_parents_walk($options,$is_page,$subtree_id,$forbidden_id);
                            --$level;
                        }
                    }
                }
            }
            $node_id = $this->tree[$node_id]['next_sibling_id'];
        }
        return;
    } // get_options_parents_walk()


    /** generate a list of siblings in a particular (sub)section used to select/change sort order via a list box
     *
     * this constructs an (ordered) list of siblings of $node_id, but excluding
     * $node_id itself. Also, an option 'sort at the top of the list' is included.
     * This allows for selecting a sibling AFTER which $node_id should appear in the section.
     * The special value for 'before all others' or 'at the top of the list' is 0, because
     * that value cannot be used by a real node.
     *
     * @param int $node_id the node for which the list of siblings must be constructed
     * @return array ready for use as an options array in a listbox or radiobuttons
     * @uses $CFG
     */
    function get_options_sort_order($node_id) {
        global $CFG;
        $class = 'level0';
        $options = array();
        $options[0] = array('option' => t('options_sort_order_at_top','admin'),
                            'title'  => t('options_sort_order_at_top_title','admin'),
                            'class'  => $class);
        $parent_id = $this->tree[$node_id]['parent_id'];
        $next_id = $this->tree[$parent_id]['first_child_id'];
        while ($next_id != 0) {
            if ($next_id != $node_id) {
                $link_text = $this->tree[$next_id]['record']['link_text'];
                $title = $this->tree[$next_id]['record']['title'];
                $sort_order = $this->tree[$next_id]['record']['sort_order'];
                $is_page = ($this->tree[$next_id]['is_page']) ? TRUE : FALSE;
                if (empty($link_text)) {
                    $link_text = $title;
                }
                $anode = array('{NODE}' => "$next_id ($link_text)");
                $option_text = t(($is_page) ? 'options_sort_order_after_page' :
                                              'options_sort_order_after_section','admin',$anode);
                if ($CFG->debug) {
                    $title .= ' ('.$sort_order.')';
                }
                $options[$next_id] = array('option' => $option_text,'title' => $title,'class' => $class);
            }
            $next_id = $this->tree[$next_id]['next_sibling_id'];
        }
        return $options;
    } // get_options_sort_order()


    /** generate a list of areas for use in a dropdown list (for moving a node to another area)
     *
     * this creates an array containing a list of areas to which the user
     * is allowed to move a node. Permissions for 
     * moving a node is a combination of permissions for deleting a node from the
     * current area, and adding a node to the target area. The current area 
     * $this->area_id is always in the list, because even if the user isn't
     * allowed to move a node to somewhere else, she is at least allowed to leave
     * the node in the area it currently is in. Therefore the option for the
     * current area MUST be possible.
     *
     * We sepcifically check for these permissions:
     * PERMISSION_AREA_ADD_PAGE or PERMISSION_AREA_ADD_SECTION
     * and not PERMISSION_NODE_ADD_PAGE or PERMISSION_NODE_ADD_SECTION
     * because the target of the move is always the top level, and not some
     * (sub)section. 
     *
     * @param int $node_id the node for which we are building this picklist
     * @param bool $is_page TRUE if this concerns a page, FALSE for a section
     * @return array ready for use as an options array in a listbox or radiobuttons
     */
    function get_options_area($node_id,$is_page) {
        global $USER;
        $options = array();
        $can_drop_current = $this->permission_delete_node($node_id,$is_page);
        $permissions = ($is_page) ? PERMISSION_AREA_ADD_PAGE : PERMISSION_AREA_ADD_SECTION;
        foreach($this->areas as $area_id => $area) {
            if (($this->area_id == $area_id) ||
                (($can_drop_current) && ($USER->has_area_permissions($permissions,$area_id)))) {
                $is_active = (db_bool_is(TRUE,$area['is_active']));
                $a_area = array('{AREA}' => $area_id,'{AREANAME}' => $area['title']);
                if (db_bool_is(TRUE,$area['is_private'])) {
                    $option_text = t(($is_active) ? 'options_private_area' :
                                                    'options_private_area_inactive','admin',$a_area);
                } else {
                    $option_text = t(($is_active) ? 'options_public_area' :
                                                    'options_public_area_inactive','admin',$a_area);
                }
                $options[$area_id] = array('option' => $option_text,'title' => $area['title'], 'class' => 'level0');
            }
        }
        return $options;
    } // get_options_area()


    /** fetch a list of available modules for inclusion on a page
     *
     * this retrieves a list of modules that can be used as a list of options in
     * a listbox or radiobuttons. Only the active modules are considered.
     * The names of the modules that are displayed in the list are translated
     * (retrieved from the modules language files). The list is ordered by
     * that translated module name.
     *
     *
     * @return array ready for use as an options array in a listbox or radiobuttons
     */
    function get_options_modules() {

        // 1 - get raw list of active modules
        $records = $this->get_module_records();
        $options = array();
        if (($records === FALSE) || (empty($records))) {
            logger('pagemanager: weird, no active modules? must be configuration error');
            return $options;
        }

        // 2 - prepare a raw list of modules with translated title/description
        $options_order = array(); // helper-array for easy sorting by $title
        foreach ($records as $module_id => $module) {
            $module_name = $module['name'];
            $title = t('title','m_'.$module_name);
            $options_order[$module_id] = $title;
            $records[$module_id]['title'] = "$title ($module_name)";
            $records[$module_id]['description'] = t('description','m_'.$module_name);
        }
        asort($options_order);

        // 3 - construct a sorted list of modules from the raw list and helper array
        foreach($options_order as $module_id => $title) {
            $options[$module_id] = array('option' => $records[$module_id]['title'],
                                         'title' => $records[$module_id]['description']);
        }
        unset($options_order);
        return $options;
    } // get_options_modules()


    /** add a message to message queue of 0 or more alerts
     *
     * this adds $alert_message to the message buffers of 0 or more alert accounts
     * The alerts that qualify to receive this addition via the alerts_areas_nodes table.
     * The logic in that table is as follows:
     *  - the area_id must match the area_id(s) (specified in $areas) OR 
     *    it must be 0 which acts as a wildcard for ALL areas
     *  - the node_id must match the node_id(s) specified in $nodes) OR
     *    it must be 0 which acts as a wildcard for ALL nodes
     * Also the account must be active and the flag for the area/node-combination
     * must be TRUE.
     *
     * As a rule this routine is called with a single area_id in $areas and
     * a collection of node_id's in $nodes. The nodes follow the path up through
     * the tree, in order to alert accounts that are only watching a section at
     * a higher level.
     * 
     * Example:
     * If user 'webmaster' adds new page, say 34, to subsection 8 in
     * section 4 in area 1, you get something like this:
     *
     *     queue_area_node_alert(1,array(8,4,34),'node 34 added','webmaster');
     *
     * The effect will be that all accounts with the following combinations of
     * area A and node N have the message added to their buffers:
     * A=0, N=1 - qualifies for all nodes in all areas
     * A=1, N=0 - qualifies for all nodes in area 1
     * A=1, N=4 - qualifies for node 4 in area 1
     * A=1, N=8 - qualifies for node 8 in area 1
     *
     * It is very well possible that no message is added at all if there is no
     * alert account watching the specified area and node (using wildcards or
     * otherwise).
     *
     * Near the end of this routine, we check the queue with pending messages,
     * and perhaps send out a few alerts. The number of messages that can be
     * sent from here is limited; we don't want to steal too much time from an
     * unsuspecting user. It is the task of cron.php to take care of eventually
     * sending the queued messages. However, sending only a few messages won't
     * be noticed. I hope.
     *
     * Note that this routine adds a timestamp to the message and, if it is 
     * specified, the name of the user.
     *
     * Also note that the messages are added to the buffer with the last message
     * at the top, it means that the receiver will travel back in time reading
     * the collection of messages. This is based on the assumption that the latest
     * messages sometimes override a previous message and therefore should be
     * read first.
     *
     * @param mixed $areas an array or a single int identifying the area(s) of interest
     * @param mixed $nodes an array or a single int identifying the node(s) of interest
     * @param string $message the message to add to the buffer of qualifying alert accounts
     * @param string $username (optional) the name of the user that initiated the action
     * @return void
     * @uses $DB;
     */
    function queue_area_node_alert($areas,$nodes,$alert_message,$username='') {
        global $DB;

        // 0 -- massage the message, add a timestamp, optional username and an extra empty line
        $message = sprintf("%s%s\n%s\n\n",strftime('%Y-%m-%d %T'),
                                          (empty($username)) ? '' : ' ('.$username.')',
                                          $alert_message);

        // 1 -- construct the area part of the statement
        // example where-clause (part 1/3): ((area_id = 0) OR (area_id = 1))
        $where_clause = '';
        if (!empty($areas)) {
            $where_clause_area = '(n.area_id = 0)'; // area_id = 0 means 'any area' in this context
            if (is_array($areas)) {
                foreach ($areas as $area_id) {
                    $where_clause_area .= sprintf(' OR (n.area_id = %d)',intval($area_id));
                }
            } else {
                $where_clause_area .= sprintf(' OR (n.area_id = %d)',intval($areas));
            }
            $where_clause .= '('.$where_clause_area.') AND ';
        }

        // 2 -- construct the node part of the statement
        // example where-clause (part 2/3): ((node_id = 0) OR (node_id = 4) OR (node_id = 8) OR (node_id = 34))
        if (!empty($nodes)) {
            $where_clause_node = '(n.node_id = 0)'; // node_id = 0 means 'any node' in this context
            if (is_array($nodes)) {
                foreach ($nodes as $node_id) {
                    $where_clause_node .= sprintf(' OR (n.node_id = %d)',intval($node_id));
                }
            } else {
                $where_clause_node .= sprintf(' OR (n.node_id = %d)',intval($nodes));
            }
            $where_clause .= '('.$where_clause_node.') AND ';
        }

        // 3 -- only send msgs to active alerts
        // example where-clause (part 3/3): combine previous two parts with check for active alert accounts/flags
        $where_clause .= '(a.is_active) AND (n.flag)';

        // 4 -- construct complete statement
        // Note that we also are constructing the update of the message field manually, so
        // we MUST take care of proper escaping and quoting. Also, the trick with $DB->concat()
        // complicates this SQL-statement, but alas this is necessary due to the non-standard way
        // MySQL interprets the string concatenation operator '||'. Aaarrrghhhhhh
        // (see http://troels.arvin.dk/db/rdbms/#functions-concat)
        $sql = sprintf('UPDATE %salerts AS a INNER JOIN %salerts_areas_nodes AS n ON a.alert_id = n.alert_id '.
                       'SET a.message_buffer = %s, a.messages = a.messages + 1 '.
                       'WHERE %s',
                       $DB->prefix,
                       $DB->prefix,
                       $DB->concat(db_escape_and_quote($message),'a.message_buffer'),
                       $where_clause);
        $retval = $DB->exec($sql);
        if ($retval === FALSE) {
            logger(__CLASS__.": error queueing alerts: '".db_errormessage()."' with sql = $sql");
        } else {
            logger(__CLASS__.": number of alerts queued: $retval");
            logger(__FUNCTION__."(): sql = ".$sql,WLOG_DEBUG);
        }

        // Even if we did not add a message ourselves, we can 'steal' some time
        // of the current user and see if there are mails that need to be sent out.
        cron_send_queued_alerts(2); // limit processing to 2 messages at this time
    } // queue_area_node_alert()


    /** attempt to lock all node records in a subtree
     *
     * this recursively walks the subtree starting at $node_id and attempts to
     *  - lock every node in the subtree, AND
     *  - write the new area_id in an auxiliary field of every node in the subtree
     *
     * With at least two trips to the database (at least one for the lock and
     * another one for writing the auxiliary field) per node, this is an expensive
     * routine. Maybe it is possible to combine locking and writing the auxiliary
     * field. However, in order to keep things readable I decided against that.
     *
     * This routine returns FALSE if any of the nodes in the subtree could NOT be
     * locked. If each and every node in the subtree is successfully locked, TRUE is
     * returned.
     *
     * Note that all these locks are reset/released the moment the actual move is
     * done, by resetting both the locked_by field and the area_id field. That may
     * hurt readability too, but less than combining lock + setting auxiliary field.
     * See {@link save_node_new_area_mass_move()} for more information.
     *
     * @param int $node_id the node which we are going to move to $new_area_id
     * @param int $new_area_id the area to which we want to move the subtree $node_id
     * @return bool FALSE on error, TRUE on success
     * @uses lock_records_subtree()
     */
    function lock_records_subtree($node_id,$new_area_id) {
        static $level = 0;
        $lockinfo = array();
        while ($node_id != 0) {
            $is_page = $this->tree[$node_id]['is_page'];
            if (!lock_record_node($node_id,$lockinfo)) {
                $msg = message_from_lockinfo($lockinfo,$node_id,$is_page);
                $this->output->add_message($msg);
                logger(__FUNCTION__."(): cannot lock node $node_id in subtree: $msg",WLOG_DEBUG);
                return FALSE;
            } elseif (db_update('nodes',array('auxiliary' => $new_area_id),array('node_id' => $node_id)) === FALSE) {
                logger(__FUNCTION__ ."(): cannot write new area_id $new_area_id to auxiliary field in node $node_id: ".
                                          db_errormessage(),WLOG_DEBUG);
                return FALSE;
            }
            if ((!$is_page) && ($this->tree[$node_id]['first_child_id'] != 0)) {
                if ($level >= MAXIMUM_ITERATIONS) {
                    $this->output->add_message(t('too_many_levels','admin',array('{NODE}' => strval($node_id))));
                    logger(__FUNCTION__.'(): too many levels in node '.$node_id,WLOG_DEBUG);
                    return FALSE;
                } else {
                    ++$level;
                    $retval = $this->lock_records_subtree($this->tree[$node_id]['first_child_id'],$new_area_id);
                    --$level;
                    if ($retval === FALSE) {
                        return FALSE;
                    }
                }
            }
            $node_id = $this->tree[$node_id]['next_sibling_id'];
        }
        return TRUE;
    } // lock_records_subtree()


    // ==================================================================
    // ======================== UTILITY ROUTINES ========================
    // ==================================================================

    /** does the user have the privilege to add a node, any node to an area?
     *
     * this routine returns TRUE if the current user has permission to
     * add at least one node to the current area. This information is used to
     * show or suppress the 'add a page' and 'add a section' links.
     *
     * Note that pages and sections are treated separately; if a user is
     * allowed to add a page it doesn't necessarily mean that she is allowed
     * to add a section too.
     *
     * Strategy: we first check the area-level (and implicit site-level)
     * permissions to add a node, anywhere in an area including at the toplevel.
     * If that doesn't work, we check for permissions to add a node to
     * an existing section at the node level (and implicit at the area and
     * site level too). If that doesn't work, we return FALSE.
     *
     * Note that it is enough to stop the search at the first hit: we need
     * only 1 hit for 'any', not all of them.
     *
     * @param bool $is_page selects either page or section permissions
     * @return bool TRUE if user can at least add 1 section/page, FALSE otherwise
     * @uses $USER
     */
    function permission_add_any_node($is_page) {
        global $USER;
        $permissions = ($is_page) ? PERMISSION_AREA_ADD_PAGE : PERMISSION_AREA_ADD_SECTION;
        if ($USER->has_area_permissions($permissions,$this->area_id)) {
            return TRUE;
        }
        $permissions = ($is_page) ? PERMISSION_NODE_ADD_PAGE : PERMISSION_NODE_ADD_SECTION;
        foreach($this->tree as $node_id => $node) {
            if ((!($node['is_page'])) && ($node_id != 0)) {
                if ($USER->has_node_permissions($permissions,$this->area_id,$node_id)) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    } // permission_add_any_node()


    /** does the user have the privilege to add a node the area or a section?
     *
     * this checks for permission to add a page or a section to the area at the
     * toplevel or to the section $node_id. If access is denied initially, the
     * upper sections are tested for the requested permission. I dubbed this
     * cascading permissions (if a section allows for adding a page, any subsections
     * inherit that permission). This routine is protected from endless loops
     * by recursing at most MAXIMUM_ITERATIONS levels.
     *
     * @param int $section_id is the section to examine or 0 for the area top level
     * @param bool $is_page selects either page or section permissions
     * @return bool TRUE if user can add section/page, FALSE otherwise
     * @uses $USER
     */
    function permission_add_node($section_id,$is_page) {
        global $USER;
        static $level = 0;

        // 1 -- try area-wide permissions
        $permissions = ($is_page) ? PERMISSION_AREA_ADD_PAGE : PERMISSION_AREA_ADD_SECTION;
        if ($USER->has_area_permissions($permissions,$this->area_id)) {
            return TRUE;
        }
        // 2 -- try section-based permissions
        $permissions = ($is_page) ? PERMISSION_NODE_ADD_PAGE : PERMISSION_NODE_ADD_SECTION;
        if ($USER->has_node_permissions($permissions,$this->area_id,$section_id)) {
            return TRUE;
        }
        // 3 -- if not this section, try the parent section (if any); cascading permissions
        $retval = FALSE;
        $parent_id = $this->tree[$section_id]['parent_id'];
        if ($parent_id != 0) {
            if ($level >= MAXIMUM_ITERATIONS) {
                logger(__FUNCTION__.'(): too many levels in node '.$section_id,WLOG_DEBUG);
                $retval = FALSE;
            } else {
                ++$level;
                $retval = $this->permission_add_node($parent_id,$is_page);
                --$level;
            }
        }
        // 4 -- done
        return $retval;
    } // permission_add_node()


    /** does the user have the privilege to edit node properties?
     *
     * this checks the edit permissions for the specified node.
     * If none are found initially, we check out the permissions of the
     * parent section. If the user allowed to add a page/section in the
     * parent section, we assume or imply that the user also has edit
     * permissions if she also has edit permissions for the parent.
     * even though the exact permission bits are not set
     * for this particular new node. IOW: if a user is able to add
     * a page/section it would be illogical not to be able to edit the
     * new page/section. However, if the edit-permissions are not 
     * area-wide, there is no way you can add permissions to a particular
     * node before it exists. (Can't do that in the account manager).
     *
     * Note that a node can also have the readonly attribute set. This
     * is more or less a tool to prevent accidental changes to a node's
     * properties: a user can easy reset the readonly flag and change the
     * node anyway. However, it requires two steps and hence at least _some_
     * thinking. Bottom line: we only look at the 'real' permissions here,
     * and not the readonly flag. (Even better: edit privilege is required
     * to reset the readonly flag so using that flag as extra permission would
     * yield pages completely uneditable).
     *
     * This routine is also used to check for content edit permissions.
     * This is only possible for pages (not sections). By default this routine
     * checks the regular permissions (edit properties/ edit advanced properties).
     *
     * @param int $node_id is the node to examine
     * @param bool $is_page TRUE means we look at page permissions, not section
     * @param bool $check_content TRUE means check edit content, else edit plain
     * @return bool TRUE if user can edit node, FALSE otherwise
     * @uses $USER
     */
    function permission_edit_node($node_id,$is_page,$check_content=FALSE) {
        global $USER;
        static $level = 0;

        // 1 -- check out which edit permissions for this node
        if ($check_content) {
            $permission = PERMISSION_NODE_EDIT_CONTENT;
        } else {
            $permission = ($is_page) ? PERMISSION_NODE_EDIT_PAGE : PERMISSION_NODE_EDIT_SECTION;
        }
        if ($USER->has_node_permissions($permission,$this->area_id,$node_id)) {
            return TRUE;
        }
        // 2 -- still here? check out cascading permissions
        $retval = FALSE;
        $parent_id = $this->tree[$node_id]['parent_id'];
        if (($parent_id != 0) && ($this->permission_add_node($parent_id,$is_page))) {
            if ($level >= MAXIMUM_ITERATIONS) {
                logger(__FUNCTION__.'(): too many levels in node '.$node_id,WLOG_DEBUG);
                $retval = FALSE;
            } else {
                ++$level;
                $retval = $this->permission_edit_node($parent_id,$is_page,$check_content);
                --$level;
            }
        }
        return $retval;
    } // permission_edit_node()


    /** does the user have the privilege to edit node content?
     *
     * this is a wrapper around routine {@link permission_edit_node()}.
     * We force is_page and check_content to TRUE.
     *
     * @param int $node_id is the node to examine
     * @return bool TRUE if user can edit node content, FALSE otherwise
     */
    function permission_edit_node_content($node_id) {
        return $this->permission_edit_node($node_id,TRUE,TRUE);
    } // permission_edit_node_content()


    /** does the user have the privilege to make node $node_id the default?
     *
     * if a user has edit permission for the new default node and also in the
     * existing default node (if any), the user is allowed to set the default
     * to node $node_id. Note that once again we use cascading permissions.
     * (See also {@link permission_edit_node()}).
     *
     * @param bool $node_id is the tentative new default
     * @return bool TRUE if user has enough permissions, otherwise FALSE
     */
    function permission_set_default($node_id) {
        $retval = FALSE;
        if ($this->permission_edit_node($node_id,$this->tree[$node_id]['is_page'])) {
            if (($home_id = $this->calc_home_id($node_id)) !== FALSE) {
                $retval = $this->permission_edit_node($home_id,$this->tree[$home_id]['is_page']);
            } else {
                $retval = TRUE;
            }
        }
        return $retval;
    } // permission_set_default()


    /** does the user have the privilege to delete a node from the area?
     *
     * @param int $node_id is the node to delete
     * @return bool TRUE if user can delete node, FALSE otherwise
     * @todo we should also take the readonly flag into account
     *       (or should we?) when determining delete permissions
     * @uses $USER
     */
    function permission_delete_node($node_id,$is_page) {
        global $USER;
        static $level = 0;

        // 1 -- try area-wide permissions
        $permission = ($is_page) ? PERMISSION_AREA_DROP_PAGE : PERMISSION_AREA_DROP_SECTION;
        if ($USER->has_area_permissions($permission,$this->area_id)) {
            return TRUE;
        }
        // 2 -- try node-level permissions but not for top-level pages/sections
        $parent_id = $this->tree[$node_id]['parent_id'];
        if ($parent_id == 0) { // delete from toplevel requires PERMISSION_AREA_DROP_xxx which this user has NOT
            return FALSE;
        } else {
            $permission = ($is_page) ? PERMISSION_NODE_DROP_PAGE : PERMISSION_NODE_DROP_SECTION;
            if ($USER->has_node_permissions($permission,$this->area_id,$node_id)) {
                return TRUE;
            }
        }
        // 3 -- genuine test for deletion from an existing section (not top level)
        $retval = FALSE;
        if (($parent_id != 0) && ($this->permission_add_node($parent_id,$is_page))) {
            if ($level >= MAXIMUM_ITERATIONS) {
                logger(__FUNCTION__.'(): too many levels in node '.$node_id,WLOG_DEBUG);
                $retval = FALSE;
            } else {
                ++$level;
                $retval = $this->permission_delete_node($parent_id,$is_page);
                --$level;
            }
        }
        return $retval;
    } // permission_delete_node()


    /** shorthand for determing whether a section is opened or closed
     *
     * @param int $section_id the section of interest
     * @return bool TRUE if the node is 'open', FALSE otherwise
     */
    function section_is_open($section_id) {
        if ($_SESSION['tree_mode'] == TREE_VIEW_MAXIMAL) {
            return TRUE;
        } elseif ($_SESSION['tree_mode'] == TREE_VIEW_MINIMAL) {
            return FALSE;
        } else {
            if (isset($_SESSION['expanded_nodes'][$section_id])) {
                return $_SESSION['expanded_nodes'][$section_id];
            } else {
                return FALSE;
            }
        }
    } // section_is_open()


    /** retrieve a list of all available module records
     *
     * this returns a list of active module-records or FALSE if none are are available
     * The list is cached via a static variable so we don't have to go to the
     * database more than once for this.
     * Note that the returned array is keyed with module_id.
     *
     * @return array|bool FALSE if no modules available or an array with module-records
     */
    function get_module_records() {
        static $records = NULL;
        if ($records === NULL) {
            $tablename = 'modules';
            $fields = '*';
            $where = array('is_active' => TRUE);
            $order = array('module_id');
            $records = db_select_all_records($tablename,$fields,$where,$order,'module_id');
        }
        return $records;
    } // get_module_records()


    /** shorthand for constructing a readable page/section name with id, name and title
     *
     * @param int $node_id get the full name of this node
     * @return string constructed full name of page/section
     */
    function node_full_name($node_id) {
        return sprintf('%d %s (%s)',$node_id,
                                    $this->tree[$node_id]['record']['link_text'],
                                    $this->tree[$node_id]['record']['title']);
    } // node_full_name()


    /** shorthand to determine whether the number of levels below section $node_id is greater than one
     *
     * @param int $node_id the section to check for grandchilderen
     * @return bool TRUE if section $node_id has a non-empty subsection, FALSE otherwise
     */
    function node_has_grandchilderen($node_id) {
        if ($this->tree[$node_id]['is_page']) { // by definition a page cannot have any descendants
            return FALSE;
        }
        // check out the childeren of $node
        $next_id = $this->tree[$node_id]['first_child_id'];
        while ($next_id != 0) {
            if ((!$this->tree[$next_id]['is_page']) && ($this->tree[$next_id]['first_child_id'] != 0)) {
                return TRUE;
            }
            $next_id = $this->tree[$next_id]['next_sibling_id'];
        }
        return FALSE;
    } // node_has_grandchilderen()


    /** get an array with all ids of ancestors of node_id and node_id itself
     *
     * note that the order of nodes is from top to bottom
     *
     * @param int $node_id start at the youngest in the family
     * @return array an array with all ancestor ids, from top until node_id
     */
    function get_node_id_and_ancestors($node_id) {
        $nodes = array();
        while ($node_id != 0) {
            $nodes = array_merge(array($node_id),$nodes);
            $node_id = $this->tree[$node_id]['parent_id'];
        }
        return $nodes;
    } // get_node_id_and_ancestors()


    /** construct a readable message from the lockinfo array
     *
     * if an attempt to lock a record fails (see {@link lock_record_node()}), the array
     * $lockinfo is filled with information about the user that has locked the record.
     * The following information is available:
     * 
     *  - 'user_id': the numerical user_id of the user holding the lock
     *  - 'username': the userid of that user
     *  - 'full_name': the full name of that user
     *  - 'user_information': the IP-address from where that user is calling
     *  - 'ctime': the date/time that user logged in (c=create)
     *  - 'atime': the date/time that user last accessed the system (a=access)
     *  - 'ltime': the date/time that user actually locked the record (l=lock)
     *
     * This routine tries to construct a more or less readable message which informs this user
     * here about that other user holding the lock.
     *
     * @param array $lockinfo contains information about another user that has obtained a record lock
     * @param int $node_id the node that is locked
     * @return string a message constructed from the lockinfo
     */
    function message_from_lockinfo($lockinfo,$node_id,$is_page) {
        $a = array('{FULL_NAME}' => $lockinfo['full_name'],
                   '{USERNAME}' => $lockinfo['username'],
                   '{IP_ADDR}' => $lockinfo['user_information'],
                   '{LOGIN_TIME}' => $lockinfo['ctime'],
                   '{LOCK_TIME}' => $lockinfo['ltime'],
                   '{NODE}' => strval($node_id));
        return t(($is_page) ? 'page_is_locked_by' : 'section_is_locked_by','admin',$a);
    } // message_from_lockinfo()


    /** calculate a new sort order and at the same time make room for a node
     *
     * this is used to calculate a new sort order number for a node that will be added
     * to section $parent_id in area $area_id. Note that this could be another
     * area than the current working area. The reference to the tree is necessary;
     * we can't simply use $this->tree and $this->area_id.
     *
     * Depending on the configuration flag $CFG->pagemanager_at_end the node is added at the end
     * of the section or at the beginning. In the latter case, the new sort order number
     * is always 10 and all the existing nodes are renumbered in such a way that
     * the second node in the section (originally the first one) gets sort order 20.
     * By not using consecutive numbers it is possible to 'insert' nodes without touching anything.
     * This is not used but it does no harm to have a sort order in steps of 10 instead of 1.
     * (I think the database doesn't care much when executing/interpreting the ORDER BY clause).
     *
     * Note that this routine not only calculates a sort order but it also manipulates the
     * database and moves other nodes in the section around in order to make room.
     *
     * @param array &$tree reference to the tree in area $area_id
     * @param int $area_id the area to look at
     * @param int $parent_id the section where we need to make room (where a node is added/inserted)
     * @return int the sort order for the new node AND maybe changed sort orders amongst the childeren of $parent_id
     * @uses $DB
     * @uses $CFG
     */
    function calculate_new_sort_order(&$tree,$area_id,$parent_id) {
        global $DB,$CFG;
        if ((isset($CFG->pagemanager_at_end)) && ($CFG->pagemanager_at_end)) {
            // calculate the new sort order, by default it is 10 larger than the last child of the parent
            $sort_order = 0;
            $next_node_id = $tree[$parent_id]['first_child_id'];
            while ($next_node_id != 0) {
                $sort_order = intval($tree[$next_node_id]['record']['sort_order']);
                $next_node_id = $tree[$next_node_id]['next_sibling_id'];
            }
            $sort_order += 10; // add the new node at the end, set sort order 10 higher than the highest
        } else {
            // insert at the beginning: we use the sort order of the first node for the new node,
            // AND we bump the rest
            $next_node_id = $tree[$parent_id]['first_child_id'];
            $sort_order = 10;
            if ($next_node_id != 0) { // bump childeren so the next will start at 20 from now on
                $delta = 20 - intval($tree[$next_node_id]['record']['sort_order']);
                $sql = sprintf('UPDATE %snodes '.
                               'SET sort_order = sort_order + %d '.
                               'WHERE (area_id = %d) AND %s',
                               $DB->prefix,
                               $delta,
                               $area_id,
                               ($parent_id == 0) ? '(node_id = parent_id)' :
                                           sprintf('(node_id <> parent_id) AND (parent_id = %d)',$parent_id));
                $DB->exec($sql);
            }
        }
        return $sort_order;
    } // calculate_new_sort_order()


    /** calculate an updated sort order and also make space in the section for moving the node around
     *
     * this calculates a new sort order for node node_id; the effect should be that node_id will
     * sort AFTER node after_id. If after_id is 0 then node_id should become the first in the section.
     *
     * Note that this routine not only calculates a sort order but it also manipulates the
     * database and moves other nodes in the section around in order to make room.
     *
     * There are several different cases possible:
     * a. $after_id == 0
     * b. sort_order($after_id) < sort_order($node_id)
     * c. sort_order($node_id) < sort_order($after_id)
     * d. $after_id is the last node in this section
     * e. $node_id == $after_id
     *
     * Case e. should not happen but if it did it would yield a no-op.
     * Case d is very similar to case c, so much even that both cases can be combined to just one.
     *
     * Strategy for case a.
     * $old_sort_order = sort_order($node_id);
     * $new_sort_order = sort_order(first_child(parent_section($node_id)))
     * $delta = $old_sort_order - sort_order(prev($node_id))
     * SET $sort_order += $delta WHERE $new_sort_order <= sort_order(node) <= $old_sort_order
     *
     * In other words: node_id gets the sort_order value from the first node in the section,
     * all nodes from the first upto position where node_id was originally move 'up' in such
     * a way that the last in that range will end up with the sort order that node_id had
     * originally.
     *
     *
     * Strategy for case b.
     * $old_sort_order = sort_order($node_id)
     * $new_sort_order = sort_order(next($after_id))
     * $delta = $old_sort_order - sort_order(prev($node_id))
     * SET $sort_order += $delta WHERE $new_sort_order <= sort_order(node) <= $old_sort_order
     *
     * Note that a and b are also quite similar.
     *
     * Strategy for case c. (and d.) 
     * $old_sort_order = sort_order($node_id)
     * $new_sort_order = sort_order($after_id)
     * $delta = $old_sort_order - sort_order(next($node_id)) (note that this is a negative value)
     * SET $sort_order += $delta WHERE $old_sort_order <= sort_order(node) <= $new_sort_order
     *
     * By mass-updating the other nodes, we hopefully don't disturb the other nodes, even
     * while they might be locked. So there, the lock on the node is not absolute, we will
     * change the record behind the back of another user holding a lock. On the other hand:
     * messing up the sort order is less messy than messing with the actual content of a node.
     * I'll take the risk. Worst case is that two processes will both update the sort order,
     * perhaps yielding two nodes with the same sort_order value. Oh well, so be it. (There is
     * this law by Pareto, something about 80 - 20. Mmmm...)
     *
     * @param int $after_id the node AFTER which $node_id should be sorted (0 means: first in the section)
     * @return int the new sort_order value for $node_id + other nodes in the section may have been moved
     * @uses $DB
     * @todo Clean up this code, it is very hairy
     */
    function calculate_updated_sort_order($node_id,$after_id) {
        global $DB;
        $old_sort_order = intval($this->tree[$node_id]['record']['sort_order']);
        $parent_id = $this->tree[$node_id]['parent_id'];

        // 0 -- sanity check
        if (($after_id != 0) && ($parent_id != $this->tree[$after_id]['parent_id'])) {
            // weird: these nodes are in different sections. Simply keep existing sort order value
            logger(__FUNCTION__."(): cannot change sort order: '$node_id' and '$after_id' are not siblings",WLOG_DEBUG);
            return $old_sort_order;
        }

        // 1 -- case a or b?
        if  (($after_id == 0) || ($this->tree[$after_id]['record']['sort_order'] < $old_sort_order)) {
            if ($after_id == 0) { // case a
                $first_child_id = $this->tree[$parent_id]['first_child_id'];
                $new_sort_order = intval($this->tree[$first_child_id]['record']['sort_order']);
            } else { // case b
                $next_after_id = $this->tree[$after_id]['next_sibling_id'];
                $new_sort_order = intval($this->tree[$next_after_id]['record']['sort_order']);
            }
            $prev_id = $this->tree[$node_id]['prev_sibling_id'];
            if ($prev_id != 0) {
                $delta = $old_sort_order - intval($this->tree[$prev_id]['record']['sort_order']);
            } else { 
                $delta = $old_sort_order; // shouldn't happen: $node_id IS the first node and should become the first
            }
            $sql = sprintf('UPDATE %snodes '.
                           'SET sort_order = sort_order + %d '.
                           'WHERE (area_id = %d) AND (%d <= sort_order) AND (sort_order <= %d)',
                           $DB->prefix,
                           $delta,
                           $this->area_id, $new_sort_order,$old_sort_order);
            if ($parent_id == 0) {
                $sql .= ' AND (parent_id = node_id)'; // limit to only toplevel nodes
            } else {
                // at 1 level from the top the section has parent_id = node_id = $parent_id and all siblings also
                // have parent_id = $parent_id. We don't want to move the parent itself so we have to exclude it
                $sql .= sprintf(' AND (parent_id = %d) AND (node_id <> %d)',$parent_id,$parent_id);
            }
            $DB->exec($sql);
            return $new_sort_order;
        }

        // 2 -- case c or d?
        if ($old_sort_order < $this->tree[$after_id]['record']['sort_order']) { // case c. or d.
            $new_sort_order = intval($this->tree[$after_id]['record']['sort_order']);
            $next_id = $this->tree[$node_id]['next_sibling_id'];
            if ($next_id != 0) {
                $delta = $old_sort_order - intval($this->tree[$next_id]['record']['sort_order']);
            } else { 
                $delta = $old_sort_order; // shouldn't happen: $node_id is NOT the last node
            }
            $sql = sprintf('UPDATE %snodes '.
                           'SET sort_order = sort_order + %d '.
                           'WHERE (area_id = %d) AND (%d <= sort_order) AND (sort_order <= %d)',
                           $DB->prefix,
                           $delta,
                           $this->area_id,$old_sort_order,$new_sort_order);
            if ($parent_id == 0) {
                $sql .= ' AND (parent_id = node_id)'; // limit to only toplevel nodes
            } else {
                // at 1 level from the top the section has parent_id = node_id = $parent_id and all siblings also
                // have parent_id = $parent_id. We don't want to move the parent itself so we have to exclude it
                $sql .= sprintf(' AND (parent_id = %d) AND (node_id <> %d)',$parent_id,$parent_id);
            }
            $DB->exec($sql);
            return $new_sort_order;
        } 

        // 3 -- nothing to do, keep the original sort_order and don't go to the database at all
        return $old_sort_order;
    } // calculate_updated_sort_order()


    /** calculate the current default node on this level
     *
     * this tries to find a sibling of the node $node_id
     * that has the flag 'is_default' set to TRUE
     *
     * @param int $node_id the node of interest
     * @param bool|int FALSE if no default node found, the default node_id otherwise
     */
    function calc_home_id($node_id) {
        $parent_id = $this->tree[$node_id]['parent_id'];
        $next_id = $this->tree[$parent_id]['first_child_id'];
        while ($next_id != 0) {
            if ($this->tree[$next_id]['is_default']) {
                return intval($next_id);
            }
            $next_id = $this->tree[$next_id]['next_sibling_id'];
        }
        return FALSE;
    } // calc_home_id()


    /** construct $this->tree for future reference
     *
     * this constructs the tree of the area $area_id so all other
     * routines can simply use that tree instead of passing it around
     * again and again via function arguments.
     *
     * @param int $area_id indicates which area to buffer if not already buffered
     * @param bool $force re-read of the tree for area $area_id
     * @return void a copy of the area tree is cached in $this->tree
     */
    function build_cached_tree($area_id,$force=FALSE) {
        if (($this->area_id !== $area_id) || ($force)) {
            $this->area_id = $area_id;
            $this->tree = tree_build($this->area_id,$force);
        }
    } // build_cached_tree()



    // ==================================================================
    // ======================== MODULE INTERFACE ========================
    // ==================================================================


    /** inform module $module_id that it is no longer linked to page $node_id
     *
     * this routine tells module $module_id that it is
     * no longer associated with node $node_id in area $area_id.
     *
     * This is done by 
     * a. loading the module's administrative interface (the admin-script file), and
     * b. calling the function <modulename>_disconnect()
     *
     * If something goes wrong (e.g. no permissions, no module found, non-existing admin-script,
     * undefined function <modulename>_disconnect()) FALSE is returned, otherwise the return
     * value of function <modulename>_disconnect() is returned.
     *
     * @param int $area_id the area where $node_id resides
     * @param int $node_id the node from which the module is disconnected
     * @param int $module_id the module that will be disconnected from node $node_id
     * @return bool FALSE on failure, otherwise the result of <modulename>_disconnect()
     * @todo should we pass the area_id at all? What happens when a node is moved
     *       to another area without informing the module? Questions, questions, questions...
     */
    function module_disconnect($area_id, $node_id, $module_id) {

        if (($module = $this->module_load_admin($module_id)) === FALSE) {
            return FALSE;
        }
        $module_disconnect = $module['name'].'_disconnect';
        if (!function_exists($module_disconnect)) {
            logger(__FUNCTION__."(): weird: function '$module_disconnect' does not exist. Huh?",WLOG_DEBUG);
            return FALSE;
        }
        $retval   = $module_disconnect($this->output,$area_id,$node_id,$module);
        $result   = ($retval) ? 'success' : 'failure';
        $priority = ($retval) ? WLOG_DEBUG : WLOG_INFO;
        logger(sprintf('%s.%s(): %s disconnecting module \'%s\' from node \'%d\'',__CLASS__,__FUNCTION__,
                       $result,$module['name'],$node_id),$priority);
        return $retval;
    } // module_disconnect()


    /** inform module $module_id that from now on it will be linked to page $node_id
     *
     * this routine tells module $module_id that from now on it is
     * associated with node $node_id in area $area_id.
     *
     * This is done by 
     * a. loading the module's administrative interface (the admin-script file), and
     * b. calling the function <modulename>_connect()
     *
     * If something goes wrong (e.g. no module found, non-existing admin-script,
     * undefined function <modulename>_connect()) FALSE is returned, otherwise the return
     * value of function <modulename>_connect() is returned.
     *
     * @param int $area_id the area where $node_id resides
     * @param int $node_id the node to which the module will be connected
     * @param int $module_id the module that will be connected to node $node_id
     * @return bool FALSE on failure, otherwise the result of <modulename>_connect()
     * @todo should we pass the area_id at all? What happens when a node is moved
     *       to another area without informing the module? Questions, questions, questions...
     */
    function module_connect($area_id, $node_id, $module_id) {

        if (($module = $this->module_load_admin($module_id)) === FALSE) {
            return FALSE;
        }
        $module_connect = $module['name'].'_connect';
        if (!function_exists($module_connect)) {
            logger(__FUNCTION__."(): weird: function '$module_connect' does not exist. Huh?",WLOG_DEBUG);
            return FALSE;
        }
        $retval   = $module_connect($this->output,$area_id,$node_id,$module);
        $result   = ($retval) ? 'success' : 'failure';
        $priority = ($retval) ? WLOG_DEBUG : WLOG_INFO;
        logger(sprintf('%s.%s(): %s connecting module \'%s\' to node \'%d\'',__CLASS__,__FUNCTION__,
                       $result,$module['name'],$node_id),$priority);
        return $retval;
    } // module_connect()


    /** show a dialog for editing the content of module $module_id linked to page $node_id
     *
     * this loads the code for module $module_id and calls the appropriate routine
     * for displaying a dialog
     *
     * The parameter $viewonly can be used to indicate readonly access to the content.
     * It is upto the called function to adhere to this flag, e.g. by just showing the
     * content instead of letting the user modify it.
     *
     * If the flag $edit_again is TRUE, this is not the first call to this routine, i.e.
     * we have been here before but probably something went wrong when saving the data
     * (e.g. en invalid date like 2008-02-31 was entered). This makes it possible to 
     * re-edit the content without starting from scratch again. If the flag is FALSE, the
     * called routine is supposed to start with the data as it is currently stored in the
     * database. Otherwise the current data is POST'ed by the user.
     *
     * If something goes wrong (e.g. no module found, non-existing admin-script,
     * undefined function <modulename>_show_edit()) FALSE is returned, otherwise the return
     * value of function <modulename>_show_edit() is returned.
     *
     * @param int $node_id the node to which the module is connected
     * @param int $module_id the module that is connected to node $node_id
     * @param bool $viewonly if TRUE, editing is not allowed (but simply showing the content is allowed)
     * @param bool $edit_again if TRUE, start with data from $_POST, otherwise read from database
     * @return bool FALSE on failure, otherwise the result of <modulename>_show_dialog()
     */
    function module_show_edit($node_id,$module_id,$viewonly,$edit_again) {
        global $WAS_SCRIPT_NAME;

        if (($module = $this->module_load_admin($module_id)) === FALSE) {
            return FALSE;
        }
        $module_show_edit = $module['name'].'_show_edit';
        if (!function_exists($module_show_edit)) {
            logger(__FUNCTION__."(): weird: function '$module_show_edit' does not exist. Huh?");
            return FALSE;
        }
        $href = href($WAS_SCRIPT_NAME,array('job' => JOB_PAGEMANAGER, 'task' => TASK_SAVE_CONTENT, 'node' => $node_id));
        $retval = $module_show_edit($this->output,$this->area_id,$node_id,$module,$viewonly,$edit_again,$href);
        logger(sprintf(__FUNCTION__."(): %s showing dialog edit content with module '%s' connected to node '%d'%s",
                       ($retval) ? 'success' : 'failure',$module['name'],$node_id,
                       ($edit_again) ? ', again' : ''),WLOG_DEBUG);
        return $retval;
    } // module_show_edit()


    /** (maybe) save the modified content of module $module_id connected to page $node_id
     *
     * this saves the module data belonging to node $node_id.
     *
     * If something goes wrong (e.g. no module found, non-existing admin-script,
     * undefined function <modulename>_save()) FALSE is returned, otherwise the return
     * value of function <modulename>_save() is returned.
     *
     * @param int $node_id the node to which the module is connected
     * @param int $module_id the module that is connected to node $node_id
     * @param bool $viewonly if TRUE, editing and thus saving is not allowed
     * @param bool &$edit_again returns TRUE if more editing is required, FALSE otherwise
     * @return bool FALSE on failure, otherwise the result of <modulename>_save()
     */
    function module_save($node_id,$module_id,$viewonly,&$edit_again) {
        global $WAS_SCRIPT_NAME;

        if (($module = $this->module_load_admin($module_id)) === FALSE) {
            return FALSE;
        }
        $module_save = $module['name'].'_save';
        if (!function_exists($module_save)) {
            logger(__FUNCTION__."(): weird: function '$module_save' does not exist. Huh?");
            return FALSE;
        }
        $retval = $module_save($this->output,$this->area_id,$node_id,$module,$viewonly,$edit_again);
        logger(sprintf(__FUNCTION__."(): %s saving content via module '%s' connected to node '%d'",
                       ($retval) ? 'success' : 'failure',$module['name'],$node_id),WLOG_DEBUG);
        return $retval;
    } // module_save()


    /** load the admin interface of a module in core
     *
     * this includes the 'admin'-part of a module via 'require_once()'. This routine
     * first figures out if the admin-script file actually exists before the
     * file is included. Also, we look at a very specific location, namely:
     * /program/modules/<modulename>/<module_admin_script> where <modulename> is retrieved
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
    function module_load_admin($module_id) {
        global $CFG;
        $modules = $this->get_module_records();
        if (!isset($modules[$module_id])) {
            logger(__FUNCTION__."(): weird: module '$module_id' is not there. Is it de-activated?");
            return FALSE;
        }
        $module = $modules[$module_id];
        unset($modules);
        $module_admin_script = $CFG->progdir.'/modules/'.$module['name'].'/'.$module['admin_script'];
        if (!file_exists($module_admin_script)) {
            logger(__FUNCTION__."(): weird: file '$module_admin_script' does not exist. Huh?");
            return FALSE;
        }
        require_once($module_admin_script);
        return $module;
    } // module_load_admin()

} // PageManager
?>