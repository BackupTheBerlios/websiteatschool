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

/** /program/install/languages/en/demodata.php - translated messages for /program/install/demodata.php (English)
 *
 * This file holds the English texts that are used in the part of the installer
 * that inserts the demonstaration data.  It is the basis for all other language files.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasinstall
 * @version $Id: demodata.php,v 1.6 2013/07/02 20:24:45 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['translatetool_title'] = 'Demodata';
$string['translatetool_description'] = 'This file contains translations of the demonstration data';

$string['error'] = 'Error in demodata: ';

$string['groupname_faculty'] = 'faculty';
$string['full_name_faculty'] = 'Members of staff';
$string['groupname_team'] = 'team';
$string['full_name_team'] = 'All employees';
$string['groupname_seniors'] = 'seniors';
$string['full_name_seniors'] = 'Pupils of grade 5 to 8';
$string['groupname_juniors'] = 'juniors';
$string['full_name_juniors'] = 'Pupils of grade 1 to 4';

$string['public_area_title'] = 'Exemplum Primary School';
$string['private_area_title'] = 'Exemplum Intranet';
$string['extra_area_title'] = 'Exemplum Inactive';

$string['public_area_path'] = 'exemplum';
$string['private_area_path'] = 'intranet';
$string['extra_area_path'] = 'inactive';

$string['alerts_initial_load'] = 'Initial installation of demodata, including this test message.';
$string['alerts_every_1440_minutes'] = 'You will receive an alert at most once every 1440 minutes (1 day).';
$string['alerts_every_60_minutes'] = 'You will receive an alert at most once every 60 minutes (1 hour).';
$string['alerts_email_address'] = 'Alerts will be mailed to this address:';
$string['alerts_all_areas'] = 'Alerts will trigger on a change in any area.';
$string['alerts_private_area'] = 'Alerts will only be sent for changes on the intranet';

$string['welcome_title'] = 'Welcome to our website';
$string['welcome_link_text'] = 'Welcome';
$string['schoolinfo_title'] = 'This section contains school information';
$string['schoolinfo_link_text'] = 'School info';
$string['aboutus_title'] = 'Information about the school';
$string['aboutus_link_text'] = 'About us';
$string['schoolterms_title'] = 'School terms and bank holidays for {SCHOOLYEAR}';
$string['schoolterms_link_text'] = '{SCHOOLYEAR}';
$string['news_title'] = 'This section holds news and newsletters';
$string['news_link_text'] = 'News';
$string['latestnews_title'] = 'Extra! Extra! Read all about it!';
$string['latestnews_link_text'] = 'Latest news';
$string['latestnewsletter_title'] = 'This is the latest issue of our newsletter';
$string['latestnewsletter_link_text'] = 'Newsletter';
$string['newsarchive_title'] = 'This section contains the news and newsletter archives';
$string['newsarchive_link_text'] = 'Archives';
$string['oldnews_title'] = 'Old newsitems';
$string['oldnews_link_text'] = 'Old news';
$string['oldnewsletters_title'] = 'Older issues of our newsletter';
$string['oldnewsletters_link_text'] = 'Old newsletters';
$string['search_title'] = 'Here you can search our site';
$string['search_link_text'] = 'Search';
$string['searchbox_title'] = 'Search our site';
$string['searchbox_link_text'] = 'Search';
$string['sitemap_title'] = 'Overview of the site';
$string['sitemap_link_text'] = 'Sitemap';
$string['mypage_title'] = 'Login/logout and jump menu';
$string['mypage_link_text'] = 'MyPage';
$string['quicktop_title'] = 'Permanent links at the top';
$string['quicktop_link_text'] = '(quicklinks top)';
$string['about_title'] = 'About our school';
$string['about_link_text'] = 'about';
$string['contact_title'] = 'How to contact us';
$string['contact_link_text'] = 'contact';
$string['quickbottom_title'] = 'Permanent links at the bottom';
$string['quickbottom_link_text'] = '(quicklinks bottom)';
$string['disclaimer_title'] = 'Disclaimer';
$string['disclaimer_link_text'] = 'disclaimer';
$string['login_title'] = 'Use this link to login';
$string['login_link_text'] = 'login';

$string['intranet_title'] = 'Welcome to the intranet';
$string['intranet_link_text'] = 'Intranet';
$string['meetings_title'] = 'This section holds the meeting schedule and the minutes';
$string['meetings_link_text'] = 'Meetings';
$string['roster_title'] = 'This is the meeting schedule';
$string['roster_link_text'] = 'Roster';
$string['minutes_title'] = 'Meeting minutes ({SCHOOLYEAR})';
$string['minutes_link_text'] = 'Minutes {SCHOOLYEAR}';
$string['minutes1_title'] = 'Minutes of the first meeting';
$string['minutes1_link_text'] = 'Summer';
$string['minutes2_title'] = 'Minutes of the second meeting';
$string['minutes2_link_text'] = 'Fall';
$string['minutes3_title'] = 'Minutes of the third meeting';
$string['minutes3_link_text'] = 'Winter';
$string['minutes4_title'] = 'Minutes of the fourth meeting';
$string['minutes4_link_text'] = 'Spring';
$string['downloads_title'] = 'A list of handy links and downloads';
$string['downloads_link_text'] = 'Downloads';

$string['welcome_content'] = 'Welcome at the Exemplum Primary School website.<br>
Please use the menu to navigate or use the MyPage jump menu.
<p>{LOREM} {IPSUM}
<p>{DOLOR} {SIT}
';
$comment['aboutus_content'] = 'Note: the name and address of this example-school are carefully chosen (just like all other example-names used in this program). Perhaps it is best to stick to it and leave it as-is (untranslated), specifically the name \'Amelia Cackle\'.';
$string['aboutus_content'] = 'Exemplum Primary School<br>
1, Rock Bottom street<br>
Gummersbach<br>
Principal: Amelia Cackle';
$string['schoolterms1_content'] = '<h2>School terms and bank holidays for {LAST_SCHOOLYEAR}</h2>
Term 1: September - October<br>
Term 2: November - December<br>
Winter holiday: last week of December<br>
Term 3: January - February<br>
Term 4: March - April<br>
Spring break: second week of April<br>
Term 5: April - May<br>
Term 6: June - July<br>
Summer holiday: August<br>
';
$string['schoolterms2_content'] = '<h2>School terms and bank holidays for {THIS_SCHOOLYEAR}</h2>
Term 1: September - October<br>
Term 2: November - December<br>
Winter holiday: last week of December+first week of January<br>
Term 3: January - February<br>
Term 4: March - April<br>
Spring break: third week of April<br>
Term 5: April - May<br>
Term 6: June - July<br>
Summer holiday: August<br>
';
$string['schoolterms3_content'] = '<h2>Preliminary school terms and bank holidays for {NEXT_SCHOOLYEAR}</h2>
Term 1: September - October<br>
Term 2: November - December<br>
Winter holiday: last two weeks of December<br>
Term 3: January - February<br>
Term 4: March - April<br>
Spring break: second week of April<br>
Term 5: April - May<br>
Term 6: June - July<br>
Summer holiday: August<br>
<br>
Note: this is a <em>preliminary</em> schedule.
';
$string['latestnews_content'] = '<strong>{TODAY}</strong><br>
We are happy to inform you that the new website is now operational.';
$string['latestnewsletter_content'] = '<h2>Newsletter 5 ({TODAY})</h2>
<h3>Contents</h3>
<ul>
<li>From the principal
<li>New teacher
<li>Plans for {THIS_SCHOOLYEAR}
</ul>
<h3>From the principal</h3>
{LOREM} {IPSUM}
<p>{DOLOR} {SIT}
<p>Amelia Cackle
<h3>New teacher</h3>
We are happy to announce that at the start
of schoolyear {NEXT_SCHOOLYEAR} miss Mary Astell
will take care of our youngest pupils.
<h3>Plans for {THIS_SCHOOLYEAR}</h3>
{SIT} {DOLOR} {IPSUM} {LOREM}
';
$string['oldnews_content'] = '<strong>{YESTERDAY}</strong><br>{LOREM}<p>
<strong>{LAST_WEEK}</strong><br>{IPSUM}<p>
<strong>{MONTHS_AGO_1}</strong><br>{DOLOR}<p>
<strong>{MONTHS_AGO_2}</strong><br>{SIT}
';
$string['oldnewsletters_content'] = '
<a href="#"><h2>Newsletter 4 ({MONTHS_AGO_1})</h2></a>
<ul><li>From the principal<li>Upcoming events<li>Did you know...?</ul>
<a href="#"><h2>Newsletter 3 ({MONTHS_AGO_2})</h2></a>
<ul><li>From the principal<li>Upcoming events<li>Art-classes for Juniors</ul>
<a href="#"><h2>Newsletter 2 ({MONTHS_AGO_3})</h2></a>
<ul><li>From the principal<li>Upcoming events<li>Excursion for Seniors</ul>
<a href="#"><h2>Newsletter 1 ({MONTHS_AGO_4})</h2></a>
<ul><li>From the principal<li>Upcoming events<li>Did you know...?</ul>
';
$string['searchbox_content'] = 'Placeholder for the search module';
$string['mypage_content'] = 'Really a placeholder for the mypage module.<p>
<p>
Useful links:
<ul>
<li><a href="{INDEX_URL}">index.php</a>
<li><a href="{ADMIN_URL}">admin.php</a>
<li><a href="{MANUAL_URL}" target="_blank">Website@School Manual</a> (opens in a new window)
<li><a href="{WEBSITEATSCHOOL_URL}">Website@School Website</a>
</ul>
<form method="POST" action="{INDEX_URL}?login=1" name="loginform">
Username:<br>
<input type="text" name="login_username" value="" size="25" maxlength="80" class="textfield">
<p>
Password:<br>
<input type="password" name="login_password" value="" size="25" maxlength="80" class="passwordfield" autocomplete="off">
<p>
<input type="submit" name="button" value="OK" class="button">
</form>
<p>
<a href="{INDEX_URL}?logout=1"><strong>Logout</strong></a>
';
$string['sitemap_content'] = 'Placeholder for the sitemap module';
$string['about_content'] = 'Here comes some information about the school.';
$string['contact_content'] = 'Placeholder for the mail module';
$string['contact_name1'] = 'Principal';
$string['contact_description1'] = 'Please send all your educational questions to our principal, Amelia Cackle';
$string['contact_thankyou1'] = 'Thank you for your message. Please allow 2 days for a reply from Amelia';
$string['contact_name2'] = 'Webmaster';
$string['contact_description2'] = 'Please direct all your website-related (technical) questions to our webmaster';
$string['contact_thankyou2'] = 'Thank you for your comments. Our webmaster will follow up as soon as possible';
$string['disclaimer_content'] = 'Here comes the disclaimer text.';
$string['login_content'] = '<h2>Login</h2>
(<em>Actually a placeholder for the mypage module</em>)
<p>
<form method="POST" action="{INDEX_URL}?login=1" name="loginform">
Username:<br>
<input type="text" name="login_username" value="" size="25" maxlength="80" class="textfield">
<p>
Password:<br>
<input type="password" name="login_password" value="" size="25" maxlength="80" class="passwordfield" autocomplete="off">
<p>
<input type="submit" name="button" value="OK" class="button">
</form>
';
$string['intranet_content'] = 'Welcome on the Exemplum Intranet.<p>{LOREM} {DOLOR}<p>{IPSUM} {SIT}';
$string['roster_content'] = '<h2>Meeting roster {THIS_SCHOOLYEAR}</h2>
Summer Meeting: last Friday of August, 3pm<br>
Fall Meeting: second Friday of November, 3pm<br>
Winter Meeting: second Friday of February, 3pm<br>
Spring Meeting: first Friday of June, <strong>9am</strong> (and not 3pm)<br>
';
$string['minutes1_content'] = '<h2>Minutes of the summer meeting {LAST_SCHOOLYEAR}</h2> {LOREM} {IPSUM} {DOLOR} {SIT}';
$string['minutes2_content'] = '<h2>Minutes of the fall meeting {LAST_SCHOOLYEAR}</h2> {IPSUM} {LOREM} {DOLOR} {SIT}';
$string['minutes3_content'] = '<h2>Minutes of the winter meeting {LAST_SCHOOLYEAR}</h2> {LOREM} {DOLOR} {IPSUM} {SIT}';
$string['minutes4_content'] = '<h2>Minutes of the spring meeting {LAST_SCHOOLYEAR}</h2> {LOREM} {IPSUM} {SIT} {DOLOR}';
$string['minutes5_content'] = '<h2>Minutes of the summer meeting {THIS_SCHOOLYEAR}</h2> {DOLOR} {LOREM} {SIT} {IPSUM}';
$string['downloads_content'] = '<h2>Handy tools for teachers</h2>
<ul>
<li><a target="_blank" href="http://www.openoffice.org">OpenOffice.org (wordprocessor, spreadsheet, etc.)</a>
<li><a target="_blank" href="http://www.mozilla.com">Firefox (webbrowser)</a>
<li><a target="_blank" href="{MANUAL_URL}">Website@School Manual</a>
</ul>
';

?>