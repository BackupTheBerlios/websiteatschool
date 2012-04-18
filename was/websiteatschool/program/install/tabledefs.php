<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker <peter@berestijn.nl>
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

/** /program/install/tabledefs.php defines all core tables in a generic way
 *
 * This is the main data definition for Website@School. This file is used by
 * the installation script {@link install.php} to create all main tables.
 *
 * Here is a reminder for the allowed parameters for field- and key definitions.
 * <pre>
 * FIELDS   | name | type | len | dec | unsigned* | notnull | default | enum_values | comment |
 * ---------+------+------+-----+-----+-----------+---------+---------+-------------+---------+
 * serial*  | req  | req  |  -  |  -  |     -     |    -    |    -    |    -        |   opt   |
 * bool     | req  | req  |  -  |  -  |     -     |   opt   |   opt   |    -        |   opt   |
 * short    | req  | req  | opt |  -  |    opt    |   opt   |   opt   |    -        |   opt   |
 * int      | req  | req  | opt |  -  |    opt    |   opt   |   opt   |    -        |   opt   |
 * long     | req  | req  | opt |  -  |    opt    |   opt   |   opt   |    -        |   opt   |
 * float    | req  | req  | opt | opt |    opt    |   opt   |   opt   |    -        |   opt   |
 * double   | req  | req  | opt | opt |    opt    |   opt   |   opt   |    -        |   opt   |
 * decimal  | req  | req  | opt | opt |    opt    |   opt   |   opt   |    -        |   opt   |
 * number   | req  | req  | opt | opt |    opt    |   opt   |   opt   |    -        |   opt   |
 * varchar  | req  | req  | opt |  -  |     -     |   opt   |   opt   |    -        |   opt   |
 * enum     | req  | req  | opt |  -  |     -     |   opt   |   opt   |   req       |   opt   |
 * char     | req  | req  | opt |  -  |     -     |   opt   |   opt   |    -        |   opt   |
 * text     | req  | req  |  -  |  -  |     -     |   opt   |    -    |    -        |   opt   |
 * longtext | req  | req  |  -  |  -  |     -     |   opt   |    -    |    -        |   opt   |
 * blob     | req  | req  |  -  |  -  |     -     |   opt   |    -    |    -        |   opt   |
 * longblob | req  | req  |  -  |  -  |     -     |   opt   |    -    |    -        |   opt   |
 * date     | req  | req  |  -  |  -  |     -     |   opt   |   opt   |    -        |   opt   |
 * time     | req  | req  |  -  |  -  |     -     |   opt   |   opt   |    -        |   opt   |
 * datetime | req  | req  |  -  |  -  |     -     |   opt   |   opt   |    -        |   opt   |
 * timestamp| req  | req  |  -  |  -  |     -     |   opt   |   opt   |    -        |   opt   |
 * 
 * INDICES  | name | type | unique | fields | reftable | reffields | comment |
 * ---------+------+------+--------+--------+----------+-----------+---------+
 * primary  | -    | req  |   -    |  req   |    -     |     -     |   opt   |
 * index    | opt  | req  |  opt   |  req   |    -     |     -     |   opt   |
 * foreign  | opt  | req  |   -    |  req   |   req    |    req    |   opt   |
 * 
 * req = required, opt = optional, - = not allowed
 * </pre>
 *
 * <b>* Important note:<b>
 * The parameter 'unsigned' is a non-standard feature in MySQL. This means that
 * it is not portable so therefore it is now deprecated. This also applies to the
 * translation of the serial type, see also {@link mysql.class.php}. All references
 * to this feature have been removed in the tabledefs below.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasinstall
 * @version $Id: tabledefs.php,v 1.8 2012/04/18 07:57:34 pfokker Exp $
 * @todo automatically create appropriate sequence name for serial fields??? or add seqdefs too?
 */
if (!defined('WASENTRY')) { die('no entry'); }

if (!isset($tabledefs)) {
    $tabledefs = array();
}

/* Note:
 * The order of table definitions is important
 * because of foreign key constraints.
 * Currently the order is as follows:
 *
 * TABLE config
 * TABLE languages
 * TABLE acls
 * TABLE users
 *    FK REFERENCES languages
 *    FK REFERENCES acls
 * TABLE groups
 * TABLE sessions
 *    FK REFERENCES users
 * TABLE modules
 * TABLE themes
 * TABLE areas
 *    FK REFERENCES themes
 * TABLE nodes
 *    FK REFERENCES areas
 *    FK REFERENCES modules
 *    FK REFERENCES sessions
 * TABLE modules_properties
 *    FK REFERENCES modules
 * TABLE themes_properties
 *    FK REFERENCES themes
 * TABLE themes_areas_properties
 *    FK REFERENCES themes
 *    FK REFERENCES areas
 * TABLE login_failures
 * TABLE phrases
 *    FK REFERENCES languages
 * TABLE log_messages
 * TABLE alerts
 * TABLE alerts_areas_nodes
 *    FK REFERENCES alerts
 * TABLE acls_areas
 *    FK REFERENCES acls
 *    FK REFERENCES areas
 * TABLE acls_nodes
 *    FK REFERENCES acls
 *    FK REFERENCES nodes
 * TABLE acls_modules
 *    FK REFERENCES acls
 *    FK REFERENCES modules
 * TABLE acls_modules_areas
 *    FK REFERENCES acls
 *    FK REFERENCES modules
 *    FK REFERENCES areas
 * TABLE acls_modules_nodes
 *    FK REFERENCES acls
 *    FK REFERENCES modules
 *    FK REFERENCES nodes
 * TABLE users_properties
 *    FK REFERENCES users
 * TABLE groups_capacities
 *    FK REFERENCES acls
 * TABLE users_groups_capacities
 *    FK REFERENCES users
 *    FK REFERENCES groups
 *    FK REFERENCES groups_capacities
 */
$tabledefs['config'] = array(
    'name' => 'config',
    'comment' => 'global configuration parameters, these end up in the global CFG object',
    'fields' => array(
        array(
            'name' => 'name', 
            'type' => 'varchar', 
            'length' => 80,
            'notnull' => TRUE,
            'comment' => 'the name of the global configuration parameter'
            ),
        array(
            'name' => 'type',
            'type' => 'enum',
            'notnull' => TRUE,
            'default' => 's',
            'enum_values' => array('b','c','d','dt','f','i','l','r','s','t'),
            'length' => 2,
            'comment' => 'parameter type: b=bool, c=checklist, d=date, dt=date/time, f=float(double), i=int, l=list, r=radio, s=string, t=time'
            ),
        array(
            'name' => 'value', 
            'type' => 'text', 
            'notnull' => FALSE,
            'comment' => 'string representation of parameter value OR a comma-delimited list of values in case of a checklist'
            ),
        array(
            'name' => 'extra',
            'type' => 'text', 
            'notnull' => FALSE,
            'comment' => 'a semicolon-delimited list of name=value pairs with additional dialog/validation information, e.g. maxlength=80 or options=true,false,filenotfound'
            ),
        array(
            'name' => 'sort_order',
            'type' => 'int',
            'notnull' => TRUE,
            'default' => 10,
            'comment' => 'this determines the order in which parameters are presented when editing the configuration'
            ),
        array(
            'name' => 'description', 
            'type' => 'text', 
            'notnull' => FALSE,
            'comment' => 'an optional short explanation of the purpose of this parameter (in English, for internal use only)'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('name')
            )
        )
    );
$tabledefs['languages'] = array(
    'name' => 'languages',
    'comment' => 'this table holds all installed languages',
    'fields' => array(
        array(
            'name' => 'language_key',
            'type' => 'varchar',
            'length' => 20,
            'notnull' => TRUE,
            'comment' => 'this unique code identifies a particular language (see ISO 639-1:2002 and ISO 639-2:1998)'
            ),
        array(
            'name' => 'parent_language_key',
            'type' => 'varchar',
            'length' => 20,
            'notnull' => FALSE,
            'comment' => 'this code identifies the language that this language is derived from'
            ),
        array(
            'name' => 'language_name',
            'type' => 'varchar',
            'length' => 80,
            'notnull' => TRUE,
            'comment' => 'the name of the language expressed in the language itself (English, Nederlands, Deutsch, etc.)'
            ),
        array(
            'name' => 'version',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'the (internal) version of this translation, should match the code version in manifest file'
            ),
        array(
            'name' => 'manifest',
            'type' => 'varchar',
            'length' => 80,
            'notnull' => TRUE,
            'comment' => 'filename of script that describes the translation, usually <language>_manifest.php'
            ),
        array(
            'name' => 'is_core',
            'type' => 'bool',
            'notnull' => TRUE,
            'default' => FALSE,
            'comment' => 'if TRUE this language cannot be uninstalled, ie. it is a core-translation'
            ),
        array(
            'name' => 'is_active',
            'type' => 'bool',
            'notnull' => TRUE,
            'default' => TRUE,
            'comment' => 'only active languages can be used on a site'
            ),
        array(
            'name' => 'dialect_in_database',
            'type' => 'bool',
            'notnull' => TRUE,
            'default' => FALSE,
            'comment' => 'if TRUE, additional translations are searched for in the phrases table in the database'
            ),
        array(
            'name' => 'dialect_in_file',
            'type' => 'bool',
            'notnull' => TRUE,
            'default' => FALSE,
            'comment' => 'if TRUE, additional translations are searched for in the data directory'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('language_key')
            ),
        array(
            'name' => 'language_name',
            'type' => 'index',
            'fields' => array('language_name')
            )
        )
    );

$tabledefs['acls'] = array(
    'name' => 'acls',
    'comment' => 'access control lists at the highest level',
    'fields' => array(
        array(
            'name' => 'acl_id',
            'type' => 'serial',
            'comment' => 'unique identification of an access control list'
            ),
        array(
            'name' => 'permissions_jobs',
            'type' => 'int',
            'default' => 0,
            'notnull' => TRUE,
            'comment' => 'bitmapped permissions for administator jobs (access to admin.php)'
            ),
        array(
            'name' => 'permissions_intranet',
            'type' => 'int',
            'default' => 0,
            'notnull' => TRUE,
            'comment' => 'bitmapped permissions for all current and future private areas'
            ),
        array(
            'name' => 'permissions_modules',
            'type' => 'int',
            'default' => 0,
            'notnull' => TRUE,
            'comment' => 'bitmapped permissions for all current and future modules in all areas'
            ),
        array(
            'name' => 'permissions_nodes',
            'type' => 'int',
            'default' => 0,
            'notnull' => TRUE,
            'comment' => 'bitmapped permissions for all current and future nodes and areas (pagemanager)'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('acl_id')
            )
        )
    );

$tabledefs['users'] = array(
    'name' => 'users',
    'comment' => 'user accounts',
    'fields' => array(
        array(
            'name' => 'user_id',
            'type' => 'serial',
            'comment' => 'unique identification of the user'
            ),
        array(
            'name' => 'username',
            'type' => 'varchar',
            'length' => 60,
            'notnull' => TRUE,
            'comment' => 'the account name, must be unique too'
            ),
        array(
            'name' => 'salt',
            'type' => 'varchar',
            'length' => 255,
            'comment' => 'this is used to salt the password hash'
            ),
        array(
            'name' => 'password_hash',
            'type' => 'varchar',
            'length' => '255',
            'comment' => 'a hash of the combination of salt and the password'
            ),
        array(
            'name' => 'bypass_mode',
            'type' => 'bool',
            'notnull' => TRUE,
            'default' => FALSE,
            'comment' => 'used for forgotten passwords: FALSE=normal, TRUE=bypass'
            ),
        array(
            'name' => 'bypass_hash',
            'type' => 'varchar',
            'length' => '255',
            'comment' => 'random string (laissez_passer) or new password hash (bypass) in bypass mode'
            ),
        array(
            'name' => 'bypass_expiry',
            'type' => 'datetime',
            'notnull' => FALSE,
            'default' => 'NULL',
            'comment' => 'contains the time the laissez-passer or bypass expires'
            ),
        array(
            'name' => 'full_name',
            'type' => 'varchar',
            'length' => 255,
            'comment' => 'the first name, infix and last name of this user'
            ),
        array(
            'name' => 'email',
            'type' => 'varchar',
            'length' => 255,
            'comment' => 'the (primary) e-mail address of this user, used to handle \'forgotten password\''
            ),
        array(
            'name' => 'is_active',
            'type' => 'bool',
            'notnull' => TRUE,
            'default' => TRUE,
            'comment' => 'instead of deleting user accounts, they are made inactive',
            ),
        array(
            'name' => 'redirect',
            'type' => 'varchar',
            'length' => 255,
            'comment' => 'this is where the user goes to after logout, could be \'index.php\'',
            ), 
        array(
            'name' => 'language_key',
            'type' => 'varchar',
            'length' => 20,
            'comment' => 'preferred language'
            ),
        array(
            'name' => 'path',
            'type' => 'varchar',
            'length' => 60,
            'notnull' => TRUE,
            'comment' => 'the place (subdirectory) to store files for this user, relative to CFG->datadir/users'
            ),
        array(
            'name' => 'acl_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to acls table, provides access control for this user'
            ),
        /* 2012-04-06: this field replaced with field skin
         * array(
         *   'name' => 'high_visibility',
         *   'type' => 'bool',
         *   'notnull' => TRUE,
         *   'default' => FALSE,
         *   'comment' => 'in admin.php an additional stylesheet is included if this is TRUE',
         * ),
         */
        array(
            'name' => 'editor',
            'type' => 'varchar',
            'length' => 20,
            'comment' => 'preferred editor'
            ),
        array(
            'name' => 'skin',
            'type' => 'varchar',
            'length' => 20,
            'notnull' => TRUE,
            'default' => 'base',
            'comment' => 'preferred skin'
            )
       ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('user_id')
            ),
        array(
            'name' => 'username_index',
            'type' => 'index',
            'fields' => array('username'),
            'unique' => TRUE,
            'comment' => 'enforce unique usernames'
            ),
        array(
            'name' => 'path_index',
            'type' => 'index',
            'fields' => array('path'),
            'unique' => TRUE,
            'comment' => 'enforce unique datadirectories too'
            ),
        array(
            'name' => 'language',
            'type' => 'foreign',
            'fields' => array('language_key'),
            'reftable' => 'languages',
            'reffields' => array('language_key')
            ),
        array(
            'name' => 'acl',
            'type' => 'foreign',
            'fields' => array('acl_id'),
            'reftable' => 'acls',
            'reffields' => array('acl_id')
            )
        )
    );
$tabledefs['groups'] = array(
    'name' => 'groups',
    'comment' => 'groups are the basis for group/capacity-based access control lists',
    'fields' => array(
        array(
            'name' => 'group_id',
            'type' => 'serial',
            'comment' => 'unique identification of the group'
            ),
        array(
            'name' => 'groupname',
            'type' => 'varchar',
            'length' => 60,
            'notnull' => TRUE,
            'comment' => 'the short groupname, must be unique too'
            ),
        array(
            'name' => 'full_name',
            'type' => 'varchar',
            'length' => 255,
            'comment' => 'the full name/description of this group'
            ),
        array(
            'name' => 'is_active',
            'type' => 'bool',
            'notnull' => TRUE,
            'default' => TRUE,
            'comment' => 'groups can be made inactive in order to quickly revoke group-based permissions for all group members',
            ),
        array(
            'name' => 'path',
            'type' => 'varchar',
            'length' => 60,
            'notnull' => TRUE,
            'comment' => 'the place (subdirectory) to store files for this group, relative to CFG->datadir/groups'
            )
       ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('group_id')
            ),
        array(
            'name' => 'groupname_index',
            'type' => 'index',
            'fields' => array('groupname'),
            'unique' => TRUE,
            'comment' => 'enforce unique groupnames'
            ),
        array(
            'name' => 'path_index',
            'type' => 'index',
            'fields' => array('path'),
            'unique' => TRUE,
            'comment' => 'enforce unique datadirectories too'
            ),
        )
    );

$tabledefs['sessions'] = array(
    'name' => 'sessions',
    'comment' => 'this table keeps track of sessions with validated users',
    'fields' => array(
        array(
            'name' => 'session_id',
            'type' => 'serial',
            'comment' => 'unique identification of a session'
            ),
        array(
            'name' => 'session_key',
            'type' => 'varchar',
            'length' => 172,
            'default' => '',
            'comment' => 'contains the unique identifier (\'token\') which is stored in the user\'s cookie'
            ),
        array(
            'name' => 'session_data',
            'type' => 'longtext',
            'comment' => 'contains the serialised session data'
            ),
        array(
            'name' => 'user_id',
            'type' => 'int',
            'comment' => 'identifies with which user this session is associated'
            ),
        array(
            'name' => 'user_information',
            'type' => 'varchar',
            'length' => 255,
            'comment' => 'holds additional information about this session\'s user, .e.g. IP-address or name',
            ),
        array(
            'name' => 'ctime',
            'type' => 'datetime',
            'comment' => 'contains the time the session was created'
            ),
        array(
            'name' => 'atime',
            'type' => 'datetime',
            'comment' => 'contains the time the session was last accessed (used for time out)'
            ),
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('session_id')
            ),
        array(
            'name' => 'sessionkey',
            'type' => 'index',
            'fields' => array('session_key'),
            'unique' => TRUE
            ),
        array(
            'name' => 'loggedinuser',
            'type' => 'foreign',
            'fields' => array('user_id'),
            'reftable' => 'users',
            'reffields' => array('user_id')
            )        
        )
    );

$tabledefs['modules'] = array(
    'name' => 'modules',
    'comment' => 'this table holds the installed modules',
    'fields' => array(
        array(
            'name' => 'module_id',
            'type' => 'serial',
            'comment' => 'unique identification of installed modules'
            ),
        array(
            'name' => 'name',
            'type' => 'varchar',
            'length' => 80,
            'notnull' => TRUE,
            'comment' => 'the name of the module, used as a directory name in /program/modules/'
            ),
        array(
            'name' => 'version',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'the (internal) module version, should match the code version in manifest file'
            ),
        array(
            'name' => 'manifest',
            'type' => 'varchar',
            'length' => 80,
            'notnull' => TRUE,
            'comment' => 'filename of script that describes the theme, usually <name>_manifest.php'
            ),
        array(
            'name' => 'is_core',
            'type' => 'bool',
            'notnull' => TRUE,
            'default' => FALSE,
            'comment' => 'if TRUE this module cannot be uninstalled, ie. it is a core-module'
            ),
        array(
            'name' => 'is_active',
            'type' => 'bool',
            'notnull' => TRUE,
            'default' => TRUE,
            'comment' => 'only active modules can be used on a site'
            ),
        array(
            'name' => 'has_acls',
            'type' => 'bool',
            'notnull' => TRUE,
            'default' => FALSE,
            'comment' => 'TRUE means that this module uses acls to regulate access'
            ),
        array(
            'name' => 'view_script',
            'type' => 'varchar',
            'length' => 80,
            'comment' => 'this script contains code to display the content of the module to the website visitor'
            ),
        array(
            'name' => 'admin_script',
            'type' => 'varchar',
            'length' => 80,
            'comment' => 'this script handles module administration'
            ),
        array(
            'name' => 'search_script',
            'type' => 'varchar',
            'length' => 80,
            'comment' => 'this script handles searching through the content of this module'
            ),
        array(
            'name' => 'cron_script',
            'type' => 'varchar',
            'length' => 80,
            'comment' => 'this script handles recurring tasks in combination with cron_interval and cron_next'
            ),
        array(
            'name' => 'cron_interval',
            'type' => 'int',
            'comment' => 'minimum number of minutes between calls to this cronjob'
            ),
        array(
            'name' => 'cron_next',
            'type' => 'datetime',
            'comment' => 'when do we need to call the cronjob again'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('module_id')
            ),
        array(
            'name' => 'modulename_index',
            'type' => 'index',
            'unique' => TRUE,
            'fields' => array('name')
            )
        )
    );

$tabledefs['themes'] = array(
    'name' => 'themes',
    'comment' => 'this table holds the installed themes',
    'fields' => array(
        array(
            'name' => 'theme_id',
            'type' => 'serial',
            'comment' => 'unique identification of installed themes'
            ),
        array(
            'name' => 'name',
            'type' => 'varchar',
            'length' => 80,
            'notnull' => TRUE,
            'comment' => 'the name of the theme, used as a directory name in /program/themes/'
            ),
        array(
            'name' => 'version',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'the (internal) theme version, should match the code version in manifest file'
            ),
        array(
            'name' => 'manifest',
            'type' => 'varchar',
            'length' => 80,
            'notnull' => TRUE,
            'comment' => 'filename of script that describes the theme, usually <name>_manifest.php'
            ),
        array(
            'name' => 'is_core',
            'type' => 'bool',
            'notnull' => TRUE,
            'default' => FALSE,
            'comment' => 'if TRUE this theme cannot be uninstalled, ie. it is a core-theme'
            ),
        array(
            'name' => 'is_active',
            'type' => 'bool',
            'default' => TRUE,
            'notnull' => TRUE,
            'comment' => 'only active themes can be used on a site'
            ),
        array(
            'name' => 'class',
            'type' => 'varchar',
            'length' => 80,
            'comment' => 'the name of the class that needs to be instantiated'
            ),
        array(
            'name' => 'class_file',
            'type' => 'varchar',
            'length' => 80,
            'comment' => 'the name of the file that holds the class definition'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('theme_id')
            ),
        array(
            'name' => 'themename_index',
            'type' => 'index',
            'unique' => TRUE,
            'fields' => array('name')
            )
        )
    );

$tabledefs['areas'] = array(
    'name' => 'areas',
    'comment' => 'every area can be public, members-only or not accessible/closed',
    'fields' => array(
        array(
            'name' => 'area_id',
            'type' => 'serial'
            ),
        array(
            'name' => 'title',
            'type' => 'varchar',
            'length' => 240
            ),
        array(
            'name' => 'is_private',
            'type' => 'bool',
            'default' => FALSE,
            'notnull' => TRUE,
            'comment' => 'private means selected authenticated users only, otherwise public access'
            ),
        array(
            'name' => 'is_active',
            'type' => 'bool',
            'default' => TRUE,
            'notnull' => TRUE,
            'comment' => 'in general areas are not deleted but made inactive instead'
            ),
        array(
            'name' => 'is_default',
            'type' => 'bool',
            'default' => FALSE,
            'notnull' => TRUE,
            'comment' => 'if no area is requested explicitly or implicitly via a node, this is the area to use'
            ),
        array(
            'name' => 'path',
            'type' => 'varchar',
            'length' => 60,
            'notnull' => TRUE,
            'comment' => 'the place to store user uploaded files etc., relative to CFG->datadir/areas'
            ),
        array(
            'name' => 'metadata',
            'type' => 'text',
            'comment' => 'contains keywords for search engines'
            ),
        array(
            'name' => 'sort_order',
            'type' => 'int',
            'notnull' => TRUE,
            'default' => 10,
            'comment' => 'this determines the order in which areas are presented in picklists'
            ),
        array(
            'name' => 'theme_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'the area uses this presentation layer'
            ),
        array(
            'name' => 'ctime',
            'type' => 'datetime',
            'comment' => 'when was this area originally created'
            ),
        array(
            'name' => 'cuser_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'identifies the user that created this area'
            ),
        array(
            'name' => 'mtime',
            'type' => 'datetime',
            'comment' => 'when was this area last modified'
            ),
        array(
            'name' => 'muser_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'identifies the user that last modified this area'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('area_id')
            ),
        array(
            'name' => 'path_index',
            'type' => 'index',
            'fields' => array('path'),
            'unique' => TRUE,
            'comment' => 'enforce unique data directories'
            ),
        array(
            'name' => 'theme',
            'type' => 'foreign',
            'fields' => array('theme_id'),
            'reftable' => 'themes',
            'reffields' => array('theme_id')
            )
        )
    );

$tabledefs['nodes'] = array(
    'name' => 'nodes',
    'comment' => 'the navigation structure of an area',
    'fields' => array(
        array(
            'name' => 'node_id',
            'type' => 'serial',
            'comment' => 'this number is used externally to refer to a page within the site'
            ),
        array(
            'name' => 'area_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'this identifies the area to which this node belongs. every node MUST have an area'
            ),
        array(
            'name' => 'parent_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'the parent of this node or node_id itself if this is a toplevel node in this area'
            ),
        array(
            'name' => 'is_page',
            'type' => 'bool',
            'default' => FALSE,
            'notnull' => TRUE,
            'comment' => 'pages have content (like files), other nodes are just for navigation (like directories)'
            ),
        array(
            'name' => 'is_default',
            'type' => 'bool',
            'default' => FALSE,
            'notnull' => TRUE,
            'comment' => 'this identifies the node that is displayed/used as a tree starting point when no node is requested explicitly'
            ),
        array(
            'name' => 'title',
            'type' => 'varchar',
            'length' => 240,
            'notnull' => FALSE,
            'comment' => 'displayed in the HTML page header and advisory text in the navigation structure'
            ),
        array(
            'name' => 'link_text',
            'type' => 'varchar',
            'length' => 240,
            'comment' => 'text displayed in navigation structure (if no link_image) or alt-tag (if link-image)'
            ),
        array(
            'name' => 'link_image',
            'type' => 'varchar',
            'length' => 240,
            'notnull' => FALSE,
            'comment' => 'if not null, this image file is displayed in the navigation structure'
            ),
        array(
            'name' => 'link_image_width',
            'type' => 'int',
            'comment' => 'advisory value for visitor\'s browser'
            ),
        array(
            'name' => 'link_image_height',
            'type' => 'int',
            'comment' => 'advisory value for visitor\'s browser'
            ),
        array(
            'name' => 'link_target',
            'type' => 'varchar',
            'length' => 80,
            'comment' => 'if not null this is the frame name or _self, _parent, _top or _blank'
            ),
        array(
            'name' => 'link_href',
            'type' => 'varchar',
            'length' => 240,
            'comment' => 'if not null this is an external link, otherwise it looks like "index.php?page=node_id"'
            ),
        array(
            'name' => 'is_hidden',
            'type' => 'bool',
            'notnull' => TRUE,
            'default' => FALSE,
            'comment' => 'if hidden, suppress links to this node in navigation'
            ),
        array(
            'name' => 'embargo',
            'type' => 'datetime',
            'notnull' => TRUE,
            'default' => '1000-01-01 00:00:00',
            'comment' => 'deny existence of a node if the current date is before embargo'
            ),
        array(
            'name' => 'expiry',
            'type' => 'datetime',
            'notnull' => TRUE,
            'default' => '9999-12-31 23:59:59',
            'comment' => 'deny existence of a node if the current date is after expiry'
            ),
        array(
            'name' => 'sort_order',
            'type' => 'int',
            'notnull' => TRUE,
            'default' => 10,
            'comment' => 'this determines the order in which nodes are presented in navigation'
            ),
        array(
            'name' => 'ctime',
            'type' => 'datetime',
            'comment' => 'when was this node originally created'
            ),
        array(
            'name' => 'mtime',
            'type' => 'datetime',
            'comment' => 'when was this node last modified'
            ),
        array(
            'name' => 'atime',
            'type' => 'datetime',
            'comment' => 'when was this node last accessed/viewed'
            ),
        array(
            'name' => 'view_count',
            'type' => 'int',
            'notnull' => TRUE,
            'default' => 0,
            'comment' => 'this counts the number of times this node was retrieved by a visitor'
            ),
        array(
            'name' => 'owner_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'this identifies the owner (presumably the author too) of this node'
            ),
        array(
            'name' => 'is_readonly',
            'type' => 'bool',
            'notnull' => TRUE,
            'default' => FALSE,
            'comment' => 'if this flag is set, the content cannot be edited, not even by the owner'
            ),
        array(
            'name' => 'module_id',
            'type' => 'int',
            'notnull' => FALSE,
            'default' => NULL,
            'comment' => 'this connects to the module generating actual node content; NULL for sections'
            ),
        array(
            'name' => 'auxiliary',
            'type' => 'int',
            'comment' => 'this helps in mass-updating selected nodes in an area'
            ),
        array(
            'name' => 'locked_since',
            'type' => 'datetime',
            'notnull' => FALSE,
            'default' => 'NULL',
            'comment' => 'if not NULL the time when was this node locked'
            ),
        array(
            'name' => 'locked_by_session_id',
            'type' => 'int',
            'notnull' => FALSE,
            'default' => 'NULL',
            'comment' => 'if not NULL this fields indicates which session_id has locked this record'
            ),
        array(
            'name' => 'style', 
            'type' => 'text', 
            'notnull' => TRUE,
            'comment' => 'additional style information to add AFTER static and area-level style'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('node_id')
            ),
        array(
            'name' => 'area_index',
            'type' => 'index',
            'fields' => array('area_id'),
            'comment' => 'hint for quick selections based on area_id'
            ),
        array(
            'name' => 'area',
            'type' => 'foreign',
            'fields' => array('area_id'),
            'reftable' => 'areas',
            'reffields' => array('area_id')
            ),
        array(
            'name' => 'module',
            'type' => 'foreign',
            'fields' => array('module_id'),
            'reftable' => 'modules',
            'reffields' => array('module_id')
            ),
        array(
            'name' => 'locked',
            'type' => 'foreign',
            'fields' => array('locked_by_session_id'),
            'reftable' => 'sessions',
            'reffields' => array('session_id')
            )
        )
    );

$tabledefs['modules_properties'] = array(
    'name' => 'modules_properties',
    'comment' => 'default values of modules',
    'fields' => array(
        array(
            'name' => 'module_property_id',
            'type' => 'serial',
            'comment' => 'unique identification of the property'
            ),
        array(
            'name' => 'module_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'this property belongs to this module, link to modules table'
            ),
        array(
            'name' => 'name', 
            'type' => 'varchar', 
            'length' => 80, 
            'notnull' => TRUE,
            'comment' => 'the name of the configuration parameter'
            ),
        array(
            'name' => 'type',
            'type' => 'enum',
            'notnull' => TRUE,
            'default' => 's',
            'enum_values' => array('b','c','d','dt','f','i','l','r','s','t'),
            'length' => 2,
            'comment' => 'parameter type: b=bool, c=checklist, d=date, dt=date/time, f=float(double), i=int, l=list, r=radio, s=string, t=time'
            ),
        array(
            'name' => 'value', 
            'type' => 'text', 
            'notnull' => FALSE,
            'comment' => 'string representation of parameter value OR a comma-delimited list of values in case of a checklist'
            ),
        array(
            'name' => 'extra',
            'type' => 'text', 
            'notnull' => FALSE,
            'comment' => 'a semicolon-delimited list of name=value pairs with additional dialog/validation information, e.g. maxlength=80 or options=true,false,filenotfound'
            ),
        array(
            'name' => 'sort_order',
            'type' => 'int',
            'notnull' => TRUE,
            'default' => 10,
            'comment' => 'this determines the order in which parameters are presented when editing the configuration'
            ),
        array(
            'name' => 'description', 
            'type' => 'text', 
            'notnull' => FALSE,
            'comment' => 'an optional short explanation of the purpose of this parameter (in English, for internal use only)'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('module_property_id')
            ),
        array(
            'name' => 'module_index',
            'type' => 'index',
            'fields' => array('module_id'),
            'comment' => 'hint for quick selections based on module_id'
            ),
        array(
            'name' => 'module',
            'type' => 'foreign',
            'fields' => array('module_id'),
            'reftable' => 'modules',
            'reffields' => array('module_id')
            )
        )
    );

$tabledefs['themes_properties'] = array(
    'name' => 'themes_properties',
    'comment' => 'default values of themes',
    'fields' => array(
        array(
            'name' => 'theme_property_id',
            'type' => 'serial',
            'comment' => 'unique identification of the property'
            ),
        array(
            'name' => 'theme_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'this property belongs to this theme, link to themes table'
            ),
        array(
            'name' => 'name', 
            'type' => 'varchar', 
            'length' => 80, 
            'notnull' => TRUE,
            'comment' => 'the name of the configuration parameter'
            ),
        array(
            'name' => 'type',
            'type' => 'enum',
            'notnull' => TRUE,
            'default' => 's',
            'enum_values' => array('b','c','d','dt','f','i','l','r','s','t'),
            'length' => 2,
            'comment' => 'parameter type: b=bool, c=checklist, d=date, dt=date/time, f=float(double), i=int, l=list, r=radio, s=string, t=time'
            ),
        array(
            'name' => 'value', 
            'type' => 'text', 
            'notnull' => FALSE,
            'comment' => 'string representation of parameter value OR a comma-delimited list of values in case of a checklist'
            ),
        array(
            'name' => 'extra',
            'type' => 'text', 
            'notnull' => FALSE,
            'comment' => 'a semicolon-delimited list of name=value pairs with additional dialog/validation information, e.g. maxlength=80 or options=true,false,filenotfound'
            ),
        array(
            'name' => 'sort_order',
            'type' => 'int',
            'notnull' => TRUE,
            'default' => 10,
            'comment' => 'this determines the order in which parameters are presented when editing the configuration'
            ),
        array(
            'name' => 'description', 
            'type' => 'text', 
            'notnull' => FALSE,
            'comment' => 'an optional short explanation of the purpose of this parameter (in English, for internal use only)'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('theme_property_id')
            ),
        array(
            'name' => 'theme_index',
            'type' => 'index',
            'fields' => array('theme_id'),
            'comment' => 'hint for quick selections based on theme_id'
            ),
        array(
            'name' => 'theme',
            'type' => 'foreign',
            'fields' => array('theme_id'),
            'reftable' => 'themes',
            'reffields' => array('theme_id')
            )
        )
    );

$tabledefs['themes_areas_properties'] = array(
    'name' => 'themes_areas_properties',
    'comment' => 'properties of themes / areas',
    'fields' => array(
        array(
            'name' => 'theme_area_property_id',
            'type' => 'serial',
            'comment' => 'unique identification of the property'
            ),
        array(
            'name' => 'theme_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to themes table'
            ),
        array(
            'name' => 'area_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to areas table'
            ),
        array(
            'name' => 'name', 
            'type' => 'varchar', 
            'length' => 80,
            'notnull' => TRUE,
            'comment' => 'the name of the configuration parameter'
            ),
        array(
            'name' => 'type',
            'type' => 'enum',
            'notnull' => TRUE,
            'default' => 's',
            'enum_values' => array('b','c','d','dt','f','i','l','r','s','t'),
            'length' => 2,
            'comment' => 'parameter type: b=bool, c=checklist, d=date, dt=date/time, f=float(double), i=int, l=list, r=radio, s=string, t=time'
            ),
        array(
            'name' => 'value', 
            'type' => 'text', 
            'notnull' => FALSE,
            'comment' => 'string representation of parameter value OR a comma-delimited list of values in case of a checklist'
            ),
        array(
            'name' => 'extra',
            'type' => 'text', 
            'notnull' => FALSE,
            'comment' => 'a semicolon-delimited list of name=value pairs with additional dialog/validation information, e.g. maxlength=80 or options=true,false,filenotfound'
            ),
        array(
            'name' => 'sort_order',
            'type' => 'int',
            'notnull' => TRUE,
            'default' => 10,
            'comment' => 'this determines the order in which parameters are presented when editing the configuration'
            ),
        array(
            'name' => 'description', 
            'type' => 'text', 
            'notnull' => FALSE,
            'comment' => 'an optional short explanation of the purpose of this parameter (in English, for internal use only)'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('theme_area_property_id')
            ),
        array(
            'name' => 'theme_index',
            'type' => 'index',
            'fields' => array('theme_id'),
            'comment' => 'hint for quick selections based on theme_id'
            ),
        array(
            'name' => 'area_index',
            'type' => 'index',
            'fields' => array('area_id'),
            'comment' => 'hint for quick selections based on area_id'
            ),
        array(
            'name' => 'theme',
            'type' => 'foreign',
            'fields' => array('theme_id'),
            'reftable' => 'themes',
            'reffields' => array('theme_id')
            ),
        array(
            'name' => 'area',
            'type' => 'foreign',
            'fields' => array('area_id'),
            'reftable' => 'areas',
            'reffields' => array('area_id')
            )
        )
    );

$tabledefs['login_failures'] = array(
    'name' => 'login_failures',
    'comment' => 'a table to remember failed login attempts and temporary blacklistings',
    'fields' => array(
        array(
            'name' => 'login_failure_id',
            'type' => 'serial',
            'comment' => 'unique identification of the failed attempt'
            ),
        array(
            'name' => 'remote_addr',
            'type' => 'varchar',
            'length' => 150,
            'notnull' => TRUE,
            'comment' => 'IP-address of the visitor that failed the login attempt/is blocked'
            ),
        array(
            'name' => 'datim',
            'type' => 'datetime',
            'comment' => 'the date/time of the failure OR the expiry date/time of the blacklisting'
            ),
        array(
            'name' => 'failed_procedure',
            'type' => 'int',
            'notnull' => TRUE,
            'default' => 0,
            'comment' => 'indicates which procedure failed'
            ),
        array(
            'name' => 'points',
            'type' => 'int',
            'notnull' => TRUE,
            'default' => 0,
            'comment' => '0=do not count this failure (anymore), 1=do count this failure'
            ),
        array(
            'name' => 'username',
            'type' => 'varchar',
            'length' => 255,
            'comment' => 'additional information about failed attempt'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('login_failure_id')
            ),
        array(
            'name' => 'remote_addr_datim',
            'type' => 'index',
            'fields' => array('remote_addr','datim'),
            'comment' => 'hint for quick selections based on remote_addr and datim'
            ),
        )
    );

$tabledefs['phrases'] = array(
    'name' => 'phrases',
    'comment' => 'this table can contain localised (dialect) phrases in addition to static language files',
    'fields' => array(
        array(
            'name' => 'phrase_id',
            'type' => 'serial',
            'comment' => 'unique identification of a phrase'
            ),
        array(
            'name' => 'language_key',
            'type' => 'varchar',
            'length' => 20,
            'notnull' => TRUE,
            'comment' => 'this language code links to the languages table'
            ),
        array(
            'name' => 'domain',
            'type' => 'varchar',
            'length' => 80,
            'notnull' => TRUE,
            'comment' => 'this indicates to which text domain this phrase belongs, could be a module or a theme'
            ),
        array(
            'name' => 'phrase_key',
            'type' => 'varchar',
            'length' => 80,
            'notnull' => TRUE,
            'comment' => 'this uniquely identifies a phrase within a particular language and domain'
            ),
        array(
            'name' => 'phrase_text',
            'type' => 'text',
            'length' => 80,
            'notnull' => TRUE,
            'comment' => 'this is the translation of the phrase'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('phrase_id')
            ),
        array(
            'name' => 'language_domain_phrase',
            'type' => 'index',
            'fields' => array('language_key','domain','phrase_key'),
            'unique' => TRUE
            ),
        array(
            'name' => 'language',
            'type' => 'foreign',
            'fields' => array('language_key'),
            'reftable' => 'languages',
            'reffields' => array('language_key')
            )        
        )
    );

$tabledefs['log_messages'] = array(
    'name' => 'log_messages',
    'comment' => 'this table contains log messages, much like a syslog file',
    'fields' => array(
        array(
            'name' => 'log_message_id',
            'type' => 'serial',
            'comment' => 'unique identification of a log message'
            ),
        array(
            'name' => 'datim',
            'type' => 'datetime',
            'comment' => 'the date/time of the event'
            ),
        array(
            'name' => 'remote_addr',
            'type' => 'varchar',
            'length' => 150,
            'notnull' => TRUE,
            'comment' => 'IP-address of the visitor'
            ),
        array(
            'name' => 'priority',
            'type' => 'int',
            'notnull' => TRUE,
            'default' => 6,
            'comment' => 'numeric value of the message priority (e.g. LOG_INFO = 6), see man syslog'
            ),
        array(
            'name' => 'user_id',
            'type' => 'int',
            'comment' => 'identifies with which user this event is associated (if any)'
            ),
        array(
            'name' => 'message',
            'type'  => 'text',
            'comment' => 'a free format string describing the event'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('log_message_id')
            ),
        )
    );

$tabledefs['alerts'] = array(
    'name' => 'alerts',
    'comment' => 'table with alert accounts, accumulate alert messages in preparation for sending email alerts',
    'fields' => array(
        array(
            'name' => 'alert_id',
            'type' => 'serial',
            'comment' => 'unique identification of alerts'
            ),
        array(
            'name' => 'full_name',
            'type' => 'varchar',
            'length' => 255,
            'comment' => 'the first name, infix and last name of the person to receive alerts (optional)'
            ),
        array(
            'name' => 'email',
            'type' => 'varchar',
            'length' => 255,
            'comment' => 'the (primary) e-mail address of the person to receive alerts'
            ),
        array(
            'name' => 'cron_interval',
            'type' => 'int',
            'comment' => 'minimum number of minutes between sending messages to this alert'
            ),
        array(
            'name' => 'cron_next',
            'type' => 'datetime',
            'comment' => 'when do we need to consider processing this alert again'
            ),
        array(
            'name' => 'messages',
            'type' => 'int',
            'notnull' => TRUE,
            'default' => 0,
            'comment' => 'the number of accumulated messages in the message buffer'
            ),
        array(
            'name' => 'message_buffer',
            'type' => 'text',
            'comment' => 'contains the accumulated alerts'
            ),
        array(
            'name' => 'is_active',
            'type' => 'bool',
            'notnull' => TRUE,
            'default' => TRUE,
            'comment' => 'alerts can be made inactive',
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('alert_id')
            )
        ),
    );

$tabledefs['alerts_areas_nodes'] = array(
    'name' => 'alerts_areas_nodes',
    'comment' => 'indicates which email alerts to send when a node and/or area is modified',
    'fields' => array(
        array(
            'name' => 'alert_area_node_id',
            'type' => 'serial',
            'comment' => 'unique identification of this alert-area-node combination'
            ),
        array(
            'name' => 'alert_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to alerts table'
            ),
        array(
            'name' => 'area_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'an area_id OR 0 (meaning: all areas)'
            ),
        array(
            'name' => 'node_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'a node_id OR 0 (meaning: all nodes)'
            ),
        array(
            'name' => 'flag',
            'type' => 'bool',
            'default' => FALSE,
            'notnull' => TRUE,
            'comment' => 'if TRUE a change in this node and/or area should yield an alert'
            )
       ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('alert_area_node_id')
            ),
        array(
            'name' => 'alert',
            'type' => 'foreign',
            'fields' => array('alert_id'),
            'reftable' => 'alerts',
            'reffields' => array('alert_id')
            )
        )
    );

$tabledefs['acls_areas'] = array(
    'name' => 'acls_areas',
    'comment' => 'access control lists at the area level',
    'fields' => array(
        array(
            'name' => 'acl_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to acls table'
            ),
        array(
            'name' => 'area_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to areas table'
            ),
        array(
            'name' => 'permissions_intranet',
            'type' => 'int',
            'default' => 0,
            'notnull' => TRUE,
            'comment' => 'bitmapped permissions for this (private) area'
            ),
        array(
            'name' => 'permissions_modules',
            'type' => 'int',
            'default' => 0,
            'notnull' => TRUE,
            'comment' => 'bitmapped permissions for all current and future modules in this area'
            ),
        array(
            'name' => 'permissions_nodes',
            'type' => 'int',
            'default' => 0,
            'notnull' => TRUE,
            'comment' => 'bitmapped permissions for all current and future nodes in this area (pagemanager)'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('acl_id','area_id')
            ),
        array(
            'name' => 'acl',
            'type' => 'foreign',
            'fields' => array('acl_id'),
            'reftable' => 'acls',
            'reffields' => array('acl_id')
            ),
        array(
            'name' => 'area',
            'type' => 'foreign',
            'fields' => array('area_id'),
            'reftable' => 'areas',
            'reffields' => array('area_id')
            )
        )
    );

$tabledefs['acls_nodes'] = array(
    'name' => 'acls_nodes',
    'comment' => 'access control lists at the node level',
    'fields' => array(
        array(
            'name' => 'acl_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to acls table'
            ),
        array(
            'name' => 'node_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to nodes table'
            ),
        array(
            'name' => 'permissions_modules',
            'type' => 'int',
            'default' => 0,
            'notnull' => TRUE,
            'comment' => 'bitmapped permissions for all current and future modules in this node'
            ),
        array(
            'name' => 'permissions_nodes',
            'type' => 'int',
            'default' => 0,
            'notnull' => TRUE,
            'comment' => 'bitmapped permissions for this node (pagemanager)'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('acl_id','node_id')
            ),
        array(
            'name' => 'acl',
            'type' => 'foreign',
            'fields' => array('acl_id'),
            'reftable' => 'acls',
            'reffields' => array('acl_id')
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

$tabledefs['acls_modules'] = array(
    'name' => 'acls_modules',
    'comment' => 'access control lists for modules at site level',
    'fields' => array(
        array(
            'name' => 'acl_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to acls table'
            ),
        array(
            'name' => 'module_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to modules table'
            ),
        array(
            'name' => 'permissions_modules',
            'type' => 'int',
            'default' => 0,
            'notnull' => TRUE,
            'comment' => 'bitmapped permissions for this module in all current and future nodes and areas'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('acl_id','module_id')
            ),
        array(
            'name' => 'acl',
            'type' => 'foreign',
            'fields' => array('acl_id'),
            'reftable' => 'acls',
            'reffields' => array('acl_id')
            ),
        array(
            'name' => 'module',
            'type' => 'foreign',
            'fields' => array('module_id'),
            'reftable' => 'modules',
            'reffields' => array('module_id')
            )

        )
    );

$tabledefs['acls_modules_areas'] = array(
    'name' => 'acls_modules_areas',
    'comment' => 'access control lists for modules at area level',
    'fields' => array(
        array(
            'name' => 'acl_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to acls table'
            ),
        array(
            'name' => 'module_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to modules table'
            ),
        array(
            'name' => 'area_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to areas table'
            ),
        array(
            'name' => 'permissions_modules',
            'type' => 'int',
            'default' => 0,
            'notnull' => TRUE,
            'comment' => 'bitmapped permissions for this module in all current and future nodes in this area'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('acl_id','module_id','area_id')
            ),
        array(
            'name' => 'acl',
            'type' => 'foreign',
            'fields' => array('acl_id'),
            'reftable' => 'acls',
            'reffields' => array('acl_id')
            ),
        array(
            'name' => 'module',
            'type' => 'foreign',
            'fields' => array('module_id'),
            'reftable' => 'modules',
            'reffields' => array('module_id')
            ),
        array(
            'name' => 'area',
            'type' => 'foreign',
            'fields' => array('area_id'),
            'reftable' => 'areas',
            'reffields' => array('area_id')
            )
        )
    );

$tabledefs['acls_modules_nodes'] = array(
    'name' => 'acls_modules_nodes',
    'comment' => 'access control lists for modules at node level',
    'fields' => array(
        array(
            'name' => 'acl_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to acls table'
            ),
        array(
            'name' => 'module_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to modules table'
            ),
        array(
            'name' => 'node_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to nodes table'
            ),
        array(
            'name' => 'permissions_modules',
            'type' => 'int',
            'default' => 0,
            'notnull' => TRUE,
            'comment' => 'bitmapped permissions for this module in this node'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('acl_id','module_id','node_id')
            ),
        array(
            'name' => 'acl',
            'type' => 'foreign',
            'fields' => array('acl_id'),
            'reftable' => 'acls',
            'reffields' => array('acl_id')
            ),
        array(
            'name' => 'module',
            'type' => 'foreign',
            'fields' => array('module_id'),
            'reftable' => 'modules',
            'reffields' => array('module_id')
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

$tabledefs['users_properties'] = array(
    'name' => 'users_properties',
    'comment' => 'provides users properties grouped per section',
    'fields' => array(
        array(
            'name' => 'user_property_id',
            'type' => 'serial',
            'comment' => 'unique identification of the user-property combination'
            ),
        array(
            'name' => 'user_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to users table'
            ),
        array(
            'name' => 'section',
            'type' => 'varchar',
            'length' => 80,
            'notnull' => TRUE,
            'comment' => 'keeps related properties grouped together, e.g. in a separate tab'
            ),
        array(
            'name' => 'name', 
            'type' => 'varchar', 
            'length' => 80, 
            'notnull' => TRUE,
            'comment' => 'the name of the configuration parameter'
            ),
        array(
            'name' => 'type',
            'type' => 'enum',
            'notnull' => TRUE,
            'default' => 's',
            'enum_values' => array('b','c','d','dt','f','i','l','r','s','t'),
            'length' => 2,
            'comment' => 'parameter type: b=bool, c=checklist, d=date, dt=date/time, f=float(double), i=int, l=list, r=radio, s=string, t=time'
            ),
        array(
            'name' => 'value', 
            'type' => 'text', 
            'notnull' => FALSE,
            'comment' => 'string representation of parameter value OR a comma-delimited list of values in case of a checklist'
            ),
        array(
            'name' => 'extra',
            'type' => 'text', 
            'notnull' => FALSE,
            'comment' => 'a semicolon-delimited list of name=value pairs with additional dialog/validation information, e.g. maxlength=80 or options=true,false,filenotfound'
            ),
        array(
            'name' => 'sort_order',
            'type' => 'int',
            'notnull' => TRUE,
            'default' => 10,
            'comment' => 'this determines the order in which parameters are presented when editing the configuration'
            ),
        array(
            'name' => 'description', 
            'type' => 'text', 
            'notnull' => FALSE,
            'comment' => 'an optional short explanation of the purpose of this parameter (in English, for internal use only)'
            )
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('user_property_id')
            ),
        array(
            'name' => 'user_index',
            'type' => 'index',
            'fields' => array('user_id'),
            'comment' => 'hint for quick selections based on user_id'
            ),
        array(
            'name' => 'user',
            'type' => 'foreign',
            'fields' => array('user_id'),
            'reftable' => 'users',
            'reffields' => array('user_id')
            )
        )
    );

$tabledefs['groups_capacities'] = array(
    'name' => 'groups_capacities',
    'comment' => 'access control lists are assigned to combinations of groups and capacity',
    'fields' => array(
        array(
            'name' => 'group_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to groups table'
            ),
        array(
            'name' => 'capacity_code',
            'type' => 'int',
            'default' => 0,
            'notnull' => TRUE,
            'comment' => 'well-known capacity codes include 0=nonmember, 1=pupil and 2=teacher'
            ),
        array(
            'name' => 'sort_order',
            'type' => 'int',
            'notnull' => TRUE,
            'default' => 10,
            'comment' => 'this determines the order in which capacities are presented when editing the configuration'
            ),
        array(
            'name' => 'acl_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to acls table'
            )
       ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('group_id','capacity_code')
            ),
        array(
            'name' => 'acl',
            'type' => 'foreign',
            'fields' => array('acl_id'),
            'reftable' => 'acls',
            'reffields' => array('acl_id')
            )
        )
    );

$tabledefs['users_groups_capacities'] = array(
    'name' => 'users_groups_capacities',
    'comment' => 'this table establishes the group memberships of users and the exact capacity (0 means nonmember)',
    'fields' => array(
        array(
            'name' => 'user_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to user table'
            ),
        array(
            'name' => 'group_id',
            'type' => 'int',
            'notnull' => TRUE,
            'comment' => 'link to groups table'
            ),
        array(
            'name' => 'capacity_code',
            'type' => 'int',
            'default' => 0,
            'notnull' => TRUE,
            'comment' => 'well-known capacity codes include 0=nonmember, 1=teacher and 2=pupil'
            )
       ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('user_id','group_id')
            ),
        array(
            'name' => 'group_index',
            'type' => 'index',
            'fields' => array('group_id'),
            'comment' => 'hint for quick selections based on group_id'
            ),
        array(
            'name' => 'user',
            'type' => 'foreign',
            'fields' => array('user_id'),
            'reftable' => 'users',
            'reffields' => array('user_id')
            ),
        array(
            'name' => 'group_fk',
            'type' => 'foreign',
            'fields' => array('group_id'),
            'reftable' => 'groups',
            'reffields' => array('group_id'),
            'comment' => 'group is a reserved word hence the name group_fk'
            ),
        array(
            'name' => 'groupcapacity',
            'type' => 'foreign',
            'fields' => array('group_id','capacity_code'),
            'reftable' => 'groups_capacities',
            'reffields' => array('group_id','capacity_code')
            )
        ),
    );

?>