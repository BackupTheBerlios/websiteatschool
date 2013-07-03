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

/** /program/modules/mailpage/languages/en/mailpage.php - translated messages for module (English)
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_mailpage
 * @version $Id: mailpage.php,v 1.6 2013/07/03 12:55:17 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['title'] = 'Mailpage';
$string['description'] = 'This module allows visitors to send a message';
$string['translatetool_title'] = 'Mailpage';
$string['translatetool_description'] = 'This file contains translations for the Mailpage-module';

$comment['mailpage_content_header'] = 'Here is the mailpage configuration dialog:

_Link to add a new address_
_Link to address 1 (10)_
_Link to address 2 (20)_
...
~Header
~Introduction
Default ~message
[~Save] [~Cancel]

Please make sure your translation has a comparable set of hotkeys (indicated via the tildes \'~\').';
$string['mailpage_content_header'] = 'Mailpage configuration';
$string['mailpage_content_explanation'] =
'Here you can configure the Mailpage module.
Use the links below to add a new destination
address or edit or delete an existing destination
address. You MUST configure at least one destination
address for this module to work.
<p>You can add an optional header and an optional
introduction to the mailpage. Also, it is possible
to enter a default message. The visitor will see
this default message in the message field. This allows for a
simple way to let the visitor answer multiple
questions in a single message.';
$string['add_new_address_label'] = 'Add a new destination address';
$string['add_new_address_title'] = 'Use this link to add a new destination address to end of the list.';
$string['edit_address_label'] = '{NAME} ({SORT_ORDER})';
$string['edit_address_title'] = 'Edit or delete destination {ADDRESS_ID}: <{EMAIL}>';
$string['header_label'] = '~Header';
$string['header_title'] = 'Header for the mailpage';
$string['introduction_label'] = '~Introduction';
$string['introduction_title'] = 'Introduction text for the mailpage';
$string['default_message_label'] = 'Default ~message';
$string['default_message_title'] = 'Initial text for message from the visitor';

$comment['mailpage_add_address_header'] = 'Here is the dialog for editing a single destination address:

Header (add or edit)
Explanation (add or edit)
~Name
~E-mail address
~Descr~iption
~Thankyou
Sort ~order
[~Save] [~Cancel] [~Delete]

Please make sure your translation has a comparable set of hotkeys (indicated via the tildes \'~\').';
$string['mailpage_add_address_header'] = 'Add new destination address';
$string['mailpage_add_address_explanation'] =
'Here you can enter the details of the new destination addresses.
You MUST add at least one destination address for this module to work.';
$string['mailpage_edit_address_header'] = 'Edit or delete destination address';
$string['mailpage_edit_address_explanation'] =
'Here you can modify or delete destination addresses.
You MUST configure at least one destination address for this module to work.';
$string['address_name_label'] = '~Name';
$string['address_name_title'] = 'The name of this destination';
$string['address_email_label'] = '~E-mail address';
$string['address_email_title'] = 'The e-mail address of this destination';
$string['address_description_label'] = 'Descr~iption';
$string['address_description_title'] = 'This text is displayed when the visitor selects this destination';
$string['address_thankyou_label'] = '~Thank-you text';
$string['address_thankyou_title'] = 'This text is displayed after the visitor submits a message to this destination';
$string['address_sort_order_label'] = 'Sort ~order';
$string['address_sort_order_title'] = 'Destinations are displayed in the order determined by this number';
$string['error_saving_data'] = 'Error saving data';
$string['error_deleting_data'] = 'Error deleting data';

$comment['error_retrieving_config'] = 'Visitor translations start here';
$string['error_retrieving_config'] = 'Error: cannot retrieve configuration data';
$string['error_retrieving_addresses'] = 'Error: no destination addresses for page {NODE}';
$string['error_retrieving_data'] = 'Error: could not retrieve data';
$string['error_token_expired'] = 'Error: the mailpage form timed out, please try again';
$string['error_storing_data'] = 'Error: could not save data';
$string['error_too_fast'] = 'Error: the server is no able to accept your message at this time. Please try again in a minute';
$string['error_sending_message'] = 'Error: message could not be sent, please try again';
$string['error_creating_token'] = 'Error: no token available for page {NODE}';

$comment['destination_label'] = 
'Here is the definition of the mailform as the visitor sees it.

Header
In tro duc tion.
~Destination: <listbox> (but suppressed if there is only one)
~Name:
~E-mail:
~Subject:
~Message:
[~Preview] [~Cancel]

Please make sure your translation has a comparable set of hotkeys (indicated via the tildes \'~\').';
$string['destination_label'] = '~Destination';
$string['destination_title'] = 'Select the destination address for your message';
$string['fullname_label'] = '~Name (required)';
$string['fullname_title'] = 'Please enter your name';
$string['email_label'] = '~E-mail (required)';
$string['email_title'] = 'Please enter your e-mail address';
$string['subject_label'] = '~Subject';
$string['subject_title'] = 'Add a subject for your message';
$string['message_label'] = '~Message (required)';
$string['message_title'] = 'Enter your nessage here';
$string['button_preview'] = '~Preview';
$string['button_send'] = '~Send';
$string['cancelled'] = 'Cancelled';

$comment['preview_header'] = 'Here is the text for the Preview. There are three buttons in this dialog:
[~Edit] [~Send] [~Cancel]
';
$string['preview_header'] = 'Preview';
$string['from'] = 'From';
$string['to'] = 'To';
$string['subject'] = 'Subject';
$string['message'] = 'Message';
$string['date'] = 'Date';
$string['ip_addr'] = 'IP-address';

$comment['subject_line'] = 'The subject line can contain these parameters: {NODE}, {IP_ADDR}, {SUBJECT}';
$string['subject_line'] = '[{NODE}] Message from {IP_ADDR}: {SUBJECT}';
$string['thankyou_header'] = 'Message has been sent';
$string['here_is_a_copy'] = 'Here is a copy of your message.';

?>