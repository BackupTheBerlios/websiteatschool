Date: 2013-06-12
Auth: Peter Fokker <peter@berestijn.nl>
File: readme.txt
Subj: Notes for CREW-server
Vers: 0.90.5

Download and install
====================

You can install the crewserver as follows:

1. Download the crewserver.zip file from your own Website@School website,
   e.g. from http://www.yourserver.org/program/modules/crew/crewserver.zip

2. Find a quiet place to unzip this file, perhaps your $HOME directory.
   Note that the file crewserver.zip unpacks into a subdirectory called
   crewserver

   $ cd  $HOME
   $ unzip  /path/to/crewserver.zip

   At this point you have all relevant files together in the directory
   $HOME/crewserver/.

   NOTE:
   Do NOT unpack this .ZIP-file in a directory that is accessible from the
   outside world and certainly not under the webserver's document root or the
   CMS Root Folder (where admin.php and index.php live).

3. Create crewserver.conf in the directory that was created while unzipping,
   i.e. in the same directory where crewserver.php resides. If you want you
   can use the example-configuration file as guidance and/or documentation.
   You can now edit crewserver.conf with your favourite editor. At the very
   least you MUST add an origin-line with the details for your server
   environment.

   $ cd  crewserver/
   $ cp  crewserver-example.conf  crewserver.conf
   $ $EDITOR  crewserver.conf

4. If you are concerned about the confidentiality of the shared keys in your
   crewserver.conf, you can minimise the permissions to say 0600 or even 0400
   as long as that file is owned by the user that will be running the
   crewserver process. (The crewserver process periodically re-reads the
   configuration file the hence needs read permissions on that file)


Starting the server
===================

You can now run the server as follows.

5. Check the permissions of the file crewserver.php and make sure that the
   read and execute permissions are set, at least for the user that whill be
   running the crewserver process. The minimal permissions are 0500 but it is
   perfectly acceptable to set the permissions to 0700, 0750 or even 0755
   (there are no secrets in crewserver.php). Also make sure that the other
   files are readable by the server process, e.g. same owner and at least
   0400 or 0640 or 0644 if you want.

   NOTE:
   The file crewserver.conf DOES contain privileged information; make sure
   permissions are as minimal as possible (preferably 0400).

6. Start the server (as ordinary user, you can do that!) by descending into
   the crewserver directory if you did not already do that in the previous
   step and executing the main program:

   $ cd $HOME/crewserver
   $ ./crewserver.php

   The server will start and depending on the configuration settings a few
   messages will be written to either syslog or stderr. If the server does
   not start you may have to change the path to the PHP-interpreter on the
   first line of crewserver.php (see below).


Notes and troubleshooting
=========================

If you have configured the server to log information to stderr, you can
capture the output of the server as follows:

  $ ./crewserver.php 2>&1 | tee -a logfile.log

Alternatively you can script your session:

  $ script logfile.log
  $ ./crewserver.php

If you want to run crewserver.php in the background while keeping an eye
on the log use this:

  $ ./crewserver.php >logfile.log 2>&1 &
  $ tail -f logfile.log

You can use Ctrl-C to kill the server (or use kill if it is running in
the background).

If you have configured the server to log information to syslog, you can try to
follow the tail of the logfile, e.g. tail -f /var/log/message.

Note that the crewserver.php file is configured to use the php command line
interpreter located in /usr/bin/php. If php is located elsewhere on your
machine, you need to adjust the first line in crewserver.php accordingly.

If you use the crewserver on a non-standard port (like the proposed port 8008
in the example configuration) you may need to adjust your firewall too before
the server can be used.


Source code
===========

The source code of the crewserver program is served by the crewserver program
itself if you know where to look. If you point your browser at
http://yourserver.org:8008/crewserver/program you receive a .ZIP-file with the
code, in compliance with the GNU AGPLv3+Additional Terms (see about.html and
license.html). Note that this works best if you leave all supporting files
(about.html, crewserver-example.conf, etc.) exactly where you unpacked them in
step 2 above. Note: your private configuration file crewserver.conf is never
made available to the outside world via this mechanism.


More information
================

Please refer to the manual that documents CREW for more information.


[eof readme.txt]
