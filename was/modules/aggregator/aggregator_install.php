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

/** /program/modules/aggregator/aggregator_install.php - installer of the aggregator module
 *
 * This file contains the aggregator module installer.
 * The interface consists of these functions:
 *
 * <code>
 * aggregator_install(&$messages,$module_id)
 * aggregator_upgrade(&$messages,$module_id)
 * aggregator_uninstall(&$messages,$module_id)
 * aggregator_demodata(&$messages,$module_id,$config,$manifest)
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
 * @package wasmod_aggregator
 * @version $Id: aggregator_install.php,v 1.2 2013/06/11 11:25:17 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** install the module
 *
 * this routine installs the module. For this module
 * there is nothing to install, so we simply return success.
 * The appropriate table is already created based on the tabledefs);
 * see install/aggregator_tabledefs.php.
 *
 * Note that the record for this module is already created in the
 * modules table; the pkey is $module_id.
 *
 * @param array &$messages collects the (error) messages
 * @param int $module_id the key for this module in the modules table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function aggregator_install(&$messages,$module_id) {
    return TRUE;
} // aggregator_install()


/** upgrade the module
 *
 * this routine performs an upgrade to the installed module.
 * Note that this 'aggregator' module currently does not need any
 * upgrade at all because there never was an earlier version.
 *
 * However, if there was to be a newer version of this module, this
 * routine is THE place to bring the database up to date compared with
 * the existing version. For example, if an additional field 'aggregator_extension'
 * was to be added to the aggregator-table, it could be added using a
 * suitable (default) value, e.g. an empty string or whatever
 *
 * Any existing aggregator could then be updated here to fill the new
 * field with data, e.g.
 *
 * UPDATE aggregator SET aggregator_extension = '';
 *
 * etcetera. For now this routine is a nop.
 *
 * @param array &$messages collects the (error) messages
 * @param int $module_id the key for this module in the modules table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function aggregator_upgrade(&$messages,$module_id) {
    return TRUE;
} // aggregator_upgrade()


/** uninstall the module
 *
 * this is a hook for future extensions of Website@School.
 * For now we simply return success. Any real code could look like this:
 * DELETE FROM aggregator;
 * to delete all existing data (but who would want that). For
 * now this is simply a nop.
 *
 * Note that bluntly deleting from the aggregator table might lead to
 * nodes without a valid module. The better way to do it would be
 * something like this:
 * <code>
 * SELECT count(node_id) AS number_of_nodes FROM aggregator;
 * 
 * if ($number_of_nodes > 0) then
 *     $messages[] = 'There are still $number_of_nodes nodes with a aggregator';
 *     return FALSE;
 * </code>
 * which in fact means that the table should already be empty before
 * we can empty it. Oh well...
 * 
 * @param array &$messages collects the (error) messages
 * @param int $module_id the key for this module in the modules table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function aggregator_uninstall(&$messages,$module_id) {
    return TRUE;
} // aggregator_uninstall()


/** add demonstration data to the system
 *
 * this routine adds to the existing set of demonstration data as specified
 * in $config.
 *
 * FIXME
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
function aggregator_demodata(&$messages,$module_id,$config,$manifest) {
    global $DB;
    $retval = TRUE; // assume success

    // FIXME this is a stub

    return $retval;
} // aggregator_demodata()

?>