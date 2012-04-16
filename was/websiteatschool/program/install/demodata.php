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

/** /program/install/demodata.php - code to install the main demodata
 *
 * this file, included from /program/install.php,  contains a
 * single routine which installs the main demodata. This is
 * done only once during a fresh install of a site.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasinstall
 * @version $Id: demodata.php,v 1.12 2012/04/16 16:16:13 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** insert basic demonstration data; the foundation for the module/theme demonstration data
 *
 * this routine inserts all sorts of demonstation data as a foundation for
 * the demonstration of various modules and themes.
 *
 * The array &$messages is used to pass (error) messages back to the
 * caller. The overall result returned is TRUE on success, or FALSE on failure.
 *
 * The parameter &$config is used to communicate essential information
 * about the site that is being installed, such as the main URL and the
 * various directories. Also the information about the first user account
 * is passed; this can be used to setup alerts etc. Finally, this array
 * is used to return the three numbers of the three demonstration areas created.
 *
 * The first demonstration area is a public area. This would be the area to
 * show off all bells and whistles of the CMS. The second demonstration area
 * is a private area. This area could be used to show off an intranet-type of
 * application, maybe accessible only to members of the team.
 * The third area is an in-active area, just for the heck of it.
 *
 * Note that the demonstration data is to be translated. All translations
 * can be found in /program/install/languages/LL/demodata.php where LL indicates
 * the language code. The language to use is specified in the parameter
 * $config['language_key'].
 *
 * This routine is completely self-contained, even the translations are handled
 * manually here.
 *
 * As an added bonus for other demodata routines (eg. the installers of demo data
 * for modules and themes) the main demodata strings are added to $config and also
 * a set of handy search/replace pairs. After calling this routine, the $config array
 * contains the following.
 *
 * <code>
 * $config['language_key']   => install language code (eg. 'en')
 * $config['dir']            => path to CMS Root Directory (eg. /home/httpd/htdocs)
 * $config['www']            => URL of CMS Root Directory (eg. http://exemplum.eu)
 * $config['progdir']        => path to program directory (eg. /home/httpd/htdocs/program)
 * $config['progwww']        => URL of program directory (eg. http://exemplum.eu/program)
 * $config['datadir']        => path to data directory (eg. /home/httpd/wasdata/a1b2c3d4e5f6)
 * $config['title']          => the name of the site
 * $config['user_username']  => userid of webmaster (eg. wblader)
 * $config['user_full_name'] => full name of webmaster (eg. Wilhelmina Bladergroen)
 * $config['user_email']     => email of webmaster (eg. w.bladergroen@exemplum.eu)
 * $config['user_id']        => numerical user_id (usually 1)
 * $config['demo_salt']      => password salt for all demodata accounts
 * $config['demo_password']  => password for all demodata accounts
 * $config['demo_areas']     => array with demo area data
 * $config['demo_groups']    => array with demo group data
 * $config['demo_users']     => array with demo user data
 * $config['demo_nodes']     => array with demo node data
 * $config['demo_string']    => array with demo strings from /program/install/languages/LL/demodata.php
 * $config['demo_replace']   => array with search/replace pairs to 'jazz up' the demo strings
 * </code>
 *
 * @param array &$messages used to return (error) messages to caller
 * @param array &$config pertinent information about the site and also returns additional demo data
 * @return bool TRUE on success + data entered into database, FALSE on error
 */
function demodata(&$messages,&$config) {
    $retval = TRUE; // assume success

    // 0A -- get hold of our translations in $string[]
    $string = array();
    $language_key = $config['language_key'];
    $filename = dirname(__FILE__).'/languages/'.$language_key.'/demodata.php';
    if (!file_exists($filename)) {
        $filename = dirname(__FILE__).'/languages/en/demodata.php';
    }
    @include($filename);
    if (empty($string)) {
        $messages[] = 'Internal error: no translations in '.$filename;
        return FALSE;
    }
    // 0B -- construct a few handy search/replace pairs to 'jazz up' the demodata
    $year = intval(strftime('%Y'));
    if (intval(strftime('%m')) <= 7) { // make schoolyear end on August 1
        $year--;
    }
    $last_schoolyear = sprintf("%04d-%04d",$year-1,$year  );   // e.g. 2008-2009
    $this_schoolyear = sprintf("%04d-%04d",$year,  $year+1);   // 2009-2010
    $next_schoolyear = sprintf("%04d-%04d",$year+1,$year+2);   // 2010-2011
    $config['demo_replace'] = array(
        '{YEAR}'                => strval($year),
        '{LAST_SCHOOLYEAR}'     => $last_schoolyear,
        '{THIS_SCHOOLYEAR}'     => $this_schoolyear,
        '{NEXT_SCHOOLYEAR}'     => $next_schoolyear,
        '{NOW}'                 => strftime('%Y-%m-%d %T'),
        '{TODAY}'               => strftime('%Y-%m-%d'),
        '{YESTERDAY}'           => strftime('%Y-%m-%d',time() - 86400),
        '{LAST_WEEK}'           => strftime('%Y-%m-%d',time() - 604800),
        '{MONTHS_AGO_1}'        => strftime('%Y-%m-%d',time()-3000000), // 3,000,000 seconds is about 35 days
        '{MONTHS_AGO_2}'        => strftime('%Y-%m-%d',time()-6000000),
        '{MONTHS_AGO_3}'        => strftime('%Y-%m-%d',time()-9000000),
        '{MONTHS_AGO_4}'        => strftime('%Y-%m-%d',time()-12000000),
        '{INDEX_URL}'           => $config['www'].'/index.php',
        '{ADMIN_URL}'           => $config['www'].'/admin.php',
        '{MANUAL_URL}'          => $config['progwww'].'/manual.php?language='.$config['language_key'],
        '{WEBSITEATSCHOOL_URL}' => 'http://websiteatschool.eu',
        '{LOREM}'               => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, '.
                                   'sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        '{IPSUM}'               => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco '.
                                   'laboris nisi ut aliquip ex ea commodo consequat.',
        '{DOLOR}'               => 'Duis aute irure dolor in reprehenderit in voluptate velit '.
                                   'esse cillum dolore eu fugiat nulla pariatur.',
        '{SIT}'                 => 'Excepteur sint occaecat cupidatat non proident, '.
                                   'sunt in culpa qui officia deserunt mollit anim id est laborum.'
        );

    // 1 -- prepare 3 demonstration areas
    if (!demodata_areas($messages,$config,$string)) {
        $retval = FALSE;
    }

    // 2 -- prepare a handful of users/groups/capacities/acls
    if (!demodata_users_groups($messages,$config,$string)) {
        $retval = FALSE;
    }

    // 3 -- prepare a few nodes with plain text
    if (!demodata_sections_pages($messages,$config,$string)) {
        $retval = FALSE;
    }

    // 4 -- setup a few alerts
    if (!demodata_alerts($messages,$config,$string)) {
        $retval = FALSE;
    }
    $config['demo_string'] = $string; // just before we leave: remember main strings for subsequent demodata routines
    return $retval;
} // demodata()


/** create three areas + themes
 *
 * @param array &$messages used to return (error) messages to caller
 * @param array &$config pertinent information about the site
 * @param array &$tr translations of demodata texts
 * @return bool TRUE on success + data entered into database, FALSE on error
 */
function demodata_areas(&$messages,&$config,&$tr) {
    global $wizard; // This is a kludge to get to the sanitise_filename() code. There must be a better way...
    global $DB;
    static $seq = 0; // circumvent file/directory name clashes by appending a 'unique' sequence number
    $retval = TRUE; // assume success

    // 0 -- setup essential information
    if (($record = db_select_single_record('themes','theme_id',array('name' => 'frugal'))) === FALSE) {
        $messages[] = $tr['error'].' '.db_errormessage();
        $retval = FALSE;
        $theme_id = 1; // lucky guess
    } else {
        $theme_id = intval($record['theme_id']);
    }
    $user_id = $config['user_id'];
    $metadata = "<meta name=\"keywords\" content=\"school website, websiteatschool, primary education, ".
                             "secondary education, freire, freinet, habermas, learing tool, it learning tool, ".
                             "ict learing tool, ict, bazaar style sheet, bss, screen reader, braille reader, ".
                             "braille terminal, learning html, learning css, free software, exemplum, site@school, ".
                             "siteatschool, websiteatschool.eu\">\n".
                "<meta name=\"description\" content=\"Website@School is a website content management system ".
                             "(CMS) for schools\">\n";
    $now = strftime('%Y-%m-%d %T');

    // 1 -- construct area records
    $areas = array(
        'public' => array(
            'title'      => $tr['public_area_title'],
            'is_private' => FALSE,
            'is_active'  => TRUE,
            'is_default' => TRUE,
            'path'       => utf8_strtolower($wizard->sanitise_filename($tr['public_area_path'])),
            'metadata'   => $metadata,
            'sort_order' => 10,
            'theme_id'   => $theme_id,
            'ctime'      => $now,
            'cuser_id'   => $user_id,
            'mtime'      => $now,
            'muser_id'   => $user_id),
        'private' => array(
            'title'      => $tr['private_area_title'],
            'is_private' => TRUE,
            'is_active'  => TRUE,
            'is_default' => FALSE,
            'path'       => utf8_strtolower($wizard->sanitise_filename($tr['private_area_path'])),
            'sort_order' => 20,
            'theme_id'   => $theme_id,
            'ctime'      => $now,
            'cuser_id'   => $user_id,
            'mtime'      => $now,
            'muser_id'   => $user_id),
        'extra' => array(
            'title' => $tr['extra_area_title'],
            'is_private' => FALSE,
            'is_active'  => FALSE,
            'is_default' => FALSE,
            'path'       => utf8_strtolower($wizard->sanitise_filename($tr['extra_area_path'])),
            'sort_order' => 30,
            'theme_id'   => $theme_id,
            'ctime'      => $now,
            'cuser_id'   => $user_id,
            'mtime'      => $now,
            'muser_id'   => $user_id)
        );

    // 1A -- make sure the area directories so not exist yet; maybe change name
    $datadir_areas = $config['datadir'].'/areas/';
    foreach ($areas as $area => $fields) {
        $path = $fields['path'];
        if ($path == '_') {
            $path .= strval(++$seq);
        }
        $ext = '';
        while (is_dir($datadir_areas.$path.$ext)) {
            $ext = strval(++$seq);
        }
        $areas[$area]['path'] = $path.$ext;
    }

    // 1B -- actually make the directories and store the name and other data in table
    foreach ($areas as $area => $fields) {
        $fullpath = $datadir_areas.$fields['path'];
        if (@mkdir($fullpath,0700)) {
            @touch($fullpath.'/index.html'); // try to "protect" directory
        } else {
            $messages[] = $tr['error']." mkdir('$fullpath')";
            $retval = FALSE;
        }

        if (($area_id = db_insert_into_and_get_id('areas',$fields,'area_id')) === FALSE) {
            $messages[] = $tr['error'].' '.db_errormessage();
            $retval = FALSE;
        }
        $area_id = intval($area_id);
        // remember the area_id
        $areas[$area]['area_id'] = intval($area_id);

        // copy the theme's default setting to this area
        $sql = sprintf('INSERT INTO %s%s(area_id,theme_id,name,type,value,extra,sort_order,description) '.
                       'SELECT %d AS area_id,theme_id,name,type,value,extra,sort_order,description '.
                       'FROM %s%s '.
                       'WHERE theme_id = %d',
                        $DB->prefix,'themes_areas_properties',intval($area_id),
                        $DB->prefix,'themes_properties',intval($theme_id));
        if ($DB->exec($sql) === FALSE) {
            $messages[] = $tr['error'].' '.db_errormessage();
            $retval = FALSE;
        }
    }
    $config['demo_areas'] = $areas;
    return $retval;
} // demodata_areas()


/** create a handful of users/groups/capacities/acls
 *
 * This routine creates the following 4 groups:
 *  - faculty (principals and teachers)
 *  - team (principals and teachers and all other employees)
 *  - seniors (pupils in grades 5 to 8)
 *  - juniors (pupils in grades 1 to 4)
 *
 * The following 7 group/capacties are also created
 *  - faculty/principal (3)
 *  - faculty/member (4)
 *  - team/member (4)
 *  - seniors/pupil (1)
 *  - seniors/teacher (2)
 *  - juniors/pupil (1)
 *  - juniors/teacher (2)
 *
 * The following 8 users are also created
 *  - Amelia Cackle (acackl): Faculty/Principal, Team/Member
 *  - Maria Montessori (mmonte): Faculty/Member, Team/Member, Seniors/Teacher
 *  - Helen Parkhurst (hparkh): Faculty/Member, Team/Member, Juniors/Teacher
 *  - Freddie Frinton (ffrint): Team/Member
 *  - Andrew Reese (andrew): Seniors/Pupil
 *  - Catherine Hayes (catherine): Seniors/Pupil
 *  - Herbert Spencer (herberd): Juniors/Pupil
 *  - Georgina King (georgina): Juniors/Pupil
 *
 * Every user and every group/capacity gets their own acl
 *  - faculty/principal: access to all private areas
 *  - faculty/member: access to intranet in $config['demo_areas']['private']['area_id']
 *  - others get no special privileges
 *
 * The arrays with groups (including the assigned group_id) and users (with
 * the assigned user_id) are stored in $config['demo_groups'] and $config['demo_users'],
 * for the caller's perusal.
 *
 * @param array &$messages used to return (error) messages to caller
 * @param array &$config pertinent information about the site, also receives copy of users/groups data
 * @param array &$tr translations of demodata texts
 * @return bool TRUE on success + data entered into database, FALSE on error
 * @todo get rid of the $wizard kludge!
 * @todo should we append an underscore to the userpaths to make sure we don't clash
 *       with the first user account?
 * @todo should we also add groups_capacities, acls, users_groups_capacities to $config or
 *       are users and groups enough?
 */
function demodata_users_groups(&$messages,&$config,&$tr) {
    global $wizard; // This is a kludge to get to the sanitise_filename() code. There must be a better way...
    $retval = TRUE; // assume success
    static $seq = 0; // circumvent file/directory name clashes by appending a 'unique' sequence number

    // 1 -- create the 4 groups
    $groups = array(
        'faculty' => array(
            'groupname' => $tr['groupname_faculty'],
            'full_name' => $tr['full_name_faculty'],
            'is_active' => TRUE,
            'path' => utf8_strtolower($wizard->sanitise_filename($tr['groupname_faculty']))),
        'team' => array(
            'groupname' => $tr['groupname_team'],
            'full_name' => $tr['full_name_team'],
            'is_active' => TRUE,
            'path' => utf8_strtolower($wizard->sanitise_filename($tr['groupname_team']))),
        'seniors' => array(
            'groupname' => $tr['groupname_seniors'],
            'full_name' => $tr['full_name_seniors'],
            'is_active' => TRUE,
            'path' => utf8_strtolower($wizard->sanitise_filename($tr['groupname_seniors']))),
        'juniors' => array(
            'groupname' => $tr['groupname_juniors'],
            'full_name' => $tr['full_name_juniors'],
            'is_active' => TRUE,
            'path' => utf8_strtolower($wizard->sanitise_filename($tr['groupname_juniors'])))
        );


    // 1A -- make sure the group directories so not exist yet; maybe change name
    $datadir_groups = $config['datadir'].'/groups/';
    foreach ($groups as $group => $fields) {
        $path = $fields['path'];
        if ($path == '_') {
            $path .= strval(++$seq);
        }
        $ext = '';
        while (is_dir($datadir_groups.$path.$ext)) {
            $ext = strval(++$seq);
        }
        $groups[$group]['path'] = $path.$ext;
    }

    // 1B -- actually make the directories and store the name and other data in table
    foreach ($groups as $group => $fields) {
        $fullpath = $datadir_groups.$fields['path']; 
        if (@mkdir($fullpath,0700)) {
            @touch($fullpath.'/index.html');
        } else {
            $messages[] = $tr['error']." mkdir('$fullpath')";
            $retval = FALSE;
        }
        if (($group_id = db_insert_into_and_get_id('groups',$fields,'group_id')) === FALSE) {
            $messages[] = $tr['error'].' '.db_errormessage();
            $retval = FALSE;
        }
        $groups[$group]['group_id'] = intval($group_id);
    }

    // 2A -- prepare 7 acls for the groups/capacities combinations and 8 acls for users
    //
    // Important note:
    // If you add more demo useraccounts, be sure to update $wizard->check_for_nameclash() too!
    //
    $acls = array(
        'faculty_principal' => array('permissions_intranet' => 1),
        'faculty_member' => array('permissions_intranet' => 0),
        'team_member' => array('permissions_intranet' => 0),
        'seniors_pupil' => array('permissions_intranet' => 0),
        'seniors_teacher' => array('permissions_intranet' => 0),
        'juniors_pupil' => array('permissions_intranet' => 0),
        'juniors_teacher' => array('permissions_intranet' => 0),
        'acackl' => array('permissions_intranet' => 0),
        'mmonte' => array('permissions_intranet' => 0),
        'hparkh' => array('permissions_intranet' => 0),
        'ffrint' => array('permissions_intranet' => 0),
        'andrew' => array('permissions_intranet' => 0),
        'catherine' => array('permissions_intranet' => 0),
        'herbert' => array('permissions_intranet' => 0),
        'georgina' => array('permissions_intranet' => 0)
        );
    foreach ($acls as $acl => $fields) {
        if (($acl_id = db_insert_into_and_get_id('acls',$fields,'acl_id')) === FALSE) {
            $messages[] = $tr['error'].' '.db_errormessage();
            $retval = FALSE;
        }
        $acls[$acl]['acl_id'] = intval($acl_id);
    }
    // 2B -- add an additional acl for facultymembers for the first private area
    $fields = array(
        'acl_id' => $acls['faculty_member']['acl_id'],
        'area_id' => $config['demo_areas']['private']['area_id'],
        'permissions_intranet' => 1);
    if (db_insert_into('acls_areas',$fields) === FALSE) {
        $messages[] = $tr['error'].' '.db_errormessage();
        $retval = FALSE;
    }

    // 3 -- construct the groups/capacities
    $groups_capacities = array(
        array(
            'group_id'      => $groups['faculty']['group_id'],
            'capacity_code' => 3, 
            'sort_order'    => 1,
            'acl_id'        => $acls['faculty_principal']['acl_id']),
        array(
            'group_id'      => $groups['faculty']['group_id'],
            'capacity_code' => 4, 
            'sort_order'    => 2,
            'acl_id'        => $acls['faculty_member']['acl_id']),
        array(
            'group_id'      => $groups['team']['group_id'],
            'capacity_code' => 4, 
            'sort_order'    => 1,
            'acl_id'        => $acls['team_member']['acl_id']),
        array(
            'group_id'      => $groups['seniors']['group_id'],
            'capacity_code' => 1, 
            'sort_order'    => 1,
            'acl_id'        => $acls['seniors_pupil']['acl_id']),
        array(
            'group_id'      => $groups['seniors']['group_id'],
            'capacity_code' => 2, 
            'sort_order'    => 2,
            'acl_id'        => $acls['seniors_teacher']['acl_id']),
        array(
            'group_id'      => $groups['juniors']['group_id'],
            'capacity_code' => 1, 
            'sort_order'    => 1,
            'acl_id'        => $acls['juniors_pupil']['acl_id']),
        array(
            'group_id'      => $groups['juniors']['group_id'],
            'capacity_code' => 2, 
            'sort_order'    => 2,
            'acl_id'        => $acls['juniors_teacher']['acl_id']),
        );
    foreach ($groups_capacities as $fields) {
        if (db_insert_into('groups_capacities',$fields) === FALSE) {
            $messages[] = $tr['error'].' '.db_errormessage();
            $retval = FALSE;
        }
    }

    // 4 -- create the user accounts
    //
    // Important note:
    // If you add more demo useraccounts, be sure to update $wizard->check_for_nameclash() too!
    //
    $users = array(
        'acackl' => array(
            'username' => 'acackl',
            'acl_id' => $acls['acackl']['acl_id'],
            'full_name' => 'Amelia Cackle'),
        'mmonte' => array(
            'username' => 'mmonte',
            'acl_id' => $acls['mmonte']['acl_id'],
            'full_name' => 'Maria Montessori'),
        'hparkh' => array(
            'username' => 'hparkh',
            'acl_id' => $acls['hparkh']['acl_id'],
            'full_name' => 'Helen Parkhurst'),
        'ffrint' => array(
            'username' => 'ffrint',
            'acl_id' => $acls['ffrint']['acl_id'],
            'full_name' => 'Freddie Frinton'),
        'andrew' => array(
            'username' => 'andrew',
            'acl_id' => $acls['andrew']['acl_id'],
            'full_name' => 'Andrew Reese'),
        'catherine' => array(
            'username' => 'catherine',
            'acl_id' => $acls['catherine']['acl_id'],
            'full_name' => 'Catherine Hayes'),
        'herbert' => array(
            'username' => 'herbert',
            'acl_id' => $acls['herbert']['acl_id'],
            'full_name' => 'Herbert Spencer'),
        'georgina' => array(
            'username' => 'georgina',
            'acl_id' => $acls['georgina']['acl_id'],
            'full_name' => 'Georgina King')
        );
    // All demo-accounts get the same password, email and language
    // We use the email of the main user, so a password reset request
    // will not work other than for the webmaster
    $salt = $config['demo_salt'];
    $password = $config['demo_password'];
    $password_hash = md5($salt.$password);
    $email = $config['user_email'];
    $language_key = $config['language_key'];
    foreach($users as $user => $fields) {
        $fields['salt']          = $salt;
        $fields['password_hash'] = $password_hash;
        $fields['email']         = $email;
        $fields['is_active']     = TRUE;
        $fields['language_key']  = $language_key;
        $fields['path'] = utf8_strtolower($wizard->sanitise_filename($fields['username']));
        $fields['editor'] = 'ckeditor';
        if (($user_id = db_insert_into_and_get_id('users',$fields,'user_id')) === FALSE) {
            $messages[] = $tr['error'].' '.db_errormessage();
            $retval = FALSE;
        }
        $fields['user_id'] = intval($user_id);
        $users[$user] = $fields;

        // create the datadirectory (name is already recorded)
        $path = $fields['path'];
        $fullpath = $config['datadir'].'/users/'.$path;
        if (@mkdir($fullpath,0700)) {
            @touch($fullpath.'/index.html');
        } else {
            $messages[] = $tr['error']." mkdir('$fullpath')";
            $retval = FALSE;
        }
    }

    // 5 -- add the users to the group/capacities
    //
    // Important note:
    // If you add more demo useraccounts, be sure to update $wizard->check_for_nameclash() too!
    //
    $users_groups_capacities = array(
        array(
            'user_id'       => $users['acackl']['user_id'],
            'group_id'      => $groups['faculty']['group_id'],
            'capacity_code' => 3),
        array(
            'user_id'       => $users['acackl']['user_id'],
            'group_id'      => $groups['team']['group_id'],
            'capacity_code' => 4),
        array(
            'user_id'       => $users['mmonte']['user_id'],
            'group_id'      => $groups['faculty']['group_id'],
            'capacity_code' => 4),
        array(
            'user_id'       => $users['mmonte']['user_id'],
            'group_id'      => $groups['team']['group_id'],
            'capacity_code' => 4),
        array(
            'user_id'       => $users['mmonte']['user_id'],
            'group_id'      => $groups['seniors']['group_id'],
            'capacity_code' => 2),
        array(
            'user_id'       => $users['hparkh']['user_id'],
            'group_id'      => $groups['faculty']['group_id'],
            'capacity_code' => 4),
        array(
            'user_id'       => $users['hparkh']['user_id'],
            'group_id'      => $groups['team']['group_id'],
            'capacity_code' => 4),
        array(
            'user_id'       => $users['hparkh']['user_id'],
            'group_id'      => $groups['juniors']['group_id'],
            'capacity_code' => 2),
        array(
            'user_id'       => $users['ffrint']['user_id'],
            'group_id'      => $groups['team']['group_id'],
            'capacity_code' => 4),
        array(
            'user_id'       => $users['andrew']['user_id'],
            'group_id'      => $groups['seniors']['group_id'],
            'capacity_code' => 1),
        array(
            'user_id'       => $users['catherine']['user_id'],
            'group_id'      => $groups['seniors']['group_id'],
            'capacity_code' => 1),
        array(
            'user_id'       => $users['herbert']['user_id'],
            'group_id'      => $groups['juniors']['group_id'],
            'capacity_code' => 1),
        array(
            'user_id'       => $users['georgina']['user_id'],
            'group_id'      => $groups['juniors']['group_id'],
            'capacity_code' => 1)
        );
    foreach ($users_groups_capacities as $fields) {
        if (db_insert_into('users_groups_capacities',$fields) === FALSE) {
            $messages[] = $tr['error'].' '.db_errormessage();
            $retval = FALSE;
        }
    }
    // 6 -- tell caller about users and groups (including assigned group_id and user_id)
    $config['demo_users'] = $users;
    $config['demo_groups'] = $groups;
    return $retval;
} // demodata_users_groups()


/** create a few sections and pages
 *
 * this constructs a complete public area with some pages and sections
 * and also the 'frugal' theme is configured for this area.
 * The information about the nodes (including the assigned node_id) is
 * copied to $config['demo_nodes'] for the caller's perusal.
 *
 * @param array &$messages used to return (error) messages to caller
 * @param array &$config pertinent information about the site; receives copy of nodes array on return
 * @param array &$tr translations of demodata texts
 * @return bool TRUE on success + data entered into database, FALSE on error
 */
function demodata_sections_pages(&$messages,&$config,&$tr) {
    $retval = TRUE;
    // 0 -- setup essential information
    $table = 'modules';
    $fields = array('module_id','name');
    $where = '';
    $order = '';
    $keyfield = 'name';
    if (($records = db_select_all_records($table,$fields,$where,$order,$keyfield)) === FALSE) {
        // if we cannot determine the module_id's there is no point to stay here and 'pollute' the database with nonsense
        $messages[] = $tr['error'].' '.db_errormessage();
        return FALSE;
    }
    $htmlpage_id = intval($records['htmlpage']['module_id']);
    $sitemap_id  = intval($records['sitemap']['module_id']);

    $replace = $config['demo_replace'];
    $year = intval($replace['{YEAR}']);
    $nodes = array(
        'welcome' => array(
            'parent_id' => 'welcome',
            'is_page' => TRUE,
            'is_default' => TRUE,
            'title' => $tr['welcome_title'],
            'link_text' => $tr['welcome_link_text'],
            'sort_order' => 10,
            'module_id' => $htmlpage_id),
        'schoolinfo' => array(
            'parent_id' => 'schoolinfo',
            'is_page' => FALSE,
            'title' => $tr['schoolinfo_title'],
            'link_text' => $tr['schoolinfo_link_text'],
            'sort_order' => 20),
        'aboutus' => array(
            'parent_id' => 'schoolinfo',
            'is_page' => TRUE,
            'title' => $tr['aboutus_title'],
            'link_text' => $tr['aboutus_link_text'],
            'sort_order' => 10,
            'module_id' => $htmlpage_id),
        'schoolterms1' => array(
            'parent_id' => 'schoolinfo',
            'is_page' => TRUE,
            'title' => strtr($tr['schoolterms_title'],array('{SCHOOLYEAR}' => $replace['{LAST_SCHOOLYEAR}'])),
            'link_text' => strtr($tr['schoolterms_link_text'],array('{SCHOOLYEAR}' => $replace['{LAST_SCHOOLYEAR}'])),
            'embargo' => sprintf('%04d-08-01 00:00:00',$year-1),
            'expiry' => sprintf('%04d-08-01 00:00:00',$year),
            'sort_order' => 20,
            'module_id' => $htmlpage_id),
        'schoolterms2' => array(
            'parent_id' => 'schoolinfo',
            'is_page' => TRUE,
            'title' => strtr($tr['schoolterms_title'],array('{SCHOOLYEAR}' => $replace['{THIS_SCHOOLYEAR}'])),
            'link_text' => strtr($tr['schoolterms_link_text'],array('{SCHOOLYEAR}' => $replace['{THIS_SCHOOLYEAR}'])),
            'embargo' => sprintf('%04d-08-01 00:00:00',$year),
            'expiry' => sprintf('%04d-08-01 00:00:00',$year+1),
            'sort_order' => 30,
            'module_id' => $htmlpage_id),
        'schoolterms3' => array(
            'parent_id' => 'schoolinfo',
            'is_page' => TRUE,
            'title' => strtr($tr['schoolterms_title'],array('{SCHOOLYEAR}' => $replace['{NEXT_SCHOOLYEAR}'])),
            'link_text' => strtr($tr['schoolterms_link_text'],array('{SCHOOLYEAR}' => $replace['{NEXT_SCHOOLYEAR}'])),
            'embargo' => sprintf('%04d-08-01 00:00:00',$year+1),
            'expiry' => sprintf('%04d-08-01 00:00:00',$year+2),
            'sort_order' => 40,
            'module_id' => $htmlpage_id),
        'news' => array(
            'parent_id' => 'news',
            'is_page' => FALSE,
            'title' => $tr['news_title'],
            'link_text' => $tr['news_link_text'],
            'sort_order' => 30),
        'latestnews' => array(
            'parent_id' => 'news',
            'is_page' => TRUE,
            'title' => $tr['latestnews_title'],
            'link_text' => $tr['latestnews_link_text'],
            'sort_order' => 10,
            'module_id' => $htmlpage_id),
        'latestnewsletter' => array(
            'parent_id' => 'news',
            'is_page' => TRUE,
            'title' => $tr['latestnewsletter_title'],
            'link_text' => $tr['latestnewsletter_link_text'],
            'sort_order' => 20,
            'module_id' => $htmlpage_id),
        'newsarchive' => array(
            'parent_id' => 'news',
            'is_page' => FALSE,
            'title' => $tr['newsarchive_title'],
            'link_text' => $tr['newsarchive_link_text'],
            'sort_order' => 30),
        'oldnews' => array(
            'parent_id' => 'newsarchive',
            'is_page' => TRUE,
            'title' => $tr['oldnews_title'],
            'link_text' => $tr['oldnews_link_text'],
            'sort_order' => 10,
            'module_id' => $htmlpage_id),
        'oldnewsletters' => array(
            'parent_id' => 'newsarchive',
            'is_page' => TRUE,
            'title' => $tr['oldnewsletters_title'],
            'link_text' => $tr['oldnewsletters_link_text'],
            'sort_order' => 20,
            'module_id' => $htmlpage_id),
        'search' => array(
            'parent_id' => 'search',
            'is_page' => FALSE,
            'title' => $tr['search_title'],
            'link_text' => $tr['search_link_text'],
            'sort_order' => 40),
        'searchbox' => array(
            'parent_id' => 'search',
            'is_page' => TRUE,
            'title' => $tr['searchbox_title'],
            'link_text' => $tr['searchbox_link_text'],
            'sort_order' => 10,
            'module_id' => $htmlpage_id),
        'sitemap' => array(
            'parent_id' => 'search',
            'is_page' => TRUE,
            'title' => $tr['sitemap_title'],
            'link_text' => $tr['sitemap_link_text'],
            'sort_order' => 20,
            'module_id' => $sitemap_id),
        'mypage' => array(
            'parent_id' => 'mypage',
            'is_page' => TRUE,
            'title' => $tr['mypage_title'],
            'link_text' => $tr['mypage_link_text'],
            'sort_order' => 50,
            'module_id' => $htmlpage_id),
        'quicktop' => array(
            'parent_id' => 'quicktop',
            'is_page' => FALSE,
            'is_hidden' => TRUE,
            'title' => $tr['quicktop_title'],
            'link_text' => $tr['quicktop_link_text'],
            'sort_order' => 60),
        'about' => array(
            'parent_id' => 'quicktop',
            'is_page' => TRUE,
            'title' => $tr['about_title'],
            'link_text' => $tr['about_link_text'],
            'sort_order' => 10,
            'module_id' => $htmlpage_id),
        'contact' => array(
            'parent_id' => 'quicktop',
            'is_page' => TRUE,
            'title' => $tr['contact_title'],
            'link_text' => $tr['contact_link_text'],
            'sort_order' => 20,
            'module_id' => $htmlpage_id),
        'quickbottom' => array(
            'parent_id' => 'quickbottom',
            'is_page' => FALSE,
            'is_hidden' => TRUE,
            'title' => $tr['quickbottom_title'],
            'link_text' => $tr['quickbottom_link_text'],
            'sort_order' => 70),
        'disclaimer' => array(
            'parent_id' => 'quickbottom',
            'is_page' => TRUE,
            'title' => $tr['disclaimer_title'],
            'link_text' => $tr['disclaimer_link_text'],
            'sort_order' => 10,
            'module_id' => $htmlpage_id),
        'login' => array(
            'parent_id' => 'quickbottom',
            'is_page' => TRUE,
            'title' => $tr['login_title'],
            'link_text' => $tr['login_link_text'],
            'sort_order' => 20,
            'module_id' => $htmlpage_id),
        'intranet' => array(
            'parent_id' => 'intranet',
            'is_page' => TRUE,
            'is_default' => TRUE,
            'title' => $tr['intranet_title'],
            'link_text' => $tr['intranet_link_text'],
            'sort_order' => 10,
            'module_id' => $htmlpage_id),
        'meetings' => array(
            'parent_id' => 'meetings',
            'is_page' => FALSE,
            'title' => $tr['meetings_title'],
            'link_text' => $tr['meetings_link_text'],
            'sort_order' => 20),
        'roster' => array(
            'parent_id' => 'meetings',
            'is_page' => TRUE,
            'title' => $tr['roster_title'],
            'link_text' => $tr['roster_link_text'],
            'sort_order' => 10,
            'module_id' => $htmlpage_id),
        'minutes' => array(
            'parent_id' => 'meetings',
            'is_page' => FALSE,
            'title' => strtr($tr['minutes_title'],array('{SCHOOLYEAR}' => $replace['{LAST_SCHOOLYEAR}'])),
            'link_text' => strtr($tr['minutes_link_text'],array('{SCHOOLYEAR}' => $replace['{LAST_SCHOOLYEAR}'])),
            'sort_order' => 20),
        'minutes1' => array(
            'parent_id' => 'minutes',
            'is_page' => TRUE,
            'title' => $tr['minutes1_title'],
            'link_text' => $tr['minutes1_link_text'],
            'sort_order' => 10,
            'module_id' => $htmlpage_id),
        'minutes2' => array(
            'parent_id' => 'minutes',
            'is_page' => TRUE,
            'title' => $tr['minutes2_title'],
            'link_text' => $tr['minutes2_link_text'],
            'sort_order' => 20,
            'module_id' => $htmlpage_id),
        'minutes3' => array(
            'parent_id' => 'minutes',
            'is_page' => TRUE,
            'title' => $tr['minutes3_title'],
            'link_text' => $tr['minutes3_link_text'],
            'sort_order' => 30,
            'module_id' => $htmlpage_id),
        'minutes4' => array(
            'parent_id' => 'minutes',
            'is_page' => TRUE,
            'title' => $tr['minutes4_title'],
            'link_text' => $tr['minutes4_link_text'],
            'sort_order' => 40,
            'module_id' => $htmlpage_id),
        'newminutes' => array(
            'parent_id' => 'meetings',
            'is_page' => FALSE,
            'title' => strtr($tr['minutes_title'],array('{SCHOOLYEAR}' => $replace['{THIS_SCHOOLYEAR}'])),
            'link_text' => strtr($tr['minutes_link_text'],array('{SCHOOLYEAR}' => $replace['{THIS_SCHOOLYEAR}'])),
            'sort_order' => 30),
        'minutes5' => array(
            'parent_id' => 'newminutes',
            'is_page' => TRUE,
            'title' => $tr['minutes1_title'],
            'link_text' => $tr['minutes1_link_text'],
            'sort_order' => 10,
            'module_id' => $htmlpage_id),
        'downloads' => array(
            'parent_id' => 'downloads',
            'is_page' => TRUE,
            'title' => $tr['downloads_title'],
            'link_text' => $tr['downloads_link_text'],
            'sort_order' => 30,
            'module_id' => $htmlpage_id)
        );
    $now = strftime('%Y-%m-%d %T');
    $user_id = $config['user_id'];
    $area_id = $config['demo_areas']['public']['area_id'];
    foreach($nodes as $node => $fields) {
        if ($node == 'intranet') { // the nodes that follow are in another area
            $area_id = $config['demo_areas']['private']['area_id'];
        }
        $fields['area_id']  = $area_id;
        $fields['ctime']    = $now;
        $fields['mtime']    = $now;
        $fields['atime']    = $now;
        $fields['owner_id'] = $user_id;

        // Note: this is the reason we don't have a FK (parent_id) referencing nodes(node_id): 0 is an invalid value
        if ($fields['parent_id'] == $node) { // parent points to self, use 0 as a sentinel
            $fields['parent_id'] = 0;
        } else { // plug in the node_id of the parent node (which we already processed)
            $fields['parent_id'] = $nodes[$fields['parent_id']]['node_id'];
        }
        if (($node_id = db_insert_into_and_get_id('nodes',$fields,'node_id')) === FALSE) {
            $messages[] = $tr['error'].' '.db_errormessage();
            $retval = FALSE;
        }
        $node_id = intval($node_id);
        $fields['node_id'] = $node_id;
        if ($fields['parent_id'] == 0) { // parent points to self, adjust the 0 in the database
            $fields['parent_id'] = $node_id;
            if (db_update('nodes',array('parent_id' => $node_id),array('node_id' => $node_id)) === FALSE) {
                $messages[] = $tr['error'].' '.db_errormessage();
                $retval = FALSE;
            }
        }
        $nodes[$node] = $fields;

        // Fill pages with actual content (sort of)
        if ($fields['is_page']) {
            switch($fields['module_id']) {
            case $htmlpage_id:
                $htmlpage_fields = array(
                    'node_id' => $node_id,
                    'version' => 1,
                    'page_data' => strtr($tr[$node.'_content'],$replace),
                    'ctime' => $now,
                    'cuser_id' => $user_id,
                    'mtime' => $now,
                    'muser_id' => $user_id);
                if (db_insert_into('htmlpages',$htmlpage_fields) === FALSE) {
                    $messages[] = $tr['error'].' '.db_errormessage();
                    $retval = FALSE;
                }
                break;
            case $sitemap_id:
                $sitemap_fields = array(
                    'node_id' => $node_id,
                    'header' => $tr['sitemap_title'],
                    'introduction' => strtr($tr[$node.'_content'],$replace),
                    'scope' => 1,
                    'ctime' => $now,
                    'cuser_id' => $user_id,
                    'mtime' => $now,
                    'muser_id' => $user_id);
                if (db_insert_into('sitemaps',$sitemap_fields) === FALSE) {
                    $messages[] = $tr['error'].' '.db_errormessage();
                    $retval = FALSE;
                }
                break;
            default:
                $messages[] = 'Internal error: unknown module '.$field['module_id'];
                break;
            }
        }
    }
    // Now plug in the correct values for quicktop/quickbottom in the theme
    $theme_updates = array(
        array(
            'fields' => array('value' => strval($nodes['quicktop']['node_id'])),
            'where' => array(
                'area_id' => $config['demo_areas']['public']['area_id'],
                'name' => 'quicktop_section_id'
                )
            ),
        array(
            'fields' => array('value' => strval($nodes['quickbottom']['node_id'])),
            'where' => array(
                'area_id' => $config['demo_areas']['public']['area_id'],
                'name' => 'quickbottom_section_id'
                )
            )
        );
    foreach($theme_updates as $theme_update) {
        if (db_update('themes_areas_properties',$theme_update['fields'],$theme_update['where']) === FALSE) {
            $messages[] = $tr['error'].' '.db_errormessage();
            $retval = FALSE;
        }
    }
    $config['demo_nodes'] = $nodes;
    return $retval;
} // demodata_sections_pages()


/** create a few alerts
 *
 *
 * @param array &$messages used to return (error) messages to caller
 * @param array &$config pertinent information about the site
 * @param array &$tr translations of demodata texts
 * @return bool TRUE on success + data entered into database, FALSE on error
 */
function demodata_alerts(&$messages,&$config,&$tr) {
    $retval = TRUE;
    $now = strftime("%Y-%m-%d %T");
    $email = $config['user_email'];
    $alerts = array(
        'webmaster' => array(
            'full_name' => $config['user_full_name'],
            'email' => $email,
            'cron_interval' => 1440, //  1440 minutes is 1 day
            'cron_next' => $now, // make a head start the first time
            'messages' => 1,
            'message_buffer' => $now."\n".
                                $tr['alerts_initial_load']."\n".
                                $tr['alerts_every_1440_minutes']."\n".
                                $tr['alerts_all_areas']."\n".
                                $tr['alerts_email_address']."\n".
                                $email." (".$config['user_full_name'].")\n",
            'is_active' => TRUE
            ),
        'acackl' => array(
            'full_name' => 'Amelia Cackle',
            'email' => $email,
            'cron_interval' => 60, //  60 minutes is 1 hour
            'cron_next' => $now, // make a head start the first time
            'messages' => 1,
            'message_buffer' => $now."\n".
                                $tr['alerts_initial_load']."\n".
                                $tr['alerts_every_60_minutes']."\n".
                                $tr['alerts_private_area']."\n".
                                $tr['alerts_email_address']."\n".
                                $email." (Amelia Cackle)\n",
            'is_active' => TRUE
            )

        );
    foreach($alerts as $alert => $fields) {
        if (($alert_id = db_insert_into_and_get_id('alerts',$fields,'alert_id')) === FALSE) {
            $messages[] = $tr['error'].' '.db_errormessage();
            $retval = FALSE;
        }
        $alerts[$alert]['alert_id'] = intval($alert_id);
    }
    $alerts_areas_nodes = array(
        'webmaster' => array(
            'alert_id' => $alerts['webmaster']['alert_id'],
            'area_id' => 0, // a change in any area will prompt an alert
            'node_id' => 0,
            'flag' => TRUE),
        'acackl' => array(
            'alert_id' => $alerts['acackl']['alert_id'],
            'area_id' => $config['demo_areas']['private']['area_id'], // alert only on change in the intranet area
            'node_id' => 0,
            'flag' => TRUE)
        );
    foreach($alerts_areas_nodes as $fields) {
        if (db_insert_into('alerts_areas_nodes',$fields) === FALSE) {
            $messages[] = $tr['error'].' '.db_errormessage();
            $retval = FALSE;
        }
    }
    return $retval;
} // demodata_alerts()

/* *****
 *

Below is the overall idea about the demo data


Groups (capacities):
- Faculty (Member, Principal) =4,3
- Team (Member) =4
- Seniors (Pupil, Teacher) =1,2
- Juniors (Pupil, Teacher) =1,2

Users
- acackl Faculty/Principal, Team/Member
- hparkh Faculty/Member, Team/Member, Seniors/Teacher
- mmonte Faculty/Member, Team/Member, Juniors/Teacher
- ffrint Team/Member
- andrew Seniors/Pupil
- catherine Seniors/Pupil
- herbert Juniors/Pupil
- georgina Juniors/Pupil

Areas
- Public area (frugal theme with bells and whistles)
- Private area (bare frugal theme)
- Inactive area (frugal theme)

Alerts
- Everything to webmaster account, every day?
- Intranet to acackl, every 1 hour

Content area 1 (public)

- Welcome (default page)
- School info
  - About us
  - School term dates 2008-2009 (expired)
  - School term dates 2009-2010
  - School term dates 2010-2011 (embargo)
- News
  - Latest news
  - Newsletter
  - Archive
    - Old news
    - Old newsletters
- Pupils
  - Juniors
  - Seniors
  - Pictures
- Teachers
  - Team
    - Principal
    - Teachers
    - Other employees
- Parents
  - Introduction
  - Links
  - Help wanted
- Search
  - Search this site
  - Site map
- Quicklinks Top (hidden)
  - about
  - contact
- Quicklinks Bottom (hidden)
  - terms of use
  - disclaimer
  - login

Content area 2 (intranet)
- Intranet
- Meeting Minutes
  - Minutes 2008-2009
    - Summer
    - Fall
    - Winter
    - Spring    
  - Minutes 2009-2010
    - Summer
    - Fall
    - Winter
    - Spring
- Downloads

***** */


?>