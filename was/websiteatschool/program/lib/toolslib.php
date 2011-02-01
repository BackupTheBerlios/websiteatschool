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

/** /program/lib/toolslib.php - tools
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.org/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: toolslib.php,v 1.1 2011/02/01 13:00:50 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/* This is the list of recognised tasks in Tools */

define('TASK_TOOLS_INTRO','intro');
define('TASK_TRANSLATETOOL','translatetool');
define('TASK_BACKUPTOOL','backuptool');
define('TASK_LOGVIEW','logview');


/** main entry point for tools (called from /program/main_admin.php)
 *
 * this routine dispatches the tasks, If the specified task
 * is not recognised, the default task TASK_TOOLS_INTRO
 * is executed.
 *
 * @param object &$output collects the html output
 * @return void results are returned as output in $output
 * @todo fix permissions for backup tool! perhaps another bit?
 */
function job_tools(&$output) {
    global $CFG,$WAS_SCRIPT_NAME,$USER;
    $output->set_helptopic('tools');
    $task = get_parameter_string('task',TASK_TOOLS_INTRO);
    switch($task) {
    case TASK_TOOLS_INTRO:
        show_tools_intro($output);
        show_tools_menu($output);
        break;

    case TASK_TRANSLATETOOL:
        if ($USER->has_job_permissions(JOB_PERMISSION_TRANSLATETOOL)) {
            include($CFG->progdir.'/lib/translatetool.class.php');
            $mgr = new TranslateTool($output);
            if ($mgr->show_parent_menu()) {
                show_tools_menu($output,$task);
            }
        } else {
            $output->add_content("<h2>".t('access_denied','admin')."</h2>");
            $output->add_content(t('job_access_denied','admin'));
            $output->add_message(t('job_access_denied','admin'));
            show_tools_menu($output,$task);
        }
        break;

    case TASK_BACKUPTOOL:
        if ($USER->has_job_permissions(JOB_PERMISSION_BACKUPTOOL)) {
            task_backuptool($output);
        } else {
            $output->add_content("<h2>".t('access_denied','admin')."</h2>");
            $output->add_content(t('job_access_denied','admin'));
            $output->add_message(t('job_access_denied','admin'));
            show_tools_menu($output,$task);
        }
        break;

    case TASK_LOGVIEW:
        if ($USER->has_job_permissions(JOB_PERMISSION_LOGVIEW)) {
            task_logview($output);
        } else {
            $output->add_content("<h2>".t('access_denied','admin')."</h2>");
            $output->add_content(t('job_access_denied','admin'));
            $output->add_message(t('job_access_denied','admin'));
            show_tools_menu($output,$task);
        }
        break;

    default:
        if (strlen($task) > 50) {
            $s = substr($task,0,44).' (...)';
        } else {
            $s = $task;
        }
        $message = t('task_unknown','admin',array('{TASK}' => htmlspecialchars($s)));
        $output->add_message($message);
        logger('tools: unknown task: '.htmlspecialchars($s));
        show_tools_intro($output);
        show_tools_menu($output);
        break;
    }
} // job_tools()


/** display an introductory text for tools + menu
 *
 * @param object &$output collects the html output
 * @return void results are returned as output in $output
 */
function show_tools_intro(&$output) {
        $output->add_content('<h2>'.t('tools_header','admin').'</h2>');
        $output->add_content(t('tools_intro','admin'));
} // task_tools_intro()


/** display the tools menu
 *
 * @param object &$output collects the html output
 * @param string $current_task indicate the current menu selection (if any)
 * @return void results are returned as output in $output
 */
function show_tools_menu(&$output,$current_task=NULL) {
    global $WAS_SCRIPT_NAME,$USER;
    $menu_items = array(
        array(
            'task' => TASK_TRANSLATETOOL,
            'anchor' => t('menu_translatetool','admin'),
            'title' => t('menu_translatetool_title','admin'),
            'permission' => JOB_PERMISSION_TRANSLATETOOL
        ),
        array(
            'task' => TASK_BACKUPTOOL,
            'anchor' => t('menu_backuptool','admin'),
            'title' => t('menu_backuptool_title','admin'),
            'permission' => JOB_PERMISSION_BACKUPTOOL
        ),
        array(
            'task' => TASK_LOGVIEW,
            'anchor' => t('menu_logview','admin'),
            'title' => t('menu_logview_title','admin'),
            'permission' => JOB_PERMISSION_LOGVIEW
        )

    );
    $output->add_menu('<h2>'.t('menu','admin').'</h2>');
    $output->add_menu('<ul>');
    foreach($menu_items as $item) {
        $parameters = array('job' => JOB_TOOLS, 'task' => $item['task']);
        $attributes = array('title' => $item['title']);
        if (!($USER->has_job_permissions($item['permission']))) {
            $attributes['class'] = ($current_task == $item['task']) ? 'dimmed current' : 'dimmed';
        } elseif ($current_task == $item['task']) {
            $attributes['class'] = 'current';
        }
        $output->add_menu('  <li>'.html_a($WAS_SCRIPT_NAME,$parameters,$attributes,$item['anchor']));
    }

    // Updater is located in a separate file and called automagically
    // whenever there is a version mismatch. However, we also provide
    // a 'manual' entrance here. 2010-12-20/PF
    $parameters = array('job' => JOB_UPDATE);
    $attributes = array('title' => t('menu_update_title','admin'));
    if (!($USER->has_job_permissions(JOB_PERMISSION_UPDATE))) {
        $attributes['class'] = 'dimmed';
    }
    $output->add_menu('  <li>'.html_a($WAS_SCRIPT_NAME,$parameters,$attributes,t('menu_update','admin')));
    $output->add_menu('</ul>');
} // show_tools_menu()


/** show an introductory text for backup tool OR stream a ZIP-file to the browser
 *
 * If we arrive here via the tools menu, the parameter download is not set.
 * In that case we show an introductory text with a link that yields a ZIP-file
 * with the backup.
 *
 * If the user follows the download link, we arrive here too but with the download
 * parameter set. We then dump the database in a variable and subsequently 
 * compress it in a ZIP-file which we stream to the browser. We do code some
 * things in the basename of the backup:
 *  - the hostname
 *  - the database name
 *  - the database prefix
 *  - the date and the time
 * which should be enough to distinguish nearly all backups if you happen to have a
 * lot of different ones. Note that the URL is also encoded as a comment in the .ZIP.
 *
 * The parameter download is currently set to 'zip'. However, we do attempt to send the
 * plain uncompressed data if that parameter is set to 'sql' (quick and dirty). Oh well.
 * hopefully there is enough memory to accomodate backups of moderate sized sites.
 *
 * Note that we need space to compress the data; a informal test yielded that we need
 * about 160% of the uncompressed size of the backup (tested with a small testset).
 * Rule of the thumb for memory: the more the merrier but at least twice the size of the
 * uncompressed backup.
 *
 * @param object &$output collects output to show to user
 * @return output displayed via $output OR ZIP-file streamed to the browser
 */
function task_backuptool(&$output) {
    global $DB,$CFG,$WAS_SCRIPT_NAME;
    $download = get_parameter_string('download',NULL);
    if (is_null($download)){
        $output->add_content("<h2>".t('backuptool_header','admin')."</h2>");
        $params = array('{DATADIRECTORY}' => htmlspecialchars($CFG->datadir));
        $output->add_content(t('backuptool_intro','admin',$params));
        $output->add_content('<p>');
        $parameters = array('job' => JOB_TOOLS, 'task' => TASK_BACKUPTOOL, 'download' => 'zip');
        $attributes = array('title' => t('backuptool_download_title','admin'));
        $anchor = t('backuptool_download','admin');
        $output->add_content(html_a($WAS_SCRIPT_NAME,$parameters,$attributes,$anchor));
        show_tools_menu($output,TASK_BACKUPTOOL);
        return;
    }
    // Try to construct a suitable hostname as part of the filename.
    $host = '';
    if (($url = parse_url($CFG->www)) !== FALSE) {
        $host = (isset($url['host'])) ? trim(ereg_replace('[^a-zA-Z0-9.-]','',$url['host']),'.') : '';
    }
    if ((empty($host)) && (isset($_SERVER['SERVER_NAME']))) {
        $host = trim(ereg_replace('[^a-zA-Z0-9.-]','',$_SERVER['SERVER_NAME']),'.');
    }
    if (empty($host)) {
        $host = 'localhost'; // last resort
    }
    // basename = host "-" database "-" prefix "-" timestamp (only acceptable chars for filename; strip fancy chars)
    $basename = $host.
                '-'.trim(ereg_replace('[^a-zA-Z0-9._-]','',$CFG->db_name),'-_.').
                '-'.trim(ereg_replace('[^a-zA-Z0-9._-]','',$CFG->prefix ),'-_.').
                '-'.strftime('%Y%m%d-%H%M%S');
    $archive  = $basename.'.zip';
    $backup   = $basename.'.sql';
    $data     = '';
    logger(sprintf('%s(): start creating/streaming backup %s',__FUNCTION__,$basename),LOG_DEBUG);
    if ($DB->dump($data)) {
        $data_length = strlen($data);
        logger(sprintf('%s(): backup file %s is %d bytes',__FUNCTION__,$backup,$data_length),LOG_DEBUG);
        if ($download == 'sql') {
            header('Content-Type: text/x-sql');
            header(sprintf('Content-Disposition: attachment; filename="%s"',$backup));
            logger(sprintf('%s(): about to stream %d bytes (backup = %s)',__FUNCTION__,$data_length,$backup),LOG_DEBUG);
            echo $data;
            logger(sprintf('%s(): success streaming data %s (%d bytes uncompressed)',__FUNCTION__,$backup,$data_length));
            exit;
        } else {
            include_once($CFG->progdir.'/lib/zip.class.php');
            $zip = new Zip;
            $comment = $CFG->www;
            if ($zip->OpenZipstream($archive,$comment)) {
                if ($zip->AddData($data,$backup)) {
                    if ($zip->CloseZip()) {
                        logger(sprintf('%s(): success streaming backup %s (%d bytes uncompressed)',
                                        __FUNCTION__,$archive,$data_length));
                     } else {
                         logger(sprintf('%s(): cannot close zipstream %s: %s',__FUNCTION__,$archive,$zip->Error));
                     }
                } else {
                    logger(sprintf('%s(): cannot add backup data to zipstream %s: %s',__FUNCTION__,$archive,$zip->Error));
                    $zip->CloseZip();
                }
                // ZipStream is done, no point in adding garbage at the end of that file, quit completely
                exit;
            } else {
                logger(sprintf('%s(): cannot open zipstream %s for backup: %s',__FUNCTION__,$archive,$zip->Error));
            }
        }
    } else {
        logger(sprintf('%s(): cannot open dump data for backup: %s',__FUNCTION__,db_errormessage()));
    }
    $output->add_message(t('backuptool_error','admin'));
    show_tools_menu($output);
    show_tools_intro($output);
} // task_backuptool()


/** quick and dirty logfile viewer
 *
 * this constructs a table with the contents of the logtable.
 * fields displayed are: datim, IP-address, username, logpriority and message
 * we use a LEFT JOIN in order to get to a meaningful username rather than a numeric user_id
 * an attempt is made to start with the last page of the logs because that would probably
 * be the most interesting part. We paginate the log in order to keep it manageable.
 *
 * @param object &$output collects output to show to user
 * @return output displayed via $output
 * @todo should we allow for fancy selection mechanisms on the logfile or is that over the top?
 */
function task_logview(&$output) {
    global $CFG,$WAS_SCRIPT_NAME,$DB;
    static $priorities = array(
        LOG_EMERG   => 'LOG_EMERG',
        LOG_ALERT   => 'LOG_ALERT',
        LOG_CRIT    => 'LOG_CRIT',
        LOG_ERR     => 'LOG_ERR',
        LOG_WARNING => 'LOG_WARNING',
        LOG_NOTICE  => 'LOG_NOTICE',
        LOG_INFO    => 'LOG_INFO',
        LOG_DEBUG   => 'LOG_DEBUG');

    // 0 -- at least we allow the user to navigate away if something goes wrong
    show_tools_menu($output,TASK_LOGVIEW);

    // 1A -- how many messages are there anyway?
    $table = 'log_messages';
    $where = ''; // could be used to select per user, per priority, etc. For now: always select everything
    if (($record = db_select_single_record($table,'COUNT(log_message_id) AS messages',$where)) === FALSE) {
        $output->add_content('<h2>'.t('menu_logview','admin').'</h2>');
        $output->add_content(t('logview_error','admin'));
        $output->add_message(t('logview_error','admin'));
        logger(sprintf('%s(): cannot retrieve log message count: %s',__FUNCTION__,db_errormessage()));
        return;
    }
    // 1B -- if there are no message we leave
    if (($num_messages = intval($record['messages'])) < 1) {
        $output->add_content('<h2>'.t('menu_logview','admin').'</h2>');
        $output->add_content(t('logview_no_messages','admin'));
        $output->add_message(t('logview_no_messages','admin'));
        logger(sprintf('%s(): no messages to show',__FUNCTION__),LOG_DEBUG);
        return;
    }

    // 2 -- which part of the logs do they want to see? (calculate/retrieve offset and limit)
    $limit = get_parameter_int('limit',$CFG->pagination_height);
    $limit = max(1,$limit);                                              // make sure 1 <= $limit
    $offset = intval(floor($num_messages  / $limit)) * $limit;           // attempt to start at begin of LAST page
    $offset = get_parameter_int('offset',max($offset,0));
    $offset = max(min($num_messages-1,$offset),0);                       // make sure 0 <= $offset < $num_messages

    // 3 -- show the pagination in the page header (if necessary)
    if (($num_messages <= $limit) && ($offset == 0)) {                   // listing fits on a single screen
        $header = '<h2>'.t('menu_logview','admin').'</h2>';
    } else {                                                             // pagination necessary, tell user where we are
        $param = array('{FIRST}' => strval($offset+1),
                       '{LAST}' => strval(min($num_messages,$offset+$limit)),
                       '{TOTAL}' => strval($num_messages));
        $header = '<h2>'.t('menu_logview','admin').' '.t('pagination_count_of_total','admin',$param).'</h2>';
        $parameters = array('job' => JOB_TOOLS, 'task' => TASK_LOGVIEW);
        $output->add_pagination($WAS_SCRIPT_NAME,$parameters,$num_messages,$limit,$offset,$CFG->pagination_width);
    }

    // 4 -- retrieve the selected messages (including optional username via LEFT JOIN)
    $sql = sprintf('SELECT l.datim, l.remote_addr, l.priority, l.user_id, u.username, l.message '.
                   'FROM %slog_messages l LEFT JOIN %susers u USING (user_id) '.
                   'ORDER BY l.datim, l.log_message_id',
                   $DB->prefix,$DB->prefix);
    if (($DBResult = $DB->query($sql,$limit,$offset)) === FALSE) {
        $output->add_message(t('logview_error','admin'));
        logger(sprintf('%s(): cannot retrieve log messages: %s',__FUNCTION__,db_errormessage()));
        return;
    }
    $records = $DBResult->fetch_all_assoc();
    $DBResult->close();

    // 5A -- setup a table with a header
    $index = $offset+1;
    $output->add_content($header);
    $class = 'header';
    $attributes = array('class' => $class,'align' => 'right');
    $output->add_content('<p>');
    $output->add_content(html_table(array('cellpadding' => '3')));
    $output->add_content('  '.html_table_row($attributes));
    $output->add_content('    '.html_table_head($attributes,t('logview_nr','admin')));
    $attributes['align'] = 'left';
    $output->add_content('    '.html_table_head($attributes,t('logview_datim','admin')));
    $output->add_content('    '.html_table_head($attributes,t('logview_remote_addr','admin')));
    $output->add_content('    '.html_table_head($attributes,t('logview_user_id','admin')));
    $output->add_content('    '.html_table_head($attributes,t('logview_priority','admin')));
    $output->add_content('    '.html_table_head($attributes,t('logview_message','admin')));
    $output->add_content('  '.html_table_row_close());

    // 5B -- step through the recordset and dump into the table
    foreach($records as $record) {
        $class = ($class == 'odd') ? 'even' : 'odd';
        $priority = (isset($priorities[$record['priority']])) ? $priorities[$record['priority']] : strval(intval($record['priority']));
        $attributes = array('class' => $class);
        $output->add_content('  '.html_table_row($attributes));
        $attributes['align'] = 'right';
        $output->add_content('    '.html_table_cell($attributes,strval($index++)));
        $attributes['align'] = 'left';
        $output->add_content('    '.html_table_cell($attributes,htmlspecialchars($record['datim'])));
        $output->add_content('    '.html_table_cell($attributes,htmlspecialchars($record['remote_addr'])));
        $output->add_content('    '.html_table_cell($attributes,htmlspecialchars($record['username'])));
        $output->add_content('    '.html_table_cell($attributes,$priority));
        $output->add_content('    '.html_table_cell($attributes,htmlspecialchars($record['message'])));
        $output->add_content('  '.html_table_row_close());
    }

    // 5C -- all done
    $output->add_content(html_table_close());
    $output->add_content('<p>');
} // task_logview()

?>