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
# along with this program. If not, see http://websiteatschool.org/license.html

/** /program/install.php - the main entrypoint for website installation
 *
 * This is one of the main entry points for Website@School. Other main
 * entry points are /admin.php, /cron.php, /file.php and /index.php.
 * There is also /program/manual.php.  Main entry points all define the
 * constant WASENTRY. This is used in various include()ed files to
 * detect break-in attempts.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.org/license.html GNU AGPLv3+Additional Terms
 * @package wasinstall
 * @version $Id: install.php,v 1.1 2011/02/01 13:00:04 pfokker Exp $
 * @todo how prevent third party-access to install.php after initial install? .htaccess? !exists(../config.php)? 
 * @todo we should make sure that autosession is disabled in php.ini, otherwise was won't work
 * @todo we should make sure that register globals is off
 * @todo we should make sure that we can actually set cookies (necessary when logging in).
 */

/** Valid entry points define WASENTRY; prevents direct access to include()'s.  */
define('WASENTRY',__FILE__);
$WAS_SCRIPT_NAME = $GLOBALS['SCRIPT_NAME'];
global $DB;

define('INSTALL_DIALOG_LANGUAGE',         0);
define('INSTALL_DIALOG_INSTALLTYPE',      1);
define('INSTALL_DIALOG_LICENSE',          2);
define('INSTALL_DIALOG_DATABASE',         3);
define('INSTALL_DIALOG_CMS',              4);
define('INSTALL_DIALOG_USER',             5);
define('INSTALL_DIALOG_COMPATIBILITY',    6);
define('INSTALL_DIALOG_CONFIRM',          7);
define('INSTALL_DIALOG_FINISH',           8);
define('INSTALL_DIALOG_DONE',             9);
define('INSTALL_DIALOG_DOWNLOAD',        10);
define('INSTALL_DIALOG_CANCELLED',       11);
define('PROJECT_SITE','websiteatschool.org');

session_name('WASINSTALL');
session_start();
include_once(dirname(__FILE__).'/version.php'); // which version are we installing anyway?
$wizard = new InstallWizard();
$wizard->run();
session_write_close();
exit;


/** class for performing installation tasks
 *
 * Overview
 *
 * Dialog screens
 *
 * The installer basically consists of some six dialog screens
 * where the user is supposed to enter some data (e.g. language, install type, etc.).
 * Each of those screens has a [Next] button and most screens have a [Previous]
 * button. Also, every screen has a [Cancel] button.
 * The [Cancel] button always immediately leads to the Cancel screen and the whole process is
 * stopped (by resetting the collected information in $_SESSION['INSTALL']).
 * The [Next] button always validates/processes the data the user entered.
 * If the results are good, we go to the next step. If the processing fails,
 * we stay where we are (ie. the current dialog is re-displayed).
 * The [Previous] button backs up one step, without saving or storing the
 * user entered data; the user MUST press [Next] to save the data entered.
 *
 * The Finish-dialog
 *
 * The FINISH-dialog has no [Previous] button, because all the real work
 * is done when the user presses [Next] in the CONFIRM-dialog. This is a one-time
 * action (creating tables, filling with demodata, etc.) so it makes no sense to backup
 * at that point.
 *
 * The stage variable and backing up via the menu
 *
 * The variable 'stage' moves along with the highest dialog the user has
 * successfully reached. This variable is responsible for greying out/disabling the menu
 * options. The menu can be used to jump back a few steps in the procedure. However,
 * once the transition from CONFIRM to FINISH is made, it is no longer possible to
 * return to previous steps (it makes no sense to do so because at that point the
 * real work is already done).
 * The jump to a particular step/dialog is done via the GET-parameter 'step'. The
 * buttons all work via the POST'ed parameter dialog.
 *
 * Special cases
 *
 * There are a few special cases:
 *  - download: this yields an immediate download of the constructed config.php and no further
 *    dialog is displayed
 *  - done: this is a pseudo-dialog. In effect it is a redirect to the newly created site (either
 *    admin.php or index.php or perhaps manual.php.
 *
 */
class InstallWizard {
    /** @var string $messages collects error messages if any */
    var $messages = array();

    /** @var array $results collects outcome of various compatibility results in human readable form */
    var $results = array();

    /** @var string $license ready-to-use HTML-code with the text of the license from /program/license.html */
    var $license = '';

    /** constructor
     *
     * this constructs the install wizard and also makes sure that
     * the INSTALL-array (kept in the $_SESSION array) is initialised
     * with default values if it did not already exist.
     */
    function InstallWizard() {
        if (!isset($_SESSION['INSTALL'])) {
            $_SESSION['INSTALL'] = $this->get_default_install_values();
        }
        return;
    } // InstallWizard()


    /** main dispatcher for the Installation Wizard
     *
     * This routine termines what needs to be done and
     * does it by calling the corresponding workhorse routines.
     *
     * @return void work done and output stored in $output (or sent directly to user's browser in case of download)
     */
    function run() {
        if ($_SESSION['INSTALL']['WAS_VERSION'] != WAS_VERSION) {
            // weird? the version number changed while we are installing. Mmmm...
            $this->messages[] = $this->t('error_wrong_version');
            $dialog = INSTALL_DIALOG_CANCELLED;
        } elseif (isset($_POST['button_cancel'])) {
            $dialog = INSTALL_DIALOG_CANCELLED;
        } elseif (($error = $this->fetch_license($this->license)) != 0) {
            $params = array('{ERROR}' => $error, '{EMAIL}' => 'errors@websiteatschool.org');
            $this->messages[] = $this->t('error_fatal',$params);
            $dialog = INSTALL_DIALOG_CANCELLED;
        } elseif (isset($_POST['dialog'])) {
            $dialog = intval($_POST['dialog']);
            if (isset($_POST['button_previous'])) {
                if ($_SESSION['INSTALL']['stage'] < INSTALL_DIALOG_FINISH) {
                    $dialog = max(--$dialog, INSTALL_DIALOG_LANGUAGE);
                }
            } elseif (isset($_POST['button_next'])) {
                switch ($dialog) {
                case INSTALL_DIALOG_LANGUAGE:     $retval = $this->save_language();        break;
                case INSTALL_DIALOG_INSTALLTYPE:  $retval = $this->save_installtype();     break;
                case INSTALL_DIALOG_LICENSE:      $retval = $this->check_license();        break;
                case INSTALL_DIALOG_DATABASE:     $retval = $this->save_database();        break;
                case INSTALL_DIALOG_CMS:          $retval = $this->save_cms();             break;
                case INSTALL_DIALOG_USER:         $retval = $this->save_user();            break;
                case INSTALL_DIALOG_COMPATIBILITY:$retval = $this->check_compatibility();  break;
                case INSTALL_DIALOG_CONFIRM:      $retval = $this->perform_installation(); break;
                case INSTALL_DIALOG_FINISH:       $retval = $this->finish();               break;
                default:
                    $this->messages[] = "Internal error: cannot process dialog '$dialog'";
                    $retval = FALSE;
                    break;
                }
                if ($this->is_already_installed()) {
                    $dialog = INSTALL_DIALOG_CANCELLED;
                } elseif ($retval) {
                    $dialog = min(++$dialog,INSTALL_DIALOG_FINISH);
                    $_SESSION['INSTALL']['stage'] = max($dialog,$_SESSION['INSTALL']['stage']);
                } else {
                    $dialog = min($dialog,$_SESSION['INSTALL']['stage']);
                }
            } elseif (isset($_POST['button_ok'])) {
                $dialog = INSTALL_DIALOG_DONE;
            } // else don't move
        } elseif (isset($_GET['step'])) {
            $dialog = intval($_GET['step']);
            $stage = $_SESSION['INSTALL']['stage'];
            if ($stage < INSTALL_DIALOG_FINISH) {
                $dialog = max(INSTALL_DIALOG_LANGUAGE,min($dialog,$stage));
            } else {
                $dialog = max(INSTALL_DIALOG_FINISH,$dialog);
            }
        } else {
            // start over (again)
            $dialog = INSTALL_DIALOG_LANGUAGE;
            $_SESSION['INSTALL']['stage'] = $dialog;
        }

        if ($dialog ==INSTALL_DIALOG_DOWNLOAD) {
            // download is a special case
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="config.php"');
            echo $this->construct_config_php();
        } else {
            header('Content-Type: text/html; charset=UTF-8');
            switch ($dialog) {
            case INSTALL_DIALOG_LANGUAGE:     $this->show_dialog_language();     break;
            case INSTALL_DIALOG_INSTALLTYPE:  $this->show_dialog_installtype();  break;
            case INSTALL_DIALOG_LICENSE:      $this->show_dialog_license();      break;
            case INSTALL_DIALOG_DATABASE:     $this->show_dialog_database();     break;
            case INSTALL_DIALOG_CMS:          $this->show_dialog_cms();          break;
            case INSTALL_DIALOG_USER:         $this->show_dialog_user();         break;
            case INSTALL_DIALOG_COMPATIBILITY:$this->show_dialog_compatibility();break;
            case INSTALL_DIALOG_CONFIRM:      $this->show_dialog_confirm();      break;
            case INSTALL_DIALOG_FINISH:       $this->show_dialog_finish();       break;
            case INSTALL_DIALOG_DONE:         $this->end_session_and_redirect(); break;
            case INSTALL_DIALOG_CANCELLED:    $this->show_dialog_cancelled();    break;
            default:
                // this shouldn't happen...
                $this->messages[] = "Internal error: cannot display dialog '$dialog'";
                $this->show_dialog_language();
                break;
            }
        }
    } // run()

    // ==================================================================
    // =========================== WORKHORSES =========================== 
    // ==================================================================

    /** construct the language selection dialog
     *
     * this dialog allows the user to pick a language. The choices are
     * determined by looking for translation files in the file system,
     * specifically for files /program/install/languages/LL/install.php
     * where LL is the language code, see {@link get_list_of_install_languages()}.
     *
     * @param string $m margin for better readability of generated HTML-code
     * @return void HTML-code sent to browser
     */
    function show_dialog_language($m='      ') {
        global $WAS_SCRIPT_NAME;
        $dialogdef = $this->get_dialogdef_language();
        $dialog_title = $this->t('dialog_language');
        $help_topic = 'install#language';

        $content = $m."<h2>{$dialog_title}</h2>\n".
                   $m.$this->t('dialog_language_explanation')."\n".
                   $m."<p>\n".
                   $m."<form action=\"{$WAS_SCRIPT_NAME}\" method=\"POST\">\n".
                   $m."  <input type=\"hidden\" name=\"dialog\" value=\"".INSTALL_DIALOG_LANGUAGE."\">\n".
                   $this->render_dialog($dialogdef,$m.'  ').
                   $m."  <p>\n".
                   $m."  ".$this->button('next')."\n".
                   $m."  ".$this->button('cancel')."\n".
                   $m."</form>\n";

        $menu = $this->get_menu(INSTALL_DIALOG_LANGUAGE,$_SESSION['INSTALL']['stage']);
        echo  $this->get_page($dialog_title,$menu,$content,$help_topic);
    } // show_dialog_language()


    /** store the selected language
     *
     * This is the companion routine for {@link show_dialog_language()}.
     * It validates and stores the user-supplied language key.
     *
     * @return bool TRUE on success
     */
    function save_language() {
        $dialogdef = $this->get_dialogdef_language();
        $retval = TRUE; // assume success
        foreach ($dialogdef as $name => $item) {
            if (!$item['show']) {
                continue;
            }
            $value = (isset($_POST[$name])) ? trim($this->magic_unquote($_POST[$name])) : '';
            switch($name) {
            case 'language':
                if (isset($item['options'][$value])) {
                    $_SESSION['INSTALL']['language_key'] = $value;
                }
                break;
            }
        }
        return $retval;
    } // save_language()


    /** fill an array with necessary information for language dialog
     *
     * Note that this is a very light-weight implentation of the dialogdef
     * idea used in the main program: we don't do fancy stuff with labels,
     * hotkeys, etc. KISS, because I don't want to rely on all the libraries
     * of the main program with all their interconnections and dependencies;
     * the installer should more or less be a stand-alone application.
     *
     * @return array array filled with field definitions and prompts etc.
     */
    function get_dialogdef_language() {
        $dialogdef = array(
            'language' => array(
                'label' => $this->t('language_label'),
                'help' => $this->t('language_help'),
                'value' => $_SESSION['INSTALL']['language_key'],
                'show' => TRUE,
                'type' => 'l',
                'options' => $this->get_list_of_install_languages())
            );
        return $dialogdef;
    } // get_dialogdef_language()


    /** construct the installtype + high visibility selection dialog
     *
     * This dialog contains a radio button where the user selects 'standard'
     * or 'custom' and also a checkbox for high visibility. As always the
     * dialog ends with buttons to move forward, backward or to cancel the
     * installation process alltogether.
     *
     * @param string $m margin for better readability of generated HTML-code
     * @return void HTML-code sent to browser
     */
    function show_dialog_installtype($m='      ') {
        global $WAS_SCRIPT_NAME;
        $dialogdef = $this->get_dialogdef_installtype();
        $dialog_title = $this->t('dialog_installtype');
        $help_topic = 'install#installtype';

        $content = $m."<h2>{$dialog_title}</h2>\n".
                   $m.$this->t('dialog_installtype_explanation')."\n".
                   $m."<p>\n".
                   $m."<form action=\"{$WAS_SCRIPT_NAME}\" method=\"POST\">\n".
                   $m."  <input type=\"hidden\" name=\"dialog\" value=\"".INSTALL_DIALOG_INSTALLTYPE."\">\n".
                   $this->render_dialog($dialogdef,$m.'  ').
                   $m."  <p>\n".
                   $m."  ".$this->button('previous')."\n".
                   $m."  ".$this->button('next')."\n".
                   $m."  ".$this->button('cancel')."\n".
                   $m."</form>\n";

        $menu = $this->get_menu(INSTALL_DIALOG_INSTALLTYPE,$_SESSION['INSTALL']['stage']);
        echo  $this->get_page($dialog_title,$menu,$content,$help_topic);
    } // show_dialog_installtype()


    /** store the selected install type + high visibility flag
     *
     * This is the companion routine for {@link show_dialog_installtype()}.
     * It stores the user-supplied choices for custom install and high visibility.
     * The values in the radio button should be 0 for standard and 1 for custom install.
     * The value we keep is a boolean indicating a custom install yes or no.
     *
     * @return bool TRUE on success
     */
    function save_installtype() {
        $dialogdef = $this->get_dialogdef_installtype();
        $retval = TRUE; // assume success
        foreach ($dialogdef as $name => $item) {
            if (!$item['show']) {
                continue;
            }
            $value = (isset($_POST[$name])) ? trim($this->magic_unquote($_POST[$name])) : '';
            switch($name) {
            case 'installtype':
                if (isset($item['options'][$value])) {
                    $_SESSION['INSTALL']['install_type_custom'] = (intval($value) != 0) ? TRUE : FALSE;
                }
                break;
            case 'high_visibility':
                $_SESSION['INSTALL']['high_visibility'] = (intval($value) != 0) ? TRUE : FALSE;
                break;
            }
        }
        return $retval;
    } // save_installtype()


    /** fill an array with necessary information for installtype dialog
     *
     * Note that this is a very light-weight implentation of the dialogdef
     * idea used in the main program: we don't do fancy stuff with labels,
     * hotkeys, etc. KISS, because I don't want to rely on all the libraries
     * of the main program with all their interconnections and dependencies;
     * the installer should more or less be a stand-alone application.
     *
     * @return array array filled with field definitions and prompts etc.
     */
    function get_dialogdef_installtype() {
        $dialogdef = array(
            'installtype' => array(
                'label' => $this->t('installtype_label'),
                'help' => $this->t('installtype_help'),
                'value' => ($_SESSION['INSTALL']['install_type_custom']) ? '1' : '0',
                'show' => TRUE,
                'type' => 'l',
                'options' => array(
                    '0' => $this->t('installtype_option_standard'),
                    '1' => $this->t('installtype_option_custom')
                     )
                ),
            'high_visibility' => array(
                'label' => $this->t('high_visibility_label'),
                'help' => $this->t('high_visibility_help'),
                'value' => $_SESSION['INSTALL']['high_visibility'],
                'show' => TRUE,
                'type' => 'b')
            );
        return $dialogdef;
    } // get_dialogdef_installtype()


    /** construct a full license agreement and an input where the user must enter 'I agree'
     *
     * This constructs a (long) license agreement dialog. We more or less force the user
     * to actually scroll through it by having the input box + buttons after the agreement text.
     * Note that the phrase to enter into the box is also translated, it may be 'I agree' in
     * English but something else in other languages (done via translation file). If the user
     * already accepted the license, the 'I agree'-text is already displayed in the dialog, in
     * the current language. (A user could type 'I agree' in English, change the language into
     * Dutch, in which case the $value in the textbox would be something like 'Ik ga accoord'
     * or whatever the translation of 'I agree' is.) This is done via an extra level of translation
     * with the 'dialog_license_i_agree' translation in the prompt.
     *
     * Also, the instruction to enter the exact words are repeated near the bottom of the screen,
     * just to make sure the user understands what to do.
     *
     * Note that the phrase the user enters is compared to the requested
     * phrase in a case-INsensitive way (see {@link check_license()}.
     *
     * @param string $m margin for better readability of generated HTML-code
     * @return void HTML-code sent to browser
     */
    function show_dialog_license($m='      ') {
        global $WAS_SCRIPT_NAME;
        $dialog_title = $this->t('dialog_license');
        $help_topic = 'install#license';
        $iagree =  $this->t('dialog_license_i_agree');
        $value = ($_SESSION['INSTALL']['license_accepted']) ? $iagree : '';
        $you_must_accept = $this->t('dialog_license_you_must_accept',array('{IAGREE}' => $iagree));

        $content = $m."<h2>{$dialog_title}</h2>\n".
                   $m.$this->t('dialog_license_explanation')."\n".
                   $m."<p>\n".
                   $m.$you_must_accept."\n".
                   $m."<p>\n".
                   $m."<hr>\n".
                   $this->license."\n".
                   $m."<hr>\n".
                   $m."<p>\n".
                   $m.$you_must_accept."\n".
                   $m."<p>\n".
                   $m."<form action=\"{$WAS_SCRIPT_NAME}\" method=\"POST\">\n".
                   $m."  <input type=\"hidden\" name=\"dialog\" value=\"".INSTALL_DIALOG_LICENSE."\">\n".
                   $m."  <input type=\"text\" name=\"license\" value=\"".
                                htmlspecialchars($value)."\" size=\"50\" maxlength=\"240\">\n".
                   $m."  <p>\n".
                   $m."  ".$this->button('previous')."\n".
                   $m."  ".$this->button('next')."\n".
                   $m."  ".$this->button('cancel')."\n".
                   $m."</form>\n";

        $menu = $this->get_menu(INSTALL_DIALOG_LICENSE,$_SESSION['INSTALL']['stage']);
        echo  $this->get_page($dialog_title,$menu,$content,$help_topic);
    } // show_dialog_license()


    /** check if the user accepts the licences
     *
     * This is the companion routine for {@link show_dialog_license()}.
     * It checks whether the user dutyfully type 'I agree'.
     *
     * @return bool TRUE on success, otherwise FALSE + error information in $this->messages array
     */
    function check_license() {
        $iagree = $this->t('dialog_license_i_agree');
        if (isset($_POST['license'])) {
            $license = $this->magic_unquote($_POST['license']);
            $_SESSION['INSTALL']['license_accepted'] = (strcasecmp(trim($iagree),trim($license)) == 0) ? TRUE : FALSE;
        }
        $retval = $_SESSION['INSTALL']['license_accepted'];
        if ($retval != TRUE) {
            $a = array('{IAGREE}' => $iagree);
            $this->messages[] = $this->t('dialog_license_you_must_accept',$a);
        }
        return $retval;
    } // check_license()


    /** construct the dialog for database (server, host, username, password, etc.)
     *
     * This dialog contains the following fields:
     *  - db_type (pick from enumerated list)
     *  - db_server (varchar(240))
     *  - db_username (varchar(240))
     *  - db_password (varchar(240))
     *  - db_name (varchar(240))
     *  - db_prefix (varchar(240))
     * 
     * One field is suppressed in the dialog if the user selected 
     * a Standard installation:
     *  - db_prefix
     *
     * @param string $m margin for better readability of generated HTML-code
     * @return void HTML-code sent to browser
     */
    function show_dialog_database($m='      ') {
        global $WAS_SCRIPT_NAME;
        $dialogdef = $this->get_dialogdef_database();
        $dialog_title = $this->t('dialog_database');
        $help_topic = 'install#database';

        $content = $m."<h2>{$dialog_title}</h2>\n".
                   $m.$this->t('dialog_database_explanation')."\n".
                   $m."<p>\n".
                   $m."<form action=\"{$WAS_SCRIPT_NAME}\" method=\"POST\">\n".
                   $m."  <input type=\"hidden\" name=\"dialog\" value=\"".INSTALL_DIALOG_DATABASE."\">\n".
                   $this->render_dialog($dialogdef,$m.'  ').
                   $m."  <p>\n".
                   $m."  ".$this->button('previous')."\n".
                   $m."  ".$this->button('next')."\n".
                   $m."  ".$this->button('cancel')."\n".
                   $m."</form>\n";

        $menu = $this->get_menu(INSTALL_DIALOG_DATABASE,$_SESSION['INSTALL']['stage']);
        echo  $this->get_page($dialog_title,$menu,$content,$help_topic);
    } // show_dialog_database()


    /** validate database information
     *
     * This is the companion routine for {@link show_dialog_database()}.
     * It stores the user-supplied data about the database.
     * We always store the data in the global _SESSION, even if something goes
     * wrong. This makes that the user will use the latest values when re-doing
     * the dialog. However, only when the values are valid, the parameter db_validated
     * is set to TRUE. This is used lateron (in the confirmation phase).
     *
     * This routine doubles as a gate keeper. Every time the user makes a mistake,
     * an error counter is incremented and the script pauses for some time
     * (see {@link errorcount_bump()}). If there are too many errors, the script
     * resets the data collected sofar and the procedure starts from scratch.
     * This is a (probably futile) attempt to make it harder to brute force an entry
     * by repeatedly probing for database credentials. Unfortunately there is no easy
     * way (at least one I can think of) to protect this script from repeated break-in
     * attempts other than by simply removing or renaming this script. Oh well.
     *
     * The following tests are performed:
     *  - fields should not fail basic tests (min/max stringlength etc.)
     *  - the database is not supposed to be in the list of 'forbidden' databases (e.g. 'test' or 'mysql')
     *  - prefix must only use letters, digits or an underscore
     *  - prefix must start with a letter
     *
     * If the above conditions are not satisfied we bail out immediately,
     * without testing other information. Otherwise, we also perform these tests:
     *  - is it possible to connect to the database server
     *  - is it possible to select the specified database
     *  - are there no tables in the database that start with the prefix
     *
     * If something needs to be done about the prefix and we are in standard mode,
     * we automatically switch to custom mode, allowing the user to edit the prefix too.
     *
     * @return bool TRUE on success, otherwise FALSE + error information in $this->messages array
     */
    function save_database() {
        $forbidden_names = array('test','mysql'); // lowercase list of banned user/database names
        $custom = ($_SESSION['INSTALL']['install_type_custom']) ? TRUE : FALSE;
        $dialogdef = $this->get_dialogdef_database();
        $_SESSION['INSTALL']['db_validated'] = TRUE; // assume success

        // 1 -- retrieve the posted data
        foreach ($dialogdef as $name => $item) {
            if (!$item['show']) {
                continue;
            }
            $value = (isset($_POST[$name])) ? trim($this->magic_unquote($_POST[$name])) : '';
            switch($name) {
            case 'db_server':
            case 'db_username':
            case 'db_password':
            case 'db_name':
            case 'db_prefix':
                $_SESSION['INSTALL'][$name] = $value;
                break;

            case 'db_type':
                // db_type MUST be one of the options
                if (isset($item['options'][$value])) {
                    $_SESSION['INSTALL'][$name] = $value;
                }
                break;
            }
            if (!$this->validate($item,$value)) {
                $_SESSION['INSTALL']['db_validated'] = FALSE; // flag that user did not pass minimal tests
            }
        }

        // 2A -- validate the prefix (possible route for sql injection in the next step)
        $prefix = $_SESSION['INSTALL']['db_prefix'];
        if (($prefix != preg_replace('/[^A-Za-z0-9_]/','',$prefix)) || (ctype_alpha(substr($prefix,0,1)) == FALSE)) {
            $_SESSION['INSTALL']['db_validated'] = FALSE;
            $this->messages[] = $this->t('error_invalid_db_prefix',array('{FIELD}' => $dialogdef['db_prefix']['label']));
            if (!$_SESSION['INSTALL']['install_type_custom']) {
                $_SESSION['INSTALL']['install_type_custom'] = TRUE;
                $this->messages[] = $this->t('warning_switch_to_custom');
            }
        }
        // 2B -- forbidden database name?
        if (in_array(strtolower($_SESSION['INSTALL']['db_name']),$forbidden_names)) {
            $_SESSION['INSTALL']['db_validated'] = FALSE;
            $params = array(
                '{FIELD}' => $dialogdef['db_name']['label'],
                '{NAME}' => $_SESSION['INSTALL']['db_name']
                );
            $this->messages[] = $this->t('error_db_forbidden_name',$params);
        }

        // 2C -- if we encountered an error already, we bail out mmediately, possibly after sleeping a while
        if (!$_SESSION['INSTALL']['db_validated']) {
            $this->errorcount_bump();
            return $_SESSION['INSTALL']['db_validated'];
        }

        // 3 -- validate the database information, check for existing prefixed tables
        if ($_SESSION['INSTALL']['db_type'] == 'mysql') {
            $db = @mysql_connect($_SESSION['INSTALL']['db_server'],
                                 $_SESSION['INSTALL']['db_username'],
                                 $_SESSION['INSTALL']['db_password']);
            if ($db === FALSE) {
                $_SESSION['INSTALL']['db_validated'] = FALSE;
                $this->messages[] = $this->t('error_db_cannot_connect');
            } else {
                if (mysql_select_db($_SESSION['INSTALL']['db_name'],$db) === FALSE) {
                    $_SESSION['INSTALL']['db_validated'] = FALSE;
                    $this->messages[] = $this->t('error_db_cannot_select_db');
                } else {
                    $sql = sprintf("SHOW TABLES LIKE '%s%%'",mysql_real_escape_string($prefix));
                    $result = mysql_query($sql);
                    if (($result !== FALSE) && (mysql_num_rows($result) > 0)) {
                        $_SESSION['INSTALL']['db_validated'] = FALSE;
                        $params = array(
                            '{FIELD}' => $dialogdef['db_prefix']['label'],
                            '{PREFIX}' => htmlspecialchars($prefix)
                            );
                        $this->messages[] = $this->t('error_db_prefix_in_use',$params);
                        if (!$_SESSION['INSTALL']['install_type_custom']) {
                            $_SESSION['INSTALL']['install_type_custom'] = TRUE;
                            $this->messages[] = $this->t('warning_switch_to_custom');
                        }
                    }
                }
                mysql_close($db);
            }
    //  } elseif ($_SESSION['INSTALL']['db_type'] == 'postgresql') {
    //      ...
        } else {
            $_SESSION['INSTALL']['db_validated'] = FALSE;
            $this->messages[] = $this->t('error_db_unsupported',array('{DATABASE}' => $_SESSION['INSTALL']['db_type']));
        }
        if ($_SESSION['INSTALL']['db_validated']) {
            $this->errorcount_reset();
        } else {
            $this->errorcount_bump();
        }
        return $_SESSION['INSTALL']['db_validated'];
    } // save_database()


    /** fill an array with necessary information for the database dialog
     *
     * Note that this is a very light-weight implentation of the dialogdef
     * idea used in the main program: we don't do fancy stuff with labels,
     * hotkeys, etc. KISS, because I don't want to rely on all the libraries
     * of the main program with all their interconnections and dependencies;
     * the installer should more or less be a stand-alone application.
     *
     * @return array array filled with field definitions and prompts etc.
     */
    function get_dialogdef_database() {
        $custom = ($_SESSION['INSTALL']['install_type_custom']) ? TRUE : FALSE;
        $dialogdef = array(
            'db_type' => array(
                'label' => $this->t('db_type_label'),
                'help' => $this->t('db_type_help'),
                'value' => $_SESSION['INSTALL']['db_type'],
                'show' => TRUE,
                'type' => 'l',
                'options' => $this->get_options_db_type()),
            'db_server' => array(
                'label' => $this->t('db_server_label'),
                'help' => $this->t('db_server_help'),
                'value' => $_SESSION['INSTALL']['db_server'],
                'show' => TRUE,
                'type' => 's',
                'minlength' => 1,
                'maxlength' => 240),
            'db_username' => array(
                'label' => $this->t('db_username_label'),
                'help' => $this->t('db_username_help'),
                'value' => $_SESSION['INSTALL']['db_username'],
                'show' => TRUE,
                'type' => 's',
                'minlength' => 1,
                'maxlength' => 240),
            'db_password' => array(
                'label' => $this->t('db_password_label'),
                'help' => $this->t('db_password_help'),
                'value' => $_SESSION['INSTALL']['db_password'],
                'show' => TRUE,
                'type' => 'p',
                'minlength' => 1,
                'maxlength' => 240),
            'db_name' => array(
                'label' => $this->t('db_name_label'),
                'help' => $this->t('db_name_help'),
                'value' => $_SESSION['INSTALL']['db_name'],
                'show' => TRUE,
                'type' => 's',
                'minlength' => 1,
                'maxlength' => 240),
            'db_prefix' => array(
                'label' => $this->t('db_prefix_label'),
                'help' => $this->t('db_prefix_help'),
                'value' => $_SESSION['INSTALL']['db_prefix'],
                'show' => $custom,
                'type' => 's',
                'minlength' => 1,
                'maxlength' => 240)
            );
        return $dialogdef;
    } // get_dialogdef_database()


    /** construct the dialog for essential cms data (title, paths, e-mail address)
     *
     * This dialog contains the following fields:
     *  - website_title (varchar(255))
     *  - website_from_address (varchar(255))
     *  - website_replyto_address (varchar(255))
     *  - cms_dir (varchar(240))
     *  - cms_www (varchar(240))
     *  - cms_progdir (varchar(240))
     *  - cms_progwww (varchar(240))
     *  - cms_dataroot (varchar(240))
     *  - cms_demodata (boolean)
     *  - cms_demodata_password (varchar(255) (but only a sha1() or md5() hash is stored eventually)
     * 
     * Some fields are suppressed in the dialog if the user selected 
     * a Standard installation:
     *  - website_replyto_address: copied from website_from_address
     *  - cms_progdir: constructed from cms_dir
     *  - cms_progwww: constructed from cms_www
     *
     * @param string $m margin for better readability of generated HTML-code
     * @return void HTML-code sent to browser
     * @todo can we suppress even more fields here in case of a Standard installation?
     */
    function show_dialog_cms($m='      ') {
        global $WAS_SCRIPT_NAME;
        $dialogdef = $this->get_dialogdef_cms();
        $dialog_title = $this->t('dialog_cms');
        $help_topic = 'install#website';

        $content = $m."<h2>{$dialog_title}</h2>\n".
                   $m.$this->t('dialog_cms_explanation')."\n".
                   $m."<p>\n".
                   $m."<form action=\"{$WAS_SCRIPT_NAME}\" method=\"POST\">\n".
                   $m."  <input type=\"hidden\" name=\"dialog\" value=\"".INSTALL_DIALOG_CMS."\">\n".
                   $this->render_dialog($dialogdef,$m.'  ').
                   $m."  <p>\n".
                   $m."  ".$this->button('previous')."\n".
                   $m."  ".$this->button('next')."\n".
                   $m."  ".$this->button('cancel')."\n".
                   $m."</form>\n";

        $menu = $this->get_menu(INSTALL_DIALOG_CMS,$_SESSION['INSTALL']['stage']);
        echo  $this->get_page($dialog_title,$menu,$content,$help_topic);
    } // show_dialog_cms()


    /** validate and store the CMS-data the user supplied
     *
     * This is the companion routine for {@link show_dialog_cms()}.
     * It stores the user-supplied data about the website (paths etc.)
     * We always store the data in the global _SESSION, even if something goes
     * wrong. This makes that the user will use the latest values when re-doing
     * the dialog.
     *
     * We try to validate the specified directories: they should at least exist.
     * The datadirectory should also be writable by us. If it not exists we try
     * to create it (and remove it after the test). Note that the PHP safe_mode
     * may complicate things here.
     *
     * @return bool TRUE on success, otherwise FALSE + error information in $this->messages array
     * @todo also take safe_mode into account? Should that be a requirement for succesfull installation?
     */
    function save_cms() {
        $custom = ($_SESSION['INSTALL']['install_type_custom']) ? TRUE : FALSE;
        $dialogdef = $this->get_dialogdef_cms();
        $_SESSION['INSTALL']['cms_validated'] = TRUE; // assume success

        // 1 -- retrieve the posted data
        foreach ($dialogdef as $name => $item) {
            if (!$item['show']) {
                continue;
            }
            $value = (isset($_POST[$name])) ? trim($this->magic_unquote($_POST[$name])) : '';
            switch($name) {
            case 'cms_title':
            case 'cms_website_from_address':
            case 'cms_website_replyto_address':
            case 'cms_demodata_password':
                $_SESSION['INSTALL'][$name] = $value;
                break;

            case 'cms_dir':
            case 'cms_www':
            case 'cms_progdir':
            case 'cms_progwww':
            case 'cms_dataroot':
                // eat the trailing slash if any
                if ((substr($value,-1,1) == '/') ||(substr($value,-1,1) == '\\')) {
                  $value = substr($value,0,-1);
                }
                $_SESSION['INSTALL'][$name] = $value;
                break;

            case 'cms_demodata':
                $_SESSION['INSTALL']['cms_demodata'] = (intval($value)) ? TRUE : FALSE;
                break;
            }

            if (($name == 'cms_demodata_password') && (!$_SESSION['INSTALL']['cms_demodata'])) {
                // Don't even check minimum length of the password field if no demodata requested
                // Note: this only works if the boolean value of cms_demodata
                // is processed BEFORE cms_demodata_password. See also {@link get_dialogdef_cms()}.
                continue;
            } elseif (!$this->validate($item,$value)) {
                $_SESSION['INSTALL']['cms_validated'] = FALSE; // flag that user did not pass minimal tests
            }
        }

        // 2 -- massage the fields that were invisible in the standard dialog
        foreach ($dialogdef as $name => $item) {
            if (!$item['show']) {
                switch($name) {
                case 'cms_website_replyto_address':
                    $_SESSION['INSTALL'][$name] = $_SESSION['INSTALL']['cms_website_from_address'];
                    break;
                case 'cms_progdir':
                    $_SESSION['INSTALL'][$name] = $_SESSION['INSTALL']['cms_dir'].'/program'; 
                    break;
                case 'cms_progwww':
                    $_SESSION['INSTALL'][$name] = $_SESSION['INSTALL']['cms_www'].'/program'; 
                    break;
                }
            }
        }

        // 3 -- check out the directories
        // 3A -- check out cms_dir
        if (!is_dir($_SESSION['INSTALL']['cms_dir'])) {
            $_SESSION['INSTALL']['cms_validated'] = FALSE;
            $params = array(
                '{FIELD}' => $dialogdef['cms_dir']['label'],
                '{DIRECTORY}' => htmlspecialchars($_SESSION['INSTALL']['cms_dir'])
                );
            $this->messages[] = $this->t('error_not_dir',$params);
        }
        // 3B -- check out cms_progdir
        if (!is_dir($_SESSION['INSTALL']['cms_progdir'])) {
            $_SESSION['INSTALL']['cms_validated'] = FALSE;
            $params = array(
                '{FIELD}' => $dialogdef['cms_progdir']['label'],
                '{DIRECTORY}' => htmlspecialchars($_SESSION['INSTALL']['cms_progdir'])
                );
            $this->messages[] = $this->t('error_not_dir',$params);
            if (!$_SESSION['INSTALL']['install_type_custom']) {
                $_SESSION['INSTALL']['install_type_custom'] = TRUE;
                $this->messages[] = $this->t('warning_switch_to_custom');
            }
        }
        // 3C -- check out cms_dataroot
        $dataroot = $_SESSION['INSTALL']['cms_dataroot'];
        $testdir = $dataroot.'/test-'.md5($dataroot.strval(time())).'-test';
        if (!is_dir($dataroot)) {
            // dataroot does not exist, try to create it
            if (!@mkdir($dataroot,0700)) {
                $_SESSION['INSTALL']['cms_validated'] = FALSE;
                $params = array(
                    '{FIELD}' => $dialogdef['cms_dataroot']['label'],
                    '{DIRECTORY}' => htmlspecialchars($dataroot)
                    );
                $this->messages[] = $this->t('error_not_create_dir',$params);
            } else {
                if (!@mkdir($testdir,0700)) {
                    $_SESSION['INSTALL']['cms_validated'] = FALSE;
                    $params = array(
                        '{FIELD}' => $dialogdef['cms_dataroot']['label'],
                        '{DIRECTORY}' => htmlspecialchars($testdir)
                        );
                    $this->messages[] = $this->t('error_not_create_dir',$params);
                } else {
                    @rmdir($testdir); // remove, this was just a simple test
                }
                @rmdir($dataroot); // remove, this was just a simple test
            }
        } else {
            // dataroot does exist, but can we create a subdirectory there?
            if (!@mkdir($testdir,0700)) {
                $_SESSION['INSTALL']['cms_validated'] = FALSE;
                $params = array(
                    '{FIELD}' => $dialogdef['cms_dataroot']['label'],
                    '{DIRECTORY}' => htmlspecialchars($testdir)
                    );
                $this->messages[] = $this->t('error_not_create_dir',$params);
            } else {
                @rmdir($testdir); // remove, this was just a simple test
            }
        }

        // 4 -- Maybe check out the generic password for the demodata-accounts
        if ($_SESSION['INSTALL']['cms_demodata']) {
            $password = $_SESSION['INSTALL']['cms_demodata_password'];
            $label = $dialogdef['cms_demodata_password']['label'];
            if (!$this->validate_password($label,$password)) {
                $_SESSION['INSTALL']['cms_validated'] = FALSE;
            }
        }

        // 5 -- check out name clash (only relevant if demodata is requested)
        if ($_SESSION['INSTALL']['cms_validated']) {
            // sofar this dialog is successful, so we should move to the
            // next dialog. However, if the demodata-switch is now TRUE and we
            // have a webmaster name clash, we already invalidate the USER-dialog
            // and present the user with a warning
            $demodata = ($_SESSION['INSTALL']['cms_demodata']) ? TRUE : FALSE;
            $label = $this->t('user_username_label');
            $username = $_SESSION['INSTALL']['user_username'];
            if (!$this->check_for_nameclash($demodata,$label,$username)) {
                $_SESSION['INSTALL']['user_validated'] = FALSE;
            }
        }

        // 6 -- all done, return validation results
        return $_SESSION['INSTALL']['cms_validated'];
    } // save_cms()


    /** fill an array with necessary information for the cms dialog
     *
     * Note that this is a very light-weight implentation of the dialogdef
     * idea used in the main program: we don't do fancy stuff with labels,
     * hotkeys, etc. KISS, because I don't want to rely on all the libraries
     * of the main program with all their interconnections and dependencies;
     * the installer should more or less be a stand-alone application.
     *
     * Note: the order of 'cms_demodata' and 'cms_demodata_password' is important:
     * the field 'cms_demodata' must come first. If not, the validation of the
     * password is not skipped if demodata is left unchecked by the user. See
     * also {@link save_cms()}.
     *
     * @return array array filled with field definitions and prompts etc.
     */
    function get_dialogdef_cms() {
        $custom = ($_SESSION['INSTALL']['install_type_custom']) ? TRUE : FALSE;
        $dialogdef = array(
            'cms_title' => array(
                'label' => $this->t('cms_title_label'),
                'help' => $this->t('cms_title_help'),
                'value' => $_SESSION['INSTALL']['cms_title'],
                'show' => TRUE,
                'type' => 's',
                'minlength' => 1,
                'maxlength' => 255),
            'cms_website_from_address' => array(
                'label' => $this->t('cms_website_from_address_label'),
                'help' => $this->t('cms_website_from_address_help'),
                'value' => $_SESSION['INSTALL']['cms_website_from_address'],
                'show' => TRUE,
                'type' => 's',
                'minlength' => 1,
                'maxlength' => 255),
            'cms_website_replyto_address' => array(
                'label' => $this->t('cms_website_replyto_address_label'),
                'help' => $this->t('cms_website_replyto_address_help'),
                'value' => $_SESSION['INSTALL']['cms_website_replyto_address'],
                'show' => $custom,
                'type' => 's',
                'minlength' => 0,
                'maxlength' => 255),
            'cms_dir' => array(
                'label' => $this->t('cms_dir_label'),
                'help' => $this->t('cms_dir_help'),
                'value' => $_SESSION['INSTALL']['cms_dir'],
                'show' => TRUE,
                'type' => 's',
                'minlength' => 1,
                'maxlength' => 240),
            'cms_www' => array(
                'label' => $this->t('cms_www_label'),
                'help' => $this->t('cms_www_help'),
                'value' => $_SESSION['INSTALL']['cms_www'],
                'show' => TRUE,
                'type' => 's',
                'minlength' => 1,
                'maxlength' => 240),
            'cms_progdir' => array(
                'label' => $this->t('cms_progdir_label'),
                'help' => $this->t('cms_progdir_help'),
                'value' => $_SESSION['INSTALL']['cms_progdir'],
                'show' => $custom,
                'type' => 's',
                'minlength' => 1,
                'maxlength' => 240),
            'cms_progwww' => array(
                'label' => $this->t('cms_progwww_label'),
                'help' => $this->t('cms_progwww_help'),
                'value' => $_SESSION['INSTALL']['cms_progwww'],
                'show' => $custom,
                'type' => 's',
                'minlength' => 1,
                'maxlength' => 240),
            'cms_dataroot' => array(
                'label' => $this->t('cms_datadir_label'),
                'help' => $this->t('cms_datadir_help'),
                'value' => $_SESSION['INSTALL']['cms_dataroot'],
                'show' => TRUE,
                'type' => 's',
                'minlength' => 1,
                'maxlength' => 240),
            'cms_demodata' => array(
                'label' => $this->t('cms_demodata_label'),
                'help' => $this->t('cms_demodata_help'),
                'value' => $_SESSION['INSTALL']['cms_demodata'],
                'show' => TRUE,
                'type' => 'b'),
            'cms_demodata_password' => array(
                'label' => $this->t('cms_demodata_password_label'),
                'help' => $this->t('cms_demodata_password_help'),
                'value' => $_SESSION['INSTALL']['cms_demodata_password'],
                'show' => TRUE,
                'type' => 'p',
                'minlength' => 8,
                'maxlength' => 255)
            );
        return $dialogdef;
    } // get_dialogdef_cms()


    /** construct the dialog for the first user account
     *
     * This dialog contains the following fields:
     *  - user_username (varchar(255))
     *  - user_full_name (varchar(255))
     *  - user_email (varchar(255))
     *  - user_password (varchar(255) (but only a sha1() or md5() hash is stored eventually)
     *
     * An additional feature of this routine is to set a default email address
     * for the account, by copying the address that can be found in the Reply-To:
     * field that was entered earlier (or constructed from the From: address).
     *
     * @param string $m margin for better readability of generated HTML-code
     * @return void HTML-code sent to browser
     */
    function show_dialog_user($m='      ') {
        global $WAS_SCRIPT_NAME;
        if (empty($_SESSION['INSTALL']['user_email'])) {
            $_SESSION['INSTALL']['user_email'] = trim($_SESSION['INSTALL']['cms_website_replyto_address']);
        }
        $dialogdef = $this->get_dialogdef_user();
        $dialog_title = $this->t('dialog_user');
        $help_topic = 'install#user';

        $content = $m."<h2>{$dialog_title}</h2>\n".
                   $m.$this->t('dialog_user_explanation')."\n".
                   $m."<p>\n".
                   $m."<form action=\"{$WAS_SCRIPT_NAME}\" method=\"POST\">\n".
                   $m."  <input type=\"hidden\" name=\"dialog\" value=\"".INSTALL_DIALOG_USER."\">\n".
                   $this->render_dialog($dialogdef,$m.'  ').
                   $m."  <p>\n".
                   $m."  ".$this->button('previous')."\n".
                   $m."  ".$this->button('next')."\n".
                   $m."  ".$this->button('cancel')."\n".
                   $m."</form>\n";

        $menu = $this->get_menu(INSTALL_DIALOG_USER,$_SESSION['INSTALL']['stage']);
        echo  $this->get_page($dialog_title,$menu,$content,$help_topic);
    } // show_dialog_user()


    /** validate and store the data for the first user account
     *
     * This is the companion routine for {@link show_dialog_user()}.
     * It stores the information about the first user account.
     * We always store the data in the global _SESSION, even if something goes
     * wrong. This makes that the user will use the latest values when re-doing
     * the dialog.
     *
     * We try to validate at least the password:
     *  - minimum of 8 characters
     *  - minimum 1 upper case, 1 lower case, 1 digit
     * Also, both username and full name should not be empty
     *
     * @return bool TRUE on success, otherwise FALSE + error information in $this->messages array
     */
    function save_user() { 
        $dialogdef = $this->get_dialogdef_user();
        $_SESSION['INSTALL']['user_validated'] = TRUE; // assume success

        // 1 -- retrieve the posted data
        foreach ($dialogdef as $name => $item) {
            if (!$item['show']) {
                continue;
            }
            $value = (isset($_POST[$name])) ? trim($this->magic_unquote($_POST[$name])) : '';
            switch($name) {
            case 'user_full_name':
            case 'user_username':
            case 'user_password':
            case 'user_email':
                $_SESSION['INSTALL'][$name] = $value;
                break;
            }
            if (!$this->validate($item,$value)) {
                $_SESSION['INSTALL']['user_validated'] = FALSE; // flag that userdata did not pass minimal tests
            }
        }

        // 2 -- check out password
        $label = $dialogdef['user_password']['label'];
        $password = $_SESSION['INSTALL']['user_password'];
        if (!$this->validate_password($label,$password)) {
            $_SESSION['INSTALL']['user_validated'] = FALSE;
        }

        // 3 -- check out name clash (only relevant if demodata is requested)
        $demodata = ($_SESSION['INSTALL']['cms_demodata']) ? TRUE : FALSE;
        $label = $dialogdef['user_username']['label'];
        $username = $_SESSION['INSTALL']['user_username'];
        if (!$this->check_for_nameclash($demodata,$label,$username)) {
            $_SESSION['INSTALL']['user_validated'] = FALSE;
        }

        return $_SESSION['INSTALL']['user_validated'];
    } // save_user()


    /** fill an array with necessary information for the first user dialog
     *
     * Note that this is a very light-weight implentation of the dialogdef
     * idea used in the main program: we don't do fancy stuff with labels,
     * hotkeys, etc. KISS, because I don't want to rely on all the libraries
     * of the main program with all their interconnections and dependencies;
     * the installer should more or less be a stand-alone application.
     *
     * @return array array filled with field definitions and prompts etc.
     */
    function get_dialogdef_user() {
        $dialogdef = array(
            'user_full_name' => array(
                'label' => $this->t('user_full_name_label'),
                'help' => $this->t('user_full_name_help'),
                'value' => $_SESSION['INSTALL']['user_full_name'],
                'show' => TRUE,
                'type' => 's',
                'minlength' => 1,
                'maxlength' => 255),
            'user_username' => array(
                'label' => $this->t('user_username_label'),
                'help' => $this->t('user_username_help'),
                'value' => $_SESSION['INSTALL']['user_username'],
                'show' => TRUE,
                'type' => 's',
                'minlength' => 1,
                'maxlength' => 255),
            'user_password' => array(
                'label' => $this->t('user_password_label'),
                'help' => $this->t('user_password_help'),
                'value' => $_SESSION['INSTALL']['user_password'],
                'show' => TRUE,
                'type' => 'p',
                'minlength' => 8,
                'maxlength' => 255),
            'user_email' => array(
                'label' => $this->t('user_email_label'),
                'help' => $this->t('user_email_help'),
                'value' => $_SESSION['INSTALL']['user_email'],
                'show' => TRUE,
                'type' => 's',
                'minlength' => 0,
                'maxlength' => 255)
            );
        return $dialogdef;
    } // get_dialogdef_user()


    /** construct the comptibility overview
     *
     * this routine displays a tabular overview of minimal compatibility requirements
     * and the current status/testresults. The table is constructed in a subroutine;
     * we only deal with the display here.
     *
     * Q: Why here, at the last stop before installation?
     * A: Because we otherwise would leak information about our environment
     *    to complete strangers (assuming anyone can execute this script).
     *
     * @param string $m margin for better readability of generated HTML-code
     * @return void HTML-code sent to browser
     * @todo more tests to perform here: safe mode, memory limit, processing time limit, register globals
     */
    function show_dialog_compatibility($m='      ') {
        global $WAS_SCRIPT_NAME;
        $dialog_title = $this->t('dialog_compatibility');
        $help_topic = 'install#compatibility';
        $results = array();
        $checked = $this->check_compatibility($results);

        $content = $m."<h2>{$dialog_title}</h2>\n".
                   $m.$this->t('dialog_compatibility_explanation')."\n".
                   $m."<p>\n".
                   $m."<form action=\"{$WAS_SCRIPT_NAME}\" method=\"POST\">\n".
                   $m."  <input type=\"hidden\" name=\"dialog\" value=\"".INSTALL_DIALOG_COMPATIBILITY."\">\n";

        $class = "even tight";
        $content .= $m."<p>\n".
                    $m."<table width=\"95%\">\n".
                    $m."  <tr class=\"header\">\n".
                    $m."    <th class=\"header\">".$this->t('compatibility_label')."</th>\n".
                    $m."    <th class=\"header\">".$this->t('compatibility_value')."</th>\n".
                    $m."    <th class=\"header\">".$this->t('compatibility_result')."</th>\n".
                    $m."  </tr>\n";
        foreach($this->results as $result) {
                $class = ($class == "even tight") ? "odd tight" : "even tight";
                $content .= $m."  <tr class=\"{$class}\">\n".
                            $m."    <td class=\"{$class}\">".$result['label']."</td>\n".
                            $m."    <td class=\"{$class}\">".$result['value']."</td>\n".
                            $m."    <td class=\"{$class}\">".$result['result']."</td>\n".
                            $m."  </tr>\n";
        }
        $content .= $m."</table>\n".
                    $m."  <p>\n".
                    $m."  ".$this->button('previous')."\n";
        if ($checked) {
            $content .= $m."  ".$this->button('next')."\n";
        }
        $content .= $m."  ".$this->button('cancel')."\n".
                    $m."</form>\n";

        $menu = $this->get_menu(INSTALL_DIALOG_COMPATIBILITY,$_SESSION['INSTALL']['stage']);
        echo  $this->get_page($dialog_title,$menu,$content,$help_topic);
    } // show_dialog_compatibility()


    /** check certain compatibility issues and optionally return test results
     *
     * this routine performs a few tests and returns an overall go/nogo signal
     * Human readable test results are stored in $this-results.
     * Return TRUE on passing all tests, FALSE otherwise + errors in $this->messages
     *
     * @return bool TRUE if all tests passed, FALSE otherwise + information in $this->messages
     * @todo add more tests, e.g. for gd, safe_mode, memory limit, etc.
     */
    function check_compatibility() {
        $this->results = array(); // collect human-readable results, start from scratch
        $retval = TRUE; // assume success

        // 0 -- website@school version
        $url = sprintf('http://%s/version/?release=%s&amp;date=%s&amp;version=%s&amp;check=%s',PROJECT_SITE,
                rawurlencode(WAS_RELEASE), rawurlencode(WAS_RELEASE_DATE), rawurlencode(WAS_VERSION),
                (WAS_ORIGINAL) ? '1' : '0');

        $link = "<a href=\"$url\" target=\"_blank\" ".
                "title=\"".$this->t('compatibility_websiteatschool_version_check_title')."\" ".
                "onclick=\"window.open('$url','','left=100,top=100,width=640,height=320'); return false;\">".
                $this->t('compatibility_websiteatschool_version_check')."</a>\n";
        $params = array('{RELEASE}' => WAS_RELEASE,
                        '{VERSION}' => WAS_VERSION,
                        '{RELEASE_DATE}' => WAS_RELEASE_DATE);
        $label = $this->t('compatibility_websiteatschool_version_label');
        $value = $this->t('compatibility_websiteatschool_version_value',$params);
        $result = $link;
        $this->results[] = array('label' => $label, 'value' => $value, 'result' => $result);

        // 1 -- webserver version
        $label = $this->t('compatibility_webserver_label');
        $value = (isset($_SERVER['SERVER_SOFTWARE'])) ? $_SERVER['SERVER_SOFTWARE'] : '?';
        $result = $this->t('compatibility_ok');
        $this->results[] = array('label' => $label, 'value' => $value, 'result' => $result);

        // 2 -- database information
        $label = $this->t('compatibility_database_label');
        if ($_SESSION['INSTALL']['db_type'] == 'mysql') {
            $db = @mysql_connect($_SESSION['INSTALL']['db_server'],
                                 $_SESSION['INSTALL']['db_username'],
                                 $_SESSION['INSTALL']['db_password']);
            if ($db === FALSE) {
                $value = $this->t('db_type_option_mysql');
                $result = $this->t('error_db_cannot_select_db');
                $this->messages[] = $result;
                $retval = FALSE;
            } else {
                $value = $this->t('db_type_option_mysql').' '.mysql_get_server_info();
                $result = $this->t('compatibility_ok');
                mysql_close($db);
            }
        } else {
            $value = $_SESSION['INSTALL']['db_type'];
            $result = $this->t('error_db_unsupported',array('{DATABASE}' => $value));
            $this->messages[] = $result;
            $retval = FALSE;
        }
        $this->results[] = array('label' => $label, 'value' => $value, 'result' => $result);

        // 3 -- phpversion
        $label = $this->t('compatibility_phpversion_label');
        $value = phpversion();
        if (!function_exists('version_compare')) {
            $result = $this->t('compatibility_phpversion_obsolete');
            $this->messages[] = $result;
            $retval = FALSE;
        } elseif (version_compare($value,'4.3.0') < 0) {
            $result = $this->t('compatibility_phpversion_too_old',array('{MIN_VERSION}' => '4.3.0'));
            $this->messages[] = $result;
            $retval = FALSE;
        } else {
            $result = $this->t('compatibility_ok');
        }
        $this->results[] = array('label' => $label, 'value' => $value, 'result' => $result);

        // 4 -- session autostart
        $label = $this->t('compatibility_autostart_session_label');
        if (intval(ini_get('session.autostart')) != 0) {
            $value = $this->t('yes');
            $result = $this->t('compatibility_autostart_session_fail');
            $this->messages[] = $result;
            $retval = FALSE;
        } else {
            $value = $this->t('no');
            $result = $this->t('compatibility_ok');
        }
        $this->results[] = array('label' => $label, 'value' => $value, 'result' => $result);

        // 5 -- safe mode (deprecated as of PHP 5.3.0 so there)
        $label = $this->t('compatibility_php_safemode_label');
        if (intval(ini_get('safe_mode')) != 0) {
            $value = $this->t('compatibility_php_safemode_warning');
            $result = $this->t('compatibility_warning');
            $this->messages[] = $value;
        } else {
            $value = $this->t('no');
            $result = $this->t('compatibility_ok');
        }
        $this->results[] = array('label' => $label, 'value' => $value, 'result' => $result);

        // 6 -- file uploads
        $label = $this->t('compatibility_file_uploads_label');
        if (intval(ini_get('file_uploads')) == 0) {
            $value = $this->t('no');
            $result = $this->t('compatibility_file_uploads_fail');
            $this->messages[] = $result;
            $retval = FALSE;
        } else {
            $value = $this->t('yes');
            $result = $this->t('compatibility_ok');
        }
        $this->results[] = array('label' => $label, 'value' => $value, 'result' => $result);

        // 7 -- GD Support
        $label = $this->t('compatibility_gd_support_label');
        $value = '';
        if ($this->gd_supported($value)) {
            $result = $this->t('compatibility_ok');
        } else {
            $result = $this->t('compatibility_warning');
            $this->messages[] = $label.': '.$value;
        }
        $this->results[] = array('label' => $label, 'value' => $value, 'result' => $result);

        // 8 -- clamscan
        //
        // If we can find clamscan, we make sure that scanning uploads is enforced.
        // The side effect is to display the version of ClamAV. It is not fatal if
        // ClamAV is not installed, even though it is recommended. The user can decide
        // to enter the path to clamdscan (or clamscan) in the Site Configuration later.
        //
        $label = $this->t('compatibility_clamscan_label');
        $clamscan_path = '';
        $clamscan_version = '';
        if ($this->clamscan_installed($clamscan_path,$clamscan_version)) {
            $value = $clamscan_version;
            $result = $this->t('compatibility_ok');
            $_SESSION['INSTALL']['clamscan_path'] = $clamscan_path;
            $_SESSION['INSTALL']['clamscan_mandatory'] = TRUE; // If clamscan is available we should enforce it
        } else {
            $value = $this->t('compatibility_clamscan_not_available');
            $result = '';
            $_SESSION['INSTALL']['clamscan_path'] = '';
            $_SESSION['INSTALL']['clamscan_mandatory'] = FALSE;
        }
        $this->results[] = array('label' => $label, 'value' => $value, 'result' => $result);

        $_SESSION['INSTALL']['compatibility_checked'] = $retval;
        return $_SESSION['INSTALL']['compatibility_checked'];
    } // check_compatibility()


    /** construct the overview/confirmation dialog
     *
     * This dialog contains an overview of the information entered
     * (excluding the passwords which are visually replaced with asterisks).
     * This is the last chance the user gets to change the data entered.
     * Once the user presses the [Next] button, the actual installation
     * takes off.
     *
     * @param string $m margin for better readability of generated HTML-code
     * @return void HTML-code sent to browser
     */
    function show_dialog_confirm($m='      ') {
        global $WAS_SCRIPT_NAME;
        $settings = array(
            'dialog_database' => array(
                'db_type_label' => $this->t('db_type_option_'.$_SESSION['INSTALL']['db_type']),
                'db_server_label' => $_SESSION['INSTALL']['db_server'],
                'db_username_label' => $_SESSION['INSTALL']['db_username'],
                'db_password_label' => '********',
                'db_name_label' => $_SESSION['INSTALL']['db_name'],
                'db_prefix_label' => $_SESSION['INSTALL']['db_prefix']
                ),
            'dialog_cms' => array(
                'cms_title_label' => $_SESSION['INSTALL']['cms_title'],
                'cms_website_from_address_label' => $_SESSION['INSTALL']['cms_website_from_address'],
                'cms_website_replyto_address_label' => $_SESSION['INSTALL']['cms_website_replyto_address'],
                'cms_dir_label' => $_SESSION['INSTALL']['cms_dir'],
                'cms_www_label' => $_SESSION['INSTALL']['cms_www'],
                'cms_progdir_label' => $_SESSION['INSTALL']['cms_progdir'],
                'cms_progwww_label' => $_SESSION['INSTALL']['cms_progwww'],
                'cms_datadir_label' => $_SESSION['INSTALL']['cms_dataroot'],
                'cms_demodata_label' => ($_SESSION['INSTALL']['cms_demodata']) ? $this->t('yes') : $this->t('no'),
                'cms_demodata_password_label' => ($_SESSION['INSTALL']['cms_demodata']) ? '********' : ''
                ),
            'dialog_user' => array(
                'user_full_name_label' => $_SESSION['INSTALL']['user_full_name'],
                'user_username_label' => $_SESSION['INSTALL']['user_username'],
                'user_password_label' => '********',
                'user_email_label' => $_SESSION['INSTALL']['user_email']
                )
            );
        $dialog_title = $this->t('dialog_confirm');
        $help_topic = 'install#confirm';

        $content = $m."<h2>{$dialog_title}</h2>\n".
                   $m.$this->t('dialog_confirm_explanation')."\n";

        foreach($settings as $dialog_name => $items) {
            $class = "even tight";
            $content .= $m."<p>\n".
                        $m."<table width=\"95%\">\n".
                        $m."  <tr class=\"header\">\n".
                        $m."    <th colspan=\"2\" class=\"header\">".htmlspecialchars($this->t($dialog_name))."</th>\n".
                        $m."  </tr>\n";
            foreach($items as $item => $value) {
                $class = ($class == "even tight") ? "odd tight" : "even tight";
                $content .= $m."  <tr class=\"{$class}\">\n".
                            $m."    <td width=\"25%\" class=\"{$class}\">".$this->t($item)."</td>\n".
                            $m."    <td width=\"75%\" class=\"{$class}\">".htmlspecialchars($value)."</td>\n".
                            $m."  </tr>\n";
            }
            $content .= $m."</table>\n";
        }
        $content .= $m."  <p>\n".
                    $m.$this->t('dialog_confirm_printme')."\n".
                    $m."  <p>\n".
                    $m."<form action=\"{$WAS_SCRIPT_NAME}\" method=\"POST\">\n".
                    $m."  <input type=\"hidden\" name=\"dialog\" value=\"".INSTALL_DIALOG_CONFIRM."\">\n".
                    $m."  ".$this->button('previous')."\n";

        // only show [Next] if everything looks OK.
        if ($this->check_validation()) {
            $content .= $m."  ".$this->button('next')."\n";
        }

        $content .= $m."  ".$this->button('cancel')."\n".
                    $m."</form>\n".
                    $m."<p>\n";
        $menu = $this->get_menu(INSTALL_DIALOG_CONFIRM,$_SESSION['INSTALL']['stage']);
        echo  $this->get_page($dialog_title,$menu,$content,$help_topic);
    } // show_dialog_confirm()


    /** shorthand to check the validation status of the relevant dialogs
     *
     * this checks the various validation flags. If a flag is false, the corresponding
     * error message is added to $this->messages and the function returns FALSE.
     *
     * @return bool TRUE if all tests are passed, FALSE otherwise + messages in $this->messages
     */
    function check_validation() {
        $checks = array(
            'license_accepted' => 'dialog_license',
            'db_validated' => 'dialog_database',
            'cms_validated' => 'dialog_cms',
            'user_validated' => 'dialog_user',
            'compatibility_checked' => 'dialog_compatibility'
            );
        $retval = TRUE; // assume success
        foreach($checks as $check => $menu_item) {
            if (!$_SESSION['INSTALL'][$check]) {
                $retval = FALSE;
                $this->messages[] = $this->t('error_bad_data',array('{MENU_ITEM}' => $this->t($menu_item)));
            }
        }
        return $retval;
    } // check_validation()


    /** perform the actual initialisation of the cms
     *
     * this routine initialises the database: creates tables,
     * inserts essential data (first user account, other defaults)
     * and optional demonstration data.
     *
     * The strategy is as follows.
     *
     *  - (1) manufacture a database object in the global $DB
     *  - (2A) create the main tables (from /program/install/tabledefs.php)
     *  - (2B) insert essential data (from /program/install/tabledata.php)
     *  - (2C) store the collected data (website title, etc.),
     *  - (2D) create the first useraccount, 
     *  - (3) if necessary, create the data directory
     *  - (4) record the currently available languages in the database
     *
     * Once the main part is done, install modules and themes based on the
     * relevant information that is stored in the corresponding manifest-file
     * by performing the following steps for each module and theme:
     *
     *  - (5A) insert a record in the appropriate table with active = FALSE
     *  - (5B) create the tables (if any tables are necessary according to the manifest)
     *  - (5C) install the item by including a file and executing function <item>_install()
     *  - (5D) flip the active flag in the record from step 5A to indicate success
     *
     * Subsequently the optional demodata is installed.
     *
     *  - (6A) a foundation is created via the function demodata() from /program/install/demodata.php
     *  - (6B) all modules + themes can add to the demo data via the appropriate subroutines
     *
     * If all goes well, this routine ends with an attempt to 
     *
     *  - (7) save the config.php file at the correct location.
     *    (it is not an error if that does not work; it only
     *    means that the  user has to upload the config.php
     *    file manually.
     *
     * @return bool TRUE on success, FALSE otherwise + messages in $this->messages[]
     * @todo should we save the config.php to the datadir if the main dir fails? Mmmm.... security implications?
     * @todo this routine badly needs refactoring
     */
    function perform_installation() {
        global $DB;
        $retval = TRUE; // assume success

        // 0 -- do we have sane values at this point? If not, send user back to correct data
        if (!$this->check_validation()) {
            return FALSE;
        }

        // 1 -- try to create $DB
        /** Manufacture an object of the database class corresponding with requested db_type */
        include_once(dirname(__FILE__).'/lib/database/databaselib.php');
        $DB = database_factory($_SESSION['INSTALL']['db_prefix'],$_SESSION['INSTALL']['db_type']);
        if ($DB === FALSE) {
            // internal error, shouldn't happen (we already checked the database access a few dialogs ago)
            $this->messages[] = 'Internal error: cannot create database object';
            return FALSE;
        }
        if (!@$DB->connect($_SESSION['INSTALL']['db_server'],
                           $_SESSION['INSTALL']['db_username'],
                           $_SESSION['INSTALL']['db_password'],
                           $_SESSION['INSTALL']['db_name'])) {
            $this->messages[] = $this->t('error_db_cannot_connect').' ('.$DB->errno.'/\''.$DB->error.'\')';
            return FALSE;
        }
        // At this point we have a working database in our hands in the global $DB.

        // 2A -- create the main tables
        if (!$this->create_tables(dirname(__FILE__).'/install/tabledefs.php')) {
            $retval = FALSE;
        }
        // 2B -- enter essential data in main tables (defaults etc.)
        if (!$this->insert_tabledata(dirname(__FILE__).'/install/tabledata.php')) {
            $retval =  FALSE;
        }
        // 2C -- enter additional configuration data to tables
        $config_updates = array(
            'version' => $_SESSION['INSTALL']['WAS_VERSION'],
            'salt' => $this->quasi_random_string(rand(22,42),62),
            'title' => $_SESSION['INSTALL']['cms_title'],
            'website_from_address' => $_SESSION['INSTALL']['cms_website_from_address'],
            'website_replyto_address' => $_SESSION['INSTALL']['cms_website_replyto_address'],
            'language_key' => $_SESSION['INSTALL']['language_key'],
            'clamscan_path' => $_SESSION['INSTALL']['clamscan_path'],
            'clamscan_mandatory' => ($_SESSION['INSTALL']['clamscan_mandatory']) ? '1' : '0'
            );
        foreach($config_updates as $name => $value) {
            $where = array('name' => $name);
            $fields = array('value' => strval($value));
            if (db_update('config',$fields,$where) === FALSE) {
                $params = array('{CONFIG}' => $name,'{ERRNO}' => $DB->errno,'{ERROR}' => $DB->error);
                $this->messages[] = $this->t('error_update_config',$params);
                $retval = FALSE;
            }
        }
        // 2D -- insert the first user account with the first acl
        $table = 'acls';
        $key_field = 'acl_id';
        $fields = array('permissions_jobs' => -1,
                        'permissions_intranet' => -1,
                        'permissions_modules' => -1,
                        'permissions_nodes' => -1);
        $acl_id = db_insert_into_and_get_id($table,$fields,$key_field);
        if ($acl_id === FALSE) {
            // This shouldn't happen in a freshly created database.
            $params = array('{TABLENAME}' => $DB->prefix.$table,'{ERRNO}' => $DB->errno,'{ERROR}' => $DB->error);
            $this->messages[] = $this->t('error_insert_into_table',$params);
            return FALSE;
        }
        $salt = $this->quasi_random_string(12,62); // pick 12 characters from [0-9][A-Z][a-z]
        $table = 'users';
        $key_field = 'user_id';
        $username = $_SESSION['INSTALL']['user_username'];
        $userdata_directory = strtolower($this->sanitise_filename($username));
        $fields = array(
            'username' => $username,
            'salt' => $salt,
            'password_hash' => md5($salt.$_SESSION['INSTALL']['user_password']),
            'full_name' => $_SESSION['INSTALL']['user_full_name'],
            'email' => $_SESSION['INSTALL']['user_email'],
            'is_active' => TRUE,
            'redirect' => '',
            'language_key' => $_SESSION['INSTALL']['language_key'],
            'path' => $userdata_directory, // this boldly assumes we will succeed in step 3D below
            'acl_id' => intval($acl_id),
            'high_visibility' => $_SESSION['INSTALL']['high_visibility'],
            'editor' => 'fckeditor'
            );
        $user_id = db_insert_into_and_get_id($table,$fields,$key_field);
        if ($user_id === FALSE) {
            $params = array('{TABLENAME}' => $DB->prefix.$table,'{ERRNO}' => $DB->errno,'{ERROR}' => $DB->error);
            $this->messages[] = $this->t('error_insert_into_table',$params);
            $retval = FALSE;
        }

        // 3A -- maybe create dataroot
        $dataroot = $_SESSION['INSTALL']['cms_dataroot'];
        if (!is_dir($dataroot)) {
            // dataroot does not exist, try to create it
            if (!@mkdir($dataroot,0700)) {
                $params = array(
                    '{FIELD}' => $this->t('cms_datadir_label'),
                    '{DIRECTORY}' => htmlspecialchars($dataroot)
                    );
                $this->messages[] = $this->t('error_not_create_dir',$params);
                $retval = FALSE;
            } // else { success creating $dataroot }
        } // else { nothing to do; we already tested for directory writability

        // 3B -- "protect" the datadirectory with a blank index.html
        @touch($dataroot.'/index.html');

        // 3C -- setup our REAL datadirectory
        $subdirectory = $_SESSION['INSTALL']['cms_datasubdir'];
        if (empty($subdirectory)) {
            $subdirectory = md5($_SESSION['INSTALL']['cms_dir'].
                                $_SESSION['INSTALL']['cms_www'].
                                $this->quasi_random_string(13,62).
                                $_SESSION['INSTALL']['cms_progdir'].
                                $_SESSION['INSTALL']['cms_progwww'].
                                $_SESSION['INSTALL']['cms_title'].
                                strval(time()));
            $datadir = $dataroot.'/'.$subdirectory;
            $params = array(
                '{FIELD}' => $this->t('cms_datadir_label'),
                '{DIRECTORY}' => htmlspecialchars($datadir)
                );
            if (is_dir($datadir)) {
                $this->messages[] = $this->t('error_directory_exists',$params);
                $retval = FALSE;
            } elseif (!@mkdir($datadir,0700)) {
                $this->messages[] = $this->t('error_not_create_dir',$params);
                $retval = FALSE;
            } else {
                $_SESSION['INSTALL']['cms_datasubdir'] = $subdirectory;
            }
        }
        // If the user changed $dataroot after a 1st failed attempt,
        // $subdirectory may not yet exist, we doublecheck and maybe mkdir
        $datadir = $dataroot.'/'.$subdirectory;
        if (!is_dir($datadir)) {
            if (!@mkdir($datadir,0700)) {
                $params = array(
                    '{FIELD}' => $this->t('cms_datadir_label'),
                    '{DIRECTORY}' => htmlspecialchars($datadir)
                    );
                $this->messages[] = $this->t('error_not_create_dir',$params);
                $retval = FALSE;
            }
        }
        $_SESSION['INSTALL']['cms_datadir'] = $datadir; // construct_config_php() needs this
        @touch($datadir.'/index.html'); // "protect" directory

        // 3D -- setup essential subdirectories under our REAL datadirectory including webmaster's userdir
        $datadir_subdirectories = array($datadir.'/areas',
                                        $datadir.'/users',
                                        $datadir.'/groups',
                                        $datadir.'/languages',
                                        $datadir.'/users/'.$userdata_directory);
        foreach ($datadir_subdirectories as $datadir_subdirectory) {
            if ((!is_dir($datadir_subdirectory)) && (!@mkdir($datadir_subdirectory,0700))) {
                $params = array(
                    '{FIELD}' => $this->t('cms_datadir_label'),
                    '{DIRECTORY}' => htmlspecialchars($datadir_subdirectory)
                    );
                $this->messages[] = $this->t('error_not_create_dir',$params);
                $retval = FALSE;
            }
            @touch($datadir_subdirectory.'/index.html'); // "protect" directory
        }

        // 4 -- languages: only add entry to table, no tabledefs or installer here
        $datadir_languages = $datadir.'/languages';
        $languages = $this->get_manifests(dirname(__FILE__).'/languages');
        foreach ($languages as $language_key => $manifest) {
            $language_key = strval($language_key);
            if ((!is_dir($datadir_languages.'/'.$language_key)) &&
                (!@mkdir($datadir_languages.'/'.$language_key,0700))) {
                $params = array(
                    '{FIELD}' => $this->t('cms_datadir_label'),
                    '{DIRECTORY}' => htmlspecialchars($datadir_languages.'/'.$language_key)
                    );
                $this->messages[] = $this->t('error_not_create_dir',$params);
                $retval = FALSE;
            }
            @touch($datadir_languages.'/'.$language_key.'/index.html'); // "protect" directory
            $table = 'languages';
            $fields = array(
                'language_key'        => $language_key,
                'language_name'       => (isset($manifest['language_name'])) ?
                                             strval($manifest['language_name']) : '('.$language_key.')',
                'parent_language_key' => (isset($manifest['parent_language_key'])) ? 
                                             strval($manifest['parent_language_key']) : '',
                'version'             => intval($manifest['version']),
                'manifest'            => $manifest['manifest'],
                'is_core'             => ((isset($manifest['is_core'])) && ($manifest['is_core'])) ? TRUE : FALSE,
                'is_active'           => TRUE,
                'dialect_in_database' => FALSE,
                'dialect_in_file'     => FALSE
                );
            if (db_insert_into($table,$fields) === FALSE) {
                // This shouldn't happen in a freshly created database. However, try to continue with the next
                $params = array('{TABLENAME}' => $DB->prefix.$table,'{ERRNO}' => $DB->errno,'{ERROR}' => $DB->error);
                $this->messages[] = $this->t('error_insert_into_table',$params);
                $retval = FALSE;
                continue;
            }
        }

        // 5 -- install modules and themes
        $subsystems = array(
            'modules'   => $this->get_manifests(dirname(__FILE__).'/modules'),
            'themes'    => $this->get_manifests(dirname(__FILE__).'/themes'),
            );
        foreach($subsystems as $subsystem => $manifests) {
            foreach ($manifests as $item => $manifest) {
                if (empty($manifest)) {
                    $this->messages[]= $this->t('warning_no_manifest',array('{ITEM}' => $item));
                    continue;
                }
                // 5A -- prepare entry in the corresponding table
                switch($subsystem) {
                case 'modules':
                    $fields = array(
                        'name' => strval($item),
                        'version' => intval($manifest['version']),
                        'manifest' => $manifest['manifest'],
                        'is_core' => ((isset($manifest['is_core'])) && ($manifest['is_core'])) ? TRUE : FALSE,
                        'is_active' => FALSE,
                        'has_acls' => ((isset($manifest['has_acls'])) && ($manifest['has_acls'])) ? TRUE : FALSE,
                        'view_script' => (isset($manifest['view_script'])) ? $manifest['view_script'] : NULL,
                        'admin_script' => (isset($manifest['admin_script'])) ? $manifest['admin_script'] : NULL,
                        'search_script' => (isset($manifest['search_script'])) ? $manifest['search_script'] : NULL,
                        'cron_script' => (isset($manifest['cron_script'])) ? $manifest['cron_script'] : NULL,
                        'cron_interval' => (isset($manifest['cron_interval'])) ? intval($manifest['cron_interval']):NULL,
                        'cron_next' => NULL
                        );
                    $key_field = 'module_id';
                    $table = 'modules';
                    break;
                case 'themes':
                    $fields = array(
                        'name' => strval($item),
                        'version' => intval($manifest['version']),
                        'manifest' => $manifest['manifest'],
                        'is_core' => ((isset($manifest['is_core'])) && ($manifest['is_core'])) ? TRUE : FALSE,
                        'is_active' => FALSE,
                        'class' => (isset($manifest['class'])) ? $manifest['class'] : NULL,
                        'class_file' => (isset($manifest['class_file'])) ? $manifest['class_file'] : NULL
                        );
                    $key_field = 'theme_id';
                    $table = 'themes';
                    break;
                default:
                    $this->messages[] = 'Internal error: unknown subsystem '.htmlspecialchars($subsystem);
                    continue;
                    break;
                }
                if (($item_id = db_insert_into_and_get_id($table,$fields,$key_field)) === FALSE) {
                    // This shouldn't happen in a freshly created database. However, try to continue with the next
                    $params = array('{TABLENAME}' => $DB->prefix.$table,'{ERRNO}' => $DB->errno,'{ERROR}' => $DB->error);
                    $this->messages[] = $this->t('error_insert_into_table',$params);
                    $retval = FALSE;
                    continue;
                }
                $is_active = TRUE; // assume we can successfully install the item
                // 5B -- maybe insert tables for item
                if ((isset($manifest['tabledefs'])) && (!empty($manifest['tabledefs']))) {
                    $filename = dirname(__FILE__).'/'.$subsystem.'/'.$item.'/'.$manifest['tabledefs'];
                    if (file_exists($filename)) {
                        if (!$this->create_tables($filename)) {
                            $retval = FALSE;
                            $is_active = FALSE;
                        }
                    }
                }
                // 5C -- maybe install this module or theme
                if ((isset($manifest['install_script'])) && (!empty($manifest['install_script']))) {
                    $filename = dirname(__FILE__).'/'.$subsystem.'/'.$item.'/'.$manifest['install_script'];
                    if (file_exists($filename)) {
                        @include_once($filename);
                        $item_install = $item.'_install';
                        if (function_exists($item_install)) {
                            if (!$item_install($this->messages,$item_id)) {
                                $retval = FALSE;
                                $is_active = FALSE;
                            }
                        }
                    }
                }
                // 5D -- indicate everything went well
                if ($is_active) {
                    $where = array($key_field => $item_id);
                    $fields = array('is_active' => $is_active);
                    if (db_update($table,$fields,$where) === FALSE) {
                        $params = array('{CONFIG}' => $item,'{ERRNO}' => $DB->errno,'{ERROR}' => $DB->error);
                        $this->messages[] = $this->t('error_update_config',$params);
                        $retval = FALSE;
                        $is_active = FALSE; // this should not happen (famous last words...)
                    }
                }
                $subsystems[$subsystem][$item]['is_active'] = $is_active;
                $subsystems[$subsystem][$item]['key_field'] = $key_field;
                $subsystems[$subsystem][$item]['item_id'] = $item_id;
            } // foreach item
        } // foreach subsystem

        // 6 -- Demodata
        if ($_SESSION['INSTALL']['cms_demodata']) {
            // 6A -- prepare essential information for demodata installation
            $demodata_config = array(
                'language_key'    => $_SESSION['INSTALL']['language_key'],
                'dir'             => $_SESSION['INSTALL']['cms_dir'],
                'www'             => $_SESSION['INSTALL']['cms_www'],
                'progdir'         => $_SESSION['INSTALL']['cms_progdir'],
                'progwww'         => $_SESSION['INSTALL']['cms_progwww'],
                'datadir'         => $_SESSION['INSTALL']['cms_datadir'],
                'user_username'   => $_SESSION['INSTALL']['user_username'],
                'user_full_name'  => $_SESSION['INSTALL']['user_full_name'],
                'user_email'      => $_SESSION['INSTALL']['user_email'],
                'user_id'         => $user_id,
                'public_area_id'  => 0, // this placeholder filled in by demodata()
                'private_area_id' => 0, // this placeholder filled in by demodata()
                'extra_area_id'   => 0, // this placeholder filled in by demodata()
                'demo_salt'       => $_SESSION['INSTALL']['cms_demodata_salt'],
                'demo_password'   => $_SESSION['INSTALL']['cms_demodata_password']
                );
            $filename = dirname(__FILE__).'/install/demodata.php';
            include_once($filename);
            if (!demodata($this->messages,$demodata_config)) {
                $this->messages[] = $this->t('error_install_demodata');
                $retval = FALSE;
            }
            // 6B -- insert demodata for all modules and themes
            foreach($subsystems as $subsystem => $manifests) {
                foreach ($manifests as $item => $manifest) {
                    $item_demodata = $item.'_demodata';
                    if (function_exists($item_demodata)) {
                        if (!$item_demodata($this->messages,intval($manifest['item_id']),$demodata_config,$manifest)) {
                            $this->messages[] = $this->t('error_install_demodata');
                            $retval = FALSE;
                        }
                    }
                }
            }
        }

        // 7 -- Finish with attempt to write config.php
        if ($retval) {
            $_SESSION['INSTALL']['config_php_written'] = $this->write_config_php();
        }
// $this->messages[] = 'STUB: always fail..';
// return FALSE;
        return $retval;
    } // perform_installation()


    /** construct the finish screen
     *
     * this dialog is displayed after a succesful installation.
     * The user is prompted to select the next destination, which can
     * be either admin.php (the backoffice), index.php (the frontpage),
     * manual.php (the documentation) or the project's home page.
     *
     * There is also an option to download config.php.
     * Once the user's choice is submitted, the session is
     * reset and config.php can no longer be downloaded. This session reset
     * is done in the 'Done' dialog (see {@link end_session_and_redirect()}).
     *
     * Note that the boolean parameter INSTALL['config_php_written'] indicates
     * whether the file config.php was already succesfully written in the
     * designated location. If this is the case, we show a different message
     * compared to the case where the user still needs to download+upload config.php.
     *
     * @param string $m margin for better readability of generated HTML-code
     * @return void HTML-code sent to browser
     */
    function show_dialog_finish($m='      ') {
        global $WAS_SCRIPT_NAME;
        $dialog_title = $this->t('dialog_finish');
        $help_topic = 'install#finish';
        $dialogdef = $this->get_dialogdef_finish();
        $url = sprintf('http://%s/version/?release=%s&amp;date=%s&amp;version=%s&amp;check=%s',PROJECT_SITE,
                rawurlencode(WAS_RELEASE), rawurlencode(WAS_RELEASE_DATE), rawurlencode(WAS_VERSION),
                (WAS_ORIGINAL) ? '1' : '0');

        $link = "<a href=\"$url\" target=\"_blank\" ".
                "title=\"".$this->t('dialog_finish_check_for_updates_title')."\" ".
                "onclick=\"window.open('$url','','left=100,top=100,width=640,height=320'); return false;\">".
                $this->t('dialog_finish_check_for_updates_anchor')."</a>\n";

        $params = array(
            '{VERSION}' => WAS_RELEASE,
            '{AHREF}' => "<a href=\"{$WAS_SCRIPT_NAME}?step=".INSTALL_DIALOG_DOWNLOAD."\" title=\"".
                         $this->t('dialog_download_title')."\">",
            '{A}' => '</a>',
            '{CMS_DIR}' => htmlspecialchars($_SESSION['INSTALL']['cms_dir']));
        if ((isset($_SESSION['INSTALL']['config_php_written'])) && ($_SESSION['INSTALL']['config_php_written'])) {
            $explanation = $this->t('dialog_finish_explanation_1',$params);
        } else {
            $explanation = $this->t('dialog_finish_explanation_0',$params);
        }
        $content = $m."<h2>{$dialog_title}</h2>\n".
                   $m.$explanation."\n".
                   $m."<p>\n".
                   $m.$this->t('dialog_finish_check_for_updates')."<br>\n".
                   $m.$link.
                   $m."<p>\n".
                   $m."<form action=\"{$WAS_SCRIPT_NAME}\" method=\"POST\">\n".
                   $m."  <input type=\"hidden\" name=\"dialog\" value=\"".INSTALL_DIALOG_FINISH."\">\n".
                   $this->render_dialog($dialogdef,$m.'  ').
                   $m."  <p>\n".
                   $m."  ".$this->button('ok')."\n".
                   $m."</form>\n";

        $menu = $this->get_menu(INSTALL_DIALOG_FINISH,$_SESSION['INSTALL']['stage']);
        echo  $this->get_page($dialog_title,$menu,$content,$help_topic);
        return;
    } // show_dialog_finish()


    /** fill an array with necessary information for finish / jump dialog
     *
     * Note that this is a very light-weight implentation of the dialogdef
     * idea used in the main program: we don't do fancy stuff with labels,
     * hotkeys, etc. KISS, because I don't want to rely on all the libraries
     * of the main program with all their interconnections and dependencies;
     * the installer should more or less be a stand-alone application.
     *
     * @return array array filled with field definitions and prompts etc.
     */
    function get_dialogdef_finish() {
        $dialogdef = array(
            'jump' => array(
                'label' => $this->t('jump_label'),
                'help' => $this->t('jump_help'),
                'value' => 'admin',
                'show' => TRUE,
                'type' => 'l',
                'options' => array(
                    'admin' => 'admin.php',
                    'index' => 'index.php',
                    'manual' => 'manual.php',
                    'project' => 'http://'.PROJECT_SITE)
                )
            );
        return $dialogdef;
    } // get_dialogdef_finish()


    /** show the user that the process has been cancelled
     *
     * this shows a screen with the message that the installation
     * procedure has been cancelled. There is a single [OK] button
     * that effectively allows the user to try again. The existing
     * session (and all the data it might contain) is destroyed.
     *
     * Note that we first construct the page (possibly in another language)
     * in a separate variable before we destroy all data collected sofar.
     *
     * @param string $m margin for better readability of generated HTML-code
     * @return void HTML-code sent to browser
     */
    function show_dialog_cancelled($m='      ') {
        global $WAS_SCRIPT_NAME;
        $dialog_title = $this->t('dialog_cancelled');
        $help_topic = 'install';

        $content = $m."<h2>{$dialog_title}</h2>\n".
                   $m.$this->t('dialog_cancelled_explanation')."\n".
                   $m."<p>\n".
                   $m."<form action\"{$WAS_SCRIPT_NAME}\" method=\"GET\">\n".
                   $m."  ".$this->button('ok')."\n".
                   $m."</form>\n";
        $menu = '';
        $output = $this->get_page($dialog_title,$menu,$content,$help_topic);
        // get rid of the cookie and the session before sending output
        unset($_SESSION['INSTALL']);
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(),'',time()-43210,'/');
        }
        session_destroy();
        echo $output;
    } // show_dialog_cancelled()


    /** unset installation data, end session and redirect the user to elsewhere
     *
     * this redirects the user to one of the possible destinations
     * as selected in the finish dialog (see {@link show_dialog_finish()}).
     * Also, the INSTALL-array is unset, effectively erasing the collected
     * data from the session. Subsequently the session cookie is reset, effectively
     * ending the session. The effect is that the user has to start over
     * if she returns to install.php. (The equivalent of 'logging off').
     *
     * @return void redirect + HTML-code sent to browser
     */
    function end_session_and_redirect() {
        $jump = (isset($_POST['jump'])) ? trim($this->magic_unquote($_POST['jump'])) : '';
        switch($jump) {
        case 'admin':  $url = $_SESSION['INSTALL']['cms_www'].'/admin.php';       break;
        case 'index':  $url = $_SESSION['INSTALL']['cms_www'].'/index.php';       break;
        case 'manual': $url = $_SESSION['INSTALL']['cms_progwww'].'/manual.php';  break;
        case 'project':$url = 'http://'.PROJECT_SITE;                             break;
        default:       $url = $_SESSION['INSTALL']['cms_www'].'/admin.php';       break;
        }
        // get rid of the cookie and the session before sending output
        unset($_SESSION['INSTALL']);
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(),'',time()-43210,'/');
        }
        session_destroy();
        header('Location: '.$url);
        echo "<a href=\"{$url}\">$url</a>\n";
        // echo "<pre>";print_r($_SESSION);
    } // end_session_and_redirect()

    // ==================================================================
    // ============================ HELPERS =============================
    // ==================================================================

    /** return an array with default configuration values
     *
     * this routine tries to calculate/guess the best default
     * values for config.php. We do so by looking in the global
     * $_SERVER variable. If that doesn't work, we simply make
     * up sensible values. In the end it is up to the user to
     * enter the correct data; the values here are mere defaults.
     *
     * A first-time install should be possible without changing/editing
     * the default values, i.e. a standard Next-Next-Finish-type of
     * installation.
     *
     * @return array array filled with sensible default values
     * @todo should we check the program version versus the stored program version here?
     * @todo there is something wrong with the default for $cms_www; FIXME (commented out for now)
     */
    function get_default_install_values() {
        //
        // 1 -- where are we located in the file system
        //
        $cms_progdir = dirname(__FILE__);
        if (($cms_dir = realpath($cms_progdir.'/..')) === FALSE) {
            $cms_dir = dirname($cms_progdir);
        }

        //
        // 2 -- is this the first time and probably a plain+simple install?
        //
        // If config.php already exists, we MUST assume a custom-type installation
        // because a regular installation would and should fail (we don't want to
        // overwrite an existing config.php and lose an existing site).
        $install_type_custom = (is_file($cms_dir.'/config.php')) ? TRUE : FALSE;

        //
        // 3 -- guess how the user gets to $cms_dir and $cms_progdir via the browser
        //
        // 3A -- guess components of main url (scheme, hostname, portnumber)
        $url = $this->guess_url();

        // 3B -- guess the paths to those directories as seen from the browser
        if (isset($_SERVER['SCRIPT_NAME'])) {
            $script_name = $_SERVER['SCRIPT_NAME'];
        } elseif (isset($GLOBALS['SCRIPT_NAME'])) {
            $script_name = $GLOBALS['SCRIPT_NAME'];
        } else {
            $script_name = '/program/install.php';
        }
        $script_dir = dirname($script_name);
        $script_parent_dir = dirname($script_dir);

        // 3C -- we can be fairly sure about the progdir path...
        $cms_progwww = $url['scheme'].'://'.$url['authority'].$script_dir;

        // 3D -- the dir path could be the parent of progdir OR we are doing 
        //       something completely different, maybe even in another document root...
//        if ($install_type_custom) {
//            if (isset($_SERVER['DOCUMENT_ROOT'])) {
//                $cms_dir = $_SERVER['DOCUMENT_ROOT'];
//            }
//            $cms_www = $url['scheme'].'://'.$url['authority'];
//        } else {
            $cms_www = $url['scheme'].'://'.$url['authority'];
            if ($script_parent_dir != '/') {
                $cms_www .= $script_parent_dir;
            }
//        }

        //
        // 4 -- try to guess a place for data outside the document root
        //
        if (isset($_SERVER['DOCUMENT_ROOT'])) {
            $cms_dataroot = realpath($_SERVER['DOCUMENT_ROOT'].'/..');
            if ($cms_dataroot === FALSE) {
                $cms_dataroot = '';
            } else {
                $cms_dataroot .= ($cms_dataroot == '/') ? 'wasdata' : '/wasdata';
            }
        } else {
            $cms_dataroot = ''; // don't want to advertise bad habit by using $cms_dir/data or (worse) $cms_progdir/data.
        }

        //
        // 5 -- guess that the website from address (or use webmaster@hostname)
        //
        if (isset($_SERVER['SERVER_ADMIN'])) {
            $website_from_address = $_SERVER['SERVER_ADMIN'];
        } else {
            $website_from_address = 'webmaster@'.$url['hostname'];
        }

        //
        // 6 -- Finally store guessed/calculated defaults and return results in an array
        //
        $default_values = array(
            'stage' => 0,
            'errorcount' => 0,

            'high_visibility' => FALSE,
            'language_key' => 'en',
            'install_type_custom' => $install_type_custom,
            'license_accepted' => FALSE,

            'db_type' => 'mysql',
            'db_server' => 'localhost',
            'db_username' => '',
            'db_password' => '',
            'db_name' => '',
            'db_prefix' => 'was_',
            'db_validated' => FALSE,

            'cms_dir' => $cms_dir,
            'cms_www' => $cms_www,
            'cms_progdir' => $cms_progdir,
            'cms_progwww' => $cms_progwww,
            'cms_dataroot' => $cms_dataroot,
            'cms_datasubdir' => '',              // filled in when performing real install
            'cms_datadir' => $cms_dataroot.'/',  // completed when performing real install
            'cms_title' => '',
            'cms_website_from_address' => $website_from_address,
            'cms_website_replyto_address' => $website_from_address,
            'cms_demodata' => ($install_type_custom) ? FALSE : TRUE,
            'cms_demodata_salt' => $this->quasi_random_string(12,62), // pick 12 characters from [0-9][A-Z][a-z]
            'cms_demodata_password' => '',
            'cms_validated' => FALSE,

            'user_username' => '',
            'user_full_name' => '',
            'user_email' => '',
            'user_password' => '',
            'user_validated' => FALSE,

            'clamscan_path' => '',               // filled in when checking compatibility
            'clamscan_mandatory' => FALSE,       // filled in when checking compatibility

            'compatibility_checked' => FALSE,

            'WAS_VERSION' => WAS_VERSION,
            'WAS_RELEASE' => WAS_RELEASE,
            'WAS_RELEASE_DATE' => WAS_RELEASE_DATE,

            'config_php_written' => FALSE
            );
        return $default_values;
    } // get_default_install_values()


    /** prepare a configuration file based on the collected information 
     *
     * @return string string with contents of config.php
     */
    function construct_config_php() {
        $s  = '<?'.'php'."\n";
        $s .= sprintf("// File:      %s\n",$_SESSION['INSTALL']['cms_dir'].'/config.php');
        $s .= sprintf("// Date:      %s\n",strftime("%Y-%m-%d %T"));
        $s .= sprintf("// Generator: %s\n",__FILE__);

        $s .= sprintf("//\n// *** %s ***\n\n",'DO NOT EDIT THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING');

        $mapping = array(
            'db_type' => 'db_type',
            'db_server' => 'db_server',
            'db_username' => 'db_username',
            'db_password' => 'db_password',
            'db_name' => 'db_name',
            'db_prefix' => 'prefix',
            'cms_dir' => 'dir',
            'cms_www' => 'www',
            'cms_progdir' => 'progdir',
            'cms_progwww' => 'progwww',
            'cms_datadir' => 'datadir'
            );
        foreach($mapping as $k => $v) {
            $s .= sprintf("\$CFG->%s = '%s';\n",$v,addslashes($_SESSION['INSTALL'][$k]));
        }
        $s .= sprintf("\n// *** %s ***\n// *** %s ***\n?>",
                      "MAKE SURE THERE ARE NO SPACES, NEWLINES, CARRIAGE",
                      "RETURNS AFTER THE TWO CHARACTERS ON THE NEXT LINE");
        return $s;
    } // construct_config_php()


    /** construct a complete HTML-page that can be sent to the user's browser
     *
     * this routine constructs a string containing the complete page to send
     * to the user's browser, starting with the <html> opening tag and ending with
     * the </html> closing tag. The constructed page is returned as a string.
     *
     * This routine also peeks into the INSTALL-array, e.g. for the language
     * key and the high_visibility flag.
     *
     * If $this->messages is not empty, the items in that array are displayed
     * between the page header (logo+helpbutton)  and the menu/content area. This
     * is the feedback of the previous action to the user (if any).
     *
     * @param string $dialog_title text to show in the browser's title bar (indicating where we are)
     * @param string $menu ready-to-use HTML-code comprising the menu at the left hand side of the page
     * @param string $content ready-to-user HTML-code holding the actual contents of the page
     * @param string $help_topic the topic or subtopic in the manual to link to 
     * @return string ready-to-use HTML-code that can be sent to the user's browser
     * @todo should we promote language and high_visibility to function parameters instead of using $_SESSION directly?
     */
    function get_page($dialog_title='',$menu='',$content='',$help_topic='install') {
        $high_visibility = $_SESSION['INSTALL']['high_visibility'];
        $install_title = $this->t('websiteatschool_install');
        $logo_title = 'The Website@School logo is a registered trademark of Vereniging Website At School';
        $alt_logo = $this->t('websiteatschool_logo');
        $language = $_SESSION['INSTALL']['language_key'];
        $help_title = $this->t('help_description');
        $help_name = $this->t('help_name');
        $help_button = ($high_visibility) ? htmlspecialchars($help_name) :
                       "<img src=\"graphics/help.gif\" width=\"32\" height=\"32\" title=\"{$help_title}\" alt=\"{$help_name}\">";
        if (strpos($help_topic,'#') !== FALSE) {
            list($topic,$subtopic) = explode('#',$help_topic);
        } else {
            $topic = $help_topic;
            $subtopic = '';
        }
        $s = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n".
             "<html>\n".
             "<head>\n".
             "  <meta name=\"MSSmartTagsPreventParsing\" content=\"TRUE\">\n".
             "  <meta name=\"keywords\" content=\"free content management system, website@school, websiteatschool\">\n".
             "  <meta name=\"description\" content=\"installation wizard for website@school content management system\">\n".
             "  <meta http-equiv=\"Content-Script-Type\" content=\"text/javascript\">\n".
             "  <meta http-equiv=\"Content-Style-Type\" content=\"text/css\">\n".
             "  <link rel=\"stylesheet\" type=\"text/css\" href=\"styles/admin_base.css\">\n";
        if ($high_visibility) {
            $s .= "  <link rel=\"stylesheet\" type=\"text/css\" href=\"styles/admin_high_visibility.css\">\n";
        }
        // return to regulare bulleted lists
        $s .= "  <style>\n".
              "    #content ul {\n".
              "      padding-left: 20px;\n".
              "    }\n".
              "    #content li {\n".
              "      padding-left: 20px;\n".
              "      font-weight: normal;\n".
              "      list-style-type: disc;\n".
              "      line-height: 1.3em;\n".
              "    }\n".
              "    #content td {\n".
              "      padding: 10px 4px 14px 4px;\n".
              "      vertical-align: top;\n".
              "    }\n".
              "    #content td.tight {\n".
              "      padding: 0px 0px 0px 0px;\n".
              "      vertical-align: top;\n".
              "    }\n".
              "    #content h3 {\n".
              "      padding-left: 0px;\n".
              "      margin-top: 0px;\n".
              "    }\n".
              "  </style>\n";

        $s .= "  <title>{$install_title} - {$dialog_title}</title>\n".
              "</head>\n\n";

        // 2 -- start of body upto error messages (maybe including a logo)
        $logo = ($high_visibility) ? '' : sprintf('<img src="%s" width="%d" height="%d" title="%s" alt="%s">',
                                                        'graphics/waslogo-284x71.png',284,71,$logo_title,$alt_logo);
        $s .= "<body>\n".
              "  <div id=\"page\">\n".
              "    <div id=\"header\">\n".
              "      <div id=\"logo\">{$logo}</div>\n".
              "      <h1>{$install_title}</h1>\n".
              "    </div>\n".
              "    <div id=\"navigation\">\n".
              "      <ul>\n".
              "        <li class=\"right\">\n".
              "          <a href=\"manual.php?language={$language}&amp;topic={$topic}&amp;subtopic={$subtopic}\"\n".
              "             title=\"{$help_title}\" target=\"_blank\"\n".
              "             onclick=\"window.open('manual.php?language={$language}&amp;topic={$topic}&amp;subtopic={$subtopic}','',".
                                   "'left=100,top=100,height=480,width=640,scrollbars=1');return false;\">\n".
              "            {$help_button}\n".
              "          </a>\n".
              "      </ul>\n".
              "    </div>\n";

        // 3 -- show error messages (if any)
        if (!empty($this->messages)) {
            $s .= "    <div id=\"messages\">\n";
            if (sizeof($this->messages) > 1) {
                $ul_start = "      <ul>\n";
                $ul_stop  = "      </ul>\n";
                $li       = "        <li>";
            } else {
                $ul_start = '';
                $ul_stop  = '';
                $li       = "      ";
            }
            $s .= $ul_start;
            foreach ($this->messages as $msg) {
                $s .= $li.$msg."\n";
            }
            $s .= $ul_stop;
            $s .= "    </div>\n";
        }
        // 4 -- display menu
        $s .= $menu;

        // 5 --  content div
        $s .= "    <div id=\"content\">\n".
                     $content.
              "    </div>\n";
        // 6 -- footer
        $a = array('{AHREF}'=>'<a href="http://'.PROJECT_SITE.'/" target="_blank">',
                   '{A}' => '</a>',
                   '{DATE}'=> strftime("%Y-%m-%d %T"));

        $s .= "    <div id=\"footer\">\n".
                       $this->appropriate_legal_notices($high_visibility).
              "    </div>\n".
              "  </div>\n".
              "</body>\n".
              "</html>\n";
        return $s;
    } // get_page()

    /** construct a link to appropriate legal notices as per AGPLv3 section 5
     *
     * This routine constructs ready-to-use HTML-code for a link to the
     * Appropriate Legal Notices, which are to be found in /program/about.html.
     * Depending on the highvisibility flag we either generate a text-based
     * link or a clickabel image.
     *
     * The actual text / image to use depends on the global constant WAS_ORIGINAL.
     * This constant is defined in /program/version.php and it should be TRUE for
     * the original version of Website@School and FALSE for modified versions.
     *
     * In the former case the anchor looks like 'Powered by Website@School', in the
     * latter case it will look like 'Based on Website@School', which is in line
     * with the requirements from the license agreement for Website@School, see
     * /program/license.html.
     *
     * IMPORTANT NOTE
     *
     * Please respect the license agreement and change the definition of
     *  WAS_ORIGINAL to FALSE if you modify this program (see /program/version.php).
     * You also should change the file '/program/about.html' and add a 'prominent
     * notice' of your modifications.
     *
     * Note: a comparable routine can be found in {@link waslib.php}.
     *
     * @param bool $high_visibility if TRUE we return a text-only link, otherwise a clickable image
     * @param string $m margin to improve readability of generated code
     * @return string ready-to-use HTML
     */
    function appropriate_legal_notices($high_visibility,$m='      ') {
        if ($high_visibility) {
            $prefix = (WAS_ORIGINAL) ? 'Powered by ' : 'Based on ';
            $anchor = 'Website@School';
        } else {
            $prefix = '';
            $anchor = sprintf('<img src="%s" width="%d" height="%d" border="0" alt="%s" title="%s">',
                          (WAS_ORIGINAL) ? 'graphics/poweredby.png' : 'graphics/basedon.png',
                          (WAS_ORIGINAL) ? 280 : 255, 
                          (WAS_ORIGINAL) ? 35 : 35,
                          (WAS_ORIGINAL) ? 'Powered by Website@School' : 'Based on Website@School',
                          'The Website@School logo is a registered trademark of Vereniging Website At School');
        }
        return sprintf('%s%s<a href="about.html" target="_blank">%s<a>',$m,$prefix,$anchor);
    } // appropriate_legal_notices()


    /** construct a clickable menu which helps the user to jump back and forth in the funnel
     *
     * this constructs a menu that allows the user to jump to a another step
     * in the procedure when appropriate. Two parameters are important:
     * the $dialog and the the $stage. The $dialog indicates the current dialog, i.e.
     * the dialog that is currently displayed in the content area. This item in the menu
     * is emphasised (e.g. the link is underlined via the style sheet). The $stage indicates
     * how far we are in the procedure. The installation consists of some eight steps,
     * and the used is encouraged to perform all steps in the natural order, by repeatedly
     * pressing the [Next] button in the dialogs. Every time a dialog appears to have
     * valid data, the $stage is incremented.
     *
     * All the menu items after the current stage are greyed out and basically inaccessible
     * for the user. Menu items before the current stage are accessible, so it is possible
     * to jump backwards to dialogs that were already processed but the only way to advance
     * to a new screen is to use the [Next] (and provide valid data, obviously).
     *
     * The greyed-out menu items have a href property consisting of a simple "#". This is
     * interpreted by the browser as a relative link within the current page. The effect is
     * that the current page stays on the screen, including any unsaved data the user may
     * already have entered. By showing the links in a different colour (grey and not blue),
     * the user can visually see which items are clickable and which are not.
     *
     * By showing all the menu items and greying-out the inaccessible ones we effectively
     * build a funnel and at the same time indicating which steps will follow in the procedure.
     *
     * There is a special case when $stage hits the FINISH-dialog. If the user has not yet
     * reached that stage, all dialogs before the finish-dialog are active and the rest is
     * greyed-out. Once the $stage reaches the FINISH-dialog, all precious dialogs become
     * instantly unreachable. This is because the step between the confirmation dialog and
     * the finish dialog is the place where the actual installation takes place. That is a
     * one-time operation and the user should not be able to jump backwards and change data
     * after all the actual installation work is already done.
     *
     * Note that the menu item to download the config.php is displayed before the finish
     * dialog. This is because it appears more logical to me (but YMMV).
     *
     * @param int $dialog indicates the current dialog
     * @param int $stage indicates the highest numbered dialog that was already reached
     * @param string $m margin for better readability of generated HTML-code
     * @return string ready-to-use HTML-code
     */
    function get_menu($dialog,$stage,$m='    ') {
        global $WAS_SCRIPT_NAME;
        $items = array(
            INSTALL_DIALOG_LANGUAGE => array(
                'anchor' => $this->t('dialog_language'),
                'title'  => $this->t('dialog_language_title')
                ),
            INSTALL_DIALOG_INSTALLTYPE => array(
                'anchor' => $this->t('dialog_installtype'), 
                'title'  => $this->t('dialog_installtype_title')
                ),
            INSTALL_DIALOG_LICENSE => array(
                'anchor' => $this->t('dialog_license'), 
                'title'  => $this->t('dialog_license_title')
                ),
            INSTALL_DIALOG_DATABASE => array(
                'anchor' => $this->t('dialog_database'),
                'title'  => $this->t('dialog_database_title')
                ),
            INSTALL_DIALOG_CMS => array(
                'anchor' => $this->t('dialog_cms'),
                'title'  => $this->t('dialog_cms_title')
                ),
            INSTALL_DIALOG_USER => array(
                'anchor' => $this->t('dialog_user'),
                'title'  => $this->t('dialog_title')
                ),
            INSTALL_DIALOG_COMPATIBILITY => array(
                'anchor' => $this->t('dialog_compatibility'),
                'title'  => $this->t('dialog_compatibility__title')
                ),
            INSTALL_DIALOG_CONFIRM => array(
                'anchor' => $this->t('dialog_confirm'),
                'title'  => $this->t('dialog_confirm_title')
                ),
            INSTALL_DIALOG_DOWNLOAD => array(
                'anchor' => $this->t('dialog_download'),
                'title'  => $this->t('dialog_download_title')
                ),
            INSTALL_DIALOG_FINISH => array(
                'anchor' => $this->t('dialog_finish'),
                'title'  => $this->t('dialog_finish_title')
                )
            );
        $s = $m."<div id=\"menu\">\n".
             $m."  <ul>\n";
        foreach ($items as $i => $item) {
            if ((INSTALL_DIALOG_LANGUAGE <= $stage) && ($stage < INSTALL_DIALOG_FINISH)) {
                if ($i <= $stage) {
                    $href = $WAS_SCRIPT_NAME."?step=$i";
                    $class = ($i == $dialog) ? ' class="current"' : '';
                } else {
                    $href = "#";
                    $class = ' class="dimmed"';
                }
            } elseif (INSTALL_DIALOG_FINISH <= $stage) {
                if ($i < INSTALL_DIALOG_FINISH) {
                    $href = "#";
                    $class = ' class="dimmed"';
                } else {
                    $href = $WAS_SCRIPT_NAME."?step=$i";
                    $class = ($i == $dialog) ? ' class="current"' : '';
                }
            }
            $s .= $m."    <li><a href=\"{$href}\"{$class} title=\"{$item['title']}\">{$item['anchor']}</a>\n";
        }
        $s .= $m."  </ul>\n".
              $m."</div>\n";
        return $s;
    } // get_menu()

    // ==================================================================
    // =========================== UTILITIES ============================ 
    // ==================================================================

    /** educated guesses for scheme, host and portname from $_SERVER
     *
     * this routine tries to guess the various components of the
     * url that was used to reach this script, based on the information
     * in the global array $_SERVER. If no information can be guessed at all,
     * the result is something like 'http://localhost'.
     *
     * Note that the 'authority' is a combination of hostname and portnumber,
     * but only if the portnumber is non-standard. For http and port 80, and
     * https and port 443 the portnumber is suppressed, because these are the
     * default ports for those schemes.
     *
     * Note that we actually guess the url that should correspond with the
     * document root.
     * 
     * @return array array with various components of guessed url
     */
    function guess_url() {
        //
        // 1 -- http or https?
        //
        if (isset($_SERVER['HTTPS'])) {
            $scheme = ($_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
        } elseif (isset($_SERVER['SERVER_PORT'])) {
            $portnumber = $_SERVER['SERVER_PORT'];
            $scheme = ($portnumber == '443') ? 'https' : 'http';
        } else {
            $scheme = 'http';
        }

        //
        // 2 -- hostname + port
        //
        if ((isset($_SERVER['SERVER_NAME'])) && (!empty($_SERVER['SERVER_NAME']))) {
            $hostname = $_SERVER['SERVER_NAME'];
        } elseif ((isset($_SERVER['HTTP_HOST'])) && (!empty($_SERVER['HTTP_HOST']))) {
            list($hostname,$portnumber) = explode(':',$_SERVER['HTTP_HOST']);
        } else {
            $hostname = 'localhost';
        }
        if (!isset($portnumber)) {
            $portnumber = ($scheme == 'https') ? 443 : 80;
        }

        //
        // 3 -- authority (based on hostname + port)
        //
        if ((($scheme == 'https') && ($portnumber == 443)) || (($scheme == 'http') && ($portnumber == 80))) {
            $authority = $hostname;
        } else {
            $authority = $hostname.':'.$portnumber;
        }

        //
        // 4 - done
        //
        return array('scheme' => $scheme, 'hostname' => $hostname, 'portnumber' => $portnumber, 'authority' => $authority);
    } // guess_url()


    /** retrieve a translated string with optional parameters filled in
     *
     * this routine tries to find a translated string based on the $key.
     * If $replace is not empty, any keys in that array that are found in the translation are
     * replace by the corresponding value.
     *
     * The translations are read from the files /program/install/languages/LL/install.php,
     * where LL is a two-letter language code (e.g. en, nl, de, fr). If the desired language is
     * not available, English is used instead. If the requested translation is not found, the
     * key of the translation is returned, sandwiched between the language codes. Usually this
     * does not happen (all keys used have a translation), but it can be helpful when creating
     * and testing a new translation.
     *
     * Note that a language file is retrieved completely. This means that all the translations are
     * read from file in one swoop; there is no need to go to the disk for every individual translation.
     * The translation file is buffered in memory via the static variable $phrases, which is an array
     * keyed by language. That means that multiple languages can co-exist in this static variable.
     * That last feature is used in constructing a list of available languages where the name of the
     * language is expressed in the language itself (see {@link get_list_of_install_languages()}).
     *
     * Note:
     * By convention the keys in $replace are upper case words, with optional underscores, sandwiched
     * between curly braces. Examples: '{FIELD}', '{DATABASE}', '{RELEASE_DATE}'. The idea is that
     * these words make life easier for translators.
     *
     * @param string $key the key in the string-array with translations
     * @param array $replace contains key-value pairs that are used to search/replace in the translated string
     * @param string $language indicates which language we should translate into
     * @return string translated text with incorporated parameters from $replace
     */
    function t($key,$replace='',$language='') {
        static $phrases = array();

        if (empty($language)) {
            $language = (isset($_SESSION['INSTALL']['language_key'])) ? $_SESSION['INSTALL']['language_key'] : 'en';
        }
        $language = substr($language,0,2); // 

        while (!isset($phrases[$language])) {
            $languages_dir = dirname(__FILE__).'/install/languages';
            $language_path = $languages_dir.'/'.$language;
            $language_file = $language_path.'/install.php';
            if ((is_dir($languages_dir)) && (is_dir($language_path)) && (is_file($language_file))) {
                $string = array();
                include($language_file);
                $phrases[$language] = $string;
                unset($string);
            } elseif ($language != 'en') {
                $language = 'en';
            } else {
                $phrases[$language] = array();
            }
        }

        if (isset($phrases[$language][$key])) {
            $phrase = (empty($replace)) ? $phrases[$language][$key] : strtr($phrases[$language][$key],$replace);
        } else {
            $phrase = '('.$language.') '.$key;
            if (is_array($replace)) {
                foreach($replace as $k => $v) {
                    $phrase .= "\n'$k'='$v'";
                }
            }
            $phrase .= ' (/'.$language.')';
        }
        return $phrase;
    } // t()


    /** shorthand for creating a submit button in the correct style
     *
     * @param string $button indicates which button to create, e.g. 'next', 'previous', 'cancel', 'finish', 'ok'.
     * @return string ready-to-use HTML-code for a submit-button
     */
    function button($button) {
        return sprintf('<input type="submit" name="button_%s" value="%s" accesskey="%s" title="%s" class="button_%s">',
                       $button,
                       $this->t($button),
                       $this->t($button.'_accesskey'),
                       $this->t($button.'_title'),
                       $button);
    } // button()


    /** retrieve a list of available languages by querying the file system for install.php translation files
     *
     * this routine constructs a list of language codes and language names (in the languages themselves)
     * based on the language subdirectories available under /program/install/. The resulting array of
     * code-name-pairs is sorted by name.
     *
     * Note that because the names of the languages are expressed in the languages themselves, this routine
     * has the side-effect of reading _all_ of the available language files into memory (see {@link t()}).
     *
     * @return array sorted list of available languages, keyed by language code
     */
    function get_list_of_install_languages() {
        $languages = array('en' => 'English'); // At least show English
        $languages_dir = dirname(__FILE__).'/install/languages';
        if (is_dir($languages_dir)) {
            if (($dp = opendir($languages_dir)) !== FALSE) {
                while (($language = readdir($dp)) !== FALSE) {
                    $language_path = $languages_dir.'/'.$language;
                    if ((substr($language,0,1) != '.') &&
                        (is_dir($language_path)) &&
                        (is_file($language_path.'/install.php'))) {
                        $languages[$language] = sprintf("%s (%s)",$this->t('language_name','',$language),$language);
                    }
                }
                closedir($dp);
            }
        }
        asort($languages);
        return $languages;
    } // get_list_of_install_languages()


    /** helper to retrieve the text of the LICENSE AGREEMENT for Website@School
     *
     * this reads the full text of the license from /program/license.html
     * into $license. Several checks are performed, if any of those fail,
     * the function returns a non-0 value. If all went well and all tests
     * are passed, the function returns 0.
     *
     * We do strip the header and the footer off the file license.html in order
     * to seamlesly integrate this text into the installation dialog.
     *
     * @param string &$license receives ready-to-use HTML-code with the text of the license from /program/license.html 
     * @return int a prime on error, 0 on success
     */
    function fetch_license(&$license) {
        if (($license_html = file_get_contents('license.html')) === FALSE) {
            return 11;
        }
        if (($n1 = strpos($license_html,"<body>")) === FALSE) {
            return 13;
        }
        $n1 += 6; // skip past the body tag
        if (($n2 = strpos($license_html,"</body>")) === FALSE) {
            return 17;
        }
        $length = $n2 - $n1;
        if ($length <= 0) {
            return 19;
        }
        $license = substr($license_html,$n1,$length);
        $md5sum = md5(str_replace(array("\r","\n","\t"," "),'',strip_tags($license)));
        if (strtolower($md5sum) != '49d0c7a462f4053087415ea8f90a40cf') {
            return 23;
        }
        if (($md5sum = md5_file('graphics/waslogo-567x142.png')) === FALSE) {
            return 29;
        }
        if (strtolower($md5sum) != 'c353849317767e6268df5f8e66b96c7e') {
            return 31;
        }
        return 0;
    } // fetch_license()


    /** this circumvents the 'magic' in magic_quotes_gpc() by conditionally stripping slashes
     *
     * This routine borrowed from {@link waslib.php}.
     *
     * @param string a string value that is conditionally unescaped
     * @return string the unescaped string 
     */
    function magic_unquote($value) {
        if (is_string($value)) {
            if (ini_get('magic_quotes_sybase') == 1) {
                $value = str_replace('\'\'','\'',$value);
            } elseif (get_magic_quotes_gpc() == 1) {
                $value = stripslashes($value);
            }
        }
        return $value;
    } // magic_unquote()


    /** quick and dirty dialogdef renderer
     *
     * This is a small routine to render simple dialogs with strings,
     * lists and checkboxes.
     *
     * Note that every element in the $dialogdef is in itself an array.
     * Recognised elements in those arrays are:
     *  - label (string): displayed before the actual input element
     *  - help (string): additional information for the user, rendered after/under the input element
     *  - value (mixed): the current value of the input element
     *  - show (bool): if TRUE, the element is displayed/rendered, otherwise it is simply skipped
     *  - type (enum): 's'=>string (text), 'p'=>password, 'l'=>list, 'b'=>bool(checkbox)
     *  - options(array): array with key-value-pairs with acceptable choices (used in 'l' (list) elements)
     *  - minlenght(int): minimum length of an input string or password
     *  - maxlenght(int): maximum length of an input string or password
     * The names of the input elements are copied from the keys used in $dialogdef.
     *
     * @param array $dialogdef contains labels, values and other information describing input elements
     * @param string $m improves readablity of generated code
     * @return string ready-to-use HTML-code comprising the rendered dialog
     */
    function render_dialog($dialogdef,$m) {
        $s = $m."<table>\n";
        $oddeven = "even";
        foreach($dialogdef as $name => $item) {
            if (!$item['show']) {
                continue;
            }
            $oddeven = ($oddeven == "even") ? "odd" : "even";
            switch ($item['type']) {
            case 's':
            case 'p':
                $inputtype = ($item['type'] == 'p') ? 'password' : 'text';
                $s .= $m."  <tr class=\"{$oddeven}\">\n".
                      $m."    <td class=\"{$oddeven}\" width=\"20%\" ><h3>{$item['label']}:</h3></td>\n".
                      $m."    <td class=\"{$oddeven}\">\n".
                      $m."      <input type=\"{$inputtype}\" name=\"{$name}\" value=\"".
                                       htmlspecialchars($item['value']).
                                       "\" size=\"50\" maxlength=\"{$item['maxlength']}\"><br>\n".
                      $m."      {$item['help']}\n".
                      $m."    </td>\n".
                      $m."  </tr>\n";
                break;

            case 'b':
                $checked = ($item['value']) ? ' checked' : '';
                $s .= $m."  <tr class=\"{$oddeven}\">\n".
                      $m."    <td class=\"{$oddeven}\" valign=\"top\"><h3>{$item['label']}:</h3></td>\n".
                      $m."    <td class=\"{$oddeven}\">\n".
                      $m."      <input type=\"checkbox\" name=\"{$name}\" value=\"1\"{$checked}> {$item['help']}\n".
                      $m."    </td>\n".
                      $m."  </tr>\n";
                break;

            case 'l':
                $s .= $m."  <tr class=\"{$oddeven}\">\n".
                      $m."    <td class=\"{$oddeven}\" valign=\"top\"><h3>{$item['label']}:</h3></td>\n".
                      $m."    <td class=\"{$oddeven}\">\n".
                      $m."      <select name=\"{$name}\">\n";
                foreach($item['options'] as $k => $v) {
                    $selected = ($item['value'] == $k) ? ' selected' : '';
                    $s .= $m."        <option value=\"".htmlspecialchars($k)."\"{$selected}>".
                                                        htmlspecialchars($v)."</option>\n";
                }
                  
                $s .= $m."      </select><br>\n".
                      $m."      ".$item['help']."\n".
                      $m."    </td>\n".
                      $m."  </tr>\n";
                break;

            default:
                // internal error, shouldn't happen
                $this->messages[] = 'Internal error: unknown type in render_dialog()';
                break;
            }
        }
        $s .= $m."</table>\n";
        return $s;
    } // render_dialog()


    /** construct a list of database options
     *
     * This constructs an array with key-value-pairs indicating
     * all available databases. Currently (september 2009) there is
     * only one database supported: mysql. However, in the future
     * we may support more databases, such as PostgreSQL.
     *
     * @return array list of available database types
     */
    function get_options_db_type() {
        return array(
            'mysql' => $this->t('db_type_option_mysql') //, 'postgresql' => $this->t('db_type_option_postgresql')
            );
    } // get_options_db_type()


    /** increment the error counter and perhaps slow things down
     *
     * this routine counts the number of errors. It is used to count the
     * number of attempts to guess a valid host/username/passord triplet.
     * If the number of errors reaches 3, a delay is added (the installation
     * program sleeps for a while). On every additional error an extra delay
     * is added (3 seconds at a time)  until the total delay reaches 24 seconds.
     * At that point the collected data are reset and effectively the user has
     * to start over.
     *
     * @return void error count incremented, optional delay introduced, and maybe data reset
     */
    function errorcount_bump() {
        $errorcount = ++$_SESSION['INSTALL']['errorcount'];
        $delay = max(0,min(24,$errorcount * 3 - 6));
        sleep($delay);
        if ($errorcount > 12) {
            $this->messages[] = $this->t('error_time_out');
            $_SESSION['INSTALL'] = $this->get_default_install_values();
        }
    } // errorcount_bump()


    /** reset the error counter
     *
     * this routine resets the effect of {@link errorcount_bump()}.
     *
     * @return void error counter reset to 0 (implying no more delays)
     */
    function errorcount_reset() {
        $_SESSION['INSTALL']['errorcount'] = 0;
    } // errorcount_reset()


    /** minimal validation of data input
     *
     * this routine is a KISS-validator; we check for min/max stringlengths in strings
     * and passwords (defaults: 0 and 255) and a valid item in listboxes. In case of error,
     * a message is added to $this->messages and the function returns FALSE. On success
     * we return TRUE. Note that additional validation could or should be done on some
     * fields, e.g. a password of sufficient complexity, etc. This routine does not do that.    
     *
     * @param array $item this array holds a field definition from a dialog definition
     * @param string $value this is the magic_unquote()'d and trim()'ed value the user POSTed
     * @return TRUE if minimal tests passed, otherwise FALSE + messages added to $this->messages
     */
    function validate($item,$value) {
        $retval = TRUE; // assume success
        switch ($item['type']) {
        case 's':
        case 'p':
            $minlength = (isset($item['minlength'])) ? intval($item['minlength']) : 0;
            $maxlength = (isset($item['maxlength'])) ? intval($item['maxlength']) : 255; // arbitrary but: KISS
            $length = strlen($value);
            $params = array('{FIELD}' => $item['label'], '{MIN}' => strval($minlength), '{MAX}' => strval($maxlength));
            if ($length < $minlength) {
                $this->messages[] = $this->t('error_too_short',$params);
                $retval = FALSE;
            } elseif ($maxlength < $length) {
                $this->messages[] = $this->t('error_too_long',$params);
                $retval = FALSE;
            }
            break;
        case 'l':
            if (!isset($item['options'][$value])) {
                $this->messages[] = $this->t('error_invalid',array('{FIELD}' => $item['label']));
                $retval = FALSE;
            }
            break;
        case 'b':
            break;
        default:
            // internal error, shouldn't happen
            $this->messages[] = 'Internal error: unknown type in validate()';
            break;
        }
        return $retval;
    } // validate()


    /** validation of password input
     *
     * this routine analyses the password provided against the minimal requirements
     * of password complexity.
     *
     * @param string $label this string holds the human-readable field name
     * @param string $password this is the magic_unquote()'d and trim()'ed password the user POSTed
     * @param int $min_lower the minimum number of lower case letters
     * @param int $min_upper the minimum number of upper case letters
     * @param int $min_digit the minimum number of digits
     * @return TRUE if tests passed, otherwise FALSE + messages added to $this->messages
     */
    function validate_password($label,$password,$min_lower=1,$min_upper=1,$min_digit=1) {
        $retval = TRUE; // assume success
        $n = strlen($password);
        $lower = 0;
        $upper = 0;
        $digit = 0;

        for ($i = 0; $i < $n; ++$i) {
            $c = $password{$i};
            if (ctype_lower($c)) {
                ++$lower;
            } elseif (ctype_upper($c)) {
                ++$upper;
            } elseif (ctype_digit($c)) {
                ++$digit;
            }
        }
        if (($lower < $min_lower) || ($upper < $min_upper) || ($digit < $min_digit)) {
            $retval = FALSE;
            $params = array(
                '{FIELD}' => $label,
                '{MIN_DIGIT}' => '1',
                '{MIN_LOWER}' => '1',
                '{MIN_UPPER}' => '1');
            $this->messages[] = $this->t('error_bad_password',$params);
        }
        return $retval;
    } // validate_password()


    /** check for name clash of new user (webmaster) and user accounts from demodata
     *
     * This routine checks to see if the name the webmaster supplied is not one
     * of the demodata accounts. If so, we flag the error in the messages.
     *
     * Note:
     * The list of accounts must be updated whenever the demodata is updated.
     * This is a kludge but I'll leave it this way for the time being. See also
     * the main file {@link demodata.php}.
     *
     * @param bool $demodata is installation of demodata requested?
     * @param string $label name of the username field in dialog
     * @param string $username the proposed username for the webmaster account
     * @return bool TRUE if there is no name clash, FALSE + message in $this->messages otherwise
     */
    function check_for_nameclash($demodata,$label,$username) {
        $retval = TRUE;
        if ($demodata) {
            $demo_accounts = array('acackl','mmonte','hparkh','ffrint','andrew','catherine','herbert','georgina');
            if (in_array(strtolower($username),$demo_accounts)) {
                $retval = FALSE;
                $this->messages[] = $this->t('error_nameclash',array('{FIELD}' => $label, '{USERNAME}' => $username));
            }
        }
        return $retval;
    } // check_for_nameclash()


    /** check for previous install
     *
     * this routine checks to see if another installation should be allowed.
     * Returns TRUE if the program was already installed or FALSE otherwise.
     *
     * @retval bool TRUE if already installed, FALSE otherwise
     */
    function is_already_installed() {
        $retval = FALSE;

        // 0 -- if we wrote config.php ourselves, we are NOT 'already' installed
        if ((isset($_SESSION['INSTALL']['config_php_written'])) && ($_SESSION['INSTALL']['config_php_written'])) {
            return FALSE;
        }

        // 1 -- where are we?
        $cms_progdir = dirname(__FILE__);
        if (($cms_dir = realpath($cms_progdir.'/..')) === FALSE) {
            $cms_dir = dirname($cms_progdir);
        }

        // 2 -- is this the first time?
        // If config.php already exists, we MUST assume the CMS is already installed.
        $retval = (is_file($cms_dir.'/config.php')) ? TRUE : FALSE;

        // 3 -- maybe report our findings via a message
        if ($retval) {
            $this->messages[] = $this->t('error_already_installed');
        }
        return $retval;
    } // is_already_installed()


    /** attempt to write the file config.php in the correct location
     *
     * this routine tries to write the file config.php. If this fails,
     * we return FALSE, otherwise TRUE. Note that the permissions of
     * the file are set to read-only (chmod 0400) for the owner of
     * the file, and nothing for group and world. That should be enough
     * for the webserver.
     *
     * @return bool TRUE on success, FALSE otherwise
     * @todo should we make the filemode (hardcoded at 0400) configurable/customisable?
     */
    function write_config_php() {
        $retval = TRUE; // assume success
        $filename = $_SESSION['INSTALL']['cms_dir'].'/config.php';
        $content = $this->construct_config_php();
        if (($fp = @fopen($filename,'wb')) === FALSE) {
            // $this->messages[] = 'cannot fopen()';
            $retval = FALSE; // cannot write config.php, indicate failure
        } elseif (($bytes_written = @fwrite($fp,$content)) === FALSE) {
            // $this->messages[] = 'cannot fwrite()';
            @fclose($fp);
            @unlink($filename);
            $retval = FALSE;
        } elseif ($bytes_written != strlen($content)) {
            // $this->messages[] = 'bytes do not add up';
            @fclose($fp);
            @unlink($filename);
            $retval = FALSE;
        } elseif (@fclose($fp) === FALSE) {
            // $this->messages[] = 'cannot fclose()';
            @unlink($filename);
            $retval = FALSE;
        } elseif (@chmod($filename,0400) === FALSE) {
            // $this->messages[] = 'cannot chmod()';
            $retval = TRUE; // really only a warning...
        }
        return $retval;
    } //write_config_php()


    /** retrieve an array of manifests for modules, themes or languages
     *
     * this examines the file system starting in the directory $path,
     * looking for manifest files. These manifest files are named after
     * the subdirectory they are in as follows.
     * Example:
     * If $path is /program/modules, this routine steps through that directory
     * and may find subdirectories 'htmlpage', 'guestbook' and 'forum'.
     * Eventually these manfest files are include()'d:
     * /program/modules/htmlpage/htmlpage_manifest.php,
     * /program/modules/guestbook/guestbook_manifest.php and
     * /program/modules/forum/forum_manifest.php.
     *
     * Every manifest file must describe the module (or language or theme)
     * via the following construct:
     * <code>
     * $manifests['htmlpage'] = array('name' => 'htmlpage', ...., 'cron_interval' => 0);
     * </code>
     *
     * After processing all the subdirectories of $path, the resulting array $manifests is
     * returned. Note that pseudo-directories like '.' and '..' are not considered. Also,
     * subdirectories 'foo' without the file 'foo_manifest.php' are also ignored.
     *
     * Note that the name of the manifest file itself is also stored in the array,
     * but excluding the subdirectory name.
     * 
     * @param string $path top directory for the search for manifest files
     * @return array zero or more arrays comprising manifests
     */
    function get_manifests($path) {
        $manifests = array();
        if (is_dir($path)) {
            if (($dp = opendir($path)) !== FALSE) {
                while (($item = readdir($dp)) !== FALSE) {
                    $item_path = $path.'/'.$item;
                    $item_manifest = $item_path.'/'.$item.'_manifest.php';
                    if ((substr($item,0,1) != '.') && (is_dir($item_path)) && (is_file($item_manifest))) {
                        @include($item_manifest);
                        $manifests[$item]['manifest'] = $item.'_manifest.php';
                    }
                }
                closedir($dp);
            }
        }
        return $manifests;
    } // get_manifests()


    /** create tables in database via include()'ing a file with tabledefs
     *
     * @param string $filename contains the table definitions
     * @return bool TRUE on success, FALSE otherwise + messages written to $this->messages
     * @uses $DB
     */
    function create_tables($filename) {
        global $DB;
        $retval = TRUE; // assume success
        $tabledefs = array();
        if (!file_exists($filename)) {
            $this->messages[] = $this->t('error_file_not_found',array('{FILENAME}' => $filename));
            return FALSE;
        } else {
            include($filename);
        }
        foreach($tabledefs as $tabledef) {
            $DB->drop_table($tabledef['name']); // DEBUG

            if ($DB->create_table($tabledef) === FALSE) {
                $params = array('{TABLENAME}' => $DB->prefix.$tabledef['name'],
                                '{ERRNO}' => $DB->errno,
                                '{ERROR}' => $DB->error
                                );
                $this->messages[] = $this->t('error_create_table',$params);
                $retval = FALSE;
            }
        }
        return $retval;
    } // create_tables()


    /** fill tables in database via include()'ing a file with tabledata
     *
     * @param string $filename contains the table definitions
     * @return bool TRUE on success, FALSE otherwise + messages written to $this->messages
     * @uses $DB
     */
    function insert_tabledata($filename) {
        global $DB;
        $retval = TRUE; // assume success
        $tabledata = array();
        if (!file_exists($filename)) {
            $this->messages[] = $this->t('error_file_not_found',array('{FILENAME}' => $filename));
            return FALSE;
        } else {
            include($filename);
        }
        foreach($tabledata as $data) {
            if (db_insert_into($data['table'],$data['fields']) === FALSE) {
                $params = array('{TABLENAME}' => $DB->prefix.$data['table'],
                                '{ERRNO}' => $DB->errno,
                                '{ERROR}' => $DB->error
                                );
                $this->messages[] = $this->t('error_insert_into_table',$params);
                $retval = FALSE;
            }
        }
        return $retval;
    } // insert_tabledata()


    /** generate a string with quasi-random characters
     *
     * This routine borrowed from {@link waslib.php}.
     *
     * @param int length of the string to generate
     * @param int number of candidate-characters to choose from
     * @retun string the generated string of $length characters
     */
    function quasi_random_string($length,$candidates=36) {
        static $alphanumerics = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $s = '';
        $max = max(9,min(61,--$candidates)); // make sure that 9 <= $max <= 61
        for ($i=0; $i<$length; ++$i) {
            $s .= $alphanumerics{rand(0,$max)};
        }
        return $s;
    } // quasi_random_string()


    /** sanitise a string to make it acceptable as a filename/directoryname
     *
     * This routine borrowed from {@link waslib.php}.
     *
     * @param string $filename the string to sanitise
     * @return string sanitised filename which is never empty
     */
    function sanitise_filename($filename)  {
        // strip leading space/dot/dash/underscore/backslash/slash
        $s = preg_replace('/^[ .\-_\\\\\\/]*/','',$filename);

        // strip trailing space/dot/dash/underscore/backslash/slash
        $s = preg_replace('/[ .\-_\\\\\\/]*$/','',$s);

        // replace embedded spaces/backslashes/slashes/at-signs/colons with underscores
        $s = strtr($s,' \\/@:','_____');

        // keep only letters/digits and embedded dots/dashes/underscores
        $s = preg_replace('/[^0-9A-Za-z.\-_]/','',$s);

        // replace sequences of underscores with a single underscore
        $s = preg_replace('/__+/','_',$s);

        // 'forbidden' words
        $forbidden = array('','aux','com1','com2','com3','com4','con','lpt1','lpt2','lpt3','lpt4','nul','prn');
        if (in_array(strtolower($s),$forbidden)) {
            $s = '_'.$s;
        }
        return $s;
    } // sanitise_filename()


    /** try to locate clamdscan or clamscan on the server
     *
     * This routine checks to see if either clamdscan or clamscan can be found
     * somewhere. This is done via educated guessing. On success we return the
     * path to the binary program file in $clamscan_path and the output of the
     * version command in $clamscan_version. The funtion returns TRUE if we did
     * find the clamscan program.
     *
     * Note that we scan the directories with opendir/readdir/closedir rather than
     * rely on file_exists() etc. because we would not find binaries with permissions 0711,
     * which would otherwise be perfectly acceptable to execute with exec().
     *
     * @param string &$clamscan_path path to binary program (output)
     * @param string &$clamscan_version version of the program we found (output)
     * @output bool TRUE on success, FALSE otherwise (and $clamscan_path and $clamscan_version undefined)
     */
    function clamscan_installed(&$clamscan_path,&$clamscan_version) {

        // 1 -- determine where to look
        $directories = array('/bin','/usr/bin','/usr/local/bin','/sbin','/usr/sbin/','/usr/local/sbin');
        $paths = (isset($_SERVER['PATH'])) ? explode(':',$_SERVER['PATH']) : array();
        foreach($paths as $path) {
            if (!in_array($path,$directories)) {
                $directories[] = $path;
            }
        }
        unset($paths);

        // 2A -- try to find clamdscan first
        $clamscans = array();
        foreach ($directories as $directory) {
            if ($handle = opendir($directory)) {
                while (($filename = readdir($handle)) !== FALSE) {
                    if (strtolower($filename) == 'clamscan') {
                        $clamscans[] = $directory.'/'.$filename; // keep for step 2B below
                    } elseif (strtolower($filename) == 'clamdscan') {
                        $command = $directory.'/'.$filename;
                        $argument = '-V'; // Request ClamAV version string
                        $lines = array();
                        $retval = -1; // assume failure
                        $lastline = exec($command.' '.$argument,$lines,$retval);
                        if ($retval == 0) { // success, we're done
                            $clamscan_path = $command;
                            $clamscan_version = $lastline;
                            closedir($handle);
                            return TRUE;
                        }
                    }
                }
            closedir($handle);
            }
        }

        // 2B -- no joy, now try clamscan's we encountered along the way
        $argument = '-V'; // Request ClamAV version string
        foreach ($clamscans as $command) {
            $lines = array();
            $retval = -1; // assume failure
            $lastline = exec($command.' '.$argument,$lines,$retval);
            if ($retval == 0) { // success
                $clamscan_path = $command;
                $clamscan_version = $lastline;
                return TRUE;
            }
        }

        // 3 -- nothing works, indicate failure to caller
        return FALSE;
    } // clamscan_installed()


    /** retrieve information about GD and supported graphics file formats
     *
     * this routine determines whether GD is enabled and which graphics file formats
     * are supported (check for GIF, JPG and PNG). If GD is not installed or if none
     * of these three formats are supported, the routine returns FALSE. Details (version
     * number, supported formats) are returned in &$details.
     *
     * GIF is a special case due to patent issues: there is a distinction between
     * read support and write support (see the PHP-documentation for details). GD-version >= 2.0.28
     * provides full support. This routine makes the distinction between fully supported
     * (GIF: Yes), reading but not writing (GIF: Readonly) and not supported (GIF: No).
     * 
     * @param string &$details returns detailed information about GD version and supported file formats
     * @return bool TRUE if at least one file format is fully supported, FALSE otherwise
     */
    function gd_supported(&$details) {
        if ((!function_exists('gd_info')) || (!function_exists('imagecreatetruecolor'))) {
            $details = $this->t('compatibility_gd_support_none');
            return FALSE;
        }
        $gdinfo = gd_info();
        $gif_support_read = ($gdinfo['GIF Read Support']) ? TRUE : FALSE;
        $gif_support_write = ($gdinfo['GIF Create Support']) ? TRUE : FALSE;
        $jpeg_support = ($gdinfo['JPG Support']) ? TRUE : FALSE;
        $png_support = ($gdinfo['PNG Support']) ? TRUE : FALSE;

        $params = array('{VERSION}' => $gdinfo['GD Version'],
                        '{GIF}' => ($gif_support_read) ? ($gif_support_write) ? $this->t('yes') : 
                                   $this->t('compatibility_gd_support_gif_readonly') : $this->t('no'),
                        '{JPG}' => ($jpeg_support) ? $this->t('yes') : $this->t('no'),
                        '{PNG}' => ($png_support) ? $this->t('yes') : $this->t('no'));
        $details = $this->t('compatibility_gd_support_details',$params);
        return ((($gif_support_read) && ($gif_support_write)) || ($jpeg_support) || ($png_support)) ? TRUE : FALSE;
    } // gd_supported()

} // InstallWizard()

?>