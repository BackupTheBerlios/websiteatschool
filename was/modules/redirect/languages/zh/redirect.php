<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2012 Vereniging Website At School, Amsterdam, <info@websiteatschool.eu>
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

/** /program/modules/redirect/languages/zh/redirect.php
 *
 * Language: zh (中文)
 * Release:  0.90.3 / 2012041700 (2012-04-17)
 *
 * @author Liu Jing Fang <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2012 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_redirect
 * @version $Id: redirect.php,v 1.1 2012/05/31 19:23:54 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['title'] = 'Redirect';
$string['description'] = 'This module handles page redirection';
$string['translatetool_title'] = 'Redirect';
$string['translatetool_description'] = 'This file contains translations for the Redirect-module';
$string['redirect_content_header'] = 'Redirect configuration';
$string['redirect_content_explanation'] = 'Here you can configure the page redirection. Enter the URL of the (external) web page where you want the current page to redirect to. Optionally you can add a link target, e.g. use \'_blank\' to open the (external) web page in a new window.';
$string['link_href'] = '~URL';
$string['link_href_title'] = ' 输入外部网页的全部URL以链接到';
$string['link_target'] = '~目标';
$string['link_target_title'] = '输入目标, 例如 _将一个新窗口设为空 (见手册)';
?>