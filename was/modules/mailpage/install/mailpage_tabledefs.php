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

/** /program/modules/mailpage/install/mailpage_tabledefs.php - data definition for module
 *
 * Two tables are defined: mailpages and mailpages_addresses. The former contains
 * the configuration data for a mailpage (header, introduction, etc.) and the
 * latter is used to store 1 or more destination addresses. The records in both
 * tables are linked to the page via node_id. The sort order in the addresses
 * is determined by the sort_order field.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_mailpage
 * @version $Id: mailpage_tabledefs.php,v 1.1 2013/06/20 14:41:34 pfokker Exp $
 */
$tabledefs['mailpages'] = array(
    'name' => 'mailpages',
    'comment' => 'main table for mailpage module stores the configuration data',
    'fields' => array(
        array(
            'name' => 'node_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'the node this mailpage is connected to'
            ),
        array(
            'name' => 'header',
            'type' => 'varchar',
            'length' => 240,
            'notnull' => TRUE,
            'comment' => 'the (optional) title to display at the top of the page'
            ),
        array(
            'name' => 'introduction',
            'type' => 'text',
            'comment' => 'the (optional) introduction/explanation above the page'
            ),
        array(
            'name' => 'message',
            'type' => 'text',
            'comment' => 'the (optional) initial message, to use mailpage as a poor mans forms module'
            ),
        array(
            'name' => 'ctime',
            'type' => 'datetime',
            'comment' => 'contains the time the mailpage was created'
            ),
        array(
            'name' => 'cuser_id',
            'type' => 'int',
            'comment' => 'identifies the user that created this mailpage'
            ),
        array(
            'name' => 'mtime',
            'type' => 'datetime',
            'comment' => 'contains the time the configuration was last updated'
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
$tabledefs['mailpages_addresses'] = array(
    'name' => 'mailpages_addresses',
    'comment' => 'additional table for mailpage module stores the destination addresses',
    'fields' => array(
        array(
            'name' => 'mailpage_address_id',
            'type' => 'serial',
            'comment' => 'unique identification of a mailpage destination address'
            ),
        array(
            'name' => 'node_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'the mailpage node this address is connected to'
            ),
        array(
            'name' => 'name',
            'type' => 'varchar',
            'length' => 80,
            'comment' => 'the name of the destination, used as item in a listbox'
            ),
        array(
            'name' => 'sort_order',
            'type' => 'int',
            'notnull' => TRUE,
            'default' => 10,
            'comment' => 'this determines the order in which addresses are presented in the listbox'
            ),
        array(
            'name' => 'email',
            'type' => 'varchar',
            'length' => 255,
            'notnull' => TRUE,
            'comment' => 'the destination email address, never conveyed to the visitor/sender'
            ),
        array(
            'name' => 'description',
            'type' => 'varchar',
            'length' => 240,
            'comment' => 'the (optional) additional description about this destination address'
            ),
        array(
            'name' => 'thankyou',
            'type' => 'varchar',
            'length' => 240,
            'comment' => 'the (optional) thank-you-message displayed after submitting mail'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('mailpage_address_id')
            ),
        array(
            'name' => 'node_index',
            'type' => 'index',
            'fields' => array('node_id','sort_order','mailpage_address_id'),
            'comment' => 'hint for quick selections based on node_id'
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