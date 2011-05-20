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

/** /program/lib/theme.class.php - taking care of themes
 *
 * This file defines a base class for dealing with themes.
 * It is always included and it can be used as a starting point for
 * other themes by inheriting from this class.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: theme.class.php,v 1.9 2011/05/20 19:19:54 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** Methods to access properties of a theme
 */
class Theme {
    /** @var a copy of the corresponding record from the themes table */
    var $theme_record = NULL;

    /** @var int $theme_id primary key of the theme */
    var $theme_id = NULL;

    /** @var int $area_id the area to display */
    var $area_id = NULL;

    /** @var a copy of the area record from the areas table */
    var $area_record = NULL;

    /** @var int $node_id the node (page) to display */
    var $node_id = NULL;

    /** @var a convenient copy of the node record copied from the area tree */
    var $node_record = NULL;

    /** @var array $tree all nodes in area $area_id, keyed by $node_id (see {@link build_tree()}). */
    var $tree = FALSE;

    /** @var array $config all properties from themes_areas_properties for this combination of theme and area */
    var $config;

    /** @var string the standard doctype (default: HTML 4.01 Transitional) */
    var $dtd = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';

    /** @var string the title to display in both the title tag and in the page itself (usually the areaname) */
    var $title = '';

    /** @var array collection of individual httP-headers that are to be sent _before_ any HTML is sent */
    var $http_headers = array();

    /** @var array collection of items/lines that will be output as part of the HTML-head section */
    var $html_head = array();

    /** @var array collection of items/lines that are part of the content area */
    var $content = array();

    /** @var array collection of messages that are to be displayed via a javascript alert() at START of page */
    var $messages_top = array();

    /** @var array collection of messages that are to be displayed inline, contained within the HTML body */
    var $messages_inline = array();

    /** @var array collection of messages that are to be displayed via a javascript alert() at END of page */
    var $messages_bottom = array();

    /** @var bool this switches the navigation between image-based and text-based */
    var $high_visibility = FALSE;

    /** @var bool $preview_mode if TRUE, we are previewing a page (from pagemanager) */
    var $preview_mode = FALSE;

    /** @var bool $friendly_url if TRUE, links via index.php/nnn/book_mark_friendly_text otherwise index.php?node=nnn */
    var $friendly_url = FALSE;

    /** @var array $breadcrumb_addendum holds an array with additional anchors that can be set by the page's module */
    var $breadcrumb_addendum = array();

    /** @var string $domain the language domain where we get our translations from, usually 't_<themename>' */
    var $domain = '';

    /** @var array $jumps holds an area_id => area_title pair for every area this user can access */
    var $jumps = array();

    /** @var string $quicktop_separator contains the delimiter between quicklinks at the top of the page */
    var $quicktop_separator = '';

    /** @var string $quicktop_separator contains the delimiter between quicklinks at the bottom of the page */
    var $quickbottom_separator = '';

    /** @var string $breadcrumb_separator contains the delimiter between breadcrumbs in the breadcrumb trail */
    var $breadcrumb_separator = ' - ';

    /** construct a Theme object
     *
     * this stores the information about this theme from the database.
     * Also, we construct/read the tree of nodes for this area $area_id. This
     * information will be used lateron when constructing the navigation.
     * The node to display is $node_id.
     *
     * Also, we prepare a list of areas where the current user is allowed to go.
     * This is handy when constructing a jumpmenu and doing it here saves a trip
     * to the database lateron in {@link get_jumpmenu()}.
     *
     * @param array $theme_record the record straight from the database
     * @param int $area_id the area of interest
     * @param int $node_id the node that will be displayed
     * @return void
     */
    function Theme($theme_record,$area_id,$node_id) {
        global $USER,$CFG;
        $charset = 'UTF-8';
        $content_type = 'text/html; charset='.$charset;
        $this->add_http_header('Content-Type: '.$content_type);

        $this->theme_record = $theme_record;
        $this->theme_id = intval($theme_record['theme_id']);
        $this->area_id = intval($area_id);
        $this->jumps = array();

        // extract areas information and
        //  - grab a copy of the full area_record 'for future reference', and
        //  - make a list of areas accessible for this user (for the area jumpmenu)
        if (($areas = get_area_records()) !== FALSE) {
            $this->area_record = $areas[$this->area_id];
            foreach($areas as $id => $area) {
                if ((db_bool_is(TRUE,$area['is_active'])) &&
                    ((db_bool_is(FALSE,$area['is_private'])) || 
                     ($USER->has_intranet_permissions(ACL_ROLE_INTRANET_ACCESS,$id)))) {
                    $this->jumps[$id] = $area['title'];
                }
            }
        } else {
            $this->area_record = array('area_id' => $this->area_id,'title' => '?');
            logger(sprintf('constructor %s(): cannot get list of areas: %s',__FUNCTION__,db_errormessage()),LOG_DEBUG);
        }
        $this->node_id = intval($node_id);
        $this->tree = $this->construct_tree($this->area_id);
        $this->node_record = $this->tree[$node_id]['record'];
        $this->config = $this->get_properties($this->theme_id,$this->area_id);

        $this->add_meta_http_equiv(array(
            'Content-Type' => $content_type,
            'Content-Script-Type' => 'text/javascript',
            'Content-Style-Type' => 'text/css')
            );
        $this->title = $this->area_record['title'];
        $this->add_meta(array(
            'MSSmartTagsPreventParsing' => 'TRUE',
            'generator' => 'Website@School',
            'description' => 'Website@School Content Management System for schools',
            'keywords' => 'Website@School, CMS for schools'));
        $this->calc_breadcrumb_trail($node_id); // only set markers in tree, don't collect anchors yet
        $this->domain = 't_'.$this->theme_record['name']; // indicates where to look for translations

        if ((isset($this->config['style_usage_static'])) && 
            ($this->config['style_usage_static']) &&
            (isset($this->config['stylesheet']))) {
            $this->add_stylesheet($this->config['stylesheet']);
        }
        $this->friendly_url = ($CFG->friendly_url) ? TRUE : FALSE;
    } // Theme()


    /** send collected HTTP-headers to user's browser
     *
     * This sends the headers that still need to be sent.
     * These are collected in the array $this->http_headers.
     * If headers are already sent, this fact is logged (and the
     * collected headers are not sent).
     *
     * @return void
     */
    function send_headers() {
        if (!empty($this->http_headers)) {
            $file = '';
            $line = 0;
            if (headers_sent($file,$line)) {
                // headers were already sent, log this strange event
                logger("headers were already sent in file $file($line):\n".implode("\n",$this->http_headers));
            } else {
                foreach($this->http_headers as $hdr) {
                    header($hdr);
                }
            }
        }
    } // send_headers()


    /** send collected output to user's browser
     *
     * This first sends any pending HTTP-headers and subsequently
     * outputs the page that is constructed by $this->get_html()
     *
     * @return void and output sent to browser
     */
    function send_output() {
        $this->send_headers();
        echo $this->get_html();
    } // send_output()


    /** construct an output page in HTML
     *
     * This constructs a full HTML-page, starting at the DTD
     * and ending with the html closing tag.
     *
     * The page is constructed using nested DIVs, the layout
     * is taken care of in a separate style sheet. All knowledge
     * about the structure of the page is contained in this routine.
     *
     * The performance of the script (# of queries, execution time)
     * is calculated as late as possible, to catch as much as we can.
     * Therefore the construction is done in two parts and performance
     * is calculated last.
     *
     * The contents of the various DIVs is constructed in various
     * helper routines in order to make this routine easy to read
     * (by humans that is). The various helper routines all are called
     * with a string of space characters; this should improve the
     * the readability of the page that is generated eventually.
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
              "    <div id=\"header\">\n".
              "      <div id=\"logo\">\n".
                       $this->get_logo('        ').
              "      </div>\n".
              "      <div id=\"quicktop\">\n".
                       $this->get_quicktop('        ').
              "      </div>\n".
              "      <h1>".$this->title."</h1>\n".
              "    </div>\n".
                   $this->get_div_breadcrumbs('    ').
              "    <div id=\"navigation\">\n".
                     $this->get_navigation('      ',$this->high_visibility).
              "    </div>\n".
                   $this->get_div_messages('    ').

              "    <div id=\"menu\">\n".
                     $this->get_menu('      ').
              "    </div>\n".

              "    <div id=\"sidebar\">\n".
              "      <div class=\"item\">\n".
                       $this->get_jumpmenu('        ').
              "      </div>\n".
              "    </div>\n".

              "    <div id=\"content\">\n".
                     $this->get_content('      ').
              "    </div>\n".
              "    <div id=\"footer\">\n".
              "      <div id=\"quickbottom\">\n";

        $t  = $this->get_quickbottom('        ').
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

        // we want to add the line with performance 
        // information as late as possible to catch
        // as much as we can 
        return $s.
               $this->get_bottomline('        ').
               $t;
    } // get_html()


    /** get all lines in the HTML head section in a single, properly indented string
     *
     * @param string $m left margin for increased readability
     * @return string generated HTML-code
     * @todo also deal with Bazaar Style Style Sheets at node level in this routine (requires new field 'nodes.style')
     */
    function get_html_head($m='') {
        //
        // Start with the lines that were added to the head section earlier on
        //
        $s  = $this->get_lines($this->html_head,$m);

        //
        // Don't forget to add the title (may have been set/changed by some
        // underlying page or module, so we do it here as a last minute addition
        // rather than in the constructor).
        //
        $s .= $m.'<title>'.htmlspecialchars($this->title)."</title>\n";

        //
        // If the area specifies metadata, we include it,
        // maybe using the margin $m for every line, too
        //
        if (isset($this->area_record['metadata'])) {
            $metadata = trim($this->area_record['metadata']);
            if (!empty($metadata)) {
                if (strlen($m) > 0) {
                    $metadata = str_replace("\n","\n".$m,$metadata);
                }
                $s .= $m.$metadata."\n";
            }
        }

        //
        // If the area-theme configuration uses additional area style,
        // include it ad-hoc
        //
        if ((isset($this->config['style_usage_area'])) && 
            ($this->config['style_usage_area']) &&
            (isset($this->config['style']))) {
            $style = trim($this->config['style']);
            if (!empty($style)) {
                if (strlen($m) > 0) {
                    $style = str_replace("\n","\n  ".$m,$style);
                }
                $s .= $m."<style><!--\n".
                      $m."  ".$style."\n".
                      $m."--></style>\n";
            }
        }
        //
        // STUB
        //
        if ((isset($this->config['style_usage_node'])) && ($this->config['style_usage_node'])) {
            $s .= $m."<style><!--\n".
                  $m."  /* STUB Page level Bazaar Style Style goes here (if any) /STUB */\n".
                  $m."--></style>\n";
        }
        return $s;
    } // get_html_head()


    /** get all lines in the content DIV in a single properly indented string
     *
     * @param string $m left margin for increased readability
     * @return string generated HTML-code
     */
    function get_content($m='') {
        return $this->get_lines($this->content,$m);
    } // get_content()


    /** get lines from an array in a single properly indented string
     *
     * This is a workhorse to convert an array of lines to a
     * properly indented block of text.
     *
     * @param array $lines contains the lines to convert to a properly indented string
     * @param string $m left margin for increased readability
     * @return string properly indented block of text
     */
    function get_lines($lines,$m='') {
        $s = (empty($lines)) ? '' : $m.implode("\n".$m,$lines)."\n";
        return $s;
    } // get_lines()


    /** get a perhaps bulleted list of messages in a DIV
     *
     * This constructs an unordered list with messages, if there are any 
     * If there is no message at all, an empty string is returned (without DIV).
     * If there is a single message, no bullet is added to the message.
     * If there are two or more messages, bullets are added.
     *
     * Note that this routine is an exception with respect to
     * the DIV-tags: this helper routine DOES generate its own DIVs
     * whenever there is at least 1 message. This means that there
     * is no DIV at all when there are no messages.
     *
     * @param string $m left margin for increased readability
     * @param string $div_id contains id of the generated div
     * @return string constructed HTML with message(s) or empty string if no messages
     */
    function get_div_messages($m='',$div_id='messages') {
        $s = '';
        if (!empty($this->messages_inline)) {
            if (sizeof($this->messages_inline) > 1) {
                $ul_start = $m."  <ul>\n";
                $ul_stop  = $m."  </ul>\n";
                $li       = $m."    <li>";
            } else {
                $ul_start = '';
                $ul_stop  = '';
                $li       = $m."  ";
            }
            $s .= $m."<div id=\"$div_id\">\n".
                  $ul_start;
            foreach($this->messages_inline as $msg) {
                $s .= $li.htmlspecialchars($msg)."\n";
            }
            $s .= $ul_stop.
                  $m."</div>\n";
        }
        return $s;
    } // get_div_messages()


    /** construct javascript alerts for messages
     *
     * This constructs a piece of HTML that yields 0 or more
     * calls to the javascript alert() function, once per message.
     * If no messages need to be displayed an empty string is
     * returned.
     *
     * @param array @messages a collection of message to display via alert()
     * @param string $m left margin for increased readability
     * @return string generated HTML-code with Javascript or empty string
     */
    function get_popups($messages,$m='') {
        $s = '';
        if (!empty($messages)) {
            $s .= $m."<script>\n".
                  $m."<!--\n";
            foreach($messages as $message) {
                  $s .= $m."  ".javascript_alert($message)."\n";
            }
            $s .= $m."-->\n".
                  $m."</script>\n";
        }
        return $s;
    } // get_popups()


    /** construct breadcrumb trail
     *
     * this constructs a breadcrumb trail with clickable links. The crumbs are separated by
     * this->breadcrumb_separator (default ' - ').
     *
     * @param string $m left margin for increased readability
     * @return string ready to use HTML with 1 or more clickable bread crumbs
     */
    function get_div_breadcrumbs($m='') {
        $s = '';
        $crumbs = 0;
        if ((isset($this->config['show_breadcrumb_trail'])) && ($this->config['show_breadcrumb_trail'])) {
            $breadcrumbs = $this->calc_breadcrumb_trail($this->node_id);
            if (!empty($breadcrumbs)) {
                foreach($breadcrumbs as $anchor) {
                    $s .= $m.'  '.(($crumbs++ == 0) ? '' : $this->breadcrumb_separator).$anchor."\n";
                }
            }
            if (!empty($this->breadcrumb_addendum)) {
                foreach($this->breadcrumb_addendum as $anchor) {
                    $s .= $m.'  '.(($crumbs++ == 0) ? '' : $this->breadcrumb_separator).$anchor."\n";
                }
            }
            if ($crumbs > 0) {
                $s = $m."<div id=\"breadcrumbs\">\n".
                     $m."  ".t('you_are_here',$this->domain)."\n".
                     $s.
                     $m."</div>\n";
            }
        }
        return $s;
    } // get_div_breadcrumbs()


    /** construct an image tag with the area logo
     *
     * This constructs HTML-code that displays the logo.
     *
     * @param string $m left margin for increased readability
     * @return string constructed image tag
     * @todo should we take path_info into account here too???? how about /area/aaa/node/nnn instead of /aaa/nnn???
     */
    function get_logo($m='') {
        global $CFG,$WAS_SCRIPT_NAME;
        if (!isset($this->config['logo_image'])) {
            return '';
        }
        $attributes = array('title' => $this->area_record['title']);
        if (isset($this->config['logo_width'])) {
            $attributes['width'] = $this->config['logo_width'];
        }
        if (isset($this->config['logo_height'])) {
            $attributes['height'] = $this->config['logo_height'];
        }
        $src = $this->config['logo_image'];
        // if this path is not absolute and does not look like scheme:// (with two slashes),
        // we must assume that this path is relative to the directory where index.php resides
        // perhaps a dangerous assumption but... oh well
        if ((substr($src,0,1) != "/") && (strpos($src,"//") === FALSE)) {
            $src = $CFG->www_short.'/'.$src;
        }
        $attributes['alt'] = t('alt_logo',$this->domain);
        
        if ($this->preview_mode) {
            $href = "#";
            $params = NULL;
        } else {
            $href = $WAS_SCRIPT_NAME;
            $params = array('area' => $this->area_id);
        }
        return $m.html_a($href,$params,NULL,html_img($src,$attributes));
    } // get_logo()


    /** construct a list of quicklinks for top of page (if any)
     *
     * (see also {@link get_quickbottom()}).
     *
     * @param string $m left margin for increased readability
     * @return string constructed list of clickable links or an empty string
     * @uses get_quicklinks()
     */
    function get_quicktop($m='') {
        return $this->get_quicklinks($m,'quicktop_section_id',$this->quicktop_separator);
    } // get_quicktop()


    /** construct a list of quicklinks for bottom of page (if any)
     *
     * (see also {@link get_quicktop()}).
     *
     * @param string $m left margin for increased readability
     * @return string constructed list of clickable links or an empty string
     * @uses get_quicklinks()
     */
    function get_quickbottom($m='') {
        return $this->get_quicklinks($m,'quickbottom_section_id',$this->quickbottom_separator);
    } // get_quickbottom()


    /** workhorse for constructing list of quicklinks
     *
     * This creates HTML-code for links that can be displayed at the top/bottom
     * of the page. These links are the pages (but not subsections) defined
     * in the quicktop_section_id  or quickbottom_section_id in $this->config.
     *
     * Note that this array may or may not exist and also that the section may
     * or may not exist and that the section may or may not contain any visible
     * pages. Mmm, that's a lot of may/maynot's...
     *
     * Also note that these links are always displayed as text, even if a graphics
     * image is defined in the corresponding node. The contents of the section can
     * be found in $this->tree. If there are two or more links, they are separated
     * with $separator (default '');
     *
     * @param string $m left margin for increased readability
     * @param string $quick_section_id the name of the property that holds the section containing these quicklinks
     * @parameter string $separator separates individual items in the list
     * @return string constructed list of clickable links or an empty string
     */
    function get_quicklinks($m,$quick_section_id,$separator='') {
        global $WAS_SCRIPT_NAME;
        $s = '';

        if (!isset($this->config[$quick_section_id])) {
            return $s;
        }
        $node_id = $this->config[$quick_section_id];
        if ((!is_int($node_id)) || ($node_id <= 0) || (!isset($this->tree[$node_id]))){
            return $s;
        }
        // At this point we know that node $node_id actually exists in the tree.
        // It _should_ be a section and not a page. However, if it is a page, we'll
        // simply return that single page.
        if ($this->tree[$node_id]['is_page']) {
            if ($this->tree[$node_id]['is_visible']) {
                $attributes = ($this->tree[$node_id]['is_breadcrumb']) ? array('class' => 'current') : NULL;
                // force a text-only link
                $s .= $m.$this->node2anchor($this->tree[$node_id]['record'],$attributes,TRUE)."\n";
            }
        } else { // section
            $item_count = 0;
            $node_id = $this->tree[$node_id]['first_child_id'];
            for ( ; ($node_id != 0); $node_id = $this->tree[$node_id]['next_sibling_id']) {
                if ($this->tree[$node_id]['is_page']) {
                    if ($this->tree[$node_id]['is_visible']) {
                        $attributes = ($this->tree[$node_id]['is_breadcrumb']) ? array('class' => 'current') : NULL;
                        // force a text-only link
                        $s .= $m.(($item_count++ == 0) ? '' : $separator).
                                 $this->node2anchor($this->tree[$node_id]['record'],$attributes,TRUE)."\n";
                    }
                }
            }
        }
        return $s;
    } // get_quicklinks()

    /** construct a top level menu (navigation bar) as an unnumbered list (UL) of list items (LI)
     *
     * this simply walks through the top level of the menu tree and
     * creates a link for each node.
     *
     * @param string $m left margin for increased readability
     * @param bool $textonly forces a text-type link even when a navigation image is stipulated in the node record
     * @return string properly indented ready-to-use HTML
     */
    function get_navigation($m='',$textonly=FALSE) {
        $item_count = 0;
        $navbar = $m."<ul>\n";
        $next_id = $this->tree[0]['first_child_id'];
        for ( ; ($next_id != 0); $next_id = $this->tree[$next_id]['next_sibling_id']) {
            if ($this->tree[$next_id]['is_visible']) {
                $attributes = ($this->tree[$next_id]['is_breadcrumb']) ? array('class' => 'current') : NULL;
                $navbar .= $m."  <li>".$this->node2anchor($this->tree[$next_id]['record'],$attributes,$textonly)."\n";
                ++$item_count;
            }
        }
        $navbar .= $m."</ul>\n";
        return ($item_count > 0) ? $navbar : "\n";
    } // get_navigation()

    /** construct the submenu starting at $menu_id OR the first breadcrumb in the top level menu
     *
     * this constructs an 'infinitely' nested set of submenus, starting at $menu_id
     * or at the first breadcrumb in the top level menu (if any).
     * If there are no suitable nodes, an empty string is returned.
     *
     * @param string $m left margin for increased readability
     * @param int $menu_id indicates where to start the menu (NULL means the first breadcrumb in top level menu)
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
        return (is_null($menu_id)) ? '' : $this->show_tree_walk($m.'  ',$menu_id);
    } // get_menu()


    /** workhorse for constructing recursive menu (walk the tree) along the breadcrumb trail
     *
     * this constructs nested (sub)menus along the breadcrumb trail. The effect is
     * that the (sub)menus that lead to the current page ($this->node_id) are 'opened'
     * whereas the other submenus are 'closed'. The (sub)menus are constructed in the form
     * of nested UL's with LI's.
     *
     * The level of recursion of the list items (LI) is indicated via class='levelNNN'.
     * The type of item is indicated via class='page' or class='section'.
     * Finally the item has an addional class='current' when it is part of the breadcrumb trail.
     *
     * The actual A-tag of the link only indicates being part of the breadcrumb trail via class='current'.
     * 
     * It is up to the style sheet to visualise these items taking all variants into account.
     * Note that we only process visible pages and sections.
     *
     * @param string $m left margin for increased readability
     * @param int $subtree_id indicates where to start this (sub)menu
     * @return string properly indented ready-to-use HTML
     */
    function show_tree_walk($m='',$subtree_id) {
        static $level = 0;
        $class_level = 'level'.strval($level);
        $s = $m."<ul>\n";
        $node_id = $subtree_id;
        for ( ; ($node_id != 0); $node_id = $this->tree[$node_id]['next_sibling_id']) {
            if ($this->tree[$node_id]['is_visible']) {
                // 1 -- show this node
                $is_page        = $this->tree[$node_id]['is_page'];
                $is_breadcrumb  = $this->tree[$node_id]['is_breadcrumb'];
                $class          = ($is_breadcrumb) ? 'current ' : '';
                $class         .= (($is_page) ? 'page ' : 'section ').$class_level;
                $attributes     = ($is_breadcrumb) ? array('class' => 'current') : NULL;
                $s .= $m.'  '.html_tag('li',array('class' => $class)).
                              $this->node2anchor($this->tree[$node_id]['record'],$attributes)."\n";

                // 2 -- maybe descend to follow the breadcrumb trail
                if ((!$is_page) && ($is_breadcrumb)) { 
                    if (($subsubtree_id = $this->tree[$node_id]['first_child_id']) > 0) {
                        ++$level;
                        if ($level > MAXIMUM_ITERATIONS) {
                            logger(__FILE__.'('.__LINE__.') too many levels in node '.$node_id);
                        } else {
                            $s .= $this->show_tree_walk($m.'  ',$subsubtree_id);
                        }
                        --$level;
                    }
                } // current subsection
            } // visible
        } // for
        $s .= $m."</ul>\n";
        return $s;
    } // show_tree_walk()


    /** construct a simple jumplist to navigate to other areas
     *
     * this constructs a listbox with areas to which the current user has access.
     * The user can pick an area from the list and press the [Go] button to navigate
     * to that area. Only the active areas are displayed. Private areas are only displayed
     * when the user actually has access to those areas.
     *
     * This routine always shows the Submit-button even when JavaScript is turned 'off'. If it is 'on',
     * a tiny snippet auto-submits the form whenever the user selects another area; no need
     * press any button anymore. However, pressing the Go button is necessary when Javascript is 'off'.
     * Rationale: the user will find out soon enough that pressing the button is superfluous, and
     * as a benefit we keep the same look and feel no matter what the state of Javascript.
     *
     * We rely on the constructor to provide us with an array of area_id=>area_title pairs
     * in the $this->jumps array.
     *
     * @param string $m add readabiliy to output
     * @return string properly indented ready-to-use HTML or an empty string on error
     * @uses dialog_get_widget()
     */
    function get_jumpmenu($m='') {
        global $USER,$WAS_SCRIPT_NAME;

        // 1 -- KISS form with a whiff of javascript (but don't  get rid of the Go-button)
        $title = t('jumpmenu_area_title',$this->domain);
        $attributes = array('name' => 'area','title' => $title,'onchange' => 'this.form.submit();');
        $jumpmenu  = $m.html_form($WAS_SCRIPT_NAME,'get')."\n".
                     $m."  ".t('jumpmenu_area',$this->domain)."\n".
                     $m."  ".html_tag('select',$attributes)."\n";

        // 2 -- fill opened form/select with available areas
        foreach($this->jumps as $k => $v) {
            $attributes = array('title' => $title, 'value' => $k);
            if ($k == $this->area_id) {
                $attributes['selected'] = NULL;
            }
            $jumpmenu .= $m.'    '.html_tag('option',$attributes,$v)."\n";
        }

        // 3 -- add button and close all open tags.
        $jumpmenu .= $m."  </select>\n".
                     $m."  ".dialog_get_widget(dialog_buttondef(BUTTON_GO))."\n".
                     $m.html_form_close()."\n";
        return $jumpmenu;
    } // get_jumpmenu()


    /** show 'powered by' and (maybe) report basic performance indicators
     *
     * This calculates the execution time of the script and the
     * number of queries. Note a special trick: we retrieve
     * the translated string in a dummy variable before calculating
     * the number of queries because otherwise we might miss one 
     * or more query from the language/translation subsystem.
     *
     * Note: for the time being the performance report commented out (2010-12-08).
     * Update: as from 2011-05-20 the performance report only displayed while debug is on,
     *
     * @param string $m left margin for increased readability
     * @return string performance report
     */
    function get_bottomline($m='') {
        global $CFG;
        $dummy = t('generated_in','admin');
        $a = array('{DATE}'=>strftime("%Y-%m-%d %T"),
                   '{QUERIES}'=>performance_get_queries(),
                   '{SECONDS}'=>sprintf("%01.3f",performance_get_seconds()));
        $s = appropriate_legal_notices($this->high_visibility,$m)."\n";
        if ($CFG->debug) {
            $s .= $m."| ".t('generated_in','admin',$a)."\n";
        }
        return $s;
    } // get_bottomline()


    /** return the reconstructed URL in a single (indented) line
     *
     * This constructs the exact URL (including the GET-parameters)
     * of the current script. This URL is returned as HTML so it
     * can be displyed. It is NOT meant to be a clickable link, but
     * as a documentation of the actual URL that was used. Note that
     * this URL can be suppressed by an appropriate 'display:none'
     * in the stylesheet, making it an item that only appears on
     * a hardcopy (media="print") and not on screen.
     *
     * @param string $m left margin for increased readability
     * @return string reconstructed URL as text
     */
    function get_address($m='') {
        global $WAS_SCRIPT_NAME,$CFG;
        $url = $CFG->www.'/index.php';
        if (isset($_SERVER['PATH_INFO'])) {
            $path_info = $_SERVER['PATH_INFO'];
            $url .= htmlspecialchars($path_info);
        }
        if (!empty($_GET)) {
            $item_count = 0;
            foreach($_GET as $k => $v) {
                $url .= (($item_count++ == 0) ? '?' : '&amp;').rawurlencode($k).'='.rawurlencode($v);
            }    
        }
        return $m.'URL:'.$url."\n";
    } // get_address()


    /** add an HTTP-header
     *
     * @param string headerline to add
     * @return void
     */
    function add_http_header($headerline) {
        $this->http_headers[] = $headerline;
    } // add_http_header()


    /** add a header to the HTML head part of the document
     *
     * @param string headerline to add
     * @return void
     */
    function add_html_header($headerline) {
        $this->html_head[] = $headerline;
    } // add_html_header()


    /** add a message to the list of popup-messages at the TOP of the document
     *
     * @param string|array $message message(s) to add
     * @return void
     */
    function add_popup_top($message) {
        if (is_array($message)) {
            $this->messages_top = array_merge($this->messages_top,$message);
        } else {
            $this->messages_top[] = $message;
        }
    } // add_popup_top()


    /** add a message to the list of popup-messages at the BOTTOM of the document
     *
     * @param string|array $message message(s) to add
     * @return void
     */
    function add_popup_bottom($message) {
        if (is_array($message)) {
            $this->messages_bottom = array_merge($this->messages_bottom,$message);
        } else {
            $this->messages_bottom[] = $message;
        }
    } // add_popup_bottom()


    /** add a message to the list of inline messages, part of the BODY of the document
     *
     * @param string|array $message message(s) to add inline
     * @return void
     */
    function add_message($message) {
        if (is_array($message)) {
            $this->messages_inline = array_merge($this->messages_inline,$message);
        } else {
            $this->messages_inline[] = $message;
        }
    } // add_message()


    /** add a link to a stylesheet to the HTML head part of the document
     *
     * this adds a link to a stylesheet file to the HTML head part of the document.
     * Note that we qualify the path to prevent problems with incorrect assumptions
     * about relative URLs, see {@link was_url()}.
     *
     * @param string $url absolute or relative url of the stylesheet (see above)
     * @return void and url added to list of headers
     * @uses was_url()
     */
    function add_stylesheet($url) {
        $s = '<link rel="stylesheet" type="text/css" href="'.htmlspecialchars(was_url($url)).'">';
        $this->add_html_header($s);
    } // add_stylesheet()


    /** add a line with meta-information to the HTML head part of the document
     *
     * @param array $meta an array with name-value-pairs that should be added to the HTML head part
     * @return void and meta data added to headers
     */
    function add_meta($meta) {
        foreach($meta as $name => $content) {
            $this->add_html_header('<meta name="'.htmlspecialchars($name).'" content="'.htmlspecialchars($content).'">');
        }
    } // add_meta()


    /** add a line with http-equiv meta-information to the HTML head part of the document
     *
     * @param array $meta an array with name-value-pairs that should be added to the HTML head part
     * @return void
     */
    function add_meta_http_equiv($meta) {
        foreach($meta as $name => $content) {
            $this->add_html_header('<meta http-equiv="'.htmlspecialchars($name).'" content="'.htmlspecialchars($content).'">');
        }
    } // add_meta_http_equiv()


    /** add a line or array of lines to the content part of the document
     *
     * @param string|array $content the line(s) of text to add
     * @return void and content added to buffer
     */
    function add_content($content) {
        if (is_array($content)) {
            $this->content = array_merge($this->content,$content);
        } else {
            $this->content[] = $content;
        }
    } // add_content()


    /** set the preview mode
     *
     * this sets the preview mode of the page currently being built.
     * If it is set to TRUE, all internal URLs (such as those pointing to a node in the
     * breadcrumb trail or in menu items) will be equal to '#' which makes it more or
     * less impossible to leave the current page because a bare '#' is considered an unnamed
     * fragment and so no new page is loaded when the link is clicked; just the thing we need.
     *
     * @param bool $is_preview_mode TRUE enables preview mode, FALSE disables it
     * @return void and flag set
     */
    function set_preview_mode($is_preview_mode) {
        $this->preview_mode = ($is_preview_mode) ? TRUE : FALSE;
    } // set_preview_mode()


    /** retrieve configuration parameters for this combination of theme and area
     *
     *
     * @param int $theme_id
     * @param int $area_id
     * @return bool|array FALSE on error, or an array with parameters
     * @uses get_properties()
     */
    function get_properties($theme_id,$area_id) {
        $tablename = 'themes_areas_properties';
        $where = array('theme_id' => $theme_id, 'area_id' => $area_id);
        return get_properties($tablename,$where);
    } // get_properties()


    /** read all nodes from table for this area and construct a tree
     *
     * this constructs the tree for this area, and makes sure that only
     * non-hidden pages and non-empty sections are visible
     *
     * @param int $area_id the tree is built from nodes within this area
     * @return array an array with the node-records linked as a tree
     */
    function construct_tree($area_id) {
        $tree = build_tree($area_id);
        foreach($tree as $node_id => $item) {
            $tree[$node_id]['is_visible'] = FALSE;
            $tree[$node_id]['is_breadcrumb'] = FALSE;
        }
        $this->calc_tree_visibility($tree[0]['first_child_id'],$tree);
        return $tree;
    } // construct_tree()


    /** calculate the visibility of the nodes in the tree
     *
     * this flags visible nodes as visible. Here 'visible' means that
     *  - the node is not hidden, not expired and not under embargo
     *  - the section has at least 1 visible node (page or section)
     * As a side effect, any subtree starting at a hidden/expired/embargo'ed
     * section is completely set to invisible so we don't risk the change to
     * accidently show a page from an invisible section.
     * This routine walks through the tree recursively.
     *
     * @param int $node_id the starting point for the tree walking
     * @param array &$tree pointer to the current tree
     * @param bool $force_invisibility 
     * @return bool TRUE when there is at least 1 visible node, FALSE otherwise
     * @todo how about making all nodes under embargo visible when previewing a page
     *       or at least the path from the node to display?
     */
    function calc_tree_visibility($node_id,&$tree,$force_invisibility=FALSE) {
        $now = strftime("%Y-%m-%d %T");
        $visible_nodes = 0;
        for ($next_id = $node_id; ($next_id != 0); $next_id = $tree[$next_id]['next_sibling_id']) {
            if ($tree[$next_id]['is_page']) {
                if (($tree[$next_id]['record']['expiry'] < $now) ||
                    ($now < $tree[$next_id]['record']['embargo']) ||
                    ($force_invisibility) ||
                    ($tree[$next_id]['is_hidden'])) {
                    $tree[$next_id]['is_visible'] = FALSE;
                } else {
                    $tree[$next_id]['is_visible'] = TRUE;
                    ++$visible_nodes;
                }
            } else { //section
                if (($tree[$next_id]['record']['expiry'] < $now) || 
                    ($now < $tree[$next_id]['record']['embargo']) ||
                    ($force_invisibility)) {
                    $tree[$next_id]['is_visible'] = FALSE;
                    $this->calc_tree_visibility($tree[$next_id]['first_child_id'],$tree,TRUE);
                } elseif ($tree[$next_id]['is_hidden']) {
                    $tree[$next_id]['is_visible'] = FALSE;
                    $this->calc_tree_visibility($tree[$next_id]['first_child_id'],$tree);
                } else {
                    if ($this->calc_tree_visibility($tree[$next_id]['first_child_id'],$tree)) {
                        $tree[$next_id]['is_visible'] = TRUE;
                        ++$visible_nodes;
                    } else {
                        $tree[$next_id]['is_visible'] = FALSE;
                    }
                }
            }
        }
        return ($visible_nodes > 0) ? TRUE : FALSE;
    } // calc_tree_visiblity()


    /** set breadcrumbs in tree AND construct list of clickable anchors
     *
     * Note: the anchors are created with the current setting of the preview mode, so if that
     * changes after we construct a list of anchors we're in trouble. I prefer late binding, so
     * the real list to use should be created in the phase where the HTML-code is constructed.
     * Mmmmm...
     *
     * @param int $node_id the node for which to calculate/set the path to the root node
     * @return array an array with anchors to be used as a clickable path to node $node_id
     * @todo split into two separate routines, one to set the tree, another to construct the list of anchors
     */
    function calc_breadcrumb_trail($node_id) {
        global $WAS_SCRIPT_NAME;
        $tries = MAXIMUM_ITERATIONS;
        $breadcrumbs = array();
        $next_id = $node_id;
        for ( ; (($next_id != 0) && ($tries-- > 0)); $next_id = $this->tree[$next_id]['parent_id']) {
            if ($this->tree[$next_id]['is_hidden']) {
                return $breadcrumbs;
            } else {
                $this->tree[$next_id]['is_breadcrumb'] = TRUE;
                // construct a clickable anchor tag and force to text only
                $anchor = $this->node2anchor($this->tree[$next_id]['record'],NULL,TRUE);
                $breadcrumbs = array_merge(array($anchor),$breadcrumbs);
            }
        }
        if ($tries <= 0) {
            $breadcrumbs = array();
            // too many iterations (endless loop?)
            logger('DEBUG '.__FILE__.'('.__LINE__.'): too many iterations (endless loop?) in node '.$node_id,LOG_DEBUG);
        }

        // Insert the name of the current area as a bread crumb too
        if ($this->preview_mode) {
            $anchor = html_a("#",NULL,NULL,$this->area_record['title']);
        } else {
            $anchor = html_a($WAS_SCRIPT_NAME,array('area' => $this->area_id),NULL,$this->area_record['title']);
        }
        $breadcrumbs = array_merge(array($anchor),$breadcrumbs);
        return $breadcrumbs;
    } // calc_breadcrumb_trail()


    /** construct an anchor from a node record
     *
     * This constructs an array with key-value-pairs that can be used to
     * construct an HTML anchor tag. At least the following keys are created
     * in the resulting array: 'href', 'title' and 'anchor'. The latter is either
     * the text or a referenct to an image that is supposed to go between the
     * opening A-tag and closing A-tag. Furtermore an optional key is created: target.
     * The contents of the input array $attributes is merged into the result.
     *
     * If the parameter $textonly is TRUE the key 'anchor' is always text.
     * If $textonly is NOT TRUE, the 'anchor' may refer to an image.
     *
     * Note that the link text is always non-empty. If the node record has an
     * empty link_text, the word 'node' followed by the node_id is returned.
     * (Otherwise it will be hard to make an actual clickable link).
     *
     * Note that we attempt to create 'friendly' URLs, ie. URLs that look very
     * much like a plain path, e.g.
     * http://www.exemplum.eu/index.php/3/Information_about_the_school rather than
     * http://www.exemplum.eu/index.php?node=3
     * When bookmarking a page, the part 'Information_about_the_school' makes it
     * easier to recognise the bookmark than when it is just some number.
     * Choice for friendly URLs is made in the global (site) configuration.
     *
     * @param array $node_record the node record to convert
     * @param array $attributes optional attributes to add to the HTML A-tag
     * @param bool $textonly if TRUE, no clickable images will be returned
     * @return string an HTML A-tag that links to the node OR to the external link (if any)
     */
    function node2anchor($node_record,$attributes=NULL,$textonly=FALSE) {
        global $WAS_SCRIPT_NAME;

        $node_id = intval($node_record['node_id']);
        $title = $node_record['title'];
        $link_text = $node_record['link_text'];
        if (empty($link_text)) {
            $link_text = "[ ".strval($node_id)." ]";
        }

        if ($this->preview_mode) {
            $href = "#";
            $params = NULL;
        } elseif ($this->friendly_url) {
            $href = $WAS_SCRIPT_NAME."/".strval($node_id).'/'.$this->friendly_bookmark($title);
            $params = NULL;
        } else {
            $href = $WAS_SCRIPT_NAME;
            $params = array('node' => $node_id);
        }

        if (!is_array($attributes)) {
            $attributes = array();
        }
        $attributes['title'] = $title;
        if (!empty($node_record['link_target'])) {
            $attributes['target'] = $node_record['link_target'];
        }

        if (($textonly) || (empty($node_record['link_image']))) {
            $anchor = $link_text;
        } else {
            $img_attr = array('width' => intval($node_record['link_image_width']),
                              'height' => intval($node_record['link_image_height']),
                              'alt' => $link_text);
            $anchor = html_img(was_url($node_record['link_image']),$img_attr);
        }
        return html_a($href,$params,$attributes,$anchor);
    } // node2anchor()


    /** construct an alphanumeric string from a node title (for a readable bookmark)
     *
     * this strips everything from $title except alphanumerics and spaces.
     * The spaces are translated to an underscore. Length of result is limited to
     * an arbitrary length of 50 characters.
     *
     * Note that the $title is UTF-8 and may contain non-ASCII characters.
     * Ths routine deals with that situation by first converting the UTF-8
     * string to ASCII as much as possible (e.g. convert 'e-aigu' to plain 'e')
     * and subsequently converting all remaining non-letter/digits to an underscore
     *
     * @param string $title input text
     * @return string string with only alphanumerics and underscores, mas 50 chars
     */
    function friendly_bookmark($title) {
        $src = utf8_strtoascii($title);
        $tgt = '';
        $tgt_len = 0;
        $subst = FALSE;
        $n = utf8_strlen($src);
        for ($i = 0; (($i < $n) && ($tgt_len < 50)); ++$i) {
            $c = utf8_substr($src,$i,1);
            if (ctype_alnum($c)) {
                $tgt .= $c;
                $tgt_len++;
                $subst = FALSE;
            } else {
                if (!$subst) {
                    $tgt .= "_";
                    $tgt_len++;
                    $subst = TRUE;
                }
            }
        }
        return $tgt;
    } // friendly_bookmark()

    /** a helper-routine during development/debugging (currently unused)
     *
     * @param int $node_id start of the subtree
     * @param array &$tree) pointer to a tree that was built earlier
     * @return void but a dump of the tree in readable form sent to stdout
     */
    function dump_subtree($node_id,&$tree) {
        static $level = 0;
        $now = strftime("%Y-%m-%d %T");
        for ($next_id = $node_id; ($next_id != 0); $next_id = $tree[$next_id]['next_sibling_id']) {
            $indent = str_repeat('   ',$level);
            if ($tree[$next_id]['is_breadcrumb']) {
                $indent .= '>>>';
            } else {
                $indent .= ($tree[$next_id]['is_visible']) ? '+' : '-';
            }
            echo sprintf("%02d: %-12s node %3d (%s) %s %s %s %s %s\n",
                $level,
                $indent,
                $next_id,
                ($tree[$next_id]['is_page']) ?                     'page' : 'sect',
                ($tree[$next_id]['is_hidden']) ?                'hidden ' : '   -   ',
                ($tree[$next_id]['record']['expiry'] < $now) ?  'expired' : '   -   ',
                ($now < $tree[$next_id]['record']['embargo']) ? 'embargo' : '   -   ',
                ($tree[$next_id]['is_visible']) ?               'visible' : '   -   ',
                ($tree[$next_id]['is_breadcrumb']) ?            ' bread ' : '   -   ');
            if (!$tree[$next_id]['is_page']) {
                ++$level;
                $this->dump_subtree($tree[$next_id]['first_child_id'],$tree);
                --$level;
            }
        }
    } // dump_subtree()



} // class Theme

?>