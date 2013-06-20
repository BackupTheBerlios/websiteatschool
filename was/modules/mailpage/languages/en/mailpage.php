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
 * @version $Id: mailpage.php,v 1.1 2013/06/20 14:41:35 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['title'] = 'Mailpage';
$string['description'] = 'This module allows visitors to send a message';
$string['translatetool_title'] = 'Mailpage';
$string['translatetool_description'] = 'This file contains translations for the Mailpage-module';

$comment['mailpage_content_header'] = 'Here is the mailpage configuration dialog:

~Header
~Introduction

Name 1
Address 1
Description 1
Thankyou 1
Sort order 1
...
Name N
Address N
Description N
Thankyou N
Sort order N

Default ~message
[~Save] [~Cancel]

Please make sure your translation has a comparable set of hotkeys (indicated via the tildes \'~\').

Note that the fields Name,...,Sort order will be repeated N times (with N=1,2,3,...) so it is
not possible to give every field their own tilde. It may be best to place a tilde just before
the first number, e.g. "Name ~{INDEX}" which would yield Name ~1 for the first name,
Name ~2 for the second, and so on. In that way the user can jump to every set of addresses
with a hotkey (assuming there are no more than 9 addresses). I think that that is better
than nothing at all even if one cannot jump to the other fields (Address,Description,...)';

$string['mailpage_content_header'] = 'Mailpage configuration';
$string['mailpage_content_explanation'] = 'Here you can configure the mailpage module.
You can add an optional header and an optional introduction to the mailpage.
The fields \'Name\', \'E-mail address\', \'Description\', \'Thank-you text\' and \'Sort order\'
define a destination for the visitor\'s message. You MUST at least enter one destination.
It is possible to add another destination by entering the appropriate details in the empty
fields at the bottom. You can change the sort order by changing the numbers in the \'Sort order\'
fields. A destination can be deleted by removing the corresponding \'Name\' and saving the changes.
Finally, it is possible to enter a default message. The visitor will see this text in
the message field. This allows for a simple way to let the visitor answer multiple
questions in a single message.';

$string['header_label'] = '~Header';
$string['header_title'] = 'Header for the mailpage';
$string['introduction_label'] = '~Introduction';
$string['introduction_title'] = 'Introduction text for the mailpage';
$string['name_label'] = 'Name ~{INDEX}';
$string['name_title'] = 'The name of this destination';
$string['sort_order_label'] = 'Sort order {INDEX}';
$string['sort_order_title'] = 'Destinations are displayed in the order determined by this number';
$string['email_label'] = 'E-mail address {INDEX}';
$string['email_title'] = 'The e-mail address of this destination';
$string['description_label'] = 'Description {INDEX}';
$string['description_title'] = 'This text is displayed when the visitor selects this destination';
$string['thankyou_label'] = 'Thank-you text {INDEX}';
$string['thankyou_title'] = 'Text displayed after the visitor submits the message to this destination';
$string['message_label'] = 'Default ~message';
$string['message_title'] = 'Initial text for message from the visitor';
$string['error_saving_data'] = 'Error saving data';
?>