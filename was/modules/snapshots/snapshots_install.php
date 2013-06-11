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

/** /program/modules/snapshots/snapshots_install.php - installer of the snapshots module
 *
 * This file contains the snapshots module installer.
 * The interface consists of these functions:
 *
 * <code>
 * snapshots_install(&$messages,$module_id)
 * snapshots_upgrade(&$messages,$module_id)
 * snapshots_uninstall(&$messages,$module_id)
 * snapshots_demodata(&$messages,$module_id,$config,$manifest)
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
 * @package wasmod_snapshots
 * @version $Id: snapshots_install.php,v 1.2 2013/06/11 11:25:35 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** install the module
 *
 * this routine installs the module. For this module
 * there is nothing to install, so we simply return success.
 * The appropriate table is already created based on the tabledefs);
 * see install/snapshots_tabledefs.php.
 *
 * Note that the record for this module is already created in the
 * modules table; the pkey is $module_id.
 *
 * @param array &$messages collects the (error) messages
 * @param int $module_id the key for this module in the modules table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function snapshots_install(&$messages,$module_id) {
    return TRUE;
} // snapshots_install()


/** upgrade the module
 *
 * this routine performs an upgrade to the installed module.
 * Note that this minimalistic 'snapshots' module does not need any
 * upgrade at all because there never was an earlier version.
 *
 * However, if there was to be a newer version of this module, this
 * routine is THE place to bring the database up to date compared with
 * the existing version. For example, if an additional field 'snapshots_extension'
 * was to be added to the snapshots-table, it could be added using a
 * suitable (default) value, e.g. an empty string or whatever
 *
 * Any existing snapshots could then be updated here to fill the new
 * field with data, e.g.
 *
 * UPDATE snapshots SET snapshots_extension = '';
 *
 * etcetera. For now this routine is a nop.
 *
 * @param array &$messages collects the (error) messages
 * @param int $module_id the key for this module in the modules table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function snapshots_upgrade(&$messages,$module_id) {
    return TRUE;
} // snapshots_upgrade()


/** uninstall the module
 *
 * this is a hook for future extensions of Website@School.
 * For now we simply return success. Any real code could look like this:
 * DELETE FROM snapshots;
 * to delete all existing data (but who would want that). For
 * now this is simpky a nop.
 *
 * Note that bluntly deleting from the snapshots table might lead to
 * nodes without a valid module. The better way to do it would be
 * something like this:
 * <code>
 * SELECT count(node_id) AS number_of_nodes FROM snapshots;
 * 
 * if ($number_of_nodes > 0) then
 *     $messages[] = 'There are still $number_of_nodes nodes with a snapshots';
 *     return FALSE;
 * </code>
 * which in fact means that the table should already be empty before
 * we can empty it. Oh well...
 * 
 * @param array &$messages collects the (error) messages
 * @param int $module_id the key for this module in the modules table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function snapshots_uninstall(&$messages,$module_id) {
    return TRUE;
} // snapshots_uninstall()


/** add demonstration data to the system
 *
 * this routine adds to the existing set of demonstration data as specified
 * in $config. Here we add a few nodes as follows:
 *
 * 'snapshots0': a section containing one page connected to the snapshots_module
 * 'snapshots1': a page in section 'snapshots0' acting as an example set of snapshots
 *
 * The section 'snapshots0' is added at the bottom of the demo-section 'news'
 * Maybe not the best place, but at least we don't add yet another top level
 * menu item.
 *
 * Note
 * If the module is installed via the Install Wizard, this routine is
 * called. However, if a module is installed as an additional module
 * after installation, the {$module}_demodata() routine is never called.
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
 * $config['demo_string']    => array with demo strings from /program/install/languages/LL/demodata.php
 * $config['demo_replace']   => array with search/replace pairs to 'jazz up' the demo strings
 * </code>
 *
 * With this information, we can add a demonstration configuration for the public area,
 * which shows off the possibilities. Note that we add our own additions to the array
 * $config so other modules and themes can determine the correct status quo w.r.t. the
 * demodata nodes etc.
 *
 * @param array &$messages collects the (error) messages
 * @param int $module_id the key for this module in the modules table
 * @param array $configuration pertinent data for the new website + demodata foundation
 * @param array $manifest a copy of the manifest for this module
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function snapshots_demodata(&$messages,$module_id,$config,$manifest) {
    global $DB;
    $retval = TRUE; // assume success

    // 0 -- get hold of the module-specific translations in $string[]
    $string = array();
    $language_key = $config['language_key'];
    $filename = dirname(__FILE__).'/languages/'.$language_key.'/snapshots.php';
    if (!file_exists($filename)) {
        $filename = dirname(__FILE__).'/languages/en/snapshots.php';
    }
    @include($filename);
    if (empty($string)) {
        $messages[] = 'Internal error: no translations in '.$filename;
        return FALSE;
    }

    // 1A -- prepare for addition of a few nodes
    $sort_order = 0;
    $parent_id = $config['demo_nodes']['news']['node_id'];
    foreach($config['demo_nodes'] as $node => $fields) {
        if (($fields['parent_id'] == $parent_id) && ($fields['node_id'] != $parent_id)) {
            $sort_order = max($sort_order,$fields['sort_order']);
        }
    }
    $sort_order += 10; // place the snapshots-section at the end of the parent section
    $nodes = array(
        'snapshots0' => array(
            'parent_id'  => $parent_id,
            'is_page'    => FALSE,
            'title'      => strtr($string['snapshots0_title'],$config['demo_replace']),
            'link_text'  => strtr($string['snapshots0_link_text'],$config['demo_replace']),
            'sort_order' => $sort_order
            ),
        'snapshots1' => array(
            'parent_id'  => 0, // sentinel, will be resolved in loop below
            'is_page'    => TRUE,
            'is_default' => TRUE,
            'title'      => strtr($string['snapshots1_title'],$config['demo_replace']),
            'link_text'  => strtr($string['snapshots1_link_text'],$config['demo_replace']),
            'sort_order' => 10,
            'module_id'  => $module_id,
            'style'      => "div.thumbnail_image a:hover img { border: 5px solid #00FF00; }\n"
            )
        );
    // 1B -- actually add the necessary nodes
    $now = strftime('%Y-%m-%d %T');
    $user_id   = $config['user_id'];
    $area_id   = $config['demo_areas']['public']['area_id'];
    foreach($nodes as $node => $fields) {
        $fields['area_id']  = $area_id;
        $fields['ctime']    = $now;
        $fields['mtime']    = $now;
        $fields['atime']    = $now;
        $fields['owner_id'] = $user_id;
        if ($fields['parent_id'] == 0) {
            $fields['parent_id'] = $config['demo_nodes']['snapshots0']['node_id']; // plug in real node_id of 'our' section
        }
        if (($node_id = db_insert_into_and_get_id('nodes',$fields,'node_id')) === FALSE) {
            $messages[] = $config['demo_string']['error'].db_errormessage();
            $retval = FALSE;
        }
        $node_id = intval($node_id);
        $fields['node_id'] = $node_id;
        $config['demo_nodes'][$node] = $fields;
    }

    // 2 -- Simulate a series of snapshots in the exemplum area data storage

    // 2A -- copy from module demodata directory to exemplum path
    $pictures = array(
        "allium.jpg", "calendula.jpg", "cynara.jpg", "lagos.jpg",
        "lavandula.jpg", "mentha.jpg", "nepeta.jpg", "ocimum.jpg",
        "origanum.jpg", "petroselinum.jpg", "salvia.jpg", "thymus.jpg"
        );
    $thumb_prefix = 'zz_thumb_';
    $path_snapshots = '/areas/'.$config['demo_areas']['public']['path'].'/snapshots'; // relative to $datadir
    $fullpath_source = dirname(__FILE__).'/install/demodata';
    $fullpath_target = $config['datadir'].$path_snapshots;
    if (@mkdir($fullpath_target,0700) === FALSE) {
        $messages[] = $config['demo_string']['error']."mkdir('{$fullpath_target}')";
        $retval = FALSE;
    } else {
        @touch($fullpath_target.'/index.html'); // try to "protect" directory
        foreach($pictures as $picture) {
            $filenames = array($picture, $thumb_prefix.$picture);
            foreach ($filenames as $filename) {
                if (!@copy($fullpath_source.'/'.$filename, $fullpath_target.'/'.$filename)) {
                    $messages[] = $config['demo_string']['error']."copy('{$path_snapshots}/{$filename}')";
                    $retval = FALSE;
                }
            }
        }
    }
    $fields = array(
            'node_id'        => $config['demo_nodes']['snapshots1']['node_id'],
            'header'         => strtr($string['snapshots1_header'],$config['demo_replace']),
            'introduction'   => strtr($string['snapshots1_introduction'],$config['demo_replace']),
            'snapshots_path' => $path_snapshots, 
            'variant'        => 1,
            'dimension'      => 512,
            'ctime'          => $now,
            'cuser_id'       => $user_id,
            'mtime'          => $now,
            'muser_id'       => $user_id);
    if (db_insert_into('snapshots',$fields) === FALSE) {
        $messages[] = $config['demo_string']['error'].db_errormessage();
        $retval = FALSE;
    }
    return $retval;
} // snapshots_demodata()

?>