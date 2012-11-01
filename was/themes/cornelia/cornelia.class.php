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

/** /program/themes/cornelia/cornelia.class.php - the class that implements the theme
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wastheme_cornelia
 * @version $Id: cornelia.class.php,v 1.1 2012/11/01 09:06:08 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

class ThemeCornelia extends Theme {

    function ThemeCornelia($theme_record,$area_id,$node_id) {
        parent::Theme($theme_record,$area_id,$node_id);
        $this->quickbottom_separator = '|';
    }

    /** construct an output page in HTML
     *
     * This constructs a full HTML-page, starting at the DTD
     * and ending with the html closing tag.
     *
     * The page is constructed using nested DIVs, the layout
     * is taken care of in separate style sheets. All knowledge
     * about the structure of the page is contained in this routine.
     *
     * As a rule the layout is based on three columns. The left hand
     * column contains free form HTML, the menu and more free form HTML.
     * The right hand column contains free form HTML, the contents of
     * 0, 1 or more html-pages and more free form HTML. The column in
     * the middle holds the actual content.
     *
     * The list of html-pages to show in the right hand column is
     * configurable per main menu item. A '0' (zero) means: no page,
     * a page number means that page, a section number means all
     * html-pages in that section and a dash ('-') means: suppress
     * the right hand side column completely. This also inserts an
     * additional stylesheet to style the 2-column layout.
     * There is also a speial print stylesheet. This is included
     * after the user clicks the special print link.
     * Note that the stylesheets are added only once, in the
     * order 'style.css', 'style2.css' and finally 'print.css' (if
     * applicable).
     *
     * The contents of the various DIVs is constructed in various
     * helper routines in order to make this routine easy to read
     * (by humans that is). The various helper routines all are called
     * with a string of space characters; this should improve the
     * the readability of the page that is generated eventually.
     *
     * The header background changes every N minutes (N=configurable
     * between 0 and 60). Also, the choice of background image is
     * linked to the currently selected item in the main navigation.
     *
     * Note that the routine $this->get_div_messages() does in fact
     * generate its own DIV tags. This is done in order to completely
     * get rid of the message DIV, we do not even want to see an empty
     * DIV if there are no messages.
     *
     * The same logic applies to the breadcrumb trail.
     *
     * @return string complete HTML-page, ready for output
     */     
    function get_html() {
        static $dejavu = 0;
        $navigation_index = $this->cornelia_navigation_index();
        $sidebar_nodes = $this->cornelia_sidebar_nodes_modules($navigation_index);

        // Initialise additional stylesheets (only once)
        if ($dejavu++ == 0) {
            // if there is no sidebar at all, add a special 2-column stylesheet addition
            if ($sidebar_nodes === FALSE) {
                if ((isset($this->config['style_usage_static'])) && 
                    ($this->config['style_usage_static']) &&
                    (isset($this->config['stylesheet2']))) {
                    $this->add_stylesheet($this->config['stylesheet2']);
                }
            }
            // in case of print, add a special style sheet for print version
            if ((isset($_GET['print'])) && (intval($_GET['print']) == 1)) {
                if ((isset($this->config['style_usage_static'])) && 
                    ($this->config['style_usage_static']) &&
                    (isset($this->config['stylesheet_print']))) {
                    $this->add_stylesheet($this->config['stylesheet_print']);
                }
            }
        }
        // Try to compute a background URL for the header
        if (($url = $this->cornelia_get_background_url($navigation_index)) !== FALSE) {
            $headerstyle = sprintf(' style="background-image: url(\'%s\');"',$url);
        } else {
            $headerstyle = '';
        }

        // Finally construct actual page
        $s  = $this->dtd."\n".
              "<html>\n".
              "<head>\n".
                $this->get_html_head('  ').
              "</head>\n".
              "<body>\n".
              "  <div id=\"top\">\n".
                   $this->get_popups($this->messages_top,'    ').
              "  </div>\n".

              "  <div id=\"page\">\n".
              "    <!-- header -->\n".
              "    <div id=\"header\"$headerstyle>\n".
              "      <div id=\"header_inside\">\n".
              "        <div id=\"logo\">\n".
                         $this->get_logo('          ').
              "        </div>\n".
              "        <h1>".$this->title."</h1>\n".
              "        <h2>".$this->config['header_text']."</h2>\n".
              "      </div>\n".
              "      <!-- navigation level 1 -->\n".
              "      <div id=\"navigation_background\">\n".
              "        <div id=\"navigation\">\n".
                         $this->get_navigation('          ',$this->text_only).
              "        </div>\n".
              "      </div>\n".
              "      <!-- navigation level 1 end -->\n".
              "    </div>\n".
              "    <!-- header end -->\n".

              "    <div id=\"quicktop_container\">\n".
              "      <div id=\"quicktop\">\n".
                       $this->get_quicktop('        ').
              "      </div>\n".
                     $this->get_div_breadcrumbs('      ').
              "    </div>\n".
                   $this->get_div_messages('    ').

              "    <div id=\"content\">\n".
              "      <!-- left column -->\n".
              "      <div id=\"leftmargin\">\n".
              "        <div id=\"leftmargin_top\">\n".
                         $this->config['left_top_html']."\n".
              "        </div>\n".
              "        <!-- navigation level 2 -->\n".
              "        <div id=\"menu\">\n".
                         $this->get_menu('          ').
              "        </div>\n".
              "        <!-- navigation level 2 end -->\n".
              "        <div id=\"leftmargin_bottom\">\n".
                         $this->config['left_bottom_html']."\n".
              "        </div>\n".
              "      </div>\n".
              "      <!-- left column end -->\n";

        if ($sidebar_nodes !== FALSE) { $s .= 
              "      <!-- right column -->\n".
              "      <div id=\"rightmargin\">\n".
              "        <div id=\"rightmargin_top\">\n".
                         $this->config['right_top_html']."\n".
              "        </div>\n".
              "        <div id=\"sidebar\">\n".
                         $this->cornelia_get_sidebar($sidebar_nodes,'          ').
              "        </div>\n".
              "        <div id=\"rightmargin_bottom\">\n".
                         $this->config['right_bottom_html']."\n".
              "        </div>\n".
              "      </div>\n".
              "      <!-- right column end -->\n";
        }

        $s .= "      <!-- middle column -->\n".
              "      <div id=\"content_inside\">\n".
              "        <h2 id=\"content_title\">".htmlspecialchars($this->node_record['title'])."</h2>\n".
                       $this->get_content('        ').
              "      </div>\n".
              "      <!-- middle column end -->\n".
              "    </div>\n".

              "    <div id=\"footer\">\n".
              "      <div id=\"quickbottom\">\n".
                       $this->get_bottomline('        ').
              "      </div>\n".
              "      <div id=\"address\">\n".
                       $this->get_address('        ').
              "      </div>\n".
              "    </div>\n".
              "  </div>\n".

              "  <div id=\"bottom\">\n".
                   $this->get_popups($this->messages_bottom,'    ').
              "  </div>\n".
              "</body>\n".
              "</html>\n";
        return $s;
    } // get_html()


    /** construct a list of quicklinks for top of page (if any) + (maybe) a print-button
     *
     *
     * @param string $m left margin for increased readability
     * @return string constructed list of clickable links or an empty string
     * @uses get_quicklinks()
     */
    function get_quicktop($m='') {
        global $CFG;
        $separator = $this->quicktop_separator;
        $s = $this->get_quicklinks($m,'quicktop_section_id',$separator);
        if ((isset($_GET['print'])) && ($_GET['print'] != 0)) { // already on printpage; suppress link
            return $s;
        }
        if (empty($s)) {
            $separator = '';
        } elseif (!empty($separator)) {
            $separator .= ' '; // for readability
        }
        $url = $CFG->www.'/index.php';
        if (isset($_SERVER['PATH_INFO'])) {
            $path_info = $_SERVER['PATH_INFO'];
            $url .= htmlspecialchars($path_info);
        }
        $item_count = 0;
        $params = (!empty($_GET)) ? $_GET : array();
        $params['print'] = '1'; 
        $attributes = array('title' => t('print_title',$this->domain),'target' => '_blank');
        return $s.$m.$separator.html_a($url,$params,$attributes,t('print',$this->domain))."\n";
    } // get_quicktop()


    /** construct the submenu starting at $menu_id OR the first breadcrumb in the top level menu
     *
     * this constructs an 'infinitely' nested set of submenus, starting at $menu_id
     * or at the first breadcrumb in the top level menu (if any).
     * If there are no suitable nodes, an empty string is returned.
     *
     * this is largely the same routine as parent::get_menu(). Difference is
     * that here we may add a menu title to the menu IF the first item in
     * the breadcrumb trail is a visible section
     *
     * @param string $m left margin for increased readability
     * @param int $menu_id indicates where to start the menu (NULL = first breadcrumb in top level menu)
     * @return string properly indented ready-to-use HTML
     * @uses show_tree_walk()
     */
    function get_menu($m='',$menu_id=NULL) {
        if (is_null($menu_id)) { // locate the toplevel section to open (if any)
            $node_id = $this->tree[0]['first_child_id'];
            for ( ; ($node_id != 0); $node_id = $this->tree[$node_id]['next_sibling_id']) {
                if ($this->tree[$node_id]['is_breadcrumb']) {
                    if (!($this->tree[$node_id]['is_page'])) {
                        $menu_id = $this->tree[$node_id]['first_child_id'];
                    }
                    break;
                }
            }
        }
        if (is_null($menu_id)) { // still nothing? we're done here
            return '';
        }
        $parent_id = $this->tree[$menu_id]['parent_id'];
        if ($parent_id == 0) {
            $menu = t('menu',$this->domain);
        } else {
            $menu = t('menu_menu',$this->domain,
                      array('{MENU}' => htmlspecialchars($this->tree[$parent_id]['record']['link_text'])));
        }
        return $m."<h3>".$menu."</h3>\n".
                   $this->show_tree_walk($m,$menu_id);
    } // get_menu()


    /** show footer text, maybe some quicklinks and 'powered by'
     *
     * @param string $m left margin for increased readability
     * @return string performance report
     */
    function get_bottomline($m='') {
        $separator = (empty($this->quickbottom_separator)) ? '' :
                     ' '.$this->quickbottom_separator; // readability
        $s = (empty($this->config['footer_text'])) ? '' : $m.$this->config['footer_text'].$separator."\n";
        $t  = $this->get_quickbottom($m);
        $s .= (empty($t)) ? '' : rtrim($t).$separator."\n";
        $s .= appropriate_legal_notices(TRUE,$m)."\n";
        return $s;
    } // get_bottomline()


    /** calculate the index of the current main navigation item
     *
     * this routine determines which main menu item is the current one,
     * starting at 1. if no menu item is current, the function returns 0.
     *
     * This index is used for two purposes:
     *  - calculating an ever changing background image for the header, and
     *  - determining which item of the comma-delimited pagenumbers to use for the sidebar.
     *
     * @return int index of current main menu item or 0 if none
     */
    function cornelia_navigation_index() {
        $index = 0;
        $next_id = $this->tree[0]['first_child_id'];
        for ( ; ($next_id != 0); $next_id = $this->tree[$next_id]['next_sibling_id']) {
            if ($this->tree[$next_id]['is_visible']) {
                ++$index;
                if ($this->tree[$next_id]['is_breadcrumb']) {
                    return $index;
                    break;
                }
            }
        }
        return 0;
    } // cornelia_navigation_index()


    /** compute the list of sidebar blocks for a main menu item
     *
     * this constructs an ordered array of node_id's corresponding
     * to the current main menu item. If the current main menu_item
     * indicates no sidebar then FALSE is returned.
     *
     * The basis for the node_ids to return is the contents of the
     * parameter 'sidebar_nodelist'. This is a comma-delimited string
     * containing node_ids, zeros or dashes.
     *
     * Example: suppose sidebar_nodelist contains "5,0,0,0,-".
     * If the first item of the main menu is current (ie. is part of
     * the breadcrumb trail), the node with node_id = 5 is examined.
     * If it is a page and the module is supported, this page's node_id
     * is returned. If, however, this is a section, all supported pages
     * within that section are returned.
     * For main menu items 2, 3 and 4 a '0' is specified. This means
     * that no pages will be displayed (an empty array is returned).
     * For main menu item 5 a dash ('-') is specified. This yields
     * a return value of FALSE which must be interpreted as 'suppress
     * the complete 3rd column of the page, including rightmargin_top
     * and rightmargin_bottom. This effectively reduces the 3-column
     * layout to a 2-column layout.
     *
     * Note that the node_ids in the sidebar_nodelist themselves must
     * not be under embargo but a hidden section (with visible pages)
     * is OK.
     *
     * Note that when there are more main menu items than entries in
     * the sidebar_nodelist, the value '0' is assumed.
     *
     * Finally, if no main menu item is current (eg. the visitor arrived
     * on a page via quicktop entries) then the value of '0' is assumed, too.
     *
     * @param int $index is the current main menu item or 0 for none
     * @return bool|array FALSE if no sidebar at all or an array node=>module pairs
     */
    function cornelia_sidebar_nodes_modules($index) {
        $nodes = array();

        // 1 -- if no current menu item: no sidebar blocks
        if ($index <= 0) {
            return $nodes; 
        }
        $sidebar_nodelist = explode(',',$this->config['sidebar_nodelist']);

        // 2 -- if no entry for this current menu item: no sidebar blocks either
        if (!isset($sidebar_nodelist[$index-1])) {
            return $nodes;
        }

        // 3 -- if the entry starts with a dash: indicate no 3rd column at all
        if (substr(trim($sidebar_nodelist[$index-1]),0,1) == '-') {
            return FALSE;
        }

        // 4 -- if the entry is zero: once again: no sidebar blocks
        if (($node_id = intval($sidebar_nodelist[$index-1])) <= 0) {
            return $nodes;
        }

        // 5 -- if the node does not exist: no sidebar blocks
        if (!isset($this->tree[$node_id])) {
            return $nodes;
        }

        // 6 -- OK, finally go look for visible pages with supported modules (currently only 'htmlpage')
        $modules = get_module_records();
        if ($this->tree[$node_id]['is_page']) {
            // 6A -- single visible page?
            if ($this->tree[$node_id]['is_visible']) {
                $module_id = $this->tree[$node_id]['record']['module_id'];
                if ($modules[$module_id]['name'] == 'htmlpage') {
                    $nodes[$node_id] = $module_id;
                }
            }
        } else { // section
            // 6B -- multiple visible pages in a possibly hidden section
            // Note: if the section is under embargo, any pages within it are under embargo too
            // this is done via the page's 'is_visible' property.
            $next_id = $this->tree[$node_id]['first_child_id'];
            for ( ; ($next_id != 0); $next_id = $this->tree[$next_id]['next_sibling_id']) {
                if (($this->tree[$next_id]['is_page']) && ($this->tree[$next_id]['is_visible'])) {
                    $module_id = $this->tree[$next_id]['record']['module_id'];
                    if ($modules[$module_id]['name'] == 'htmlpage') {
                        $nodes[$next_id] = $module_id;
                    }
                }
            }
        }

        // 7 -- return array of module_ids keyed by node_id (but could be empty)
        return $nodes;
    } // cornelia_sidebar_nodes_modules()


    /** retrieve data for sidebar 
     *
     * this retrieves the content of the various sidebar blocks (if any)
     * currently only the htmlpage module is supported.
     * 
     * @param array $sidebar_nodes contains node-module pairs to show
     * @param string $m add to human readability
     * @return string ready-to-use HTML-code
     */
    function cornelia_get_sidebar($sidebar_nodes,$m='') {
        $s = '';
        $modules = get_module_records();
        if (!empty($sidebar_nodes)) {
            $index = 0;
            foreach($sidebar_nodes as $node_id => $module_id) {
                ++$index;
                switch ($modules[$module_id]['name']) {
                case 'htmlpage':
                    $s .= $this->cornelia_get_sidebar_htmlpage($index,$node_id,$m);
                   break;
                }
            }
        }
        return $s;
    } // cornelia_get_sidebar()


    /** retrieve page data for htmlpage module on a node
     *
     * this retrieves data from the page $node_id (requires knowledge of
     * internals of htmlpage module/table) in order to show that in a box
     * in the 3rd column.
     * The content is wrapped in a div which has a unique ID of the form
     * sidebar_blockN where N counts from 1 upward and a common class
     * sidebar_htmlpage. This allows for connecting style information to
     * a particular sidebar block in a flexible way.
     *
     * @param in $index block number (1-based)
     * @param int $node_id identifies the pagedata to retrieve 
     * @param string $m increased readbility
     * @return string ready-to-use HTML-code
     */
    function cornelia_get_sidebar_htmlpage($index,$node_id,$m='') {
        $attributes = array('id' => 'sidebar_block'.strval($index),'class' => 'sidebar_htmlpage');
        $s = $m.html_tag('div',$attributes)."\n";
        // fetch actual content from database (requires knowledge of internals of htmlpage module/table)
        $table = 'htmlpages';
        $node_id = intval($node_id);
        $where = array('node_id' => $node_id);
        $fields = array('page_data');
        if (($record = db_select_single_record($table,$fields,$where)) === FALSE) {
            logger(sprintf('%s: no pagedata (node=%d): %s',__FUNCTION__,$node_id,db_errormessage()));
        } else {
            $s .= $record['page_data']."\n";
        }
        $s .= $m."</div>\n";
        return $s;
    } // cornelia_get_sidebar_htmlpage()


/*

The specified directory can be absolute or relative.
If it is absolute, it MUST start with a '/'. 

If the specified directory starts with a '/' it is assumed to start in
the data root, e.g. something like /areas/exemplum/banners. This will
eventually lead to a URL of the form
/file.php/areas/exemplum/banners/mentha-banner.jpg using
was_file_url()

If the specified directory does NOT start with a '/' it is assumed to
be a directory relative to the directory where index.php and friends
live, unless it starts with program/ in which case it is a static dir
somewhere in the program hierarchy.


*/

/** compute URL for a background image based on current time and index
 *
 * this routine returns a URL to be used as a background image. The
 * URL is different depending on the time of day and the $index parameter.
 * The latter is thought to be the position of the current item in the
 * main navigation (starting at 1) or 0 in case no item in the main
 * navigation is current.
 *
 * Configuration is done via two parameters:
 * the path in $this->config['header_banners_directory'] and
 * the interval in $this->config['header_banners_interval'].
 *
 * The interval is expressed in minutes. It means that a particular page
 * has the same background image for the duration of the interval and
 * another one in the next interval. The banners directory is supposed
 * to contain banner files of the correct dimensions, e.g. 980x170.
 *
 * If the specified directory starts with a '/' it is assumed to start in
 * the data root, e.g. something like /areas/exemplum/banners. This will
 * eventually lead to a URL of the form
 * /file.php/areas/exemplum/banners/mentha-banner.jpg using
 * was_file_url()
 *
 * If the specified directory does NOT start with a '/' it is assumed to
 * be a directory relative to the directory where index.php and friends
 * live, unless it starts with program/ in which case it is a static dir
 * somewhere in the program hierarchy. This is done via was_url().
 *
 * @param int $index indicates current main navigation item or 0 for none
 * @param bool $fully_qualified if TRUE forces the URL to contain a scheme, authority etc.
 * @return string ready to use URL
 * @uses was_file_url()
 * @uses was_url()
 */
    function cornelia_get_background_url($index,$fully_qualified=FALSE) {
        global $CFG;
        //
        // 0 -- sanity checks
        //
        $path = trim($this->config['header_banners_directory']);
        $interval = intval($this->config['header_banners_interval']);
        if ((empty($path)) || ($interval <= 0)) {
            return FALSE;
        }
        if ((!utf8_validate($path)) || (strpos('/'.$path.'/','/../') !== FALSE)) {
            logger(sprintf("%s.%s(): invalid path '%s'; bailing out",__CLASS__,__FUNCTION__,$path));
            return FALSE;
        }

        //
        // 1 -- where to find the files?
        //
        if (substr($path,0,1) == '/') {
            $full_path = $CFG->datadir.$path;
        } elseif (substr($path,0,8) == 'program/') {
            $full_path = $CFG->progdir.substr($path,7);
        } else {
            $full_path = $CFG->dir.'/'.$path;
        }
        // get rid of trailing slash if any
        if (substr($full_path,-1) == '/') { $full_path = substr($full_path,0,-1); }

        if (($handle = @opendir($full_path)) === FALSE) {
            logger(sprintf("%s.%s(): cannot open directory '%s'",__CLASS__,__FUNCTION__,$path));
            return FALSE;
        }

        //
        // 2 -- scan the directory for graphics files (skip the thumbnails)
        //
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
            if (substr($entryname,0,$prefix_len) == THUMBNAIL_PREFIX) { // thumbnail, next please
                continue;
            }
            if (($image_info = @getimagesize($full_entryname)) === FALSE) { // not an image, next please
                continue;
            }
            $files[] = $entryname;
        }
        closedir($handle);


        //
        // 3 -- is there any image at all?
        //        
        if (($n = sizeof($files)) <= 0) {
            return FALSE;
        }
        if ($n == 1) {
            $entryname = $files[0]; // Looks like Ford T: choice of 1
        } else {
            // use a different image every $interval minutes
            $entryname = $files[($index + time() / ( 60 * $interval)) % $n];
        }

        if (substr($path,0,1) == '/') {
            return was_file_url($path.'/'.$entryname,$fully_qualified);
        } else {
            return was_url($path.'/'.$entryname,$fully_qualified);
        }
    } // cornelia_get_background_url()

} // ThemeCornelia

?>