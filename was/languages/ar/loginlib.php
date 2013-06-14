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

/** /program/languages/ar/loginlib.php
 *
 * Language: ar (العربية)
 * Release:  0.90.4 / 2013061400 (2013-06-14)
 *
 * @author Said Taki <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_ar
 * @version $Id: loginlib.php,v 1.3 2013/06/14 19:59:50 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'الدخول';
$string['translatetool_description'] = 'هذا الملف يحتوي على ترجمات عن الاتصال / قطع الاتصال';
$string['access_denied'] = 'ونفى وصول';
$string['change_password'] = 'تغيير كلمة المرور';
$string['change_password_confirmation_message'] = 'تم تغيير كلمة السر الخاصة بك.

 وكان في استقبال طلب تغيير كلمة المرور
{REMOTE_ADDR}في  {DATETIME}.من عنوان

مع أطيب التحيات،

 المسؤول عن الموقع الخاص بك الآلي.';
$string['change_password_confirmation_subject'] = 'تم تغيير كلمة السر الخاصة بك.';
$string['contact_webmaster_for_new_password'] = 'يرجى الاتصال بمسؤول الموقع لتغيير كلمة المرور.';
$string['do_you_want_to_try_forgot_password_procedure'] = 'اسم المستخدم أو كلمة السر غير صحيحة. هل تريد استخدام الإجراء "هل نسيت كلمة السر"؟';
$string['email_address'] = 'البريد الإلكتروني';
$string['failure_sending_laissez_passer_mail'] = 'فشل في إرسال البريد الإلكتروني من رمز واحد متعدد الاستخدامات. حاول مرة أخرى، أو الاتصال بمدير الموقع إذا استمرت المشكلة.';
$string['failure_sending_temporary_password'] = 'فشل في إرسال البريد الإلكتروني كلمة مرور مؤقتة. حاول مرة أخرى، أو الاتصال بمدير الموقع إذا استمرت المشكلة.';
$string['forgot_password'] = 'نسيت كلمة المرور؟';
$string['forgotten_password_mailmessage1'] = 'هنا هو وجود صلة مع رمز لمرة واحدة التي تسمح لك لطلب جديد،
 كلمة السر المؤقتة. انسخ الرابط أدناه لشريط العنوان في المتصفح
 والصحافة [أدخل]:

     {AUTO_URL}

 بدلا من ذلك، يمكنك الذهاب إلى هذا الموقع:

     {MANUAL_URL}

 وأدخل اسم المستخدم الخاص بك، وهذا رمز لمرة واحدة:

     {جوازات مرور}

 علما بأن هذا القانون هو فقط صالحة لمدة دقيقة {INTERVAL}.

 وكان في استقبال طلب هذا الرمز لمرة واحدة من هذا العنوان:

     {REMOTE_ADDR}

 حظا سعيدا!

 المسؤول عن الموقع الخاص بك الآلي';
$string['forgotten_password_mailmessage2'] = 'هنا هو كلمة السر المؤقت:

     {PASSWORD}

 علما بأن كلمة المرور هذه صالحة لمدة دقائق فقط {INTERVAL}.

 وكان في استقبال طلب هذه كلمة مرور مؤقتة من هذا العنوان:

     {REMOTE_ADDR}

 حظا سعيدا!

 المسؤول عن الموقع الخاص بك الآلي';
$string['home_page'] = '(الصفحة الرئيسية)';
$string['invalid_credentials_please_retry'] = 'اسم المستخدم أو كلمة السر غير صحيحة، حاول مرة أخرى';
$string['invalid_laissez_passer_please_retry'] = 'تستخدم مرة واحدة رمز غير صالح، حاول مرة أخرى';
$string['invalid_new_passwords'] = ' وكانت كلمة السر الجديدة غير مقبولة. الأسباب المحتملة:
 فإن كلمة السر 1 لا تتطابق مع ثانية واحدة؛
 وكانت كلمة المرور الجديدة لا تكفي طويلة (الحد الأدنى {MIN_LENGTH})،
 لم تكن هناك قضية الرسائل أدنى بما فيه الكفاية (الحد الأدنى {MIN_LOWER})،
 رسائل الحالة العلوي (minumum MIN_UPPER {}) أو أرقام (الحد الأدنى {MIN_DIGIT})
 أو كان كلمة السر الجديدة هي نفسها باعتبارها واحدة القديم الخاص بك.
 يرجى محاولة للتفكير في كلمة مرور جديدة جيدة وإعادة المحاولة.';
$string['invalid_username_email_please_retry'] = 'اسم المستخدم غير صالح وعنوان البريد الإلكتروني، حاول مرة أخرى.';
$string['laissez_passer'] = 'تستخدم مرة واحدة رمز';
$string['login'] = 'دخول';
$string['logout_forced'] = 'اضطر قطع';
$string['logout_successful'] = 'الدخول ناجح';
$string['message_box'] = 'لمربع الرسالة';
$string['must_change_password'] = 'الآن يجب تغيير كلمة السر الخاصة بك.';
$string['new_password1'] = 'كلمة مرور جديدة';
$string['new_password2'] = 'تأكيد كلمة السر الجديدة';
$string['OK'] = 'موافق';
$string['password'] = 'كلمة السر';
$string['password_changed'] = 'تم تغيير كلمة السر الخاصة بك.';
$string['please_enter_new_password_twice'] = 'أدخل اسم المستخدم وكلمة المرور، ثم كلمة المرور الجديدة مرتين ثم اضغط على';
$string['please_enter_username_email'] = 'أدخل اسم المستخدم أو عنوان البريد الإلكتروني والضغط على زر.';
$string['please_enter_username_laissez_passer'] = 'أدخل اسم المستخدم ورمز واحد لاستخدام واضغط على زر.';
$string['please_enter_username_password'] = 'إدخال اسم المستخدم وكلمة السر، ثم اضغط على زر.';
$string['request_bypass'] = 'طلب كلمة مرور مؤقتة';
$string['request_laissez_passer'] = 'تطبيق واحد لاستخدام رمز';
$string['see_mail_for_further_instructions'] = 'التحقق من بريدك الالكتروني لمزيد من التعليمات';
$string['see_mail_for_new_temporary_password'] = 'فحص البريد كلمة السر الخاصة بك مؤقتة';
$string['too_many_change_password_attempts'] = 'الكثير من التجربة والخطأ لتغيير كلمة المرور';
$string['too_many_login_attempts'] = 'الكثير من التجربة والخطأ الصدد.';
$string['username'] = 'دخول';
$string['your_forgotten_password_subject1'] = 'رد: طلب تسجيل الدخول رمز لاستخدام مرة واحدة';
$string['your_forgotten_password_subject2'] = 'رد: طلب للحصول على كلمة مرور مؤقتة';
?>