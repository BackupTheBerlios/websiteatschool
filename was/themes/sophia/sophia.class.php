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

/** /program/themes/sophia/sophia.class.php - the class that implements the theme
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wastheme_sophia
 * @version $Id: sophia.class.php,v 1.2 2013/06/11 11:25:55 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

class ThemeSophia extends Theme {

    function ThemeSophia($theme_record,$area_id,$node_id) {
        parent::Theme($theme_record,$area_id,$node_id);
        $this->quickbottom_separator = '|';
    }

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
              "      <h2>".$this->config['header_text']."</h2>\n".
              "      <div id=\"navigation\">\n".
                       $this->get_navigation('        ',$this->text_only).
              "      </div>\n".
              "    </div>\n".
                   $this->get_div_breadcrumbs('    ').
                   $this->get_div_messages('    ').
              "    <div id=\"leftmargin\">\n".
              "      <div id=\"leftmargin_top\">\n".
                       $this->config['left_top_html'].
              "      </div>\n".
              "      <div id=\"menu\">\n".
                       $this->get_menu('        ').
              "      </div>\n".
              "      <div id=\"leftmargin_bottom\">\n".
                       $this->config['left_bottom_html'].
              "      </div>\n".
              "    </div>\n".
              "    <div id=\"content\">\n".
              "      <h2 id=\"content_title\">".htmlspecialchars($this->node_record['title'])."</h2>\n".
                     $this->get_content('      ').
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


    /** construct a top level menu (navigation bar) as an unnumbered list (UL) of list items (LI)
     *
     * this walks through the top level of the menu tree and
     * creates a link for each node with a distinct class for every LI
     * This allows for buttons in different shapes and colours.
     *
     * By default the theme comes with three different shapes (see also style.css):
     *  - an ellipse (file: stencil0.png, 110x70)
     *  - a hexagon (file: stencil6.png, 110x70)
     *  - a drum (file: stencil8.png, 110x70)
     *
     * Different colours are assigned (in style.css) by overruling the
     * default colour using the (unique) class of each button. Look for
     * classes "navigation_buttonN", with N=1,2,...
     *
     * @param string $m left margin for increased readability
     * @param bool $textonly forces a text-type link even when a navigation image is stipulated
     * @return string properly indented ready-to-use HTML
     */
    function get_navigation($m='',$textonly=FALSE) {
        $item_count = 0;
        $navbar = $m."<ul>\n";
        $next_id = $this->tree[0]['first_child_id'];
        for ( ; ($next_id != 0); $next_id = $this->tree[$next_id]['next_sibling_id']) {
            if ($this->tree[$next_id]['is_visible']) {
                ++$item_count;
                $attr_li = array('class' => sprintf('navigation_button%d%s',$item_count,
                                    ($this->tree[$next_id]['is_breadcrumb']) ? ' current' : ''));
                $attr_a = ($this->tree[$next_id]['is_breadcrumb']) ? array('class' => 'current') : NULL;
                $navbar .= $m.'  '.
                           html_tag('li',$attr_li).
                           $this->node2anchor($this->tree[$next_id]['record'],$attr_a,$textonly)."\n";
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
                   $this->show_tree_walk($m.'  ',$menu_id);
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

} // ThemeSophia

?>