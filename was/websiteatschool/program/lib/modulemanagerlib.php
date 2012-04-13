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

/** /program/lib/modulemanagerlib.php - modulemanager
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: modulemanagerlib.php,v 1.4 2012/04/13 08:01:33 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** main entry point for modulemanager (called from admin.php)
 *
 * @param object &$output collects the html output
 * @return void results are returned as output in $output
 */
function job_modulemanager(&$output) {
    $output->add_content('<h2>'.t('description_modulemanager','admin').'</h2>');
    $msg = t('function_not_yet_implemented','admin');
    $output->add_content($msg);
    $output->add_message($msg);
}
?>