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

/** /program/modules/htmlpage/htmlpage_search.php - interface to the search-part of the htmlpage module
 *
 * This file defines the interface with the htmlpage-module for searching content.
 * The interface consists of this function:
 *
 * <code>
 * htmlpage_search($keywords,$areas)
 * </code>
 *
 * This function is called whenever data is searched.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_htmlpage
 * @version $Id: htmlpage_search.php,v 1.2 2011/02/03 14:04:00 pfokker Exp $
 * @todo change this stub into a real search function, with limits on the
 *       number of results, an offset where to start and perhaps even a time limit.
 *       for now this always returns an empty array
 */
if (!defined('WASENTRY')) { die('no entry'); }


/** search the content of the htmlpage linked to node $node_id
 *
 * @param string|array $keywords one or more keywords to search for
 * @param int|array $areas one or more $area_id's to search
 * @return bool|array FALSE on failure, an array with search results on success
 */
function htmlpage_search($keywords,$areas) {
    echo "STUB: ".__FUNCTION__."()\n";
    $results = array();
    return $results;
} // htmlpage_search()

?>