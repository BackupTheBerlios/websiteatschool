<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker <peter@berestijn.nl>
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

/** /program/lib/translatetool.class.php - taking care of language translations
 *
 * This file defines a class for managing translations.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: translatetool.class.php,v 1.11 2012/04/18 09:02:02 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

define('TRANSLATETOOL_CHORE_OVERVIEW',          'overview');
define('TRANSLATETOOL_CHORE_LANGUAGE_ADD',      'language_add');
define('TRANSLATETOOL_CHORE_LANGUAGE_SAVE_NEW', 'language_savenew');
define('TRANSLATETOOL_CHORE_LANGUAGE_EDIT',     'language_edit');
define('TRANSLATETOOL_CHORE_LANGUAGE_SAVE',     'language_save');
define('TRANSLATETOOL_CHORE_EDIT',              'edit');
define('TRANSLATETOOL_CHORE_SAVE',              'save');

/** This parameter identifies the language. Note: it should not be confused with the global 'language' parameter */
define('TRANSLATETOOL_PARAM_LANGUAGE_KEY',      'language_key');
define('TRANSLATETOOL_PARAM_DOMAIN',            'domain');


/** Methods to access properties of a language and modify translations
 *
 * This class is used to manage languages and translations.
 * The following functions are supplied
 *
 *  - add a new language
 *  - edit the properties of an existing language (including active flag)
 *  - add/edit translations of texts
 *
 * The default action is to show a list of existing languages. From there
 * the user can navigate to adding/editing language properties or manipulating
 * translations in a particular language.
 *
 */
class TranslateTool {
    /** @var object|null collects the html output */
    var $output = NULL;

    /** @var bool if TRUE the calling routing is allowed to use the menu area (e.g. show config mgr menu) */
    var $show_parent_menu = FALSE;

    /** @var array list of all language records (including inactive ones), keyed with language_key */
    var $languages = array();

    /** @var array list of all language domains grouped by program, modules, themes and install */
    var $domains = array();

    /** construct a TranslateTool object
     *
     * This initialises the TranslateTool and also dispatches the chore to do.
     *
     * @param object &$output collects the html output
     * @uses $CFG
     * @uses $LANGUAGE
     */
    function TranslateTool(&$output) {
        global $CFG,$LANGUAGE;
        $this->output = &$output;
        $this->output->set_helptopic('translatetool');
        $this->languages = $LANGUAGE->retrieve_languages(); // All languages, including the inactive ones (if any)
        $this->domains = $this->get_domains();

        $chore = get_parameter_string('chore',TRANSLATETOOL_CHORE_OVERVIEW);
        switch($chore) {
        case TRANSLATETOOL_CHORE_OVERVIEW:
            $this->languages_overview();
            break;

        case TRANSLATETOOL_CHORE_LANGUAGE_ADD:
            $this->language_add();
            break;

        case TRANSLATETOOL_CHORE_LANGUAGE_SAVE_NEW:
            $this->language_savenew();
            break;

        case TRANSLATETOOL_CHORE_LANGUAGE_EDIT:
            $this->language_edit();
            break;

        case TRANSLATETOOL_CHORE_LANGUAGE_SAVE:
            $this->language_save();
            break;

        case TRANSLATETOOL_CHORE_EDIT:
            $this->translation_edit();
            break;

        case TRANSLATETOOL_CHORE_SAVE:
            $this->translation_save();
            break;

        default:
            $s = (utf8_strlen($chore) <= 50) ? $chore : utf8_substr($chore,0,44).' (...)';
            $message = t('chore_unknown','admin',array('{CHORE}' => htmlspecialchars($s)));
            $output->add_message($message);
            logger(sprintf('%s.%s(): unknown chore \'%s\'',__CLASS__,__FUNCTION__,htmlspecialchars($s)));
            $this->languages_overview();
            break;
        }
    } // TranslateTool()


    /** allow the caller to use the menu area (or not)
     *
     * this routine tells the caller if it is OK to use
     * the menu area (TRUE returned) or not (FALSE returned).
     *
     * @return bool TRUE if menu area is available, FALSE otherwise
     */
    function show_parent_menu() {
        return $this->show_parent_menu;
    } // show_parent_menu()


    // ==================================================================
    // =========================== WORKHORSES ===========================
    // ==================================================================


    /** display list of languages with edit icons and an option to add a language
     *
     * this constructs the languages overview: a link to add a language, followed by
     * a list of languages based on the languages in the database. Every language has
     * an icon through which the properties of the language can be modified, including
     * setting/resetting the active flag. (Only active languages can be used on the
     * website and in the CMS). Note that we use _all_ languages here, including
     * inactive ones.
     *
     * Note that the calling routine (the tools manager) is allowed to
     * display a menu because we set the parameter show_parent_menu to
     * TRUE here.
     *
     * The constructed list looks something like this:
     *
     * <code>
     *     Add a language
     * [E] Deutsch (de) (inactive)
     * [E] English (en)
     * [E] Nederlands (nl)
     * ...
     * </code>
     *
     * The clickable icons [E] lead to the Edit language properties.
     * The clickable titles lead to the actual translations
     * The clickable link 'Add an area' leads to the add new language dialog.
     *
     * @return void results are returned as output in $this->output
     * @todo should we add a paging function to the (perhaps looooong) list of languages?
     * @uses $USER
     * @uses $WAS_SCRIPT_NAME
     * @uses $CFG
     */
    function languages_overview() {
        global $USER,$WAS_SCRIPT_NAME,$CFG;

        // 1 -- Start content and UL-list
        $this->output->add_content('<h2>'.t('menu_translatetool','admin').'</h2>');
        $this->output->add_content('<ul>');

        // 2 -- Add an 'add a language' option
        $this->output->add_content('  <li class="list">');
        // line up the prompt with links to existing languages below (if any)
        if (!$this->output->text_only) {
            $icon_blank = '    '.$this->output->skin->get_icon('blank');
            $this->output->add_content($icon_blank);
        } // else { 
            //don't clutter the text-only interface with superfluous layout fillers 
        // }
        $a_attr = array('title'=> t('translatetool_add_a_language_title','admin'));
        $a_param = $this->a_param(TRANSLATETOOL_CHORE_LANGUAGE_ADD);
        $anchor = t('translatetool_add_a_language','admin');
        $this->output->add_content('    '.html_a($WAS_SCRIPT_NAME,$a_param,$a_attr,$anchor));

        // 3 -- Add a list of existing languages (both active and inactive)
        if (sizeof($this->languages) > 0) {
            foreach($this->languages as $language_key => $language) {
                $a_param = $this->a_param(TRANSLATETOOL_CHORE_EDIT,$language_key);
                $a_attr = array('title' => t('translatetool_edit_translation_title','admin'));
                $params = array('{LANGUAGE_KEY}' => $language_key, '{LANGUAGE_NAME}' => $language['language_name']);
                $anchor = t('translatetool_edit_translation','admin',$params);
                $anchor .= (db_bool_is(TRUE,$language['is_active'])) ? '' : ' ('.t('inactive','admin').')';
                $this->output->add_content('  <li class="list">');
                $this->output->add_content('    '.$this->get_icon_edit($language_key));
                $this->output->add_content('    '.html_a($WAS_SCRIPT_NAME,$a_param,$a_attr,$anchor));
            }
        }

        // 4 -- close the list and allow caller to show the configuration manager menu too
        $this->output->add_content('</ul>');
        $this->show_parent_menu = TRUE;
    } // languages_overview()


    /** present the language dialog where the user can enter properties for a new language
     *
     * this displays a dialog where the user can enter the properties
     * of a new language. These properties are: 
     *  - name (expressed in the language itself)
     *  - key (2- or 3-letter code, presumably based on ISO 639-1 or ISO 639-2
     *  - parent_key (this language is based on which existing language)
     *  - active flag
     *
     * The new languageis saved via performing the 'chore' TRANSLATETOOL_CHORE_LANGUAGE_SAVE_NEW.
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME
     * @uses $USER
     */
    function language_add() {
        global $WAS_SCRIPT_NAME,$USER;

        $this->output->add_content('<h2>'.t('translatetool_add_language_header','admin').'</h2>');
        $this->output->add_content(t('translatetool_add_language_explanation','admin'));
        $href = href($WAS_SCRIPT_NAME,$this->a_param(TRANSLATETOOL_CHORE_LANGUAGE_SAVE_NEW));
        $dialogdef = $this->get_dialogdef_language();
        $this->output->add_content(dialog_quickform($href,$dialogdef));
        $this->show_parent_menu = TRUE;
    } // language_add()


    /** show the language edit dialog
     *
     * display a dialog where the user can modify language properties.
     * we re-use the routine that created the add language dialog.
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME
     * @uses $USER
     */
    function language_edit() {
        global $WAS_SCRIPT_NAME,$USER;
        $language_key = get_parameter_string(TRANSLATETOOL_PARAM_LANGUAGE_KEY);

        // 1 -- basic sanity
        if (($this->languages === FALSE) || (!isset($this->languages[$language_key]))) {
            // are they trying to trick us, specifying an invalid language?
            logger(sprintf('%s.%s(): weird: user tried to edit non-existing language \'%s\'',
                            __CLASS__,__FUNCTION__,htmlspecialchars($language_key)));
            $params = array('{LANGUAGE_KEY}' => htmlspecialchars($language_key));
            $this->output->add_message(t('invalid_language','admin',$params));
            $this->languages_overview();
            return;
        }

        // 2 -- still here? show dialog
        $this->output->add_content('<h2>'.t('translatetool_edit_language_header','admin').'</h2>');
        $this->output->add_content(t('translatetool_edit_language_explanation','admin'));
        $href = href($WAS_SCRIPT_NAME,$this->a_param(TRANSLATETOOL_CHORE_LANGUAGE_SAVE,$language_key));
        $dialogdef = $this->get_dialogdef_language($language_key);
        $this->output->add_content(dialog_quickform($href,$dialogdef));
        $this->show_parent_menu = TRUE;
    } // language_edit()


    /** save the newly added language to the database
     *
     * This saves the essential information of a new language to the database,
     * using sensible defaults for the other fields. Also, a data directory
     * is created in $CFG->datadir
     *
     * If something goes wrong, the user can redo the dialog, otherwise we
     * return to the languages overview, with the newly added language in the
     * list, too.
     *
     * Apart from the standard checks the following checks are done:
     *  - the language key should be an acceptable directory name
     *  - the language key should be lowercase
     *  - the language key should not exist already (in $this->languages)
     *  - the directory should not yet exist
     *  - the directory must be created here and now
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME
     * @uses $CFG
     * @uses $USER
     * @uses $LANGUAGE
     */
    function language_savenew() {
        global $WAS_SCRIPT_NAME,$USER,$CFG,$LANGUAGE;

        // 1 -- bail out if user pressed cancel button
        if (isset($_POST['button_cancel'])) {
            $this->output->add_message(t('cancelled','admin'));
            $this->languages_overview();
            return;
        }
        // 2 -- validate the data; check for generic errors (string too short, number too small, etc)
        $dialogdef = $this->get_dialogdef_language();
        $invalid = (dialog_validate($dialogdef)) ? FALSE : TRUE;

        // 3 -- additional validation & massaging of the language key
        $fname = (isset($dialogdef['language_key']['label'])) ? $dialogdef['language_key']['label'] : 'language_key';
        $params = array('{FIELD}' => str_replace('~','',$fname));

        // 3A -- additional check: the language key doubles as a directory name AND should be lowercase
        $path = $dialogdef['language_key']['value'];
        $languagedata_directory = strtolower(sanitise_filename($path));
        if ($path != $languagedata_directory) {
            // User probably entered a few 'illegal' characters. This is no good
            $dialogdef['language_key']['value'] = $languagedata_directory; // 'Help' user with a better proposition?
            ++$dialogdef['language_key']['errors'];
            $params['{VALUE}'] = htmlspecialchars($path);
            $dialogdef['language_key']['error_messages'][] = t('validate_bad_filename','',$params);
            $invalid = TRUE;
        }

        // 3B -- additional check: unique language key name entered
        if (isset($this->languages[$languagedata_directory])) { // Oops, already exists
            ++$dialogdef['language_key']['errors'];
            $params['{VALUE}'] = $languagedata_directory;
            $dialogdef['language_key']['error_messages'][] = t('validate_not_unique','',$params);
            $invalid = TRUE;
        }

        // 3C -- additional check: can we create said directory?
        $languagedata_full_path = $CFG->datadir.'/languages/'.$languagedata_directory;
        $languagedata_directory_created = @mkdir($languagedata_full_path,0700);
        if ($languagedata_directory_created) {
            @touch($languagedata_full_path.'/index.html'); // "protect" the newly created directory from prying eyes
        } else {
            // Mmmm, failed; probably already exists then. Oh well. Go flag error.
            ++$dialogdef['language_key']['errors'];
            $params['{VALUE}'] = '/languages/'.$languagedata_directory;
            $dialogdef['language_key']['error_messages'][] = t('validate_already_exists','',$params);
            $invalid = TRUE;
        }

        // 3E -- if there were any errors go redo dialog while keeping data already entered
        if ($invalid) {
            if ($languagedata_directory_created) { // Only get rid of the directory _we_ created
                @unlink($languagedata_full_path.'/index.html');
                @rmdir($languagedata_full_path);
            }
            // there were errors, show them to the user and do it again
            foreach($dialogdef as $k => $item) {
                if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                    $this->output->add_message($item['error_messages']);
                }
            }
            $this->output->add_content('<h2>'.t('translatetool_add_language_header','admin').'</h2>');
            $this->output->add_content(t('translatetool_add_language_explanation','admin'));
            $href = href($WAS_SCRIPT_NAME,$this->a_param(TRANSLATETOOL_CHORE_LANGUAGE_SAVE_NEW));
            $this->output->add_content(dialog_quickform($href,$dialogdef));
            return;
        }

        // 4 -- go save the new language
        $language_key = $dialogdef['language_key']['value'];
        $language_name = $dialogdef['language_name']['value'];
        $parent_key = ($dialogdef['parent_language_key']['value'] == '--') ? 
                        NULL : $dialogdef['parent_language_key']['value'];
        $is_active = ($dialogdef['language_is_active']['value'] == '1') ? TRUE : FALSE;
        $fields = array(
            'language_key'        => $language_key,
            'parent_language_key' => $parent_key,
            'language_name'       => $language_name,
            'version'             => 0,
            'manifest'            => '',
            'is_core'             => FALSE,
            'is_active'           => $is_active,
            'dialect_in_database' => FALSE,
            'dialect_in_file'     => FALSE
            );
        if (db_insert_into('languages',$fields) === FALSE) {
            if ($languagedata_directory_created) { // Only get rid of the file/directory _we_ created
                @unlink($languagedata_full_path.'/index.html');
                @rmdir($languagedata_full_path);
            }
            logger(sprintf('%s.%s(): saving new language \'%s\' failed: %s',
                            __CLASS__,__FUNCTION__,htmlspecialchars($language_key),db_errormessage()));
            $this->output->add_message(t('translatetool_language_savenew_failure','admin'));
        } else {
            $params = array('{LANGUAGE_KEY}' => $language_key,'{LANGUAGE_NAME}' => $language_name);
            $this->output->add_message(t('translatetool_language_savenew_success','admin',$params));
            logger(sprintf("%s.%s(): success saving new language '%s' (%s) with data directory /languages/%s",
                           __CLASS__,__FUNCTION__,$language_name,$language_key,$languagedata_directory));
            $this->languages = $LANGUAGE->retrieve_languages(TRUE); // TRUE means force reread from database after add
        }
        $this->languages_overview();
    } // language_savenew()


    /** validate and save modified data to database
     *
     * this saves data from the edit language  dialog if data validates.
     * If the data does NOT validate, the edit screen is displayed again
     * otherwise the languages overview is displayed again.
     *
     * @return void results are returned as output in $this->output
     * @uses $WAS_SCRIPT_NAME
     * @uses $CFG
     * @uses $USER
     * @uses $LANGUAGE
     */
    function language_save() {
        global $CFG,$WAS_SCRIPT_NAME,$USER,$LANGUAGE;
        $language_key = get_parameter_string(TRANSLATETOOL_PARAM_LANGUAGE_KEY);

        // 1 -- basic sanity
        if (($this->languages === FALSE) || (!isset($this->languages[$language_key]))) {
            // are they trying to trick us, specifying an invalid language?
            logger(sprintf('%s.%s(): weird: user tried to save properties of non-existing language \'%s\'',
                            __CLASS__,__FUNCTION__,htmlspecialchars($language_key)));
            $params = array('{LANGUAGE_KEY}' => htmlspecialchars($language_key));
            $this->output->add_message(t('invalid_language','admin',$params));
            $this->languages_overview();
            return;
        }

        // 2 -- if the user cancelled the operation, there is no point in hanging 'round
        if (isset($_POST['button_cancel'])) {
            $this->output->add_message(t('cancelled','admin'));
            $this->languages_overview();
            return;
        }

        // 3 -- validate the data; check for generic errors (string too short, number too small, etc)
        $dialogdef = $this->get_dialogdef_language($language_key);
        if (!dialog_validate($dialogdef)) { // show errors to the user and do it again
            foreach($dialogdef as $k => $item) {
                if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                    $this->output->add_message($item['error_messages']);
                }
            }
            $this->output->add_content('<h2>'.t('translatetool_edit_language_header','admin').'</h2>');
            $this->output->add_content(t('translatetool_edit_language_explanation','admin'));
            $href = href($WAS_SCRIPT_NAME,$this->a_param(TRANSLATETOOL_CHORE_LANGUAGE_SAVE,$language_key));
            $this->output->add_content(dialog_quickform($href,$dialogdef));
            return;
        }

        // 4 -- still here? go save changes
        $language_name = $dialogdef['language_name']['value'];
        $parent_key = ($dialogdef['parent_language_key']['value'] == '--') ? 
                        NULL : $dialogdef['parent_language_key']['value'];
        $is_active = ($dialogdef['language_is_active']['value'] == '1') ? TRUE : FALSE;
        $fields = array(
            'parent_language_key' => $parent_key,
            'language_name'       => $language_name,
            'is_active'           => $is_active
            );
        $where = array('language_key' => $language_key);
        if (db_update('languages',$fields,$where) === FALSE) {
            logger(sprintf('%s.%s(): saving changes forlanguage \'%s\' failed: %s',
                            __CLASS__,__FUNCTION__,htmlspecialchars($language_key),db_errormessage()));
            $this->output->add_message(t('translatetool_language_save_failure','admin'));
        } else {
            $params = array('{LANGUAGE_KEY}' => $language_key,'{LANGUAGE_NAME}' => $language_name);
            $this->output->add_message(t('translatetool_language_save_success','admin',$params));
            logger(sprintf("%s.%s(): success saving language properties '%s' (%s)",
                            __CLASS__,__FUNCTION__,$language_name,$language_key));
            $this->languages = $LANGUAGE->retrieve_languages(TRUE); // TRUE means force reread from database after edit
        }
        $this->languages_overview();
    } // language_save()


    /** show an edit dialog with phrases from $full_domain in $language_key
     *
     * After some sanity checking this routine shows al dialog where the user can
     * edit translations for the selected language and domain.
     * Note that this could be a huge dialog, depending on the size of the language
     * domein ('admin' is notoriously large). Sending this routine to the browser can
     * take some time.
     */
    function translation_edit() {
        global $WAS_SCRIPT_NAME;

        $language_key = get_parameter_string(TRANSLATETOOL_PARAM_LANGUAGE_KEY);

        // 1A -- basic sanity for language
        if (($this->languages === FALSE) || (!isset($this->languages[$language_key]))) {
            // are they trying to trick us, specifying an invalid language?
            logger(sprintf('%s.%s(): weird: user tried to edit translations for non-existing language \'%s\'',
                            __CLASS__,__FUNCTION__,htmlspecialchars($language_key)));
            $params = array('{LANGUAGE_KEY}' => htmlspecialchars($language_key));
            $this->output->add_message(t('invalid_language','admin',$params));
            $this->languages_overview();
            return;
        }

        $full_domain = get_parameter_string(TRANSLATETOOL_PARAM_DOMAIN,'was');

        // 1B -- basic sanity for domain
        if (!isset($this->domains[$full_domain])) {
            logger(sprintf('%s.%s(): weird: user requested non-existing domain \'%s\'; using default instead',
                            __CLASS__,__FUNCTION__,htmlspecialchars($full_domain)));
            $params = array('{FULL_DOMAIN}' => htmlspecialchars($full_domain));
            $this->output->add_message(t('invalid_language_domain','admin',$params));
            // we will change the domain to the default domain now.
            $full_domain = 'was';
        }

        // At this point we have a valid language/full_domain combo. We now need to setup
        // the edit dialog for this combo and also a menu so the user can navigate to another language domain.

        // 2 -- still here? show dialog & menu
        $example_html = '<strong>';
        $example_variable = '{VALUE}';
        $example_tilde = '~';
        $examples = array('{EXAMPLE_HTML}'     => $this->code_highlight($example_html),
                          '{EXAMPLE_VARIABLE}' => $this->code_highlight($example_variable),
                          '{EXAMPLE_TILDE}'    => $this->code_highlight($example_tilde));
        $params = array('{LANGUAGE_KEY}' => $language_key,
                        '{LANGUAGE_NAME}' => $this->languages[$language_key]['language_name'],
                        '{FULL_DOMAIN}' => $this->domains[$full_domain]['title']);
        $this->output->add_content('<h2>'.t('translatetool_edit_language_domain_header','admin',$params).'</h2>');
        $this->output->add_content(t('translatetool_edit_language_domain_explanation','admin',$examples));
        $this->output->add_content('<p>');
        $href = href($WAS_SCRIPT_NAME,$this->a_param(TRANSLATETOOL_CHORE_SAVE,$language_key,$full_domain));
        $dialogdef = $this->get_dialogdef_language_domain($language_key,$full_domain);
        $this->output->add_content($this->render_translation_dialog($href,$dialogdef));

        $this->show_domain_menu($language_key,$full_domain);
    } // translation_edit()


    /** save the modified translations in a file in the tree CFG->datadir/languages/
     *
     * this routine validates the dialog data and attempts to save the changes in the file
     * $full_domain in the directory CFG->datadir/languages/$language_key/. Also, we may
     * need to send a message to the Website@School project with our changes (depending
     * on the flag _submit).
     *
     * @return void data saved and output written to browser via $this->output
     */
    function translation_save() {
        global $WAS_SCRIPT_NAME,$LANGUAGE;

        $language_key = get_parameter_string(TRANSLATETOOL_PARAM_LANGUAGE_KEY);

        // 1A -- basic sanity for language
        if (($this->languages === FALSE) || (!isset($this->languages[$language_key]))) {
            logger(sprintf('%s.%s(): weird: user tried to edit translations for non-existing language \'%s\'',
                            __CLASS__,__FUNCTION__,htmlspecialchars($language_key)));
            $params = array('{LANGUAGE_KEY}' => htmlspecialchars($language_key));
            $this->output->add_message(t('invalid_language','admin',$params));
            $this->languages_overview();
            return;
        }

        $full_domain = get_parameter_string(TRANSLATETOOL_PARAM_DOMAIN,'was');

        // 1B -- basic sanity for domain
        if (!isset($this->domains[$full_domain])) {
            logger(sprintf('%s.%s(): weird: user requested non-existing domain \'%s\'',
                            __CLASS__,__FUNCTION__,htmlspecialchars($full_domain)));
            $params = array('{FULL_DOMAIN}' => htmlspecialchars($full_domain));
            $this->output->add_message(t('invalid_language_domain','admin',$params));
            $this->languages_overview();
            return;
        }

        // 2 -- if the user cancelled the operation, there is no point in hanging 'round
        if (isset($_POST['button_cancel'])) {
            $this->output->add_message(t('cancelled','admin'));
            $this->languages_overview();
            return;
        }

        // 3 -- validate the dialog and maybe redo it
        $dialogdef = $this->get_dialogdef_language_domain($language_key,$full_domain);
        if (!dialog_validate($dialogdef)) {
            // there were errors, show them to the user and do it again
            foreach($dialogdef as $k => $item) {
                if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                    $this->output->add_message($item['error_messages']);
                }
            }
            $example_html = '<strong>';
            $example_variable = '{VALUE}';
            $example_tilde = '~';
            $examples = array('{EXAMPLE_HTML}'     => $this->code_highlight($example_html),
                              '{EXAMPLE_VARIABLE}' => $this->code_highlight($example_variable),
                              '{EXAMPLE_TILDE}'    => $this->code_highlight($example_tilde));
            $params = array('{LANGUAGE_KEY}' => $language_key,
                            '{LANGUAGE_NAME}' => $this->languages[$language_key]['language_name'],
                            '{FULL_DOMAIN}' => $this->domains[$full_domain]['title']);
            $this->output->add_content('<h2>'.t('translatetool_edit_language_domain_header','admin',$params).'</h2>');
            $this->output->add_content(t('translatetool_edit_language_domain_explanation','admin',$examples));
            $this->output->add_content('<p>');
            $href = href($WAS_SCRIPT_NAME,$this->a_param(TRANSLATETOOL_CHORE_SAVE,$language_key,$full_domain));
            $this->output->add_content($this->render_translation_dialog($href,$dialogdef));
            // no menu this time, let user concentrate on task at hand ie errorfree data input
            return;
        }

        // 4 -- actually proceed with saving the data

        // 4A -- construct a diff between the system translation (if any) and the dialogdef values
        $strings = array();
        $dummy = array();
        $diff = array();
        $this->get_strings_system($language_key,$full_domain,$strings,$dummy);
        foreach($dialogdef as $name => $item) {
            if ((!isset($item['key'])) || ($item['type'] == F_SUBMIT)) { // skip buttons and 'meta'-fields
                continue;
            }
            $key = $item['key'];
            # massage $value and standardise on \n as EOL
            $value = str_replace("\r\n","\n",$item['value']);
            $value = str_replace("\n\r","\n",$value);
            $value = str_replace("\r","\n",$value);
            if ((!isset($strings[$key])) || ($strings[$key] != $value)) {
                $diff[$key] = $value;
            }
        }
        // 4B -- if the diff is non-empty, go write and maybe submit it
        $params = array(
            '{LANGUAGE_KEY}' => $language_key,
            '{LANGUAGE_NAME}' => $this->languages[$language_key]['language_name'],
            '{FULL_DOMAIN}' => $this->domains[$full_domain]['title']
            );
        $diff_count = sizeof($diff);
        if ($diff_count == 0) { // No changes need to be saved
            $this->output->add_message(t('translatetool_no_changes_to_save','admin',$params));
        } else {
            $diff['_submit'] = ($dialogdef['_submit']['value'] == '1') ? '1' : '0';
            $diff['_full_name'] = $dialogdef['_full_name']['value'];
            $diff['_email'] = $dialogdef['_email']['value'];
            $diff['_notes'] = $dialogdef['_notes']['value'];
            $retval = $this->put_strings_userfile($language_key,$full_domain,$diff);
            if (!$retval) {
                logger(sprintf('%s.%s(): could not write translation file for %s - %s (%d items)',
                               __CLASS__,__FUNCTION__,$language_key,$full_domain,$diff_count));
            } else {
                logger(sprintf('%s.%s(): success writing translation file for %s - %s: %d items',
                               __CLASS__,__FUNCTION__,$language_key,$full_domain,$diff_count),WLOG_DEBUG);
                if (db_bool_is(FALSE,$this->languages[$language_key]['dialect_in_file'])) {
                    $table = 'languages';
                    $fields = array('dialect_in_file' => TRUE);
                    $where = array('language_key' => $language_key);
                    $retval = db_update($table,$fields,$where);
                    if ($retval) {
                        logger(sprintf('%s.%s(): updated language %s: dialect_in_file is now enabled',
                               __CLASS__,__FUNCTION__,$language_key),WLOG_DEBUG);
                    } else {
                        logger(sprintf('%s.%s(): update of language %s failed: %s',
                               __CLASS__,__FUNCTION__,$language_key,db_errormessage()));
                    }
                }
            }
            if ($retval) {
                $this->output->add_message(t('translatetool_translation_save_success','admin',$params));
            } else {
                $this->output->add_message(t('translatetool_translation_save_failure','admin',$params));
            }
            if ($diff['_submit'] == '1') {
                if ($this->submit_diff_to_project($language_key,$full_domain,$diff)) {
                    $this->output->add_message(t('translatetool_translation_submit_success','admin',$params));
                    logger(sprintf('%s.%s(): success submitting translations for %s - %s: %d items',
                                   __CLASS__,__FUNCTION__,$language_key,$full_domain,$diff_count),WLOG_DEBUG);
                } else {
                    logger(sprintf('%s.%s(): could not submit translations for %s - %s (%d items)',
                                   __CLASS__,__FUNCTION__,$language_key,$full_domain,$diff_count));
                    $this->output->add_message(t('translatetool_translation_submit_failure','admin',$params));
                }
            }
        }
        
        // 5 -- clear cache and force reread of translation just saved
        $LANGUAGE->reset_cache($language_key,$full_domain);
        $this->translation_edit();
    } // translation_save()


    // ==================================================================
    // ======================== UTILITY ROUTINES ========================
    // ==================================================================

    /** construct the language dialog (used for both add and edit)
     *
     * this constructs a language dialog definition, maybe filled with data
     * The main difference between dialogs for add and edit is that an existing
     * language code ($language_key) cannot be changed; the corresponding field
     * is shown in 'viewonly' mode. Another small difference is that an existing
     * language cannot have itself as a parent language.
     *
     * Note that we populate the 'edit' dialog with existing data from $this->languages.
     *
     * @param string $language_key identifies the language to edit (empty string for add new language)
     * @return array contains the dialog definition
     */
    function get_dialogdef_language($language_key='') {
        $language_name = '';
        $parent_language_key = '';
        $is_active = '1';
        if ((!empty($language_key)) && (isset($this->languages[$language_key]))) {
            $language = $this->languages[$language_key];
            $language_name = (isset($language['language_name'])) ? $language['language_name'] : '';
            $parent_language_key = (isset($language['parent_language_key'])) ? $language['parent_language_key'] : '';
            $is_active = ((isset($language['is_active'])) && (db_bool_is(TRUE,$language['is_active']))) ? '1' : '';
        }
        $dialogdef = array(
            'language_key' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'language_key',
                'minlength' => 2,
                'maxlength' => 3,
                'columns' => 30,
                'label' => t('translatetool_language_key_label','admin'),
                'title' => t('translatetool_language_key_title','admin'),
                'value' => $language_key,
                'viewonly' => (empty($language_key)) ? FALSE : TRUE
                ),
            'language_name' => array(
                'type' => F_ALPHANUMERIC,
                'name' => 'language_name',
                'minlength' => 1,
                'maxlength' => 80,
                'columns' => 30,
                'label' => t('translatetool_language_name_label','admin'),
                'title' => t('translatetool_language_name_title','admin'),
                'value' => $language_name,
                ),
            'parent_language_key' => array(
                'type' => F_LISTBOX,
                'name' => 'parent_language_key',
                'value' => $parent_language_key,
                'label' => t('translatetool_language_parent_label','admin'),
                'title' => t('translatetool_language_parent_title','admin'),
                'options' => $this->get_options_languages($language_key),
                ),
            'language_is_active' => array(
                'type' => F_CHECKBOX,
                'name' => 'language_is_active',
                'options' => array('1' => t('translatetool_language_is_active_check','admin')),
                'label' => t('translatetool_language_is_active_label','admin'),
                'title' => t('translatetool_language_is_active_title','admin'),
                'value' => $is_active
                ),
            'button_save' => dialog_buttondef(BUTTON_SAVE),
            'button_cancel' => dialog_buttondef(BUTTON_CANCEL)
            );
        return $dialogdef;
    } // get_dialogdef_language()


    /** construct the translation dialog for selected language and domain
     *
     * this constructs a translation dialog definition, filled with translations
     * for language $language_key and domain $full_domain. The labels for the fields
     * are derived from the English texts in $full_domain, in the order specified by
     * the English file. If the English file contains comments, these are added to
     * the item too (to be displayed as additional information for the translator).
     * The current translation of the $full_domain is retrieved the usual way, via
     * function t() (shorthand for $LANGUAGE->get_phrase()) but without translating
     * any variables (e.g. {VALUE}).
     * Note that if a translation of a phrase does not exist in the target language,
     * the get_phrase() routine will eventually yield the English translation (after
     * trying the language parents first). Also note that the translated phrases
     * could be retrieved from a user file (ie. a file from 
     * $CFG->datadir/languages/$language_key/$full_domain).
     *
     * @param string $language_key identifies the language to edit
     * @param string $full_domain identifies the language domain
     * @return array contains the dialog definition
     * @uses $USER
     * @uses $CFG
     * @todo try to figure this out: when the delimiter in $name was a dot '.' $_POST contained a '_' instead. WTF?
     *       (it seems that a colon works... for now)
     */
    function get_dialogdef_language_domain($language_key='',$full_domain='') {
        global $USER,$CFG;

        // 1 -- get the source language English ('en') (both strings and comments)
        $dialogdef = array();
        $strings = array();
        $comments = array();
        $this->get_strings_system('en',$full_domain,$strings,$comments);

        // 2 -- get the existing translation (if any) via the regular translation routine t() and make dialogdef.
        $i = 0;
        foreach($strings as $k => $v) {
            $name = $full_domain.':'.$k; // weird: when this delimiter was a dot '.' $_POST contained a '_' instead. WTF?
            $dialogdef[$name] = array(
                'type' => F_ALPHANUMERIC,
                'name' => $name,
                'key' => $k,                           // keep key w/o full_domain around (handy for diff'ing lateron)
                'minlength' => 0,
                'maxlength' => 65432,                  // arbitrary choice
                'columns' => 70,                       // arbitrary choice
                'rows' => $this->guess_row_count($v),
                'label' => sprintf("<strong>%d</strong>: %s",++$i,$this->code_highlight($v)),
                'title' => $k,
                'value' => t($k,$full_domain,'','',$language_key),
                'comment' => (isset($comments[$k])) ? nl2br(htmlspecialchars($comments[$k])) : ''
                );
        }

        // 3 -- add fields for storing metainformation about this translation file
        $submit = '1';
        $full_name = $USER->full_name;
        $email = $USER->email;
        $notes = '';
        $filename = sprintf('%s/languages/%s/%s.php',$CFG->datadir,$language_key,$full_domain);
        if ((db_bool_is(TRUE,$this->languages[$language_key]['dialect_in_database'])) ||
            ((db_bool_is(TRUE,$this->languages[$language_key]['dialect_in_file'])) && (file_exists($filename)))) {
            $full_name = t('_full_name',$full_domain,'','',$language_key);
            $email = t('_email',$full_domain,'','',$language_key);
            $notes = t('_notes',$full_domain,'','',$language_key);
        }
        $dialogdef['_submit'] = array(
            'type' => F_CHECKBOX,
            'name' => '_submit',
            'options' => array(1 => t('translatetool_submit_check','admin')),
            'title' => t('translatetool_submit_title','admin'),
            'comment' => t('translatetool_submit_label','admin'),
            'value' => $submit
            );
        $dialogdef['_full_name'] = array(
            'type' => F_ALPHANUMERIC,
            'name' => '_full_name',
            'minlength' => 1,
            'maxlength' => 255,
            'columns' => 70,
            'label' => t('translatetool_full_name_label','admin'),
            'title' => t('translatetool_full_name_title','admin'),
            'value' => $full_name,
            );
        $dialogdef['_email'] = array(
            'type' => F_ALPHANUMERIC,
            'name' => '_email',
            'minlength' => 0,
            'maxlength' => 255,
            'columns' => 70,
            'label' => t('translatetool_email_label','admin'),
            'title' => t('translatetool_email_title','admin'),
            'value' => $email,
            );
        $dialogdef['_notes'] = array(
            'type' => F_ALPHANUMERIC,
            'name' => '_notes',
            'minlength' => 0,
            'maxlength' => 65432,
            'columns' => 70,
            'rows' => 10,
            'label' => t('translatetool_notes_label','admin'),
            'title' => t('translatetool_notes_title','admin'),
            'value' => $notes,
            );

        // 4 -- always finish with submit buttons
        $dialogdef['button_save'] = dialog_buttondef(BUTTON_SAVE);
        $dialogdef['button_cancel'] = dialog_buttondef(BUTTON_CANCEL);
        return $dialogdef;
    } // get_dialogdef_language_domain()


    /** fetch a list of languages available as parent language
     *
     * this constructs a list of languages that can be used as a list of parent
     * language options in a listbox or radiobuttons.
     *
     * @param string $skip_language_key suppress this language in list (language cannot be its own parent)
     * @return array ready for use as an options array in a listbox or radiobuttons
     */
    function get_options_languages($skip_language_key='') {
        $options['--'] = array('option' => t('translatetool_parent_language_none_option','admin'),
                               'title' => t('translatetool_parent_language_none_title','admin'));
        if (sizeof($this->languages) > 0) {
            foreach($this->languages as $language_key => $language) {
                if ((!empty($skip_language_key)) && ($skip_language_key == $language_key)) {
                    continue;
                }
                $params = array('{LANGUAGE_KEY}' => $language_key, '{LANGUAGE_NAME}' => $language['language_name']);
                $title = t('translatetool_parent_language_option_title','admin',$params);
                $option = t('translatetool_parent_language_option_option','admin',$params);
                $option .= (db_bool_is(TRUE,$language['is_active'])) ? '' : ' ('.t('inactive','admin').')';
                $options[$language_key] = array('option' => $option, 'title' => $title);
            }
        }
        return $options;
    } // get_options_languages()


    /** display the domain menu via $this->output
     *
     * This displays a clickable menu on in the menu area on the left of the screen.
     *
     * @param string $language_key the language currently being edited
     * @param string $current_domain the currently selected language domain (used to emphasize the option in the menu)
     * @return void results are returned as output in $this->output
     */
    function show_domain_menu($language_key,$current_domain='') {
        global $WAS_SCRIPT_NAME;
        $this->show_parent_menu = FALSE; // Make sure parent doesn't add a menu too
        $this->output->add_menu('<h2>'.t('menu','admin').'</h2>');
        $this->output->add_menu('<ul>');
        $grouping = '';
        foreach($this->domains as $name => $domain) {
            if ($grouping != $domain['grouping']) {
                if (!empty($grouping)) {
                    $this->output->add_menu('  </ul>');
                }
                $grouping = $domain['grouping'];
                $this->output->add_menu('  <li>'.t('translatetool_domain_grouping_'.$grouping,'admin'));
                $this->output->add_menu('  <ul>');
            }
            $parameters = $this->a_param(TRANSLATETOOL_CHORE_EDIT,$language_key,$name);
            $attributes = array('title' => $domain['description']);
            if ($current_domain == $name) {
                $attributes['class'] = 'current';
            }
            $this->output->add_menu('    <li>'.html_a($WAS_SCRIPT_NAME,$parameters,$attributes,$domain['title']));
        }
        $this->output->add_menu('  </ul>');
        $this->output->add_menu('</ul>');
    } // show_domain_menu()


    /** construct a clickable icon to edit the properties of this language
     *
     * @param string $language_key
     * @return string ready-to-use A-tag
     * @uses $CFG
     * @uses $USER
     * @uses $WAS_SCRIPT_NAME
     */
    function get_icon_edit($language_key) {
        global $CFG,$WAS_SCRIPT_NAME,$USER;

        // 2 -- construct the icon (image or text)
        $title = t('icon_language_edit','admin');
        $alt = t('icon_language_edit_alt','admin');
        $text = t('icon_language_edit_text','admin');
        $anchor = $this->output->skin->get_icon('edit', $title, $alt, $text);

        // 3 -- construct the A tag
        $a_params = $this->a_param(TRANSLATETOOL_CHORE_LANGUAGE_EDIT,$language_key);
        $a_attr = array('title' => $title);
        return html_a($WAS_SCRIPT_NAME,$a_params,$a_attr,$anchor);
    } // get_icon_edit()


    /** shorthand for the anchor parameters that lead to the translate tool
     *
     * @param string $chore the next chore that could be done
     * @param string|null $language_key the language of interest or NULL if none
     * @param string|null $domain the full domain of interest or NULL if none
     * @return array ready-to-use array with parameters for constructing a-tag
     */
    function a_param($chore,$language_key=NULL,$domain=NULL) {
        $parameters = array(
            'job' => JOB_TOOLS,
            'task' => TASK_TRANSLATETOOL,
            'chore' => $chore);
        if (!is_null($language_key)) { $parameters[TRANSLATETOOL_PARAM_LANGUAGE_KEY] = strval($language_key); }
        if (!is_null($domain))       { $parameters[TRANSLATETOOL_PARAM_DOMAIN]       = strval($domain); }
        return $parameters;
    } // a_param()


    /** return an ordered list of translation domains
     *
     * this constructs a list of language domains, grouped by
     * 'program','modules','themes' or 'install'. This array is the basis
     * for validating full domains (in $_POST'ed data) and also to construct
     * a menu.
     *
     * Note that we use the translations from the files themselves in the
     * current language to construct this list. Every translatefile should have
     * at least the string 'translatetool_title' and 'translatetool_description'.
     * Currently the sort order is based on the (internal) name of the modules.
     * This should do the trick for translators: the order of files to translate
     * in the menu does not depend on the translation of the module- or theme-title.
     * (In the page manager and elsewhere it may be different).
     *
     * @return array contains list of displayable titles and descriptions, keyed by full_domain
     */
    function get_domains() {
        global $CFG;
        $domains = array();

        // 1 -- Straightforward list of files for the core program itself
        $domains['was'] = array(
            'grouping'    => 'program',
            'title'       => t('translatetool_title','was'),
            'description' => t('translatetool_description','was')
            );
        $domains['loginlib'] = array(
            'grouping'    => 'program',
            'title'       => t('translatetool_title','loginlib'),
            'description' => t('translatetool_description','loginlib')
            );
        $domains['admin'] = array(
            'grouping'    => 'program',
            'title'       => t('translatetool_title','admin'),
            'description' => t('translatetool_description','admin')
            );

        // 2 -- A tricky list of modules and themes which re-uses the tablename as grouping parameter
        $where = '';
        $order = 'name';
        $field = 'name';
        foreach(array('modules' => 'm_','themes' => 't_') as $table => $prefix) {
            if (($records = db_select_all_records($table,$field,$where,$order,$field)) === FALSE) {
                continue;
            }
            foreach($records as $name => $record) {
                $domains[$prefix.$name] = array(
                    'grouping'    => $table,
                    'title'       => t('translatetool_title',$prefix.$name),
                    'description' => t('translatetool_description',$prefix.$name)
                    );
            }
        }
        // 3 -- Straightforward list of installation translations (located elsewhere in the /program directory tree)
        $domains['i_install'] = array(
            'grouping'    => 'install',
            'title'       => t('translatetool_title','install','',$CFG->progdir.'/install/languages'),
            'description' => t('translatetool_description','install','',$CFG->progdir.'/install/languages')
            );
        $domains['i_demodata'] = array(
            'grouping'    => 'install',
            'title'       => t('translatetool_title','demodata','',$CFG->progdir.'/install/languages'),
            'description' => t('translatetool_description','demodata','',$CFG->progdir.'/install/languages')
            );
        return $domains;
    } // get_domains()


    /** try to calculate a reasonable number of textarea rows based on the contents of $text
     *
     * By using a reference we prevent the endless coping of (long) strings to the stack;
     * this should save time & space.
     *
     * @param string &$text the string to analyse
     * @param int $maximum the maximum value this routine returns
     * @return int a positive number indicating the # or rows or $maximum (default maximum = 15)
     */
    function guess_row_count(&$text,$maximum=15) {
        $rows = 1;
        if (($index = strpos($text,"\n")) !== FALSE) {
            ++$rows; ++$rows;
            while ((($index = strpos($text,"\n",++$index)) !== FALSE) && ($rows < $maximum)) {
                ++$rows;
            }
        }
        return $rows;
    } // guess_row_count()


    /** hightlight code constructs in texts that are to be translated
     *
     * this routine highlights the following code constructs:
     *
     *  - HTML-tages such as '<strong>' and '<em>'
     *  - Variables such as '{USERNAME}' and '{FILE}'
     *  - Tildes in hotkeys such as  '~Yes' and '~No'
     *
     * All of these code elements are sandwiched between $highlight_on
     * and $highlight_off.
     * The HTML-tags are escaped using htmlspecialchars making it possible 
     * to actually display them as text (otherwise they might
     * be rendered as actual code in the browser). The HTML-codes '<br>' and '<p>' receive
     * special treatment: they are rendered as visible text and also as a newline.
     *
     * Note:
     * This assumes that all '{' are eventually followed by a '}'. As long as this is true,
     * we can easily use a str_replace() to sandwich {VARIABLE} between highlights.
     * If there is only a single '{' or '}' the highlights won't match.
     * It _could_ be a problem and if it is, the relevant code should iterate / chomp
     * chomp through the string with something like ereg('({[a-zA-Z0-9_]+})',$string,$regs)
     *
     * As an added bonus, sequences of two consecutive spaces are replaced with non-breakable
     * spaces. This is handy for phrases that use spaces to indent text, e.g. in simple
     * text-only email messages.
     *
     * By using a reference we prevent the endless coping of (long) strings to the stack;
     * this should save time & space.
     *
     * @param string &$source the string that needs code highlighting
     * @param string $highlight_on is inserted before the code element that is highlighted
     * @param string $highlight_off is inserted after the code element that is highlighted
     * @return string the string with highlighted code elements and escaped HTML-tags
     * @todo should we turn to ereg() instead of a simple str_replace() for {VARIABLE} highlighting?
     */
    function code_highlight(&$source,$highlight_on='<span class="translatetool_code">',$highlight_off='</span>') {

        // 1 -- HTML-tags linke <strong> and <em>
        $target = '';
        $offset = 0;
        while (($i = strpos($source,'<',$offset)) !== FALSE) {
            $target .= substr($source,$offset,$i-$offset); // copy source upto the next opening bracket '<'
            if (($j = strpos($source,'>',$i)) === FALSE) {
                $tag = substr($source,$i); // no closing '>' so use remainder of source as tag (shouldn't happen)
                $offset = strlen($source); // start next iteration at end of string (i.e. end the while loop)
            } else {
                $offset = ++$j; // start next iteration past the closing '>'
                $tag = substr($source,$i,$j-$i); // $j - $i is now the length of the tag from '<' upto & including '>'
            }
            $target .= $highlight_on.htmlspecialchars($tag).$highlight_off;
            // aid visibility by adding a new line for (variations of) <br> and <p>: this yields <br> after nl2br() below
            if (eregi("<br[>\n ]|<p[>\n ]",$tag)) {
                $target .= "\n";
            }
        }
        $target .= substr($source,$offset); // copy remainder of the string after the last '>'

        // 2 -- Highlight variables like {USERNAME} and {MAX_FILE_SIZE} and also tildes (hotkeys)
        $search = array('{','}','~','  ');
        $replace = array($highlight_on.'{', '}'.$highlight_off,$highlight_on.'~'.$highlight_off,'&nbsp;&nbsp;');
        return nl2br(str_replace($search,$replace,$target));
    } // code_highlight()


    /** retrieve strings (translations) and comments from an official (system) translation file
     *
     * This routine reads the system translations for $language_key and $full_domain from
     * a file. For the translations of the main program we look for a single file in
     * $CFG->progdir/languages/$language_key/. For modules, themes and addons
     * we try two different locations: one within the moduel/theme/addon directory tree and
     * subsequently in the generic directory $CFG->progdir/languages/$language_key/.
     *
     * The names of modules/themes/addons are derived by stripping the 2-character
     * prefix (m_, t_ or a_) from the full domain.
     *
     * Translations for the installer are searched for in the /program/install/languages tree.
     *
     * @param string $languagekey the two or three letter ISO 639 language code
     * @param string $full_domein the language domain of interest
     * @param array &$string receives the translations (this parameter must be called 'string')
     * @param array &$comment receives the comments (this parameter must be called 'comment')
     * @return void results are stored in arrays &$string and &$comment
     */
    function get_strings_system($language_key,$full_domain,&$string,&$comment) {
        global $CFG;
        $string = array();
        $comment = array();
        $prefix = substr($full_domain,0,2);
        $domain = substr($full_domain,2);
        switch($prefix) {
        case 'm_':
            $filenames = array($CFG->progdir.'/modules/'.$domain.'/languages/'.$language_key.'/'.$domain.'.php',
                               $CFG->progdir.'/languages/'.$language_key.'/'.$full_domain.'.php');
            break;
        case 't_':
            $filenames = array($CFG->progdir.'/themes/'.$domain.'/languages/'.$language_key.'/'.$domain.'.php',
                               $CFG->progdir.'/languages/'.$language_key.'/'.$full_domain.'.php');
            break;
        case 'a_':
            $filenames = array($CFG->progdir.'/addons/'.$domain.'/languages/'.$language_key.'/'.$domain.'.php',
                               $CFG->progdir.'/languages/'.$language_key.'/'.$full_domain.'.php');
            break;
        case 'i_':
            $filenames = array($CFG->progdir.'/install/languages/'.$language_key.'/'.$domain.'.php',
                               $CFG->progdir.'/languages/'.$language_key.'/'.$full_domain.'.php');
            break;
        default:
            $filenames = array($CFG->progdir.'/languages/'.$language_key.'/'.$full_domain.'.php');
            break;
        }
        foreach($filenames as $filename) {
            if (file_exists($filename)) {
                include($filename);
            }
        }
    } // get_strings_system()


    /** render a translation dialog based on a dialog definition
     *
     * This routine looks a bit like the generic {@link dialog_quickform()}.
     * The differences are:
     *  - we show a comment (if any) in a box before label and input
     *  - the labels don't have hotkeys based on tildes at all (except the submit buttons)
     *  - comments and labels are wrapped in separate div's especially for the occasion
     *
     * We do take any errors into account: fields with errors are displayed using the
     * additional error class (which shows a label completely in red to indicate the error).
     *
     * @param string $href the target of the HTML form
     * @param array &$dialogdef the array which describes the complete dialog
     * @param string $method method to submit data to the server, either 'post' or 'get'
     * @param string|array $attributes holds the attributes to add to the form tag
     * @return array constructed HTML-form with dialog, one line per array element
     * @uses html_form()
     */
    function render_translation_dialog($href,&$dialogdef,$method='post',$attributes='') {
        $buttons_seen = FALSE;
        $a = array(0 => html_form($href,$method,$attributes)); // result starts with opening a form tag
        foreach($dialogdef as $item) {
            if (!isset($item['name'])) { // skip spurious item (possibly empty array)
                continue;
            }
            if ((isset($item['comment'])) && (!empty($item['comment']))) {
                $a[] = '<div class="translatetool_comment">';
                $a[] = $item['comment'];
                $a[] = '</div>';
            }
            if ((isset($item['label'])) && (!empty($item['label']))) {
                $errorclass = ((isset($item['errors'])) && ($item['errors'] > 0)) ? ' error' : '';
                $a[] = sprintf('<div class="translatetool_label%s">',$errorclass);
                $a[] = $item['label'];
                $a[] = '</div>';
            }
            $widget = dialog_get_widget($item);
            if (is_array($widget)) {
                // add every radio button on a separate line
                $postfix = ($item['type'] == F_RADIO) ? '<br>' : '';
                foreach ($widget as $widget_line) {
                    $a[] = $widget_line.$postfix;
                }
            } else {
                // quick and dirty:
                // add a <p> before the first button in a dialog
                // add a <p> after every regular input item except buttons and hidden fields
                // result: fields line up nicely and buttons are on a single row
                $postfix = '';
                if ($item['type'] == F_SUBMIT) {
                    if (!$buttons_seen) {
                        $buttons_seen = TRUE;
                        $a[] = '<p>';
                    }
                } elseif (!((isset($item['hidden'])) && ($item['hidden']))) {
                    $postfix = '<p>';
                }
                $a[] = $widget.$postfix;
            }
        }
        $a[] = '<p>';
        $a[] = html_form_close();
        return $a;
    } // render_translation_dialog()


    /** save new or changed translations to a file under CFG->datadir/languages
     *
     * @param string $language_key identifies the language to save
     * @param string $full_domain indicates which language domain needs to be saved
     * @param array &$diff contains all key-value-pairs for the modified translation
     * @return bool TRUE on success, FALSE on failure
     */
    function put_strings_userfile($language_key,$full_domain,&$diff) {
        global $CFG;
        $text = '';
        $this->diff_to_text($language_key,$full_domain,$diff,$text);

        $filepath = sprintf('%s/languages/%s/%s.php',$CFG->datadir,$language_key,$full_domain);
        if (($fp = @fopen($filepath,"w")) === FALSE) {
            logger(sprintf('%s.%s(): cannot fopen() file \'%s\'',__CLASS__,__FUNCTION__,$filepath));
            return FALSE;
        }
        $bytes_to_write = strlen($text);
        if (($bytes_written = @fwrite($fp,$text,$bytes_to_write)) === FALSE) {
            logger(sprintf('%s.%s(): cannot fwrite() file \'%s\'',__CLASS__,__FUNCTION__,$filepath));
            @fclose($fp);
            return FALSE;
        } elseif ($bytes_written != $bytes_to_write) {
            logger(sprintf('%s.%s(): only %d of %d bytes fwritten to file \'%s\'',__CLASS__,__FUNCTION__,
                                     $bytes_written,$bytes_to_write,$filepath));
            @fclose($fp);
            return FALSE;
        }
        if (!@fclose($fp)) {
            logger(sprintf('%s.%s(): cannot fclose() file \'%s\'',__CLASS__,__FUNCTION__,$filepath));
            return FALSE;
        }
        if (!@chmod($filepath,0600)) {
            logger(sprintf('%s.%s(): chmod(%s) to 0600 failed but trying to continue nevertheless',
                            __CLASS__,__FUNCTION__,$filepath));
        }
        logger(sprintf('%s.%s(): %d bytes written to \'%s\'',__CLASS__,__FUNCTION__,$bytes_written,$filepath),WLOG_DEBUG);
        return TRUE;
    } // put_strings_userfile()


    /** convert an array with key-value-pairs to a php source file that can be included as a user translation
     *
     * All key-value-pairs are converted to something like this:
     * <code>
     * ...
     * $string['key'] = 'value';
     * ...
     * </code>
     *
     * We specifially use single quotes in order to prevent any variable expansion
     * within the strings. We do escape embedded single quotes, naturally.
     * Furtermore, some metadata is added to the top of the resulting file,
     * including information about the creation time, the program version that
     * was used, the version of the (English) source file on which this translation
     * is based and finally information about the file version of the system strings
     * which was used to diff against.
     *
     * Note: If the file with these system strings do not exist (because
     * the language is all new, indicated by a version 'v0', _all_ strings are stored in
     * the diff and thus in the user file. That file could be used as a new starting
     * point for the new language in a next version of the program.
     *
     * Note: we try very hard to defeat tricks with the contents of the metadata
     * (i.e. we don't trust _full_name and _email to not contain tricks like '*' followed
     * by '/' (which would prematurely end the comment in the header) etc.
     *
     * @param string $language_key identifies the language
     * @param string $full_domain indicates the language domain
     * @param array &$diff contains all key-value-pairs for the modified translation
     * @param string &$text receives the complete sourcefile created from $diff
     * @return bool TRUE on success, FALSE on failure
     */
    function diff_to_text($language_key,$full_domain,&$diff,&$text) {
        global $CFG;
        $search           = array("\n","\r","*/","<?","?>");
        $replace          = array(" ", " ", " ", " ", " " );
        $author           = str_replace($search,$replace,$diff['_full_name']);
        $email            = str_replace($search,$replace,$diff['_email']);
        $english_version  = str_replace($search,$replace,strval($this->languages['en']['version']));
        $english_name     = str_replace($search,$replace,strval($this->languages['en']['language_name']));
        $language_version = str_replace($search,$replace,strval($this->languages[$language_key]['version']));
        $language_name    = str_replace($search,$replace,strval($this->languages[$language_key]['language_name']));
        $program_version  = str_replace($search,$replace,strval($CFG->version));
        $program_release  = str_replace($search,$replace,strval(WAS_RELEASE));
        $filename         = sprintf('/languages/%s/%s.php',$language_key,$full_domain);
        $year             = strftime('%Y');
        $now              = strftime("%Y-%m-%d %T");

        $text = '<'.'?'."php\n";
        $text .= <<<EOT
/** $filename
 *
 * @author {$author} <{$email}>
 * @copyright Copyright (C) {$year} {$author}
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_{$language_key}
 *
 * created: {$now}
 * program: {$program_release} / {$program_version}
 * base: en v{$english_version} ({$english_name})
 * diff: {$language_key} v{$language_version} ({$language_name})
 */
if (!defined('WASENTRY')) { die('no entry'); }

EOT;
        foreach($diff as $key => $value) {
            $text .= sprintf("\$string['%s'] = '%s';\n",
                str_replace('\'','\\\'',$key),
                str_replace('\'','\\\'',$value));
        }
        $text .= '?'.'>';
    } // diff_to_text()


    /** send new or changed translations back to the project
     *
     * This sends an e-mail back to the project with the translation.
     * We do so in the form of an attachment, but with a 'safe' extension
     * (.bin rather than .php). This means that we will be able to traverse
     * any firewalls and spamfilters and malware detectors.
     *
     * The _notes are used as the body of the message, the file is attached.
     *
     * Note that we send a copy of the message to the site itself (either
     * the from-addres or the reply-to-address).
     *
     * @param string $language_key identifies the language to submit
     * @param string $full_domain indicates which language domain needs to be submitted
     * @param array &$diff contains all key-value-pairs for the modified translation
     * @return bool TRUE on success, FALSE on failure
     */
    function submit_diff_to_project($language_key,$full_domain,&$diff) {
        global $CFG;

        /** make sure utility routines for creating/sending email messages are available */
        require_once($CFG->progdir.'/lib/email.class.php');

        $language_name = $this->languages[$language_key]['language_name'];
        $email = new Email;

        $mailto = 'translations@websiteatschool.eu';
        $email->set_mailto($mailto,'Website@School Translations');

        $subject = sprintf('Website@School Translation: %s (%s) - %s',$language_name,$language_key,$full_domain);
        $email->set_subject($subject);

        $name = trim($diff['_full_name']); // maybe add name of translator to human readable From: and Cc: header
        $name = (empty($name)) ? $CFG->title : $CFG->title.' - '.$name;
        $email->set_mailfrom($CFG->website_from_address,$name);

        $addrcc = (empty($CFG->website_replyto_address)) ? $CFG->website_from_address : $CFG->website_replyto_address;
        $email->add_mailcc($addrcc,$name);

        $message = sprintf("Language name:   %s\n".
                           "Language key:    %s\n".
                           "Language domain: %s\n\n",$language_name,$language_key,$full_domain).
                           wordwrap(str_replace(array("\r\n","\r","\n"),"\n",trim($diff['_notes'])),70,"\n",TRUE);
        $email->set_message($message);

        $attachment = '';
        $this->diff_to_text($language_key,$full_domain,$diff,$attachment);
        $attachment_name = sprintf('%s-%s.bin',$language_key,$full_domain);
        $email->add_attachment($attachment,$attachment_name);

        if ($retval = $email->send()) { // success, mail was accepted for delivery
            logger(sprintf('%s.%s(): success sending \'%s\' to <%s>',__CLASS__,__FUNCTION__,$subject,$mailto),WLOG_DEBUG);
        } else {
            logger(sprintf('%s.%s(): failure sending \'%s\' to <%s>',__CLASS__,__FUNCTION__,$subject,$mailto));
        }
        return $retval;
    } // submit_diff_to_project()

} // TranslateTool

?>