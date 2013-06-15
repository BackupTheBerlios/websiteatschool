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

/** /program/install/languages/da/install.php
 *
 * Language: da (Dansk)
 * Release:  0.90.4 / 2013061400 (2013-06-14)
 *
 * @author Christian Borum Loebner - Olesen  <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_da
 * @version $Id: install.php,v 1.5 2013/06/15 14:07:29 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Installere';
$string['translatetool_description'] = 'Denne fil indeholder oversættelse af installationsprogrammet';
$string['websiteatschool_install'] = 'Website@School iInstallering';
$string['websiteatschool_logo'] = 'Website@School logo';
$string['help_name'] = 'hjælp';
$string['help_description'] = 'Hjælp (åbner i et nyt vidnue)';
$string['next'] = 'Næste';
$string['next_accesskey'] = 'N';
$string['next_title'] = 'Brug [Alt-N] eller [Cmnd-N] som en tastaturgenvej for denne knap';
$string['previous'] = 'tidligere';
$string['previous_accesskey'] = 'P';
$string['previous_title'] = 'Brug [Alt-P] eller [Cmnd-P] som en tastaturgenvej for denne knap';
$string['cancel'] = 'Cancel';
$string['cancel_accesskey'] = 'C';
$string['cancel_title'] = ' Brug [Alt-C] eller [Cmnd-C] som en tastaturgenvej for denne knap';
$string['ok'] = 'OK';
$string['ok_accesskey'] = 'K';
$string['ok_title'] = 'Brug [Alt-K] eller [Cmnd-K] som en tastaturgenvej for denne kanp';
$string['yes'] = 'Ja';
$string['no'] = 'Nej';
$string['language_name'] = 'Dansk';
$string['dialog_language'] = 'Sprog';
$string['dialog_language_title'] = 'Vælg installeringssprog';
$string['dialog_language_explanation'] = 'Vælg det sprog der skal bruges under installeringsproceduren';
$string['language_label'] = 'Sprog';
$string['language_help'] = '';
$string['dialog_installtype'] = 'Installationstype';
$string['dialog_installtype_title'] = 'Vælg mellem standard og brugerdefineret installation';
$string['dialog_installtype_explanation'] = 'Vær venlig at vælge installationsscenariet fra listen nedenfor';
$string['installtype_label'] = 'Installationsscenario';
$string['installtype_help'] = 'Vær venlig at vælge det relevante installationsscenarie.<br><strong>Standard</strong> indebærer en direct installing med en minimum af spørgsmål der skal besvares<br><strong>Brugerdefineret</strong> giver dig fuld kontrol over alle installeringsmulgheder.';
$string['installtype_option_standard'] = 'Standard';
$string['installtype_option_custom'] = 'Brugerdefineret';
$string['high_visibility_label'] = 'Højsynlighed';
$string['high_visibility_help'] = 'Marker boksen for at bruge "kun-tekst" brugerflade under installationen';
$string['dialog_license'] = 'Licens';
$string['dialog_license_title'] = 'Læs og accepter dette softwares licens';
$string['dialog_license_explanation'] = 'Du har fået licens til dette software, men kun hvis du læser, forstår og acceptere the følgende krav og betingelser. Vær opmærksom på licensen for engelske version gælder, selvom du installere softwaren på et andet sprog';
$string['dialog_license_i_agree'] = 'Jeg accepterer';
$string['dialog_license_you_must_accept'] = 'Du skal acceptere licensaftalen ved at indtaste "<b>{IAGREE}</b>" (uden citation) i boksen nedenfor';
$string['dialog_database'] = 'Database';
$string['dialog_database_title'] = 'Indtast information om databasens server';
$string['dialog_database_explanation'] = 'Vær venlig at indtaste egenskaber for databasens server i feltet nedenfor';
$string['db_type_label'] = 'Type';
$string['db_type_help'] = 'Vælg en af de tilgængelige database-typer';
$string['db_type_option_mysql'] = 'MySQL';
$string['db_server_label'] = 'Server';
$string['db_server_help'] = 'Dette er databases server adresse, normalt <strong>localhost</strong>. Andre eksempler: <strong>mysql.example.org</strong> or <strong>example.dbserver.provider.net:3306</strong>.';
$string['db_username_label'] = 'Brugernavn';
$string['db_username_help'] = 'En validt brugernavn/password-kombination er påkrævet for at oprette forbindelse til databasens server. Vær venlig ikke at bruge "root" kontoen for databasen, men en mindre priviligeret en, f.eks.   <strong>wasuser</strong> or <strong>eksempel_wwwa</strong>.';
$string['db_password_label'] = 'Password';
$string['db_password_help'] = 'En validt brugernavn/password-kombination er påkrævet for at oprette forbindelse til databasens server.';
$string['db_name_label'] = 'Navn på database';
$string['db_name_help'] = 'Dette er navnet på databasen som skal bruges. Vær opmærksom på databasen allerede skal eksistere: dette installeringsprogram er ikke lavet til at lave databaser ( af sikkerhedsmæssige årsager). Eksempler <strong>www</strong> or <strong>eksempel_www</strong>.';
$string['db_prefix_label'] = 'Prefix';
$string['db_prefix_help'] = 'Alle tabelnavne i database starter med dette prefix. Dette muliggør flere installationer i den samme database. Vær opmærksom på at prefixet skal begynde med et bogstav. F.eks.  Examples: <strong>was_</strong> or <strong>cms2_</strong>.';
$string['dialog_cms'] = 'Webside';
$string['dialog_cms_title'] = 'Indtast det essentielle webside information';
$string['dialog_cms_explanation'] = 'Vær venlig at indtaste det essentielle information til hjemmesiden i felterne nedenfor.';
$string['cms_title_label'] = 'Navn på websiden';
$string['cms_title_help'] = 'Din websides navn';
$string['cms_website_from_address_label'] = 'Fra: addresse';
$string['cms_website_from_address_help'] = 'Denne e-mail adresse bruges til udgående mails, f.eks. alarmer og password påmindelser';
$string['cms_website_replyto_address_label'] = 'Svar til: addresse';
$string['cms_website_replyto_address_help'] = 'Denne e-mail adresse er tilføjet til udgående mail og kan bruges til specificere en mailbox hvor svar bliver læst (af dig) og ikke slettet (af webserverens software)';
$string['cms_dir_label'] = 'Website-mappe';
$string['cms_dir_help'] = 'Dette er en stig til mappen med index.php og konfig.php,f.eks. <strong>/hjem/httpd/htdocs</strong> eller <strong>C:\Program Files\Apache Group\Apache\htdocs</strong>.';
$string['cms_www_label'] = 'Webside URL';
$string['cms_www_help'] = 'Dette er hoved URL som leder til din hjemmeside - det sted hvor index.php can findes Eksempler kunne være: <strong>http://www.example.org</strong> or <strong>https://example.org:443/schoolsite</strong>.';
$string['cms_progdir_label'] = 'Programmappe';
$string['cms_progdir_help'] = 'Dette er stigen til mappen indeholdende Website@School programfiler (normal undermappen <strong>program</strong> of the website directory). Examples: <strong>/home/httpd/htdocs/program</strong> or <strong>C:\Program Files\Apache Group\Apache\htdocs\program</strong>.';
$string['cms_progwww_label'] = 'Program URL';
$string['cms_progwww_help'] = 'Dette er URL\'en som leder til programmappen (normalt websidens URL efterfulgt af <strong>/program</strong>). Examples are: <strong>http://www.example.org/program</strong> or <strong>https://example.org:443/schoolsite/program</strong>.';
$string['cms_datadir_label'] = 'Datamappe';
$string['cms_datadir_help'] = 'Denne mappe indeholder uploaded filer og andre datafiler. Det er meget vigtigt at denne mappe ligger udenfor dokumentets rod, som derfor ikke er direkte tilgængelig med en browser. Vær opmærksom på at webserveren skal have tilstrækkelig adgang til at læse, lave og skrive filer her. Eksempler er: <strong>/home/httpd/wasdata</strong> or <strong>C:\Program Files\Apache Group\Apache\wasdata</strong>.';
$string['cms_demodata_label'] = 'Fyld databasen';
$string['cms_demodata_help'] = 'Marker denne boks hvis du vil starte din nye webside med demonstrationsdata';
$string['cms_demodata_password_label'] = 'Demonstrations password';
$string['cms_demodata_password_help'] = 'Det samme demonstrations password will blive tilskrevet til <em>alle</em> demonstrations brugerkonti. Vær venlig at vælge et godt password: vælg mindst 8 tegn - små bogstaver, store bogstaver og tal. Du kan lade dette felt stå tomt, hvis du ikke markerede boksen, "Fyld databasen" ovenfor.';
$string['dialog_user'] = 'Brugerkonto';
$string['dialog_user_title'] = 'Lav den første konto';
$string['dialog_user_explanation'] = 'Vær venlig at indtaste information til den første brugerkonto på denne nye hjemmeside. Vær opmærksom på at denne konto har fuld administrator privilegier og har adgang til alt, så enhver der har adgang til denne konto har mulighed for at gøre alt.';
$string['user_full_name_label'] = 'Fuldt navn';
$string['user_full_name_help'] = 'Vær venlig at indtaste fit eget navn eller, hvis du vil, et andet (funktionelt) navn, f.eks. <strong>Wilhelmina Bladergroen</strong> eller <strong>Master Web</strong>.';
$string['user_username_label'] = 'Brugernavn';
$string['user_username_help'] = 'Vær venlig at indtaste et loginnavn som du vil bruge til denne konti. Du skal skrive dette navn hver  gang du vil logge ind. Eksempler: <strong>wblade</strong> or <strong>webmaster</strong>.';
$string['user_password_label'] = 'Password';
$string['user_password_help'] = 'Vær venlig at vælge et godt password: Vælg minimum 8 tegn: store bogstaver, små bogstaver, tal og special tegn, såsom % (procent), = (lighedstegn),  /(skråstreg) og . (punktum). Lad være med at dele dit password med andre, men lav yderligere konti til dine kollegaer i stedet for.';
$string['user_email_label'] = 'E-mail addresse';
$string['user_email_help'] = 'Vær venlig at indtaste din mailadresse her. Du skal bruge denne adresse hver gang du vil have et nyt password. Vær sikker på at det kun er dig, der har adgang til denne mailbox (brug ikke en delt mailbox). Eksempler: Examples: <strong>wilhelmina.bladergroen@example.org</strong> or <strong>webmaster@example.org</strong>.';
$string['dialog_compatibility'] = 'Kompatibilitet';
$string['dialog_compatibility_title'] = 'Tjek kompatibilitet';
$string['dialog_compatibility_explanation'] = 'Nedenfor er en oversigt over påkrævede og ønskede indstillinger. Du skal være sikker på at kravene are mødt før du forsætter';
$string['compatibility_label'] = 'Test';
$string['compatibility_value'] = 'Værdi';
$string['compatibility_result'] = 'Resultat';
$string['compatibility_ok'] = 'OK';
$string['compatibility_warning'] = 'Advarsel';
$string['compatibility_websiteatschool_version_label'] = 'Website@School';
$string['compatibility_websiteatschool_version_check'] = '(tjek)';
$string['compatibility_websiteatschool_version_value'] = 'version {RELEASE} ({VERSION}) {RELEASE_DATE}';
$string['compatibility_websiteatschool_version_check_title'] = 'Tjek for senere versioner af Website@School';
$string['compatibility_phpversion_label'] = 'PHP version';
$string['compatibility_phpversion_obsolete'] = 'PHP version er forældet';
$string['compatibility_phpversion_too_old'] = 'PHP versionen er forældet: minimum er {MIN_VERSION}';
$string['compatibility_php_safemode_label'] = 'PHP sikker tilstand';
$string['compatibility_php_safemode_warning'] = 'Sikker tilstand er aktiveret. Vær venlig at slå det fra i php.ini';
$string['compatibility_webserver_label'] = 'Webserver';
$string['compatibility_autostart_session_label'] = 'Automatisk session start';
$string['compatibility_autostart_session_fail'] = 'Automatic session start er aktiveret. Vær venlig at slå det fra i php.ini';
$string['compatibility_file_uploads_label'] = 'Fil-upload';
$string['compatibility_file_uploads_fail'] = 'File uploads er slået fra. Vær venlig at slå det til i php.ini';
$string['compatibility_database_label'] = 'Databaseserver';
$string['compatibility_clamscan_label'] = 'Clamscan anti-virus';
$string['compatibility_clamscan_not_available'] = '(ikke tilgængelig)';
$string['compatibility_gd_support_label'] = 'GD understøttelse';
$string['compatibility_gd_support_none'] = 'GD er ikke understøttet';
$string['compatibility_gd_support_gif_readonly'] = 'Readonly';
$string['compatibility_gd_support_details'] = '{VERSION} (GIF: {GIF}, JPG: {JPG}, PNG: {PNG})';
$string['dialog_confirm'] = 'Bekræftelse';
$string['dialog_confirm_title'] = 'Bekræftelse';
$string['dialog_confirm_explanation'] = 'Du er i gang med at installere din nye webside. Tjek konfigurerings-indstillingerne omhyggeligt nedenfor og tryk derefter (Næste) for at starte den egentlige installeringsproces. Det tager et stykke tid.';
$string['dialog_confirm_printme'] = 'Tip: print denne side og behold hardcopy versionen for at fremtidig reference';
$string['dialog_cancelled'] = 'Annullere';
$string['dialog_cancelled_title'] = '';
$string['dialog_cancelled_explanation'] = 'Installeringen af Website@School er blevet annulleret. Tryk på knappen nedenfor for at prøve igen eller tryk på knappen, hjæp, for at læse manualen.';
$string['dialog_finish'] = 'Færdig';
$string['dialog_finish_title'] = 'Færdiggør installationsproceduren';
$string['dialog_finish_explanation_0'] = 'Installeringen af Website@School {VERSION} er nu næsten færdig.<p> Der skal gøres endnu to ting:<ol><li>Du skal nu {AHREF}downloade{A} filen konfig.php, og<li>Du skal palcere filen konfig.php i <tt><strong>{CMS_DIR}</strong></tt>.</ol>Når konfig.php er på plads, kan du lukke "installeringen" ved at trykke på [OK] knappen nedenfor.';
$string['dialog_finish_explanation_1'] = 'Installeringen af  Website@School {VERSION} er nu færdig.<p>Du kan lukke "installeringen"  ved at trykke på [OK] kanppen nedenfor.';
$string['dialog_finish_check_for_updates'] = 'Hvis du vil, kan du følge linket nedenfor for at tjekke for opdateringer ( linket åbner i et nyt vindue).';
$string['dialog_finish_check_for_updates_anchor'] = 'Tjek for Website@School opdateringer';
$string['dialog_finish_check_for_updates_title'] = 'Tjek status for din version af Website@School';
$string['jump_label'] = 'Gå til';
$string['jump_help'] = 'Vælg det sted som du vil gå til efter du har trykket [OK] knappen nedenfor';
$string['dialog_download'] = 'Download konfig.php';
$string['dialog_download_title'] = 'Download konfig.php til din computer';
$string['dialog_unknown'] = 'Ukendt';
$string['error_already_installed'] = 'Fejl: Website@School er allerede installeret';
$string['error_wrong_version'] = 'Fejl: forkert nummer af versionen. Downloadede du en ny version under installationen';
$string['error_fatal'] = 'Alvorlig fejl {ERROR}: Vær venlig at kontakte &lt;{EMAIL}&gt; for hjælp';
$string['error_php_obsolete'] = 'Fejl: PHP versionen er for gammel';
$string['error_php_too_old'] = 'Fejl: PHP version ({VERSION}) er for gammel: brug minimum {MIN_VERSION}';
$string['error_not_dir'] = 'Fejl: {FIELD}: mappen eksisterer ikke: {DIRECTORY}';
$string['warning_switch_to_custom'] = 'Fejl: skrift til brugerdefineret installering så fejlene kan rettes';
$string['error_not_create_dir'] = 'Fejl: {FIELD}: mappen kan ikke oprettes: {DIRECTORY}';
$string['error_db_unsupported'] = 'Fejl: database {DATABASE} understøttes ikke i øjeblikket';
$string['error_db_cannot_connect'] = 'Fejl:kan ikke oprettes forbindelse med databasens server';
$string['error_db_cannot_select_db'] = 'Fejl: kan ikke åbne databasen';
$string['error_invalid_db_prefix'] = 'Fejl: {FIELD}: skal begynde med et bogstav, må indeholde bogstaver, tal og underscores';
$string['error_db_prefix_in_use'] = 'Fejl: {FIELD}: bruges allerede: {PREFIX}';
$string['error_time_out'] = 'Alvorlig fejl: time-out';
$string['error_db_parameter_empty'] = 'Fejl: tomme database parametre accepteres ikke';
$string['error_db_forbidden_name'] = 'Fejl: {FIELD}: navnet kan ikke accepteres: {NAME}';
$string['error_too_short'] = 'Fejl: {FIELD}: strengen er for kort (mimimum = {MIN})';
$string['error_too_long'] = 'Fejl: {FIELD}: strengen er for lang (maximum = {MAX})';
$string['error_invalid'] = 'Fejl: {FIELD}: forkert værdi';
$string['error_bad_password'] = 'Fejl: {FIELD}: værdien kan ikke accepteres; minimum krav er: tal: {MIN_DIGIT}, små bogstaver: {MIN_LOWER}, store bogstaver: {MIN_UPPER}';
$string['error_bad_data'] = '{MENU_ITEM}: Fejl er fundet, vær venlig at rette disse først ( via menuen)';
$string['error_file_not_found'] = 'Fejl: kan ikke finde filen: {FILENAME}';
$string['error_create_table'] = 'Fejl: kan ikke oprette tabel: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_insert_into_table'] = 'Fejl: kan ikke indsætte data i tabellen: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_update_config'] = 'Fejl: kan ikke opdatere konfigurering: {CONFIG} ({ERRNO}/{ERROR})';
$string['warning_no_manifest'] = 'Advarsel: tom manifest eller intet manifest for {ITEM}';
$string['error_install_demodata'] = 'Fejl: kan ikke installere demonstrations data';
$string['error_directory_exists'] = 'Fejl: {FIELD}: mappen eksisterer allerede: {DIRECTORY}';
$string['error_nameclash'] = 'Fejl: {FIELD}: vær venlig at ændre navnet {USERNAME}; det er allerede i brug som demonstrations brugerkonti';
$string['warning_mysql_obsolete'] = 'Advarsel: version \'{VERSION}\' af MySQL er forældet og understøtter ikke UTF-8. Vær venlig at opgradere MySQL';
?>