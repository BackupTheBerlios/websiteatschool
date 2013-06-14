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

/** /program/languages/sv/loginlib.php
 *
 * Language: sv (Svenska)
 * Release:  0.90.4 / 2013061400 (2013-06-14)
 *
 * @author Hansje Cozijnsen <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_sv
 * @version $Id: loginlib.php,v 1.1 2013/06/14 19:59:58 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Logga in';
$string['translatetool_description'] = 'Denna fil innehåller översättningar som handlar om att logga in/logga ut';
$string['access_denied'] = 'Ingen tillgång';
$string['change_password'] = 'Ändra lösenord';
$string['change_password_confirmation_message'] = 'Lösenordet har ändrats.

Din ansökan till ett nytt lösenord har tagits emot ifrån adressen:
{REMOTE_ADDR} på {DATETIME}.

Med vänlig hälsning,

Din automatiserad webbmaster';
$string['change_password_confirmation_subject'] = 'Ditt lösenord har ändrats';
$string['contact_webmaster_for_new_password'] = 'Var vänlig och ta kontakt med din webbmaster för att ändra ditt lösenord';
$string['do_you_want_to_try_forgot_password_procedure'] = 'Felaktiga uppgifter. Vill du försöka förfarandet  "Glömt lösenord"?';
$string['email_address'] = 'E-postadress';
$string['failure_sending_laissez_passer_mail'] = 'Skicka e-post med engångskoden har misslyckats. Försök igen eller ta kontakt med din webbmaster om problemet kvarstår.';
$string['failure_sending_temporary_password'] = 'Skicka e-post med ett tillfälligt lösenord har misslyckats. Försök igen eller ta kontakt med din webbmaster om problemet kvarstår.';
$string['forgot_password'] = 'Glömt lösenordet?';
$string['forgotten_password_mailmessage1'] = 'Med denna link får du en engångskod för att ansöka ett nytt tillfälligt lösenord. Kopiera länkan nedan till adressfält av din webbläsaren och tryck [Gå]:

    {AUTO_URL}

Du kan också gå hit:

    {MANUAL_URL}

och fyll i din användarnamn och nedanstående engångskod:

    {LAISSEZ_PASSER}

Ta hänsyn till att denna koden  bara gäller i {INTERVAL} minuter. 

Ansökan till att skicka engångskoden har tagits emot ifrån adressen:

    {REMOTE_ADDR}

Lycka till!

Din automatiserad webbmaster';
$string['forgotten_password_mailmessage2'] = 'Här är ditt tillfälliga lösenord:

    {PASSWORD}

Ta hänsyn till att detta lösenord  bara gäller i {INTERVAL} minuter.

Ansökan till tillfälliga lösenordet har tagits emot ifrån adressen:

    {REMOTE_ADDR}

Lycka till!

Din automatiserad webbmaster';
$string['home_page'] = '(home)';
$string['invalid_credentials_please_retry'] = 'Felaktiga uppgifter, försök igen.';
$string['invalid_laissez_passer_please_retry'] = 'Felaktig engångskod, försök igen.';
$string['invalid_new_passwords'] = 'Ditt nya lösenord är inte godkänd. Möjliga orsaker:
det först inmatade lösenordet matchar inte det andra;
nya lösenordet var inte tillräckligt lång (minimum {MIN_LENGTH}),
det fanns inte tillräkligt med små bokstäver (minimum {MIN_LOWER}),
stora bokstäver (minumum {MIN_UPPER}) eller siffror (minimum {MIN_DIGIT})
eller ditt nya lösenord var detsamma som ditt gamla. Forsök att hitta på ett bra nytt lösenord och försök igen
';
$string['invalid_username_email_please_retry'] = 'Felaktig användarnamn och e-post adress, försök igen.';
$string['laissez_passer'] = 'Engångskod';
$string['login'] = 'Logga in';
$string['logout_forced'] = 'Du var tvungna att logga ut.';
$string['logout_successful'] = 'Du är loggat ut.';
$string['message_box'] = 'Meddelanderuta';
$string['must_change_password'] = 'Du måste nu ändra ditt lösenord.';
$string['new_password1'] = 'Nytt lösenord';
$string['new_password2'] = 'Bekräfta nytt lösenord';
$string['OK'] = 'OK';
$string['password'] = 'Lösenord';
$string['password_changed'] = 'Ändring av ditt lösenord har lyckats.';
$string['please_enter_new_password_twice'] = 'Mata in din användarnamn och gamla lösenord och även ditt nya lösenord två gånger och tryck på knappen..';
$string['please_enter_username_email'] = 'Mata in din användarnamn och e-post adress och tryck på knappen.';
$string['please_enter_username_laissez_passer'] = 'Mata in din användarnamn och engångskoden och tryck på knappen.';
$string['please_enter_username_password'] = 'Mata in din användarnamn och lösenord och tryck på knappen.';
$string['request_bypass'] = 'Ansök tillfälligt lösenord';
$string['request_laissez_passer'] = 'Ansök engångskod';
$string['see_mail_for_further_instructions'] = 'Var vänlig och se din e-post för mer information.';
$string['see_mail_for_new_temporary_password'] = 'Var vänlig och se din e-post för ditt nya tillfälliga lösenord.';
$string['too_many_change_password_attempts'] = 'För många misslyckade försök att ändra lösenord.';
$string['too_many_login_attempts'] = 'För många misslyckade försök att logga in.';
$string['username'] = 'Användarnamn';
$string['your_forgotten_password_subject1'] = 'SV:  engångskod ansökan';
$string['your_forgotten_password_subject2'] = 'SV: ansökan tillfälligt lösenord';
?>