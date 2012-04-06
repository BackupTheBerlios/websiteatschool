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

/** /program/themes/schoolyard/schoolyard.class.php - implements the Schoolyard Theme by David Prousch
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wastheme_schoolyard
 * @version $Id: schoolyard.class.php,v 1.4 2012/04/06 18:47:25 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** this class implements the schoolyard theme (based on a design by David Prousch)
 *
 */
class ThemeSchoolyard extends Theme {

    /** construct a ThemeSchoolyard object
     *
     * First we do the regular initialisation, and subsequently we set a few parameters
     * (very un-exciting).
     *
     * @param array $theme_record the record straight from the database
     * @param int $area_id the area of interest
     * @param int $node_id the node that will be displayed
     * @return void
     */
    function ThemeSchoolyard($theme_record,$area_id,$node_id) {
        parent::Theme($theme_record,$area_id,$node_id);
        $this->quicktop_separator    = '|'; //  override default values in parent
        $this->quickbottom_separator = '|';
        $this->breadcrumb_separator  = '-';
    } // ThemeSchoolyard()


    /** construct an output page in HTML
     *
     * This constructs a full HTML-page, starting at the DTD
     * and ending with the html closing tag.
     *
     * This routine returns a full HTML-page, usually including
     * logo, (area) title, main navigation, breadcrumbs trail (optional),
     * menu, jumpmenu and a footer with links to printer friendly version + logout.
     * If the page is called with print=1 as one of the get-parameters, all those
     * extra stuff is suppressed by including the additional print.css stylesheet
     * (configurable). This allows for making a clean print of only the content.
     * This additional stylesheet is added only once, even if this routine is
     * called more than once (shouldn't happen). This stylesheet is configurable
     * just like the regular stylesheet.
     *
     * Note that there might be a jumpmenu (to go to other areas). This is only
     * displayed if there is another area to go to. If the current area is the only
     * one available, we don't bother the user with an extra navigation widget.
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

        // helpers for CSS-design
        //$this->add_popup_top('Test TOP');
        //$this->add_message('Test Message');
        //$this->add_popup_bottom('Test BOTTOM');

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
        // 1 -- Construct the page (part 1)
        $s  = $this->dtd."\n".
              "<html>\n".
              "<head>\n".
              "  <!-- Website@School CMS licensed under GNU/AGPLv3 - http://websiteatschool.eu\n".
              "       Theme name: 'schoolyard'\n".
              "       Design: David Prousch, <translators@websiteatschool.eu> (May 2006)\n".
              "       Re-implemented by: Peter Fokker <peter@berestijn.nl> (June 2011)\n".
              "  -->\n".
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
              "      <h1>".$this->title."</h1>\n".
              "    </div>\n".
              "    <div id=\"navigation\">\n".
                     $this->get_navigation('      ',$this->text_only).
              "    </div>\n".
              "    <div id=\"navigation_belt\"></div>\n".
              "    <div id=\"information\">\n".
                     $this->get_div_breadcrumbs('      ').
                     $this->schoolyard_get_div_quicktop('      ').
              "    </div>\n".
                   $this->get_div_messages('    ').
              "    <div id=\"menu\">\n".
                     $this->get_menu('    ').
                     ((sizeof($this->jumps) <= 1) ? '' : $this->get_jumpmenu('      ')).
              "    </div>\n".
              "    <!-- content -->\n".
              "    <div id=\"content\">\n\n".
              "      <h2 id=\"content_title\">".htmlspecialchars($this->node_record['title'])."</h2>\n".
                     $this->get_content()."\n".
              "    </div>\n".
              "    <div id=\"lastupdate\">".t('lastupdated',$this->domain,$aparams)."</div>\n".
              "    <!-- end content -->\n\n".
              "    <!-- start page bottom -->\n".
              "    <div id=\"footer\">\n".
              "      <div id=\"quickbottom\">\n";

        $quickbottom = $this->get_quickbottom('        ');
        if (!empty($quickbottom)) {
              $s .= $quickbottom.' '.$this->quickbottom_separator."\n";
        }

        // At this point we have almost the complete page in $s, upto the 'powered by' link.
        // That link with 'appropriate legal notices' is added last. We now continue with the
        // remainder of the page (everthing that follows the 'appropriate legal messages').
        // This way we capture the correct # of queries, etc., including those needed for the
        // remainder of the page. The performance report is sandwiched between $s and $t in the return statement.

        // 2 -- Construct the page (part 2)
        $separator = (empty($this->quickbottom_separator)) ? '' : $this->quickbottom_separator.' '; // readability
        $t  = "        ".$separator.t('copyright',$this->domain,$aparams)."\n".
                         $this->schoolyard_printpage($separator,'        ').    // maybe print the print-page link
                         $this->schoolyard_logout($separator,'        ').       // maybe print the logout $USERNAME link
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

        // we want to add the line with performance information as late as possible to catch as much as we can
        return $s.
               $this->get_bottomline('        ').
               $t;
    } // get_html()


    /** construct a 'print this page' link
     *
     * @param string $separator a visual separator that is prepended
     * @param string $m margin for increased readability
     * @return string ready to use HTML-code
     */
    function schoolyard_printpage($separator='',$m='') {
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
        return $m.$separator.html_a($url,$params,$attributes,t('print',$this->domain))."\n";
    } // schoolyard_printpage()


    /** conditionally construct a logout link
     *
     * @param string $separator a visual separator that is prepended
     * @param string $m margin for increased readability
     * @return string ready to use HTML-code
     */
    function schoolyard_logout($separator='',$m='') {
        global $CFG,$USER;
        if ($USER->is_logged_in) {
            $aparams = array('{USERNAME}' => htmlspecialchars($USER->username),
                             '{FULL_NAME}' => htmlspecialchars($USER->full_name));
            $anchor = t('logout_username',$this->domain,$aparams);
            $params = array('logout' => '1');
            $attributes = array('title' => t('logout_username_title',$this->domain,$aparams));
            $s = $m.$separator.html_a($CFG->www_short.'/index.php',$params,$attributes,$anchor)."\n";
        } else {
            $s = ''; // or perhaps a login prompt?
        }
        return $s;
    } // schoolyard_logout()


    /** construct an optional div for quicklinks at the top if any
     *
     * @param string $m margin for readability
     * @return string ready-to-use HTML-code for div or empty string of nothing to show
     */
    function schoolyard_get_div_quicktop($m='') {
        $quicktop = $this->get_quicktop($m.'  ');
        if (empty($quicktop)) {
            return '';
        }
        return $m."<div id=\"quicktop\">\n".
                     $quicktop.
               $m."</div>\n";
    } // schoolyard_get_div_quicktop()
} // ThemeSchoolyard

?>