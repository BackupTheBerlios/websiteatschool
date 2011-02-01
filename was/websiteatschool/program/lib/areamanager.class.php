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

/** /program/lib/areamanager.class.php - taking care of area management
 *
 * This file defines a class for managing areas (add, edit, delete, view).
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.org/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: areamanager.class.php,v 1.1 2011/02/01 13:00:16 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

define('AREAMANAGER_CHORE_VIEW',       'view');
define('AREAMANAGER_CHORE_ADD',        'add');
define('AREAMANAGER_CHORE_DELETE',     'delete');
define('AREAMANAGER_CHORE_EDIT',       'edit');
define('AREAMANAGER_CHORE_EDIT_THEME', 'edittheme');
define('AREAMANAGER_CHORE_RESET_THEME','resettheme');
define('AREAMANAGER_CHORE_SAVE_NEW',   'savenew');
define('AREAMANAGER_CHORE_SAVE',       'save');
define('AREAMANAGER_CHORE_SET_DEFAULT','setdefault');

define('AREAMANAGER_DIALOG_ADD',        1);
define('AREAMANAGER_DIALOG_DELETE',     2);
define('AREAMANAGER_DIALOG_EDIT',       3);
define('AREAMANAGER_DIALOG_EDIT_THEME', 4);
define('AREAMANAGER_DIALOG_RESET_THEME',5);


/** Methods to access properties of an area
 *
 * This class is used to manage areas. The following functions are supplied
 *
 *  - add a new area (requires PERMISSION_SITE_ADD_AREA)
 *  - set default area (requires PERMISSION_AREA_EDIT_AREA)
 *  - delete existing area (requires PERMISSION_SITE_DROP_AREA)
 *  - edit area properties (requires PERMISSION_AREA_EDIT_AREA)
 *  - view list of areas (requires permissions for add, edit, delete or ACL_ROLE_INTRANET_ACCESS)
 *
 * The default action is to show a list of existing areas for which the
 * user has some form of permission. This could be either one of the permissions
 * mentioned above or the permission to view the area (intranet).
 *
 * @todo we need to take care of spurious spaces in inputs (or do we?)
 */
class AreaManager {
    /** @var object|null collects the html output */
    var $output = NULL;

    /** @var array list of cached area records keyed with area_id */
    var $areas = array();

    /** @var bool if TRUE the calling routing is allowed to use the menu area (e.g. show config mgr menu) */
    var $show_parent_menu = FALSE;

    /** construct an AreaManager object
     *
     * This initialises the AreaManager and also dispatches the chore to do.
     *
     * @param object &$output collects the html output
     * @uses $CFG
     */
    function AreaManager(&$output) {
        global $CFG;
        $this->output = &$output;
        $this->output->set_helptopic('areamanager');

        $chore = get_parameter_string('chore',AREAMANAGER_CHORE_VIEW);
        switch($chore) {
        case AREAMANAGER_CHORE_VIEW:
            $this->area_overview();
            break;

        case AREAMANAGER_CHORE_ADD:
            $this->area_add();
            break;

        case AREAMANAGER_CHORE_SET_DEFAULT:
            $this->area_setdefault();
            break;

        case AREAMANAGER_CHORE_DELETE:
            $this->area_delete();
            break;

        case AREAMANAGER_CHORE_EDIT:
            $this->area_edit();
            break;

        case AREAMANAGER_CHORE_EDIT_THEME:
            $this->area_edittheme();
            break;

        case AREAMANAGER_CHORE_RESET_THEME:
            $this->area_resettheme();
            break;

        case AREAMANAGER_CHORE_SAVE_NEW:
            $this->area_savenew();
            break;

        case AREAMANAGER_CHORE_SAVE:
            $this->area_save();
            break;

        default:
            $s = (strlen($chore) <= 50) ? $chore : substr($chore,0,44).' (...)';
            $message = t('chore_unknown','admin',array('{CHORE}' => htmlspecialchars($s)));
            $output->add_message($message);
            logger('areamanager: unknown chore: '.htmlspecialchars($s));
            $this->area_overview();
            break;
        }
    }

    /** allow the caller to use the menu area (or not)
     *
     * this routine tells the caller if it is OK to use
     * the menu area (TRUE returned) or not (FALSE returned).
     (
     * @return bool TRUE if menu area is available, FALSE otherwise
     */
    function show_parent_menu() {
        return $this->show_parent_menu;
    } // show_parent_menu()

    // ==================================================================
    // =========================== WORKHORSES ===========================
    // ==================================================================


    /** display list of areas with edit/delete icons etc. and option to add an area
     *
     * this constructs the heart of the area manager: an optional link to add an
     * area followed by a list of links for all areas to which the user has access.
     * From here the user can set the default area, attempt to delete an area and
     * edit the basic and advanced properties of an area. All actions that manipulate
     * an area return here eventually.
     *
     * Note that the calling routine (the configuration manager) is allowed to
     * display a menu because we set the parameter show_parent_menu to TRUE here.
     *
     * The constructed list looks something like this:
     *
     * <code>
     *              Add an area
     * [H] [D] [E] (public) Exemplum Primary School (1, 10)
     * [ ] [D] [E] (private) Exemplum Intranet (2, 20)
     * [ ] [D] [E] (public) Exemplum Inactive (3, 30) (inactive)
     * ...
     * </code>
     *
     * The clickable icons [H] and [ ] manipulate the default area
     * The clickable icons [D] lead to a Delete area confirmation dialog
     * The clickable icons [E] lead to the Edit area (theme parameters)
     * The clickable titles lead to the Edit area (basic parameters)
     * The clickable link 'Add an area' leads to the add new area dialog.
     *
     * The area titles are dimmed (grayed-out) if the user is able to
     * see these areas (because they're public or the user has at most
     * intranet acces for that area). Private areas for which the user
     * has no access at all don't show up in the list. If the user has
     * Edit-permissions, the area title is not dimmed and the area can
     * be edited.
     *
     * @return void results are returned as output in $this->output
     * @todo should we make two categories: 'public' and 'private' in 
     *       the list of areas? Maybe handy when there are
     *       many manu areas, but it would be inconsistend with
     *       the page manager menu which simply lists the areas
     *       in the sort order. Easy way out: the user is perfectly
     *       capable to set the sort order in such a way
     *       that the sort order already groups the public and 
     *       private areas. Oh well....
     * @todo should we add a paging function to the list of areas?
     *       Currently all areas are shown in a single list...
     * @uses $USER
     * @uses $WAS_SCRIPT_NAME
     * @uses $CFG
     */
    function area_overview() {
        global $USER,$WAS_SCRIPT_NAME,$CFG;

        // 1 -- Start content and UL-list
        $this->output->add_content('<h2>'.t('menu_areas','admin').'</h2>');
        $this->output->add_content('<ul>');

        // 2 -- Add an 'add an area' if user is allowed
        if ($USER->has_site_permissions(PERMISSION_SITE_ADD_AREA)) {
            $this->output->add_content('  <li class="list">');
            // line up the prompt with links to existing areas below (if any)
            if (!$USER->high_visibility) {
                $img_attr = array('width' => 16, 'height' => 16, 'title' => '', 'alt' => t('spacer','admin'));
                $icon_blank = '    '.html_img($CFG->progwww_short.'/graphics/blank16.gif',$img_attr);
                for ($i=0; $i<3; ++$i) {
                    $this->output->add_content($icon_blank);
                }
            } // else
                // don't clutter the high-visiblity interface with superfluous layout fillers
            $a_attr = array('title'=> t('areamanager_add_an_area_title','admin'));
            $a_param = $this->a_param(AREAMANAGER_CHORE_ADD);
            $anchor = t('areamanager_add_an_area','admin');
            $this->output->add_content('    '.html_a($WAS_SCRIPT_NAME,$a_param,$a_attr,$anchor));
        }

        // 3 -- Add a list of existing areas (if there are any) from the user's point of view
        //
        // 3A -- Construct a list of areas for which this user has at least view or admin access
        $records = get_area_records();
        if ($records !== FALSE) {
            foreach($records as $area_id => $record) {
                if (($USER->has_site_permissions(PERMISSION_SITE_ADD_AREA)) ||
                    ($USER->has_site_permissions(PERMISSION_SITE_DROP_AREA)) ||
                    ($USER->has_area_permissions(PERMISSION_AREA_EDIT_AREA,$area_id))) {
                    $areas[$area_id] = $record + array('is_admin' => TRUE);
                } elseif ((db_bool_is(FALSE,$record['is_private'])) ||
                          ($USER->has_intranet_permissions(ACL_ROLE_INTRANET_ACCESS,$area_id))) {
                    $areas[$area_id] = $record + array('is_admin' => FALSE);
                } // else
                    // suppress this area because user has no access whatsoever
            }
        }

        // 3B -- Now create delete/edit etc. links if possible (if any areas available)
        if (sizeof($areas) > 0) {
            foreach($areas as $area_id => $area) {
                $this->output->add_content('  <li class="list">');
                $this->output->add_content('    '.$this->get_icon_home($area_id,$areas));
                $this->output->add_content('    '.$this->get_icon_delete($area_id));
                $this->output->add_content('    '.$this->get_icon_edit($area_id));

                $a_param = $this->a_param(AREAMANAGER_CHORE_EDIT,$area_id);
                $title = t(($USER->has_area_permissions(PERMISSION_AREA_EDIT_AREA,$area_id)) ?
                           'icon_area_edit' : 'icon_area_edit_access_denied','admin');
                $a_attr = array('title' => $title);
                if (!$area['is_admin']) {
                    $a_attr['class'] = 'dimmed';
                }
                $params = array('{AREA_FULL_NAME}' => $area['title'], 
                                '{AREA}' => $area_id,
                                '{SORT_ORDER}' => $area['sort_order']);
                $anchor = t((db_bool_is(TRUE,$area['is_private'])) ? 
                            'area_edit_private_title' : 'area_edit_public_title','admin',$params);
                $anchor .= (db_bool_is(TRUE,$area['is_active'])) ? '' : ' ('.t('inactive','admin').')';
                $this->output->add_content('    '.html_a($WAS_SCRIPT_NAME,$a_param,$a_attr,$anchor));
            }
        }

        // 4 -- close the list and allow caller to show the configuration manager menu too
        $this->output->add_content('</ul>');
        $this->show_parent_menu = TRUE;
    } // area_overview()


    /** present a dialog where the user can enter minimal properties for a new area
     *
     * this displays a dialog where the user can enter the minimal necessary properties
     * of a new area. These properties are: 
     *  - name
     *  - public or private
     *  - the theme to use
     * Other properties will be set to default values and can be edited lateron,
     * by editing the area.
     *
     * The new area is saved via performing the 'chore' AREAMANAGER_CHORE_SAVE_NEW.
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME
     * @uses $USER
     */
    function area_add() {
        global $WAS_SCRIPT_NAME,$USER;

        if (!$USER->has_site_permissions(PERMISSION_SITE_ADD_AREA)) {
            logger("areamanager: user attempted to add an area without permission");
            $msg = t('task_area_add_access_denied','admin');
            $this->output->add_message($msg);
            $this->output->add_popup_bottom($msg);
            $this->area_overview();
            return;
        }
        $this->output->add_content('<h2>'.t('areamanager_add_area_header','admin').'</h2>');
        $this->output->add_content(t('areamanager_add_area_explanation','admin'));
        $href = href($WAS_SCRIPT_NAME,$this->a_param(AREAMANAGER_CHORE_SAVE_NEW));
        $dialogdef = $this->get_dialogdef_add_area();
        $this->output->add_content(dialog_quickform($href,$dialogdef));
    } // area_add()


    /** make the selected area the default for the site
     *
     * this sets a default area. First we check permissions and if
     * the user 
     *  - is allowed to set the default bit on the target area, AND
     *  - is allowed to reset the default bit on the current default area
     * We actually
     *  - reset the default bit from the current default (if there is one), AND
     *  - set the default bit for the selected node.
     *
     * Note: if the user sets the default node on the current default node,
     * the default is reset and subsequently set again (two trips to the database),
     * This also updates the mtime of the record.
     *
     * @return void results are returned as output in $this->output
     * @uses $USER
     * @todo should we send alerts? If so, can we use the routine to queue
     *       messages from pagemanager? A reason not to send alerts: the
     *       alerts will be sent as soon as a page is added to the new area,
     *       so why bother?
     * @todo should we acknowledge the changed default to the user or is it enough to see the icon 'move'?
     */
    function area_setdefault() {
        global $USER;

        // 0 -- sane proposed new default area_id?
        $new_default_area_id = get_parameter_int('area',0);
        $areas = get_area_records();
        if (($areas === FALSE) || (!isset($areas[$new_default_area_id]))) {
            // are they trying to trick us, specifying an invalid area?
            logger("areamanager: weird: user tried to make non-existing area '$new_default_area_id' default");
            $this->output->add_message(t('invalid_area','admin',array('{AREA}' => strval($new_default_area_id))));
            $this->area_overview();
            return;
        }

        // 1 -- check out permissions
        $user_has_permission = FALSE;
        $old_default_area_id = 0; // sentinel for remove old default
        if ($USER->has_area_permissions(PERMISSION_AREA_EDIT_AREA,$new_default_area_id)) {
            $user_has_permission = TRUE;
            foreach($areas as $area) {
                if (db_bool_is(TRUE,$area['is_default'])) {
                    $old_default_area_id = $area['area_id'];
                    $user_has_permission = $USER->has_area_permissions(PERMISSION_AREA_EDIT_AREA,$old_default_area_id);
                    break;
                }
            }
        }

        // 2 -- change the default area if permission granted
        if ($user_has_permission) {
            $now = strftime('%Y-%m-%d %T');
            $fields = array('mtime' => $now, 'muser_id' => $USER->user_id);
            if (($old_default_area_id != $new_default_area_id) && ($old_default_area_id != 0)){
                $where = array('area_id' => intval($old_default_area_id));
                $fields['is_default'] = FALSE;
                db_update('areas',$fields,$where);
            }
            $fields['is_default'] = TRUE;
            $where = array('area_id' => intval($new_default_area_id));
            db_update('areas',$fields,$where);

            // always log the event
            logger(sprintf('areamanager: new default area %d (was %s)',
    			$new_default_area_id,
    			($old_default_area_id != 0) ? strval($old_default_area_id) : 'none'));
            $areas = get_area_records(TRUE); // force reread of areas/flush cached version
        } else {
            $params = array('{AREA}' => $new_default_area_id);
            $this->output->add_message(t('task_set_default_area_access_denied','admin',$params));
        }
        $this->area_overview();
    } // area_setdefault()


    /** delete an area from ths site after confirmation
     *
     * this either presents a confirmation dialog to the user OR deletes an area.
     * First the user's permissions are checked and also there should be no nodes left
     * in the area before anything is done. Only allowing deletion of an empty area
     * is safety measure: we don't want to accidently delete many many nodes from
     * an area in one go (see also {@link task_node_delete()}). Also, we don't want
     * to introduce orphaned node records (by deleting the area record without deleting
     * nodes).
     *
     * Note that this routine could have been split into two routines, with the
     * first one displaying the confirmation dialog and the second one 'saving the changes'.
     * However, I think it is counter-intuitive to perform a deletion of data under
     * the name of 'saving'. So, I decided to use the same routine for both displaying
     * the dialog and acting on the dialog.
     *
     * @return void results are returned as output in $this->output
     * @todo should we also require the user to delete any files associated with the area before we even consider
     *       deleting it? Or is is OK to leave the files and still delete the area. We do require that nodes are
     *       removed from the area, but that is mainly because of maintaining referential integrity. Mmmmm... Maybe
     *       that applies to the files as well, especially in a private area. Food for thought.
     * @todo since multiple tables are involved, shouldn't we use transaction/rollback/commit?
     *       Q: How well is MySQL suited for transactions? A: Mmmmm.... Which version? Which storage engine?
     * @uses $USER
     */
    function area_delete() {
        global $USER;

        // 0 -- sane area_id for deletion?
        $area_id = get_parameter_int('area',0);
        $areas = get_area_records();
        if (($areas === FALSE) || (!isset($areas[$area_id]))) {
            // are they trying to trick us, specifying an invalid area?
            logger("areamanager: weird: user tried to delete non-existing area '$area_id'");
            $this->output->add_message(t('invalid_area','admin',array('{AREA}' => strval($area_id))));
            $this->area_overview();
            return;
        }

        // 1A -- are we allowed to perform delete operation?
        if (!$USER->has_site_permissions(PERMISSION_SITE_DROP_AREA)) {
            logger("areamanager: user attempted to delete area '$area_id' without permission");
            $msg = t('icon_area_delete_access_denied','admin');
            $this->output->add_message($msg);
            $this->output->add_popup_bottom($msg);
            $this->area_overview();
            return;
        }
        // 1B -- are there any nodes left in the area?
        $record = db_select_single_record('nodes','count(*) as nodes',array('area_id' => $area_id));
        if ($record === FALSE) {
            logger("areamanager: deletion of area '$area_id' failed: ".db_errormessage());
            $params = array('{AREA}' => $area_id,'{AREA_FULL_NAME}' => $areas[$area_id]['title']);
            $this->output->add_message(t('error_deleting_area','admin',$params));
            $this->area_overview();
            return;
        } elseif ($record['nodes'] > 0) {
            $nodes = $record['nodes'];
            logger("areamanager: cannot delete area '$area_id' because $nodes nodes still exist in that area'");
            $params = array('{AREA}' => $area_id,
                            '{AREA_FULL_NAME}' => $areas[$area_id]['title'],
                            '{NODES}'=>strval($nodes));
            $this->output->add_message(t('error_deleting_area_not_empty','admin',$params));
            $this->area_overview();
            return;
        }
        // 1C -- are there any files left in this area

        // At this point we should check to see if there are any files associated with this
        // area in the data directory. See comments in the todo above.

        // 2 -- actually perform the delete operation OR show the confirmation dialog
        if ((isset($_POST['dialog'])) && ($_POST['dialog'] == AREAMANAGER_DIALOG_DELETE)) {
            // stage 2 - do delete if user pressed delete button or do nothing after Cancel button
            if (isset($_POST['button_delete'])) {
                $error_count = 0;
                $where = array('area_id' => $area_id);
                // clean up a bunch of tables associated with this area
                $tables = array('themes_areas_properties','users_areas','alerts_areas_nodes','areas');
                // db_start_transaction();
                foreach($tables as $table) {
                    $retval = db_delete($table,$where);
                    if ($retval === FALSE) {
                        logger("areamanager: delete area '$area_id' from table '$table' failed: ".db_errormessage());
                        ++$error_count;
                        // db_rollback_transaction();
                        // break;
                    } else {
                        logger("areamanager: delete area '$area_id': records deleted from '$table': $retval ",LOG_DEBUG);
                    }
                }
                $params = array('{AREA}' => $area_id,'{AREA_FULL_NAME}' => $areas[$area_id]['title']);
                if ($error_count == 0) {
                    // db_commit_transaction()
                    logger("areamanager: successfully deleted area '$area_id'");
                    $this->output->add_message(t('area_deleted','admin',$params));
                } else {
                    $this->output->add_message(t('error_deleting_area','admin',$params));
                }
                $areas = get_area_records(TRUE);  // force re-read of areas after deletion
            } else { // user cancelled
                $this->output->add_message(t('cancelled','admin'));
            }
            $this->area_overview();
        } else {
            // stage 1 - show dialog
            $this->show_dialog_confirm_delete($area_id,$areas);
        }
        return;
    } // area_delete()


    /** show the theme/area configuration dialog and the edit menu
     *
     * this displays the list of configurable properties of the theme
     * currently associated with this area in a dialog so that the user
     * can modify the values. Since the area-theme configuration is a
     * more or less 'standard' list of properties, we can use the
     * generic configuration manipulator contained in the ConfigAssistant
     * class.
     *
     * @return void results are returned as output in $this->output
     * @uses $CFG
     * @uses $WAS_SCRIPT_NAME
     * @uses ConfigAssistant
     * @uses $USER
     */
    function area_edittheme() {
        global $CFG,$WAS_SCRIPT_NAME,$USER;

        $area_id = get_parameter_int('area',0);
        $areas = get_area_records();

        // 0 - basic sanity
        if (($areas === FALSE) || (!isset($areas[$area_id]))) {
            // are they trying to trick us, specifying an invalid area?
            logger("areamanager: weird: user tried to edit non-existing area '$area_id'");
            $this->output->add_message(t('invalid_area','admin',array('{AREA}' => strval($area_id))));
            $this->area_overview();
            return;
        }

        // 1 -- are we allowed to perform the edit operation?
        if (!$USER->has_area_permissions(PERMISSION_AREA_EDIT_AREA,$area_id)) {
            logger("areamanager: user attempted to edit area '$area_id' without permission");
            $msg = t('icon_area_edit_access_denied','admin');
            $this->output->add_message($msg);
            $this->output->add_popup_bottom($msg);
            $this->area_overview();
            return;
        }

        // 2 -- actually show the dialog using the ConfigAssistant
        include_once($CFG->progdir.'/lib/configassistant.class.php');

        $area_name = $areas[$area_id]['title'];
        $theme_id = $areas[$area_id]['theme_id'];
        $themes = $this->get_theme_records();
        $theme_name = $themes[$theme_id]['name'];
        $table = 'themes_areas_properties';
        $keyfield = 'theme_area_property_id';
        $prefix = '';
        $language_domain = 't_'.$theme_name;
        $where = array('area_id' => $area_id, 'theme_id' => $theme_id);
        $hidden_fields = array(array(
            'type' => F_INTEGER,
            'name' => 'dialog',
            'value' => AREAMANAGER_DIALOG_EDIT_THEME,
            'hidden' => TRUE));
        $assistant = new ConfigAssistant($table,$keyfield,$prefix,$language_domain,$where,$hidden_fields);
        $href = href($WAS_SCRIPT_NAME,$this->a_param(AREAMANAGER_CHORE_SAVE,$area_id));
        $params = array('{AREA}' => strval($area_id), 
                        '{AREA_FULL_NAME}' => $area_name,
                        '{THEME_NAME}' => $theme_name);
        $this->output->add_content('<h2>'.t('areamanager_edit_theme_header','admin',$params).'</h2>');
        $this->output->add_content(t('areamanager_edit_theme_explanation','admin',$params));
        $assistant->show_dialog($this->output,$href);
        $this->show_edit_menu($area_id,AREAMANAGER_CHORE_EDIT_THEME);
    } // area_edittheme()


    /** reset the theme configuration to the factory defaults
     *
     * this is a two-step process: we either show a confirmation dialog or
     * we actually overwrite the existing theme configuration with the default
     * values.
     *
     * @return void results are returned as output in $this->output
     * @uses $USER
     * @uses $CFG
     * @uses $WAS_SCRIPT_NAME
     * @uses $DB
     */
    function area_resettheme() {
        global $CFG,$WAS_SCRIPT_NAME,$DB,$USER;

        $area_id = get_parameter_int('area',0);
        $areas = get_area_records();
        $area_name = $areas[$area_id]['title'];
        $theme_id = intval($areas[$area_id]['theme_id']);
        $themes = $this->get_theme_records();
        $theme_name = $themes[$theme_id]['name'];

        // 0 - basic sanity
        if (($areas === FALSE) || (!isset($areas[$area_id]))) {
            // are they trying to trick us, specifying an invalid area?
            logger("areamanager: weird: user tried to reset theme in non-existing area '$area_id'");
            $this->output->add_message(t('invalid_area','admin',array('{AREA}' => strval($area_id))));
            $this->area_overview();
            return;
        }

        // 1 -- are we allowed to perform an edit operation?
        if (!$USER->has_area_permissions(PERMISSION_AREA_EDIT_AREA,$area_id)) {
            logger("areamanager: user attempted to reset theme for area '$area_id' without permission");
            $msg = t('icon_area_edit_access_denied','admin');
            $this->output->add_message($msg);
            $this->output->add_popup_bottom($msg);
            $this->area_overview();
            return;
        }

        // 2 -- actually perform the reset operation OR show the confirmation dialog
        $params = array('{AREA}' => $area_id,'{AREA_FULL_NAME}' => $area_name,'{THEME_NAME}' => $theme_name);
        if ((isset($_POST['dialog'])) && ($_POST['dialog'] == AREAMANAGER_DIALOG_RESET_THEME)) {
            // stage 2 - do reset if user pressed Save button or do nothing after Cancel button
            if (isset($_POST['button_save'])) {
                if ($this->reset_theme_defaults($area_id,$theme_id)) {
                    logger("areamanager: successfully reset properties of theme '$theme_id' in area '$area_id'");
                    $this->output->add_message(t('area_theme_reset','admin',$params));
                } else {
                    $this->output->add_message(t('error_area_theme_reset','admin',$params));
                }
            } else { // user cancelled
                $this->output->add_message(t('cancelled','admin'));
            }
            // $this->area_overview();
            // It is more logical to show the edit theme properties diaolog after a reset IMHO
            $this->area_edittheme();
        } else {
            // stage 1 - show dialog
            $dialogdef = array(
                'dialog' => array(
                    'type' => F_INTEGER,
                    'name' => 'dialog',
                    'value' => AREAMANAGER_DIALOG_RESET_THEME,
                    'hidden' => TRUE
                ),
                dialog_buttondef(BUTTON_SAVE),
                dialog_buttondef(BUTTON_CANCEL)
                );
            $this->output->add_content('<h2>'.t('reset_theme_area_header','admin',$params).'</h2>');
            $this->output->add_content(t('reset_theme_area_explanation','admin'));
            $this->output->add_content('<p>');
            $this->output->add_content(t('reset_theme_area_are_you_sure','admin'));
            $href = href($WAS_SCRIPT_NAME,$this->a_param(AREAMANAGER_CHORE_RESET_THEME,$area_id));
            $this->output->add_content(dialog_quickform($href,$dialogdef));
        }
        return;
    } // area_resettheme()


    /** show the basic properties edit dialog and the edit menu
     *
     * Note that this dialog does NOT show every area property: the path
     * to the datafiles is missing. It feels too complicated to allow the
     * user to actually change the path because in that case we need to
     * move all existing files to the new location, etc. etc.
     * I did consider to allow the GURU to perform that task (ie editing
     * the path), but eventually decided against it: it is simply not worth
     * it. However, if you know the way to the database and manually edit
     * the path field you can do so, but in that case you're on your own...
     * As a result we don't even show the path to the data directory
     * (it is commented out in the get_dialogdef() routine).
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME
     * @uses $USER
     */
    function area_edit() {
        global $WAS_SCRIPT_NAME,$USER;
        $area_id = get_parameter_int('area',0);
        $areas = get_area_records();

        // 0 - basic sanity
        if (($areas === FALSE) || (!isset($areas[$area_id]))) {
            // are they trying to trick us, specifying an invalid area?
            logger("areamanager: weird: user tried to edit non-existing area '$area_id'");
            $this->output->add_message(t('invalid_area','admin',array('{AREA}' => strval($area_id))));
            $this->area_overview();
            return;
        }

        // 1 -- are we allowed to perform the edit operation?
        if (!$USER->has_area_permissions(PERMISSION_AREA_EDIT_AREA,$area_id)) {
            logger("areamanager: user attempted to edit area '$area_id' without permission");
            $msg = t('icon_area_edit_access_denied','admin');
            $this->output->add_message($msg);
            $this->output->add_popup_bottom($msg);
            $this->area_overview();
            return;
        }
        $this->output->add_content('<h2>'.t('areamanager_edit_area_header','admin').'</h2>');
        $this->output->add_content(t('areamanager_edit_area_explanation','admin'));
        $href = href($WAS_SCRIPT_NAME,$this->a_param(AREAMANAGER_CHORE_SAVE,$area_id));
        $dialogdef = $this->get_dialogdef_edit_area($area_id);
        $this->get_dialog_data($dialogdef,$areas[$area_id]);
        $this->output->add_content(dialog_quickform($href,$dialogdef));
        $this->show_edit_menu($area_id,AREAMANAGER_CHORE_EDIT);
    } // area_edit()


    /** validate and save modified data to database
     *
     * this saves data from both the edit and the edit theme dialog if data validates.
     * If the data does NOT validate, the edit screen is displayed again
     * otherwise the area overview is displayed again.
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME
     * @uses $CFG
     * @uses $USER
     */
    function area_save() {
        global $CFG,$WAS_SCRIPT_NAME,$USER;
        $area_id = get_parameter_int('area',0);
        $areas = get_area_records();

        // 0 - basic sanity
        if (($areas === FALSE) || (!isset($areas[$area_id]))) {
            // are they trying to trick us, specifying an invalid area?
            logger("areamanager: weird: user tried to save data to non-existing area '$area_id'");
            $this->output->add_message(t('invalid_area','admin',array('{AREA}' => strval($area_id))));
            $this->area_overview();
            return;
        }

        // 1 -- are we allowed to perform the edit and thus the save operation?
        if (!$USER->has_area_permissions(PERMISSION_AREA_EDIT_AREA,$area_id)) {
            logger("areamanager: user attempted to save data to area '$area_id' without permission");
            $msg = t('icon_area_edit_access_denied','admin');
            $this->output->add_message($msg);
            $this->output->add_popup_bottom($msg);
            $this->area_overview();
            return;
        }

        // 2 -- if the user cancelled the operation, there is no point in hanging 'round
        if (isset($_POST['button_cancel'])) {
            $this->output->add_message(t('cancelled','admin'));
            $this->area_overview();
            return;
        }

        // 3 -- we need to know which dialog we're dealing with
        if (!isset($_POST['dialog'])) {
            logger("areamanager: weird: 'dialog' not set in area_save() (area='$area_id')",LOG_DEBUG);
            $this->area_overview();
            return;
        }
        $dialog = intval($_POST['dialog']);
        if ($dialog == AREAMANAGER_DIALOG_EDIT_THEME) {
            $theme_id = $areas[$area_id]['theme_id'];
            $themes = $this->get_theme_records();
            $theme_name = $themes[$theme_id]['name'];
            include_once($CFG->progdir.'/lib/configassistant.class.php');
            $table = 'themes_areas_properties';
            $keyfield = 'theme_area_property_id';
            $prefix = '';
            $language_domain = 't_'.$theme_name;
            $where = array('area_id' => $area_id, 'theme_id' => $theme_id);
            $hidden_fields = array(array(
                'type' => F_INTEGER,
                'name' => 'dialog',
                'value' => AREAMANAGER_DIALOG_EDIT_THEME,
                'hidden' => TRUE));
            $assistant = new ConfigAssistant($table,$keyfield,$prefix,$language_domain,$where,$hidden_fields);
            if (!$assistant->save_data($this->output)) {
                $href = href($WAS_SCRIPT_NAME,$this->a_param(AREAMANAGER_CHORE_SAVE,$area_id));
                $assistant->show_dialog($this->output,$href);
                // since they blew it, we will not show the edit menu at this point; 
                // user should concentrate on getting input data right (or use cancel)
            } else {
                $this->area_overview();
            }
        } elseif ($dialog == AREAMANAGER_DIALOG_EDIT) {
            $dialogdef = $this->get_dialogdef_edit_area($area_id);
            if (!dialog_validate($dialogdef)) {
                // there were errors, show them to the user and do it again
                foreach($dialogdef as $k => $item) {
                    if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                        $this->output->add_message($item['error_messages']);
                    }
                }
                $this->output->add_content('<h2>'.t('areamanager_edit_area_header','admin').'</h2>');
                $this->output->add_content(t('areamanager_edit_area_explanation','admin'));
                $href = href($WAS_SCRIPT_NAME,$this->a_param(AREAMANAGER_CHORE_SAVE,$area_id));
                $this->output->add_content(dialog_quickform($href,$dialogdef));
                // no edit menu, let user concentrate on task at hand ie errorfree data input
                return;
            }

            $now = strftime('%Y-%m-%d %T');
            $fields = array(
                'mtime' => $now,
                'muser_id' => $USER->user_id
                );
            $theme_id = 0;
            foreach($dialogdef as $k => $item) {
                if (isset($item['name'])) {
                    switch ($item['name']) {
                    case 'area_title':
                        $fields['title'] = $item['value'];
                        break;

                    // This field should not be editable and thus should not be saved
                    //case 'area_is_private':
                    //    $fields['is_private'] = ($item['value'] == 1) ? TRUE : FALSE;
                    //    break;

                    case 'area_is_active':
                        $fields['is_active'] = ($item['value'] == 1) ? TRUE : FALSE;
                        break;

                    case 'area_theme_id':
                        $theme_id = intval($item['value']);
                        $fields['theme_id'] = $theme_id;
                        break;

                    // This field should not be editable and thus should not be saved
                    //case 'area_path':
                    //    $fields['path'] = $item['value'];
                    //    break;

                    case 'area_metadata':
                        $fields['metadata'] = $item['value'];
                        break;

                    case 'area_sort_order':
                        $fields['sort_order'] = intval($item['value']);
                        break;

                    default:
                        break;
                    }
                }
            }
            $where = array('area_id' => $area_id);
            $params = array('{AREA}' => $area_id, '{AREA_FULL_NAME}' => $fields['title']);
            if (db_update('areas',$fields,$where) === FALSE) {
                logger("areamanager: area data save failed for area '$area_id': ".db_errormessage());
                $this->output->add_message(t('areamanager_save_area_failure','admin',$params));
            } elseif ((intval($areas[$area_id]['theme_id']) != $theme_id) &&
                ($this->count_existing_theme_properties($area_id,$theme_id) <= 0)) {
                // If the user changed the theme AND if there is no theme config yet, make sure there is one
                if ($this->reset_theme_defaults($area_id,$theme_id)) {
                    logger("areamanager: success saving area AND theme properties in area '$area_id', theme '$theme_id'",
                           LOG_DEBUG);
                    $this->output->add_message(t('areamanager_save_area_success','admin',$params));
                } else {
                    logger("areamanager: theme '$theme_id' data save failed for area '$area_id': ".db_errormessage());
                    $this->output->add_message(t('areamanager_save_area_failure','admin',$params));
                }
            } else {
                logger("areamanager: success saving changed properties in area '$area_id'",LOG_DEBUG);
                $this->output->add_message(t('areamanager_save_area_success','admin',$params));
            }
            $areas = get_area_records(TRUE); // TRUE means force reread of area records
            $this->area_overview();
        } else {
            logger("areamanager: weird: invalid dialog '$dialog' in area_save (area=$area_id)",LOG_DEBUG);
            $this->area_overview();
        }
    } // area_save()


    /** save the newly added area to the database
     *
     * This saves the essential information of a new area to the database,
     * using sensible defaults for the other fields. Also, a data directory
     * is created and the relative path is stored in the new area record.
     *
     * If something goes wrong, the user can redo the dialog, otherwise we
     * return to the area overview.
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME
     * @uses $CFG
     * @uses $USER
     */
    function area_savenew() {
        global $WAS_SCRIPT_NAME,$USER,$CFG;

        // 1 -- bail out if user pressed cancel button
        if (isset($_POST['button_cancel'])) {
            $this->output->add_message(t('cancelled','admin'));
            $this->area_overview();
            return;
        }
        // 2 -- dow we have permission to add an area?
        if (!$USER->has_site_permissions(PERMISSION_SITE_ADD_AREA)) {
            logger("areamanager: user attempted to add an area without permission");
            $msg = t('task_area_add_access_denied','admin');
            $this->output->add_message($msg);
            $this->output->add_popup_bottom($msg);
            $this->area_overview();
            return;
        }

        // 3 -- validate the data
        $invalid = FALSE;
        $dialogdef = $this->get_dialogdef_add_area();

        // 3A -- check for generic errors (string too short, number too small, etc)
        if (!dialog_validate($dialogdef)) {
            $invalid = TRUE;
        }

        // 3B -- additional check: valid datadirectory name entered
        $path = $dialogdef['area_path']['value'];
        $fname = (isset($dialogdef['area_path']['label'])) ? $dialogdef['area_path']['label'] : 'area_path';
        $params = array('{FIELD}' => str_replace('~','',$fname));
        $areadata_directory = sanitise_filename($path);
        if ($path != $areadata_directory) {
            // User probably entered a few 'illegal' characters. This is no good
            $dialogdef['area_path']['value'] = $areadata_directory; // 'Help' user with a better proposition
            ++$dialogdef['area_path']['errors'];
            $params['{VALUE}'] = htmlspecialchars($path);
            $dialogdef['area_path']['error_messages'][] = t('validate_bad_filename','',$params);
            $invalid = TRUE;
        }

        // 3C -- additional check: unique datadirectory name entered
        $areadata_directory = strtolower($areadata_directory);
        $where = array('path' => $areadata_directory);
        if (db_select_single_record('areas','area_id',$where) !== FALSE) {
            // Oops, a record with that path already exists. Go flag error
            ++$dialogdef['area_path']['errors'];
            $params['{VALUE}'] = $areadata_directory;
            $dialogdef['area_path']['error_messages'][] = t('validate_not_unique','',$params);
            $invalid = TRUE;
        }

        // 3D -- additional check: can we create said directory?
        $areadata_full_path = $CFG->datadir.'/areas/'.$areadata_directory;
        $areadata_directory_created = @mkdir($areadata_full_path,0700);
        if ($areadata_directory_created) {
            @touch($areadata_full_path.'/index.html'); // "protect" the newly created directory from prying eyes
        } else {
            // Mmmm, failed; probably already exists then. Oh well. Go flag error.
            ++$dialogdef['area_path']['errors'];
            $params['{VALUE}'] = '/areas/'.$areadata_directory;
            $dialogdef['area_path']['error_messages'][] = t('validate_already_exists','',$params);
            $invalid = TRUE;
        }

        // 3E -- if there were any errors go redo dialog while keeping data already entered
        if ($invalid) {
            if ($areadata_directory_created) { // Only get rid of the directory _we_ created
                @unlink($areadata_full_path.'/index.html');
                @rmdir($areadata_full_path);
            }
            // there were errors, show them to the user and do it again
            foreach($dialogdef as $k => $item) {
                if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                    $this->output->add_message($item['error_messages']);
                }
            }
            $this->output->add_content('<h2>'.t('areamanager_add_area_header','admin').'</h2>');
            $this->output->add_content(t('areamanager_add_area_explanation','admin'));
            $href = href($WAS_SCRIPT_NAME,$this->a_param(AREAMANAGER_CHORE_SAVE_NEW));
            $this->output->add_content(dialog_quickform($href,$dialogdef));
            return;
        }

        // 4 -- go save the new area
        $sort_order = $this->sort_order_new_area();
        $now = strftime('%Y-%m-%d %T');
        $theme_id = intval($dialogdef['area_theme_id']['value']);
        $area_title = $dialogdef['area_title']['value'];
        $fields = array(
            'title' => $area_title,
            'is_private' => ($dialogdef['area_is_private']['value'] == 1) ? TRUE : FALSE,
            'is_active' => TRUE,
            'is_default' => FALSE,
            'path' => $areadata_directory,
            'metadata' => '',
            'sort_order' => $sort_order,
            'theme_id' => $theme_id,
            'ctime' => $now,
            'cuser_id' => $USER->user_id,
            'mtime' => $now,
            'muser_id' => $USER->user_id,
            );

        // 4A -- store area data
        $success = TRUE; 
        $new_area_id = db_insert_into_and_get_id('areas',$fields,'area_id');
        if ($new_area_id === FALSE) {
            if ($areadata_directory_created) { // Only get rid of the directory _we_ created
                @unlink($areadata_full_path.'/index.html');
                @rmdir($areadata_full_path);
            }
            logger("areamanager: saving new area failed: ".db_errormessage());
            $success = FALSE;
        }

        // 4B -- handle theme settings for this area
        if ($success) {
            if (!$this->reset_theme_defaults($new_area_id,$theme_id)) {
                logger("areamanager: saving new area-theme properties failed: ".db_errormessage());
                $success = FALSE;
            }
        }
        // 5 -- tell user about results of the operation
        if ($success) {
            $params = array('{AREA}' => $new_area_id,'{AREA_FULL_NAME}' => $area_title);
            $this->output->add_message(t('areamanager_savenew_area_success','admin',$params));
            logger(sprintf("areamanager: success saving new area '%d' %s with data directory /areas/%s",
                           $new_area_id,$area_title,$areadata_directory));
        } else {
            $this->output->add_message(t('areamanager_savenew_area_failure','admin'));
        }
        $this->area_overview();
    } // area_savenew()


    /** construct the add area dialog
     *
     * this constructs an add area dialog definition with the bare minimal fields.
     *
     * @return array contains the dialog definition
     */
    function get_dialogdef_add_area() {
        $dialogdef = array(
            'dialog' => array(
                'type' => F_INTEGER,
                'name' => 'dialog',
                'value' => AREAMANAGER_DIALOG_ADD,
                'hidden' => TRUE
            ),
            'area_title' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'area_title',
                'minlength' => 1,
                'maxlength' => 240,
                'columns' => 30,
                'label' => t('areamanager_add_area_title_label','admin'),
                'title' => t('areamanager_add_area_title_title','admin'),
                'value' => '',
                ),
            'area_is_private' => array(
                'type' => F_CHECKBOX,
                'name' => 'area_is_private',
                'options' => array(1 => t('areamanager_add_area_is_private_check','admin')),
                'label' => t('areamanager_add_area_is_private_label','admin'),
                'title' => t('areamanager_add_area_is_private_title','admin'),
                ),
            'area_path' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'area_path',
                'minlength' => 1,
                'maxlength' => 240,
                'columns' => 50,
                'label' => t('areamanager_add_area_path_label','admin'),
                'title' => t('areamanager_add_area_path_title','admin'),
                'value' => ''
                ),
            'area_theme_id' => array(
                'type' => F_LISTBOX,
                'name' => 'area_theme_id',
                'value' => '',
                'label' => t('areamanager_add_area_theme_id_label','admin'),
                'title' => t('areamanager_add_area_theme_id_title','admin'),
                'options' => $this->get_options_themes(),
                ),
            'button_save' => dialog_buttondef(BUTTON_SAVE),
            'button_cancel' => dialog_buttondef(BUTTON_CANCEL)
            );
        return $dialogdef;
    } // get_dialogdef_add_area()


    /** construct the edit area basic properties dialog
     *
     * Note that this dialog makes the private/public flag readonly;
     * this field is only displayed. Also note that the datadirectory
     * path is shown readonlye too. It is simply too much hassle to allow
     * the user to change this path because that would imply that the
     * existing files should move along. We'll keep it simple.
     * However, it must be possible to look up the name of the data dir,
     * so therefore we do display it.
     * 
     * @param int $area_id indicates for which area
     * @return array contains the dialog definition
     */
    function get_dialogdef_edit_area($area_id) {
        $dialogdef = array(
            'dialog' => array(
                'type' => F_INTEGER,
                'name' => 'dialog',
                'value' => AREAMANAGER_DIALOG_EDIT,
                'hidden' => TRUE
                ),
            'area_title' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'area_title',
                'minlength' => 1,
                'maxlength' => 240,
                'columns' => 50,
                'label' => t('areamanager_edit_area_title_label','admin'),
                'title' => t('areamanager_edit_area_title_title','admin'),
                'value' => '',
                ),
            'area_is_active' => array(
                'type' => F_CHECKBOX,
                'name' => 'area_is_active',
                'options' => array(1 => t('areamanager_edit_area_is_active_check','admin')),
                'label' => t('areamanager_edit_area_is_active_label','admin'),
                'title' => t('areamanager_edit_area_is_active_title','admin'),
                ),
            'area_is_private' => array(
                'type' => F_CHECKBOX,
                'name' => 'area_is_private',
                'options' => array(1 => t('areamanager_edit_area_is_private_check','admin')),
                'label' => t('areamanager_edit_area_is_private_label','admin'),
                'title' => t('areamanager_edit_area_is_private_title','admin'),
                'viewonly' => TRUE
                ),
            'area_path' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'area_path',
                'minlength' => 0,
                'maxlength' => 240,
                'columns' => 50,
                'label' => t('areamanager_edit_area_path_label','admin'),
                'title' => t('areamanager_edit_area_path_title','admin'),
                'value' => '',
                'viewonly' => TRUE
                ),
            'area_metadata' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'area_metadata',
                'columns' => 50,
                'rows' => 10,
                'label' => t('areamanager_edit_area_metadata_label','admin'),
                'title' => t('areamanager_edit_area_metadata_title','admin'),
                'value' => '',
                ),
            'area_sort_order' => array(
                'type' => F_INTEGER,
                'name' => 'area_sort_order',
                'columns' => 10,
                'maxlength' => 10,
                'label' => t('areamanager_edit_area_sort_order_label','admin'),
                'title' => t('areamanager_edit_area_sort_order_title','admin'),
                ),
            'area_theme_id' => array(
                'type' => F_LISTBOX,
                'name' => 'area_theme_id',
                'value' => '',
                'label' => t('areamanager_edit_area_theme_id_label','admin'),
                'title' => t('areamanager_edit_area_theme_id_title','admin'),
                'options' => $this->get_options_themes(),
                ),
            'button_save' => dialog_buttondef(BUTTON_SAVE),
            'button_cancel' => dialog_buttondef(BUTTON_CANCEL)
            );
        return $dialogdef;
    } // get_dialogdef_edit_area()


    /** fill the dialog with current area data from the database
     *
     * Note that area_path is no longer a part of the dialogdef
     *(see also {@link get_dialogdef_edit_area()}) but if it were, the
     * data would still be fetched.
     *
     * @param array &$dialogdef contains dialog definition that requires the data
     * @param array $record conveniently holds a copy of the area record
     * @return void &$dialogdef is filled with data from the record
     */
    function get_dialog_data(&$dialogdef,$record) {
        foreach ($dialogdef as $k => $item) {
            if (isset($item['name'])) {
                $name = $item['name'];
            } else {
                continue;
            }
            switch ($name) {
            case 'area_title':
                $dialogdef[$k]['value'] = $record['title'];
                break;
            case 'area_is_active':
                $dialogdef[$k]['value'] = (db_bool_is(TRUE,$record['is_active'])) ? '1' : '';
                break;
            case 'area_is_private':
                $dialogdef[$k]['value'] = (db_bool_is(TRUE,$record['is_private'])) ? '1' : '';
                break;
            case 'area_path':
                $dialogdef[$k]['value'] = $record['path'];
                break;
            case 'area_metadata':
                $dialogdef[$k]['value'] = $record['metadata'];
                break;
            case 'area_sort_order':
                $dialogdef[$k]['value'] = $record['sort_order'];
                break;
            case 'area_theme_id':
                $dialogdef[$k]['value'] = $record['theme_id'];
                break;
            }
        }
    } // get_dialog_data()


    /** fetch a list of themes available for an area
     *
     * this retrieves a list of themes that can be used as a list of options in
     * a listbox or radiobuttons. Only the active themes are considered.
     * The names of the themes that are displayed in the list are translated
     * (retrieved from the themes language files). The list is ordered by
     * that translated theme name.
     *
     * @return array ready for use as an options array in a listbox or radiobuttons
     */
    function get_options_themes() {

        // 1 - get raw list of active themes
        $records = $this->get_theme_records();
        $options = array();
        if (($records === FALSE) || (empty($records))) {
            logger('configurationmanager: weird, no active themes? must be configuration error');
            return $options;
        }

        // 2 - prepare a raw list of modules with translated title/description
        $options_order = array(); // helper-array for easy sorting by $title
        foreach ($records as $theme_id => $theme) {
            $theme_name = $theme['name'];
            $title = t('title','t_'.$theme_name);
            $options_order[$theme_id] = $title;
            $records[$theme_id]['title'] = "$title ($theme_name)";
            $records[$theme_id]['description'] = t('description','t_'.$theme_name);
        }
        asort($options_order);

        // 3 - construct a sorted list of modules from the raw list and helper array
        foreach($options_order as $theme_id => $title) {
            $options[$theme_id] = array('option' => $records[$theme_id]['title'],
                                         'title' => $records[$theme_id]['description']);
        }
        unset($options_order);
        return $options;
    } // get_options_themes()


    /** retrieve a list of all available theme records
     *
     * this returns a list of active theme-records or FALSE if none are are available
     * The list is cached via a static variable so we don't have to go to the
     * database more than once for this.
     * Note that the returned array is keyed with theme_id.
     *
     * @param bool $forced if TRUE forces reread from database (resets the cache)
     * @return array|bool FALSE if no themes available or an array with theme-records
     */
    function get_theme_records($forced = FALSE) {
        static $records = NULL;
        if (($records === NULL) || ($forced)) {
            $tablename = 'themes';
            $fields = '*';
            $where = array('is_active' => TRUE);
            $order = array('theme_id');
            $records = db_select_all_records($tablename,$fields,$where,$order,'theme_id');
        }
        return $records;
    } // get_theme_records()


    /** show the name of an area and ask the user for a confirmation of deletion
     *
     * this displays a confirmation question for deletion of an area.
     * If the user presses Delete button, the area will be deleted,
     * if the user presses Cancel then nothing is deleted.
     *
     * @param int $area_id
     * @param array &$areas records with area information of all areas
     * @return void results are returned as output in $this->output
     */
    function show_dialog_confirm_delete($area_id,&$areas) {
        global $WAS_SCRIPT_NAME;
        $dialogdef = array(
            'dialog' => array(
                'type' => F_INTEGER,
                'name' => 'dialog',
                'value' => AREAMANAGER_DIALOG_DELETE,
                'hidden' => TRUE
            ),
            dialog_buttondef(BUTTON_DELETE),
            dialog_buttondef(BUTTON_CANCEL)
            );
        $area = $areas[$area_id];
        $params = array('{AREA}' => $area_id,'{AREA_FULL_NAME}' => $area['title']);

        $this->output->add_content('<h2>'.t('delete_an_area_header','admin',$params).'</h2>');
        $this->output->add_content(t('delete_area_explanation','admin'));
        $this->output->add_content('<ul>');
        $area_description = t((db_bool_is(TRUE,$area['is_private'])) ? 
                               'area_delete_private_title' : 'area_delete_public_title','admin',$params);
        $area_description  .= (db_bool_is(TRUE,$area['is_active'])) ? '' : ' ('.t('inactive','admin').')';
        $this->output->add_content('  <li class="level0">'.$area_description);
        $this->output->add_content('</ul>');
        $this->output->add_content(t('delete_are_you_sure','admin'));
        $href = href($WAS_SCRIPT_NAME,$this->a_param(AREAMANAGER_CHORE_DELETE,$area_id));
        $this->output->add_content(dialog_quickform($href,$dialogdef));
    } // show_dialog_confirm_delete()


    /** display the edit menu via $this->output
     *
     * This displays a clickable menu on in the menu area.
     *
     * @param int $area_id the area currently being edited
     * @param string $current_chore the currently selected edit screen (used to emphasize the option in the menu)
     * @return void results are returned as output in $this->output
     */
    function show_edit_menu($area_id,$current_chore='') {
        global $WAS_SCRIPT_NAME;
        $menu_items = array(
            array(
                'chore' => AREAMANAGER_CHORE_EDIT,
                'anchor' => t('areamanager_menu_edit','admin'),
                'title' => t('areamanager_menu_edit_title','admin')
            ),
            array(
                'chore' => AREAMANAGER_CHORE_EDIT_THEME,
                'anchor' => t('areamanager_menu_edit_theme','admin'),
                'title' => t('areamanager_menu_edit_theme_title','admin')
            ),
            array(
                'chore' => AREAMANAGER_CHORE_RESET_THEME,
                'anchor' => t('areamanager_menu_reset_theme','admin'),
                'title' => t('areamanager_menu_reset_theme_title','admin')
            )
        );
        $this->show_parent_menu = FALSE; // Mke sure parent doesn't add a menu too
        $this->output->add_menu('<h2>'.t('menu','admin').'</h2>');
        $this->output->add_menu('<ul>');
        foreach($menu_items as $item) {
            $parameters = $this->a_param($item['chore'],$area_id);
            $attributes = array('title' => $item['title']);
            if ($current_chore == $item['chore']) {
                $attributes['class'] = 'current';
            }
            $this->output->add_menu('  <li>'.html_a($WAS_SCRIPT_NAME,$parameters,$attributes,$item['anchor']));
        }
        $this->output->add_menu('</ul>');
    } // show_edit_menu()


    // ==================================================================
    // ======================== UTILITY ROUTINES ========================
    // ==================================================================


    /** construct a clickable icon to set the default area
     *
     * the 'default' icon is displayed for the default area, the
     * 'non-default' icon for all others. The user is allowed to make the
     * area the default area if the user has edit permissions for both the
     * old and the new default area.
     *
     * @param int $area_id 
     * @param array &$areas records with area information of all areas
     * @return string ready-to-use A-tag
     * @uses $CFG
     * @uses $USER
     * @uses $WAS_SCRIPT_NAME
     */
    function get_icon_home($area_id,&$areas) {
        global $CFG,$WAS_SCRIPT_NAME,$USER;

        // 1A -- check out permissions: we need them for this area AND the current default area
        $user_has_permission = FALSE;
        if ($USER->has_area_permissions(PERMISSION_AREA_EDIT_AREA,$area_id)) {
            $user_has_permission = TRUE;
            foreach($areas as $area) {
                if (db_bool_is(TRUE,$area['is_default'])) {
                    $user_has_permission = $USER->has_area_permissions(PERMISSION_AREA_EDIT_AREA,$area['area_id']);
                    break;
                }
            }
        }
        // 1B -- construct a title/mouseover depending on permissions/current default area
        if (!($user_has_permission)) {
            $title = t('icon_area_default_access_denied','admin');
        } elseif (db_bool_is(TRUE,$areas[$area_id]['is_default'])) {
            $title = t('icon_area_is_default','admin');
        } else {
            $title = t('icon_area_default','admin');
        }

        // 2 -- construct the icon (image or text)
        if (db_bool_is(TRUE,$areas[$area_id]['is_default'])) {
            if ($USER->high_visibility) {
                $anchor = html_tag('span','class="icon"','['.t('icon_area_default_text','admin').']');
            } else {
                $img_attr = array('height' => 16, 'width' => 16,
                                  'title' => $title, 'alt' => t('icon_area_default_alt','admin'));
                $anchor = html_img($CFG->progwww_short.'/graphics/startsection.gif',$img_attr);
            }
        } else {
            if ($USER->high_visibility) {
                $anchor = html_tag('span','class="icon"','['.t('icon_area_not_default_text','admin').']');
            } else {
                $img_attr = array('height' => 16, 'width' => 16, 
                                  'title' => $title, 'alt' => t('icon_area_not_default_alt','admin'));
                $anchor = html_img($CFG->progwww_short.'/graphics/not_startsection.gif',$img_attr);
            }
        }

        // 3 -- construct the A tag
        $a_params = $this->a_param(AREAMANAGER_CHORE_SET_DEFAULT,$area_id);
        $a_attr = array('name' => 'area'.strval($area_id),'title' => $title);
        if (!$user_has_permission) {
            $a_attr['class'] = 'dimmed';
        }
        return html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
    } // get_icon_home()


    /** construct a clickable icon to delete this area
     *
     * @param int $area_id the area to delete
     * @return string ready-to-use A-tag
     * @uses $CFG
     * @uses $USER
     * @uses $WAS_SCRIPT_NAME
     * @todo should we check to see if the area is empty before showing delete icon?
     *       Or is it soon enough to refuse deletion when the user already clicked the icon?
     *       I'd say the latter. For now...
     */
    function get_icon_delete($area_id) {
        global $CFG,$WAS_SCRIPT_NAME,$USER;

        // 1 -- does the user have permission to delete this node at all?
        $user_has_permission = $USER->has_site_permissions(PERMISSION_SITE_DROP_AREA);

        // 2 -- construct the icon (image or text)
        $title = t(($user_has_permission) ? 'icon_area_delete' : 'icon_area_delete_access_denied','admin');
        if ($USER->high_visibility) {
            $anchor = html_tag('span','class="icon"','['.t('icon_area_delete_text','admin').']');
        } else {
            $img_attr = array('height' => 16, 'width' => 16, 'title' => $title, 'alt' => t('icon_area_delete_alt','admin'));
            $anchor = html_img($CFG->progwww_short.'/graphics/delete.gif',$img_attr);
        }

        // 3 -- construct the A tag
        $a_params = $this->a_param(AREAMANAGER_CHORE_DELETE,$area_id);
        $a_attr = array('title' => $title);
        if (!$user_has_permission) {
            $a_attr['class'] = 'dimmed';
        }
        return html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
    } // get_icon_delete()


    /** construct a clickable icon to edit theme properties of this area (edit advanced)
     *
     * @param int $area_id the area to edit
     * @return string ready-to-use A-tag
     * @uses $CFG
     * @uses $USER
     * @uses $WAS_SCRIPT_NAME
     */
    function get_icon_edit($area_id) {
        global $CFG,$WAS_SCRIPT_NAME,$USER;

        // 1 -- does the user have permission to edit this node at all?
        $user_has_permission = $USER->has_area_permissions(PERMISSION_AREA_EDIT_AREA,$area_id);

        // 2 -- construct the icon (image or text)
        $title = t(($user_has_permission) ? 'icon_area_edit' : 'icon_area_edit_access_denied','admin');
        if ($USER->high_visibility) {
            $anchor = html_tag('span','class="icon"','['.t('icon_area_edit_text','admin').']');
        } else {
            $img_attr = array('height' => 16, 'width' => 16,
                              'title' => $title, 'alt' => t('icon_area_edit_alt','admin'));
            $anchor = html_img($CFG->progwww_short.'/graphics/edit.gif',$img_attr);
        }

        // 3 -- construct the A tag
        $a_params = $this->a_param(AREAMANAGER_CHORE_EDIT_THEME,$area_id);
        $a_attr = array('title' => $title);
        if (!$user_has_permission) {
            $a_attr['class'] = 'dimmed';
        }
        return html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
    } // get_icon_edit()


    /** shorthand for the anchor parameters that lead to the area manager
     *
     * @param string $chore the next chore that could be done
     * @param int|null $area_id the area of interest or NULL if none
     * @return array ready-to-use array with parameters for constructing a-tag
     */
    function a_param($chore,$area_id=NULL) {
        $parameters = array('job' => JOB_CONFIGURATIONMANAGER,'task' => TASK_AREAS,'chore' => $chore);
        if (!is_null($area_id)) {
            $parameters['area'] = strval($area_id);
        }
        return $parameters;
    } // a_param()


    /** determine the value for the sort order of a new area
     *
     * this calculates a new sort order value based on the existing minimum
     * or maximum values of existing areas (if any).
     *
     * Note
     * The default sort order for areas differs from that of pages and sections:
     * we assume that the person managing areas knows where to find the newly added area
     * (at the bottom) whereas for a page/section maintainer it is probably more convenient
     * to have a new page/section added at the top of the (perhaps very long) list.
     *
     * @param bool $at_begin if TRUE the new area is placed before all others, otherwise it is added at the end
     * @return int ready-to-use value for sort order of a new record
     */
    function sort_order_new_area($at_begin=FALSE) {
        $record = db_select_single_record('areas','COUNT(*) AS n, MIN(sort_order) AS lo, MAX(sort_order) AS hi');
        if ($record === FALSE) {
            logger("areamanager: calculation of new sort order failed (using default 10): ".db_errormessage());
            $sort_order = 10;
        } elseif ($record['n'] == 0) {
            $sort_order = 10; // since there are no areas, we'll use the default value of 10 for the new and only area
        } elseif ($at_begin) {
            $sort_order = $record['lo'] - 10;
        } else {
            $sort_order = $record['hi'] + 10;
        }
        return $sort_order;
    } // sort_order_new_area()
    

    /** reset the theme properties of an area to the default values
     *
     * this deletes any existing properties for the combination of $area_id
     * and $theme_id from the properties table. After that, a copy of the
     * defaults of the theme $theme_id is inserted in the areas-themes properties
     * table. _Eventually_ this may exhaust the available primary keys in the
     * area-theme-properties table. Oh well: with a handful of properties each time
     * you need a lot of resets...
     *
     * This routine returns TRUE on success or FALSE on error. In the latter case
     * either we were not able to delete old values OR we were not able to insert
     * a copy of the default properties. 
     *
     * @param int $area_id
     * @param int $theme_id
     * @return TRUE on success, FALSE on failure
     */
    function reset_theme_defaults($area_id,$theme_id) {
        global $DB;

        //
        // 1 -- get rid of the old values by deleting the existing records
        //
        $where = array('area_id' => intval($area_id),'theme_id' => intval($theme_id));
        $table = 'themes_areas_properties';
        // db_start_transaction();
        $retval = db_delete($table,$where);
        if ($retval === FALSE) {
            logger("areamanager: delete properties of theme '$theme_id' in area '$area_id' ".
                   "from table '$table' failed: ".db_errormessage());
            // db_rollback_transaction();
            return FALSE;
        }
        logger("areamanager: properties of theme '$theme_id' in area '$area_id' ".
               "from table '$table': records deleted from '$table': $retval ",LOG_DEBUG);

        //
        // 2 -- insert new values by copying from the theme defaults table
        //
        $sql = sprintf('INSERT INTO %s%s(area_id,theme_id,name,type,value,extra,sort_order,description) '.
                       'SELECT %d AS area_id,theme_id,name,type,value,extra,sort_order,description '.
                       'FROM %s%s '.
                       'WHERE theme_id = %d',
                        $DB->prefix,'themes_areas_properties',intval($area_id),
                        $DB->prefix,'themes_properties',intval($theme_id));
        $retval = $DB->exec($sql);
        if ($retval === FALSE) {
            logger("areamanager: insert defaults for theme '$theme_id' in area '$area_id' failed: ".db_errormessage());
            // db_rollback_transaction();
            return FALSE;
        }
        logger("areamanager: default properties of theme '$theme_id' set for area '$area_id'; ".
               "records added to '$table': $retval ",LOG_DEBUG);
            // db_commit_transaction()
         return TRUE;
    } // reset_theme_defaults()


    /** determine the number of existing properties for a theme in an area
     *
     * @param int $area_id
     * @param int $theme_id
     * @return int number of existing properties
     */
    function count_existing_theme_properties($area_id,$theme_id) {
        $where = array('area_id' => intval($area_id),'theme_id' => intval($theme_id));
        $record = db_select_single_record('themes_areas_properties','count(*) as properties',$where);
        if ($record === FALSE) {
            logger("areamanager: error counting properties in theme '$theme_id and area '$area_id': ".db_errormessage());
            $num = 0;
        } else {
            $num = intval($record['properties']);
        }
        return $num; 
    } // count_existing_theme_properties()
}

?>