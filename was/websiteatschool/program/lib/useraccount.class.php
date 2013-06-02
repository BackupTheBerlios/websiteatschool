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

/** /program/lib/useraccount.class.php - taking care of useraccounts
 *
 * This file defines a class for dealing with users. Also, the global job permission
 * constants and access control constants are defined. This file is always included,
 * even when a visitor is anonymous (ie. not logged in).
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: useraccount.class.php,v 1.9 2013/06/02 12:34:55 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** Guru permissions = all permission bits are set, even the unused ones */
define('JOB_PERMISSION_GURU',-1);

/** This permission is required for every user that is to logon to admin.php */
define('JOB_PERMISSION_STARTCENTER',1);

/** This permission allows the user to access the page manager and add/edit/delete nodes according to the user's ACLs */
define('JOB_PERMISSION_PAGEMANAGER',2);

/**  This permission allows the user to access the file manager and upload/delete files in selected places */
define('JOB_PERMISSION_FILEMANAGER',4);

/** This permission allows the user to access the module manager and configure modules */
define('JOB_PERMISSION_MODULEMANAGER',8);

/** This (dangerous) permission allows access to add/edit/delete users and groups (including escalate privileges) */
define('JOB_PERMISSION_ACCOUNTMANAGER',16);

/** This permission allows the user to access the configuration manager and change the site configuration */
define('JOB_PERMISSION_CONFIGURATIONMANAGER',32);

/** This permissions allows the user to access the site statistics */
define('JOB_PERMISSION_STATISTICS',64);

/** This allows the user to translate the program, by modifying existing translations or adding new languages */
define('JOB_PERMISSION_TRANSLATETOOL',128);

/** This allows the user to download a backup of the database */
define('JOB_PERMISSION_BACKUPTOOL',256);

/** This allows the user to view the contents of the log table */
define('JOB_PERMISSION_LOGVIEW',512);

/** This allows the user to perform a system upgrade (see also {@link was_version_check()} and {@link main_admin()}) */
define('JOB_PERMISSION_UPDATE',1024);

/** combine the permssions for the tools in a single bit mask for convenient testing */
define('JOB_PERMISSION_TOOLS',JOB_PERMISSION_TRANSLATETOOL | JOB_PERMISSION_BACKUPTOOL | JOB_PERMISSION_LOGVIEW | JOB_PERMISSION_UPDATE);

/** NOTE: This quasi-permission should always be defined to be the highest permission << 1*/
define('JOB_PERMISSION_NEXT_AVAILABLE_VALUE',2048);

/** This mask can be used to isolate only the 'official' permissions from an integer value */
define('JOB_PERMISSION_MASK',JOB_PERMISSION_NEXT_AVAILABLE_VALUE - 1);

define('PERMISSION_NODE_EDIT_CONTENT',	1);	// contentmaster

define('PERMISSION_NODE_DROP_CONTENT',	2);	// pagemaster
define('PERMISSION_NODE_ADD_CONTENT',	4);	// pagemaster
define('PERMISSION_NODE_EDIT_PAGE',	8);	// pagemaster

define('PERMISSION_NODE_DROP_PAGE',	16);	// sectionmaster
define('PERMISSION_NODE_ADD_PAGE',	32);	// sectionmaster
define('PERMISSION_NODE_DROP_SECTION',	64);	// sectionmaster
define('PERMISSION_NODE_ADD_SECTION',	128);	// sectionmaster
define('PERMISSION_NODE_EDIT_SECTION',	256);	// sectionmaster

define('PERMISSION_AREA_DROP_PAGE',	512);	// areamaster
define('PERMISSION_AREA_ADD_PAGE',	1024);	// areamaster
define('PERMISSION_AREA_DROP_SECTION',	2048);	// areamaster
define('PERMISSION_AREA_ADD_SECTION',	4096);	// areamaster
define('PERMISSION_AREA_EDIT_AREA',	8192);	// areamaster

define('PERMISSION_SITE_DROP_AREA',	16384);	// sitemaster
define('PERMISSION_SITE_ADD_AREA',	32768);	// sitemaster
define('PERMISSION_SITE_EDIT_SITE',	65536);	// sitemaster

define('ACL_ROLE_NONE', 0);
define('ACL_ROLE_GURU', -1);
define('ACL_ROLE_INTRANET_ACCESS', 1);
define('ACL_ROLE_PAGEMANAGER_CONTENTMASTER',PERMISSION_NODE_EDIT_CONTENT);
define('ACL_ROLE_PAGEMANAGER_PAGEMASTER',   ACL_ROLE_PAGEMANAGER_CONTENTMASTER |
                                            PERMISSION_NODE_DROP_CONTENT |
                                            PERMISSION_NODE_ADD_CONTENT |
                                            PERMISSION_NODE_EDIT_PAGE);
define('ACL_ROLE_PAGEMANAGER_SECTIONMASTER',ACL_ROLE_PAGEMANAGER_PAGEMASTER |
                                            PERMISSION_NODE_DROP_PAGE |
                                            PERMISSION_NODE_ADD_PAGE |
                                            PERMISSION_NODE_DROP_SECTION |
                                            PERMISSION_NODE_ADD_SECTION |
                                            PERMISSION_NODE_EDIT_SECTION);
define('ACL_ROLE_PAGEMANAGER_AREAMASTER',   ACL_ROLE_PAGEMANAGER_SECTIONMASTER |
                                            PERMISSION_AREA_DROP_PAGE |
                                            PERMISSION_AREA_ADD_PAGE |
                                            PERMISSION_AREA_DROP_SECTION |
                                            PERMISSION_AREA_ADD_SECTION |
                                            PERMISSION_AREA_EDIT_AREA);
define('ACL_ROLE_PAGEMANAGER_SITEMASTER',   ACL_ROLE_PAGEMANAGER_AREAMASTER |
                                            PERMISSION_SITE_DROP_AREA |
                                            PERMISSION_SITE_ADD_AREA |
                                            PERMISSION_SITE_EDIT_SITE);


/** Methods to access properties of the account of the logged in user
 *
 * 
 * This deals mainly with retrieving information about the user that is
 * currently logged in. There is one exception: a user that is NOT logged
 * in can still have a $USER object, but there are no privileges in that
 * case. The special user_id in that case is 0.
 * 
 * The constructor reads the important data from the database. This
 * includes things like the full name of the user and the email
 * address. This information is stored in the object and can be used,
 * e.g. $USER->email. This information is basically copied from the table
 * 'users'.
 * 
 * Furthermore, any properties for this user are retrieved from the table
 * 'users_properties'. All properties are stored in an array. These can
 * be used directly via $USER->properties['foobar'].
 * 
 * Access Control
 * 
 * Finally we deal with access control. This has become quite complex but
 * still managable (I hope). There are six tables dealing with acl's:
 *  - acls: site-wide permissions for jobs, intranet, modules and nodes
 *  - acls_areas: permissions for intranet, modules and nodes at the area
 *    level
 *  - acls_nodes: permissions for modules and nodes a the node level
 *  - acls_modules: permissions for modules at the site level
 *  - acls_modules_areas: permissions for modules at the area level
 *  - acls_modules_nodes: permissions for modules at the node level
 * 
 * The user has at least one associated ACL: the acl_id field in the user
 * record. Additional ACLs are associated with the user via group
 * memberships. All ACLs are integer bitmasks where a '1' grants a
 * permission for something and a '0' denies permission. 
 * 
 * All bits '0' is a special case: this is the default (nothing allowed)
 * and hence does not have to be stored: the mere non-existence of
 * permissions implies no permissions.
 * 
 * All bits '1' is also a special case, dubbed 'ROLE_GURU'. If an ACL has
 * this value, it means that all current (and future) permissions are
 * granted. A user with ROLE_GURU can do anything.
 * 
 * Of the six tables, only the first one (acls) is read immediately in
 * the constructor. The others are read on demand. This is done by
 * initially setting the corresponding cache variable to NULL. If the
 * table has been read, the variable will always be of type 'array',
 * even though that array may be empty (indicating no permissions).
 * 
 * The permissions from the ACLs are combined between the user's acl and
 * the optional group-acls. Only the combination of user and group
 * permissions is cached in order to save space. This is done by OR'ing
 * the permission bits. Note that the condition all bits '0' is not
 * stored, also to save space.
 * 
 * There are some functions to test for individual permissions:
 * 
 *  - has_site_permissions()
 *  - has_area_permissions()
 *  - has_node_permissions()
 *  - has_module_site_permissions()
 *  - has_module_area_permissions()
 *  - has_module_node_permissions()
 *  - has_job_permissions()
 *  - has_intranet_permissions()
 * 
 * Example: in order to determine wheter a user has access to the
 * intranet in area #2, the following could be used:
 * 
 * <code>
 * $area_id = 2;
 * if ($USER->has_intranet_permissions(ACL_ROLE_INTRANET_ACCESS,$area_id)) {
 *     ....
 * }
 * </code>
 * 
 * The effect of this call is as follows. First the routine checks the
 * (already cached) intranet-permissions at the site level. If access is
 * granted at the site level, there is no need to look any further
 * because obviously this user has access to this intranet (private area)
 * and all other current and future intranets. If not, the routine looks
 * at intranet permissions at the area level. The first time this will
 * trigger reading and caching the table for area-level permissions. In
 * this case (intranet-access), the area-level permissions provide the
 * definitive go/nogo for this user (there is no point in having
 * intranet-access-permissions at the node level).
 * 
 * Note that the 'lower' ACL is only checked if the 'higher' does not
 * provide answers. This saves unnecessary trips to the database.
 * 
 * Note that this works much the same for the other
 * has_xxx_permissions(): first the site-level is tried, then the
 * area-level and finally the node-level (when applicable).
 * 
 * ACLs for modules
 * 
 * Access to the CMS itself is fairly fine-grained. The permissions are
 * stored in the fields 'permissions_nodes' in the tables 'acls'
 * (site-level), acls_areas (area-level) and acls_nodes
 * (node-level). These permissions basically deal with the page manager
 * (the piece de resistance of the whole system).
 * 
 * However, there are modules that can be linked to nodes, e.g. a chat or
 * a forum or an agenda which also require autorised users and
 * permissions. These permissions are stored in three tables:
 * acls_modules (site-level). acls_modules_areas (area-level) and
 * acls_modules_nodes (node-level). This works pretty much the same as
 * the permissions for the CMS itself, be it that there is an extra
 * parameter, namely the module_id.
 * 
 * Once again, the permissions are only read when necessary. I.e., if the
 * site-level already grants a permission, the area and node level are
 * not read from the database. This saves time and space.
 * 
 * Roles and permissions
 * 
 * Permissions are indivial flags that allow or disallow a certain feature,
 * e.g. 'adding a page to a section'. In order to keep these permissions
 * manageable groups of permissions are combined yielding a limited number
 * of 'roles'. A 'role' is a combination of 1 or more permission bits.
 * Assigning permissions (in the user account manager) is done by assigning
 * these 'roles' to a user, either sitewide, areawide or per node. These roles
 * are dubbed sitemaster, areamaster, sectionmaster, pagemaster and contentmaster.
 * The 'higher' roles incoporate the 'lower' roles: permissions of a sectionmaster
 * include those of a pagemaster and a contentmaster.
 *
 */
class Useraccount {
    /** @var int $user_id */
    var $user_id = 0;

    /** @var string $username */
    var $username = '';

    /** @var string $full_name */
    var $full_name = '';

    /** @var string $email */
    var $email = '';

    /** @var string $language_key */
    var $language_key = '';

    /** @var string $path directory holding personal data files relative to "{$CFG->datadir}/users/" */
    var $path = '';

    /** @var int $acl_id identifies the main acl for this user */
    var $acl_id = 0;

    /** @var array $related_acls holds acl_id -> groupname/capacity pairs related to this user */
    var $related_acls = array();

    /** @var array $acls caches site-level permissions, keyed by [$field] */
    var $acls = array('permissions_jobs'     => ACL_ROLE_NONE,
                      'permissions_intranet' => ACL_ROLE_NONE,
                      'permissions_modules'  => ACL_ROLE_NONE,
                      'permissions_nodes'    => ACL_ROLE_NONE);

    /** @var null|array $acls_areas caches area-level permissions, keyed by [$area_id][$field] */
    var $acls_areas = NULL;

    /** @var null|array $acls_nodes caches node-level permissions, keyed by [$node_id][$field] */
    var $acls_nodes = NULL;

    /** @var null|array $acls_modules site-level modules permissions, keyed by [$module_id] */
    var $acls_modules = NULL;

    /** @var null|array $acls_modules_areas area-level modules permissions, by [$module_id][$area_id] */
    var $acls_modules_areas = NULL;

    /** @var null|array $acls_modules_nodes node-level modules permissions, by [$module_id][$node_id]*/
    var $acls_modules_nodes = NULL;

    /** @var array $properties */
    var $properties = array();

    /** @var string $editor the user's preferred editor (empty means system default from $CFG->editor) */
    var $editor = '';

    /** @var string $skin the preferred skin for this user */
    var $skin = '';

    /** @var bool $is_logged_in TRUE if user is logged in, FALSE otherwise */
    var $is_logged_in = FALSE;

    /** @var array|null cache for admin permissions based on node permissions */
    var $area_permissions_from_nodes = NULL;


    /** get pertinent user information in core
     *
     * Note:
     * We used to have a bool named 'high_visibility' in both the users table
     * and this class. That changed with version 0.90.4 (April 2012) and we now
     * have a field and variable 'skin' which is a varchar(20). The values were
     * mapped as follows: high_availability=FALSE -> skin='base' and 
     * high_availability=TRUE -> skin='textonly'. The extra test for the
     * existence of $record['skin'] was necessary for the case where the user
     * wanted to upgrade from 0.90.3 to 0.90.4 where 'skin' replaced 'high_visibility'.
     * 
     * @param int $user_id identifies data from which user to load, 0 means no user/a passerby
     * @return void
     */
    function Useraccount($user_id = 0) {
        $user_id = intval($user_id);
        $this->user_id = $user_id;
        if ($this->user_id == 0) { // just a passerby gets no privileges
            return FALSE;
        }
        // Now try to fetch data for this user from database
        $fields = '*';
        $record = db_select_single_record('users',$fields,array('user_id'=>$user_id, 'is_active'=>TRUE));
        if ($record === FALSE) {
            logger('useraccount: cannot find record for user_id \''.$user_id.'\'',WLOG_INFO,$user_id);
            return FALSE;
        }
        $this->username = $record['username'];
        $this->full_name = $record['full_name'];
        $this->email = $record['email'];
        $this->language_key = $record['language_key'];
        $this->path = $record['path'];
        $this->editor = $record['editor'];
        $this->skin = (isset($record['skin'])) ? $record['skin'] : 'base'; // see note above

        // Prepare for retrieval of acls/permissions
        $this->acl_id = intval($record['acl_id']);
        $this->related_acls = calc_user_related_acls($user_id);

        // Always fetch the site-wide permissions from acls table (others are cached on demand)
        $fields = array_keys($this->acls);
        $where = $this->where_acl_id();
        $records = db_select_all_records('acls',$fields,$where);
        if ($records === FALSE) {
            logger('useraccount: cannot find acls records for user_id \''.$user_id.'\'',WLOG_INFO,$user_id);
        } else {
            foreach($records as $record) {
                foreach($fields as $field) {
                    $this->acls[$field] |= $record[$field];
                }
            }
        }

        // get all properties for this user in 2D array (sections, entries)
        $tablename = 'users_properties';
        $fields = array('section','name','type','value');
        $where = array('user_id' => $user_id);
        $order = array('section','name');
        $records = db_select_all_records($tablename,$fields,$where,$order);
        if ($records !== FALSE) {
            $properties = array();
            foreach($records as $rec) {
                $properties[$rec['section']][$rec['name']] = convert_to_type($rec['type'],$rec['value']);
            }
            $this->properties = $properties;
        }
    return TRUE;
    } // Useraccount()


    /** determine user's permissions for the site-level
     *
     * this looks at the site-level permissions for manipulating nodes
     * and areas etc. The permissions are cached from the table acls.
     *
     * @param int $mask bitmap of OR'ed permissions to test for
     * @param string $field name of permissions to check (default 'permissions_nodes')
     * @return bool TRUE if at least one permission in $mask is granted, FALSE otherwise
     */
    function has_site_permissions($mask,$field='permissions_nodes') {
        if ($this->user_id == 0) { // just a passerby gets no privileges
            return FALSE;
        }
        if (($this->acls[$field] & $mask) != 0) {
            return TRUE;
        }
        return FALSE;
    } // has_site_permissions()


    /** determine user's permissions for an area
     *
     * this looks at the area-level permissions for manipulating nodes
     * and areas. However, we first look at the site-level permissions.
     * If those already satisfy the request, we return immediately.
     * If not, the permissions are fetched from the table acls_areas or
     * from the cached data. We only fetch the data if it is really
     * necessary.
     *
     * @param int $mask bitmap of OR'ed permissions to test for
     * @param int $area_id which area to test
     * @param string $field name of permissions to check (default 'permissions_nodes')
     * @return bool TRUE if at least one permission in $mask is granted, FALSE otherwise
     */
    function has_area_permissions($mask,$area_id,$field='permissions_nodes') {
        if ($this->user_id == 0) { // just a passerby gets no privileges
            return FALSE;
        }
        if ($this->has_site_permissions($mask,$field)) {
            return TRUE;
        }
        if (is_null($this->acls_areas)) { // not cached yet, go fetch
            $this->acls_areas = $this->fetch_acls_from_table('acls_areas');
        }
        $area_id = intval($area_id);
        if (isset($this->acls_areas[$area_id][$field])) {
            if (($this->acls_areas[$area_id][$field] & $mask) != 0) {
                return TRUE;
            }
        }
        return FALSE;
    } // has_area_permissions()


    /** determine user's permissions for a node within an area
     *
     * @param int $mask bitmap of OR'ed permissions to test for
     * @param int $area_id which area to test
     * @param int $node_id which node to test
     * @param string $field name of permissions to check (default 'permissions_nodes')
     * @return bool TRUE if at least one permission in $mask is granted, FALSE otherwise
     * @todo FixMe: we need to take the parent nodes into account too!
     */
    function has_node_permissions($mask,$area_id,$node_id,$field='permissions_nodes') {
        if ($this->user_id == 0) { // just a passerby gets no privileges
            return FALSE;
        }
        if ($this->has_area_permissions($mask,$area_id,$field)) {
            return TRUE;
        }
        if (is_null($this->acls_nodes)) { // not cached yet, go fetch
            $this->acls_nodes = $this->fetch_acls_from_table('acls_nodes');
        }
        $node_id = intval($node_id);
        if (isset($this->acls_nodes[$node_id][$field])) {
            if (($this->acls_nodes[$node_id][$field] & $mask) != 0) {
                return TRUE;
            }
        }
        return FALSE;
    } // has_node_permissions()


    /** determine user's permissions for a module at the site-level
     *
     * this looks at the site-level permissions for manipulating
     * mdules. The permissions are cached from the table acls_modules.
     *
     * @param int $mask bitmap of OR'ed permissions to test for
     * @param int  $module_id identifies the module we are considering
     * @return bool TRUE if at least one permission in $mask is granted, FALSE otherwise
     */
    function has_module_site_permissions($mask,$module_id) {
        if ($this->user_id == 0) { // just a passerby gets no privileges
            return FALSE;
        }
        if (($this->acls['permissions_modules'] & $mask) != 0) {
            return TRUE;
        }
        if (is_null($this->acls_modules)) { // not cached yet, go fetch
            $this->acls_modules = $this->fetch_acls_from_table('acls_modules');
        }
        $module_id = intval($module_id);
        if (isset($this->acls_modules[$module_id])) {
            if (($this->acls_modules[$module_id] & $mask) != 0) {
                return TRUE;
            }
        }
        return FALSE;
    } // has_module_site_permissions()


    /** determine user's permissions for a module at the area level
     *
     * this looks at the area-level permissions for manipulating nodes
     * and areas. However, we first look at the site-level permissions.
     * If those already satisfy the request, we return immediately.
     * If not, the permissions are fetched from the table acls_modules_areas
     * or from the cached data. We only fetch the data if it is really
     * necessary.
     *
     * @param int $mask bitmap of OR'ed permissions to test for
     * @param int module_id identifies the module we are considering
     * @param int $area_id which area to test
     * @return bool TRUE if at least one permission in $mask is granted, FALSE otherwise
     */
    function has_module_area_permissions($mask,$module_id,$area_id) {
        if ($this->user_id == 0) { // just a passerby gets no privileges
            return FALSE;
        }
        if (($this->has_module_site_permissions($mask,$module_id)) ||
            ($this->has_area_permissions($mask,$area_id,'permissions_modules'))) {
            return TRUE;
        }
        if (is_null($this->acls_modules_areas)) { // not cached yet, go fetch
            $this->acls_modules_areas = $this->fetch_acls_from_table('acls_modules_areas');
        }
        $module_id = intval($module_id);
        $area_id = intval($area_id);
        if (isset($this->acls_modules_areas[$module_id][$area_id])) {
            if (($this->acls_modules_areas[$module_id][$area_id] & $mask) != 0) {
                return TRUE;
            }
        }
        return FALSE;
    } // has_module_area_permissions()


    /**  determine user's permissions for a module at the node level
     *
     * @param int $mask bitmap of OR'ed permissions to test for
     * @param int module_id identifies the module we are considering
     * @param int $area_id which area to test
     * @param int $node_id which node to test
     * @return bool TRUE if at least one permission in $mask is granted, FALSE otherwise
     * @todo FixMe: we need to take the parent nodes into account too!
     */
    function has_module_node_permissions($mask,$module_id,$area_id,$node_id) {
        if ($this->user_id == 0) { // just a passerby gets no privileges
            return FALSE;
        }
        if (($this->has_module_area_permissions($mask,$module_id,$area_id)) || 
            ($this->has_node_permissions($mask,$area_id,$node_id,'permissions_modules'))) {
            return TRUE;
        }
        if (is_null($this->acls_modules_nodes)) { // not cached yet, go fetch
            $this->acls_modules_nodes = $this->fetch_acls_from_table('acls_modules_nodes');
        }
        $module_id = intval($module_id);
        $node_id = intval($node_id);
        if (isset($this->acls_modules_nodes[$module_id][$node_id])) {
            if (($this->acls_modules_nodes[$module_id][$node_id] & $mask) != 0) {
                return TRUE;
            }
        }
        return FALSE;
    } // has_module_node_permissions()


    /** determine user's permissions for a job
     *
     * @param int $mask bitmap of OR'ed permissions to test for
     * @return bool TRUE if at least one permission in $mask is granted, FALSE otherwise
     */
    function has_job_permissions($mask) {
        if ($this->user_id == 0) { // just a passerby gets no privileges
            return FALSE;
        }
        return $this->has_site_permissions($mask,'permissions_jobs');
    } // has_job_permissions()


    /** determine user's permissions for an intranet area
     *
     * this looks at the area-level permissions for intranet areas.
     *
     * @param int $mask bitmap of OR'ed permissions to test for
     * @param int $area_id which area to test
     * @return bool TRUE if at least one permission in $mask is granted, FALSE otherwise
     */
    function has_intranet_permissions($mask,$area_id) {
        if ($this->user_id == 0) { // just a passerby gets no privileges
            return FALSE;
        }
        return $this->has_area_permissions($mask,$area_id,'permissions_intranet');
    } // has_area_permissions()


    /** determine whether the user has administrator privilege
     *
     * If this user has access to the admin startcenter, she is considered
     * an administrator. Further access depends on the other bits in
     * the job permissions, but at least she is allowed to enter the
     * system via admin.php.
     *
     * @return bool TRUE if user is considered an admin, FALSE otherwise
     */
    function is_admin() {
        if ($this->user_id == 0) { // just a passerby gets no privileges
            return FALSE;
        }
        return $this->has_job_permissions(JOB_PERMISSION_STARTCENTER);
    } // is_admin()


    /** determine whether the user has administrator privilege for pagemanager
     *
     * This routine determines whether a user has any privileges
     * at all for the page manager. This is true in the following cases:
     *
     *  - the user has sitewide permissions that belong to one of the
     *    roles contentmaster, pagemaster, sectionmaster or areamaster, OR
     *  - the user has areawide permissions for one of those roles, OR
     *  - the user has permissions for one of those roles in at least one
     *    node in the requested area.
     *
     * The calculations in the third case are cached for all areas.
     *
     * @param int $area_id the area to examine
     * @return bool TRUE if user is considered a pagemanager admin, FALSE otherwise
     */
    function is_admin_pagemanager($area_id) {
        global $DB;

        if ($this->user_id == 0) { // just a passerby gets no privileges
            return FALSE;
        }

        // 1 -- do we by any chance have sitewide or areawide pagemanager admin permissions?
        $mask = ACL_ROLE_PAGEMANAGER_CONTENTMASTER |
                ACL_ROLE_PAGEMANAGER_PAGEMASTER |
                ACL_ROLE_PAGEMANAGER_SECTIONMASTER |
                ACL_ROLE_PAGEMANAGER_AREAMASTER;
        if ($this->has_area_permissions($mask,$area_id)) {
            return TRUE;
        }
        // 2 -- No, but perhaps there are node-only permissions
        //      Check to see if information is already cached, if not: fetch it
        if (is_null($this->area_permissions_from_nodes)) {
            $this->area_permissions_from_nodes = array();
            $sql = sprintf('SELECT n.area_id, MIN(permissions_nodes) minval, MAX(permissions_nodes) maxval '.
                           'FROM %sacls_nodes a INNER JOIN %snodes n USING (node_id) '.
                           'WHERE %s '.
                           'GROUP BY n.area_id',
                           $DB->prefix,$DB->prefix,$this->where_acl_id('a.acl_id'));
            if (($DBResult = $DB->query($sql)) === FALSE) {
                logger('useraccount: is_admin_pagemanager(): '.db_error());
                return FALSE;
            } else {
                $records = $DBResult->fetch_all_assoc('area_id');
                $DBResult->close();
                foreach($records as $id => $record) {
                    if (($record['minval'] != 0) || ($record['maxval'] !=0)) {
                        $this->area_permissions_from_nodes[$id] = TRUE;
                    }
                }
                unset($records);
            }
        }
        if (isset($this->area_permissions_from_nodes[$area_id])) {
            return $this->area_permissions_from_nodes[$area_id];
        }
        return FALSE;
    } // is_admin_pagemanager()


    /** retrieve acl-data from table into a sparse array
     *
     * @param string $table name of the table which holds the acls
     * @return array zero or more elements with permissions
     */
    function fetch_acls_from_table($table) {
        $where = $this->where_acl_id();
        $a = array();
        switch ($table) {
        case 'acls_areas':
            $fields = array('permissions_intranet','permissions_modules','permissions_nodes');
            $keys = array('area_id');
            break;

        case 'acls_nodes':
            $fields = array('permissions_modules','permissions_nodes');
            $keys = array('node_id');
            break;

        case 'acls_modules':
            $fields = array('permissions_modules');
            $keys = array('module_id');
            break;

        case 'acls_modules_areas':
            $fields = array('permissions_modules');
            $keys = array('module_id','area_id');
            break;

        case 'acls_modules_nodes':
            $fields = array('permissions_modules');
            $keys = array('module_id','node_id');
            break;

        default:
            logger(sprintf("%s(): unknown table '%s'; cannot retrieve acls",__FUNCTION__,$table));
            return array(); // empty array equates to: no access
            break;
        }
        $records = db_select_all_records($table,'*',$where);
        if ($records === FALSE) {
            logger(sprintf("%s(): cannot get acls from '%s'; %s'",__FUNCTION__,$table,db_errormessage()));
            return array(); // empty array equates to: no access
        }
        if (sizeof($keys) == 1) {
            $key = $keys[0];
            if (sizeof($fields) > 1) { // acls_areas, acls_nodes
                foreach($records as $record) {
                    $k = intval($record[$key]);
                    foreach ($fields as $f) {
                        if (($v = intval($record[$f])) != 0) {
                            $a[$k][$f] = (isset($a[$k][$f])) ? $a[$k][$f] | $v : $v;
                        }
                    }
                }
            } else { // acls_modules
                $field = $fields[0];
                foreach($records as $record) {
                    $k = intval($record[$key]);
                    if (($v = intval($record[$field])) != 0) {
                        $a[$k] = (isset($a[$k])) ? $a[$k] | $v : $v;
                    }
                }
            }
        } else { // acls_modules_areas, acls_modules_nodes
            $field = $fields[0];
            foreach($records as $record) {
                if (($v = intval($record[$field])) != 0) {
                    $k0 = intval($record[$keys[0]]);
                    $k1 = intval($record[$keys[1]]);
                    $a[$k0][$k1] = (isset($a[$k0][$k1])) ? $a[$k0][$k1] | $v : $v;
                }
            }
        }
        unset($records);
        return $a;
    } // fetch_acls_from_table()


    /** a convenient routine to construct a selection of acls
     *
     * this constructs a where clause of the
     * form '(acl_id = 1) OR (acl_id = 2) OR (acl_id = 3)'
     *
     * @param string $field identifies the fieldname to check
     * @return string ready-to-use where-clause without the word 'where'
     */
    function where_acl_id($field = 'acl_id') {
        $where = sprintf('(%s = %d)',$field,$this->acl_id);
        foreach($this->related_acls as $acl_id => $dummy) {
            $where .= sprintf(' OR (%s = %d)',$field,$acl_id);
        }
        return $where;
    } // where_acl_id()
} // Useraccount


?>