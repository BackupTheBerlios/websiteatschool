#!/usr/bin/php -q
<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker <peter@berestijn.nl>
#
# This program is free software: you can redistribute it and/or modify it under
# the terms of the GNU Affero General Public License version 3 as published by
# the Free Software Foundation supplemented with the Additional Terms, as set
# forth in the License Agreement for Website@School (see license.html and about.html).
#
# This program is distributed in the hope that it will be useful, but
# WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
# FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License
# for more details.
#
# You should have received a copy of the License Agreement for Website@School
# along with this program. If not, see http://websiteatschool.eu/license.html
#
# $Id: crewserver.php,v 1.2 2013/06/12 13:06:35 pfokker Exp $
#
(PHP_SAPI==='cli') or die('access denied');
define('CREW_SERVER_VERSION','0.90.5');
define('CREW_SERVER_DATE','2013-06-12');
define('CREW_SERVER_NAME','CREW-server');
initialise();
main();
exit;
function initialise() {
  global $ORIGINS;
  global $SERVER_ADDRESS, $SERVER_PORT, $LOG2SYSLOG, $MARK_TIME,$MAX_DELTA, $DEBUG, $SERVER_SOURCE;
  define('WLOG_EMERG',0);
  define('WLOG_ALERT',1);
  define('WLOG_CRIT',2);
  define('WLOG_ERR',3);
  define('WLOG_WARNING',4);
  define('WLOG_NOTICE',5);
  define('WLOG_INFO',6);
  define('WLOG_DEBUG',7);
  $SERVER_ADDRESS = '0.0.0.0';
  $SERVER_PORT = 8008;
  $LOG2SYSLOG = NULL;
  $MARK_TIME = 900;
  $MAX_DELTA = 120;
  $DEBUG = WLOG_INFO;
  $ORIGINS = array();
  set_time_limit(0);
  error_reporting(E_ALL);
  define('PROG', 'crewserver');
  define('CREW_SERVER_PROTOCOL', 'crew.websiteatschool.eu');
  define('WEBSOCKETS_UUID',   '258EAFA5-E914-47DA-95CA-C5AB0DC85B11');
  if (!defined('MSG_DONTWAIT')) { define('MSG_DONTWAIT',0x40); }
  define('MAX_RECV_BUFFER', 2048);
  define('WASENTRY',__FILE__);
  require_once(dirname(WASENTRY).'/utf8lib.php');
  require_once(dirname(WASENTRY).'/zip.class.php');
  $SERVER_SOURCE = agplv3_compliance();
}
#
# MAIN PROGRAM
#
function main() {
  global $ORIGINS;
  global $SERVER_ADDRESS, $SERVER_PORT, $LOG2SYSLOG, $MARK_TIME,$MAX_DELTA, $DEBUG;
  logger('starting server: '.CREW_SERVER_NAME.' '.CREW_SERVER_VERSION);
  logger('This program is free software: you can redistribute it and/or modify it under');
  logger('the terms of the GNU Affero General Public License version 3 as published by');
  logger('the Free Software Foundation supplemented with the Additional Terms, as set');
  logger('forth in the License Agreement for Website@School (see license.html and about.html).');
  if (!read_config(TRUE)) {
    if (is_null($LOG2SYSLOG)) $LOG2SYSLOG=FALSE;
    logger('error: cannot retrieve origins. Does the configuration file even exist?',WLOG_ERR);
    exit(3);
  }
  if (is_null($LOG2SYSLOG)) $LOG2SYSLOG=TRUE;
  logger(sprintf('server address  = %s',$SERVER_ADDRESS));
  logger(sprintf('server port     = %d',$SERVER_PORT));
  logger(sprintf('log destination = %s',($LOG2SYSLOG)?'syslog':'stderr'));
  logger(sprintf('debug-level     = %d',$DEBUG));
  logger(sprintf('maximum delta   = %d s',$MAX_DELTA));
  logger(sprintf('mark-interval   = %d s',$MARK_TIME));
  if (sizeof($ORIGINS) < 1) {
    logger('warning: there are currently no origins defined in the configuration file',WLOG_WARNING);
    logger(sprintf("please add one or more origins to '%s.conf', e.g.",basename(__FILE__)),WLOG_WARNING);
    logger("origin = url, pass, workhops, workers",WLOG_WARNING);
    logger("where",WLOG_WARNING);
    logger("url       origin, e.g. 'http://exemplum.eu'",WLOG_WARNING);
    logger("pass      plain text password (no spaces or commas allowed)",WLOG_WARNING);
    logger("workshops is the maximum # of workshops for this origin (default 1)",WLOG_WARNING);
    logger("workers   is the maximum # of simultaneous workers per workshop (default 4)",WLOG_WARNING);
    logger("note 1: empty lines and lines starting with a '#' are ignored",WLOG_WARNING);
    logger("note 2: multiple origin lines are allowed",WLOG_WARNING);
    logger("got to go now, byebye...",WLOG_WARNING);
    exit(4);
  }
  logger(sprintf('currently serving %d origin(s)',sizeof($ORIGINS)));
  $i = 0; foreach($ORIGINS as $k => $v) {
    logger(sprintf("origin[%d] = '%s'",++$i,$k),WLOG_DEBUG);
  }
  logger(sprintf('logmessages go to %s',($LOG2SYSLOG) ? 'syslog' : 'stderr'));
  $server = new CrewServer($SERVER_ADDRESS, $SERVER_PORT);
  $retval = $server->run();
  logger('shutting down server: '.CREW_SERVER_NAME.' '.CREW_SERVER_VERSION);
  closelog();
  exit($retval);
}
#
# CrewServer
#
class CrewServer {
  var $server_address;
  var $server_port;
  var $server_socket;
  var $server_run_flag = FALSE;
  var $sockets = array();
  var $dirty_sockets = array();
  var $mark_time;
  var $mark_next = 0;
  var $clients = array();
  var $workshops = array();
  var $cids = 0;
  var $wids = 0;
  function CrewServer($server_address='0.0.0.0', $server_port=8008) {
    global $MARK_TIME;
    $this->server_address = $server_address;
    $this->server_port = $server_port;
    $this->mark_next = time() + $MARK_TIME;
  }
  function initialise() {
    $msg = sprintf("initialising socket for listening %s:%d",$this->server_address,$this->server_port);
    logger($msg,WLOG_DEBUG);
    if (($this->server_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === FALSE) {
      logger("cannot create socket: ".sockerr(), WLOG_ERR);
      return(FALSE);
    }
    if (socket_set_option($this->server_socket, SOL_SOCKET, SO_REUSEADDR, 1) === FALSE) {
      logger("cannot set socket option: ".sockerr(), WLOG_ERR);
      return(FALSE);
    }
    if (socket_bind($this->server_socket, $this->server_address, $this->server_port) === FALSE) {
      logger("cannot bind socket: ".sockerr($this->server_socket), WLOG_ERR);
      return(FALSE);
    }
    if (socket_listen($this->server_socket,17) === FALSE) {
      logger("cannot listen on socket: ".sockerr($this->server_socket), WLOG_ERR);
      return(FALSE);
    }
    $this->sockets = array();
    $key = $this->cids++;
    $this->sockets[$key] = $this->server_socket;
    logger(sprintf('%s: success (id=#%d)',$msg,$key));
    return TRUE;
  }
  function run() {
    $retval = 0;
    if ($this->initialise() === FALSE) {
      logger("FATAL: cannot initialise; bailing out", WLOG_ERR);
      $retval = 2;
      return $retval;
    }
    $this->server_run_flag = TRUE;
    logger('waiting for connections');
    $socks_except = array();
    while ($this->server_run_flag) {
      $tv_sec = $this->get_tv_sec();
      $socks_read = $this->sockets;
      $socks_write = $this->dirty_sockets;
      if (($n = socket_select($socks_read, $socks_write, $socks_except, $tv_sec)) === FALSE) {
        logger('select error: '.sockerr(), WLOG_ERR);
        $retval = 1;
        break;
      } elseif ($n <= 0) {
        continue;
      }
      foreach ($socks_read as $socket) {
        if ($socket == $this->server_socket) {
          if (($client_socket = socket_accept($this->server_socket)) === FALSE) {
            logger('error accepting new connection: '.sockerr());
          } else {
            $cid = $this->cids++;
            $this->sockets[$cid] = $client_socket;
            $this->clients[$cid] = new CrewClient($this, $client_socket, $cid);
          }
        } else {
          if (($cid = $this->lookup_client_cid($socket)) === FALSE) {
            logger('unknown client; closing the corresponding socket');
            $this->disconnect_socket($socket);
          } else {
            $buf = '';
            if (($n = socket_recv($socket, $buf, MAX_RECV_BUFFER, MSG_DONTWAIT)) === FALSE) {
              logger(sprintf('error recv data from client #%d: %s',$cid,sockerr($socket)));
            } elseif ($n <= 0) {
              $this->disconnect_client($socket);
            } else {
              $this->clients[$cid]->recv($n,$buf);
            }
          }
        }
      }
      foreach($socks_write as $socket) {
        if (($cid = $this->lookup_client_cid($socket)) === FALSE) {
          logger('unknown client; closing the corresponding socket');
          $this->disconnect_socket($socket);
        } else {
          if (($n = socket_write($socket,$this->clients[$cid]->buf_out)) === FALSE) {
            $msg = sprintf('error writing data to socket #%d: %s', $cid, sockerr($socket));
            logger($msg);
          } elseif ($n < strlen($this->clients[$cid]->buf_out)) {
            $this->clients[$cid]->buf_out = substr($this->clients[$cid]->buf_out,$n);
          } else {
            $this->clients[$cid]->buf_out = '';
            if (($socket_key = array_search($socket,$this->dirty_sockets)) !== FALSE) {
              array_splice($this->dirty_sockets,$socket_key,1);
            }
            if ($this->clients[$cid]->state == 2) {
              $this->disconnect_client($socket);
            }
          }
        }
      }
    }
    socket_close($this->server_socket);
    return $retval;
  }
  function get_tv_sec() {
    global $DEBUG,$MARK_TIME;
    if ($MARK_TIME <= 0) {
      return NULL;
    }
    if (($now = time()) >= $this->mark_next) {
      read_config();
      logger(sprintf('---MARK--- (clients: %d, workshops: %d)',
                     sizeof($this->clients),
                     sizeof($this->workshops)));
      $this->mark_next = $now + $MARK_TIME;
      if ($DEBUG >= WLOG_DEBUG) {
        $this->dump_overview();
      }
      foreach($this->clients as $cid => $client) {
        $payload = "---MARK--- ({$client->nick})";
        $frame = '';
        $this->frame_encode(0x89, $payload, $frame);
        $this->clients[$cid]->send($frame);
        logger("sending PING($cid): '$payload'",WLOG_DEBUG);
      }
    }
    return($this->mark_next - $now);
  }
  function dump_overview() {
    if ((sizeof($this->clients) > 0 ) || (sizeof($this->workshops) > 0)) {
      $s ="OVERVIEW\nWORKERS\n";
      foreach($this->clients as $cid => $client) {
        $wid =  $client->wid;
        $nick = $client->nick;
        $shop = (isset($this->workshops[$wid])) ? $this->workshops[$wid]->name : 'unknown';
        $s .= "client $cid ($nick) works in workshop $wid ($shop)\n";
      }
      $s .= "WORKSHOPS\n";
      foreach($this->workshops as $wid => $workshop) {
        $origin = $workshop->origin;
        $shop = $workshop->name;
        $count = sizeof($workshop->clients);
        $s .= "workshop $wid ($origin$shop) has $count worker(s):\n";
        foreach ($workshop->clients as $cid => $client) {
          $nick = $this->clients[$cid]->nick;
          $name = $this->clients[$cid]->name;
          $s .= "  client $cid $name ($nick)\n";
        }
      }
      $s .= "OVERVIEW END";
      logger($s,WLOG_DEBUG);
    }
  }
  function disconnect_socket($socket) {
    if (($cid = array_search($socket,$this->sockets)) !== FALSE) {
      logger(sprintf('removing socket #%d from list of sockets',$cid),WLOG_DEBUG);
      unset($this->sockets[$cid]);
    }
    if (($socket_key = array_search($socket,$this->dirty_sockets)) !== FALSE) {
      logger(sprintf('removing socket #%d from list of dirty sockets too',$cid),WLOG_DEBUG);
      array_splice($this->dirty_sockets,$socket_key,1);
    }
    socket_close($socket);
  }
  function disconnect_client($socket) {
    foreach($this->clients as $cid => $client) {
      if ($client->socket == $socket) {
        $wid = $client->wid;
        if (isset($this->workshops[$wid])) {
          $this->workshops[$wid]->disjoin($client);
          if (sizeof($this->workshops[$wid]->clients) <= 0) {
            logger(sprintf('removing empty workshop #%d', $wid));
            unset($this->workshops[$wid]);
          }
        }
        unset($this->clients[$cid]);
        break;
      }
    }
    $this->disconnect_socket($socket);
  }
  function lookup_client_cid($socket) {
    foreach($this->clients as $cid => $client) {
      if ($client->socket == $socket) {
        return $cid;
      }
    }
    return FALSE;
  }
  function find_workshop($origin, $name, $smax, $wmax, $cid, &$error) {
    $wid = NULL;
    $shops = $workers = 0;
    $sockname = sprintf('#%d (%s:%d)',$cid, $this->clients[$cid]->remote_address, $this->clients[$cid]->remote_port);
    foreach($this->workshops as $workshop) {
      if ($workshop->origin == $origin) {
        ++$shops;
        if ($workshop->name == $name) {
          $workers = sizeof($workshop->clients);
          $wid = $workshop->wid;
          break;
        }
      }
    }
    if (is_null($wid)) {
      if ($shops < $smax) {
        $wid = ++$this->wids;
        $this->workshops[$wid] = new CrewWorkshop($this,$wid,$origin,$name);
        $retval = $wid;
      } else {
        logger(sprintf('%s: no shops left: SMAX=%d',$sockname,$smax));
        $error = "SMAX=$smax";
        $retval = FALSE;
      }
    } else {
      if ($workers < $wmax) {
        $retval = $wid;
      } else {
        logger(sprintf('%s: no worker places left: WMAX=%d',$sockname, $wmax));
        $error = "WMAX=$wmax";
        $retval = FALSE;
      }
    }
    return $retval;
  }
  function frame_encode($fin_opcode, $payload, &$frame) {
    $retval = TRUE;
    $b0 = $fin_opcode & 0x8F;
    $length = strlen($payload);
    if ($length < 125) {
      $frame = pack('CC',$b0, $length).$payload;
    } elseif ($length < 65536) {
      $frame = pack('CCn', $b0, 126, $length).$payload;
    } elseif ($length < 2147483648) {
      $frame = pack('CCNN', $b0, 127, 0, $length).$payload;
    } else {
      logger('unsupported payload length (max is 2147483647)');
      $retval = FALSE;
    }
    return $retval;
  }
}
#
# CrewClient
#
class CrewClient {
  var $server = NULL;
  var $socket;
  var $cid = 0;
  var $wid = 0;
  var $remote_address = '';
  var $remote_port = 0;
  var $local_address = '';
  var $local_port = 0;
  var $headers = array();
  var $state = 0;
  var $buf_out = '';
  var $buf_in = '';
  var $workshop = NULL;
  var $authenticated = FALSE;
  var $nick = '';
  var $name = '';
  var $date = '';
  var $attr = '';
  var $range = array(0, 0);
  function CrewClient(&$server, $socket, $cid) {
    $this->server = &$server;
    $this->socket = $socket;
    $this->cid = $cid;
    if (socket_getpeername($socket,$this->remote_address,$this->remote_port) === FALSE) {
      $this->remote_address = 'unknown';
      $this->remote_port = 0;
      logger('cannot retrieve peer name: '.sockerr($socket));
    }
    if (socket_getsockname($socket,$this->local_address,$this->local_port) === FALSE) {
      $this->local_address = 'unknown';
      $this->local_port = 0;
      logger('cannot retrieve sock name: '.sockerr($socket));
    }
    logger(sprintf('new client #%d: %s:%d [%s:%d]',$cid, $this->remote_address,$this->remote_port,
                   $this->local_address,$this->local_port));
  }
  function recv($length, &$buffer) {
    if ($this->state == 0) {
      if ($this->valid_handshake($buffer)) {
        $this->state = 1;
      } else {
        $this->state = 2;
      }
    } elseif ($this->state == 1) {
      $payload = '';
      $fin_opcode = 0;
      $this->buf_in .= $buffer;
      while (($length = $this->frame_available($this->buf_in)) !== FALSE) {
        if ($this->frame_decode($this->buf_in, $fin_opcode, $payload)) {
          $opcode = $fin_opcode & 0x0F;
          if ($opcode == 0x08) {
            $response = '';
            $error = pack('n',1000)."BYE";
            $this->server->frame_encode(0x88, $error, $response);
            $this->send($response);
            $this->state = 2;
            break;
          } elseif ($opcode == 0x09) {
            logger(sprintf("received PING(%d): '%s'",$this->cid,$payload));
            $response = '';
            $this->server->frame_encode(0x8A, $payload, $response);
            $this->send($response);
          } elseif ($opcode == 0x0A) {
            logger(sprintf("received PONG(%d): '%s'",$this->cid,$payload),WLOG_DEBUG);
          } else {
            $error = '';
            if ($this->process_request($fin_opcode,$payload,$error) === FALSE) {
              $response = '';
              $error = pack('n',1008).$error;
              $this->server->frame_encode(0x88, $error, $response);
              $this->send($response);
              $this->state = 2;
              break;
            }
          }
        }
        $this->buf_in = substr($this->buf_in,$length);
      }
    }
  }
  function process_request($fin_opcode, &$payload, &$error) {
    $retval = TRUE;
    if (!$this->authenticated) {
      if ($this->valid_authentication($payload)) {
        $this->authenticated = TRUE;
        $origin = $this->headers['origin'];
        $request_uri = $this->headers['request_uri'];
        $smax = get_org_property($origin,1);
        $wmax = get_org_property($origin,2);
        if (($wid=$this->server->find_workshop($origin,$request_uri,$smax,$wmax,$this->cid,$error))===FALSE){
          $retval = FALSE;
        } else {
          $this->workshop = &$this->server->workshops[$wid];
          $this->workshop->join($this, $payload);
        }
      }
    } else {
      $cmd = $payload[0];
      switch ($cmd) {
      case 'D':
        $this->workshop->process_diff($this,$payload);
        break;
      case 'M':
        $this->workshop->cast_message($this, $payload);
        break;
      case 'R':
        $this->workshop->send_user_info($this);
        $this->workshop->send_userlist($this);
        $this->workshop->send_text_full($this);
        break;
      default:
        logger(sprintf("unknown request: '$cmd' from '%d' (%s)",$this->cid,$this->nick));
        break;
      }
    }
    return $retval;
  }
  function valid_authentication($buffer) {
    global $MAX_DELTA;
    $retval = TRUE;
    $sockname = sprintf('#%d (%s:%d)',$this->cid, $this->remote_address,$this->remote_port);
    $a = explode("\t",$buffer);
    if ((sizeof($a) < 5) || ($a[0] != 'A')) {
      logger("$sockname: authentication failed");
      $retval = FALSE;
    } else {
      $orig = $this->headers['origin'];
      $shop = $this->headers['request_uri'];
      $this->nick = $a[1];
      $this->name = $a[2];
      $this->date = $a[3];
      $sig1 = $a[4];
      $hmac_key = get_org_property($orig);
      $hmac_msg = $orig.$shop.$this->name.$this->nick.$this->date;
      $sig2 = hmac($hmac_key,$hmac_msg);
      if ($sig1 != $sig2) {
        logger("$sockname: invalid signature; access denied");
        $retval = FALSE;
      } else {
        $m = array();
        $pattern = '/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/';
        if (preg_match($pattern,$this->date,$m)) {
          $delta = time() - gmmktime($m[4],$m[5],$m[6],$m[2],$m[3],$m[1]);
          if ($MAX_DELTA < abs($delta)) {
            logger("$sockname: signature not (yet) valid (anymore): delta = ".$delta);
            $retval = FALSE;
          } else {
            logger(sprintf("%s: valid signature; delta = %ds",$sockname,$delta),WLOG_DEBUG);
          }
        } else {
          logger(sprintf("%s: invalid date pattern '%s'; bailing out",$sockname,$this->date));
          $retval = FALSE;
        }
      }
    }
    if (!$retval) {
      $payload = pack('n',1008).'Unauthorised';
      $response = '';
      $this->server->frame_encode(0x88, $payload, $response);
      $this->send($response);
      $this->state = 2;
    }
    return $retval;
  }
  function valid_handshake($buffer) {
    $matches = array();
    $sockname = sprintf('#%d (%s:%d)',$this->cid, $this->remote_address,$this->remote_port);
    # massage $buffer, standardise on \n as EOL, unfold extended lines (RFC2616 4.2)
    $handshake = str_replace("\r\n", "\n", $buffer);
    $handshake = str_replace("\n\r", "\n", $handshake);
    $handshake = str_replace("\r",   "\n", $handshake);
    $handshake = str_replace("\n ",  " ",  $handshake);
    $handshake = str_replace("\n\t", " ",  $handshake);
    $patterns = array('request_uri' => '/GET (.*) HTTP/',
                      'host'        => '/Host[\t ]*:[\t ]+(.*)\n/i',
                      'version'     => '/Sec-WebSocket-Version[\t ]*:[\t ]+(.*)\n/i',
                      'upgrade'     => '/Upgrade[\t ]*:[\t ]+(.*)\n/i',
                      'connection'  => '/Connection[\t ]*:[\t ]+(.*)\n/i',
                      'nonce'       => '/Sec-Websocket-Key[\t ]*:[\t ]+(.*)\n/i',
                      'origin'      => '/Origin[\t ]*:[\t ]+(.*)\n/i',
                      'protocol'    => '/Sec-Websocket-Protocol[\t ]*:[\t ]+(.*)\n/i',
                      'extensions'  => '/Sec-Websocket-Extensions[\t ]*:[\t ]+(.*)\n/i'
                      );
    $headers = array();
    foreach($patterns as $field => $pattern) {
      $headers[$field] = (preg_match($pattern, $handshake, $matches)) ? trim($matches[1]) : NULL;
      logger("$sockname: $field = ".$headers[$field], WLOG_DEBUG);
    }
    $status_code = 101;
    if (is_null($headers['request_uri'])) {
      logger("$sockname: no request_uri specified");
      $status_code = 400;
    } elseif (strncasecmp($headers['request_uri'],'/crewserver/program',19) == 0) {
      logger("$sockname: request for server source code");
      $status_code = 200;
    }
    if ($status_code != 200) {
      if ((is_null($headers['version'])) || (intval($headers['version']) != 13)) {
        logger("$sockname: version not supported or not specified");
        $status_code = 400;
      }
      if (is_null($headers['host'])) {
        logger("$sockname: no host specified");
        $status_code = 400;
      }
      if (is_null($headers['upgrade'])) {
        logger("$sockname: missing upgrade field");
        $status_code = 400;
      } elseif (stristr($headers['upgrade'],'websocket') === FALSE) {
        logger("$sockname: upgrade field should include the websocket keyword");
        $status_code = 400;
      }
      if (is_null($headers['connection'])) {
        logger("$sockname: missing connection field");
        $status_code = 400;
      } elseif (stristr($headers['connection'],'Upgrade') === FALSE) {
        logger("$sockname: connection field should include the Upgrade token");
        $status_code = 400;
      }
      if (is_null($headers['nonce'])) {
        logger("$sockname: missing Sec-WebSocket-Key field");
        $status_code = 400;
      }
      if (is_null($headers['protocol'])) {
        logger("$sockname: missing Sec-WebSocket-Protocol field");
        $status_code = 400;
      } elseif (stristr($headers['protocol'],CREW_SERVER_PROTOCOL) === FALSE) {
        logger("$sockname: Sec-WebSocket-Protocol not recognised: ".$headers['protocol']);
        $status_code = 400;
      }
      if (is_null($headers['origin'])) {
        logger("$sockname: missing Origin field");
        $status_code = 400;
      } else {
        $headers['origin'] = utf8_strtolower($headers['origin']);
        if (get_org_property($headers['origin']) === FALSE) {
          logger("$sockname: server is not configured for this origin: ".$headers['origin']);
          $status_code = 403;
        }
      }
    }
    $status_name = array(101 => "Switching Protocols",
                         200 => "OK",
                         400 => "Bad Request",
                         403 => "Forbidden"
                         );
    if ($status_code == 101) {
      $accept_key = base64_encode(pack('H*',sha1($headers['nonce'].WEBSOCKETS_UUID)));
      $response = sprintf("HTTP/1.1 %d %s\r\n".
                          "Date: %s\r\n".
                          "Server: %s\r\n".
                          "Upgrade: websocket\r\n".
                          "Connection: Upgrade\r\n".
                          "Sec-WebSocket-Accept: %s\r\n".
                          "\r\n",
                          $status_code,$status_name[$status_code],
                          gmdate('D, d M Y H:i:s').' GMT',
                          CREW_SERVER_NAME.' '.CREW_SERVER_VERSION,
                          $accept_key);
    } elseif ($status_code == 200) {
      global $SERVER_SOURCE;
      $response = sprintf("HTTP/1.1 %d %s\r\n".
                          "Date: %s\r\n".
                          "Server: %s\r\n".
                          "Content-Length: %d\r\n".
                          "Connection: close\r\n".
                          "Content-Type: application/zip\r\n".
                          "Content-Disposition: attachment; filename=\"crewserver-program.zip\"\r\n".
                          "\r\n".
                          "%s",
                          $status_code,$status_name[$status_code],
                          gmdate('D, d M Y H:i:s').' GMT',
                          CREW_SERVER_NAME.' '.CREW_SERVER_VERSION,
                          strlen($SERVER_SOURCE),
                          $SERVER_SOURCE);
    } else {
      $message = sprintf("%d %s\r\n",$status_code,$status_name[$status_code]);
      $response = sprintf("HTTP/1.1 %d %s\r\n".
                          "Date: %s\r\n".
                          "Server: %s\r\n".
                          "Content-Length: %d\r\n".
                          "Connection: close\r\n".
                          "Content-Type: text/plain; charset=US-ASCII\r\n".
                          "\r\n".
                          "%s",
                          $status_code,$status_name[$status_code],
                          gmdate('D, d M Y H:i:s').' GMT',
                          CREW_SERVER_NAME.' '.CREW_SERVER_VERSION,
                          strlen($message),
                          $message);
    }
    $this->headers = $headers;
    $this->send($response);
    return ($status_code == 101) ? TRUE : FALSE;
  }
  function send($buffer) {
    $this->buf_out .= $buffer;
    if ((array_search($this->socket,$this->server->dirty_sockets)) === FALSE) {
      $this->server->dirty_sockets[] = $this->socket;
    }
  }
  function frame_available(&$buffer) {
    if (($n = strlen($buffer)) < 2) {
      return FALSE;
    }
    $b1 = ord($buffer[1]);
    $overhead = ($b1 & 0x80) ? 6 : 2;
    $length = $b1 & 0x7F;
    if ($length <= 125) {
      $retval = ($length + $overhead <= $n) ? $length + $overhead : FALSE;
    } elseif ($length == 126) {
      $overhead += 2;
      $length = (ord($buffer[2]) << 8) + ord($buffer[3]);
      $retval = ($length + $overhead <= $n) ? $length + $overhead : FALSE;
    } elseif ($length == 127) {
      $overhead += 8;
      $length = ((ord($buffer[6]) & 0x7F) << 24) + (ord($buffer[7]) << 16) +
        (ord($buffer[8]) << 8) + ord($buffer[9]);
      $retval = ($length + $overhead <= $n) ? $length + $overhead : FALSE;
    } else {
      logger(sprintf('%s(%d): huge frame detected; bailing out',__FILE__,__LINE__));
      $payload = pack('n',1002).'Protocol Error';
      $response = '';
      $this->server->frame_encode(0x88, $payload, $response);
      $this->send($response);
      $this->state = 2;
      $retval = FALSE;
    }
    return $retval;
  }
  function frame_decode($data,&$fin_opcode,&$payload) {
    $retval = TRUE;
    $fin_opcode = $b0 = ord($data[0]);
    $final  = ($b0 & 0x80) ? TRUE : FALSE;
    $rsv123 = ($b0 >> 4) & 0x07;
    if ($rsv123 != 0) {
      logger('reserved bits are non-zero');
      $retval = FALSE;
    }
    $opcode = $b0 & 0x0F;
    $b1 = ord($data[1]);
    $masked = ($b1 & 0x80) ? TRUE : FALSE;
    $length = $b1 & 0x7F;
    if ($length <= 125) {
      $offset = ($masked) ? 6 : 2;
    } elseif ($length == 126) {
      $offset = ($masked) ? 8 : 4;
      $length = (ord($data[2]) << 8) + ord($data[3]);
      if ($length < 125) {
        logger('overlong 16-bit length detected');
        $retval = FALSE;
      }
    } else {
      $offset = ($masked) ? 14 : 10;
      if ((ord($data[2]) != 0) || (ord($data[3]) != 0) ||
          (ord($data[4]) != 0) || (ord($data[5]) != 0) ||
          ((ord($data[6]) & 0x80) != 0)) {
        logger('unsupported 63-bit length (max is 2147483647)');
        $retval = FALSE;
      }
      $length = (ord($data[6]) << 24) +
        (ord($data[7]) << 16) +
        (ord($data[8]) << 8) +
        ord($data[9]);
      if ($length < 65536) {
        logger('overlong 64-bit length detected');
        $retval = FALSE;
      }
    }
    if ($retval) {
      if ($masked) {
        $mask = array(ord($data[$offset-4]),
                      ord($data[$offset-3]),
                      ord($data[$offset-2]),
                      ord($data[$offset-1])
                      );
        for ($i=0; $i < $length; ++$i) {
          $data[$i+$offset] = chr(ord($data[$i+$offset]) ^ $mask[$i % 4]);
        }
      }
      $payload .= substr($data,$offset,$length);
    }
    return $retval;
  }
}
#
# CrewWorkshop
#
class CrewWorkshop {
  var $clients = array();
  var $wid = 0;
  var $origin = '';
  var $name = '';
  var $text = '';
  var $attr = '';
  function CrewWorkshop(&$server,$wid, $origin, $name) {
    $this->server = &$server;
    $this->wid = $wid;
    $this->origin = $origin;
    $this->name = $name;
    $this->clients = array();
  }
  function get_userlist() {
    $n = sizeof($this->clients);
    $m = 4;
    $s = sprintf("U\t%d\t%d",$n,$m);
    foreach ($this->clients as $cid => $client) {
      $range = (($hi=$client->range[0]) == ($lo=$client->range[1])) ?
        strval($hi) : strval($hi).'-'.strval($lo);
      $s .= sprintf("\t%s\t%s\t%s\t%s",$client->nick, $client->name,$client->attr,$range);
    }
    return $s;
  }
  function get_next_attr() {
    $used = array();
    $high = ord('A')-1;
    foreach ($this->clients as $cid => $client) {
      $c = ord($client->attr);
      $used[] = $c;
      $high = max($high,$c);
    }
    if ($high < ord('Z')) {
      return chr($high+1);
    }
    for ($i=ord('A'); $i <= ord('Z'); ++$i) {
      if (!in_array($i,$used)) {
        return chr($i);
      }
    }
    return 'Z';
  }
  function join(&$client,$payload) {
    $this->clients[$client->cid] = &$client;
    $client->wid = $this->wid;
    $client->attr = $this->get_next_attr();
    $client->range = array(0,0);
    if (sizeof($this->clients) <= 1) {
      $a = explode("\t", $payload);
      $this->text = $a[5];
      $this->attr = str_repeat('@',utf8_strlen($this->text));
    }
    $this->send_user_info($client);
    $this->cast_userlist();
    $this->cast_user_enters($client);
    $this->send_text_full($client);
  }
  function disjoin($client) {
    $cid = $client->cid;
    $nick = $client->nick;
    if (isset($this->clients[$cid])) {
      logger(sprintf('removing client #%d (%s) from workshop #%d', $cid, $nick, $this->wid));
      logger(sprintf('disconnecting client #%d (%s) (%s:%d [%s:%d])', $cid,$nick,
                     $client->remote_address, $client->remote_port,
                     $client->local_address, $client->local_port),WLOG_DEBUG);
      $this->attr = str_replace($client->attr,"@",$this->attr);
      $this->cast_user_leaves($client);
      unset($this->clients[$cid]);
      $this->cast_userlist();
    } else {
      logger(sprintf("weird: disjoining user '%d' not a member of workshop '%d'",$cid,$this->wid));
    }
  }
  function cast_userlist() {
    $this->send($this->get_userlist());
  }
  function cast_user_enters($client) {
    $payload=sprintf("E\t%s\t%s\t%s",$client->nick,$client->name,$client->attr);
    $this->send($payload);
  }
  function cast_user_leaves($client) {
    $payload=sprintf("L\t%s\t%s\t%s",$client->nick,$client->name,$client->attr);
    $this->send($payload);
  }
  function cast_message($client,$message) {
    $a = explode("\t",$message);
    $payload = sprintf("M\t%s\t%s: %s",$client->attr,$client->nick,$a[1]);
    $this->send($payload);
  }
  function send_text_full($client) {
    $a=$this->attr;
    $s=$this->text;
    $t = '';
    $n = strlen($s);
    $i=0;
    $j=0;
    while ($i<$n) {
      $b=ord($s[$i++]);
      if (($b & 0xC0) == 0x80) continue;
      if (($b & 0xF8) == 0xF0) {
        $t .= $a[$j];
      }
      $t .= $a[$j++];
    }
    $cid = $client->cid;
    $data = sprintf("T\t%s\t%s",$t,$s);
    $buffer = '';
    $this->server->frame_encode(0x81,$data,$buffer);
    $this->clients[$cid]->send($buffer);
  }
  function send_user_info($client) {
    $cid = $client->cid;
    $payload = sprintf("I\t%s\t%s\t%s",$client->attr,$client->remote_address,$client->remote_port);
    $frame = '';
    $this->server->frame_encode(0x81,$payload,$frame);
    $this->clients[$cid]->send($frame);
  }
  function send_userlist($client) {
    $cid = $client->cid;
    $payload =$this->get_userlist();
    $frame = '';
    $this->server->frame_encode(0x81,$payload,$frame);
    $this->clients[$cid]->send($frame);
  }
  function send($data,$encode=TRUE) {
    if ($encode) {
      $buffer = '';
      if (!$this->server->frame_encode(0x81,$data,$buffer)) {
        logger(sprintf('%s(): cannot encode data (wid=%d)',__FUNCTION__,$this->wid));
        return;
      }
    } else {
      $buffer = $data;
    }
    foreach($this->clients as $cid => $client) {
      $this->clients[$cid]->send($buffer);
    }
  }
  function process_diff(&$client, $payload) {
    $a = explode("\t",$payload);
    if (sizeof($a) < 2) {
      logger(sprintf("%s(): payload too short: '%s'",__FUNCTION__,$payload));
      return;
    }
    if (sizeof($a) < 8) {
      if (strpos($a[1],'-')) {
        list($r0,$r1) = explode('-',$a[1]);
        $client->range[0] = intval($r0);
        $client->range[1] = intval($r1);
      } else {
        $client->range[0] = $client->range[1] = intval($a[1]);
      }
      $jranges = $this->calc_jranges();
      $s = "R\t".implode("\t",$jranges);
      $this->send($s);
      return;
    }
    $offset = intval($a[2]);
    $prelen = intval($a[3]);
    $oldlen = intval($a[4]);
    $newlen = intval($a[5]);
    $postlen = intval($a[6]);
    $newpretext = ($prelen <= 0)  ? '' : utf8_substr($a[7],0,              $prelen );
    $newtext    = ($newlen <= 0)  ? '' : utf8_substr($a[7],$prelen,        $newlen );
    $newpostext = ($postlen <= 0) ? '' : utf8_substr($a[7],$prelen+$newlen,$postlen);
    $mismatch = FALSE;
    $oldpretext = utf8_substr($this->text,$offset,$prelen);
    if (($prelen > 0) && ($oldpretext != $newpretext)) {
      $mismatch = TRUE;
      logger(sprintf("%s(): pre-context is different: '%s' != '%s'",__FUNCTION__,
                     hexdump($oldpretext,TRUE), hexdump($newpretext,TRUE)),WLOG_DEBUG);
    }
    $oldpostext = utf8_substr($this->text,$offset+$prelen+$oldlen,$postlen);
    if (($postlen > 0) && ($oldpostext != $newpostext)) {
      $mismatch = TRUE;
      logger(sprintf("%s(): post-context is different: '%s' != '%s'",__FUNCTION__,
                     hexdump($oldpostext,TRUE), hexdump($newpostext,TRUE)),WLOG_DEBUG);
    }
    if ($mismatch) {
      logger(sprintf('no match for diff context; dropping diff from client #%d',$client->cid));
      return;
    }
    $oldtext = ($oldlen <= 0)  ? '' : utf8_substr($this->text,$offset+$prelen, $oldlen);
    $pivot = $offset + $prelen;
    $offsetext = utf8_substr($this->text,0,$offset);
    $this->text = $offsetext.$a[7].utf8_substr($this->text,$pivot+$oldlen+$postlen);
    $this->attr = substr($this->attr,0,$pivot).
                  str_repeat($client->attr,$newlen).
                  substr($this->attr,$pivot+$oldlen);
    if ($newlen != $oldlen) {
      $delta = $newlen - $oldlen;
      foreach($this->clients as $cid => $worker) {
        for ($i=0; $i<2; ++$i) {
          if (($c=$worker->range[$i]) >= $pivot) {
            $this->clients[$cid]->range[$i] = max($c+$delta,$pivot);
          }
        }
      }
    }
    if (strpos($a[1],'-')) {
      list($r0,$r1) = explode('-',$a[1]);
      $client->range[0] = intval($r0);
      $client->range[1] = intval($r1);
    } else {
      $client->range[0] = $client->range[1] = intval($a[1]);
    }
    $jranges = $this->calc_jranges();
    $s = sprintf("P\t%s\t%d\t%d\t%d\t%d\t%d\t%s\t",
                 $client->attr,
                 utf16_strlen($offsetext),
                 utf16_strlen($newpretext),
                 utf16_strlen($oldtext),
                 utf16_strlen($newtext),
                 utf16_strlen($newpostext),
                 $a[7]).implode("\t",$jranges);
    $this->send($s);
    return;
  }
  function calc_jranges() {
    $u2j = array();
    foreach($this->clients as $cid => $worker) {
      $u2j[$worker->range[0]] = $worker->range[0];
      $u2j[$worker->range[1]] = $worker->range[1];
    }
    ksort($u2j);
    $u = 0;
    $b = 0;
    $j = 0;
    $n = strlen($this->text);
    foreach($u2j as $k => $v) {
      while (($u < $k) && ($b < $n)) {
        $byte = ord($this->text[$b++]);
        if (($byte & 0xC0) == 0x80) {
          continue;
        }
        ++$u;
        ++$j;
        if (($byte & 0xF8) == 0xF0) {
          ++$j;
        }
      }
      $u2j[$u]=$j;
    }
    $a = array();
    foreach($this->clients as $cid => $worker) {
      if ($worker->range[0] == $worker->range[1]) {
        $a[] = strval($u2j[$worker->range[0]]);
      } else {
        $a[] = strval($u2j[$worker->range[0]]).'-'.strval($u2j[$worker->range[1]]);
      }
    }
    return $a;
  }
}
#
# Utilities
#
function logger($message, $level=WLOG_INFO) {
  global $LOG2SYSLOG,$DEBUG;
  static $flag = NULL;
  static $messages = array();
  if (is_null($LOG2SYSLOG)) {
    $messages[] = array($message,$level);
    return;
  }
  if (is_null($flag)) {
    if ($LOG2SYSLOG) {
      $flag = openlog(PROG, LOG_PID, LOG_USER);
    } else {
      $flag = FALSE;
    }
    foreach($messages as $msg) {
      logger($msg[0],$msg[1]);
    }
    $messages = array();
  }
  if ($level > $DEBUG) {
    return;
  }
  if ($flag && syslog($level, $message)) {
    return;
  }
  fwrite(STDERR, sprintf("%s %s(%d.%d): %s\r\n",strftime('%Y-%m-%d %T'),PROG,LOG_USER,$level,$message));
  return;
}
function sockerr($socket=NULL) {
  $last_error = (is_null($socket)) ? socket_last_error() : socket_last_error($socket);
  return strval($last_error)."/".socket_strerror($last_error);
}
function dump_buffer($length, $buffer, $message='') {
  if (!empty($message)) {
    logger($message);
  }
  $off = $hex = $txt = '';
  for ($i=0; $i<$length; ++$i) {
    if ($i == 0) {
      $off = sprintf("%04X",$i);
    } elseif (($i % 16) == 0) {
      logger(sprintf('%s: %-50.50s %s',$off,$hex,$txt));
      $hex = $txt = '';
      $off = sprintf("%04X",$i);
    }
    $c = ord($buffer[$i]);
    $hex .= sprintf('%02X ',$c);
    $txt .= ((32 < $c) && ($c < 127)) ? chr($c) : '.';
  }
  logger(sprintf('%s: %-50.50s %s',$off,$hex,$txt));
}
function hmac($key, $message, $raw=FALSE, $hash="sha1") {
  $bs = 64;
  if (($n = strlen($key)) > $bs) {
    $n = strlen($key=pack('H*', $hash($key)));
  }
  if ($n < $bs) {
    $key .= str_repeat(chr(0), $bs-$n);
  }
  $opad = $ipad = $key;
  for ($i=0; $i < $bs; ++$i) {
    $c = ord($key[$i]);
    $opad[$i] = chr(0x5C ^ $c);
    $ipad[$i] = chr(0x36 ^ $c);
  }
  $hmac = $hash($opad.pack('H*', $hash($ipad.$message)));
  return ($raw) ? pack('H*',$hmac) : $hmac;
}
function get_org_property($origin,$property=0) {
  global $ORIGINS;
  if (isset($ORIGINS[$origin])) {
    return $ORIGINS[$origin][$property];
  }
  read_config();
  if (isset($ORIGINS[$origin])) {
    return $ORIGINS[$origin][$property];
  }
  return FALSE;
}
function hexdump($s,$raw=FALSE) {
  if ($raw) {
    $hex = '';
    for ($i=0; $i<strlen($s); ++$i) {
      $hex .= ($i) ? ' ' : '';
      $hex .= sprintf('%02X',ord($s[$i]));
    }
    return $hex;
  }
  $t = '0000:';
  $hex = $chars = '';
  for ($i=0; $i<strlen($s); ++$i) {
    if (($i) && (($i % 16) == 0)) {
      $t .= sprintf("%-49.49s %s\n%04X:",$hex,$chars,$i);
      $hex = $chars = '';
    }
    $c = ord($s[$i]);
    $hex .= sprintf(' %02X',$c);
    $chars .= ((32<=$c) && ($c<127)) ? chr($c) : '.';
  }
  $t .= sprintf("%-49.49s %s\n",$hex,$chars);
  return $t;
}
function read_config($force=FALSE) {
  global $ORIGINS;
  global $SERVER_ADDRESS, $SERVER_PORT, $LOG2SYSLOG, $MARK_TIME,$MAX_DELTA, $DEBUG;
  static $last_mtime=0;
  $cfg = basename(__FILE__,'.php').'.conf';
  $conf = dirname(__FILE__).'/'.$cfg;
  clearstatcache();
  if ((@$mtime=filemtime($conf)) === FALSE) {
    logger(sprintf("%s(): warning: cannot stat '%s'; keeping existing %d origins",
                   __FUNCTION__,$conf,sizeof($ORIGINS)),WLOG_WARNING);
    return FALSE;
  }
  if (($mtime <= $last_mtime) && (!($force))) {
    logger(sprintf("%s: unchanged since %s", $cfg,strftime('%Y-%m-%d %T',$mtime)),WLOG_DEBUG);
    return TRUE;
  }
  if (($buffer = file_get_contents($conf)) === FALSE) {
    logger(sprintf("%s(): error reading '%s'; keeping existing %d origins",
                   __FUNCTION__,$conf,sizeof($ORIGINS)),WLOG_ERROR);
    return FALSE;
  }
  $lines = explode("\n",$buffer);
  $num = 0;
  $origins = array();
  foreach ($lines as $line) {
    ++$num;
    $line = trim($line);
    if ((empty($line)) || ($line[0] == "#") || (strpos($line,'=')===FALSE)) {
      continue;
    }
    list($k,$v) = explode('=', $line, 2);
    $k = trim($k);
    $v = trim($v);
    if (strcasecmp($k,'origin') == 0) {
      $a = explode(',',$v);
      if (sizeof($a) <= 1) {
        logger(sprintf("error in %s:%d: cannot parse line '%s'",$conf,$num,$line));
        continue;
      }
      $origin = strtolower(trim($a[0]));
      $password = trim($a[1]);
      $workshops = min(max((isset($a[2])) ? intval($a[2]) : 1,1),32);
      $workers = min(max((isset($a[3])) ? intval($a[3]) : 4,1),26);
      $origins[$origin] = array($password, $workshops, $workers);
    } elseif (strcasecmp($k,'debug') == 0) {
      $level = max(min(intval($v),WLOG_DEBUG),WLOG_EMERG);
      if ($DEBUG == $level) {
        logger(sprintf('%s:%d: keeping debug level at %d',$cfg,$num,$DEBUG),WLOG_DEBUG);
      } else {
        logger(sprintf('%s:%d: changing debug level from %d to %d',$cfg,$num,$DEBUG,$level));
        $DEBUG = $level;
      }
    } elseif (strcasecmp($k,'max_delta')==0) {
      $newval = max(min(intval($v),86400),1);
      if ($MAX_DELTA == $newval) {
        logger(sprintf('%s:%d: keeping max_delta at %d',$cfg,$num,$MAX_DELTA),WLOG_DEBUG);
      } else {
        logger(sprintf('%s:%d: changing max_delta from %d to %d',$cfg,$num,$MAX_DELTA,$newval));
        $MAX_DELTA = $newval;
      }
    } elseif (strcasecmp($k,'mark_time')==0) {
      $newval = max(min(intval($v),86400),1);
      if ($MARK_TIME == $newval) {
        logger(sprintf('%s:%d: keeping mark_time at %d',$cfg,$num,$MARK_TIME),WLOG_DEBUG);
      } else {
        logger(sprintf('%s:%d: changing mark_time from %d to %d',$cfg,$num,$MARK_TIME,$newval));
        $MARK_TIME = $newval;
      }
    } elseif (strcasecmp($k,'server_address')==0) {
      if (($SERVER_ADDRESS != $v) && (!($force))) {
        logger(sprintf('%s:%d: cannot change server address (current=%s)',$cfg,$num,$SERVER_ADDRESS));
      } elseif ($force) {
        logger(sprintf('%s:%d: setting server address to %s',$cfg,$num,$v));
        $SERVER_ADDRESS = $v;
      }
    } elseif (strcasecmp($k,'server_port')==0) {
      $newval = intval($v);
      if (($SERVER_PORT != $newval) && (!($force))) {
        logger(sprintf('%s:%d: cannot change server port (current=%d)',$cfg,$num,$SERVER_PORT));
      } elseif ($force) {
        logger(sprintf('%s:%d: setting server port to %d',$cfg,$num,$newval));
        $SERVER_PORT = $newval;
      }
    } elseif (strcasecmp($k,'log2syslog')==0) {
      $newval = (intval($v)) ? TRUE : FALSE;
      if (($LOG2SYSLOG != $newval) && (!($force))) {
        logger(sprintf('%s:%d: cannot change log destination (current=%s)',
                       $cfg,$num,($LOG2SYSLOG) ? 'syslog':'stderr'));
      } elseif ($force) {
        $LOG2SYSLOG = $newval;
        logger(sprintf('%s:%d: setting log destination to %s',$cfg,$num,($newval) ? 'syslog':'stderr'));
      }
    }
  }
  $deleted = 0;
  $added = 0;
  $modified = 0;
  $same = 0;
  logger(sprintf("%s: origins overview (+ added, ! modified, = unchanged, - deleted):",$cfg),WLOG_DEBUG);
  foreach($ORIGINS as $k => $v) {
    if (isset($origins[$k])) {
      if (($origins[$k][0] != $v[0]) ||
          ($origins[$k][1] != $v[1]) ||
          ($origins[$k][2] != $v[2])) {
        logger(sprintf('! %s (shops:%d workers:%d)',$k,$origins[$k][1],$origins[$k][2]),WLOG_DEBUG);
        ++$modified;
      } else {
        logger(sprintf('= %s (shops:%d workers:%d)',$k,$origins[$k][1],$origins[$k][2]),WLOG_DEBUG);
        ++$same;
      }
    } else {
      logger(sprintf('- %s (shops:%d workers:%d)',$k,$v[1],$v[2]),WLOG_DEBUG);
      ++$deleted;
    }
  }
  foreach($origins as $k => $v) {
    if (!isset($ORIGINS[$k])) {
      logger(sprintf('+ %s (shops:%d workers:%d)',$k,$v[1],$v[2]),WLOG_DEBUG);
      ++$added;
    }
  }
  logger(sprintf("%s: origins: %d added, %d modified, %d unchanged, %d deleted",
                 $cfg, $added, $modified, $same, $deleted));
  $ORIGINS = $origins;
  $last_mtime = $mtime;
  return TRUE;
}
function utf16_strlen($s) {
  $b=0;
  $j=0;
  $i=0;
  $n=strlen($s);
  while ($i<$n) {
    $b=ord($s[$i++]);
    if (($b & 0xC0) == 0x80) continue;
    ++$j;
    if (($b & 0xF8) == 0xF0) ++$j;
  }
  return $j;
}
function agplv3_compliance() {
  $paths = get_included_files();
  $src = '';
  $zip = new Zip;
  $zip->OpenZipbuffer($src);
  foreach($paths as $path) {
    $zip->AddFile($path,'crewserver/'.basename($path));
  }
  $files = array(basename(WASENTRY,'.php').'-example.conf',
                 'readme.txt','about.html','license.html','graphics/waslogo-567x142.png');
  foreach($files as $file) {
    $path = dirname(WASENTRY).'/'.$file;
    if (file_exists($path)) {
      $zip->AddFile($path, 'crewserver/'.$file);
    } else {
      $data = sprintf("File '%s' was not found\n",$file);
      $zip->AddData($data, 'crewserver/'.$file);
    }
  }
  $zip->CloseZip();
  return $src;
}
?>