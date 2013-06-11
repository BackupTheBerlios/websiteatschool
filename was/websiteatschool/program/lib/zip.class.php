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

/** /program/lib/zip.class.php - create simple ZIP-archives
 *
 * This file implements class Zip which allows for creating ZIP-archives on the fly
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: zip.class.php,v 1.4 2013/06/11 11:26:07 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

define('ZIP_TYPE_NONE',  ''      );
define('ZIP_TYPE_FILE',  'file'  );
define('ZIP_TYPE_STREAM','stream');
define('ZIP_TYPE_BUFFER','buffer');

/** Create simple and compatible ZIP-archives
 *
 * With this class it is possible to create 
 * ZIP-archives that are compatible with the
 * original PKZip 2.04g. This class does not
 * provide a way to read ZIP-archives.
 * 
 * There are three possible options for the output:
 *  - write the ZIP-archive directly to a file (OpenZipfile())
 *  - output ('stream') directly to the user's browser, including
 *    appropriate headers (OpenZipstream())
 *  - collect the output in a buffer in memory (OpenZipbuffer()).
 * 
 * There are two different ways to add to the ZIP-archive:
 *  - add a file from the filesystem (AddFile())
 *  - add data from memory as if it was a file (AddData())
 * 
 * The ZIP-archive needs to be closed before it is useable (CloseZip()).
 * 
 * Special features:
 * 
 *  - it is not necessary to manually add a directory to the ZIP-archive
 *    because all directories that lead to a file will be added
 *    automatically 
 *  - both AddFile() and AddData() allow for on-the-fly (re)naming;
 *    i.e. the name of the file in the ZIP-archive can be different
 *    from the name of the file in the filesystem
 *  - it is possible to add a comment to an individual file
 *  - it is possible to add a comment to the ZIP-archive
 * 
 * Limitations
 *
 * This class might use a lot of memory when creating ZIP-archives,
 * especially the ZIP_TYPE_BUFFER variant which eventually requires
 * the size of the resulting ZIP-archive plus (worst case) the size
 * of the largest file plus the size of the largest compressed file.
 * This might be a problem with large files or many, many smaller
 * files. A workaround could be to either stream the ZIP-archive directly
 * (ZIP_TYPE_STREAM) or write to a file (ZIP_TYPE_FILE) because those
 * variants only require the size of the largest file, the largest
 * compressed file and the size of the central directory.
 *
 * This class is not able to read ZIP-archives.
 *
 * This class either stores a file as-is using the PKZIP 'Store'
 * method or compresses the file using the 'Deflate' method. There is
 * no support for other (more advanced) compression algoritms and
 * no encryption is used.
 *
 * References
 *
 * I implemented this class using the following references.
 * 
 * [1] The ultimate definition of the ZIP-archive format as published by
 *     PKWare, Inc. See: {@link http://www.pkware.com/appnote.txt} or 
 *     {@link http://www.pkware.com/support/zip-application-note}. I used
 *     version 6.3.2 which was published on 28 September 2007.
 * 
 * [2] RFC1950 - ZLIB Compressed Data Format Specification version 3.3,
 *     P. Deutsch, J-L. Gailly (May 1996), {@link http://www.faqs.org/rfcs/rfc1950}
 * 
 * [3] RFC1951 - DEFLATE Compressed Data Format Specification version 1.3,
 *     P. Deutsch (May 1996), {@link http://www.faqs.org/rfcs/rfc1951}
 * 
 * [4] Disk Operating System Technical Reference, IBM Corporation 1985,
 *     Chapter 5 (DOS Disk Directory).
 * 
 * [5] Official registration of the application/zip MIME-type:
 *     {@link http://www.iana.org/assignments/media-types/application/zip}
 *
 * Examples
 *
 * Typical usage of this class is as follows.
 *
 * Example 1 - store 3 existing files in a ZIP-archive
 * <pre>
 * $zip = new Zip;
 * $zip->OpenFile("/tmp/test.zip");
 * $zip->AddFile("/tmp/foo.txt");
 * $zip->AddFile("/tmp/bar.txt");
 * $zip->AddFile("/tmp/baz.txt");
 * $zip->CloseZip();
 * </pre>
 *
 * Example 2 - store a chunk of data in a ZIP-archive in memory
 * <pre>
 * $zip_archive = '';
 * $data = "This is example-data that ends up in file QUUX.TXT";
 * $zip = new Zip;
 * $zip->OpenZipbuffer($zip_archive);
 * $zip->AddData($data,'QUUX.TXT');
 * $zip->CloseZip();
 * </pre>
 *
 * Example 3 - directly stream a file in a ZIP-archive and rename on the fly
 * <pre>
 * $zip = new Zip;
 * $zip->OpenStream('htdocs.zip');
 * $zip->AddFile("/var/www/index.html",'INDEX.HTM');
 * $zip->CloseZip();
 * </pre>
 *
 * All methods return TRUE on success or FALSE on failure. If the method failed,
 * an (English) error message can be found in $zip->Error.
 */
class Zip {
    /** @var array $central_directory buffer for the central directory entries
     *
     * This array is keyed by relative filename (both files and directories),
     * no leading '/' though directories have a trailing '/'.
     */
    var $central_directory = array();

    /** @var int $offset always points to the next local file header in the ZIP-archive */
    var $offset = 0;

    /** @var string $zip_type ZIP-archive destination: file, stream or buffer */
    var $zip_type = ZIP_TYPE_NONE;

    /** @var string $zip_comment a file wide comment */
    var $zip_comment = '';

    /** @var string $Error collects error messages if things go wrong */
    var $Error = '';

    /** @var string $zip_path name of the zipfile if $zip_type is ZIP_TYPE_FILE */
    var $zip_path = '';

    /** @var null|resource $zip_filehandle handle on the zipfile output if $zip_type is ZIP_TYPE_FILE */
    var $zip_filehandle = NULL;

    /** @var string $zip_buffer reference to output buffer if $zip_type is ZIP_TYPE_BUFFER */
    var $zip_buffer = '';

    /** @var int $no_name_files is used to construct names for otherwise unnamed files */
    var $no_name_files = 0;

    /** constructor initialises all variables */
    function Zip() {
        $this->central_directory = array();
        $this->offset = 0;
        $this->zip_type = ZIP_TYPE_NONE;
        $this->zip_comment = '';
        $this->Error = '';
        $this->zip_path = '';
        $this->zip_filehandle = NULL;
        $this->zip_buffer = '';
        $this->no_name_files = 0;
    } // Zip()


    /** open a file for subsequent output of ZIP-archive
     *
     * this opens the file $path for writing and also sets the zip_type to
     * ZIP_TYPE_FILE. The optional $comment is stored for future reference.
     * The file must be closed afterwards via {@link CloseZip()}.
     *
     * @param string $path the (absolute) pathname of the destination file
     * @param string $comment an optional comment to include in the ZIP-archive
     * @return bool TRUE on success, FALSE if an error occurred + message in $this->Error
     */
    function OpenZipfile($path,$comment='') {
        $retval  = TRUE; // assume success
        $this->central_directory = array();
        $this->offset = 0;
        $this->zip_type = ZIP_TYPE_FILE;
        $this->zip_comment = $comment;
        $this->Error = '';
        $this->no_name_files = 0;
        $this->zip_path = $path;
        $this->zip_filehandle = @fopen($this->zip_path,'wb');
        if ($this->zip_filehandle === FALSE) {
            $this->zip_error(__FUNCTION__,sprintf("cannot open zipfile '%s' for output",$this->zip_path));
            $retval = FALSE;
        }
        return $retval;
    } // OpenZipfile()


    /** start with a stream (direct output) indicating an application/zip type of content
     *
     * this starts the output of the ZIP-archive directly to the browser.
     * The Content-Type and the Content-Disposition are set by sending headers.
     * The stream must be closed afterwards via {@link CloseZip()}.
     * 
     * @param string $name the name of the ZIP-archive that is suggested to the browser
     * @param string $comment an optional comment to include in the ZIP-archive
     * @return bool TRUE on success, FALSE if an error occurred + message in $this->Error
     */
    function OpenZipstream($name,$comment='') {
        $retval = TRUE; // assume success
        $this->central_directory = array();
        $this->offset = 0;
        $this->zip_type = ZIP_TYPE_STREAM;
        $this->zip_comment = $comment;
        $this->Error = '';
        $this->no_name_files = 0;
        $filename = '';
        $linenumber = 0;
        if (headers_sent($filename,$linenumber)) {
            $this->zip_error(__FUNCTION__,
                             sprintf("headers already sent in file '%s' line %d; cannot properly send attachment '%s'",
                                     $filename,$linenumber,$name));
            $retval = FALSE;
        } else {
            header('Content-Type: application/zip');
            header(sprintf('Content-Disposition: attachment; filename="%s"',$name));
        }
        return $retval;
    } // OpenZipstream()


    /** prepare the user supplied buffer for subsequent ZIP-archive data
     *
     * @param string &$buffer a pointer to the buffer where we can write the ZIP-archive
     * @param string $comment an optional comment to include in the ZIP-archive
     * @return bool TRUE on success, FALSE if an error occurred + message in $this->Error
     */
    function OpenZipbuffer(&$buffer,$comment='') {
        $retval = TRUE; // assume success
        $this->central_directory = array();
        $this->offset = 0;
        $this->zip_type = ZIP_TYPE_BUFFER;
        $this->zip_comment = $comment;
        $this->Error = '';
        $this->no_name_files = 0;
        $this->zip_buffer = &$buffer;
        $this->zip_buffer = '';
        return $retval;
    } // OpenZipbuffer()


    /** add the contents of an existing file to the current ZIP-archive
     *
     * this reads the file $path into a buffer, and subsequently adds the
     * data to the ZIP-archive.
     *
     * @param string $path the full (absolute) name of the file to add to the ZIP-archive
     * @param string $filename the preferred name of the file in the ZIP-archive (default is $path)
     * @param string $comment an optional comment for this specific file
     * @return bool TRUE on success, FALSE if an error occurred + message in $this->Error
     */
    function AddFile($path,$filename='',$comment='') {
        $retval = TRUE; // assume success
        $this->Error = '';

        // sanity check
        if (($this->zip_type != ZIP_TYPE_FILE) &&
            ($this->zip_type != ZIP_TYPE_STREAM) &&
            ($this->zip_type != ZIP_TYPE_BUFFER)) {
            $this->zip_error(__FUNCTION__,'no ZIP-archive opened yet');
            return FALSE;
        }

        $buffer = file_get_contents($path);
        if ($buffer === FALSE) {
            $this->zip_error(__FUNCTION__,sprintf("cannot retrieve contents of file '%s'",$path));
            return FALSE;
        }
        if (empty($filename)) {
            $filename = realpath($path); // get rid already of relative components or symbolic links
            if ($filename === FALSE) { // if this didn't work, we'll leave it to the worker routine to construct a name
                $filename = '';
            }
        }
        $timestamp = @filemtime($path);
        if ($timestamp === FALSE) { // error retrieving mtime, shouldn't happen...
            $timestamp = time(); // ...however, we simply use the current time instead (without warning)
        }
        return $this->zip_add_data($buffer,$filename,$comment,$timestamp); // delegate the heavy lifting
    } // AddFile()


    /** add data to the current ZIP-archive
     *
     * This adds the data to the current ZIP-archive.
     *
     * @param string $data the data to add to the ZIP-archive
     * @param string $filename the preferred name of the file in the ZIP-archive
     * @param string $comment an optional comment for this specific file
     * @param int $timestamp the unix timestamp to associate with the file
     * @return bool TRUE on success, FALSE if an error occurred + message in $this->Error
     */
    function AddData($data,$filename='',$comment='',$timestamp=0) {
        $retval = TRUE; // assume success
        $this->Error = '';

        // sanity check
        if (($this->zip_type != ZIP_TYPE_FILE) &&
            ($this->zip_type != ZIP_TYPE_STREAM) &&
            ($this->zip_type != ZIP_TYPE_BUFFER)) {
            $this->zip_error(__FUNCTION__,'no ZIP-archive opened yet');
            return FALSE;
        }
        if ($timestamp == 0) {
            $timestamp = time();
        }
        return $this->zip_add_data($data,$filename,$comment,$timestamp); // delegate the heavy lifting
    } // AddData()


    /** finish the ZIP-archive by outputting the central directory and closing output
     *
     * this finishes the ZIP-archive by constructing and outputting the Central Directory
     * and subsequently closing the output file (in case of ZIP_TYPE_FILE). The call to
     * CloseZip() is necessary to create a complete ZIP-archive, including the Central
     * Directory.
     *
     * @return bool TRUE on success, FALSE if an error occurred + message in $this->Error
     */
    function CloseZip() {
        $retval = TRUE; // assume success
        $this->Error = '';

        $dir_entries = sizeof($this->central_directory);
        $dir_data = implode('',$this->central_directory);
        $dir_length = strlen($dir_data);
        $comment_length = strlen($this->zip_comment);
        $dir_data .= pack('V',0x06054B50).      // end of central dir signature
                     pack('v',0).               // number of this disk
                     pack('v',0).               // number of disk with central directory start
                     pack('v',$dir_entries).    // total # of central directory entries on this disk
                     pack('v',$dir_entries).    // total # of central directory entries
                     pack('V',$dir_length).     // size of the central directory
                     pack('V',$this->offset).   // offset of central directory
                     pack('v',$comment_length). // .ZIP file comment length
                     $this->zip_comment;        // .ZIP file comment

        switch ($this->zip_type) {
        case ZIP_TYPE_FILE:
            if (@fwrite($this->zip_filehandle,$dir_data) === FALSE) {
                $this->zip_error(__FUNCTION__,sprintf("cannot write central directory to zipfile '%s'",$this->zip_path));
                $retval = FALSE;
            }
            if (@fclose($this->zip_filehandle) === FALSE) {
                $this->zip_error(__FUNCTION__,sprintf("cannot close zipfile '%s'",$this->zip_path));
                $retval = FALSE;
            }
            $this->zip_filehandle = NULL;
            break;

        case ZIP_TYPE_STREAM:
            echo $dir_data;
            break;

        case ZIP_TYPE_BUFFER:
            $this->zip_buffer .= $dir_data;
            break;
        }
        $this->zip_type = ZIP_TYPE_NONE;
        return $retval;
    } // CloseZip()


    // #############################################################################
    // ################################# WORKHORSES ################################
    // #############################################################################

    /** workhorse function to add data to the current ZIP-archive
     *
     * This actually adds the data to the current ZIP-archive.
     *
     * Note that we try to make a wise decision about compressed data: the compressed
     * data should be smaller than the uncompressed data. If not, we
     * don't bother and simply store the data as-is.
     *
     * We also try to keep the number of copies of the data down to a minimum by
     * not copying the $data but selecting between $data and $zdata only when we
     * are really ready to write output the data.
     *
     * @param string &$data a pointer to a buffer with data to add to the ZIP-archive
     * @param string $filename the preferred name of the file in the ZIP-archive
     * @param string $comment an optional comment for this specific file (could be '')
     * @param int $timestamp the unix timestamp to associate with the file
     * @return bool TRUE on success, FALSE if an error occurred + message in $this->Error
     * @todo should we handle the possibility of an additional 4 bytes for DICTID (RFC1950, reference [2])?
     * @todo should we handle the option of a better compression level (eg. level 9) in gzcompress()?
     *       we could check to see if CMF equals 0x78 and FLG is either 0x01, 0x5E, 0x9C or 0xDA
     *       the latter 4 values might have an effect on general purpose bit flag bits 2 and 3.
     *       for now we'll just keep it simple, but there might be a little something to improve here.
     */
    function zip_add_data(&$data,$filename,$comment,$timestamp) {
        $retval = TRUE; // assume success

        $path = $this->make_suitable_filename($filename);
        if ($this->zip_add_directories(dirname($path),$timestamp) === FALSE) {
            $this->zip_error(__FUNCTION__,sprintf("error adding path of '%s' to ZIP-archive",$path));
            return FALSE;
        }
        $last_mod_file_datim = $this->dos_time_date($timestamp);
        $crc32 = crc32($data);
        $size = strlen($data);
        $zdata = gzcompress($data);
        $zlength = strlen($zdata) - 6;      // Account for CMF, FLG and also ADLER32 (see RFC1950 reference [2])
        if ($zlength < $size) {
            $method = 8;                    // Deflated
            $version_unzip = 20;            // Need 2.0 for Deflate method
            $zsize = $zlength;
        } else {
            $method = 0;                    // Stored
            $version_unzip = (strpos($path,'/') === FALSE) ? 10 : 20; // 1.0 knows Store method, directories need 2.0
            $zsize = $size;                 // compressed size identical to uncompressed size
        }

        $pathlength = strlen($path);
        $clength = strlen($comment);
        $central = pack('V',0x02014B50).    // central file header signature
                   pack('v',0).             // version made by
                   pack('v',$version_unzip).// version needed to extract
                   pack('v',0).             // general purpose bit flag
                   pack('v',$method).       // compression method (0=Stored, 8=Deflated)
                   $last_mod_file_datim.    // last mod file time, last mod file date
                   pack('V',$crc32).        // crc-32
                   pack('V',$zsize).        // compressed size
                   pack('V',$size).         // uncompressed size
                   pack('v',$pathlength).   // file name length
                   pack('v',0).             // extra field length
                   pack('v',$clength).      // file comment length
                   pack('v',0).             // disk number start
                   pack('v',0).             // internal file attributes
                   pack('V',0x20).          // external file attribytes (DOS: 0x10=directory, 0x20=archive bit)
                   pack('V',$this->offset). // relative offset of local header
                   $path.                   // file name (variable size)
                   ''.                      // extra field (variable size)
                   $comment;                // file comment (variable size)

        $local  =  pack('V',0x04034B50).    // local file header signature
                   pack('v',$version_unzip).// version needed to extract
                   pack('v',0).             // general purpose bit flag
                   pack('v',$method).       // compression method (0=Stored, 8=Deflated)
                   $last_mod_file_datim.    // last mod file time, last mod file date
                   pack('V',$crc32).        // crc-32
                   pack('V',$zsize).        // compressed size
                   pack('V',$size).         // uncompressed size
                   pack('v',$pathlength).   // file name length
                   pack('v',0).             // extra field length
                   $path.                   // file name (variable size)
                   '';                      // extra field (variable size)
        $this->central_directory[$path] = $central;

        switch ($this->zip_type) {
        case ZIP_TYPE_FILE:
            if (@fwrite($this->zip_filehandle,$local) === FALSE) {
                $this->zip_error(__FUNCTION__,sprintf("cannot write local header for '%s' to zipfile",$path));
                $retval = FALSE;
            } else {
                $this->offset += strlen($local);
            }
            // if Deflated: chop off 2 bytes at start (CMF, FLG) and 4 bytes at end (ADLER32) from $zdata
            if (@fwrite($this->zip_filehandle,($method == 0) ? $data : substr($zdata,2,-4)) === FALSE) {
                $this->zip_error(__FUNCTION__,sprintf("cannot write data for '%s' to zipfile",$path));
                $retval = FALSE;
            } else {
                $this->offset += $zsize;
            }
            break;

        case ZIP_TYPE_STREAM:
            echo $local;
            // if Deflated: chop off 2 bytes at start (CMF, FLG) and 4 bytes at end (ADLER32) from $zdata
            echo ($method == 0) ? $data : substr($zdata,2,-4);
            $this->offset += strlen($local) + $zsize;
            break;

        case ZIP_TYPE_BUFFER:
            $this->zip_buffer .= $local;
            // if Deflated: chop off 2 bytes at start (CMF, FLG) and 4 bytes at end (ADLER32) from $zdata
            $this->zip_buffer .= ($method == 0) ? $data : substr($zdata,2,-4);
            $this->offset += strlen($local) + $zsize;
            break;
        }
        return $retval;
    } // zip_add_data()

    /** workhorse function to add 0, 1 or more directories to the current ZIP-archive
     *
     * this routine works from top to bottom through the specified path, adding directories
     * to the archive. If a particular directory was already added before, it is not added
     * again. This information is based on the existence of the corresponding key in the
     * central_directory array.
     *
     * @param string $pathname contains 0, 1 or more directories that lead to the file that needs to be added
     * @param int $timestamp unix timestamp to associate with the directory
     * @return bool TRUE on success, FALSE if an error occurred + message in $this->Error
     */
    function zip_add_directories($pathname,$timestamp) {
        $retval = TRUE; // assume success
        if (empty($pathname)) { // no path at all is fine
            return $retval;
        }
        $last_mod_file_datim = $this->dos_time_date($timestamp);
        $directories = explode('/',$pathname);
        $path = '';
        foreach($directories as $directory) {
            if ($directory == '.') {
                continue;
            }
            $path .= $directory.'/';
            if (isset($this->central_directory[$path])) {
                continue;
            }
            $pathlength = strlen($path);
            $central = pack('V',0x02014B50).    // central file header signature
                       pack('v',0).             // version made by
                       pack('v',20).            // version needed to extract
                       pack('v',0).             // general purpose bit flag
                       pack('v',0).             // compression method (0=Stored, 8=Deflated)
                       $last_mod_file_datim.    // last mod file time, last mod file date
                       pack('V',0).             // crc-32
                       pack('V',0).             // compressed size
                       pack('V',0).             // uncompressed size
                       pack('v',$pathlength).   // file name length
                       pack('v',0).             // extra field length
                       pack('v',0).             // file comment length
                       pack('v',0).             // disk number start
                       pack('v',0).             // internal file attributes
                       pack('V',0x10).          // external file attribytes (DOS: 0x10=directory, 0x20=archive bit)
                       pack('V',$this->offset). // relative offset of local header
                       $path.                   // file name (variable size)
                       ''.                      // extra field (variable size)
                       '';                      // file comment (variable size)

            $local  =  pack('V',0x04034B50).    // local file header signature
                       pack('v',20).            // version needed to extract
                       pack('v',0).             // general purpose bit flag
                       pack('v',0).             // compression method (0=Stored, 8=Deflated)
                       $last_mod_file_datim.    // last mod file time, last mod file date
                       pack('V',0).             // crc-32
                       pack('V',0).             // compressed size
                       pack('V',0).             // uncompressed size
                       pack('v',$pathlength).   // file name length
                       pack('v',0).             // extra field length
                       $path.                   // file name (variable size)
                       '';                      // extra field (variable size)

            $this->central_directory[$path] = $central;
            switch ($this->zip_type) {
            case ZIP_TYPE_FILE:
                if (@fwrite($this->zip_filehandle,$local) === FALSE) {
                    $this->zip_error(__FUNCTION__,sprintf("cannot write local header for '%s' to zipfile",$path));
                    $retval = FALSE;
                } else {
                    $this->offset += strlen($local);
                }
                break;

            case ZIP_TYPE_STREAM:
                echo $local;
                $this->offset += strlen($local);
                break;

            case ZIP_TYPE_BUFFER:
                $this->zip_buffer .= $local;
                $this->offset += strlen($local);
                break;
            }
        }
        return $retval;
    } // zip_add_directories()

    /** add an error message to the list of error messages
     *
     * @param string $function name of the function/method where the error occurred
     * @param string $message the error message to add
     * @return void message added to list
     */
    function zip_error($function,$message) {
        if (!empty($this->Error)) {
            $this->Error .= "\n";
        }
        $this->Error .= sprintf("%s->%s(): %s",__CLASS__,$function,$message);
    } // zip_error()


    /** construct a suitable filename for use in ZIP-archive
     *
     * this analyses and edits the string $filename in such a way
     * that a suitable name for use in a ZIP-archive remains.
     * This means that:
     *
     *  - MS-DOS driverletters are removed from the path
     *  - backslashes are replaced with slashes
     *  - leading './' if any is removed
     *  - a leading slash is removed
     *
     * @param string $filename name to analyse/massage
     * @return string suitable filename with or without a path
     */
    function make_suitable_filename($filename) {
        $filename = strtr($filename,'\\','/');
        if ((ctype_alpha($filename{0})) && ($filename{1} == ':')) { // lose MS-DOS driveletter d:
            $filename = substr($filename,2);
        }
        if ($filename == '.') { // could be the result of realpath() earlier
            $filename = '';
        } else {
            if (substr($filename,0,2) == './') { // this too is not appropriate in a zipfile
                $filename = substr($filename,2);
            }
            while ($filename{0} == '/') { // lose the leading slashes, we require relative paths in ZIP-archive
                 $filename = substr($filename,1);
            }
        }
        if (empty($filename)) {
            // we MUST have a name. If none specified, construct one starting at 'file0000'.
            $filename = sprintf('file%04d',$this->no_name_files++);
        }
        return $filename;
    } // make_suitable_filename()


    /** construct an MS-DOS time and date based on unix timestamp
     *
     * this routine constructs a string of 2 x 2 bytes with the time and the
     * date in the following format.
     *
     * <pre>
     * 15 14 13 12 11 10  9  8  7  6  5  4  3  2  1  0
     *  h  h  h  h  h  m  m  m  m  m  m  x  x  x  x  x
     *
     * hhhhh = hour, from 0 - 23 (5 bits)
     * mmmmmm = minute, from 0 to 59 (6 bits)
     * xxxxx = duoseconds, from 0 to 29 (5 bits)
     *
     * 15 14 13 12 11 10  9  8  7  6  5  4  3  2  1  0
     *  y  y  y  y  y  y  y  m  m  m  m  d  d  d  d  d
     *
     * yyyyyyy = year offset from 1980, from 0 - 119 (7 bits)
     * mmmm = month, from 1 to 12 (4 bits)
     * ddddd = day, from 1 to 31 (5 bits)
     * </pre>
     *
     * Note that the time resolution is 2 seconds whereas the unix timestamp
     * has a 1 second resolution. This means that the seconds are rounded down.
     * Also note that the specification [4] indicates that the maximum value
     * for year offset is 119 which corresponds with 2099 rather than the
     * maximum of 127 which would yield the year 2107.
     *
     * @param int $timestamp unix timestamp (seconds since 1970-01-01 00:00:00)
     * @return string packed string with time and date (little endian, 4 bytes)
     */
    function dos_time_date($timestamp) {
        $dt = getdate($timestamp);
        $dos_time = (intval($dt['hours']) << 11) | (intval($dt['minutes']) << 5) | (intval($dt['seconds']) >> 1);
        $dos_date = ((intval($dt['year']) - 1980) << 9) | (intval($dt['mon']) << 5) | (intval($dt['mday']));
        return pack('v',$dos_time).pack('v',$dos_date);
    } // dos_time_date()

} // Zip

?>