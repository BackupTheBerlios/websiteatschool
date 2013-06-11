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

/** /program/modules/snapshots/install/snapshots_tabledefs.php - data definition for module
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_snapshots
 * @version $Id: snapshots_tabledefs.php,v 1.2 2013/06/11 11:25:36 pfokker Exp $
 */
$tabledefs['snapshots'] = array(
    'name' => 'snapshots',
    'comment' => 'main table for snapshots module stores snapshots configuration per node',
    'fields' => array(
        array(
            'name' => 'node_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'the node the snapshots are connected to'
            ),
        array(
            'name' => 'header',
            'type' => 'varchar',
            'length' => 240,
            'notnull' => TRUE,
            'comment' => 'the (optional) title to display at the top of every page'
            ),
        array(
            'name' => 'introduction',
            'type' => 'text',
            'comment' => 'the (optional) introduction/explanation above the thumbnails'
            ),
        array(
            'name' => 'snapshots_path',
            'type' => 'varchar',
            'length' => 240,
            'notnull' => TRUE,
            'comment' => 'snapshots directory relative to $CFG->datadir eg. areas/exemplum/snapshots'
            ),
        array(
            'name' => 'variant',
            'type' => 'int',
            'default' => 1,
            'notnull' => TRUE,
            'comment' => 'variant of the initial display: 1=introduction+thumbnails, 2=first image, 3=slideshow'
            ),
        array(
            'name' => 'dimension',
            'type' => 'int',
            'default' => 512,
            'notnull' => TRUE,
            'comment' => 'dimension of the box surrounding the full-size snapshots'
            ),
        array(
            'name' => 'ctime',
            'type' => 'datetime',
            'comment' => 'contains the time the snapshots configuration  was created'
            ),
        array(
            'name' => 'cuser_id',
            'type' => 'int',
            'comment' => 'identifies the user that created this snapshots configuration'
            ),
        array(
            'name' => 'mtime',
            'type' => 'datetime',
            'comment' => 'contains the time the configuration were last updated'
            ),
        array(
            'name' => 'muser_id',
            'type' => 'int',
            'comment' => 'identifies the user that last modified the configuration'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('node_id')
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