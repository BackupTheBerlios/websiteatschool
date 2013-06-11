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

/** /program/modules/aggregator/install/aggregator_tabledefs.php - data definition for module
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_aggregator
 * @version $Id: aggregator_tabledefs.php,v 1.2 2013/06/11 11:25:17 pfokker Exp $
 */
$tabledefs['aggregator'] = array(
    'name' => 'aggregator',
    'comment' => 'main table for aggregator module stores aggregator configuration per node',
    'fields' => array(
        array(
            'name' => 'node_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'the node the aggregator is connected to'
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
            'comment' => 'the (optional) introduction/explanation at the top of the page'
            ),
        array(
            'name' => 'node_list',
            'type' => 'varchar',
            'length' => 240,
            'notnull' => TRUE,
            'comment' => 'a comma-delimted list of node_id\'s to aggregate'
            ),
        array(
            'name' => 'items',
            'type' => 'int',
            'default' => 0,
            'notnull' => TRUE,
            'comment' => 'the maximum number of aggregated items to show'
            ),
        array(
            'name' => 'reverse_order',
            'type' => 'bool',
            'notnull' => TRUE,
            'default' => FALSE,
            'comment' => 'if TRUE the items in a section are enumerated in reverse order'
            ),
        array(
            'name' => 'htmlpage_length',
            'type' => 'int',
            'default' => 1,
            'notnull' => TRUE,
            'comment' => 'the length of the htmlpage summary expressed in paragraphs'
            ),
        array(
            'name' => 'snapshots_width',
            'type' => 'int',
            'default' => 16,
            'notnull' => TRUE,
            'comment' => 'the available width for snapshots (in pixels)'
            ),
        array(
            'name' => 'snapshots_height',
            'type' => 'int',
            'default' => 16,
            'notnull' => TRUE,
            'comment' => 'the available height for snapshots (in pixels)'
            ),
        array(
            'name' => 'snapshots_visible',
            'type' => 'int',
            'default' => 1,
            'notnull' => TRUE,
            'comment' => 'the number of visible snapshots'
            ),
        array(
            'name' => 'snapshots_showtime',
            'type' => 'int',
            'default' => 5,
            'notnull' => TRUE,
            'comment' => 'the delay between snapshot changes (in seconds)'
            ),
        array(
            'name' => 'ctime',
            'type' => 'datetime',
            'comment' => 'contains the time the aggregator configuration  was created'
            ),
        array(
            'name' => 'cuser_id',
            'type' => 'int',
            'comment' => 'identifies the user that created this aggregator configuration'
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