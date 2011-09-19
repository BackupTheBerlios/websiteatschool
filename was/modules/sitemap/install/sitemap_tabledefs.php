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

/** /program/modules/sitemap/install/sitemap_tabledefs.php - data definition for module
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_sitemap
 * @version $Id: sitemap_tabledefs.php,v 1.3 2011/09/19 10:06:52 pfokker Exp $
 */
$tabledefs['sitemaps'] = array(
    'name' => 'sitemaps',
    'comment' => 'main table for sitemap module stores sitemap configuration per node',
    'fields' => array(
        array(
            'name' => 'node_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'the node this sitemap is connected to'
            ),
        array(
            'name' => 'header',
            'type' => 'varchar',
            'length' => 240,
            'notnull' => TRUE,
            'comment' => 'the (optional) title to display at the top of the sitemap'
            ),
        array(
            'name' => 'introduction',
            'type' => 'text',
            'comment' => 'the (optional) introduction/explanation above the sitemap'
            ),
        array(
            'name' => 'scope',
            'type' => 'int',
            'default' => 0,
            'notnull' => TRUE,
            'comment' => 'scope of the map: 0=small, 1=medium, 2=large'
            ),
        array(
            'name' => 'ctime',
            'type' => 'datetime',
            'comment' => 'contains the time the sitemap was created'
            ),
        array(
            'name' => 'cuser_id',
            'type' => 'int',
            'comment' => 'identifies the user that created this sitemap'
            ),
        array(
            'name' => 'mtime',
            'type' => 'datetime',
            'comment' => 'contains the time the configuration was last updated'
            ),
        array(
            'name' => 'muser_id',
            'type' => 'int',
            'comment' => 'identifies the user that last modified the sitemap configuration'
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