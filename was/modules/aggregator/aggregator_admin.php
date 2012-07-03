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

/** /program/modules/aggregator/aggregator_admin.php - management interface for aggregator-module
 *
 * This file defines the administrative interface to this module.
 * The interface consists of the following four functions.
 *
 * <code>
 * aggregator_disconnect(&$output,$area_id,$node_id,$module)
 * aggregator_connect(&$output,$area_id,$node_id,$module)
 * aggregator_show_edit(&$output,$area_id,$node_id,$module,$viewonly,$edit_again,$href)
 * aggregator_save(&$output,$area_id,$node_id,$module,$viewonly,&$edit_again)
 * </code>
 *
 * These functions are called from pagemanagerlib.php whenever necessary.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_aggregator
 * @version $Id: aggregator_admin.php,v 1.2 2012/07/03 20:34:35 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** disconnect this module from a node
 *
 * this breaks the link between the node $node_id in area $area_id and this module.
 * For now we simply delete the record with the aggregator configuration data. 
 * 
 * @param object &$output collects the html output (if any)
 * @param int $area_id the area in which $node_id resides
 * @param int $node_id the node from which we need to disconnect
 * @param array $module the module record straight from the database
 * @return bool TRUE on success, FALSE otherwise
 */
function aggregator_disconnect(&$output,$area_id,$node_id,$module) {
    $where = array('node_id' => intval($node_id));
    $retval = db_delete('aggregator',$where);
    return ($retval === FALSE) ? FALSE : TRUE;
} // aggregator_disconnect()


/** connect this module to a node
 *
 * this makes the link between the node $node_id in area $area_id and this module.
 * In this case we simply link a single record to node $node_id in a
 * 1-to-1 relation.
 *
 * Note that we set the parameters to more or less reasonable values.
 * It is up to the user to configure the aggregator with appropriate settings.
 * 
 * @param object &$output collects the html output (if any)
 * @param int $area_id the area in which $node_id resides
 * @param int $node_id the node to which we need to connect
 * @param array $module the module record straight from the database
 * @return bool TRUE on success, FALSE otherwise
 */
function aggregator_connect(&$output,$area_id,$node_id,$module) {
    global $USER;
    $now = strftime('%Y-%m-%d %T');
    $fields = array(
        'node_id'            => intval($node_id),
        'header'             => '',
        'introduction'       => '',
        'node_list'          => '',
        'items'              => 10,
        'reverse_order'      => FALSE,
        'htmlpage_length'    => 2,
        'snapshots_width'    => 512,
        'snapshots_height'   => 120,
        'snapshots_visible'  => 3,
        'snapshots_showtime' => 5,
        'ctime'              => $now,
        'cuser_id'           => $USER->user_id,
        'mtime'              => $now,
        'muser_id'           => $USER->user_id);
    $retval = db_insert_into('aggregator',$fields);
    if ($retval !== 1) {
        logger(sprintf('%s(): cannot connect aggregator to node \'%d\': %s',__FUNCTION__,$node_id,db_errormessage()));
        $retval = FALSE;
    } else {
        $retval = TRUE;
    }
    return $retval;
} // aggregator_connect()


/** present the user with a dialog to modify the aggregator that are connected to node $node_id
 *
 * this prepares a dialog for the user filled with existing data (if any), possibly allowing
 * the user to modify the content. If the flag $viewonly is TRUE, this routine should only
 * display the content rather than let the user edit it. If the flag $edit_again is TRUE,
 * the routine should use the data available in the $_POST array, otherwise it should read
 * the data from the database (or wherever the data comes from). The parameter $href is the
 * place where the form should be POST'ed.
 *
 * The dialog should be added to the $output object. Useful routines are:
 * <code>
 * $output->add_content($content): add $content to the content area
 * $output->add_message($message): add $message to the message area (feedback to the user)
 * $output->add_popup_bottom($message): make $message popup in the browser after loading the page (uses javascript)
 * $output->add_popup_top($message): make $message popup in the browser before loading the page (uses javascript)
 * </code>
 * 
 * @param object &$output collects the html output (if any)
 * @param int $area_id the area in which $node_id resides
 * @param int $node_id the node to which this module is connected
 * @param array $module the module record straight from the database
 * @param bool $viewonly if TRUE, editing is not allowed (but simply showing the content is allowed)
 * @param bool $edit_again if TRUE start with data from $_POST, else use data from database
 * @param string $href the action property of the HTML-form, the place where data will be POST'ed
 * @return bool TRUE on success + output stored via $output, FALSE otherwise
 */
function aggregator_show_edit(&$output,$area_id,$node_id,$module,$viewonly,$edit_again,$href) {
    $dialogdef = aggregator_get_dialogdef($viewonly);
    if ($edit_again) {
        // retrieve and validate the POSTed values
        dialog_validate($dialogdef); // no need to show messages; we did that alread in aggregator_save()
        aggregator_check_node_list($dialogdef['node_list'],$area_id,$node_id);
    } else {
        // make a fresh start with data from the database
        $where = array('node_id' => intval($node_id));
        if (($record = db_select_single_record('aggregator','*',$where)) === FALSE) {
            logger(sprintf('%s(): error retrieving aggregator configuration: %s',__FUNCTION__,db_errormessage()));
        } else {
            foreach($dialogdef as $name => $item) {
                switch($name) {
                    case 'header':
                    case 'introduction':
                    case 'node_list':
                    case 'items':
                    case 'reverse_order':
                    case 'htmlpage_length':
                    case 'snapshots_width':
                    case 'snapshots_height':
                    case 'snapshots_visible':
                    case 'snapshots_showtime':
                        $dialogdef[$name]['value'] = strval($record[$name]);
                        break;
                }
            }
        }
    }
    $output->add_content('<h2>'.t('aggregator_content_header','m_aggregator').'</h2>');
    $output->add_content(t('aggregator_content_explanation','m_aggregator'));
    $output->add_content(dialog_quickform($href,$dialogdef));
    return TRUE;
} // aggregator_show_edit()


/** save the modified content data of this module linked to node $node_id
 *
 * this validates and saves the data that was submitted by the user.
 * If validation fails, or storing the data doesn't work, the flag $edit_again
 * is set to TRUE and the return value is FALSE.
 *
 * If the user has cancelled the operation, the flag $edit_again is set to FALSE
 * and the return value is also FALSE.
 *
 * If the modified data is stored successfully, the return value is TRUE (and
 * the value of $edit_again is a don't care).
 *
 * Here is a summary of return values.
 *
 *  - retval = TRUE ==> data saved successfully
 *  - retval = FALSE && edit_again = TRUE ==> re-edit the data, show the edit dialog again
 *  - retval = FALSE && edit_again = FALSE ==> cancelled, do nothing
 *
 * @param object &$output collects the html output (if any)
 * @param int $area_id the area in which $node_id resides
 * @param int $node_id the node to which the content is connected
 * @param array $module the module record straight from the database
 * @param bool $viewonly if TRUE, editing and hence saving is not allowed
 * @param bool &$edit_again set to TRUE if we need to edit the content again, FALSE otherwise
 * @return bool TRUE on success + output stored via $output, FALSE otherwise
 */
function aggregator_save(&$output,$area_id,$node_id,$module,$viewonly,&$edit_again) {
    global $USER;
global $CFG;
$CFG->debug=TRUE;
error_reporting(-1);

    // 1 -- bail out if cancelled or viewonly
    if ((isset($_POST['button_cancel'])) || ($viewonly)) {
        $edit_again = FALSE;
        return FALSE;
    }

    // 2 -- redo if invalid data was submitted
    $invalid = FALSE;
    $dialogdef = aggregator_get_dialogdef($viewonly);
    if (!dialog_validate($dialogdef)) {
        $invalid = TRUE;
    }
    if (!aggregator_check_node_list($dialogdef['node_list'],$area_id,$node_id)) {
        $invalid = TRUE;
    }

    // show errors to the user and redo
    if ($invalid) {
        foreach($dialogdef as $k => $item) {
            if ((isset($item['errors'])) && ($item['errors'] > 0)) {
                $output->add_message($item['error_messages']);
            }
        }
        $edit_again = TRUE;
        return FALSE;
    }

    // 3 -- actually save the new setting
    $now = strftime('%Y-%m-%d %T');
    $table = 'aggregator';
    $fields = array(
        'header'             => $dialogdef['header']['value'],
        'introduction'       => $dialogdef['introduction']['value'],
        'node_list'          => $dialogdef['node_list']['value'],
        'items'              => intval($dialogdef['items']['value']),
        'reverse_order'      => ($dialogdef['reverse_order']['value'] == 1) ? TRUE : FALSE,
        'htmlpage_length'    => intval($dialogdef['htmlpage_length']['value']),
        'snapshots_width'    => intval($dialogdef['snapshots_width']['value']),
        'snapshots_height'   => intval($dialogdef['snapshots_height']['value']),
        'snapshots_visible'  => intval($dialogdef['snapshots_visible']['value']),
        'snapshots_showtime' => intval($dialogdef['snapshots_showtime']['value']),
        'mtime'              => $now,
        'muser_id'           => $USER->user_id);
    $where = array('node_id' => intval($node_id));
    if (db_update($table,$fields,$where) === FALSE) {
        logger(sprintf('%s(): error saving config value: %s',__FUNCTION__,db_errormessage()));
        $edit_again = TRUE;
        return FALSE;
    } else {
        return TRUE; // $edit_again is a don't care
    }
} // aggregator_save()


/** construct a dialog definition for the aggregator configuration data
 *
 * @param int $viewonly if TRUE the Save button is not displayed and config values cannot be changed
 * @return array dialog definition
 */
function aggregator_get_dialogdef($viewonly) {
    $dialogdef = array(
        'header' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'header',
            'minlength' => 0,
            'maxlength' => 240,
            'columns' => 30,
            'viewonly' => $viewonly,
            'label' => t('header_label','m_aggregator'),
            'title' => t('header_title','m_aggregator'),
            'value' => '',
            ),
        'introduction' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'introduction',
            'minlength' => 0,
            'maxlength' => 32768, // arbitrary; 32 kB
            'columns' => 50,
            'rows' => 10,
            'viewonly' => $viewonly,

            'label' => t('introduction_label','m_aggregator'),
            'title' => t('introduction_title','m_aggregator'),
            'value' => '',
            ),
        'node_list' => array(
            'type' => F_ALPHANUMERIC,
            'name' => 'node_list',
            'minlength' => 0,
            'maxlength' => 240,
            'columns' => 30,
            'viewonly' => $viewonly,
            'label' => t('node_list_label','m_aggregator'),
            'title' => t('node_list_title','m_aggregator'),
            'value' => '',
            ),
        'items' => array(
            'type' => F_INTEGER,
            'name' => 'items',
            'columns' => 10,
            'maxlength' => 10,
            'minvalue' => 0,
            'maxvalue' => 99, // arbitrary but seems a sane limit for the # of items
            'viewonly' => $viewonly,
            'title' => t('items_title','m_aggregator'),
            'label' => t('items_label','m_aggregator')
            ),
        'reverse_order' => array(
            'type' => F_CHECKBOX,
            'name' => 'reverse_order',
            'options' => array(1 => t('reverse_order_check','m_aggregator')),
            'viewonly' => $viewonly,
            'title' => t('reverse_order_title','m_aggregator'),
            'label' => t('reverse_order_label','m_aggregator')
            ),
        'htmlpage_length' => array(
            'type' => F_INTEGER,
            'name' => 'htmlpage_length',
            'columns' => 10,
            'maxlength' => 10,
            'minvalue' => 0,
            'maxvalue' => 99, // arbitrary but seems a sane limit for the # of paragraphs
            'viewonly' => $viewonly,
            'title' => t('htmlpage_length_title','m_aggregator'),
            'label' => t('htmlpage_length_label','m_aggregator')
            ),
        'snapshots_width' => array(
            'type' => F_INTEGER,
            'name' => 'snapshots_width',
            'columns' => 10,
            'maxlength' => 10,
            'minvalue' => 16,
            'maxvalue' => 9999, // arbitrary but seems a sane limit for a screen width
            'viewonly' => $viewonly,
            'title' => t('snapshots_width_title','m_aggregator'),
            'label' => t('snapshots_width_label','m_aggregator')
            ),
        'snapshots_height' => array(
            'type' => F_INTEGER,
            'name' => 'snapshots_height',
            'columns' => 10,
            'maxlength' => 10,
            'minvalue' => 16,
            'maxvalue' => 9999, // arbitrary but seems a sane limit for a screen height
            'viewonly' => $viewonly,
            'title' => t('snapshots_height_title','m_aggregator'),
            'label' => t('snapshots_height_label','m_aggregator')
            ),
        'snapshots_visible' => array(
            'type' => F_INTEGER,
            'name' => 'snapshots_visible',
            'columns' => 10,
            'maxlength' => 10,
            'minvalue' => 1,
            'maxvalue' => 32, // arbitrary but seems a sane limit for the # of visible images
            'viewonly' => $viewonly,
            'title' => t('snapshots_visible_title','m_aggregator'),
            'label' => t('snapshots_visible_label','m_aggregator')
            ),
        'snapshots_showtime' => array(
            'type' => F_INTEGER,
            'name' => 'snapshots_showtime',
            'columns' => 10,
            'maxlength' => 10,
            'minvalue' => 1,
            'maxvalue' => 3600, // arbitrary but 3600s (1h) seems a sane limit for delay between images
            'viewonly' => $viewonly,
            'title' => t('snapshots_showtime_title','m_aggregator'),
            'label' => t('snapshots_showtime_label','m_aggregator')
            )
        );
    if (!$viewonly) {
        $dialogdef['button_save'] = dialog_buttondef(BUTTON_SAVE);
    }
    $dialogdef['button_cancel'] = dialog_buttondef(BUTTON_CANCEL);
    return $dialogdef;
} // aggregator_get_dialogdef()


/** validate and massage the user-supplied node list
 *
 * this checks the node list the user entered,
 * returns TRUE if the tests are passed. Currently the only
 * test is checking the node numbers are >= 1.
 * 
 * @param array &$item holds the field definition from the $dialogdef for the aggregator_path
 * @param int $area_id the area in which we are editing a snapshot module configuration
 * @param int $node_id the node to which the snapshot module is connected (unused)
 * @return bool TRUE if valid node list, otherwise FALSE + messages in dialogdef
 * @todo perhaps we should check more thoroughly for node existence but that implies also
 *       checking for user access etc. etc. We postpone that to later when the visitor's
 *       credentials will be checked against the list of nodes. So: a syntax check only. Sort of.
 */
function aggregator_check_node_list(&$item,$area_id,$node_id) {
    $nodes = explode(',',trim($item['value'],', '));
    $value = '';
    $glue = '';
    $retval = TRUE; // assume success
    foreach ($nodes as $k => $v) {
        $v = intval($v);
        if ($v> 0) {
            $value .= $glue.strval($v);
            $glue = ',';
        } else {
            ++$item['errors'];
            $fname = str_replace('~','',$item['label']);
            $params = array('{FIELD}' => $fname, '{VALUE}' => strval($v));
            $item['error_messages'][] = t('invalid_node','m_aggregator',$params);
            $retval = FALSE;
        }
    }
    if ($retval) {
        $item['value'] = $value;
    }
    return $retval;
} // aggregator_check_node_list()

?>