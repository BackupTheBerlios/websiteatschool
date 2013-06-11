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

/** /program/languages/de/loginlib.php
 *
 * Language: de (Deutsch)
 * Release:  0.90.3 / 2012041700 (2012-04-17)
 *
 * @author David Prousch <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_de
 * @version $Id: loginlib.php,v 1.3 2013/06/11 11:25:10 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Login';
$string['translatetool_description'] = 'Diese Datei enthält Übersetzungen, die den Login/Logout-Vorgang betreffen';
$string['access_denied'] = 'Zugang verweigert';
$string['change_password'] = 'Passwort ändern';
$string['change_password_confirmation_message'] = 'Das Passwort wurde geändert.

Die Passwort-Änderungsanfrage wurde erhalten. 
Von {REMOTE_ADDR} am {DATETIME}.

Mit freundlichen Grüßen

Ihr Webmaster';
$string['change_password_confirmation_subject'] = 'Das Passwort wurde erfolgreich geändert';
$string['contact_webmaster_for_new_password'] = 'Bitte kontaktieren Sie den Webmaster um das Passwort zu ändern.';
$string['do_you_want_to_try_forgot_password_procedure'] = 'Ungültige Anmeldedaten. Sie können über die Option \'Passwort vergessen\' ein neues Passwort erhalten.';
$string['email_address'] = 'E-Mail Adresse';
$string['failure_sending_laissez_passer_mail'] = 'Das Versenden der E-Mail ist fehlgeschlagen. Bitte versuchen Sie es noch einmal. Sollte das Problem weiterhin bestehen, kontaktieren Sie bitte den Webmaster.';
$string['failure_sending_temporary_password'] = 'Das Senden der E-Mail mit dem Übergangspasswort ist fehlgeschlagen. Bitte versuchen Sie es noch einmal. Sollte das Problem weiterhin bestehen, kontaktieren Sie bitte den Webmaster.';
$string['forgot_password'] = 'Passwort vergessen?';
$string['forgotten_password_mailmessage1'] = 'Hiermit erhalten Sie einen Link mit einem Einmal-Code. Damit wird ein Übergangspasswort angefordert. Kopieren Sie den unten angegebenen Link in die Adress-Leiste Ihres Browsers und drücken Sie [Enter]:

    {AUTO_URL}

Alternativ können Sie diesen Link aufrufen:

    {MANUAL_URL}

und Ihren Benutzernamen, sowie den Einmal-Code eingeben:

    {LAISSEZ_PASSER}

Beachten Sie, dass der Code nur für {INTERVAL} Minuten gültig ist.

Die Anfrage von diesem Einmal-Code wurde von dieser Adresse erhalten:

    {REMOTE_ADDR}


Wir hoffen, das Verfahren war für Sie erfolgreich!';
$string['forgotten_password_mailmessage2'] = 'Dies ist ihr Übergangspasswort:

    {PASSWORD}

Bitte beachten Sie, dass dieses Passwort nur für {INTERVAL} Minuten gültig ist.

Die Anfrage für dieses Übergangspasswort wurde von dieser Adresse erhalten:

    {REMOTE_ADDR}


Wir hoffen, das Verfahren war für Sie erfolgreich!';
$string['home_page'] = '(Home)';
$string['invalid_credentials_please_retry'] = 'Ungültige Anmeldedaten. Bitte versuchen Sie es noch einmal.';
$string['invalid_laissez_passer_please_retry'] = 'Ungültiger Einmal-Code. Bitte versuchen Sie es noch einmal.';
$string['invalid_new_passwords'] = 'Ihr neues Passwort wurde nicht akzeptiert. Mögliche Gründe:
- das erste Passwort stimmt nicht mit dem zweiten Passwort überein
- das neue Passwort war nicht lang genug (minimum {MIN_LENGTH})
- es gab nicht genügend Kleinbuchstaben (minimum {MIN_LOWER}),
Großbuchtstaben (minumum {MIN_UPPER}) oder Sonderzeichen (minimum {MIN_DIGIT})
- oder das neue Passwort stimmte mit Ihrem alten Passwort überein.

Bitte geben Sie ein neues sicheres Passwort ein.';
$string['invalid_username_email_please_retry'] = 'Ungültiger Benutzername und ungülitge E-Mail Adresse, Bitte versuchen Sie es noch einmal.';
$string['laissez_passer'] = 'Einmal-Code';
$string['login'] = 'Login';
$string['logout_forced'] = 'Sie wurden vom System abgemeldet.';
$string['logout_successful'] = 'Sie haben sich erfolgreich abgemeldet.';
$string['message_box'] = 'Nachrichten';
$string['must_change_password'] = 'Bitte ändern Sie Ihr Passwort.';
$string['new_password1'] = 'Neues Passwort.';
$string['new_password2'] = 'Bitte bestätigen Sie das neue Passwort.';
$string['OK'] = 'OK';
$string['password'] = 'Passwort';
$string['password_changed'] = 'Ihr Passwort wurde erfolgreich geändert.';
$string['please_enter_new_password_twice'] = 'Bitte geben Sie Ihren Benutzernamen und Ihr Passwort ein und zusätzlich Ihr neues Passwort zweimal. Klicken Sie dann auf die Schaltfläche.';
$string['please_enter_username_email'] = 'Bitte geben Sie Ihren Benutzernamen und Ihre E-Mail Adresse ein. Klicken Sie dann auf die Schaltfläche.';
$string['please_enter_username_laissez_passer'] = 'Bitte geben Sie Ihren Benutzernamen und den Einmal-Code ein. Klicken Sie dann auf die Schaltfläche.';
$string['please_enter_username_password'] = 'Bitte geben Sie Ihren Benutzernamen und Ihr Passwort ein. Klicken Sie dann auf die Schaltfläche.';
$string['request_bypass'] = 'Übergangspasswort anfordern';
$string['request_laissez_passer'] = 'Einmal-Login Code anfordern';
$string['see_mail_for_further_instructions'] = 'In der Ihnen zugesandten E-Mail ist das weitere Vorgehen beschrieben.';
$string['see_mail_for_new_temporary_password'] = 'In der Ihnen zugesandten E-Mail ist das Übergangspasswort enthalten.';
$string['too_many_change_password_attempts'] = 'Zu viele Nachfragen für eine Passwortänderung.';
$string['too_many_login_attempts'] = 'Zu viele Login-Versuche.';
$string['username'] = 'Benutzername';
$string['your_forgotten_password_subject1'] = 'Re: Ihr angeforderter Einmal-Code';
$string['your_forgotten_password_subject2'] = 'Re: Ihr angefordertes Übergangspasswort';
?>