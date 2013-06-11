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

/** /program/lib/htmllib.php - useful functions for generating HTML-code
 *
 * This file provides various utility routines that aid in creating HTML-code.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: htmllib.php,v 1.6 2013/06/11 11:26:05 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

// ==================================================================
// ========================= HTML ROUTINES ==========================
// ==================================================================

/** construct an HTML A tag with optional parameters and attributes
 *
 * this constructs an A tag of the form
 * <pre>
 * &lt;a href="$href?p1=v1&p2=v2 att1="val1" att2="val2"&gt;
 * </pre>
 *
 * If no parameters are specified, nothing is added to the href.
 * If no attributes are specified the tag only has the href attribute
 * If $params is an array, the elements of this array are added to
 * the href after a rawurlencode(). The complete $href is then
 * escaped via htmlspecialchars().
 * If $attributes is an array, all elements are added
 * as escaped key-value-pairs. If string, then just append.
 * If $anchor is not empty, the string is appended to the constructed
 * opening tag and subsequently a closing tag is appended.
 *
 * Note that urlencoding and specialchars are applied to the URL property
 * and that the other properties are only are htmlspecialchars()'ed.
 * The optional $anchor is not changed in any way.
 *
 * Examples:
 * <pre>
 * html_a('index.php'): &lt;a href="index.php"&gt;
 * html_a('index.php',array('foo'=>'bar')): &lt;a href="index.php?foo=bar"&gt;
 * html_a('index.php',array('x'=>'y'),array('title'=>'foo')): &lt;a href="index.php?x=y" title="foo"&gt;
 * html_a('index.php','',array('class'=>'dimmed'),'baz'): &lt;a href="index.php" class="dimmed"&gt;baz&lt;/a&gt;
 * html_a('','',array('name'=>'chapter1'),'chapter 1'): &lt;a name="chapter1"&gt;chapter 1&lt;/a&gt;
 * </pre>
 *
 * @param string $href holds the hypertext reference
 * @param string|array $params holds the parameters to add to the $href
 * @param string|array $attributes holds the attributes to add to the tag
 * @param string $anchor if not empty this string and a closing tag are appended
 * @return string ready to use A tag
 */
function html_a($href='',$params=NULL,$attributes=NULL,$anchor=NULL) {
    global $WAS_SCRIPT_NAME;
    $s = "<a";
    if (!empty($href)) {
        if (!$params !== NULL) {
            if (is_array($params)) {
                $glue = "?";
                foreach ($params as $k => $v) {
                    $href .= $glue.rawurlencode($k)."=".rawurlencode($v);
                    $glue = "&";
                }
            } elseif (is_string($params)) {
                $href .= "?".$params;
            }
        }
        $s .= " href=\"".htmlspecialchars($href)."\"";
    }
    $s .= html_attributes($attributes).">";
    if ($anchor !== NULL) {
        $s .= $anchor."</a>";
    }
    return $s;
} // html_a()


/** construct an HTML IMG tag with optional attributes
 *
 * this constructs an IMG tag of the form
 * <pre>
 * <img src="$href" att1="val1" att2="val2">
 * </pre>
 *
 * If no attributes are specified the tag only has the src attribute
 * If $attributes is an array, all elements are added
 * as raw encoded key-value-pairs. If it is a string, then just append.
 *
 * Examples:
 * <pre>
 * html_img('icon.gif'): &lt;img src="icon.gif"&gt;
 * html_img('icon.gif',array('width'=>16, 'height'=>16)): &lt;img src="icon.gif" width="16" height="16"&gt;
 * </pre>
 *
 * @param string $src holds the url to the image file
 * @param string|array $attributes holds the attributes to add to the tag
 * @return string ready to use IMG tag
 */
function html_img($src='',$attributes=NULL) {
    global $WAS_SCRIPT_NAME;
    $s = "<img";
    if (!empty($src)) {
        $s .= " src=\"".htmlspecialchars($src)."\"";
    }
    $s .= html_attributes($attributes).">";
    return $s;
} // html_img()


/** construct a generic HTML-tag with attributes, optionally close it too
 *
 * @param string $tag is the HTML-tag to create, e.g. 'span' or 'script'
 * @param mixed $attributes holds the attributes to add to the tag or NULL
 * @param mixed $content if not NULL this string and a closing tag are appended
 * @return string ready to use HTML-tag
 */
function html_tag($tag='',$attributes=NULL,$content=NULL) {
    $s = "<".$tag.html_attributes($attributes).">";
    if ($content !== NULL) {
        $s .= $content."</".$tag.">";
    }
    return $s;
} // html_tag()


/** companion of html_tag: close the tag
 *
 * @param string $tag is the HTML-tag to close, e.g. 'span' or 'script'
 * @return string ready to use HTML close tag
 */
function html_tag_close($tag='') {
    return '</'.$tag.'>';
} // html_tag_close()


/** convert an array of name-value pairs to a string
 *
 * this converts an array of name-value-pairs to a string containing
 * attribute="content" items, where both 'attribute' and 'content'
 * are properly escaped (with htmlspecialchars()). Properties that
 * don't have content, such as 'disabled' or 'selected' or 'checked'
 * can be specified using the special value NULL, e.g. 
 * array('disabled' => NULL).
 * If the parameter $attributes happens to be a string, it is returned
 * with a space prepended. If it is neither a string nor an array (e.g. NULL),
 * an empty string is returned.
 * Note that the attribute="content" elements are delimited with spaces,
 * and that a leading space is prepended (but not trailing space is added).
 *
 * @param mixed $attributes an array or string with attributes or NULL
 * @return string properly escaped and spaced HTML string with name="value" etc.
 */
function html_attributes($attributes) {
    $str = "";
    if (is_array($attributes)) {
        foreach($attributes as $k => $v) {
            $str .= " ".htmlspecialchars($k);
            if ($v !== NULL) {
                $str .= "=\"".htmlspecialchars($v)."\"";
            }
        }
    } elseif (is_string($attributes)) {
        $str = rtrim(' '.$attributes);
    }
    return $str;
} // html_attributes()


/** construct the opening of a HTML form
 *
 * @param string $action the url to submit to
 * @param string $method either get or post (default)
 * @param string|array $attributes holds the attributes to add to the tag
 * @return string ready-to-user HTML-code
 */
function html_form($action,$method='post',$attributes='') {
    $method = (0 == strcasecmp($method,'get')) ? 'get' : 'post';
    return '<form'.html_attributes(array('action' => $action, 'method' => $method)).
                   html_attributes($attributes).'>';
} // html_form()

/** companion of html_form: close the tag
 *
 */
function html_form_close() {
    return '</form>';
} // html_form_close()


/** construct a href from a path, params and a fragment
 *
 * @param string $path the hypertext reference
 * @param array|string $params the parameter(s) to add to the $path
 * @param string $fragment the optional position within the page
 * @result $string ready-to-use $href
 * @todo should we merge this with html_a() and/or rename this routine to html_href()?
 */
function href($path,$params='',$fragment='') {
    $s = $path;
    if (!empty($params)) {
        if (is_array($params)) {
            $glue = "?";
            foreach ($params as $k => $v) {
                $s .= $glue.rawurlencode($k)."=".rawurlencode($v);
                $glue = "&";
            }
        } elseif (is_string($params)) {
            $s .= "?".$params;
        }
    }
    if (!empty($fragment)) {
        $s .= '#'.rawurlencode($fragment);
    }
    return $s; 
} // href()


/** STUB */
function html_input_text($name) {
    $s = '<input type="text" name="'.$name.'">';
    return $s;
}

/** STUB */
function html_input_select($name,$options) {
    $s = "<select name=\"".$name."\">\n";
    foreach($options as $k => $v) {
        $s .= '  <option value="'.$k.'">'.htmlspecialchars($v)."\n";
    }
    $s .= "</select>\n";
    return $s;
}

/** STUB */
function html_input_radio($name,$options) {
    $s = "";
    foreach($options as $k => $v) {
        $s .= "<br><input type=\"radio\" name=\"$name\" value=\"".$k."\">".htmlspecialchars($v)."\n";
    }
    return $s;
}

/** STUB */
function html_input_submit($name,$value) {
    return "<input type=\"submit\" name=\"$name\" value=\"$value\">";
}


/** construct the opening of a HTML table
 *
 * @param string|array $attributes holds the attributes to add to the tag
 * @param string $m margin for improved code readability
 * @return string ready-to-user HTML-code
 */
function html_table($attributes=NULL,$content=NULL) {
    return html_tag('table',$attributes,$content);
} // html_table()


/** construct table closing tag
 *
 * @param string $m margin for improved code readability
 * @return string ready-to-user HTML-code
 */
function html_table_close() {
    return '</table>';
}


function html_table_row($attributes=NULL,$content=NULL) {
    return html_tag('tr',$attributes,$content);
} // html_table_row()


function html_table_row_close() {
    return '</tr>';
} // html_table_row_close()


function html_table_cell($attributes=NULL,$content=NULL) {
    return html_tag('td',$attributes,$content);
} // html_table_cell()


function html_table_cell_close() {
    return '</td>';
} // html_table_cell_close()


function html_table_head($attributes=NULL,$content=NULL) {
    return html_tag('th',$attributes,$content);
} // html_table_head()


function html_table_head_close() {
    return '</th>';
} // html_table_head_close()

?>