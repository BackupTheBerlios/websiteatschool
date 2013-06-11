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

/** /program/lib/configassistant.class.php - dealing with lists of configuration parameters
 *
 * This file defines a class for dealing (edit+save but not create or delete) with
 * lists of configuration parameters. The main purpose is to allow easy editing of
 * configuration of parts of the system, including the main program configuration.
 * 
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: configassistant.class.php,v 1.7 2013/06/11 11:26:05 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/**  class for editing standard configuration tables
 *
 * Overview<br>
 * --------
 * 
 * A configuration table works like this: every parameter (property,
 * configuration item) is stored in a record in the configuration
 * table. The core of a configuration table consists of these fields:
 * <pre>
 * 
 *  - name varchar(240): this is the name of the configuration parameter
 *  - type varchar(2): parameter type: b=bool, c=checklist, d=date, dt=date/time,
 *    f=float(double), i=int, l=list, r=radio, s=string, t=time (see below for
 *    more information)
 *  - value text: string representation of parameter value OR a comma-delimited
 *    list of values in case of a checklist
 *  - extra text: a semicolon-delimited list of name=value pairs with additional
 *    dialog/validation information, e.g. maxlength=80 or
 *    options=true,false,filenotfound (see below for more information)
 *  - sort_order integer: this determines the order in which parameters are
 *    presented when editing the configuration
 *  - description text: an optional short explanation of the purpose of this
 *    parameter (in English, for internal use only)
 * 
 * </pre>
 * 
 * There can be additional fields, e.g. links to parent tables in a 1-on-N relation,
 * e.g. themes and themes_properties via theme_id. Also, the configuration table can
 * have a separate primary key to uniquely identify a record but this is not necessary.
 * In the config table the primary key is the name of the parameter.
 * 
 * Parameter types<br>
 * ---------------
 * 
 * Here is an overview of the various parameter types.
 * <pre>
 * 
 *  - b=bool:
 *    This type is used to store yes/no type of parameters. The parameter is 
 *    considered 'TRUE' whenever the integer value of the value field is
 *    non-zero. If the integer value of the value field is zero, the parameter
 *    is considered to be 'FALSE'. Note that the NULL value also yields a zero
 *    integer value and hence 'FALSE'.
 * 
 *  - c=checklist:
 *    This type is an array of boolean parameters. The value of a parameter
 *    of this type is stored as a comma-delimited list of values which are
 *    to be considered 'TRUE'. If none of the elements of this array are
 *    'TRUE', the comma-delimited list is empty. The list of possible
 *    values MUST be specified in the 'extra' field in the 'options=' item.
 *    (see below for more information about the 'extra' field).
 * 
 *  - d=date:
 *    This type is used to store (valid) dates, in the standard format 'yyyy-mm-dd'.
 *    (Validated in {@link valid_datetime()}, values from '0000-01-01' - '9999-12-31').
 * 
 *  - dt=date/time:
 *    This type is used to store (valid) date/time combinations, in the standard
 *    format 'yyyy-mm-dd hh:mm:ss'. (Validated in {@link valid_datetime()}, values
 *    from '0000-01-01 00:00:00' - '9999-12-31 23:59:59').
 * 
 *  - f=float(double):
 *    This type is used to store real (floating point) numbers with double precision.
 * 
 *  - i=int:
 *    This type is used to store integer numbers.
 * 
 *  - l=list:
 *    This type is used to store a single value from a list of available options
 *    (a 'picklist'). The current value is stored in the value field, and a list
 *    of possible values MUST be specified in the 'extra' field in the 'options=' item.
 *    (see below for more information about the 'extra' field).
 * 
 *  - r=radio:
 *    This type is also used to store a single value from a list of available options,
 *    much like the list-type (a 'picklist'). The difference is the representation in
 *    a dialog: a list parameter uses only a single line, whereas a group of radio
 *    buttons usually uses as many lines as there are available options. The current
 *    value is stored in the value field, and a list of possible values MUST be
 *    specified in the 'extra' field in the 'options=' item. (see below for more
 *    information about the 'extra' field).
 * 
 *  - s=string:
 *    This type is used to store generic text information. The maximum length of the
 *    string is the maximum length of the text field in the database (in MySQL this
 *    is 65535 bytes).
 * 
 *  - t=time:
 *    This type is used to store a (valid) time, in the format 'hh:mm:ss'.
 *    (Validated in {@link valid_datetime()}, values from '00:00:00' - '23:59:59').
 * 
 * </pre>
 * 
 * The Extra field<br>
 * ---------------
 * 
 * This field can contain additional information about the parameter, either
 * for validation or for display purposes. The contents of this field is a
 * list of semicolon-delimited name=value-pairs. The following items are
 * recognised (see also {@link dialoglib.php}.
 * <pre>
 * 
 *  - rows=&lt;int&gt;
 *    This is the number of rows to display in a dialog. It can apply to
 *    string-type variables and yield a textarea-tag (as opposed to an input-tag of
 *    type 'text'). It can also apply to a set of radio buttons in a very specific
 *    way: if the number of rows is 1, the radio buttons are displayed on a single
 *    line in the dialog.
 * 
 *  - columns=&lt;int&gt;
 *    This is number of columns to use for input (but not the necessry the maximum
 *    length of the input). If this item is omitted, default values apply, e.g.
 *    30 for date, time. datetime; 20 for float (double) and 10 for integer, etc.
 * 
 *  - minlenght=&lt;int&gt;
 *    This is the minimum number of characters that must be input. When this item
 *    is set to 1, an empty string is not allowed.
 * 
 *  - maxlength=&lt;int&gt;
 *    This is the maximum number of characters that can be input.
 * 
 *  - minvalue=&lt;mixed&gt;
 *    This is the minimum value for the field. The type of the minumum value
 *    is the same as the type of the variable itself. It applies to integers,
 *    floats, dates, datetimes and times.
 * 
 *  - maxvalue=&lt;mixed&gt;
 *    This is the maximum value for the field. The type of the maxumum value
 *    is the same as the type of the variable itself. It applies to integers,
 *    floats, dates, datetimes and times.
 * 
 *  - decimals=&lt;int&gt;
 *    This is the number of decimals that should be displayed in dialogs.
 *    It applies to floats.
 * 
 *  - options=option1,option2,option3,(...)
 *    This is a comma-delimited list of valid values for a list, radio or
 *    checklist parameter. The value of the parameter is one of the options
 *    in this list (for parameter types list and radio) OR a comma-delimited
 *    list of zero or more items (for checklists).
 * 
 *  - viewonly=&lt;int&gt;
 *    If the integer value of this item is non-zero, the user is not allowed
 *    to edit the value of the parameter. However, it is supposed to be displayed
 *    in the dialog nevertheless. 
 * 
 * </pre>
 * 
 * Generating dialogs for editing<br>
 * ------------------------------
 * 
 * The ConfigAssistant is clever enough to read and write the configuration
 * parameters from the specified table, using a where-clause when necessary.
 * Also, the ConfigAssistant automatically constructs translations of screen
 * prompts in a very specific way when constructing dialogs.
 * 
 * The translation keys are constructed as follows.
 * <pre>
 * 
 *  - simple string-like parameters (string, date, time, etc.)
 *    name  = &lt;prefix&gt;&lt;name&gt;
 *    label = &lt;prefix&gt;&lt;name&gt;_label
 *    title = &lt;prefix&gt;&lt;name&gt;_title
 * 
 *  - bool parameter
 *    name   = &lt;prefix&gt;&lt;name&gt;
 *    label  = &lt;prefix&gt;&lt;name&gt;_label
 *    title  = &lt;prefix&gt;&lt;name&gt;_title
 *    option = &lt;prefix&gt;&lt;name&gt;_option
 * 
 *  - list or radio parameter
 *    name    = &lt;prefix&gt;&lt;name&gt;
 *    label   = &lt;prefix&gt;&lt;name&gt;_label,
 *    title   = &lt;prefix&gt;&lt;name&gt;_title
 *    options:
 *    label = &lt;prefix&gt;&lt;name&gt;_&lt;option1&gt;_label
 *    title = &lt;prefix&gt;&lt;name&gt;_&lt;option1&gt;_title
 *         ...
 *    label = &lt;prefix&gt;&lt;name&gt;_&lt;optionN&gt;_label
 *    title = &lt;prefix&gt;&lt;name&gt;_&lt;optionN&gt;_title
 * 
 *  - checlist parameter
 *    name   = &lt;prefix&gt;&lt;name&gt;
 *    label  = &lt;prefix&gt;&lt;name&gt;_label
 *    title  = &lt;prefix&gt;&lt;name&gt;_title
 *    options:
 *    title  = &lt;prefix&gt;&lt;name&gt;_&lt;option1&gt;_title
 *    option = &lt;prefix&gt;&lt;name&gt;_&lt;option1&gt;_option
 *         ...
 *    title  = &lt;prefix&gt;&lt;name&gt;_&lt;optionN&gt;_title
 *    option = &lt;prefix&gt;&lt;name&gt;_&lt;optionN&gt;_option
 * 
 * </pre>
 * 
 * The string <prefix> can be used to avoid name clashes in the 'admin' (or other)
 * language file. The translations are then looked up in the specified language
 * domain (default: admin).
 *
 * Examples<br>
 * --------
 *
 * Example 1: sending a dialog to the user for editing all the parameters in the the main config table:
 *
 * <pre>
 * $table = 'config';
 * $keyfield = 'name';
 * $assistant = new ConfigAssistant($table,$keyfield);
 * $href = 'index.php?job=(...)&task=editconfig';
 * $assistant->show_dialog($output,$href);
 * </pre>
 *
 * Example 2: saving the modified data to the table
 *
 * <pre>
 * $table = 'config';
 * $keyfield = 'name';
 * $assistant = new ConfigAssistant($table,$keyfield);
 * if (!$assistant->save_data($output)) {
 *     echo "FAILED";
 * } else {
 *     echo "SUCCESS saving configuration";
 * }
 *</pre>
 *
 * Sounds easy to use, doesn't it?
 *
 * @todo implement checklist
 */
class ConfigAssistant {
    /** @var string $table the table that contains the configuration */
    var $table = '';

    /** @var array $fields the list of essential fields to retrieve from the the table */
    var $fields = array('name','type','value','extra');

    /** @var mixed $where a string with a whereclause (without 'WHERE') or an array with conditions */
    var $where = '';

    /** @var string $keyfield a string indicating the keyfield to uniquely identify the configuration parameter */
    var $keyfield = '';

    /** @var array $records the cached list of configuration values straight from the database */
    var $records = NULL;

    /** @var $string $prefix is prepended for every translation/language key and the dialog item name */
    var $prefix = '';

    /** @var $string $domain the language domain where to look for translations (default: 'admin') */
    var $language_domain = '';

    /** @var array $dialogdef an array with a dialog ready to use for {@link dialog_quickform()} */
    var $dialogdef = NULL;

    /** @var array $dialogdef_hidden array with additional fields that should be included in the dialog */
    var $dialogdef_hidden = '';

    /** constructor for the configuration assistant
     *
     * This stores the parameters, sets defaults when applicable and subsequently reads 
     * selected config parameters into the $this->records for future reference.
     *
     * @param string $table the table where the configuration parameters are stored
     * @param string $keyfield the field that uniquely identifies the configuration parameters
     * @param string $prefix is prepended for every translation/language key and the also dialog item name
     * @param string $domain the language domain where to look for translations (default: 'admin')
     * @param mixed $where a whereclause (without 'WHERE') or an array with conditions
     * @param array $dialogdef_hidden additional fields for inclusion in dialog definition
     * @return void object setup and data buffered in object
     */
    function ConfigAssistant($table,$keyfield,$prefix='',$domain='',$where='',$dialogdef_hidden='') {
        $this->table = $table;
        $this->keyfield = $keyfield;
        if (!in_array($keyfield,$this->fields)) { // we need to have this field for db_select_all_records()
            $this->fields[] = $keyfield;
        }
        $this->prefix = (empty($prefix)) ? '' : $prefix;
        $this->language_domain = (empty($domain)) ? 'admin' : $domain;
        $this->where = $where;
        $this->dialogdef_hidden = $dialogdef_hidden;
        $this->records = db_select_all_records($this->table,$this->fields,$this->where,'sort_order',$this->keyfield);
        if ($this->records === FALSE) {
            logger('configassistant: could not retrieve config data from database: '.db_errormessage());
        }
    } // ConfigAssistant()


    /** add a complete dialog to the content area of the output
     *
     * @param object &$output the object that collects the output
     * @param string $href the target for the form that will be created
     * @result output added to content part of output object
     * @uses dialog_quickform()
     */
    function show_dialog(&$output,$href) {
        if (empty($this->dialogdef)) {
            $this->dialogdef = $this->get_dialogdef();
        }
        $output->add_content(dialog_quickform($href,$this->dialogdef));
    } // show_dialog()

    /** save the modified configuration parameters to the database
     *
     * @param object &$output the object that collects the output
     * @result FALSE on error + error messages added to messages part of output or TRUE and data stored to database.
     * @uses dialog_validate()
     */
    function save_data(&$output) {
        if (empty($this->dialogdef)) {
            $this->dialogdef = $this->get_dialogdef();
        }
        if (!dialog_validate($this->dialogdef)) {
            foreach($this->dialogdef as $k => $item) {
                if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                    $output->add_message($item['error_messages']);
                }
            }
            return FALSE;
        }

        $errors = 0;
        $records_changed = 0;
        $records_unchanged = 0;
        if (is_array($this->records)) {
            foreach($this->records as $id => $record) {
                $name = $this->prefix.'record_'.strval($id);
                if (isset($this->dialogdef[$name])) {
                    if ($this->dialogdef[$name]['value'] != $this->dialogdef[$name]['old_value']) {
                        ++$records_changed;
                        $fields = array('value' => strval($this->dialogdef[$name]['value']));
                        $where = array($this->keyfield => $id);
                        if (db_update($this->table,$fields,$where) === FALSE) {
                            logger('configassistant: error saving config value: '.db_errormessage());
                            ++$errors;
                        } else {
                            logger(sprintf("configassistant: success updating %s[%s] => '%s'",
                                            $this->table,$id,strval($this->dialogdef[$name]['value'])),WLOG_DEBUG);
                        }
                    } else {
                        ++$records_unchanged;
                    }  // if (modified)
                } // if (data available) 
            } // foreach
        } // if (records)
        logger(sprintf('configassistant: save configuration in table %s: unchanged: %d, changed: %d, errors: %d',
                       $this->table,$records_unchanged, $records_changed,$errors),WLOG_DEBUG);
        if ($errors == 0) {
            $output->add_message(t('success_saving_data','admin'));
            $retval = TRUE;
        } else {
            $output->add_message(t('errors_saving_data','admin',array('{ERRORS}' => strval($errors))));
            $retval = FALSE;
        }
        return $retval;
    } // save_data()

    /** construct an array with the dialog information
     *
     * @return array the dialog information 
     * @todo implement checklist
     */
    function get_dialogdef() {
        $dialogdef = (is_array($this->dialogdef_hidden)) ? $this->dialogdef_hidden : array();
        if (is_array($this->records)) {
            foreach($this->records as $id => $record) {
                $extra = $this->get_extra($record['type'],$record['extra']);
                $name = $this->prefix.'record_'.strval($id);
                $item = array(
                    'type' => F_ALPHANUMERIC,
                    'name' => $name,
                    'value' => $record['value'],
                    'label' => t($this->prefix.$record['name'].'_label',$this->language_domain),
                    'title' => t($this->prefix.$record['name'].'_title',$this->language_domain),
                    'is_modified' => FALSE,
                    'columns' => 50
                    );

                switch($record['type']) {
                case 'b':
                    $item['type'] = F_CHECKBOX;
                    $item['options'] = array(1=>t($this->prefix.$record['name'].'_option',$this->language_domain));
                    $item['value'] = (intval($record['value']) != 0) ? '1' : '';
                    break;

                case 'c':
                    // not implemented yet
                    break;

                case 'd':
                    $item['type'] = F_DATE;
                    $item['columns'] = 20;
                    $item['maxlength'] = 20;
                    break;

                case 'dt':
                    $item['type'] = F_DATETIME;
                    $item['columns'] = 20;
                    $item['maxlength'] = 20;
                    break;

                case 'f':
                    $item['type'] = F_REAL;
                    if (is_numeric($record['value'])) {
                        $decimals = (isset($extra['decimals'])) ? abs(intval($extra['decimals'])) : 2;
                        $item['value'] = sprintf("%1.".$decimals."f",floatval($record['value']));
                    } else {
                        $item['value'] = NULL;
                    }
                    $item['columns'] = 20;
                    $item['maxlength'] = 20;
                    break;

                case 'i':
                    $item['type'] = F_INTEGER;
                    $item['value'] = (is_numeric($record['value'])) ? intval($record['value']) : NULL;
                    $item['columns'] = 20;
                    $item['maxlength'] = 12;
                    break;

                case 'l':
                    $item['type'] = F_LISTBOX;
                    $item['options'] = $this->get_options_from_extra($extra,$record['name']);
                    $item['value'] = $record['value'];
                    break;

                case 'r':
                    $item['type'] = F_RADIO;
                    $item['options'] = $this->get_options_from_extra($extra,$record['name']);
                    $item['value'] = $record['value'];
                    break;

                case 's':
                    $item['type'] = F_ALPHANUMERIC;
                    break;

                case 't':
                    $item['type'] = F_TIME;
                    $item['columns'] = 20;
                    $item['maxlength'] = 20;
                    break;

                default:
                    break;
                }
                if (!empty($extra)) {
                    foreach($extra as $k => $v) {
                        if ($k != 'options') {
                            $item[$k] = $v;
                        }
                    }
                }
                $item['old_value'] = $item['value'];
                $dialogdef[$name] = $item;
            } // foreach
        } // if (any records at all)
        $dialogdef[] = dialog_buttondef(BUTTON_SAVE);
        $dialogdef[] = dialog_buttondef(BUTTON_CANCEL);
        return $dialogdef;
    } // get_dialogdef()


    /** construct an array based on name=value pairs in an 'extra' field
     *
     * This constructs an array based on the name=value pairs in the extras string.
     * Most recognised parameters yield an integer value. Exceptions are:
     * minvalue and maxvalue: these yield a variable of the same type as the config parameter itself
     * options yields an array with all options from the comma delimited list
     * Unknown name=value pairs are logged with WLOG_DEBUG.
     *
     * @param string $type variable type (necessary for calculating minvalue/maxvalue)
     * @param string $extras semicolon-delimited list of name=value pairs
     * @return array string $extras parsed into an array (or an empty array if no name=value pairs found)
     */
    function get_extra($type,$extras) {
        $extra = array();
        $extras = trim($extras);
        if (empty($extras)) {
            return $extra;
        }
        $name_value_pairs = explode(';',$extras);
        if (!empty($name_value_pairs)) {
            foreach($name_value_pairs as $pair) {
                list($name,$value) = explode('=',$pair);
                switch ($name) {
                case 'rows':
                case 'columns':
                case 'minlength':
                case 'maxlength':
                case 'decimals':
                case 'viewonly':
                    $extra[$name] = intval($value);
                    break;
                case 'maxvalue':
                case 'minvalue':
                    if ($type == 'i') {
                        $extra[$name] = intval($value);
                    } elseif ($type == 'f') {
                        $extra[$name] = floatval($value);
                    } else {
                        $extra[$name] = $value;
                    }
                    break;
                case 'options':
                    $extra[$name] = explode(',',$value);
                    break;
                default:
                    logger("configassistant: weird: '$pair' not recognised in table '{$this->table}'",WLOG_DEBUG);
                    break;
                }
            }
        }
        return $extra;
    } // get_extra()
        

    function get_options_from_extra($extra,$name) {
        $options = array();
        if (is_array($extra['options'])) {
            foreach($extra['options'] as $option) {
                $options[$option] = array('option'=> t($this->prefix.$name.'_'.$option.'_option',$this->language_domain),
                                          'title' => t($this->prefix.$name.'_'.$option.'_title',$this->language_domain));
            }
        }
        return $options;
    } // get_options_from_extra()

} // ConfigAssistant

?>