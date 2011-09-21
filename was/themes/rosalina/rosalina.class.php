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
# along with this program. If not, see http://websiteatschool.eu/license.html

/** /program/themes/rosalina/rosalina.class.php - a theme with HV Menu (Javascript-based)
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wastheme_rosalina
 * @version $Id: rosalina.class.php,v 1.3 2011/09/21 18:54:19 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** this class implements the rosalina theme (based on HV Menu
 *
 */
class ThemeRosalina extends Theme {

    /** @var array $menu_top holds limits for toplevel menu items (in px): min_width,char_width,max_width,height */
    var $menu_top;

    /** @var array $menu_sub holds limits for submenu items (in px): min_width,char_width,max_width,height */
    var $menu_sub;

    /** construct a ThemeRosalina object
     *
     * First we do the regular initialisation, and subsequently we calculate the
     * areas available to this user in $this->jumps; Also we set all hvmenu-parameters
     * that are not already set in $this->config. This makes it possible to drop certain
     * parameters from the configuration (see {@link rosalina_install()}) and still
     * construct a valid hvmenu config.
     *
     * Finally we pre-calculate the limits $menu_top and $menu_width for use in the
     * treewalker (see {@link rosalina_show_tree_walk()}).
     *
     * @param array $theme_record the record straight from the database
     * @param int $area_id the area of interest
     * @param int $node_id the node that will be displayed
     * @return void
     */
    function ThemeRosalina($theme_record,$area_id,$node_id) {
        global $USER;
        parent::Theme($theme_record,$area_id,$node_id);

        // Make sure all parameters will exist in $this->config even when not in theme properties table
        $hvmenu_defaults = array(
            'LowBgColor'           => "#FFFFFF",            // Background color when mouse is not over
            'LowSubBgColor'        => "#FFFFFF",            // Background color when mouse is not over on subs
            'HighBgColor'          => "#0000FF",            // Background color when mouse is over
            'HighSubBgColor'       => "#FF0000",            // Background color when mouse is over on subs
            'FontLowColor'         => "#000000",            // Font color when mouse is not over
            'FontSubLowColor'      => "#000000",            // Font color subs when mouse is not over
            'FontHighColor'        => "#FFFFFF",            // Font color when mouse is over
            'FontSubHighColor'     => "#FFFFFF",            // Font color subs when mouse is over
            'BorderColor'          => "#FF0000",            // Border color
            'BorderSubColor'       => "#0000FF",            // Border color for subs
            'BorderWidth'          => 2,                    // Border width
            'BorderBtwnElmnts'     => TRUE,                 // Border between elements 1 or 0
            'FontFamily'           => "verdana,sans-serif", // Font family menu items
            'FontSize'             => 9.0,                  // Font size menu items
            'FontBold'             => TRUE,                 // Bold menu items 1 or 0
            'FontItalic'           => FALSE,                // Italic menu items 1 or 0
            'MenuTextCentered'     => "left",               // Item text position 'left', 'center' or 'right'
            'MenuCentered'         => "left",               // Menu horizontal position 'left', 'center' or 'right'
            'MenuVerticalCentered' => "top",                // Menu vertical position 'top', 'middle','bottom' or static
            'ChildOverlap'         => 0.0,                  // horizontal overlap child/ parent (from -1.0 to 1.0)
            'ChildVerticalOverlap' => 0.0,                  // vertical overlap child/ parent (from -1.0 to 1.0)
            'StartTop'             => 0,                    // Menu offset x coordinate
            'StartLeft'            => 0,                    // Menu offset y coordinate
            'VerCorrect'           => 0,                    // Multiple frames y correction
            'HorCorrect'           => 0,                    // Multiple frames x correction
            'LeftPaddng'           => 6,                    // Left padding
            'TopPaddng'            => 2,                    // Top padding
            'FirstLineHorizontal'  => TRUE,                 // SET TO 1 FOR HORIZONTAL MENU, 0 FOR VERTICAL
            'MenuFramesVertical'   => FALSE,                // Frames in cols or rows 1 or 0
            'DissapearDelay'       => 1000,                 // delay before menu folds in
            'TakeOverBgColor'      => TRUE,                 // Menu frame takes over background color subitem frame
            'FirstLineFrame'       => "self",               // Frame where first level appears
            'SecLineFrame'         => "self",               // Frame where sub levels appear
            'DocTargetFrame'       => "self",               // Frame where target documents appear
            'TargetLoc'            => "hvmenu",             // span id for relative positioning
            'HideTop'              => FALSE,                // Hide first level when loading new document 1 or 0
            'MenuWrap'             => TRUE,                 // enables/ disables menu wrap 1 or 0
            'RightToLeft'          => FALSE,                // enables/ disables right to left unfold 1 or 0
            'UnfoldsOnClick'       => FALSE,                // Level 1 unfolds onclick/ onmouseover
            'WebMasterCheck'       => FALSE,                // menu tree checking on or off 1 or 0
            'ShowArrow'            => FALSE,                // Uses arrow gifs when 1
            'KeepHilite'           => TRUE,                 // Keep selected path highligthed
            'Arrws'                => 'tri.gif,5,10,'.      // Three arrow image files: "filename",width,height
                                      'tridown.gif,10,5,'.
                                      'trileft.gif,5,10'
            );
        foreach($hvmenu_defaults as $k => $v) {
            if (!isset($this->config['hvmenu_'.$k])) { $this->config['hvmenu_'.$k] = $v; }
        }
        $limits = (isset($this->config['menu_top'])) ? explode(',',$this->config['menu_top']) : '';
        $this->menu_top = array(
            (isset($limits[0])) ? intval($limits[0]) : 120, // min width of toplevel menu items (px)
            (isset($limits[1])) ? intval($limits[1]) : 8,   // average character width in toplevel memu item (px)
            (isset($limits[2])) ? intval($limits[2]) : 300, // max width of toplevel menu items (px)
            (isset($limits[3])) ? intval($limits[3]) : 20   // height of toplevel menu (px)
            );
        $limits = (isset($this->config['menu_sub'])) ? explode(',',$this->config['menu_sub']) : '';
        $this->menu_sub = array(
            (isset($limits[0])) ? intval($limits[0]) : 150, // min width of submenu items (px)
            (isset($limits[1])) ? intval($limits[1]) : 8,   // average character width in submemu item (px)
            (isset($limits[2])) ? intval($limits[2]) : 500, // max width of submenu items (px)
            (isset($limits[3])) ? intval($limits[3]) : 20   // height of submenu (px)
            );
        $this->quicktop_separator = '|';
        $this->quickbottom_separator = '|';
        $this->breadcrumb_separator = '-';
    } // ThemeRosalina()


    /** construct an output page in HTML
     *
     * This constructs a full HTML-page, starting at the DTD
     * and ending with the html closing tag.
     *
     * @return string complete HTML-page, ready for output
     */
    function get_html() {
        global $CFG;
        $s  = $this->dtd."\n".
              "<html>\n".
              "<head>\n".
              "  <!-- Website@School CMS licensed under GNU/AGPLv3 - http://websiteatschool.eu\n".
              "       Theme name: 'rosalina'\n".
              "       Design: Peter Fokker and OBS Rosa Boekdrukker based on an earlier version of the same name\n".
              "       Re-implemented by: Peter Fokker <peter@berestijn.nl>\n".
              "       May 2011\n\n".

              "       This theme uses HV Menu by Ger Versluis (http://www.burmees.nl/) - July 2003\n".
              "       Submitted to Dynamic Drive (http://www.dynamicdrive.com)\n".
              "       Visit http://www.dynamicdrive.com for this script and more\n".
              "  -->\n".
              $this->get_html_head('  ').
              "</head>\n".
              "<body>\n".
              "  <div id=\"top\">\n".
                   $this->get_popups($this->messages_top,'    ').
              "  </div>\n".
              "  <div id=\"page\">\n".
                   $this->rosalina_get_page_head('    ').
                   $this->get_div_breadcrumbs('    ').
                   $this->rosalina_navigation_menu('    ').
                   $this->get_div_messages('    ').
              "    <!-- content -->\n".
              "    <div id=\"content\">\n\n".
                     $this->get_content()."\n".
              "    </div>\n".
              "    <!-- end content -->\n\n".
              "    <!-- start page bottom -->\n".
              "    <div id=\"footer\">\n".
              "      <div id=\"quickbottom\">\n".
                       $this->get_quickbottom('        ');

        // At this point we have almost the complete page in $s, upto and including
        // the quicklinks at the bottom. The next item in this bottom line
        // is the 'appropriate legal notices' link and maybe the performance report.
        // After that we add the 'last updated' information and the (C)yyyy message
        // and all various closing tags. In order to report the correct # of queries,
        // we FIRST construct the remainder of the page (in $t), and finally return
        // the performance report as late as possible (in the return statement).

        // Construct the lastupdate and copyright message...
        $current_year = intval(strftime('%Y'));
        $create_year = intval(substr($this->node_record['ctime'],0,4));
        $aparams = array(
          '{UPDATE_YEAR}' => substr($this->node_record['mtime'],0,4),
          '{UPDATE_MONTH}' => substr($this->node_record['mtime'],5,2),
          '{UPDATE_DAY}' => substr($this->node_record['mtime'],8,2),
          '{SITENAME}' => $CFG->title,
          '{COPYRIGHT_YEAR}' => ($current_year == $create_year) ? strval($current_year)
                                                                : strval($create_year).'-'.strval($current_year));

        // ...and the remainder of the page
        $separator = (empty($this->quickbottom_separator)) ? '' : $this->quickbottom_separator.' '; // readability
        $t  = "        ".$separator.t('lastupdated',$this->domain,$aparams)."\n".
              "        ".$separator.t('copyright',$this->domain,$aparams)."\n".
              "      </div>\n".
              "      <div id=\"address\">\n".
                         $this->get_address('        ').
              "      </div>\n".
              "    </div>\n".
              "    <!-- end page bottom -->\n".
              "  </div>\n".

              "  <div id=\"bottom\">\n".
                   $this->get_popups($this->messages_bottom,'    ').
              "  </div>\n".
              "</body>\n".
              "</html>\n";

        // we want to add the line with performance 
        // information as late as possible to catch
        // as much as we can
        return $s.
               $this->get_bottomline('        ').
               $t;
    } // get_html()


    /** construct the page top
     *
     * @param string $m left margin for increased readability
     * @return string ready to use HTML
     */
    function rosalina_get_page_head($m='') {
        global $USER;
        $width = $this->config['logo_width'];
        $s = $m."<!-- start of page top -->\n".
             $m."<div id=\"header\">\n".
             $m."  <div id=\"logo\">\n".
             $m."    <!-- logo -->\n".
                       $this->get_logo($m.'      ')."\n".
             $m."  </div>\n".
             $m."  <div id=\"quicktop\">\n".
             $m."    <!-- quicklinks -->\n".
                       $this->get_quicktop($m.'      ').
             $m."  </div>\n".
             $m."  <div id=\"quickjump\">\n".
                       $this->get_jumpmenu($m.'    ').
             $m."  </div>\n".
             $m."</div>\n".
             $m."<!-- end of page top -->\n\n";
        return $s;
    } // rosalina_get_page_head()


    /** construct an image tag with the area logo
     *
     * This constructs HTML-code that displays the logo,
     * maybe in the form of a clickable map.
     *
     * This routine honours the preview_mode by replacing the
     * URLs with a bare "#" in order to prevent the visiter
     * to actually surf away from a preview
     *
     * @param string $m left margin for increased readability
     * @return string constructed image tag
     * @todo should we take path_info into account here too???? how about /area/aaa/node/nnn instead of /aaa/nnn???
     */
    function get_logo($m='') {
        global $WAS_SCRIPT_NAME,$USER;
        if (!isset($this->config['logo_image'])) {
            return '';
        }

        // 1 -- prepare common attributes for logo image (alt height title width and also src
        $attributes = array(
            'title' => (isset($this->config['logo_title'])) ? $this->config['logo_title'] : $this->area_record['title'],
            'alt' => (isset($this->config['logo_alt'])) ? $this->config['logo_alt'] : t('alt_logo',$this->domain)
            );
        if (isset($this->config['logo_width'])) {
            $attributes['width'] = $this->config['logo_width'];
        }
        if (isset($this->config['logo_height'])) {
            $attributes['height'] = $this->config['logo_height'];
        }
        $src = was_url($this->config['logo_image']); // apply some heuristics to perhaps qualify the src path

        // 2 -- maybe build one or more hotspots or otherwise create a simple clickable logo
        $hotspots = $this->config['logo_hotspots'];
        if ($hotspots > 0) { // logo is a clickable map with at least 1 hotspot
            $mapname = "logo_hotspots";
            $attributes['usemap'] = '#'.$mapname;
            $logo = $this->rosalina_hotspot_map($USER->is_logged_in,$mapname,$hotspots,$this->config,$m).
                    $m.html_img($src,$attributes);
        } else {
            $title = $this->area_record['title'];
            $params = array('area' => $this->area_id);
            $href = was_node_url(NULL,$params,$title,$this->preview);
            $logo = $m.html_a($href,NULL,NULL,html_img($src,$attributes));
        }
        return $logo;
    } // get_logo()


    /** create a hotspot map for the logo
     *
     * This constructs an HTML map called $mapname, with $hotspots shapes
     * linked to the corresponding URLs. Note that this routine honours the
     * the preview_mode by replacing URLs with a bare "#".
     *
     * The construction of this map is performed by consulting the theme configuration.
     * The shapes are defined via a single string per item, e.g. $parameters['logo_hotspot_1'].
     * However, note that $parameters may contain more information than just these
     * shape definitions (as a matter of fact it is simply a copy of the _full_ theme
     * configuration of this area).
     *
     * A shape definition line should look like this:
     *
     * shape ";" coords ":" href ";" title ";" alt_href ";" alt_title ";" target
     *
     * where the first three components are mandatory (shape, coords, href) and the
     * rest is optional. As a rule a hotspot is linked to 'href' and 'title'.
     * However, if the user is logged_in, the alternative parameters 'alt_href' and
     * 'alt_title' area used instead. This allows for a single hotspot acting diffently
     * based on the logged in status. If no alternative href/title is defined, the
     * standard href/title are used. The last parameter indicates the target of the
     * link. By default it is undefined which implies the same browser window. It
     * could be '_blank' to open in a fresh window. Note that the target parameter only
     * works when NOT in preview mode (otherwise they could still escape from the preview
     * window).
     *
     * Example: If the definition would be
     * <code>
     * "rectangle;0,0,284,71;/index.php?login=1;Login;/index.php?logout=1;Logout"
     * </code>
     * the hotspot would link to the login-box when the user is NOT logged in,
     * and the user would be logged out when she was already logged in.
     *
     * @param bool $logged_in use the alternative href/title if available
     * @param string $mapname name of the HTML-map
     * @param int $hotspots the number of hotspots to create
     * @param array $parameters an array holding (among other items) the $hotspots hotspot definition lines
     * @param string $m left margin for increased readability
     * @return string ready to use HTML
     *
     */
    function rosalina_hotspot_map($logged_in,$mapname,$hotspots,$parameters,$m='') {
        $s = $m.html_tag('map',array('name' => $mapname))."\n";
        for ($i = 1; $i <= $hotspots; ++$i) {
            $hotspot = explode(';',$parameters['logo_hotspot_'.strval($i)]); 
            $attributes = array(
                'shape'  => (isset($hotspot[0])) ? htmlspecialchars($hotspot[0]) : '',
                'coords' => (isset($hotspot[1])) ? htmlspecialchars($hotspot[1]) : '',
                'href'   => (isset($hotspot[2])) ? htmlspecialchars($hotspot[2]) : '#',
                'title'  => (isset($hotspot[3])) ? htmlspecialchars($hotspot[3]) : ''
                );
            if ($logged_in) {
                $attributes['href']  = ((isset($hotspot[4])) && (!(empty($hotspot[4])))) ? 
                                       htmlspecialchars($hotspot[4]) : $attributes['href'];
                $attributes['title'] = ((isset($hotspot[5])) && (!(empty($hotspot[5])))) ?
                                       htmlspecialchars($hotspot[5]) : $attributes['title'];
            }
            if ($this->preview_mode) {
                $attributes['href'] = '#';
            } elseif ((isset($hotspot[6])) && (!(empty($hotspot[6])))) {
                $attributes['target'] = htmlspecialchars($hotspot[6]);
            }
            $attributes['alt'] = $attributes['title'];
            $s .= $m.'  '.html_tag('area',$attributes)."\n";
        }
        $s .= $m."</map>\n";
        return $s;
    } // rosalina_hotspot_map()


    /** construct the navigation menu
     *
     * This implements the HV Menu by Ger Versluis.
     *
     * @param string $m left margin for increased readability
     * @return string ready to use HTML (including JavaScript)
     * @uses $CFG
     */
    function rosalina_navigation_menu($m='') {
        global $CFG;
        $attributes = array('type' => 'text/javascript',
                            'src'  => $CFG->progwww_short.'/themes/'.$this->theme_record['name'].'/menu_com.js');
        $s = "$m<!-- start of navigation menu -->\n".
             "$m<!-- HV Menu - by Ger Versluis (http://www.burmees.nl/) - July 2003 -->\n".
             "$m<!-- Submitted to Dynamic Drive (http://www.dynamicdrive.com)       -->\n".
             "$m<!-- Visit http://www.dynamicdrive.com for this script and more     -->\n".
             "$m<script type=\"text/javascript\">\n".
                $this->rosalina_hvmenu_config($m.'  ').
                $this->rosalina_hvmenu($m.'  ').
             "$m  function BeforeStart(){return}\n".
             "$m  function AfterBuild(){return}\n".
             "$m  function BeforeFirstOpen(){return}\n".
             "$m  function AfterCloseAll(){return}\n".
             "$m  function Go(){return}\n".
             "$m</script>\n".
              $m.html_tag('script',$attributes,'')."\n";

        $height_in_px = $this->menu_top[3] + 2 * $this->config['hvmenu_BorderWidth'];
        $s .= "$m<div id=\"hvmenu_container\" style=\"height: ${height_in_px}px;\">\n".
              "$m  <div id=\"hvmenu\">&nbsp;</div>\n".
              "$m</div>\n";
        //
        // non-javascript-menu here - graceful degradation.... 
        //
        $s .= "$m<noscript>\n".
              "$m  <div id=\"noscript\">\n".
                     $this->get_menu_areas($m.'    ').
                     parent::get_navigation($m.'    ').
                     parent::get_menu($m.'  ').
              "$m  </div>\n".
              "$m</noscript>\n".
              "$m<!-- end of navigation menu -->\n\n";
        return $s;
    } // rosalina_navigation_menu()


    /** construct the necessary JavaScript that HV Menu needs
     *
     * the configuration of HV Menu is stored in the theme configuration in $this->config.
     * Note that the names of the parameters are carefully chosen to match those that
     * HV Menu expects. These parameters can be recognised by the first 7 ASCII-characters
     * in their names, i.e. they all start with 'hvmenu_'.
     *
     * The PHP variable type determines how we translate the value of these parameters
     * to the correct JavaScript form. The array with pointers to images of arrows Arrws
     * is a special case. The values 'float' parameters get 2 decimals for completeness' sake.
     * Booleans map to integers 1 (True) and 0 (False).
     *
     * Another exception is the HV Menu-parameter NoOffFirstLineMenus (sic), The number of
     * top level menu items is the number of (visible) items in the tree.
     *
     * @param string $m left margin for increased readability
     * @return string ready to use HTML
     */
    function rosalina_hvmenu_config($m='') {
        global $CFG,$USER;

        // 1 -- calculate and store the number of items in the main (top level) menu in the JavaScript-configuration
        $item_count = $this->rosalina_menucount($this->tree[0]['first_child_id']);
        $s = $m."var NoOffFirstLineMenus=${item_count}\n";

        // 2 -- step through configuration and process 'our' parameters (those that start with 'hvmenu_')
        foreach($this->config as $k => $v) {
            if (substr($k,0,7) == 'hvmenu_') { // all ASCII; no UTF-8 issues here
                $jsvar = substr($k,7);
                if       (is_bool($v))        { $s .= sprintf("%svar %s=%d;\n",   $m,$jsvar,($v) ? 1 : 0);
                } elseif (is_int($v))         { $s .= sprintf("%svar %s=%d;\n",   $m,$jsvar, $v);
                } elseif (is_float($v))       { $s .= sprintf("%svar %s=%1.2f;\n",$m,$jsvar, $v);
                } elseif ($jsvar != 'Arrws')  { $s .= sprintf("%svar %s='%s';\n", $m,$jsvar, $v);
                } else {
                    $themedir = $CFG->progwww_short.'/themes/'.$this->theme_record['name'];
                    $arrows = explode(',',$v);
                    for ($i=0; $i<9; $i += 3) {
                        $s .= ($i == 0) ? $m."var Arrws=[" : ',';
                        $s .= "'${themedir}/${arrows[$i]}',${arrows[$i+1]},${arrows[$i+2]}";
                    }
                    $s .= "];\n";
                }
            }
        }
        return $s;
    } // rosalina_hvmenu_config()


    /** construct the necessary JavaScript code for definition of HV Menu
     *
     * this constructs the necessary configuration Array's for HV Menu.
     * The first menu (menu at the top level) can be either Horizontal or Vertical.
     * In the latter case we need to calculate the width of the menu in pixels (#width_px)
     * based on the longest menu item. In the former case every item has its own
     * individual length. This is indicated to the treewalker by setting $width_px to 0.
     *
     * @param string $m left margin for increased readability
     * @return string ready to use HTML
     * @uses $CFG
     */
    function rosalina_hvmenu($m='') {
        global $CFG;

        //
        // 1 -- maybe calculate width of vertical main menu
        //
        $subtree_id = $this->tree[0]['first_child_id'];
        if ($this->config['hvmenu_FirstLineHorizontal']) {
            $width_px = 0;
        } else { // if vertical menu calc a single width for all items in main menu
            $width = $this->rosalina_menuwidth($subtree_id);
            $width_px = min(max($this->menu_top[0],$width * $this->menu_top[1]),$this->menu_top[2]);
        }
        //
        // 2 -- generate menus by walking the tree
        //
        $s = $this->rosalina_show_tree_walk($m,$subtree_id,'Menu',$width_px);
        return $s;
    } // rosalina_hvmenu()


    /** this treewalker shows the current menu and descends recursively
     *
     * this routine creates a menu and descends intoe any submenus (sections)
     *
     * Note that we expect the caller to pre-calculate the width of the menu items (in $width_px).
     * However, if $width_px is 0, we calculate individual widths per item, using the limits from
     * $tis->menu_top, because only {@link rosalina_hvmenu()} can call us with $width_px = 0 and thus
     * we are creating the top level menu. Any submenus are constructed by recursion and this routine
     * never calls itself with $width_px = 0.
     *
     * @param string $m left margin for increased readability
     * @param int $subtree_id starting node of this menu
     * @param string $menu_name base name of the corresponding JavaScript menu
     * @param int $width_px the precalculated width of this menu OR 0 if individual widths wanted (only at top level)
     * @return string ready to use HTML
     */
    function rosalina_show_tree_walk($m='',$subtree_id,$menu_name,$width_px) {
        global $CFG;
        static $level = 0;
        $item_width_px = $width_px; // initially assume parent says all items are the same width

        $node_id = $subtree_id;
        $i = 0;
        $s = '';
        for ( ; ($node_id != 0); $node_id = $this->tree[$node_id]['next_sibling_id']) {
            if ($this->tree[$node_id]['is_visible']) {
                ++$i;
                $href = was_node_url($this->tree[$node_id]['record'],NULL,'',$this->preview_mode);
                if ($width_px <= 0) {
                    $width = utf8_strlen($this->tree[$node_id]['record']['link_text']);
                    $item_width_px = min(max($this->menu_top[0],$width * $this->menu_top[1]),$this->menu_top[2]);
                }
                if ($this->tree[$node_id]['is_page']) {
                    $s .= sprintf('%s%s%d=new Array("%s","%s","",%d,%d,%d);',
                                 $m,$menu_name,$i,
                                 htmlspecialchars($this->tree[$node_id]['record']['link_text']),
                                 $href,
                                 0,
                                 $this->menu_sub[3],$item_width_px)."\n";
                } else {
                    $sub_id = $this->tree[$node_id]['first_child_id'];
                    $sub_count = $this->rosalina_menucount($sub_id);
                    $sub_width = $this->rosalina_menuwidth($sub_id);
                    $sub_width_px = min(max($this->menu_sub[0],$sub_width * $this->menu_sub[1]),$this->menu_sub[2]);
                    $s .= sprintf('%s%s%d=new Array("%s","%s","",%d,%d,%d);',
                                 $m,$menu_name,$i,
                                 htmlspecialchars($this->tree[$node_id]['record']['link_text']),
                                 $href,
                                 $sub_count,
                                 $this->menu_sub[3],$item_width_px)."\n";
                    ++$level;
                    if ($level > MAXIMUM_ITERATIONS) {
                        logger(__FILE__.'('.__LINE__.') too many levels in node '.$node_id,WLOG_DEBUG);
                    } else {
                        $s .= $this->rosalina_show_tree_walk($m.'  ',$sub_id,$menu_name.strval($i).'_',$sub_width_px);
                    }
                    --$level;
                }
            }
        }
        return $s;
    } // rosalina_show_tree_walk()


    /** calculate the maximum-width of the items in the section (menu) starting at $node_id
     *
     * this steps through the linked list (section ) starting at $node_id
     * and determines the maximum width of the link text of visible items, expressed in characters
     * (not bytes)
     *
     * @param int $node_id indicates where to start
     * @return int maximum width in characters
     */
    function rosalina_menuwidth($node_id) {
        $width = 0;
        for ( ; ($node_id != 0); $node_id = $this->tree[$node_id]['next_sibling_id']) {
            if ($this->tree[$node_id]['is_visible']) {
                $width = max($width,utf8_strlen($this->tree[$node_id]['record']['link_text']));
            }
        }
        return $width;
    } // rosalina_menuwidth()


    /** calculate the number of items in the section (menu) starting at $node_id
     *
     * this steps through the linked list (section ) starting at $node_id
     * and simply counts the number of visible items
     *
     * @param int $node_id indicates where to start
     * @return int number of menu items
     */
    function rosalina_menucount($node_id) {
        $n = 0;
        for ( ; ($node_id != 0); $node_id = $this->tree[$node_id]['next_sibling_id']) {
            if ($this->tree[$node_id]['is_visible']) {
                ++$n;
            }
        }
        return $n;
    } // rosalina_menucount()


    /** construct a simple UL-based jump menu to select another area (when no Javascript is available)
     *
     * this constructs a list of clickable links to navigate to other areas.
     * This function is only used when the user has disabled Javascript (it is sandwiched between
     * noscript-tags, see {@link get_html()}). Note that there is no point in having a jump menu
     * when there is not at least another area. If there is only one, an empty string  is returned
     * 
     * @param string $m left margin for increased readability
     * @return string ready-to-use HTML or empty string if not at least 2 areas are available
     * @uses $WAS_SCRIPT_NAME
     */
    function get_menu_areas($m='') {
        global $WAS_SCRIPT_NAME;
        if (sizeof($this->jumps) <= 1) { // do not add jump menu if there's just 1 area
            return '';
        }
        $jump_bar = $m."<ul>\n";
        $href = ($this->preview_mode) ? "#" : $WAS_SCRIPT_NAME;
        foreach($this->jumps as $area_id => $area_title) {
            $params = array('area' => $area_id);
            $href = was_node_url(NULL,$params,$area_title,$this->preview_mode);
            $attributes = ($this->area_id == $area_id) ? array('class' => 'current') : NULL;
            $jump_bar .= $m.'  <li>'.html_a($href,NULL,$attributes,$area_title)."\n";
        }
        $jump_bar .= $m."</ul>\n";
        return $jump_bar;
    } // get_menu_areas()


    /** construct a list of quicklinks for bottom of page (if any) ending with a separator
     *
     * This is a slight variation of parent::get_quickbottom(): if there is at least
     * one quicklink at the bottom, we append a quickbottom separator to the result in order
     * to visually separate the quicklinks at the left from the appropriate legal notices.
     *
     * @param string $m left margin for increased readability
     * @return string constructed list of clickable links or an empty string
     * @uses get_quicklinks()
     */
    function get_quickbottom($m='') {
        $quicklinks = $this->get_quicklinks($m,'quickbottom_section_id',$this->quickbottom_separator);
        if (!empty($quicklinks)) {
            $quicklinks .= $m.$this->quickbottom_separator."\n";
        }
        return $quicklinks;
    } // get_quickbottom()

} // class ThemeRosalina

?>