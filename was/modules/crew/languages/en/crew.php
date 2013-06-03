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
 * @version $Id: crew.php,v 1.2 2013/06/03 16:14:24 pfokker Exp $
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
$string['description'] = 'This module provides Collaborative Remote Editor Workshop';
$string['translatetool_title'] = 'Workshop (CREW)';
$string['translatetool_description'] = 'This file contains translations for the Workshop (CREW) module';

$comment['config_header'] = 'Here is the main (global) configuration dialog:

~Origin
~Location
Secret ~key
[~Save] [~Cancel]

Please make sure your translation has a comparable set of hotkeys (indicated via the tildes \'~\').';
$string['config_header'] = 'Workshop (CREW) module configuration';
$string['config_explanation'] = 'Here you can configure the Workshop (CREW) module
(CREW=Collaborative Remote Editor Workshop). The
parameters below need to be set correctly otherwise
it is not possible to use this module to create or
edit documents using a workshop page. Please consult
the manual for technical details.';
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
$string['visibility_all_title'] = 'Allow logged-in users to read the workshop document';
$string['visibility_workers_label'] = '~Individuals';
$string['visibility_workers_title'] = 'Allow only individual users to read the workshop document';

$string['crew_acl_role_readonly_option'] = 'Read';
$string['crew_acl_role_readonly_title'] = 'Permission to read the document';
$string['crew_acl_role_readwrite_option'] = 'Read and edit';
$string['crew_acl_role_readwrite_title'] = 'Permission to read and edit the document';


$string['crew_view_access_denied'] = 'Sorry, you currently have no permissions to view this page';
$string['last_updated_by'] = 'Last updated: {DATIM} by {FULL_NAME} ({USERNAME})';

$string['skin_label'] = 'Skin';
$string['skin_title'] = 'Please select a skin to use for this session';
$string['skin_standard_option'] = 'Base';
$string['skin_standard_title'] = 'Default skin';
$string['skin_bw_option'] = 'LowVision';
$string['skin_bw_title'] = 'High contrast (black+white) interface';
$string['skin_rb_option'] = 'Red Grey Blue';
$string['skin_rb_title'] = 'Primary colours interface';
$string['skin_by_option'] = 'Mondrian';
$string['skin_by_title'] = 'Black and yellow';

$string['crew_requires_js_and_ws'] = 'Sorry, this module requires JavaScript and the WebSocket protocol';

$comment['crew_button_save'] = 'Below are the translations for the JavaScript-part of CREW.
If you want to see these prompts in action you have to have
a working Websocket server and a properly configured
installation of Website@School.';
$string['crew_button_save'] = 'Save';
$string['crew_button_save_title'] = 'Save the text and end the editing session';
$string['crew_button_saveedit'] = "Save+Edit";
$string['crew_button_saveedit_title'] = 'Save the text and continue the editing session';
$string['crew_button_cancel'] = 'Cancel';
$string['crew_button_cancel_title'] = 'Discard the changes and end the editing session';
$string['crew_button_refresh'] = 'Refresh';
$string['crew_button_refresh_title'] = 'Retrieve a fresh copy of the current version of text';
$string['crew_button_send'] = 'Send';
$string['crew_button_send_title'] = 'Send an instant message to all participants';
$comment['crew_button_sound'] = 'This parameter intentionally left blank because the corresponding button is graphical';
$string['crew_button_sound'] = '';
$string['crew_button_sound_title'] = 'Press the button to switch the sound on or off';
$string['crew_js_websocket_not_supported'] = 'WebSocket protocol not supported';
$string['crew_js_initialised'] = 'INITIALISED';
$string['crew_js_connected'] = 'CONNECTED';
$string['crew_js_disconnected_clean'] = 'DISCONNECTED (clean): code={CODE} reason={REASON}';
$string['crew_js_disconnected_unclean'] = 'DISCONNECTED (unclean): code={CODE} reason={REASON}';
$string['crew_js_unknown_msg'] = '{ORIGIN}: unknown message: {DATA}';
$string['crew_js_error'] = 'ERROR: {DATA}';
$string['crew_js_save_characters'] = 'SAVE ({LENGTH} characters)';
$string['crew_js_saveedit_characters'] = 'SAVE ({LENGTH} characters) + EDIT';
$string['crew_js_cancel_characters'] = 'CANCEL ({LENGTH} characters)';
$string['crew_js_sound_off'] = 'SOUND OFF';
$string['crew_js_sound_on'] = 'SOUND ON';
$string['crew_js_enters_workshop'] = '{NAME} ({NICK}) enters the workshop';
$string['crew_js_leaves_workshop'] = '{NAME} ({NICK}) leaves the workshop';
$string['crew_js_malformed_message'] = 'ERROR: malformed message {DATA}';
$string['crew_js_unloading'] = 'UNLOADING';
$string['crew_js_authenticating'] = 'AUTHENTICATING {NAME} ({NICK})';
$string['crew_js_error_relocate'] = 'INTERNAL ERROR: relocate parameters n={N} and users={USERS} differ';
$string['crew_js_error_patchcount'] = 'INTERNAL ERROR: less than {N} patch parameters: {COUNT} ({DATA})';
$string['crew_js_error_context'] = 'INTERNAL ERROR: context {N} missing: {OLD} {NEW}';
$string['crew_js_error_usercount'] = 'INTERNAL ERROR: patch n={N} and users={USERS}';



?>