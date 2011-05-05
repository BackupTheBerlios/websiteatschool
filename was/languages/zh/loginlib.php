<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2011 Vereniging Website At School, Amsterdam, <info@websiteatschool.eu>
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

/** /program/languages/zh/loginlib.php
 *
 * Language: zh (中文)
 * Release:  0.90.1 / 2011050500 (2011-05-05)
 *
 * @author Liu Jing Fang <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2011 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_zh
 * @version $Id: loginlib.php,v 1.1 2011/05/05 06:17:23 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = '登入';
$string['translatetool_description'] = '这个文件包含关于处理登入/登出的翻译';
$string['access_denied'] = '路径被拒绝';
$string['change_password'] = '修改密码';
$string['change_password_confirmation_message'] = '密码已更改

密码修改请求已于 {DATETIME} 从地址 {REMOTE_ADDR} 被接收。

祝好,

您的自动网络管理员';
$string['change_password_confirmation_subject'] = '密码已被成功更改';
$string['contact_webmaster_for_new_password'] = '更改密码请与网站管理员联系';
$string['do_you_want_to_try_forgot_password_procedure'] = '无效资质。你是否想尝试“忘记密码”程序？  ';
$string['email_address'] = '电子邮箱地址';
$string['failure_sending_laissez_passer_mail'] = '使用一次性编码发送电子邮件失败。请重试或与网络管理员联系。';
$string['failure_sending_temporary_password'] = '使用临时密码发送电子邮件失败。请重试或与网络管理员联系。';
$string['forgot_password'] = '忘记密码？';
$string['forgotten_password_mailmessage1'] = '这是一个一次性编码链接，该链接会使您获取到一个新的、临时性的密码。复制该链接到浏览器中的地址栏后按[Enter]:

    {AUTO_URL}

或者，您可以到以下地址：

    {MANUAL_URL}

然后输入您的用户名和该一次性编码:

    {LAISSEZ_PASSER}

注意此编码仅在{INTERVAL}分钟内有效。

该一次性编码的请求从以下地址中被接收:

    {REMOTE_ADDR}

祝好运!

您的自动网络管理员';
$string['forgotten_password_mailmessage2'] = '这是您的临时密码:

    {PASSWORD}

注意此编码仅在 {INTERVAL}分钟内有效。

该临时密码的请求从以下地址中被接收：

    {REMOTE_ADDR}

祝好运!

您的自动网络管理员';
$string['home_page'] = '(主页)';
$string['invalid_credentials_please_retry'] = '无效资质，请重试。';
$string['invalid_laissez_passer_please_retry'] = '无效一次性编码，请重试。';
$string['invalid_new_passwords'] = '您的新密码不被承认。可能的原因：
前后输入的密码不一致；
新密码长度不够（最短{MIN_LENGTH})
没有足够的小写字母（最少{MIN_LOWER}),大写字母（最少{MIN_UPPER}）或数字（最少{MIN_DIGIT}）
或您的新密码与旧密码一致。
请设计一个新密码并重试。
';
$string['invalid_username_email_please_retry'] = '无效用户名和电子邮件地址，请重试。';
$string['laissez_passer'] = '一次性编码';
$string['login'] = '登入';
$string['logout_forced'] = '您被强制登出。';
$string['logout_successful'] = '成功登出';
$string['message_box'] = '信息栏';
$string['must_change_password'] = '您此时必须更改密码';
$string['new_password1'] = '新密码';
$string['new_password2'] = '确认新密码';
$string['OK'] = '确定';
$string['password'] = '密码';
$string['password_changed'] = '您的密码已成功更改。';
$string['please_enter_new_password_twice'] = '请输入用户名和密码及您的新密码两次并按确定。';
$string['please_enter_username_email'] = '请输入用户名及电子邮件地址并按确定。';
$string['please_enter_username_laissez_passer'] = '请输入您的用户名及一次性编码并按确定。';
$string['please_enter_username_password'] = '请输入您的用户名和密码并按确定。';
$string['request_bypass'] = '请求临时密码';
$string['request_laissez_passer'] = '请求一次性登录编码';
$string['see_mail_for_further_instructions'] = '更多指导请查看您的电子邮件';
$string['see_mail_for_new_temporary_password'] = '请从您的电子邮件中查看新的临时密码。';
$string['too_many_change_password_attempts'] = '过多密码更改尝试';
$string['too_many_login_attempts'] = '过多次登录尝试';
$string['username'] = '用户名';
$string['your_forgotten_password_subject1'] = '回复：一次性登录编码请求';
$string['your_forgotten_password_subject2'] = '回复：临时密码请求';
?>