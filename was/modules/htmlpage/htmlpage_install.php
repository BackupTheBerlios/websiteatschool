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

/** /program/modules/htmlpage/htmlpage_install.php - installer of the htmlpage module
 *
 * This file contains the htmlpage module installer.
 * The interface consists of these functions:
 *
 * <code>
 * htmlpage_install(&$messages,$module_id)
 * htmlpage_upgrade(&$messages,$module_id)
 * htmlpage_uninstall(&$messages,$module_id)
 * htmlpage_demodata(&$messages,$module_id,$config,$manifest)
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
 * @package wasmod_htmlpage
 * @version $Id: htmlpage_install.php,v 1.3 2011/03/07 14:14:19 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** install the module
 *
 * this routine installs the module. For this module
 * there is nothing to install, so we simply return success.
 * The appropriate table is already created based on the tabledefs);
 * see install/htmlpage_tabledefs.php.
 *
 * Note that the record for this module is already created in the
 * modules table; the pkey is $module_id.
 *
 * @param array &$messages collects the (error) messages
 * @param int $module_id the key for this module in the modules table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function htmlpage_install(&$messages,$module_id) {
    return TRUE;
} // htmlpage_install()


/** upgrade the module
 *
 * this routine performs an upgrade to the installed module.
 * Note that this minimalistic 'htmlpage' module does not need any
 * upgrade at all because there never was an earlier version.
 *
 * However, if there was to be a newer version of this module, this
 * routine is THE place to bring the database up to date compared with
 * the existing version. For example, if an additional field 'page_data_backup'
 * was to be added to the htmlpage-table, it could be added using a
 * suitable (default) value, e.. the current contents of 'page_data'.
 *
 * Any existing htmlpage could then be updated here to fill the new
 * field with data, e.g.
 *
 * UPDATE htmlpage SET page_data_backup = page_data;
 *
 * etcetera. For now this routine is a nop.
 *
 * @param array &$messages collects the (error) messages
 * @param int $module_id the key for this module in the modules table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function htmlpage_upgrade(&$messages,$module_id) {
    return TRUE;
} // htmlpage_upgrade()


/** uninstall the module
 *
 * this is a hook for future extensions of Website@School.
 * For now we simply return success. Any real code could look like this:
 * DELETE FROM htmlpage;
 * to delete all existing data (but who would want that). For
 * now this is simpky a nop.
 *
 * @param array &$messages collects the (error) messages
 * @param int $module_id the key for this module in the modules table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function htmlpage_uninstall(&$messages,$module_id) {
    return TRUE;
} // htmlpage_uninstall()


/** add demonstration data to the system
 *
 * this routine is a no-op because all htmlpage demodata is already
 * created in the main demodata-routine in /program/install/demodata.php.
 * This routine is retained here as an example alias placeholder.
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
function htmlpage_demodata(&$messages,$module_id,$config,$manifest) {
    return TRUE;
} // htmlpage_demodata()

?>