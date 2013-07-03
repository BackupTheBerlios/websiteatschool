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

/** /program/modules/mailpage/mailpage_search.php - interface to the search-part of the mailpage module
 *
 * This file defines the interface with the mailpage-module for searching content.
 * The interface consists of this function:
 *
 * <code>
 * mailpage_search($keywords,$areas)
 * </code>
 *
 * This function is called whenever data is searched.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_mailpage
 * @version $Id: mailpage_search.php,v 1.1 2013/06/20 14:41:34 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** search the content of the mailpage linked to node $node_id
 *
 * there is nothing to search in the mailpage so this routine is a nop
 * we return an empty array to indicate success (even though nothing was found)
 *
 * @param string|array $keywords one or more keywords to search for
 * @param int|array $areas one or more $area_id's to search
 * @return bool|array FALSE on failure, an array with search results on success
 */
function mailpage_search($keywords,$areas) {
    $results = array();
    return $results;
} // mailpage_search()

?>