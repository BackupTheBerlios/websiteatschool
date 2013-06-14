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

/** /program/languages/hu/loginlib.php
 *
 * Language: hu (Magyar)
 * Release:  0.90.4 / 2013061400 (2013-06-14)
 *
 * @author Erika Swiderski <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_hu
 * @version $Id: loginlib.php,v 1.3 2013/06/14 19:59:55 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Belépés';
$string['translatetool_description'] = 'Ez a fájl a belépés/kilépés fordításait tartalmazza';
$string['access_denied'] = 'Visszautasított kapcsolat';
$string['change_password'] = 'Jelszóváltoztatás';
$string['change_password_confirmation_message'] = 'Jelszava megváltozott. 

A jelszó megváltoztatását a(z) {REMOTE_ADDR} címről kezdeményezték, {DATETIME}-kor.

Az ön automatikus webmestere';
$string['change_password_confirmation_subject'] = 'A jelszót sikeresen megváltoztatta';
$string['contact_webmaster_for_new_password'] = 'A jelszó megváltoztatásáért keresse meg a webmestert.';
$string['do_you_want_to_try_forgot_password_procedure'] = 'Hibás felhasználónév vagy jelszó. Elfelejtette?';
$string['email_address'] = 'E-mail cím';
$string['failure_sending_laissez_passer_mail'] = 'Nem sikerült az az egyszeri jelszót tartalmazó e-mailt elküldeni. Próbálja újra vagy kérje a webmester segítségét.';
$string['failure_sending_temporary_password'] = 'Nem sikerült az ideiglenes jelszót tartalmazó e-mailt elküldeni. Próbálja újra vagy kérje a webmester segítségét.';
$string['forgot_password'] = 'Elfelejtette a jelszavát?';
$string['forgotten_password_mailmessage1'] = 'Ezen a linken találja az egyszeri jelszót, amellyel egy új, ideiglenes jelszót igényelhet. Másolja be a linket a böngészőbe, és nyomjon [Enter]-t:

    {AUTO_URL}

Vagy kattintson ide:

    {MANUAL_URL}

és írja be a felhasználónevét és ezt az egyszeri jelszót:

    {LAISSEZ_PASSER}

Figyelem! A jelszó mindössze {INTERVAL} percig érvényes.

Az egyszeri jelszót erről az e-mail címről kérték:

    {REMOTE_ADDR}

Sok szerencsét! 

Az ön automatikus webmestere';
$string['forgotten_password_mailmessage2'] = 'Ez az ön ideiglenes jelszava: 

    {PASSWORD}

Figyelem! A jelszó mindössze {INTERVAL} percig érvényes.

Az ideiglenes jelszót erről az e-mail címről kérték:

    {REMOTE_ADDR}

Sok szerencsét! 

Az ön automatikus webmestere';
$string['home_page'] = '(home)';
$string['invalid_credentials_please_retry'] = 'Hibás felhasználónév vagy jelszó, próbálja újra!';
$string['invalid_laissez_passer_please_retry'] = 'Hibás egyszeri jelszó, próbálja újra!';
$string['invalid_new_passwords'] = 'Jelszava nem megfelelő. Lehetséges problémák:
az első és a második jelszó nem egyezik;
az első jelszó nem elég hosszú  (minimum {MIN_LENGTH} karakter),
nincs elég kisbetű benne (minimum {MIN_LOWER} karakter), nincs elég nagybetű (minumum {MIN_UPPER} karakter) vagy szám (minimum {MIN_DIGIT}) benne; 
esetleg az új jelszó megegyezik a régivel.

Válasszon új jelszót és próbálja újra!';
$string['invalid_username_email_please_retry'] = 'Hibás felhasználónév és e-mail, próbálja újra!';
$string['laissez_passer'] = 'Egyszeri jelszó';
$string['login'] = 'Belépés';
$string['logout_forced'] = 'Ki kellett lépnie';
$string['logout_successful'] = 'Sikeresen kilépett';
$string['message_box'] = 'Üzenet-táe';
$string['must_change_password'] = 'Meg kell változtatnia a jelszavát.';
$string['new_password1'] = 'Új jelszó.';
$string['new_password2'] = 'Erősítse meg az új jelszavát.';
$string['OK'] = 'OK';
$string['password'] = 'Jelszó';
$string['password_changed'] = 'Jelszava megváltozott.';
$string['please_enter_new_password_twice'] = 'Adja meg felhasználónevét, jelszavát és új jelszavát kétszer, majd nyomja meg a gombot.';
$string['please_enter_username_email'] = 'Adja meg felhasználónevét és e-mail címét, majd nyomja meg a gombot. .';
$string['please_enter_username_laissez_passer'] = 'Adja meg felhasználónevét és egyszeri jelszavát, majd nyomja meg a gombot.';
$string['please_enter_username_password'] = 'Adja meg felhasználónevét és jelszavát, majd nyomja meg a gombot';
$string['request_bypass'] = 'Ideiglenes jelszó igénylése';
$string['request_laissez_passer'] = 'Egyszeri belépési kód igénylése';
$string['see_mail_for_further_instructions'] = 'További segítséget e-mail-ben talál.';
$string['see_mail_for_new_temporary_password'] = 'Új ideiglenes jelszava e-mail-ben érkezik.';
$string['too_many_change_password_attempts'] = 'Túl sok jelszóváltoztatási próbálkozás.';
$string['too_many_login_attempts'] = 'Túl sok belépési próbálkozás.';
$string['username'] = 'Felhasználónév';
$string['your_forgotten_password_subject1'] = 'Re: Egyszeri belépési kód igénylése';
$string['your_forgotten_password_subject2'] = 'Re: Ideiglenes jelszó igénylése';
?>