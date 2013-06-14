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

/** /program/languages/fi/loginlib.php
 *
 * Language: fi (Suomi)
 * Release:  0.90.4 / 2013061400 (2013-06-14)
 *
 * @author Laura Råman <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_fi
 * @version $Id: loginlib.php,v 1.1 2013/06/14 19:59:54 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Kirjaudu';
$string['translatetool_description'] = 'Tämä tiedosto sisältää käännöksiä koskien sisään ja ulos kirjautumista';
$string['access_denied'] = 'Pääsy kielletty';
$string['change_password'] = 'Muuta salasana';
$string['change_password_confirmation_message'] = 'Salasanasi on muutettu.

Salasanan muokkauspyyntö otettiin vastaan 
osoitteesta {REMOTE_ADDR} {DATETIME}.

Ystävällisin terveisin,

Automaattinen Webmaster.';
$string['change_password_confirmation_subject'] = 'Salasanasi muuttaminen onnistui';
$string['contact_webmaster_for_new_password'] = 'Ole hyvä ja ota yhteyttä webmasteriin muuttaaksesi salasanan.';
$string['do_you_want_to_try_forgot_password_procedure'] = 'Virheelliset tunukset. Haluatko koittaa \'unohditko salasanan\' toimintoa?';
$string['email_address'] = 'Sähköpostiosoite';
$string['failure_sending_laissez_passer_mail'] = 'Kertakoodin sisältävää sähköpostia lähetettäessä on tapahtunut virhe. Ole hyvä ja koita uudelleen tai ota yhteyttä webmasteriin mikäli  ongelma esiintyy uudelleen.';
$string['failure_sending_temporary_password'] = 'Tilapäisen salasanan sisältävää sähköpostia lähetettäessä on tapahtunut virhe. Ole hyvä ja koita uudelleen tai ota yhteyttä webmasteriin mikäli  ongelma esiintyy uudelleen.';
$string['forgot_password'] = 'Unohditko salasanan?';
$string['forgotten_password_mailmessage1'] = 'Tässä on linkki ja kertakoodi, jonka avulla voit pyytää uuden väliaikaisen salasanan. Kopioi alla oleva linkki selaimesi osoitekenttään ja paina [Enter]:

    {AUTO_URL}

Vaihtoehtoisesti voit jatkaa tänne:

    {MANUAL_URL}

ja antaa käyttäjätunnuksen ja tämän kertakoodin:

    {LAISSEZ_PASSER}

Huomaa, että tämä koodi on voimassa vain {INTERVAL} minuutia.

Tämä kertakoodi pyydettiin osoitteesta:

    {REMOTE_ADDR}

Onnea!

Automaattinen Webmaster';
$string['forgotten_password_mailmessage2'] = 'Tässä väliaikainen salasana:

    {PASSWORD}

Huomaa, että tämä salasana on voimassa vain {INTERVAL} minuutia.

Tämä väliaikainen salasana pyydettiin osoitteesta:

    {REMOTE_ADDR}

Onnea!

Automaattinen Webmaster';
$string['home_page'] = '(koti)';
$string['invalid_credentials_please_retry'] = 'Virheelliset tunnukset, ole hyvä ja koita uudelleen.';
$string['invalid_laissez_passer_please_retry'] = 'Virheellinen kertakoodi, ole hyvä ja koita uudelleen.';
$string['invalid_new_passwords'] = 'Uutta salasanaasi ei voitu hyväksyä. Mahdollisia syitä:
ensimmäinen salasana erosi toisesta;
uusi salasana ei ollut tarpeeksi pitkä (vähintään {MIN_LENGTH}),
se ei sisältänyt tarpeeksi pieniä kirjaimia (vähintään {MIN_LOWER}),
isoja kirjaimia (vähintään {MIN_UPPER}) tai numeroita (vähintään {MIN_DIGIT})
tai uusi salasana on sama kuin vanha.
Ole hyvä ja koita miettiä hyvä uusi salasana ja koita uudelleen.';
$string['invalid_username_email_please_retry'] = 'Virheellinen käyttäjätunnus ja sähköpostiosoite, ole hyvä ja koita uudelleen.';
$string['laissez_passer'] = 'kertakoodi';
$string['login'] = 'kirjaudu sisään';
$string['logout_forced'] = 'Uloskirjautuminen tapahtui pakosta.';
$string['logout_successful'] = 'Uloskirjautuminen onnistui.';
$string['message_box'] = 'Viestikansio';
$string['must_change_password'] = 'Sinun tulee vaihtaa salasanasi nyt.';
$string['new_password1'] = 'Uusi salasana';
$string['new_password2'] = 'Vahvista uusi salasana';
$string['OK'] = 'OK';
$string['password'] = 'Salasana';
$string['password_changed'] = 'Salasanan muuttaminen onnistui.';
$string['please_enter_new_password_twice'] = 'Ole hyvä ja syötä käyttäjätunnus ja salasana sekä uusi salasana kahdesti ja paina painiketta.';
$string['please_enter_username_email'] = 'Ole hyvä ja syötä käyttäjätunnus ja sähköpostiosoite ja paina painiketta.';
$string['please_enter_username_laissez_passer'] = 'Ole hyvä ja syötä käyttäjätunnus ja kertakoodi ja paina painiketta.';
$string['please_enter_username_password'] = 'Ole hyvä ja syötä käyttäjätunnus ja salasana ja paina OK-painiketta.';
$string['request_bypass'] = 'Pyydä väliaikainen salasana';
$string['request_laissez_passer'] = 'Pyydä kertakoodi sisäänkirjautumista varten';
$string['see_mail_for_further_instructions'] = 'Ole hyvä ja lue lisäohjeita sähköpostistasi.';
$string['see_mail_for_new_temporary_password'] = 'Ole hyvä ja katso uusi väliaikainen salasana sähköpostistasi.';
$string['too_many_change_password_attempts'] = 'Liian monta yritystä salasanan vaihtamiseen.';
$string['too_many_login_attempts'] = 'Liian monta sisäänkirjautumisyritystä.';
$string['username'] = 'Käyttäjänimi';
$string['your_forgotten_password_subject1'] = 'Re: Kertakoodin pyyntö sisäänkirjautumista varten';
$string['your_forgotten_password_subject2'] = 'Re: Väliaikainen salasana pyyntö';
?>