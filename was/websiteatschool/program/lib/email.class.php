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

/** /program/lib/email.class.php - wrapper for sending mail
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: email.class.php,v 1.5 2013/06/11 11:26:05 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** Email implements a simple interface to send mail
 *
 * This class can be used to send mail from Website@School,
 * e.g. alerts, new passwords, feedback to the project (with
 * translations), etc.
 *
 * Typical use:
 * <pre>
 * require_once('email.class.php');
 * $mailer = new Email;
 * $mailer->set_mailto($email,$name);
 * $mailer->set_subject($subject);
 * $mailer->set_message($message);
 * $mailer->add_attachment($data,$name);
 * $mailer->send();
 * </pre>
 *
 *
 */
class Email {
    /** @var array $mailfrom contains addr and name for From: (single address) */
    var $mailfrom = array();

    /** @var array $mailreplyto contains addr and name for Reply-To: (single address) */
    var $mailreplyto = array();

    /** @var array $mailto contains addr and name for To: (single address)*/
    var $mailto = array();

    /** @var array $mailcc contains an array of arrays containing addr and name for Cc: (array of addresses) */
    var $mailcc = array();

    /** @var string $subject contains the message subject */
    var $subject = '';

    /** @var array $headers associative array with field names and field values of additional headers */
    var $headers = array();

    /** @var array $message associative array with message properties: body, mimetype, charset, encoding */
    var $message = array();

    /** @var array $attachments array of arrays with attachment properties: body, name, mimetype, charset, encoding */
    var $attachments = array();

    /** @var string $eol end of line character(s), usually CR + LF */
    var $eol = "\r\n";

    /** @var string $charset default character set to use in display names and subject */
    var $charset = "UTF-8";

    /** @var bool $minimal default value of flag for limiting the literal representation in {@link rfc2047_qchar()} */
    var $minimal = FALSE;

    /** @var int $max_length limit for line length */
    var $max_length = 76;

    /** constructor resets all variables to a known (default) state
     *
     * @return void
     */
    function Email() {
        $this->reset_all();
    } // Email()

    /** reset all variables to their default values
     *
     * @return void and variables set
     */
    function reset_all() {
        global $CFG;
        $this->set_mailfrom($CFG->website_from_address,$CFG->title);
        if (!empty($CFG->website_replyto_address)) {
            $this->set_mailreplyto($CFG->website_replyto_address,$CFG->title);
        } else {
            $this->mailreplyto = array();
        }
        $this->mailto = array();
        $this->mailcc = array();
        $this->subject = '';
        $this->headers = array();
        $this->message = array();
        $this->attachments = array();
    } // reset_all()

    /** store the subject of the mail message
     *
     * Embedded CRs and LFs in $subject are removed
     * and the result is stored until message send time.
     *
     * @param string $subject
     * @return void;
     */
    function set_subject($subject) {
        $forbidden = array("\r","\n");
        $this->subject = str_replace($forbidden,'',$subject);
    } // set_subject()

    /** record the address and the name for the From: header
     *
     * Embedded CRs LFs "<" and "> are removed from $addr,
     * and the result is stored, together with $name.
     *
     * @param string $addr the address eg. 'webmaster@example.com'
     * @param string $name the name, eg. 'Exemplum Primary School'
     * @return void
     */
    function set_mailfrom($addr,$name='') {
        $forbidden = array("\r","\n","<",">");
        $this->mailfrom = array(
            'addr' => str_replace($forbidden,'',$addr),
            'name' => $name);
    } // set_mailfrom()

    /** record the address and the name for the Reply-To: header
     *
     * Embedded CRs LFs "<" and "> are removed from $addr,
     * and the result is stored, together with $name.
     *
     * @param string $addr the address eg. 'info@example.com'
     * @param string $name the name, eg. 'Exemplum Primary School'
     * @return void
     */
    function set_mailreplyto($addr,$name='') {
        $forbidden = array("\r","\n","<",">");
        $this->mailreplyto = array(
            'addr' => str_replace($forbidden,'',$addr),
            'name' => $name);
    } // set_mailreplyto()

    /** record the address and the name for the To: header
     *
     * Embedded CRs LFs "<" and ">" are removed from $addr,
     * and the result is stored, together with $name.
     *
     * @param string $addr the address eg. 'hparkh@example.com'
     * @param string $name the name, eg. 'Helen Parkhurst'
     * @return void
     */
    function set_mailto($addr,$name='') {
        $forbidden = array("\r","\n","<",">");
        $this->mailto = array(
            'addr' => str_replace($forbidden,'',$addr),
            'name' => $name);
    } // set_mailto()

    /** add an address and name for the Cc: header
     *
     * Embedded CRs LFs "<" and "> are removed from $addr,
     * and the result is stored, together with $name.
     * Note that this function can be called multiple times,
     * where each call adds an address to the list.
     *
     * @param string $addr the address eg. 'acackl@example.com'
     * @param string $name the name, eg. 'Amelia Cackle'
     * @return void
     */
    function add_mailcc($addr,$name='') {
        $forbidden = array("\r","\n","<",">");
        $this->mailcc[] = array(
            'addr' => str_replace($forbidden,'',$addr),
            'name' => $name);
    } // add_mailcc()

    /** set the message
     *
     * This simply stores the message body until it can be sent.
     * Note that there can be just 1 message. It is assumed to be
     * plain text. However, perhaps it could be a 
     * multipart/alternative (not tested).
     *
     * @param string $message content to send
     * @param string $mimetype type of the content, usually 'text/plain'
     * @param string $charset character set to use
     * @param string $encoding the desired encoding (defaults to quoted-printable because we expect text)
     * @return void
     */
    function set_message($message,$mimetype='text/plain',$charset='UTF-8',$encoding='quoted-printable') {
        $this->message = array(
            'body' => $message,
            'mimetype' => $mimetype,
            'charset' => $charset,
            'encoding' => $encoding);
    } // set_message()

    /** add an attachment
     *
     * This simply adds an attachment with associated properties.
     * Multiple attachments can be added by calling this routine multiple times.
     *
     * @param string $attachment presumably 8bit data to attach to the message
     * @param string $name the suggested filename to use when receiving the attachment
     * @param string $mimetype type of the content, usually the generic 'application/octet-stream'
     * @param string $charset character set to use (only applicable when $mimetype indicates 'text')
     * @param string $encoding the desired encoding (defaults to base64 because we expect binary data)
     * @return void
     */
    function add_attachment($attachment,$name,$mimetype='application/octet-stream',$charset='UTF-8',$encoding='base64') {
        $this->attachments[] = array(
            'body' => $attachment,
            'name' => $name,
            'mimetype' => $mimetype,
            'charset' => $charset,
            'encoding' => $encoding);
    } // add_attachment()

    /** manually add a header to the mail message
     *
     * This adds a header to the message headers. Possible candidates are
     *  - 'Priority' with possible values (from RFC2156): "normal" | "non-urgent" | "urgent"
     *  - 'Importance' with possible values (from RFC2156): "low" | "normal" | "high"
     *
     * Note that the following headers may be overwritten in the course
     * of constructing the message to send (see {@link send()}:
     *  - To: (depending on how mail() handles the first parameter internally)
     *  - Subject: (depending on how mail() handles the second parameter internally)
     *  - From:
     *  - Reply-To:
     *  - Cc:
     *  - X-Mailer:
     *  - Message-ID:
     *  - MIME-Version:
     *  - Content-Type:
     *  - Content-Transfer-Encoding:
     *
     * It is possible to use tricks such as using a different capitalisation to defeat this.
     * (I said it was a simple class, didn't I?)
     *      
     * @param string $name is the name of the header field 
     * @param string $value is the contents of the header field
     * @return void
     * @todo should we bring the Capi-Tali-Sation of $name in line with the default capitalisation in the list above?
     */
    function set_header($name,$value='') {
        $this->headers[$name] = $value;
    } // set_header()

    /** send the message using the prepared information (To:, Subject:, the message and attachments etc.)
     *
     * This actually sends the message (using PHP's mail() command), using all the prepared data & headers etc.
     *
     * Depending on the contents of the message and the number of added attachments, there are the following
     * possibilities.
     *
     *  1. the message is a single plain 7bit ASCII-text with lines shorter than 76 characters, no attachments
     *  2. the message is a text with either 8bit values OR lines longer than 76 characters but still no attachments
     *  3. there are attachments too
     *
     * Ad 1: no need for MIME in that case; simply use the message as-is, using the default
     *  - 7bit encoding and US-ASCII charset. Easy
     *
     * Ad 2: need MIME but not multipart: headers to add:
     *  - MIME-Version: 1.0
     *  - Content-Type: text/plain; charset="UTF-8"
     *  - Content-Transfer-Encoding: quoted-printable
     *
     * Ad 3. need MIME Multipart; headers to add:
     *  - MIME-Version: 1.0
     *  - Content-Type: multipart/mixed; boundary="$boundary"
     *
     * and in the body we need to construct the sequence of message and 1 or more attachments,
     * each with their own headers like
     *  - Content-Type: text/plain; charset="US-ASCII"
     *  - Content-Transfer-Encoding: 7bit
     *    or
     *  - Content-Type: text/plain; charset="UTF-8"
     *  - Content-Transfer-Encoding: quoted-printable
     *
     * and for the attachments (could be more than 1):
     *  - Content-Type: application/octet-stream; name="$name"
     *  - Content-Transfer-Encoding: base64
     *  - Content-Disposition: attachment; filename="$name"
     *
     * and of course with a $boundary between the various parts of the message
     *
     * Note that we listen to the caller most of the time, ie. if the caller specifies an attachment
     * is of type 'application/x-zip', who are we to question that (in {@link add_attachment()}. However,
     * if a message is clearly 7bit ASCII with short lines and of type 'text' (text/plain or text/html or
     * some other text-subtype), we do change the charset to US-ASCII and encoding to 7bit, to make
     * the message just a little bit more readable for the receiver.
     *
     * Finally, we use the mail() command to actually send the message. We add an additional
     * parameter which should instruct sendmail to use the from-address also as the return path
     * (if that is allowed and the webserver is a 'trusted user').
     *
     * @return bool TRUE on success, FALSE on failure (actually the return value of mail())
     * @uses is_7bit()
     * @uses rfc5322_address()
     * @uses rfc2047_qstring()
     * @uses rfc5322_message_id()
     */
    function send() {
        $remaining = $this->max_length - strlen('To: ');
        // quirks in mail(): the (single) to: address MUST be on one line!
        $mailto = $this->rfc5322_address($this->mailto['addr'],$remaining,
                                         $this->mailto['name'],TRUE, // TRUE means use legacy: addr "(" name ")"
                                         $this->charset,$this->minimal,$this->max_length,''); // don't use CR,LF in eol

        $remaining = $this->max_length - strlen('Subject: ');
        $subject = $this->rfc2047_qstring($this->subject,$remaining,
                                          $this->charset,$this->minimal,$this->max_length,$this->eol);

        $headers = $this->headers; // we always use a copy of $this->headers to 'play' with, keep original 'as-is'

        $remaining = $this->max_length - strlen('From: ');
        $headers['From'] = $this->rfc5322_address($this->mailfrom['addr'],$remaining,
                                                  $this->mailfrom['name'],FALSE, // FALSE means no legacy mode
                                                  $this->charset,$this->minimal,$this->max_length,$this->eol);

        if (strcasecmp($this->mailfrom['addr'],$this->mailreplyto['addr']) != 0) {
            $remaining = $this->max_length - strlen('Reply-To: ');
            $headers['Reply-To'] = $this->rfc5322_address($this->mailreplyto['addr'],$remaining,
                                                          $this->mailreplyto['name'],FALSE, // FALSE means no legacy mode
                                                          $this->charset,$this->minimal,$this->max_length,$this->eol);
        }
        if (sizeof($this->mailcc) > 0) {
            $remaining = $this->max_length - strlen('Cc: ') - 1; //  -1 is for the glue
            $headers['Cc'] = '';
            $glue = '';
            foreach($this->mailcc as $mailcc) {
                $headers['Cc'] .= $glue.$this->rfc5322_address($mailcc['addr'],$remaining,
                                                               $mailcc['name'],FALSE, // FALSE means no legacy mode
                                                               $this->charset,$this->minimal,
                                                               $this->max_length - 1, // keep 1 position for the glue
                                                               $this->eol);
                if ($glue == '') { $glue = ','; }
            }
        }

        $headers['X-Mailer'] = sprintf('Website@School Mailer %s (%s, v%s)',WAS_RELEASE,WAS_RELEASE_DATE,WAS_VERSION);
        $headers['Message-ID'] = $this->rfc5322_message_id();

        if (sizeof($this->attachments) < 1) {
            if (($this->is_7bit($this->message['body'])) &&
                (strcasecmp($this->message['mimetype'],'text/plain') == 0) &&      // plain ASCII text
                (wordwrap($this->message['body'],78) == $this->message['body'])) { // and lines no longer than 78 bytes
                //
                // Case 1: 7bit ASCII-text and no attachment
                //
                $body = str_replace(array("\r\n","\n\r","\r"),"\n",$this->message['body']);
                $message = str_replace("\n",$this->eol,$body);
                $headers['MIME-Version'] = '1.0';
                $headers['Content-Type'] = 'text/plain; charset="US-ASCII"';
                $headers['Content-Transfer-Encoding'] = '7bit';
            } else {
                //
                // Case 2: message is not plain ASCII text or has long lines: go encode
                //
                $mimetype = $this->message['mimetype'];
                $charset = $this->message['charset'];
                $encoding = $this->message['encoding'];
                $headers['MIME-Version'] = '1.0';
                if (strncasecmp($mimetype,'text/',5) == 0) {
                    $headers['Content-Type'] = sprintf('%s; charset="%s"',$mimetype,$charset);
                } else {
                    $headers['Content-Type'] = $mimetype;
                }
                if (strcasecmp($encoding,'quoted-printable') == 0) {
                    $headers['Content-Transfer-Encoding'] = 'quoted-printable';
                    $message = quoted_printable($this->message['body']);
                } else {
                    $headers['Content-Transfer-Encoding'] = 'base64';
                    $message = wordwrap(base64_encode($message['body']),$this->max_length,$this->eol,TRUE);
                }
            }
        } else {
            //
            // Case 3: a full-fledged MIME-message with attachments and all
            //
            $boundary = sprintf('----=_%s_%d_%d',strftime('%Y%m%d%H%M%S'),intval(getmypid()),get_unique_number());
            $headers['MIME-Version'] = '1.0';
            $headers['Content-Type'] = sprintf('multipart/mixed; boundary="%s"',$boundary);
            $preamble = 'This is a multi=part message in MIME format.';

            $message = $preamble.$this->eol.
                       sprintf('%s--%s%s',$this->eol,$boundary,$this->eol);

            $mimetype = $this->message['mimetype'];
            $charset = $this->message['charset'];
            $encoding = $this->message['encoding'];
            if (($this->is_7bit($this->message['body'])) && (strncasecmp($mimetype,'text/',5) == 0) && // ASCII text
                (wordwrap($this->message['body'],78) == $this->message['body'])) { // no lines longer than 78 bytes
                //
                // 3A 1: comparable to case 1: 7bit ASCII body with short lines
                //
                $body = str_replace(array("\r\n","\n\r","\r"),"\n",$this->message['body']);
                $charset = 'US-ASCII';
                $encoding = '7bit';
                $message .= sprintf('Content-Type: %s; charset="%s"',$mimetype,$charset).$this->eol.
                            sprintf('Content-Transfer-Encoding: %s',$encoding).$this->eol.
                            $this->eol.
                            str_replace("\n",$this->eol,$body);
            } else {
                //
                // 3A 2: comparable to case 2: message is not plain ASCII or has long lines: go encode
                //
                if (strncasecmp($mimetype,'text/',5) == 0) {
                    $message .= sprintf('Content-Type: %s; charset="%s"',$mimetype,$charset).$this->eol;
                } else {
                    $message .= sprintf('Content-Type: %s',$mimetype).$this->eol;
                }
                if (strcasecmp($encoding,'quoted-printable') == 0) {
                    $message .= 'Content-Transfer-Encoding: quoted-printable'.$this->eol.
                                $this->eol.
                                quoted_printable($this->message['body']);
                } else {
                    $message .= 'Content-Transfer-Encoding: base64'.$this->eol.
                                $this->eol.
                                wordwrap(base64_encode($message['body']),$this->max_length,$this->eol,TRUE);
                }
            }
            //
            // 3B -- attachments, each with its own properties (we believe the caller)
            //
            foreach($this->attachments as $attachment) {
                $message .= sprintf('%s--%s%s',$this->eol,$boundary,$this->eol);

                $name = $attachment['name'];
                $mimetype = $attachment['mimetype'];
                $charset = $attachment['charset'];
                $encoding = $attachment['encoding'];
                if (strncasecmp($mimetype,'text/',5) == 0) {
                    $message .= sprintf('Content-Type: %s; charset="%s"',$mimetype,$charset).$this->eol;
                } else {
                    $message .= sprintf('Content-Type: %s; name="%s"',$mimetype,$name).$this->eol;
                }
                $message .= sprintf('Content-Disposition: attachment; filename="%s"',$name).$this->eol;
                if (strcasecmp($encoding,'quoted-printable') == 0) {
                    $message .= 'Content-Transfer-Encoding: quoted-printable'.$this->eol.
                                $this->eol.
                                quoted_printable($attachment['body']);
                } else {
                    $message .= 'Content-Transfer-Encoding: base64'.$this->eol.
                                $this->eol.
                                wordwrap(base64_encode($attachment['body']),$this->max_length,$this->eol,TRUE);
                }
            }
            $message .= sprintf('%s--%s--%s',$this->eol,$boundary,$this->eol);
        }

        // Join all the headers but do NOT add an EOL after the last header
        $mailheaders = '';
        $glue = '';
        foreach($headers as $k => $v) {
            $mailheaders .= $glue.$k.': '.$v;
            if ($glue == '') { $glue = $this->eol; }
        }
        
        // finally send the message
        $additional = '-f'.$this->mailfrom['addr'];
        $retval = mail($mailto,$subject,$message,$mailheaders,$additional);

        return $retval;
    } // send()

    /** encode a string according to RFC2047 (Message Header Extensions for Non-ASCII Text)
     *
     * This routine encodes $source according to RFC2047 (Message Header Extensions for Non-ASCII Text)
     * using the 'Q'-encoding (somewhat comparable to quoted_printable). However, if the $source uses
     * only harmless 7bit characters and falls within the limit of $remaining characters it is returned
     * unchanged and the number of $remaining characters is updated accordingly.
     *
     * In all other cases ($source contains bytes > 127, $source is longer than $remaining, etc.) this
     * encodes the string into 'encoded-word's of max 75 chars. These 'encoded-word's look like this:
     * <pre>
     * "=?" charset "?" encoding "?" encoded-text "?="
     * </pre>
     * with 'encoding' always equal to "Q" (similar to quoted printable).
     * The actual encoding of characters is done in {@link rfc2047_qchar()}. The boolean flag $minimal
     * can be used to limit the literal representation to only digits and letters, using generic 8bit
     * encoding by setting it to TRUE.
     *
     * If multiple 'encoded-word's are necessary, they are separated from each other by a folding space,
     * i.e. newline (using $eol) followed by a normal space (ASCII 32). The end result never ends with
     * such a folding space; the returned value always ends with the "?=" of the last 'encoded-word'.
     *
     * Note 1:
     * I found it quite hard to read the combination of RFC5322 and RFC2047 because I had some trouble
     * distinguishing the rules for RFC5322-type headers. I finally settled for this simplified set
     *
     *  - From:, To:, Cc: and Reply-To: are all of type 'mailbox' to me (KISS, no 'group's and stuff)
     *
     *  - A 'mailbox' can be either written as [ 'display-name' ] "<" addr-spec ">" OR as
     *    addr-spec "(" ctext ")", which both allow for FWS (folding white space)
     *
     *  - A Subject: field is simply 'unstructured', which also allows for FWS
     *
     * Furthermore, this routine simply encodes non-ASCII text and therefore makes no assumptions
     * about the contents of $source; it is the caller's responsability to make sure that
     * the 'ctext' or 'unstructured' or 'display-name' conforms to RFC5322.
     *
     * Note 2:
     * I have not implemented fancy and streamlined code to minimise the amount of encoded characters,
     * ie. leaving pure ASCII-words unencoded and encoding only non-ASCII words or words containing "=?"
     * because I could not invest that amount of time. Maybe in a later version... (famous last words).
     * I did optimise for short and pure and simple ASCII strings because I expect that generated 
     * Subject: headers and other headers will be ASCII most of the time. We'll see how that works out.
     *
     * Note 3:
     * Quirk: if initially there is not enough space for the shortest possible 'encoded-word', we insert
     * a FWS even if $source has no WSP at that point. Basically it means that we add a character to the
     * result. This may or may not be a problem for the caller. OTOH: the caller should provide enough
     * space in the first place, so there.
     *
     * References: see {@link http://www.ietf.org/rfc/rfc2047.txt} and {@link http://www.ietf.org/rfc/rfc5322.txt}.
     *
     * @param string $source the string to encode (could be 7bit)
     * @param int &$remaining the number of bytes remanining on the current output line (not counting any CR+LF)
     * @param string $charset indicates character set used in $source
     * @param bool $minimal if TRUE, rfc2047_qchar() limits literal encoding to digits and letters
     * @param int $max_length limit on output line length (and indirect of the 'encoded-word' length) to max 76 (75)
     * @param string $eol the end of line character(s), default as per RFC5322 (RFC822) is CR chr(13) + LF chr(10)
     * @return string possibly encoded $source with possibly embedded folding whitespace 
     * @todo maybe optimise this routine to let pure ASCII-words through unencoded (in a later version)
     * @uses rfc2047_qchar()
     * @uses is_7bit()
     */
    function rfc2047_qstring($source,&$remaining,$charset="UTF-8",$minimal=FALSE,$max_length=76,$eol="\r\n") {
        //
        // 1 -- shortcut for the easy cases (simple and short 7-bit strings)
        //
        $n = strlen($source);
        if (($this->is_7bit($source)) &&                    // maybe not US-ASCII but 7 bits nevertheless...
            (strpos($source,"=?") === FALSE) &&             // ...and no spurious begin of an 'encoded-word'...
            ($n <= $remaining)) {                           // ...and room to move: we're done!
            $remaining -= $n;
            return $source;
        }

        //
        // 2 -- prepare for actual rfc2047 encoding (always of type 'Q')
        //
        $encode_head = '=?'.$charset.'?Q?';
        $encode_tail = '?=';
        $overhead = strlen($encode_head) + strlen($encode_tail);

        // 3 -- quick and dirty: simply encode everything including plain ASCII
        $target = '';                                       // collects output
        $i = 0;                                             // index to step through $source
        $max_length = max(min(76,$max_length),$overhead+4); // +1 for folding, +3 for 'worst case' "=XX" encoded_char
        $remaining = min($max_length - 1,$remaining);       // keep first 'encoded-word' within 75 bytes too

        //
        // 3A -- get started with special case (no space left on the current output line)
        //
        $c = ord($source{0});
        $required_len = 1;                                  // create parameter to allow return of value
        $encoded_char = $this->rfc2047_qchar($c,$required_len,$minimal);
        if ($remaining < $overhead + $required_len) {       // Alas, we MUST fold at this point... :-(
            if (($c == 9) || ($c == 32)) {
                $target .= $eol.chr($c).$encode_head; 
                $remaining = $max_length - 1 - $overhead;   // already take length of $encode_tail into account
            } else {                                        // See note above: alas, we HAVE to insert an extra WSP here
                $target .= $eol.chr(32).$encode_head.$encoded_char;
                $remaining = $max_length - 1 - $overhead - strlen($encoded_char);
            }
            ++$i;                                           // Remember we already processed the first character
        } else {                                            // unconditionally start with the first 'encoded-word'
            $target .= $encode_head;
            $remaining -= $overhead;
        }

        //
        // 3B -- step through string and convert into 'encoded-word's of at most $max_length - 1 bytes
        //
        while ($i < $n) {
            $c = ord($source{$i++});
            $encoded_char = $this->rfc2047_qchar($c,$required_len,$minimal);
            if ($remaining < $required_len) {
                $target .= $encode_tail.$eol.chr(32).$encode_head;
                $remaining = $max_length - 1 - $overhead;
            }
            $target .= $encoded_char;
            $remaining -= strlen($encoded_char);
        }
        $target .= $encode_tail;
        return $target;
    } // rfc2047_qstring()

    /** encode an 8-bit byte according to Q-encoding in RFC2047
     *
     * This routine encodes a single integer ASCII code into either
     *  - literal representaion
     *  - generic 8bit representation, ie. "=" followed by 2 (uppercas)e hexdigits
     *  - an underscore character
     *
     * If $minimal is FALSE, all printable ASCII characters from 33 "!" to 126 "~"
     * except 61 "=", 63 "?" and 95 "_" use literal representation. Character 32 " "
     * is represented as an underscore (for improved readabilitu/deciphering).
     *
     * If $minimal is TRUE, only digits "0" - "9" and letters "A" - "Z" and "a" - "z"
     * use literal representation and character 32 " " uses generic 8bit encoding "=20".
     *
     * The latter case yields only digits, letters, equal-sign and question mark, which
     * should travel undisturbed through any mail transdfer agent.
     *
     * There is a special situation when encoding UTF8 where characters can span multiple
     * octets. The length of such a sequence can be determined by the number of most significant
     * 1's in a row in the first octet. If $c is the first octet of a UTF8-sequence, we tell
     * the caller the total length of the encoded sequence, not just the length of the encoded
     * 1st octet (which would always be 3). This forces the caller to start a new 'encoded-word'
     * with enough room for the complete sequence if necessary, preventing a multi-octet sequence
     * to span two 'encoded-words'. Note that characters in a UTF8-tail yield length 3, even when
     * more UTF8-tail octets follow. That is OK because the first character already 'reserved' the
     * space when the first octet was processed.
     *
     * Here is a small truth table for sequence lengths (see also RFC3629).
     * bit pattern   range len  comments   
     * 0xxx.xxxx     0-127   3  ASCII
     * 10xx.xxxx   128-191   3  octet is part of UTF8-tail
     * 110x.xxxx   192-223   6  UTF8-2, beginning of a sequence of 2 octets
     * 1110.xxxx   224-239   9  UTF8-3, beginning of a sequence of 3 octets
     * 1111.0xxx   240-247  12  UTF8-4, beginning of a sequence of 4 octets
     * 1111.10xx   248-251   3  sequence of 5 characters not defined in RFC3929, settle for length 3
     * 1111.110x   252-253   3  sequence of 6 characters not defined in RFC3929, settle for length 3
     * 1111.111x   254-255   3  no sequence at all, settle for length 3
     *
     * Note that if $c is NOT UTF8 but say ISO-5988-1, the worst that can happen is that a perfectly
     * valid single octet character in the range 192-247 would indicat a length of more than the
     * necessary 3, pushing up to 4 characters to the next 'encoded-word'. Oh well, I can live with that.
     *
     * References: {@link http://www.ietf.org/rfc/rfc2047.txt}, {@link http://www.ietf.org/rfc/rfc3629.txt}.
     *
     * @param int $c the character to encode
     * @param int &$required_len returns the space required for this encoded char (UTF8-aware)
     * @param bool $minimal if TRUE, only [0-9A-Za-z] use literal representation, otherwise encoding is more relaxed
     * @return string the encoded character either as literal or as generic 8bit in the form "=XX"; $required_len set
     */
    function rfc2047_qchar($c,&$required_len,$minimal=FALSE) {
        if ($c == 32) {
            $encoded_char = ($minimal) ? '=20' : '_';
            $required_len = ($minimal) ? 3 : 1;
        } elseif (($minimal) && (((48 <= $c) && ($c <= 57)) ||      // '0' - '9'
                                 ((65 <= $c) && ($c <= 90)) ||      // 'A' - 'Z'
                                 ((97 <= $c) && ($c <= 122)))) {    // 'a' - 'z'

            $encoded_char = chr($c);
            $required_len = 1;
        } elseif ((!$minimal) && ((33 <= $c) && ($c <= 126) &&               // printable ASCII
                                  ($c != 61) && ($c != 63) && ($c != 95))) { // and not '=', '?', or '_'
            $encoded_char = chr($c);
            $required_len = 1;
        } else {
            $encoded_char = sprintf('=%02X',$c);
            if     ($c < 192) { $required_len =  3; } // ASCII/UTF8-1 or UTF8-tail (see RFC3629 section 4) 
            elseif ($c < 224) { $required_len =  6; } // UTF8-2 (including overlong sequences)
            elseif ($c < 240) { $required_len =  9; } // UTF8-3 (including surrogate pairs)
            elseif ($c < 248) { $required_len = 12; } // UTF8-4
            else              { $required_len =  3; } //longer/unrecognised sequences
        }
        return $encoded_char;
    } // rfc2047_qchar()

    /** a small utility routine to determine if a string has only 7bit characters
     *
     * @param string $source the text to examine
     * @return bool TRUE if no 8-bit characters were found, FALSE otherwise
     */
    function is_7bit($source) {
        $n = strlen($source);
        for ($i=0; $i < $n; ++$i) {
            if (ord($source{$i}) & 128) {
               return FALSE;
            }
        }
        return TRUE;
    } // is_7bit()

    /** construct an address field according to RFC5322 (RFC822)
     *
     * This routine constructs an address according to (simplified) rules in RFC5322 section 3.4.
     * Depending on the $legacy flag, the parameters are used to construct either an 'angle-addr'
     * <pre>
     * DQUOT $display_name DQUOT SPACE "<" $addr_spec ">"
     * </pre>
     * or an address with the display-name "hidden" in a comment
     * <pre>
     * $addr_spec SPACE "(" $display_name ")"
     * </pre>
     *
     * Basically the $addr_spec is not modified; it is assumed that this string obeys the rules
     * for 'addr-spec' in RFC5322. Specifically we do not encode this information
     * (with {@link rfc2047_qstring()} or otherwise). However, we DO strip any CR and/or LF characters
     * because these might cause problems lateron (eg. an unwanted extra blank line in the mail headers).
     * Also, we definately do not want to have angle brackets, so we remove those too, just to be sure.
     *
     * The $display_name can be modified. Reading RFC5322 yields the following (simplified) rules.
     * 'display-name' => 'phrase' => 1*'word'; 'word' => 'atom' | 'quoted-string'. In other words:
     * it is allowed to use a quoted string, ie. 
     *  - a DQUOTE, followed by
     *  - a string with printable ASCII characters not being DQUOTE or the quote character backslash, followed by
     *  - a DQOTE.
     *
     * Note that FWS (folding white space) is allowed between the two DQUOTEs.
     * This leads an easy way out of to stripping DQUOTEs and backslashes before the
     * 'display-name' is encoded and/or folded.
     *
     * If the legacy-flag is set, we use the parameter $display_name to construct a 
     * (simplified) comment, ie.
     *  - a "(", followed by
     *  - a string of ctext characters not containing "(", ")" or a backslash, followed by
     *  - a ")".
     * Here, too, folding whitespace is allowed between the opening "(" and closing ")".
     * This variant leads the easy way out of stripping parentheses and backslashes
     * before the 'display-name' is encoded and/or folded as a comment.
     *
     * Note:
     * All this cleaning up of CR, LF, DQUOTE, etc. does NOT weed out other CTLs (ASCII 0...ASCII 31).
     *
     * @param string $addr_spec is an address of the form 'local-part' "@" 'domain' (RFC5322 section 3.4.1)
     * @param int &$remaining indicates how much space is left on the current output line
     * @param string $display_name is the human readable name associated with $addr_spec
     * @param bool $legacy if TRUE, output is in the legacy format 'addr-spec' "(" 'ctext' ")"
     * @param string $charset indicates character set used in $display_name
     * @param bool $minimal if TRUE, eventually rfc2047_qchar() limits literal encoding to digits and letters
     * @param int $max_length limit on output line length (and indirect of the 'encoded-word' length) to max 76 (75)
     * @param string $eol the end of line character(s), default as per RFC5322 (RFC822) is CR chr(13) + LF chr(10)
     * @return string ready-to-use header, possibly encoded and/or folded; $remaining updated
     * @uses rfc2047_qstring()
     */
    function rfc5322_address($addr_spec,&$remaining,$display_name='',$legacy=FALSE,
                                                    $charset="UTF-8",$minimal=FALSE,$max_length=76,$eol="\r\n") {
        $address = ''; // collects output
        $forbidden = array(chr(10),chr(13),'<','>');
        $addr_spec = str_replace($forbidden,'',$addr_spec); // make sure addr_spec is clean
        if ($legacy) {
            $required = strlen($addr_spec);
            if ($remaining < $required) {
                $address .= $eol.chr(32);
                $remaining = $max_length - 1;       // -1 for the folding space
            }
            $address .= $addr_spec;
            $remaining -= $required;
            $forbidden = array(chr(10),chr(13),'(',')','\\'); // easy way out: strip unwanted characters from input
            $display_name = str_replace($forbidden,'',$display_name);
            if (!empty($display_name)) {
                if ($remaining < 2) {
                    $address .= $eol;
                    $remaining = $max_length;;
                }
                $address .= " (";
                $remaining -= 2;
                $address .= $this->rfc2047_qstring($display_name,$remaining,$charset,$minimal,$max_length,$eol);
                if ($remaining < 1) {
                    $address .= $eol.chr(32);
                    $remaining = $max_length - 1;       // -1 for the folding space
                }
                $address .= ")";
                --$remaining;
            }
        } else {
            $forbidden = array(chr(10),chr(13),chr(34),'\\'); // easy way out: strip unwanted characters from input
            $display_name = str_replace($forbidden,'',$display_name);
            $angle_addr = '<'.$addr_spec.'>';
            if (!empty($display_name)) {
                if ($remaining < 1) {
                    $address .= $eol.chr(32);
                    $remaining = $max_length - 1;       // -1 for the folding space
                }
                $address .= chr(34);                    // DQUOTE opening 'quoted-string'
                --$remaining;
                $address .= $this->rfc2047_qstring($display_name,$remaining,$charset,$minimal,$max_length,$eol);
                if ($remaining < 1) {
                    $address .= $eol.chr(32);
                    $remaining = $max_length - 1;       // -1 for the folding space
                }
                $address .= chr(34);                    // DQUOTE closing 'quoted-string'
                --$remaining;
                if (strlen($angle_addr) < $remaining) { // SP + angle_addr will fit on remaining space
                    $address .= chr(32);                // so we won't have folding problems after adding
                    --$remaining;                       // this simple SP
                }
            }
            $required = strlen($angle_addr);
            if ($remaining < $required) {
                $address .= $eol.chr(32);
                $remaining = $max_length - 1;           // -1 for the folding space
            }
            $address .= $angle_addr;
            $remaining -= $required;
        }
        return $address;
    } // rfc5322_address()

    /** construct a message-id conforming to RFC5322 (RFC2822, RFC822)
     *
     * This constructs a message id according to specifications in section
     * 3.6.4 in RFC5322 Internet Message Format (October 2008),
     * see {@link http://www.ietf.org/rfc/rfc5322.txt}.
     *
     * Note that the 'id-left' in the 'msg-id' also contains the remote
     * IP-address. This could be an IPv4 address in the usual dotted-decimal
     * form but it could also be an IPv6 address like '::1' (3 characters) or
     * '[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]' (41 characters).
     * The total maximum-length of 'id-left' may add up to
     * 11 (32-bit signed pid) + 1 (dot) + 41 (full IPv6 w/ brackets) + 1 (hash) +
     * 11 (32-bit signed remote port) + 1 (dot) + 14 (date/time) + 11 (signed
     * unique number) = 91 characters. This is longer than the recommended
     * linelength of 78 characters, but the absolute maximum of 998 characters
     * will probably NOT be reached. OTOH: we do not actually check for
     * huge domain names and/or server names. Oh well.
     *
     * Note that we massage the IPv6 address by replacing any ':', '[' and ']'
     * with '!', '{' and '}' respectively because the former are not allowed in
     * a dot-atom-text. As a matter of fact we translate most 'specials' to
     * 'atext' (RFC5322 3.2.3). Notable exception: the dot stays.
     *
     * @return string message-id according to RFC5322 section 3.6.4
     * @todo how about UTF-8 hostnames? Mmmm...
     */
    function rfc5322_message_id() {
        global $CFG;

        // 1 -- construct id-right according to RFC5322
        $id_right = '';

        // 1A -- try bare host/domain name from this site's URL
        if (($url = parse_url($CFG->www)) !== FALSE) {
            // labels [RFC1035] are dot-atom-text [RFC5322] hence the ereg_replace()
            $id_right = (isset($url['host'])) ? trim(ereg_replace('[^a-zA-Z0-9.-]','',$url['host']),'.') : '';
        }
        // 1B -- if no joy, try the server name
        if ((empty($id_right)) && (isset($_SERVER['SERVER_NAME']))) {
            $id_right = trim(ereg_replace('[^a-zA-Z0-9.-]','',$_SERVER['SERVER_NAME']),'.');
        }
        // 1C -- last resort: localhost
        if (empty($id_right)) {
            $id_right = 'localhost.localdomain'; // last resort
        }
        // 2 -- construct msg-id with a hopefully unique id-left
        $msg_id = sprintf('<%d.%s#%d.%s.%d@%s>',
                            intval(getmypid()),
                            (isset($_SERVER['REMOTE_ADDR'])) ? strtr($_SERVER['REMOTE_ADDR'],
                                                                     ':[]()<>;@,\\',
                                                                     '!{}{}{}!*-/' ) : '127.0.0.1',
                            (isset($_SERVER['REMOTE_PORT'])) ? intval($_SERVER['REMOTE_PORT']) : 42,
                            strftime('%Y%m%d%H%M%S'),
                            get_unique_number(),
                            $id_right);
        return $msg_id;    
    } // rfc5322_message_id()


} // Email

?>