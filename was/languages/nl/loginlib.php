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

/** /program/languages/nl/loginlib.php - translated messages for login procedure and change password
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: loginlib.php,v 1.2 2011/02/03 14:03:59 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['translatetool_title'] = 'Login';
$string['translatetool_description'] = 'Dit bestand bevat vertalingen voor inloggen/uitloggen';

$string['access_denied'] = 'Geen toegang';

$string['change_password'] = 'Wijzig wachtwoord';

$string['change_password_confirmation_message'] = 
'Uw wachtwoord is gewijzigd.

Het verzoek tot wijzigen van het wachtwoord is ontvangen
van adres {REMOTE_ADDR} op {DATETIME}.

Met vriendelijke groet,

Uw automatische webmaster.';

$string['change_password_confirmation_subject'] = 'Uw wachtwoord is gewijzigd';

$string['contact_webmaster_for_new_password'] = 'Neem contact op met de webmaster om uw wachtwoord te wijzigen.';

$string['do_you_want_to_try_forgot_password_procedure'] = 'Ongeldige gegevens. Wilt u misschien de procedure voor \'wachtwoord vergeten\' starten?';

$string['email_address'] = 'E-mail-adres';

$string['failure_sending_laissez_passer_mail'] = 'Fout bij het verzenden van de eenmalige code. Probeer het nog eens of neem contact op met de webmaster als het verzenden blijft mislukken.';

$string['failure_sending_temporary_password'] = 'Fout bij het verzenden van het tijdelijke wachtwoord. Probeer het nog eens of neem contact op met de webmaster als het verzenden blijft mislukken.';

$string['forgot_password'] = 'Wachtwoord vergeten?';

$string['forgotten_password_mailmessage1'] = 
'Hier is een link met een eenmalige code waarmee u een nieuw,
tijdelijk wachtwoord kunt opvragen. Neem onderstaande link over
in de adresbalk van uw browser en druk op [Enter]:

    {AUTO_URL}

U kunt ook naar de volgende pagina gaan:

    {MANUAL_URL}

en daar handmatig uw gebruikersnaam en onderstaande code invullen:

    {LAISSEZ_PASSER}

Merk op dat deze code slechts {INTERVAL} minuten geldig is.

Het verzoek voor het toezenden van deze eenmalige code is ontvangen
vanaf dit adres:

    {REMOTE_ADDR}

Veel succes!

Uw automatische webmaster';

$string['forgotten_password_mailmessage2'] = 
'Hier is uw tijdelijke nieuwe wachtwoord:

    {PASSWORD}

Merk op dat dit wachtwoord slechts {INTERVAL} minuten geldig is.

Het verzoek voor het toezenden van het tijdelijke wachtwoord is ontvangen
vanaf dit adres:

    {REMOTE_ADDR}

Veel succes!

Uw automatische webmaster';

$string['home_page'] = '(home)';

$string['invalid_credentials_please_retry'] = 'Ongeldige gegevens, probeer het nogmaals.';

$string['invalid_laissez_passer_please_retry'] = 'Ongeldige eenmalige code, probeer het nogmaals.';

$string['invalid_new_passwords'] = 
'Uw nieuwe wachtwoord is niet acceptabel. Mogelijk oorzaken:
het eerst ingevoerde nieuwe wachtwoord was niet gelijk aan het tweede;
het nieuwe wachtwoord was niet lang genoeg (minimum {MIN_LENGTH}),
er zaten niet genoeg kleine letters (minimum {MIN_LOWER}),
hoofdletters (minumum {MIN_UPPER}) of cijfers (minimum {MIN_DIGIT})
in het nieuwe wachtwoord of het nieuwe wachtwoord was hetzelfde als
het oude. Probeer een goed nieuw wachtwoord te bedenken en probeer
het nogmaals';

$string['invalid_username_email_please_retry'] = 'Ongeldige gebruikersnaam en e-mail-adres, probeer het nogmaals.';

$string['laissez_passer'] = 'Eenmalige code';

$string['login'] = 'Login';

$string['logout_forced'] = 'U bent gedwongen uitgelogd.';

$string['logout_successful'] = 'U bent met succes uitgelogd.';

$string['message_box'] = 'Bericht';

$string['must_change_password'] = 'U moet nu uw wachtwoord wijzigen.';

$string['new_password1'] = 'Nieuw wachtwoord';

$string['new_password2'] = 'Bevestig nieuw wachtwoord';

$string['OK'] = 'OK';

$string['password'] = 'Wachtwoord';

$string['password_changed'] = 'Uw wachtwoord is met succes gewijzigd.';

$string['please_enter_new_password_twice'] = 'Voer tweemaal uw nieuwe wachtwoord in en druk op de knop.';

$string['please_enter_username_email'] = 'Voer uw gebruikersnaam en e-mail-adres in en druk op de knop.';

$string['please_enter_username_laissez_passer'] = 'Voer uw gebruikersnaam en eenmalige code in en druk op de knop.';

$string['please_enter_username_password'] = 'Voer uw gebruikersnaam en wachtwoord in en druk op de knop.';

$string['request_bypass'] = 'Verzoek voor tijdelijk wachtwoord';

$string['request_laissez_passer'] = 'Verzoek voor eenmalige inlogcode';

$string['see_mail_for_further_instructions'] = 'Zie uw e-mail voor nadere instructies.';

$string['see_mail_for_new_temporary_password'] = 'Zie uw e-mail voor uw nieuwe tijdelijke wachtwoord.';

$string['too_many_change_password_attempts'] = 'Teveel mislukte pogingen om wachtwoord te wijzigen.';

$string['too_many_login_attempts'] = 'Teveel mislukte pogingen om in te loggen.';

$string['username'] = 'Gebruikersnaam';

$string['your_forgotten_password_subject1'] = 'Betreft: eenmalige inlogcode';

$string['your_forgotten_password_subject2'] = 'Betreft: tijdelijk wachtwoord';


?>