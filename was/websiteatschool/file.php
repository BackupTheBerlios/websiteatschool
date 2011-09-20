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

/** /file.php - the main entrypoint for serving files
 *
 * This is one of the main entry points for Website@School. Other main
 * entry points are {@link admin.php}, {@link cron.php}, {@link index.php}
 * and also {@link install.php}. Main entry points all define the constant
 * WASENTRY. This is used in various include()ed files to detect break-in
 * attempts.
 *
 * This is a kickstarter for /program/main_file.php, but first we check
 * for 'maintenance mode': if the file 'maintenance.html' exists in the 
 * current directory, we bail out and redirect the visitor to that file.
 *
 * If we're NOT in maintenance mode, we read the essential configuration
 * parameters from the file 'config.php' in the current directory and 
 * continue with /program/main_index.php to do the actual work.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: file.php,v 1.3 2011/09/20 11:54:46 pfokker Exp $
 */
/** Valid entry points define WASENTRY; prevents direct access to include()'s. */
define('WASENTRY',__FILE__);

if (file_exists('maintenance.html')) {
    header('Location: maintenance.html');
    die;
}

if (file_exists(dirname(WASENTRY).'/config.php')) {
    unset($CFG); /* prevent tricks with stray globals */
    $CFG = new stdClass;
    require_once(dirname(WASENTRY).'/config.php');
} else {
    die('condition code 010');
}

if (file_exists($CFG->progdir.'/main_file.php')) {
    require_once($CFG->progdir.'/main_file.php');
} else {
    die('condition code 015');
}

/* main_file() does all the work. It is defined in /program/main_file.php */
main_file();

?>