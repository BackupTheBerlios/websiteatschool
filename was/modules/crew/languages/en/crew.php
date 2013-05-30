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

/** /program/modules/crew/languages/en/crew.php - translated messages for module (English)
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_crew
 * @version $Id: crew.php,v 1.1 2013/05/30 15:38:21 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$comment['title'] = 'Note to translators:
The original full name of this module  (\'Collaborative Remote Editor Workshop\')
is shortened to \'CREW\'. However, since this is much too long a name we
use the phrase \'Workshop (CREW)\' to refer to the module. If you are translating
the name of the module it may be best to translate the \'Workshop\' part and simply
leave the acronym \'CREW\' untranslated. Please feel free to add an explanation
once or twice to explain this acronym. Example: in Dutch a workshop is called
\'werkplaats\' so the translated title below could be \'Werkplaats (CREW)\'.
In the config_explanation below (number 6) you could add something like this:

\'Hier kunt u de module Werkplaats (CREW) (Engels:
Collaborative Remote Editor Workshop) configureren\'

to make the reader aware of the meaning of the acronym CREW.';

$string['title'] = 'Workshop (CREW)';
$string['description'] = 'This module provides Collaborative Remote Editor Workshops';
$string['translatetool_title'] = 'Workshop (CREW)';
$string['translatetool_description'] = 'This file contains translations for the Workshop (CREW) module';

$comment['config_header'] = 'Here is the main (global) configuration dialog:

~Origin
~Location
Secret ~key
[~Save] [~Cancel]

Please make sure your translation has a comparable set of hotkeys (indicated via the tildes \'~\').';

$string['config_header'] = 'Workshops (CREW) module configuration';
$string['config_explanation'] = 'Here you can configure the Workshops (CREW) module (CREW=Collaborative Remote Editor Workshop). The parameters below need to be set correctly otherwise it is not possible to use this module to create or edit documents using a workshop page.<dl>
<dt>Origin
<dd>This parameter MUST match the location of your website as seen in the browser of the website visitor.
<dt>Location
<dd>This URL MUST point to the CREW/Websocket server you are using for this module. This could be your own server, perhaps on a non-standard port 8008, e.g. <code>ws://www.yourserver.org:8008</code> or another server at standard port 80, e.g. <code>ws://www.somewhere.org</code>.
<dt>Secret key
<dd>This parameter MUST match the secrect code in the CREW/Websocket server you will be using. If this server is not under your control you have to negotiate with the owner of the server to obtain a valid secret code.
</dl>
Please consult the manual for more technical details.';

$string['config_origin_label'] = '~Origin';
$string['config_origin_title'] = 'This URL must match the origin as seen in the browser of the user';
$string['config_location_label'] = '~Location';
$string['config_location_title'] = 'This URL must point to a CREW/Websocket server (see manual)';
$string['config_secret_label'] = 'Secret ~key';
$string['config_secret_title'] = 'This shared secret code must match with the CREW/Websocket server';


$comment['crew_content_header'] = 'Here is the configuration dialog:

~Header
~Introduction
Visibility of the workshop document
[ ] ~World
[ ] ~Authenticated
[X] ~Individuals
[~Save] [~Cancel]

Please make sure your translation has a comparable set of hotkeys (indicated via the tildes \'~\').';

$string['crew_content_header'] = 'Workshop (CREW) configuration';
$string['crew_content_explanation'] = 'Here you can configure the workshop. You can add an optional
header and an optional introduction to the workshop page.
You can also change the visibility of the workshop.
Use one of the following options:
\'world\' to allow anonymous visitors to view the document,
\'authenticated\' to allow any logged-in user to view the document,
or \'individuals\' to grant access to specific users via the list below';
$string['header_label'] = '~Header';
$string['header_title'] = 'Header for the workshop';
$string['introduction_label'] = '~Introduction';
$string['introduction_title'] = 'Introduction text for workshop';
$string['visibility_label'] = 'Visibility of the workshop document';
$string['visibility_title'] = 'Select one of the options to set the visibility of the workshop document';
$string['visibility_world_label'] = '~World';
$string['visibility_world_title'] = 'Allow anonymous visitors to read the workshop document';
$string['visibility_all_label'] = '~Authenticated';
$string['visibility_all_title'] = 'Allow all logged-in users to read the workshop document';
$string['visibility_workers_label'] = '~Individuals';
$string['visibility_workers_title'] = 'Allow only individual users to read the workshop document';

$string['crew_acl_role_readonly_option'] = 'Read';
$string['crew_acl_role_readonly_title'] = 'Permission to read the document';
$string['crew_acl_role_readwrite_option'] = 'Read and edit';
$string['crew_acl_role_readwrite_title'] = 'Permission to read and edit the document';


?>