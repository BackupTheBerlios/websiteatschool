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
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_htmlpage
 * @version $Id: htmlpage_install.php,v 1.2 2011/02/03 14:04:00 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** install the module
 *
 * this routine installs the module. For this module
 * there is nothing to install, so we simply return success.
 * (The appropriate table is already created based on the tabledefs).
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
 * @param array &$messages collects the (error) messages
 * @param int $module_id the key for this module in the modules table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function htmlpage_upgrade(&$messages,$module_id) {
    $messages[] = "STUB: ".__FUNCTION__."()";
    return TRUE;
} // htmlpage_upgrade()


/** uninstall the module
 *
 * @param array &$messages collects the (error) messages
 * @param int $module_id the key for this module in the modules table
 * @return bool TRUE on success + output via $messages, FALSE otherwise
 */
function htmlpage_uninstall(&$messages,$module_id) {
    $messages[] = "STUB: ".__FUNCTION__."()";
    return TRUE;
} // htmlpage_uninstall()


/** add demonstration data to the system
 *
 * this routine is a no-op because all htmlpage demodata is already
 * created in the main demodata-routine in /program/install/demodata.php.
 * This routine is retained here as an example alias placeholder.
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