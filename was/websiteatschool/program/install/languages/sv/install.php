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

/** /program/install/languages/sv/install.php
 *
 * Language: sv (Svenska)
 * Release:  0.90.4 / 2013061400 (2013-06-14)
 *
 * @author Hansje Cozijnsen <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_sv
 * @version $Id: install.php,v 1.1 2013/06/14 20:00:33 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Installation';
$string['translatetool_description'] = 'Denna fil innehåller översättningar för installations programmet';
$string['websiteatschool_install'] = 'Website@School Installation';
$string['websiteatschool_logo'] = 'logo Website@School';
$string['help_name'] = 'hjälp';
$string['help_description'] = 'Hjälp (öppnas i ett nytt fönster)';
$string['next'] = 'Vidare';
$string['next_accesskey'] = 'V';
$string['next_title'] = 'Använd [Alt-V] eller [Cmnd-V] som kortkommandot för denna knapp';
$string['previous'] = 'Tillbaka';
$string['previous_accesskey'] = 'T';
$string['previous_title'] = 'Använd [Alt-T] eller [Cmnd-T] som kortkommandot för denna knapp';
$string['cancel'] = 'Avbryt';
$string['cancel_accesskey'] = 'A';
$string['cancel_title'] = 'Använd [Alt-A] eller [Ctrl-Alt-A] som kortkommandot för denna knapp';
$string['ok'] = 'OK';
$string['ok_accesskey'] = 'O';
$string['ok_title'] = 'Använd [Alt-O] eller [Ctrl-Alt-O] som kortkommandot för denna knapp';
$string['yes'] = 'Ja';
$string['no'] = 'Nej';
$string['language_name'] = 'Svenska';
$string['dialog_language'] = 'Språk';
$string['dialog_language_title'] = 'Välj språket för installation';
$string['dialog_language_explanation'] = 'Välj det språket som används under installationens förfarandet.';
$string['language_label'] = 'Språk';
$string['language_help'] = '';
$string['dialog_installtype'] = 'Installationstyp';
$string['dialog_installtype_title'] = 'Välj mellan Vanlig och Anpassad installation';
$string['dialog_installtype_explanation'] = 'Var vänlig och välj installations scenario ifrån nedanstående lista';
$string['installtype_label'] = 'Installationsscenario';
$string['installtype_help'] = 'Välj ett installationsscenario.<br><strong>Vanlig</strong> betyder en entydig installation med få frågor som ska svaras på, <br><strong>Anpassad</strong> ger full kontroll över alla installionens val.';
$string['installtype_option_standard'] = 'Vanlig';
$string['installtype_option_custom'] = 'Anpassad';
$string['high_visibility_label'] = 'Extra synlighet';
$string['high_visibility_help'] = 'Klicka rutan för att använda en bara-text gränssnitt under installationen.';
$string['dialog_license'] = 'Licens';
$string['dialog_license_title'] = 'Läs och godta licensen för denna programvara';
$string['dialog_license_explanation'] = 'Du får endast licens till denna programvara om du läser, förstår och godtar följande villkor. Obs! Här gäller den Engelska licens version, även om du installerar programvaran met att annat språk.';
$string['dialog_license_i_agree'] = 'Jag instämmer';
$string['dialog_license_you_must_accept'] = 'Du måste godta licensavtalet genom att skriva &quot;<b>{IAGREE}</b>&quot; (utan citationstecken) i nedanstående ruta.';
$string['dialog_database'] = 'Databas';
$string['dialog_database_title'] = 'Mata in information om databasserver';
$string['dialog_database_explanation'] = 'Var vänlig och matar in egenskaper av din databas i nedanstående rutar';
$string['db_type_label'] = 'Modell';
$string['db_type_help'] = 'Välj en av dem tillgängliga databas modeller';
$string['db_type_option_mysql'] = 'MySQL';
$string['db_server_label'] = 'Server';
$string['db_server_help'] = 'Detta är adressen till datavasserver, vanligen <strong>localhost</strong>. Andra exemplen: <strong>mysql.example.org</strong> eller <strong>example.dbserver.provider.net:3306</strong>.';
$string['db_username_label'] = 'Användernamn';
$string['db_username_help'] = 'En giltig kombination av användernamn och lösenord krävs för att koppla dig till databasservern. Var vänlig och använd inte baskontot till databasservern utan en mindre priviligierat ett, t ex <strong>wasuser</strong> eller <strong>example_wwwa</strong>';
$string['db_password_label'] = 'Lösenord';
$string['db_password_help'] = 'En giltig kombination av användernamn och lösenord krävs för att koppla dig till databasservern';
$string['db_name_label'] = 'Databasnamn';
$string['db_name_help'] = 'Detta är namnet av databasen du ska använda. Obs! Databasen borde redan finnas, installationsprogrammet är inte konstruerat för att skapa databas (av säkerhetsskäl). Exemplen: <strong>www</strong> eller <strong>example_www</strong>.';
$string['db_prefix_label'] = 'Prefix';
$string['db_prefix_help'] = 'Namnen av alla tabeller i databasen börjar med denna prefix. Detta tillåter fler installationer i samma databas. Ta hänsyn till att prefixet ska börja med en bokstav. Exemplen: <strong>was_</strong> eller <strong>cms2_</strong>.';
$string['dialog_cms'] = 'Webbsajt';
$string['dialog_cms_title'] = 'Mata in viktig information till sajten';
$string['dialog_cms_explanation'] = 'Var vänlig och matar in viktig information till sajten i nedanstående fält.';
$string['cms_title_label'] = 'Namn';
$string['cms_title_help'] = 'Namnet av din webbsajt';
$string['cms_website_from_address_label'] = 'Från: e-postadress';
$string['cms_website_from_address_help'] = 'E-postadressen används för utgående e-post, t ex varningar eller lösenord påminnelser';
$string['cms_website_replyto_address_label'] = 'Svar till: e-postadress';
$string['cms_website_replyto_address_help'] = 'E-postadressen ska tillfogas till utgående e-post och kan användas för att specificera en e-brevlåda där svar ska läsas (av dig) och inte blir förkastat (av webbserverns programvara).';
$string['cms_dir_label'] = 'Webbsajts katalog';
$string['cms_dir_help'] = 'Detta är vägen till katalogen som innehåller index.php och config.php, t ex <strong>/home/httpd/htdocs</strong> eller <strong>C:\Program Files\Apache Group\Apache\htdocs</strong>.';
$string['cms_www_label'] = 'Webbsajts URL';
$string['cms_www_help'] = 'Detta är huvud URL som ledar till din webbsajt, dvs. stället där index.php kan besökas. Exemplen är: <strong>http://www.example.org</strong> eller <strong>https://example.org:443/schoolsite</strong>.';
$string['cms_progdir_label'] = 'Programkatalogen';
$string['cms_progdir_help'] = 'Detta är vägen till katalogen som innehåller filerna till Website@School programmet (vanligtvis i underkatalogen <strong>program</strong> av webbsajtens katalog). Exemplen: <strong>/home/httpd/htdocs/program</strong> eller <strong>C:\Program Files\Apache Group\Apache\htdocs\program</strong>.';
$string['cms_progwww_label'] = 'Program URL';
$string['cms_progwww_help'] = 'Detta är URL-en som ledar till programkatalogen (vanligtvis webbsajtens URL följd av <strong>/program</strong>). Exemplen: <strong>http://www.example.org/program</strong> eller <strong>https://example.org:443/schoolsite/program</strong>.';
$string['cms_datadir_label'] = 'Datakatalogen';
$string['cms_datadir_help'] = 'Detta är en katalog som innehåller uppladdade filer och andra datafiler. Det är mycket viktigt att katalogen finns utanför dokumentets rot, dvs. att den inte är direkt åtkomlig med en webbläsaren. Ta hänsyn till att webbservern ska ha tillräckligt med permissioner för att läsa, skapa och skriva filer. Exemplen är: <strong>/home/httpd/wasdata</strong> eller <strong>C:\Program Files\Apache Group\Apache\wasdata</strong>.';
$string['cms_demodata_label'] = 'Befolka databas';
$string['cms_demodata_help'] = 'Klicka rutan om du vill börja att befolka din webbsajt med demonstrations datainnehåll';
$string['cms_demodata_password_label'] = 'Demonstrations lösenord';
$string['cms_demodata_password_help'] = 'Lösenordet ska anvisas till <em>alla</em> demonstrations användarkonton. Var vänlig och välj ett bra lösenord: välj åtminstone 8 skrifttecken ifrån storbokstäver, små bokstäver och siffror. Du kan lämna fältet tomt om du inte har klickat rutan \'Befolka databas\' ovanför.';
$string['dialog_user'] = 'Använderkonto';
$string['dialog_user_title'] = 'Skapa första konto';
$string['dialog_user_explanation'] = 'Var vänlig och mata in information till det första använderkontot för denna nya webbsajt. Ta hänsyn till att detta konto ska ha alla administrations privileger och alla permissioner som finns, så vem som helst som har tillgång till detta konto kan göra vad som helst!';
$string['user_full_name_label'] = 'Fullständigt namn';
$string['user_full_name_help'] = 'Var vänlig och ange här ditt namn, eller om du föredrar, ett annat (funktionellt) namn, t ex <strong>Wilhelmina Bladergroen</strong> eller <strong>Meester Web</strong>.';
$string['user_username_label'] = 'Användarnamn';
$string['user_username_help'] = 'Var vänlig och ange här ditt namn för att logga in som du vill använda för kontot. Du ska skriva namnet varje gång du vill logga in. Exemplen:  <strong>wblade</strong> eller <strong>webmeester</strong>.';
$string['user_password_label'] = 'Lösenord';
$string['user_password_help'] = 'Tänk på ett bra lösenord: välj åtminstone 8 skrivtecken från stor bokstäver, små bokstäver, siffror och special tecken såsom % (procenttecken), = (likhetstecken), / (snedstreck) och . (punkt). Dela inte ditt lösenord med andra, men skapa ytterligare konto för dina kolleger istället.';
$string['user_email_label'] = 'E-postadress';
$string['user_email_help'] = 'Var vänlig och ange ditt e-postadress här. Du behöver adressen när du ska efterfråga ett nytt lösenord. Ta hänsyn till att bara du har tillgång till e-postlådan (använd inte en delad e-brevlåda). Exemplen: <strong>wilhelmina.bladergroen@example.org</strong> eller <strong>webmaster@example.org</strong>.';
$string['dialog_compatibility'] = 'Kompatibilitet';
$string['dialog_compatibility_title'] = 'Kontrolera kompatibilitet';
$string['dialog_compatibility_explanation'] = 'Undan finns ett översikt av nödvändiga och önskade insättningar. Du måste se till att kraven är uppfyllda innan du fortsätter';
$string['compatibility_label'] = 'Prövning';
$string['compatibility_value'] = 'Värde';
$string['compatibility_result'] = 'Resultat';
$string['compatibility_ok'] = 'OK';
$string['compatibility_warning'] = 'VARNING';
$string['compatibility_websiteatschool_version_label'] = 'Website@School';
$string['compatibility_websiteatschool_version_check'] = '(kolla)';
$string['compatibility_websiteatschool_version_value'] = 'version {RELEASE} ({VERSION}) {RELEASE_DATE}';
$string['compatibility_websiteatschool_version_check_title'] = 'Kolla för nyare Website@School-versioner';
$string['compatibility_phpversion_label'] = 'PHP-version';
$string['compatibility_phpversion_obsolete'] = 'PHP-version är föråldrade';
$string['compatibility_phpversion_too_old'] = 'PHP-version är för gammal: minimum är {MIN_VERSION}';
$string['compatibility_php_safemode_label'] = 'PHP felsäkert läge';
$string['compatibility_php_safemode_warning'] = 'Felsäkert läge är på. Var vänlig och stäng av den i php.ini';
$string['compatibility_webserver_label'] = 'Webbserver';
$string['compatibility_autostart_session_label'] = 'Automatisk början av session';
$string['compatibility_autostart_session_fail'] = 'Automatisk början av session är på. Var vänlig och stäng av den i php.ini';
$string['compatibility_file_uploads_label'] = 'Ladda upp filer';
$string['compatibility_file_uploads_fail'] = 'Ladda upp filer är avstängt. Var vänlig och sätt på den i php.ini';
$string['compatibility_database_label'] = 'Databasserver';
$string['compatibility_clamscan_label'] = 'Clam granskning antivirus (programm)';
$string['compatibility_clamscan_not_available'] = '(inte tillgänglig)';
$string['compatibility_gd_support_label'] = 'GD-stöd';
$string['compatibility_gd_support_none'] = 'GD stöds inte';
$string['compatibility_gd_support_gif_readonly'] = 'Endast läsa';
$string['compatibility_gd_support_details'] = '{VERSION} (GIF: {GIF}, JPG: {JPG}, PNG: {PNG})';
$string['dialog_confirm'] = 'Anpassning';
$string['dialog_confirm_title'] = 'Bekräfta anpassningsinsättningar';
$string['dialog_confirm_explanation'] = 'Du är på väg att installera din nya webbsajt. Kolla noggrant alla anpassningsinsättningar nedan och tryck [Följande] för att verkligen börja installationsprocessen. Det kan ta ett tag.';
$string['dialog_confirm_printme'] = 'Tips: Skriv ut denna sida och hålla papperskopia för framtida referens';
$string['dialog_cancelled'] = 'Inställd';
$string['dialog_cancelled_title'] = '';
$string['dialog_cancelled_explanation'] = 'Installation av Website@School är inställd. Tryck på knappen nedan för att försöka igen eller klicka på hjälp knappen för att läsa handboken.';
$string['dialog_finish'] = 'Avsluta';
$string['dialog_finish_title'] = 'Avslut installations förfarandet';
$string['dialog_finish_explanation_0'] = 'Installationen av  Website@School {VERSION} är nästan färdig.<p>Du behöver göra två saker till: <ol><li>Du ska {AHREF} ladda ner {A} filen config.php, och <li>du ska placera filen config.php i <tt><strong>{CMS_DIR}</strong></tt>.</ol> Om filen config.php är på plats, kan du stänga av installationsprogrammet genom att trycka på [OK]-knappen nedan.';
$string['dialog_finish_explanation_1'] = 'Installationen av Website@School {VERSION} är färdig.<p>Du kan stänga av installationsprogrammet genom att trycka på nedanstående [OK] knappen.';
$string['dialog_finish_check_for_updates'] = 'Om du vill kan du, genom nedanstående länk, kolla för nya uppdateringar (länker öppnar i ett nytt fönster).';
$string['dialog_finish_check_for_updates_anchor'] = 'Kolla för Website@School uppdateringar';
$string['dialog_finish_check_for_updates_title'] = 'Kolla din status av Website@School-version';
$string['jump_label'] = 'Gå till';
$string['jump_help'] = 'Välj platsen du vill gå till efter att du har tryckt på nedanstående [OK]-knapp.';
$string['dialog_download'] = 'Ladda ner config.php';
$string['dialog_download_title'] = 'Ladda ner config.php till din dator';
$string['dialog_unknown'] = 'Okänt';
$string['error_already_installed'] = 'Fel: Website@School är redan installerad';
$string['error_wrong_version'] = 'Fel: versionsnummer stämmer inte. Har du laddat ner en ny version under installationen?';
$string['error_fatal'] = 'Öåterkalleligt fel {ERROR}: var vänlig och ta kontakt med &lt;{EMAIL}&gt; för att få hjälp';
$string['error_php_obsolete'] = 'Fel: versionen av PHP är för gammal';
$string['error_php_too_old'] = 'Fel: versionen av PHP ({VERSION}) är för gammal: använd åtminstone version {MIN_VERSION}';
$string['error_not_dir'] = 'Fel: {FIELD}: katalogen finns inte: {DIRECTORY}';
$string['warning_switch_to_custom'] = 'Varning: bytar till anpassad installation för att alla fel kan bli korrigerade';
$string['error_not_create_dir'] = 'Fel: {FIELD}: katalogen kan inte skapas: {DIRECTORY}';
$string['error_db_unsupported'] = 'Fel:database modell {DATABASE} stöds inte';
$string['error_db_cannot_connect'] = 'Fel: kan inte ta kontakt med databasservern';
$string['error_db_cannot_select_db'] = 'Fel: kan inte öppna databasen';
$string['error_invalid_db_prefix'] = 'Fel: {FIELD}: ska börja med en bokstav, får endast innehålla bokstäver, siffror och understreck';
$string['error_db_prefix_in_use'] = 'Fel: {FIELD}: används redan i databasen: {PREFIX}';
$string['error_time_out'] = 'Öåterkalleligt fel: tidsgräns';
$string['error_db_parameter_empty'] = 'Fel: parametrar av databasen ska inte vara tom';
$string['error_db_forbidden_name'] = 'Fel: {FIELD}: namnet är inte tillåtet {NAME}';
$string['error_too_short'] = 'Fel: {FIELD}: inmatad text är för kort (mimimum = {MIN})';
$string['error_too_long'] = 'Felt: {FIELD}: inmatad text är för långt (maximum = {MAX})';
$string['error_invalid'] = 'Fel: {FIELD}: ogiltig värde';
$string['error_bad_password'] = 'Fel: {FIELD}: värde inte godkänt; minimim krav är: siffor: {MIN_DIGIT}, små bokstav: {MIN_LOWER}, stor bokstav: {MIN_UPPER}';
$string['error_bad_data'] = '{MENU_ITEM}: fel upptäckt i inmatad text: var vänlig och korrigera dem först genom menyn';
$string['error_file_not_found'] = 'Fel: fil inte hittat: {FILENAME}';
$string['error_create_table'] = 'Fel: kan inte skapa tabellen: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_insert_into_table'] = 'Fel: kan inte foga in data i tabellen {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_update_config'] = 'Fel: kan inte uppdatera kofigurationen: {CONFIG} ({ERRNO}/{ERROR})';
$string['warning_no_manifest'] = 'Varning: tom manifest eller ingen hittat för {ITEM}';
$string['error_install_demodata'] = 'Fel: kan inte installera demonstrations data';
$string['error_directory_exists'] = 'Fel: {FIELD}:katalogen finns redan: {DIRECTORY}';
$string['error_nameclash'] = 'Fel: {FIELD}: var vänlig och  ändra på namnet {USERNAME}; namnet används redan som demonstrations använderkonto';
$string['warning_mysql_obsolete'] = 'Varning: version \'{VERSION}\' av MySQL är föråldraden och stödjer inte UTF-8. Var vänlig och uppdatera MySQL';
?>