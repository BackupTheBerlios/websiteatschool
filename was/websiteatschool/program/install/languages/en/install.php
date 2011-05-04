<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker <peter@berestijn.nl>
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

/** /program/install/languages/en/install.php - translated messages for /program/install.php (English)
 *
 * This file holds the English texts that are used in the installer.
 * It is the basis for all other language files.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasinstall
 * @version $Id: install.php,v 1.4 2011/05/04 13:53:43 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['translatetool_title'] = 'Install';
$string['translatetool_description'] = 'This file contains translations of the installation program';

$string['websiteatschool_install'] = 'Website@School Install';
$string['websiteatschool_logo'] = 'Website@School logo';
$string['help_name'] = 'help';
$string['help_description'] = 'Help (opens in a new window)';
$string['next'] = 'Next';
$string['next_accesskey'] = 'N';
$string['next_title'] = 'Use [Alt-N] or [Cmnd-N] as a keyboard shortcut for this button';
$string['previous'] = 'Previous';
$string['previous_accesskey'] = 'P';
$string['previous_title'] = 'Use [Alt-P] or [Cmnd-P] as a keyboard shortcut for this button';
$string['cancel'] = 'Cancel';
$string['cancel_accesskey'] = 'C';
$string['cancel_title'] = 'Use [Alt-C] or [Cmnd-C] as a keyboard shortcut for this button';
$string['ok'] = 'OK';
$string['ok_accesskey'] = 'K';
$string['ok_title'] = 'Use [Alt-K] or [Cmnd-K] as a keyboard shortcut for this button';
$string['yes'] = 'Yes';
$string['no'] = 'No';

$comment['language_name'] = 'LANGUAGE DIALOG
The name of the language should be expressed in the language itself, e.g. English, Nederlands, Deutsch, Fran√ßais, etc.';
$string['language_name'] = 'English';
$string['dialog_language'] = 'Language';
$string['dialog_language_title'] = 'Select the installation language';
$string['dialog_language_explanation'] = 'Please select the language to use during the installation procedure.';
$string['language_label'] = 'Language';
$string['language_help'] = '';

$comment['dialog_installtype'] = 'INSTALLATION TYPE DIALOG';
$string['dialog_installtype'] = 'Installation Type';
$string['dialog_installtype_title'] = 'Choose between Standard and Custom installation';
$string['dialog_installtype_explanation'] = 'Please choose the installation scenario from the list below';
$string['installtype_label'] = 'Installation Scenario';
$string['installtype_help'] = 'Please select the appropriate installation scenario.<br><strong>Standard</strong> means a straightforward installation with a minimum of questions to answer,<br><strong>Custom</strong> gives you full control of all installation options.';
$string['installtype_option_standard'] = 'Standard';
$string['installtype_option_custom'] = 'Custom';
$string['high_visibility_label'] = 'High visibility';
$string['high_visibility_help'] = 'Check the box to use a text-only user interface during installation.';

$comment['dialog_license'] = 'LICENSE DIALOG';
$string['dialog_license'] = 'License';
$string['dialog_license_title'] = 'Read and accept the license for this software';
$string['dialog_license_explanation'] = 'This software is licensed to you if and only if you read, understand and agree with the following terms and conditions. Note that the English version of this license agreement applies, even when you install the software using another language.';
$string['dialog_license_i_agree'] = 'I agree';
$string['dialog_license_you_must_accept'] = 'You must accept the license agreement by typing &quot;<b>{IAGREE}</b>&quot; (without the quotes) in the box below.';

$comment['dialog_database'] = 'DATABASE DIALOG';
$string['dialog_database'] = 'Database';
$string['dialog_database_title'] = 'Enter information about the database server';
$string['dialog_database_explanation'] = 'Please enter the properties of your database server in the fields below.';
$string['db_type_label'] = 'Type';
$string['db_type_help'] = 'Select one of the available database types.';
$string['db_type_option_mysql'] = 'MySQL';
$string['db_server_label'] = 'Server';
$string['db_server_help'] = 'This is the database server address, usually <strong>localhost</strong>. Other examples: <strong>mysql.example.org</strong> or <strong>example.dbserver.provider.net:3306</strong>.';
$string['db_username_label'] = 'Username';
$string['db_username_help'] = 'A valid username/password-combination is required to connect to the database server. Please do not use the root account of the database server but a less privileged one, e.g. <strong>wasuser</strong> or <strong>example_wwwa</strong>.';
$string['db_password_label'] = 'Password';
$string['db_password_help'] = 'A valid username/password-combination is required to connect to the database server.';
$string['db_name_label'] = 'Database name';
$string['db_name_help'] = 'This is the name of the database to use. Note that this database should already exist; this installation program is not designed to create databases (for security reasons). Examples: <strong>www</strong> or <strong>example_www</strong>.';
$string['db_prefix_label'] = 'Prefix';
$string['db_prefix_help'] = 'All tablenames in the database start with this prefix. This allows for multiple installations in the same database. Note that the prefix must begin with a letter. Examples: <strong>was_</strong> or <strong>cms2_</strong>.';

$comment['dialog_cms'] = 'WEBSITE DIALOG';
$string['dialog_cms'] = 'Website';
$string['dialog_cms_title'] = 'Enter the essential website information';
$string['dialog_cms_explanation'] = 'Please enter the essential website information in the fields below.';
$string['cms_title_label'] = 'Website title';
$string['cms_title_help'] = 'The name of your website.';
$string['cms_website_from_address_label'] = 'From: address';
$string['cms_website_from_address_help'] = 'This e-mail address is used for outgoing mail, e.g. alerts and password reminders.';
$string['cms_website_replyto_address_label'] = 'Reply-To: address';
$string['cms_website_replyto_address_help'] = 'This e-mail address is added to outgoing mail and can be used to specify a mailbox where replies are actually read (by you) and not discarded (by the webserver software).';
$string['cms_dir_label'] = 'Website directory';
$string['cms_dir_help'] = 'This is the path to the directory that holds index.php and config.php, e.g. <strong>/home/httpd/htdocs</strong> or <strong>C:\\Program Files\\Apache Group\\Apache\\htdocs</strong>.';
$string['cms_www_label'] = 'Website URL';
$string['cms_www_help'] = 'This is the main URL that leads to your website i.e. the place where index.php can be visited. Examples are: <strong>http://www.example.org</strong> or <strong>https://example.org:443/schoolsite</strong>.';
$string['cms_progdir_label'] = 'Program directory';
$string['cms_progdir_help'] = 'This is the path to the directory that holds the Website@School program files (usually the subdirectory <strong>program</strong> of the website directory). Examples: <strong>/home/httpd/htdocs/program</strong> or <strong>C:\\Program Files\\Apache Group\\Apache\\htdocs\\program</strong>.';
$string['cms_progwww_label'] = 'Program URL';
$string['cms_progwww_help'] = 'This is the URL that leads to the program directory (usually the website URL followed by <strong>/program</strong>). Examples are: <strong>http://www.example.org/program</strong> or <strong>https://example.org:443/schoolsite/program</strong>.';
$string['cms_datadir_label'] = 'Data directory';
$string['cms_datadir_help'] = 'This is a directory that holds uploaded files and other data files. It is very important that this directory is located outside the document root, i.e. is not directly accessible with a browser. Note that the webserver must have sufficient permission to read, create and write files here. Examples are: <strong>/home/httpd/wasdata</strong> or <strong>C:\\Program Files\\Apache Group\\Apache\\wasdata</strong>.';
$string['cms_demodata_label'] = 'Populate database';
$string['cms_demodata_help'] = 'Check this box if you want to start with your new website using demonstration data.';
$string['cms_demodata_password_label'] = 'Demonstration password';
$string['cms_demodata_password_help'] = 'The same demonstration password will be assigned to <em>all</em> demonstration user accounts. Please choose a good password: pick at least 8 characters from upper case letters, lowercase letters and digits. You can leave this field blank if you did not check the box \'Populate database\' above.';

$comment['dialog_user'] = 'USER ACCOUNT DIALOG';
$string['dialog_user'] = 'User Account';
$string['dialog_user_title'] = 'Create the first account';
$string['dialog_user_explanation'] = 'Please enter the information for the first user account for this new website. Note that this account will have full administrator privileges and all permissions possible so anyone with access to this account can do anything.';
$string['user_full_name_label'] = 'Full name';
$comment['user_full_name_help'] = 'Note: do not simply translate the name \'Wilhelmina Bladergroen\': this name was carefully chosen (just like all other example names used throughout this program). If you insist, you could replace her name with that of another pedagogue and education reformer, say CÈlestin Freinet.';
$string['user_full_name_help'] = 'Please enter your own name or, if you prefer, another (functional) name, e.g. <strong>Wilhelmina Bladergroen</strong> or <strong>Master Web</strong>.';
$string['user_username_label'] = 'Username';
$string['user_username_help'] = 'Please enter the login name you want to use for this account. You need to type this name every time you want to login. Examples: <strong>wblade</strong> or <strong>webmaster</strong>.';
$string['user_password_label'] = 'Password';
$string['user_password_help'] = 'Please choose a good password: pick at least 8 characters from upper case letters, lowercase letters, digits and special characters such as % (percent), = (equals),  / (slash) and . (dot). Do not share your password with others, but create additional accounts for your colleagues instead.';
$string['user_email_label'] = 'E-mail address';
$string['user_email_help'] = 'Please enter you e-mail address here. You need this address whenever you need to request a new password. Make sure that only you have access to this mailbox (do not use a shared mailbox). Examples: <strong>wilhelmina.bladergroen@example.org</strong> or <strong>webmaster@example.org</strong>.';

$comment['dialog_compatibility'] = 'COMPATIBILITY DIALOG';
$string['dialog_compatibility'] = 'Compatibility';
$string['dialog_compatibility_title'] = 'Check compatibility';
$string['dialog_compatibility_explanation'] = 'Below is an overview of required and desired settings. You need to make sure that the requirements are satisfied before you continue.';
$string['compatibility_label'] = 'Test';
$string['compatibility_value'] = 'Value';
$string['compatibility_result'] = 'Result';
$string['compatibility_ok'] = 'OK';
$string['compatibility_warning'] = 'WARNING';
$string['compatibility_websiteatschool_version_label'] = 'Website@School';
$string['compatibility_websiteatschool_version_check'] = '(check)';
$string['compatibility_websiteatschool_version_value'] = 'version {RELEASE} ({VERSION}) {RELEASE_DATE}';
$string['compatibility_websiteatschool_version_check_title'] = 'Check for later versions of Website@School';
$string['compatibility_phpversion_label'] = 'PHP version';
$string['compatibility_phpversion_obsolete'] = 'PHP version is obsolete';
$string['compatibility_phpversion_too_old'] = 'PHP version is too old: minimum is {MIN_VERSION}';
$string['compatibility_php_safemode_label'] = 'PHP Safe Mode';
$string['compatibility_php_safemode_warning'] = 'Safe Mode is On. Please switch it Off in php.ini';
$string['compatibility_webserver_label'] = 'Webserver';
$string['compatibility_autostart_session_label'] = 'Automatic session start';
$string['compatibility_autostart_session_fail'] = 'Automatic session start is On. Please switch it Off in php.ini';
$string['compatibility_file_uploads_label'] = 'File uploads';
$string['compatibility_file_uploads_fail'] = 'File uploads is Off. Please switch in On in php.ini';
$string['compatibility_database_label'] = 'Database server';
$string['compatibility_clamscan_label'] = 'Clamscan anti-virus';
$string['compatibility_clamscan_not_available'] = '(not available)';
$string['compatibility_gd_support_label'] = 'GD Support';
$string['compatibility_gd_support_none'] = 'GD is not supported';
$string['compatibility_gd_support_gif_readonly'] = 'Readonly';
$string['compatibility_gd_support_details'] = '{VERSION} (GIF: {GIF}, JPG: {JPG}, PNG: {PNG})';

$comment['dialog_confirm'] = 'CONFIRMATION DIALOG';
$string['dialog_confirm'] = 'Confirmation';
$string['dialog_confirm_title'] = 'Confirm settings';
$string['dialog_confirm_explanation'] = 'You are about to install your new website. Carefully check the configuration settings below and subsequently press [Next] to start the actual installation process. That may take a while.';
$string['dialog_confirm_printme'] = 'Tip: print this page and keep the hardcopy for future reference.';

$comment['dialog_cancelled'] = 'CANCEL DIALOG';
$string['dialog_cancelled'] = 'Cancelled';
$string['dialog_cancelled_title'] = '';
$string['dialog_cancelled_explanation'] = 'The installation of Website@School has been cancelled. Press the button below to retry or click on the help button to read the manual.';

$string['dialog_finish'] = 'Finish';
$string['dialog_finish_title'] = 'Finish the installation procedure';
$string['dialog_finish_explanation_0'] = 'The installation of Website@School {VERSION} is now almost complete.<p>There are two things left to do:<ol><li>You now have to {AHREF}download{A} the file config.php, and<li>You have to place the file config.php in <tt><strong>{CMS_DIR}</strong></tt>.</ol>Once config.php is in place, you can close the installer by pressing the [OK] button below.';
$string['dialog_finish_explanation_1'] = 'The installation of Website@School {VERSION} is now complete.<p>You can close the installer by pressing the [OK] button below.';
$string['dialog_finish_check_for_updates'] = 'If you wish, you can follow the link below to check for updates (link opens in a new window).';
$string['dialog_finish_check_for_updates_anchor'] = 'Check for Website@School updates.';
$string['dialog_finish_check_for_updates_title'] = 'check the status of your version of Website@School';

$string['jump_label'] = 'Jump to';
$string['jump_help'] = 'Select the location where you want to go after pressing the [OK] button below';

$string['dialog_download'] = 'Download config.php';
$string['dialog_download_title'] = 'Download config.php to your computer';

$string['dialog_unknown'] = 'Unknown';

$string['error_already_installed'] = 'Error: Website@School is already installed';
$string['error_wrong_version'] = 'Error: wrong version number. Did you download a new version during the installation?';
$string['error_fatal'] = 'Fatal error {ERROR}: please contact &lt;{EMAIL}&gt; for assistance';
$string['error_php_obsolete'] = 'Error: the version of PHP is too old';
$string['error_php_too_old'] = 'Error: the version of PHP ({VERSION}) is too old: use at least version {MIN_VERSION}';
$string['error_not_dir'] = 'Error: {FIELD}: directory does not exist: {DIRECTORY}';
$string['warning_switch_to_custom'] = 'Warning: switching to custom install so errors can be corrected';
$string['error_not_create_dir'] = 'Error: {FIELD}: directory can not be created: {DIRECTORY}';
$string['error_db_unsupported'] = 'Error: database {DATABASE} is currently not supported';
$string['error_db_cannot_connect'] = 'Error: cannot establish connection with the database server';
$string['error_db_cannot_select_db'] = 'Error: cannot open the database';
$string['error_invalid_db_prefix'] = 'Error: {FIELD}: must start with a letter, may contain only letters, digits or underscores';
$string['error_db_prefix_in_use'] = 'Error: {FIELD}: already in use: {PREFIX}';
$string['error_time_out'] = 'Fatal error: time-out';
$string['error_db_parameter_empty'] = 'Error: empty database parameters are not acceptable';
$string['error_db_forbidden_name'] = 'Error: {FIELD}: this name is not acceptable: {NAME}';
$string['error_too_short'] = 'Error: {FIELD}: string is too short (mimimum = {MIN})';
$string['error_too_long'] = 'Error: {FIELD}: string is too long (maximum = {MAX})';
$string['error_invalid'] = 'Error: {FIELD}: invalid value';
$string['error_bad_password'] = 'Error: {FIELD}: value not acceptable; minimum requirements are: digits: {MIN_DIGIT}, lowercase: {MIN_LOWER}, uppercase: {MIN_UPPER}';
$string['error_bad_data'] = '{MENU_ITEM}: errors are detected, please correct these first (via the menu)';
$string['error_file_not_found'] = 'Error: cannot find file: {FILENAME}';
$string['error_create_table'] = 'Error: cannot create table: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_insert_into_table'] = 'Error: cannot insert data into table: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_update_config'] = 'Error: cannot update configuration: {CONFIG} ({ERRNO}/{ERROR})';
$string['warning_no_manifest'] = 'Warning: empty manifest or no manifest for {ITEM}';
$string['error_install_demodata'] = 'Error: cannot install demonstration data';
$string['error_directory_exists'] = 'Error: {FIELD}: directory already exists: {DIRECTORY}';
$string['error_nameclash'] = 'Error: {FIELD}: please change the name {USERNAME}; it is already used as a demonstration user account';

// v0.90.2
$string['warning_mysql_obsolete'] = 'Warning: version \'{VERSION}\' of MySQL is obsolete and it does not support UTF-8. Please upgrade MySQL';

?>