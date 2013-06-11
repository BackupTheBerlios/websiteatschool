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

/** /program/modules/redirect/redirect_view.php - interface to the view-part of the redirect module
 *
 * This file defines the interface with the redirect-module for viewing content.
 * The interface consists of this function:
 *
 * <code>
 * redirect_view(&$output,$area_id,$node_id,$module)
 * </code>
 *
 * This function is called from /index.php when the node to display is connected
 * to this module.
 *
 * However, since this module is the redirect module, this routine is never called
 * because the execution of the redirection is done in main_index.php rather than
 * here (deeply nested) in a module. At that higher level we are in a better position
 * to handle the premature end of execution of main_index.php following sending the
 * redirect header. Therefore this routine, too, is a nop.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_redirect
 * @version $Id: redirect_view.php,v 1.2 2013/06/11 11:25:26 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** display the content of the redirect linked to node $node_id
 *
 * Since this module is the redirect module, this routine should never be called
 * because the execution of the redirection is done in main_index.php rather than
 * here (deeply nested) in a module. At that higher level we are in a better position
 * to handle the premature end of execution of main_index.php following sending the
 * redirect header. Therefore this routine is a nop.
 *
 * However, it is possible that the module is (still) connected to the node but
 * without a (valid) url. In that case the visitor sees a blank page but with
 * complete navigation etc. Can't win 'm all...
 *
 * @param object &$theme collects the (html) output
 * @param int $area_id identifies the area where $node_id lives
 * @param int $node_id the node to which this module is connected
 * @param array $module the module record straight from the database
 * @return bool TRUE on success + output via $theme, FALSE otherwise
 */
function redirect_view(&$theme,$area_id,$node_id,$module) {
    logger(sprintf('%s(): this function should not have been called',__FUNCTION__));
    return TRUE; // indicate success anyway
} // redirect_view()

?>