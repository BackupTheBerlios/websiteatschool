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

/** /program/languages/nl/was.php - generic translated messages (Dutch)
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: was.php,v 1.4 2013/06/03 10:42:59 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['translatetool_title'] = 'Algemeen';
$string['translatetool_description'] = 'Dit bestand bevat algemeen in het beheersysteem gebruikte vertalingen'; 

$string['logout_username'] = 'Logout {USERNAME}';
$string['login'] = 'Login';

$string['button_ok'] = 'O~K';
$string['button_save'] = '~Opslaan';
$string['button_cancel'] = '~Annuleren';
$string['button_delete'] = '~Wissen';
$string['button_yes'] = '~Ja';
$string['button_no'] = '~Nee';
$string['button_go'] = '~Ga';
$string['button_edit'] = '~Bewerken';
$string['hotkey_for_button'] = 'Gebruik Alt-{HOTKEY} of Cmnd-{HOTKEY} als sneltoets voor deze knop';

$string['validate_too_short'] = '{FIELD}: ingevoerde tekst is te kort (minimum = {MIN})';
$string['validate_too_long'] = '{FIELD}: ingevoerde tekst is te lang (maximum = {MAX})';
$string['validate_too_small'] = '{FIELD}: ingevoerde waarde is te klein (minimum = {MIN})';
$string['validate_too_large'] = '{FIELD}: ingevoerde waarde is te groot (maximum = {MAX})';
$string['validate_invalid'] = '{FIELD}: ongeldige waarde';
$string['validate_invalid_datetime'] = '{FIELD}: ongeldige datum/tijd';
$string['validate_not_unique'] = '{FIELD}: ingevoerde waarde is niet uniek';
$string['validate_already_exists'] = '{FIELD}: bestand of map \'{VALUE}\' bestaat reeds';
$string['validate_different_passwords'] = '{FIELD1} en {FIELD2}: wachtwoorden zijn niet gelijk';
$string['validate_bad_password'] = '{FIELD}: wachtwoord voldoet niet aan minimum-eisen: lengte: {MIN_LENGTH}, cijfers: {MIN_DIGIT}, onderkast: {MIN_LOWER}, kapitalen: {MIN_UPPER}';
$string['validate_bad_filename'] = '{FIELD}: bestandsnaam is niet acceptabel: \'{VALUE}\'';

$string['alerts_mail_subject'] = 'Alerts voor {SITENAME}: {ALERTS}';
$string['alerts_processed'] = 'Aantal verwerkte alerts: {ALERTS}';

$string['problem_with_module'] = 'Er is een probleem met module {MODULE} in pagina {NODE}. Kunt u de webmaster hiervan op de hoogte stellen? Dank u.';

$string['capacity_name_unknown'] = '(Onbekend {CAPACITY})';
$string['capacity_name_0'] = '-- Geen --';
$string['capacity_name_1'] = 'Leerling';
$string['capacity_name_2'] = 'Leraar';
$string['capacity_name_3'] = 'Hoofd';
$string['capacity_name_4'] = 'Lid';
$string['capacity_name_5'] = 'Projectleider';
$string['capacity_name_6'] = 'Penningmeester';
$string['capacity_name_7'] = 'Secretaris';
$string['capacity_name_8'] = 'Voorzitter';
$string['capacity_name_9'] = 'Redacteur';
$string['capacity_name_10'] = 'Uitgever';
$string['capacity_name_11'] = 'Hoedanigheid 11';
$string['capacity_name_12'] = 'Hoedanigheid 12';
$string['capacity_name_13'] = 'Hoedanigheid 13';
$string['capacity_name_14'] = 'Hoedanigheid 14';
$string['capacity_name_15'] = 'Hoedanigheid 15';
$string['capacity_name_16'] = 'Hoedanigheid 16';
$string['capacity_name_17'] = 'Hoedanigheid 17';
$string['capacity_name_18'] = 'Hoedanigheid 18';
$string['capacity_name_19'] = 'Hoedanigheid 19';

$string['file_not_found'] = '404 Bestand niet gevonden: "{FILE}"';

?>