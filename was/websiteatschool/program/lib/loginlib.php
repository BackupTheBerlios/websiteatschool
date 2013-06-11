<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker <peter@berestijn.nl>
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

/** /program/lib/loginlib.php -- functions to handle user login/logout
 *
 * Visitors need to authenticate when they want to see a 'protected' area
 * or when they want to modify the website content. This requires a user
 * account and the visitor presenting valid credentials (username +
 * password).
 * 
 * We don't want malicious scripts trying to get in with brute force.
 * However, we need to accomodate users that make typo's while entering
 * credentials. Also we want to allow for sending password reminders,
 * in a safe way.
 * 
 * Features:
 *  - users are allowed N login attempts within an interval of T1 minutes
 *  - users can request a new password (a 'bypass') to be mailed to them.
 *    this additional password is valid for only T2 minutes
 *  - if a user has requested a bypass, the user is forced to change
 *    her password. the new password must differ from the old password
 *    and also from the bypass
 *  - if too many failures are detected in the last T1 minutes, login
 *    attempts from the corresponding IP-address are blocked for T3 minutes
 *
 * N  = $CFG->login_max_failures, default 10
 *
 * T1 = $CFG->login_failures_interval, default 12 minutes
 *
 * T2 = $CFG->login_bypass_interval, default 30 minutes
 *
 * T3 = $CFG->login_blacklist_interval, default 8 minutes
 *
 * Once a user is authenticated, a PHP-session is established, using our own
 * database based session handler. The session key is stored in a cookie
 * in the user's browser. Presenting this cookie on subsequent calls is
 * enough to gain access. The logout routine takes care of killing both
 * the user's cookie and the session in the database.
 *
 * There are several different login procedures.
 *
 * 1. Normal login
 *    The user enters a valid username and password and is subsequently logged in.
 *
 * 2. Change password
 *    The user enters a valid username and password and also a valid new password
 *    (twice). A salted hash of the new password is recorded in the database and
 *    the user is logged in.
 *
 * 3. Forgotten password, phase 1: sending a laissez-passer
 *    The user presents a valid combination of username and email address.
 *    Subsequently a one-time logon-code (dubbed 'laissez-passer') is
 *    sent to the user's email address. This code is valid for at most
 *    T2 minutes. This code can be used, exactly once, to send a temporary
 *    password via email.
 *
 * 4. Forgotten password, phase 2: sending a temporary password
 *    The user clicks the link received in phase 1 and a temporary
 *    password (dubbed 'bypass') is sent to the user. This temporary
 *    password is valid for another T2 minutes.
 *
 * 5. Message box
 *    This is a pseudo-procedure. A simple 'message box' type of screen is
 *    displayed but no real interaction is anticipated via this screen.
 *    This is used to tell the user that things didnt work out (too many
 *    failures) or to check their mail for further instructions (e.g. when
 *    a laissez passer was sent). Whenever the user acknowledges this screen
 *    by clicking the button, she usually is directed to $WAS_SCRIPT_NAME.
 *
 * 6. Blacklist
 *    This is also a pseudo-procedure. The corresponding number is used to
 *    identify blacklisted IP-addresses in the database.
 *
 * Note that when the user logs in after a temporary password has been sent,
 * the normal login procedure is immediately followed by a (forced) 'change
 * password' procedure. This makes sure that a temporary password will be
 * changed immediately after the user logs in.
 *
 * Note that each of the procedures can be entered 'manually', i.e. by
 * opening index.php?login=X the user starts procedure X. This allows for the
 * user to change her password whenever she feels this is necessary, without
 * going through the trouble of the 'forgotten password'-procedure which 
 * eventually ends with the user changing her password too.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: loginlib.php,v 1.7 2013/06/11 11:26:06 pfokker Exp $
 * @todo should we suppress the username in the laissez-passer routine? We _do_ leak the
 *       the username in an insecure email message. This does require making the
 *       laissez-passer code unique in the database (currently only username+code
 *       has to be unique and that's easy because the username itself is unique).
 * @todo should we normalize the remote_addr everywhere? We now rely on the remote_addr
 *       being equal to some stored value (in the database) but with an IPv6 address
 *       there are several possibilities to have different representations of the
 *       same address (e.g. '::dead:beef' is equivalent to ::0:dead:beef' or even
 *       '::DeAd:BeeF' or '0000:0000:0000:0000:0000:0000:DEAD:BEEF'. This problem
 *       also exists with IPv4: '127.0.0.1' is equivalent to '127.000.000.001'. *sigh*
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** useful when debugging routines in this file: 0=production, 1=debugging */
define('LOGIN_DEBUG',0);

/** this only shows the login dialog */
define('LOGIN_PROCEDURE_SHOWLOGIN',0);

/** this is the usual procedure for logging in */
define('LOGIN_PROCEDURE_NORMAL',1);

/** this is the procedure to change the user's password */
define('LOGIN_PROCEDURE_CHANGE_PASSWORD',2);

/** this is phase 1 of the 'forgot password' procedure */
define('LOGIN_PROCEDURE_SEND_LAISSEZ_PASSER',3);

/** this is phase 2 of the 'forgot password' procedure */ 
define('LOGIN_PROCEDURE_SEND_BYPASS',4);

/** this is a pseudo procedure, used to deliver some message to the user */ 
define('LOGIN_PROCEDURE_MESSAGE_BOX',5);

/** this is a pseudo procedure, used to record blacklisted IP-addresses */
define('LOGIN_PROCEDURE_BLACKLIST',6);

/** this is the number of seconds to delay responding after a login action fails (slow 'm down..) */
define('LOGIN_FAILURE_DELAY_SECONDS',3);

/** this hardcoded minimal length is enforced whenever a user wants to change her password */
define('MINIMUM_PASSWORD_LENGTH',6);

/** this is the hardcoded minimal number of lower case characters in a new password */
define('MINIMUM_PASSWORD_LOWERCASE',1);

/** this is the hardcoded minimal number of upper case characters in a new password */
define('MINIMUM_PASSWORD_UPPERCASE',1);

/** this is the hardcoded minimal number of digits in a new password */
define('MINIMUM_PASSWORD_DIGITS',1);

/** this selects authentication via username+password in authenticate_user() */
define('BY_PASSWORD',1);

/** this selects authentication via username+email in authenticate_user() */
define('BY_EMAIL',2);

/** this selects authentication via username+laissez_passer in authenticate_user() */
define('BY_LAISSEZ_PASSER',3);

/** this defines the maximum line length in messages and instructions */
define('MAXIMUM_LINE_LENGTH',50);

/** end a session (logout the user) and maybe redirect
 *
 * This routine ends the current session if it exists (as indicated by the
 * cookie presented by the user's browser). An empty value is sent
 * to the browser (effectively deleting the cookie) and also
 * the session is ended. The routine ends either with
 * showing a generic login dialog OR a redirection to
 * a user-defined page.
 *
 * Note that as a rule this routine does NOT return but instead calls exit().
 * However, there are cases where this routine DOES return, notably when
 * no session appears to be established (no cookie submitted by the browser
 * or a non-existing/expired session). If the routine does return, the status
 * is equivalent to a logged out user; no session exists so the user simply 
 * should not be logged in.
 *
 * @return void this function sometimes never returns
 * @uses $CFG
 * @uses dbsession_setup()
 */
function was_logout() {
    global $CFG;

    if (isset($_COOKIE[$CFG->session_name])) {
        /** install our own database based session handler */
        require_once($CFG->progdir.'/lib/dbsessionlib.php');
        dbsession_setup($CFG->session_name);
        if (dbsession_exists($_COOKIE[$CFG->session_name])) {
            session_start();
            $redirect = (isset($_SESSION['redirect'])) ? $_SESSION['redirect'] : '';
            $user_id = $_SESSION['user_id'];
            $logmessage = 'logout: \''.$_SESSION['username'].'\' ('.$user_id.'): success';
            $logmessage .= ' (session started '.$_SESSION['session_start'].', count='.$_SESSION['session_counter'].')';
            $_SESSION = array(); // get rid of all session variables

            /* kill the session/cookie in the user's browser (with the correct cookie parameters)... */
            $a = session_get_cookie_params();
            setcookie($CFG->session_name,'',time()-86400,$a['path'],$a['domain'],$a['secure']);

            /* ...and also in the sessions table */
            session_destroy();
            /* log the event */
            logger($logmessage,WLOG_INFO,$user_id);
            if (!empty($redirect)) {
                header('Location: '.$redirect);
                echo '<a href="'.$redirect.'">'.htmlspecialchars($redirect) .'</a>';
            } else {
                show_login(LOGIN_PROCEDURE_NORMAL,t('logout_successful','loginlib'));
            }
        } else {
            /* kill the cookie in the user's browser even when the session does no longer exist in database */
            $a = session_get_cookie_params();
            setcookie($CFG->session_name,'',time()-86400,$a['path'],$a['domain'],$a['secure']);
            logger('logout: reset user\'s cookie even without corresponding session record in database',WLOG_DEBUG);
            /* since we don't know where to go next (no redirect), we'll go to login screen #1 */
            show_login(LOGIN_PROCEDURE_NORMAL,t('logout_forced','loginlib'));
        }
        exit;
    } else { // nothing because no cookie was/is set
        logger('logout: nothing to do');
    }
} // was_logout()

/** execute the selected login procedure
 *
 * The login process is controlled via the parameter 'login'
 * provided by the user via 'index.php?login=N or via the
 * 'action' property in a HTML-form. These numbers correspond to the
 * LOGIN_PROCEDURE_* constants defined near the top of this file.
 * Here's a reminder:
 *
 * 1. LOGIN_PROCEDURE_NORMAL this is the usual procedure for logging in
 * 2. LOGIN_PROCEDURE_CHANGE_PASSWORD this is the procedure to change the user's password
 * 3. LOGIN_PROCEDURE_SEND_LAISSEZ_PASSER this is phase 1 of the 'forgot password' procedure
 * 4. LOGIN_PROCEDURE_SEND_BYPASS this is phase 2 of the 'forgot password' procedure
 *
 * Note that this routine only returns to the caller after either a succesful
 * regular login (i.e. after completing LOGIN_PROCEDURE_NORMAL). All the
 * other variants and error conditions yield another screen and an immediate exit and
 * hence no return to caller. If this routine returns, it returns the user_id
 * of the authenticated user (the primary key into the users table). It is up to the caller to
 * retrieve additional information about this user; any information read from the database
 * during login is discarded. This prevents password hashes still lying around.
 * 
 * Note that a successful login has the side effect of garbage collection:
 * whenever we experience a successful login any obsolete sessions are removed.
 * This makes sure that locked records eventually will be unlocked, once the corresponding
 * session no longer exists. The garbage collection routine is also called from
 * the PHP session handler every once in a while, but here we make 100% sure that
 * garbage is collected at least at every login. (Note: obsolete sessions should not
 * be a problem for visitors that are not logged in, because you have to be logged in
 * to be able to lock a record.)
 *
 * @param int $procedure the login procedure to execute
 * @param string $message the message to display when showing the login dialog
 * @return void|int no return on error, otherwise the user_id of the authenticated user
 * @uses $CFG
 * @uses dbsession_setup()
 * @uses dbsession_garbage_collection()
 */
function was_login($procedure=LOGIN_PROCEDURE_SHOWLOGIN,$message='') {
    global $CFG;

    // get rid of the cookie (if we received one) and the corresponding session;
    // the user's browser should NOT present us with a cookie during the login procedures.
    if (isset($_COOKIE[$CFG->session_name])) {
        was_logout();
        exit;
    }

    // If this IP-address is currently blacklisted, tell the visitor access is denied
    if (login_is_blacklisted($_SERVER['REMOTE_ADDR'])) {
        show_login(LOGIN_PROCEDURE_MESSAGE_BOX,t('access_denied','loginlib'));
        exit;
    }

    switch(intval($procedure)) {

    case LOGIN_PROCEDURE_NORMAL:
        if ((isset($_POST['login_username'])) && (isset($_POST['login_password']))) {
            $username = magic_unquote($_POST['login_username']);
            $password = magic_unquote($_POST['login_password']);
            $user = authenticate_user(BY_PASSWORD,$username,$password);
            if ($user !== FALSE) {
                login_failure_reset($_SERVER['REMOTE_ADDR']);
                if (db_bool_is(FALSE,$user['bypass_mode'])) {
                    // valid credentials and not in a bypass mode: start session and return user_id
                    require_once($CFG->progdir.'/lib/dbsessionlib.php');
                    dbsession_setup($CFG->session_name);
                    $session_key = dbsession_create($user['user_id'],$_SERVER['REMOTE_ADDR']);
                    session_id($session_key);
                    session_start();
                    $_SESSION['session_id'] = dbsession_get_session_id($session_key);
                    $user_id = intval($user['user_id']);
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['redirect'] = $user['redirect'];
                    $_SESSION['language_key'] = $user['language_key'];
                    $_SESSION['remote_addr'] = $_SERVER['REMOTE_ADDR'];
                    $_SESSION['salt'] = $CFG->salt; // allow for extra check on rogue sessions
                    $_SESSION['username'] = $username;
                    logger('login: \''.$username.'\' ('.$user_id.'): success',WLOG_INFO,$user_id);
                    // now that we logged on successfully, make sure that obsolete sessions
                    // will not bother us (or other logged in users). Note the 900 seconds minimum duration;
                    $time_out = max(900,intval(ini_get('session.gc_maxlifetime')));
                    dbsession_garbage_collection($time_out);
                    return $user_id; // SUCCESS! User is logged in, tell caller!
                } else {
                    show_login(LOGIN_PROCEDURE_CHANGE_PASSWORD,t('must_change_password','loginlib'));
                    exit;
                }
            }
            // Invalid credentials; pretend we're busy (slow user down), increment failure count...
            $failure_count = login_failure_increment($_SERVER['REMOTE_ADDR'],LOGIN_PROCEDURE_NORMAL,$username);
            login_failure_delay($_SERVER['REMOTE_ADDR']);
            if ($failure_count < intval($CFG->login_max_failures)) {
                show_login(LOGIN_PROCEDURE_NORMAL,t('invalid_credentials_please_retry','loginlib'));
            } elseif ($failure_count == intval($CFG->login_max_failures)) {
                show_login(LOGIN_PROCEDURE_SEND_LAISSEZ_PASSER,t('do_you_want_to_try_forgot_password_procedure','loginlib'));
            } else {
                login_failure_blacklist_address($_SERVER['REMOTE_ADDR'],60*intval($CFG->login_blacklist_interval),$username);
                show_login(LOGIN_PROCEDURE_MESSAGE_BOX,t('contact_webmaster_for_new_password','loginlib'));
            }
        } else {
            show_login(LOGIN_PROCEDURE_NORMAL);
        }
        exit;
        break;

    case LOGIN_PROCEDURE_CHANGE_PASSWORD:
        if ((isset($_POST['login_username'])) && (isset($_POST['login_password'])) &&
            (isset($_POST['login_new_password1'])) && (isset($_POST['login_new_password2']))) {
            $username = magic_unquote($_POST['login_username']);
            $password = magic_unquote($_POST['login_password']);
            $new_password1 = magic_unquote($_POST['login_new_password1']);
            $new_password2 = magic_unquote($_POST['login_new_password2']);
            $user = authenticate_user(BY_PASSWORD,$username,$password);
            //
            // step 1 - perform some checks on the proposed new passwords
            //
            if ($user !== FALSE) {
                // user authenticated: we can now also check re-use of existing passwords
                $salt = $user['salt'];
                $password_hash = $user['password_hash'];
                $bypass_hash = $user['bypass_hash'];
            } else {
                // user not authenticated so we cannot check for re-use of existing passwords
                $salt = '';
                $password_hash = '';
                $bypass_hash = '';
            }
            if (!acceptable_new_password($new_password1,$new_password2,$salt,$password_hash,$bypass_hash)) {
                show_login(LOGIN_PROCEDURE_CHANGE_PASSWORD,t('invalid_new_passwords','loginlib',array(
                    '{MIN_LENGTH}' => MINIMUM_PASSWORD_LENGTH,
                    '{MIN_LOWER}' => MINIMUM_PASSWORD_LOWERCASE,
                    '{MIN_UPPER}' => MINIMUM_PASSWORD_UPPERCASE,
                    '{MIN_DIGIT}' => MINIMUM_PASSWORD_DIGITS)));
                exit;
            }
            //
            // step 2 - if authenticated, actually change password and reset failure counters/blacklists
            //
            if ($user !== FALSE) {
                // allow the user in: 
                //  - start new session, 
                //  - immediately write/close it,
                //  - send user an email about success with changing password,
                //  - and finally leave the user with message box on screen
                //
                login_change_password($user['user_id'],$new_password1);
                login_failure_reset($_SERVER['REMOTE_ADDR']);
                require_once($CFG->progdir.'/lib/dbsessionlib.php');
                dbsession_setup($CFG->session_name);
                $session_key = dbsession_create($user['user_id'],$_SERVER['REMOTE_ADDR']);
                session_id($session_key);
                session_start();
                $_SESSION['session_id'] = dbsession_get_session_id($session_key);
                $user_id = intval($user['user_id']);
                $_SESSION['user_id'] = $user_id;
                $_SESSION['redirect'] = $user['redirect'];
                $_SESSION['language_key'] = $user['language_key'];
                $_SESSION['remote_addr'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['salt'] = $CFG->salt; // allow for extra check on rogue sessions
                $_SESSION['username'] = $username;
                session_write_close(); // save the session
                login_send_confirmation($user);
                logger('login: \''.$username.'\' ('.$user_id.'), change password: success',WLOG_INFO,$user_id);
                show_login(LOGIN_PROCEDURE_MESSAGE_BOX,t('password_changed','loginlib'));
                exit;
            }
            // Invalid credentials; pretend we're busy (slow user down), increment failure count...
            $failure_count = login_failure_increment($_SERVER['REMOTE_ADDR'],LOGIN_PROCEDURE_CHANGE_PASSWORD,$username);
            login_failure_delay($_SERVER['REMOTE_ADDR']);
            if ($failure_count < intval($CFG->login_max_failures)) {
                show_login(LOGIN_PROCEDURE_CHANGE_PASSWORD,t('invalid_credentials_please_retry','loginlib'));
            } elseif ($failure_count == intval($CFG->login_max_failures)) {
                show_login(LOGIN_PROCEDURE_SEND_LAISSEZ_PASSER,t('too_many_login_attempts','loginlib'));
            } else {
                login_failure_blacklist_address($_SERVER['REMOTE_ADDR'],60*intval($CFG->login_blacklist_interval),$username);
                show_login(LOGIN_PROCEDURE_MESSAGE_BOX,t('too_many_change_password_attempts','loginlib'));
            }
        } else {
            show_login(LOGIN_PROCEDURE_CHANGE_PASSWORD);
        }
        exit;
        break;

    case LOGIN_PROCEDURE_SEND_LAISSEZ_PASSER:
        if ((isset($_POST['login_username'])) && (isset($_POST['login_email']))) {
            $username = magic_unquote($_POST['login_username']);
            $email = magic_unquote($_POST['login_email']);
            $user = authenticate_user(BY_EMAIL,$username,$email);
            if ($user !== FALSE) {
                if (login_send_laissez_passer($user)) {
                    show_login(LOGIN_PROCEDURE_MESSAGE_BOX,t('see_mail_for_further_instructions','loginlib'));
                } else {
                    show_login(LOGIN_PROCEDURE_MESSAGE_BOX,t('failure_sending_laissez_passer_mail','loginlib'));
                }
                exit;
            } else {
                // Not authenticated; pretend we're busy (slow user down), increment failure count...
                $failure_count = login_failure_increment($_SERVER['REMOTE_ADDR'],LOGIN_PROCEDURE_SEND_LAISSEZ_PASSER,$username);
                login_failure_delay($_SERVER['REMOTE_ADDR']);
                if ($failure_count < intval($CFG->login_max_failures)) {
                    show_login(LOGIN_PROCEDURE_SEND_LAISSEZ_PASSER,t('invalid_username_email_please_retry','loginlib'));
                } elseif ($failure_count == intval($CFG->login_max_failures)) {
                    show_login(LOGIN_PROCEDURE_MESSAGE_BOX,t('too_many_login_attempts','loginlib'));
                } else {
                    login_failure_blacklist_address($_SERVER['REMOTE_ADDR'],60*intval($CFG->login_blacklist_interval),$username);
                    show_login(LOGIN_PROCEDURE_MESSAGE_BOX,t('too_many_login_attempts','loginlib'));
                }
                exit;
            }
        } else {
            show_login(LOGIN_PROCEDURE_SEND_LAISSEZ_PASSER);
        }
        exit;
        break;

    case LOGIN_PROCEDURE_SEND_BYPASS:
        if ((isset($_GET['code'])) && (isset($_GET['username']))) {
            $laissez_passer = magic_unquote($_GET['code']);
            $username = magic_unquote($_GET['username']);;
        } elseif ((isset($_POST['login_username'])) && (isset($_POST['login_laissez_passer']))) {
            $laissez_passer = magic_unquote($_POST['login_laissez_passer']);
            $username = magic_unquote($_POST['login_username']);
        } else {
            show_login(LOGIN_PROCEDURE_SEND_BYPASS);
            exit;
        }
        // still here? Then we check the laissez_passer and send a second email to the user
        $user = authenticate_user(BY_LAISSEZ_PASSER,$username,$laissez_passer);
        if ($user !== FALSE) {
            login_failure_reset($_SERVER['REMOTE_ADDR']);
            if (login_send_bypass($user)) {
                show_login(LOGIN_PROCEDURE_NORMAL,t('see_mail_for_new_temporary_password','loginlib'));
            } else {
                show_login(LOGIN_PROCEDURE_MESSAGE_BOX,t('failure_sending_temporary_password','loginlib'));
            }
        } else {
            $failure_count = login_failure_increment($_SERVER['REMOTE_ADDR'],LOGIN_PROCEDURE_SEND_BYPASS,$username);
            login_failure_delay($_SERVER['REMOTE_ADDR']);
            if ($failure_count < intval($CFG->login_max_failures)) {
                show_login(LOGIN_PROCEDURE_SEND_BYPASS,t('invalid_laissez_passer_please_retry','loginlib'));
            } elseif ($failure_count == intval($CFG->login_max_failures)) {
                show_login(LOGIN_PROCEDURE_MESSAGE_BOX,t('too_many_login_attempts','loginlib'));
            } else {
                login_failure_blacklist_address($_SERVER['REMOTE_ADDR'],60*intval($CFG->login_blacklist_interval),$username);
                show_login(LOGIN_PROCEDURE_MESSAGE_BOX,t('too_many_login_attempts','loginlib'));
            }
        }
        exit;
        break;

    case LOGIN_PROCEDURE_SHOWLOGIN:
        show_login(LOGIN_PROCEDURE_NORMAL,$message);
        exit;
        break;

    default:
        show_login(LOGIN_PROCEDURE_NORMAL);
        exit;
        break;
    }
} // was_login()

/** show complete login dialog and exit
 *
 * There are different variations of this dialog.
 *
 *  1. LOGIN_PROCEDURE_NORMAL Plain login
 *<pre>
 *    (message)
 *    Username: _____
 *    Password: _____
 *    [OK]
 *    <home page> <forgotten password?>
 *</pre>
 *  This screen is used for plain user authentication.
 *  As a rule the user uses the correct primary password to authenticate.
 *  However, it is also possible to enter the 'bypass' password instead.
 *  If the authentication fails, that fact is recorded.
 *  If the number of failures exceeds threshold N, the user is shown
 *  screen #3 LOGIN_PROCEDURE_SEND_LAISSEZ_PASSER. If the number is still below N1 screen #1 is shown again.
 *
 * The link <forgotten password?> takes the user directly to screen #3 LOGIN_PROCEDURE_SEND_LAISSEZ_PASSER.
 *
 * 2. LOGIN_PROCEDURE_CHANGE_PASSWORD - Login/change password
 * <pre>
 *    (message)
 *    Username: _____
 *    Old password: _____
 *    New password1: _____
 *    New password2: _____
 *    [OK]
 * </pre>
 * This screen is used to change the user's password. If both new passwords
 * are different, the user is redirected to the same screen #3 until she
 * gets it right. Otherwise, if the old password is either the valid original
 * password OR the bypass password, the password is changed and the mode is
 * reset to 'normal'. The one-time codes and the bypass password are reset.
 * Also, as a result, the user is logged in. If the user failed to enter the
 * proper old password more than N1 times, the mode is also reset to normal
 * (invalidating the laissez-passer and the bypass password) and the user is
 * dropped at a screen #4 basically telling her to contact the webmaster.
 * In this process the user is also logged out if necessary.
 *
 * 3. LOGIN_PROCEDURE_SEND_LAISSEZ_PASSER Request bypass
 * <pre>
 *    (message)
 *    Username: _____
 *    Email: _____
 *    [OK]
 * </pre>
 * This screen is used to help the user reset her password. It is displayed
 * automatically after N1 failed login attempts. This screen can also be
 * reached via the <forgot password?> link in screen #1.
 *
 * If the user presents an invalid combination of username and email address,
 * this failure is also recorded. If the number of failures has reached
 * the threshold N2, the user is taken to a screen #4 that basically tells the
 * user ask the webmaster for assistance and that's that.
 *
 * If the user presents a valid combination of username and email address,
 * an email with a message like 'click the link below for a new password'
 * is sent to the email address. After that mail is sent a screen #4 is
 * displayed, basically telling the user to await further instructions that
 * were sent via mail.
 *
 * Note that resetting the password is a two-step process. First the user
 * is sent a one-time code laissez-passer embedded in a link. Clicking the
 * link before it expires (after T minutes) yields a second emai message 
 * containing a bypass password that can be used to login and subsequently
 * change the primary password. After that both the laissez-passer and the
 * bypass password are invalidated.
 *
 * 4. LOGIN_PROCEDURE_SEND_BYPASS - Send a temporary password
 *
 * Phase 2 of the forgot password procedure.
 *
 * 5. LOGIN_PROCEDURE_MESSAGE_BOX Alert
 * <pre>
 *    (message)
 * </pre>
 *
 * This screen is user to communicate various messages to the user, e.g.
 * 'check your mail for instructions', 'contact webmaster', etc.
 *
 * @param int the $screen screen variant to show, could be 1,...,5
 * @param string $message the message to show just above the first field, used for feedback to user
 * @param string $username the default username to show in the dialog
 * @return void this routine never returns but it does send a page to the user
 */
function show_login($screen=1,$message='',$username='') {
    global $CFG;
    global $WAS_SCRIPT_NAME;

    switch(intval($screen)) {

    case LOGIN_PROCEDURE_NORMAL:
        $title = t('login','loginlib').' - '.$CFG->title;
        $focus = (empty($username)) ? 'login_username' : 'login_password';
        $s = login_page_open($title,$focus);

        $action = $WAS_SCRIPT_NAME.'?login=1';
        // try to remember if the user wants to navigate to a particular area/node
        if (isset($_GET['area'])) { $action .= '&amp;area='.intval($_GET['area']); }
        if (isset($_GET['node'])) { $action .= '&amp;node='.intval($_GET['node']); }
        $s .= login_dialog_open($CFG->title,$action,$message,'          ');
        $s .= login_dialog_instruction(t('please_enter_username_password','loginlib'),'              ');
        $tabindex = 1;
        $s .= login_dialog_text_input(t('username','loginlib'),'login_username',$username,$tabindex++,'              ');
        $s .= login_dialog_password_input(t('password','loginlib'),'login_password',$tabindex++,'              ');
        $s .= login_dialog_submit_input(t('OK','loginlib'),'button',$tabindex++,'              ');
        $s .= login_dialog_home_forgot_password(t('forgot_password','loginlib'),'              ');
        $s .= login_dialog_close($action,'          ');
        $s .= login_page_close($message);
        break;

    case LOGIN_PROCEDURE_CHANGE_PASSWORD:
        $title = t('change_password','loginlib').' - '.$CFG->title;
        $focus = (empty($username)) ? 'login_username' : 'login_password';
        $s = login_page_open($title,$focus);

        $action = $WAS_SCRIPT_NAME.'?login='.LOGIN_PROCEDURE_CHANGE_PASSWORD;
        // try to remember if the user wants to navigate to a particular area/node
        if (isset($_GET['area'])) { $action .= '&amp;area='.intval($_GET['area']); }
        if (isset($_GET['node'])) { $action .= '&amp;node='.intval($_GET['node']); }
        $s .= login_dialog_open($CFG->title,$action,$message,'          ');
        $s .= login_dialog_instruction(t('please_enter_new_password_twice','loginlib'),'              ');
        $tabindex = 1;
        $s .= login_dialog_text_input(t('username','loginlib'),'login_username',$username,$tabindex++,'              ');
        $s .= login_dialog_password_input(t('password','loginlib'),'login_password',$tabindex++,'              ');
        $s .= login_dialog_password_input(t('new_password1','loginlib'),'login_new_password1',$tabindex++,'              ');
        $s .= login_dialog_password_input(t('new_password2','loginlib'),'login_new_password2',$tabindex++,'              ');
        $s .= login_dialog_submit_input(t('OK','loginlib'),'button',$tabindex++,'              ');
        $s .= login_dialog_home_forgot_password('','              ');
        $s .= login_dialog_close($action,'          ');
        $s .= login_page_close($message);
        break;

    case LOGIN_PROCEDURE_SEND_LAISSEZ_PASSER:
        $title = t('request_laissez_passer','loginlib').' - '.$CFG->title;
        $focus = (empty($username)) ? 'login_username' : 'login_email';
        $s = login_page_open($title,$focus);

        $action = $WAS_SCRIPT_NAME.'?login='.LOGIN_PROCEDURE_SEND_LAISSEZ_PASSER;
        $s .= login_dialog_open($CFG->title,$action,$message,'          ');
        $s .= login_dialog_instruction(t('please_enter_username_email','loginlib'),'              ');
        $tabindex = 1;
        $s .= login_dialog_text_input(t('username','loginlib'),'login_username',$username,$tabindex++,'              ');
        $s .= login_dialog_text_input(t('email_address','loginlib'),'login_email','',$tabindex++,'              ');
        $s .= login_dialog_submit_input(t('OK','loginlib'),'button',$tabindex++,'              ');
        $s .= login_dialog_home_forgot_password('','              ');
        $s .= login_dialog_close($action,'          ');
        $s .= login_page_close($message);
        break;

    case LOGIN_PROCEDURE_SEND_BYPASS:
        $title = t('request_bypass','loginlib').' - '.$CFG->title;
        $focus = (empty($username)) ? 'login_username' : 'login_laissez_passer';
        $s = login_page_open($title,$focus);

        $action = $WAS_SCRIPT_NAME.'?login='.LOGIN_PROCEDURE_SEND_BYPASS;
        $s .= login_dialog_open($CFG->title,$action,$message,'          ');
        $s .= login_dialog_instruction(t('please_enter_username_laissez_passer','loginlib'),'              ');
        $tabindex = 1;
        $s .= login_dialog_text_input(t('username','loginlib'),'login_username',$username,$tabindex++,'              ');
        $s .= login_dialog_text_input(t('laissez_passer','loginlib'),'login_laissez_passer','',$tabindex++,'              ');
        $s .= login_dialog_submit_input(t('OK','loginlib'),'button',$tabindex++,'              ');
        $s .= login_dialog_home_forgot_password('','              ');
        $s .= login_dialog_close($action,'          ');
        $s .= login_page_close($message);
        break;

    case LOGIN_PROCEDURE_MESSAGE_BOX:
        $title = t('message_box','loginlib').' - '.$CFG->title;
        $focus = 'button';
        $s = login_page_open($title,$focus);

        $action = $WAS_SCRIPT_NAME;
        $glue = '?';
        if (isset($_GET['area'])) { $action .= $glue.'area='.intval($_GET['area']); $glue = '&amp;'; }
        if (isset($_GET['node'])) { $action .= $glue.'node='.intval($_GET['node']); $glue = '&amp;'; }

        $s .= login_dialog_open($CFG->title,$action,$message,'          ');
        $tabindex = 1;
        $s .= login_dialog_submit_input(t('OK','loginlib'),'button',$tabindex++,'              ');
        $s .= login_dialog_home_forgot_password('','              ');
        $s .= login_dialog_close($action,'          ');
        $s .= login_page_close($message);
        break;

    default:
        $s = 'OOOPS?! unknown screen '.intval($screen);
        trigger_error('internal error: '.$s);
        break;
    }
    $charset = 'UTF-8';
    $content_type = 'text/html; charset='.$charset;
    header('Content-Type: '.$content_type);
    echo $s;
    exit;
} // show_login()

/** construct the start of a simple HTML-page and open a full size table
 *
 * This routine starts with a plain HTML-page, with a title and possibly
 * a single line of Javascript to place the cursor in a particular input
 * field (defined later in the page). After that, a main table is opened
 * and within that 1x1 table the table cell is opened. {@link login_page_close()}
 * closes that cell.
 *
 * @param string $title the title of the HTML-page
 * @param string $focus (optional) the name of the field to focus on (via Javascript)
 * @return string the constructed HTML
 * @uses $CFG;
 * @see login_page_close()
 */
function login_page_open($title,$focus='') {
    global $CFG;
    $border = (LOGIN_DEBUG) ? '1' : '0';
    //$s ='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'."\n";
    $s ="<html>\n".
        "  <head>\n".
        "    <title>$title</title>\n".
        login_stylesheet().
        "  </head>\n".
        "  <body";
    if (!empty($focus)) {
        $s .= " onload=\"self.focus(); document.loginform.".$focus.".focus()\"";
    }
    $s.=">\n".
        "    <table width=\"100%\" height=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"{$border}\">\n".
        "      <tr>\n".
        "        <td valign=\"middle\" align=\"center\" id=\"outer_cell\">\n";
    return $s;
} // login_page_open()

/** a simple in-line style sheet conveniently grouped in a single routine
 *
 * @return string ready-to-use CSS-code including style tags
 * @todo this routine needs some cleaning up
 */
function login_stylesheet() {
    global $CFG;
    $s = <<<EOT
    <style type="text/css">
    <!--
    body {
        background-color: #EFEFFF;
        background-image: url({$CFG->progwww_short}/graphics/puzzle_tile.gif);
        color: #0000FF;
    }
    a {
        font-size: 0.8em;
    }
    :link { color: #0000FF; }
    :visited { color: #00007F; }
    :active { color: #FF0000; }
    .alert {
        background-color: #FFFF00;
    }
    .textfield { font-family: verdana; background-color: #EFEFFF; }
    .passwordfield { font-family: verdana; background-color: #EFEFFF; }
    .button { font-family: verdana; background-color: #DFDFDF; }
    td { background-color: #FFFFFF; }
    td#outer_cell { background-color: transparent; }
    td#title_cell { background-color: #CFCFCF; text-align: center }
    -->
    </style>

EOT;
    return $s;
} // login_stylesheet()

/** construct the end of the simple HTML-page, closing the full size table
 *
 * @param string $alert_message (optional) message to show via a javascript alert()
 * @see login_page_open()
 * @return string the constructed HTML
 * @uses javascript_alert()
 */
function login_page_close($alert_message='') {
    $s ="        </td>\n".
        "      </tr>\n".
        "    </table>\n";
    if (!empty($alert_message)) {
        $alert_message = wordwrap($alert_message,MAXIMUM_LINE_LENGTH);
        $s .= "  <script>\n".
              "  <!--\n".
              "    ".javascript_alert($alert_message)."\n".
              "  -->\n".
              "  </script>\n";
    }
    $s.="  </body>\n".
        "</html>\n";
    return $s;
} // login_page_close()

/** construct the start of the login dialog, opening the form and the secondary table
 *
 * This optionally opens an HTML-form (which is optionally closed in
 * companion routine {@link login_dialog_close()}) and subsequently 
 * starts a table with two columns. The first row of the table shows
 * the $title, the second row may show a wordwrap()'ed feedback message
 * for the user in a different background colour.
 *
 * @param string $title text to show in the dialog title bar
 * @param string $action (optional) if not empty a HTML-form pointing to $action is opened
 * @param string $message (optional) feedback message to show to user
 * @param string $m (optional) margin to add for code readability
 * @return string constructed HTML
 * @see login_dialog_close()
 * @todo should we add another 'powered by' link to '/program/about.html'?
 */
function login_dialog_open($title,$action='',$message='',$m='') {
    global $CFG;
    $border = (LOGIN_DEBUG) ? '1' : '0';
    $img = sprintf('<img src="%s/graphics/waslogo-567x142.png" width="%d" height="%d" border="%s" alt="%s" title="%s">',
                    $CFG->progwww_short,
                    567,142,$border,
                    (WAS_ORIGINAL) ? 'Powered by Website@School' : 'Based on Website@School',
                    'The Website@School logo is a registered trademark of Vereniging Website At School');
    if (empty($message)) {
        $message = '&nbsp;';
        $alert_class = '';
    } else {
        $message = wordwrap($message,MAXIMUM_LINE_LENGTH);
        $alert_class = ' class="alert"';
    }
    $s = '';
    if (!empty($action)) {
        $s .= $m."<form method=\"POST\" action=\"{$action}\" name=\"loginform\">\n";
    }
    $s .= <<<EOT
$m  <table style="border:1px solid #00007F" cellspacing="0" cellpadding="7" border="{$border}">
$m    <tr>
$m      <td colspan="2" id="title_cell"><h2>{$title}</h2></td>
$m    </tr>
$m    <tr>
$m      <td colspan="2" align="center" width="567">
$m        {$img}
$m      </td>
$m    </tr>
$m    <tr>
$m      <td align="center" colspan="2"{$alert_class}>{$message}</td>
$m    </tr>

EOT;
    return $s;
} // login_dialog_open()

/** close the login dialog/table and maybe an opened HTML-form
 *
 * @see login_dialog_open()
 * @param string $action (optional) if not empty the currently open HTML-form is closed
 * @param string $m (optional) margin to add for code readability
 * @return string constructed HTML
 */
function login_dialog_close($action='',$m='') {
    $s = $m."  </table>\n";
    $s .= (empty($action)) ? $m : $m."</form>\n";
    return $s;
} // login_dialog_close()

/** add a row to the table/dialog with wordwrap()'ed instruction for the user
 *
 * @param string $instruction instructive message to show to user
 * @param string $m (optional) margin to add for code readability
 * @return string constructed HTML
 */
function login_dialog_instruction($instruction,$m='') {
    $instruction = wordwrap($instruction ,MAXIMUM_LINE_LENGTH);
    $s = $m."<tr>\n".
         $m."  <td align=\"center\" colspan=\"2\">&nbsp;<br>{$instruction}<br>&nbsp;</td>\n".
         $m."</tr>\n";
    return $s;
} // login_dialog_instruction()

/** add a row with an ordinary input field to the login dialog/table
 *
 * @param string $prompt the text to show in the 1st column of the table row
 * @param string $name the name of the input field
 * @param string $value (optional) value to preload the field value (default empty string)
 * @param int $tabindex (optional) determines in which order fields are accessed in the dialog
 * @param string $m (optional) margin to add for code readability
 * @return string constructed HTML
 */
function login_dialog_text_input($prompt,$name,$value='',$tabindex='',$m='') {
    $s = $m."<tr>\n".
         $m."  <td align=\"right\">{$prompt}</td>\n".
         $m."  <td align=\"left\"><input type=\"text\" name=\"{$name}\" value=\"{$value}\"";
    if (!empty($tabindex)) {
        $s .= " tabindex=\"{$tabindex}\"";
    }
    $s .= " size=\"25\" maxlength=\"80\" class=\"textfield\"></td>\n".
          $m."</tr>\n";
    return $s;
} // login_dialog_text_input()

/** add a row with a password input field to the login dialog/table
 *
 * This generates HTML for another table row. Special feature: try to
 * suppress autocomplete via an extra parameter. See
 * {@link http://www.owasp.org/index.php/Guide_to_Authentication#Browser_remembers_passwords}
 *
 * @param string $prompt the text to show in the 1st column of the table row
 * @param string $name the name of the input field
 * @param int $tabindex (optional) determines in which order fields are accessed in the dialog
 * @param string $m (optional) margin to add for code readability
 * @return string constructed HTML
 */
function login_dialog_password_input($prompt,$name,$tabindex='',$m='') {
    $s = $m."<tr>\n".
         $m."  <td align=\"right\">{$prompt}</td>\n".
         $m."  <td align=\"left\"><input type=\"password\" name=\"{$name}\" value=\"\"";
    if (!empty($tabindex)) {
        $s .= " tabindex=\"{$tabindex}\"";
    }
    $s .= " size=\"25\" maxlength=\"80\" class=\"passwordfield\" autocomplete=\"off\"></td>\n".
          $m."</tr>\n";
    return $s;
} // login_dialog_password_input()

/** add a row with a submit button to the login dialog/table
 *
 * @param string $buttontext the text to show in the button
 * @param string $name the name of the button
 * @param int $tabindex (optional) determines in which order fields are accessed in the dialog
 * @param string $m (optional) margin to add for code readability
 * @return string constructed HTML
 */
function login_dialog_submit_input($buttontext,$name,$tabindex='',$m='') {
    $s = $m."<tr>\n".
         $m."  <td valign=\"middle\" align=\"center\" colspan=\"2\">\n".
         $m."    <input type=\"submit\" name=\"{$name}\" value=\"{$buttontext}\"";
    if (!empty($tabindex)) {
        $s .= " tabindex=\"{$tabindex}\"";
    }
    $s .= " class=\"button\">\n".
          $m."  </td>\n".
          $m."</tr>\n";
    return $s;
} // login_dialog_submit_input()

/** add a row with links to home page and forgot password dialog to the login dialog/table
 *
 * This constructs a link to the home page in the left hand dialog/table column
 * and optionally a link to the start of the forgot password dialog. The latter
 * is only displayed if $forgot is not empty.
 *
 * @param string $forgot anchor text to display with link to forgot password dialog
 * @param string $m (optional) margin to add for code readability
 * @return string constructed HTML
 */
function login_dialog_home_forgot_password($forgot,$m='') {
    global $CFG;
    global $WAS_SCRIPT_NAME;
    $home = t('home_page','loginlib');
    $s = $m."<tr>\n".
         $m."  <td valign=\"bottom\" align=\"left\">\n".
         $m."    <a href=\"{$CFG->www_short}/index.php\">{$home}</a>\n".
         $m."  </td>\n";
    if (empty($forgot)) {
        $s .=$m."  <td></td>\n";
    } else {
        $s .= $m."  <td valign=\"bottom\" align=\"right\">\n".
              $m."    <a href=\"{$WAS_SCRIPT_NAME}?login=".LOGIN_PROCEDURE_SEND_LAISSEZ_PASSER."\">{$forgot}</a>\n".
              $m."  </td>\n";
    }
    $s .= $m."</tr>\n";
    return $s;
} // login_dialog_home_forgot_password()

/** send a special one-time login code to the user via email
 *
 * This generates a temporary code with which the user can request
 * a new temporary password. This code can be used only once.
 * Note that this code is valid for only a limited time.
 * This code simply overwrites the bypass password (the temporary
 * password) in the user record. This means that if a phase 2
 * is pending, a new phase 1 will replace the old phase 2.
 *
 * The temporary code consists of digits and uppercase characters.
 * However, it is longer (20 characters) than the minimum password
 * length of 6, so a brute force on such a code will likely not
 * succeed (36^20 is much more than the usual 62^6).
 *
 * This routine also brings the user's record into 'bypass mode'.
 * This mode is reset to 'normal' after the user has successfully
 * changed her password.
 *
 * A log message recording the event is added via {@link logger()}.
 *
 * @param array $user the user record from database
 * @return bool FALSE on failure otherwise TRUE
 * @uses $CFG
 * @uses logger()
 */
function login_send_laissez_passer($user) {
    global $CFG;

    $laissez_passer = quasi_random_string(20,QUASI_RANDOM_DIGITS_UPPER);
    $minutes = intval($CFG->login_bypass_interval);
    $datim = strftime("%Y-%m-%d %T",time() + $minutes * 66); // 60 seconds plus 10% for good measure
    $user_id = intval($user['user_id']);
    $username = $user['username'];
    $mailto = $user['email'];
    $full_name = replace_crlf($user['full_name'],' ');
    $logmessage = sprintf('%s(): sending to %s (%d) %s (%s): ',__FUNCTION__,$username,$user_id,$mailto,$full_name);

    $fields = array('bypass_mode' => TRUE, 'bypass_hash' => $laissez_passer, 'bypass_expiry' => $datim);
    $where = array('user_id' => $user_id);
    $num_affected = db_update('users',$fields,$where);
    if (($num_affected === FALSE) || ($num_affected != 1)) {
        logger($logmessage.'failed',WLOG_INFO,$user_id);
        return FALSE;
    }

    $manual_url = "{$CFG->www}/index.php?login=".LOGIN_PROCEDURE_SEND_BYPASS;
    $auto_url = $manual_url."&username=".$username."&code=".$laissez_passer;
    $subject = replace_crlf(t('your_forgotten_password_subject1','loginlib'),' ');
    $message = t('forgotten_password_mailmessage1','loginlib',array(
                 '{AUTO_URL}' => $auto_url,
                 '{MANUAL_URL}' => $manual_url,
                 '{LAISSEZ_PASSER}' => $laissez_passer,
                 '{INTERVAL}' => $minutes,
                 '{REMOTE_ADDR}' => $_SERVER['REMOTE_ADDR']));

    /** make sure utility routines for creating/sending email messages are available */
    require_once($CFG->progdir.'/lib/email.class.php');
    $email = new Email;
    $email->set_mailto($mailto,$full_name);
    $email->set_subject($subject);
    $email->set_message($message);
    $retval = $email->send();
    $logmessage .= ($retval) ? 'success' : 'failed';
    logger($logmessage,WLOG_INFO,$user_id);
    return $retval;
} // login_send_laissez_passer()

/** send a new (temporary) password to the user via email
 *
 * This generates a new temporary password for the user,
 * stores it in the user record and sends an email message
 * to the user with the temporary password (in plain text)
 * and further instructions.
 *
 * Note that the password is valid only for a limited time;
 * sending a password in plain text appears to be an acceptable risk.
 * Note that the limited time is increased with 10% in order
 * to give the user a reasonable margin to enter the correct
 * password.
 *
 * Also note that the existing salt is used to salt the temporary
 * password; this makes it easier to check for validity of both
 * the regular password and the temporary password lateron.
 *
 * A log message recording the event is added via {@link logger()}.
 *
 * @param array $user an associative array with the user record
 * @return bool FALSE on failure, TRUE otherwise
 * @uses $CFG
 * @uses logger()
 */
function login_send_bypass($user) {
    global $CFG;

    $temporary_password = quasi_random_string(8,QUASI_RANDOM_DIGITS_UPPER_LOWER);
    $minutes = intval($CFG->login_bypass_interval);
    $datim = strftime("%Y-%m-%d %T",time() + $minutes * 66); // 60 seconds plus 10% for good measure
    $user_id = intval($user['user_id']);
    $username = $user['username'];
    $mailto = $user['email'];
    $full_name = replace_crlf($user['full_name'],' ');
    $logmessage = sprintf('%s(): sending to %s (%d) %s (%s): ',__FUNCTION__,$username,$user_id,$mailto,$full_name);

    $temporary_password_hash = password_hash($user['salt'],$temporary_password);

    $fields = array(
        'bypass_mode' => TRUE,
        'bypass_hash' => $temporary_password_hash,
        'bypass_expiry' => $datim);
    $where = array(
        'user_id' => $user_id);
    $num_affected = db_update('users',$fields,$where);
    if (($num_affected === FALSE) || ($num_affected != 1)) {
        logger($logmessage.'failed',WLOG_INFO,$user_id);
        return FALSE;
    }
    $subject = replace_crlf(t('your_forgotten_password_subject2','loginlib'),' ');
    $message = t('forgotten_password_mailmessage2','loginlib',array(
                 '{PASSWORD}' => $temporary_password,
                 '{INTERVAL}' => $minutes,
                 '{REMOTE_ADDR}' => $_SERVER['REMOTE_ADDR']));

    /** make sure utility routines for creating/sending email messages are available */
    require_once($CFG->progdir.'/lib/email.class.php');
    $email = new Email;
    $email->set_mailto($mailto,$full_name);
    $email->set_subject($subject);
    $email->set_message($message);
    $retval = $email->send();

    $logmessage .= ($retval) ? 'success' : 'failed';
    logger($logmessage,WLOG_INFO,$user_id);
    return $retval;
} // login_send_bypass()

/** send email to user confirming password change
 *
 * This sends an email to the user's email addres confirming
 * that the user's password was changed. Note that the new
 * password is _NOT_ sent to the user.
 *
 * @param array $user an associative array with the user record
 * @return bool FALSE on failure, TRUE otherwise
 * @uses $CFG
 */
function login_send_confirmation($user) {
    global $CFG;

    $datim = strftime("%Y-%m-%d %T");
    $user_id = intval($user['user_id']);
    $mailto = $user['email'];
    $full_name = replace_crlf($user['full_name'],' ');
    $subject = replace_crlf(t('change_password_confirmation_subject','loginlib'),' ');
    $message = t('change_password_confirmation_message','loginlib',array(
                 '{DATETIME}' => $datim,
                 '{REMOTE_ADDR}' => $_SERVER['REMOTE_ADDR']));

    /** make sure utility routines for creating/sending email messages are available */
    require_once($CFG->progdir.'/lib/email.class.php');
    $email = new Email;
    $email->set_mailto($mailto,$full_name);
    $email->set_subject($subject);
    $email->set_message($message);
    $retval = $email->send();
    return $retval;
} // login_send_confirmation()

/** check the user's credentials in one of three ways
 *
 * This authenticates the user's credentials. There are some variants:
 *
 *  - by password: the user's password should match
 *  - by email: the user's email should match
 *  - by laissez passer: the one-time authentication code should match
 *
 * Strategy: we first read the active record for user $username in core.
 * If there is none, the user does not exist or is inactive => return FALSE.
 *
 * After that we check the validity of the token:
 *
 *  - a password is checked via the password hash or, if that fails, via the
 *    bypass hash. In the latter case, the bypass should not yet be expired
 *    (a bypass and a laissez_passer are valid until the 'bypass_expiry' time).
 *
 *  - an email address is checked caseINsensitive and without leading/trailing spaces
 *
 *  - a laissez_passer is check much the same way as the bypass password, be it
 *    that the code is stored 'as-is' rather than as a hash. The comparison is
 *    caseINsensitive.
 *
 * If the credentials are considered valid, an array with the user record
 * is returned, otherwise FALSE is returned.
 *
 * Because there are actually several checks to be done, we decided not to use SQL 
 * like: SELECT * FROM users WHERE username=$username AND password=$password,
 * not the least because we need to have the salt in our hands before we can
 * successfully compare password hashes.
 *
 * Note:
 * The 'special cases' (checking email, checking laissez_passer, checking bypass)
 * all have their token stripped from leading and trailing spaces. We don't want
 * to further confuse the user by not accepting a spurious space that was entered
 * in the heat of the moment when the user has 'lost' her password. Therefore we
 * also always trim the username. Rationale: usernames and also the generated
 * passwords etc. never have leading/trailing spaces. However, one cannot be sure
 * that a user has not entered a real password with leading/trailing space, so we
 * do NOT trim the $token in the first attempt in the case 'BY_PASSWORD' below.
 *
 * @param int $by_what_token which authentication token to use
 * @param string $username username the user entered in the dialog
 * @param string $token the token is either password, email or laissez_passer entered by the user
 * @return bool|array FALSE if invalid credentials, array with user record otherwise
 */
function authenticate_user($by_what_token,$username,$token) {
    $u = db_select_single_record('users','*',array('username' => trim($username), 'is_active' => TRUE));
    if ($u === FALSE) {
        return FALSE;
    }

    switch($by_what_token) {
    case BY_PASSWORD:
        if (password_hash_check($u['salt'],$token,$u['password_hash'])) { // no trim() on password, see above
            return $u;
        } elseif (db_bool_is(TRUE,$u['bypass_mode'])) {
            $expiry = string2time($u['bypass_expiry']);
            if ($expiry != FALSE) {
                if ((time() < $expiry) && (password_hash_check($u['salt'],trim($token),$u['bypass_hash']))) {
                    return $u;
                }
            }
        }
        break;

    case BY_EMAIL:
        if (strcasecmp(trim($token),trim($u['email'])) == 0) {
            return $u;
        }
        break;

    case BY_LAISSEZ_PASSER:
        $expiry = string2time($u['bypass_expiry']);
        if ($expiry != FALSE) {
            if ((time() < $expiry) && (strcmp(trim($token),trim($u['bypass_hash'])) == 0)) {
                return $u;
            }
        }
        break;

    default:
        trigger_error('internal error: unknown value for \'by_what_token\'');
        break;
    }
    return FALSE;
} // authenticate_user()

/** update the users database with a new (randomly salted) password and reset bypass mode to normal
 *
 * This updates the user record for user with user_id and stores the new password. The new
 * password and a new random salt are hashed together and the result is stored, together with
 * the new salt, overwriting the old salt and the old password hash. The bypass mode is reset
 * to normal and the bypass hash is reset. Return TRUE on success.
 *
 * @param int $user_id identify the user record by user_id
 * @param string $new_password the new password in plain text
 * @return bool FALSE on failure, TRUE otherwise
 */
function login_change_password($user_id,$new_password) {
    $new_salt = password_salt();
    $new_hash = password_hash($new_salt,$new_password);
    $fields = array(
        'salt' => $new_salt,
        'password_hash' => $new_hash,
        'bypass_mode' => FALSE,
        'bypass_hash' => NULL,
        'bypass_expiry' => NULL);
    $where = array('user_id' => $user_id);
    $num_affected = db_update('users',$fields,$where);
    if (($num_affected === FALSE) || ($num_affected != 1)) {
        return FALSE;
    }
    return TRUE;
} // login_change_password()

/** check the new passwords satisfy password requirements
 *
 * Users should provide the same password twice, to prevent typo's,
 * so both passwords should be equal. Also, the following requirements
 * should be satisfied:
 *  - the minimum password length should be MINIMUM_PASSWORD_LENGTH (default 6)
 *  - the new password should contain at least MINIMUM_PASSWORD_LOWERCASE lowercase letter a-z (default 1)
 *  - the new password should contain at least MINIMUM_PASSWORD_UPPERCASE upper case  letter A-Z (default 1)
 *  - the new password should contain at least MINIMUM_PASSWORD_DIGITS digit 0-9 (default 1)
 *  - the new password should not be the same as the previous password
 *  - the new password should not be the same as the bypass password (if any was issued)
 *
 * Note that the bypass-generator also should satisfy these rules. This
 * could lead to the thought of accepting the bypass-password as the 
 * permanent one. However, since this temporary password was sent to the
 * user in a plain-text email message, we should consider this a 'bad'
 * password.
 *
 * The minimum password length and other minimum values are not configurable 
 * (via $CFG) because that would make it too easy (too tempting) to give in 
 * and use weak passwords (too short, only lowercase, etc.) However, if your
 * really MUST, you could change the MINIMUM_PASSWORD_* constants defined above.
 *
 * Note that the check agains existing (temporary and regular) passwords is
 * not performed if the corresponding parameters are empty. If they are empty,
 * this routine only performs the first 4 checks in the list above.
 *
 * @param string $new_password1 new password
 * @param string $new_password2 new password again, to prevent typo's
 * @param string $salt (optional) the salt that was used to hash the old password and the bypass
 * @param string $password_hash (optional) the hashed existing password
 * @param string $bypass_hash (optional) the hashed bypass password
 * @return bool TRUE if new password is acceptable, FALSE otherwise
 */
function acceptable_new_password($new_password1,$new_password2,$salt='',$password_hash='',$bypass_hash='') {
    $n = strlen($new_password1);
    if (($n < MINIMUM_PASSWORD_LENGTH) || ($new_password1 != $new_password2)) {
        return FALSE;
    }
    $lower = 0;
    $upper = 0;
    $digit = 0;

    for ($i = 0; $i < $n; ++$i) {
        $c = $new_password1{$i};
        if (ctype_lower($c)) {
            ++$lower;
        } elseif (ctype_upper($c)) {
            ++$upper;
        } elseif (ctype_digit($c)) {
            ++$digit;
        }
    }
    if (($lower < MINIMUM_PASSWORD_LOWERCASE) ||
        ($upper < MINIMUM_PASSWORD_UPPERCASE) ||
        ($digit < MINIMUM_PASSWORD_DIGITS)) {
        return FALSE;
    }
    if (!empty($salt)) {
        $new_hash = password_hash($salt,$new_password1);
        if ($new_hash == $password_hash) {
            return FALSE;
        } elseif ((!empty($bypass_hash)) && ($new_hash == $bypass_hash)) {
            return FALSE;
        }
    }
    return TRUE;
} // acceptable_new_password()

/** calculate a hash from a salt and a password
 *
 * This routine constructs a hash of the combination of salt and password.
 * By default the md5() function is used to calculate a 32-character
 * long string of hexadcimal digits. If the parameter $algorithm is 1
 * then the sha1() function is used and a 40-character long string of
 * hexadecimal digits is returned.
 *
 * Note that we do not use the crypt() function because that could introduce
 * a portability issue. If a website is migrated to another machine, the used
 * crypt algoritm might no longer be available, and that would effectively lock
 * out all users. Both md5() and sha1() are standard PHP-functions (since 4.3.x)
 * and should be portable, which makes any installed table of users portable too.
 *
 * @param string $salt
 * @param string $password
 * @param int $algorithm (optional) algorithm to use: 0=md5, 1=sha1
 * @return string a hexadecimal representation of the hash of the combination of salt and password
 */
function password_hash($salt,$password,$algorithm=0) {
    if ($algorithm == 1) {
        $hash = sha1($salt.$password);
    } else {
        $hash = md5($salt.$password);
    }
    return $hash;
} // password_hash()

/** check equivalency of salt+password against hash
 *
 * This verifies whether the hash of $salt and $password
 * is the same as $hash. Note that the two hashes are compared
 * in a caseINsensitive way. Usually these hashes are using
 * lowercase hexadecimal digits but a caseINsensitive compare makes
 * A,...,F equivalent to a,...,f.
 *
 * If the length of the presented $hash is 40 characters, it
 * is assumed that the hash algorithm to use is sha1, otherwise
 * the default algorithm (md5) is used.
 *
 * @param string $salt salt
 * @param string $password password to check
 * @param string $hash hash to check against
 * @return bool TRUE if salt+password are equivalent to hash, FALSE otherwise
 * @uses password_hash()
 */ 
function password_hash_check($salt,$password,$hash) {
    $algorithm = (strlen($hash) == 40) ? 1 : 0;
    return (strcasecmp($hash,password_hash($salt,$password,$algorithm))==0) ? TRUE : FALSE;
} // password_hash_check()


/** generate a quasi random string to salt the password hash
 *
 * this generates a quasi-randomg string of digits and letters
 * to be used as a salt when calculating a password hash.
 *
 * @param int $length the number of characters in the generated string
 * @return string quasi-random string
 */
function password_salt($length=12) {
    return quasi_random_string($length,QUASI_RANDOM_DIGITS_UPPER_LOWER);
} // password_salt()


/** find out if a remote address is blacklisted at this time
 *
 * This routine checks if this remote address is blacklisted
 * in the login_failures table with a datim that lies in the future.
 * If this is the case, the address is indeed blacklisted and TRUE is
 * returned. Note that we sum the points much the same way as
 * in {@link login_failure_increment} rather than counting
 * 'blacklist-records'.
 *
 * @param string $remote_addr the remote IP-address to be checked
 * @return bool FALSE if the IP-address is not blacklisted, TRUE otherwise
 */
function login_is_blacklisted($remote_addr) {
    $retval = FALSE;
    $now = strftime('%Y-%m-%d %T');
    $where = 'remote_addr = '.db_escape_and_quote($remote_addr).
        ' AND failed_procedure = '.intval(LOGIN_PROCEDURE_BLACKLIST).
        ' AND '.db_escape_and_quote($now).' < datim';
    $record = db_select_single_record('login_failures','SUM(points) AS score',$where);
    if ($record !== FALSE) {
        $retval = (0 < intval($record['score']));
    } else {
        trigger_error('could not calculate blacklist score');
    }
    return $retval;
} // login_is_blacklisted()

/** deactivate all login failures/blacklisting scores for remote_addr
 *
 * This resets all the scores for all failed login attempts and blacklistings
 * for the specified IP-addres. The records in the login_failures table are
 * deactivated by deleting the records for this remote_addr.
 *
 * Note that the failed logins and the blacklistings are recorede in the log_messages
 * table via {@link logger()}. Therefore we can automatically keep this table
 * 'login_failures' clean without cron jobs.
 *
 * This routine resets _all_ scores, including any blacklisting that might still
 * be active, i.e. which has a datim in the future.
 *
 * @param string $remote_addr the remote IP-address is the origin of the failure
 * @return bool FALSE on error, the number of deactivated failures on success
 */
function login_failure_reset($remote_addr) {
    return db_delete('login_failures',array('remote_addr' => $remote_addr));
} // login_failure_reset()

/** add remote_addr to the blacklist for specified interval (in seconds)
 *
 * @param string $remote_addr the remote IP-address is the origin of the failure
 * @param int $delay_in_seconds the number of seconds to put this address on the blacklist
 * @param string $username extra information, could be useful for troubleshooting afterwards
 * @return bool|int FALSE on error, the id of the inserted record on success
 */
function login_failure_blacklist_address($remote_addr,$delay_in_seconds,$username='') {
    $release_time = strftime('%Y-%m-%d %T',time() + intval($delay_in_seconds));
    $logmessage = 'login: blacklisting \''.$remote_addr.'\' until '.$release_time.': ';
    $retval = db_insert_into('login_failures',array(
        'remote_addr' => $remote_addr,
        'datim' => $release_time,
        'failed_procedure' => LOGIN_PROCEDURE_BLACKLIST,
        'points' => 1,
        'username' => $username));
    $logmessage .= ($retval === FALSE) ? 'failed' : 'success';
    if ($retval !== FALSE) {
        $retval = db_last_insert_id('login_failures','login_failure_id');
    }
    if ($delay_in_seconds > LOGIN_FAILURE_DELAY_SECONDS) {
        // only record a 'real' blacklisting in logs, the 3-second blacklisting is not interesting, really
        logger($logmessage);
    }
    return $retval;
} // login_failure_blacklist_address()

/** delay execution of this script for a few seconds and blacklist the remote_addr during the delay
 *
 * This immediately blacklists the remote address for LOGIN_FAILURE_DELAY_SECONDS
 * seconds. Once that is done, the execution is delayed for that same period of
 * time. After the delay, the temporary blacklisting is removed from the table.
 * The whole purpose of this rapid succession of an INSERT and a DELETE is to prevent
 * brute force attack scripts that do not wait for an answer and/or use multiple
 * connections. This routine defeats that trick, because nothing can be done when
 * an IP-address is blacklisted.
 *
 * @param string $remote_addr the remote IP-address that is the origin of the failure
 * @return bool|int FALSE on failure, or 1 on success
 * @uses $CFG
 */
function login_failure_delay($remote_addr) {
    global $CFG;
    $message = 'system: delay = '.LOGIN_FAILURE_DELAY_SECONDS;
    $retval = login_failure_blacklist_address($remote_addr,LOGIN_FAILURE_DELAY_SECONDS,$message);
    sleep(LOGIN_FAILURE_DELAY_SECONDS);
    if ($retval !== FALSE) {
        $retval = db_delete('login_failures',array('login_failure_id' => intval($retval)));
    }
    return $retval;
} // login_failure_delay()

/** add 1 point to score for a particular IP-address and failed procedure, return the new score
 *
 * This records a login failure in a table and returns the the number
 * of failures for the specified procedure in the past T1 minutes.
 *
 * @param string $remote_addr the remote IP-address that is the origin of the failure
 * @param int $procedure indicates in which procedure the user failed
 * @param string $username extra information, could be useful for troubleshooting afterwards
 * @return int the current score
 */
function login_failure_increment($remote_addr,$procedure,$username='') {
    global $CFG;
    // this array used to validate $procedure _and_ to make a human readable description with logger()
    static $procedure_names = array(
        LOGIN_PROCEDURE_NORMAL => 'normal login',
        LOGIN_PROCEDURE_CHANGE_PASSWORD => 'change password',
        LOGIN_PROCEDURE_SEND_LAISSEZ_PASSER => 'send laissez passer',
        LOGIN_PROCEDURE_SEND_BYPASS => 'send bypass'
        );
    $retval = 0;
    $procedure = intval($procedure);
    if (isset($procedure_names[$procedure])) {
        $now = strftime('%Y-%m-%d %T');
        $retval = db_insert_into('login_failures',array(
            'remote_addr' => $remote_addr,
            'datim' => $now,
            'failed_procedure' => $procedure,
            'points' => 1,
            'username' => $username));
        if ($retval !== FALSE) {
            $minutes = intval($CFG->login_failures_interval);
            $interval_begin = strftime('%Y-%m-%d %T',time() - $minutes * 60);
            $where = 'remote_addr = '.db_escape_and_quote($remote_addr).
                ' AND failed_procedure = '.$procedure.
                ' AND '.db_escape_and_quote($interval_begin).' < datim';
            $record = db_select_single_record('login_failures','SUM(points) AS score',$where);
            if ($record !== FALSE) {
                $retval = intval($record['score']);
            } else {
                logger('could not calculate failure score',WLOG_DEBUG);
            }
        } else {
            logger('could not increment failure count',WLOG_DEBUG);
        }
        logger('login: failed; procedure='.$procedure_names[$procedure].', count='.$retval.', username=\''.$username.'\'');
    } else {
        logger('internal error: unknown procedure',WLOG_DEBUG);
    }
    return $retval;
} // login_failure()

?>