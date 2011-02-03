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

/** /program/languages/en/loginlib.php - translated messages for login procedure and change password
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: loginlib.php,v 1.2 2011/02/03 14:03:59 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['translatetool_title'] = 'Login';
$string['translatetool_description'] = 'This file contains translations dealing with login/logout';

$string['access_denied'] = 'Access denied';

$string['change_password'] = 'Change password';

$string['change_password_confirmation_message'] = 
'Your password has been changed.

The password change request was received 
from address {REMOTE_ADDR} on {DATETIME}.

Kind regards,

Your automated webmaster.';

$string['change_password_confirmation_subject'] = 'Your password was succesfully changed';

$string['contact_webmaster_for_new_password'] = 'Please contact the webmaster to have your password changed.';

$string['do_you_want_to_try_forgot_password_procedure'] = 'Invalid credentials. Do you want to try the \'forgot password\' procedure?';

$string['email_address'] = 'E-mail address';

$string['failure_sending_laissez_passer_mail'] = 'Failure sending e-mail with one-time code. Please retry or contact the webmaster if this problem persists.';

$string['failure_sending_temporary_password'] = 'Failure sending e-mail with temporary password. Please retry or contact the webmaster if this problem persists.';

$string['forgot_password'] = 'Forgotten your password?';

$string['forgotten_password_mailmessage1'] = 
'Here is a link with a one-time code that will allow you to request a new,
temporary password. Copy the link below to the address bar in your browser
and press [Enter]:

    {AUTO_URL}

Alternatively, you can go to this location:

    {MANUAL_URL}

and enter your username and this one-time code:

    {LAISSEZ_PASSER}

Note that this code is valid for only {INTERVAL} minutes.

The request for this one-time code was received from this address:

    {REMOTE_ADDR}

Good luck!

Your automated webmaster';

$string['forgotten_password_mailmessage2'] = 
'Here is your temporary password:

    {PASSWORD}

Note that this password is valid for only {INTERVAL} minutes.

The request for this temporary password was received from this address:

    {REMOTE_ADDR}

Good luck!

Your automated webmaster';

$string['home_page'] = '(home)';

$string['invalid_credentials_please_retry'] = 'Invalid credentials, please try again.';

$string['invalid_laissez_passer_please_retry'] = 'Invalid one-time code, please try again.';

$string['invalid_new_passwords'] = 'Your new password was not acceptable. Possible reasons:
the first password did not match the second one;
the new password was not long enough (minimum {MIN_LENGTH}),
there were not enough lower case letters (minimum {MIN_LOWER}),
upper case letters (minumum {MIN_UPPER}) or digits (minimum {MIN_DIGIT})
or your new password was the same as your old one.
Please try to think of a good new password and retry.';

$string['invalid_username_email_please_retry'] = 'Invalid username and e-mail address, please retry.';

$string['laissez_passer'] = 'One-time code';

$string['login'] = 'Login';

$string['logout_forced'] = 'You are forcefully logged out.';

$string['logout_successful'] = 'You are successfully logged out.';

$string['message_box'] = 'Message box';

$string['must_change_password'] = 'You have to change your password now.';

$string['new_password1'] = 'New password';

$string['new_password2'] = 'Confirm new password';

$string['OK'] = 'OK';

$string['password'] = 'Password';

$string['password_changed'] = 'Your password was successfully changed.';

$string['please_enter_new_password_twice'] = 'Please enter your username and password and also your new password twice and press the button';

$string['please_enter_username_email'] = 'Please enter your username and e-mail address and press the button.';

$string['please_enter_username_laissez_passer'] = 'Please enter your username and one-time code and press the button.';

$string['please_enter_username_password'] = 'Please enter your username and password and press the button.';

$string['request_bypass'] = 'Request temporary password';

$string['request_laissez_passer'] = 'Request one-time login code';

$string['see_mail_for_further_instructions'] = 'Please see your e-mail for further instructions.';

$string['see_mail_for_new_temporary_password'] = 'Please see your e-mail for your new temporary password.';

$string['too_many_change_password_attempts'] = 'Too many attempts to change password.';

$string['too_many_login_attempts'] = 'Too many login attempts.';

$string['username'] = 'Username';

$string['your_forgotten_password_subject1'] = 'Re: One-time login code request';

$string['your_forgotten_password_subject2'] = 'Re: Temporary password request';


?>