/* 
 * This file is part of Website@School, a Content Management System especially designed for schools.
 * Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker <peter@berestijn.nl>
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by
 * the Free Software Foundation supplemented with the Additional Terms, as set
 * forth in the License Agreement for Website@School (see /program/license.html).
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License
 * for more details.
 *
 * You should have received a copy of the License Agreement for Website@School
 * along with this program. If not, see http://websiteatschool.eu/license.html
 */

/** /program/modules/snapshots/slideshow.js - simple snapshots slideshow function
 *
 * This file contains the Javascript-code that implements a simple
 * slideshow function for the snapshots module. It should be included
 * at the top of the HTML-page (in the head-section). Configuration for
 * the list of snapshots is done dynamically (from {@link snapshots_view.php}).
 * Configuration is done via an array of arrays called img[].
 * The n'th image (starting at 0) in that array is defined as follows:
 *
 * img[n][0] = width of the image (in pixels)
 * img[n][1] = height of the image (in pixels)
 * img[n][2] = the url of the image file (src-attribute of the img tag)
 * img[n][3] = the number of seconds to display this image
 * img[n][4] = title to add to the display (document title)
 *
 * Note that the array is defined in this file, but the configuration is done
 * inline in the HTML-page, ie. after this file is already loaded and img is
 * already created.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_snapshots
 * @version $Id: slideshow.js,v 1.1 2012/05/30 12:47:17 pfokker Exp $
 */
var img=new Array(); // contains image definitions (see above for syntax)
var hwnd=null;       // handle to the _blank pop-up window we create
var delta=1;         // increment to get to next (can be -1 too)
var next=0;          // points to the next image to show
var timer;           // used to keep track of the timing per image
var running=0;       // (sort of) prevents running two instances
var msg=['loading...','no images']; // error/warning messages
var waitasec=0;      // time (seconds) we're already waiting for img to preload


/** create new window to show images and start the loop
 *
 * @param int n indicates the image to start with (0=first)
 * @return void
 */
function show_start(n) {
  if (img.length <= 0) {
    alert(msg[1]);
    return;
  }
  features = 'width='+screen.width+',height='+screen.height+
             ',location=0,menubar=0,scrollbars=0,status=0,titlebar=0,toolbar=0';
  hwnd = window.open('','',features);
  var html='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">\n'+
           '<html><head><title></title></head>\n'+
           '<body text="#FFFF00" bgcolor="#000000">\n'+
           '<div style="text-align:center;cursor:none;">\n'+
           '<img id="image" width="1" height="1">\n';
  var i=0;
  for (i=0; i<img.length; ++i) {
    html += '<img id="image'+i.toString()+'" style="display:none;">\n';
  }
  html += '</div></body></html>';

  hwnd.document.open();
  hwnd.document.writeln(html);
  hwnd.document.close();

  next=n % img.length;
  if (running) {
    clearTimeout(timer);
    running=0;
  }
  running=1;
  // preload the requested image and let the dust settle for a few seconds
  hwnd.document.getElementById('image'+next.toString()).src=img[next][2];
  hwnd.document.title=msg[0];
  timer=setTimeout("show_next()",2000);
  hwnd.focus();
} // show_start()


/** show the next image from the list and also start (pre)loading another one
 *
 * Note:
 * Code for determining the viewport inspired by an article by Andy Langton
 * http://andylangton.co.uk/articles/javascript/get-viewport-size-javascript/
 * and subsequent refinements by 'elstcb'.
 *
 * @return void
 */
function show_next() {
  //
  // quit immediately if user removed popup window via [Alt-F4] or otherwise
  //
  if ((hwnd == null) || (hwnd.document == null)) {
    clearTimeout(timer);
    running=0;
    return;
  }

  //
  // make sure the image to show is done preloading
  //
  if (!hwnd.document.getElementById('image'+next.toString()).complete) {
    ++waitasec;
    hwnd.document.title=msg[0]+' ('+waitasec.toString()+')';
    timer=setTimeout("show_next()",1000);
    return;
  } else {
    waitasec=0;
  }

  //
  // determine viewport in (vx,vy) (see note above)
  //
  var d=hwnd.document;
  var e=d.documentElement;
  var b=d.getElementsByTagName('body')[0];
  var vx=hwnd.innerWidth||e.clientWidth||b.clientWidth;
  var vy=hwnd.innerHeight||e.clientHeight||b.clientHeight;

  //
  // get to work on the image that was preloaded in the last iteration
  //
  var px=img[next][0];
  var py=img[next][1];
  var src=img[next][2];
  var zzz=img[next][3];
  var alt=img[next][4];

  //
  // scale the image to maximum that fits within viewport
  //
  var r=Math.min(px*vy,py*vx);
  var x=Math.ceil(r/py);
  var y=Math.ceil(r/px);

  //
  // plugin the preloaded image+properties into the visible img tag
  //
  e=d.getElementById('image')
  e.height=y;
  e.width=x;
  e.src=src;
  e.alt=alt;
  d.title=alt;

  //
  // bump the pointer to the next image (could go backwards)...
  //
  next += delta+img.length;
  next %= img.length;
  
  //
  // ...and start the preload in the corresponding invisible img tag
  //
  px=img[next][0];
  py=img[next][1];
  src=img[next][2];

  var r=Math.min(px*vy,py*vx);
  var x=Math.ceil(r/py);
  var y=Math.ceil(r/px);

  e=d.getElementById('image'+next.toString());
  e.height=y;
  e.width=x;
  e.src=src;

  // see ya in zzz seconds...
  timer=setTimeout("show_next()",1000*zzz);
} // show_next()

// eof slideshow.js
