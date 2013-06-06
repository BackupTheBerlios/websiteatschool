/*
 * This file is part of Website@School, a Content Management System especially designed for schools.
 * Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker <peter@berestijn.nl>
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

/** /program/modules/crew/crew.js - tools for cooperative remote editor workshop
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_crew
 * @version $Id: crew.js,v 1.2 2013/06/06 13:51:34 pfokker Exp $
 */
var crew='CREW';
var crewName='Cooperative Remote Educational Workshop';
var crewVersion='0.90.4';
var crewDate='2013-06-04';
var crewId='$Id: crew.js,v 1.2 2013/06/06 13:51:34 pfokker Exp $';

var w;			// reference to this window's opener

var ws;			// websocket
var protocols='crew.websiteatschool.eu';

var divEdit;		// Main edit area
var divMembers;		// List of current workshop (co-)workers
var btnSave;		// Save current version of text + exit
var btnSaveEdit;	// Save current version of test + continue
var btnCancel;		// Abandon current version of text + exit
var btnRefresh;		// Request fresh copy of status quo from socket server
var txtMessage;		// Buffer to create out-of-band message
var btnMessage;		// Send txtMessage to workshop
var btnSound;		// Toggle sound
var divMessages;	// Container for received out-of-band messages

var userList=[];	// 0-based array with user information (nick, name, etc.)
var workText='';	// String containing the current version of the text
var workAttr='';	// String containinf the ownership of the text per character

var myColor='?';	// This is the color I write in
var myAddress='';	// My IP-address as seen by the server (received via I-cmd)
var myPort='';		// My port as seen by the server
var myRange='0';	// My current position (may be a range like sss-eee or point ccc if collapsed)

var debugCount=0;
var debug=function(s) { var d=new Date; return; logger('DEBUG('+(++debugCount)+'.'+d.getSeconds()+'): '+s,'error'); }
var maxContextLength=3;	// context in Diff

var beep;		// an audio object used to play a sound whenever a char message is received
var beeps=false;	// toggle to enable/disable beeps (we start with false and toggle once in init)
var timeRedraw=null;	// make sure we eventually repaint the edit buffer

//        ===INITIALISATION===
 
/** initialise the workshop
 *
 * prepare the current window for the workshop. This includes opening
 * the websocket connection and setting up the user interface. If the
 * browser does not support the websocket protocol, we bail out 
 * after telling the user it didn't work out.
 *
 */
function crew_init() {
  // 0 -- create access to the window that called us
  w=window.opener;

  // 1 -- setup handy references to our user interface
  // 1A -- main edit area
  divEdit=document.getElementById('divCrewEdit');
  divMembers=document.getElementById('divCrewMembers');
  btnSave=document.getElementById('btnCrewSave');
  btnCancel=document.getElementById('btnCrewCancel');
  btnSaveEdit=document.getElementById('btnCrewSaveEdit');
  btnRefresh=document.getElementById('btnCrewRefresh');
  // 1B -- chat
  txtMessage=document.getElementById('txtCrewMessage');
  btnMessage=document.getElementById('btnCrewMessage');
  divMessages=document.getElementById('divCrewMessages');
  btnSound=document.getElementById('btnCrewSound');
  // 2 -- is websocket supported anyway?
  if (!window.WebSocket) {
    btnSave.disabled=true;
    btnSaveEdit.disabled=true;
    btnRefresh.disabled=true;
    btnMessage.disabled=true;
    txtMessage.disabled=true;
    btnCancel.onclick=doCancel;
    logger(hhmm()+str(0),'error'); // WebSocket protocol not supported
    ws=(function(){ return { // create a dummy so doCancel() can call safely ws.send()
      send: function(msg) { return msg; },
      close: function() { return; }
    }}())
    return;
  }

  // 3 -- continue initialising
  document.title=reqWorkshop;
  btnSave.onclick=doSave;
  btnSaveEdit.onclick=doSaveEdit;
  btnCancel.onclick=doCancel;
  btnRefresh.onclick=doRefresh;
  btnMessage.onclick=doMessage;
  btnSound.onclick=doSoundToggle;
  divEdit.onmouseup=doDiff;
  divEdit.onkeyup=doDiff;
  //  divEdit.onblur=doDiff;

  window.onbeforeunload=onBeforeUnload;
  txtMessage.onkeypress=onKeyPressMessage;

  var url=reqLocation + reqWorkshop;
  ws=new WebSocket(url,protocols);
  ws.onopen=function(msg) { onOpen(msg);    };
  ws.onclose=function(msg) { onClose(msg);   };
  ws.onmessage=function(msg) { onMessage(msg); };
  ws.onerror=function(msg) { onError(msg);   };
  logger(hhmm()+str(1)); // INITIALISED

  // prevent [Esc] from killing the websockets connection
  window.addEventListener('keydown', function(e) {
    var k=(window.event)?e.keyCode:e.which;
    if (k==27) e.preventDefault();});

  // At least one name while waiting for the first U-message from server...
  userList=[[reqUserId, reqUserName, '@', 0]];
  divEdit.innerHTML=workText=workAttr=''; // filled in eventually via T-message from server

  // setup the beep functionality
  beep=new Audio();
  if (beep.canPlayType('audio/ogg')) beep.src=crewDir+'/beep.ogg';
  else if (beep.canPlayType('audio/x-wav')) beep.src=crewDir+'/beep.wav';
  else if (beep.canPlayType('audio/mpeg')) beep.src=crewDir+'/beep.mp3';
  beep.load();
  doSoundToggle(); // initially switch sound on
  redrawUserList();
  divEdit.focus(); // start in edit pane
} // crew_init()

//        ===WEBSOCKET FUNCTIONS===

function onOpen(msg) {
  logger(hhmm()+str(2)); // CONNECTED
  sendAuthentication();
} // onOpen()

function onClose(e) {
  var r={'{CODE}':e.code||'???','{REASON}':e.reason||'????'};
  var s=str((e.wasClean)?3:4,r);
  var color=(e.wasClean)?'@':'error';
  if (e.code==1008) { // server closed with error: Unauthorised or workers>WMAX or shops>SMAX
    btnSave.disabled=true;
    btnSaveEdit.disabled=true;
    btnRefresh.disabled=true;
    btnMessage.disabled=true;
    txtMessage.disabled=true;
    color='error';
  }
  logger(hhmm()+s,color); // 'DISCONNECTED (clean|unclean): code={CODE} reason={REASON}
} // onClose()

function onMessage(msg) {
  switch (msg.data.charAt(0)) {
  case 'E': handleEnter(msg.data); break;
  case 'I': handleInfo(msg.data); break;
  case 'L': handleLeave(msg.data); break;
  case 'M': handleMessage(msg.data); break;
  case 'P': handlePatch(msg.data); break;
  case 'R': handleRelocate(msg.data); break;
  case 'T': handleTextRefresh(msg.data); break;
  case 'U': handleUserList(msg.data); break;
  default:
    var r={'{ORIGIN}':msg.origin||'???','{DATA}':msg.data||''}; 
    logger(hhmm()+str(5,r),'error'); // {ORIGIN}: unknown message: {DATA}
    break;
  }
} // onMessage()

function onError(msg) {
  var r={'{DATA}':msg.data||''};
  logger(hhmm()+str(6,r),'error'); //'ERROR: {DATA}
} // onError()


//        ===EVENTHANDLERS===

function onBeforeUnload() {
  logger(hhmm()+str(15)); // UNLOADING
  ws.close();
} // onBeforeUnload()

/** save the current text via form frmEdit in opener and end edit session too */
function doSave() {
  var r={'{LENGTH}':workText.length,'{LIMIT}':maxDocumentSize};
  if (workText.length>maxDocumentSize) {
    logger(hhmm()+str(21,r),'error'); // ERROR: document is too large ({LENGTH} characters), maximum is {LIMIT}
    if (beeps) beep.play();
    return; // Don't save, don't quit either.
  }
  var t=w.document.getElementById('txtText'); // use textarea t to submit new text
  var f=w.document.getElementById('frmEdit'); // via this form f
  var b=document.createElement('input'); // and save button b
  t.value=workText;
  b.type='text';
  b.name='button_save';
  b.value='SaveDummy';
  f.appendChild(b);

  var msg='M\t'+str(7,r); // SAVE ({LENGTH} characters)
  ws.send(msg);
  f.submit();
  window.close();
} // doSave()

/** save the current text via form frmEdit in opener and resume editing */
function doSaveEdit() {
  var r={'{LENGTH}':workText.length,'{LIMIT}':maxDocumentSize};
  if (workText.length>maxDocumentSize) {
    logger(hhmm()+str(21,r),'error'); // ERROR: document is too large ({LENGTH} characters), maximum is {LIMIT}
    if (beeps) beep.play();
    divEdit.focus();
    return; // Don't save
  }
  var t=w.document.getElementById('txtText'); // use textarea t to submit new text
  var f=w.document.getElementById('frmEdit'); // via this form f
  var b=document.createElement('input'); // and save button b
  t.value=workText;
  b.type='text';
  b.name='button_saveedit';
  b.value='SaveEditDummy';
  f.appendChild(b);
  var msg='M\t'+str(8,r); // SAVE ({LENGTH} characters) + EDIT
  ws.send(msg);
  f.submit();
  divEdit.focus();
} // doSaveEdit()

/** cancel the session and return to the opener window by closing this one */
function doCancel() {
  var f=w.document.getElementById('frmEdit'); // cancel the operation by submitting form f
  var b=document.createElement('input'); // using button b
  b.type='text';
  b.name='button_cancel';
  b.value='CancelDummy';
  f.appendChild(b);
  var r={'{LENGTH}':workText.length};
  var msg='M\t'+str(9,r); // CANCEL ({LENGTH} characters)
  f.submit();
  ws.send(msg);
  window.close();
} // doCancel()

/** request a fresh working copy of the current text
 *
 * Format: R
 *
 * Eventually this yields a T-reply with attributes and text
 */
function doRefresh() {
  var msg='R';
  ws.send(msg);
} // doRefresh()

/** broadcast a message to all workers in the shop
 *
 * Format: M | message
 *
 * Eventually this yields an M-reply from the server with this message.
 * Note that we reset the input field after sending this message.
 */
function doMessage() {
  var s=txtMessage.value.trim();
  if (s.length>0) {
    var msg='M\t'+s;
    ws.send(msg);
  }
  txtMessage.value='';
  txtMessage.focus();
} // doMessage()


/** toggle the sound on/off; tell user locally via logger
 */
function doSoundToggle() {
  beeps=!(beeps);
  btnSound.className=(beeps)?'soundon':'soundoff';
  logger(hhmm()+str((beeps)?11:10)); // SOUND ON or SOUND OFF
} // doSoundToggle()

/** broadcast a message after user enters [Enter]
 *
 * when the user presses [Enter] we send a message
 * just like the send button does (see doMessage())
 */
function onKeyPressMessage(e) {
  var key=(window.event) ? e.keyCode : e.which;
  if (key == 13) {
    doMessage();
  }
} // onKeyPressMessage()

/** add function f to list of functions to execute on load */
function onLoad(f) {
  if (window.addEventListener) {
    window.addEventListener('load', f, false);
  } else if (window.attachEvent) {
    window.attachEvent('load', f);
  }
} // onLoad()

//        ===CREW MESSAGE HANDLERS===

/** send authentication and maybe provide initial text (if we're the first to arrive)
 *
 * Format: A | nick | name | date | signature | text
 */
function sendAuthentication() {
  var t=w.document.getElementById('txtText');
  var r={'{NICK}':reqUserId,'{NAME}':reqUserName};
  logger(hhmm()+str(16,r)); // AUTHENTICATING: {NAME} ({NICK})
  s='A\t'+reqUserId+'\t'+reqUserName+'\t'+reqDate+'\t'+reqSignature+'\t'+t.value;
  ws.send(s);
} // sendAuthentication()


/** process an incoming message
 *
 * format: "M" | attr | message
 * where | indicates the delimiter (tab char)
 *
 * The attr parameter is a single letter 
 * 'A',...,'Z' which maps to one of the
 * color stylesin which to display the message.
 * If we wanted we could display messages in
 * the sender's color. However, we decided
 * against that because the userid is also
 * part of the message so we already know who's
 * talking. Therefore the color is '@'.
 */
function handleMessage(data) {
  var a=data.split('\t');
  if (a.length < 3) {
    var r={'{DATA}':data};
    logger(hhmm()+str(14,r),'error'); // ERROR: malformed message {DATA}
    return;
  }
  // logger(hhmm()+a[2], a[1]); // a[1]=user color
  logger(hhmm()+a[2], '@'); // @=system color
  if (beeps) beep.play();
} // handleMessage()


/** process incoming message about worker entering the shop
 *
 * Format: E | nick | name | attr
 *
 * We received information from the server that user name
 * with userid nick has entered the workshop.
 */
function handleEnter(data) {
  var a=data.split('\t');
  if (a.length < 4) {
    var r={'{DATA}':data};
    logger(hhmm()+str(14,r),'error'); // ERROR: malformed message {DATA}
    return;
  }
  var r={'{NICK}':a[1],'{NAME}':a[2],'{ATTR}':a[3]};
  // logger(hhmm()+str(12,r),a[3]); // a[3]=user color, '@'=system colour
  logger(hhmm()+str(12,r),'@'); // {NAME} ({NICK}) enters the workshop
  if (beeps) beep.play();
} // handleEnter()


/** process incoming message about worker leaving the shop
 *
 * Format: L | nick | name | attr
 *
 * We received information from the server that user name
 * with userid nick has left the workshop.
 *
 * Since this user is leaving, there is no point in
 * having the user's color around in the workAttr. We should
 * replace this user's color with the generic '@' to really
 * wipe all traces of this worker.
 */
function handleLeave(data) {
  var a=data.split('\t');
  if (a.length < 4) {
    var r={'{DATA}':data};
    logger(hhmm()+str(14,r),'error'); // ERROR: malformed message {DATA}
    return;
  }
  var r={'{NICK}':a[1],'{NAME}':a[2],'{ATTR}':a[3]};
  // logger(hhmm()+str(13,r),a[3]); // a[3]=user color, '@'=system colour
  logger(hhmm()+str(13,r),'@'); // {NAME} ({NICK}) leaves the workshop
  if (beeps) beep.play();
  var p=new RegExp(a[3],'g'); // replace this color with generic system color
  workAttr=workAttr.replace(p,'@');
  redrawWork();
} // handleLeave()


/** Workshop sends us information about us
 *
 * Format: "I" | attr | address | port
 */
function handleInfo(data) {
  var a=data.split('\t');
  if (a.length < 4) {
    var r={'{DATA}':data};
    logger(hhmm()+str(14,r),'error'); // ERROR: malformed message {DATA}
    return;
  }
  myColor=a[1];
  myAddress=a[2];
  myPort=a[3];
} // handleInfo()


/** process an incoming userlist
 *
 * Format: "U" | n | m 
 *             | nick1 | name1 | color1 | range1
 *             | nick2 | name2 | color2 | range2
 *             ...
 *             | nickn | namen | colorn | rangen
 *
 * where n identifies the number of users and m the
 * number of properties of a user (here: 4).
 *
 * Note that the 4th parameter per record (the 'range') is the
 * current cursor position of this user in the text. There are
 * more messages that update this value, specifically the
 * patch message 'P' also provides all positions of all current
 * users.
 *
 * The caret-position is actually a range that could be collapsed.
 * If it really is a range, it looks like a text string 'sss-eee'
 * with sss indicating the starting position and eee the ending
 * position. If both are the same (the range is collapsed) this
 * field contains just a single number indicating 'the' caret
 * position.
 *
 * Part of handling the userlist is to simply overwrite the current
 * userList and replacing it with this new version. The new version
 * of the list is rendered in the pane with worker names via
 * redrawUserList().
 */
function handleUserList(data) {
  var a=data.split('\t');
  if (a.length < 3) {
    var r={'{DATA}':data};
    logger(hhmm()+str(14,r),'error'); // ERROR: malformed message {DATA}
    return;
  }
  var n=parseInt(a[1],10);
  var m=parseInt(a[2],10);
  if (a.length != n * m + 3) {
    var r={'{DATA}':data};
    logger(hhmm()+str(14,r),'error'); // ERROR: malformed message {DATA}
    return;
  }
  userList=[];
  var k=3;
  for (var i=0; i<n; ++i) {
    userList[i]=[];
    for (var j=0; j<m; ++j) {
      userList[i][j]=a[k++];
    }
    if (userList[i][2]==myColor) {
      myRange=userList[i][3];
    }
  }
  redrawUserList();
} // handleUserList()


/** process an incoming full text + attributes
 *
 * Format: "T" | attributes | text
 * 
 * attributes is a string indicating the 'owner'
 * of a character in the text. These are plain
 * ascii characters, where '@' indicates the
 * system, 'A' the first real user in userList,
 * and so on upto 'Z'.
 * The length of attributes and text should
 * be equal: one ownership character for every
 * text character
 */
function handleTextRefresh(data) {
  var a=data.split('\t');
  if (a.length < 3) {
    var r={'{DATA}':data};
    logger(hhmm()+str(14,r),'error'); // ERROR: malformed message {DATA}
    return;
  }
  workAttr=a[1];
  workText=a[2];
  redrawWork();
} // handleTextRefresh()


/** process an incoming relocation (aka cursor movement)
 *
 * Format: R | range1 | ... | rangen
 */
function handleRelocate(data) {
    var a=data.split('\t');
    a.shift(); // lose the 'R'
    var n=a.length;
    if (n!=userList.length) {
      var r={'{N}':n,'{USERS}':userList.length};
      logger(hhmm()+str(17,r)); // INTERNAL ERROR: parameters n={N} and users={USERS} differ
      return;
    }
    for (var i=0;i<n;++i) {
      userList[i][3]=a[i];
      if (userList[i][2]==myColor) {
	myRange=a[i];
      }
    }
    redrawWork();
} // handleRelocate()

/** process an incoming patch
 *
 * Format: P | attr | offset | prelen | oldlen | newlen | postlen | text | range1 | ... | rangen
 */
function handlePatch(data) {
// debug('received patch: '+data);
// original string is in workText
// original attributes are in workAttr

//    var scrollTop=txtMaster.scrollTop;
    var a=data.split('\t');
    if (a.length < 8) {
      var r={'{N}':'8', '{COUNT}':a.length,'{DATA}':data};
      logger(hhmm()+str(18,r)); // INTERNAL ERROR: less than {N} patch parameters: {COUNT} ({DATA})
      return;
    }
    a.shift(); // lose the 'P'
    var attr=a.shift();
    if (attr==myColor) { // implementing 'local echo'
      debug('local echo in handlePatch');
      return;
    }
    var offset=Number(a.shift());
    var c0=Number(a.shift());
    var d0=Number(a.shift());
    var d1=Number(a.shift());
    var c1=Number(a.shift());
    var p1=a.shift();

    // at this point we have extracted the various offsets and lengths
    // the text in the patch is in p1 (could be '') and a[] contains
    // the ranges for all current workers in the natural order, ie the
    // order indicated by the userList array.

    var cold=workText.substr(offset,c0);
    var cnew=p1.substr(0,c0);
    if (cold!=cnew) {
      var r={'{OLD}':cold,'{NEW}':cnew,'{N}':'0'};
      logger(hhmm()+str(19,r)); // INTERNAL ERROR: context {N} missing: {OLD} {NEW}'
      return;
    }
    cold=workText.substr(offset+c0+d0,c1);
    cnew=p1.substr(c0+d1,c1);
    if (cold!=cnew) {
      var r={'{OLD}':cold,'{NEW}':cnew,'{N}':'1'};
      logger(hhmm()+str(19,r)); // INTERNAL ERROR: context {N} missing: {OLD} {NEW}'
      return;
    }
    workText=workText.substring(0,offset)+p1+workText.substring(offset+c0+d0+c1);
    workAttr=workAttr.substring(0,offset+c0)+str_repeat(attr,d1)+workAttr.substring(offset+c0+d0);

    var n=userList.length;
    if (n!=a.length) {
      var r={'{N}':n,'{USERS}':a.length};
      logger(hhmm()+str(20,r)); // INTERNAL ERROR: patch n={N} and users={USERS}
      return;
    }
    for (var i=0; i<n; ++i) {
      userList[i][3]=a[i];
      if (userList[i][2]==myColor) {
	myRange=a[i];
      }
    }
//    debug('Patch: success ("'+p1+'")');
//    txtMaster.scrollTop=scrollTop;
    redrawWork();
} // handlePatch()

/** make a diff of divEdit and workText and send to server
 *
 * we are about to create a message as follows:
 *
 * D | range | offset | prelength | oldlength | newlength | postlength | "text"
 *
 * or
 *
 * D | range
 *
 * where
 *
 * range is our current cursor position/selected range ("sss-eee" or "ccc") UTF-units
 * offset is the offset to the first utf8 character of the pre-context
 * prelength is the # of utf8 characters in the pre-context
 * oldlength is the old # of utf8 characters between pre-context and post-context
 * newlength is the new # of utf8 characters between pre-context and post-context
 * postlength is the # of utf8 characters in the post-conext
 * text is the new text that should go between pre-context and post-context
 *
 * In the case the only difference is a move of the cursor, the shorter form is sent
 * to the server.
 *
 */
function doDiff() {
// ***** countCharacters(divEdit,'doDiff 1/2');
  divEdit.normalize();
// ***** countCharacters(divEdit,'doDiff 2/2');
  var currentWork=currentWorkText(divEdit);
  var currentRange=currentWork.range();
  var s0=workText;		// bare old string
  var s1=currentWork.value();	// bare new string with BR->\n
  var i =0;			// length of common prefix
  var j =0;			// length of common postfix
  var n0=s0.length;		// length of the original string
  var n1=s1.length;		// length of the new string
  var m =Math.min(n0,n1);	// length of the shortest string
  var k0=0;
  var k1=0;

  // calculate length i of common prefix (could be 0)
  for (i=0; ((s0[i]==s1[i]) && (i<m)); ) {
    ++i;
  }
  if ((i==m) && (n0==n1)) { // s0 and s1 are equal; no diff in text, but maybe a range change?
    if (myRange!=currentRange) {
      ws.send('D\t'+currentWork.urange()); // talk to server using UTF-units, not JS-units
      //debug('schedule(cursor):'+myRange+' ? '+currentRange);
      myRange=currentRange;
      for (i=0; i<userList.length; ++i) {
	if (userList[i][2]==myColor) {
	  userList[i][3]=myRange;
	  break;
	}
      }
//debug('U:D\t'+currentWork.urange());
//debug('J:D\t'+currentWork.range());
      }
    scheduleRedraw();
//debug('diff return TRUE');
    return true;
  }
//debug('diff at i='+i);
  // calculate length j of common postfix (could be 0)
  for (j=0, k0=n0, k1=n1; ((j + i < m) && (s0[--k0] == s1[--k1])); ) {
    ++j;
  }

  // j is the postfix length, could be 0
//debug('prefix='+i+', postfix='+j);

  // at this point we know we have i chars in common at the beginning
  // and j chars at the end of the two strings. The changed data starts
  // at offset i: the old data for n0-i-j chars, the new data for n1-i-j chars.
  // We send only the new data (+context) and we rely on the context matching
  // exactly at the receiving end. The diff looks like this:
  // "D" | range | offset | c0 | d0 | d1 | c1 | "text"
  // where
  // "D" is the literal letter D
  // range indicates our current caret position/selected range
  // offset indicates where the patch starts within s0/s1
  // c0 is the context length before the changes
  // d0 is the length of the changed part in the old string
  // d1 is the length of the changed part in the new string
  // c1 is the context length after the changes
  // "text" is the combination of precontext + new text + postcontext
  // Note that all offsets are in JS-units. We send UTF-units to the server,
  // so we have to convert (get rid of surrogate pairs).

  var c0=Math.min(i,maxContextLength);
  var d0=n0-i-j;
  var d1=n1-i-j;
  var c1=Math.min(j,maxContextLength);
  var offset=i-c0;

  // now we need to take care of not accidently splitting surrogate pairs
  if ((s0.charCodeAt(offset) & 0xDC00)==0xDC00) { // prefix starts with surrogate trail
    --offset;
    ++c0;
  }
  if ((s0.charCodeAt(offset+c0) & 0xDC00)==0xDC00) { // string starts with surrogate trail
    --c0;
    ++d0;
    ++d1;
  }
  if ((s0.charCodeAt(offset+c0+d0) & 0xDC00)==0xDC00) { // postfix starts with surrogate trail
    --c1;
    ++d0;
    ++d1;
  }
  var uoffset=offset;
  var uc0=c0;
  var ud0=d0;
  var ud1=d1;
  var uc1=c1;
  var n=offset+c0+d0+c1;
  for (i=0; i<n; ++i) {
    if ((s0.charCodeAt(i) & 0xDC00)==0xDC00) {
      if (i<offset)
	--uoffset;
      else if (i<offset+c0)
	--uc0;
      else if (i<offset+c0+d0)
	--ud0;
      else
	--uc1;
    }
  }
  for(i=offset+c0,n=offset+c0+d1; i<n; ++i) {
    if ((s1.charCodeAt(i) & 0xDC00)==0xDC00) {
      --ud1;
    }
  }
  var msg='D\t'+currentWork.urange()+'\t'+uoffset+'\t'+uc0+'\t'+ud0+
    '\t'+ud1+'\t'+uc1+'\t'+s1.substring(offset,offset+c0+d1+c1);
  
  ws.send(msg);
//  debug('U:'+msg);
//  debug('J:'+'D\t'+currentWork.range()+'\t'+offset+'\t'+c0+'\t'+d0+
//    '\t'+d1+'\t'+c1+'\t'+s1.substring(offset,offset+c0+d1+c1));

  // local patch implementing 'local echo'
  var pivot=offset+c0; // from here there are changes
  var delta=d1-d0; // the change in length and so also the movement of other carets beyond the pivot point
  workText=workText.substring(0,offset)+
    s1.substring(offset,offset+c0+d1+c1)+
    workText.substring(offset+c0+d0+c1);
  workAttr=workAttr.substring(0,pivot)+
    str_repeat(myColor,d1)+
    workAttr.substring(pivot+d0);

//debug('schedule(diff):'+myRange+' ? '+currentRange);
  myRange=currentRange;

  var r;
  for (i=0; i<userList.length; ++i) { // FixMe: we should re-implement the userList information so we
    if (userList[i][2]==myColor) { // really only use integers instead of the messy mix of strings/ints
      userList[i][3]=myRange;
    } else if (delta) {
      r=String(userList[i][3]).split('-');	// sss-eee or ccc 
      if (r.length<2) {		// ccc, maybe correct cursor position
	if (r[0]>=pivot) userList[i][3]=Number(r[0])+delta;
      } else {
	if (r[0]>=pivot) r[0]=Number(r[0])+delta;
	if (r[1]>=pivot) r[1]=Number(r[1])+delta;
	userList[i][3]=r[0]+'-'+r[1];
      }
      // debug('localecho['+i+']='+userList[i][3]);
    }
  }
  scheduleRedraw();

//debug(workText);
//debug(workAttr);
//debug('diff return FALSE=>TRUE');
  return true;
} // doDiff()


//        ===HELPER ROUTINES===

/** refresh the content of the Members div and list all current users
 *
 * - show the word 'CREW' with "Cooperative Remote Educational Workshop v.r.p (yyyy-mm-dd)" in title
 * - show (short) workshop name in h3 with full origin+name in title
 * - show our own name in h3 (title=our IPaddress/port/attr)
 * - show all users with their own attr, e.g.:
 *   Freddie Frinton<br>(ffrint) with
 *   '(ffrint)' in reverse color (class='AB')
 */
function redrawUserList() {
  var p; // generic helper
  var s; // span element
  var c; // class helper

  // 0--clean up existing pane
  while (divMembers.childNodes.length > 0) {
    divMembers.removeChild(divMembers.firstChild);
  }

  // 1--CREW in various different colors at the top
  p=document.createElement('H3');
  p.title=htmlSpecialChars(crewName+' v'+crewVersion+' ('+crewDate+')');
  for (var i=0; i<crew.length; ++i) {
    s=document.createElement('SPAN');
    s.className=String.fromCharCode(65+i);
    s.appendChild(document.createTextNode(crew[i]));
    p.appendChild(s);
  }
  divMembers.appendChild(p);

  // 2--Workshop name
  p=document.createElement('h3');
  p.style.wordwrap='break-word';
  p.title=htmlSpecialChars(reqOrigin+reqWorkshop);
  p.innerHTML=reqShop;
  divMembers.appendChild(p);

  // 3--Full Name of this member
  p=document.createElement('h3');
  p.style.wordwrap='break-word';
  p.title=htmlSpecialChars('IP='+myAddress+' port='+myPort+' color='+myColor);
  p.innerHTML=htmlSpecialChars(reqUserName);
  p.className=myColor;
  divMembers.appendChild(p);

  // 4--List of all members
  for (var i=0; i<userList.length; ++i) {
    c=userList[i][2]+'B';
    // Full Name + userid + color in mouse over
    p=document.createElement('P');
    p.title=htmlSpecialChars(userList[i][1]+' ('+userList[i][0]+') '+userList[i][2]);
    // Full Name in default color
    p.appendChild(document.createTextNode(userList[i][1]));
    p.appendChild(document.createElement('BR'));
    // '(userid)' with user's background color
    s=document.createElement('SPAN');
    s.className=c;
    s.appendChild(document.createTextNode('('+userList[i][0]+')'));
    // Done, add to list
    p.appendChild(s);
    divMembers.appendChild(p);
  }
} // redrawUserList()

/** write a colored message to the list of messages
 *
 * this writes nessage s to the logger div.
 * either at the top or at the bottom (see b below).
 * we keep at most 120 messages in the list (they
 * fall off at the end). The attr parameter a
 * contains either a '@' implying no coloring at all,
 * an rgb-triplet, eg. #FF0000 for red OR the name
 * of a class that can be applied to the P that is
 * added. All users have their own color A,...,Z
 * and an appropriate class can be defined in the
 * css-file to manipulate the colors.
 * 
 * @param string s
 * @param string a attribute (color) code or #rgb triplet (default '@')
 * @return void msg written to list, list pruned
 */
function logger(s, a) {
  a=a || '@';
  var p=document.createElement('p');
  p.style.wordWrap='break-word';
  if (a[0]=='#') {
    p.style.color=a;
  } else if (a[0]!='@') {
    p.className=a;
  }
  p.appendChild(document.createTextNode(s));
  if (logReverse) { // insert msg at the top
    while (divMessages.childNodes.length > 120) {
      divMessages.removeChild(divMessages.lastChild);
    }
    divMessages.insertBefore(p,divMessages.firstChild);
  } else { // append msg at bottom+scroll into view
    divMessages.appendChild(p);
    divMessages.scrollTop=divMessages.scrollHeight;
  }
} // logger()


/** schedule or re-schedule a redrawWork() in the near future */
function scheduleRedraw() {
  if (timeRedraw) { // cancel the wait for redrawWork because we're redrawing already
    clearTimeout(timeRedraw);
//debug('clearTimeout');
  }
  timeRedraw=setTimeout(scheduleRedrawAction,1000);
//debug('setTimeout(1000)');
} // scheduleRedraw()

function scheduleRedrawAction() {
//debug('timeout fired - begin redraw');
  redrawWork();
//debug('timeout fired - end redraw');
}

function redrawWork() {
  if (timeRedraw) { // cancel the wait for redrawWork because we're redrawing already
    clearTimeout(timeRedraw);
    timeRedraw=null;
//debug('clearTimeout/null');
  }
// ***** countCharacters(divEdit,'startRedraw');
//debug('skip redrawWork()');
//return;
  divEdit.innerHTML=''; // FixMe: should we do a removeChild() walk?
  var last=workAttr[0];
  var t='';
  var c; // current character/shorthand for workText[i]
  var span;
  var txt;
  var br;
  var caret;
  var caretxt;
  var sel=window.getSelection();
  sel.removeAllRanges();
  var range=document.createRange();
  var r=myRange.split('-');	// sss-eee or ccc 
  if (r.length<2) {		// ccc, make a 'collapsed range' ccc-ccc
    r[1]=r[0];
  }
//debug('myRange: '+myRange+' r[]='+r.join());
  var t=''; // buffer used for building text nodes
  var i=0; // index in workText and workAttr
  var p; // helper for position of caret
  var n; // helper used as a limit for i
  var k=0; // index in carets array
  var carets=getCarets(); // [ caret,"attr","title"] ... [-1,"@",""]
  var last=workAttr[0];
  var span=document.createElement('SPAN');
  span.className=last;
  for (var k=0; k<carets.length; ++k) {
debug('carets['+k+']=['+carets[k][0]+','+carets[k][1]+','+carets[k][2]+']');
    // 1 -- process plain text upto the next caret position or end of text
    for (n=(carets[k][0] == -1) ? workText.length : carets[k][0]; i<n; ++i) {
      c=workText[i];
      if (workAttr[i] != last) {
	if (t.length > 0) {
	  txt=document.createTextNode(t);
	  span.appendChild(txt);
	  t='';
	}
	divEdit.appendChild(span);
	last=workAttr[i];
	span=document.createElement('SPAN');
	span.className=last;
      }
      if (c == '\n') {
        if (t.length > 0) {
          txt=document.createTextNode(t);
          span.appendChild(txt);
          t='';
        }
        br=document.createElement('BR');
        span.appendChild(br);
      } else {
        t += c;
      }
    }
    // 2 -- process caret
    if (i==carets[k][0]) {
      caret=document.createElement('SPAN');
      caret.className=carets[k][1]+'B';
      caret.title=carets[k][2];
      // at EOF we add an extra visibleCaret, much like at EOL
      c=(i>=workText.length) ? visibleCaret : workText[i];
      p=i; // remember index of the start of the caret (surrogates mess up value of i below)
      if (c == '\n') {
	caretxt=document.createTextNode(visibleCaret); // make caret show
	caret.appendChild(caretxt);
	br=document.createElement('BR');
	caret.appendChild(br);
      } else {
	if ((c.charCodeAt(0) & 0xDC00) == 0xD800) { // c is a surrogate lead
	  c += workText[++i]; // add surrogate trail to cursor and bump pointer
	  //debug('surrogate pair: '+c.charCodeAt(0).toString(16)+' '+c.charCodeAt(1).toString(16));
	}
	caretxt=document.createTextNode(c);
	caret.appendChild(caretxt);
      }
      // finish the pending text node and maybe set start/end of our range at the END of the text
      if (t.length > 0) {
        txt=document.createTextNode(t);
        span.appendChild(txt);
	if (p==r[1]) range.setEnd(txt,t.length);
	if (p==r[0]) range.setStart(txt,t.length);
        t='';
      } else {
	// there was no previous text, so we resort to maybe putting the range in the coloured block cursor
	if (p==r[1]) range.setEnd(caretxt,0);
	if (p==r[0]) range.setStart(caretxt,0);
      }
      span.appendChild(caret);
      ++i;

      // This is really tricky business. I could not seem to get this
      // right: we now set the insertion point/range inside the coloured
      // block that identifies the text cursor. If the range start/end
      // were set between two elements, ie. container was the parent of
      // the span that is contained in caret, the user had to tap the
      // cursor left twice after a redrawWork before it would move.
      // Illustration of the variants I tried based on this HTML-snippet:
      // <SPAN>qwe<SPAN>r</SPAN>ty</SPAN>
      // with p==3, ie. the caret (shown as '|') between the 'e' and the 'r'.
      //
      // Eventually I went with the logic as follows: if there was pending
      // text, we set the insertion point at the end of that text, eg.
      // <SPAN>qwe|<SPAN>r</SPAN>ty</SPAN>. However, if there was no text
      // we still added the insertion point in the coloured block cursor like so:
      // <SPAN>qwe<SPAN>r</SPAN>ty</SPAN>
      //
      // *sigh*

      // <SPAN>qwe<SPAN>|r</SPAN>ty</SPAN> // offset 0 in the #text("r")
      //      if (p==r[1]) range.setEnd(caretxt,0);
      //      if (p==r[0]) range.setStart(caretxt,0);

      // <SPAN>qwe<SPAN>|r</SPAN>ty</SPAN> // offset 0 in the 2nd SPAN
      //      if (p==r[1]) range.setEnd(caret,0);
      //      if (p==r[0]) range.setStart(caret,0);

      // <SPAN>qwe|<SPAN>r</SPAN>ty</SPAN> // offset 1 in the 1st SPAN
      //      if (p==r[1]) range.setEnd(span,span.childNodes.length-1);
      //      if (p==r[0]) range.setStart(span,span.childNodes.length-1);

      // <SPAN>qwe|<SPAN>r</SPAN>ty</SPAN> // offset 1 in the 1st SPAN
      //      if (p==r[1]) range.setEndBefore(caret);
      //      if (p==r[0]) range.setStartBefore(caret);

    }
  }
  if (t.length > 0) {
    txt=document.createTextNode(t);
    span.appendChild(txt);
  }
  divEdit.appendChild(span);
  sel.addRange(range);
  //debug("==redrawWork==");
// ***** countCharacters(divEdit,'doneRedraw');
} // redrawWork()

/** construct information about all carets; collapsing equivalent positions to one entry
 *
 * the 2-D userList array holds n entries: [ "nick", "name", "attr", caret ]
 * we construct a sorted array with caret, attr and nick followed by an ending entry.
 * Example:
 * a[0]=[ caret1, "attr1", "nick1" ]
 * a[1]=[ caret2, "attr3", "nick2, nick3" ]
 * a[2]=[ -1, "@", "" ]
 * Here caret2 and caret3 are the same and the 3rd element is a concatenation of the nick's.
 * The last entry with impossible position -1 is used as a stop criterion in the routine
 * that renders the work area.
 *
 * If we have a range, ie. a position like sss-eee and it is our own range,
 * we add both points to the array. We don't do that for all the other carets.
 * This allows for maintaining our own selection between redraws.
 */
function getCarets() {
  var a=[];
  var n=userList.length;
  var r;
  for (var i=0; i<n; ++i) {
    r=String(userList[i][3]).split('-');
    if (r.length<2) {		// ccc, make a 'collapsed range' ccc-ccc
      r[1]=r[0];
    }
    if ((userList[i][2]==myColor) && (r[0]!=r[1])) { // add our own range via separate entry
      a.push([ r[0], userList[i][2], userList[i][0] ]);
    }
    a.push([ r[1], userList[i][2], userList[i][0] ]); // caret, attr, nick
  }
  if ((n=a.length) > 1) { // anything to sort at all?
    a.sort(function(p,q) { // yes. order by position and add ourselves at the bottom
      var d=p[0]-q[0];
      if (d==0) {
	if (q[1]==myColor) d=-1; else 
	  if (p[1]==myColor) d= 1; else d=0;
      }
      return d;
    });
    var b=[];
    var j=0;
    b[0]=a[0];
    for (i=1; i<n; ++i) {
      if (b[j][0]==a[i][0]) { // two entries with the same position; copy attr and append title
        b[j][1]=a[i][1];
        b[j][2]+=', '+a[i][2];
      } else {
	b[++j]=a[i];
      }
    }
    a=b;
  }
  a.push([-1,'@','']);
  return a;
} // getCarets()

/** analyse the current version of workText at node with carets and all
 *
 * this function analyses node by walking the tree and
 * extracts/calculates interesting and useful information
 * such as caret position and the bare string representation
 * of the text in node.
 *
 * usage: var currentText=currentWorkText(divEdit)
 * int currentText.caret() => integer value indicating the current caret position
 * string currentText.value() => plaintext with BR->'\n'
 * string currentText.range() => sss-eee with sss=start and eee=end OR ccc if collapsed
 * string currentText.urange() => like range but with UTF-units (no surrogate pairs)
 * int currentText.ucaret() => like caret but with UTF-units (no surrogate pairs)
 */
function currentWorkText(node) {
  //debug(hhmm()+'===entering aWorkText===');
  var s; // selection object
  var i; // loop counter
  var r; // range object
  var g; // helper to determine if range falls completely within node
  var rsc=node; // range start container
  var rso=0;    // range start offset
  var rec=node; // range end container
  var reo=0;    // range end offset
  var rc=true;  // range is collapsed
  if (window.getSelection) { // Firefox
    s=window.getSelection();
    if (s.rangeCount) {
      for (i=0; i<s.rangeCount; ++i) {
	r=s.getRangeAt(i);
	if (isDescendant(node,r.commonAncestorContainer)) {
	  rsc=r.startContainer;
	  rso=r.startOffset;
	  rec=r.endContainer;
	  reo=r.endOffset;
	  rc=r.collapsed;
	} else debug('commonAncestorContainer outside node; continue');
      } // for
    } else debug('no ranges at all');
  } else { // IE
    logger('FixMe: window.getSelection() undefined','error'); // FixMe
  }
  // At this point we have one or two positions as [node,offset] pairs

  var c0=0; // first selection point
  var c1=0; // last selection point (could be the same if collapsed)
  var t=''; // used to build the plain text string
  var uc0=0; // first selection point in UTF-units
  var uc1=0; // last selection point in UTF-units;

  var vc=new RegExp(visibleCaret,'g'); // used for eating visibleCarets

  function f(n) {
    var q; // node helper
    var h; // hit in regexp.exec()
    var i;
    if ((rsc==n) || (rec==n)) { // special case: calculate at least 1 offset within this node n
      if (n.nodeType==3) {
	if (n==rsc) { c0=t.length+rso; } // assume we do NOT have a visibleCaret in n.nodeValue
	if (n==rec) { c1=t.length+reo; } // but correct afterwards in the while loop below if we do
	vc.lastIndex=0;
	while ((h=vc.exec(n.nodeValue))!=null) {
	  if ((n==rsc) && (h.index<rso)) { --c0; } // adjust positions because of visibleCaret
	  if ((n==rec) && (h.index<reo)) { --c1; } // that is going to be deleted
	}
	t += n.nodeValue.replace(vc,'');
      } else {
	if ((rso==0) && (rsc==n)) c0=t.length;
	if ((reo==0) && (rec==n)) c1=t.length;
	for (q=n.firstChild,i=1; (q!=null); q=q.nextSibling,++i) {
	  f(q);
	  if ((rso==i) && (rsc==n)) c0=t.length;
	  if ((reo==i) && (rec==n)) c1=t.length;
	}
      }
    } else { // ezpz: no complicated offset calculations
      if (n.nodeType==3) {
	t+=n.nodeValue.replace(vc,''); // eat any visibleCarets
      } else if (n.nodeName=='BR') { // a BR cannot have children (is a void element)
	t+='\n';
      } else {
      	for (q=n.firstChild; (q!=null); q=q.nextSibling) f(q);
      }
    }
  }

  f(node);
  c0=Math.min(c0,t.length);
  c1=Math.min(c1,t.length);
  // calculate corresponding UTF-units / get rid of surrogate pair double count
  uc0=Math.min(c0,c1);
  uc1=c1=Math.max(c0,c1);
  c0=uc0;
  // we now are certain 0 <= c0 <= c1 and also uc0==c0 and uc1==c1
  for (i=0; i<c0; ++i) {
    if ((t.charCodeAt(i) & 0xDC00)==0xDC00) {
      --uc0;
      --uc1;
    }
  }
  for ( ; i<c1; ++i) {
    if ((t.charCodeAt(i) & 0xDC00)==0xDC00) {
      --uc1;
    }
  }
  return {
    value: function() { return t; },
    caret: function() { return c1; },
    range: function() { return (c0==c1) ? c0.toString() : c0+'-'+c1; },
   urange: function() { return (uc0==uc1) ? uc0.toString() : uc0+'-'+uc1; },
   ucaret: function() { return uc1; }
  }
} // currentWorkText()

//        ===UTILITY ROUTINES===

/** find out if node n descends from ancestor a
 *
 * @param object a ancestor
 * @param object n node
 * @retval bool TRUE if node is a descendant of a
 */
function isDescendant(a,n) {
  for ( ; n != null; n=n.parentNode) {
    if (a == n)
      return true;    
  }
  return false;
} // isDescendant()


/** utility routine to show the current hour and minute as 'hh:mm ' including trailing space
 *
 */
function hhmm() {
  var d=new Date;
  var h=d.getHours();
  var m=d.getMinutes();
  return ((h<10)?'0'+h:h)+':'+((m<10)?'0'+m:m)+' ';
} // hhmm()

/** escape some HTML-characters
 *
 * utility routine escapes HTML-characters much the
 * same as the PHP-function with a similar name.
 * Characters '&' '<' '>' and '"' are converted to
 * their corresponding entities.
 *
 * @param string s
 * @return string escaped string s
 */
function htmlSpecialChars(s) { 
  return s.replace(/&/g,'&amp;'). 
           replace(/</g,'&lt;').
           replace(/>/g,'&gt;').
           replace(/"/g,'&quot;');
  return s.replace(/"/g,'&quot;'); // FixMe: stupid line to match the " in the previous
} // htmlSpecialChars()


/** utility routine to generate repeated strings
 *
 */
function str_repeat(s,n) {
  var t='';
  while (n>0) {
    if (n&1) t+=s;
    n>>=1;
    s+=s;
  }
  return t;
} // str_repeat()

/* relic of the past
// Based on countCharacters() (Flanagan 4th edition p287)
function countCharacters(node,txt) {
  var level=0;

  var rsc=node; // range start container
  var rso=0;    // range start offset
  var rec=node; // range end container
  var reo=0;    // range end offset
  var rc=true;  // range collapsed
  var sel;      // selection
  var r;        // range
  var i;        // index
  if (window.getSelection) { // Firefox
    sel=window.getSelection();
    if (sel.rangeCount) {
      for (i=0; i<sel.rangeCount; ++i) {
	r=sel.getRangeAt(i);
	if (isDescendant(node,r.commonAncestorContainer)) {
	  rsc=r.startContainer;
	  rso=r.startOffset;
	  rec=r.endContainer;
	  reo=r.endOffset;
	  rc=r.collapsed;
	} else debug('commonAncestor outside node; continue');
      } // for
    } else debug('no ranges at all');
  } else { // IE
    logger('FixMe: window.getSelection() undefined','error'); // FixMe
  }

  function f(n) {
    var x='';
    if (n==rsc) x+=' (rso='+rso+')';
    if (n==rec) x+=' (reo='+reo+')';
    if (n.nodeType==3) {
      var len=n.length;
      var val=n.nodeValue;
      var hex='';
      for (var i=0; i<len; ++i) {
	if ((n==rsc)&&(i==rso))      hex+=(rc)?'<>':'<';
	else if ((n==rec)&&(i==reo)) hex+='>';
	else                         hex+=' ';
        hex+=val.charCodeAt(i).toString(16);
      }
      if ((n==rsc)&&(len==rso))       hex+=(rc)?'<>':'<';
      else if ((n==rec)&&(len==reo)) hex+='>';
      debug(level+str_repeat(' .',level)+' t=3: +'+len+' "'+val+'" hex:'+hex+x);
      return len;
    } else if (n.nodeName=="BR") {
      debug(level+str_repeat(' .',level)+' t='+n.nodeType+': +1 nodeName="'+n.nodeName+'"'+x);
      return 1;
    }
    debug(level+str_repeat(' .',level)+' t='+n.nodeType+' nodeName="'+n.nodeName+'"'+x);
    var num=0;
    ++level;
    for(var c=n.firstChild; c!=null; c=c.nextSibling) {
      num += f(c);
    }
    --level;
    if (level==0) {
     debug(level+' \u2211='+num);
     debug('doneCountCharacters:'+txt);
    }
    return num;
  } // f()
  debug('startCountCharacters:'+txt);
  return f(node);
} // countCharacters()
*/

/** construct a translated string optionally with parameters
 *
 * this returns the i'th string in the global list of translations in tr[].
 * If r is specified it is supposed to be an associative array with 1 or
 * more properties.
 * Example:
 *
 * tr[17]='{USER} has left the building';
 * r={'{USER}':'Elvis'};
 * s=str(17,r); // s='Elvis has left the building'
 *
 * @param int i identifies the message
 * @param object r is an optional associative array with parameters
 * @return string translated string with optional parameters
 * @uses tr[]
 */
function str(i,r) {
  var s=tr[i]||'?['+i+']?';
  if (r) { // if passed we use key-value pairs to substitute inside string s
    var re;
    for (k in r) {
      re=new RegExp(k,'g');
      s=s.replace(re,r[k]);
    }
  }
  return s;
} // str()

/* eof crew.js */
