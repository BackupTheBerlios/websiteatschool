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

/** /program/languages/fa/loginlib.php
 *
 * Language: fa (‫فارسی)
 * Release:  0.90.4 / 2013061400 (2013-06-14)
 *
 * @author A. Darvishi <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_fa
 * @version $Id: loginlib.php,v 1.4 2013/06/14 19:59:53 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'دخول‌ به‌ سيستم‌،';
$string['translatetool_description'] = 'این فایل حاوی ترجمه ها کار کردن با ورود/خروج از سیستم';
$string['access_denied'] = 'دسترسی به منكر';
$string['change_password'] = 'تغییر اسم رمز';
$string['change_password_confirmation_message'] = 'اسم رمز شما تغییر داده شده است. رمز عبور درخواست تغییر مورد استقبال واقع شد از آدرس {remote_addr} به {datetime}.  مثل اینکه شما می خوريد، اتوماتيک مسوول سايت.';
$string['change_password_confirmation_subject'] = 'اسم رمز شما عوض شده بود با موفقیت پیوند برقرار کرد';
$string['contact_webmaster_for_new_password'] = 'لطفا تماس بگیرید بايد مسوول سايت به اسم رمز شما تغيير كرده اند.';
$string['do_you_want_to_try_forgot_password_procedure'] = 'نام کاربر نامعتبر. آیا شما میخواهید که سعی کنید به "روند" اسم رمز را فراموش کرده؟';
$string['email_address'] = 'آدرس ایمیل';
$string['failure_sending_laissez_passer_mail'] = 'عدم ارسال ایمیل با یک بار کد. لطفا سعی دوباره را کلیک کنید یا تماس با اين مسوول سايت اگر اين مشكل پابرجاست';
$string['failure_sending_temporary_password'] = 'عدم ارسال ایمیل با اسم رمز موقتی. لطفا سعی دوباره را کلیک کنید یا تماس با اين مسوول سايت اگر اين مشكل پابرجاست';
$string['forgot_password'] = 'اسم رمز خود را فراموش کرده اند؟';
$string['forgotten_password_mailmessage1'] = 'در اینجا یک لینک با یک بار که کد امکان می دهد که به درخواست جديد، اسم رمز موقتی. کپی کردن لینک زیر را در نوار آدرس مرورگر شما و مطبوعات [enter] : {auto_url} applications به این محل: {و} manual_url را وارد کنید نام کاربری و این یک بار کد: {laissez_passer} توجه داشته باشید که این کد معتبر است برای تنها {فاصله} دقیقه. درخواست برای این یک بار از این کد آدرس: {remote_addr} موفق باشيد! شما اتوماتيک مسوول سايت';
$string['forgotten_password_mailmessage2'] = 'در اينجا اسم رمز موقتی: {PASSWORD} توجه داشته باشید که این اسم رمز اسم رمز صحیح است زيرا تنها {فاصله} دقیقه. اين درخواست به اين اسم رمز{INTERVAL} موقتی از این آدرس: {remote_addr}
 موفق باشيد! 
 مسوول سايت';
$string['home_page'] = '(صفحه اول )';
$string['invalid_credentials_please_retry'] = 'نام کاربر نامعتبر است، لطفاً دوباره امتحان کنید.';
$string['invalid_laissez_passer_please_retry'] = 'نامعتبر است یک بار کد، لطفاً دوباره امتحان کنید.';
$string['invalid_new_passwords'] = 'اسم رمز جدید قابل قبول نبود. ممكن است دلايل: اين مسابقه اولين اسم رمز دوم نداشت; يكي از اين اسم رمز جدید نبود وقت کافی (حداقل {min_length} )، نه به اندازه کافی وجود حروف (حداقل {min_lower} )، حروف بزرگ (minumum {min_upper}) یا رقم (حداقل {min_digit}) یا اسم رمز جدید بود و همان سال يكي از شما. لطفا سعی کنید فکر می کنم از یک اسم رمز جدید و سعی دوباره را کلیک کنید.';
$string['invalid_username_email_please_retry'] = 'نام کاربری و آدرس ایمیل نامعتبر است، لطفاً سعی دوباره را کلیک کنید.';
$string['laissez_passer'] = 'یک بار کد';
$string['login'] = 'ورود به سیستم';
$string['logout_forced'] = 'زور وارد سیستم شما شوند.';
$string['logout_successful'] = 'شما با موفقیت وارد سیستم شوید';
$string['message_box'] = 'جعبه پیام';
$string['must_change_password'] = 'شما باید اسم رمز خود را تغییر دهید.';
$string['new_password1'] = 'اسم رمز جدید';
$string['new_password2'] = 'اسم رمز جدید را تأیید کنید';
$string['OK'] = 'خوبه.';
$string['password'] = 'اسم رمز';
$string['password_changed'] = 'اسم رمز شما با موفقیت تغییر داده شد.';
$string['please_enter_new_password_twice'] = 'لطفاً نام کاربر و اسم رمز اسم رمز جدید و همچنین دو بار و دکمه';
$string['please_enter_username_email'] = 'لطفاً نام کاربری و آدرس ایمیل و دکمه ای.';
$string['please_enter_username_laissez_passer'] = 'لطفاً نام کاربر و یک بار کد و دکمه';
$string['please_enter_username_password'] = 'لطفاً نام کاربر و اسم رمز وارد کرده و دکمه را فشار دهید.';
$string['request_bypass'] = 'درخواست اسم رمز موقتی';
$string['request_laissez_passer'] = 'درخواست یک بار کد ورود به سیستم';
$string['see_mail_for_further_instructions'] = 'لطفا ایمیل شما را ببینم برای راهنمایی بیشتر.';
$string['see_mail_for_new_temporary_password'] = 'لطفا شما که ایمیل جدید برای شما اسم رمز موقتی';
$string['too_many_change_password_attempts'] = 'تلاش زیادی برای تغییر رمز عبور.';
$string['too_many_login_attempts'] = 'من نيز از تلاش هاي ورود به سیستم.';
$string['username'] = 'نام کاربری';
$string['your_forgotten_password_subject1'] = 'عطف به : یک بار درخواست کد ورود به سیستم';
$string['your_forgotten_password_subject2'] = 'اسم رمز موقتی: درخواست مجدد';
?>