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

/** /program/lib/filelib.php - utilities for manipulating files
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: filelib.php,v 1.3 2011/09/21 18:54:20 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** return an array with mimetypes keyed by file extension
 *
 * This routine returns an array with 'known' combinations
 * of (lower case) file extensions and (lowercase) mime types.
 * This array can be used in two ways.
 *
 * Example 1: find a mimetype by extension
 *
 * <pre>
 * $mimetypes = get_mimetypes_array();
 * $mimetype = $mimetypes['jpg']; // this should yield 'image/jpeg'
 * </pre>
 *
 * Example 2: find an exension by mimetype
 *
 * <pre>
 * $mimetypes = get_mimetypes_array();
 * $extension = array_search('image/jpeg',$mimetypes); // this should yield 'jpg'
 * </pre>
 *
 * Note that in that last example the first matching element is used.
 * This implies that the most common extension for a certain mimetype
 * should come first in the array, i.e. 'jpg'=>'image/jpeg' should
 * come before 'jpeg'=>'image/jpeg'.
 *
 * The list below is based on the list of mime types as distributed 
 * with the Apache webserver software.
 *
 * Changes and tweaks to the list below:
 *
 * application/octet-stream: default extension is an empty string ''
 * application/postscript: default extension is ps
 * audion/mpeg: default extension is mp3
 * image/jpeg: default extension is jpg
 * text/plain: default extension is txt
 * video//quicktime: default extension is mov
 *
 * <b>NOTE</b><br>
 * Please do not change the mapping for both the empty extension '' and the
 * binary extension 'bin'; these extensions must map to 'application/octet-stream'
 * because this is necessary to defeat tricks with uploading files with double
 * extensions (as used in the File Manager).
 *
 * @return array with (lowercase) mimetypes keyed by (lowercase) extension
 */
function get_mimetypes_array() {
    return array(
        '' => 'application/octet-stream',    // DO NOT CHANGE THIS (SEE NOTE)
        'bin' => 'application/octet-stream', // DO NOT CHANGE THIS (SEE NOTE)
        'aif' => 'audio/x-aiff',
        'atom' => 'application/atom+xml',
        'au' => 'audio/basic',
        'avi' => 'video/x-msvideo',
        'bcpio' => 'application/x-bcpio',
        'bmp' => 'image/bmp',
        'cgm' => 'image/cgm',
        'cpio' => 'application/x-cpio',
        'cpt' => 'application/mac-compactpro',
        'csh' => 'application/x-csh',
        'css' => 'text/css',
        'dcr' => 'application/x-director',
        'djvu' => 'image/vnd.djvu',
        'doc' => 'application/msword',
        'dtd' => 'application/xml-dtd',
        'dvi' => 'application/x-dvi',
        'etx' => 'text/x-setext',
        'ez' => 'application/andrew-inset',
        'gif' => 'image/gif',
        'gram' => 'application/srgs',
        'grxml' => 'application/srgs+xml',
        'gtar' => 'application/x-gtar',
        'hdf' => 'application/x-hdf',
        'hqx' => 'application/mac-binhex40',
        'html' => 'text/html',
        'ice' => 'x-conference/x-cooltalk',
        'ico' => 'image/x-icon',
        'ics' => 'text/calendar',
        'ief' => 'image/ief',
        'igs' => 'model/iges',
        'jpg' => 'image/jpeg',
        'js' => 'application/x-javascript',
        'latex' => 'application/x-latex',
        'm3u' => 'audio/x-mpegurl',
        'man' => 'application/x-troff-man',
        'mathml' => 'application/mathml+xml',
        'me' => 'application/x-troff-me',
        'mid' => 'audio/midi',
        'mif' => 'application/vnd.mif',
        'movie' => 'video/x-sgi-movie',
        'mov' => 'video/quicktime',
        'mp3' => 'audio/mpeg',
        'mpeg' => 'video/mpeg',
        'ms' => 'application/x-troff-ms',
        'msh' => 'model/mesh',
        'mxu' => 'video/vnd.mpegurl',
        'nc' => 'application/x-netcdf',
        'oda' => 'application/oda',
        'ogg' => 'application/ogg',
        'pbm' => 'image/x-portable-bitmap',
        'pdb' => 'chemical/x-pdb',
        'pdf' => 'application/pdf',
        'pgm' => 'image/x-portable-graymap',
        'pgn' => 'application/x-chess-pgn',
        'png' => 'image/png',
        'pnm' => 'image/x-portable-anymap',
        'ppm' => 'image/x-portable-pixmap',
        'ppt' => 'application/vnd.ms-powerpoint',
        'ps' => 'application/postscript',
        'ram' => 'audio/x-pn-realaudio',
        'ras' => 'image/x-cmu-raster',
        'rdf' => 'application/rdf+xml',
        'rgb' => 'image/x-rgb',
        'rm' => 'application/vnd.rn-realmedia',
        'rtf' => 'text/rtf',
        'rtx' => 'text/richtext',
        'sgml' => 'text/sgml',
        'sh' => 'application/x-sh',
        'shar' => 'application/x-shar',
        'sit' => 'application/x-stuffit',
        'skp' => 'application/x-koan',
        'smi' => 'application/smil',
        'spl' => 'application/x-futuresplash',
        'src' => 'application/x-wais-source',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc' => 'application/x-sv4crc',
        'svg' => 'image/svg+xml',
        'swf' => 'application/x-shockwave-flash',
        't' => 'application/x-troff',
        'tar' => 'application/x-tar',
        'tcl' => 'application/x-tcl',
        'tex' => 'application/x-tex',
        'texinfo' => 'application/x-texinfo',
        'tiff' => 'image/tiff',
        'tsv' => 'text/tab-separated-values',
        'txt' => 'text/plain',
        'ustar' => 'application/x-ustar',
        'vcd' => 'application/x-cdlink',
        'vxml' => 'application/voicexml+xml',
        'wav' => 'audio/x-wav',
        'wbmp' => 'image/vnd.wap.wbmp',
        'wbxml' => 'application/vnd.wap.wbxml',
        'wmlc' => 'application/vnd.wap.wmlc',
        'wmlsc' => 'application/vnd.wap.wmlscriptc',
        'wmls' => 'text/vnd.wap.wmlscript',
        'wml' => 'text/vnd.wap.wml',
        'wrl' => 'model/vrml',
        'xbm' => 'image/x-xbitmap',
        'xhtml' => 'application/xhtml+xml',
        'xls' => 'application/vnd.ms-excel',
        'xml' => 'application/xml',
        'xpm' => 'image/x-xpixmap',
        'xslt' => 'application/xslt+xml',
        'xul' => 'application/vnd.mozilla.xul+xml',
        'xwd' => 'image/x-xwindowdump',
        'xyz' => 'chemical/x-xyz',
        'zip' => 'application/zip',
        //
        // mimetypes with alternative extensions go below
        //
        'ai' => 'application/postscript',
        'aifc' => 'audio/x-aiff',
        'aiff' => 'audio/x-aiff',
        'asc' => 'text/plain',
        'cdf' => 'application/x-netcdf',
        'class' => 'application/octet-stream',
        'dir' => 'application/x-director',
        'djv' => 'image/vnd.djvu',
        'dll' => 'application/octet-stream',
        'dmg' => 'application/octet-stream',
        'dms' => 'application/octet-stream',
        'dxr' => 'application/x-director',
        'eps' => 'application/postscript',
        'exe' => 'application/octet-stream',
        'htm' => 'text/html',
        'ifb' => 'text/calendar',
        'iges' => 'model/iges',
        'jpeg' => 'image/jpeg',
        'jpe' => 'image/jpeg',
        'kar' => 'audio/midi',
        'lha' => 'application/octet-stream',
        'lzh' => 'application/octet-stream',
        'm4u' => 'video/vnd.mpegurl',
        'mesh' => 'model/mesh',
        'midi' => 'audio/midi',
        'mp2' => 'audio/mpeg',
        'mpe' => 'video/mpeg',
        'mpga' => 'audio/mpeg',
        'mpg' => 'video/mpeg',
        'qt' => 'video/quicktime',
        'ra' => 'audio/x-pn-realaudio',
        'roff' => 'application/x-troff',
        'sgm' => 'text/sgml',
        'silo' => 'model/mesh',
        'skd' => 'application/x-koan',
        'skm' => 'application/x-koan',
        'skt' => 'application/x-koan',
        'smil' => 'application/smil',
        'snd' => 'audio/basic',
        'so' => 'application/octet-stream',
        'texi' => 'application/x-texinfo',
        'tif' => 'image/tiff',
        'tr' => 'application/x-troff',
        'vrml' => 'model/vrml',
        'xht' => 'application/xhtml+xml',
        'xsl' => 'application/xml'
        );
} // get_mimetypes_array()


/** determine the mimetype of a file
 *
 * This routine tries to discover the mimetype of a file.
 * First we try to determine the mimetype via the fileinfo extension.
 * If that doesn't work, we try the deprecated mime_content_type() function.
 * If that doesn't work, we try to shell out to file(1).
 * If that doesn't work, we resort to "guessing" the mimetype based
 * on the extension of the file or else we return the generic
 * 'application/octet-stream'.
 *
 * Note that in step 3 we shell out and try to execute the file(1) command.
 * The results are checked against a pattern to assert that we are
 * really dealing with a mime type. The pattern is described in RFC2616
 * (see sections 3.7 and 2.2):
 *
 *<pre>
 *      media-type     = type "/" subtype *( ";" parameter )
 *      type           = token
 *      subtype        = token
 *      token          = 1*&lt;any CHAR except CTLs or separators&gt;
 *      separators     = "(" | ")" | "&lt;" | "&gt;" | "@"
 *                     | "," | ";" | ":" | "\" | &lt;"&gt;
 *                     | "/" | "[" | "]" | "?" | "="
 *                     | "{" | "}" | SP | HT
 *      CHAR           = &lt;any US-ASCII character (octets 0 - 127)&gt;
 *      CTL            = &lt;any US-ASCII control character
 *                       (octets 0 - 31) and DEL (127)&gt;
 *      SP             = &lt;US-ASCII SP, space (32)&gt;
 *      HT             = &lt;US-ASCII HT, horizontal-tab (9)&gt;
 *      &lt;"&gt;            = &lt;US-ASCII double-quote mark (34)&gt;
 *</pre>
 *
 * This description means we should look for two tokens containing
 * letters a-z or A-Z, digits 0-9 and these special characters:
 * ! # $ % & ' * + - . ^ _ ` | or ~. That's it.
 *
 * Note that file(1) may return a mime type with additional parameters.
 * e.g. 'text/plain; charset=US-ASCII'. This fits the pattern, because
 * it starts with a token, a slash and another token.
 *
 * The optional parameter $name is used to determine the mimetype based
 * on the extension (as a last resort), even when the current name of the
 * file is meaningless, e.g. when uploading a file, the name of the file
 * (from $_FILES['file0']['tmp_name']) is something like '/tmp/php4r5dwfw',
 * even though $_FILES['file0']['name'] might read 'S6301234.JPG'.
 * If $name is not specified (i.e. is empty), we construct it from $path.
 *
 * @param string $path fully qualified path to the file to test
 * @param string $name name of the file, possibly different from $path
 * @return string mimetype of the file $path
 * @todo there is room for improvement here: 
 *       the code in step 1 and step 2 is largely untested
 */
function get_mimetype($path,$name='') {

    // 0 -- quick check for file of type 'image/*' (suppress annoying warning message from getimagesize())
    if ((($imagesize = @getimagesize($path)) !== FALSE) && (is_array($imagesize)) && (isset($imagesize['mime']))) {
        $mimetype = $imagesize['mime'];
        // logger(sprintf('%d: %s(): path=%s name=%s mime=%s',0,__FUNCTION__,$path,$name,$mimetype),WLOG_DEBUG);
        return $mimetype;
    }

    // 1 -- try the finfo-route if it is available
    if ((function_exists('finfo_open')) &&
        (function_exists('finfo_file')) &&
        (function_exists('finfo_close')) &&
        (defined(FILEINFO_MIME))) {
        $finfo = finfo_open(FILEINFO_MIME);
        if ($finfo !== FALSE) {
            $mimetype = finfo_file($finfo,$path);
            $finfo_close($finfo);
            if ($mimetype !== FALSE) {
                // logger(sprintf('%d: %s(): path=%s name=%s mime=%s',1,__FUNCTION__,$path,$name,$mimetype),WLOG_DEBUG);
                return $mimetype;
            }
        }
    }

    // 2 -- now try the deprecated mime_content_type method
    if (function_exists('mime_content_type')) {
        $mimetype = mime_content_type($path);
        // logger(sprintf('%d: %s(): path=%s name=%s mime=%s',2,__FUNCTION__,$path,$name,$mimetype),WLOG_DEBUG);
        return $mimetype;
    }

    // 3 -- now try to shell out and use the file command
    $command = sprintf('file -b -i %s',escapeshellarg($path)); // -b = brief output, -i = output mime type strings
    $dummy = array();
    $retval = 0;
    $mimetype = exec($command,$dummy,$retval);
    if ($retval == 0) {
        // now assert that the result looks like a mimetype and not an error message
        if (get_mediatype($mimetype) !== FALSE) {
            // logger(sprintf('%d: %s(): path=%s name=%s mime=%s',3,__FUNCTION__,$path,$name,$mimetype),WLOG_DEBUG);
            return $mimetype;
        }
    }

    // 4 -- take a wild guess; boldly assume that the file extension carries any meaning whatsoever
    $ext = strtolower(pathinfo((empty($name)) ? $path : $name,PATHINFO_EXTENSION));
    $mimetypes = get_mimetypes_array();
    $mimetype = (isset($mimetypes[$ext])) ? $mimetypes[$ext] : 'application/octet-stream';
    // logger(sprintf('%d: %s(): path=%s name=%s mime=%s',4,__FUNCTION__,$path,$name,$mimetype),WLOG_DEBUG);
    return $mimetype;
} // get_mimetype()


/** extract the mediatype and -subtype from a full mimetype
 *
 * this extracts the mediatype and -subtype from a full mimetype, i.e.
 * 'text/plain' from 'text/plain; charset=US-ASCII' (see also {@link get_mimetype()} and RFC2616).
 * If $mimetype doesn't look like a mimetype, we return FALSE.
 *
 * @param string $mimetype the full mimetype to examine, possibly with parameters
 * @return bool|string FALSE on invalid mimetype, otherwise the extracted mediatype and -subtype in lowercase
 */
function get_mediatype($mimetype) {
    $pattern = "/^[a-zA-Z0-9!#\$%&'\*+\-.\^_`|~]+\/[a-zA-Z0-9!#\$%&'\*+\-.\^_`|~]+/";
    $matches = array();
    if (preg_match($pattern,$mimetype,$matches) == 1) {
        return strtolower($matches[0]);
    } else {
        return FALSE;
    }
} // get_mediatype()

?>