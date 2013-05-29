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

/** /program/modules/redirect/languages/en/redirect.php - translated messages for module (English)
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_redirect
 * @version $Id: redirect.php,v 1.2 2013/05/29 15:25:26 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['title'] = 'Redirect';
$string['description'] = 'This module handles page redirection';
$string['translatetool_title'] = 'Redirect';
$string['translatetool_description'] = 'This file contains translations for the Redirect-module';

$comment['redirect_content_header'] = 'Here is the redirect configuration dialog:

~URL
~Target
[~Save] [~Cancel]

Please make sure your translation has a comparable set of hotkeys (indicated via the tildes \'~\').';

$string['redirect_content_header'] = 'Redirect configuration';
$string['redirect_content_explanation'] = 'Here you can configure the page redirection. Enter the URL of the (external) web page where you want the current page to redirect to. Optionally you can add a link target, e.g. use \'_blank\' to open the (external) web page in a new window.';
$string['link_href'] = '~URL';
$string['link_href_title'] = 'Enter the full URL of the external webpage to link to';
$string['link_target'] = '~Target';
$string['link_target_title'] = 'Enter the target, e.g. _blank for a new window (see manual)';
?>