<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam, <info@websiteatschool.eu>
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

/** /program/install/languages/tr/install.php
 *
 * Language: tr (Türkçe)
 * Release:  0.90.3 / 2012041700 (2012-04-17)
 *
 * @author Dirk Schouten <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_tr
 * @version $Id: install.php,v 1.3 2013/06/11 11:26:03 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Installatie';
$string['translatetool_description'] = 'Dit bestand bevat de vertalingen voor installatie-programma';
$string['websiteatschool_install'] = 'Website@School Installatie';
$string['websiteatschool_logo'] = 'logo Website@School';
$string['help_name'] = 'help';
$string['help_description'] = 'Help (opent in een nieuw venster)';
$string['next'] = 'Verder';
$string['next_accesskey'] = 'V';
$string['next_title'] = 'Gebruik [Alt-V] of [Cmnd-V] als sneltoets voor deze knop';
$string['previous'] = 'Terug';
$string['previous_accesskey'] = 'T';
$string['previous_title'] = 'Gebruik [Alt-T] of [Cmnd-T] als sneltoets voor deze knop';
$string['cancel'] = 'Annuleren';
$string['cancel_accesskey'] = 'A';
$string['cancel_title'] = 'Gebruik [Alt-A] of [Cmnd-A] als sneltoets voor deze knop';
$string['ok'] = 'OK';
$string['ok_accesskey'] = 'K';
$string['ok_title'] = 'Gebruik [Alt-K] of [Cmnd-K] als sneltoets voor deze knop';
$string['yes'] = 'Ja';
$string['no'] = 'Nee';
$string['language_name'] = 'Türkçe';
$string['dialog_language'] = 'Taal';
$string['dialog_language_title'] = 'Selecteer de taal voor de installatie';
$string['dialog_language_explanation'] = 'Kies de tijdens de installatieprocedure te gebruiken taal.';
$string['language_label'] = 'Taal';
$string['language_help'] = '';
$string['dialog_installtype'] = 'Installatietype';
$string['dialog_installtype_title'] = 'Kies tussen een Standaard of Aangepaste installatie';
$string['dialog_installtype_explanation'] = 'Kies het installatiescenario uit onderstaande lijst';
$string['installtype_label'] = 'Installatiescenario';
$string['installtype_help'] = 'Maak een keuze voor het installatiescenario.<br><strong>Standaard</strong> betekent een rechttoe-rechtaan installatie met een minimum aan vragen,<br><strong>Aangepast</strong> geeft u alle mogelijkheden voor de installatie-opties.';
$string['installtype_option_standard'] = 'Standaard';
$string['installtype_option_custom'] = 'Aangepast';
$string['high_visibility_label'] = 'Extra zichtbaarheid';
$string['high_visibility_help'] = 'Vink dit vakje aan om de tekstinterface te gebruiken tijdens de installatie.';
$string['dialog_license'] = 'Licentie';
$string['dialog_license_title'] = 'Lees en accepteer de licentie voor deze programmatuur';
$string['dialog_license_explanation'] = 'U krijgt een licentie voor het gebruik van deze programmatuur uitsluitend en alleen indien u onderstaande licentieovereenkomst leest, begrijpt en accoord gaat met de daarin vervatte voorwaarden. Merk op dat de Engelse versie van deze licentieovereenkomst van toepassing is, zelfs indien u de programmatuur installeert in het Nederlands of een andere taal.';
$string['dialog_license_i_agree'] = 'Ik stem toe';
$string['dialog_license_you_must_accept'] = 'U moet accoord gaan met de licentieovereenkomst door &quot;<b>{IAGREE}</b>&quot; (zonder de aanhalingstekens) in te typen in het invoerveld hieronder.';
$string['dialog_database'] = 'Database';
$string['dialog_database_title'] = 'Gegevens van de database server invoeren';
$string['dialog_database_explanation'] = 'Voert u alstublieft de gegevens over de database server in onderstaande velden in.';
$string['db_type_label'] = 'Type';
$string['db_type_help'] = 'Kies een van de beschikbare database-types uit de lijst.';
$string['db_type_option_mysql'] = 'MySQL';
$string['db_server_label'] = 'Server';
$string['db_server_help'] = 'Dit is het adres van de database-server, meestal <strong>localhost</strong>. Andere voorbeelden: <strong>mysql.example.org</strong> of <strong>example.dbserver.provider.net:3306</strong>.';
$string['db_username_label'] = 'Gebruikersnaam';
$string['db_username_help'] = 'Een geldige combinatie van gebruikersnaam en wachtwoord is noodzakelijk voor een verbinding met de database-server. Gebruikt u alstublieft niet het hoofdaccount (\'root account\') van de database-server maar een gewoon account met minder privileges, bijvoorbeeld <strong>wasuser</strong> of <strong>example_wwwa</strong>.';
$string['db_password_label'] = 'Wachtwoord';
$string['db_password_help'] = 'Een geldige combinatie van gebruikersnaam en wachtwoord is noodzakelijk voor een verbinding met de database-server.';
$string['db_name_label'] = 'Databasenaam';
$string['db_name_help'] = 'Dit is de naam van de te gebruiken database. Merk op dat deze database reeds moet bestaan; dit installatieprogramma voorziet niet in het aanmaken van databases (uit veiligheidsoverwegingen). Voorbeelden: <strong>www</strong> of <strong>example_www</strong>.';
$string['db_prefix_label'] = 'Voorvoegsel';
$string['db_prefix_help'] = 'De namen van alle tabellen in de database worden voorafgegaan door dit voorvoegsel (\'prefix\'). Dit maakt het mogelijk om dezelfde database te gebruiken voor verschillende installaties van Website@School. Merk op dat dit voorvoegsel moet beginnen met een letter. Voorbeelden: <strong>was_</strong> of <strong>cms2_</strong>.';
$string['dialog_cms'] = 'Website';
$string['dialog_cms_title'] = 'Essenti&euml;le gegevens van de website invoeren';
$string['dialog_cms_explanation'] = 'Voert u hieronder alstublieft de gegevens over uw website in.';
$string['cms_title_label'] = 'Naam';
$string['cms_title_help'] = 'De naam van uw website.';
$string['cms_website_from_address_label'] = 'From: e-mail-adres';
$string['cms_website_from_address_help'] = 'Dit e-mail-adres wordt gebruikt als afzenderadres voor uitgaande electronische berichten, bijvoorbeeld bij alerts en wachtwoordherinneringen.';
$string['cms_website_replyto_address_label'] = 'Reply-To: e-mail-adres';
$string['cms_website_replyto_address_help'] = 'Dit e-mail-adres wordt toegevoegd aan uitgaande berichten. Het kan worden gebruikt om een postbus op te geven waar antwoordberichten daadwerkelijk gelezen worden (door u) en niet verdwijnen in het niets (door toedoen van de webserver-programmatuur).';
$string['cms_dir_label'] = 'Website-directory';
$string['cms_dir_help'] = 'Dit is het pad naar de directory waar bestanden als index.php en config.php te vinden zijn, bijvoorbeeld <strong>/home/httpd/htdocs</strong> of <strong>C:\Program Files\Apache Group\Apache\htdocs</strong>.';
$string['cms_www_label'] = 'Website URL';
$string['cms_www_help'] = 'Dit is de hoofd-URL die leidt naar uw website, dat wil zeggen de plaats waar index.php bezocht kan worden. Voorbeelden zijn: <strong>http://www.example.org</strong> of <strong>https://example.org:443/schoolsite</strong>.';
$string['cms_progdir_label'] = 'Programma-directory';
$string['cms_progdir_help'] = 'Dit is het pad naar de directory waar de programmabestanden van Website@School zich bevinden (meestal in de subdirectory <strong>program</strong> van de website-directory). Voorbeelden: <strong>/home/httpd/htdocs/program</strong> of <strong>C:\Program Files\Apache Group\Apache\htdocs\program</strong>.';
$string['cms_progwww_label'] = 'Programma-URL';
$string['cms_progwww_help'] = 'Dit is de URL die leidt naar de programmadirectory (meestal de hoofd-URL van de website gevolgd door <strong>/program</strong>). Voorbeelden: <strong>http://www.example.org/program</strong> of <strong>https://example.org:443/schoolsite/program</strong>.';
$string['cms_datadir_label'] = 'Datadirectory';
$string['cms_datadir_help'] = 'Dit is de directory die uploadbestanden en andere gegevensbestanden bevat. Het is erg belangrijk dat deze directory zich buiten de zogenaamde Document Root bevindt, dat wil zeggen: deze directory moet niet van buitenaf toegankelijk zijn met een browser. Merk op dat de webserver voldoende permissies moet hebben om in deze datadirectory bestanden en subdirectories te lezen, schrijven en aan te maken. Voorbeelden: <strong>/home/httpd/wasdata</strong> of <strong>C:\Program Files\Apache Group\Apache\wasdata</strong>.';
$string['cms_demodata_label'] = 'Database vullen';
$string['cms_demodata_help'] = 'Vink het vakje aan om de database te vullen met demonstratie-gegevens en voorbeeldpagina\'s.';
$string['cms_demodata_password_label'] = 'Demonstratiewachtwoord';
$string['cms_demodata_password_help'] = 'Dit wachtwoord zal worden toegekend aan <em>alle</em> demonstratie-gebruikersaccounts. Bedenk een goed wachtwoord: kies minimaal 8 tekens uit hoofdletters, kleine letters en cijfers. Laat dit veld leeg als u het vakje \'Database vullen\' hierboven niet hebt aangevinkt.';
$string['dialog_user'] = 'Gebruikersaccount';
$string['dialog_user_title'] = 'Maak het eerste account aan';
$string['dialog_user_explanation'] = 'Voert u alstublieft de gegevens in van het eerste account voor deze nieuwe website. Merk op dat dit account over alle beheerdersprivileges beschikt en volledige bevoegdheid heeft waardoor iedereen die toegang heeft tot dit account volledige toegang heeft tot alles.';
$string['user_full_name_label'] = 'Volledige naam';
$string['user_full_name_help'] = 'Geef hier uw eigen naam of, als u dat liever hebt, een andere (functionele) naam op, bijv. <strong>Wilhelmina Bladergroen</strong> of <strong>Meester Web</strong>.';
$string['user_username_label'] = 'Loginnaam';
$string['user_username_help'] = 'Geeft u hier alstublieft de loginnaam op die u wilt gebruiken voor dit account. U gebruikt deze naam iedere keer als u zich wilt aanmelden. Voorbeelden: <strong>wblade</strong> of <strong>webmeester</strong>.';
$string['user_password_label'] = 'Wachtwoord';
$string['user_password_help'] = 'Bedenk een goed wachtwoord: kies minimaal 8 tekens uit hoofdletters, kleine letters, cijfers en bijzondere tekens als % (procent), = (is-gelijk),  / (schuine streep) en . (punt). Vertel uw wachtwoord nooit aan anderen; maak liever een aanvullend  beheerdersaccount aan voor uw collega\'s.';
$string['user_email_label'] = 'E-Mail-adres';
$string['user_email_help'] = 'Geef hier uw eigen e-mail-adres op. Dit adres hebt u nodig als u onverhoopt een nieuw wachtwoord moet aanvragen. Overtuig u ervan dat u alleen zelf toegang hebt tot deze postbus (gebruik geen gezamenlijke postbus). Voorbeelden: <strong>wilhelmina.bladergroen@example.org</strong> of <strong>webmaster@example.org</strong>.';
$string['dialog_compatibility'] = 'Compatibiliteit';
$string['dialog_compatibility_title'] = 'Controleer compatibiliteit';
$string['dialog_compatibility_explanation'] = 'Hieronder staat een overzicht van noodzakelijke en gewenste instellingen. Overtuig uzelf ervan dat aan alle vereisten is voldaan vooordat u op [Volgende] drukt.';
$string['compatibility_label'] = 'Test';
$string['compatibility_value'] = 'Waarde';
$string['compatibility_result'] = 'Uitkomst';
$string['compatibility_ok'] = 'OK';
$string['compatibility_warning'] = 'WAARSCHUWING';
$string['compatibility_websiteatschool_version_label'] = 'Website@School';
$string['compatibility_websiteatschool_version_check'] = '(controleer)';
$string['compatibility_websiteatschool_version_value'] = 'versie {RELEASE} ({VERSION}) {RELEASE_DATE}';
$string['compatibility_websiteatschool_version_check_title'] = 'Controleer op nieuwere Website@School-versies';
$string['compatibility_phpversion_label'] = 'PHP-versie';
$string['compatibility_phpversion_obsolete'] = 'PHP-version is obsoleet';
$string['compatibility_phpversion_too_old'] = 'PHP-versie is te oud: het minimum is {MIN_VERSION}';
$string['compatibility_php_safemode_label'] = 'PHP veilige modus';
$string['compatibility_php_safemode_warning'] = 'Veilig modus is aan. Zet dit a.u.b. uit in php.ini';
$string['compatibility_webserver_label'] = 'Webserver';
$string['compatibility_autostart_session_label'] = 'Automatische start sessies';
$string['compatibility_autostart_session_fail'] = 'Automatische start sessies is aan. Zet dit a.u.b. uit in php.ini';
$string['compatibility_file_uploads_label'] = 'Bestanden uploaden';
$string['compatibility_file_uploads_fail'] = 'Bestanden uploaden is uit. Zet dit a.u.b. aan in php.ini';
$string['compatibility_database_label'] = 'Database-server';
$string['compatibility_clamscan_label'] = 'Clamscan anti-virus';
$string['compatibility_clamscan_not_available'] = '(niet beschikbaar)';
$string['compatibility_gd_support_label'] = 'GD-ondersteuning';
$string['compatibility_gd_support_none'] = 'GD wordt niet ondersteund';
$string['compatibility_gd_support_gif_readonly'] = 'Alleen lezen';
$string['compatibility_gd_support_details'] = '{VERSION} (GIF: {GIF}, JPG: {JPG}, PNG: {PNG})';
$string['dialog_confirm'] = 'Bevestiging';
$string['dialog_confirm_title'] = 'Bevestig de configuratiegegevens';
$string['dialog_confirm_explanation'] = 'U staat op het punt om uw nieuwe website te installeren. Controleer onderstaande configuratiegegevens zorgvuldig en druk daarna op [Volgende] om het installatieproces daadwerkelijk te starten. De installatie kan enige tijd duren.';
$string['dialog_confirm_printme'] = 'Tip: druk deze pagina af op papier en bewaar de gegevens in uw administratie';
$string['dialog_cancelled'] = 'Geannuleerd';
$string['dialog_cancelled_title'] = '';
$string['dialog_cancelled_explanation'] = 'De installatie van Website@School is geannuleerd. Druk op onderstaande knop om de procedure nogmaals te starten of klik op de helpknop om het handboek te lezen.';
$string['dialog_finish'] = 'Afsluiten';
$string['dialog_finish_title'] = 'Het installatieprogramma afsluiten';
$string['dialog_finish_explanation_0'] = 'De installatie van Website@School {VERSION} is nu nagenoeg gereed.<p>Er moeten nog twee handelingen uitgevoerd worden:<ol><li>U moet nu het bestand config.php {AHREF}downloaden{A}, en<li>U moet vervolgens het bestand config.php moeten plaatsen in <tt><strong>{CMS_DIR}</strong></tt>.</ol>Zodra het bestand config.php op zijn plaats staat, kunt u het installatiepgrogramma afsluiten door op onderstaande [OK]-knop te drukken.';
$string['dialog_finish_explanation_1'] = 'De installatie van Website@School {VERSION} is nu gereed.<p>U kunt het installatiepgrogramma afsluiten door op onderstaandde [OK]-knop te drukken.';
$string['dialog_finish_check_for_updates'] = 'Als u wilt kunt u via onderstaande link controleren of er updates zijn (link opent in een nieuw venster).';
$string['dialog_finish_check_for_updates_anchor'] = 'Controleer op Website@School updates.';
$string['dialog_finish_check_for_updates_title'] = 'controleer de status van uw Website@School-versie';
$string['jump_label'] = 'Ga naar';
$string['jump_help'] = 'Kies de locatie waar u naartoe wilt gaan nadat u op onderstaande [OK]-knop drukt.';
$string['dialog_download'] = 'Download config.php';
$string['dialog_download_title'] = 'Download config.php naar uw computer';
$string['dialog_unknown'] = 'Onbekend';
$string['error_already_installed'] = 'Fout: Website@School is reeds ge&iuml;nstalleerd';
$string['error_wrong_version'] = 'Fout: versienummer klopt niet. Hebt u een nieuwe versie gedownload tijdens de installatieprocedure?';
$string['error_fatal'] = 'Fatale fout {ERROR}: neem contact op met &lt;{EMAIL}&gt; voor assistentie';
$string['error_php_obsolete'] = 'Fout: deze versie van PHP is te oud';
$string['error_php_too_old'] = 'Fout: de versie  van PHP ({VERSION}) is te oud: gebruik minimaal versie {MIN_VERSION}';
$string['error_not_dir'] = 'Fout: {FIELD}: directory bestaat niet: {DIRECTORY}';
$string['warning_switch_to_custom'] = 'Waarschuwing: omschakeling naar Aangepaste installatie om fouten te kunnen herstellen';
$string['error_not_create_dir'] = 'Fout: {FIELD}: directory kan niet worden aangemaakt: {DIRECTORY}';
$string['error_db_unsupported'] = 'Fout: database type {DATABASE} wordt niet ondersteund';
$string['error_db_cannot_connect'] = 'Fout: kan geen verbinding met de database-server maken';
$string['error_db_cannot_select_db'] = 'Fout: de database kan niet geopend worden';
$string['error_invalid_db_prefix'] = 'Fout: {FIELD}: moet beginnen met een letter, mag uitsluitend letters, cijfers en laag streepje (underscore) bevatten';
$string['error_db_prefix_in_use'] = 'Fout: {FIELD}: reeds gebruikt in deze database: {PREFIX}';
$string['error_time_out'] = 'Fatale fout: time-out';
$string['error_db_parameter_empty'] = 'Fout: de gegevens van de database mogen niet blanco zijn';
$string['error_db_forbidden_name'] = 'Fout: {FIELD}: deze naam is niet geoorloofd: {NAME}';
$string['error_too_short'] = 'Fout: {FIELD}: ingevoerde tekst is te kort (mimimum = {MIN})';
$string['error_too_long'] = 'Fout: {FIELD}: ingevoerde tekst is te lang  (maximum = {MAX})';
$string['error_invalid'] = 'Fout: {FIELD}: ongeldige waarde';
$string['error_bad_password'] = 'Fout: {FIELD}: voldoet niet aan de minimale eisen: cijfers: {MIN_DIGIT}, onderkast: {MIN_LOWER}, kapitalen: {MIN_UPPER}';
$string['error_bad_data'] = '{MENU_ITEM}: fouten in ingevoerde gegevens ontdekt: corrigeren via het menu alstublieft';
$string['error_file_not_found'] = 'Fout: bestand niet gevonden: {FILENAME}';
$string['error_create_table'] = 'Fout: kan tabel niet aanmaken: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_insert_into_table'] = 'Fout: kan geen gegevens invoegen in tabel: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_update_config'] = 'Fout: kan configuratiegegeven niet instellen: {CONFIG} ({ERRNO}/{ERROR})';
$string['warning_no_manifest'] = 'Waarschuwing: leeg manifest of geen manifest gevonden voor {ITEM}';
$string['error_install_demodata'] = 'Fout: kan demonstatie-gegevens niet installeren';
$string['error_directory_exists'] = 'Fout: {FIELD}: directory bestaat al: {DIRECTORY}';
$string['error_nameclash'] = 'Fout: {FIELD}: verandert u alstublieft de naam {USERNAME}; deze naam wordt al gebruikt als demonstratie-gebruikersaccount';
$string['warning_mysql_obsolete'] = 'Waarschuwing: versie \'{VERSION}\' van MySQL is obsoleet en ondersteunt geen UTF-8. Installeer een recentere versie van MySQL';
?>