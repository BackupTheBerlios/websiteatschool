<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2012 Vereniging Website At School, Amsterdam, <info@websiteatschool.eu>
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

/** /program/install/languages/de/install.php
 *
 * Language: de (Deutsch)
 * Release:  0.90.3 / 2012041700 (2012-04-17)
 *
 * @author Fabienne Kudzielka <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2012 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_de
 * @version $Id: install.php,v 1.1 2012/04/17 15:20:30 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Installieren';
$string['translatetool_description'] = 'Diese Datei enthält Übersetzungen des installierten Programms';
$string['websiteatschool_install'] = 'Website@School Installieren';
$string['websiteatschool_logo'] = 'Website@School Logo';
$string['help_name'] = 'Hilfe';
$string['help_description'] = 'Hilfe (öffnet ein neues Fenster)';
$string['next'] = 'Weiter';
$string['next_accesskey'] = 'W';
$string['next_title'] = 'Benützen Sie [Alt-W] oder [Cmnd-W] als Tastenkürzel für diesen Knopf';
$string['previous'] = 'Nächste';
$string['previous_accesskey'] = 'N';
$string['previous_title'] = 'Benützen Sie [Alt-N] oder [Cmnd-N] als Tastenkürzel für diesen Knopf';
$string['cancel'] = 'Abbrechen';
$string['cancel_accesskey'] = 'A';
$string['cancel_title'] = 'Benützen Sie [Alt-A] oder [Cmnd-A] als Tastenkürzel für diesen Knopf';
$string['ok'] = 'OK';
$string['ok_accesskey'] = 'K';
$string['ok_title'] = 'Benützen Sie [Alt-K] oder [Cmnd-K] als Tastenkürzel für diesen Knopf';
$string['yes'] = 'Ja';
$string['no'] = 'Nein';
$string['language_name'] = 'Deutsch';
$string['dialog_language'] = 'Sprache';
$string['dialog_language_title'] = 'Wählen Sie die Sprache der Installation';
$string['dialog_language_explanation'] = 'Bitten wählen Sie die Sprache die verwendet wird während des Installationsprozesses.';
$string['language_label'] = 'Sprache';
$string['language_help'] = '';
$string['dialog_installtype'] = 'Installations Typ';
$string['dialog_installtype_title'] = 'Wählen Sie zwischen Standart und Benutzerdefinierter Installation';
$string['dialog_installtype_explanation'] = 'Bitte wählen Sie die Installationsszenario aus der untenstehenden Liste';
$string['installtype_label'] = 'Installationsszenario';
$string['installtype_help'] = 'Bitte wählen Sie das betreffende Szenario.<br><strong>Standart</strong> bedeutet eine einfache Installation mit einem Minimum an zu beantwortenden Fragen,<br><strong>Benutzerdefiniert</strong> bietet Ihnen volle Kontrolle über alle Installationsmöglichkeiten.';
$string['installtype_option_standard'] = 'Standart';
$string['installtype_option_custom'] = 'Benutzerdefiniert';
$string['high_visibility_label'] = 'Hohe Sichtbarkeit';
$string['high_visibility_help'] = 'Kästchen anklicken um während der Installation die Text Schnittstelle anzuwenden.';
$string['dialog_license'] = 'Lizenz';
$string['dialog_license_title'] = 'Lesen und akzeptieren Sie die Lizenz für diese Software';
$string['dialog_license_explanation'] = 'Sie kriegen eine Lizenz für diese Software wenn, und nur dann wenn sie die folgenden Bedingungen und Konditionen lesen, verstehen und zustimmen. Beachten Sie, dass die englische Version der Lizenzvereinbarung zutrifft, selbst wenn Sie die Software in einer andere Sprache installieren.';
$string['dialog_license_i_agree'] = 'Ich stimme zu';
$string['dialog_license_you_must_accept'] = 'Sie stimmen der Vereinbarung zu indem Sie "<b>{IAGREE}</b>" (ohne Anführungszeichen) in das untenstehende Eingabefeld einführen.';
$string['dialog_database'] = 'Datenbank';
$string['dialog_database_title'] = 'Geben Sie Informationen ein über den Datenbank-Server';
$string['dialog_database_explanation'] = 'Bitte geben Sie die Eigenschaften des Datenbank-Servers ein in den untenstehenden Feldern.';
$string['db_type_label'] = 'Typ';
$string['db_type_help'] = 'Selektieren Sie einen der verfügbaren Datenbank-Typen.';
$string['db_type_option_mysql'] = 'MySQL';
$string['db_server_label'] = 'Server';
$string['db_server_help'] = 'Dies ist die Datenbank-Server Adresse, meistens <strong>localhost</strong>. Weitere Beispiele: <strong>mysql.beispiel.org</strong> oder <strong>beispiel.dbserver.provider.net:3306</strong>.';
$string['db_username_label'] = 'Benutzername';
$string['db_username_help'] = 'Eine gültige Benutzername/Passwort Kombination is nötig für die Verbindung mit dem Datenbank-Server. Bitte benutzen Sie nicht den Root-Benutzerzugang des Datenbank-Servers, sondern einen weiniger Bevorzugten, z.B. <strong>wasbenutzer</strong> oder <strong>beispiel_wwwa</strong>.';
$string['db_password_label'] = 'Passwort';
$string['db_password_help'] = 'Für die Verbindung mit dem Datenbank Server is eine gültige Benutzername/Passwort- Kombination nötig.';
$string['db_name_label'] = 'Datenbank Name';
$string['db_name_help'] = 'Dies ist der Name der anzuwendenden Datenbank. Beachten Sie, dass diese Datenbank schon existiert; dieses Installationsprogramm ist (aus Sicherheit) nicht entworfen für die Erstellung von Datenbanken. Beispiele: <strong>www</strong> oder <strong>beispiel_www</strong>.';
$string['db_prefix_label'] = 'Präfix';
$string['db_prefix_help'] = 'Alle Tabellennamen in der Datenbank beginnen mit  diesem Präfix. Dies erlaubt mehrfache Installation in derselben Datenbank. Beachten Sie, dass das Präfix mit einem Buchstaben beginnen muss. Beispiele: <strong>was_</strong> oder <strong>cms2_</strong>.';
$string['dialog_cms'] = 'Website';
$string['dialog_cms_title'] = 'Geben Sie die wesentlichen Website-Informationen ein.';
$string['dialog_cms_explanation'] = 'Bitte geben Sie die wesentlichen Website-Informationen in den untenstehenden Feldern ein.';
$string['cms_title_label'] = 'Titel der Website';
$string['cms_title_help'] = 'Der Name Ihrer Website.';
$string['cms_website_from_address_label'] = 'Von: Adresse';
$string['cms_website_from_address_help'] = 'Diese Adresse wird benutzt für ausgehende E-mails, z.B.  Warnungen und Passwort Erinnerungen.';
$string['cms_website_replyto_address_label'] = 'Antworten: Adresse';
$string['cms_website_replyto_address_help'] = 'Diese E-Mail Adresse ist zugefügt bei ausgehenden E-Mails und kann benutzt werden um die Mailbox zu spezifizieren, wo (durch Sie selbst) die Antworten gelesen werden können und nicht verworfen werden (durch die Webserver Software)';
$string['cms_dir_label'] = 'Website Verzeichnis';
$string['cms_dir_help'] = 'Dies ist der Pfad zum Verzeichnis das index.php und config.php beinhaltet, z.B. <strong>/home/httpd/htdocs</strong> or <strong>C:\Program Files\Apache Group\Apache\htdocs</strong>.';
$string['cms_www_label'] = 'Website URL';
$string['cms_www_help'] = 'Dies ist der Haupt-URL der zu Ihrer Website führt, das heisst zum Ort wo index.php besichtigt werden kann. Beispiele: <strong>http://www.beispiele.org</strong> oder <strong>https://beispiel.org:443/schoolsite</strong>.';
$string['cms_progdir_label'] = 'Programm Verzeichnis';
$string['cms_progdir_help'] = 'Dies ist der Pfad zum Verzeichnis, das Website@School Programm Dateien enthält (meistens das Unterverzeichnis <strong>program</strong> des Website Verzeichnisses). Beispiele: <strong>/home/httpd/htdocs/program</strong> oder <strong>C:\Program Files\Apache Group\Apache\htdocs\program</strong>.';
$string['cms_progwww_label'] = 'Programm URL';
$string['cms_progwww_help'] = 'Dies ist der URL der zum Programm Verzeichnis führt (meistens der Website URL gefolgt durch <strong>/program</strong>). Beispiele: <strong>http://www.beispiel.org/programm</strong> oder <strong>https://beispiel.org:443/schoolsite/programm</strong>.';
$string['cms_datadir_label'] = 'Daten Verzeichnis';
$string['cms_datadir_help'] = 'Dies is ein Verzeichnis das übertragene Dateien und andere Dateien enthält. Es ist sehr wichtig, dass dieses Verzeichnis ausserhalb der Document-Root lokalisiert ist, das heisst, dass es nicht direkt zugänglich ist mit eine Browser. Beachten Sie, dass der Webserver ausreichend berechtigt ist Dateien zu lesen, zu erzeugen und zu schreiben. Beispiele: <strong>/home/httpd/wasdata</strong> oder <strong>C:\Program Files\Apache Group\Apache\wasdata</strong>.';
$string['cms_demodata_label'] = 'Datenbank einrichten';
$string['cms_demodata_help'] = 'Klicken Sie das Kästchen an, wenn Sie Ihre neue Website beginnen wollen mit Hilfe von Demonstrationsdaten.';
$string['cms_demodata_password_label'] = 'Demonstration-Passwort';
$string['cms_demodata_password_help'] = 'Dasselbe Demonstration-Passwort wird zugewiesen an <em>alle</em> Demonstration-Benutzerzugänge. Bitte wählen Sie ein gutes Passwort: wählen Sie mindestens 8 Zeichen bestehend aus Grossbuchstaben, Kleinbuchstaben und Ziffern. Sie können diese Feld leer lassen, wenn Sie oben das Kästchen \'Datenbank einrichten\' nicht angeklickt haben.';
$string['dialog_user'] = 'Benutzer Zugang';
$string['dialog_user_title'] = 'Erstellen Sie den ersten Zugang';
$string['dialog_user_explanation'] = 'Bitte geben Sie die Informationen ein für den ersten Zugang für diese neue Website. Bitte beachten Sie, dass dieser Zugang alle Administrator Privilegien und alle möglichen Berechtigungen haben wird, so dass jeder mit Zugriff zum Zugang alles tun kann.';
$string['user_full_name_label'] = 'Vollständiger Name';
$string['user_full_name_help'] = 'Bitten geben Sie ihren eigenen Namen ein, oder wenn Sie es bevorzugen einen anderen (sachlichen) Namen, z.B. <strong>Wilhelmina Bladergroen</strong> oder <strong>Master Web</strong>.';
$string['user_username_label'] = 'Benutzername';
$string['user_username_help'] = 'Bitte geben Sie den Login-Namen ein, den Sie für diesen Zugang benützen wollen. Sie müssen diesen Namen jedes Mal eingeben zum Einloggen. Beispiele: <strong>wblade</strong> oder <strong>webmaster</strong>.';
$string['user_password_label'] = 'Passwort';
$string['user_password_help'] = 'Bitten wählen Sie ein gutes Passwort: wählen Sie mindestens 8 Zeichen bestehend aus Grossbuchstaben, Kleinbuchstaben, Ziffern und speziellen Zeichen wie % (Prozent), = (Gleichheitszeichen), / (Schrägstrich) und . (Punkt). Teilen Sie das Passwort nicht mit Anderen, sondern erstellen Sie zusätzliche Zugänge für Ihre Kollegen.';
$string['user_email_label'] = 'E-Mail Adresse';
$string['user_email_help'] = 'Bitte geben Sie hier Ihre E-Mail Adress ein. Sie benötigen diese Adresse jedes Mal wenn Sie ein neues Passwort anfordern. Vergewissern Sie sich, dass nur Sie Zugang haben zu dieser Mailbox (benützen Sie keine geteilte Mailbox). Bespiele: <strong>wilhelmina.bladergroen@beispiel.org</strong> oder <strong>webmaster@beispiel.org</strong>.';
$string['dialog_compatibility'] = 'Kompatibilität';
$string['dialog_compatibility_title'] = 'Kompatibilität kontrollieren';
$string['dialog_compatibility_explanation'] = 'Unten sehen Sie eine Übersicht mit notwendigen und gewünschten Einstellungen. Vergewissern Sie sich, dass Anforderungen erfüllt sind ehe Sie weiter gehen.';
$string['compatibility_label'] = 'Test';
$string['compatibility_value'] = 'Wert';
$string['compatibility_result'] = 'Resultat';
$string['compatibility_ok'] = 'OK';
$string['compatibility_warning'] = 'WARNUNG';
$string['compatibility_websiteatschool_version_label'] = 'Website@School';
$string['compatibility_websiteatschool_version_check'] = '(überprüfen)';
$string['compatibility_websiteatschool_version_value'] = 'Version {RELEASE} ({VERSION}) {RELEASE_DATE}';
$string['compatibility_websiteatschool_version_check_title'] = 'Check für neuere Versionen von Website@School';
$string['compatibility_phpversion_label'] = 'PHP Version';
$string['compatibility_phpversion_obsolete'] = 'PHP Version is überholt';
$string['compatibility_phpversion_too_old'] = 'PHP Version ist zu alt: Minimum ist {MIN_VERSION}';
$string['compatibility_php_safemode_label'] = 'PHP Sicherheits Modus';
$string['compatibility_php_safemode_warning'] = 'Sicherheits Modus is Ein. Bitte schalten Sie es Aus in php.ini';
$string['compatibility_webserver_label'] = 'Webserver';
$string['compatibility_autostart_session_label'] = 'Automatische Session Start';
$string['compatibility_autostart_session_fail'] = 'Automatische Session Start ist Ein. Bitte schalten Sie es Aus in php.ini';
$string['compatibility_file_uploads_label'] = 'Dateien Uploads';
$string['compatibility_file_uploads_fail'] = 'Dateien Uploads is Aus. Bitten schalten Sie es Ein in php.ini';
$string['compatibility_database_label'] = 'Datenbank Server';
$string['compatibility_clamscan_label'] = 'Clamscan Anti-Virus';
$string['compatibility_clamscan_not_available'] = '(nicht verfügbar)';
$string['compatibility_gd_support_label'] = 'GD Unterstützung';
$string['compatibility_gd_support_none'] = 'GD wird nicht unterstützt';
$string['compatibility_gd_support_gif_readonly'] = 'Nur lesen';
$string['compatibility_gd_support_details'] = '{VERSION} (GIF: {GIF}, JPG: {JPG}, PNG: {PNG})';
$string['dialog_confirm'] = 'Bestätigung';
$string['dialog_confirm_title'] = 'Einstellungen bestätigen';
$string['dialog_confirm_explanation'] = 'Sie sind im Begriff Ihre neue Website zu installieren. Kontrollieren Sie die Konfigurations-Einstellungen sorgfälltig und drücken Sie anschliessend [Nächste] um den aktuellen Installationsprozess zu starten. Dies kann eine Weile dauern.';
$string['dialog_confirm_printme'] = 'Tipp: drucken Sie diese Seite aus und bewahren Sie die Kopie für zukünftige Referenz.';
$string['dialog_cancelled'] = 'Abgebrochen';
$string['dialog_cancelled_title'] = '';
$string['dialog_cancelled_explanation'] = 'Die Installation der Website@School wurde abgebrochen. Drücken Sie den untenstehenden Knopf für Wiederholung oder drücken Sie den Hilfe-Knopf um das Handbuch zu lesen.';
$string['dialog_finish'] = 'Beenden';
$string['dialog_finish_title'] = 'Installationsprozess beenden';
$string['dialog_finish_explanation_0'] = 'Die Installation von Website@School {VERSION} ist nun beinahe komplett. <p> Noch zwei Dinge sind zu tun: <ol><il> Sie müssen jetzt die Datei config.php {AHREF}herunterladen{A}, und<li>Sie müssen die Datei config.php in <tt><strong>{CMS_DIR}</strong></tt> platzieren. Wenn config.php platziert ist, können Sie die Installation schliessen, indem Sie den untenstehenden [OK] -Knopf drücken.';
$string['dialog_finish_explanation_1'] = 'Die Installation von Website@School {VERSION} ist jetzt komplett.<p>Sie können die Installation schliessen, indem Sie den untenstehenden [OK] -Knopf drücken.';
$string['dialog_finish_check_for_updates'] = 'Wenn Sie möchten, können Sie dem untenstehenden Link folgen, um Updates zu suchen (Link öffnet in neuem Fenster)';
$string['dialog_finish_check_for_updates_anchor'] = 'Updates für Website@School suchen.';
$string['dialog_finish_check_for_updates_title'] = 'Kontrollieren Sie den Status Ihrer Version von Website@School';
$string['jump_label'] = 'Gehe zu';
$string['jump_help'] = 'Wählen Sie den Ort wohin Sie wollen nachdem Sie den untenstehenden [OK]-Knopf gedrückt haben.';
$string['dialog_download'] = 'Laden Sie config.php herunter';
$string['dialog_download_title'] = 'Laden Sie config.php auf den Computer';
$string['dialog_unknown'] = 'Unbekannt';
$string['error_already_installed'] = 'Fehler: Website@School ist schon installiert';
$string['error_wrong_version'] = 'Fehler: falsche Versionsnummer. Haben Sie eine neue Version heruntergeladen während de Installation?';
$string['error_fatal'] = 'Fataler Fehler {ERROR}: für Assistenz kontaktieren Sie &lt;{EMAIL}&gt;';
$string['error_php_obsolete'] = 'Fehler: die Version von PHP ist zu alt';
$string['error_php_too_old'] = 'Fehler: die Version von PHP ({VERSION}) ist zu alt: benutzen Sie die neueste Version {MIN_VERSION}';
$string['error_not_dir'] = 'Fehler: {FIELD}: Verzeichnis existiert nicht: {DIRECTORY}';
$string['warning_switch_to_custom'] = 'Warnung: wechseln Sie zu Benutzer Installation, so dass Fehler korrigiert werden können.';
$string['error_not_create_dir'] = 'Fehler: {FIELD}: Verzeichnis kann nicht erstellt werden: {DIRECTORY}';
$string['error_db_unsupported'] = 'Fehler: Datenbank {DATABASE} ist zur Zeit nicht unterstützt';
$string['error_db_cannot_connect'] = 'Fehler: Verbindung mit dem Datenbank-Server kann nicht erstellt werden';
$string['error_db_cannot_select_db'] = 'Fehler: Datenbank kann nicht geöffnet werden';
$string['error_invalid_db_prefix'] = 'Fehler: {FIELD}: muss mit einem Buchstaben beginnen, darf nur Buchstaben, Ziffern oder Unterstrich enthalten';
$string['error_db_prefix_in_use'] = 'Fehler: {FIELD}: schon im Einsatz: {PREFIX}';
$string['error_time_out'] = 'Fataler Fehler: Unterbrechung';
$string['error_db_parameter_empty'] = 'Fehler: leere Datenbank-Parameter werden nicht akzeptiert';
$string['error_db_forbidden_name'] = 'Fehler: {FIELD}: dieser Name wird nicht akzeptiert: {NAME}';
$string['error_too_short'] = 'Fehler: {FIELD}: Zeichenfolge ist zu kurz (Mimimum = {MIN})';
$string['error_too_long'] = 'Fehler: {FIELD}: Zeichenfolge ist zu lang (Maximum = {MAX})';
$string['error_invalid'] = 'Fehler: {FIELD}: ungültiger Wert';
$string['error_bad_password'] = 'Fehler: {FIELD}: Wert wird nicht akzeptiert; mindest Voraussetzung: Ziffern: {MIN_DIGIT}, Kleinbuchstaben: {MIN_LOWER}, Grossbuchstaben: {MIN_UPPER}';
$string['error_bad_data'] = '{MENU_ITEM}: es sind Fehler gefunden, bitten korrigieren Sie diese erst (über das Menu)';
$string['error_file_not_found'] = 'Fehler: kann Datei nicht finden: {FILENAME}';
$string['error_create_table'] = 'Fehler: kann Tabelle nicht erzeugen: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_insert_into_table'] = 'Fehler: kann Daten nicht in Tabelle einfügen: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_update_config'] = 'Fehler: kann Konfiguration nicht updaten: {CONFIG} ({ERRNO}/{ERROR})';
$string['warning_no_manifest'] = 'Warnung: leeres Manifest oder kein Manifest für {ITEM}';
$string['error_install_demodata'] = 'Fehler: kann Demonstrations-Daten nicht installieren';
$string['error_directory_exists'] = 'Fehler: {FIELD}: Verzeichnis existiert schon: {DIRECTORY}';
$string['error_nameclash'] = 'Fehler: {FIELD}: bitte ändern Sie den Namen {USERNAME}; er wird schon gebraucht als ein Demonstrations-Benutzerzugang';
$string['warning_mysql_obsolete'] = 'Warnung: Version \'{VERSION}\' von MySQL ist veraltet und unterstützt UTF-8 nicht. Bitte updaten Sie MySQL';
?>