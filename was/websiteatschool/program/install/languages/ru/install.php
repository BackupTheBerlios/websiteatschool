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

/** /program/install/languages/ru/install.php
 *
 * Language: ru (Русский)
 * Release:  0.90.4 / 2013061400 (2013-06-14)
 *
 * @author Anastassia Blechko <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_ru
 * @version $Id: install.php,v 1.1 2013/06/14 20:00:33 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Установка';
$string['translatetool_description'] = 'Этот файл содержит переводы программы установки';
$string['websiteatschool_install'] = 'Установка Website@School';
$string['websiteatschool_logo'] = 'Логотип Website@School';
$string['help_name'] = 'помощь';
$string['help_description'] = 'Помощь (откроется в новом окне)';
$string['next'] = 'Дальше';
$string['next_accesskey'] = 'Д';
$string['next_title'] = 'Используйте [Alt-Д] или [Cmnd-Д] в качестве «быстрой клавиши» для этой кнопки';
$string['previous'] = 'Предыдущее';
$string['previous_accesskey'] = 'П';
$string['previous_title'] = 'Используйте [Alt-П] или [Cmnd-П] в качестве «быстрой клавиши» для этой кнопки	';
$string['cancel'] = 'Отменить';
$string['cancel_accesskey'] = 'О';
$string['cancel_title'] = 'Используйте [Alt-О] или [Cmnd-О] в качестве «быстрой клавиши» для этой кнопки	';
$string['ok'] = 'OK';
$string['ok_accesskey'] = 'K';
$string['ok_title'] = 'Используйте [Alt-К] или [Cmnd-К] в качестве «быстрой клавиши» для этой кнопки	';
$string['yes'] = 'Да';
$string['no'] = 'Нет';
$string['language_name'] = 'Русский';
$string['dialog_language'] = 'Язык';
$string['dialog_language_title'] = 'Выберите язык установки';
$string['dialog_language_explanation'] = 'Пожалуйста, выберите язык для использования во время процедуры установки.';
$string['language_label'] = 'Язык';
$string['language_help'] = '';
$string['dialog_installtype'] = 'Вид установки';
$string['dialog_installtype_title'] = 'Сделайте выбор между стандартной и пользовательской установками';
$string['dialog_installtype_explanation'] = 'Пожалуйста, выберите сценарий установки из списка, который приводится ниже';
$string['installtype_label'] = 'Сценарий установки	';
$string['installtype_help'] = 'Пожалуйста, выберите подходящий сценарий установки. <br> <strong> Стандартный </strong> означает просто установку с минимальным количеством вопросов, требующих реагирования <br><strong> Пользовательский</strong> дает Вам полный контроль над всеми параметрами установки.';
$string['installtype_option_standard'] = 'Стандартный';
$string['installtype_option_custom'] = 'Пользовательский';
$string['high_visibility_label'] = 'Высокая видимость	';
$string['high_visibility_help'] = 'Установите флажок, чтобы использовать текстовый интерфейс пользователя во время установки.';
$string['dialog_license'] = 'Лицензия';
$string['dialog_license_title'] = 'Прочтите и примите условия лицензии для данного программного обеспечения';
$string['dialog_license_explanation'] = 'Это программное обеспечение предоставляет Вам лицензию на право его использования, если Вы нижеследующие положения и условия прочитали, поняли и согласны с ними. При этом следует иметь в виду, что английская версия данного лицензионного соглашения применяется даже при установке программного обеспечения с использованием какого-либо другого языка.';
$string['dialog_license_i_agree'] = 'Я согласен';
$string['dialog_license_you_must_accept'] = 'Вы должны принять лицензионное соглашение, введя "<b>{IAGREE}</b>" (без кавычек) в предусмотренное для этого поле (ниже).';
$string['dialog_database'] = 'База данных';
$string['dialog_database_title'] = 'Введите информацию о сервере базы данных';
$string['dialog_database_explanation'] = 'Пожалуйста, введите свойства Вашего сервера базы данных в поле, указанном ниже.';
$string['db_type_label'] = 'Тип';
$string['db_type_help'] = 'Выберите один из доступных типов базы данных.';
$string['db_type_option_mysql'] = 'MySQL';
$string['db_server_label'] = 'Сервер';
$string['db_server_help'] = 'Это адрес сервера базы данных, как правило, <strong> localhost</strong>.  Другие примеры: <strong> mysql.example.org</strong> или <strong> example.dbserver.provider.net: 3306 </ STRONG>.';
$string['db_username_label'] = 'Имя пользователя';
$string['db_username_help'] = 'Для подключения к серверу базы данных требуется дейсвительная комбинация имя пользователя/пароль. Пожалуйста, используйте не корневую учетную запись сервера базы данных, а какую-либо менее привилегированную, как например, <strong> wasuser </strong> или <strong> example_wwwa </strong>.';
$string['db_password_label'] = 'Пароль';
$string['db_password_help'] = 'Для подключения к серверу базы данных требуется действительная комбинация имя пользователя/пароль.';
$string['db_name_label'] = 'Имя базы данных';
$string['db_name_help'] = 'Это имя базы данных для пользования. Обратите внимание, что такая база данных уже должна существовать; предлагаемая программа установки не предназначена для создания базы данных (по соображениям безопасности). Примеры: <strong>www</strong> или <strong> example_www</strong>.';
$string['db_prefix_label'] = 'Префикс';
$string['db_prefix_help'] = 'Все имена таблиц в базе данных начинаются с этого префикса. Это позволяет осуществлять в одной и той же базе данных множество установок. Обратите внимание, что префикс должен начинаться с буквы. Примеры: <strong> was_</strong> или <strong> cms2_</strong>.';
$string['dialog_cms'] = 'Веб-сайт';
$string['dialog_cms_title'] = 'Введите необходимую информацию о веб-сайте';
$string['dialog_cms_explanation'] = 'Пожалуйста, введите необходимую информацию о веб-сайте в поле, предусмотренное для этого ниже.';
$string['cms_title_label'] = 'Название веб-сайта';
$string['cms_title_help'] = 'Имя Вашего веб-сайта.';
$string['cms_website_from_address_label'] = 'От: адрес електронной почты';
$string['cms_website_from_address_help'] = 'Этот адрес электронной почты используется для исходящей корреспонденции, например, для предупреждений и напоминания пароля.';
$string['cms_website_replyto_address_label'] = 'Ответить на: адрес електронной почты';
$string['cms_website_replyto_address_help'] = 'Этот адрес электронной почты будет добавлен в исходящую корреспонденцию и может быть использован для указания почтового ящика, где ответы будут действительно прочитаны (Вами), то есть не будут отвергнуты (программным обеспечением веб-сервера).';
$string['cms_dir_label'] = 'Директорий веб-сата';
$string['cms_dir_help'] = 'Это путь к директорию, который содержит index.php и config.php, например, <strong>/home/httpd/htdocs</strong> или <strong>C:\Program Files\Apache Group\Apache\htdocs</strong>';
$string['cms_www_label'] = 'URL веб-сайта';
$string['cms_www_help'] = 'Это главный URL, который ведет к Вашему сайту, то есть место, где можно посетить index.php. Примерами являются:<strong>http://www.example.org</strong> или <strong>https://example.org:443/schoolsite</strong>.';
$string['cms_progdir_label'] = 'Директорий программы';
$string['cms_progdir_help'] = 'Это путь к директорию, который содержит програмные файлы Website@School (обычно суб-директорий <strong> program</strong> из директория веб-сайта). Например: <strong>/home/httpd/htdocs/program</strong> или <strong>C:\Program Files\Apache Group\Apache\htdocs\program</strong>.';
$string['cms_progwww_label'] = 'URL программы';
$string['cms_progwww_help'] = 'Это URL, который ведёт к директорию программы (обычно это адрес веб-сайта, после которого следует <strong>/ program</strong>). Примеры: <strong>http://www.example.org/program</strong> или <strong>https://example.org:443/schoolsite/program</strong>.';
$string['cms_datadir_label'] = 'Директорий данных';
$string['cms_datadir_help'] = 'Это каталог, который содержит загруженные и прочие файлы данных. Очень важно, чтобы такой каталог находился за пределами корня документа, то есть чтобы к нему не было прямого доступа при помощи браузера. Обратите внимание, что веб-сервер должен иметь разрешения, необходимые для чтения, создания и записи файлов. Например: <strong>/home/httpd/wasdata</strong> или <strong>C:\Program Files\Apache Group\Apache\wasdata</strong>.';
$string['cms_demodata_label'] = 'Заполнение базы данных';
$string['cms_demodata_help'] = 'Установите флажок, если Вы хотите начать создание своего нового сайта с помощью демонстрационных данных.';
$string['cms_demodata_password_label'] = 'Пароль для демонстрационного доступа';
$string['cms_demodata_password_help'] = 'Тот же пароль доступа для демонстрации будет назначен для <em> все</em> демонстрации аккаунтов пользователей. Пожалуйста, выберите надёжный пароль: он должен включать в себя не менее 8 прописных и строчных букв, а также цифр. Это поле можно оставить пустым, если Вы ранее не установили флажок для функции «Заполнение базы данных", находящейся вверху.';
$string['dialog_user'] = 'Аккаунт пользователя';
$string['dialog_user_title'] = 'Создайте первый аккаунт';
$string['dialog_user_explanation'] = 'Пожалуйста, введите информацию к первой учетной записи пользователя для этого нового веб-сайта. Имейте в виду, что обладатель этого аккаунта будет наделён полными правами администратора и всеми, какие только возможны, разрешениями, так что любой, имеющий доступ к такому аккаунту, сможет делать что угодно.';
$string['user_full_name_label'] = 'Полное имя';
$string['user_full_name_help'] = 'Пожалуйста, введите свое имя или, если угодно, другое (функциональное) имя, например, <strong>Wilhelmina Bladergroen</strong> или <strong> Веб-мастер</strong>.';
$string['user_username_label'] = 'Имя пользователя, логин';
$string['user_username_help'] = 'Пожалуйста, введите имя, которое Вы хотите использовать для этой учетной записи. Вы должны вводить это имя каждый раз, когда вы хотите войти. Например: <strong>wblade</strong> или <strong>веб-мастер</strong>.';
$string['user_password_label'] = 'Пароль';
$string['user_password_help'] = 'Пожалуйста, выберите надёжный пароль: он должен включать в себя не менее 8 прописных и строчных букв, цифр, а также специальных символов, таких как % (проценты), = (равно), / (слэш, косая черта), и . (точка). Не следует сообщать свой пароль другим лицам; вместо этого для своих коллег рекомендуется создать дополнительные учетные записи.';
$string['user_email_label'] = 'Адрес электронной почты';
$string['user_email_help'] = 'Пожалуйста, введите здесь свой адрес электронной почты. Этот адрес нужен на тот случай, если Вам понадобится запросить новый пароль. Убедитесь, что только Вы имеете доступ к этому почтовому ящику (не следует пользоваться общим почтовым ящиком). Примеры: <strong>wilhelmina.bladergroen@example.org</strong> или <strong> webmaster@example.org</strong>.';
$string['dialog_compatibility'] = 'Совместимость';
$string['dialog_compatibility_title'] = 'Проверьте совместимость';
$string['dialog_compatibility_explanation'] = 'Ниже приведен обзор обязательных и желательных параметров. Прежде чем продолжить, Вам необходимо убедиться в том, что требования выполнены.';
$string['compatibility_label'] = 'Тест';
$string['compatibility_value'] = 'Значение';
$string['compatibility_result'] = 'Результат';
$string['compatibility_ok'] = 'OK';
$string['compatibility_warning'] = 'ПРЕДУПРЕЖДЕНИЕ';
$string['compatibility_websiteatschool_version_label'] = 'Website@School';
$string['compatibility_websiteatschool_version_check'] = '(проверить)';
$string['compatibility_websiteatschool_version_value'] = 'версия {RELEASE} ({VERSION}) {RELEASE_DATE}';
$string['compatibility_websiteatschool_version_check_title'] = 'Проверьте на наличие более поздних версий Website@School';
$string['compatibility_phpversion_label'] = 'PHP-версия';
$string['compatibility_phpversion_obsolete'] = 'PHP версия устарела';
$string['compatibility_phpversion_too_old'] = 'PHP версия устарела: требуется как минимум {MIN_VERSION}';
$string['compatibility_php_safemode_label'] = 'PHP безопасный режим';
$string['compatibility_php_safemode_warning'] = 'Безопасный режим включен. Пожалуйста, выключите его в php.ini';
$string['compatibility_webserver_label'] = 'Веб-сервер';
$string['compatibility_autostart_session_label'] = 'Автоматический запуск сессии';
$string['compatibility_autostart_session_fail'] = 'Автоматический запуск сессии включен. Пожалуйста, выключите его в php.ini';
$string['compatibility_file_uploads_label'] = 'Загрузка файлов';
$string['compatibility_file_uploads_fail'] = 'Загрузка файлов отключена. Пожалуйста, включите её в php.ini';
$string['compatibility_database_label'] = 'Сервер базы данных';
$string['compatibility_clamscan_label'] = 'Clamscan антивирус';
$string['compatibility_clamscan_not_available'] = '(не доступен)';
$string['compatibility_gd_support_label'] = 'GD Поддержка';
$string['compatibility_gd_support_none'] = 'GD не поддерживается';
$string['compatibility_gd_support_gif_readonly'] = 'Только для чтения';
$string['compatibility_gd_support_details'] = '{VERSION} (GIF: {GIF}, JPG: {JPG}, PNG: {PNG})';
$string['dialog_confirm'] = 'Подтверждение';
$string['dialog_confirm_title'] = 'Подтвердите настройки';
$string['dialog_confirm_explanation'] = 'Вы собираетесь установить свой новый веб-сайт. Тщательно проверьте приведенные ниже настройки конфигурации, после чего нажмите [Next] ([Дальше]), чтобы начать сам процесс установки. Это может занять некоторое время.';
$string['dialog_confirm_printme'] = 'Совет: распечатайте эту страницу и сохраните ее в печатном виде для дальнейшего использования.';
$string['dialog_cancelled'] = 'Отменено';
$string['dialog_cancelled_title'] = '';
$string['dialog_cancelled_explanation'] = 'Установка программы Website@School была отменена. Нажмите на кнопку ниже, чтобы повторить, или нажмите на кнопку помощи, чтобы прочитать руководство.';
$string['dialog_finish'] = 'Закончить';
$string['dialog_finish_title'] = 'Завершите процедуру установки';
$string['dialog_finish_explanation_0'] = 'Установка Website@School {VERSION} уже почти завершена. <p> Есть две вещи, которые осталось сделать: <ol><li> Теперь Вам надо {AHREF} скачать{A} файл config.php, и <li> Вы должны поместить файл config.php в <tt><strong>{CMS_DIR}</strong></tt>.</ol> После того, как config.php будет на месте, Вы можете закрыть программу установки, нажав на кнопку [ОК], находящуюся ниже.';
$string['dialog_finish_explanation_1'] = 'Теперь установка Website@School {VERSION} завершена. <p> Вы можете закрыть программу установки, нажав на кнопку [ОК], которая находится ниже.';
$string['dialog_finish_check_for_updates'] = 'При желании Вы можете перейти по ссылке, которая приводится ниже, чтобы проверить программу на наличие обновлений (ссылка откроется в новом окне).';
$string['dialog_finish_check_for_updates_anchor'] = 'Проверьте наличие обновлений для Website@School';
$string['dialog_finish_check_for_updates_title'] = 'Проверьте состояние Вашей версии Website@School';
$string['jump_label'] = 'Перейдите на';
$string['jump_help'] = 'Выберите место, куда Вы хотите перейти после нажатия на кнопку [ОК], которая находится ниже.';
$string['dialog_download'] = 'Загрузите config.php	';
$string['dialog_download_title'] = 'Загрузите config.php	 на Ваш компьютер	';
$string['dialog_unknown'] = 'Неизвестно';
$string['error_already_installed'] = 'Ошибка: Website@School уже установлена';
$string['error_wrong_version'] = 'Ошибка: неправильный номер версии. Новую ли версию Вы загрузили при установке?';
$string['error_fatal'] = 'Фатальная ошибка {ERROR}: пожалуйста, свяжитесь с <{EMAIL}>, чтобы запросить помощь';
$string['error_php_obsolete'] = 'Ошибка: версия PHP устарела';
$string['error_php_too_old'] = 'Ошибка: версия PHP ({VERSION}) устарела: воспользуйтесь по крайней мере версией {MIN_VERSION}';
$string['error_not_dir'] = 'Ошибка: {FIELD}: директорий не существует: {DIRECTORY}';
$string['warning_switch_to_custom'] = 'Внимание: переход на пользовательскую установку, чтобы можно было устранить ошибки';
$string['error_not_create_dir'] = 'Ошибка: {FIELD}: директорий не может быть создан: {DIRECTORY}';
$string['error_db_unsupported'] = 'Ошибка: база данных {DATABASE} в настоящее время не поддерживается';
$string['error_db_cannot_connect'] = 'Ошибка: не удалось установить соединение с сервером базы данных';
$string['error_db_cannot_select_db'] = 'Ошибка: невозможно открыть базу данных';
$string['error_invalid_db_prefix'] = 'Ошибка: {FIELD}: должно начинаться с буквы, может содержать только буквы, цифры или знаки подчеркивания';
$string['error_db_prefix_in_use'] = 'Ошибка: {FILE} уже используется: {PREFIX}';
$string['error_time_out'] = 'Фатальная ошибка: тайм-аут';
$string['error_db_parameter_empty'] = 'Ошибка: пустые параметры базы данных неприемлемы';
$string['error_db_forbidden_name'] = 'Ошибка: {FIELD}: это имя неприемлемо: {NAME}';
$string['error_too_short'] = 'Ошибка: {FIELD}: строка слишком короткая (минимальная длина = {MIN})';
$string['error_too_long'] = 'Ошибка: {FIELD}: строка слишком длинная (максимальная длина = {MAX})';
$string['error_invalid'] = 'Ошибка: {FIELD}: недопустимое значение';
$string['error_bad_password'] = 'Ошибка: {FIELD}: Значение неприемлемо; минимальные требования: цифры: {MIN_DIGIT}, строчные буквы: {MIN_LOWER}, прописные буквы: {MIN_UPPER}';
$string['error_bad_data'] = '{MENU_ITEM}: обнаружены ошибки; пожалуйста, сначала исправьте их (через меню)';
$string['error_file_not_found'] = 'Ошибка: не удается найти файл: {FILENAME}';
$string['error_create_table'] = 'Ошибка: невозможно создать таблицу: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_insert_into_table'] = 'Ошибка: не удалось поместить данные в таблицу: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_update_config'] = 'Ошибка: не удалось обновить конфигурацию: {CONFIG} ({ERRNO}/{ERROR})';
$string['warning_no_manifest'] = 'Предупреждение: пустой манифест или нет манифеста для {ITEM}	';
$string['error_install_demodata'] = 'Ошибка: не удается установить демонстрационные данные';
$string['error_directory_exists'] = 'Ошибка: {FIELD}: каталог уже существует: {DIRECTORY}';
$string['error_nameclash'] = 'Ошибка: {FIELD}: пожалуйста, измените имя {USERNAME}; оно уже используется в качестве демонстрационной учетной записи пользователя';
$string['warning_mysql_obsolete'] = 'Внимание: версия \'{VERSION}\' из MySQL устарела и она не поддерживает UTF-8. Пожалуйста, обновите MySQL';
?>