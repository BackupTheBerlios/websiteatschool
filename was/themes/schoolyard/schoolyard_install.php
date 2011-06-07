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


/** /program/themes/schoolyard/schoolyard_install.php -- installer of the schoolyard theme
 *
 * This file contains the schoolyard theme installer.
 * The interface consists of these functions:
 *
 * <code>
 * schoolyard_install(&$messages,$theme_id)
 * schoolyard_upgrade(&$messages,$theme_id)
 * schoolyard_uninstall(&$messages,$theme_id)
 * schoolyard_demodata(&$messages,$theme_id,$config,$manifest)
 * </code>
 *
 * These functions can be called from the main installer and/or admin.php.
 *
 * Note
 * You cannot be sure about the environment from which these routines are called.
 * If the caller is the Install Wizard, you do not have all subroutines available.
 * However, it IS possible to manipulate the database via the db_*() routines and/or
 * the global $DB object. Therefore you have to keep the install routine extremly
 * simple. You also have no option to interact with the user; the install has to
 * be a silent install; you can only indicate success (TRUE) or failure (FALSE)
 * and maybe an error message in $messages[] but that's it. Good luck.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wastheme_schoolyard
 * @version $Id: schoolyard_install.php,v 1.1 2011/06/07 18:25:01 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** install the theme
 *
 * this routine performs the necessary actions to make this theme usable.
 * More specific, this routine adds a handfull of default values into the
 * themes_properties table. Once a theme is actually used in an area, these
 * defaults are copied from the themes_properties table to the
 * themes_areas_properties table for the selected area. The user can subsequently
 * edit these properties in the Area Manager.
 *
 * @param array &$messages collects the (error) messages
 * @param int $theme_id the key for this theme in the themes table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 * @uses schoolyard_get_properties()
 */
function schoolyard_install(&$messages,$theme_id) {
    $properties = schoolyard_get_properties();
    $retval = TRUE; // assume success
    $table = 'themes_properties';
    $sort_order = 0;
    foreach($properties as $name => $property) {
        $property['theme_id'] = $theme_id;
        $property['name'] = $name;
        $sort_order += 10;
        $property['sort_order'] = $sort_order;
        if (db_insert_into($table,$property) === FALSE) {
            $messages[] = __FUNCTION__.'(): '.db_errormessage();
            $retval = FALSE;
        }
    }
    return $retval;
} // schoolyard_install()


/** upgrade the theme
 *
 * this routine performs an upgrade to the installed theme.
 *
 * Note that the initial version of this 'schoolyard' theme does
 * not need any upgrade at all because there never was an earlier
 * version (well, duh).
 *
 * However, if there was to be a newer version of this theme, this
 * routine is THE place to bring the database up to date compared with
 * the existing version. For example, if an additional property 'foobar'
 * was to be added to the theme configuration, it could be added
 * to the themes_properties table with a suitable (default) value,
 * Any existing areas with this theme could have their configuration
 * updated with this additional foobar property, e.g.
 * INSERT INTO themes_properties: foobar
 * for all areas in themes_areas_properties with theme_id = $theme_id do
 *     INSERT INTO themes_areas_properties: foobar
 * etcetera,
 *
 * The current version of the theme could be determined by consulting
 * the databse (db_select_single_record(themes,'*','theme_id = $theme_id')
 * etcetera.
 *
 * Note that it is the responbabilty of the caller to correctly store
 * the data from the manifest in the themes table. You should not do
 * this here, in this routine.
 *
 * Currently this is a quick and dirty routine to
 *  - update changed sort_order in the existing settings, OR
 *  - add fields that were not available in the current settings
 *
 * In the future we could make it more sophisticated by
 * updating the themes_areas_properties too. Oh well. KISS
 *
 * @param array &$messages collects the (error) messages
 * @param int $theme_id the key for this theme in the themes table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 * @uses schoolyard_get_properties()
 * @todo maybe make this a little less quick and dirty?
 */
function schoolyard_upgrade(&$messages,$theme_id) {
    $retval = TRUE;
    $theme_id = intval($theme_id);
    $table = 'themes_properties';

    // 1 -- fetch current settings
    $where = array('theme_id' => $theme_id);
    $fields = array('name','sort_order');
    $order = 'sort_order';
    $keyfield = 'name';
    if (($settings = db_select_all_records($table,$fields,$where,$order,$keyfield)) === FALSE) {
        $messages[] = sprintf('%s(): cannot get settings from %s: %s',__FUNCTION__,$table,db_errormessage());
        $retval = FALSE;
        $settings = array(); // step 3 below expects an array
    }

    // 2 -- get new properties
    $properties = schoolyard_get_properties();

    // 3 -- selectively update settings or add as new
    $sort_order = 0;
    foreach($properties as $name => $property) {
        $sort_order += 10;
        if (isset($settings[$name])) { // existing property, maybe update sort order
            if ($sort_order != $settings[$name]['sort_order']) {
                $fields = array('sort_order' => $sort_order);
                $where = array('theme_id' => $theme_id, 'name' => $name);
                if (db_update($table,$fields,$where) === FALSE) {
                    $messages[] = __FUNCTION__.'(): '.db_errormessage();
                    $retval = FALSE;
                }
            } // else do_not_touch_existing_setting()
        } else {
            $property['theme_id'] = $theme_id;
            $property['name'] = $name;
            $property['sort_order'] = $sort_order;
            if (db_insert_into($table,$property) === FALSE) {
                $messages[] = __FUNCTION__.'(): '.db_errormessage();
                $retval = FALSE;
            }
        }
    }
    return $retval;
} // schoolyard_upgrade()


/** uninstall the theme
 *
 * this is a hook for future extensions of Website@School.
 * For now we simply return success. Any real code could look like this:
 *
 * DELETE FROM themes_areas_properties WHERE theme_id = $theme_id;
 * DELETE FROM themes_properties WHERE theme_id = $theme_id;
 * DELETE FROM themes WHERE theme_id = $theme_id;
 *
 * or whatever.
 *
 * @param array &$messages collects the (error) messages
 * @param int $theme_id the key for this theme in the themes table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function schoolyard_uninstall(&$messages,$theme_id) {
    return TRUE;
} // schoolyard_uninstall()


/** add demonstration data to the system
 *
 * this routine adds demonstration data to the freshly installed system
 *
 * Note
 * If the theme is installed via the Install Wizard, this routine is
 * called. However, if a theme is installed as an additional theme
 * after installation, the {$theme}_demodata() routine is never called.
 * This is because the only time you know that demodata is installed is
 * when the Install Wizard runs. If we're called from admin.php, the
 * webmaster may have already deleted existing (core) demodata so you
 * never can be sure what to expect. To put it another way: it is hard
 * to re-construct $config when we're NOT the Instal Wizard.
 *
 * The array $config contains the following information.
 *
 * <code>
 * $config['language_key']   => install language code (eg. 'en')
 * $config['dir']            => path to CMS Root Directory (eg. /home/httpd/htdocs)
 * $config['www']            => URL of CMS Root Directory (eg. http://exemplum.eu)
 * $config['progdir']        => path to program directory (eg. /home/httpd/htdocs/program)
 * $config['progwww']        => URL of program directory (eg. http://exemplum.eu/program)
 * $config['datadir']        => path to data directory (eg. /home/httpd/wasdata/a1b2c3d4e5f6)
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
 * </code>
 *
 * With this information, we can add a demonstration configuration for the public area,
 * which shows off the possibilities.
 *
 * @param array &$messages collects the (error) messages
 * @param int $theme_id the key for this theme in the themes table
 * @param array $config pertinent data for the new website + demodata foundation
 * @param array $manifest a copy from the manifest for this theme
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function schoolyard_demodata(&$messages,$theme_id,$config,$manifest) {
    global $DB;
    $retval = TRUE; // assume success

    // 0 -- get hold of our translations in $string[]
    $string = array();
    $language_key = $config['language_key'];
    $filename = dirname(__FILE__).'/languages/'.$language_key.'/schoolyard.php';
    if (!file_exists($filename)) {
        $filename = dirname(__FILE__).'/languages/en/schoolyard.php';
    }
    @include($filename);
    if (empty($string)) {
        $messages[] = 'Internal error: no translations in '.$filename;
        return FALSE;
    }

    // 1A -- prepare for setting up demo settings
    $quicktop_section_id = intval($config['demo_nodes']['quicktop']['node_id']);
    $quickbottom_section_id = intval($config['demo_nodes']['quickbottom']['node_id']);
    $www = $config['www'];
    $area_id = intval($config['demo_areas']['public']['area_id']);
    $theme_id = intval($theme_id);

    // 1B -- actually setup settings
    $settings = array(
        'quicktop_section_id'         => strval($quicktop_section_id),
        'quickbottom_section_id'      => strval($quickbottom_section_id),
        'show_breadcrumb_trail'       => '1',
        'logo_image'                  => 'program/themes/schoolyard/waslogo.png',
        'logo_width'                  => '284',
        'logo_height'                 => '71',
        'style_usage_static'          => '1',
        'stylesheet'                  => 'program/themes/schoolyard/style.css',
        'stylesheet_print'            => 'program/themes/schoolyard/print.css',
        'style_usage_area'            => '1',
        'style'                       => '',
        'style_usage_node'            => '1',
        );

    // 2A -- start with the default settings (copy from theme_properties)
    $sql = sprintf('INSERT INTO %s%s(area_id,theme_id,name,type,value,extra,sort_order,description) '.
                   'SELECT %d AS area_id,theme_id,name,type,value,extra,sort_order,description '.
                   'FROM %s%s '.
                   'WHERE theme_id = %d',
                    $DB->prefix,'themes_areas_properties',$area_id,
                    $DB->prefix,'themes_properties',$theme_id);
    if ($DB->exec($sql) === FALSE) {
        $messages[] = __FUNCTION__.'(): '.db_errormessage();
        $retval = FALSE;
    }
    // 2B -- update/overwrite the default settings with our own demo-data for area $area_id
    foreach ($settings as $name => $value) {
        $fields = array('value' => strval($value));
        $where = array('area_id' => $area_id, 'theme_id' => $theme_id, 'name' => strval($name));
        if (db_update('themes_areas_properties',$fields,$where) === FALSE) {
            $messages[] = __FUNCTION__.'(): '.db_errormessage();
            $retval = FALSE;
        }
    }
    return $retval;
} // schoolyard_demodata()


/** construct a list of default properties for this theme
 *
 * this routine is used when installing schoolyard for the first time
 * and also when upgrading the theme to the latest version
 *
 * @return array ready-to-use array with settings keyed by property name
 */
function schoolyard_get_properties() {
    $properties = array(
        #
        # Generic
        #
        'quicktop_section_id' => array(
            'type' => 'i',
            'value' => '0',
            'extra' => 'minvalue=0',
            'description' => 'indicates which section to use for the quicklinks at the top of the page'),
        'quickbottom_section_id' => array(
            'type' => 'i',
            'value' => '0',
            'extra' => 'minvalue=0',
            'description' => 'indicates which section to use for the quicklinks at the bottom of the page'),
        'show_breadcrumb_trail' => array(
            'type' => 'b',
            'value' => '1',
            'extra' => '',
            'description' => 'this enables/disables the display of the breadcrumb trail in the navigation'),

        #
        # Logo
        #
        'logo_image' => array(
            'type' => 's',
            'value' => 'program/themes/schoolyard/waslogo.png',
            'extra' => 'maxlength=255',
            'description' => 'the URL of the logo file or a path relative to the directory holding index.php'),
        'logo_width' => array(
            'type' => 'i',
            'value' => '284',
            'extra' => 'minvalue=0;maxvalue=2048',
            'description' => 'the width of the logo in pixels'),
        'logo_height' => array(
            'type' => 'i',
            'value' => '71',
            'extra' => 'minvalue=0;maxvalue=1536',
            'description' => 'the height of the logo in pixels'),
        #
        # Stylesheets and ad hoc style
        #
        'style_usage_static' => array(
            'type' => 'b',
            'value' => '1',
            'extra' => '',
            'description' => 'if TRUE this includes the static stylesheet'),
        'stylesheet' => array(
            'type' => 's',
            'value' => 'program/themes/schoolyard/style.css',
            'extra' => 'maxlength=255',
            'description' => 'the URL of the stylesheet or a path relative to the directory holding index.php'),
        'stylesheet_print' => array(
            'type' => 's',
            'value' => 'program/themes/schoolyard/print.css',
            'extra' => 'maxlength=255',
            'description' => 'the URL of the additional print stylesheet or a path relative to index.php'),
        'style_usage_area' => array(
            'type' => 'b',
            'value' => '1',
            'extra' => '',
            'description' => 'if TRUE this includes the additional style information'),
        'style' => array(
            'type' => 's',
            'value' => '',
            'extra' => 'maxlength=65535;rows=10;columns=70',
            'description' => 'additional style information that will be processed AFTER the static stylesheet file'),
        'style_usage_node' => array(
            'type' => 'b',
            'value' => '1',
            'extra' => '',
            'description' => 'if TRUE this allows for addition style information from individual sections/pages')
        );
    return $properties;
} // schoolyard_get_properties()

?>