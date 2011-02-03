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

/** /program/install/index.php - redirector for website installation
 *
 * The sole purpose of this file is to redirect users from
 * /program/install to /program/install.php. 
 * The latter is the main entry point for website installation.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasinstall
 * @version $Id: index.php,v 1.2 2011/02/03 14:04:03 pfokker Exp $
 */
$url = '../install.php';
header('Location: '.$url);
echo "<html>\n".
     "<head>\n".
     "  <title>redirect</title>\n".
     "</head>\n".
     "<body>\n".
     "  Redirect: <a href=\"$url\">".htmlspecialchars($url)."</a>\n";
     "  <p>$message\n".
     "</body>\n".
     "</html>\n";
?>