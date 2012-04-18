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


/** /program/themes/rosalina/rosalina_install.php -- installer of the rosalina theme
 *
 * This file contains the rosalina theme installer.
 * The interface consists of these functions:
 *
 * <code>
 * rosalina_install(&$messages,$theme_id)
 * rosalina_upgrade(&$messages,$theme_id)
 * rosalina_uninstall(&$messages,$theme_id)
 * rosalina_demodata(&$messages,$theme_id,$config,$manifest)
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
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wastheme_rosalina
 * @version $Id: rosalina_install.php,v 1.2 2012/04/18 07:57:29 pfokker Exp $
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
 * @uses rosalina_get_properties()
 */
function rosalina_install(&$messages,$theme_id) {
    $properties = rosalina_get_properties();
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
} // rosalina_install()


/** upgrade the theme
 *
 * this routine performs an upgrade to the installed theme.
 *
 * Note that the initial version of this 'rosalina' theme does
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
 * @uses rosalina_get_properties()
 * @todo maybe make this a little less quick and dirty?
 */
function rosalina_upgrade(&$messages,$theme_id) {
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
    $properties = rosalina_get_properties();

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
} // rosalina_upgrade()


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
function rosalina_uninstall(&$messages,$theme_id) {
    return TRUE;
} // rosalina_uninstall()


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
function rosalina_demodata(&$messages,$theme_id,$config,$manifest) {
    global $DB;
    $retval = TRUE; // assume success

    // 0 -- get hold of our translations in $string[]
    $string = array();
    $language_key = $config['language_key'];
    $filename = dirname(__FILE__).'/languages/'.$language_key.'/rosalina.php';
    if (!file_exists($filename)) {
        $filename = dirname(__FILE__).'/languages/en/rosalina.php';
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

    // Hotspot 1 - the blue jigsaw piece leads to admin.php
    $hotspot_1 = implode(';',array('poly', '0,0,37,37,52,22,52,0',
                                   $www.'/admin.php', $string['demo_admin_php_title']));

    // Hotspot 2 - the gray jigsaw piece leads to login/logout (index.php)
    $hotspot_2 = implode(';',array('poly', '0,1,37,38,5,70,0,70',
                                   $www.'/index,php?login=1',  $string['demo_index_php_login_title'],
                                   $www.'/index.php?logout=1', $string['demo_index_php_logout_title']));

    // Hotspot 3 - the red jigsaw piece leads to the websiteatschool project website in a new window
    $hotspot_3 = implode(';',array('poly','6,70,52,24,52,70',
                                   'http://websiteatschool.eu',$string['demo_websiteatschool_eu_title'],
                                   '','',
                                   '_blank'));

    // Hotspot 4 - the red word 'Website' leads to the defaul area (via index.php without parameters)
    $hotspot_4 = implode(';',array('rect','53,17,157,58',
                                   $www.'/index.php',$string['demo_index_php_title']));

    // Hotspot 5 - the gray word '@' leads to the demodata contact page
    $hotspot_5 = implode(';',array('circle','173,36,15',
                                   sprintf('%s/index.php?node=%d',$www,
                                           $config['demo_nodes']['contact']['node_id']),
                                   $config['demo_nodes']['contact']['title']));

    // Hotspot 6 - the blue word 'School' leads explicitly to the public area (via index.php?area=NN)
    $hotspot_6 = implode(';',array('rect','189,9,279,58',
                                   sprintf('%s/index.php?area=%d',$www,$area_id),
                                   $config['demo_areas']['public']['title']));

    // 1B -- actually setup settings
    $settings = array(
        'quicktop_section_id'         => strval($quicktop_section_id),
        'quickbottom_section_id'      => strval($quickbottom_section_id),
        'show_breadcrumb_trail'       => '1',
        'logo_image'                  => 'program/themes/rosalina/waslogo.png',
        'logo_width'                  => '284',
        'logo_height'                 => '71',
        'logo_title'                  => $string['demo_logo_title'],
        'logo_alt'                    => $string['demo_logo_alt'],
        'logo_hotspots'               => '6',
        'logo_hotspot_1'              => $hotspot_1,
        'logo_hotspot_2'              => $hotspot_2,
        'logo_hotspot_3'              => $hotspot_3,
        'logo_hotspot_4'              => $hotspot_4,
        'logo_hotspot_5'              => $hotspot_5,
        'logo_hotspot_6'              => $hotspot_6,
        'logo_hotspot_7'              => '',
        'logo_hotspot_8'              => '',
        'style_usage_static'          => '1',
        'stylesheet'                  => 'program/themes/rosalina/style.css',
        'style_usage_area'            => '1',
        'style'                       => '',
        'style_usage_node'            => '1',
        'hvmenu_LowBgColor'           => '#FFCCCC',
        'hvmenu_LowSubBgColor'        => '#FF9999',
        'hvmenu_HighBgColor'          => '#CCCCFF',
        'hvmenu_HighSubBgColor'       => '#CCCCFF',
        'hvmenu_FontLowColor'         => '#000000',
        'hvmenu_FontSubLowColor'      => '#000000',
        'hvmenu_FontHighColor'        => '#000000',
        'hvmenu_FontSubHighColor'     => '#000000',
        'hvmenu_BorderColor'          => '#666666',
        'hvmenu_BorderSubColor'       => '#666666',
        'hvmenu_BorderWidth'          => '1',
        'hvmenu_BorderBtwnElmnts'     => '1',
        'hvmenu_FontFamily'           => 'Verdana,sans-serif',
        'hvmenu_FontSize'             => '9.5',
        'hvmenu_FontBold'             => '1',
        'hvmenu_FontItalic'           => '0',
        'hvmenu_MenuTextCentered'     => 'left',
        'hvmenu_MenuCentered'         => 'left',
        'hvmenu_MenuVerticalCentered' => 'top',
        'hvmenu_ChildOverlap'         => '0.0',
        'hvmenu_ChildVerticalOverlap' => '0.0',
        'hvmenu_StartTop'             => '0',
        'hvmenu_StartLeft'            => '0',
        'hvmenu_LeftPaddng'           => '8',
        'hvmenu_TopPaddng'            => '2',
        'hvmenu_FirstLineHorizontal'  => '1',
        'hvmenu_DissapearDelay'       => '1000',
        'hvmenu_MenuWrap'             => '1',
        'hvmenu_RightToLeft'          => '0',
        'hvmenu_UnfoldsOnClick'       => '0',
        'hvmenu_ShowArrow'            => '1',
        'hvmenu_KeepHilite'           => '1',
        'hvmenu_Arrws'                => 'tri.gif,5,10,tridown.gif,10,5,trileft.gif,5,10',
        'menu_top'                    => '120,10,300,20',
        'menu_sub'                    => '150,10,500,20'
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
} // rosalina_demodata()


/** construct a list of default properties for this theme
 *
 * this routine is used when installing rosalina for the first time
 * and also when upgrading the theme to the latest version
 *
 * Note that HV Menu has a lot of parameters dealing with frames and framesets.
 * Because no frames are used in the Rosalina theme, these parameters are not part
 * of the defaults. However, instead of deleting these settings from this installation
 * script I have commented them out in the array $properties below. The necessary
 * values for these 'missing' parameters (which must exist in the JavaScript configuration of
 * HV Menu) are set in the constructor. The effect is that the user never sees these
 * parameters and thus cannot set them to incorrect values for Rosalina.
 *
 * @return array ready-to-use array with settings keyed by property name
 */
function rosalina_get_properties() {
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
        # Logo and hotspots
        #
        'logo_image' => array(
            'type' => 's',
            'value' => 'program/themes/rosalina/waslogo.png',
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
        'logo_title' => array(
            'type' => 's',
            'value' => '',
            'extra' => 'maxlength=255',
            'description' => 'the title / tooltip to show when hovering over the logo image'),
        'logo_alt' => array(
            'type' => 's',
            'value' => '',
            'extra' => 'maxlength=255',
            'description' => 'alternative text for school logo'),
        'logo_hotspots' => array(
            'type' => 'i',
            'value' => '0',
            'extra' => 'minvalue=0;maxvalue=8',
            'description' => 'the number of hotspots in the logo, 0 for none'),
        'logo_hotspot_1' => array(
            'type' => 's',
            'value' => '',
            'extra' => 'maxlength=255',
            'description' => 'hotspot definition: shape;coords;href;text;alt_href;alt_text;target'),
        'logo_hotspot_2' => array(
            'type' => 's',
            'value' => '',
            'extra' => 'maxlength=255',
            'description' => 'hotspot definition: shape;coords;href;text;alt_href;alt_text;target'),
        'logo_hotspot_3' => array(
            'type' => 's',
            'value' => '',
            'extra' => 'maxlength=255',
            'description' => 'hotspot definition: shape;coords;href;text;alt_href;alt_text;target'),
        'logo_hotspot_4' => array(
            'type' => 's',
            'value' => '',
            'extra' => 'maxlength=255',
            'description' => 'hotspot definition: shape;coords;href;text;alt_href;alt_text;target'),
        'logo_hotspot_5' => array(
            'type' => 's',
            'value' => '',
            'extra' => 'maxlength=255',
            'description' => 'hotspot definition: shape;coords;href;text;alt_href;alt_text;target'),
        'logo_hotspot_6' => array(
            'type' => 's',
            'value' => '',
            'extra' => 'maxlength=255',
            'description' => 'hotspot definition: shape;coords;href;text;alt_href;alt_text;target'),
        'logo_hotspot_7' => array(
            'type' => 's',
            'value' => '',
            'extra' => 'maxlength=255',
            'description' => 'hotspot definition: shape;coords;href;text;alt_href;alt_text;target'),
        'logo_hotspot_8' => array(
            'type' => 's',
            'value' => '',
            'extra' => 'maxlength=255',
            'description' => 'hotspot definition: shape;coords;href;text;alt_href;alt_text;target'),
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
            'value' => 'program/themes/rosalina/style.css',
            'extra' => 'maxlength=255',
            'description' => 'the URL of the stylesheet or a path relative to the directory holding index.php'),
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
            'description' => 'if TRUE this allows for addition style information from individual sections/pages'),
        #
        # HV Menu
        #
        'hvmenu_LowBgColor' => array(
            'type' => 's',
            'value' => '#FFCCCC',
            'extra' => 'maxlength=20',
            'description' => 'Background color when mouse is not over'),
        'hvmenu_LowSubBgColor' => array(
            'type' => 's',
            'value' => '#FF9999',
            'extra' => 'maxlength=20',
            'description' => 'Background color when mouse is not over on subs'),
        'hvmenu_HighBgColor' => array(
            'type' => 's',
            'value' => '#CCCCFF',
            'extra' => 'maxlength=20',
            'description' => 'Background color when mouse is over'),
        'hvmenu_HighSubBgColor' => array(
            'type' => 's',
            'value' => '#CCCCFF',
            'extra' => 'maxlength=20',
            'description' => 'Background color when mouse is over on subs'),
        'hvmenu_FontLowColor' => array(
            'type' => 's',
            'value' => '#000000',
            'extra' => 'maxlength=20',
            'description' => 'Font color when mouse is not over'),
        'hvmenu_FontSubLowColor' => array(
            'type' => 's',
            'value' => '#000000',
            'extra' => 'maxlength=20',
            'description' => 'Font color subs when mouse is not over'),
        'hvmenu_FontHighColor' => array(
            'type' => 's',
            'value' => '#000000',
            'extra' => 'maxlength=20',
            'description' => 'Font color when mouse is over'),
        'hvmenu_FontSubHighColor' => array(
            'type' => 's',
            'value' => '#000000',
            'extra' => 'maxlength=20',
            'description' => 'Font color subs when mouse is over'),
        'hvmenu_BorderColor' => array(
            'type' => 's',
            'value' => '#666666',
            'extra' => 'maxlength=20',
            'description' => 'Border color'),
        'hvmenu_BorderSubColor' => array(
            'type' => 's',
            'value' => '#666666',
            'extra' => 'maxlength=20',
            'description' => 'Border color for subs'),
        'hvmenu_BorderWidth' => array(
            'type' => 'i',
            'value' => '1',
            'extra' => 'minvalue=0;maxvalue=255',
            'description' => 'Border width'),
        'hvmenu_BorderBtwnElmnts' => array(
            'type' => 'b',
            'value' => '1',
            'extra' => '',
            'description' => 'Border between elements 1 or 0'),
        'hvmenu_FontFamily' => array(
            'type' => 's',
            'value' => 'Verdana,sans-serif',
            'extra' => 'maxlength=255',
            'description' => 'Font family menu items'),
        'hvmenu_FontSize' => array(
            'type' => 'f',
            'value' => '9.5',
            'extra' => 'minvalue=1.0;maxvalue=144.0;decimals=1',
            'description' => 'Font size menu items'),
        'hvmenu_FontBold' => array(
            'type' => 'b',
            'value' => '1',
            'extra' => '',
            'description' => 'Bold menu items 1 or 0'),
        'hvmenu_FontItalic' => array(
            'type' => 'b',
            'value' => '0',
            'extra' => '',
            'description' => 'Italic menu items 1 or 0'),
        'hvmenu_MenuTextCentered' => array(
            'type' => 'l',
            'value' => 'left',
            'extra' => 'options=left,center,right',
            'description' => "Item text position 'left', 'center' or 'right'"),
        'hvmenu_MenuCentered' => array(
            'type' => 'l',
            'value' => 'left',
            'extra' => 'options=left,center,right',
            'description' => "Menu horizontal position 'left', 'center' or 'right'"),
        'hvmenu_MenuVerticalCentered' => array(
            'type' => 'l',
            'value' => 'top',
            'extra' => 'options=top,middle,bottom,static',
            'description' => "Menu vertical position 'top', 'middle','bottom' or static"),
        'hvmenu_ChildOverlap' => array(
            'type' => 'f',
            'value' => '0.0',
            'extra' => 'minvalue=-1.0;maxvalue=1.0;decimals=2',
            'description' => 'horizontal overlap child/ parent'),
        'hvmenu_ChildVerticalOverlap' => array(
            'type' => 'f',
            'value' => '0.0',
            'extra' => 'minvalue=-1.0;maxvalue=1.0;decimals=2',
            'description' => 'vertical overlap child/ parent'),
        'hvmenu_StartTop' => array(
            'type' => 'i',
            'value' => '0',
            'extra' => 'minvalue=0;maxvalue=1536',
            'description' => 'Menu offset x coordinate'),
        'hvmenu_StartLeft' => array(
            'type' => 'i',
            'value' => '0',
            'extra' => 'minvalue=0;maxvalue=2048',
            'description' => 'Menu offset y coordinate'),
//        'hvmenu_VerCorrect' => array(
//            'type' => 'i',
//            'value' => '0',
//            'extra' => 'minvalue=0;maxvalue=1536;viewonly=1',
//            'description' => 'Multiple frames y correction'),
//        'hvmenu_HorCorrect' => array(
//            'type' => 'i',
//            'value' => '0',
//            'extra' => 'minvalue=0;maxvalue=2048;viewonly=1',
//            'description' => 'Multiple frames x correction'),
        'hvmenu_LeftPaddng' => array(
            'type' => 'i',
            'value' => '8',
            'extra' => 'minvalue=0;maxvalue=2048',
            'description' => 'Left padding'),
        'hvmenu_TopPaddng' => array(
            'type' => 'i',
            'value' => '2',
            'extra' => 'minvalue=0;maxvalue=1536',
            'description' => 'Top padding'),
        'hvmenu_FirstLineHorizontal' => array(
            'type' => 'b',
            'value' => '1',
            'extra' => '',
            'description' => 'SET TO 1 FOR HORIZONTAL MENU, 0 FOR VERTICAL'),
//        'hvmenu_MenuFramesVertical' => array(
//            'type' => 'b',
//            'value' => '0',
//            'extra' => 'viewonly=1',
//            'description' => 'Frames in cols or rows 1 or 0'),
        'hvmenu_DissapearDelay' => array(
            'type' => 'i',
            'value' => '1000',
            'extra' => 'minvalue=0;maxvalue=60000',
            'description' => 'delay before menu folds in'),
//        'hvmenu_TakeOverBgColor' => array(
//            'type' => 'b',
//            'value' => '1',
//            'extra' => 'viewonly=1',
//            'description' => 'Menu frame takes over background color subitem frame'),
//        'hvmenu_FirstLineFrame' => array(
//            'type' => 's',
//            'value' => 'self',
//            'extra' => 'maxlength=20;viewonly=1',
//            'description' => 'Frame where first level appears'),
//        'hvmenu_SecLineFrame' => array(
//            'type' => 's',
//            'value' => 'self',
//            'extra' => 'maxlength=20;viewonly=1',
//            'description' => 'Frame where sub levels appear'),
//        'hvmenu_DocTargetFrame' => array(
//            'type' => 's',
//            'value' => 'self',
//            'extra' => 'maxlength=20;viewonly=1',
//            'description' => 'Frame where target documents appear'),
//        'hvmenu_TargetLoc' => array(
//            'type' => 's',
//            'value' => 'hvmenu',
//            'extra' => 'maxlength=20;viewonly=1',
//            'description' => 'span id for relative positioning'),
//        'hvmenu_HideTop' => array(
//            'type' => 'b',
//            'value' => '0',
//            'extra' => 'viewonly=1',
//            'description' => 'Hide first level when loading new document 1 or 0'),
        'hvmenu_MenuWrap' => array(
            'type' => 'b',
            'value' => '1',
            'extra' => '',
            'description' => 'enables/ disables menu wrap 1 or 0'),
        'hvmenu_RightToLeft' => array(
            'type' => 'b',
            'value' => '0',
            'extra' => '',
            'description' => 'enables/ disables right to left unfold 1 or 0'),
        'hvmenu_UnfoldsOnClick' => array(
            'type' => 'b',
            'value' => '0',
            'extra' => '',
            'description' => 'Level 1 unfolds onclick/ onmouseover'),
//        'hvmenu_WebMasterCheck' => array(
//            'type' => 'b',
//            'value' => '0',
//            'extra' => 'viewonly=1',
//            'description' => 'menu tree checking on or off 1 or 0'),
        'hvmenu_ShowArrow' => array(
            'type' => 'b',
            'value' => '1',
            'extra' => '',
            'description' => 'Uses arrow gifs when 1'),
        'hvmenu_KeepHilite' => array(
            'type' => 'b',
            'value' => '1',
            'extra' => '',
            'description' => 'Keep selected path highligthed'),
        'hvmenu_Arrws' => array(
            'type' => 's',
            'value' => 'tri.gif,5,10,tridown.gif,10,5,trileft.gif,5,10',
            'extra' => '',
            'description' => 'Three arrow image files: filename,width,height'),
        'menu_top' => array(
            'type' => 's',
            'value' => '80,8,300,20',
            'extra' => '',
            'description' => 'top menu dimensions: width_min,width_char,width_max,height (default: 120,8,300,20)'),
        'menu_sub' => array(
            'type' => 's',
            'value' => '150,8,500,20',
            'extra' => '',
            'description' => 'sub menu dimensions: width_min,width_char,width_max,height (default: 150,8,500,20)')
        );
    return $properties;
} // rosalina_get_properties()

?>