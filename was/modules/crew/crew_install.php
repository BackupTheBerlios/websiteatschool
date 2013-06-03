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

/** /program/modules/crew/crew_install.php - installer of the crew module
 *
 * This file contains the crew module installer.
 * The interface consists of these functions:
 *
 * <code>
 * crew_install(&$messages,$module_id)
 * crew_upgrade(&$messages,$module_id)
 * crew_uninstall(&$messages,$module_id)
 * crew_demodata(&$messages,$module_id,$config,$manifest)
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
 * @package wasmod_crew
 * @version $Id: crew_install.php,v 1.1 2013/05/30 15:38:20 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** install the module
 *
 * this routine installs the module. For this module there
 * are a few properties that need to be stored in the main
 * modules_properties table.  The specific table for this module is
 * already  created based on the tabledefs); see install/crew_tabledefs.php.
 *
 * Note that the record for this module is already created in the
 * modules table; the pkey is $module_id.
 *
 * @param array &$messages collects the (error) messages
 * @param int $module_id the key for this module in the modules table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function crew_install(&$messages,$module_id) {
    $properties = array(
        'origin' => array(
            'type' => 's',
            'value' => (isset($_SERVER['HTTP_HOST'])) ? 'http://'.$_SERVER['HTTP_HOST'] : '',
            'extra' => 'maxlength=255',
            'description' => 'this must match the origin as seen by the browser of the CREW-user'),
        'location' => array(
            'type' => 's',
            'value' => '',
            'extra' => 'maxlength=255',
            'description' => 'this is the location (URL) of the websocket server'),
        'secret' => array(
            'type' => 's',
            'value' => '',
            'extra' => 'maxlength=255',
            'description' => 'this shared secret is necessary to access the websocket server')
        );
    $retval = TRUE; // assume success
    $table = 'modules_properties';
    $sort_order = 0;
    foreach($properties as $name => $property) {
        $property['module_id'] = $module_id;
        $property['name'] = $name;
        $sort_order += 10;
        $property['sort_order'] = $sort_order;
        if (db_insert_into($table,$property) === FALSE) {
            $messages[] = __FUNCTION__.'(): '.db_errormessage();
            $retval = FALSE;
        }
    }
    return $retval;
} // crew_install()


/** upgrade the module
 *
 * this routine performs an upgrade to the installed module.
 * Note that this module does not need any
 * upgrade at all because there never was an earlier version.
 *
 * However, if there was to be a newer version of this module, this
 * routine is THE place to bring the database up to date compared with
 * the existing version. For example, if an additional field 'crew_extension'
 * was to be added to the crew-table, it could be added using a
 * suitable (default) value, e.g. an empty string or whatever
 *
 * Any existing crew records could then be updated here to fill the new
 * field with data, e.g.
 *
 * UPDATE workshops SET crew_extension = '';
 *
 * etcetera. For now this routine is a nop.
 *
 * @param array &$messages collects the (error) messages
 * @param int $module_id the key for this module in the modules table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function crew_upgrade(&$messages,$module_id) {
    return TRUE;
} // crew_upgrade()


/** uninstall the module
 *
 * this is a hook for future extensions of Website@School.
 * For now we simply return success. Any real code could look like this:
 * DELETE FROM workshops;
 * to delete all existing data (but who would want that). For
 * now this is simply a nop.
 *
 * Note that bluntly deleting from the workshops table might lead to
 * nodes without a valid module. The better way to do it would be
 * something like this:
 * <code>
 * SELECT count(node_id) AS number_of_nodes FROM workshops;
 * 
 * if ($number_of_nodes > 0) then
 *     $messages[] = 'There are still $number_of_nodes nodes with a workshop';
 *     return FALSE;
 * </code>
 * which in fact means that the table should already be empty before
 * we can empty it. Oh well...
 * 
 * @param array &$messages collects the (error) messages
 * @param int $module_id the key for this module in the modules table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function crew_uninstall(&$messages,$module_id) {
    return TRUE;
} // crew_uninstall()


/** add demonstration data to the system
 *
 * this routine is a no-op for now. This should be fixed (FixMe).
 *
 * This routine is retained here as an example and also because a routine
 * by this name should exist (even if it does nothing).
 *
 * Note
 * If the module is installed via the Install Wizard, this routine is
 * called. However, if a module is installed as an additional module
 * after installation, the {$mode}_demodata() routine is never called.
 * This is because the only time you know that demodata is installed is
 * when the Install Wizard runs. If we're called from admin.php, the
 * webmaster may have already deleted existing (core) demodata so you
 * never can be sure what to expect. To put it another way: it is hard
 * to re-construct $config when we're NOT the Instal Wizard.
 *
 * @param array &$messages collects the (error) messages
 * @param int $module_id the key for this module in the modules table
 * @param array $configuration pertinent data for the new website + demodata foundation
 * @param array $manifest a copuy from the manifest for this module
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function crew_demodata(&$messages,$module_id,$config,$manifest) {
    return TRUE;
} // crew_demodata()

?>