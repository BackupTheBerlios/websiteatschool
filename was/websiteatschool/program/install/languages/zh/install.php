<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2012 Vereniging Website At School, Amsterdam, <info@websiteatschool.eu>
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

/** /program/install/languages/zh/install.php
 *
 * Language: zh (中文)
 * Release:  0.90.3 / 2012041700 (2012-04-17)
 *
 * @author Liu Jing Fang <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2012 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_zh
 * @version $Id: install.php,v 1.4 2012/04/17 14:52:18 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = '安装';
$string['translatetool_description'] = '这个文件包含安装程序的翻译';
$string['websiteatschool_install'] = '网页@学校安装';
$string['websiteatschool_logo'] = '网页@学校标志';
$string['help_name'] = '帮助';
$string['help_description'] = '帮助(在一个新窗口中打开）';
$string['next'] = '下一个';
$string['next_accesskey'] = '下';
$string['next_title'] = '用 [Alt-N] 或[Cmnd-N] 作为此键的键盘快捷按钮';
$string['previous'] = '之前';
$string['previous_accesskey'] = '前';
$string['previous_title'] = '用[Alt-P] 或 [Cmnd-P] 作为此键的键盘快捷按钮';
$string['cancel'] = '取消';
$string['cancel_accesskey'] = '取';
$string['cancel_title'] = '用 [Alt-C] 或 [Cmnd-C] 作为此键的键盘快捷键';
$string['ok'] = '确定';
$string['ok_accesskey'] = '确';
$string['ok_title'] = '用 [Alt-K] 或[Cmnd-K] 作为此键的键盘快捷按钮';
$string['yes'] = '是';
$string['no'] = '否';
$string['language_name'] = '中文';
$string['dialog_language'] = '语言';
$string['dialog_language_title'] = '选择安装语言';
$string['dialog_language_explanation'] = '请选择安装程序过程中使用的语言';
$string['language_label'] = '语言';
$string['language_help'] = '';
$string['dialog_installtype'] = '安装类型';
$string['dialog_installtype_title'] = '选择标准安装或定制安装';
$string['dialog_installtype_explanation'] = '请在以下表中选择安装模式';
$string['installtype_label'] = '安装模式';
$string['installtype_help'] = '请选择合适的安装模式。<br> <strong>标准</strong> 意味着便捷安装配以最少量的问题，<br> <strong>定制</strong> 给您所有安装选项的控制权。';
$string['installtype_option_standard'] = '标准';
$string['installtype_option_custom'] = '定制';
$string['high_visibility_label'] = '高可见度';
$string['high_visibility_help'] = '勾画方格以在安装过程中使用一个仅限文字的用户干涉';
$string['dialog_license'] = '资质';
$string['dialog_license_title'] = '阅读并为此软件认可资质';
$string['dialog_license_explanation'] = '这个软件只有在您阅读，明白，并且同意以下条件时授权于您。注意此资质的英文版本同意书生效，尽管您使用的是其他语言安装此软件。';
$string['dialog_license_i_agree'] = '我同意';
$string['dialog_license_you_must_accept'] = '您必须通过在以下方格中输入 "<b>{IAGREE}</b>" （不加引号）来接受资质许可。';
$string['dialog_database'] = '数据库';
$string['dialog_database_title'] = '输入数据库服务商的信息';
$string['dialog_database_explanation'] = '请在以下部分输入您的数据库供应商的属性';
$string['db_type_label'] = '类型';
$string['db_type_help'] = '选择可用的数据库类型之一';
$string['db_type_option_mysql'] = 'MySQL';
$string['db_server_label'] = '服务商';
$string['db_server_help'] = '这是数据库服务商的地址，通常<strong>本地户主</strong>.其他例子：</strong>mysql.example.org</strong>或者<strong>example.dbserver.provider.net:3306</strong>.';
$string['db_username_label'] = '用户名';
$string['db_username_help'] = '需要一个有效的用户名/密码-结合以链接到数据库服务商。请不要使用数据库服务商的根账户而使用一个略低级别的，例如<strong>wasuser</strong> or <strong>example_wwwa</strong>.';
$string['db_password_label'] = '密码';
$string['db_password_help'] = '需要一个有效的用户名/密码以链接到数据库服务商';
$string['db_name_label'] = '数据库名称';
$string['db_name_help'] = '这是要使用的数据库的名称。注意这个数据库应该已经存在；此安装程序不是为创建数据库设计（安全考虑）。例如<strong>www</strong> or <strong>example_www</strong>.';
$string['db_prefix_label'] = '前缀';
$string['db_prefix_help'] = '所有数据库中的标题名称以此前缀开头。这使得在同一个数据库中完成多级安装成为可能。注意前缀必须以字母开头。';
$string['dialog_cms'] = '网页';
$string['dialog_cms_title'] = '输入必要的网页信息';
$string['dialog_cms_explanation'] = '请在以下部分输入必要的网页信息';
$string['cms_title_label'] = '网页标题';
$string['cms_title_help'] = '您的网址的姓名';
$string['cms_website_from_address_label'] = '来自：地址';
$string['cms_website_from_address_help'] = '此电子邮件地址用于输出的邮件，例如.警告及密码提醒';
$string['cms_website_replyto_address_label'] = '回复-至：地址';
$string['cms_website_replyto_address_help'] = '此电子邮件地址会被加入到输出的邮件中并用于指定一个（由您）可以读到回复的邮箱并且不被（网络软件服务商）删除。';
$string['cms_dir_label'] = '网页导引';
$string['cms_dir_help'] = '这是持有 index.php和 config.php的通往目录的路径，例如<strong>/home/httpd/htdocs</strong>或 <strong>C:\Program Files\Apache Group\Apache\htdocs</strong>.';
$string['cms_www_label'] = '网页URL';
$string['cms_www_help'] = '这是主要使您通往您的网站的URL链接。例如index.php可以被访问的地址。举例 <strong>http://www.example.org</strong> 或 <strong>https://example.org:443/schoolsite</strong>.';
$string['cms_progdir_label'] = '程序目录';
$string['cms_progdir_help'] = '这是通向网页@学校的程序文件目录的路径（通常是网页目录的次级目录<strong>程序</strong> ）。例如：strong>/home/httpd/htdocs/program</strong> 或<strong>C:\Program Files\Apache Group\Apache\htdocs\program</strong>.';
$string['cms_progwww_label'] = '程序URL';
$string['cms_progwww_help'] = '这是通向程序目录的URL（通常是跟随有<strong>/程序</strong>的网页URL）。举例：<strong>http://www.example.org/program</strong> 或<strong>https://example.org:443/schoolsite/program</strong>.';
$string['cms_datadir_label'] = '数据目录';
$string['cms_datadir_help'] = '这是包含有上载文件和其他数据文件的目录。非常重要的是此目录应当位于文件主干之外，即不能直接通过浏览器进入。注意网络供应商必须获得足够的许可以在此阅读、创建和撰写文件。例如：<strong>/home/httpd/wasdata</strong> 或<strong>C:\Program Files\Apache Group\Apache\wasdata</strong>.';
$string['cms_demodata_label'] = '填充数据库';
$string['cms_demodata_help'] = '勾画此方格如果您想使用演示数据开始您的新网页';
$string['cms_demodata_password_label'] = '演示密码';
$string['cms_demodata_password_help'] = '同样的演示密码将会被指定到<em>all</em>的演示用户账户。请选择一个好的密码：至少8个字符，包含大小写及数字。如果您没有勾画以上‘填充数据库’的方格，在此留空。';
$string['dialog_user'] = '用户账户';
$string['dialog_user_title'] = '创建第一个用户';
$string['dialog_user_explanation'] = '请为此新网站输入第一个用户账户的信息。注意此账户会有全部的管理员特权及所有可能的允许，即任何持有此通道的人可以做任何事情。';
$string['user_full_name_label'] = '全名';
$string['user_full_name_help'] = '请输入您的名称，或者，如果您原意，另一个（功能）名称，例如<strong>Wilhelmina Bladergroen</strong>或<strong>Master Web</strong>.';
$string['user_username_label'] = '用户名';
$string['user_username_help'] = '请输入您想为此账户使用的登录名。您需要在每次登陆时输入此用户名。例如： <strong>wblade</strong> 或 <strong>webmaster</strong>.';
$string['user_password_label'] = '密码';
$string['user_password_help'] = '请选择一个好的密码：至少包含8个字符，包含大小写，数字及特殊字符如%（百分比），=（等号），/（斜杠）和 . (点）。不要与其他人共享密码，而为您的同时创建额外的账户。';
$string['user_email_label'] = '电子邮件地址';
$string['user_email_help'] = '请在此输入电子邮件地址。您无论何时申请新密码时都要使用此地址。确定只有您可以进入此邮箱（不要使用一个共享的邮箱）。举例：<strong>wilhelmina.bladergroen@example.org</strong> or <strong>webmaster@example.org</strong>.';
$string['dialog_compatibility'] = '兼容性';
$string['dialog_compatibility_title'] = '检查兼容性';
$string['dialog_compatibility_explanation'] = '以下是需要和偏好设定的预览。您要取保在您继续之前寻求得到满足。';
$string['compatibility_label'] = '测试';
$string['compatibility_value'] = '数值';
$string['compatibility_result'] = '结果';
$string['compatibility_ok'] = '确';
$string['compatibility_warning'] = '警告';
$string['compatibility_websiteatschool_version_label'] = '网页@学校';
$string['compatibility_websiteatschool_version_check'] = '(检查)';
$string['compatibility_websiteatschool_version_value'] = '版本 {RELEASE} ({VERSION}) {RELEASE_DATE}';
$string['compatibility_websiteatschool_version_check_title'] = '检查后期版本的网页@学校';
$string['compatibility_phpversion_label'] = 'PHP 版本';
$string['compatibility_phpversion_obsolete'] = 'PHP版本过时';
$string['compatibility_phpversion_too_old'] = 'PHP 版本过旧: 最低为 {MIN_VERSION}';
$string['compatibility_php_safemode_label'] = 'PHP安全模式';
$string['compatibility_php_safemode_warning'] = '安全模式为开。请在php.ini中将其关闭';
$string['compatibility_webserver_label'] = '网页服务商';
$string['compatibility_autostart_session_label'] = '自动部分开始';
$string['compatibility_autostart_session_fail'] = '自动部分开始为开。请在php.ini中将其关闭。';
$string['compatibility_file_uploads_label'] = '文件上传';
$string['compatibility_file_uploads_fail'] = '文件上传为关。请在php.ini中专为开';
$string['compatibility_database_label'] = '数据库服务商';
$string['compatibility_clamscan_label'] = 'Clamscan防病毒';
$string['compatibility_clamscan_not_available'] = '(不可用)';
$string['compatibility_gd_support_label'] = 'GD帮助';
$string['compatibility_gd_support_none'] = 'GD不被支持';
$string['compatibility_gd_support_gif_readonly'] = '只读';
$string['compatibility_gd_support_details'] = '{VERSION} (GIF: {GIF}, JPG: {JPG}, PNG: {PNG})';
$string['dialog_confirm'] = '确认';
$string['dialog_confirm_title'] = '确认设置';
$string['dialog_confirm_explanation'] = '你即将安装新网页。仔细查看下面的配置设置之后按[下一步]正式开始安装进程。这可能需要一段时间。';
$string['dialog_confirm_printme'] = '小贴士：打印此页并留存以备日后参考';
$string['dialog_cancelled'] = '取消';
$string['dialog_cancelled_title'] = '';
$string['dialog_cancelled_explanation'] = '网页@学校的安装已被取消。按以下按键以重试或点击帮助按钮阅读安装手册。';
$string['dialog_finish'] = '完成';
$string['dialog_finish_title'] = '完成安装过程';
$string['dialog_finish_explanation_0'] = '网页@学校{VERSION} 的安装已即将完成。<p>还有两件事情要做：<ol><li>您现在需要{AHREF}下载{A}文件config.php和<li>.您需要将文件 config.php置于<tt><strong>{CMS_DIR}</strong></tt>.</ol>一旦config.php到位，您可以通过按下面的[确定]按钮关闭安装程序。';
$string['dialog_finish_explanation_1'] = ' 网页@学校{VERSION} 的安装已经完成。<p>您可以通过按以下的[确定]按钮关闭安装程序。';
$string['dialog_finish_check_for_updates'] = '如果您愿你，您可以通过以下链接查看更新（链接会在新窗口中打开）。';
$string['dialog_finish_check_for_updates_anchor'] = '检查网页@学校更新';
$string['dialog_finish_check_for_updates_title'] = '检查您版本网页@学校的状态';
$string['jump_label'] = '跳至';
$string['jump_help'] = '点击一下的[确定]按钮后选择您想前往的位置';
$string['dialog_download'] = '下载config.php';
$string['dialog_download_title'] = '下载config.php到您的电脑';
$string['dialog_unknown'] = '未知';
$string['error_already_installed'] = '错误：网页@学校已经安装';
$string['error_wrong_version'] = '错误：错误的版本号。您是否在安装过程中下载了一个新版本？';
$string['error_fatal'] = '严重错误{ERROR}：请联系<{EMAIL}> 获取帮助';
$string['error_php_obsolete'] = '错误：PHP的版本过旧';
$string['error_php_too_old'] = '错误：PHP ({VERSION}) 的版本过旧：至少使用版本{MIN_VERSION}';
$string['error_not_dir'] = '错误：{FIELD}:目录不存在：{DIRECTORY}';
$string['warning_switch_to_custom'] = '警告：切换到常规安装以更改错误';
$string['error_not_create_dir'] = '错误：{FIELD}:目录不可以被创建：{DIRECTORY}';
$string['error_db_unsupported'] = '错误：数据库{DATABASE}当前不被支持';
$string['error_db_cannot_connect'] = '错误：不能与数据库服务商建立联系';
$string['error_db_cannot_select_db'] = '错误：不能打开数据库';
$string['error_invalid_db_prefix'] = '错误：{FIELD}:必须以一个字母开头，可以只包含字母，数字或下划线';
$string['error_db_prefix_in_use'] = '错误：{FIELD}：已经使用中：{PREFIX}';
$string['error_time_out'] = '严重错误：超时';
$string['error_db_parameter_empty'] = '错误：不可以有空白的数据库参数';
$string['error_db_forbidden_name'] = '错误: {FIELD}:此名称不被接受: {NAME}';
$string['error_too_short'] = '错误: {FIELD}: 字符串过短 (mimimum = {MIN})';
$string['error_too_long'] = '错误: {FIELD}: 字符串过长 (maximum = {MAX})';
$string['error_invalid'] = '错误: {FIELD}: 无效值';
$string['error_bad_password'] = '错误: {FIELD}: 数值不被接受; 最小需求为: 数字: {MIN_DIGIT}, 小写字母: {MIN_LOWER}, 大写字母: {MIN_UPPER}';
$string['error_bad_data'] = '{MENU_ITEM}: 发现错误, 请先将其更改 (通过菜单)';
$string['error_file_not_found'] = '错误：找不到文件 {FILENAME}';
$string['error_create_table'] = '错误：无法创建表格：{TABLENAME} ({ERRNO}/{ERROR})';
$string['error_insert_into_table'] = '错误：无法将数据填入到表格：{TABLENAME} ({ERRNO}/{ERROR})';
$string['error_update_config'] = '错误：不能更新配置：{CONFIG} ({ERRNO}/{ERROR})';
$string['warning_no_manifest'] = '警告：空白表单或没有{ITEM}的表单';
$string['error_install_demodata'] = '错误：无法安装演示数据';
$string['error_directory_exists'] = '错误：{FIELD}: 目录已经存在：{DIRECTORY}';
$string['error_nameclash'] = '错误： {FIELD}: 请更改名称 {USERNAME}; 已经被用于演示用户帐户';
$string['warning_mysql_obsolete'] = 'Warning: version \'{VERSION}\' of MySQL is obsolete and it does not support UTF-8. Please upgrade MySQL';
?>