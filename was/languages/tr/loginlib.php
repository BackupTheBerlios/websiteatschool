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

/** /program/languages/tr/loginlib.php
 *
 * Language: tr (Türkçe)
 * Release:  0.90.3 / 2012041700 (2012-04-17)
 *
 * @author Ülkü Gaga <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_tr
 * @version $Id: loginlib.php,v 1.3 2013/06/11 11:25:15 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Oturum aç';
$string['translatetool_description'] = 'Bu dosya oturum açıp kapamayla ilgili olan çevirileri içerir';
$string['access_denied'] = 'Erişim reddedildi';
$string['change_password'] = 'Şifreyi değiştir';
$string['change_password_confirmation_message'] = 'Şifreniz değiştirilmiştir.

Şifreyi değiştirime talebiniz
{REMOTE_ADDR} adresinden {DATETIME} tarihinde iletilmiştir.

Saygılarımla,

Otomatik webmaster.';
$string['change_password_confirmation_subject'] = 'Şifreyiniz başarılı bir şekilde değiştirilmiştir';
$string['contact_webmaster_for_new_password'] = 'Şifreyinizi değiştirmek için lütfen webmasterla  irtibata geçiniz.';
$string['do_you_want_to_try_forgot_password_procedure'] = 'Geçersiz bilgi. \'Şifremi unuttum\' yöntemini uygulamak istermisiniz?';
$string['email_address'] = 'E-Posta adresi';
$string['failure_sending_laissez_passer_mail'] = 'E-posta tekkullanımlık kodu gönderirken hata veriyor. Lütfen tekrar deneyiniz. Eğer aynı sorun devam ediyorsa webmasterla  irtibata geçiniz.';
$string['failure_sending_temporary_password'] = 'E-posta geçici şifreyi gönderirken hata veriyor. Lütfen tekrar deneyiniz. Eğer aynı sorun devam ediyorsa webmasterla  irtibata geçiniz.';
$string['forgot_password'] = 'Şifrenizi mi unuttunuz?';
$string['forgotten_password_mailmessage1'] = 'Buradan geçici şifre üretebilmek için gerekli olan tekkullanımlık kodu talep edebilirsiniz. Bağlantı adresini Web tarayıcısındaki URL/Adres Çubuğuna kopyalayın ve [Enter] a basin 

{AUTO_URL}

Alternatif olarak aşağıdaki bağlantı adresinede girebilirsiniz:

{MANUAL_URL}

burada kullanıcı adınızı ve asağıda belirtilen tekkullanımlık kodu giriniz.

{LAISSEZ_PASSER}

Bu tekkullanımlık kodun sadece {INTERVAL} dakika için geçerli olduğunu unutmayın.

Tekkullanımlık kod isteğiniz {REMOTE_ADDR} adresinden alındı​​.  

İyi şanslar!

Otomatik webmaster';
$string['forgotten_password_mailmessage2'] = 'Geçici şifreniz:

    {PASSWORD}

Bu geçici şifrenin sadece {INTERVAL} dakika için geçerli olduğunu unutmayın.

Geçici şifre talebiniz {REMOTE_ADDR} adresinden alındı​​.  


İyi şanslar!

Otomatik webmaster';
$string['home_page'] = '(Ana sayfa)';
$string['invalid_credentials_please_retry'] = 'Geçersiz bilgi, lütfen tekrar deneyiniz.';
$string['invalid_laissez_passer_please_retry'] = 'Geçersiz tekkullanımlık kod, lütfen tekrar deneyiniz.';
$string['invalid_new_passwords'] = 'Yeni şifreniz geçersiz. Olası nedenler:
İlk şifre ile ikinci şifre uymuyor;
Yeni şifre yeterince uzun değil(minimum {MIN_LENGTH}),
Şifrenin içerisindeki küçük harfler (minimum {MIN_LOWER}),
baş harfler (minumum {MIN_UPPER}) veya rakamlar (minimum {MIN_DIGIT})yeterli değil.
Yeni şifreniz eski şifrenizle aynı olabilir.
Lütfen İyi bir yeni şifre düşünmeye çalışın ve yeniden deneyin. 


';
$string['invalid_username_email_please_retry'] = 'Geçersiz kullanıcı adı ve E-Posta adresi, lütfen tekrar deneyiniz.';
$string['laissez_passer'] = 'Tekkullanımlık kod';
$string['login'] = 'Oturum aç';
$string['logout_forced'] = 'Oturumunuz zorunlu olarak kapatılmıştır.';
$string['logout_successful'] = 'Oturumunuz başarıyla  kapatılmıştır.';
$string['message_box'] = 'Mesaj kutusu';
$string['must_change_password'] = 'Artık şifrenizi değiştirmelisiniz.';
$string['new_password1'] = 'Yeni şifre';
$string['new_password2'] = 'Yeni şifreyi onayla';
$string['OK'] = 'Tamam';
$string['password'] = 'Şifre';
$string['password_changed'] = 'Şifrenizi başarıyla değiştirilmiştir.';
$string['please_enter_new_password_twice'] = 'Lütfen kullanıcı adınızı ve şifrenizi giriniz ve yeni şifrenizide iki kez girip tuşa basınız.';
$string['please_enter_username_email'] = 'Lütfen kullanıcı adınızı ve E-Posta adresinizi girip tuşa basınız.';
$string['please_enter_username_laissez_passer'] = 'Lütfen kullanıcı adınızı ve tekkullanımlık kodunuzu girip tuşa basınız.';
$string['please_enter_username_password'] = 'Lütfen kullanıcı adınızı ve şifrenizi girip tuşa basınız.';
$string['request_bypass'] = 'Geçiçi şifre talebiniz';
$string['request_laissez_passer'] = 'Oturum açabilmek için tekkullanımlık kod talebiniz';
$string['see_mail_for_further_instructions'] = 'Lütfen daha fazla talimat için E-Postanıza bakınız.';
$string['see_mail_for_new_temporary_password'] = 'Lütfen yeni geçiçi şifreniz için E-Postanıza bakınız.';
$string['too_many_change_password_attempts'] = 'Çok fazla başarısız şifre değiştirme girişiminde bulundunuz.';
$string['too_many_login_attempts'] = 'Çok fazla başarısız oturum açma girişiminde bulundunuz..';
$string['username'] = 'Kullanıcı adı';
$string['your_forgotten_password_subject1'] = 'Konu: tekkullanımlık oturum açma kodu talebiniz';
$string['your_forgotten_password_subject2'] = 'Konu: geçici şifre talebiniz';
?>