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

/** /program/modules/snapshots/inlineshow.js - inline slideshow function for snapshots
 *
 * This file contains the Javascript-code that implements a simple
 * inline slideshow function. Multiple inline slideshows can co-exist
 * because each has its own configuration (determined via a handle).
 *
 * The following functions are defined:
 *
 * int inline_show_create(width, height, visible)
 * void inline_show_add(handle, width, height, url, delay, title)
 * void inline_show_run(handle)
 * void inline_show_pause(handle)
 * void inline_show_bump(handle)
 *
 * Configuration is done by calling inline_show_add() once for every image
 * in the inline slideshow. Subsequently calling inline_show_run() starts
 * the action. Calling inline_show_pause() toggles running/pausing the
 * inline slideshow.
 *
 * Note that all variables are defined in this file, but the configuration is done
 * inline in the HTML-page, ie. after this file is already loaded.
 *
 * Styling can be done using the id's and classes that are generated:
 *
 * <div id="prefix_container" class="inline_show_container">
 *  <img id="prefix_imageI" class="inline_show_image"> I=0,...,visible-1
 *  <img id="prefix_queueJ" class="inline_show_queue"> J=0,...,images-1
 * </div>
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_snapshots
 * @version $Id: inlineshow.js,v 1.1 2012/07/01 11:20:11 pfokker Exp $
 */

/*
 * (global) variable declarations
 */
var inline_shows=0;			/* # of defined inline shows, also supplies handle */
var inline_show_width=new Array();	/* (effective) width of the inline_show container */
var inline_show_height=new Array();	/* (effective) height of the inline_show container */
var inline_show_visible=new Array();	/* the number of visible images (minimum 1) */
var inline_show_prefix=new Array();	/* the unique prefix to use in element id's */
var inline_show_image=new Array();	/* holds the image properties */
var inline_show_images=new Array();	/* holds the # of images in image array */
var inline_show_delta=new Array();	/* increment to next visible image (usually +1) */
var inline_show_next=new Array();	/* index of next image to show (0=first) */
var inline_show_timer=new Array();	/* timer keeps track of timing per image */
var inline_show_running=new Array();	/* (sort of) prevents running two instances of the same show */
var inline_show_waitasec=new Array();	/* # of seconds we're already waiting for images to load */
var inline_show_msg=['loading...','no images']; /* error/warning messages */


function inline_show_create(width,height,visible) {
  var h=inline_shows++;
  inline_show_width[h]=(width<1)?1:Math.floor(width);
  inline_show_height[h]=(height<1)?1:Math.floor(height);
  inline_show_visible[h]=(visible<1)?1:visible;
  inline_show_prefix[h]='inline_show_'+h.toString();
  inline_show_image[h]=new Array();
  inline_show_images[h]=0;
  inline_show_delta[h]=1;
  inline_show_next[h]=0;
  inline_show_timer[h]=0;
  inline_show_running[h]=0;
  inline_show_waitasec[h]=0;
  return h;
} // inline_show_create()


function inline_show_add(h,width,height,url,delay,title) {
  var vx=Math.floor(inline_show_width[h] / inline_show_visible[h]);
  var vy=inline_show_height[h];
  var px=width;
  var py=height;
  var r=Math.min(px*vy,py*vx);
  width=Math.ceil(r/py);
  height=Math.ceil(r/px);
  delay=(delay<1)?1:Math.floor(delay);
  inline_show_image[h].push([width,height,url,delay,title]);
  inline_show_images[h]=inline_show_image[h].length;
} // inline_show_add() */


function inline_show_run(h) {
  var i,j;
  var prefix=inline_show_prefix[h];
  var html='<div id="'+prefix+'_container" class="inline_show_container">\n';
  for (i=0; i<inline_show_visible[h]; ++i) {
    html += '<img id="'+prefix+'_image'+i.toString()+'" class="inline_show_image" width="1" height="1" onclick="inline_show_pause('+h.toString()+')" >\n';
  }
  for (i=0; i<inline_show_images[h]; ++i) {
    html += '<img id="'+prefix+'_queue'+i.toString()+'" class="inline_show_queue" style="display:none;">\n';
  }
  html += '</div>';
  document.writeln(html);
  if (inline_show_images[h] <= 0) {
    document.writeln(inline_show_msg[1]); // no images; bail out
    return;
  }
  if (inline_show_running[h]) {
    clearTimeout(inline_show_timer[h]);
    inline_show_running[h]=0;
  }
  inline_show_running[h]=1;
  inline_show_next[h]=0; // always start with the first image 

  // preload the visible requested images and let the dust settle for a little while
  for (i=0; i<inline_show_visible[h]; ++i) {
    j=(i % inline_show_images[h]);
    document.getElementById(prefix+'_queue'+j.toString()).src=inline_show_image[h][j][2];
  }
  inline_show_timer[h]=setTimeout(function(){inline_show_bump(h)},1000);
} // inline_show_run()


function inline_show_pause(h) {
  if (inline_show_running[h]) {
    clearTimeout(inline_show_timer[h]);
    inline_show_running[h]=0;
  } else {
    inline_show_running[h]=1;
    inline_show_bump(h);
  }
} // inline_show_pause()


function inline_show_bump(h) {
  var e,i,j;

  // do we still exist?
  if (document == null) {
    clearTimeout(inline_show_timer[h]);
    inline_show_running[h]=0;
    return;
  }

  // check if images are preloaded (max wait: 30s/image)
  var prefix=inline_show_prefix[h];
  if (inline_show_waitasec[h]<30) {
    for (i=0; i<inline_show_visible[h]; ++i) {
      j=(i % inline_show_images[h]);
      if (!document.getElementById(prefix+'_queue'+j.toString()).complete) {
        ++inline_show_waitasec[h];
        inline_show_timer[h]=setTimeout(function(){inline_show_bump(h)},1000);
        return;
      }
    }
  }
  inline_show_waitasec[h]=0;

  var next=inline_show_next[h];
  for (i=0; i<inline_show_visible[h]; ++i) {
    j=((next+i) % inline_show_images[h]);
    e=document.getElementById(prefix+'_image'+i.toString());
    e.width=inline_show_image[h][j][0];
    e.height=inline_show_image[h][j][1];
    e.src=inline_show_image[h][j][2];
    e.alt=inline_show_image[h][j][4];
    e.title=inline_show_image[h][j][4];
  }
  var zzz=inline_show_image[h][next][3]; // use the delay of the 1st visible image
  next=((next+inline_show_delta[h]+inline_show_images[h]) % inline_show_images[h]);
  inline_show_next[h]=next;
  for (i=0; i<inline_show_visible[h]; ++i) {
    j=((next+i) % inline_show_images[h]);
    document.getElementById(prefix+'_queue'+j.toString()).src=inline_show_image[h][j][2];
  }

  inline_show_timer[h]=setTimeout(function(){inline_show_bump(h)},1000*zzz);
} // inline_show_bump()

// eof inlineshow.js
