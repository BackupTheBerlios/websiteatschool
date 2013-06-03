<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker <peter@berestijn.nl>
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

/** /program/languages/en/was.php - generic translated messages 
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: was.php,v 1.4 2013/06/03 10:42:59 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['translatetool_title'] = 'General';
$string['translatetool_description'] = 'This file contains general translations used in the CMS';

$string['logout_username'] = 'Logout {USERNAME}';
$string['login'] = 'Login';

$string['button_ok'] = '~OK';
$string['button_save'] = '~Save';
$string['button_cancel'] = '~Cancel';
$string['button_delete'] = '~Delete';
$string['button_yes'] = '~Yes';
$string['button_no'] = '~No';
$string['button_go'] = '~Go';
$string['button_edit'] = '~Edit';
$string['hotkey_for_button'] = 'Use Alt-{HOTKEY} or Cmnd-{HOTKEY} as a keyboard shortcut for this button';

$string['validate_too_short'] = '{FIELD}: string is too short (minimum = {MIN})';
$string['validate_too_long'] = '{FIELD}: string is too long (maximum = {MAX})';
$string['validate_too_small'] = '{FIELD}: value is too small (minimum = {MIN})';
$string['validate_too_large'] = '{FIELD}: value is too large (maximum = {MAX})';
$string['validate_invalid'] = '{FIELD}: invalid value';
$string['validate_invalid_datetime'] = '{FIELD}: invalid date/time';
$string['validate_not_unique'] = '{FIELD}: value must be unique';
$string['validate_already_exists'] = '{FIELD}: file or directory \'{VALUE}\' already exists';
$string['validate_different_passwords'] = '{FIELD1} and {FIELD2}: passwords are not equal';
$string['validate_bad_password'] = '{FIELD}: password not acceptable; minimum requirements are: length: {MIN_LENGTH}, digits: {MIN_DIGIT}, lowercase: {MIN_LOWER}, uppercase: {MIN_UPPER}';
$string['validate_bad_filename'] = '{FIELD}: filename not acceptable: \'{VALUE}\'';

$string['alerts_mail_subject'] = 'Alerts for website {SITENAME}: {ALERTS}';
$string['alerts_processed'] = 'Number of alerts processed: {ALERTS}';

$string['problem_with_module'] = 'There is a problem with module {MODULE} in page {NODE}. Can you please tell the webmaster about it? Thank you.';

$comment['capacity_name_unknown'] = 'Here starts the mapping of capacities to readable text';
$string['capacity_name_unknown'] = '(Unknown {CAPACITY})';
$string['capacity_name_0'] = '-- None --';
$string['capacity_name_1'] = 'Pupil';
$string['capacity_name_2'] = 'Teacher';
$string['capacity_name_3'] = 'Principal';
$string['capacity_name_4'] = 'Member';
$string['capacity_name_5'] = 'Project lead';
$string['capacity_name_6'] = 'Treasurer';
$string['capacity_name_7'] = 'Secretary';
$string['capacity_name_8'] = 'Chair';
$string['capacity_name_9'] = 'Editor';
$string['capacity_name_10'] = 'Publisher';
$string['capacity_name_11'] = 'Capacity 11';
$string['capacity_name_12'] = 'Capacity 12';
$string['capacity_name_13'] = 'Capacity 13';
$string['capacity_name_14'] = 'Capacity 14';
$string['capacity_name_15'] = 'Capacity 15';
$string['capacity_name_16'] = 'Capacity 16';
$string['capacity_name_17'] = 'Capacity 17';
$string['capacity_name_18'] = 'Capacity 18';
$string['capacity_name_19'] = 'Capacity 19';

$string['file_not_found'] = '404 File Not Found: "{FILE}"';

?>