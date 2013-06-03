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

/** /program/modules/crew/languages/nl/crew.php - translated messages for module (Dutch)
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_crew
 * @version $Id: crew.php,v 1.1 2013/06/03 16:14:24 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['title'] = 'Werkplaats (CREW)';
$string['description'] = 'Een editor waarmee gezamenlijk op afstand documenten gemaakt kunnen worden';
$string['translatetool_title'] = 'Werkplaats (CREW)';
$string['translatetool_description'] = 'Dit bestand bevat de vertalingen bij de Werkplaats (CREW)-module';

$string['config_header'] = 'Werkplaats (CREW)-module configuratie';
$string['config_explanation'] = 'Hier kunt u de Werkplaats (CREW)-module (Engels:
Collaborative Remote Editor Workshop) configureren.
Onderstaande parameters moeten correct worden ingevuld
omdat de module anders niet zal werken en niet gebruikt
kan worden om documenten te maken via een werkplaats-pagina.
Raadpleeg het Website@School handboek voor technische details.';
$string['config_origin_label'] = '~Bron';
$string['config_origin_title'] = 'Deze URL moet kloppen met de bron zoals de browser die waarneemt';
$string['config_location_label'] = '~Locatie';
$string['config_location_title'] = 'Deze URL moet wijzen naar een CREW/Websocket-server (zie handboek)';
$string['config_secret_label'] = '~Geheime sleutel';
$string['config_secret_title'] = 'Deze gedeelde geheime code moet overeenstemmen met die van de CREW/Websocket-server';

$string['crew_content_header'] = 'Werkplaats (CREW) configuratie';
$string['crew_content_explanation'] = 'Hier kunt u de werkplaats configureren. U kunt een optionele titel en een optionele introductietekst toevoegen aan de werkplaats-pagina. U kunt ook de zichbaarheid van de werkplaats-pagina instellen. Gebruik daarvoor een van de volgende opties:
\'wereld\' om het document aan anonieme bezoekers te tonen,
\'gebruikers\' om het document aan ingelogde gebruikers te tonen of
\'individueel\' om toegang te geven aan geslecteerde gebruikers uit onderstaande lijst'; 

$string['header_label'] = '~Titel';
$string['header_title'] = 'Titel voor de werkplaats';
$string['introduction_label'] = '~Introductie';
$string['introduction_title'] = 'Introductietekst voor de werkplaats';
$string['visibility_label'] = 'Zichtbaarheid van het document';
$string['visibility_title'] = 'Kies een van de beschikbare opties';
$string['visibility_world_label'] = '~Wereld';
$string['visibility_world_title'] = 'Toon document aan anonieme gebruikers';
$string['visibility_all_label'] = '~Gebruikers';
$string['visibility_all_title'] = 'Toon document aan ingelogde ingelogde gebruikers';
$string['visibility_workers_label'] = '~Individueel';
$string['visibility_workers_title'] = 'Toon document alleen aan geselecteerde individuen';

$string['crew_acl_role_readonly_option'] = 'Lezen';
$string['crew_acl_role_readonly_title'] = 'Permissie om het document te lezen';
$string['crew_acl_role_readwrite_option'] = 'Lezen en bewerken';
$string['crew_acl_role_readwrite_title'] = 'Permissie om het document te lezen en te bewerken';

$string['crew_view_access_denied'] = 'Sorry, u hebt momenteel onvoldoende permissies om deze pagina te bekijken';
$string['last_updated_by'] = 'Laatst bijgewerkt: {DATIM} door {FULL_NAME} ({USERNAME})';

$string['skin_label'] = 'Weergave';
$string['skin_title'] = 'Selecteer de weergave voor deze sessie';
$string['skin_standard_option'] = 'Basis';
$string['skin_standard_title'] = 'Standaardweergave';
$string['skin_bw_option'] = 'Slechtziend';
$string['skin_bw_title'] = 'Hoog contrast (zwart+wit) interface';
$string['skin_rb_option'] = 'Rood Grijs Blauw';
$string['skin_rb_title'] = 'Interface met primaire kleuren';
$string['skin_by_option'] = 'Mondriaan';
$string['skin_by_title'] = 'Zwart met geel';

$string['crew_requires_js_and_ws'] = 'Sorry, deze module vereist JavaScript en het WebSocket-protocol';

$string['crew_button_save'] = 'Opslaan';
$string['crew_button_save_title'] = 'Sla de tekst op en eindig de sessie';
$string['crew_button_saveedit'] = "Opslaan+Bewerken";
$string['crew_button_saveedit_title'] = 'Sla de tekst op en vervolg de sessie';
$string['crew_button_cancel'] = 'Annuleren';
$string['crew_button_cancel_title'] = 'Annuleer de wijzigingen en eindig de sessie';
$string['crew_button_refresh'] = 'Vernieuwen';
$string['crew_button_refresh_title'] = 'Lees de huidige tekst nogmaals in';
$string['crew_button_send'] = 'Zenden';
$string['crew_button_send_title'] = 'Zend een bericht naar alle deelnemers';
$string['crew_button_sound'] = '';
$string['crew_button_sound_title'] = 'Druk op deze knop om geluit AAN of UIT te schakelen';
$string['crew_js_websocket_not_supported'] = 'WebSocket protocol niet ondersteund';
$string['crew_js_initialised'] = 'GEINITIALISEERD';
$string['crew_js_connected'] = 'VERBONDEN';
$string['crew_js_disconnected_clean'] = 'ONTKOPPELD (foutloos): code={CODE} reden={REASON}';
$string['crew_js_disconnected_unclean'] = 'ONTKOPPELD (met fouten): code={CODE} reden={REASON}';
$string['crew_js_unknown_msg'] = '{ORIGIN}: onbekend bericht: {DATA}';
$string['crew_js_error'] = 'FOUT: {DATA}';
$string['crew_js_save_characters'] = 'OPSLAAN ({LENGTH} tekens)';
$string['crew_js_saveedit_characters'] = 'OPSLAAN ({LENGTH} tekens) + BEWERKEN';
$string['crew_js_cancel_characters'] = 'ANNULEREN ({LENGTH} tekens)';
$string['crew_js_sound_off'] = 'GELUID UIT';
$string['crew_js_sound_on'] = 'GELUID AAN';
$string['crew_js_enters_workshop'] = '{NAME} ({NICK}) betreedt de werkplaats';
$string['crew_js_leaves_workshop'] = '{NAME} ({NICK}) verlaat de werkplaats';
$string['crew_js_malformed_message'] = 'FOUT: misvormd bericht {DATA}';
$string['crew_js_unloading'] = 'ONTLADEN';
$string['crew_js_authenticating'] = 'AUTHENTICEREN {NAME} ({NICK})';
$string['crew_js_error_relocate'] = 'INTERNE FOUT: verplaatsingparameters n={N} en gebruikers={USERS} verschillen';
$string['crew_js_error_patchcount'] = 'INTERNE FOUT: minder dan {N} patch parameters: {COUNT} ({DATA})';
$string['crew_js_error_context'] = 'INTERNE FOUT: context {N} ontbreekt: {OLD} {NEW}';
$string['crew_js_error_usercount'] = 'INTERNE FOUT: patch n={N} en gebruikers={USERS}';


?>