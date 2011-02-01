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
# along with this program. If not, see http://websiteatschool.org/license.html

/** /program/lib/module.class.php - taking care of modules
 *
 * This file defines a base class for dealing with modules.
 * It is always included and it can be used as a base to inherit from.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.org/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: module.class.php,v 1.1 2011/02/01 13:00:10 pfokker Exp $
 * @todo we probably need to get rid of this file because it is not used (2010-12-07/PF)
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** Methods to access properties of a module
 */
class Module {
    /* @var array cached module record from modules table in database */
    var $module_record = NULL;

    /* @var int the node to which this module is linked */
    var $node_id = NULL;

    /**
     * @param array|null $module_record cached from modules table in database
     * @param int|null #node_id the node linked to this module
     * @return void
     */
    function Module($module_record=NULL,$node_id=NULL) {
        $this->module_record = $module_record;
        $this->node_id = $node_id;
    }

    /** get the actual content for node $node_id
     *
     * @param int $node_id identifies the node
     * @return string content that can be displayed straight away
     */
    function get_content() {
        return '';
    }

    /** get additional breadcrumb trail
     *
     * @param int $node_id identifies the node
     * @return array an array with 0 or more additional breadcrumbs
     */
    function get_breadcrumb_anchors() {
        $a = array();
        return $a;
    }
}

?>