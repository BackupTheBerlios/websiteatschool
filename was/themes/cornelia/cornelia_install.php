<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker <peter@berestijn.nl>
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

/** /program/themes/cornelia/cornelia_install.php -- installer for the cornelia theme
 *
 * This file contains the cornelia theme installer.
 * The interface consists of these functions:
 *
 * <code>
 * cornelia_install(&$messages,$theme_id)
 * cornelia_upgrade(&$messages,$theme_id)
 * cornelia_uninstall(&$messages,$theme_id)
 * cornelia_demodata(&$messages,$theme_id,$config,$manifest)
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
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wastheme_cornelia
 * @version $Id: cornelia_install.php,v 1.2 2013/06/11 11:25:41 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** install the theme
 *
 * this routine performs the necessary actions to make this theme usable.
 * More specific, this routine adds a handful of default values into the
 * themes_properties table. Once a theme is actually used in an area, these
 * defaults are copied from the themes_properties table to the
 * themes_areas_properties table for the selected area. The user can subsequently
 * edit these properties in the Area Manager.
 *
 * @param array &$messages collects the (error) messages
 * @param int $theme_id the key for this theme in the themes table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function cornelia_install(&$messages,$theme_id) {
    $now = strftime('%Y-%m-%d %T');
    $properties = array(
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'quicktop_section_id',
                'type' => 'i',
                'value' => '0',
                'sort_order' => 10,
                'extra' => 'minvalue=0',
                'description' => 'indicates which section to use for the quicklinks at the top of the page'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'quickbottom_section_id',
                'type' => 'i',
                'value' => '0',
                'sort_order' => 20,
                'extra' => 'minvalue=0',
                'description' => 'indicates which section to use for the quicklinks at the bottom of the page'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'logo_image',
                'type' => 's',
                'value' => 'program/graphics/waslogo-284x71.png',
                'sort_order' => 30,
                'extra' => 'maxlength=255',
                'description' => 'the URL of the logo file or a path relative to the directory holding index.php'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'logo_width',
                'type' => 'i',
                'value' => '284',
                'sort_order' => 40,
                'extra' => 'minvalue=0;maxvalue=2048',
                'description' => 'the width of the logo in pixels'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'logo_height',
                'type' => 'i',
                'value' => '71',
                'sort_order' => 50,
                'extra' => 'minvalue=0;maxvalue=1536',
                'description' => 'the height of the logo in pixels'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'show_breadcrumb_trail',
                'type' => 'b',
                'value' => '1',
                'sort_order' => 60,
                'description' => 'this enables/disables the display of the breadcrumb trail in the navigation'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'style_usage_static',
                'type' => 'b',
                'value' => '1',
                'sort_order' => 70,
                'extra' => '',
                'description' => 'if TRUE this includes the static stylesheet'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'stylesheet',
                'type' => 's',
                'value' => 'program/themes/cornelia/style.css',
                'sort_order' => 80,
                'extra' => 'maxlength=255',
                'description' => 'the URL of the stylesheet or a path relative to the directory holding index.php'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'stylesheet2',
                'type' => 's',
                'value' => 'program/themes/cornelia/style2.css',
                'sort_order' => 90,
                'extra' => 'maxlength=255',
                'description' => 'the URL or (relative) path of an additional stylesheet for 2 columns'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'stylesheet_print',
                'type' => 's',
                'value' => 'program/themes/cornelia/print.css',
                'sort_order' => 100,
                'extra' => 'maxlength=255',
                'description' => 'the URL or (relative) path of an additional stylesheet for print'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'style_usage_area',
                'type' => 'b',
                'value' => '1',
                'sort_order' => 110,
                'extra' => '',
                'description' => 'if TRUE this includes the additional style information'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'style',
                'type' => 's',
                'value' => "",
                'sort_order' => 120,
                'extra' => 'maxlength=65535;rows=10;columns=70',
                'description' => 'additional style information that will be processed AFTER the static stylesheet file'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'style_usage_node',
                'type' => 'b',
                'value' => '1',
                'sort_order' => 130,
                'extra' => '',
                'description' => 'if TRUE this allows for addition style information from individual sections/pages'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'header_text',
                'type' => 's',
                'value' => '',
                'sort_order' => 140,
                'extra' => 'maxlength=255',
                'description' => 'additional plain text added to the page header'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'header_banners_directory',
                'type' => 's',
                'value' => 'program/themes/cornelia/banners',
                'sort_order' => 150,
                'extra' => 'maxlength=255',
                'description' => 'directory with panoramic header photos 980px x 170px'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'header_banners_interval',
                'type' => 'i',
                'value' => '3',
                'sort_order' => 160,
                'extra' => 'minvalue=0;maxvalue=60',
                'description' => 'time (minutes) to show a header image (0=no image at all)'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'left_top_html',
                'type' => 's',
                'value' => "",
                'sort_order' => 170,
                'extra' => 'maxlength=65535;rows=10;columns=70',
                'description' => 'additional free form html above the menu'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'left_bottom_html',
                'type' => 's',
                'value' => "",
                'sort_order' => 180,
                'extra' => 'maxlength=65535;rows=10;columns=70',
                'description' => 'additional free form html below the menu'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'sidebar_nodelist',
                'type' => 's',
                'value' => '',
                'sort_order' => 190,
                'extra' => 'maxlength=255',
                'description' => 'comma-delimited list of nodes providing content of sidebar'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'right_top_html',
                'type' => 's',
                'value' => "",
                'sort_order' => 200,
                'extra' => 'maxlength=65535;rows=10;columns=70',
                'description' => 'additional free form html at sidebar top'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'right_bottom_html',
                'type' => 's',
                'value' => "",
                'sort_order' => 210,
                'extra' => 'maxlength=65535;rows=10;columns=70',
                'description' => 'additional free form html at sidebar bottom'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'footer_text',
                'type' => 's',
                'value' => '',
                'sort_order' => 220,
                'extra' => 'maxlength=255',
                'description' => 'additional plain text added to the page footer'
                )
            )
        );
    $retval = TRUE; // assume success
    foreach($properties as $property) {
        if (db_insert_into($property['table'],$property['fields']) === FALSE) {
            $messages[] = __FUNCTION__.'(): '.db_errormessage();
            $retval = FALSE;
        }
    }
    return $retval;
} // cornelia_install()


/** upgrade the theme
 *
 * this routine performs an upgrade to the installed theme.
 * Note that this minimalistic 'cornelia' theme does not need any
 * upgrade at all because there never was an earlier version.
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
 * Note that it is the responsability of the caller to correctly store
 * the data from the manifest in the themes table. You don't have to
 * do this here, in this routine.
 *
 * @param array &$messages collects the (error) messages
 * @param int $theme_id the key for this theme in the themes table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function cornelia_upgrade(&$messages,$theme_id) {
    $retval = TRUE;
    // $messages[] = sprintf('%s(): %s upgrading theme_id '%d',__FUNCTION__,($retval) ? 'success' : 'failure',$theme_id);
    return $retval;
} // cornelia_upgrade()


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
function cornelia_uninstall(&$messages,$theme_id) {
    return TRUE;
} // cornelia_uninstall()


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
 * $config['title']          => the name of the site (as entered by Wilhelmina Bladergroen)
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
 * With this information, we can add a demonstration configuration for the public area,
 * which shows off the possibilities.
 *
 * @param array &$messages collects the (error) messages
 * @param int $theme_id the key for this theme in the themes table
 * @param array $config pertinent data for the new website + demodata foundation
 * @param array $manifest a copy from the manifest for this theme
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function cornelia_demodata(&$messages,$theme_id,$config,$manifest) {
    global $DB;
    $retval = TRUE; // assume success

    // 1A -- prepare for setting up demo settings
    $quicktop_section_id         = intval($config['demo_nodes']['quicktop']['node_id']);
    $quickbottom_section_id      = intval($config['demo_nodes']['quickbottom']['node_id']);
    $schoolterms2_id             = intval($config['demo_nodes']['schoolterms2']['node_id']);
    $latestnews_id               = intval($config['demo_nodes']['latestnews']['node_id']);
    $progwww                     = $config['progwww'];
    $slogan                      = $config['demo_string']['welcome_title'];
    $area_id                     = intval($config['demo_areas']['public']['area_id']);
    $theme_id                    = intval($theme_id);
    $theme_name                  = $manifest['name'];
    $aboutus                     = $config['demo_string']['aboutus_content'];
    $email                       = $config['replyto'];
    $lorem                       = strtr('{LOREM}',$config['demo_replace']);
    $ipsum                       = strtr('{IPSUM}',$config['demo_replace']);
    $sidebar_nodelist            = sprintf('%d,0,%d,0,-',$latestnews_id,$schoolterms2_id);
    $style                       = "#leftmargin { min-height: 300px; }\n".
                                   "#rightmargin { min-height: 300px; }\n";

    // 1B -- actually construct settings
    $settings = array(
        'quicktop_section_id'    => strval($quicktop_section_id),
        'quickbottom_section_id' => strval($quickbottom_section_id),
        'header_text'            => sprintf('<span style="background-color: #FFFF00;">%s</span>',$slogan),
        'left_top_html'          => sprintf("<img src=\"%s/themes/%s/origanum.jpg\" width=\"120\" height=\"90\" alt=\"\">\n",
                                            $progwww, $theme_name),
        'left_bottom_html'       => sprintf("<div style=\"margin-bottom: 30px;\">\n%s\n</div>\n",$aboutus),
        'right_top_html'         => $lorem,
        'sidebar_nodelist'       => $sidebar_nodelist,
        'right_bottom_html'      => $ipsum,
        'footer_text'            => sprintf('<b><a href="mailto:%s">%s</a></b>',$email,$email),
        'style'                  => $style
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
} // cornelia_demodata()

?>