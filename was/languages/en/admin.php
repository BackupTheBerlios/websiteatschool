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

/** /program/languages/en/admin.php - translated messages for /program/admin.php (English)
 *
 * This file is the 'mother-of-all-languages' file: it is the basis for all other translations.
 * Because there are so many strings to translate, it is easy to lose track of which one is
 * used where, etc. Also, it is sometimes hard to figure out what the purpose of a word or
 * a phrase is without the context.
 *
 * I try to make that a little easier by adding comments to this (main) language file.
 * These comments will be collected in a separate array called $comment. The actual
 * translations and texts end up in an array called $string. By using the same key
 * in the $comment array, it is possible to add clarification. This clarification can be
 * made visible in the translation tool, helping the translator grasping the purpose and
 * context of the texts to translate.
 *
 * Note that the order in which the texts appear in this file can also determine the order in
 * which the strings are displayed in the translation tool. The comments 'follow' the
 * strings rather than vice versa. It is not an error if a string doesn't have a comment.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: admin.php,v 1.12 2011/09/23 14:40:09 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['translatetool_title'] = 'Administration';
$string['translatetool_description'] = 'This file contains translations of the CMS administration interface';

$string['generated_in'] = 'generated on {DATE} in {QUERIES} queries and {SECONDS} seconds';
$string['logo_websiteatschool'] = 'logo Website@School&reg;';
$string['end_this_session'] = 'end this session, logout the user';
$string['logout_username'] = 'logout {USERNAME}';
$string['view_public_area'] = 'public area';
$string['go_view_public_area_no_logout'] = 'go view public area without logging out';
$string['check_was_release'] = 'check the status of your version of Website@School';
$string['version_x_y_z'] = 'version {VERSION}';
$string['login_user_success'] = 'You are logged in as: {USERNAME}';
$string['job_access_denied'] = 'You have no permissions to do this job; access denied';
$string['task_access_denied'] = 'You have no permissions to perform this task; access denied';
$string['unknown_job'] = 'Command "{JOB}" not recognised';

$string['name_startcenter'] = 'start';
$string['name_pagemanager'] = 'pages';
$string['name_filemanager'] = 'files';
$string['name_modulemanager'] = 'modules';
$string['name_accountmanager'] = 'accounts';
$string['name_configurationmanager'] = 'configuration';
$string['name_statistics'] = 'statistics';
$string['name_tools'] = 'tools';
$string['name_help'] = 'help';

$string['description_startcenter'] = 'Website@School Start';
$string['description_pagemanager'] = 'Page Manager';
$string['description_filemanager'] = 'File Manager';
$string['description_modulemanager'] = 'Module Manager';
$string['description_accountmanager'] = 'Account Manager';
$string['description_configurationmanager'] = 'Configuration Manager';
$string['description_statistics'] = 'Statistics';
$string['description_tools'] = 'Tools';
$string['description_help'] = 'Help (opens in a new window)';

$string['no_access_startcenter'] = 'Access to Website@School Start has been disabled for your account';
$string['no_access_pagemanager'] = 'Access to Page Manager has been disabled for your account';
$string['no_access_filemanager'] = 'Access to File Manager has been disabled for your account';
$string['no_access_modulemanager'] = 'Access to Module Manager has been disabled for your account';
$string['no_access_accountmanager'] = 'Access to Account Manager has been disabled for your account';
$string['no_access_configurationmanager'] = 'Access to Configuration Manager has been disabled for your account';
$string['no_access_statistics'] = 'Access to Statistics has been disabled for your account';
$string['no_access_tools'] = 'Access to Tools  has been disabled for your account';
$string['no_access_help'] = 'Access to Help has been disabled for your account';

$string['access_denied'] = 'Access denied';

$string['no_access_admin_php'] = 'Access to Website@School has been disabled for your account. Follow one of the links below to continue:';
$string['view_login_dialog'] = 'login';

$string['url'] = 'URL';
$string['public_area'] = 'Public area';
$string['private_area'] = 'Protected area';
$string['no_areas_available'] = 'No suitable areas available';
$string['inactive'] = 'inactive';
$string['menu'] = 'Menu';

$string['area_admin_access_denied'] = 'You do not have administrator privileges for area {AREA}';

$string['add_a_page'] = 'Add a page';
$string['add_a_page_title'] = 'Click here to add a new page';
$string['add_a_section'] = 'Add a section';
$string['add_a_section_title'] = 'Click here to add a new section';

$string['hidden'] = 'hidden';
$string['embargo'] = 'embargo';
$string['expired'] = 'expired';

$string['spacer'] = 'spacer';

$string['icon_delete'] = 'Delete this node';
$string['icon_delete_access_denied'] = 'You have no permissions to delete this node';
$string['icon_delete_alt'] = 'icon delete';
$string['icon_delete_text'] = 'D';

$string['icon_edit'] = 'Edit this node';
$string['icon_edit_access_denied'] = 'You have no permissions to edit this node';
$string['icon_edit_alt'] = 'icon edit';
$string['icon_edit_text'] = 'E';

$string['icon_default'] = 'Make this node the home (default) node';
$string['icon_default_access_denied'] = 'You have no permissions to change the home (default) node';
$string['icon_default_alt'] = 'icon home';
$string['icon_default_text'] = 'H';
$string['icon_not_default_text'] = '_';
$string['icon_not_default_alt'] = 'icon not home';
$string['icon_is_default'] = 'This node is the home (default) node';

$string['icon_preview_page'] = 'Display a preview of the page (in a separate window)';
$string['icon_preview_page_access_denied'] = 'You have no permission to display a preview of the page';
$string['icon_preview_page_alt'] = 'icon page';
$string['icon_preview_page_text'] = 'P';

$string['icon_open_section'] = 'Open the section (expand the tree 1 level)';
$string['icon_open_section_alt'] = 'icon closed folder';
$string['icon_open_section_text'] = '+';

$string['icon_close_section'] = 'Close the section (collapse the tree)';
$string['icon_close_section_alt'] = 'icon opened folder';
$string['icon_close_section_text'] = '-';

$string['icon_open_area'] = 'Open the area (expand the full tree)';
$string['icon_open_area_alt'] = 'icon closed folder';
$string['icon_open_area_text'] = '+';

$string['icon_close_area'] = 'Close the area (collapse the tree)';
$string['icon_close_area_alt'] = 'icon opened folder';
$string['icon_close_area_text'] = '-';

$string['icon_open_site'] = 'Open all areas (expand the full tree in all areas)';
$string['icon_open_site_alt'] = 'icon closed folder';
$string['icon_open_site_text'] = '+';

$string['icon_close_site'] = 'Close all areas (collapse the tree in all areas)';
$string['icon_close_site_alt'] = 'icon opened folder';
$string['icon_close_site_text'] = '-';

$string['icon_visible'] = 'This node is visible (not hidden, no embargo, not expired)';
$string['icon_visible_access_denied'] = 'You have no permissions to edit this node';
$string['icon_visible_alt'] = 'icon visible';
$string['icon_visible_text'] = '_';

$string['icon_invisible_hidden'] = 'This node is invisible (hidden)';
$string['icon_invisible_embargo'] = 'This node is invisible (embargo until {DATIM})';
$string['icon_invisible_expiry'] = 'This node is invisible (expiration on {DATIM})';
$string['icon_invisible_alt'] = 'icon invisible';
$string['icon_invisible_text'] = 'I';



$string['too_many_levels'] = 'Can not display section {NODE}: nested too deep';

$string['no_nodes_yet'] = 'No nodes have been added to this area yet';

$string['set_tree_view'] = 'Set tree view:';
$string['set_view_minimal'] = 'minimal';
$string['set_view_custom'] = 'customised';
$string['set_view_maximal'] = 'maximal';
$string['set_view_minimal_title'] = 'view the collapsed tree (all sections closed)';
$string['set_view_custom_title'] = 'view the customised tree (some sections closed, some open)';
$string['set_view_maximal_title'] = 'view the expanded tree (all sections opened)';


$string['access_denied_preview'] = 'You do not have the necessary privileges to preview this page. Please close the window and return to the page manager';

$string['invalid_node'] = 'Invalid node {NODE}';
$string['task_set_default_access_denied'] = 'You have no permissions make node {NODE} the home (default) node';


$string['startcenter_welcome'] = 'Welcome';
$string['startcenter_welcome_text'] = 'This is the startpage of the Website@School website management system. Here you can manage your website.';

$string['click_here_for_documentation'] = 'Click here to view the documentation';
$string['icon_documentation'] = 'icon book';

$string['click_to_send_mail_to_us'] = 'Click here to send mail to us';
$string['icon_sendmail'] = 'icon envelope';
$string['please_send_us_mail'] = 'When your schoolsite is up and running, we would appreciate receiving an email with the URL. Click on the icon to send us this email or send mail to <strong>{MAILTO}</strong>. Many thanks on behalf of the Website@School team.';
$string['view_documentation'] = 'Click on the icon to view the documentation (opens in a new window)';
$string['icon_information'] = 'icon information';
$string['check_new_version'] = 'Click on the icon to check if there is a new version (your version is {VERSION})';

$string['task_node_add_access_denied'] = 'You do not have the necessary privileges to add a node to this area';



$comment['add_a_page_header'] = 
"Here begins the translations for the add a node (page/section) dialog.
Most texts are generic enough to use the word 'node' but sometimes a distinction between 'page' and 'section' is necessary.
The items in the dialog all have a distinct hotkey (identified by a tilde preceding the hotkey letter). This is the list for adding a node:
~Name
~Description
~Parent
~Module (applies only to pages, not sections)
  ~Visible
  ~Hidden
  ~Embargo
~Save
~Cancel
Note that the hotkeys for the buttons Save and Cancel are defined in the main language file, not here.
If you translate this dialog, please make sure that the hotkeys are unique within the dialog: no two items should have the same hotkey.
Note that the strings for adding and editing a node are very much alike. However, because the hotkeys in other languages might be different between dialogs, they are not the same strings; they have different keys, e.g. add_node_title and edit_node_title.";

$string['add_a_page_header'] = 'Add a page';
$string['add_a_section_header'] = 'Add a section';

$string['add_section_explanation'] = 'Here you can add a new section by entering the section title and other information. Once the section has been added, you can add pages and subsections to the new section';
$string['add_page_explanation'] = 'Here you can add a new page by entering the page title, the page module and other information.';

$string['add_node_linktext'] = '~Name';
$string['add_node_linktext_title'] = 'Please enter the short name (used in navigation)';

$string['add_node_title'] = '~Description of the page/section';
$string['add_node_title_title'] = 'Please enter the description of the page/section';

$string['add_node_parent_section'] = '~Parent section';
$string['add_node_parent_section_title'] = 'Please select the section where the new page/section should be added';

$string['add_node_module'] = '~Module';
$string['add_node_module_title'] = 'Please select an appropriate module for this page';

$string['add_node_initial_visibility'] = 'Initial visibility';
$string['add_node_initial_visibility_title'] = 'Please choose the initial visibility of the new page/section';

$string['add_node_visible'] = '~Visible';
$string['add_node_visible_title'] = 'This option makes a page/section visible in navigation and accessible';

$string['add_node_hidden'] = '~Hidden';
$string['add_node_hidden_title'] = 'If you make a page/section hidden, the page will not be displayed in the site navigation but it will be accessible';

$string['add_node_embargo'] = '~Embargo';
$string['add_node_embargo_title'] = 'If you place a page/section under embargo, it will not be visible or accessible at all until the embargo date';

$string['cancelled'] = 'Cancelled';
$string['error_adding_node'] = 'Error adding page or section to database';
$string['page_added'] = 'Added to area {AREA}: new page {NODE} {LINK} ({TITLE})';
$string['section_added'] = 'Added to area {AREA}: new section {NODE} {LINK} ({TITLE})';
$string['node_has_no_name'] = '(page/section {NODE} has no name)';

$string['new_default_node_in_section'] = 'Area {AREA}: new default page/section {NEW} in section {PARENT})';
$string['new_default_node_in_area'] = 'Area {AREA}: new default page/section {NEW}';
$string['old_default_node'] = '(old default {OLD})';


$comment['edit_basic'] = 'The strings below go in the edit menu';
$string['edit_basic'] = 'Basic properties';
$string['edit_advanced'] = 'Advanced';
$string['edit_content'] = 'Content';
$string['edit_basic_page_title'] = 'Edit the basic properties of page {NODE}';
$string['edit_basic_section_title'] = 'Edit the basic properties of section {NODE}';
$string['edit_advanced_page_title'] = 'Edit the advanced properties of page {NODE}';
$string['edit_advanced_section_title'] = 'Edit the advanced properties of section {NODE}';
$string['edit_content_title'] = 'Edit the content of page {NODE}';

$string['task_edit_page_access_denied'] = 'You do not have the necessary privileges to edit page {NODE}';
$string['task_edit_section_access_denied'] = 'You do not have the necessary privileges to edit section {NODE}';
$string['page_is_locked_by'] = 'Page {NODE} is locked by {FULL_NAME} since {LOCK_TIME} ({USERNAME} logged in from {IP_ADDR} at {LOGIN_TIME})';
$string['section_is_locked_by'] = 'Section {NODE} is locked by {FULL_NAME} since {LOCK_TIME} ({USERNAME} logged in from {IP_ADDR} at {LOGIN_TIME})';




$comment['edit_a_page_header'] = 'This is the beginning of the edit node dialog (basic properties).
The following words/hotkeys are used.
~Name
~Description
~Parent
~Module (applies only to pages, not sections)
~Order
~Save
~Cancel
Note that these words are almost the same as in the add node dialog. However, the hotkeys might differ between those dialogs in other languages than English, therefore these strings are distinct.
';
$string['edit_a_page_header'] = 'Edit basic properties of page {NODE}';
$string['edit_a_section_header'] = 'Edit basic properties of section {NODE}';
$string['edit_page_explanation'] = 'Here you can change the basic properties of page {NODE_FULL_NAME}, such as the page title and the page module.';
$string['edit_section_explanation'] = 'Here you can change the basic properties of section {NODE_FULL_NAME}, such as the name and the description.';

$string['edit_node_linktext'] = '~Name';
$string['edit_node_linktext_title'] = 'Please enter the short name of the page/section (used in navigation)';

$string['edit_node_title'] = '~Description of the page/section';
$string['edit_node_title_title'] = 'Please enter the description of the page/section';

$string['edit_node_parent_section'] = '~Parent section';
$string['edit_node_parent_section_title'] = 'Please select the section where this page/section should be located';

$string['edit_node_module'] = '~Module';
$string['edit_node_module_title'] = 'Please select an appropriate module for this page';

$string['edit_node_sort_order'] = '~Order';
$string['edit_node_sort_order_title'] = 'Please select the appropriate sort order';


$comment['options_sort_order_at_top'] = 'The strings below are used in the picklist with which the user selects the sort order of nodes.';
$string['options_sort_order_at_top'] = 'Before all other pages/sections';
$string['options_sort_order_at_top_title'] = 'Position this page/section before all the other pages/sections in this (sub)section';
$string['options_sort_order_after_page'] = 'After page {NODE}';
$string['options_sort_order_after_section'] = 'After section {NODE}';


$comment['options_parents_at_toplevel'] = 'This string is used to construct a list of parents for both add node and edit node';
$string['options_parents_at_toplevel'] = 'At the top of the area';
$string['options_parents_at_toplevel_title'] = 'Put this page/section at the toplevel of this area, not in any (sub)section';
$string['options_parents_section'] = 'Section {NODE}';


$comment['edit_a_page_advanced_header'] = 'This is the beginning of the edit node dialog (advanced properties).
The following words/hotkeys are used.
~Area
~Icon pathname
Icon ~width
Icon hei~ght
~Target
~URL
~Hidden
~Embargo
E~xpiry
~Readonly
~Save
~Cancel
';
$string['edit_a_page_advanced_header'] = 'Edit advanced properties of page {NODE}';
$string['edit_a_section_advanced_header'] = 'Edit advanced properties of section {NODE}';
$string['edit_page_advanced_explanation'] = 'Here you can edit the advanced properties of page {NODE_FULL_NAME}, such as the embargo date and the read only attribute.';
$string['edit_section_advanced_explanation'] = 'Here you can edit the advanced properties of section {NODE_FULL_NAME}, such as the embargo date and the read only attribute.';


$string['edit_node_area_id'] = '~Area';
$string['edit_node_area_id_title'] = 'Select the area where you want to move this node to';

$string['edit_node_link_image'] = '~Icon pathname';
$string['edit_node_link_image_title'] = 'Enter the pathname to the icon file for this node';

$string['edit_node_link_image_width'] = 'Icon ~width';
$string['edit_node_link_image_width_title'] = 'Enter the width of the icon (in pixels)';

$string['edit_node_link_image_height'] = 'Icon hei~ght';
$string['edit_node_link_image_height_title'] = 'Enter the height of the icon (in pixels)';

$string['edit_node_link_target'] = '~Target';
$string['edit_node_link_target_title'] = 'Enter the target, e.g. _blank for a new window (see manual)';

$string['edit_node_link_href'] = '~URL';
$string['edit_node_link_href_title'] = 'Enter the full URL of the external webpage to link to';

$string['edit_node_is_hidden'] = 'Hidden';
$string['edit_node_is_hidden_title'] = 'Check the box to make the page/section hidden (but accessible)';
$string['edit_node_is_hidden_label'] = '~Hide the page/section';

$string['edit_node_is_readonly'] = 'Readonly';
$string['edit_node_is_readonly_title'] = 'Check the box to protect this page/section against accidental editing';
$string['edit_node_is_readonly_label'] = 'Make this page/section ~readonly';

$string['edit_node_embargo'] = '~Embargo';
$string['edit_node_embargo_title'] = 'Enter the date/time on which this page/section will be published automatically';

$string['edit_node_expiry'] = 'E~xpiry';
$string['edit_node_expiry_title'] = 'Enter the date/time on which this page/section will become inaccessible for visitors';

$comment['options_public_area'] = 'Strings below used in the advanced edit node dialog for moving nodes to another area';
$string['options_public_area'] = 'Public area {AREA} ({AREANAME})';
$string['options_private_area'] = 'Protected area {AREA} ({AREANAME})';
$string['options_public_area_inactive'] = 'Public area {AREA} ({AREANAME}) (inactive)';
$string['options_private_area_inactive'] = 'Protected area {AREA} ({AREANAME}) (inactive)';

$string['node_no_longer_readonly'] = 'Readonly attribute removed from page/section {NODE_FULL_NAME}';
$string['node_still_readonly'] = 'Readonly attribute still applies to page/section {NODE_FULL_NAME}';

$string['error_saving_node'] = 'Error saving page/section to database';
$string['page_saved'] = 'Success saving page {NODE_FULL_NAME}';
$string['section_saved'] = 'Success saving section {NODE_FULL_NAME}';
$string['node_was_edited'] = 'Page/section in area {AREA} changed: {NODE_FULL_NAME}';
$string['node_was_edited_and_moved'] = 'Page/section in area {AREA} moved to area {NEWAREA}: {NODE_FULL_NAME}';
$string['error_moving_subtree'] = 'Error moving subtree {NODE_FULL_NAME} from area {AREA} to area {NEWAREA}';
$string['success_moving_subtree'] = 'Success moving subtree {NODE_FULL_NAME} from area {AREA} to area {NEWAREA}';
$string['subtree_was_moved'] = 'Subtree in area {AREA} moved to area {NEWAREA}: {NODE_FULL_NAME}';

$string['task_delete_node_access_denied'] = 'You do not have the necessary privileges to delete page/section {NODE} from this area';
$string['task_delete_node_limited'] = 'You cannot delete section {NODE_FULL_NAME} because it has non-emtpy subsections';

$string['task_delete_node_is_readonly'] = 'You cannot delete page/section {NODE_FULL_NAME} because it is readonly';

$string['page_full_name'] = 'Page {NODE_FULL_NAME}';
$string['section_full_name'] = 'Section {NODE_FULL_NAME}';
$string['delete_a_page_header'] = 'Confirm delete of page {NODE_FULL_NAME}';
$string['delete_a_section_header'] = 'Confirm delete of section {NODE_FULL_NAME}';
$string['delete_page_explanation'] = 'You are about to delete the following page:';
$string['delete_section_explanation'] = 'You are about to delete the following section and all pages and sections it contains:';
$string['delete_are_you_sure'] = 'Are you sure you want to proceed?';
$string['error_deleting_node'] = 'Error deleting page/section {NODE_FULL_NAME} from area {AREA}';
$string['page_deleted'] = 'Deleted from area {AREA}: page {NODE_FULL_NAME}';
$string['section_deleted'] = 'Deleted from area {AREA}: section {NODE_FULL_NAME}';
$string['errors_deleting_childeren'] = 'Number of errors deleting pages/sections from section {NODE_FULL_NAME}: {COUNT}';
$string['error_editing_node_content'] = 'Error editing content of page {NODE_FULL_NAME}';

$string['page_content_edited'] = '(Area {AREA}) Content of page changed: {NODE_FULL_NAME}';



$comment['configurationmanager_intro'] = 'Start of translations for the configuration manager';
$string['configurationmanager_intro'] = 'This is the Configuration Manager. Please select a task from the menu';
$string['configurationmanager_header'] = 'Configuration Manager';

$string['menu_areas'] = 'Areas';
$string['menu_areas_title'] = 'View, add, edit or delete areas';
$string['menu_site'] =  'Site';
$string['menu_site_title'] = 'View or edit global site configuration';
$string['menu_alerts'] =  'Alerts';
$string['menu_alerts_title'] = 'View, add, edit or delete alerts';
$string['task_unknown'] = 'Unknown task {TASK}';
$string['chore_unknown'] = 'Unknown chore {CHORE}';

$string['areamanager_add_an_area'] = 'Add an area';
$string['areamanager_add_an_area_title'] = 'Click here to add a new area';

$string['icon_area_default'] = 'Make this area the home (default) area';
$string['icon_area_default_access_denied'] = 'You have no permission to change the default area';
$string['icon_area_default_alt'] = 'icon home';
$string['icon_area_default_text'] = 'H';
$string['icon_area_not_default_text'] = '_';
$string['icon_area_not_default_alt'] = 'icon not home';
$string['icon_area_is_default'] = 'This area is the home (default) area';

$string['icon_area_delete'] = 'Delete this area';
$string['icon_area_delete_access_denied'] = 'You have no permission to delete this area';
$string['icon_area_delete_alt'] = 'icon delete';
$string['icon_area_delete_text'] = 'D';

$string['icon_area_edit'] = 'Edit this area';
$string['icon_area_edit_access_denied'] = 'You have no permission to edit this area';
$string['icon_area_edit_alt'] = 'icon edit';
$string['icon_area_edit_text'] = 'E';

$string['area_edit_public_title'] = '(public) {AREA_FULL_NAME} ({AREA}, {SORT_ORDER})';
$string['area_edit_private_title'] = '(private) {AREA_FULL_NAME} ({AREA}, {SORT_ORDER})';

$string['area_delete_public_title'] = '{AREA_FULL_NAME} (public area {AREA})';
$string['area_delete_private_title'] = '{AREA_FULL_NAME} (private area {AREA})';

$string['invalid_area'] = 'Invalid area {AREA}';
$string['error_deleting_area'] = 'Error deleting area {AREA} ({AREA_FULL_NAME})';
$string['error_deleting_area_not_empty'] = 'Cannot delete area {AREA} ({AREA_FULL_NAME}): must be empty. (remaining number of pages/sections: {NODES})';
$string['area_deleted'] = 'Area {AREA} ({AREA_FULL_NAME}) deleted';

$string['delete_an_area_header'] = 'Confirm delete of area {AREA_FULL_NAME}';
$string['delete_area_explanation'] = 'You are about to delete the following area:';

$string['task_area_add_access_denied'] = 'You do not have the necessary privileges to add an area to the site';
$string['task_set_default_area_access_denied'] = 'You have no permissions to make area {AREA} the default area';



$comment['areamanager_add_area_header'] = 
"Here begins the translations for the add an area dialog.
The items in the dialog all have a distinct hotkey (identified by a tilde preceding the hotkey letter).
This is the list for adding an area:
~Name
~Private
~Data folder
~Theme
~Save
~Cancel
Note that the hotkeys for the buttons Save and Cancel are defined in the main language file, not here.
If you translate this dialog, please make sure that the hotkeys are unique within the dialog: no two
items should have the same hotkey.
Note that the strings for adding and editing a node are very much alike. However, because the hotkeys
in other languages might be different between dialogs, they are not the same strings; they have
different keys, e.g. add_area_title and edit_area_title.";
$string['areamanager_add_area_header'] = 'Add an area';
$string['areamanager_add_area_explanation'] = 'Here you can add a new area to the site by entering the area name and other information. Once the area has been added to the site, you can add pages and subsections to the new area using the Page Manager. Note that you can not change a private area into a public area or vice versa.';
$string['areamanager_add_area_title_label'] = '~Name';
$string['areamanager_add_area_title_title'] = 'Please enter the name of the new area';
$string['areamanager_add_area_is_private_label'] = 'Private area';
$string['areamanager_add_area_is_private_title'] = 'Check the box to make this area a private area';
$string['areamanager_add_area_is_private_check'] = 'Mark the area as ~private (cannot be changed lateron)';
$string['areamanager_add_area_path_label'] = '~Data folder (cannot be changed lateron)';
$string['areamanager_add_area_path_title'] = 'This folder holds data files for this area';
$string['areamanager_add_area_theme_id_label'] = '~Theme';
$string['areamanager_add_area_theme_id_title'] = 'Select the theme for this area';


$string['errors_saving_data'] = 'There were problems saving the changes. Errorcount: {ERRORS}';
$string['success_saving_data'] =  'Success saving changes to the database';

$string['areamanager_edit_theme_header'] = 'Configure theme \'{THEME_NAME}\' for area {AREA}';
$string['areamanager_edit_theme_explanation'] = 'Here you can configure the theme {THEME_NAME} for area {AREA} ({AREA_FULL_NAME}).<br>The properties you can change here only apply to a this particular area, i.e. every area can have a unique set of properties for a particular combination of area and theme.';


$string['areamanager_menu_edit'] = 'Basic properties';
$string['areamanager_menu_edit_title'] = 'Edit the basic area properties';
$string['areamanager_menu_edit_theme'] = 'Theme configuration';
$string['areamanager_menu_edit_theme_title'] = 'Configure the theme for this area';
$string['areamanager_menu_reset_theme'] = 'Reset theme';
$string['areamanager_menu_reset_theme_title'] = 'Reset the theme configuration for this area to defaults';


$comment['areamanager_edit_area_header'] = 
"This is the start of the area edit basic properties dialog.
The dialog/hotkeys looks like this:
~Name
Mark as ~active area
Mark as ~private area
~Data folder
Sort ~order
~Theme
~Save
~Cancel
";
$string['areamanager_edit_area_header'] = 'Edit basic properties of this area';
$string['areamanager_edit_area_explanation'] = 'Here you can edit the basic properties of an area. Note that it is <b>not</b> possible to change a private area into a public area or vice versa.';
$string['areamanager_edit_area_title_label'] = '~Name';
$string['areamanager_edit_area_title_title'] = 'The name of the area';
$string['areamanager_edit_area_is_active_label'] = 'Active area';
$string['areamanager_edit_area_is_active_title'] = 'Check the box to make the area active';
$string['areamanager_edit_area_is_active_check'] = 'Mark this area as ~active';
$string['areamanager_edit_area_is_private_label'] = 'Private area';
$string['areamanager_edit_area_is_private_title'] = 'If the box is checked this area is private';
$string['areamanager_edit_area_is_private_check'] = 'Mark this area as ~private (cannot be changed)';
$string['areamanager_edit_area_path_label'] = '~Data folder (pathname cannot be changed)';
$string['areamanager_edit_area_path_title'] = 'This folder holds data files for this area';
$string['areamanager_edit_area_metadata_label'] = '~Metadata';
$string['areamanager_edit_area_metadata_title'] = 'This information is added to the HTML header of every page';
$string['areamanager_edit_area_sort_order_label'] = 'Sort ~order';
$string['areamanager_edit_area_sort_order_title'] = 'Areas are displayed in the order determined by this number';
$string['areamanager_edit_area_theme_id_label'] = '~Theme';
$string['areamanager_edit_area_theme_id_title'] = 'Select the theme for this area';

$string['areamanager_save_area_success'] = 'Success saving changes in area {AREA} ({AREA_FULL_NAME})';
$string['areamanager_save_area_failure'] = 'There were problems saving changes in area {AREA} ({AREA_FULL_NAME})';

$string['areamanager_savenew_area_success'] = 'Success adding new area {AREA} ({AREA_FULL_NAME})';
$string['areamanager_savenew_area_failure'] = 'There were problems saving the new area';

$comment['site_config_header'] = 'Here is the Site Configuration dialog (ConfigurationManager | Site)';
$string['site_config_header'] = 'Site configuration';
$string['site_config_explanation'] = 'Here you can modify the global parameters for the complete site.';
$string['site_config_version_label'] = 'Internal version number (do not change)';
$string['site_config_version_title'] = 'Internal version number';
$string['site_config_salt_label'] = 'Security code';
$string['site_config_salt_title'] = 'This string is used as a salt in the session key hash, to discourage brute force attacks';
$string['site_config_session_name_label'] = 'Session name';
$string['site_config_session_name_title'] = 'The name of the cookie in the user\'s browser';
$string['site_config_session_expiry_label'] = 'Session expiry interval (seconds, default 86400)';
$string['site_config_session_expiry_title'] = 'This is the maximum duration of a session in seconds, 86400s = 24h';
$string['site_config_login_max_failures_label'] = 'Maximum allowed login attempts (default 10)';
$string['site_config_login_max_failures_title'] = 'User is blacklisted after this number of failed login attempts within failures interval';
$string['site_config_login_failures_interval_label'] = 'Login failures interval (minutes, default=12)';
$string['site_config_login_failures_interval_title'] = 'Only login failures within this interval are counted';
$string['site_config_login_bypass_interval_label'] = 'Valid bypass interval (minutes, default 30)';
$string['site_config_login_bypass_interval_title'] = 'A bypass code is only valid for this amount of time';
$string['site_config_login_blacklist_interval_label'] = 'Blacklist interval (minutes, default 8)';
$string['site_config_login_blacklist_interval_title'] = 'How long is the user blacklisted after too many failures';
$string['site_config_title_label'] = 'Website title';
$string['site_config_title_title'] = 'This is the name of the website';
$string['site_config_website_from_address_label'] = 'Website From: e-mail address';
$string['site_config_website_from_address_title'] = 'Mail from the CMS is sent from this address';
$string['site_config_website_replyto_address_label'] = 'Website Reply-To: e-mail address';
$string['site_config_website_replyto_address_title'] = 'Replies to the CMS should be sent to this address';
$string['site_config_language_key_label'] = 'Default language (2-letter lowercase code, default \'en\')';
$string['site_config_language_key_title'] = 'This sets the default language for the website';
$string['site_config_pagination_height_label'] = 'Number of items per screen (in long lists)';
$string['site_config_pagination_height_title'] = 'This sets the size of the screen when displaying very long lists';
$string['site_config_pagination_width_label'] = 'Maximum number screens (in long lists)';
$string['site_config_pagination_width_title'] = 'This is the number of visible jumps in the pagination navigation bar';

$string['site_config_editor_label'] = 'Default editor';
$string['site_config_editor_title'] = 'This is default editor for new user accounts';
$string['site_config_editor_fckeditor_option'] = 'FCKeditor';
$string['site_config_editor_fckeditor_title'] = 'JavaScript-based WYSIWYG word processor';
$string['site_config_editor_plain_option'] = 'Plain';
$string['site_config_editor_plain_title'] = 'No-frills plain text editor';
$string['site_config_friendly_url_label'] = '';
$string['site_config_friendly_url_title'] = 'Check this box to generate proxy-friendly URLs in site-navigation';
$string['site_config_friendly_url_option'] = 'Use proxy-friendly URLs';
$string['site_config_clamscan_path_label'] = 'Fully qualified path to ClamAV virus scanner program';
$string['site_config_clamscan_path_title'] = 'Leave empty if the ClamAV virus scanner is not available';
$string['site_config_clamscan_mandatory_label'] = '';
$string['site_config_clamscan_mandatory_title'] = 'Check this box for mandatory virus scanning on file uploads';
$string['site_config_clamscan_mandatory_option'] = 'Scan files for viruses on upload';
$string['site_config_upload_max_files_label'] = 'Maximum number of files per upload (non-Java)';
$string['site_config_upload_max_files_title'] = 'This sets the maximum number of simultaneous uploads in File Manager';
$string['site_config_thumbnail_dimension_label'] = 'Maximum height and width for thumbnail images';
$string['site_config_thumbnail_dimension_title'] = 'This sets the maximum dimensions of thumbnails created when uploading images';
$string['site_config_filemanager_files_label'] = 'List of allowable extensions in File Manager (comma-delimited)';
$string['site_config_filemanager_files_title'] = 'All file uploads are limited to files with these extensions';
$string['site_config_filemanager_images_label'] = 'List of extensions recognised as images (comma-delimited)';
$string['site_config_filemanager_images_title'] = 'Only files with one of these extensions are selectable when browsing images from the FCK Editor';
$string['site_config_filemanager_flash_label'] = 'List of extensions recognised as flash files (comma-delimited)';
$string['site_config_filemanager_flash_title'] = 'Only files with one of these extensions are selectable when browsing flash files from the FCK Editor';

$string['area_theme_reset'] = 'Properties for theme {THEME_NAME} reset to defaults for area {AREA} ({AREA_FULL_NAME})';
$string['error_area_theme_reset'] = 'There were problems resetting the properties for theme {THEME_NAME} in area {AREA} ({AREA_FULL_NAME})';

$string['reset_theme_area_header'] = 'Reset properties of theme {THEME_NAME} for area {AREA} ({AREA_FULL_NAME})';
$string['reset_theme_area_explanation'] = 'You are about to overwrite the existing theme properties with the default values.';
$string['reset_theme_area_are_you_sure'] = 'Are you sure you want to continue?';


$comment['accountmanager_header'] = 'Strings for account manager start here';
$string['accountmanager_header'] = 'Account Manager';
$string['accountmanager_intro'] = 'This is the Account Manager. Please select a task from the menu';
$string['accountmanager_summary'] = 'Summary';
$string['accountmanager_users'] = 'Users';
$string['accountmanager_groups'] = 'Groups';
$string['accountmanager_active'] = 'Active';
$string['accountmanager_inactive'] = 'Inactive';
$string['accountmanager_total'] = 'Total';

$string['menu_users'] =  'Users';
$string['menu_users_title'] = 'View, add, edit or delete user accounts';
$string['menu_groups'] =  'Groups';
$string['menu_groups_title'] = 'View, add, edit or delete groups';

$string['groupmanager_add_a_group'] = 'Add a group';
$string['groupmanager_add_a_group_title'] = 'Click here to add a new group';

$string['groupmanager_group_edit_title'] = 'Edit \'{FULL_NAME}\'';
$string['groupmanager_group_capacity_edit_title'] = 'Edit properties for this group/capacity';

$string['icon_group_delete'] = 'Delete this group';
$string['icon_group_delete_alt'] = 'icon delete';
$string['icon_group_delete_text'] = 'D';

$string['icon_group_edit'] = 'Edit this group';
$string['icon_group_edit_alt'] = 'icon edit';
$string['icon_group_edit_text'] = 'E';

$string['groupmanager_add_group_header'] = 'Add a new group';
$string['groupmanager_add_group_explanation'] = 'Here you can add a new group by entering the group name and other information, notably the capacities in which a user can join a group. Once the group has been created, you can add additional information (access control) per capacity';
$string['groupmanager_add_group_name_label'] = '~Name';
$string['groupmanager_add_group_name_title'] = 'Please enter the short name of the new group (must be unique)';
$string['groupmanager_add_group_fullname_label'] = '~Description';
$string['groupmanager_add_group_fullname_title'] = 'Please enter the long name (description) of the new group';
$string['groupmanager_add_group_is_active_label'] = 'Active group';
$string['groupmanager_add_group_is_active_title'] = 'Check the box to make this group active';
$string['groupmanager_add_group_is_active_check'] = 'Mark the group as ~active';
$string['groupmanager_add_group_capacity_label'] = 'Capacity ~{INDEX}';
$string['groupmanager_add_group_capacity_title'] = 'Select a capacity for membership of this group';

$string['groupmanager_savenew_group_failure'] = 'There were problems saving the new group';
$string['groupmanager_savenew_group_success'] = 'Success adding new group {GROUP} ({GROUP_FULL_NAME})';

$string['error_invalid_parameters'] = 'Error: invalid parameters for request';
$string['error_retrieving_data'] = 'Error: cannot retrieve data from database';


$string['groupmanager_edit_group_header'] = 'Edit a group';
$string['groupmanager_edit_group_explanation'] = 'Here you can edit basic group properties.';
$string['groupmanager_edit_group_name_label'] = '~Name';
$string['groupmanager_edit_group_name_title'] = 'Please enter the short name of the new group (must be unique)';
$string['groupmanager_edit_group_fullname_label'] = '~Description';
$string['groupmanager_edit_group_fullname_title'] = 'Please enter the long name (description) of the new group';
$string['groupmanager_edit_group_is_active_label'] = 'Active group';
$string['groupmanager_edit_group_is_active_title'] = 'Check the box to make this group active';
$string['groupmanager_edit_group_is_active_check'] = 'Mark the group as ~active';
$string['groupmanager_edit_group_capacity_label'] = 'Capacity ~{INDEX}';
$string['groupmanager_edit_group_capacity_title'] = 'Select a capacity for membership of this group';
$string['groupmanager_edit_group_path_label'] = '~Data folder (pathname cannot be changed)';
$string['groupmanager_edit_group_path_title'] = 'This folder holds the shared data files for this group';

$string['groupmanager_edit_group_success'] = 'Success saving group {GROUP} ({GROUP_FULL_NAME})';


$string['groupmanager_delete_group_header'] = 'Confirm delete of group {GROUP} ({GROUP_FULL_NAME})';
$string['groupmanager_delete_group_explanation'] = 'You are about to delete the following group and associated capacites and user accounts:';
$string['groupmanager_delete_group_breadcrumb'] = 'delete';
$string['groupmanager_delete_group_group'] = '{GROUP_FULL_NAME} ({GROUP})';
$string['groupmanager_delete_group_capacity'] = '{CAPACITY}: {COUNT}';


$string['groupmanager_delete_group_success'] = 'Success deleting group {GROUP} ({GROUP_FULL_NAME})';
$string['groupmanager_delete_group_failure'] = 'There were errors deleting group {GROUP} ({GROUP_FULL_NAME})';

$string['usermanager_delete_group_dir_not_empty'] = 'The data folder of group \'{GROUP_FULL_NAME}\' ({GROUP}) is not empty yet. Please remove the files and folders first.';
$string['usermanager_delete_group_not_self'] = 'You cannot delete group \'{GROUP_FULL_NAME}\' ({GROUP}) because you are associated with this group as \'{CAPACITY}\'. You have to remove your association with the group before you can delete it';
$string['usermanager_delete_group_capacity_not_self'] = '{FIELD}: You cannot delete capacity \'{CAPACITY}\' from \'{GROUP_FULL_NAME}\' ({GROUP}) because you are associated with this group in that capacity. You have to remove your own association with the group/capacity before you can delete it';


$string['groupmanager_capacity_overview_header'] = 'Overview: {GROUP} - {CAPACITY}';
$string['groupmanager_capacity_overview_explanation'] = 'Here is an overview of all user accounts associated with this group ({GROUP_FULL_NAME}) and capacity ({CAPACITY})';
$string['groupmanager_capacity_overview_no_members'] = 'Currently no user accounts are associated with this group ({GROUP_FULL_NAME}) and capacity ({CAPACITY})';

$string['groupmanager_group_menu_edit'] = 'Basic properties';
$string['groupmanager_group_menu_edit_title'] = 'Edit the basic group properties';

$string['groupmanager_capacity_intranet_header'] = 'Intranet access: {GROUP} - {CAPACITY}';
$string['groupmanager_capacity_intranet_explanation'] = 'Select the roles for intranet access you wish to assign to this group ({GROUP_FULL_NAME}) and capacity ({CAPACITY}) and press [Save] to save your changes.';


// $string['errors_saving_data'] = 'There were problems saving the changes. Errorcount: {ERRORS}';
// $string['success_saving_data'] =  'Success saving changes to the database';

$string['acl_error_saving_field'] = '{FIELD}: error saving data';


$string['usermanager_user_edit_title'] = 'Edit \'{FULL_NAME}\'';

$string['breadcrumb_you_are_here'] = 'You are here:';
$string['breadcrumb_next'] = '&gt;';

$string['menu_groupcapacity_overview'] =  'Overview';
$string['menu_groupcapacity_overview_title'] = 'Display overview of members of this group/capacity';
$string['menu_groupcapacity_intranet'] =  'Intranet';
$string['menu_groupcapacity_intranet_title'] = 'Modify permissions for intranet access (private areas)';

$string['menu_groupcapacity_module_title'] = 'Modify permissions for this module';
$string['menu_groupcapacity_admin'] =  'Admin';
$string['menu_groupcapacity_admin_title'] = 'Modify permissions for administration (webmaster functions)';
$string['menu_groupcapacity_pagemanager'] =  'Page Manager';
$string['menu_groupcapacity_pagemanager_title'] = 'Modify permissions for pages (webmaster functions)';



$comment['acl_role_none_option'] = 'Messages dealing with Access Control Lists )(ACLs) start here';
$string['acl_role_none_option'] = '--';
$string['acl_role_none_title'] = 'Null, nothing: this role corresponds to no permissions at all';
$string['acl_role_guru_option'] = 'Guru';
$string['acl_role_guru_title'] = 'Everything: this role provides all possible permissions, perhaps even more';
$string['acl_role_intranet_access_option'] = 'Access';
$string['acl_role_intranet_access_title'] = 'Intranet access granted: private areas can be visited';
$string['acl_role_unknown'] = 'unknown';

$string['acl_role_pagemanager_contentmaster_option'] = 'Contentmaster';
$string['acl_role_pagemanager_contentmaster_title'] = 'Only page content can be modified';
$string['acl_role_pagemanager_pagemaster_option'] = 'Pagemaster';
$string['acl_role_pagemanager_pagemaster_title'] = 'Page properties and page content can be modified';
$string['acl_role_pagemanager_sectionmaster_option'] = 'Sectionmaster';
$string['acl_role_pagemanager_sectionmaster_title'] = 'Section properties can be modified and subsections and pages can be added';
$string['acl_role_pagemanager_areamaster_option'] = 'Areamaster';
$string['acl_role_pagemanager_areamaster_title'] = 'Area properties can be modified and top-level sections and pages can be added';
$string['acl_role_pagemanager_sitemaster_option'] = 'Sitemaster';
$string['acl_role_pagemanager_sitemaster_title'] = 'Site properties can be modified and areas, sections and pages can be added';

$string['acl_all_areas_label'] = 'All current and future areas';
$string['acl_all_private_areas_label'] = 'All current and future private areas';
$string['acl_area_label'] = 'Area {AREA}: {AREA_FULL_NAME}';
$string['acl_area_inactive_label'] = 'Area {AREA}: {AREA_FULL_NAME} (inactive)';
$string['acl_page_label'] = 'Page {NODE}: {NODE_FULL_NAME}';
$string['acl_section_label'] = 'Section {NODE}: {NODE_FULL_NAME}';

$string['acl_column_header_realm'] = 'Realm';
$string['acl_column_header_role'] = 'Role';
$string['acl_column_header_related'] = 'Related';

$string['acl_job_guru_label'] = 'Guru';
$string['acl_job_guru_check'] = 'All permissions';
$string['acl_job_guru_title'] = 'Checking this box implies all current and future job permissions';

$string['acl_job_1_label'] = 'Startcenter';
$string['acl_job_1_check'] = 'Basic administrator';
$string['acl_job_1_title'] = 'Checking this box grants access to admin.php';

$string['acl_job_2_label'] = 'Manipulate pages and sections';
$string['acl_job_2_check'] = 'Page Manager';
$string['acl_job_2_title'] = 'Check this box to allow access to the Page Manager';

$string['acl_job_4_label'] = 'Upload files';
$string['acl_job_4_check'] = 'File Manager';
$string['acl_job_4_title'] = 'Check this box to allow access to the File Manager';

$string['acl_job_8_label'] = 'Module administration';
$string['acl_job_8_check'] = 'Module Manager';
$string['acl_job_8_title'] = 'Check this box to allow access to the Module Manager';

$string['acl_job_16_label'] = 'Users and Groups';
$string['acl_job_16_check'] = 'Account Manager';
$string['acl_job_16_title'] = 'Check this box to allow access to the Account Manager';

$string['acl_job_32_label'] = 'Site configuration and area manager';
$string['acl_job_32_check'] = 'Configuration Manager';
$string['acl_job_32_title'] = 'Check this box to allow access to the Configuration Manager';

$string['acl_job_64_label'] = 'Pageviews and performance';
$string['acl_job_64_check'] = 'Statistics';
$string['acl_job_64_title'] = 'Check this box to allow access to the Statistics';

$string['acl_job_128_label'] = 'Tools';
$string['acl_job_128_check'] = 'Translations';
$string['acl_job_128_title'] = 'Check this box to allow access to the Translation Tool';

$string['acl_job_256_label'] = 'Tools';
$string['acl_job_256_check'] = 'Backups';
$string['acl_job_256_title'] = 'Check this box to allow access to the Backup Tool';

$string['acl_job_512_label'] = 'Tools';
$string['acl_job_512_check'] = 'Log Viewer';
$string['acl_job_512_title'] = 'Check this box to allow access to the Log Viewer';

$string['acl_job_1024_label'] = 'Tools';
$string['acl_job_1024_check'] = 'Update Manager';
$string['acl_job_1024_title'] = 'Check this box to allow access to the Update manager';

$string['groupmanager_capacity_admin_header'] = 'Administrator permissions: {GROUP} - {CAPACITY}';
$string['groupmanager_capacity_admin_explanation'] = 'Select the permissions for administration you wish to assign to this group ({GROUP_FULL_NAME}) and capacity ({CAPACITY}) and press [Save] to save your changes.<p><strong>Note</strong><br>The Guru-option implies <em>all</em> other permissions, current and future. Please do assign this option with care';

$string['groupmanager_capacity_pagemanager_header'] = 'Page Manager permissions: {GROUP} - {CAPACITY}';
$string['groupmanager_capacity_pagemanager_explanation'] = 'Select the roles for Page Manager access you wish to assign to this group ({GROUP_FULL_NAME}) and capacity ({CAPACITY}) and press [Save] to save your changes.';

$string['usermanager_all_users_title'] = 'Display a list of all users';
$string['usermanager_all_users'] = 'All users';
$string['usermanager_all_users_count'] = 'All users ({COUNT})';
$string['usermanager_users_nogroup_title'] = 'Display a list of all users without a group at all';
$string['usermanager_users_nogroup'] = 'No group';
$string['usermanager_users_nogroup_count'] = 'No group ({COUNT})';
$string['usermanager_users_group_title'] = 'Display a list of {GROUP_FULL_NAME}';
$string['usermanager_users_group'] = '{GROUP}';
$string['usermanager_users_group_count'] = '{GROUP} ({COUNT})';

$string['usermanager_add_a_user'] = 'Add a user';
$string['usermanager_add_a_user_title'] = 'Click here to add a new user';

$string['usermanager_user_edit'] = '{FULL_NAME} ({USERNAME})';
$string['usermanager_user_edit_title'] = 'Edit this user';

$string['icon_user_delete'] = 'Delete this user';
$string['icon_user_delete_alt'] = 'icon delete';
$string['icon_user_delete_text'] = 'D';

$string['icon_user_edit'] = 'Edit this user';
$string['icon_user_edit_alt'] = 'icon edit';
$string['icon_user_edit_text'] = 'E';


$string['usermanager_add_user_header'] = 'Add a new user';
$string['usermanager_add_user_explanation'] = 'Here you can add a new user by entering the user name and other information. Once the user has been created, you can add additional information (access control) to the user account';
$string['usermanager_add_username_label'] = '~Name';
$string['usermanager_add_username_title'] = 'Please enter the login name of the new user (must be unique)';
$string['usermanager_add_user_fullname_label'] = '~Full name';
$string['usermanager_add_user_fullname_title'] = 'Please enter the full name of the new user';
$string['usermanager_add_user_password1_label'] = '~Password';
$string['usermanager_add_user_password1_title'] = 'Minimum requirements: characters: {MIN_LENGTH}, digits: {MIN_DIGIT}, lowercase: {MIN_LOWER}, uppercase: {MIN_UPPER}';
$string['usermanager_add_user_password2_label'] = 'Confirm pass~word';
$string['usermanager_add_user_password2_title'] = 'Minimum requirements: characters: {MIN_LENGTH}, digits: {MIN_DIGIT}, lowercase: {MIN_LOWER}, uppercase: {MIN_UPPER}';
$string['usermanager_add_user_email_label'] = '~E-mail';
$string['usermanager_add_user_email_title'] = 'Please enter the e-mail address of the new user';
$string['usermanager_add_user_is_active_label'] = 'Active user';
$string['usermanager_add_user_is_active_title'] = 'Check the box to make this user active';
$string['usermanager_add_user_is_active_check'] = 'Mark the user as ~active';

$string['usermanager_savenew_user_failure'] = 'There were problems saving the new user';
$string['usermanager_savenew_user_success'] = 'Success adding new user {USERNAME} ({FULL_NAME})';


$string['usermanager_delete_user_header'] = 'Confirm delete of user {USERNAME} ({FULL_NAME})';
$string['usermanager_delete_user_explanation'] = 'You are about to delete the following user account:';
$string['usermanager_delete_user_breadcrumb'] = 'delete';
$string['usermanager_delete_user_user'] = '{FULL_NAME} ({USERNAME})';
$string['usermanager_delete_user_success'] = 'Success deleting user {USERNAME} ({FULL_NAME})';
$string['usermanager_delete_user_failure'] = 'There were errors deleting user {USERNAME} ({FULL_NAME})';
$string['usermanager_delete_user_dir_not_empty'] = 'The data folder of {FULL_NAME} ({USERNAME}) is not empty yet. Please remove the files and folders first.';
$string['usermanager_delete_user_not_self'] = 'You cannot delete your own account';


$string['menu_user_basic'] =  'Basic';
$string['menu_user_basic_title'] = 'Edit basic properties of user account';
$string['menu_user_advanced'] =  'Advanced';
$string['menu_user_advanced_title'] = 'Edit advanced properties of user account';
$string['menu_user_groups'] =  'Groups';
$string['menu_user_groups_title'] = 'Modify group memberships for this user acccount';
$string['menu_user_intranet'] =  'Intranet';
$string['menu_user_intranet_title'] = 'Modify permissions for intranet access (private areas)';

$string['menu_user_module_title'] = 'Modify permissions for this module';
$string['menu_user_admin'] =  'Admin';
$string['menu_user_admin_title'] = 'Modify permissions for administration (webmaster functions)';
$string['menu_user_pagemanager'] =  'Page Manager';
$string['menu_user_pagemanager_title'] = 'Modify permissions for pages (webmaster functions)';


$comment['usermanager_edit_user_header'] = 
"This is the start of the edit basic user properties dialog.
The dialog/hotkeys looks like this:
~Name
~Password
Confirm pass~word
~Full name
E-~mail
Mark as ~active
~Redirection
~Language
~High visibility
~Editor
~Data directory
~Save
~Cancel
";

$string['usermanager_edit_user_header'] = 'Edit user {USERNAME} ({FULL_NAME})';
$string['usermanager_edit_user_explanation'] = 'Here you can edit the basic properties of user {FULL_NAME} ({USERNAME}).';
$string['usermanager_edit_username_label'] = '~Name';
$string['usermanager_edit_username_title'] = 'Please enter the new login name of this user (must be unique)';
$string['usermanager_edit_user_fullname_label'] = '~Full name';
$string['usermanager_edit_user_fullname_title'] = 'Please enter the full name of the user';
$string['usermanager_edit_user_password1_label'] = '~Password';
$string['usermanager_edit_user_password1_title'] = 'Minimum requirements: characters: {MIN_LENGTH}, digits: {MIN_DIGIT}, lowercase: {MIN_LOWER}, uppercase: {MIN_UPPER}';
$string['usermanager_edit_user_password2_label'] = 'Confirm pass~word';
$string['usermanager_edit_user_password2_title'] = 'Minimum requirements: characters: {MIN_LENGTH}, digits: {MIN_DIGIT}, lowercase: {MIN_LOWER}, uppercase: {MIN_UPPER}';
$string['usermanager_edit_user_email_label'] = 'E-~mail';
$string['usermanager_edit_user_email_title'] = 'Please enter the new e-mail address of the user';
$string['usermanager_edit_user_is_active_label'] = 'Active user';
$string['usermanager_edit_user_is_active_title'] = 'Check the box to make this user active';
$string['usermanager_edit_user_is_active_check'] = 'Mark the user as ~active';
$string['usermanager_edit_user_redirect_label'] = '~Redirection (where to go after logout)';
$string['usermanager_edit_user_redirect_title'] = 'Enter a URL to go to after logout (blank implies the default area)';
$string['usermanager_edit_user_language_label'] = '~Language';
$string['usermanager_edit_user_language_title'] = 'Select the preferred language for this user';
$string['usermanager_edit_user_high_visibility_label'] = 'Enable text interface';
$string['usermanager_edit_user_high_visibility_title'] = 'Check the box enable high visibility for this user';
$string['usermanager_edit_user_high_visibility_check'] = '~High visibility';
$string['usermanager_edit_user_editor_label'] = '~Editor';
$string['usermanager_edit_user_editor_title'] = 'Select the preferred editor/word processor for this user';
$string['usermanager_edit_user_path_label'] = '~Data folder (pathname cannot be changed)';
$string['usermanager_edit_user_path_title'] = 'This folder holds the personal data files for this user';

$string['usermanager_save_user_failure'] = 'There were problems with saving account {USERNAME} ({FULL_NAME})';
$string['usermanager_save_user_success'] = 'Success saving changes in account {USERNAME} ({FULL_NAME})';

$string['pagination_start'] = 'View:';
$string['pagination_glue'] = '&nbsp;';
$string['pagination_previous'] = 'Previous';
$string['pagination_next'] = 'Next';
$string['pagination_all'] = 'All';
$string['pagination_more_left'] = '&lt;';
$string['pagination_more_right'] = '&gt;';
$string['pagination_count_of_total'] = '[{FIRST}-{LAST} of {TOTAL}]';

$string['usermanager_user_groups_header'] = 'Memberships {USERNAME} ({FULL_NAME})';
$string['usermanager_user_groups_explanation'] = 'Here you can add and delete group memberships of user {FULL_NAME} ({USERNAME}).';
$string['usermanager_user_groups_add'] = 'Add a group membership';
$string['usermanager_user_groups_add_title'] = 'Click here to add a new group membership to this user account';
$string['usermanager_user_groups'] = '{GROUP} ({GROUP_FULL_NAME}) / {CAPACITY}';

$string['icon_membership_delete'] = 'Delete this group membership';
$string['icon_membership_delete_alt'] = 'icon delete';
$string['icon_membership_delete_text'] = 'D';

$string['usermanager_user_groupadd_header'] = 'Add a group membership to user {USERNAME} ({FULL_NAME})';
$string['usermanager_user_groupadd_explanation'] = 'Please select a group/capacity-combination from the list below and press [Save] to add the user {FULL_NAME} ({USERNAME}) to that group';

$string['usermanager_user_groupadd_groupcapacity_label'] = '~New group/capacity';
$string['usermanager_user_groupadd_groupcapacity_title'] = 'Please select a group/capacity-combination from the list';
$string['usermanager_user_groupadd_groupcapacity_none_available'] = '-- No groups available --';

$string['usermanager_delete_usergroup_success'] = 'Success ending membership {GROUP} ({GROUP_FULL_NAME}) / {CAPACITY}';
$string['usermanager_delete_usergroup_failure'] = 'There were errors ending membership {GROUP} ({GROUP_FULL_NAME}) / {CAPACITY}';

$string['usermanager_intranet_header'] = 'Intranet access: {USERNAME} ({FULL_NAME})';
$string['usermanager_intranet_explanation'] = 'Select the roles for intranet access you wish to assign to this user ({FULL_NAME}) and press [Save] to save your changes.';

$string['usermanager_admin_header'] = 'Administrator permissions: {USERNAME} ({FULL_NAME})';
$string['usermanager_admin_explanation'] = 'Select the permissions for administration you wish to assign to this user ({FULL_NAME}) and press [Save] to save your changes.<p><strong>Note</strong><br>The Guru-option implies <em>all</em> other permissions, current and future. Please do assign this option with care';

$string['usermanager_pagemanager_header'] = 'Page Manager permissions: {USERNAME} ({FULL_NAME})';
$string['usermanager_pagemanager_explanation'] = 'Select the roles for Page Manager access you wish to assign to this user ({FULL_NAME}) and press [Save] to save your changes.';

$string['filemanager_root'] = 'All Files';
$string['filemanager_root_title'] = 'Navigate to the top level directory';
$string['filemanager_personal'] = 'My Files';
$string['filemanager_personal_title'] = 'Navigate to the directory with personal files';
$string['filemanager_areas'] = 'Areas';
$string['filemanager_areas_title'] = 'Navigate to the directories with files per area';
$string['filemanager_groups'] =  'Groups';
$string['filemanager_groups_title'] = 'Navigate to the directories with files per group';
$string['filemanager_users'] =  'Users';
$string['filemanager_users_title'] = 'Navigate to the directories with files per user';

$string['filemanager_navigate_to'] = 'Navigate to \'{DIRECTORY}\'';
$string['filemanager_preview'] = 'Preview file \'{FILENAME}\'';
$string['filemanager_select'] = 'Select file \'{FILENAME}\'';
$string['filemanager_delete_file'] = 'Delete file \'{FILENAME}\'';
$string['filemanager_delete_directory'] = 'Delete folder \'{DIRECTORY}\'';
$string['filemanager_select_directory_entry_title'] = 'Check the box to select this folder';
$string['filemanager_select_file_entry_title'] = 'Check the box to select this file';
$string['filemanager_select_all_entries_title'] = 'Check the box to select all entries';

$string['filemanager_add_file'] = 'Add (upload) files';
$string['filemanager_add_file_title'] = 'Use this link to upload one or more files';
$string['filemanager_add_directory'] = 'Create a new subfolder';
$string['filemanager_add_directory_title'] = 'Use this link to create a subfolder in this folder';

$string['filemanager_parent'] = 'Up one level';
$string['filemanager_parent_title'] = 'Use this link to change to the parent folder';

$string['invalid_path'] = 'Invalid path \'{PATH}\'';

$string['icon_preview_file_alt'] = 'icon file preview';
$string['icon_preview_file_text'] = 'P';

$string['icon_delete_file'] = 'Delete this file';
$string['icon_delete_file_alt'] = 'icon file delete';
$string['icon_delete_file_text'] = 'D';

$string['icon_delete_directory'] = 'Delete this folder';
$string['icon_delete_directory_alt'] = 'icon folder delete';
$string['icon_delete_directory_text'] = 'D';

$string['filemanager_column_file'] = 'Name';
$string['filemanager_column_size'] = 'Size (in bytes)';
$string['filemanager_column_date'] = 'Date/time';
$string['filemanager_sort_asc'] = 'Sort column in ascending order';
$string['filemanager_sort_desc'] = 'Sort column in descending order';
$string['filemanager_select_file_entries'] = 'Select all files';
$string['filemanager_select_file_entries_title'] = 'Check the box to select all files'; 


$string['filemanager_add_subdirectory_header'] = 'Create a subfolder';
$string['filemanager_add_subdirectory_explanation'] = 'Here you can add a new subfolder. The name of the new folder should contain only letters, digits, dots, dashes or (single) underscores. Other characters such as slashes, colons and at-signs are not acceptable and are replaced with an underscore or even removed completely.';
$string['filemanager_add_subdirectory_label'] = '~Foldername';
$string['filemanager_add_subdirectory_title'] = 'Please enter the name of the new folder';
$string['filemanager_add_subdirectory_success'] = 'Subfolder \'{DIRECTORY}\' added to \'{PATH}\'';
$string['filemanager_add_subdirectory_failure'] = 'Error: could not add subfolder \'{DIRECTORY}\' to \'{PATH}\'';

$string['icon_open_directory_alt'] = 'icon closed folder';
$string['icon_open_directory_text'] = '+';

$string['filemanager_nothing_to_delete'] = 'Warning: nothing to delete';
$string['filemanager_success_delete_file'] = 'Success deleting file \'{FILENAME}\'';
$string['filemanager_success_delete_files'] = 'Success deleting {COUNT} files';
$string['filemanager_failure_delete_file'] = 'Error deleting file \'{FILENAME}\'';
$string['filemanager_failure_delete_files'] = 'Errors deleting {COUNT} files';
$string['filemanager_delete_file_header'] = 'Confirm file delete';
$string['filemanager_delete_file_explanation'] = 'You are about to delete the following file from \'{PATH}\':';
$string['filemanager_delete_files_explanation'] = 'You are about to delete the following {COUNT} files from \'{PATH}\':';

$string['filemanager_directory_not_empty'] = 'Subfolder \'{DIRECTORY}\' cannot be deleted unless it is empty';
$string['filemanager_success_delete_directory'] = 'Success deleting subfolder \'{DIRECTORY}\'';
$string['filemanager_failure_delete_directory'] = 'Error deleting subfolder \'{DIRECTORY}\'';
$string['filemanager_delete_directory_header'] = 'Confirm folder delete';
$string['filemanager_delete_directory_explanation'] = 'You are about to delete the following subfolder from \'{PATH}\':';


$string['filemanager_add_files_header'] = 'Add (upload) files';
$string['filemanager_add_files_explanation'] = 'Here you can add (upload) new files to the folder \'{DIRECTORY}\'. The names of the new files should contain only letters, digits, dots, dashes or (single) underscores. Other characters such as slashes, colons and at-signs are not acceptable and are automatically replaced with an underscore or even removed completely thus changing the name of the file as it is stored. If a file already exists it is preserved and the new file will be stored under another name.<p>Note:<br>File size is limited to {MAX_FILE_SIZE} bytes, total upload size is limited to {POST_MAX_SIZE} bytes.';
$string['filemanager_add_file_label'] = 'Filename';
$string['filemanager_add_file_title'] = 'Please enter the local path of the file to add or use the Browse-button';
$string['filemanager_add_files_label'] = 'Filename ({INDEX})';
$string['filemanager_add_files_title'] = 'Please enter the local path of the file to add or use the Browse-button';

$string['filemanager_add_files_upload_size_error'] = '{FIELD}: size error {ERROR} while uploading file \'{FILENAME}\'; file size is limited to {MAX_FILE_SIZE} bytes, upload size is limited to {POST_MAX_SIZE} bytes';
$string['filemanager_add_files_upload_error'] = '{FIELD}: error {ERROR} while uploading; file \'{FILENAME}\' skipped';
$string['filemanager_add_files_virus_found'] = '{FIELD}: virus(es) found; file \'{FILENAME}\' skipped';
$string['filemanager_add_files_virusscan_failed'] = '{FIELD}: error {ERROR} while scanning for viruses; file \'{FILENAME}\' skipped';
$string['filemanager_virus_mailsubject1'] = 'Virusalert for website {SITENAME}: attempt to upload virus'; 
$string['filemanager_virus_mailmessage1'] = 
'There was an attempt to upload a file containing
a virus. The output of the virusscanner is as follows:

{OUTPUT}

The currently logged in user was

{FULL_NAME} ({USERNAME})

and the file was {PATH} ({FILENAME}).

Kind regards,

Your automated webmaster';
$string['filemanager_virus_mailsubject2'] = 'Virusalert for website {SITENAME}: scanner failed'; 
$string['filemanager_virus_mailmessage2'] = 
'There was a problem while scanning the file
{PATH} ({FILENAME}) for viruses.

The output of the virusscanner is as follows:

{OUTPUT}

The currently logged in user was

{FULL_NAME} ({USERNAME})

The file was rejected because virusscanning is mandatory.

Kind regards,

Your automated webmaster';
$string['filemanager_add_files_success'] = 'Success adding file \'{FILENAME}\' as \'{TARGET}\' to folder \'{PATH}\'';
$string['filemanager_add_files_error'] = 'Error adding file \'{FILENAME}\' as \'{TARGET}\' to folder \'{PATH}\'';
$string['filemanager_add_files_results'] = 'Files added: {SAVECOUNT}, files ignored: {SKIPCOUNT}';
$string['filemanager_add_files_filetype_mismatch'] = 'Error: mismatch between filename (\'{FILENAME}\') and filetype (\'{FILETYPE}\'); file skipped. Rename the file (e.g. to \'{TARGET}\') and try again.';
$string['filemanager_add_files_filetype_banned'] = 'Error: file \'{FILENAME}\' and filetype \'{FILETYPE}\' not allowed; file skipped.';
$string['filemanager_add_files_forbidden_name'] = 'Error adding file \'{FILENAME}\' as \'{TARGET}\' to folder \'{PATH}\': name is not acceptable. Please rename the file and try again.';

$string['filemanager_title_thumb_file'] = '{FILENAME} (size (bytes): {SIZE}, modified: {DATIM})';
$string['filemanager_title_thumb_image'] = '{FILENAME} (dimensions: {WIDTH}x{HEIGHT}, size (bytes): {SIZE}, modified: {DATIM})';

$comment['tools_intro'] = 'Start of translations for the tools';
$string['tools_intro'] = 'Here you can find various tools.
<p>With the Translate Tool you can add new translations to the program or modify existing translations.
<p>The Backup Tool allows for downloading the complete database for this site.
<p>Log View allows you to browse through log messages.
<p>Please select a tool from the menu';
$string['tools_header'] = 'Tools';
$string['menu_translatetool'] = 'Translate Tool';
$string['menu_translatetool_title'] = 'Create new or modify existing translations';
$string['menu_backuptool'] =  'Backup Tool';
$string['menu_backuptool_title'] = 'Create a backup of the database';
$string['menu_logview'] =  'Log View';
$string['menu_logview_title'] = 'Browse log messages';
$string['menu_update'] =  'Update Manager';
$string['menu_update_title'] = 'View/update internal version numbers';

$string['translatetool_add_a_language'] = 'Add a language';
$string['translatetool_add_a_language_title'] = 'Click here to add a new language';

$string['icon_language_edit'] = 'Edit language properties';
$string['icon_language_edit_alt'] = 'icon edit';
$string['icon_language_edit_text'] = 'E';
$string['translatetool_edit_translation'] = '{LANGUAGE_NAME} ({LANGUAGE_KEY})';
$string['translatetool_edit_translation_title'] = 'Click here to edit translations for this language';


$string['translatetool_add_language_header'] = 'Add a new language';
$string['translatetool_add_language_explanation'] = 'Here you can add a new language to the CMS by entering the language name and other information. Once the language has been added to the CMS, you can add translations of all texts used throughout the CMS.';

$string['translatetool_edit_language_header'] = 'Edit language properties';
$string['translatetool_edit_language_explanation'] = 'Here you can modify the language properties.';

$string['translatetool_language_name_label'] = '~Name (expressed in the language itself)';
$string['translatetool_language_name_title'] = 'Please enter the name of the language';
$string['translatetool_language_is_active_label'] = 'Active';
$string['translatetool_language_is_active_title'] = 'Check the box to make this language active';
$string['translatetool_language_is_active_check'] = 'Mark the language as ~active';
$string['translatetool_language_parent_label'] = '~Parent language';
$string['translatetool_language_parent_title'] = 'Select the language to use as a basis for the new translations';
$string['translatetool_language_key_label'] = '~Language code (ISO 639)';
$string['translatetool_language_key_title'] = 'Enter the 2 or 3 letter code for this language (lowercase)';


$string['translatetool_parent_language_none_option'] = '(none)';
$string['translatetool_parent_language_none_title'] = 'Null, nothing: this language is not based on an existing one';
$string['translatetool_parent_language_option_option'] = '{LANGUAGE_NAME} ({LANGUAGE_KEY})';
$string['translatetool_parent_language_option_title'] = 'Select this language ({LANGUAGE_NAME}) as the basis for the translations';

$string['invalid_language'] = 'Invalid language code \'{LANGUAGE_KEY}\'';

$string['translatetool_language_savenew_success'] = 'Success adding new language {LANGUAGE_NAME} ({LANGUAGE_KEY})';
$string['translatetool_language_savenew_failure'] = 'There were problems saving the new language';

$string['translatetool_language_save_success'] = 'Success saving properties of language {LANGUAGE_NAME} ({LANGUAGE_KEY})';
$string['translatetool_language_save_failure'] = 'There were problems saving the language properties';

$string['invalid_language_domain'] = 'Invalid language domain \'{FULL_DOMAIN}\'';

$string['translatetool_domain_grouping_program'] = 'Program';
$string['translatetool_domain_grouping_modules'] = 'Modules';
$string['translatetool_domain_grouping_themes'] = 'Themes';
$string['translatetool_domain_grouping_install'] = 'Install';

$string['translatetool_edit_language_domain_header'] = 'Translation for {LANGUAGE_NAME} ({LANGUAGE_KEY}) - {FULL_DOMAIN}';
$string['translatetool_edit_language_domain_explanation'] = 'Here you can modify the translations. Please note that codes like {EXAMPLE_HTML} and {EXAMPLE_VARIABLE} should be left as-is (untranslated); these codes are necessary for the proper working of the program and should be copied verbatim.<p>
Also note the tilde is used as a shorthand notation to define hotkeys in dialogs. For example, a field labeled <strong>{EXAMPLE_TILDE}File</strong> could be selected by pressing the keycombination [Alt-F] or [Cmnd-F]. The German translation could be <strong>{EXAMPLE_TILDE}Datei</strong>, with hotkey [Alt-D] or [Cmnd-D]. It is important that a hotkey is unique within a dialog, i.e. if you, the translator, have used the letter D already in that dialog, your German translation might become <strong>Dat{EXAMPLE_TILDE}ei</strong>, yielding hotkey [Alt-E] or [Cmnd-E].<p>
In short: it is up to you, the translator, to choose a good set of hotkeys by carefully placing the tildes.';

$string['translatetool_full_name_label'] = 'Translator name';
$string['translatetool_full_name_title'] = 'Here you can enter your name (for crediting you as translation author)';
$string['translatetool_email_label'] = 'Translator email address';
$string['translatetool_email_title'] = 'Here you can enter your email address (for crediting you as translation author)';
$string['translatetool_notes_label'] = 'Translator notes';
$string['translatetool_notes_title'] = 'This is a place to store your comments about this translation';
$string['translatetool_submit_label'] = 'The fields below are used for submitting your translation to the Website@School project.
<p>You can use the \'translator notes\' field below to pass on information about your translation; this field will become the body of an email message.';
$string['translatetool_submit_check'] = 'Submit this translation';
$string['translatetool_submit_title'] = 'Check this box to submit your translation to the Website@School project';

$string['translatetool_no_changes_to_save'] = 'No changes need to be saved for {LANGUAGE_NAME} ({LANGUAGE_KEY}) - {FULL_DOMAIN}';

$string['translatetool_translation_save_success'] = 'Success saving changes for {LANGUAGE_NAME} ({LANGUAGE_KEY}) - {FULL_DOMAIN}';
$string['translatetool_translation_save_failure'] = 'An error occorred while saving changes for {LANGUAGE_NAME} ({LANGUAGE_KEY}) - {FULL_DOMAIN}';
$string['translatetool_translation_submit_success'] = 'Success submitting changes for {LANGUAGE_NAME} ({LANGUAGE_KEY}) - {FULL_DOMAIN}';
$string['translatetool_translation_submit_failure'] = 'An error occorred while submitting changes for {LANGUAGE_NAME} ({LANGUAGE_KEY}) - {FULL_DOMAIN}';

$string['backuptool_header'] = 'Backup Tool';
$string['backuptool_intro'] = 'Here you can create a backup copy of the database. You should keep this backup in a safe place. Follow the link below to start the backup process.<p>
<strong>Note:</strong><br>
This does not download the files from the data directory (<strong>{DATADIRECTORY}</strong>); you have to find another means to make a backup of those files. You may want to consult your ISP.';
$string['backuptool_download'] = 'Download Backup';
$string['backuptool_download_title'] = 'Click here for the database backup';
$string['backuptool_error'] = 'There were problems generating the backup';

$string['logview_error'] = 'There were errors retrieving log messages';
$string['logview_no_messages'] = 'There are no messages to show';
$string['logview_nr'] = 'Nr';
$string['logview_datim'] = 'Date/time';
$string['logview_remote_addr'] = 'Address';
$string['logview_user_id'] = 'User';
$string['logview_priority'] = 'Priority';
$string['logview_message'] = 'Message';

$comment['update_header'] = 'Here are the prompts dealing with the automatic update routine(s)';
$string['update_header'] = 'Update Manager';
$string['update_intro'] = 'This is the update manager. Below is an overview of the current internal en external versions of the core system and the various subsystems. If there is a discrepancy between the two versions, you can perform the update by following the link \'[Update]\' or perform the installation of the subsystem by following the link \'[Install]\' in the last column';
$string['update_version_database'] = 'Internal';
$string['update_version_manifest'] = 'Version';
$string['update_release_date_manifest'] = 'Date';
$string['update_release_manifest'] = 'Release';
$string['update_status'] = 'Status';
$string['update_core'] = 'core';
$string['update_core_success'] = 'Success updating core system to version {VERSION}';
$string['update_core_error'] = 'Error updating core system to version {VERSION}';
$string['update_core_warnning_core_goes_first'] = 'Warning: the core system must be updated first';
$string['update_subsystem_languages'] = 'Languages';
$string['update_subsystem_language_success'] = 'Success updating/installing language {LANGUAGE}';
$string['update_subsystem_language_error'] = 'Error updating/installing language {LANGUAGE}';
$string['update_subsystem_modules'] = 'Modules';
$string['update_subsystem_module_success'] = 'Success updating/installing module {MODULE}';
$string['update_subsystem_module_error'] = 'Error updating/installing module {MODULE}';
$string['update_subsystem_themes'] = 'Themes';
$string['update_subsystem_theme_success'] = 'Success updating/installing theme {THEME}';
$string['update_subsystem_theme_error'] = 'Error updating/installing theme {THEME}';
$string['update_status_ok'] = 'OK';
$string['update_status_error'] = 'ERROR';
$string['update_status_update'] = 'Update';
$string['update_status_install'] = 'Install';
$string['update_version_database_too_old'] = 'The internal version {VERSION} is too old; you have to re-install and/or upgrade manually.';
$string['update_field_value_too_long'] = 'Table \'{TABLE}\' field \'{FIELD}\': content is longer than {LENGTH} characters: \'{CONTENT}\'.';
$string['update_please_correct_field_value_manually'] = 'The number of fields that need to be shortened manually (outside of Website@School) is {ERRORS}';



?>