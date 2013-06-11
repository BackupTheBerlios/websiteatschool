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

/** /program/modules/htmlpage/htmlpage.class.php - module for plain HTML-pages
 *
 * This file defines a class for dealing with plain HTML-pages.
 * It is derived from the base class Modules.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_htmlpage
 * @version $Id: htmlpage.class.php,v 1.4 2013/06/11 11:25:20 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

class ModuleHtmlpage extends Module {

    /** get the actual content for node $node_id
     *
     * @param int $node_id identifies the node
     * @return string content that can be displayed straight away
     */
    function get_content() {
        return "STUB: ModuleHtmlpage: get_content($node_id)\n";
    }
} // ModuleHtmlpage

?>