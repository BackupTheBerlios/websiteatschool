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

/** /program/lib/aclmanager.class.php - dealing with access control lists
 *
 * This file defines a class for dealing (edit+save but not create or delete) with
 * lists of access control parameters. The main purpose is to allow easy editing of
 * the many many permission bitmaps that are possible for both users and groups.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: aclmanager.class.php,v 1.4 2011/09/21 18:54:20 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** acl for intranet permissions */
define('ACL_TYPE_INTRANET',1);

/** acl for administrator permissions */
define('ACL_TYPE_ADMIN',2);

/** acl for pagemanager permissions */
define('ACL_TYPE_PAGEMANAGER',3);

/** acl for individual module permissions */
define('ACL_TYPE_MODULE',4);


/** limit available role options to 'none' and 'guru' (used in pagemanager permissions) */
define('ACL_LEVEL_NONE',0);

/** limit available role options to pages (used in pagemanager permissions) */
define('ACL_LEVEL_PAGE',1);

/** limit available role options to pages and sections (used in pagemanager permissions) */
define('ACL_LEVEL_SECTION',2);

/** limit available role options to pages, sections and areas (used in pagemanager permissions) */
define('ACL_LEVEL_AREA',3);

/** no limit on available role options (used in pagemanager permissions) */
define('ACL_LEVEL_SITE',4);


/** class for manipulating (edit+save) access control lists
 *
 *
 * Overview<br>
 * --------
 *
 * Every user account is associated with an access control list.
 * This access control list boils down to a total of six tables
 * in the database:
 *
 *  - acls
 *  - acls_areas
 *  - acls_nodes
 *  - acls_modules
 *  - acls_modules_areas
 *  - acls_modules_nodes
 *
 * These tables are defined as follows.
 * <pre>
 *
 * acls:
 * acl_id                serial*
 * permissions_jobs      int
 * permissions_intranet  int
 * permissions_modules   int
 * permissions_nodes     int
 *
 * acls_areas:
 * acl_id                int* (link to acls)
 * area_id               int* (link to areas)
 * permissions_intranet  int
 * permissions_modules   int
 * permissions_nodes     int
 *
 * acls_nodes:
 * acl_id                int* (link to acls)
 * node_id               int* (link to nodes)
 * permissions_modules   int
 * permissions_nodes     int
 *
 * acls_modules:
 * acl_id                int* (link to acls)
 * module_id             int* (link to modules)
 * permissions_modules   int
 *
 * acls_modules_areas:
 * acl_id                int* (link to acls)
 * module_id             int* (link to modules)
 * area_id               int* (link to areas)
 * permissions_modules   int
 *
 * acls_modules_nodes:
 * acl_id                int* (link to acls)
 * module_id             int* (link to modules)
 * node_id               int* (link to nodes)
 * permissions_modules   int
 *
 * *marked fields are (part of) the primary key
 * </pre>
 *
 * The six tables mentioned above deal with the following
 * permission bitmasks.
 *
 *  - permissions_jobs
 *  - permissions_intranet
 *  - permissions_modules
 *  - permissions_nodes
 *
 * The reasons to split these permission masks into six tables are:
 *
 * 1. Some permissions only apply to the site-level and it makes no
 *    sense to specify them for a particular combination of area,
 *    node or module. Example: permissions_jobs.
 *
 * 2. Some permissions can be granted for current and future objects.
 *    Example: permissions_intranet. If these permissions are granted
 *    at the site level (in table acls), then they apply not oly to all
 *    current protected areas but also to all future protected areas.
 *    The same permissions could be granted on a per-area-basis but that
 *    might require adjusting the permissions once a new protected
 *    area is added to the site.
 *
 * 3. Sometimes it is more convenient to specify the permissions on
 *    a higher level because otherwise the size of the database may
 *    get out of hand. Example: if every user has a permission bitmask
 *    for every node on the site, the corresponding acl would have
 *    number_of_users x number_of_nodes entries. That is completely
 *    unmanageable, even for small to medium size sites.
 *
 *
 * Users and group/capacities<br>
 * --------------------------
 *
 * A user can also participate in a group in a particular
 * capacity, e.g. member of group 'grade8' in the 'pupil'-
 * or the 'teacher'-capacity. Every combination of group
 * and capacity (eg 'grade8/pupil') is also associated with
 * an access control list.
 *
 * The full access control list for a user is the combination
 * of the ACL directly associated with the user account and the
 * ACLs associated with the group/capacities that apply to the
 * user account.  The effective permissions for a user are the
 * result of OR'ing the permissions of all ACLs.
 *
 * A specific permission is always indicated by a bit set to
 * '1'. If a particular bit is set to '0', the user does not
 * have the corresponding permission. This implies that the
 * (special) bitmask 0 (zero, 32 bits are all not set)
 * corresponds to 'no permissions at all'. It also implies
 * that the (special) bitmask -1 (minus one, 32 bits are all
 * set) equates to 'all permissions'.
 *
 * Therefore, the *easy* way to grant access is to set the
 * permissions bitmask to -1. This is the so-called Guru-option
 * or -role or the Guru-permissions. However, note that
 * granting a user or a group/capacity Guru-permissions, means
 * that that user (these users) can do serious harm to the
 * system because she (they) are allowed to do anything. The
 * *safe* way is to grant as few permissions as possible.
 *
 * Roles<br>
 * -----
 *
 * In order to make it easier to setup the access controls and
 * stay away from directly manipulating individual bits in a
 * bitmask the various permission bits are combined into roles.
 *
 * Two roles are always available for selection:
 *
 *  - permissions == 0: ROLE_NONE
 *  - permissions == -1: ROLE_GURU
 *
 * Defining other roles is done at the appropriate place, e.g.
 * inside the code for a module.
 *
 * Example: suppose that there is a module called 'Forum' which
 * works with authenticated users. Depending on this module's
 * permission bits the users are allowed to perform certain
 * actions, e.g.
 *
 *  - read messages in the forum (bit 0, value 1)
 *  - write messages in the forum (bit 1, value 2)
 *  - edit their own messages (bit 2, value 4)
 *  - edit other users' messages (bit 3, value 8)
 *  - manage useraccounts for the forum (bit 4, value 8)
 *
 * This leads to many possible combinations of set and reset bits.
 * However, it is more practical to combine these bits into a
 * few roles with a descriptive name:
 *
 *  - permissions = 1: ROLE_FORUM_VISITOR => "Visitor"
 *  - permissions = 1+2+4 = 7: ROLE_FORUM_MEMBER => "Member"
 *  - permissions = 1+2+4+8 = 15: ROLE_FORUM_MODERATOR => "Moderator"
 *  - permissions = 1+2+4+8+16 = 31: ROLE_FORUM_ADMINISTRATOR => "Administrator"
 *
 * By using these symbolic names for certain combinations of bitmasks
 * it becomes easier to manage many users and many forums without
 * having to know what every bit means, exactly.
 *
 * Obviously these roles (defined via the module in this example)
 * will end up in a dropdown list where the appropriate role
 * can be assigned.
 *
 * Note that it is not necessary to have hierarchical roles as
 * demontrated in this example. It is very wel possible to
 * define two roles that must work together: ROLE_EDITOR could
 * be a bitmask that allows for adding (1), editing (2),
 * deleting (4), previewing (8) news articles whereas
 * ROLE_PUBLISHER could be limited to previewing (8) and
 * publishing (16) news articles, but not editing them. That
 * would make sure that at least two different people are
 * required to create and publish an article. (However, any
 * 'Guru', with all permissions granted due to the -1 bitmask,
 * could create + publish articles by herself.)
 *
 *
 * Module-permissions in acls, acls_areas and acls_nodes<br>
 * -----------------------------------------------------
 *
 * The fields permissions_modules in the tables acls,
 * acls_areas and acls_nodes should be considered as 'blanket
 * permissions'.  If a permission is set in either of these
 * tables, the permissions apply to *all* modules at site level
 * (acls), area_level (acls_areas) or node_level (acls_nodes).
 *
 * Because these permissions apply to *all* modules, the only
 * realistic roles in these cases can be either ROLE_NONE
 * (permissions = 0) or ROLE_GURU (permissions = -1). Any other
 * role could be meaningless for one or more modules.
 *
 * Furthermore, it is a little over the top to specify
 * permissions for *all* modules in a particular node. (It
 * almost doesn't make sense). Therefore, the corresponding
 * dialog only deals with these two roles ROLE_NONE and
 * ROLE_GURU at the site level and the area level. The node
 * level is not used for modules (but it is for pagemanager
 * permissions - the field permissions_nodes - at the node
 * level).
 *
 *
 * Typical usage<br>
 * -------------
 *
 * Example 1: displaying a dialog with intranet permissions for a group
 *
 * <pre>
 * $acl = new AclManager($output,$acl_id,ACL_TYPE_INTRANET);
 * $acl->set_action(array('job'=>'accountmanager','task'=>'groupsave','group'=>'8');
 * $acl->set_dialog(GROUPMANAGER_DIALOG_INTRANET);
 * $acl->show_dialog();
 * ...
 * </pre>
 * The result of this snippet is that a complete dialog is
 * output to the content area of the $output object, including
 * the current values from the database. The whole dialog is
 * wrapped in a FORM-tag with action property based in the array
 * set with the set_action() method.  The dialog is POSTed with 
 * either a Save or a Cancel button.
 *
 * Example 2: saving the data for the intranet permissions for a group
 *
 * <pre>
 * $acl = new AclManager($output,$acl_id,ACL_TYPE_INTRANET);
 * $acl->set_action(array('job'=>'accountmanager','task'=>'groupsave','group'=>'8');
 * $acl->set_dialog($dialog);
 * if (!$acl->save_data()) {
 *     $acl->show_dialog(); // redo dialog, but without a distracting menu this time
 *     return;
 * }
 * ...
 * </pre>
 *
 * The effect of this snippet is that an attempt is done to
 * validate and save the data as it was POSTed (ie: the new values
 * area available in $_POST[]). If, however, saving the data did
 * not work, the dialog is displayed again, this time using the
 * data from $_POST[] rather than from the database.
 *
 * Example 3: displaying a dialog with admin permissions for a user
 *
 * <pre>
 * $related_acls = array($acl_id1 => "group1/capacity1",$acl_id2 => "group2/capacity2", ...);
 * $acl = new AclManager($this->output,$acl_id,ACL_TYPE_ADMIN);
 * $acl->set_related_acls($related_acls);
 * $acl->set_action(array('job'=>'accountmanager','task'=>'usersave','user'=>'23');
 * $acl->set_dialog(USERMANAGER_DIALOG_ADMIN);
 * $acl->show_dialog();
 * </pre>
 *
 * This comparable to example 1. The difference is that in a User-ACL
 * there is an option to display existing permissions from the user's
 * group/capacities. This information is displayed in the third column
 * in the dialog. This provides a clue for the user that certain permissions
 * might already be granted to the user via a group membership.
 * The related permissions are communicated via an array with (integer)
 * acl_id's as key and a string value identifying the group/capacity.
 *
 * @todo there is someting not right with buffering the tabledefs.
 *       If an error occurs, we get FALSE instead of an array. Mmmmm....
 */
class AclManager {
    //
    // Essential variables
    //
    /** @var object|null collects the html output */
    var $output = NULL;

    /** @var int $acl_id identifies the ACL we are dealing with */
    var $acl_id = 0;

    /** @var int $acl_type identifies the type of ACL we are dealing with */
    var $acl_type = 0;

    /** @var array $related_acls if not NULL identifies a list of acl_id => 'description' pairs with related ACLs */
    var $related_acls = NULL;

    /** @var array|null $a_params_save holds the parameters for the action property of the HTML-form that is created */
    var $a_params_save = NULL;

    /** @var string $header the title of the dialog, displayed at the top of the content area  */
    var $header = '';

    /** @var string $intro the introductory text for the dialog, displayed below the $header */
    var $intro = '';

    /** @var int $dialog identifies the exact dialog and it is added to the dialog as hidden field */
    var $dialog = 0;


    //
    // Variables for the pagination feature
    //
    /** @var array $pagination_a_params holds the parameters for linking to another view of the dialog */
    var $pagination_a_params = NULL;

    /** @var int $pagination_limit the preferred size of a screenfull of dialog lines */
    var $pagination_limit = NULL;

    /** @var int $pagination_offset the record where the current screen begins */
    var $pagination_offset = NULL;

    /** @var bool $pagination_enabled if TRUE we do try to paginate the display (default=FALSE) */
    var $pagination_enabled = FALSE;


    //
    // Variables for the area expand/collapse feature (pagemanager)
    //
    /** @var array $area_view_a_params holds the parameters for linking to opening/closing an area */
    var $area_view_a_params = NULL;

    /** @var array|bool $area_view_areas_open identifies which areas are currently 'open' and 'closed' */
    var $area_view_areas_open = FALSE;

    /** @var bool $area_view_enabled if TRUE we add icons to areas so they can expand/collapse (default=FALSE)*/
    var $area_view_enabled = FALSE;


    //
    // Internal variables
    //
    /** @var bool $pagination_total holds the total number of elements to display */
    var $pagination_total = 0;


    /** @var array $dialogdef holds the current dialogdef, maybe including error messages from a failed validation */
    var $dialogdef = NULL;


    /** @var int $dialogdef_areas_total holds the total number of items that could be displayed in the dialogdef */
    var $dialogdef_areas_total = NULL;

    /** @var array $dialogdef_areas holds information of zero or more areas and the number of contained nodes */
    var $dialogdef_areas = array();


    /** constructor for the AclManager
     *
     * this constructs a new AclManager object. Essential inforation such as the acl_id and the acl_type are
     * stored, for future reference.
     *
     * @param object &$output holds the output that eventually is send to the user's browser
     * @param int $acl_id identifies the ACL we are dealing with (primary key in acls table)
     * @param int $acl_type identifies the type of ACL we are dealing with, e.g. ACL_TYPE_INTRANET or ACL_TYPE_ADMIN
     * @return void object setup and data buffered
     */
    function AclManager(&$output,$acl_id,$acl_type) {
        $this->output = &$output;
        $this->acl_id = intval($acl_id);
        $this->acl_type = intval($acl_type);
    } // AclManager()


    /** further initialise the AclManager with related Acl's
     *
     * this stores the array with 0, 1 or more key-value-pairs of the form
     * $acl_id => $group_capacity_name, e.g. 3 => 'staff/member', 4 => 'grade7/teacher'
     *
     * @param array $related_acls identifies a list with related ACLs
     * @return void data is buffered
     */
    function set_related_acls($related_acls = NULL) {
        $this->related_acls = $related_acls;
    } // set_related_acls()


    /** further initialise the AclManager with the dialog action property
     *
     * this stores an array with parameters that must be added to the
     * action property of the HTML form that will be POSTed, i.e. the URL
     * to which the dialog will be posted. Example of such an array is:
     * array('job' => 'accountmanager', 'task' => 'user_save', 'user' => 123);
     * The information in this array is later combined with WAS_SCRIPT_NAME.
     *
     * @param array $a_params
     * @return void data is buffered
     */
    function set_action($a_params = NULL) {
        $this->a_params_save = $a_params;
    } // set_action()


    /** further initialise the AclManager with the dialog header
     *
     * this stores a string that is used as a title for the dialog
     * Note that this header may be extended with a (translated) string
     * like '[{FIRST}-{LAST} of {TOTAL}]' in case of a paginated display.
     *
     * @param string $header text to show as title
     * @return void data is buffered
     */
    function set_header($header = '') {
        $this->header = $header;
    } // set_header()


    /** further initialise the AclManager with the dialog introductory text
     *
     * this stores a string that is displayed after the dialog header.
     * This text supposedly contains some more information about the dialog.
     *
     * @param string $intro introductory text for the dialog
     * @return void data is buffered
     */
    function set_intro($intro = '') {
        $this->intro = $intro;
    } // set_intro()


    /** further initialise the AclManager with the dialog identification
     *
     * this stores an integer number that is used to identify the dialog.
     * This number is subsequently added to the dialog as a hidden field,
     * which makes it possible to identify the dialog once it is POSTed
     *
     * @param int $dialog a unique identification (within this job) of the dialog
     * @return void data is buffered
     */
    function set_dialog($dialog = 0) {
        $this->dialog = $dialog;
    } // set_dialog()


    /** further initialise the AclManager and enable the dialog pagination feature
     *
     * this stores the information that is necessary when a dialog has to be
     * broken up into two or more screens (via the pagination facility in $output).
     * This routine stores the essential information such as the parameters that
     * lead to the correct page (in $a_params) and the current offset. The other
     * necessary parameters are calculated dynamically before {@link add_pagination()}
     * is called.
     *
     * Note that pagination is only enabled after this routine is called at least once;
     * by default we do NOT do pagination. (Actually: pagination is only used in the acl_types
     * ACL_TYPE_PAGEMANAGER and ACL_TYPE_MODULE).
     *
     * @param array $a_params basic parameters (excluding $offset and $limit) that lead to the correct page
     * @param int $limit the preferred size of a screenfull of dialog lines
     * @param int $offset the record where the current screen begins
     * @return void data is buffered and the pagination feature is enabled
     * @uses $CFG;
     */
    function enable_pagination($a_params, $limit, $offset) {
        global $CFG;
        $this->pagination_a_params = $a_params;
        $this->pagination_limit = intval($limit);
        if ($this->pagination_limit <= 0) {
            $this->pagination_limit = $CFG->pagination_height;
        }
        $this->pagination_offset = max(intval($offset),0); // make sure $offset >= 0
        $this->pagination_enabled = TRUE;
    } // enable_pagination()


    /** further initialise the AclManager and enable the area expand/collapse feature
     *
     * this stores the necessary information about 'open' and 'closed' areas.
     * The parameter $areas_open indicates the current state of affairs:
     * ($areas_open === FALSE) means all areas are closed
     * ($areas_open === TRUE) means all areas are opened
     * If $areas_open is an array, it contains area_id's as key and TRUE or FALSE as value.
     * A value of TRUE indicates that an area is currently 'open', FALS or no value set means 'closed'.
     *
     * The parameters in $a_params combined with $WAS_SCRIPT_NAME yield an URL where the
     * changes are processed.
     *
     * @param array $a_params basic parameters (excluding $area) that lead to the page where expand/collapse is processed
     * @param array|bool $areas_open indicator(s) for 'open' and 'closed' areas
     * @return void data is buffered and the area expand/collapse feature is enabled
     */
    function enable_area_view($a_params, $areas_open) {
        $this->area_view_a_params = $a_params;
        $this->area_view_areas_open = $areas_open;
        $this->area_view_enabled = TRUE;
    } // enable_area_view()


    /** show the dialog where the selected Acl can be modified
     *
     * this shows the dialog corresponding to the acl_type that was previously selected,
     * including existing data from the previously selected acl_id. Note that this routine
     * is only a simple dispatcher; actual work is done in subroutines.
     *
     * @return void output added to $output object
     */
    function show_dialog() {
        switch($this->acl_type) {
        case ACL_TYPE_INTRANET:
            $this->show_dialog_intranet();
            break;

        case ACL_TYPE_ADMIN:
            $this->show_dialog_admin();
            break;

        case ACL_TYPE_PAGEMANAGER:
            $this->show_dialog_pagemanager();
            break;

        case ACL_TYPE_MODULE:
            $output->add_message('STUB: not implemented');
            // break; // fall through
        default:
             logger(sprintf("aclmanager->show_dialog(): weird acl_type '%d'",$acl_type));
            break;
        }
    } // show_dialog()


    /** save the changed data for the selected acl_type
     *
     * this interprets the data from the selected dialog and saves the (changed)
     * permission data accordingly. This, too, is merely a dispatcher to the
     * subroutines that do the actual work.
     *
     * @return bool FALSE on error, TRUE otherwise
     */
    function save_data() {
        switch($this->acl_type) {
        case ACL_TYPE_INTRANET:
            $retval = $this->save_data_intranet();
            break;

        case ACL_TYPE_ADMIN:
            $retval = $this->save_data_admin();
            break;

        case ACL_TYPE_PAGEMANAGER:
            $retval = $this->save_data_pagemanager();
            break;

        case ACL_TYPE_MODULE:
            // break; // fall through
        default:
            logger(sprintf("aclmanager->save_data(): weird acl_type '%d'",$acl_type));
            $retval = FALSE;
            break;
        }
        return $retval;
    } // save_data()


    // #############################################################################
    // ############################# PRIVATE ROUTINES ##############################
    // #############################################################################

    /** display a tabular form for manipulating intranet permissions
     *
     * This dialog is a table consisting of 2 (group acl) or 3 (user acl) columns.
     * The first column holds the text 'All areas' or the name of a private area (if any)
     * The second column holds a listbox where the user can select 1 out of 3 roles:
     * 0 = "--", 1 = "Access", -1 = "Guru". The optional third column holds corresponding
     * (existing) roles based on a group/capacity membership of the user.
     * This 3rd column is displayed only when there are related acls (indicated via
     * related_acls not empty)
     *
     * @result output added to content part of the output object
     */
    function show_dialog_intranet() {
        global $WAS_SCRIPT_NAME;
        if (is_null($this->dialogdef)) {
            $this->dialogdef = $this->get_dialogdef_intranet($this->acl_id,$this->related_acls);
        }
        $this->output->add_content('<h2>'.$this->header.'</h2>');
        $this->output->add_content($this->intro);
        $show_related = (empty($this->related_acls)) ? FALSE : TRUE;
        $href = href($WAS_SCRIPT_NAME,$this->a_params_save);
        $this->output->add_content($this->dialog_tableform($href,$this->dialogdef,$show_related));
    } // show_dialog_intranet()


    /** save the changed roles for intranet access to the tables 'acls' and 'acls_areas'
     *
     * this interprets the data from the intranet dialog and saves the changed roles
     * accordingly
     *
     * @return bool FALSE on error, TRUE otherwise
     */
    function save_data_intranet() {
        if (is_null($this->dialogdef)) {
            $this->dialogdef = $this->get_dialogdef_intranet($this->acl_id,$this->related_acls);
        }
        return $this->save_data_permissions();
    } // save_data_intranet()

    /** save the changed roles in the dialog to the corresponding tables 'acls'
     *
     * this interprets the data from the current dialog and saves the changed roles
     * accordingly. Note that the information about tables and fields etc. is all
     * contained in the dialogdef so we can use this generic save_data() routine.
     *
     * @return bool FALSE on error, TRUE otherwise
     */
    function save_data_permissions() {
        if (is_null($this->dialogdef)) {
            logger("save_data_permissions(): huh? dialogdef not set? cannot cope with that",WLOG_DEBUG);
            return FALSE;
        }
        if (!dialog_validate($this->dialogdef)) {
            // there were errors, show them to the user return error
            foreach($this->dialogdef as $k => $item) {
                if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                    $this->output->add_message($item['error_messages']);
                }
            }
            return FALSE;
        }
        // At this point we have valid values in $this->dialogdef
        // Also, we have the old values in our hands so we can sense the difference
        $errors = 0;
        foreach($this->dialogdef as $k => $item) {
            if ((!isset($item['name'])) ||                                // skip spurious item (possibly empty array)
                (!isset($item['table_name'])) ||                          // skip fields not associated with a data table
                ($item['type'] == F_SUBMIT) ||                            // skip submit and cancel buttons
                ((isset($item['hidden'])) && ($item['hidden'])) ||        // skip hidden fields too
                (intval($item['old_value']) == intval($item['value']))) { // skip unchanged fields
                continue;
            }
            // At this point we DO have an associated data table
            // and we DO have a changed value. Now we need to
            // save the changes in the database. We first try to
            // update an existing record.
            $table = $item['table_name'];
            $field = $item['table_field'];
            $where = $item['table_where'];
            $value = intval($item['value']);
            $failed = FALSE;
            $rows = db_update($table,array($field => $value),$where);
            if ($rows === FALSE) { // oops, failed
                $failed = TRUE;
            } elseif ($rows == 0) {
                // apparently there was no record yet. Go add one.
                if ($table == 'acls') { // this should not happen
                    logger(sprintf("aclmanager: weird! no record acl_id='%d' in 'acls'?",$where['acl_id']));
                    $failed = TRUE;
                } else {
                    $fields = $where;
                    $fields[$field] = $value;
                    if (db_insert_into($table,$fields) === FALSE) {
                        logger(sprintf("aclmanager: cannot insert record in %s: %s",$table,db_errormessage()));
                        $failed = TRUE;
                    }
                }
            }
            if ($failed) {
                $message = t('acl_error_saving_field','admin',
                    array('{FIELD}' => (isset($item['label'])) ? str_replace('~','',$item['label']) : $item['name']));
                ++$errors;
                ++$this->dialogdef[$k]['errors'];
                $this->dialogdef[$k]['error_messages'][] = $message;
                $this->output->add_message($message);
            }
        }
        if ($errors == 0) {
            $this->output->add_message(t('success_saving_data','admin'));
            $retval = TRUE;
        } else {
            $this->output->add_message(t('errors_saving_data','admin',array('{ERRORS}' => $errors)));
            $retval = FALSE;
        }
        return $retval;
    } // save_data_permissions()


    /** display a tabular form for manipulating admin permissions
     *
     * This dialog is a table consisting of 2 (group acl) or 3 (user acl) columns.
     * The first column holds the various job names/descriptions.
     * The second column holds a checkbox for the job.
     * The optional third column holds corresponding
     * (existing) permissions based on a group/capacity membership of the user.
     * This 3rd column is displayed only when there are related acls (indicated via
     * related_acls not empty)
     *
     * @result output added to content part of the output object
     */
    function show_dialog_admin() {
        global $WAS_SCRIPT_NAME;
        if (is_null($this->dialogdef)) {
            $this->dialogdef = $this->get_dialogdef_admin($this->acl_id,$this->related_acls);
        }
        $this->output->add_content('<h2>'.$this->header.'</h2>');
        $this->output->add_content($this->intro);
        $show_related = (empty($this->related_acls)) ? FALSE : TRUE;
        $href = href($WAS_SCRIPT_NAME,$this->a_params_save);
        $this->output->add_content($this->dialog_tableform($href,$this->dialogdef,$show_related));
    } // show_dialog_admin()


    /** save changed job permissions to the database
     *
     * this saves the changed job permissions to the acls table.
     *
     * If the user selected the guru option, we simply set the permissions to
     * JOB_PERMISSION_GURU (i.e. all permissions set). If not, we iterate through
     * all existing permissions and set the corresponding bits.
     * After that the data is saved to the correct acls-record.
     *
     * @todo fix the crude error check on dialogdef === FALSE here
     * @return bool FALSE on error, TRUE otherwise
     */
    function save_data_admin() {
        if (is_null($this->dialogdef)) {
            $this->dialogdef = $this->get_dialogdef_admin($this->acl_id,$this->related_acls);
        }
        if ($this->dialogdef === FALSE) {
            return FALSE; // shouldn't happen...
        }
        if (!dialog_validate($this->dialogdef)) {
            // there were errors, show them to the user return error
            foreach($this->dialogdef as $k => $item) {
                if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                    $this->output->add_message($item['error_messages']);
                }
            }
            return FALSE;
        }
        // At this point we have valid values in $this->dialogdef
        // Also, we have the old values in our hands so we can sense the difference

        // Job permissions are a special case because we manipulate the bits here.
        // Strategy:
        // If they want to set guru-permissions => set $jobs to -1 and we're done
        // If not, iterate throug the individual bits
        // Since the dialogdef is keyed with the fieldnames we can directly
        // access the interesting fields. Here we go.

        if ($this->dialogdef['job_guru']['value'] == 1) {
            $permissions_jobs = JOB_PERMISSION_GURU;
        } else {
            $permissions_jobs = 0;
            for ($job = 1; ($job < JOB_PERMISSION_NEXT_AVAILABLE_VALUE); $job <<= 1) {
                if ($this->dialogdef['job_'.strval($job)]['value'] == 1) {
                    $permissions_jobs |= $job;
                }
            }
        }
        $acl_id = intval($this->acl_id);
        $rows = db_update('acls',array('permissions_jobs' => $permissions_jobs),array('acl_id' => $acl_id));
        if ($rows !== FALSE) {
            $this->output->add_message(t('success_saving_data','admin'));
            $retval = TRUE;
        } else {
            $this->output->add_message(t('errors_saving_data','admin',array('{ERRORS}' => 1)));
            logger(sprintf("aclmanager: error updating job permissions for acl_id='%d': %s",$acl_id,db_errormessage()));
            $retval = FALSE;
        }
        return $retval;
    } // save_data_admin()


    /** display a tabular form for manipulating pagemanager permissions
     *
     * This dialog is a table consisting of 2 (group acl) or 3 (user acl) columns.
     * The first column identifies the site, areas or nodes within areas.
     * The second column holds a listbox where the user can select a role for that
     * particular item. The roles 0 = "--" and  -1 = "Guru" are always available.
     * The optional third column holds corresponding (existing) roles based on a
     * group/capacity membership of the user. This 3rd column is displayed only
     * when there are related acls (indicated by $related_acls not being empty).
     *
     * The main purpose of this routine is to show some $this->pagination_limit
     * table rows (starting at $this->pagination_offset) and corresponding [Save]
     * and [Cancel] buttons that eventually lead to the save routine. (If pagination
     * is not enabled, the full overview is displayed).
     *
     * Note that the actual pagination is performed in {@link get_dialogdef_pagemanager()}.
     * The additional feature of expanding/collapsing areas in the display is
     * also done in {@link get_dialogdef_pagemanager()}.
     *
     * @result output added to content part of the output object
     * @uses $WAS_SCRIPT_NAME
     * @uses $CFG;
     */
    function show_dialog_pagemanager() {
        global $WAS_SCRIPT_NAME, $CFG;
        if (is_null($this->dialogdef)) {
            $this->dialogdef = $this->get_dialogdef_pagemanager($this->acl_id, $this->related_acls);
        }
        $header = $this->header;
        if (($this->pagination_enabled) && 
            (($this->pagination_limit < $this->pagination_total) || ($this->pagination_offset != 0))) {
            $param = array(
                '{FIRST}' => strval($this->pagination_offset+1),
                '{LAST}' => strval(min($this->pagination_total,$this->pagination_offset+$this->pagination_limit)),
                '{TOTAL}' => strval($this->pagination_total)
                );
            $header .= ' '.t('pagination_count_of_total','admin',$param);
            $this->output->add_pagination($WAS_SCRIPT_NAME,
                                          $this->pagination_a_params,
                                          $this->pagination_total,
                                          $this->pagination_limit,
                                          $this->pagination_offset,
                                          $CFG->pagination_width);
        }
        $this->output->add_content('<h2>'.$header.'</h2>');
        $this->output->add_content($this->intro);
        $show_related = (empty($this->related_acls)) ? FALSE : TRUE;
        $href = href($WAS_SCRIPT_NAME,$this->a_params_save);
        $this->output->add_content($this->dialog_tableform($href,$this->dialogdef,$show_related));
    } // show_dialog_pagemanager()


    /** calculate the total number of items (site, areas, nodes) to show in dialog
     *
     * the 'open' or 'closed' status of an area is dictated by $open_areas:
     *  - if $open_areas is an array the elements look like $area_id => $show, where
     *    $show == TRUE indicates the area is 'open' and $show == FALSE indicates the area is 'closed'
     *  - if $open_area is a boolean and the value is TRUE. _all_ areas are to be considered 'open'
     *  - otherwise _all_ areas are to be considered 'closed'.
     *
     * The returned value $total is the sum of the number of areas and the number of 'showable' nodes (as per the
     * information in $open_areas). If there are no areas at all, $total is 0. If an error occurs, this routine
     * returns FALSE.
     *
     * The parameter $areas is used as a return value. It is keyed with $area_id and filled with pertinent
     * information about the areas:
     *  - int $area_id: the number of the area (also the key of the the $areas array)
     *  - string $title the name of the area
     *  - bool $is_active indicating an active area (TRUE) or an inactive area (FALSE)
     *  - int $nodes the total number of nodes in this area (could be 0 if no nodes were added yet)
     *  - int permissions_nodes the bitmap containing the existing node permissions for this area
     *
     * Also, the following information is added to the resulting array:
     *  - bool $show if TRUE, all nodes in this area should be displayed
     *  - int $first indicating the offset of the row for this area, relative from the start of the list of areas
     *  - int $last indicating the offset of the last item to show in this area (could be the same as $first)
     *
     * The latter three values are used to skip $offset rows when constructing the dialog.
     *
     * @param array &$areas an array with summary information about areas, including the # of nodes to show
     * @return int|bool FALSE on error + &$areas empty OR the number of items to show + &$areas filled with summary data
     * @uses $DB;
     */
    function calc_areas_total(&$areas) {
        global $DB;
        $areas = array(); // start with a safe return value: nothing
        $total = 0;

        //
        // 1 -- how many areas and nodes are there anyway?
        //
        $sql = sprintf("SELECT a.area_id, a.title, a.is_active, COUNT(n.node_id) AS nodes, aa.permissions_nodes ".
                       "FROM %sareas a ".
                       "LEFT JOIN %snodes n USING (area_id) ".
                       "LEFT JOIN %sacls_areas aa ON ((aa.acl_id = %d) AND (a.area_id = aa.area_id)) ".
                       "GROUP BY a.area_id, a.title, a.is_active ".
                       "ORDER BY a.sort_order",
                       $DB->prefix,$DB->prefix,$DB->prefix,$this->acl_id);
        if (($DBResult = $DB->query($sql)) === FALSE) {
            logger('calc_areas_total(): cannot count nodes in areas: '.db_errormessage(),WLOG_DEBUG);
            return FALSE;
        }
        $records = $DBResult->fetch_all_assoc('area_id'); // $records is keyed by 'area_id'
        $DBResult->close();

        //
        // 2 -- calculate sum of areas and 'showable' nodes
        //

        //
        // At this point we have an array holding the number of showable nodes per area
        // Now calculate the total number of lines (table rows) required for the dialog, i.e.
        // the sum of
        // - A (1 for each of the A areas)
        // - sum of nodes in visible (ie. expanded, not collapsed) areas
        // The latter sum could be 0 if all nodes are suppressed or when there are no nodes in the area yet
        //
        if (($this->area_view_enabled) && (is_array($this->area_view_areas_open))) {
            foreach($records as $area_id => $record) {
                $show = ((isset($this->area_view_areas_open[$area_id])) && ($this->area_view_areas_open[$area_id])) ? 
                        TRUE : FALSE;
                $records[$area_id]['show'] = $show;
                $records[$area_id]['first'] = $total;
                $total += ($show) ? intval($record['nodes']) : 0;
                $records[$area_id]['last'] = $total++;
            }
        } else {
            $show = ((is_bool($this->area_view_areas_open)) && ($this->area_view_areas_open)) ? TRUE : FALSE;
            foreach($records as $area_id => $record) {
                $records[$area_id]['show'] = $show;
                $records[$area_id]['first'] = $total;
                $total += ($show) ? intval($record['nodes']) : 0;
                $records[$area_id]['last'] = $total++;
            }
        }
        if ($total > 0) {
            //
            // Do we need to paginate the display?
            //
            if ((!$this->pagination_enabled) || 
                (($total <= $this->pagination_limit) && ($this->pagination_offset == 0))) { // no pagination necessary
                $areas = $records;
            } else { // pagination requested/necessary: copy selected areas and discard the rest
                $first = $this->pagination_offset;
                $last = $first + $this->pagination_limit - 1;
                $areas = array();
                foreach($records as $area_id => $record) {
                    if ($last < $record['first']) { // we already showed what was to show, nothing more to do
                        break;
                    } elseif ($record['last'] < $first) { // nothing to be done in this area, move on to the next
                        continue;
                    } else {
                        $areas[$area_id] = $record; // keep this one, it is interesting
                    }
                }
            }
            unset($records); // we no longer need those, we have got our selection in $areas now
        }
        return $total;
    } // calc_areas_total()



    /** save the changed roles for pagemanager to the tables 'acls' and 'acls_areas' and 'acls_nodes'
     *
     * this interprets the data from the pagemanager dialog and saves the changed roles
     * accordingly
     *
     * @return bool FALSE on error, TRUE otherwise
     */
    function save_data_pagemanager() {
        if (is_null($this->dialogdef)) {
            $this->dialogdef = $this->get_dialogdef_pagemanager($this->acl_id, $this->related_acls);
        }
        return $this->save_data_permissions();
    } // save_data_pagemanager()



    // ==================================================================
    // ==================================================================
    // ==================================================================

    /** construct an array with the intranet dialog information
     *
     * this creates an array with 1 or more list boxes with the
     * current roles for $acl_id for intranet access at the
     * site level and for individual private areas. This dialog
     * is supposed to be rendered as a 2-column (group acl) or
     * 3 column (user acl) table. The contents of the 3rd column
     * is a list (an array) of related permissions, ie. the permissions
     * a user has been granted via a group membership. The related
     * information is stored in an extra array element 'related'.
     *
     * The related information is constructed only in the case where
     * $related_acls is not NULL.
     *
     * The dialog is filled with the current values via $item['value']
     * but as a side effect the current value is also recorded in $item['old_value']).
     * This makes it easier to determine whether any values have changed
     * (see {@link save_data_internet()}).
     *
     * @param int $acl_id the acl of interest
     * @param array $related_acls NULL or a list of acl_id => "group/capacity" pairs for related permissions
     * @return array the intranet dialog information
     * @todo handle the related information in this dialog
     */
    function get_dialogdef_intranet($acl_id,$related_acls=NULL) {
        //
        // 0 -- initialise
        //
        $roles = $this->get_roles_intranet();
        $dialogdef = array();
        if (!empty($this->dialog)) {
            $dialogdef[] = array(
                'type' => F_INTEGER,
                'name' => 'dialog',
                'value' => intval($this->dialog),
                'hidden' => TRUE);
        }

        //
        // 1 -- construct site level role widget, fill with current role value if any
        //
        $value = ACL_ROLE_NONE; // assume no permissions at all
        $related = array();
        $acls = $this->get_permissions($acl_id,$related_acls,array('acl_id','permissions_intranet'));
        foreach($acls as $id => $acl) {
            $permissions = intval($acl['permissions_intranet']);
            if ($id == $acl_id) {
                $value = $permissions;
            } elseif ($permissions != ACL_ROLE_NONE) {
                $role = (isset($roles[$permissions])) ? $roles[$permissions]['option'] : t('acl_role_unknown','admin');
                $related[] = $related_acls[$id].': '.$role;
            } // else no related permissions, so keep quiet
        }
        $dialogdef['acl_site'] = array(
            'type' => F_LISTBOX,
            'name' => 'acl_site',
            'value' => $value,
            'old_value' => $value,
            'table_name' => 'acls',
            'table_field' => 'permissions_intranet',
            'table_where' => array('acl_id' => intval($acl_id)),
            'options' => $roles,
            'label' => t('acl_all_private_areas_label','admin'),
            'is_modified' => FALSE,
            'related' => $related);

        //
        // 2 -- construct an area level role widget for every private area, fill with current role value, if any
        //
        $table = 'areas';
        $fields = array('area_id','title','is_active');
        $where = array('is_private' => TRUE);
        $areas = db_select_all_records($table,$fields,$where,'sort_order','area_id');
        if ($areas !== FALSE) {
            // iterate through all areas
            $permissions_areas = $this->get_permissions_areas($acl_id,$related_acls,$areas);
            foreach($areas as $area_id => $area) {
                $value = ACL_ROLE_NONE;
                $related = array();
                $acls = (isset($permissions_areas[$area_id])) ? $permissions_areas[$area_id] : array();
                foreach($acls as $id => $acl) {
                    $permissions = intval($acl['permissions_intranet']);
                    if ($id == $acl_id) {
                        $value = $permissions;
                    } elseif ($permissions != ACL_ROLE_NONE) {
                        $role = (isset($roles[$permissions])) ? $roles[$permissions]['option']
                                                              : t('acl_role_unknown','admin');
                        $related[] = $related_acls[$id].': '.$role;
                    } // else no related permissions, so keep quiet
                }
                $params = array('{AREA}' => strval($area_id),'{AREA_FULL_NAME}' => $area['title']);
                $is_active = db_bool_is(TRUE,$area['is_active']);
                $label = t(($is_active) ? 'acl_area_label' : 'acl_area_inactive_label','admin',$params);
                $name = 'acl_area_'.strval($area_id);
                $dialogdef[$name] = array(
                    'type' => F_LISTBOX,
                    'name' => $name,
                    'value' => $value,
                    'old_value' => $value,
                    'table_name' => 'acls_areas',
                    'table_field' => 'permissions_intranet',
                    'table_where' => array('acl_id' => intval($acl_id), 'area_id' => intval($area_id)),
                    'options' => $roles,
                    'label' => $label,
                    'is_modified' => FALSE,
                    'related' => $related);
            }
        }

        //
        // 3 -- always finish with Save and Cancel
        //
        $dialogdef[] = dialog_buttondef(BUTTON_SAVE);
        $dialogdef[] = dialog_buttondef(BUTTON_CANCEL);
        return $dialogdef;
    } // get_dialogdef_intranet()


    /** construct an array with the admin dialog information
     *
     * this creates an array with widgets for all possible admin jobs for $acl_id.
     *
     * This dialog is supposed to be rendered as a 2-column (group acl) or
     * 3 column (user acl) table. The contents of the 3rd column
     * is a list (an array) of related permissions, ie. the permissions
     * a user has been granted via a group membership. The related
     * information is stored in an extra array element 'related'.
     *
     * The related information is constructed only in the case where
     * $related_acls is not NULL.
     *
     * The dialog is filled with the current values via $item['value']
     * but as a side effect the current value is also recorded in $item['old_value']).
     * This makes it easier to determine whether any values have changed
     * (see {@link save_data_admin()}).
     *
     * @param int $acl_id the acl of interest
     * @param array $related_acls NULL or a list of acl_id => "group/capacity" pairs for related permissions
     * @return array the admin dialog information
     * @todo handle the related information in this dialog
     */
    function get_dialogdef_admin($acl_id,$related_acls=NULL) {
        //
        // 0 -- initialise
        //
        $dialogdef = array();
        if (!empty($this->dialog)) {
            $dialogdef['dialog'] = array(
                'type' => F_INTEGER,
                'name' => 'dialog',
                'value' => intval($this->dialog),
                'hidden' => TRUE);
        }

        //
        // 1 -- retrieve existing permissions (including related)
        //
        if (is_null($related_acls)) {
            $related_acls = array();
        }
        $fields = array('acl_id','permissions_jobs');
        $where = sprintf("(acl_id = %d)",$acl_id);
        if (!empty($related_acls)) {
            foreach($related_acls as $related_acl_id => $dummy) {
                $where .= sprintf(" OR (acl_id = %d)",$related_acl_id);
            }
            $where = '('.$where.')';
        }
        $sort_order = 'acl_id';
        $key_field = 'acl_id';
        if (($records = db_select_all_records('acls',$fields,$where,$sort_order,$key_field)) === FALSE) {
            logger('get_dialogdef_admin(): cannot retrieve acls/job permissions: '.db_errormessage());
            return FALSE;
        }
        $permissions = array();
        foreach($records as $related_acl_id => $record) {
            $permissions[$related_acl_id] = intval($record['permissions_jobs']);
        }

        //
        // 2 - start with guru permissions
        //
        $value = ($permissions[$acl_id] == JOB_PERMISSION_GURU) ? '1' : '';
        $related = array();
        foreach($related_acls as $related_acl_id => $description) {
            if ($permissions[$related_acl_id] == JOB_PERMISSION_GURU) {
                $related[] = $description;
            }
        }
        $dialogdef['job_guru'] = array(
            'type' => F_CHECKBOX,
            'name' => 'job_guru',
            'options' => array(1 => t('acl_job_guru_check','admin')),
            'label' => t('acl_job_guru_label','admin'),
            'title' => t('acl_job_guru_title','admin'),
            'value' => $value,
            'old_value' => $value,
            'related' => $related);


        //
        // 3 -- loop through all defined jobs
        //
        for ($job = 1; ($job < JOB_PERMISSION_NEXT_AVAILABLE_VALUE); $job <<= 1) {
            $value = ($permissions[$acl_id] & $job) ? '1' : '';
            $related = array();
            foreach($related_acls as $related_acl_id => $description) {
                if ($permissions[$related_acl_id] & $job) {
                    $related[] = $description;
                }
            }
            $dialogdef['job_'.strval($job)] = array(
                'type' => F_CHECKBOX,
                'name' => 'job_'.strval($job),
                'options' => array(1 => t('acl_job_'.strval($job).'_check','admin')),
                'label' => t('acl_job_'.strval($job).'_label','admin'),
                'title' => t('acl_job_'.strval($job).'_title','admin'),
                'value' => $value,
                'old_value' => $value,
                'related' => $related);
        }

        //
        // 4 -- always finish with Save and Cancel
        //
        $dialogdef['button_save'] = dialog_buttondef(BUTTON_SAVE);
        $dialogdef['button_cancel'] = dialog_buttondef(BUTTON_CANCEL);
        return $dialogdef;
    } // get_dialogdef_admin()


    /** construct a dialog definition for pagemanager permissions
     *
     */
    function get_dialogdef_pagemanager($acl_id,$related_acls) {
        global $DB;
        //
        // 0 -- initialise
        //
        $roles_area = $this->get_roles_pagemanager(ACL_LEVEL_AREA);
        $roles_site = $this->get_roles_pagemanager(ACL_LEVEL_SITE);

        $dialogdef = array();
        if (!empty($this->dialog)) {
            $dialogdef['dialog'] = array(
                'type' => F_INTEGER,
                'name' => 'dialog',
                'value' => intval($this->dialog),
                'hidden' => TRUE);
        }

        //
        // 1 -- construct site level role widget, fill with current role value if any
        //
        $value = ACL_ROLE_NONE; // assume no permissions at all
        $related = array();
        $acls = $this->get_permissions($acl_id,$related_acls,array('acl_id','permissions_nodes'));
        foreach($acls as $id => $acl) {
            $permissions = intval($acl['permissions_nodes']);
            if ($id == $acl_id) {
                $value = $permissions;
            } elseif ($permissions != ACL_ROLE_NONE) {
                $role = (isset($roles_site[$permissions])) ? $roles_site[$permissions]['option']
                                                           : t('acl_role_unknown','admin');
                $related[] = $related_acls[$id].': '.$role;
            } // else no related permissions, so keep quiet
        }
        $dialogdef['acl_site'] = array(
            'type' => F_LISTBOX,
            'name' => 'acl_site',
            'value' => $value,
            'old_value' => $value,
            'table_name' => 'acls',
            'table_field' => 'permissions_nodes',
            'table_where' => array('acl_id' => intval($acl_id)),
            'options' => $roles_site,
            'label' => t('acl_all_areas_label','admin'),
            'is_modified' => FALSE,
            'related' => $related);
        if ($this->area_view_enabled) {
            $dialogdef['acl_site']['area_id'] = 0; // sentinel value for site level expand/collapse
            $dialogdef['acl_site']['area_is_open'] = ($this->area_view_areas_open === FALSE) ? FALSE : TRUE;
            $dialogdef['acl_site']['area_offset'] = 0;
        }

        //
        // 2A -- prepare for area level widgets: calculate total # of elements to show
        //
        $areas = array();
        $this->pagination_total = $this->calc_areas_total($areas);
        if ($this->pagination_enabled) {
            $first = $this->pagination_offset;
            $last = min($this->pagination_total,$first + $this->pagination_limit) - 1;
        } else {
            $first = 0;
            $last = $this->pagination_total - 1;
        }

        if ($this->pagination_total > 0) { // there is at least 1 area to show
            $permissions_areas = $this->get_permissions_areas($acl_id,$related_acls,$areas);
            foreach($areas as $area_id => $area) {
                $value = ACL_ROLE_NONE;
                $related = array();
                $acls = (isset($permissions_areas[$area_id])) ? $permissions_areas[$area_id] : array();
                foreach($acls as $id => $acl) {
                    $permissions = intval($acl['permissions_nodes']);
                    if ($id == $acl_id) {
                        $value = $permissions;
                    } elseif ($permissions != ACL_ROLE_NONE) {
                        $role = (isset($roles_area[$permissions])) ? $roles_area[$permissions]['option']
                                                                   : t('acl_role_unknown','admin');
                        $related[] = $related_acls[$id].': '.$role;
                    } // else no related permissions, so keep quiet
                }
                $params = array('{AREA}' => strval($area_id),'{AREA_FULL_NAME}' => $area['title']);
                $is_active = db_bool_is(TRUE,$area['is_active']);
                $label = t(($is_active) ? 'acl_area_label' : 'acl_area_inactive_label','admin',$params);
                $name = 'acl_area_'.strval($area_id);
                // show area itself
                $dialogdef[$name] = array(
                    'type' => F_LISTBOX,
                    'name' => $name,
                    'value' => $value,
                    'old_value' => $value,
                    'table_name' => 'acls_areas',
                    'table_field' => 'permissions_nodes',
                    'table_where' => array('acl_id' => intval($acl_id), 'area_id' => intval($area_id)),
                    'options' => $roles_area,
                    'label' => $label,
                    'is_modified' => FALSE,
                    'related' => $related);
                if ($this->area_view_enabled) {
                    $dialogdef[$name]['area_id'] = $area_id;
                    $dialogdef[$name]['area_is_open'] = $area['show'];
                    $dialogdef[$name]['area_offset'] = $area['first'];
                }

                //
                // maybe show tree of nodes too
                //
                if (($area['show']) && ($area['nodes'] > 0)) {
                    $index = $area['first'] + 1;
                    $tree = $this->tree_build($area_id);
                    $permissions_nodes = $this->get_permissions_nodes_in_area($area_id,$acl_id,$related_acls);
                    foreach($tree as $node_id => $node) {
                        $tree[$node_id]['permissions'] = (isset($permissions_nodes[$node_id])) ?
                                                             $permissions_nodes[$node_id]  : array();
                    }
                    $this->show_tree_walk($dialogdef,$tree,$permissions_nodes,$index,$tree[0]['first_child_id'],$first,$last,$acl_id,$related_acls);
                    unset($tree);
                    unset($permissions_nodes);

                }
            }
        }
        //
        // 4 -- always finish with Save and Cancel
        //
        $dialogdef['button_save'] = dialog_buttondef(BUTTON_SAVE);
        $dialogdef['button_cancel'] = dialog_buttondef(BUTTON_CANCEL);
        return $dialogdef;
    } // get_dialogdef_pagemanager()





    // ==================================================================
    // ======================== UTILITY ROUTINES ========================
    // ==================================================================


    /** contstruct an option list with roles for intranet access
     *
     * @return array ready-to-use options array for listbox or radiobuttons
     */
    function get_roles_intranet() {
        $roles = array(
            ACL_ROLE_NONE => array('option' => t('acl_role_none_option','admin'),
                                   'title' => t('acl_role_none_title','admin')),
            ACL_ROLE_INTRANET_ACCESS => array('option' => t('acl_role_intranet_access_option','admin'),
                                              'title' =>  t('acl_role_intranet_access_title','admin')),
            ACL_ROLE_GURU => array('option' => t('acl_role_guru_option','admin'),
                                   'title' => t('acl_role_guru_title','admin'))
            );
        return $roles;
    } // get_roles_intranet()


    /** construct an option list with roles for pagemanager access
     *
     * @param int $level limits permissions to level 'page', 'section', 'area' or 'site'
     * @return array ready-to-use options array for listbox or radiobuttons
     */
    function get_roles_pagemanager($level=ACL_LEVEL_NONE) {
        $roles = array(
            ACL_ROLE_NONE => array(
                'option' => t('acl_role_none_option','admin'),
                'title' => t('acl_role_none_title','admin')
                )
            );
        if ($level > ACL_LEVEL_NONE) {
            $roles[ACL_ROLE_PAGEMANAGER_CONTENTMASTER] = array(
                'option' => t('acl_role_pagemanager_contentmaster_option','admin'),
                'title' => t('acl_role_pagemanager_contentmaster_title','admin')
                );
            $roles[ACL_ROLE_PAGEMANAGER_PAGEMASTER] = array(
                'option' => t('acl_role_pagemanager_pagemaster_option','admin'),
                'title' => t('acl_role_pagemanager_pagemaster_title','admin')
                );
        }
        if ($level > ACL_LEVEL_PAGE) {
            $roles[ACL_ROLE_PAGEMANAGER_SECTIONMASTER] = array(
                'option' => t('acl_role_pagemanager_sectionmaster_option','admin'),
                'title' => t('acl_role_pagemanager_sectionmaster_title','admin')
                );
        }
        if ($level > ACL_LEVEL_SECTION) {
            $roles[ACL_ROLE_PAGEMANAGER_AREAMASTER] = array(
                'option' => t('acl_role_pagemanager_areamaster_option','admin'),
                'title' => t('acl_role_pagemanager_areamaster_title','admin')
                );
        }
        if ($level > ACL_LEVEL_AREA) {
            $roles[ACL_ROLE_PAGEMANAGER_SITEMASTER] = array(
                'option' => t('acl_role_pagemanager_sitemaster_option','admin'),
                'title' => t('acl_role_pagemanager_sitemaster_title','admin')
                );
        }
        $roles[ACL_ROLE_GURU] = array(
                'option' => t('acl_role_guru_option','admin'),
                'title' => t('acl_role_guru_title','admin')
                );
        return $roles;
    } // get_roles_pagemanager()


    /** construct a form with a dialog in a table with 2 or 3 columns
     *
     * this constructs a 2- or 3-column table and fills it with data from
     * the dialogdef.
     *
     * The first column holds the labels for the widgets.
     * The second column holds the corresponding widget, e.g. a list box with roles.
     * The optional third column (depends on the flag $show_related) shows
     * related information. This is used to list group/capacities and roles
     * from related groups (ie. groups of which the user is a member).
     *
     * The table has headers for the columns: 'Realm','Role' and optional 'Related'.
     * Rows in the table can have alternating colours via the odd/even class.
     * This is done via the stylesheet.
     *
     * @param string $href the target of the HTML form
     * @param array &$dialogdef the array which describes the complete dialog
     * @return array constructed HTML-form with dialog, one line per array element
     * @todo bailing out on non-array is a crude way of error handling: this needs to be fixed
     */
    function dialog_tableform($href,&$dialogdef,$show_related=FALSE) {
        if (!is_array($dialogdef)) {
            logger('dialog_tableform(): weird: there is no valid dialogdef?');
            return array(t('error_retrieving_data','admin'));
        }
        // result starts with opening a form tag and a 2- or 3-column table
        $attributes = array('class' => 'header');
        $a = array(
                 html_form($href),
                 html_table(array('class' => 'acl_form')),
                 '  '.html_table_row($attributes),
                 '    '.html_table_head($attributes,t('acl_column_header_realm','admin')),
                 '    '.html_table_head($attributes,t('acl_column_header_role','admin'))
             );
        if ($show_related) {
            $a[] = '    '.html_table_head($attributes,t('acl_column_header_related','admin'));
        }
        $a[] = '  '.html_table_row_close();
   
        $oddeven = 'even';
        $postponed = array();
        foreach($dialogdef as $item) {
            if (!isset($item['name'])) { // skip spurious item (possibly empty array)
                continue;
            }
            if (($item['type'] == F_SUBMIT) || ((isset($item['hidden'])) && ($item['hidden']))) {
                // always postpone the buttons and hidden fields to the end
                $postponed[] = $item;
                continue;
            }
            $oddeven = ($oddeven == 'even') ? 'odd' : 'even';
            $attributes = array('class' => $oddeven);
            $a[] = '  '.html_table_row($attributes);
            //
            // column 1 - realm
            //
            if ($this->area_view_enabled) {
                if (isset($item['area_id'])) { // site level or area level
                    $icon = $this->get_icon_area($item['area_id'],$item['area_is_open'],$item['area_offset']);
                } else { // node level, show a blank icon to line things up
                    $icon = $this->get_icon_blank();
                }
            } else {
                $icon = '';
            }
            $a[] = '    '.html_table_cell($attributes,ltrim($icon.' '.dialog_get_label($item)));
            //
            // column 2 - role
            //
            $widget = dialog_get_widget($item);
            if (is_array($widget)) {
                $a[] = '    '.html_table_cell($attributes);
                // add every radio button on a separate line
                $postfix = ($item['type'] == F_RADIO) ? '<br>' : '';
                foreach ($widget as $widget_line) {
                    $a[] = '      '.$widget_line.$postfix;
                }
                $a[] = '    '.html_table_cell_close();
            } else {
                $a[] = '    '.html_table_cell($attributes,$widget);
            }
            //
            // column 3 (optional) - related items in a single comma delimited string
            //
            if ($show_related) {
                $related = ((isset($item['related'])) && (!empty($item['related']))) ? $item['related'] : '';
                $cell_content = (is_array($related)) ? implode(',<br>',$related) : $related;
                $a[] = '    '.html_table_cell($attributes,$cell_content);
            }
            $a[] = '  '.html_table_row_close();
        }
        $a[] = html_table_close();
        // now handle the postponed fields such as the Save and Cancel buttons and the hidden fields
        if (sizeof($postponed) > 0) {
            foreach($postponed as $item) {
                $a[] = dialog_get_widget($item);
            }
        }
        $a[] = html_form_close();
        return $a;
    } // dialog_tableform()


    /** retrieve an array with 0, 1 or more records with permissions from table 'acls'
     *
     * this constructs an array with all (or selected) permissions from the 'acls' table for the
     * specified acl $acl_id and optionally for all related acl_id's in $related_acls.
     * The resulting array is keyed by acl_id.
     *
     * @param int $acl_id the primary acl_id (used for both users and groups)
     * @param array|null $related_acls an array with related acls for this user or NULL for group acls
     * @return array 0, 1 or more acls-records keyed by acl_id
     */
    function get_permissions($acl_id,$related_acls=NULL) {
        $table = 'acls';
        $fields ='*';
        $where = sprintf("(acl_id = %d)",$acl_id);
        if (!empty($related_acls)) {
            foreach($related_acls as $id => $acl) {
                $where .= sprintf(" OR (acl_id = %d)",$id);
            }
            $where = "(".$where.")";
        }
        if (($permissions = db_select_all_records($table,$fields,$where,'','acl_id')) === FALSE) {
            logger("aclmanager: failure retrieving data from table '$table': ".db_errormessage);
            return array();
        }
        return $permissions;
    } // get_permissions();


    /** retrieve an array with 0, 1 or more records with permissions from table 'acls_areas'
     *
     * this constructs an array with all permissions from the 'acls_areas' table for the
     * specified acl $acl_id and optionally for all related acl_id's in $related_acls and optional
     * areas. The resulting array is keyed by area_id and acl_id.
     *
     * Note that by making the result keyed by area_id first (and then acl_id) it becomes possible
     * to step throug a list of areas and have 0,1 or more acls for that area in a single array,
     * e.g. $acls = $permissions[16] yields the selected acls that apply to area 16. That is handy
     * when constructing dialogs iterating through areas such as intranet permissions.
     *
     * @param int $acl_id the primary acl_id (used for both users and groups)
     * @param array|null $related_acls an array with related acls for this user keyed by 'acl_id' or NULL for group acls
     * @param array|null $areas an array with areas of interest keyed by 'area_id' or NULL for all areas
     * @return array 0, 1 or more acls-records keyed by area_id and acl_id
     */
    function get_permissions_areas($acl_id,$related_acls=NULL,$areas=NULL) {
        $table = 'acls_areas';
        $fields ='*';
        // 1A -- selection of acls via '((acl_id = 1) OR (acl_id = 2) OR (acl_id = 3))'
        $where = sprintf("(acl_id = %d)",$acl_id);
        if (!empty($related_acls)) {
            foreach($related_acls as $id => $acl) {
                $where .= sprintf(" OR (acl_id = %d)",$id);
            }
            $where = "(".$where.")";
        }
        // 1B -- optional additional selection of areas via ' AND ((area_id = 4) OR (area_id = 5))'
        if (!empty($areas)) {
            $where_area = '';
            foreach($areas as $area_id => $area) {
                $where_area .= sprintf("%s(area_id = %d)",(empty($where_area)) ? '' : ' OR ',$area_id);
            }
            if (!empty($where_area)) {
                $where .= " AND (".$where_area.")";
            }
        }
        // 2 -- fetch raw data from the database in a single set of database records
        if (($records = db_select_all_records($table,$fields,$where)) === FALSE) {
            logger("aclmanager: failure retrieving data from table '$table': ".db_errormessage);
            return array();
        }

        // 3 -- construct a 2D-array keyed with area_id and acl_id (in that order)
        $permissions = array();
        foreach ($records as $record) {
            $area_id = intval($record['area_id']);
            $acl_id = intval($record['acl_id']);
            $permissions[$area_id][$acl_id] = $record;
        }
        unset($records);
        return $permissions;
    } // get_permissions_areas();


    /** retrieve an array with 0, 1 or more records with permissions from table 'acls_nodes'
     *
     * this constructs an array with all permissions from the 'acls_nodes' table for the
     * specified acl $acl_id and optionally for all related acl_id's in $related_acls and optional
     * nodes. The resulting array is keyed by node_id and acl_id.
     *
     * Note that by making the result keyed by node_id first (and then acl_id) it becomes possible
     * to step throug a list of nodes and have 0,1 or more acls for that node in a single array,
     * e.g. $acls = $permissions[16] yields the selected acls that apply to node 16. That is handy
     * when constructing dialogs iterating through nodes such as pagemanager permissions.
     *
     * @param array $area_id the area where the nodes reside
     * @param int $acl_id the primary acl_id (used for both users and groups)
     * @param array|null $related_acls an array with related acls for this user keyed by 'acl_id' or NULL for group acls
     * @return array 0, 1 or more acls-records keyed by node_id and acl_id
     */
    function get_permissions_nodes_in_area($area_id,$acl_id,$related_acls=NULL) {
        global $DB;
        $fields ='*';

        // 1A -- selection of acls via '((an.acl_id = 1) OR (an.acl_id = 2) OR (an.acl_id = 3))'
        $where = sprintf("(an.acl_id = %d)",$acl_id);
        if (!empty($related_acls)) {
            foreach($related_acls as $id => $acl) {
                $where .= sprintf(" OR (an.acl_id = %d)",$id);
            }
            $where = "(".$where.")";
        }
        $sql = sprintf("SELECT an.* ".
                       "FROM %sacls_nodes an ".
                       "INNER JOIN %snodes n ON ((an.node_id = n.node_id) AND (n.area_id = %d)) ".
                       "WHERE %s",
                       $DB->prefix,$DB->prefix,$area_id,$where);

        // 2 -- fetch raw data from the database in a single set of database records
        if (($DBResult = $DB->query($sql)) === FALSE) {
            logger('aclmanager(): cannot retrieve nodes permissions in area $area_id: '.db_errormessage());
            return array();
        }
        $records = $DBResult->fetch_all_assoc();
        $DBResult->close();

        // 3 -- construct a 2D-array keyed with node_id and acl_id (in that order)
        $permissions = array();
        foreach ($records as $record) {
            $node_id = intval($record['node_id']);
            $acl_id = intval($record['acl_id']);
            $permissions[$node_id][$acl_id] = $record;
        }
        unset($records);
        return $permissions;
    } // get_permissions_nodes_in_area();


    /** build a tree of all nodes in an area
     *
     * this routine constructs a tree-structure of all nodes in area $area_id in much
     * the same way as {@link tree_build()} does. However, in this routine we keep the
     * cargo limited to a minimum: the fields we retrieve from the nodes table and
     * store in the tree are:
     *  - node_id
     *  - parent_id
     *  - is_page
     *  - title
     *  - link_text
     *  - module_id
     * Also, the tree is not cached because that does not make sense here: we only
     * use it to construct a dialogdef and that is a one-time operation too.
     *
     * @parameter int $area_id the area for which to build the tree
     * @param int $acl_id the primary acl_id (used for both users and groups)
     * @param array|null $related_acls an array with related acls for this user keyed by 'acl_id' or NULL for group acls
     * @return array ready to use tree structure w/ permissions
     */

    function tree_build($area_id) {

        // 1 -- Start with 'special' node 0 is root of the tree
        $tree = array(0 => array(
            'node_id' => 0,
            'parent_id' => 0,
            'prev_sibling_id' => 0,
            'next_sibling_id' => 0,
            'first_child_id' => 0,
            'is_page' => FALSE,
            'title' => '',
            'link_text' => '',
            'module_id' => 0)
            );


        $where = array('area_id' => intval($area_id));
        $order = array('CASE WHEN (parent_id = node_id) THEN 0 ELSE parent_id END', 'sort_order','node_id');
        $fields = array('node_id','parent_id','is_page','title','link_text','module_id');
        $records = db_select_all_records('nodes',$fields,$where,$order,'node_id');

        // 2 -- step through all node records and copy the relevant fields
        if ($records !== FALSE) {
            foreach($records as $record) {
                $node_id = intval($record['node_id']);
                $parent_id = intval($record['parent_id']);
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
                    'is_page' => $is_page,
                    'title' => $record['title'],
                    'link_text' => $record['link_text'],
                    'module_id' => intval($record['module_id'])
                    );
            }
        }
        unset($records); // free memory

        // 3 -- step through all collected records and add links to childeren and siblings
        $prev_node_id = 0;
        $sort_order = 0;
        foreach ($tree as $node_id => $node) {
            $parent_id = $node['parent_id'];
            if (!isset($tree[$parent_id])) {
                logger("aclmanager: node '$node_id' is an orphan because parent '$parent_id' does not exist in tree[]");
            } elseif ($parent_id == $tree[$prev_node_id]['parent_id']) {
                $tree[$prev_node_id]['next_sibling_id'] = $node_id;
                $tree[$node_id]['prev_sibling_id'] = $prev_node_id;
            } else {
                $tree[$parent_id]['first_child_id'] = $node_id;
            }
            $prev_node_id = $node_id;
        }

        // 4 -- 'root node' 0 is a special case, the top level nodes are in fact childeren, not siblings
        $tree[0]['first_child_id'] = $tree[0]['next_sibling_id'];
        $tree[0]['next_sibling_id'] = 0;

        // 5 -- done!
        return $tree;
    } // tree_build()


    /** add the specified node to dialogdef, optionally all subtrees, and subsequently all siblings
     *
     * this routine adds a widget to the dialogdef for the specified node
     * After that, any subtrees of this node are added too, using recursion
     * This continues for all siblings of the specified node until there are no more
     * (indicated by a sibling_id equal to zero).
     *
     * @param array &$dialogdef collects the widgets
     * @param array &$tree a reference to the complete tree built earlier
     * @param array &$permissions_nodes contains permissions per node
     * @param int &$index only add node to dialogdef if $index is between $first and $last, increments for every node
     * @param int $node_id the first node of this tree level to show
     * @param int $first lower bound of interval
     * @param int $last upper bound of interval
     * @param int $acl_id the acl we are rendering
     * @param array|null &$related_acls an array with related acls for this user keyed by 'acl_id' or NULL for group acls
     * @return void results are returned as widgets in $dialogdef
     * @uses $USER
     * @uses $CFG
     * @uses $WAS_SCRIPT_NAME
     * @uses show_tree_walk()
     */
    function show_tree_walk(&$dialogdef,&$tree,&$permissions_nodes,&$index,$node_id,$first,$last,$acl_id,&$related_acls) {
        global $CFG,$WAS_SCRIPT_NAME;
        static $level = 0;
        static $roles_page = NULL;
        static $roles_section = NULL;

        if (is_null($roles_page)) {
            $roles_page = $this->get_roles_pagemanager(ACL_LEVEL_PAGE);
        }
        if (is_null($roles_section)) {
            $roles_section = $this->get_roles_pagemanager(ACL_LEVEL_SECTION);
        }
        $class = 'level'.intval($level);

        while ($node_id > 0) {
            if ($index > $last) { // nothing more to do
                return;
            }
            $is_page = $tree[$node_id]['is_page'];
            if ($first <= $index) {
                //  1 -- display this node
                $value = ACL_ROLE_NONE;
                $roles = ($is_page) ? $roles_page : $roles_section;
                $related = array();
                $acls = (isset($permissions_nodes[$node_id])) ? $permissions_nodes[$node_id]  : array();
                foreach($acls as $id => $acl) {
                    $permissions = intval($acl['permissions_nodes']);
                    if ($id == $acl_id) {
                        $value = $permissions;
                    } elseif ($permissions != ACL_ROLE_NONE) {
                        $role = (isset($roles[$permissions])) ? $roles[$permissions]['option']
                                                              : t('acl_role_unknown','admin');
                        $related[] = $related_acls[$id].': '.$role;
                    } // else no related permissions, so keep quiet
                }
                $params = array('{NODE}' => strval($node_id),'{NODE_FULL_NAME}' => $tree[$node_id]['link_text']);
                $label = t(($is_page) ? 'acl_page_label' : 'acl_section_label','admin',$params);
                $name = 'acl_node_'.strval($node_id);
                // show node itself
                $dialogdef[$name] = array(
                    'type' => F_LISTBOX,
                    'name' => $name,
                    'value' => $value,
                    'old_value' => $value,
                    'table_name' => 'acls_nodes',
                    'table_field' => 'permissions_nodes',
                    'table_where' => array('acl_id' => intval($acl_id), 'node_id' => intval($node_id)),
                    'options' => $roles,
                    'label' => html_tag('span',array('class' => $class),$label),
                    'is_modified' => FALSE,
                    'related' => $related
                    );
            }
            ++$index;

            // 2 -- maybe descend tree and show recursively
            $subtree_id = $tree[$node_id]['first_child_id'];
            if ($subtree_id > 0) {
                ++$level;
                if ($level > MAXIMUM_ITERATIONS) {
                    logger('aclmanager: too many levels in node '.$node_id,WLOG_DEBUG);
                } else {
                    $this->show_tree_walk($dialogdef,$tree,$permissions_nodes,$index,$subtree_id,$first,$last,$acl_id,$related_acls);
                }
                --$level;
            }

            // 3 -- bump pointer and continue with next node on this level
            $node_id = $tree[$node_id]['next_sibling_id'];
        }
        return;
    } // show_tree_walk()


    /** construct a clickable icon to open/close this area
     *
     * This is a toggle: if the area is closed the closed icon is shown,
     * but the action in the A-tag is to open the icon (and vice versa).
     *
     * @uses $CFG
     * @uses $USER
     * @uses $WAS_SCRIPT_NAME
     * @param int $area_id the area to open/close (0 means: open site level)
     * @param bool $area_is_open current status
     * @param int $offset the position of this icon in the current list of items
     * @return string ready-to-use HTML-icon
     */
    function get_icon_area($area_id,$area_is_open,$offset) {
        global $CFG,$WAS_SCRIPT_NAME,$USER;
        $img_attr = array('height' => 16, 'width' => 16);
        $a_params = $this->area_view_a_params;
        $a_params['area'] = $area_id;
        if ($offset != 0) {
            $a_params['offset'] = $offset;
        }

        if ($area_is_open) {
            $title = t(($area_id == 0) ? 'icon_close_site' : 'icon_close_area','admin');
            $img_attr['title'] = $title;
            $img_attr['alt'] = t(($area_id == 0) ? 'icon_close_site_alt' : 'icon_close_area_alt','admin');
            if ($USER->high_visibility) {
                $anchor = html_tag('span','class="icon"','['.t(($area_id == 0) ? 'icon_close_site_text' : 
                                                                                 'icon_close_area_text','admin').']');
            } else {
                $anchor = html_img($CFG->progwww_short.'/graphics/folder_open.gif',$img_attr);
            }
        } else {
            $title = t(($area_id == 0) ? 'icon_open_site' : 'icon_open_area','admin');
            $img_attr['title'] = $title;
            $img_attr['alt'] = t(($area_id == 0) ? 'icon_open_site_alt' : 'icon_open_area_alt','admin');
            if ($USER->high_visibility) {
                $anchor = html_tag('span','class="icon"','['.t(($area_id == 0) ? 'icon_open_site_text' : 
                                                                                 'icon_open_area_text','admin').']');
            } else {
                $anchor = html_img($CFG->progwww_short.'/graphics/folder_closed.gif',$img_attr);
            }
        }
        $a_attr = array('title' => $title);
        return html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
    } // get_icon_area()

    /** construct a spacer of standard icon width (to line up items)
     *
     * @return string ready-to-use HTML-icon (or empty string if user wants high-visibility)
     * @uses $CFG
     * @uses $USER
     */
    function get_icon_blank() {
        global $CFG,$USER;
        if ($USER->high_visibility) {
            $spacer = '';
        } else {
            $img_attr = array('width' => 16, 'height' => 16, 'title' => '', 'alt' => t('spacer','admin'));
            $spacer = html_img($CFG->progwww_short.'/graphics/blank16.gif',$img_attr);
        }
        return $spacer;
    } // get_icon_blank()

} // AclManager

?>