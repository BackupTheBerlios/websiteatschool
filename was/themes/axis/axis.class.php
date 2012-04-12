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

/** /program/themes/axis/axis.class.php - implements the Axis Theme
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wastheme_axis
 * @version $Id: axis.class.php,v 1.1 2012/04/12 20:57:18 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** this class implements the axis theme
 *
 */
class ThemeAxis extends Theme {

    /** construct an output page in HTML
     *
     * This constructs a full HTML-page, starting at the DTD
     * and ending with the html closing tag.
     *
     * This routine returns a full HTML-page, including a navigation
     * menu and a footer with links to printer friendly version + logout.
     * If the page is called with print=1 as one of the get-parameters, the
     * background and navigation menu are suppressed by including the
     * additional print.css stylesheet (configurable).
     * This more or less allows for making a clean print of only the content.
     * This additional stylesheet is added only once, even if this routine is
     * called more than once (shouldn't happen). This stylesheet is configurable
     * just like the regular stylesheet.
     *
     * Suppressing the background image (for printing) involves NOT generating
     * the container div with id="page", or rather: we use a different id when
     * viewed in regulare mode (id="page") or when viewed in print mode
     * (id="print"). This allows for different tricks in print.css and at the
     * very least allows for suppressing the background image.
     *
     * @return string complete HTML-page, ready for output
     */
    function get_html() {
        global $CFG,$USER;
        static $dejavu = 0;
        if ((isset($_GET['print'])) && (intval($_GET['print']) == 1) && ($dejavu++ == 0)) {
            if ((isset($this->config['style_usage_static'])) && 
                ($this->config['style_usage_static']) &&
                (isset($this->config['stylesheet_print']))) {
                $this->add_stylesheet($this->config['stylesheet_print']);
            }
        }
        $container = ($dejavu) ? "print" : "page";

        // helpers for CSS-design
        // $this->add_popup_top('Test TOP');
        // $this->add_message('Test Message');
        // $this->add_popup_bottom('Test BOTTOM');

        // 0 -- Prepare for the lastupdate and copyright message
        $current_year = intval(strftime('%Y'));
        $create_year = intval(substr($this->node_record['ctime'],0,4));
        $aparams = array(
          '{UPDATE_YEAR}' => substr($this->node_record['mtime'],0,4),
          '{UPDATE_MONTH}' => substr($this->node_record['mtime'],5,2),
          '{UPDATE_DAY}' => substr($this->node_record['mtime'],8,2),
          '{SITENAME}' => $CFG->title,
          '{COPYRIGHT_YEAR}' => ($current_year == $create_year) ? strval($current_year)
                                                                : strval($create_year).'-'.strval($current_year));
        $separator = ' |';
        // 1 -- Construct the page in one go (KISS).
        $s  = $this->dtd."\n".
              "<html>\n".
              "<head>\n".
              "  <!-- Website@School CMS licensed under GNU/AGPLv3 - http://websiteatschool.eu\n".
              "       Theme name: 'axis'\n".
              "       Implemented by: Peter Fokker <peter@berestijn.nl> (April 2012)\n".
              "  -->\n".
              $this->get_html_head('  ').
              "</head>\n".
              "<body>\n".
              "  <div id=\"top\">\n".
                   $this->get_popups($this->messages_top,'    ').
              "  </div>\n".

              "  <div id=\"$container\">\n".
              "    <div id=\"header\"></div>\n".
              "    <div id=\"menu\">\n".
                     $this->get_menu('    ',$this->tree[0]['first_child_id']).
              "    </div>\n".

              "    <!-- content -->\n".
              "    <div id=\"content\">\n\n".
                     $this->get_div_messages('      ').
              "      <h2 id=\"content_title\">".htmlspecialchars($this->node_record['title'])."</h2>\n".
                     $this->get_content()."\n".
              "    </div>\n".
              "    <!-- end content -->\n\n".

              "    <!-- start page bottom -->\n".
              "    <div id=\"footer\">\n".
              "      <div id=\"footer-left\">".t('copyright',$this->domain,$aparams)."</div>\n".
              "      <div id=\"footer-right\">\n".
                       $this->axis_logout($separator,'        ').
                       $this->axis_printpage($separator,'        ').
                       appropriate_legal_notices(TRUE,'        ')."\n".
              "      </div>\n".
              "    </div>\n".
              "    <div id=\"address\">\n".
                       $this->get_address('      ').
              "    </div>\n".
              "  </div>\n".

              "  <div id=\"bottom\">\n".
                   $this->get_popups($this->messages_bottom,'    ').
              "  </div>\n".
              "</body>\n".
              "</html>\n";
        return $s;
    } // get_html()


    /** construct a 'print this page' link
     *
     * this link is added on the left of the 'powered by websiteatschool'
     * widget at the bottom of the page, unless we are already creating
     * a print version.
     *
     * @param string $separator a visual separator that is prepended
     * @param string $m margin for increased readability
     * @return string ready to use HTML-code
     */
    function axis_printpage($separator='',$m='') {
        global $CFG;
        if ((isset($_GET['print'])) && ($_GET['print'] != 0)) { // printpage already requested; suppress print-link
            return ''; 
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
        return $m.html_a($url,$params,$attributes,t('print',$this->domain))." ".$separator."\n";
    } // axis_printpage()


    /** conditionally construct a logout link
     *
     * this link is added on the left of the 'powered by websiteatschool'
     * and 'print' widgets at the bottom of the page, but only if the
     * current user is logged in.
     *
     * @param string $separator a visual separator that is appended
     * @param string $m margin for increased readability
     * @return string ready to use HTML-code
     */
    function axis_logout($separator='',$m='') {
        global $CFG,$USER;
        if ($USER->is_logged_in) {
            if ((isset($_GET['print'])) && ($_GET['print'] != 0)) { // no point to _print_ the logout link
                return ''; 
            }
            $aparams = array('{USERNAME}' => htmlspecialchars($USER->username),
                             '{FULL_NAME}' => htmlspecialchars($USER->full_name));
            $anchor = t('logout_username',$this->domain,$aparams);
            $params = array('logout' => '1');
            $attributes = array('title' => t('logout_username_title',$this->domain,$aparams));
            $s = $m.html_a($CFG->www_short.'/index.php',$params,$attributes,$anchor)." ".$separator."\n";
        } else {
            $s = ''; // or perhaps a login prompt?
        }
        return $s;
    } // axis_logout()

} // ThemeAxis

?>