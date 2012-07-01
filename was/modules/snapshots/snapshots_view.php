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

/** /program/modules/snapshots/snapshots_view.php - interface to the view-part of the snapshots module
 *
 * This file defines the interface with the snapshots-module for viewing content.
 * The interface consists of this function:
 *
 * <code>
 * snapshots_view(&$output,$area_id,$node_id,$module)
 * </code>
 *
 * This function is called from /index.php when the node to display is connected
 * to this module. Internally all the work is done in the Snapshot class.
 * This class is also used in the module that aggregates different nodes into
 * a single HTML-document.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_snapshots
 * @version $Id: snapshots_view.php,v 1.2 2012/07/01 11:20:11 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** display the snapshots from the directory linked to node $node_id
 *
 * this routine is only a helper to create a new SnapshotViewer instance
 * which is where the real work is done.
 *
 * there are three different variants (depends on configuration parameter 'variant'):
 *
 *  - 1 (thumbs): show the title, the introduction and thumbnails of all snapshots
 *  - 2 (first): show the first snapshot from the series full-size
 *  - 3 (slideshow): automatically rotate through all snapshots (uses javascript)
 *
 * The default is 1 (thumbs).
 *
 * @param object &$theme collects the (html) output
 * @param int $area_id identifies the area where $node_id lives
 * @param int $node_id the node to which this module is connected
 * @param array $module the module record straight from the database
 * @return bool TRUE on success + output via $theme, FALSE otherwise
 */
function snapshots_view(&$theme,$area_id,$node_id,$module) {
    $mod = new SnapshotViewer($theme,$area_id,$node_id,$module);
    return $mod->run();
} // snapshots_view()


/** this class implements methods to display snapshots
 */
class SnapshotViewer {
    /** @var object $theme collects the (html) output */
    var $theme;

    /** @var int $area_id indicates the working area */
    var $area_id;

    /** @var int $node_id indicates the node associated with the snapshots */
    var $node_id;

    /** @var array $module_record the module record straight from the database */
    var $module_record;

    /** @var string $domain the language domain where we get our translations from, usually 'm_<modulename>' */
    var $domain = '';

    /** @var string $header the (optional) title to display */
    var $header = '';

    /** @var string $introduction the (optional) introductory text to display */
    var $introduction = '';

    /** @var int $variant 1=thumbnails, 2=first, 3=slideshow */
    var $variant = '';

    /** @var int $dimension defines the box size for variant 2 */
    var $dimension = '';

    /** @var string $snapshots_path the directory containing the snapshots */
    var $snapshots_path = '';

    /** @var null|array $snapshots holds all snapshot information from snapshot directory or NULL if not yet set */
    var $snapshots = NULL;

    /** @var int $default_showtime is the default # of seconds between images in a slideshow */
    var $default_showtime = 5;


    /** the constructor only stores relevant data for future use
     *
     * @param object &$theme collects the (html) output
     * @param int $area_id identifies the area where $node_id lives (currently unused)
     * @param int $node_id the node to which this module is connected
     * @param array $module the module record straight from the database
     */
    function SnapshotViewer(&$theme,$area_id,$node_id,$module) {
        $this->theme = &$theme;
        $this->area_id = intval($area_id);
        $this->node_id = intval($node_id);
        $this->module_record  = $module;
        $this->domain = 'm_'.$this->module_record['name']; // indicates where to look for translations
    } // SnapshotViewer()


    /** task dispatcher
     *
     * this routine decides what to do and calls the appropriate workhorse routine(s)
     *
     * @return bool TRUE on success, FALSE otherwise
     * @todo check permissions (ACL) to prevent leaking a private area path to anonymous visitors?
     */
    function run() {
        global $CFG;
        $m = '      ';
        // 1 -- determine the directory path and other configuration information
        $this->get_snapshots_configuration($this->node_id);

        // 2A -- get a list of available files from $snapshots_path
        $this->snapshots = $this->get_snapshots($this->snapshots_path);
        $snapshots_count = sizeof($this->snapshots);

        // 2B -- if there are none we bail out but DO show the header+introduction
        if ($snapshots_count <= 0) {
            if (!empty($this->header)) {
                $this->theme->add_content($m.html_tag('h3',array('class' => 'snapshots_header'), $this->header));
            }
            if (!empty($this->introduction)) {
                $this->theme->add_content($m.html_tag('div',array('class' => 'snapshots_introduction'), $this->introduction));
            }
            $msg = t('no_snapshots_available',$this->domain);
            $this->theme->add_message($msg);
            $this->theme->add_content($m.'<h3>'.$msg.'</h3>');
            return TRUE;
        }

        // 3A -- get ready to do some real work
        $stylesheet = 'program/modules/snapshots/snapshots.css';
        $this->theme->add_stylesheet($stylesheet);
        $this->javascript_include_once('/modules/snapshots/slideshow.js');
        $this->javascript_add_img_array();

        // 3B -- what do they want?
        $snapshot_index = get_parameter_int('snapshot',NULL);
        if ((!is_null($snapshot_index)) && (0 < $snapshot_index) && ($snapshot_index <= $snapshots_count)) {
            $retval = $this->view_snapshot($snapshot_index);
        } else {
            // if no specific image was specified, we allow the user to choose the variant,
            // while using the value from the database as a (sensible) default.
            $variant = get_parameter_int('variant',$this->variant);
            switch($variant) {
            case 1:
                $retval = $this->view_thumbnails();
                break;
            case 2:
                $retval = $this->view_snapshot(1);
                break;
            case 3:
                $retval = $this->view_slideshow();
                break;
            default:
                $retval = $this->view_thumbnails(); // shouldn't happen
                break;
            }
        }
        return $retval;
    } // run()


    /** display snapshots in the form of 0 or more clickable thumbnails
     *
     * @return bool TRUE on success + output via $this->theme, FALSE otherwise
     */
    function view_thumbnails() {
        global $CFG;
        $m = '      ';
        // 0 -- always show a navigation bar
        $this->add_snapshot_navbar(0);
        //
        // 1 -- always show the header/introductory text
        //
        if (!empty($this->header)) {
            $this->theme->add_content($m.html_tag('h3',array('class' => 'snapshots_header'), $this->header));
        }
        if (!empty($this->introduction)) {
            $this->theme->add_content($m.html_tag('div',array('class' => 'snapshots_introduction'), $this->introduction));
        }
        //
        // 2 -- step through list of snapshots
        //
        $thumb_dimension = $CFG->thumbnail_dimension;
        $index = 0;
        foreach($this->snapshots as $i => $snapshot) {
            ++$index;
            $key = $snapshot['key'];
            $alt = $key;
            $title = sprintf('%s (%dx%d, %s)',$key,$snapshot['width'],$snapshot['height'],$snapshot['human_size']);
            $image_dimension = max(1,max($snapshot['width'],$snapshot['height']));
            $attributes = array(
                'width' => intval(($snapshot['width'] * $thumb_dimension) / $image_dimension),
                'height' => intval(($snapshot['height'] * $thumb_dimension) / $image_dimension),
                'alt' => $alt,
                'title' => $title);
            $img = html_img(was_file_url($snapshot['thumb']),$attributes);
            $params = array('snapshot' => strval($index));
            $href = was_node_url($this->theme->node_record,$params,$key,$this->theme->preview_mode);

            $this->theme->add_content($m.html_tag('div',array('class' => 'thumbnail_container')));
            $this->theme->add_content($m.'  '.html_tag('div',array('class' => 'thumbnail_caption'),$key));
            $this->theme->add_content($m.'  '.html_tag('div',array('class' => 'thumbnail_image'), html_a($href,NULL,NULL,$img)));
            $this->theme->add_content($m.'</div>');
        }
        $this->theme->add_content($m.'<div style="clear: both;"></div>');
        return TRUE;
    } // view_thumbnails()


    /** display a single full-size snapshot scaled to the specified dimension
     *
     * @param int $snapshot_index indicates the snapshot to show
     * @return bool TRUE on success, FALSE otherwise
     */
    function view_snapshot($snapshot_index) {
        global $CFG,$DB;
        $m = '      '; // readability
        //
        // 0 -- sanity check - does snapshot_id actually exist in this snapshot series?
        //
        $snapshots_count = sizeof($this->snapshots);
        if (($snapshot_index <= 0) || ($snapshots_count < $snapshot_index)) {
            logger(sprintf("%s.%s(): no snapshot '%d' in node '%d'",__CLASS__,__FUNCTION__,$snapshot_index,$this->node_id));
            $message = t('warning_no_such_snapshot',$this->domain,array('{SNAPSHOT}' => intval($snapshot_index)));
            $this->theme->add_message($message);
            return $this->view_thumbnails();
        }
        //
        // 1 -- show a navigation bar
        //
        $snapshot = $this->snapshots[$snapshot_index-1];
        $this->add_snapshot_navbar($snapshot_index);
        $snapshot_dimension = max(1,min($this->dimension,9999));
        //
        // 2A -- scale the image to fit inside the specified box
        //
        $image_dimension = max(1,max($snapshot['width'],$snapshot['height']));
        $w = intval(($snapshot['width'] * $snapshot_dimension) / $image_dimension);
        $h = intval(($snapshot['height'] * $snapshot_dimension) / $image_dimension);
        //
        // 2B -- preparations
        //
        $key = $snapshot['key'];
        if ($snapshot_index < sizeof($this->snapshots)) { // at least 1 more to go
            $alt = t('move_next_alt',$this->domain);
            $params = array('snapshot' => $snapshot_index + 1);
            $caption = $this->snapshots[$snapshot_index]['key']; // key of the next image
        } else {
            $alt = t('move_up_alt',$this->domain);
            $params = array('variant' => 1);
            $caption = NULL;
        }
        $title = sprintf('%s (%dx%d, %s)',$snapshot['key'],$snapshot['width'],
                         $snapshot['height'],$snapshot['human_size']);
        $attributes = array('width' => $w,'height' => $h,'title' => $title,'alt' => $alt);
        $img = html_img(was_file_url($snapshot['image']),$attributes);
        //
        // 2C -- fimally show this image as a link to the NEXT image (or the thumbnails overview)
        //
        $href = was_node_url($this->theme->node_record,$params,$caption,$this->theme->preview_mode);
        $this->theme->add_content($m.html_a($href,NULL,NULL,$img));

        //
        // 2D -- also add this specific image to the breadcrumb trail
        //
        $params = array('snapshot' => $snapshot_index);
        $href = was_node_url($this->theme->node_record,$params,$key,$this->theme->preview_mode);
        $this->theme->breadcrumb_addendum[] = html_a($href,NULL,NULL,$key);
        return TRUE;
    } // view_snapshot();


    /** show the regular thumbnails overview and then pop-up a full-screen slideshow on top
     *
     * this is basically the same as the thumbnail overview, be it that
     * we get the effect of 'automagically' entering the slideshow (take
     * it from the top).
     *
     * @return bool TRUE on success (always)
     */
    function view_slideshow() {
        $m='      ';
        $retval = $this->view_thumbnails();
        $this->theme->add_content($m.'<script type="text/javascript"><!--');
        $this->theme->add_content($m.'  show_start(0);');
        $this->theme->add_content($m.'//--></script>');
        return $retval;
    } // view_slideshow()


    /** add a navigation bar / tool bar for a snapshot
     *
     * this bar contains the following elements:
     *  - double arrow left links to the first snapshot in the series
     *  - left arrow links to the previous snapshot in the series
     *  - up arrow links to the thumbnails overview
     *  - right arrow links to the next snapshot in the series
     *  - double right arrow links to the last snapshot in the series
     *  - position indicator (snapshot i from n snapshots) in the form: i/n
     *
     * @param int $snapshot_index indicates which element of $snapshots contains the current snapshot
     * @return void navigation bar added to output
     * @todo clean up this ugly code
     */
    function add_snapshot_navbar($snapshot_index,$m='      ') {
        $images = array(
            0 => array(
                'black' => 'llb.png',
                'gray'  => 'llg.png',
                'title' => t('move_first_title',$this->domain),
                'alt'   => t('move_first_alt',$this->domain)
                ),
            1 => array(
                'black' => 'lb.png',
                'gray'  => 'lg.png',
                'title' => t('move_prev_title',$this->domain),
                'alt'   => t('move_prev_alt',$this->domain)
                ), 
            2 => array(
                'black' => 'ub.png',
                'gray'  => 'ug.png',
                'title' => t('move_up_title',$this->domain),
                'alt'   => t('move_up_alt',$this->domain)
                ), 
            3 => array(
                'black' => 'rb.png',
                'gray'  => 'rg.png',
                'title' => t('move_next_title',$this->domain),
                'alt'   => t('move_next_alt',$this->domain)
                ), 
            4 => array(
                'black' => 'rrb.png',
                'gray'  => 'rrg.png',
                'title' => t('move_last_title',$this->domain),
                'alt'   => t('move_last_alt',$this->domain)
                )
            );
        $current = $snapshot_index-1; // array is 0-based, index is 1-based
        $last = sizeof($this->snapshots)-1;
        $nav[0] = 0;
        $nav[1] = ($current > 0)      ? $current-1 : (($current < 0) ? $last : $current);
        $nav[2] = NULL;
        $nav[3] = ($current < $last)  ? $current+1 : $current;
        $nav[4] = $last;
        $this->theme->add_content($m.html_tag('div',array('class' =>'snapshot_toolbar')));
        foreach($nav as $id => $index) {
            $attributes = array(
                'width'  => 16,
                'height' => 16,
                'title'  => $images[$id]['title'],
                'alt'    => $images[$id]['alt']
                );
            if ($current < 0) {
                if ($id == 1) {
                    $attributes['title'] = t('move_last_title',$this->domain);
                    $attributes['alt']   = t('move_last_alt',$this->domain);
                } elseif ($id == 3) {
                    $attributes['title'] = t('move_first_title',$this->domain);
                    $attributes['alt']   = t('move_first_alt',$this->domain);
                }
            }
            if (is_null($index)) { // special case: this yields the snapshots overview (thumbnails)
                if ($current < 0) { // and thsi IS the snapshots overview (thumbnails): use gray icon
                    $img = html_img(was_url('program/modules/snapshots/'.$images[$id]['gray']),$attributes);
                } else {
                    $img = html_img(was_url('program/modules/snapshots/'.$images[$id]['black']),$attributes);
                }
                $params = array('variant' => '1');
                $caption = '';
            } elseif ($index === $current) {
                $params = array('snapshot' => $index+1);
                $attributes['alt'] = t('move_current_alt',$this->domain);
                $attributes['title'] = t('move_current_title',$this->domain);
                $img = html_img(was_url('program/modules/snapshots/'.$images[$id]['gray']),$attributes);
                $caption = $this->snapshots[$index]['key'];
            } else {
                $params = array('snapshot' => $index+1);
                $img = html_img(was_url('program/modules/snapshots/'.$images[$id]['black']),$attributes);
                $caption = $this->snapshots[$index]['key'];
            }
            $href = was_node_url($this->theme->node_record,$params,$caption,$this->theme->preview_mode);
            $this->theme->add_content($m.'  '.html_a($href,NULL,NULL,$img));
        }
        // at this point we add another button with a link to the slideshow code




        $attributes = array(
            'width'  => 16,
            'height' => 16,
            'title'  => t('slideshow_title',$this->domain),
            'alt'    => t('slideshow_alt',$this->domain)
            );
        $img = html_img(was_url('program/modules/snapshots/sb.png'),$attributes);
        $href=sprintf('javascript:show_start(%d);',max(0,$current));
        $slideshow = str_replace('\'','\\\'',html_a($href,NULL, NULL,$img));
        $this->theme->add_content($m.'  <script type="text/javascript"><!--');
        $this->theme->add_content($m.'    '.sprintf('document.write(\'%s\');',$slideshow));
        $this->theme->add_content($m.'  //--></script>');

        if ($current >= 0) { // not in thumbnails overview
            $params = array(
                '{SNAPSHOT}'  => $current+1,
                '{SNAPSHOTS}' => sizeof($this->snapshots),
                '{CAPTION}'   => $this->snapshots[$current]['key']);
            $this->theme->add_content($m.'  '.html_tag('span',array('class'=>'snapshot_status'),
                                                       t('snapshot_status',$this->domain,$params)));
        }
        $this->theme->add_content($m.'</div>');
    } // add_snapshot_navbar()


    /** retrieve all image files (snapshots) from directory $path
     *
     * This creates an array containing a (filtered) listing of the 
     * images in the directory called $path.
     * These items are suppressed:
     *  - current directory '.'
     *  - parent directory '..'
     *  - index.html (used to 'protect' directory against prying eyes)
     *  - symbolic links
     *
     * The files THUMBNAIL_PREFIX* (the thumbnails of images) are
     * a special case: these are used to show a small image in the
     * thumbnail overview.
     *
     * A second filtering makes sure that the items returned are actually images.
     *
     * @param string $path the directory to scan (relative to $CFG->datadir)
     * @return array all snapshots in the directory (could be empty)
     * @uses $CFG;
     */
    function get_snapshots($path) {
        global $CFG;
        //
        // 1 -- fetch list of files from directory
        //
        $full_path = $CFG->datadir.$path;
        if (($handle = @opendir($full_path)) === FALSE) {
            logger(sprintf("%s.%s(): cannot open directory '%s'",__CLASS__,__FUNCTION__,$path));
            return array();
        }
        $prefix_len = strlen(THUMBNAIL_PREFIX);
        $extensions = explode(',',str_replace(array('.',' '),'',$CFG->filemanager_images));
        $files = array();
        while (($entryname = readdir($handle)) !== FALSE) {
            $full_entryname = $full_path.'/'.$entryname;
            if (($entryname == '.') || ($entryname == '..') || ($entryname == 'index.html') ||
                (is_link($full_entryname)) || (!(is_file($full_entryname)))) {
                continue;
            }
            // we are now fairly sure $entryname is a genuine file. check extension (if any)
            if (strpos($entryname,'.') === FALSE) {
                $ext = '';
            } else {
                $components = explode('.',$entryname);
                $ext = utf8_strtolower(array_pop($components));
                unset($components);
            }
            if (array_search($ext,$extensions) === FALSE) { // not an image file, next please
                continue;
            }
            $is_thumbnail = (substr($entryname,0,$prefix_len) == THUMBNAIL_PREFIX) ? TRUE : FALSE;
            $snapshot = ($is_thumbnail) ? substr($entryname,$prefix_len) : $entryname;
            if (!isset($files[$snapshot])) {
                $files[$snapshot] = array();
            }
            if ($is_thumbnail) {
                $files[$snapshot]['thumb'] = $path.'/'.$entryname;
            } else {
                $files[$snapshot]['image'] = $path.'/'.$entryname;
                $files[$snapshot]['size'] = filesize($full_entryname);
            }
        }
        closedir($handle);
        ksort($files); // order by filename which usually boils down to chronological order

        //
        // 2 - postprocess the list of files; keep only image files (+fetch additional data)
        //
        $snapshots = array();
        foreach($files as $key => $file) {
            if (!isset($file['image'])) { // skip thumbnail-only entries
                continue;
            }
            $full_image = $CFG->datadir.$file['image'];
            if (($image_info = @getimagesize($full_image)) === FALSE) { // not an image, next please
                continue;
            }
            $file['key'] = $key;
            $file['width'] = $image_info[0];
            $file['height'] = $image_info[1];
            if (!isset($file['thumb'])) {
                $file['thumb'] = $file['image']; // use original picture if no thumb specified
            }
            $file['human_size'] = ($file['size'] < 10240000) ? sprintf('%d kB',$file['size'] >> 10) :
                                                               sprintf('%d MB',$file['size'] >> 20);
            $snapshots[] = $file; // keep this image file
        }
        unset($files);
        return $snapshots;
    } // get_snapshots()


    /** construct an image configuration array for javascript processing
     *
     * this steps through the snapshots list and prepares an javascript-array.
     * The n'th image (starting at 0) in that array is defined as follows:
     *
     * img[n][0] = width of the image (in pixels)
     * img[n][1] = height of the image (in pixels)
     * img[n][2] = the url of the image file (src-attribute of the img tag)
     * img[n][3] = the number of seconds to display this image
     * img[n][4] = title to add to the display (document title)
     *
     * @param string $m code readability
     * @return void
     */
    function javascript_add_img_array($m='      ') {
        $code = array();
        $code[] = '<script type="text/javascript"><!--';
        $n = sizeof($this->snapshots);
        $index = 0;
        foreach($this->snapshots as $i => $snapshot) {
            // Tricky Business...
            // if key matches 'nnn_mmm_*.*' use 'mmm' for showtime if sensible
            // and also strip the nnn_mmm_ part with the intention to be able
            // to sort a bunch of photos on nnn_ and use the embedded display time
            $key = $snapshot['key'];
            $matches = array();
            $showtime = $this->default_showtime;
            if (preg_match('/^[0-9]*_([0-9]*)_/',$key,$matches)) {
                $showtime = intval($matches[1]);
                if ((0 < $showtime) && ($showtime < 3600)) {
                    $key = substr($snapshot['key'],strlen($matches[0]));
                }
            }
            $title = sprintf('%d/%d - %s (%dx%d, %s)',++$index,$n,$key,
                       $snapshot['width'],$snapshot['height'],$snapshot['human_size']);
            $code[] = sprintf('  img[%d]=[%d,%d,\'%s\',%d,\'%s\'];',
                $i,$snapshot['width'],$snapshot['height'],
                str_replace('\'','\\\'',was_file_url($snapshot['image'])),
                $showtime,
                str_replace('\'','\\\'',$title));
        }
        // plug in the (translated) error/warning messages
        $code[] = sprintf("  msg[0]='%s';",str_replace('\'','\\\'',t('js_loading',$this->domain)));
        $code[] = sprintf("  msg[1]='%s';",str_replace('\'','\\\'',t('js_no_images',$this->domain)));
        $code[] = '//--></script>';
        foreach($code as $line) {
            $this->theme->add_content($m.$line);
        }
        return;
    } // javascript_add_img_array()


    /** retrieve configuration data for this set of snapshots
     *
     * this routine fetches the configuration from the snapshots table and stores
     * the sanitised information from the various fields in the object variables.
     *
     * @param int $node_id this key identifies the snapshots series
     * @return void and information stored in object variables
     * @todo check for information leaks (private path) here?
     */
    function get_snapshots_configuration($node_id) {
        //
        // 1 -- retrieve the relevant data for this series of snapshots from database
        //
        $table = 'snapshots';
        $fields = array('header','introduction','snapshots_path','variant', 'dimension');
        $where = array('node_id' => intval($node_id));
        $record = db_select_single_record($table,$fields,$where);
        if ($record === FALSE) {
            logger(sprintf('%s.%s(): error retrieving configuration: %s',__CLASS__,__FUNCTION__,db_errormessage()));
            $record = array('header' => '', 'introduction' => '', 'snapshots_path' => '', 'variant' => 1, 'dimension' => 512);
        }
        $this->header = trim($record['header']);
        $this->introduction = trim($record['introduction']);
        $this->variant = intval($record['variant']);
        $this->dimension = intval($record['dimension']);

        //
        // 2 -- sanity checks (just in case); massage pathname
        //
        $path = trim($record['snapshots_path']);
        if ((!utf8_validate($path)) || (strpos('/'.$path.'/','/../') !== FALSE)) {
            logger(sprintf("%s.%s(): invalid path '%s'; using root path",__CLASS__,__FUNCTION__,$path));
            $path = '/'; // shouldn't happen
        }
        if (substr($path,0,1) != '/') { $path = '/'.$path; }
        if (substr($path,-1) == '/') { $path = substr($path,0,-1); }
        $this->snapshots_path = $path;

        // FIXME: check permissions here to prevent leaking a private area path to anonymous visitors?
        return;
    } // get_snapshots_configuration()


    /** include an external javascript file once
     *
     * this adds an inclusion of a javascript file once in the document
     * we are creating in $this->theme. If multiple instances of this
     * SnapshowViewer-class exist the file is included only once.
     *
     * @param string $filename name of the js-file relative to /program directory
     * @return void $filename inluded in $this->theme on the first call, otherwise nothing happens
     */
    function javascript_include_once($filename) {
        static $filenames = array();
        global $CFG;
        if (isset($filenames[$filename])) {
          ++$filenames[$filename];
        } else {
          $filenames[$filename] = 1;
          $this->theme->add_html_header(html_tag('script',array(
              'type' => 'text/javascript',
              'src' =>  $CFG->progwww_short.$filename),''));
        }
    } // javascript_include_once()

} // SnapshotViewer

/** this class implements methods to display snapshots
 */
class SnapshotViewerInline extends SnapshotViewer {

    /** @var int $inline_show_width is the available width in the inline slideshow */
    var $inline_show_width = 120;

    /** @var int $inline_show_height is the available height in the inline slideshow */
    var $inline_show_height = 120;

    /** @var int $inline_show_visible_images is the number of images to show simultaneously */
    var $inline_show_visible_images = 1;


    /** the constructor only stores relevant data for future use
     *
     * @param object &$theme collects the (html) output
     * @param int $area_id identifies the area where $node_id lives (currently unused)
     * @param int $node_id the node to which this module is connected
     * @param array $module the module record straight from the database
     * @param int $width the available width for the inline slideshow
     * @param int $height the available height for the inline slideshow
     * @param int $visible the # of visible images in the inline slideshow
     */
    function SnapshotViewerInline(&$theme,$area_id,$node_id,$module,$width,$height,$visible) {
        parent::SnapshotViewer($theme,$area_id,$node_id,$module);
        $this->inline_show_width = $width;
        $this->inline_show_height = $height;
        $this->inline_show_visible_images = $visible;
    } // SnapshotViewerInline()

    /** read configuration paramerters and actually generate the inline slide show
     *
     * this routine decides what to do and calls the appropriate workhorse routine(s)
     *
     * @return bool TRUE on success, FALSE otherwise
     * @todo check permissions (ACL) to prevent leaking a private area path to anonymous visitors?
     */
    function run() {
        global $CFG;
        $m = '      ';
        // 1 -- determine the directory path and other configuration information
        $this->get_snapshots_configuration($this->node_id);

        // 2A -- get a list of available files from $snapshots_path
        $this->snapshots = $this->get_snapshots($this->snapshots_path);
        $snapshots_count = sizeof($this->snapshots);
        if ($snapshots_count <= 0) {
            $msg = t('no_snapshots_available',$this->domain);
            $this->theme->add_message($msg);
            $this->theme->add_content($m.'<h3>'.$msg.'</h3>');
            return TRUE;
        }
        $this->javascript_include_once('/modules/snapshots/inlineshow.js');
        $this->javascript_add_inline_show();
        return TRUE;
    } // run()


    /** construct the necessary Jaascript-code to do the inline slideshow configuration
     *
     * this steps through the snapshots list and prepares the necessary javascript
     * function calls to create and populate the inline slideshow.
     * The following slideshow parameters are conveyed:
     *  - the available width
     *  - the available height
     *  - the number of visible images
     *
     * The following image parameters are conveyed:
     *  - width of the image (in pixels)
     *  - height of the image (in pixels)
     *  - the url of the image file (src-attribute of the img tag)
     *  - the number of seconds to display this image
     *  - title to add to the display (document title)
     *
     * @param string $m code readability
     * @return void
     */
    function javascript_add_inline_show($m='      ') {
        // sanity check
        $w=max(16,intval($this->inline_show_width));
        $h=max(16,intval($this->inline_show_height));
        $n=max(1,intval($this->inline_show_visible_images));

        $code = array();
        $code[] = '<script type="text/javascript"><!--';
        $code[] = sprintf('  var h=inline_show_create(%d,%d,%d);',$w,$h,$n);
        $n = sizeof($this->snapshots);
        $index = 0;
        foreach($this->snapshots as $i => $snapshot) {
            // see javascript_add_img_array: same trick with embedded showtime
            $key = $snapshot['key'];
            $matches = array();
            $showtime = $this->default_showtime;
            if (preg_match('/^[0-9]*_([0-9]*)_/',$key,$matches)) {
                $showtime = intval($matches[1]);
                if ((0 < $showtime) && ($showtime < 3600)) {
                    $key = substr($snapshot['key'],strlen($matches[0]));
                }
            }
            $title = sprintf('%d/%d - %s (%dx%d, %s)',++$index,$n,$key,
                       $snapshot['width'],$snapshot['height'],$snapshot['human_size']);
            $code[] = sprintf('  inline_show_add(h,%d,%d,\'%s\',%d,\'%s\');',
                $snapshot['width'],$snapshot['height'],
                str_replace('\'','\\\'',was_file_url($snapshot['image'])),
                $showtime,
                str_replace('\'','\\\'',$title));
        }
        // plug in the (translated) error/warning messages
        $code[] = sprintf("  inline_show_msg[0]='%s';",str_replace('\'','\\\'',t('js_loading',$this->domain)));
        $code[] = sprintf("  inline_show_msg[1]='%s';",str_replace('\'','\\\'',t('js_no_images',$this->domain)));
        $code[] = '  inline_show_run(h);';
        $code[] = '//--></script>';
        foreach($code as $line) {
            $this->theme->add_content($m.$line);
        }
        return;
    } // javascript_add_inline_show()
} // SnapshotViewerInline
  
?>