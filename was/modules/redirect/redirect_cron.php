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

/** /program/modules/redirect/redirect_cron.php - interface to the cron-part of the redirect module
 *
 * This file defines the interface with the redirect-module for cron.
 * The interface consists of this function:
 *
 * <code>
 * redirect_cron()
 * </code>
 *
 * This function is called whenever cron determines that it is time to perform this function.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_redirect
 * @version $Id: redirect_cron.php,v 1.2 2013/06/11 11:25:26 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** routine that is called periodically by cron
 *
 * there is nothing in this module that needs to be done by cron,
 * so this routine is basically a nop.
 *
 * @return bool TRUE on success, FALSE otherwise
 */
function redirect_cron($keywords,$areas) {
    return TRUE;
} // redirect_cron()

?>