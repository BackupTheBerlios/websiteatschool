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

/** /program/modules/crew/install/crew_tabledefs.php - data definition for module
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_crew
 * @version $Id: crew_tabledefs.php,v 1.1 2013/05/30 15:38:21 pfokker Exp $
 */
$tabledefs['workshops'] = array(
    'name' => 'workshops',
    'comment' => 'main table for crew module stores the workshop data',
    'fields' => array(
        array(
            'name' => 'node_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'the node this workshop is connected to'
            ),
        array(
            'name' => 'header',
            'type' => 'varchar',
            'length' => 240,
            'notnull' => TRUE,
            'comment' => 'the (optional) title to display at the top of the visible page'
            ),
        array(
            'name' => 'introduction',
            'type' => 'text',
            'comment' => 'the (optional) introduction/explanation above the visible document'
            ),
        array(
            'name' => 'visibility',
            'type' => 'int',
            'default' => 0,
            'notnull' => TRUE,
            'comment' => 'visibility of the document: 0=workers, 1=accountholders, 2=world'
            ),
        array(
            'name' => 'document',
            'type' => 'longtext',
            'comment' => 'contains the plain text document created in the workshop'
            ),
        array(
            'name' => 'ctime',
            'type' => 'datetime',
            'comment' => 'contains the time the workshop was created'
            ),
        array(
            'name' => 'cuser_id',
            'type' => 'int',
            'comment' => 'identifies the user that created this workshop'
            ),
        array(
            'name' => 'mtime',
            'type' => 'datetime',
            'comment' => 'contains the time the document or configuration was last updated'
            ),
        array(
            'name' => 'muser_id',
            'type' => 'int',
            'comment' => 'identifies the user that last modified the document or configuration'
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
