<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam, <info@websiteatschool.eu>
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

/** /program/languages/da/loginlib.php
 *
 * Language: da (Dansk)
 * Release:  0.90.3 / 2012041700 (2012-04-17)
 *
 * @author Christian Borum Loebner - Olesen  <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_da
 * @version $Id: loginlib.php,v 1.2 2013/06/11 11:25:09 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Login';
$string['translatetool_description'] = 'Denne fil indeholder oversættelser om login/logout';
$string['access_denied'] = 'Adgang nægtet';
$string['change_password'] = 'Skift password';
$string['change_password_confirmation_message'] = 'Dit password er ændret.

Forespørgsel om ændrign af password blev modtaget fra adressen {REMOTE_ADDR} on {DATETIME}.

Med venlige hilsner

Din automatiske webmaster!';
$string['change_password_confirmation_subject'] = 'Dit password er nu ændret';
$string['contact_webmaster_for_new_password'] = 'Kontakt webmasteren, hvis du vil ændre dit password';
$string['do_you_want_to_try_forgot_password_procedure'] = 'Forkerte brugeroplysninger. Du kan bruge "glemt password" funktionen';
$string['email_address'] = 'E-mail adresse';
$string['failure_sending_laissez_passer_mail'] = 'Der opstod problemer med at sende mailen med "one-time"koden. Prøv igen eller kontakt webmasteren hvis problemet opstår igen.';
$string['failure_sending_temporary_password'] = 'Der opstod problemer med at sende mailen med midlertidigt password . Prøv igen eller kontakt webmasteren hvis problemet opstår igen.';
$string['forgot_password'] = 'Har du glemt dit password?';
$string['forgotten_password_mailmessage1'] = 'Her er et link med en "one-time" kode, som gør det muligt for dig anmode om et nyt midlertidigt password. Kopier linket nedenfor ind i din browsers adresselinje og tryk [Enter]:

    {AUTO_URL}

Alternativt kan du gå til denne adresse:

    {MANUAL_URL}

og indsætte brugernavn og "one-time" koden:

    {LAISSEZ_PASSER}

Vær opmærksom på at denne kode kun kan bruges i {INTERVAL} minutter.

Forespørgelse om "one-time" koden var modtaget fra følgende adresse:

    {REMOTE_ADDR}

Held og lykke!

Din automatiske webmaster!';
$string['forgotten_password_mailmessage2'] = 'Her er dit midlertidige password:

    {PASSWORD}

Vær opmærksom på at passwordet kun er gyldigt i {INTERVAL} minutter.

Forespørgelse om det midlertidige password var modtaget fra følgende adresse:

    {REMOTE_ADDR}

Held og lykke!

Din automatiske webmaster!';
$string['home_page'] = '(Hjem)';
$string['invalid_credentials_please_retry'] = 'Forkerte brugeroplysninger, vær venlig at prøve igen.';
$string['invalid_laissez_passer_please_retry'] = 'Forkert "one-time" code, vær venlig at prøve igen.';
$string['invalid_new_passwords'] = 'Det nye password er ikke accepteret. Dette kan muligvis skyldes:
det første password er ikke det samme som det andet;
Det første password er ikke langt nok (minimum {MIN_LENGTH}),
Der var ikke nok små bogstaver (minimum {MIN_LOWER}),
store bogstaver (minumum {MIN_UPPER}) eller tal (minimum {MIN_DIGIT})
eller at dit nye password er det samme som dit gamle.
Vær venlig at finde et nyt password og prøv igen.';
$string['invalid_username_email_please_retry'] = 'Invalid username and e-mail address, please retry.';
$string['laissez_passer'] = 'One-time code';
$string['login'] = 'Login';
$string['logout_forced'] = 'Du er tvungent blevet logget ud.';
$string['logout_successful'] = 'Din logout er vellykket';
$string['message_box'] = 'beskedboks';
$string['must_change_password'] = 'Du bliver nødt til at ændre dit password nu!';
$string['new_password1'] = 'Nyt password';
$string['new_password2'] = 'Bekræft nyt password';
$string['OK'] = 'OK';
$string['password'] = 'Password';
$string['password_changed'] = 'Ændring af dit password er vellykket';
$string['please_enter_new_password_twice'] = 'Vær venlig at indsætte dit brugernavn, password og også dit nye password to gange og tryk derefter på knappen';
$string['please_enter_username_email'] = 'Vær venlig at skrive dit brugernavn og emailadresse og tryk på knappen';
$string['please_enter_username_laissez_passer'] = 'Vær venlig at skrive dit brugernavn og "one-time" kode og tryk på knappen';
$string['please_enter_username_password'] = 'Vær venlig at skrive dit brugernavn og password og tryk på knappen';
$string['request_bypass'] = 'Anmod om midlertidigt password';
$string['request_laissez_passer'] = 'Anmod om one-time login kode';
$string['see_mail_for_further_instructions'] = 'Vær venlig at tjekke din mail for yderligere vejledning';
$string['see_mail_for_new_temporary_password'] = 'Vær venlig at tjekke din mail for fit nye midlertidige password';
$string['too_many_change_password_attempts'] = 'Der er brugt for mange forsøg på at skifte password';
$string['too_many_login_attempts'] = 'Der er brugt for mange forsøg på at logge ind';
$string['username'] = 'Brugernavn';
$string['your_forgotten_password_subject1'] = 'Re: Anmodning om One-time login kode';
$string['your_forgotten_password_subject2'] = 'Re: Anmodning om midlertidigt password';
?>