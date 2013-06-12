#!/usr/bin/php -q
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

/** /devel/tools/minjs.php - minimise javascript files (sort of)
 *
 * this small quick and dirty program attempts to strip comments from
 * javascript code in order to make it run a little faster.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @version $Id: minjs.php,v 1.1 2013/06/12 14:00:24 pfokker Exp $
 */
$c          = '';
$comment    = FALSE;
$slash      = FALSE;
$slashslash = FALSE;
$asterisk   = FALSE;
$line       = '';
$quoted     = FALSE;
$quote      = '';

while (($c = getchar()) !== FALSE) {
  if ($quoted) {
    if ($backslash) {
      $line .= $c;
      $backslash = FALSE;
    } elseif ($c == '\\') {
      $line .= $c;
      $backslash = TRUE;
    } elseif ($c == $quote) {
      $line .= $c;
      $quoted = FALSE;
      $backslash = FALSE;
    } else {
      $line .= $c;
    }
  } else {
    if ($comment) { // we are in a block comment
      if ($c == '*') { // keep quiet even when 2 asterisks in a row
	$asterisk = TRUE;
      } else if (($c == '/') && ($asterisk)) {
	$comment=FALSE;
	$asterisk=FALSE;
      } else {
	$asterisk=FALSE;
      }
      debug($c);
    } elseif ($slashslash) { // we are in a line comment
      debug($c);
      if ($c == "\n") {
	$slashslash = FALSE;
	$line = trim($line);
	if (!empty($line))
	  puts($line."\n");
	$line = '';
      }
    } else if ($slash) {
      if ($c == '/') {
	$slashslash=TRUE;
	debug('//');
      } elseif ($c == '*') {
	$comment=TRUE;
	debug('/*');
      } else {
	$line .= '/'.$c;
      }
      $slash = FALSE;
    } else if ($c == '/') { // maybe start a // or /* comment
      $slash = TRUE;
    } else if ($c == "\n") {
      $line = trim($line);
      if (!empty($line))
	puts($line."\n");
      $line = '';
    } elseif (($c == "'") || ($c == '"')) { // start of quoted string?
      $quoted = TRUE;
      $quote = $c;
      $line .= $c;
      $slash = FALSE;
    } else {
      $line .= $c;
    }
  }
}
if (!empty($line)) {
  puts(trim($line)."\n");
  $line = '';
}
exit(0);

function getchar() { return fgetc(STDIN); }
function puts($s) { return fwrite(STDOUT, $s, strlen($s)); }
function debug($s) { return; fwrite(STDERR, $s, strlen($s)); }

/* eof */
