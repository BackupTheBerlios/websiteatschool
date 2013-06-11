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

/** /program/modules/htmlpage/install/htmlpage_tabledefs.php - data definition for module
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_htmlpage
 * @version $Id: htmlpage_tabledefs.php,v 1.5 2013/06/11 11:25:21 pfokker Exp $
 */
$tabledefs['htmlpages'] = array(
    'name' => 'htmlpages',
    'comment' => 'main table for htmlpage module stores actual htmlpage data',
    'fields' => array(
        array(
            'name' => 'htmlpage_id',
            'type' => 'serial'
            ),
        array(
            'name' => 'node_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'the node this data is connected to'
            ),
        array(
            'name' => 'version',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'the version of the content is incremented on every save'
            ),
        array(
            'name' => 'page_data',
            'type' => 'longtext',
            'comment' => 'contains the HTML-code for this page (the content)'
            ),
        array(
            'name' => 'ctime',
            'type' => 'datetime',
            'comment' => 'contains the time the page was created'
            ),
        array(
            'name' => 'cuser_id',
            'type' => 'int',
            'comment' => 'identifies the user that created this page'
            ),
        array(
            'name' => 'mtime',
            'type' => 'datetime',
            'comment' => 'contains the time the page data was last updated'
            ),
        array(
            'name' => 'muser_id',
            'type' => 'int',
            'comment' => 'identifies the user that last modified this page'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('htmlpage_id')
            ),
        array(
            'name' => 'node',
            'type' => 'foreign',
            'fields' => array('node_id'),
            'reftable' => 'nodes',
            'reffields' => array('node_id')
            )
        )
    );
?>