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

/** /program/languages/ru/loginlib.php
 *
 * Language: ru (Русский)
 * Release:  0.90.4 / 2013061400 (2013-06-14)
 *
 * @author Anastassia Blechko <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_ru
 * @version $Id: loginlib.php,v 1.1 2013/06/14 19:59:57 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Логин';
$string['translatetool_description'] = 'Данный файл содержит переводы, касающиеся процедуры входа/выхода';
$string['access_denied'] = 'Доступ запрещен';
$string['change_password'] = 'Измените пароль';
$string['change_password_confirmation_message'] = 'Ваш пароль изменён.		
Запрос на изменение пароля был получен с адреса {REMOTE_ADDR} в {DATETIME}.

С уважением,

Ваш автоматизированный вебмастер

';
$string['change_password_confirmation_subject'] = 'Ваш пароль был успешно изменён';
$string['contact_webmaster_for_new_password'] = 'Пожалуйста, обратитесь к вебмастеру, чтобы сменить свой пароль';
$string['do_you_want_to_try_forgot_password_procedure'] = 'Неверные данные. Хотите начать процедуру ´Забыли пароль´ ?';
$string['email_address'] = 'Адрес электронной почты';
$string['failure_sending_laissez_passer_mail'] = 'Произошла ошибка при отправке сообщения с разовым кодом. Пожалуйста, повторите попытку или обратитесь к вебмастеру, если проблема сохранится.';
$string['failure_sending_temporary_password'] = 'Произошла ошибка при отправке сообщения с временным паролем. Пожалуйста, повторите попытку или обратитесь к вебмастеру, если проблема сохранится.';
$string['forgot_password'] = 'Забыли пароль?';
$string['forgotten_password_mailmessage1'] = 'Данная ссылка содержит разовый код, который позволит Вам запросить новый, временный код. Скопируйте ссылку, находящуюся внизу, в строку адреса в Вашем браузере и нажмите [Enter]:
{AUTO_URL}

Вы можете также перейти на эту страницу:
{MANUAL_URL}
И введите своё имя пользователя и этот разовый код:	
{LAISSEZ_PASSER}

Обратите внимание, что этот код действителен только в течение {INTERVAL} минут. 
Запрос этого одноразового кода был получен с этого адреса:
{REMOTE_ADDR}		

Удачи!
Ваш автоматизированный вебмастер
';
$string['forgotten_password_mailmessage2'] = 'Это Ваш временный пароль:		
{PASSWORD}	

Обратите внимание, что этот пароль действителен только в течение {INTERVAL} минут. 		
Запрос этого временного пароля был получен с этого адреса:
{REMOTE_ADDR}		
	
Удачи!

Ваш автоматизированный вебмастер
';
$string['home_page'] = '(домашняя страница)';
$string['invalid_credentials_please_retry'] = 'Неверные данные. Пожалуйста, попытайтесь ещё раз.';
$string['invalid_laissez_passer_please_retry'] = 'Неверный разовый код. Пожалуйста, попытайтесь ещё раз.';
$string['invalid_new_passwords'] = 'Ваш новый пароль не принят. 
Возможные причины этого:
Первый пароль не соответствует второму;
Новый пароль недостаточно длинный (не менее {MIN_LENGTH};
Недостаточно строчных букв (не менее {MIN_LOWER}), заглавных букв (не менее {MIN_UPPER}) или цифр (не менее {MIN_DIGIT}) в пароле; или Ваш новый пароль совпадает со старым. Пожалуйста, придумайте другой пароль и попытайтесь ещё раз.	
';
$string['invalid_username_email_please_retry'] = 'Неверное имя пользователя и адрес электронной почты. Пожалуйста, попытайтесь ещё раз.';
$string['laissez_passer'] = 'Разовый код';
$string['login'] = 'Вход';
$string['logout_forced'] = 'Ваша работа в системе была завершена принудительно.';
$string['logout_successful'] = 'Вы успешно вышли из системы.';
$string['message_box'] = 'Окно сообщений';
$string['must_change_password'] = 'Теперь Вам необходимо изменить пароль.';
$string['new_password1'] = 'Новый пароль';
$string['new_password2'] = 'Подтвердите новый пароль';
$string['OK'] = 'OK';
$string['password'] = 'Пароль';
$string['password_changed'] = 'Ваш пароль был успешно изменён';
$string['please_enter_new_password_twice'] = 'Пожалуйста, введите свои имя пользователя и пароль, а также Ваш новый пароль дважды, затем нажмите кнопку.';
$string['please_enter_username_email'] = 'Пожалуйста, введите свои имя пользователя и адрес электронной почты, затем нажмите кнопку.';
$string['please_enter_username_laissez_passer'] = 'Пожалуйста, введите свои имя пользователя и разовый код, затем нажмите кнопку.';
$string['please_enter_username_password'] = 'Пожалуйста, введите свои имя пользователя и пароль, затем нажмите кнопку.';
$string['request_bypass'] = 'Запрос временного пароля';
$string['request_laissez_passer'] = 'Запрос разового кода для входа в систему';
$string['see_mail_for_further_instructions'] = 'Пожалуйста, откройте свою почту, чтобы получить дальнейшие инструкции.';
$string['see_mail_for_new_temporary_password'] = 'Пожалуйста, откройте свою почту, чтобы получить новый временный пароль.';
$string['too_many_change_password_attempts'] = 'Слишком много попыток сменить пароль	';
$string['too_many_login_attempts'] = 'Слишком много попыток войти в систему.	';
$string['username'] = 'Имя пользователя';
$string['your_forgotten_password_subject1'] = 'Касательно: Запрос разового кода для входа в систему';
$string['your_forgotten_password_subject2'] = ' Касательно: Запрос временного пароля';
?>