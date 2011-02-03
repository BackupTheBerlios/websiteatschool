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

/** /cron.php - the main entrypoint for processing cron jobs
 *
 * This is one of the main entry points for Website@School. Other main
 * entry points are {@link /index.php}, {@link /admin.php}, {@link /file.php}
 * and also {@link /program/install.php}. Main entry points all define the constant
 * WASENTRY. This is used in various include()ed files to detect break-in
 * attempts.
 *
 * This is a kickstarter for /program/main_cron.php.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: cron.php,v 1.2 2011/02/03 14:04:01 pfokker Exp $
 */

/** Valid entry points define WASENTRY; prevents direct access to include()'s. */
define('WASENTRY',__FILE__);

if (file_exists(dirname(WASENTRY).'/config.php')) {
    unset($CFG); /* prevent tricks with stray globals */
    require_once(dirname(WASENTRY).'/config.php');
} else {
    die('condition code 010');
}

if (file_exists($CFG->progdir.'/main_cron.php')) {
    require_once($CFG->progdir.'/main_cron.php');
} else {
    die('condition code 015');
}

/* main_cron() does all the work. It is defined in /program/main_cron.php */
main_cron();

?>