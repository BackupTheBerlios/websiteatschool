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

/** /program/lib/dialoglib.php - useful functions for manipulating dialogs
 *
 * This file provides various utility routines for creating and
 * validating user dialogs.
 *
 * A dialog is a collection of input elements grouped together in a form.
 * An input element (or field) has at least a type (e.g. F_ALPHANUMERIC or
 * F_DATETIME) and a name (e.g. 'title' or 'expiry') and optionally one
 * or more of the other possible properties.  The name of an input
 * element uniquely identifies the field in the dialog; it can be used to
 * retrieve the data entered by the user from the global $_POST array.
 *
 * A dialog can be defined via a 0-based array, where every array element
 * is separate input element. Each of the input elements is in itself an
 * associative array with property-value-pairs. The recognition of a
 * property depends on the type, e.g. the number of decimals is
 * irrelevant for a string-type input element.
 *
 * Here is an overview of properties and field types to which they apply.
 * An 'x' means required, an 'o' means optional and a '-' means don't
 * care.
 *
 * <pre>
 *                 type
 *                 | name
 *                 | | value
 *                 | | | rows
 *                 | | | | columns
 *                 | | | | |   minlength
 *                 | | | | |   | maxlength
 *                 | | | | |   | | decimals
 *                 | | | | |   | | | minvalue
 *                 | | | | |   | | | | maxvalue
 *                 | | | | |   | | | | |   options
 *                 | | | | |   | | | | |   | label
 *                 | | | | |   | | | | |   | | accesskey
 *                 | | | | |   | | | | |   | | | alt
 *                 | | | | |   | | | | |   | | | | class
 *                 | | | | |   | | | | |   | | | | |   viewonly
 *                 | | | | |   | | | | |   | | | | |   | tabindex
 *                 | | | | |   | | | | |   | | | | |   | | title
 *                 | | | | |   | | | | |   | | | | |   | | | hidden
 *                 | | | | |   | | | | |   | | | | |   | | | | id
 *                 | | | | |   | | | | |   | | | | |   | | | | |
 * F_ALPHANUMERIC  x x o o o   o o - - -   - o o o o   o o o o o
 * F_INTEGER       x x o - o   o o - o o   - o o o o   o o o o o
 * F_REAL          x x o - o   o o o o o   - o o o o   o o o o o
 * F_DATE          x x o - o   o o - o o   - o o o o   o o o o o
 * F_TIME          x x o - o   o o - o o   - o o o o   o o o o o
 * F_DATETIME      x x o - o   o o - o o   - o o o o   o o o o o
 * F_PASSWORD      x x o - o   o o - - -   - o o o o   o o o o o
 * F_CHECKBOX      x x o - -   - - - - -   x o o o o   o o o o o
 * F_LISTBOX       x x o o -   - - - - -   x o o o o   o o o o o
 * F_RADIO         x x o - -   - - - - -   x o o o o   o o o o o
 * F_FILE          x x o - o   - - - - -   - o o o o   o o o - o
 * F_SUBMIT        x x x - -   - - - - -   - - o o o   o o o - o
 * F_RICHTEXT      x x o o o   o o - - -   - o o o o   o o o o o
 * </pre>
 *
 * There are two more properties: 'errors' and 'error_messages'. These
 * properties can be set whenever the validation of the input yields an
 * error. If all is well, 'errors' is either not set or equal to 0.
 *
 * Here is a description of the various properties.
 *
 *  - type:
 *    the type of the input element, one of the F_* constants defined at
 *    the top of this file.
 *
 *  - name:
 *    the name to uniquely identify the input element
 *
 *  - value:
 *    the current value of the element, this value needs to be displayed
 *    in the dialog
 *
 *  - rows:
 *    the number of text rows for this element. This applies only to
 *    generic text fields (alphanumerics) and listboxes.
 *
 *  - columns:
 *    the number of characters to show in the input element. This applies
 *    only to text inputs (not lists or checkboxes).
 *
 *  - minlength:
 *    the minimum number of characters that need to be entered by the
 *    user. If 0, the field can be left empty.
 *
 *  - maxlength:
 *    the maximum number of characters that can be entered by the
 *    user. Note that this number is no necessarily the same as the
 *    number of columns to display (or even the product of columns and
 *    rows).
 *
 *  - decimals:
 *    the number of decimals in the fraction of real numbers
 *
 *  - minvalue:
 *    the minimum value that needs to be entered. This applies to numeric
 *    fields (integer, real) and also to dates and times.
 *
 *  - maxvalue:
 *    the maximum value that can be entered. This applies to numeric
 *    fields (integer, real) and also to dates and times.
 *
 *  - options:
 *    this is a list (array) of key-value-pairs that identify the
 *    allowable values for a listbox or radio buttons. The key is used as
 *    the fields value and the value is the descriptive title of the
 *    option.
 *
 *  - label:
 *    this is the text that is displayed _before_ the input element. It
 *    is used to indicate the purpose of the input element.
 *
 *  - accesskey:
 *    this is a single letter that can be used to access the element via
 *    the keyboard by using a key combination like [Alt-A].
 *
 *  - alt:
 *    an alternative text that describes the element (accessibility)
 *
 *  - class:
 *    this identifies the class(es) that need to be associated with this
 *    element. This allows for changing the style of the element.
 *
 *  - viewonly:
 *    indicates that this element is not to be changed by the user but
 *    that it can be displayed nevertheless
 *
 *  - tabindex:
 *    a number that indicates the order in which fields area accessed
 *    when using the [Tab] key to move to the next element.
 *
 *  - title:
 *    a descriptive title that is displayed when the mouse is hovering
 *    over the element
 *
 *  - hidden:
 *    identifies a field that should be part of the dialog, but not
 *    visible to the user. Note that by specifying a hidden list, it is
 *    possible to validate the value of the hidden input agains the list
 *    of acceptable values once it returns from the user. However, one
 *    should never trust the value of a field that is sent in 'hidden'
 *    form, because aftera all it is still user input, no matter what.
 *
 *  - id
 *    a unique (within the document) identifier for this input element.
 *    This id is used to link a label to an input. The id should start
 *    with a letter. The corresponding label is identified by appending
 *    the string '_label' to the id.
 *    
 *
 *  - errors:
 *    the number of errors encountered after validating the value of this
 *    element
 *
 *  - error_messages:
 *    an array of messages identifying the problems encountered with this
 *    element during validation
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: dialoglib.php,v 1.6 2012/04/18 07:57:35 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

define('F_ALPHANUMERIC','alphanumeric');
define('F_INTEGER','integer');
define('F_REAL','real');
define('F_DATE','date');
define('F_TIME','time');
define('F_DATETIME','datetime');
define('F_PASSWORD','password');
define('F_CHECKBOX','checkbox');
define('F_LISTBOX','listbox');
define('F_RADIO','radio');
define('F_SUBMIT','submit');
define('F_FILE','file');
define('F_RICHTEXT','richtext');

define('ATTR_CLASS_ERROR','error');
define('ATTR_CLASS_VIEWONLY','viewonly');

define('BUTTON_OK','ok');
define('BUTTON_CANCEL','cancel');
define('BUTTON_SAVE','save');
define('BUTTON_DELETE','delete');
define('BUTTON_YES','yes');
define('BUTTON_NO','no');
define('BUTTON_GO','go');


/** construct a generic form with a dialog
 *
 * this constructs an HTML form with a simple dialog where
 *
 *  - every label and every widget has its own line
 *    (enforced by a BR-tag)
 *  - label/widget-combinations are separated with a P-tag
 *  - buttons are stringed together on a single line (ie no trailing BR)
 *
 * This should be sufficient for many dialogs.
 * If the layout needs to be more complex a custom dialog can
 * always be constructed using functions {@link dialog_get_label()}
 * and {@link dialog_get_widget()}.
 *
 * @param string $href the target of the HTML form
 * @param array &$dialogdef the array which describes the complete dialog
 * @param string $method method to submit data to the server, either 'post' or 'get'
 * @param string|array $attributes holds the attributes to add to the form tag
 * @return array constructed HTML-form with dialog, one line per array element
 * @uses html_form()
 */
function dialog_quickform($href,&$dialogdef,$method='post',$attributes='') {
    $buttons_seen = FALSE;
    $a = array(0 => html_form($href,$method,$attributes)); // result starts with opening a form tag
    foreach($dialogdef as $item) {
        if (!isset($item['name'])) { // skip spurious item (possibly empty array)
            continue;
        }
        $label = dialog_get_label($item);
        if (!empty($label)) {
            $a[] = '<p>';
            $a[] = $label.'<br>';
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
            // add a <br> after every 1-line item except buttons
            // result: fields line up nicely and buttons are on a single row
            $postfix = '';
            if ($item['type'] == F_SUBMIT) {
                if (!$buttons_seen) {
                    $buttons_seen = TRUE;
                    $a[] = '<p>';
                }
            } elseif (!((isset($item['hidden'])) && ($item['hidden']))) {
                $postfix = '<br>';
            }
            $a[] = $widget.$postfix;
        }
    }
    $a[] = '<p>';
    $a[] = html_form_close();
    return $a;
} // dialog_quickform()


/** construct a label for a dialog input element
 *
 * this constructs the label for an input.
 * It is built inside a label-tag, possibly with these attributes:
 * id, for, accesskey, class, title.
 * The class is a special case: if there were errors
 * the class 'ATTR_CLASS_ERROR' is added to the class attribute,
 * in case of viewonly the class ATTR_CLASS_VIEWONLY' is added too.
 * (Note that the latter shouldn't happen: how can a viewonly field
 * yield any errors at all unless someone is trying to crack the program?)
 *
 * Because some browsers require that the label of a listbox is linked
 * to the select tag (done via 'id' and 'for'), we MUST have some 'id'
 * for that particular tag. If it is not there, we generate one and add it
 * automagically.
 *
 * @param array &$item the parameters that describe the dialog input element
 * @return string ready-to-use HTML
 * @todo if we let the hotkey from the label prevail and add it to the input tag, why add a hotkey to the label too?
 */
function dialog_get_label(&$item) {
    // a hidden input cannot have a label
    if (isset($item['hidden'])) {
        if ($item['hidden']) {
            return '';
        }
    }
    if (!isset($item['label'])) {
        return ''; // no label, nothing to do
    }
    // some browsers insist on a tight link between label and select
    if (($item['type'] == F_LISTBOX) && (!isset($item['id']))) {
        $item['id'] = 'id'.strval(get_unique_number()); // assign a unique id for every item
    }
    $label = accesskey_tilde_to_underline($item['label']);
    $attributes = array();
    if (isset($item['id'])) {
        $attributes['id'] = $item['id']."_label";
        $attributes['for'] = $item['id'];
    }
    $hotkey = accesskey_from_string($item['label']);
    if (!empty($hotkey)) {
        $attributes['accesskey'] = $hotkey;
    } elseif (isset($item['accesskey'])) {
        $attributes['accesskey'] = $item['accesskey'];
    }
    if (isset($item['title'])) {
        $attributes['title'] = $item['title'];
    }

    $class = dialog_get_class($item);
    if (!empty($class)) {
        $attributes['class'] = $class;
    }
    return html_tag('label',$attributes,$label);
} // dialog_get_label()


/** construct an actual HTML input widget for dialog input element
 *
 * this constructs the actual HTML-code for a dialog input element.
 * If the input element is 'hidden', we generate a minimalistic
 * hidden field with no labels, accesskeys or whatever: basically
 * just a name-value-pair to communicate to a subsequent form.
 *
 * If the item is genuine (it has a name and a type), we construct
 * the input using a workhorse routine.
 *
 * @param array &$item the parameters that describe the dialog input element
 * @return array|string 1 or more lines of ready-to-use HTML
 * @todo we could manipulate the title attribute of input strings
 *       like "please enter a number between {MIN} and {MAX}" based
 *       on the various value properties instead of just displaying the title.
 *       oh well, for a future version, perhaps...
 * @todo we now only cater for buttons via input type="submit" without the option
 *       to visualise the accesskey. Using the button tag could solve that, but
 *       button is not defined beforde HTML 4.01. What to do?
 */
function dialog_get_widget(&$item) {
    if ((!isset($item['name'])) || (empty($item['name']))) {
        return '';
    }
    $name = $item['name'];

    if ((!isset($item['type'])) || (empty($item['type']))) {
        return '';
    }
    $value = (isset($item['value'])) ? $item['value'] : '';

    if ((isset($item['hidden'])) && ($item['hidden'])) {
        return html_tag('input',array('type' => 'hidden','name' => $name, 'value' => $value));
    }

    $f_type = $item['type'];
    switch($f_type) {
    case F_ALPHANUMERIC:
    case F_INTEGER:
    case F_REAL:
    case F_DATE:
    case F_TIME:
    case F_DATETIME:
    case F_PASSWORD:
        $retval = dialog_get_widget_textinput($item,$name,$value,$f_type);
        break;

    case F_SUBMIT:
        $retval = dialog_get_widget_submit($item,$name,$value);
        break;

    case F_CHECKBOX:
    case F_RADIO:
        $retval = dialog_get_widget_radiocheckbox($item,$name,$value,$f_type);
       break;

    case F_LISTBOX:
        $retval = dialog_get_widget_listbox($item,$name,$value);
        break;

    case F_RICHTEXT:
        $retval = dialog_get_widget_richtextinput($item,$name,$value,$f_type);
        break;

    case F_FILE:
        $retval = dialog_get_widget_file($item,$name,$value);
        break;

    default:
        $retval = 'INTERNAL ERROR: UNKNONWN FIELDTYPE "'.htmlspecialchars($f_type).'" (should not have happened)';
        break;
    }
    return $retval;
} // dialog_get_widget()


/** validate and check values that were submitted via a user dialog
 *
 * this steps through the definition of a dialog and retrieves the values submitted by
 * the user via $_POST[]. The values are checked against the constraints (e.g. minimum string length,
 * date range, etc.). If the submitted value is considered valid, it is stored in the corresponding
 * value of the dialogdef element, maybe properly reformatted (in case of dates/times/datetimes).
 * If there were errors, these are recorded in the dialog definition element, in the form of one or
 * more readable error messages. Also the error count (per element) is incremented. This makes it
 * easy to
 *  - inform the user about what was wrong with the input data
 *  - determine whether there was an error at all (if $dialogdef[$k]['errors'] > 0).
 *
 * Note that this routine has the side effect of filling the dialog array with the data that
 * was submitted by the user via $_POST. If the validation is successful, the data is ready
 * to be saved into the database. If it is not, the data entered is still available in the
 * dialogdef which makes it easy to return to the user and let the user correct the errors without
 * losing all the data input because of a silly mistake in some input field.
 *
 * Update 2009-03-17: We no longer validate the view-only fields because these fields are not POST'ed 
 * by the browser and hence cannot be validated. This also means that there is no value set from $_POST
 * for those fields.
 *
 * Update 2011-09-29: added UTF-8 validation, replace with U+FFFD (Unicode replacement character) on fail
 *
 * @param array &$dialogdef the complete dialog definition; contains detailed errors and/or reformatted values
 * @return bool TRUE if all submitted values are considered valid, FALSE otherwise
 * @todo add an error message to
 */
function dialog_validate(&$dialogdef) {
    $total_errors = 0;
    foreach($dialogdef as $k => $item) {
        if (isset($item['name'])) {
            if ((isset($item['viewonly'])) && ($item['viewonly'])) {
                continue;
            }
            $name = $item['name'];
            $fname = (isset($item['label'])) ? str_replace('~','',$item['label']) : $name;
            $dialogdef[$k]['errors'] = 0;
            $dialogdef[$k]['error_messages'] = array();
            $f_type = (isset($item['type'])) ? $item['type'] : '';
            $value = (isset($item['value'])) ? $item['value'] : '';
            if (isset($_POST[$name])) {
                if (utf8_validate($_POST[$name])) {
                    $posted_value = magic_unquote($_POST[$name]);
                } else {
                    $posted_value = "\xEF\xBF\xBD"; // UTF-8 encoded substitution character U+FFFD
                    ++$dialogdef[$k]['errors'];
                    $dialogdef[$k]['error_messages'][] = t('validate_invalid','',array('{FIELD}' => $fname));
                }
            } else {
                $posted_value = ''; // should be NULL but empty string is more convenient here
            }
            switch($f_type) {
            case F_DATE:
            case F_TIME:
            case F_DATETIME:
            case F_ALPHANUMERIC:
            case F_INTEGER:
            case F_REAL:
            case F_PASSWORD:
            case F_RICHTEXT:
                if (($f_type == F_DATE) || ($f_type == F_TIME) || ($f_type == F_DATETIME)) {
                    $datetime_value = '';
                    $is_valid_datetime = valid_datetime($f_type,$posted_value,$datetime_value);
                    if (!$is_valid_datetime) {
                        ++$total_errors;
                        ++$dialogdef[$k]['errors'];
                        $dialogdef[$k]['error_messages'][] = t('validate_invalid_datetime','',array('{FIELD}'=>$fname));
                    } // else we have a valid date/time, and a properly reformatted copy for comparisons too
                }
                if (isset($item['minlength'])) {
                    $minlength = intval($item['minlength']);
                    if (strlen($posted_value) < $minlength) {
                        ++$total_errors;
                        ++$dialogdef[$k]['errors'];
                        $dialogdef[$k]['error_messages'][] = t('validate_too_short','',array(
                            '{FIELD}' => $fname, '{MIN}'=>strval($minlength)));
                    }
                }
                if (isset($item['maxlength'])) {
                    $maxlength = intval($item['maxlength']);
                    if ($maxlength < strlen($posted_value)) {
                        ++$total_errors;
                        ++$dialogdef[$k]['errors'];
                        $dialogdef[$k]['error_messages'][] = t('validate_too_long','',array(
                            '{FIELD}' => $fname, '{MAX}'=>strval($maxlength)));
                    }
                }
                if (isset($item['minvalue'])) {
                    switch($f_type) {
                    case F_INTEGER:
                        if (intval($posted_value) < intval($item['minvalue'])) {
                            ++$total_errors;
                            ++$dialogdef[$k]['errors'];
                            $dialogdef[$k]['error_messages'][] = t('validate_too_small','',array(
                                '{FIELD}' => $fname, '{MIN}'=> $item['minvalue']));
                        }
                        break;
                    case F_REAL:
                        if (floatval($posted_value) < floatval($item['minvalue'])) {
                            ++$total_errors;
                            ++$dialogdef[$k]['errors'];
                            $dialogdef[$k]['error_messages'][] = t('validate_too_small','',array(
                                '{FIELD}' => $fname, '{MIN}'=> $item['minvalue']));
                        }
                        break;
                    case F_DATE:
                    case F_TIME:
                    case F_DATETIME:
                        if ($is_valid_datetime) {
                            // there's no point in checking a value if the value itself is invalid
                            if ($datetime_value < $item['minvalue']) {
                                ++$total_errors;
                                ++$dialogdef[$k]['errors'];
                                $dialogdef[$k]['error_messages'][] = t('validate_too_small','',array(
                                    '{FIELD}' => $fname, '{MIN}'=> $item['minvalue']));
                            }
                        }
                        break;
                    }
                }
                if (isset($item['maxvalue'])) {
                    switch($f_type) {
                    case F_INTEGER:
                        if (intval($item['maxvalue']) < intval($posted_value)) {
                            ++$total_errors;
                            ++$dialogdef[$k]['errors'];
                            $dialogdef[$k]['error_messages'][] = t('validate_too_large','',array(
                                '{FIELD}' => $fname, '{MAX}'=> $item['maxvalue']));
                        }
                        break;
                    case F_REAL:
                        if (floatval($item['maxvalue']) < floatval($posted_value)) {
                            ++$total_errors;
                            ++$dialogdef[$k]['errors'];
                            $dialogdef[$k]['error_messages'][] = t('validate_too_large','',array(
                                '{FIELD}' => $fname, '{MAX}'=> $item['maxvalue']));
                        }
                        break;
                    case F_DATE:
                    case F_TIME:
                    case F_DATETIME:
                        if ($is_valid_datetime) {
                            // there's no point in checking a value if the value itself is invalid
                            if ($item['maxvalue'] < $datetime_value) {
                                ++$total_errors;
                                ++$dialogdef[$k]['errors'];
                                $dialogdef[$k]['error_messages'][] = t('validate_too_large','',array(
                                    '{FIELD}' => $fname, '{MAX}'=> $item['maxvalue']));
                            }
                        }
                        break;
                    }
                }
                // finally format the data
                switch($f_type) {
                case F_INTEGER:
                    $dialogdef[$k]['value'] = strval(intval($posted_value));
                    break;
                case F_REAL:
                    $decimals = (isset($item['decimals'])) ? abs(intval($item['decimals'])) : 2;
                    $dialogdef[$k]['value'] = sprintf("%1.".$decimals."f",floatval($posted_value));
                    break;
                case F_DATE:
                case F_TIME:
                case F_DATETIME:
                    $dialogdef[$k]['value'] = ($is_valid_datetime) ? $datetime_value : $posted_value;;
                    break;
                default:
                    $dialogdef[$k]['value'] = $posted_value;
                    break;
                }
                break;

            case F_CHECKBOX:
                // there are two options:
                // either $posted_value equals the value in the options list
                // OR it does't. (well duh). However, it it doesn't match AND
                // it is not an empty string (see above, should be NULL), it
                // is an error nonetheless. OK. Here we go.
                if (!empty($posted_value)) {
                    if (!isset($item['options'][$posted_value])) {
                        // oops, something rottenin the state of Denmark...
                        ++$total_errors;
                        ++$dialogdef[$k]['errors'];
                        $dialogdef[$k]['error_messages'][] = t('validate_invalid','',array('{FIELD}' => $fname));
                        $dialogdef[$k]['value'] = '';
                    } else {
                        $dialogdef[$k]['value'] = $posted_value;
                    }
                } else {
                    $dialogdef[$k]['value'] = '';
                }
                break;
            case F_RADIO:
            case F_LISTBOX:
                // the value should exist in the options array
                if (!isset($item['options'][$posted_value])) {
                    // oops, something rotten in the state of Denmark...
                    ++$total_errors;
                    ++$dialogdef[$k]['errors'];
                    $dialogdef[$k]['error_messages'][] = t('validate_invalid','',array('{FIELD}' => $fname));
                    $dialogdef[$k]['value'] = '';
                } else {
                    $dialogdef[$k]['value'] = $posted_value;
                }
                break;

            case F_FILE:
                // any value is OK, because
                // 1. checking is done separately in the filemanager (including virusscan etc.)
                // 2. if there is an error in 1 file, the other uploaded files could be perfectly
                //    OK. Sending the user back to the upload dialog would be counter-productive
                //    because the 'good' files would have to be uploaded again and she would have
                //    to type in/browse the files again (and again and again...)
                break;

            case F_SUBMIT:
                // any value is OK, no check needed
                break;

            default:
                ++$total_errors;
                $dialogdef[$k]['errors'] = 1;
                $dialogdef[$k]['error_messages'] = $item['name'].' - INTERNAL ERROR: unknown type';
                break;
            }
        }
    }
    return ($total_errors == 0);
} // dialog_validate()



// ==================================================================
// =========================== WORKHORSES ===========================
// ==================================================================


/** construct an input field, usually for text input OR a textarea for multiline input
 *
 * this constructs most variations on text fields, including password fields
 * Many of the defined field types (the F_* constants) can be handled via a
 * simple input of type text. The semantics of the field (eg. is it an integer,
 * a real) have no impact on the HTML-input: at that level it is still plain text.
 * However, for a password we use the password type in order to make the value
 * display as asterisks. If the number of rows is more than 1, the input element
 * becomes a text area. Note that is generally only applies to F_ALPHANUMERIC
 * but that is not enforced here (you can make a multiline F_DATE, even though
 * it doesn't make much sense).
 *
 * The properties recognised translate to the following HTML-code/attributes
 * <pre>
 * name      : name
 * value     : value
 * accesskey : accesskey
 * rows      : rows (textarea only)
 * columns   : cols (textarea) or size (input type="text")
 * maxlength : maxlength
 * alt       : alt
 * class     : class (also depends on viewonly and errors)
 * tabindex  : tabindex
 * id        : id
 * title     : title
 * viewonly  : disabled AND addition of ATTR_CLASS_ERROR to class list (if viewonly == TRUE)
 * errors    : addition of ATTR_CLASS_ERROR to class list (if errors > 0)
 * </pre>
 *
 * @param array &$item the parameters that describe the dialog input element
 * @param string $name the name of the input element ('fieldname')
 * @param mixed $value the (current) value of the input element to show ('field value')
 * @param string $f_type the type of the field (eg text, number, date, time, ...)
 * @return array|string 1 or more lines of ready-to-use HTML
 * @todo if we let the hotkey from the label prevail and add it to the input tag, why add a hotkey to the label too?
 */
function dialog_get_widget_textinput(&$item,$name,$value,$f_type) {
        $attributes = array('name' => $name);
        if ((isset($item['rows'])) && ($item['rows'] > 1)) {
            $widget = 'textarea';
            $attributes['rows'] = $item['rows'];
            if (isset($item['columns'])) {
                $attributes['cols'] = $item['columns'];
            }
        } else {
            if ($f_type == F_PASSWORD) {
                $widget = 'password';
            } else {
                $widget = 'text';
            }
            $attributes['type'] = $widget;
            $attributes['value'] = $value;
            if (isset($item['maxlength'])) {
                $attributes['maxlength'] = $item['maxlength'];
            }
            if (isset($item['columns'])) {
                $attributes['size'] = $item['columns'];
            }
            if (isset($item['alt'])) {
                $attributes['alt'] = $item['alt'];
            }
        }
        $hotkey = (isset($item['label'])) ? accesskey_from_string($item['label']) : '';
        if (!empty($hotkey)) {
            $attributes['accesskey'] = $hotkey;
        } elseif (isset($item['accesskey'])) {
            $attributes['accesskey'] = $item['accesskey'];
        }
        $class = dialog_get_class($item);
        if (!empty($class)) {
            $attributes['class'] = $class;
        }
        if (isset($item['tabindex'])) {
            $attributes['tabindex'] = $item['tabindex'];
        }
        if (isset($item['id'])) {
            $attributes['id'] = $item['id'];
        }
        if (isset($item['title'])) { // see todo #1 above
            $attributes['title'] = $item['title'];
        }
        if ((isset($item['viewonly'])) && ($item['viewonly'])) {
            $attributes['disabled'] = NULL;
        }
        if ($widget == 'textarea') {
            return html_tag($widget,$attributes,htmlspecialchars($value));
        } else {
            return html_tag('input',$attributes);
        }
} // dialog_get_widget_textinput()


/** construct a submit button
 *
 * this constructs a submit button. For compatibiliy we use a simple
 * input of type submit because the button widget is only available
 * since HTML 4. We may change that in the future, and force everyone
 * to use at least HTML 4. For now it is as it is.
 *
 * Note that the label of the button is retrieved from $value rather than
 * from the label property. We do use the $value as a string possibly containing
 * hotkeys (via prepending a letter with a tilde) and we also set the
 * accesskey to that value. However, it is different from other widgets
 * because an input cannot display underlines (a button can).
 *
 * The properties recognised translate to the following HTML-code/attributes
 * <pre>
 * name      : name
 * value     : value
 * accesskey : accesskey
 * alt       : alt
 * class     : class (also depends on viewonly and errors)
 * tabindex  : tabindex
 * id        : id
 * title     : title
 * viewonly  : disabled AND addition of ATTR_CLASS_ERROR to class list (if viewonly == TRUE)
 * errors    : addition of ATTR_CLASS_ERROR to class list (if errors > 0)
 * </pre>
 *
 * @param array &$item the parameters that describe the dialog input element
 * @param string $name the name of the input element ('fieldname')
 * @param mixed $value the button's label possibly including a tilde indicating hotkey
 * @param string $f_type the type of the field (eg text, number, date, time, ...)
 * @return array|string 1 or more lines of ready-to-use HTML
 */
function dialog_get_widget_submit(&$item,$name,$value) {
        $attributes = array('name' => $name);
        $widget = 'submit';
        $attributes['type'] = $widget;
        $attributes['value'] = $value;
        if (isset($item['alt'])) {
            $attributes['alt'] = $item['alt'];
        }
        $hotkey = accesskey_from_string($value);
        if (!empty($hotkey)) {
            $attributes['accesskey'] = $hotkey;
        } elseif (isset($item['accesskey'])) {
            $attributes['accesskey'] = $item['accesskey'];
        }
        $attributes['value'] = str_replace('~','',$value); // can't show <u>...</u> in value attribute of input widget
        $class = dialog_get_class($item);
        if (!empty($class)) {
            $attributes['class'] = $class;
        }
        if (isset($item['tabindex'])) {
            $attributes['tabindex'] = $item['tabindex'];
        }
        if (isset($item['id'])) {
            $attributes['id'] = $item['id'];
        }
        if (isset($item['title'])) {
            $attributes['title'] = $item['title'];
        }
        if ((isset($item['viewonly'])) && ($item['viewonly'])) {
            $attributes['disabled'] = NULL;
        }
        return html_tag('input',$attributes);
} // dialog_get_widget_submit()


/** construct a checkbox or 1 or more radiobuttons
 *
 * this constructs a checkbox or a list of radiobuttons.
 *
 * Note:
 * because a checkbox and radionbuttons are very similar, they are
 * handled in the same workhorse routine. Maybe we should split this
 * in the name of code clarity. Oh well...
 *
 * If we are generating a checkbox, the result looks something like this:
 * <pre>
 * &ltinput type="checkbox" value="1" checked ...&gt;&lt;label ...&gt;option text&lt;label&gt;
 * </pre>
 *
 * If we are generating radiobuttons, the result looks something like this:
 * <pre>
 * &ltinput type="radio" value="1" checked ...&gt;&lt;label ...&gt;option 1 text&lt;label&gt;
 * &ltinput type="radio" value="2" ...&gt;&lt;label ...&gt;option 2 text&lt;label&gt;
 * &ltinput type="radio" value="3" ...&gt;&lt;label ...&gt;option 3 text&lt;label&gt;
 * </pre>
 *
 * The number of lines in the result depends on the number of items in the
 * options array in $item. In case of a checkbox there should only be one,
 * in case of radiobuttons there should be more than 1.
 *
 * There are two different ways to specify the options. The simple way is to
 * have a single options array with 'value' => 'option text' pairs. In this case
 * the available properties such as title, class and viewonly are copied from
 * the corresonding properties in the $item array,
 *
 * The other way is to have an array of arrays like this:
 * <code>
 * $item['options'] = array('1'=>array('title'=>'...','option'=>'...'),2 => array(...));
 * </code>
 *
 * This allows for setting properties of individual options, e.g. one of the
 * radio buttons could be made viewonly while the others are still selectable.
 *
 * Note that such a non-simple array of arrays doesn't make much sense for
 * a single, simple checkbox.
 *
 * The properties recognised translate to the following HTML-code/attributes
 * <pre>
 * name      : name
 * value     : value AND perhaps 'checked' if value matches option value
 * accesskey : accesskey
 * alt       : alt
 * class     : class (also depends on viewonly and errors)
 * tabindex  : tabindex
 * id        : id
 * title     : title
 * viewonly  : disabled AND addition of ATTR_CLASS_ERROR to class list (if viewonly == TRUE)
 * errors    : addition of ATTR_CLASS_ERROR to class list (if errors > 0)
 * label     : tilde+letter may change the accesskey
 * </pre>
 *
 * Note:
 * In case of radiobuttons, the document-wide unique id is constructed from the specified
 * id by appending an underscore and an indexnumber (except for the first item in te list).
 * This id can be overruled by an id specified in the options array-of-arrays.
 *
 * Even when an item has an explicit accesskey, the access key can be overruled by the
 * the 'hotkey' derived from a tilde+letter combination in the options array, either in
 * the simple case or in case of and array-of-arrays.
 *
 * Note that also the generic title can be overruled by a title that is defined in the
 * options array-of-arrays.
 *
 * @param array &$item the parameters that describe the dialog input element
 * @param string $name the name of the input element ('fieldname')
 * @param mixed $value the (current) value of the input element to show ('field value')
 * @param string $f_type the type of the field (eg text, number, date, time, ...)
 * @return array|string 1 or more lines of ready-to-use HTML
 */
function dialog_get_widget_radiocheckbox(&$item,$name,$value,$f_type) {
        $widget = ($f_type == F_CHECKBOX) ? 'checkbox' : 'radio';
        $a = array();
        if ((isset($item['options'])) && (is_array($item['options'])) && (!empty($item['options']))) {
            $listindex = 0;
            foreach($item['options'] as $option_value => $option) {
                ++$listindex;
                $attributes_input = array('name' => $name, 'type' => $widget);
                $attributes_label = array();
                $attributes_input['value'] = $option_value;
                if ($value == $option_value) {
                    $attributes_input['checked'] = NULL;
                }
                if (isset($item['alt'])) {
                    $attributes_input['alt'] = $item['alt'];
                }
                if (isset($item['tabindex'])) {
                    $attributes_input['tabindex'] = $item['tabindex'];
                }
                if (isset($item['id'])) { // construct unique id for radiobuttons 2,3,4,...
                    $attributes_input['id'] = ($listindex == 1) ? $item['id'] : $item['id'].'_'.$listindex;
                }
                if (isset($item['title'])) {
                    $attributes_input['title'] = $item['title'];
                    $attributes_label['title'] = $item['title'];
                }
                if ((isset($item['viewonly'])) && ($item['viewonly'])) {
                    $attributes_input['disabled'] = NULL;
                }
                $class = dialog_get_class($item);
                if (!empty($class)) {
                    $attributes_input['class'] = $class;
                    $attributes_label['class'] = $class;
                }
                if (is_array($option)) {
                    if (isset($option['id'])) {
                        $attributes_label['id'] = $option['id'];
                    }
                    if (isset($option['class'])) {
                        $attributes_label['class'] = dialog_get_class($item,$option['class']);
                        $attributes_input['class'] = dialog_get_class($item,$option['class']);
                    }
                    if (isset($option['title'])) {
                        $attributes_label['title'] = $option['title'];
                        $attributes_input['title'] = $option['title'];
                    }
                    if (isset($option['tabindex'])) {
                        $attributes_input['tabindex'] = $option['tabindex'];
                    }
                    if ((isset($option['viewonly'])) && ($option['viewonly'])) {
                        $attributes_input['disabled'] = NULL;
                    }
                    $label = (isset($option['option'])) ? $option['option'] : $option_value;
                    $hotkey = accesskey_from_string($label);
                    if (!empty($hotkey)) {
                        $attributes_input['accesskey'] = $hotkey;
                    } elseif (isset($option['accesskey'])) {
                        $attributes_input['accesskey'] = $option['accesskey'];
                    }
                } else {
                    $label = (!empty($option)) ? $option : $option_value;
                    $hotkey = accesskey_from_string($label);
                    if (!empty($hotkey)) {
                        $attributes_input['accesskey'] = $hotkey;
                    } elseif (isset($item['accesskey'])) {
                        $attributes_input['accesskey'] = $item['accesskey'];
                    }
                }
                $a[] = html_tag('input',$attributes_input).
                       html_tag('label',$attributes_label,accesskey_tilde_to_underline($label));
            }
        }
        if (sizeof($a) == 1) {
            return $a[0];
        } else {
            return $a;
        }
} // dialog_get_widget_radiocheckbox()


/** construct a listbox
 *
 * this constructs a listbox
 *
 * The number of lines in the result depends on the number of items in the
 * options array in $item. The result always starts with a SELECT opening tag,
 * followed by N OPTION tags and finally a SELECT closing tag.
 *
 * There are two different ways to specify the options. The simple way is to
 * have a single options array with 'value' => 'option text' pairs. In this case
 * the available properties such as title, class and viewonly are copied from
 * the corresonding generic properties in the $item array,
 *
 * The other way is to have an array of arrays like this:
 * <code>
 * $item['options'] = array('1'=>array('title'=>'...','option'=>'...'),2 => array(...));
 * </code>
 *
 * This allows for setting properties of individual options, e.g. one of the
 * options could be made viewonly while the others are still selectable.
 *
 * The properties recognised translate to the following HTML-code/attributes
 * <pre>
 * name      : name
 * value     : value AND perhaps 'selected' if value matches option value
 * accesskey : accesskey
 * alt       : alt
 * class     : class (also depends on viewonly and errors)
 * tabindex  : tabindex
 * id        : id
 * title     : title
 * viewonly  : disabled AND addition of ATTR_CLASS_ERROR to class list (if viewonly == TRUE)
 * errors    : addition of ATTR_CLASS_ERROR to class list (if errors > 0)
 * label     : tilde+letter may change the accesskey
 * </pre>
 *
 * Note that the options within the SELECT tag are indented 2 spaces for readability.
 *
 * @param array &$item the parameters that describe the dialog input element
 * @param string $name the name of the input element ('fieldname')
 * @param mixed $value the (current) value of the input element to show ('field value')
 * @param string $f_type the type of the field (eg text, number, date, time, ...)
 * @return array 2 or more lines of ready-to-use HTML
 */
function dialog_get_widget_listbox(&$item,$name,$value) {
    // some browsers insist on a tight link between label and select
    if (($item['type'] == F_LISTBOX) && (!isset($item['id']))) {
        $item['id'] = 'id'.strval(get_unique_number()); // assign a unique id for every item
    }

    $attributes = array('name' => $name);
    if ((isset($item['rows'])) && ($item['rows'] > 1)) {
        $attributes['size'] = $item['rows'];
    }
    $options_attributes = array();
    $class = dialog_get_class($item);
    if (!empty($class)) {
        $attributes['class'] = $class;
        $options_attributes['class'] = $class;
    }
    $hotkey = (isset($item['label'])) ? accesskey_from_string($item['label']) : '';
    if (!empty($hotkey)) {
        $attributes['accesskey'] = $hotkey;
    } elseif (isset($option['accesskey'])) {
        $attributes['accesskey'] = $option['accesskey'];
    }
    if (isset($item['tabindex'])) {
        $attributes['tabindex'] = $item['tabindex'];
    }
    if (isset($item['title'])) {
        $attributes['title'] = $item['title'];
        $options_attributes['title'] = $item['title'];
    }
    if (isset($item['id'])) {
        $attributes['id'] = $item['id'];
    }
    if ((isset($item['viewonly'])) && ($item['viewonly'])) {
        $attributes['disabled'] = NULL;
    }
    $a = array(html_tag('select',$attributes));
    if ((isset($item['options'])) && (is_array($item['options'])) && (!empty($item['options']))) {
        foreach($item['options'] as $option_value => $option) {
            $attributes = $options_attributes; // freshly inherited from select tag
            $attributes['value'] = $option_value;
            if ($value == $option_value) {
                $attributes['selected'] = NULL;
            }
            if (is_array($option)) {
                if (isset($option['class'])) {
                    $attributes['class'] = dialog_get_class($item,$option['class']);
                }
                if (isset($option['title'])) {
                    $attributes['title'] = $option['title'];
                }
                if (isset($option['id'])) {
                    $attributes['id'] = $option['id'];
                }
                if ((isset($option['viewonly'])) && ($option['viewonly'])) {
                    $attributes['disabled'] = NULL;
                }
                $label = (isset($option['option'])) ? $option['option'] : $option_value;
            } else {
                $label = (!empty($option)) ? $option : $option_value;
            }
            $a[] = '  '.html_tag('option',$attributes,htmlspecialchars($label));
        }
    }
    $a[] = '</select>';
    return $a;
} // dialog_get_widget_listbox()


/** construct an input field using the user's preferred editor
 *
 * this constructs an input for rich text like a page with HTML-code.
 * Most users will probably have selected the FCKeditor. However,
 * there is also the option to use the so-called 'plain' editor, which
 * is nothing more than a textarea in disguise.
 *
 * The properties recognised translate to the following HTML-code/attributes
 * <pre>
 * name      : name
 * value     : value
 * accesskey : accesskey
 * rows      : rows
 * columns   : cols
 * maxlength : maxlength
 * alt       : alt
 * class     : class (also depends on viewonly and errors)
 * tabindex  : tabindex
 * id        : id
 * title     : title
 * viewonly  : disabled AND addition of ATTR_CLASS_ERROR to class list (if viewonly == TRUE)
 * errors    : addition of ATTR_CLASS_ERROR to class list (if errors > 0)
 * </pre>
 *
 * @param array &$item the parameters that describe the dialog input element
 * @param string $name the name of the input element ('fieldname')
 * @param mixed $value the (current) value of the input element to show ('field value')
 * @return array|string 1 or more lines of ready-to-use HTML
 * @todo if we let the hotkey from the label prevail and add it to the input tag, why add a hotkey to the label too?
 */
function dialog_get_widget_richtextinput(&$item,$name,$value,$f_type) {
        global $CFG, $USER,$LANGUAGE;
        $rows = max((isset($item['rows'])) ? intval($item['rows']) : 4,4); // at least 4 rows, always
        $preferred_editor = (!empty($USER->editor)) ? $USER->editor : $CFG->editor;

        if ($preferred_editor == 'ckeditor') {
            require_once($CFG->progdir.'/lib/ckeditor/ckeditor.php');
            $editor = new CKEditor();
            $editor->basePath = $CFG->progwww_short.'/lib/ckeditor/';
            $editor->returnOutput = TRUE;
            $editor->config['defaultLanguage'] = $LANGUAGE->get_current_language();
            $editor->config['height'] = sprintf("%dem", $rows); // assume 1em per line
            $editor->config['width'] = (isset($item['columns'])) ? 10 * intval($item['columns']) : '100%';
            $editor->config['filebrowserLinkBrowseUrl'] = sprintf('%s/admin.php?job=%s',$CFG->www_short,JOB_FILEBROWSER);
            $editor->config['filebrowserImageBrowseUrl'] = sprintf('%s/admin.php?job=%s',$CFG->www_short,JOB_IMAGEBROWSER);
            $editor->config['filebrowserFlashBrowseUrl'] = sprintf('%s/admin.php?job=%s',$CFG->www_short,JOB_FLASHBROWSER);
            $editor->config['skin'] = 'kama';
            /* Try to set sensible dimensions in case JavaScript is disabled and CKEditor
             * falls back on a plain textarea. We attempt to use the full available width
             * of the element containting this textarea/editor via a style attribute.
             * Note: the default dimensions in CKEditor are: rows=8, cols=60
             * (see /program/lib/ckeditor/ckeditor_phpX.php).
             */
            $editor->textareaAttributes['rows'] = $rows;
            if (isset($item['columns'])) {
                $editor->textareaAttributes['cols'] = $item['columns'];
            }
            $editor->textareaAttributes['style'] = 'width: 100%;';
            return $editor->editor($name,$value);
        } elseif (($preferred_editor == 'fckeditor') && !(isset($_GET['fcksource']))) {
            require_once($CFG->progdir.'/lib/fckeditor/fckeditor.php');
            $editor = new FCKeditor($name);
            if ($editor->IsCompatible()) {
                $editor->Value = $value;
                $editor->BasePath = $CFG->progwww_short.'/lib/fckeditor/';
                $editor->Config['AutoDetectLanguage'] = FALSE;
                $editor->Config['DefaultLanguage'] = $LANGUAGE->get_current_language();
                $editor->Height = 20 * $rows; // heuristic: assume 20px per row
                $editor->Width = (isset($item['columns'])) ? 10 * intval($item['columns']) : '100%';
                $editor->Config['LinkBrowserURL'] = sprintf('%s/admin.php?job=%s',$CFG->www_short,JOB_FILEBROWSER);
                $editor->Config['LinkUpload'] = 'false';
                $editor->Config['ImageBrowserURL'] = sprintf('%s/admin.php?job=%s',$CFG->www_short,JOB_IMAGEBROWSER);
                $editor->Config['ImageUpload'] = 'false';
                $editor->Config['FlashBrowserURL'] = sprintf('%s/admin.php?job=%s',$CFG->www_short,JOB_FLASHBROWSER);
                $editor->Config['FlashUpload'] = 'false';
                $editor->Config['SkinPath'] = $editor->BasePath.'editor/skins/silver/';
                return $editor->CreateHtml(); 
            } // else
                // fall through to plain text editor (the only other option re: preferred editor)
        }
        // Still here? You want the plain editor then (ie. a textarea)
        $attributes = array('name' => $name);
        $widget = 'textarea';
        $attributes['rows'] = $rows;
        if (isset($item['columns'])) {
            $attributes['cols'] = $item['columns'];
        }
        $hotkey = (isset($item['label'])) ? accesskey_from_string($item['label']) : '';
        if (!empty($hotkey)) {
            $attributes['accesskey'] = $hotkey;
        } elseif (isset($item['accesskey'])) {
            $attributes['accesskey'] = $item['accesskey'];
        }
        $class = dialog_get_class($item);
        if (!empty($class)) {
            $attributes['class'] = $class;
        }
        if (isset($item['tabindex'])) {
            $attributes['tabindex'] = $item['tabindex'];
        }
        if (isset($item['id'])) {
            $attributes['id'] = $item['id'];
        }
        if (isset($item['title'])) { // see todo #1 above
            $attributes['title'] = $item['title'];
        }
        if ((isset($item['viewonly'])) && ($item['viewonly'])) {
            $attributes['disabled'] = NULL;
        }
        $attributes['style'] = 'width: 100%;';
        return html_tag($widget,$attributes,htmlspecialchars($value));
} // dialog_get_widget_richtextinput()


/** construct an input field for file upload
 *
 * this constructs an input widget for uploading files. This usually includes
 * a button to browse the user's local file system (depends on browser).
 *
 * Note that it is NOT possible to 'preload' a value in this input field; any
 * predefined value is ignored by the browser. As a workaround we _could_
 * show the $value to the user using an additional comment, e.g. by adding
 * it to the label or something. For now, we simply do nothing with the value.
 *
 * The properties recognised translate to the following HTML-code/attributes
 * <pre>
 * name      : name
 * value     : value (see note above)
 * accesskey : accesskey
 * columns   : cols (textarea) or size (input type="text")
 * alt       : alt
 * class     : class (also depends on viewonly and errors)
 * tabindex  : tabindex
 * id        : id
 * title     : title
 * viewonly  : disabled AND addition of ATTR_CLASS_ERROR to class list (if viewonly == TRUE)
 * errors    : addition of ATTR_CLASS_ERROR to class list (if errors > 0)
 * </pre>
 *
 * @param array &$item the parameters that describe the dialog input element
 * @param string $name the name of the input element ('fieldname')
 * @param mixed $value the (current) value of the input element to show ('field value')
 * @return array|string one or more lines of ready-to-use HTML
 * @todo if we let the hotkey from the label prevail and add it to the input tag, why add a hotkey to the label too?
 * @todo should we do something with an um-empty $value? If so, waht? The browser ignores this...
 */
function dialog_get_widget_file(&$item,$name,$value) {
        $attributes = array('name' => $name);
        $attributes['type'] = 'file';
        $attributes['value'] = $value; // see note above
        if (isset($item['columns'])) {
            $attributes['size'] = $item['columns'];
        }
        if (isset($item['alt'])) {
            $attributes['alt'] = $item['alt'];
        }
        $hotkey = (isset($item['label'])) ? accesskey_from_string($item['label']) : '';
        if (!empty($hotkey)) {
            $attributes['accesskey'] = $hotkey;
        } elseif (isset($item['accesskey'])) {
            $attributes['accesskey'] = $item['accesskey'];
        }
        $class = dialog_get_class($item);
        if (!empty($class)) {
            $attributes['class'] = $class;
        }
        if (isset($item['tabindex'])) {
            $attributes['tabindex'] = $item['tabindex'];
        }
        if (isset($item['id'])) {
            $attributes['id'] = $item['id'];
        }
        if (isset($item['title'])) { // see todo #1 above
            $attributes['title'] = $item['title'];
        }
        if ((isset($item['viewonly'])) && ($item['viewonly'])) {
            $attributes['disabled'] = NULL;
        }
        return html_tag('input',$attributes);
} // dialog_get_widget_file()


// ==================================================================
// ======================== UTILITY ROUTINES ========================
// ==================================================================


/** shortcut for generating a dialogdef for a button
 *
 * this constructs an array describing a button. The button definition
 * is bare bones but it includes name, class, value and optionally a title.
 * If no $value is specified, a translated value is retrieved for the button.
 * If no $title is specified, a title indicating the hotkey is constructed.
 * This more or less works around the problem that hotkeys cannot be visualised
 * in an input type="submit" button (It is possible in a button tag, but we don't
 * use that because it requires HTML 4. Maybe later...)
 *
 * @param string $button_type one of the predefined button constants, e.g. BUTTON_OK
 * @param string $value the label used for display including hotkey, eg. '~Yes' or '~Cancel'
 * @param string $title the text displayed via a mouseover
 * @return array ready-to-use $item for a dialog
 */
function dialog_buttondef($button_type,$value='',$title='') {
    $button = array('type' => F_SUBMIT);
    switch($button_type) {
    case BUTTON_OK:
        $button['name'] = 'button_ok';
        $button['class'] = 'button_ok';
        $button['value'] = t('button_ok');
        break;
    case BUTTON_CANCEL:
        $button['name'] = 'button_cancel';
        $button['class'] = 'button_cancel';
        $button['value'] = t('button_cancel');
        break;
    case BUTTON_SAVE:
        $button['name'] = 'button_save';
        $button['class'] = 'button_save';
        $button['value'] = t('button_save');
        break;
    case BUTTON_DELETE:
        $button['name'] = 'button_delete';
        $button['class'] = 'button_delete';
        $button['value'] = t('button_delete');
        break;
    case BUTTON_YES:
        $button['name'] = 'button_yes';
        $button['class'] = 'button_yes';
        $button['value'] = t('button_yes');
        break;
    case BUTTON_NO:
        $button['name'] = 'button_no';
        $button['class'] = 'button_no';
        $button['value'] = t('button_no');
        break;
    case BUTTON_GO:
        $button['name'] = 'button_go';
        $button['class'] = 'button_go';
        $button['value'] = t('button_go');
        break;
    default:
        $button['name'] = 'button_ok';
        $button['class'] = 'button_ok';
        $button['value'] = t('button_ok');
        break;
    }
    if (!empty($value)) {
        $button['value'] = $value;
    }
    if (!empty($title)) {
        $button['title'] = $title;
    } else {
        $hotkey = accesskey_from_string($button['value']);
        if (!empty($hotkey)) {
            $button['title'] = t('hotkey_for_button','',array('{HOTKEY}' => $hotkey));
        }
    }
    return $button;
} // dialog_buttondef()


/** construct a space-delimited list of classes that apply to this item
 *
 * this constructs a string with applicable classes for this element.
 * if the item has validation errors, the class ATTR_CLASS_ERROR
 * is added, if the item is viewonly, the class ATTR_CLASS_VIEWONLY is
 * added. This allows for the CSS to change the style depending on these
 * situations.
 * 
 * @param array &$item the parameters that describe the dialog input element
 * @param string $class class to start with, otherwise use $item['class']
 * @return string a space-delimited list of applicable classes
 */
function dialog_get_class(&$item,$class=NULL) {
    if ($class !== NULL) {
        $glue = (empty($class)) ? '' : ' ';
    } elseif ((isset($item['class'])) && (!empty($item['class']))){
        $class = $item['class'];
        $glue = ' ';
    } else {
        $class = '';
        $glue = '';
    }
    if ((isset($item['errors'])) && ($item['errors'] > 0)) {
        $class .= $glue.ATTR_CLASS_ERROR;
        $glue = ' ';
    }
    if ((isset($item['viewonly'])) && ($item['viewonly'])) {
        $class .= $glue.ATTR_CLASS_VIEWONLY;
    }
    return $class;
} // dialog_get_class()


/** replace tilde+character with emphasised character to indicate accesskey
 *
 * this replaces the combination of a tilde and the character following
 * it with $tag_open followed by the character followed by $tag_close.
 *
 * Example:
 * accesskey_tilde_to_underline("~Username") yields "<u>U</u>sername"
 * accesskey_tilde_to_underline("Pass~word",'<b>','</b>') yields "Pass<b>w</b>ord"
 * accesskey_tilde_to_underline("~Role",'','') yields "Role"
 *
 * Note that we only accept ASCII here: if a tilde is followed by a non-ASCII-character
 * (e.g. the first byte of a multibyte UTF-8 sequence) we silently ignore and still 'eat' the tilde.
 *
 * @param string $string the string to process
 * @param string $tag_open the tag that starts the emphasis
 * @param string $tag_close the tag that ends the emphasis
 * @return string the processed string
 */
function accesskey_tilde_to_underline($string,$tag_open='<u>',$tag_close='</u>') { 
    $target = '';
    while (($pos = strpos($string,'~')) !== FALSE) {
        $target .= substr($string,0,$pos);
        $hotkey = substr($string,$pos+1,1);
        if ((!empty($hotkey)) && ((ord($hotkey) & 0x80) == 0x00)) { // MUST be ASCII; skip any multibyte UTF-8 sequence
            $target .= $tag_open.$hotkey.$tag_close;
            $string = substr($string,$pos+2); // eat both the tilde and the ASCII-character
        } else {
            $string = substr($string,$pos+1); // only eat the tilde but leave the first byte of the UTF-8  sequence
        }
    }
    return $target.$string;
} // accesskey_tilde_to_underline()


/** return the ASCII-character that follows the first tilde in a string
 *
 * this returns the ASCII-character that follows the first tilde in the
 * string (if any). This is the character that could be added as
 * an accesskey to some HTML tag, e.g. a label or an input.
 *
 * Note that a tilde followed by a UTF-8 sequence of 2 or more bytes
 * does NOT yield a hotkey at all but an empty string instead.
 *
 * @param string $string the string to process
 * @return string the hotkey character or an empty string
 */
function accesskey_from_string($string) {
    $hotkey = '';
    $pos = strpos($string,'~');
    if ($pos !== FALSE) {
        $c = substr($string,$pos+1,1);
        $hotkey = ((ord($c) & 0x80) == 0x00) ? $c : '';
    }
    return $hotkey;
} // accesskey_from_string()


/** check validity of date, time or datetime
 *
 * this checks the validity of dates and times. If all tests are passed
 * successfully, the input value is reformatted in the standard format
 * corresponding with that field type:
 *
 *  - F_DATE becomes yyyy-mm-dd (with leading zeros for month or day where applicable)
 *  - F_TIME becomes hh:mm:ss (with leading zeros when applicable)
 *  - F_DATETIME is combination of F_DATE and F_TIME glued together with a space: yyyy-mm-dd hh:mm:ss
 *
 * Valid values for dates are within the range 0000-01-01 ... 9999-12-31 (but note that the year
 * is always displayed with 4 digits). This routine takes leap years into account the same way
 * the standard function checkdate() does.
 *
 * Valid values for times are between 00:00:00 and 23:59:59. Note that we don't deal with leap seconds
 * or other fancy stuff (this is not rocket science): KISS. Usually we only need times to determine
 * an embargo date/time anyway.
 *
 * Also, this routine doesn't know about time zones and daylight savings time.
 *
 * @param string $f_type indicates the field type we are expecting, can be F_DATE, F_TIME or F_DATETIME
 * @param string $input the string that needs to be checked
 * @param string &$output if the input is valid, this contains a properly formatted value
 * @return bool TRUE if the input was valid, FALSE otherwise
 */
function valid_datetime($f_type,$input,&$output) {
    $num = array(0,0,0,0,0,0);
    $inside = FALSE;
    $index = 0;
    $n = strlen($input);
    for ($i = 0; $i < $n; ++$i) {
        $x = ord(substr($input,$i,1)) - ord('0');
        if ($inside) {
            if ((0 <= $x) && ($x <= 9)) {
                $num[$index] = 10 * $num[$index] + $x;
            } else {
                $inside = FALSE;
                ++$index;
            }
        } else {
            if ((0 <= $x) && ($x <= 9)) {
                $num[$index] = $x;
                $inside = TRUE;
            }
        }
    }
    // At this point we have all the numbers from $input in an array
    $retval = FALSE;  // assume the worst and...
    $output = $input; // ...prepare to return the user's original input
    switch ($f_type) {
    case F_DATE:
        // year MUST be at most 4 digits because otherwise we lose the easy comparison feature of two datetimes
        // (string compare says "10999-01-01" < "9999-12-31" which is not what we want)
        if ((checkdate($num[1],$num[2],$num[0])) && ($num[0] < 10000)) {
            $output = sprintf("%04d-%02d-%02d",$num[0],$num[1],$num[2]);
            $retval = TRUE;
        }
        break;
    case F_TIME:
        if (($num[0] < 24) && ($num[1] < 60) && ($num[2] < 60)) {
            $output = sprintf("%02d:%02d:%02d",$num[0],$num[1],$num[2]);
            $retval = TRUE;
        }
        break;
    case F_DATETIME:
        // year MUST be at most 4 digits because otherwise we lose the easy comparison feature of two datetimes
        // (string compare says "10999-01-01" < "9999-12-31" which is not what we want)
        if ((checkdate($num[1],$num[2],$num[0])) && ($num[0] < 10000) &&
            (($num[3] < 24) && ($num[4] < 60) && ($num[5] < 60))) {
            $output = sprintf("%04d-%02d-%02d %02d:%02d:%02d",$num[0],$num[1],$num[2],$num[3],$num[4],$num[5]);
            $retval = TRUE;
        }
        break;
    }
    return $retval;
} // valid_datetime()

?>