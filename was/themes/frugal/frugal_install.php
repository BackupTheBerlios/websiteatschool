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

/** /program/themes/frugal/frugal_install.php -- installer of the frugal theme
 *
 * This file contains the frugal theme installer.
 * The interface consists of these functions:
 *
 * <code>
 * frugal_install(&$messages,$theme_id)
 * frugal_upgrade(&$messages,$theme_id)
 * frugal_uninstall(&$messages,$theme_id)
 * frugal_demodata(&$messages,$theme_id,$config,$manifest)
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
 * @package wastheme_frugal
 * @version $Id: frugal_install.php,v 1.5 2012/04/18 07:57:28 pfokker Exp $
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
 */
function frugal_install(&$messages,$theme_id) {
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
                'value' => 'program/styles/base.css',
                'sort_order' => 80,
                'extra' => 'maxlength=255',
                'description' => 'the URL of the stylesheet or a path relative to the directory holding index.php'
                )
            ),
        array(
            'table' => 'themes_properties',
            'fields' => array(
                'theme_id' => $theme_id,
                'name' => 'style_usage_area',
                'type' => 'b',
                'value' => '1',
                'sort_order' => 90,
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
                'value' => "/* Demo of Bazaar Style Style sheets */\n".
                           "#content h1 {\n".
                           "  background-color: #FF33FF;\n".
                           "}\n",
                'sort_order' => 100,
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
                'sort_order' => 110,
                'extra' => '',
                'description' => 'if TRUE this allows for addition style information from individual sections/pages'
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
} // frugal_install()


/** upgrade the theme
 *
 * this routine performs an upgrade to the installed theme.
 * Note that this minimalistic 'frugal' theme does not need any
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
 * Note that it is the responbabilty of the caller to correctly store
 * the data from the manifest in the themes table. You don't have to
 * do this here, in this routine.
 *
 * @param array &$messages collects the (error) messages
 * @param int $theme_id the key for this theme in the themes table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function frugal_upgrade(&$messages,$theme_id) {
    $retval = TRUE;
    // $messages[] = sprintf('%s(): %s upgrading theme_id '%d',__FUNCTION__,($retval) ? 'success' : 'failure',$theme_id);
    return $retval;
} // frugal_upgrade()


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
function frugal_uninstall(&$messages,$theme_id) {
    return TRUE;
} // frugal_uninstall()


/** add demonstration data to the system
 *
 * this routine is a no-op because all frugal demodata is already
 * created in the main demodata-routine in /program/install/demodata.php.
 * This routine is retained here as an example and also because a routine
 * by this name should exist (even if it does nothing).
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
 * Fortunately 'frugal' does not need demodata.
 *
 * @param array &$messages collects the (error) messages
 * @param int $theme_id the key for this theme in the themes table
 * @param array $configuration pertinent data for the new website + demodata foundation
 * @param array $manifest a copuy from the manifest for this theme
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function frugal_demodata(&$messages,$theme_id,$config,$manifest) {
    return TRUE;
} // frugal_demodata()

?>