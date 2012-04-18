<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker <peter@berestijn.nl>
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

/** /config-example.php - example of the main configuration file
 *
 * This file demonstrates and documents the contents of the main configuration
 * file 'config.php'. This file only contains the parameters that are necessary 
 * to make a connection to the database and to identify the location of the program
 * directory both in the file system on the webserver and as seen through a
 * web browser from the outside. It is also possible to switch debugging on
 * via the optional $CFG-debug variable. All other configuration parameters
 * are to be found in the database.
 *
 * As a rule, the configuration file is generated at installation time. It
 * MUST be called 'config.php' and it MUST reside in the same directory as the
 * main entry points {@link index.php}, {@link admin.php}, {@link cron.php}
 * and {@link file.php}.
 *
 * Here is an overview of the 11 essential parameters and the 1 optional
 * parameter kept in the global {@link $CFG} object.
 *
 *
 *  - $CFG->db_type defines the database type.
 *
 * Currently the only database type supported is 'mysql'.
 * Maybe other databases will be supported in the future.
 *
 * Default: 'mysql'
 *
 *
 *  - $CFG->db_server defines the name of the database server.
 *
 * In the case of 'mysql' the format is 'hostname:port' where
 * 'hostname' is a valid host (default 'localhost') and 'port'
 * is a portnumber or the path to a local socket, e.g.
 * '/var/lib/mysql/mysql.sock'. If the ':' and the portnumber
 * are omitted, the default port 3306 is used.
 *
 * Default: 'localhost'
 *
 *
 *  - $CFG->db_username holds the username to use when connecting to the server.
 *
 * Default: ''
 *
 *
 *  - $CFG->db_password holds the password to use when connecting to the server.
 *
 * Default: ''
 *
 *
 *  - $CFG->db_name holds the name of the database to use.
 *
 * Default: 'was'
 *
 *
 *  - $CFG->prefix holds the tablename prefix.
 *
 * The name of every table is prefixed with this value. This makes
 * it possible to have two or more different instances of Website@School
 * in the same database, simply by using a different prefix for every
 * instance. Using a prefix ending with an underscore makes the
 * resulting table names more readable and it also prevents
 * mis-interpretation of a table name as an SQL keyword.
 *
 * Default: 'was_'
 *
 *
 *  - $CFG->dir is the absolute directory path of 'index.php' and 'config.php'.
 *
 * The main entry points (index.php, admin.php, etc.) are located in
 * $CFG->dir whereas all other program files are located in the directory
 * tree starting in $CFG->progdir.
 *
 * Usually the path in $CFG-dir is the same as the path to the document root
 * of the webserver.
 *
 * Examples:
 *
 * - /var/www/html (Red Hat),
 *
 * - /home/exemplum/public_html (DirectAdmin),
 *
 * - /home/httpd/htdocs (Open NA),
 *
 * - C:\PROGRAM FILES\EASYPHP\WWW (Windows).
 *
 * Default: '/home/httpd/htdocs'
 *
 *
 *  - $CFG->www is the URI which corresponds with the directory $CFG->dir.
 *
 * This URI is of the form scheme://hostname:port/path, where
 * - scheme is either 'http' or 'https'
 * - hostname is the name of the server
 * - port is the number of the port the server uses to serve webpages
 * - path is a path relative to the document root
 *
 * Note that the colon followed by a port number are optional; the
 * port number defaults to 80 for http and to 443 for https. Also
 * note that the path is optional. If the path is omitted, the
 * document root of 'hostname' is implied.
 *
 * Examples:
 *
 * - http://www.example.com
 *
 * - https://www.example.com
 *
 * - http://www.example.com:81/web
 *
 * - https://www.example.com:443 (the portnumber is superfluous here)
 *
 * - http://www.example.com:80 (the portnumber is superfluous here)
 *
 * Default: (none)
 *
 *
 *  - $CFG->progdir is the absolute path to the program directory
 *
 * The main entry points (index.php, admin.php, etc.) are located in
 * $CFG->dir whereas all other program files are located in the directory
 * tree starting in $CFG->progdir.
 *
 * Usually this directory is a subdirectory of the document root, or more
 * precise: a subdirectory of $CFG->dir. The default name of this subdirectory
 * is 'program'.
 *
 * Default: '/home/httpd/htdocs/program'
 *
 *
 *  - $CFG->progwww is the URI which corresponds with the directory $CFG->progdir.
 *
 * This URI is also of the form 'scheme://hostname:port/path', see the
 * explanation for $CFG->www above.
 *
 * As a rule this URI is $CFG->www followed by the relative path 'program'.
 *
 * Important note:
 * If you select different schemes for $CFG->www and $CFG->progwww, the
 * browser of the website visitor may complain about mixing secure and insecure
 * resources on the same page. It is best to use either 'http' or 'https' and
 * not to mix both.
 *
 * Default: (none)
 *
 *
 *  - $CFG->datadir is the absolute path to a private directory outside the document root.
 *
 * This path points to a directory in which user documents are stored. This
 * directory must be writeable for the user account which runs the webserver
 * (often a user account named 'www-user' or 'www' or 'httpd' or 'apache').
 * This directory should NOT be accessible from the outside. Note that because
 * of this there  is no $CFG->datawww which corresponds to $CFG->datadir.
 * The data directory should be located outside the document root. All files
 * that need to be served from this directory tree will be served via the
 * program code in 'file.php' in the directory $CFG->dir.
 *
 * Note: some ISPs do not allow you to store data outside the document root.
 * In that case you could use a 'difficult' directory name under the document
 * root, e.g. '/home/httpd/htdocs/d3b07384d113edec49eaa6238ad5ff00', which
 * makes it harder to guess the name and directly access the files in this
 * directory via a browser.
 *
 * Default: (none)
 *
 *
 *  - $CFG->debug is a parameter to switch debugging ON
 *
 * Via this optional boolean variable debugging can be switched on. If the
 * parameter is not defined, it defaults to FALSE, see {@link init.php}.
 *
 * Default: (variable is not defined)
 *
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: config-example.php,v 1.3 2012/04/18 07:57:32 pfokker Exp $
 */
$CFG->db_type = 'mysql';
$CFG->db_server = 'localhost';
$CFG->db_username = 'wasuser';
$CFG->db_password = '53cr3t';
$CFG->db_name = 'was';
$CFG->prefix = 'was_';

$CFG->dir = '/home/httpd/htdocs';
$CFG->www = 'http://www.example.com';
$CFG->progdir = '/home/httpd/htdocs/program';
$CFG->progwww = 'http://www.example.com/program';
$CFG->datadir = '/home/httpd/data';

// $CFG->debug = FALSE

/* This file MUST end with a '?' followed by a '>'. Trailing end-of-line is NOT allowed! */
?>