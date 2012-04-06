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

/** /program/languages/nl/admin.php - translated messages for /program/admin.php (Dutch)
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: admin.php,v 1.16 2012/04/06 18:47:24 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['translatetool_title'] = 'Beheer';
$string['translatetool_description'] = 'Dit bestand bevat vertalingen van de beheer-interface';

$string['generated_in'] = 'gegenereerd op {DATE} in {QUERIES} queries en {SECONDS} seconden';
$string['logo_websiteatschool'] = 'logo Website@School&reg;';
$string['end_this_session'] = 'de sessie be&euml;ndigen, de gebruiker afmelden';
$string['logout_username'] = '{USERNAME} afmelden';
$string['view_public_area'] = 'naar de openbare site';
$string['go_view_public_area_no_logout'] = 'naar het openbare deel van de site, zonder af te melden';
$string['check_was_release'] = 'controleer de status van uw versie van Website@School';
$string['version_x_y_z'] = 'versie {VERSION}';
$string['login_user_success'] = 'U bent ingelogd als: {USERNAME}';
$string['job_access_denied'] = 'U hebt geen permissie voor dit commando; toegang geweigerd';
$string['task_access_denied'] = 'U hebt geen permissie voor deze taak; toegang geweigerd';
$string['unknown_job'] = 'Opdracht "{JOB}" niet herkend';

$string['name_startcenter'] = 'start';
$string['name_pagemanager'] = 'paginas';
$string['name_filemanager'] = 'bestanden';
$string['name_modulemanager'] = 'modules';
$string['name_accountmanager'] = 'accounts';
$string['name_configurationmanager'] = 'configuratie';
$string['name_statistics'] = 'statistieken';
$string['name_tools'] = 'gereedschappen';
$string['name_help'] = 'help';

$string['description_startcenter'] = 'Website@School Start';
$string['description_pagemanager'] = 'Paginabeheer';
$string['description_filemanager'] = 'Bestandsbeheer';
$string['description_modulemanager'] = 'Modulebeheer';
$string['description_accountmanager'] = 'Accountbeheer';
$string['description_configurationmanager'] = 'Configuratiebeheer';
$string['description_statistics'] = 'Statistieken';
$string['description_tools'] = 'Gereedschappen';
$string['description_help'] = 'Helpfunctie (opent in een nieuw venster)';

$string['no_access_startcenter'] = 'Toegang tot Website@School Start is uitgeschakeld voor uw account';
$string['no_access_pagemanager'] = 'Toegang tot Paginabeheer is uitgeschakeld voor uw account';
$string['no_access_filemanager'] = 'Toegang tot Bestandsbeheer is uitgeschakeld voor uw account';
$string['no_access_modulemanager'] = 'Toegang tot Modulebeheer is uitgeschakeld voor uw account';
$string['no_access_accountmanager'] = 'Toegang tot Accountbeheer is uitgeschakeld voor uw account';
$string['no_access_configurationmanager'] = 'Toegang tot Configuratiebeheer is uitgeschakeld voor uw account';
$string['no_access_statistics'] = 'Toegang tot Statistieken is uitgeschakeld voor uw account';
$string['no_access_tools'] = 'Toegang tot Gereedschappen is uitgeschakeld voor uw account';
$string['no_access_help'] = 'Toegang tot Helpfunctie is uitgeschakeld voor uw account';

$string['access_denied'] = 'Geen toegang';
$string['no_access_admin_php'] = 'Toegang tot Website@School is uitgeschakeld voor uw account. Volg een van onderstaande links om door te gaan:';
$string['view_login_dialog'] = 'login';
$string['url'] = 'URL';
$string['public_area'] = 'Publiekelijk gebied';
$string['private_area'] = 'Beveiligd gebied';
$string['no_areas_available'] = 'Geen geschikte gebieden beschikbaar';
$string['inactive'] = 'inactief';
$string['menu'] = 'Menu';

$string['add_a_page'] = 'Nieuwe pagina';
$string['add_a_page_title'] = 'Klik hier om een nieuwe pagina toe te voegen';
$string['add_a_section'] = 'Nieuwe sectie';
$string['add_a_section_title'] = 'Klik hier om een nieuwe sectie toe te voegen';

$string['area_admin_access_denied'] = 'U hebt geen beheerdersrechten voor gebied {AREA}';
$string['hidden'] = 'verborgen';
$string['embargo'] = 'embargo';
$string['expired'] = 'vervallen';

$string['spacer'] = 'spati&euml;ring';

$string['icon_delete'] = 'Pagina of sectie wissen';
$string['icon_delete_access_denied'] = 'U hebt geen permissie om deze pagina/sectie te wissen';
$string['icon_delete_alt'] = 'icoon wissen';
$string['icon_delete_text'] = 'W';

$string['icon_edit'] = 'Pagina of sectie bewerken';
$string['icon_edit_access_denied'] = 'U hebt geen permissie om deze pagina/sectie te bewerken';
$string['icon_edit_alt'] = 'icoon bewerken';
$string['icon_edit_text'] = 'B';

$string['icon_default'] = 'Promoveer deze pagina/sectie tot startpagina of -sectie';
$string['icon_default_access_denied'] = 'U hebt geen permissie om deze pagina/sectie tot startpagina of -sectie te promoveren';
$string['icon_default_alt'] = 'icoon startpagina of startsectie';
$string['icon_default_text'] = 'S';
$string['icon_not_default_text'] = '_';
$string['icon_not_default_alt'] = 'icoon niet startpagina of startsectie';
$string['icon_is_default'] = 'Dit is momenteel de startpagina of -sectie';

$string['icon_preview_page'] = 'Toon voorbeeld van de pagina (in nieuw venster)';
$string['icon_preview_page_access_denied'] = 'U hebt geen permissie om een voorbeeld van de pagina te tonen';
$string['icon_preview_page_alt'] = 'icoon pagina';
$string['icon_preview_page_text'] = 'V';

$string['icon_open_section'] = 'Open de sectie (klap de boomstructuur 1 niveau uit)';
$string['icon_open_section_alt'] = 'icoon gesloten map';
$string['icon_open_section_text'] = '+';

$string['icon_close_section'] = 'Sluit de sectie (klap de onderliggende boomstructuur in)';
$string['icon_close_section_alt'] = 'icoon geopende map';
$string['icon_close_section_text'] = '-';

$string['icon_open_area'] = 'Open het gebied (klap de boomstructuur helemaal uit)';
$string['icon_open_area_alt'] = 'icoon gesloten map';
$string['icon_open_area_text'] = '+';

$string['icon_close_area'] = 'Sluit het gebied (klap de onderliggende boomstructuur in)';
$string['icon_close_area_alt'] = 'icoon geopende map';
$string['icon_close_area_text'] = '-';

$string['icon_open_site'] = 'Open alle gebieden (klap de boomstructuur in alle gebieden helemaal uit)';
$string['icon_open_site_alt'] = 'icoon gesloten map';
$string['icon_open_site_text'] = '+';

$string['icon_close_site'] = 'Sluit alle gebieden (klap de onderliggende boomstructuur in alle gebieden in)';
$string['icon_close_site_alt'] = 'icoon geopende map';
$string['icon_close_site_text'] = '-';


$string['icon_visible'] = 'Deze pagina of sectie is zichtbaar (niet verborgen, geen embargo, niet vervallen)';
$string['icon_visible_access_denied'] = 'U hebt geen permissie om deze pagina/sectie te bewerken';
$string['icon_visible_alt'] = 'icoon zichtbaar';
$string['icon_visible_text'] = '_';

$string['icon_invisible_hidden'] = 'Deze pagina/sectie is onzichtbaar (verborgen)';
$string['icon_invisible_embargo'] = 'Deze pagina/sectie is onzichtbaar (embargo tot {DATIM})';
$string['icon_invisible_expiry'] = 'Deze pagina/sectie is onzichtbaar (vervallen op {DATIM})';
$string['icon_invisible_alt'] = 'icoon onzichtbaar';
$string['icon_invisible_text'] = 'O';

$string['too_many_levels'] = 'Kan sectie {NODE} niet weergeven: boomstructuur is te diep';
$string['no_nodes_yet'] = 'Dit gebied bevat nog geen enkele pagina of sectie';

$string['set_tree_view'] = 'Stel beeld in:';
$string['set_view_minimal'] = 'ingeklapt';
$string['set_view_custom'] = 'aangepast';
$string['set_view_maximal'] = 'uitgeklapt';
$string['set_view_minimal_title'] = 'bekijk de ingeklapte boom (alle secties gesloten)';
$string['set_view_custom_title'] = 'bekijk de gedeeltelijk uitgeklapte boom (niet alle secties geopend)';
$string['set_view_maximal_title'] = 'bekijk de uitgeklapte boom (alle secties geopend)';

$string['access_denied_preview'] = 'U hebt onvoldoende permissies om een voorbeeld van deze pagina te zien. Sluit a.u.b. het venster en ga terug naar paginabeheer';

$string['invalid_node'] = 'Ongeldige sectie of pagina {NODE}';
$string['task_set_default_access_denied'] = 'U hebt geen permissie om pagina/sectie {NODE} tot standaard te promoveren';

$string['startcenter_welcome'] = 'Welkom';
$string['startcenter_welcome_text'] = 'Dit is de startpagina van het Website@School beheersysteem, hier kunt u uw site bijhouden en aanpassen.';

$string['click_here_for_documentation'] = 'Klik hier voor de documentatie';
$string['icon_documentation'] = 'icoon boek';

$string['click_to_send_mail_to_us'] = 'Klik hier om ons een email te sturen';
$string['icon_sendmail'] = 'icoon enveloppe';
$string['please_send_us_mail'] = 'Als uw schoolsite online is, zouden we het op prijs stellen een email met de URL te ontvangen. Door op het icoon te klikken kunt u ons mailen of stuur een email naar <strong>{MAILTO}</strong>. Alvast bedankt namens het Website@School team.';
$string['view_documentation'] = 'Klik op het icoon om de documentatie te lezen (opent in een nieuw venster)';
$string['icon_information'] = 'icoon informatie';
$string['check_new_version'] = 'Klik op het icoon om te zien of er een nieuwe versie is (uw versie is {VERSION})';

$string['task_node_add_access_denied'] = 'U hebt geen permissie om een sectie/pagina toe te voegen';


$string['add_a_page_header'] = 'Nieuwe pagina';
$string['add_a_section_header'] = 'Nieuwe sectie';
$string['add_section_explanation'] = 'Hier kunt u een nieuwe sectie toevoegen door een titel en andere gegevens in te voeren. Zodra de nieuwe sectie is toegevoegd kunt u paginas en subsecties aan de sectie toevoegen.';
$string['add_page_explanation'] = 'Hier kunt u een nieuwe sectie toevoegen door een titel, een module en andere gegevens in te voeren.';
$string['add_node_title'] = 'Om~schrijving';
$string['add_node_title_title'] = 'Geef hier de omschrijving van de pagina of sectie op';
$string['add_node_linktext'] = '~Naam';
$string['add_node_linktext_title'] = 'Geef hier de korte naam (wordt gebruikt bij navigatie)';
$string['add_node_parent_section'] = '~Bovenliggende sectie';
$string['add_node_parent_section_title'] = 'Geef hier aan in welke sectie de nieuwe sectie opgenomen moet worden';

$string['add_node_module'] = '~Module';
$string['add_node_module_title'] = 'Kies hier een geschikte module voor deze pagina';
$string['add_node_visible'] = '~Zichtbaar';
$string['add_node_visible_title'] = 'Deze optie maakt dat de pagina zichtbaar is in de navigatie en bovendien toegankelijk is';
$string['add_node_hidden'] = '~Verborgen';
$string['add_node_hidden_title'] = 'Als u de pagina/sectie verbergt dan is deze niet zichtbaar in de navigatie maar wel toegankelijk';
$string['add_node_embargo'] = '~Embargo';
$string['add_node_embargo_title'] = 'Onder embargo betekent dat de pagina pas zichtbaar en toegankelijk wordt na het moment dat het embargo vervalt';
$string['add_node_initial_visibility'] = 'Initi&euml;le zichtbaarheid';
$string['add_node_initial_visibility_title'] = 'Kies hier of de pagina/sectie meteen zichbaar wordt of niet';


$string['cancelled'] = 'Geannuleerd';
$string['error_adding_node'] = 'Fout bij toevoegen pagina of sectie aan database';
$string['page_added'] = 'Toegevoegd aan gebied {AREA}: nieuwe pagina {NODE} {LINK} ({TITLE})';
$string['section_added'] = 'Toegevoegd aan gebied {AREA}: nieuwe sectie {NODE} {LINK} ({TITLE})';
$string['node_has_no_name'] = '(pagina/sectie {NODE} heeft geen naam)';

$string['new_default_node_in_section'] = 'Nieuwe startpagina/sectie {NEW} in sectie {PARENT} in gebied {AREA}';
$string['new_default_node_in_area'] = 'Nieuwe startpagina/sectie {NEW} in gebied {AREA}';
$string['old_default_node'] = '(oude startpagina/sectie was {OLD})';


$string['edit_basic'] = 'Basiseigenschappen';
$string['edit_advanced'] = 'Geavanceerd';
$string['edit_content'] = 'Inhoud';

$string['edit_basic_page_title'] = 'Bewerk de basiseigenschappen van pagina {NODE}';
$string['edit_basic_section_title'] = 'Bewerk de basiseigenschappen van sectie {NODE}';
$string['edit_advanced_page_title'] = 'Bewerk de geavanceerde eigenschappen van pagina {NODE}';
$string['edit_advanced_section_title'] = 'Bewerk de geavanceerde eigenschappen van sectie {NODE}';
$string['edit_content_title'] = 'Bewerk de inhoud van pagina {NODE}';

$string['task_edit_page_access_denied'] = 'U hebt onvoldoende rechten om pagina {NODE} te bewerken';
$string['task_edit_section_access_denied'] = 'U hebt onvoldoende rechten om sectie {NODE} te bewerken';
$string['page_is_locked_by'] = 'Pagina {NODE} is vergrendeld door {FULL_NAME} sinds {LOCK_TIME} ({USERNAME} is ingelogd vanaf {IP_ADDR} sinds {LOGIN_TIME})';
$string['section_is_locked_by'] = 'Sectie {NODE} is vergrendeld door {FULL_NAME} sinds {LOCK_TIME} ({USERNAME} is ingelogd vanaf {IP_ADDR} sinds {LOGIN_TIME})';

$string['edit_a_page_header'] = 'Bewerk de basiseigenschappen van pagina {NODE}';
$string['edit_a_section_header'] = 'Bewerk de basiseigenschappen van sectie {NODE}';
$string['edit_page_explanation'] = 'Hier kunt u de basiseigenschappen van een pagina aanpassen, zoals de titel of de module';
$string['edit_section_explanation'] = 'Hier kunt u de basiseigenschappen van een pagina aanpassen, zoals de titel';

$string['edit_node_linktext'] = '~Naam';
$string['edit_node_linktext_title'] = 'Geef hier de korte naam (wordt gebruikt bij navigatie)';

$string['edit_node_title'] = 'Om~schrijving';
$string['edit_node_title_title'] = 'Geef hier de omschrijving van de pagina of sectie op';

$string['edit_node_parent_section'] = '~Bovenliggende sectie';
$string['edit_node_parent_section_title'] = 'Geef hier aan in welke sectie de nieuwe sectie opgenomen moet worden';

$string['edit_node_module'] = '~Module';
$string['edit_node_module_title'] = 'Kies hier een geschikte module voor deze pagina';

$string['edit_node_sort_order'] = '~Volgorde';
$string['edit_node_sort_order_title'] = 'Kies hier de juiste plaats in de sorteervolgorde';


$string['options_sort_order_at_top'] = 'Helemaal vooraan';
$string['options_sort_order_at_top_title'] = 'Plaats deze pagina/sectie voor alle andere paginas/secties in deze (sub)sectie';
$string['options_sort_order_after_page'] = 'Na pagina {NODE}';
$string['options_sort_order_after_section'] = 'Na sectie {NODE}';

$string['options_parents_at_toplevel'] = 'Op het hoogste niveau in dit gebied';
$string['options_parents_at_toplevel_title'] = 'Plaats deze pagina niet in een (sub)sectie maar op het hoogste niveau';
$string['options_parents_section'] = 'Sectie {NODE}';



$string['edit_a_page_advanced_header'] = 'Bewerk de geavanceerde eigenschappen van pagina {NODE}';
$string['edit_a_section_advanced_header'] = 'Bewerk de geavanceerde eigenschappen van sectie {NODE}';
$string['edit_page_advanced_explanation'] = 'Hier kunt u geavanceerde eigenschappan van een pagina aanpassen, zoals de embargodatum en de verborgen pagina';
$string['edit_section_advanced_explanation'] = 'Hier kunt u geavanceerde eigenschappan van een sectie aanpassen, zoals de embargodatum en de verborgen pagina';


$string['edit_node_area_id'] = '~Gebied';
$string['edit_node_area_id_title'] = 'Selecteer een gebied waarnaar u deze pagina/sectie wilt verplaatsen';

$string['edit_node_link_image'] = '~Pictogram bestandsnaam';
$string['edit_node_link_image_title'] = 'Voer bestandsnaam en -pad van het pictogrambestand in';

$string['edit_node_link_image_width'] = '~Breedte';
$string['edit_node_link_image_width_title'] = 'Geef de breedte van het pictogram (in pixels)';

$string['edit_node_link_image_height'] = '~Hoogte';
$string['edit_node_link_image_height_title'] = 'Geef de hoogte van het pictogram (in pixels)';

$string['edit_node_link_target'] = '~Doel';
$string['edit_node_link_target_title'] = 'Geef het doel op, bijv. _blank voor openen in nieuw venster (zie handboek)';

$string['edit_node_link_href'] = '~URL';
$string['edit_node_link_href_title'] = 'Geef de volledige URL van de externe webpagina op';

$string['edit_node_is_hidden'] = 'Zichtbaarheid in navigatie';
$string['edit_node_is_hidden_title'] = 'Een verborgen pagina/sectie is wel toegankelijk maar is onzichbaar in navigatie';
$string['edit_node_is_hidden_label'] = '~Verberg deze pagina/sectie';

$string['edit_node_is_readonly'] = 'Schrijfbeveiliging';
$string['edit_node_is_readonly_title'] = 'Een beveiligde pagina kan niet per ongeluk gewijzigd worden';
$string['edit_node_is_readonly_label'] = 'Beveilig deze pagina/sectie tegen ~wijzigingen';

$string['edit_node_embargo'] = '~Embargo';
$string['edit_node_embargo_title'] = 'Geef de datum/tijd op wanneer deze pagina/sectie automatisch gepubliceerd wordt';

$string['edit_node_expiry'] = 'Verva~ldatum';
$string['edit_node_expiry_title'] = 'Geef de datum/tijd op wanneer deze pagina/sectie automatisch vervalt (totaal onzichtbaar wordt)';

$string['edit_node_style_label'] = 'E~xtra stijlinformatie op pagina/sectieniveau';
$string['edit_node_style_title'] = 'Aanvullende stijlinformatie voor pagina of sectie (inclusief onderliggende paginas/secties)';

$string['options_public_area'] = 'Publiekelijk gebied {AREA} ({AREANAME})';
$string['options_private_area'] = 'Beveiligd gebied {AREA} ({AREANAME})';
$string['options_public_area_inactive'] = 'Publiekelijk gebied {AREA} ({AREANAME}) (inactief)';
$string['options_private_area_inactive'] = 'Beveiligd gebied {AREA} ({AREANAME}) (inactief)';

$string['node_no_longer_readonly'] = 'Schrijfbeveiliging verwijderd voor pagina/sectie {NODE_FULL_NAME}';
$string['node_still_readonly'] = 'Schrijfbeveiliging nog steeds van toepassing op pagina/sectie {NODE_FULL_NAME}';

$string['error_saving_node'] = 'Fout bij het opslaan van de gewijzigde  pagina/sectie';
$string['page_saved'] = 'Pagina {NODE_FULL_NAME} opgeslagen';
$string['section_saved'] = 'Sectie {NODE_FULL_NAME} opgeslagen';
$string['node_was_edited'] = 'Pagina/sectie in gebied {AREA} gewijzigd: {NODE_FULL_NAME}';
$string['node_was_edited_and_moved'] = 'Pagina/sectie {NODE_FULL_NAME} verplaatst van gebied {AREA} naar gebied {NEWAREA}';
$string['error_moving_subtree'] = 'Fout bij verplaatsen {NODE_FULL_NAME} van gebied {AREA} naar gebied {NEWAREA}';
$string['success_moving_subtree'] = 'Tak {NODE_FULL_NAME} met succes verplaatst van gebied {AREA} naar gebied {NEWAREA}';
$string['subtree_was_moved'] = 'Tak in gebied {AREA} varplaatst naar gebied {NEWAREA}: {NODE_FULL_NAME}';


$string['task_delete_node_access_denied'] = 'U hebt onvoldoende rechten om pagina/sectie {NODE} te verwijderen';
$string['task_delete_node_limited'] = 'U kunt sectie {NODE_FULL_NAME} niet wissen omdat deze sectie subsecties bevat';
$string['task_delete_node_is_readonly'] = 'U kunt pagina/sectie {NODE_FULL_NAME} niet wissen wegens de schrijfbeveiliging';

$string['page_full_name'] = 'Pagina {NODE_FULL_NAME}';
$string['section_full_name'] = 'Sectie {NODE_FULL_NAME}';
$string['delete_a_page_header'] = 'Bevestig wissen van pagina {NODE_FULL_NAME}';
$string['delete_a_section_header'] = 'Bevestig wissen van sectie {NODE_FULL_NAME}';
$string['delete_page_explanation'] = 'U staat op het punt om de volgende pagina definitief te wissen:';
$string['delete_section_explanation'] = 'U staat op het punt om de volgende sectie en alle paginas en subsecties die deze bevat definitief te wissen:';
$string['delete_are_you_sure'] = 'Weet u zeker dat u verder wilt gaan?';

$string['error_deleting_node'] = 'Fout bij wissen pagina/sectie {NODE_FULL_NAME} in gebied {AREA}';
$string['page_deleted'] = 'Gewist uit gebied {AREA}: pagina {NODE_FULL_NAME}';
$string['section_deleted'] = 'Gewist uit gebied {AREA}: sectie {NODE_FULL_NAME}';
$string['errors_deleting_childeren'] = 'Aantal fouten bij wissen paginas/secties uit sectie {NODE_FULL_NAME}: {COUNT}';

$string['error_editing_node_content'] = 'Fout bij bewerken inhoud van pagina {NODE_FULL_NAME}';
$string['page_content_edited'] = '(Gebied {AREA}) Inhoud van pagina gewijzigd: {NODE_FULL_NAME}';





$string['configurationmanager_intro'] = 'Dit is Configuratiebeheer. Kies een taak uit het menu.';
$string['configurationmanager_header'] = 'Configuratiebeheer';
$string['menu_areas'] = 'Gebieden';
$string['menu_areas_title'] = 'Inzien, toevoegen, wijzigen en verwijderen van gebieden';
$string['menu_site'] =  'Site';
$string['menu_site_title'] = 'Inzien of wijzigen van de siteconfiguratie';
$string['menu_users'] =  'Gebruikers';
$string['menu_users_title'] = 'Inzien, toevoegen, wijzigien en verwijderen van gebruikersaccounts';
$string['menu_alerts'] =  'Alerts';
$string['menu_alerts_title'] = 'Inzien, toevoegen, wijzigien en verwijderen van alerts';
$string['task_unknown'] = 'Onbekende taak {TASK}';
$string['chore_unknown'] = 'Onbekende opgave {CHORE}';


$string['areamanager_add_an_area'] = 'Nieuw gebied';
$string['areamanager_add_an_area_title'] = 'Klik hier om een nieuw gebied toe te voegen';

$string['icon_area_default'] = 'Promoveer dit gebied tot startgebied';
$string['icon_area_default_access_denied'] = 'U hebt geen permissie om dit gebied tot startgebied te promoveren';
$string['icon_area_default_alt'] = 'icoon startgebied';
$string['icon_area_default_text'] = 'H';
$string['icon_area_not_default_text'] = '_';
$string['icon_area_not_default_alt'] = 'icon niet startgenbied';
$string['icon_area_is_default'] = 'Dit gebied is het startgebied';

$string['icon_area_delete'] = 'Gebied verwijderen';
$string['icon_area_delete_access_denied'] = 'U hebt geen permissie om dit gebied te verwijderen';
$string['icon_area_delete_alt'] = 'icoon verwijderen';
$string['icon_area_delete_text'] = 'V';

$string['icon_area_edit'] = 'Bewerk dit gebied';
$string['icon_area_edit_access_denied'] = 'U hebt geen permissie om dit gebied te bewerken';
$string['icon_area_edit_alt'] = 'icoon bewerken';
$string['icon_area_edit_text'] = 'B';

$string['area_edit_public_title'] = '(publiek) {AREA_FULL_NAME} ({AREA}, {SORT_ORDER})';
$string['area_edit_private_title'] = '(beveiligd) {AREA_FULL_NAME} ({AREA}, {SORT_ORDER})';

$string['area_delete_public_title'] = '{AREA_FULL_NAME} (publiekelijk gebied {AREA})';
$string['area_delete_private_title'] = '{AREA_FULL_NAME} (beveiligd gebied {AREA})';

$string['invalid_area'] = 'Ongeldig gebied {AREA}';
$string['error_deleting_area'] = 'Fout bij verwijderen gebied {AREA} ({AREA_FULL_NAME})';
$string['error_deleting_area_not_empty'] = 'Kan gebied {AREA} ({AREA_FULL_NAME}) niet verwijderen: gebied is niet leeg. (aantal paginas/secties: {NODES})';
$string['error_deleting_area_dir_not_empty'] = 'De datamap van gebied \'{AREA_FULL_NAME}\' ({AREA}) is nog niet leeg. Verwijder eerst de bestanden en submappen.';
$string['area_deleted'] = 'Gebied {AREA} ({AREA_FULL_NAME}) verwijderd';

$string['delete_an_area_header'] = 'Bevestig verwijdering van gebied {AREA_FULL_NAME}';
$string['delete_area_explanation'] = 'U staat op het punt om het volgende gebied te verwijderen:';

$string['task_area_add_access_denied'] = 'U hebt geen permissie om een gebied toe te voegen';
$string['task_set_default_area_access_denied'] = 'U hebt geen permissie om gebied {AREA} tot startgebied te promoveren';



$string['areamanager_add_area_header'] = 'Nieuw gebied toevoegen';
$string['areamanager_add_area_explanation'] = '
Hier kunt u een nieuw gebied toevoegen door de naam en andere informatie op te geven. Zodra het nieuwe gebied is gemaakt kunt u paginas en secties toevoegen via Paginabeheer. Merk op dat het niet mogelijk is om achteraf een beveiligd gebied om te zetten in een publiekelijk gebied of andersom.';

$string['areamanager_add_area_title_label'] = '~Naam';
$string['areamanager_add_area_title_title'] = 'Geef hier de naam van het nieuwe gebied op';

$string['areamanager_add_area_is_private_label'] = 'Beveiligd gebied';
$string['areamanager_add_area_is_private_title'] = 'Aanvinken om dit gebied als beveiligd gebied aan te merken';
$string['areamanager_add_area_is_private_check'] = 'Merk dit gebied als ~beveiligd aan (kan later niet meer gewijzigd worden)';
$string['areamanager_add_area_path_label'] = '~Datamap (kan later niet meer gewijzigd worden)';
$string['areamanager_add_area_path_title'] = 'Deze map bevat de gegevensbestanden voor dit gebied';
$string['areamanager_add_area_theme_id_label'] = '~Thema';
$string['areamanager_add_area_theme_id_title'] = 'Selecteer het thema voor dit gebied';

$string['errors_saving_data'] = 'Er waren problemen bij het opslaan van de gegevens. Aantal fouten: {ERRORS}';
$string['success_saving_data'] =  'Wijzigingen zijn opgeslagen in de database';

$string['areamanager_edit_theme_header'] = 'Configureer thema \'{THEME_NAME}\' voor gebied {AREA}';
$string['areamanager_edit_theme_explanation'] = '
Hier kunt u het thema {THEME_NAME} voor gebied {AREA} ({AREA_FULL_NAME}) configureren.<br>De eigenschappen die u hier kunt veranderen zijn uitsluitend van toepassing op dit gebied, d.w.z. elk gebied kan zijn eigen unieke eigenschappen hebben in combinatie met een bepaald thema.';


$string['areamanager_menu_edit'] = 'Basiseigenschappen';
$string['areamanager_menu_edit_title'] = 'Bewerk de basiseigenschappen';
$string['areamanager_menu_edit_theme'] = 'Themaconfiguratie';
$string['areamanager_menu_edit_theme_title'] = 'Configureer het thema voor dit gebied';
$string['areamanager_menu_reset_theme'] = 'Reset thema';
$string['areamanager_menu_reset_theme_title'] = 'Ga terug naar de standaardinstellingen van dit thema voor dit gebied';


$string['areamanager_edit_area_header'] = 'Bewerk de basiseigenschappen van dit gebied';
$string['areamanager_edit_area_explanation'] = 'Hier kunt u de basiseigenschappen van een gebied bewerken. Het is <b>niet</b> mogelijk om achteraf nog een publiekelijk gebied om te zetten in een beveiligd gebied of omgekeerd.';
$string['areamanager_edit_area_title_label'] = '~Naam';
$string['areamanager_edit_area_title_title'] = 'De naam van het gebied';
$string['areamanager_edit_area_is_active_label'] = 'Actief gebied';
$string['areamanager_edit_area_is_active_title'] = 'Aanvinken om het gebied actief te maken';
$string['areamanager_edit_area_is_active_check'] = 'Merk dit gebied aan als actie~f';
$string['areamanager_edit_area_is_private_label'] = 'Beveiligd gebied';
$string['areamanager_edit_area_is_private_title'] = 'Als dit veld is aangevinkt is het gebied een beveiligd gebied';
$string['areamanager_edit_area_is_private_check'] = 'Merk dit gebied aan als ~beveiligd gebied (kan niet gewijzigd worden)';
$string['areamanager_edit_area_path_label'] = '~Datamap (padnaam kan niet gewijzigd worden)';
$string['areamanager_edit_area_path_title'] = 'Deze map bevat de gegevensbestanden voor dit gebied';
$string['areamanager_edit_area_metadata_label'] = '~Metadata';
$string['areamanager_edit_area_metadata_title'] = 'Deze informatie wordt bij elke pagina meegezonden in de HTML-header';
$string['areamanager_edit_area_sort_order_label'] = '~Sorteervolgorde';
$string['areamanager_edit_area_sort_order_title'] = 'De weergavevolgorde van gebieden wordt bepaald door dit getal';
$string['areamanager_edit_area_theme_id_label'] = '~Thema';
$string['areamanager_edit_area_theme_id_title'] = 'Selecteer het thema voor dit gebied';

$string['areamanager_save_area_success'] = 'Wijzigingen in gebied {AREA} ({AREA_FULL_NAME}) opgeslagen';
$string['areamanager_save_area_failure'] = 'Er waren problemen bij het opslaan van gebied {AREA} ({AREA_FULL_NAME})';

$string['areamanager_savenew_area_success'] = 'Gegevens van nieuw gebied {AREA} ({AREA_FULL_NAME}) opgeslagen';
$string['areamanager_savenew_area_failure'] = 'Er waren problemen bij het opslaan van het nieuwe gebied';


$string['site_config_header'] = 'Siteconfiguratie';
$string['site_config_explanation'] = 'Hier kunt u de globale parameters voor de site aanpassen.';
$string['site_config_version_label'] = 'Intern versienummer (niet wijzigen s.v.p.)';
$string['site_config_version_title'] = 'Intern versienummer';
$string['site_config_salt_label'] = 'Veiligheidscode';
$string['site_config_salt_title'] = 'Deze tekst wordt gebruikt bij het genereren van sessiecodes, dit maakt voor het kwaadwillenden moeilijker om binnen te dringen';
$string['site_config_session_name_label'] = 'Sessienaam';
$string['site_config_session_name_title'] = 'De naam van het cookie in de browser van de gebruiker';
$string['site_config_session_expiry_label'] = 'Maximale tijdsduur sessie (in seconden, standaard 86400)';
$string['site_config_session_expiry_title'] = 'Een sessie bestaat niet langer dan deze tijdsduur, 86400s = 24h';
$string['site_config_login_max_failures_label'] = 'Maximumaantal inlogpogingen (standaard 10)';
$string['site_config_login_max_failures_title'] = 'De gebruiker komt tijdelijk op de zwarte lijst na dit aantal mislukte logins';
$string['site_config_login_failures_interval_label'] = 'Interval van mislukte logins (minuten, standaard=12)';
$string['site_config_login_failures_interval_title'] = 'Alleen de mislukte inlogpogingen in dit tijdsinterval tellen mee voor de zwarte lijst';
$string['site_config_login_bypass_interval_label'] = 'Geldigheidsduur van een tijdelijk wachtwoord (minuten, standaard 30)';
$string['site_config_login_bypass_interval_title'] = 'Een tijdelijk wachtwoord is beperkt geldig';
$string['site_config_login_blacklist_interval_label'] = 'Tijdsduur zwarte lijst (minuten, standaard 8)';
$string['site_config_login_blacklist_interval_title'] = 'Hoe lang staat een gebruiker op de zwarte lijst na teveel foute inlogpogingen';
$string['site_config_title_label'] = 'Titel van de website';
$string['site_config_title_title'] = 'Dit is de naam van de website';
$string['site_config_website_from_address_label'] = 'Website From: e-mail adres';
$string['site_config_website_from_address_title'] = 'Gebruikt afzenderadres voor verzonden mail vanuit het CMS';
$string['site_config_website_replyto_address_label'] = 'Website Reply-To: e-mail adres';
$string['site_config_website_replyto_address_title'] = 'Antwoorden aan het CMS zouden aan dit adres gericht moeten worden';
$string['site_config_language_key_label'] = 'Standaardtaal (2-lettercode kleine letters, standaard \'en\' (Engels))';
$string['site_config_language_key_title'] = 'Dit is de standaard-taal voor de website';

$string['site_config_pagination_height_label'] = 'Aantal items per scherm (in lange lijsten)';
$string['site_config_pagination_height_title'] = 'Hier stelt u de schermgrootte in voor weergave van lange lijsten';
$string['site_config_pagination_width_label'] = 'Maximum aantal schermen (in lange lijsten)';
$string['site_config_pagination_width_title'] = 'Dit is het aantal zichtbare links in de navigatiebalk bij paginering';

$string['site_config_editor_label'] = 'Standaard tekstverwerker';
$string['site_config_editor_title'] = 'Dit is de standaard tekstverwerker voor nieuwe gebruikers-accounts';
$string['site_config_editor_ckeditor_option'] = 'CKEditor';
$string['site_config_editor_ckeditor_title'] = 'Een uitgebreide op JavaScript gebaseerde WYSIWYG tekstverwerker';
$string['site_config_editor_fckeditor_option'] = 'FCKeditor';
$string['site_config_editor_fckeditor_title'] = 'Een uitgebreide op JavaScript gebaseerde WYSIWYG tekstverwerker';
$string['site_config_editor_plain_option'] = 'Teksteditor';
$string['site_config_editor_plain_title'] = 'Eenvoudige editor voor platte tekst';
$string['site_config_friendly_url_label'] = '';
$string['site_config_friendly_url_title'] = 'Aanvinken om vriendelijke URLs te genereren in menu-navigatie';
$string['site_config_friendly_url_option'] = 'Gebruik proxy-vriendelijke URLs';
$string['site_config_clamscan_path_label'] = 'Volledig pad naar het ClamAV virus scanner programma';
$string['site_config_clamscan_path_title'] = 'Laat dit veld leeg indien de ClamAV virus scanner niet beschikbaar is';
$string['site_config_clamscan_mandatory_label'] = '';
$string['site_config_clamscan_mandatory_title'] = 'Aanvinken voor verplichte scan op virussen bij uploads van bestanden';
$string['site_config_clamscan_mandatory_option'] = 'Scan bestanden op virussen bij uploaden';
$string['site_config_upload_max_files_label'] = 'Maximumaantal bestanden per upload (non-Java)';
$string['site_config_upload_max_files_title'] = 'Dit is het maximumaantal gelijktijdige uploads bij Bestandsbeheer';
$string['site_config_thumbnail_dimension_label'] = 'Maximale hoogte en breedte voor voorbeeldplaatjes (thumbnails)';
$string['site_config_thumbnail_dimension_title'] = 'Dit zijn de maximale afmetingen van voorbeelden die gemaakt worden tijdens het uploaden van afbeeldingen';
$string['site_config_filemanager_files_label'] = 'Lijst van toegestane extensies bij Bestandsbeheer (komma-gescheiden)';
$string['site_config_filemanager_files_title'] = 'Alle file uploads worden getoetst aan deze extensies';
$string['site_config_filemanager_images_label'] = 'Lijst van extensies die als afbeelding worden beschouwd (komma-gescheiden)';
$string['site_config_filemanager_images_title'] = 'Uitsluitend bestanden met een van deze extensies zijn zichtbaar bij bladeren naar afbeeldingen in FCK Editor';
$string['site_config_filemanager_flash_label'] = 'Lijst van extensies die als flashbestand worden beschouwd (komma-gescheiden)';
$string['site_config_filemanager_flash_title'] = 'Uitsluitend bestanden met een van deze extensies zijn zichtbaar bij bladeren naar flashbestanden in FCK Editor';
$string['site_config_pagemanager_at_end_label'] = '';
$string['site_config_pagemanager_at_end_title'] = 'Aanvinken om paginas en secties toe te voegen aan het eind van een (sub)sectie';
$string['site_config_pagemanager_at_end_option'] = 'Nieuwe paginas/secties achteraan toevoegen';

$string['area_theme_reset'] = 'Standaardinstellingen hersteld voor thema {THEME_NAME} en gebied {AREA} ({AREA_FULL_NAME})';
$string['error_area_theme_reset'] = 'Er waren problemen bij het herstellen van de standaardinstellingen voor thema {THEME_NAME} in gebied {AREA} ({AREA_FULL_NAME})';

$string['reset_theme_area_header'] = 'Standaardinstellingen herstellen voor thema{THEME_NAME} in gebied {AREA} ({AREA_FULL_NAME})';
$string['reset_theme_area_explanation'] = 'U staat op het punt om de bestaande instellingen te overschrijven met de standaardinstellingen';
$string['reset_theme_area_are_you_sure'] = 'Weet u zeker dat u door wilt gaan?';


$string['accountmanager_header'] = 'Account Manager';
$string['accountmanager_intro'] = 'Dit is de Account Manager. Maak een keuze uit het menu';
$string['accountmanager_summary'] = 'Samenvatting';
$string['accountmanager_users'] = 'Gebruikers';
$string['accountmanager_groups'] = 'Groepen';
$string['accountmanager_active'] = 'Actief';
$string['accountmanager_inactive'] = 'Inactief';
$string['accountmanager_total'] = 'Totaal';

$string['menu_users'] =  'Gebruikers';
$string['menu_users_title'] = 'Inzien, toevoegen, bewerken of uitschakelen van gebruikersaccounts';
$string['menu_groups'] =  'Groepen';
$string['menu_groups_title'] = 'Inzien, toevoegen, bewerken of uitschakelen van groepen';

$string['groupmanager_add_a_group'] = 'Nieuwe groep';
$string['groupmanager_add_a_group_title'] = 'Klik hier om een nieuwe groep toe te voegen';

$string['groupmanager_group_edit_title'] = 'Bewerk de gegevens van deze groep';
$string['groupmanager_group_capacity_edit_title'] = 'Bewerk de gegevens van deze groep/hoedanigheid';

$string['icon_group_delete'] = 'Deze groep verwijderen';
$string['icon_group_delete_alt'] = 'icoon verwijderen';
$string['icon_group_delete_text'] = 'W';

$string['icon_group_edit'] = 'Bewerk deze groep';
$string['icon_group_edit_alt'] = 'icoon bewerken';
$string['icon_group_edit_text'] = 'B';

$string['groupmanager_add_group_header'] = 'Voeg een nieuwe groep toe';
$string['groupmanager_add_group_explanation'] = '
Hier kunt u een nieuwe groep toevoegen door gegevens van de groep en andere informatie in te voeren, met name de hoedanigheden waarin gebruikers lid kunnen worden van een groep. Zodra de groep is toegevoegd kunt u overige gegevens (toegangscontrole) invoeren, per hoedanigheid';
$string['groupmanager_add_group_name_label'] = '~Naam';
$string['groupmanager_add_group_name_title'] = 'Voer de (korte) naam van de groep in (moet uniek zijn)';
$string['groupmanager_add_group_fullname_label'] = '~Beschrijving';
$string['groupmanager_add_group_fullname_title'] = 'Voer de lange naam (beschrijving) van de nieuwe groep in';
$string['groupmanager_add_group_is_active_label'] = 'Actieve groep';
$string['groupmanager_add_group_is_active_title'] = 'Aanvinken om de groep actief te maken';
$string['groupmanager_add_group_is_active_check'] = '~Actief';
$string['groupmanager_add_group_capacity_label'] = 'Hoedanigheid ~{INDEX}';
$string['groupmanager_add_group_capacity_title'] = 'Kies een mogelijke hoedanigheid voor groepslidmaatschap';

$string['groupmanager_savenew_group_failure'] = 'Er waren problemen met het opslaan van de nieuwe groep';
$string['groupmanager_savenew_group_success'] = 'De groep {GROUP} ({GROUP_FULL_NAME}) is toegevoegd aan de database';

$string['error_invalid_parameters'] = 'Fout: ongeldige parameters voor verzoek';
$string['error_retrieving_data'] = 'Fout: kan geen data uit database ophalen';

$string['groupmanager_edit_group_header'] = 'Bewerk een groep';
$string['groupmanager_edit_group_explanation'] = 'Hier kunt u de basiseigenschappen van deze groep wijzigen.';
$string['groupmanager_edit_group_name_label'] = '~Naam';
$string['groupmanager_edit_group_name_title'] = 'Voer de (korte) naam van de groep in (moet uniek zijn)';
$string['groupmanager_edit_group_fullname_label'] = '~Beschrijving';
$string['groupmanager_edit_group_fullname_title'] = 'Voer de lange naam (beschrijving) van de groep in';
$string['groupmanager_edit_group_is_active_label'] = 'Actieve groep';
$string['groupmanager_edit_group_is_active_title'] = 'Aanvinken om de groep actief te maken';
$string['groupmanager_edit_group_is_active_check'] = '~Actief';
$string['groupmanager_edit_group_capacity_label'] = 'Hoedanigheid ~{INDEX}';
$string['groupmanager_edit_group_capacity_title'] = 'Kies een mogelijke hoedanigheid voor groepslidmaatschap';
$string['groupmanager_edit_group_path_label'] = '~Datamap (padnaam kan niet gewijzigd worden)';
$string['groupmanager_edit_group_path_title'] = 'Deze map bevat de gedeelde gegevensbestanden van deze groep';

$string['groupmanager_edit_group_success'] = 'Wijzigingen in {GROUP} ({GROUP_FULL_NAME}) zijn opgeslagen';


$string['groupmanager_delete_group_header'] = 'Bevestig wissen van groep {GROUP} ({GROUP_FULL_NAME})';
$string['groupmanager_delete_group_explanation'] = 'U staat op het punt de volgende groep en bijbehorende hoedanigheden en gebruikersaccounts te wissen:';
$string['groupmanager_delete_group_breadcrumb'] = 'wissen';
$string['groupmanager_delete_group_group'] = '{GROUP_FULL_NAME} ({GROUP})';
$string['groupmanager_delete_group_capacity'] = '{CAPACITY}: {COUNT}';


$string['groupmanager_delete_group_success'] = 'Groep {GROUP} ({GROUP_FULL_NAME}) is met succes verwijderd';
$string['groupmanager_delete_group_failure'] = 'Er zijn fouten opgetreden bij het verwijderen van groep {GROUP} ({GROUP_FULL_NAME})';

$string['usermanager_delete_group_dir_not_empty'] = 'De datamap van groep \'{GROUP_FULL_NAME}\' ({GROUP}) is nog niet leeg. Verwijder eerst de bestanden en submappen.';
$string['usermanager_delete_group_not_self'] = 'U kunt de groep \'{GROUP_FULL_NAME}\' ({GROUP}) niet verwijderen omdat u zelf verbonden bent met deze groep als \'{CAPACITY}\'. U moet eerst deze verbinding met de groep verbreken voordat u de groep kunt verwijderen';
$string['usermanager_delete_group_capacity_not_self'] = '{FIELD}: U kunt de hoedanigheid \'{CAPACITY}\' van \'{GROUP_FULL_NAME}\' ({GROUP}) niet verwijderen omdat u zelf verbonden bent met deze groep in die hoedanigheid. U moet eerst uw eigen verbinding met deze groep/hoedanigheid verbreken voordat u de hoedanigheid kunt verwijderen';


$string['groupmanager_capacity_overview_header'] = 'Overzicht: {GROUP} - {CAPACITY}';
$string['groupmanager_capacity_overview_explanation'] = 'Hier is een overzicht van alle gebruikersaccounts die verbonden zijn met deze groep ({GROUP_FULL_NAME}) en hoedanigheid ({CAPACITY})';
$string['groupmanager_capacity_overview_no_members'] = 'Er zijn op dit moment geen gebruikersaccounts verbonden aan deze groep ({GROUP_FULL_NAME}) en hoedanigheid ({CAPACITY})';

$string['groupmanager_group_menu_edit'] = 'Basiseigenschappen';
$string['groupmanager_group_menu_edit_title'] = 'Bewerk de basiseigenschappen';

$string['groupmanager_capacity_intranet_header'] = 'Toegang tot intranet: {GROUP} - {CAPACITY}';
$string['groupmanager_capacity_intranet_explanation'] = 'Kies de rollen voor toegang tot het intranet die u wilt toewijzen aan deze groep ({GROUP_FULL_NAME}) en hoedanigheid ({CAPACITY}) en druk op [Opslaan] om de gegevens op te slaan.';


// $string['errors_saving_data'] = 'There were problems saving the changes. Errorcount: {ERRORS}';
// $string['success_saving_data'] =  'Success saving changes to the database';

$string['acl_error_saving_field'] = '{FIELD}: fout bij opslaan gegevens';


$string['usermanager_user_edit_title'] = 'Bewerk \'{FULL_NAME}\'';

$string['breadcrumb_you_are_here'] = 'U bent hier:';
$string['breadcrumb_next'] = '&gt;';

$string['menu_groupcapacity_overview'] =  'Overzicht';
$string['menu_groupcapacity_overview_title'] = 'Geef overzicht van verbonden gebruikersaccounts van deze groep/hoedanigheid';
$string['menu_groupcapacity_intranet'] =  'Intranet';
$string['menu_groupcapacity_intranet_title'] = 'Wijzig de permissies voor toegang tot intranet (beveiligde gebieden)';

$string['menu_groupcapacity_module_title'] = 'Wijzig de permissies voor deze module';
$string['menu_groupcapacity_admin'] =  'Beheer';
$string['menu_groupcapacity_admin_title'] = 'Wijzig de permissies voor beheer (webmaster-functies)';
$string['menu_groupcapacity_pagemanager'] =  'Paginabeheer';
$string['menu_groupcapacity_pagemanager_title'] = 'Wijzig permissies van paginabeheer (webmaster-functies)';

$string['acl_role_none_option'] = '--';
$string['acl_role_none_title'] = 'Nul, niets, nada: deze rol geeft geen enkele permissie';
$string['acl_role_guru_option'] = 'Goeroe';
$string['acl_role_guru_title'] = 'Alles: deze rol geeft alle permissies die er zijn, misschien zelfs nog meer';
$string['acl_role_intranet_access_option'] = 'Toegang';
$string['acl_role_intranet_access_title'] = 'Toegang tot intranet toegestaan; beveiligde gebieden kunnen bezocht worden';
$string['acl_role_unknown'] = 'onbekend';

$string['acl_role_pagemanager_contentmaster_option'] = 'Inhoudbeheerder';
$string['acl_role_pagemanager_contentmaster_title'] = 'Alleen de pagina-inhoud kan worden gewijzigd';
$string['acl_role_pagemanager_pagemaster_option'] = 'Paginabeheerder';
$string['acl_role_pagemanager_pagemaster_title'] = 'Pagina-eigenschappen en -inhoud kunnen worden gewijzigd';
$string['acl_role_pagemanager_sectionmaster_option'] = 'Sectiebeheerder';
$string['acl_role_pagemanager_sectionmaster_title'] = 'Sectie-eigenschappen kunnen worden gewijzigd en subsecties en paginas kunnen worden toegevoegd';
$string['acl_role_pagemanager_areamaster_option'] = 'Gebiedbeheerder';
$string['acl_role_pagemanager_areamaster_title'] = 'Gebiedeigenschappen kunnen worden gewijzigd en secties en paginas kunnen worden toegevoegd (op het hoogste niveau)';
$string['acl_role_pagemanager_sitemaster_option'] = 'Sitebeheerder';
$string['acl_role_pagemanager_sitemaster_title'] = 'Eigenschappen voor huidige en toekomstige gebieden kunnen worden gewijzigd en gebieden, secties en paginas kunnen worden toegevoegd';

$string['acl_all_areas_label'] = 'Alle huidige en toekomstige gebieden';
$string['acl_all_private_areas_label'] = 'Alle huidige en toekomstige (beveiligde) gebieden';
$string['acl_area_label'] = 'Gebied {AREA}: {AREA_FULL_NAME}';
$string['acl_area_inactive_label'] = 'Gebied {AREA}: {AREA_FULL_NAME} (inactive)';
$string['acl_page_label'] = 'Pagina {NODE}: {NODE_FULL_NAME}';
$string['acl_section_label'] = 'Sectie {NODE}: {NODE_FULL_NAME}';

$string['acl_column_header_realm'] = 'Sfeer';
$string['acl_column_header_role'] = 'Rol';
$string['acl_column_header_related'] = 'Gerelateerd';


$string['acl_job_guru_label'] = 'Goeroe';
$string['acl_job_guru_check'] = 'Alle permissies';
$string['acl_job_guru_title'] = 'Aanvinken betekent toestemming voor alle huidige en toekomstige taken';

$string['acl_job_1_label'] = 'Startcenter';
$string['acl_job_1_check'] = 'Basale beheerdertaken';
$string['acl_job_1_title'] = 'Aanvinken voor toegang tot admin.php';

$string['acl_job_2_label'] = 'Beheren van pagina\'s en secties';
$string['acl_job_2_check'] = 'Paginabeheer';
$string['acl_job_2_title'] = 'Aanvinken voor toegang tot paginabeheer';

$string['acl_job_4_label'] = 'Upload van bestanden';
$string['acl_job_4_check'] = 'Bestandsbeheer';
$string['acl_job_4_title'] = 'Aanvinken voor toegang tot bestandsbeheer';

$string['acl_job_8_label'] = 'Beheer van modules';
$string['acl_job_8_check'] = 'Modulebeheer';
$string['acl_job_8_title'] = 'Aanvinken voor toegang tot modulebeheer';

$string['acl_job_16_label'] = 'Gebruikers en groepen';
$string['acl_job_16_check'] = 'Accountbeheer';
$string['acl_job_16_title'] = 'Aanvinken voor toegang tot accountbeheer';

$string['acl_job_32_label'] = 'Configuratie en beheer van gebieden';
$string['acl_job_32_check'] = 'Configuratiebeheer';
$string['acl_job_32_title'] = 'Aanvinken voor toegang tot configuratiebeheer';

$string['acl_job_64_label'] = 'Statistiek en performance';
$string['acl_job_64_check'] = 'Statistieken';
$string['acl_job_64_title'] = 'Aanvinken voor toegang tot statistieken';

$string['acl_job_128_label'] = 'Gereedschappen';
$string['acl_job_128_check'] = 'Vertalingen';
$string['acl_job_128_title'] = 'Aanvinken voor toegang tot vertaalgereedschap';

$string['acl_job_256_label'] = 'Gereedschappen';
$string['acl_job_256_check'] = 'Backup';
$string['acl_job_256_title'] = 'Aanvinken voor toegang tot backup-gereedschap';

$string['acl_job_512_label'] = 'Gereedschappen';
$string['acl_job_512_check'] = 'Logbestand';
$string['acl_job_512_title'] = 'Aanvinken voor toegang tot logbestand';

$string['acl_job_1024_label'] = 'Gereedschappen';
$string['acl_job_1024_check'] = 'Updatebeheer';
$string['acl_job_1024_title'] = 'Aanvinken voor toegang tot updatebeheer';

$string['groupmanager_capacity_admin_header'] = 'Toegang tot beheer: {GROUP} - {CAPACITY}';
$string['groupmanager_capacity_admin_explanation'] = 'Kies hieronder de permissies die u wilt toewijzen aan deze groep ({GROUP_FULL_NAME}) en hoedanigheid ({CAPACITY}) en druk op [Opslaan] om de wijzigingen op te slaan.<p><strong>Opmerking</strong><br>De Goeroe-optie impliceert <em>alle</em> overige permissies, zowel de huidige alsmede toekomstige. Bedenk goed aan wie u deze permissies toekent';

$string['groupmanager_capacity_pagemanager_header'] = 'Toegang tot paginabeheer: {GROUP} - {CAPACITY}';
$string['groupmanager_capacity_pagemanager_explanation'] = 'Kies de rollen voor toegang tot het paginabeheer die u wilt toewijzen aan deze groep ({GROUP_FULL_NAME}) en hoedanigheid ({CAPACITY}) en druk op [Opslaan] om de gegevens op te slaan.';

$string['usermanager_all_users_title'] = 'Geef een lijst van alle gebruikers weer';
$string['usermanager_all_users'] = 'Alle gebruikers';
$string['usermanager_all_users_count'] = 'Alle gebruikers ({COUNT})';
$string['usermanager_users_nogroup_title'] = 'Geef een lijst van gebruikers zonder groep';
$string['usermanager_users_nogroup'] = 'Geen groep';
$string['usermanager_users_nogroup_count'] = 'Geen groep ({COUNT})';
$string['usermanager_users_group_title'] = 'Geef lijst van gebruikes in groep {GROUP_FULL_NAME} weer';
$string['usermanager_users_group'] = '{GROUP}';
$string['usermanager_users_group_count'] = '{GROUP} ({COUNT})';

$string['usermanager_add_a_user'] = 'Nieuwe gebruiker toevoegen';
$string['usermanager_add_a_user_title'] = 'Klik hier om een nieuwe gebruiker toe te voegen';

$string['usermanager_user_edit'] = '{FULL_NAME} ({USERNAME})';
$string['usermanager_user_edit_title'] = 'Bewerk deze gebruiker';

$string['icon_user_delete'] = 'Gebruiker wissen';
$string['icon_user_delete_alt'] = 'icoon wissen';
$string['icon_user_delete_text'] = 'W';

$string['icon_user_edit'] = 'Gebruiker bewerken';
$string['icon_user_edit_alt'] = 'icoon bewerken';
$string['icon_user_edit_text'] = 'B';


$string['usermanager_add_user_header'] = 'Nieuwe gebruiker toevoegen';
$string['usermanager_add_user_explanation'] = '
Hier kunt u een nieuw gebruikersaccount toevoegen door de onderstaande informatie in te voeren. Nadat het gebruikersaccount is aangemaakt kunt u overige informatie invoeren (bijv. toegangscontrole) voor dit account';

$string['usermanager_add_username_label'] = '~Naam';
$string['usermanager_add_username_title'] = 'Geef hier de loginnaam van het nieuwe gebruikersaccount op (moet uniek zijn)';
$string['usermanager_add_user_fullname_label'] = '~Volledige naam';
$string['usermanager_add_user_fullname_title'] = 'Geef hier de volledige naam van de gebruiker';

$string['usermanager_add_user_password1_label'] = '~Wachtwoord';
$string['usermanager_add_user_password1_title'] = 'Minimale eisen: lengte: {MIN_LENGTH}, cijfers: {MIN_DIGIT}, onderkast: {MIN_LOWER}, kapitalen: {MIN_UPPER}';
$string['usermanager_add_user_password2_label'] = '~Bevestig wachtwoord';
$string['usermanager_add_user_password2_title'] = 'Minimale eisen: lengte: {MIN_LENGTH}, cijfers: {MIN_DIGIT}, onderkast: {MIN_LOWER}, kapitalen: {MIN_UPPER}';
$string['usermanager_add_user_email_label'] = 'Net~post';
$string['usermanager_add_user_email_title'] = 'Geef hier het netpostadres van de gebruiker';
$string['usermanager_add_user_is_active_label'] = 'Actieve gebruiker';
$string['usermanager_add_user_is_active_title'] = 'Aanvinken om het gebruikersaccount te activeren';
$string['usermanager_add_user_is_active_check'] = '~Merk account aan als actief';

$string['usermanager_savenew_user_failure'] = 'Er waren problemen bij het opslaan van het nieuwe gebruikersaccount';
$string['usermanager_savenew_user_success'] = 'Nieuw gebruikersaccount {USERNAME} ({FULL_NAME}) is succesvol toegevoegd';


$string['usermanager_delete_user_header'] = 'Bevestig wissen van gebruikersaccount {USERNAME} ({FULL_NAME})';
$string['usermanager_delete_user_explanation'] = 'U staat op het punt om het volgende gebruikersaccount definitief te wissen:';
$string['usermanager_delete_user_breadcrumb'] = 'wissen';
$string['usermanager_delete_user_user'] = '{FULL_NAME} ({USERNAME})';
$string['usermanager_delete_user_success'] = 'Gebruikersaccount {USERNAME} ({FULL_NAME}) gewist';
$string['usermanager_delete_user_failure'] = 'Er waren problemen bij het wissen van het account {USERNAME} ({FULL_NAME})';
$string['usermanager_delete_user_dir_not_empty'] = 'De datamap van {FULL_NAME} ({USERNAME}) is nog niet leeg. Verwijder eerst de bestanden en submappen.';
$string['usermanager_delete_user_not_self'] = 'U kunt uw eigen gebruikersaccount niet wissen';


$string['menu_user_basic'] =  'Basiseigenschapen';
$string['menu_user_basic_title'] = 'Bewerk de basiseigenschappen van deze gebruiker';
$string['menu_user_advanced'] =  'Geavanceerd';
$string['menu_user_advanced_title'] = 'Bewerk de geavanceerde eigenschappen van deze gebruiker';
$string['menu_user_groups'] =  'Groepen';
$string['menu_user_groups_title'] = 'Stel de groepslidmaatschappen voor dit gebruikersaccount in';
$string['menu_user_intranet'] =  'Intranet';
$string['menu_user_intranet_title'] = 'Wijzig de permissies voor toegang tot intranet (beveiligde gebieden)';

$string['menu_user_module_title'] = 'Wijzig de permissies voor deze module';
$string['menu_user_admin'] =  'Beheer';
$string['menu_user_admin_title'] = 'Wijzig de permissies voor beheer (webmaster-functies)';
$string['menu_user_pagemanager'] =  'Paginabeheer';
$string['menu_user_pagemanager_title'] = 'Wijzig permissies van paginabeheer (webmaster-functies)';


$string['usermanager_edit_user_header'] = 'Bewerk {USERNAME} ({FULL_NAME})';
$string['usermanager_edit_user_explanation'] = 'Hier kunt u de basiseigenschappen van het gebruikersaccount {FULL_NAME} ({USERNAME}) aanpassen.';
$string['usermanager_edit_username_label'] = '~Naam';
$string['usermanager_edit_username_title'] = 'Geef hier de loginnaam van het nieuwe gebruikersaccount op (moet uniek zijn)';
$string['usermanager_edit_user_fullname_label'] = '~Volledige naam';
$string['usermanager_edit_user_fullname_title'] = 'Geef hier de volledige naam van de gebruiker';
$string['usermanager_edit_user_password1_label'] = '~Wachtwoord';
$string['usermanager_edit_user_password1_title'] = 'Minimale eisen: lengte: {MIN_LENGTH}, cijfers: {MIN_DIGIT}, onderkast: {MIN_LOWER}, kapitalen: {MIN_UPPER}';
$string['usermanager_edit_user_password2_label'] = '~Bevestig wachtwoord';
$string['usermanager_edit_user_password2_title'] = 'Minimale eisen: lengte: {MIN_LENGTH}, cijfers: {MIN_DIGIT}, onderkast: {MIN_LOWER}, kapitalen: {MIN_UPPER}';
$string['usermanager_edit_user_email_label'] = 'Net~post';
$string['usermanager_edit_user_email_title'] = 'Geef hier het netpostadres van de gebruiker';
$string['usermanager_edit_user_is_active_label'] = 'Actieve gebruiker';
$string['usermanager_edit_user_is_active_title'] = 'Aanvinken om het gebruikersaccount te activeren';
$string['usermanager_edit_user_is_active_check'] = '~Merk het account aan als actief';
$string['usermanager_edit_user_redirect_label'] = 'Omleidin~g (waarheen na uitloggen)';
$string['usermanager_edit_user_redirect_title'] = 'Geef een URL om naartoe te gaan na uitloggen (blanco betekent het standaard-gebied van de webstek)';
$string['usermanager_edit_user_language_label'] = '~Taal';
$string['usermanager_edit_user_language_title'] = 'Selecteer de taal voor deze gebruiker';
$string['usermanager_edit_user_editor_label'] = 'Te~kstverwerker';
$string['usermanager_edit_user_editor_title'] = 'Selecteer de standaard tekstverwerker/editor voor deze gebruiker';
$string['usermanager_edit_user_skin_label'] = 'Weergav~e';
$string['usermanager_edit_user_skin_title'] = 'Selecteer de standaardweergave voor deze gebruiker';

$string['usermanager_edit_user_skin_base_option'] = 'Basis';
$string['usermanager_edit_user_skin_base_title'] = 'Standaardweergave (grafisch)';
$string['usermanager_edit_user_skin_textonly_option'] = 'Tekst';
$string['usermanager_edit_user_skin_textonly_title'] = 'Weergave met uitsluitend tekst';
$string['usermanager_edit_user_skin_braille_option'] = 'Braille';
$string['usermanager_edit_user_skin_braille_title'] = 'Voor Braille-terminals en spraaksynthesizers';
$string['usermanager_edit_user_skin_big_option'] = 'Groot';
$string['usermanager_edit_user_skin_big_title'] = 'Grotere letters en afbeeldingen';
$string['usermanager_edit_user_skin_lowvision_option'] = 'Slechtziend';
$string['usermanager_edit_user_skin_lowvision_title'] = 'Weergave voor slechtzienden (hoog contrast)';

$string['usermanager_edit_user_path_label'] = '~Datamap (padnaam kan niet gewijzigd worden)';
$string['usermanager_edit_user_path_title'] = 'Deze map bevat de persoonlijke gegevensbestanden van deze gebruiker';

$string['usermanager_save_user_failure'] = 'Problemen bij het opslaan gebruikersaccount {USERNAME} ({FULL_NAME})';
$string['usermanager_save_user_success'] = 'Gebruikersaccount {USERNAME} ({FULL_NAME}) is succesvol opgeslagen';

$string['pagination_start'] = 'Ga naar:';
$string['pagination_glue'] = '&nbsp;';
$string['pagination_previous'] = 'Vorige';
$string['pagination_next'] = 'Volgende';
$string['pagination_all'] = 'Alle';
$string['pagination_more_left'] = '&lt;';
$string['pagination_more_right'] = '&gt;';
$string['pagination_count_of_total'] = '[{FIRST}-{LAST} van {TOTAL}]';


$string['usermanager_user_groups_header'] = 'Lidmaatschappen van {USERNAME} ({FULL_NAME})';
$string['usermanager_user_groups_explanation'] = 'Hier kunt u de groepslidmaatschappen van gebruiker {FULL_NAME} ({USERNAME}) beheren.';
$string['usermanager_user_groups_add'] = 'Groepslidmaatschap toevoegen';
$string['usermanager_user_groups_add_title'] = 'Klik hier om een nieuw groepslidmaatschap toe te voegen voor deze gebruiker';
$string['usermanager_user_groups'] = '{GROUP} ({GROUP_FULL_NAME}) / {CAPACITY}';

$string['icon_membership_delete'] = 'Be&euml;indig dit groepslidmaatschap';
$string['icon_membership_delete_alt'] = 'icon wissen';
$string['icon_membership_delete_text'] = 'W';

$string['usermanager_user_groupadd_header'] = 'Groepslidmaatschap toevoegen aan gebruikersaccount {USERNAME} ({FULL_NAME})';
$string['usermanager_user_groupadd_explanation'] = '
Kies uit onderstaande lijst de gewenste groep en hoedanigheid en druk op [Opslaan] om de gebruiker {FULL_NAME} ({USERNAME}) toe te voegen aan de gekozen groep.';

$string['usermanager_user_groupadd_groupcapacity_label'] = '~Nieuwe groep/hoedanigheid';
$string['usermanager_user_groupadd_groupcapacity_title'] = 'Kies een groep/hoedanigheid-combinatie uit te lijst';
$string['usermanager_user_groupadd_groupcapacity_none_available'] = '-- Geen groepen beschikbaar --';

$string['usermanager_delete_usergroup_success'] = 'Groepslidmaatschap {GROUP} ({GROUP_FULL_NAME}) / {CAPACITY} met succes be&euml;indigd';
$string['usermanager_delete_usergroup_failure'] = 'Er waren problemen bij het be&euml;indigen van groepslidmaatschap {GROUP} ({GROUP_FULL_NAME}) / {CAPACITY}';



$string['usermanager_intranet_header'] = 'Toegang tot intranet: {USERNAME} ({FULL_NAME})';
$string['usermanager_intranet_explanation'] = 'Kies de rollen voor toegang tot het intranet die u wilt toewijzen aan deze gebruiker ({FULL_NAME}) en druk op [Opslaan] om de gegevens op te slaan.';

$string['usermanager_admin_header'] = 'Toegang tot beheer: {USERNAME} ({FULL_NAME})';
$string['usermanager_admin_explanation'] = 'Kies hieronder de permissies die u wilt toewijzen aan deze gebruiker ({FULL_NAME}) en druk op [Opslaan] om de wijzigingen op te slaan.<p><strong>Opmerking</strong><br>De Goeroe-optie impliceert <em>alle</em> overige permissies, zowel de huidige alsmede toekomstige. Bedenk goed aan wie u deze permissies toekent';

$string['usermanager_pagemanager_header'] = 'Toegang tot paginabeheer: {USERNAME} ({FULL_NAME})';
$string['usermanager_pagemanager_explanation'] = 'Kies de rollen voor toegang tot het paginabeheer die u wilt toewijzen aan deze gebruiker ({FULL_NAME}) en druk op [Opslaan] om de gegevens op te slaan.';


$string['filemanager_root'] = 'Alle bestanden';
$string['filemanager_root_title'] = 'Naar de map op het hoogste niveau';
$string['filemanager_personal'] = 'Mijn bestanden';
$string['filemanager_personal_title'] = 'Naar de map met persoonlijke bestanden';
$string['filemanager_areas'] = 'Gebieden';
$string['filemanager_areas_title'] = 'Naar de mappen met bestanden per gebied';
$string['filemanager_groups'] =  'Groepen';
$string['filemanager_groups_title'] = 'Naar de mappen met bestanden per groep';
$string['filemanager_users'] =  'Gebruikers';
$string['filemanager_users_title'] = 'Naar de mappen met bestanden per gebruiker';
$string['filemanager_navigate_to'] = 'Naar \'{DIRECTORY}\'';
$string['filemanager_preview'] = 'Voorbeeld van bestand \'{FILENAME}\'';
$string['filemanager_select'] = 'Selecteer bestand \'{FILENAME}\'';

$string['filemanager_delete_file'] = 'Wis bestand \'{FILENAME}\'';
$string['filemanager_delete_directory'] = 'Verwijder map \'{DIRECTORY}\'';
$string['filemanager_select_directory_entry_title'] = 'Aanvinken om deze map te selecteren';
$string['filemanager_select_file_entry_title'] = 'Aanvinken om dit bestand te selecteren';
$string['filemanager_select_all_entries_title'] = 'Aanvinken om alle mappen en bestanden te selecteren';

$string['filemanager_add_file'] = 'Bestanden toevoegen (uploaden)';
$string['filemanager_add_file_title'] = 'Gebruik deze link om 1 of meer bestanden toe te voegen';
$string['filemanager_add_directory'] = 'Map toevoegen';
$string['filemanager_add_directory_title'] = 'Gebruik deze link om een submap te maken';

$string['filemanager_parent'] = 'E&eacute;n niveau omhoog';
$string['filemanager_parent_title'] = 'Gebruik deze link om naar de bovenliggende map te navigeren';


$string['invalid_path'] = 'Ongeldig pad \'{PATH}\'';

$string['icon_preview_file_alt'] = 'icoon voorbeeld bestand';
$string['icon_preview_file_text'] = 'V';

$string['icon_delete_file'] = 'Wis dit bestand';
$string['icon_delete_file_alt'] = 'icoon bestand wissen';
$string['icon_delete_file_text'] = 'W';

$string['icon_delete_directory'] = 'Verwijder deze map';
$string['icon_delete_directory_alt'] = 'icoon map wissen';
$string['icon_delete_directory_text'] = 'W';

$string['filemanager_column_file'] = 'Naam';
$string['filemanager_column_size'] = 'Grootte (in bytes)';
$string['filemanager_column_date'] = 'Datum/tijd';
$string['filemanager_sort_asc'] = 'Sorteer kolom in oplopende volgorde';
$string['filemanager_sort_desc'] = 'Sorteer kolom in aflopende volgorde';
$string['filemanager_select_file_entries'] = 'Selecteer alle bestanden';
$string['filemanager_select_file_entries_title'] = 'Aanvinken om alle bestanden te selecteren';

$string['filemanager_add_subdirectory_header'] = 'Map toevoegen';
$string['filemanager_add_subdirectory_explanation'] = 'Hier kunt u een nieuwe submap toevoegen. De naam van de nieuwe map mag alleen letters, cijfers, mintekens en (enkelvoudige) onderlijningstekens bevatten. Andere tekens, bijv. deelstrepen, dubbele punten of apenstaartjes, zijn niet acceptabel en worden vervangen door een onderlijningsteken of zelfs helemaal genegeerd.';
$string['filemanager_add_subdirectory_label'] = '~Mapnaam';
$string['filemanager_add_subdirectory_title'] = 'Geef de naam van de nieuwe map in';
$string['filemanager_add_subdirectory_success'] = 'Submap \'{DIRECTORY}\' toegevoegd aan \'{PATH}\'';
$string['filemanager_add_subdirectory_failure'] = 'Fout: toevoegen submap \'{DIRECTORY}\' aan \'{PATH}\' mislukt';

$string['icon_open_directory_alt'] = 'icoon gesloten map';
$string['icon_open_directory_text'] = '+';

$string['filemanager_nothing_to_delete'] = 'Waarshuwing: geen bestanden om te wissen opgegeven';
$string['filemanager_success_delete_file'] = 'Bestand \'{FILENAME}\' gewist';
$string['filemanager_success_delete_files'] = '{COUNT} bestanden gewist';
$string['filemanager_failure_delete_file'] = 'Fout bij wissen bestand \'{FILENAME}\'';
$string['filemanager_failure_delete_files'] = 'Fouten bij het wissen van {COUNT} bestanden';
$string['filemanager_delete_file_header'] = 'Bevestiging wissen bestanden';
$string['filemanager_delete_file_explanation'] = 'U staat op het punt het volgende bestand te wissen uit \'{PATH}\':';
$string['filemanager_delete_files_explanation'] = 'U staat op het punt om de volgende {COUNT} bestanden te wissen uit \'{PATH}\':';

$string['filemanager_directory_not_empty'] = 'Map \'{DIRECTORY}\' kan niet worden verwijderd tenzij deze leeg is';
$string['filemanager_success_delete_directory'] = 'Submap \'{DIRECTORY}\' succesvol verwijderd';
$string['filemanager_failure_delete_directory'] = 'Fouten bij het verwijderen van submap \'{DIRECTORY}\'';
$string['filemanager_delete_directory_header'] = 'Bevestiging verwijderen submap';
$string['filemanager_delete_directory_explanation'] = 'U staat op het punt om de volgende submap te wissen uit \'{PATH}\':';

$string['filemanager_add_files_header'] = 'Bestanden toevoegen (uploaden)';
$string['filemanager_add_files_explanation'] = 'Hier kunt u nieuwe bestanden toevoegen aan (uploaden naar) de map \'{DIRECTORY}\'. De namen van de nieuwe bestanden mogen alleen letters, cijfers, punten, mintekens en (enkelvoudige) onderlijningstekens bevatten. Andere tekens, bijv. deelstrepen, dubbele punten of apenstaartjes, zijn niet acceptabel en worden automatisch vervangen door een onderlijningsteken of zelfs helemaal genegeerd, waardoor de naam van het bestand wodt aangepast bij het opslaan. Als een bestand van de opgegeven naam reeds bestaat, dan blijft dat behouden en het nieuwe bestand wordt opgeslagen onder een andere naam.<p>Let op:<br>De maximaal toegestane bestandsgrootte is {MAX_FILE_SIZE} bytes, de maximaal toegestane uploadgrootte is {POST_MAX_SIZE} bytes.';
$string['filemanager_add_file_label'] = 'Bestandsnaam';
$string['filemanager_add_file_title'] = 'Geef de naam van het bestand op of gebruik de knop om te bladeren';
$string['filemanager_add_files_label'] = 'Bestandsnaam ({INDEX})';
$string['filemanager_add_files_title'] = 'Geef de naam van het bestand op of gebruik de knop om te bladeren';

$string['filemanager_add_files_upload_size_error'] = '{FIELD}: fout {ERROR} bij het uploaden van bestand \'{FILENAME}\'; 
de maximaal toegestane bestandsgrootte is {MAX_FILE_SIZE} bytes, de maximaal toegestane uploadgrootte is {POST_MAX_SIZE} bytes';
$string['filemanager_add_files_upload_error'] = '{FIELD}: fout {ERROR} bij het uploaden; bestand \'{FILENAME}\' overgeslagen';
$string['filemanager_add_files_virus_found'] = '{FIELD}: virus(sen) gevonden; bestand \'{FILENAME}\' overgeslagen';
$string['filemanager_add_files_virusscan_failed'] = '{FIELD}: fout {ERROR} bij het scannen op virussen; bestand \'{FILENAME}\' overgeslagen';
$string['filemanager_virus_mailsubject1'] = 'Virusalert voor {SITENAME}: poging tot upload virus'; 
$string['filemanager_virus_mailmessage1'] = 
'Er is een poging gedaan om een virus te uploaden.
De uitvoer van de virusscanner was als volgt:

{OUTPUT}

De ingelogde gebruiker was:

{FULL_NAME} ({USERNAME})

en het ging om het bestand {PATH} ({FILENAME}).

Met vriendelijke groet,

Uw automatische webmaster';
$string['filemanager_virus_mailsubject2'] = 'Virusalert voor {SITENAME}: probleem met virusscanner'; 
$string['filemanager_virus_mailmessage2'] = 
'Er was een probleem bij het scannen van het bestand
{PATH} ({FILENAME}) op virussen.

De uitvoer van de virusscanner was als volgt:

{OUTPUT}

De ingelogde gebruiker was:

{FULL_NAME} ({USERNAME})

Het bestand is afgewezen omdat het scannen op virussen verplicht is.

Met vriendelijke groet,

Uw automatische webmaster';
$string['filemanager_add_files_success'] = 'Bestand \'{FILENAME}\' met succes toegevoegd aan map \'{PATH}\' onder de naam  \'{TARGET}\'';
$string['filemanager_add_files_error'] = 'Fout bij het toevoegen van bestand \'{FILENAME}\' aan map \'{PATH}\' onder de naam \'{TARGET}\'';
$string['filemanager_add_files_results'] = 'Bestanden toegevoegd: {SAVECOUNT}, bestanden overgeslagen: {SKIPCOUNT}';
$string['filemanager_add_files_filetype_mismatch'] = 'Fout: bestandsnaam (\'{FILENAME}\') en type (\'{FILETYPE}\') komen niet overeen; bestand overgeslagen. Hernoem het bestand (bijv. naar \'{TARGET}\') en probeer het nogmaals.';
$string['filemanager_add_files_filetype_banned'] = 'Fout: bestandsnaam \'{FILENAME}\' en bestandstype \'{FILETYPE}\' niet toegestaan; bestand overgeslagen.';
$string['filemanager_add_files_forbidden_name'] = 'Fout bij het toevoegen van bestand \'{FILENAME}\' aan map \'{PATH}\' onder de naam \'{TARGET}\': de naam is niet acceptabel. Hernoem het bestand en probeer het nogmaals.';

$string['filemanager_title_thumb_file'] = '{FILENAME} (grootte (bytes): {SIZE}, gewijzigd: {DATIM})';
$string['filemanager_title_thumb_image'] = '{FILENAME} (afmetingen: {WIDTH}x{HEIGHT}, grootte (bytes): {SIZE}, gewijzigd: {DATIM})';


$string['tools_intro'] = 'Hier vindt u verschillende gereedschappen.
<p>Met het Vertaalgereedschap kunt u nieuwe vertalingen toevoegen aan het programma of bestaande vertalingen wijziggen.
<p>Het Backup-gereedschap kunt u gebruiken voor het integraal downloaden van de database.
<p>Met Logbestand kunt u bladeren door de berichten in het logbestand.
<p>Kies alstublieft een gereedschap uit het menu.';
$string['tools_header'] = 'Gereedschappen';
$string['menu_translatetool'] = 'Vertaalgereedschap';
$string['menu_translatetool_title'] = 'Maak nieuwe vertalingen of wijzig bestaande vertalingen';
$string['menu_backuptool'] =  'Backup-gereedschap';
$string['menu_backuptool_title'] = 'Maak een integrale backup van de database';
$string['menu_logview'] =  'Logbestand';
$string['menu_logview_title'] = 'Blader door het logbestand';
$string['menu_update'] =  'Updatebeheer';
$string['menu_update_title'] = 'Bekijk interne versienummers of werk ze bij';


$string['translatetool_add_a_language'] = 'Nieuwe taal';
$string['translatetool_add_a_language_title'] = 'Klik hier om een nieuwe taal toe te voegen';

$string['icon_language_edit'] = 'Taal bewerken';
$string['icon_language_edit_alt'] = 'icoon bewerken';
$string['icon_language_edit_text'] = 'B';
$string['translatetool_edit_translation'] = '{LANGUAGE_NAME} ({LANGUAGE_KEY})';
$string['translatetool_edit_translation_title'] = 'Klik hier voor het bewerken van vertalingen in deze taal';


$string['translatetool_add_language_header'] = 'Voeg een nieuwe taal toe';
$string['translatetool_add_language_explanation'] = 'Hier kunt u een nieuwe taal toevoegen aan het CMS door de naam van de taal en andere gegevens van de taal in te voeren. Zodra de taal is toegevoegd aan het CMS kunt u de vertalingen toevoegen voor alle teksten die in het CMS gebruikt worden.';

$string['translatetool_edit_language_header'] = 'Eigenschappen taal bewerken';
$string['translatetool_edit_language_explanation'] = 'Hier kunt u de eigenschappen van deze taal bewerken.';

$string['translatetool_language_name_label'] = '~Naam (uitgedrukt in de taal zelf)';
$string['translatetool_language_name_title'] = 'Voer alstublieft de naam van de taal in';
$string['translatetool_language_is_active_label'] = 'Actief';
$string['translatetool_language_is_active_title'] = 'Aanvinken om de taal te activeren';
$string['translatetool_language_is_active_check'] = '~Merk de taal aan als actief';
$string['translatetool_language_parent_label'] = '~Basistaal';
$string['translatetool_language_parent_title'] = 'Kies een taal die als basis kan dienen voor de vertalingen';
$string['translatetool_language_key_label'] = '~Taalcode (ISO 639)';
$string['translatetool_language_key_title'] = 'Voer de 2- of 3-letterige code voor deze taal in (kleine letters)';

$string['translatetool_parent_language_none_option'] = '(geen)';
$string['translatetool_parent_language_none_title'] = 'Geen. Deze taal is niet gebaseerd op een bestaande taal';
$string['translatetool_parent_language_option_option'] = '{LANGUAGE_NAME} ({LANGUAGE_KEY})';
$string['translatetool_parent_language_option_title'] = 'Kies deze taal ({LANGUAGE_NAME}) als basis voor de vertalingen';

$string['invalid_language'] = 'Ongeldige taalcode \'{LANGUAGE_KEY}\'';

$string['translatetool_language_savenew_success'] = 'Gegevens van de nieuwe taal {LANGUAGE_NAME} ({LANGUAGE_KEY}) opgeslagen';
$string['translatetool_language_savenew_failure'] = 'Er waren problemen bij het opslaan van de nieuwe taal';

$string['translatetool_language_save_success'] = 'Wijzigingen in de eigenschappen van taal {LANGUAGE_NAME} ({LANGUAGE_KEY}) opgeslagen';
$string['translatetool_language_save_failure'] = 'Er waren problemen bij het opslaan van de gewijzigde eigenschappen van de taal';

$string['invalid_language_domain'] = 'Ongeldig taaldomein \'{FULL_DOMAIN}\'';

$string['translatetool_domain_grouping_program'] = 'Programma';
$string['translatetool_domain_grouping_modules'] = 'Modules';
$string['translatetool_domain_grouping_themes'] = 'Themas';
$string['translatetool_domain_grouping_install'] = 'Installatie';

$string['translatetool_edit_language_domain_header'] = 'Vertaling voor {LANGUAGE_NAME} ({LANGUAGE_KEY}) - {FULL_DOMAIN}';
$string['translatetool_edit_language_domain_explanation'] = '
Hier kunt u de vertalingen wijzigen. Merk op dat codes als &lt;P&gt; en {VALUE} ongewijzigd overgenomen moeten worden in de vertaling aangezien deze codes noodzakelijk zijn voor de goede werking van het programma.';


$string['translatetool_edit_language_domain_explanation'] = 'Hier kunt u vertalingen bewerken. Merk op dat codes als {EXAMPLE_HTML} en {EXAMPLE_VARIABLE} letterlijk overgenomen moeten worden, d.w.z. niet vertaald moeten worden. Deze codes zijn noodzakelijk voor de goede werking van het programma.
<p>Merk tevens op dat de tilde wordt gebruikt om de zogenaamde sneltoets aan te geven. Voorbeeld: als in het Engelse origineel gerefereerd wordt aan het invoerveld <strong>{EXAMPLE_TILDE}File</strong>, dan is het mogelijk om dat veld te selecteren met [Alt-F] of [Cmnd-F]. De Duitse vertaling van datzelfde invoerveld zou <strong>{EXAMPLE_TILDE}Datei</strong> kunnen zijn, met sneltoets [Alt-D] of [Cmnd-D]. Het is echter essentieel dat de sneltoetsen uniek zijn per dialoog. Als u, als vertaler, de letter D al gebruikt hebtvoor een ander veld zou uw Duitse vertaling bijvoorbeeld <strong>Dat{EXAMPLE_TILDE}ei</strong> kunnen worden, met sneltoets [Alt-E] of [Cmnd-E].
<p>Kort samengevat: het is aan u, de vertaler, om de sneltoetsen aan te wijzen door het plaatsen van tildes.';

$string['translatetool_full_name_label'] = 'Naam van de vertaler';
$string['translatetool_full_name_title'] = 'Hier kunt u uw naam invullen (voor vermelding als auteur van deze vertaling)';
$string['translatetool_email_label'] = 'E-mail-adres van de vertaler';
$string['translatetool_email_title'] = 'Hier kunt u uw e-mail-adres invullen (voor vermelding als auteur van deze vertaling)';
$string['translatetool_notes_label'] = 'Notities';
$string['translatetool_notes_title'] = 'Dit is de plaats om uw opmerkingen over deze vertaling te noteren';

$string['translatetool_submit_label'] = 'De velden hieronder worden gebruikt voor het inzenden van uw vertaling aan het Website@School-project.
<p>U kunt het veld \'Notities\' gebruiken als begeleidend schrijven; dit veld wordt uiteindelijk gebruikt voor de inhoud van een e-mail-bericht aan het project.';

$string['translatetool_submit_check'] = 'Deze vertaling inzenden';
$string['translatetool_submit_title'] = 'Vink het vakje aan om uw vertaling naar het Website@School project te zenden';

$string['translatetool_no_changes_to_save'] = 'Geen wijzigingen om op te slaan voor {LANGUAGE_NAME} ({LANGUAGE_KEY}) - {FULL_DOMAIN}';

$string['translatetool_translation_save_success'] = 'Wijzigingen voor {LANGUAGE_NAME} ({LANGUAGE_KEY}) - {FULL_DOMAIN} succesvol opgeslagen';
$string['translatetool_translation_save_failure'] = 'Er waren problemen bij het opslaan van de wijzigingen voor {LANGUAGE_NAME} ({LANGUAGE_KEY}) - {FULL_DOMAIN}';
$string['translatetool_translation_submit_success'] = 'Wijzigingen voor {LANGUAGE_NAME} ({LANGUAGE_KEY}) - {FULL_DOMAIN} succesvol ingezonden';
$string['translatetool_translation_submit_failure'] = 'Er waren problemen bij het inzenden van de wijzigingen voor {LANGUAGE_NAME} ({LANGUAGE_KEY}) - {FULL_DOMAIN}';

$string['backuptool_header'] = 'Backup-gereedschap';
$string['backuptool_intro'] = 'Hier kunt u een integrale backup maken van de database. Bewaar deze backup op een veilige plaats. Volg de link hieronder om het backup-proces te starten.<p>
<strong>Let op</strong><br>
Deze procedure maakt geen backup van de bestanden (files) in de datadirectory (<strong>{DATADIRECTORY}</strong>) maar uitsluitend van de database. De bestanden zult u op een andere wijze veilig moeten stellen. Neem desgewenst contact op met uw provider.';
$string['backuptool_download'] = 'Download database backup';
$string['backuptool_download_title'] = 'Klik hier voor een database backup';
$string['backuptool_error'] = 'Er waren problemen met het maken van de backup';

$string['logview_error'] = 'Er waren problemen bij het ophalen van logberichten';
$string['logview_no_messages'] = 'Geen logberichten aanwezig';
$string['logview_nr'] = 'Nr';
$string['logview_datim'] = 'Datum/tijd';
$string['logview_remote_addr'] = 'Adres';
$string['logview_user_id'] = 'Gebruiker';
$string['logview_priority'] = 'Prioriteit';
$string['logview_message'] = 'Logbericht';

$string['update_header'] = 'Updatebeheer';
$string['update_intro'] = 'Dit is updatebeheer. Hieronder staat een overzicht van de huidige interne en externe versienummers van het basissysteem en de diverse subsystemen. Als de interne versie afwijkt van de externe, dan kunt u de interne versie opwaarderen naar de externe door de link \'[Opwaarderen]\' in de laatste kolom te volgen of installeren via de link \'[Installeren]\'.';
$string['update_version_database'] = 'Intern';
$string['update_version_manifest'] = 'Versie';
$string['update_release_date_manifest'] = 'Datum';
$string['update_release_manifest'] = 'Uitgave';
$string['update_status'] = 'Status';
$string['update_core'] = 'basis';
$string['update_core_success'] = 'Basissysteem met succes opgewaardeerd naar versie {VERSION}';
$string['update_core_error'] = 'Fout bij het opwaarderen van het basissysteem naar versie {VERSION}';
$string['update_core_warnning_core_goes_first'] = 'Waarschuwing: het basissysteem moet eerst opgewaardeerd worden';
$string['update_subsystem_languages'] = 'Talen';
$string['update_subsystem_language_success'] = 'Taal {LANGUAGE} succesvol opgewaardeerd/ge&iuml;nstalleerd';
$string['update_subsystem_language_error'] = 'Fout bij het opwaarderen/installeren van taal {LANGUAGE}';
$string['update_subsystem_modules'] = 'Modules';
$string['update_subsystem_module_success'] = 'Module {MODULE} met succes opgewaardeerd/ge&iuml;nstalleerd';
$string['update_subsystem_module_error'] = 'Fout bij het opwaarderen/installeren van module {MODULE}';
$string['update_subsystem_themes'] = 'Thema\'s';
$string['update_subsystem_theme_success'] = 'Thema {THEME} met succes opgewaardeerd/ge&iuml;nstalleerd';
$string['update_subsystem_theme_error'] = 'Fout bij het opwaarderen/installeren van thema {THEME}';
$string['update_status_ok'] = 'OK';
$string['update_status_error'] = 'FOUT';
$string['update_status_update'] = 'Opwaarderen';
$string['update_status_install'] = 'Installeren';
$string['update_version_database_too_old'] = 'De interne versie {VERSION} is te oud; u moet helaas herinstalleren en/of handmatig opwaarderen.';
$string['update_field_value_too_long'] = 'Tabel \'{TABLE}\' veld \'{FIELD}\': inhoud is langer dan {LENGTH} tekens: \'{CONTENT}\'.';
$string['update_please_correct_field_value_manually'] = 'Het aantal velden dat handmatig (buiten Website@School om) ingekort moet worden is {ERRORS}';
$string['update_warning_obsolete_file'] = 'Waarschuwing: het bestand \'{FILENAME}\' is niet meer nodig (sinds versie {VERSION}) en kan veilig worden verwijderd';

?>