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

/** /program/lib/language.class.php - taking care of translations of messages
 *
 * This file defines a class for dealing with translation of phrases.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: language.class.php,v 1.4 2013/06/11 11:26:05 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** Translations of messages in different languages
 *
 *
 */
class Language {
    /** @var array $phrases a cache of translated phrases */
    var $phrases = array();

    /** @var array $languages a cached list of all language records */
    var $languages = array();

    /** @var string $default_domain the text domain to use if none is specified */
    var $default_domain;

    /** constructor
     *
     * Set up the instance of this class. If no default domain is specified, 'was' is used.
     * We always read the current list of all languages into core, for future reference.
     *
     * @param string $default_domain used when no domain is specified when requesting a translation
     * @return void
     */
    function Language($default_domain='') {
        $this->retrieve_languages();
        $this->phrases = array();
        $this->default_domain = (empty($default_domain)) ? 'was' : $default_domain;
    } // Language()


    /** retrieve an array with all active languages from the database
     *
     * This reads all languages from the database. If there's nothing
     * there, we still return an array with a single element for the English
     * language 'en', because 'en' is the native language of this program.
     * If the language 'en' was not found, we still add it to the array.
     * The resulting array is usually sorted by language name.
     *
     * @param bool $force_reread if TRUE we always go to the database, else we try the cached version first
     * @return array all languages from database, including at least 'en'
     */
    function retrieve_languages($force_reread=FALSE) {
        if ((empty($this->languages)) || ($force_reread)) {
            $english = array(
                'language_key'        => 'en',
                'language_name'       => 'English',
                'parent_language_key' => NULL,
                'version'             => 0,
                'manifest'            => '',
                'is_core'             => SQL_TRUE,
                'is_active'           => SQL_TRUE,
                'dialect_in_database' => SQL_FALSE,
                'dialect_in_file'     => SQL_FALSE
                );
            $table     = 'languages';
            $fields    = '*';
            $where     = '';
            $order     = 'language_name';
            $keyfield  = 'language_key';
            $this->languages = db_select_all_records($table,$fields,$where,$order,$keyfield);
            if ($this->languages === FALSE) {
                $this->languages = array('en' => $english);
            } elseif (!isset($this->languages['en'])) {
                $this->languages['en'] = $english;
            }
            $this->languages['en']['is_core'] = SQL_TRUE; // just to be sure noone is playing games.
            $this->languages['en']['is_active'] = SQL_TRUE; // There is at least 1 active language now.
        }
        return $this->languages;
    } // retrieve_languages()


    /** return an array with active languages and language names
     *
     * this returns an array with language_key => language_name pairs,
     * one entry per active language, ordered by language name. This array can be
     * used in language picklists or to translate a language key to readable form.
     * Note that we use the name of a language expressed in the language itself.
     *
     * @return array language_keys and names
     */
    function get_active_language_names() {
        $names = array();
        foreach($this->languages as $language_key => $language) {
            if (db_bool_is(TRUE,$language['is_active'])) {
                $names[$language_key] = $language['language_name'];
            }
        }
        asort($names);
        return $names;
    } // get_active_language_names()


    /** determine the default language to use for translation of phrases
     *
     * This routine determines which language to use for prompts
     * and messages if not specified explicitly in calls to $this->get_phrase().
     * There are various ways in which a language can
     * be determined. Here's the list, in order of significance:
     *
     *  - $_GET['language']
     *  - $_SESSION['language_key']
     *  - $USER->language_key
     *  - $CFG->language_key
     *  - constant value 'en' (the native language)
     *
     * Note that all languages are validated agains the list of
     * valid and active languages as collected in $this->languages.
     * If a language is NOT valid, the next test is tried.
     * If all else fails we return 'en' for English, which
     * is the native language and which should always be valid.
     *
     * @uses $CFG
     * @uses $USER;
     * @return string a valid language code for an active language
     */
    function get_current_language() {
        global $CFG;
        global $USER;
        if (isset($_GET['language'])) {
            $lang = magic_unquote($_GET['language']);
            if ((isset($this->languages[$lang])) && (db_bool_is(TRUE,$this->languages[$lang]['is_active']))) {
                return $lang;
            }
        }
        if (isset($_SESSION['language_key'])) {
            $lang = $_SESSION['language_key'];
            if ((isset($this->languages[$lang])) && (db_bool_is(TRUE,$this->languages[$lang]['is_active']))) {
                return $lang;
            }
        }
        if (isset($USER->language_key)) {
            $lang = $USER->language_key;
            if ((isset($this->languages[$lang])) && (db_bool_is(TRUE,$this->languages[$lang]['is_active']))) {
                return $lang;
            }
        }
        if (isset($CFG->language_key)) {
            $lang = $CFG->language_key;
            if ((isset($this->languages[$lang])) && (db_bool_is(TRUE,$this->languages[$lang]['is_active']))) {
                return $lang;
            }
        }
        return 'en'; // if all else fails, return the 'native' language
    } // get_current_language()


    /** return the $string array after including a file
     *
     * This includes the specified language file $filename (if it
     * exists) and returns the array $string. This assumes
     * that filename actually consists of lines like this:
     *
     * <code>
     * ...
     * $string['key_of_a_phrase'] = 'content of this phrase';
     * $string['key_with_variable'] = 'Hello, {USERNAME}.';
     * ...
     * </code>
     * 
     * Because the file is included within the context of this function,
     * the contents are added to the local array $string rather than
     * some global array. This is a feature.
     *
     * Note that the included file MUST name the array '$string'
     * because otherwise this function will return an empty array.
     * This means that any 'old' Site@School language files must be
     * manipulated before they can be re-used. I'd consider this a
     * feature too.
     *
     * @param string $filename which language file to include
     * @return array all phrases from the specified file or an empty array
     */
    function get_phrases_from_file($filename) {
        $string = array();
        if (file_exists($filename)) {
            include($filename);
        }
        return $string;
    } // get_phrases_from_file()


    /** retrieve phrases from the database for specified domain and language
     *
     * @param string $full_domain text domain to look for
     * @param string $language_key the language to look for
     * @return array associative array with phrase_keys and translations
     */
    function get_phrases_from_database($full_domain,$language_key) {
        $phrases = array();
        $table = 'phrases';
        $fields = array('phrase_key','phrase_text');
        $where = array('language_key' => $language_key, 'domain' => $full_domain);
        $records = db_select_all_records($table,$fields,$where);
        if ($records !== FALSE) {
            foreach($records as $record) {
                $phrases[$record['phrase_key']] = $record['phrase_text'];
            }
        }
        unset($records);
        return $phrases;
    } // get_phrases_from_database()


    /** translation of phrases via a phrase key
     *
     * This routine looks up the text associated with the phrase key.
     * If no domain is specified, the domain 'was' is tried. If no
     * valid language is specified, the current language is used.
     * If a location hint is specified, we trust the caller knows
     * best where to look and we try locating a translations file
     * in that directory location first.
     *
     * Note that phrases in a particular language which are found later 
     * in the search overwrite the phrases found earlier. These additional
     * phrases (dubbed 'dialect' or 'userdefined translations') can be
     * used to overwrite or correct existing standard ('official') translations.
     * These dialect phrases can be stored in a file in the languages subdirectory
     * of the data directory (writable for the web server but hopefully 
     * outside the document root) and/or in the table 'phrases' in the 
     * database.
     *
     * Whether these dialect phrases are actually fetched from disk or database
     * depends on the configuration of the language, via the boolean fields
     * 'dialect_in_database' and 'dialect_in_file'; if the corresponding switch
     * is not TRUE, we don't even bother to go and look, which saves time.
     *
     * Finally, if a particular phrase is not found in the requested language,
     * we recursively try the parent language(s) of the requested language until
     * there are no more parents. After that, we go for the 'en' translation.
     * If that fails too, we return the phrase_key itself, sandwiched between
     * the strings '(lang) ' and ' (/lang)', where 'lang' is the requested language
     * code. Of course this should not happen if all translations are correct.
     * (Famous last words...)
     *
     * If a translation is found, we replace all occurences of the keys in the
     * array 'replace' in the translation with the corresponding values. This is done
     * via a simple search and replace rather than a printf()-like way or
     * (shudder) with complicated regex'es.
     *
     * Note that we store search results in the array $this->phrases so we can re-use
     * those phrases in a next call. We cache the results on a per-domain basis,
     * based on the assumption that after the first phrase in a particular domain
     * is requested, it is likely that more phrases in the same domain will be requested.
     *
     * Note that the resulting phrases are cached using the original language
     * as the key (in $this->phrases). This means that if a phrase in say 'de' or 'fr'
     * was not found and 'en' was used instead, the English phrases are cached
     * in the 'de' or 'fr' branch of the static array. This saves us time on the
     * next call because we then use the phrases in the substitute language right
     * away instead of going to look everywhere everytime.
     *
     * Translations are fetched in such a way that the user-defined translations
     * ('dialect') prevail over the system-defined ('official') translations. However,
     * attempts to look for a phrase in a parent language (or 'en') only add the
     * missing translations, preserving the translations in this full_domain that
     * were already found. Quick illustration with Dutch (nl) and English (en):
     * search order is: $nl_database, $nl_userfile, $nl_system, $en_database,
     * $en_userfile, $en_system. The 'en' translations are only used if no
     * corresponding Dutch translation is found. However, the English 'dialect'
     * prevails over the English 'system' translation.
     *
     * Examples of typical use of this routine:
     *
     * <code>
     * echo $LANGUAGE->get_phrase('username');
     * </code>
     * display the phrase from 'was.php' in the current language
     *
     * <code>
     * echo $LANGUAGE->get_phrase('username','login');
     * </code>
     * display the phrase from 'login.php' in the current language
     *
     * <code>
     * echo $LANGUAGE->get_phrase('welcome','',array('{USERNAME}' => $USER->username));
     * </code>
     * display the phrase from was.php in the default language,
     * substituting the variable '{USERNAME}'.
     *
     * @param string $phrase_key indicates the phrase that needs to be translated
     * @param string $full_domain (optional) indicates the text domain (perhaps with a prefix)
     * @param array $replace (optional) an assoc array with key-value-pairs to insert into the translation
     * @param string $location_hint (optional) hints at a directory location of language files
     * @param string $language_key (optional) target language
     * @return string translated string with optional values from array 'replace' inserted
     * @uses $LANGUAGES
     * @todo should we return an error for an invalid specific language?
     */
    function get_phrase($phrase_key,$full_domain='',$replace='',$location_hint='',$language_key='') {
        //
        // Step 0 - setup default values where necessary
        //
        if (empty($language_key)) {
            // no specific language requested, use the current language
            $language_key = $this->get_current_language();
        } elseif (!isset($this->languages[$language_key])) {
            // specific language is not valid, silently use the current language instead
            $language_key = $this->get_current_language();
        }
        if (empty($full_domain)) {
            $full_domain = $this->default_domain;
        }

        //
        // Step 1 - try to satisfy request via our own static cache
        //
        if (isset($this->phrases[$language_key][$full_domain][$phrase_key])) {
            $phrase = $this->phrases[$language_key][$full_domain][$phrase_key];
            return (empty($replace)) ? $phrase : strtr($phrase,$replace);
        }

        //
        // Step 2 - cache miss in step 1, setup a list of languages to try
        //
        $languages = $this->get_languages_to_try($language_key);

        //
        // Step 3 - make sure that the language subarray already exists
        //
        if (!isset($this->phrases[$language_key])) {
            $this->phrases[$language_key] = array($full_domain => array());
        } elseif (!isset($this->phrases[$language_key][$full_domain])) {
            $this->phrases[$language_key][$full_domain] = array();
        } // else { this array already exists, good }

        //
        // Step 4 - loop through the list of languages searching for a translation
        //
        foreach ($languages as $language) {
            $phrases = array();
            $filenames = $this->get_filenames_to_try($full_domain,$location_hint,$language);
            foreach($filenames as $filename) { 
                $phrases = $this->get_phrases_from_file($filename) + $phrases;
            }
            if (db_bool_is(TRUE,$this->languages[$language]['dialect_in_database'])) {
                $phrases = $this->get_phrases_from_database($full_domain,$language) + $phrases;
            }
            $this->phrases[$language_key][$full_domain] += $phrases;
            if (isset($this->phrases[$language_key][$full_domain][$phrase_key])) {
                $phrase = $this->phrases[$language_key][$full_domain][$phrase_key];
                return (empty($replace)) ? $phrase : strtr($phrase,$replace);
            }
        }
        //
        // Step 5 - no joy with a real language, return the key itself as last resort
        //
        $s = "({$language_key}) ".$phrase_key;
        if (is_array($replace)) {
            foreach($replace as $k => $v) {
                $s .= "\n'$k'='$v'";
            }
        }
        $s .= " (/{$language_key})";
        $this->phrases[$language_key][$full_domain][$phrase_key] = $s;
        return $s;
    } // get_phrase()


    /** calculate a list of possible languages and parent-languages to try for translations
     *
     * This constructs an array with all ancestors (=parent languages) of $language_key
     * and English if that language was not yet added.
     *
     * @param string $language_key language of which to find all parents
     * @return array ordered list of language, all parent languages and English
     */
    function get_languages_to_try($language_key) {
        $a = array($language_key);
        $parent_key = $this->languages[$language_key]['parent_language_key'];
        while ((!empty($parent_key)) && (isset($this->languages[$parent_key])) && (!in_array($parent_key,$a))) {
            $a[] = $parent_key;
            $parent_key = $this->languages[$parent_key]['parent_language_key'];
        }
        if (!in_array('en',$a)) {
            $a[] = 'en';
        }
        return $a;
    } // get_languages_to_try()


    /** calculate an ordered list of filenames to try for translation of phrases
     *
     * WAS uses a separate language file for every text domain;
     * basically the name of the text domain is the name of the
     * file without the .php-extension. However, in order to prevent
     * name clashes, modules and themes and addons have their own 
     * prefix: 'm_' for modules and 't_' for themes and 'a_' for
     * addons.
     *
     * The language translations for the installer are based on more or
     * less the same trick: the prefix 'i_' identifies files in the
     * directory /program/install/languages.
     *
     * This trick with prefixing leads to the following search orders
     * for generic phrases and module-, theme- and addon-specific phrases.
     *
     * Example 1: phrases with $domain='login':
     * {$CFG->progdir}/languages/{$language_key}/login.php
     * {$CFG->datadir}/languages/{$language_key}/login.php
     *
     * Example 2: phrases with $domain='m_guestbook':
     * {$CFG->progdir}/modules/guestbook/languages/{$language_key}/guestbook.php
     * {$CFG->progdir}/languages/{$language_key}/m_guestbook.php
     * {$CFG->datadir}/languages/{$language_key}/m_guestbook.php
     *
     * Example 3: phrases with $domain='login' and a hint in $location_hint:
     * {$location_hint}/{$language_key}/login.php
     * {$CFG->datadir}/languages/{$language_key}/login.php
     *
     * Example 4: phrases with $domain='m_guestbook' and a hint in $location_hint:
     * {$location_hint}/{$language_key}/guestbook.php
     * {$location_hint}/{$language_key}/m_guestbook.php
     * {$CFG->datadir}/languages/{$language_key}/m_guestbook.php
     *
     * Example 5: phrases with $domain='i_demodata':
     * {$CFG->progdir}/install/languages/{$language_key}/demodata.php
     * {$CFG->datadir}/languages/{$language_key}/i_demodata.php
     *
     * @param string $full_domain indicates the text domain including optional module/theme/addon prefix
     * @param string $location_hint hints at a location of language file(s)
     * @param string $language_key target language
     * @return array an ordered list of filenames
     * @uses $CFG
     */
    function get_filenames_to_try($full_domain,$location_hint,$language_key) {
        global $CFG;
        static $extensions = array('m_' => 'modules', 't_' => 'themes', 'a_' => 'addons');

        $filenames = array();

        // Minimal validation of location hint: this directory should at least exist
        $directory = ((!empty($location_hint)) && (is_dir($location_hint))) ? $location_hint : '';

        // Modules/themes/addons have a 2-char prefix in their full_domain.
        // If this is the case, we start with a language path as close to the
        // module/theme/addon as possible _or_ in the hint location.
        $prefix = substr($full_domain,0,2);
        $domain = substr($full_domain,2);
        if (isset($extensions[$prefix])) {
            $extension = $extensions[$prefix];
            if (empty($directory)) {
                $filenames[] = $CFG->progdir.'/'.$extension.'/'.$domain.'/languages/'.$language_key.'/'.$domain.'.php';
            } else {
                $filenames[] = $directory.'/'.$language_key.'/'.$domain.'.php';
            }
        } elseif ($prefix == 'i_') {
            if (empty($directory)) {
                $filenames[] = $CFG->progdir.'/install/languages/'.$language_key.'/'.$domain.'.php';
            } else {
                $filenames[] = $directory.'/'.$language_key.'/'.$domain.'.php';
            }
        }
        if (empty($directory)) {
            $filenames[] = $CFG->progdir.'/languages/'.$language_key.'/'.$full_domain.'.php';
        } else {
            $filenames[] = $directory.'/'.$language_key.'/'.$full_domain.'.php';
        }

        // If this language has the flag 'dialect_in_file' set, we try $CFG->datadir/languages too
        if (db_bool_is(TRUE,$this->languages[$language_key]['dialect_in_file'])) {
            $filenames[] = $CFG->datadir.'/languages/'.$language_key.'/'.$full_domain.'.php';
        }
        return $filenames;
    } // get_filenames_to_try()

    /** remove selected entries (per language+domain, per language, or all) from cache
     *
     * @param string $language_key the language
     * @param string $full_domain the language domain
     * @return void selected parts of cache reset
     */
    function reset_cache($language_key='',$full_domain='') {
        if (((!empty($language_key))) && (isset($this->phrases[$language_key]))) {
            if (((!empty($full_domain))) && (isset($this->phrases[$language_key][$full_domain]))) {
                $this->phrases[$language_key][$full_domain] = array();
            } else {
                $this->phrases[$language_key] = array();
            }
        } else {
            $this->phrases = array();
        }
    } // reset_cache()

} // Language

?>