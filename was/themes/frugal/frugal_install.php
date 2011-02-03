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
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wastheme_frugal
 * @version $Id: frugal_install.php,v 1.2 2011/02/03 14:04:01 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** install the theme
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
 * @param array &$messages collects the (error) messages
 * @param int $theme_id the key for this theme in the themes table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function frugal_upgrade(&$messages,$theme_id) {
    $messages[] = "STUB: ".__FUNCTION__."()";
    return TRUE;
} // frugal_upgrade()


/** uninstall the theme
 *
 * @param array &$messages collects the (error) messages
 * @param int $theme_id the key for this theme in the themes table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function frugal_uninstall(&$messages,$theme_id) {
    $messages[] = "STUB: ".__FUNCTION__."()";
    return TRUE;
} // frugal_uninstall()


/** add demonstration data to the system
 *
 * this routine is a no-op because all frugal demodata is already
 * created in the main demodata-routine in /program/install/demodata.php.
 * This routine is retained here as an example alias placeholder.
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