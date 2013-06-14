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

/** /program/languages/pl/loginlib.php
 *
 * Language: pl (Polski)
 * Release:  0.90.4 / 2013061400 (2013-06-14)
 *
 * @author Waldemar Pankiw <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_pl
 * @version $Id: loginlib.php,v 1.4 2013/06/14 19:59:56 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Logowanie';
$string['translatetool_description'] = 'Ten plik zawiera tłumaczenia zwrotów związanych z logowaniem';
$string['access_denied'] = 'Odmówiono dostępu';
$string['change_password'] = 'Zmień hasło';
$string['change_password_confirmation_message'] = 'Hasło zostało zmienione.

Wniosek o zmianę hasła został wniesiony 
przez {REMOTE_ADDR} dnia: {DATETIME}.

Pozdrawiam,
Automatyczny administrator.';
$string['change_password_confirmation_subject'] = 'Zmiana hasła wykonana poprawnie';
$string['contact_webmaster_for_new_password'] = 'Skontaktuj się z administratorem w celu zmiany hasła.';
$string['do_you_want_to_try_forgot_password_procedure'] = 'Nieudana identyfikacja. Czy chcesz rozpocząć procedurę "przypomnij hasło"?';
$string['email_address'] = 'Adres e-mail';
$string['failure_sending_laissez_passer_mail'] = 'Wysłanie maila z jednorazowym kodem nie udało się. Spróbuj ponownie lub skontaktuj się z administratorem jeśli problem powraca.';
$string['failure_sending_temporary_password'] = 'Wysłanie maila z hasłem tymczasowym nie udało się. Spróbuj ponownie lub skontaktuj się z administratorem jeśli problem powraca.';
$string['forgot_password'] = 'Zapomniałeś hasła?';
$string['forgotten_password_mailmessage1'] = 'Oto linka z jednorazowym kodem umożliwiającym wniesienie wniosku o nowe, tymczasowe hasło. 
Skopiuj poniższą linkę do paska adresu w przeglądarce i wciśnij [Enter]:

    {AUTO_URL}

Lub wejdź na poniższą stronę:

    {MANUAL_URL}

i po podaniu login użytkownika wpisz następujący jednorazowy kod:

    {LAISSEZ_PASSER}

Uwaga: Ten kod jest ważny tylko  przez {INTERVAL} minut(y).

Wniosek o ten jednorazowy kod został wniesiony  przez:

    {REMOTE_ADDR}

Powodzenia!
Automatyczny administrator';
$string['forgotten_password_mailmessage2'] = 'Tymczasowe hasło:

    {PASSWORD}

Uwaga: to hasło ważne jest tylko przez {INTERVAL} minut.

Wniosek o to jednorazowe hasło został wniesiony  przez:

    {REMOTE_ADDR}

Powodzenia!
Automatyczny administrator';
$string['home_page'] = '(Strona główna)';
$string['invalid_credentials_please_retry'] = 'Nieudana identyfikacja, spróbuj ponownie.';
$string['invalid_laissez_passer_please_retry'] = 'Nieważny kod jednorazowy, spróbuj ponownie.';
$string['invalid_new_passwords'] = 'Hasło  nieważne. Możlwe powody: pierwsze i drugie hasło nie są identyczne;  nowe hasło za krótkie (minimum {MIN_LENGTH}),
za mało małych liter (minimum {MIN_LOWER}),
dużych liter (minumum {MIN_UPPER}) lub cyfr (minimum {MIN_DIGIT})
albo nowe hasło było takie same jak stare.
Wymyśl dobre nowe hasło i spróbuj ponownie.';
$string['invalid_username_email_please_retry'] = 'Nieważny login i adres e-mail,  spróbuj ponownie.';
$string['laissez_passer'] = 'Kod jednorazowy';
$string['login'] = 'Zaloguj';
$string['logout_forced'] = 'Zostałeś wylogowany przez system.';
$string['logout_successful'] = 'Zstałeś poprawnie wylogowany.';
$string['message_box'] = 'Skrzynka wiadomości';
$string['must_change_password'] = 'Musisz zmienić hasło teraz.';
$string['new_password1'] = 'Nowe hasło';
$string['new_password2'] = 'Potwierdź nowe hasło';
$string['OK'] = 'OK';
$string['password'] = 'Hasło';
$string['password_changed'] = 'Zmiana hasła wykonana poprawnie.';
$string['please_enter_new_password_twice'] = 'Podaj swój login i hasło oraz dwukrotnie nowe hasło następnie wciśnij przycisk';
$string['please_enter_username_email'] = 'Podaj swój login i adres e-mail  i kliknij na OK.';
$string['please_enter_username_laissez_passer'] = 'Podaj swój login i kod jednorazowy następnie wciśnij przycisk.';
$string['please_enter_username_password'] = 'Podaj swój login i hasło i kliknij na OK.';
$string['request_bypass'] = 'Wniosek o tymczasowe hasło';
$string['request_laissez_passer'] = 'Wniosek o jednorazowy kod zalogowania.';
$string['see_mail_for_further_instructions'] = 'Dalsze instrukcje przesłano e-mailem';
$string['see_mail_for_new_temporary_password'] = 'Nowe tymczasowe hasło przesłano e-mailem';
$string['too_many_change_password_attempts'] = 'Za duża ilość prób zmiany hasła.';
$string['too_many_login_attempts'] = 'Za duża ilość prób zalogowania się.';
$string['username'] = 'Login';
$string['your_forgotten_password_subject1'] = 'Dotyczy: wniosku o jednorazowy kod zalogowania.';
$string['your_forgotten_password_subject2'] = 'Dotyczy: wniosku o tymczasowe hasło';
?>