<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2011 Vereniging Website At School, Amsterdam, <info@websiteatschool.eu>
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

/** /program/install/languages/pl/install.php
 *
 * Language: pl (Polski)
 * Release:  0.90.2 / 2011092900 (2011-09-29)
 *
 * @author Waldemar Pankiw <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2011 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_pl
 * @version $Id: install.php,v 1.1 2011/09/29 19:11:37 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Instalacja';
$string['translatetool_description'] = 'Ten plik zawiera tłumaczenie programu instalacyjnego';
$string['websiteatschool_install'] = 'Instalacja Website@School';
$string['websiteatschool_logo'] = 'Logo Website@School';
$string['help_name'] = 'Pomoc';
$string['help_description'] = 'pomoc (otwiera sie w nowyn oknie)';
$string['next'] = 'Następny';
$string['next_accesskey'] = 'N';
$string['next_title'] = 'Użyj skrótu [Alt-N] lub [Cmnd-N] dla tego przycisku';
$string['previous'] = 'Poprzedni';
$string['previous_accesskey'] = 'P';
$string['previous_title'] = 'Użyj skrótu [Alt-P] lub [Cmnd-P] dla tego przycisku';
$string['cancel'] = 'Anuluj';
$string['cancel_accesskey'] = 'A';
$string['cancel_title'] = 'Użyj [Alt-A] or [Cmnd-A] jako klawisz szybkiego dostępu dla tego przycisku';
$string['ok'] = 'OK';
$string['ok_accesskey'] = 'K';
$string['ok_title'] = 'Użyj skrótu [Alt-K] or [Cmnd-K] dla tego przycisku';
$string['yes'] = 'Tak';
$string['no'] = 'Nie';
$string['language_name'] = 'Polski';
$string['dialog_language'] = 'Język';
$string['dialog_language_title'] = 'Wybierz język instalacji';
$string['dialog_language_explanation'] = 'Wybier język używany w procesie instalacji';
$string['language_label'] = 'Język';
$string['language_help'] = '';
$string['dialog_installtype'] = 'Typ instalacji';
$string['dialog_installtype_title'] = 'Wybierz Standartową lub Własną instalację';
$string['dialog_installtype_explanation'] = 'Wybierz procedurę instalacji z poniższej listy';
$string['installtype_label'] = 'Procedura instalacji';
$string['installtype_help'] = 'Wybierz odpowiednią procedurę instalacji.<br><strong>Standart</strong> bezpośrednia instalacja z minimalnym udziałem użytkownika,<br><strong>Własna</strong> daje pełny wybór wszystkich opcji.';
$string['installtype_option_standard'] = 'Standart';
$string['installtype_option_custom'] = 'Własna';
$string['high_visibility_label'] = 'Pełna widzialność';
$string['high_visibility_help'] = 'Zaznacz pole, żeby ograniczyć interfejs użytkownika tylko do tekstu';
$string['dialog_license'] = 'Licencja';
$string['dialog_license_title'] = 'Przeczytaj i zaakceptuj licencje tego programu';
$string['dialog_license_explanation'] = 'Ten program zostanie tobie oddany w licencji jeśli przeczytasz, zrozumiesz i zgodzisz się na pomiższe warunki. Zauważ, że tylko wersja angielska licencji jest obowiązująca, niezależnie od języka w jakim zainstalowany jest program.';
$string['dialog_license_i_agree'] = 'Zgadzam się';
$string['dialog_license_you_must_accept'] = 'Musisz zaakceptować licencję  &quot;<b>{IAGREE}</b>&quot; (bez cudzysłowa) w poniższym polu..';
$string['dialog_database'] = 'Baza danych';
$string['dialog_database_title'] = 'Podaj informację a serwerze bany danych';
$string['dialog_database_explanation'] = 'Podaj właściwości serwera bazy danych w poniższych polach.';
$string['db_type_label'] = 'Typ';
$string['db_type_help'] = 'Wybierz jędną z dostępnych baz danych';
$string['db_type_option_mysql'] = 'MySQL';
$string['db_server_label'] = 'Serwer';
$string['db_server_help'] = 'To jest adres serwera bazy danych, zwykle <strong>localhost</strong>. Inny przykład: <strong>mysql.bazadanych.org</strong> lub <strong>bazadanych.serwer.operator.net:3306</strong>.';
$string['db_username_label'] = 'Login użytkownika';
$string['db_username_help'] = 'Żeby połączyć się z bazą danych wymagany jest rejestrowany login użytkownika i hasło. Nie używaj katalogu głównego serwera bazy danych  lecz podkatalogu n.p.  <strong>To_ja</strong> lub <strong>dane</strong>.';
$string['db_password_label'] = 'Hasło';
$string['db_password_help'] = 'Żeby połączyć się z bazą danych wymagany jest rejestrowany login użytkownika i hasło.';
$string['db_name_label'] = 'Nazwa bazy danych';
$string['db_name_help'] = 'To jest nazwa istniejącej bazy danych. (Program instalacyjny nie może ze względów bezpieczeństwa utworzyć bazy danych) przykład: <strong>www</strong> or <strong>przyklad_www</strong>.';
$string['db_prefix_label'] = 'Przedrostek';
$string['db_prefix_help'] = 'Nazwy wszystkich tebel w bazie danych muszą zaczynac się przedrostkiem. To umożliwia wielokrotne instalacje w tej samen bazie danych. Przedrostek musi zaczynaś sie literą. Przykład: <strong>was_</strong> or <strong>cms2_</strong>.';
$string['dialog_cms'] = 'Witryna';
$string['dialog_cms_title'] = 'Podaj ważne informacje o witrynie';
$string['dialog_cms_explanation'] = 'Wypełnij ważne informacje o witrynie w poniższych polach';
$string['cms_title_label'] = 'Tytuł witryny';
$string['cms_title_help'] = 'Nazwa witryny.';
$string['cms_website_from_address_label'] = 'Od: adres e-mail';
$string['cms_website_from_address_help'] = 'Ten adres e-mail używany jest dla poczty wysyłanej n.p. alarmy lub przypomnienia haseł';
$string['cms_website_replyto_address_label'] = 'Adres zwrotny: adres e-mail';
$string['cms_website_replyto_address_help'] = 'Ten adres e-mail jest dodany do wychodzącej poczty i podaje skrytkę gdzie odpowiedzi bedą naprawde czytane (przez ciebie) a nie usuwane (przez program serwera).';
$string['cms_dir_label'] = 'Katalog witryny';
$string['cms_dir_help'] = 'To jest ścieżka do katalogu, w kórym się znajduje index.php i confih.php, n.p. <strong>/home/httpd/htdocs</strong> or <strong>C:\Program Files\Apache Group\Apache\htdocs</strong>.';
$string['cms_www_label'] = 'URL witryny';
$string['cms_www_help'] = 'To jest główny URL do twojej witryny tzn. miejsca, gdzie można znakeźć index.php. Na przykład: <strong>http://www.example.org</strong> or <strong>https://example.org:443/szkoła</strong>.';
$string['cms_progdir_label'] = 'Katalog programu';
$string['cms_progdir_help'] = 'To jest ścieżka do katalogu, w którym sie znajdują pliki programu  Website@School (zwykle podkatalog <strong>program</strong> katalogu witryny). Na przykład: <strong>/home/httpd/htdocs/program</strong> or <strong>C:\Program Files\Apache Group\Apache\htdocs\program</strong>.';
$string['cms_progwww_label'] = 'URL programu';
$string['cms_progwww_help'] = 'URL do katalogu z programem (zwykle jest to URL witryny, po którym następuje <strong>/program</strong>). Na przykład: <strong>http://www.example.org/program</strong> lub <strong>https://example.org:443/schoolsite/program</strong>.';
$string['cms_datadir_label'] = 'Katalog danych';
$string['cms_datadir_help'] = 'W tym katalogu znajdują się nagrane pliki i inne pliki danycBardzo ważne jet aby ten katalog znajdował się poza głównym katalogiem dokumentów, tzn , żeby nie był vespośrednio destępny z przeglądarki. Serwer witryny musi mieć  odpowiednie uprawnienia do czytania, tworzenia, i zapisywania plków tutaj. Na przykład:  <strong>/home/httpd/wasdata</strong> lub <strong>C:\Program Files\Apache Group\Apache\wasdata</strong>.';
$string['cms_demodata_label'] = 'Rozpowrzechnij bazę danych';
$string['cms_demodata_help'] = 'Zaznacz w polu jeśli chcesz zacząć nową witrynę używając  danych z demonstracji';
$string['cms_demodata_password_label'] = 'Hasło demonstracji';
$string['cms_demodata_password_help'] = 'Takie same hasło będzie udostępnione  <em>wszystkim</em> użytkownikom demonstracji. Wybierz dobre hasło: przynajmniej 8 znaków z dużymi i małymi literami oraz cyframi. Zostaw pole puste jeśli nie zaznaczyłeś pola \' Rozpowrzechnij bazę danych\' powyżej.';
$string['dialog_user'] = 'Konto użytkownika';
$string['dialog_user_title'] = 'Utwórz pierwsze konto';
$string['dialog_user_explanation'] = 'Wprowadź dane pierwszego konta użytkownika dla tej witryny. Zauważ, że to konto ma wszystkie możliwe uprawnienia adminstratora tak, że każdy użykownik z dostępem do tego konta może zrobić wszystko,';
$string['user_full_name_label'] = 'Pełne nazwisko';
$string['user_full_name_help'] = 'Podaj swoje nazwisko lub inne jeśli wolisz n.o. <strong>Wilhelmina Bladergroen</strong> lub <strong>Master Web</strong>.';
$string['user_username_label'] = 'Login użytkownika';
$string['user_username_help'] = 'Podaj swój login dla tego konta. Musisz podawac to imię przy każdym zalogowamiu. Examples: <strong>wblade</strong> or <strong>webmaster</strong>.';
$string['user_password_label'] = 'Hasło';
$string['user_password_help'] = 'Wybierz dobre hasło: przynajmniej 8 znaków,małe i duże litery, cyfry i znaki specjalne takie jak  % (procent), = (znak równości),  / (ukośnik) i . (kropka). Nie dziel się hasłem z nikim, Utwórz kolegom raczej dodatkowe konta..';
$string['user_email_label'] = 'Adres e-mail';
$string['user_email_help'] = 'Podaj swój adres e-mail. Ten adres będzie ci potrzebny gdy będziesz chciał uzyskać nowe hasło. Nie dziel się z nikim tym adresem. Prztkłady:: <strong>wilhelmina.bladergroen@example.org</strong> lub <strong>webmaster@example.org</strong>.';
$string['dialog_compatibility'] = 'Kompatybilność';
$string['dialog_compatibility_title'] = 'Sprawdź kompatybilność';
$string['dialog_compatibility_explanation'] = 'Poniżej znajduje sie wykaz wymaganych i porządanych ustawień. Upewnij sie, że spełnione są nezbędne wymagania zanim pójdziesz dalej.';
$string['compatibility_label'] = 'Test';
$string['compatibility_value'] = 'Wartość';
$string['compatibility_result'] = 'Rezultat';
$string['compatibility_ok'] = 'OK';
$string['compatibility_warning'] = 'Uwaga';
$string['compatibility_websiteatschool_version_label'] = 'Website@School';
$string['compatibility_websiteatschool_version_check'] = '(kontrola)';
$string['compatibility_websiteatschool_version_value'] = 'wersja {RELEASE} ({VERSION}) {RELEASE_DATE}';
$string['compatibility_websiteatschool_version_check_title'] = 'Sprawdź czy są nowe wersje  Website@School';
$string['compatibility_phpversion_label'] = ' wersja PHP';
$string['compatibility_phpversion_obsolete'] = 'wersja PHP jest przedawniona';
$string['compatibility_phpversion_too_old'] = 'Wersja PHP jest przestarzała: minimalne wymagana wersja {MIN_VERSION}';
$string['compatibility_php_safemode_label'] = 'PHP Tryb bezpieczny';
$string['compatibility_php_safemode_warning'] = 'Tryb bezpieczny jest włączony. Wyłącz w php.ini';
$string['compatibility_webserver_label'] = 'Serwer www';
$string['compatibility_autostart_session_label'] = 'Automatyczny start sesji';
$string['compatibility_autostart_session_fail'] = 'Automatyczny start sesji jest włączony. Wyłącz go w php.ini';
$string['compatibility_file_uploads_label'] = 'Nagrywanie plików';
$string['compatibility_file_uploads_fail'] = 'Nagrywanie plików jest wyłączone. Włącz w php.ini';
$string['compatibility_database_label'] = 'Serwer bazy danych';
$string['compatibility_clamscan_label'] = 'Clamscan anty-virus';
$string['compatibility_clamscan_not_available'] = '(neidostępne)';
$string['compatibility_gd_support_label'] = 'Obsługa GD';
$string['compatibility_gd_support_none'] = 'GD nie jest obsługiwany';
$string['compatibility_gd_support_gif_readonly'] = 'TylkoDoOdczytu';
$string['compatibility_gd_support_details'] = '{VERSION} (GIF: {GIF}, JPG: {JPG}, PNG: {PNG})';
$string['dialog_confirm'] = 'Potwierdzenie';
$string['dialog_confirm_title'] = 'Potwierdź ustawienia';
$string['dialog_confirm_explanation'] = 'Instalujesz nową witrynę. Sprawdź uważnie poniższą konfigurację i wciśnij [Następny], żeby rozpocząć proces instalacji, który może potrwać chwilkę.';
$string['dialog_confirm_printme'] = 'Wskazówka: Wydrukuj tę stronę i zachowaj kopię. ';
$string['dialog_cancelled'] = 'Anulowano';
$string['dialog_cancelled_title'] = '';
$string['dialog_cancelled_explanation'] = 'Instalacja Website@School została anulowana. Wciśnij poniższy przycisk i ponów próbe lub wciśnij przycisk pomocy i przeczytaj podręcznik.';
$string['dialog_finish'] = 'Koniec';
$string['dialog_finish_title'] = 'Zakończ instalację';
$string['dialog_finish_explanation_0'] = 'Instalacja Website@School {VERSION} dobiega już końca.<p>Pozostały jeszcze  wie rzeczy:<ol><li>Musisz {AHREF}nagrać{A} plik config.php, i<li>Musisz umeścić plik config.php w <tt><strong>{CMS_DIR}</strong></tt>.</ol>Po uieszczeniu config.php możesz zamknąć program instalacyjny wciskając [OK] poniżejOnce config.php is in place, you can close the installer by pressing the [OK] button below.';
$string['dialog_finish_explanation_1'] = 'Instalacja Website@School {VERSION} jest zakończona.<p>Zamkinji program instalacyjny wciskając [OK] .';
$string['dialog_finish_check_for_updates'] = 'Po zakończeniu instalacji możesz sprawdzić aktualność wersji (linka otwiera się w nowym oknie)';
$string['dialog_finish_check_for_updates_anchor'] = 'Sprawdź aktualność wersji  Website@School.';
$string['dialog_finish_check_for_updates_title'] = 'Sprawdź status twojej wersji Website@School';
$string['jump_label'] = 'Idź do';
$string['jump_help'] = 'Podaj miejsce, do którego chcesz sie udać wciskając [OK]';
$string['dialog_download'] = 'Pobierz config.php';
$string['dialog_download_title'] = 'Ściagnij config.php do swojego komputera';
$string['dialog_unknown'] = 'Nieznany';
$string['error_already_installed'] = 'Błąd: Website@School jest już zainstalowana';
$string['error_wrong_version'] = 'Błąd: błędny numer wersji. Czy pobrałeś nową wersję podczas instalacji?';
$string['error_fatal'] = 'Błąd krytyczny {ERROR}: skontaktuj się z &lt;{EMAIL}&gt; ';
$string['error_php_obsolete'] = 'Błąd: wersja PHP jest przestarzała';
$string['error_php_too_old'] = 'Błąd: ta wersja  PHP ({VERSION}) jest przedawniona użyj minimalnie {MIN_VERSION}';
$string['error_not_dir'] = 'Błąd: {FIELD}: folder nie istnieje: {DIRECTORY}';
$string['warning_switch_to_custom'] = 'Uwaga: przałączenie na program instalacji własnej aby skorygować błędy';
$string['error_not_create_dir'] = 'Błąd: {FIELD}: folderu nie można utworzyć: {DIRECTORY}';
$string['error_db_unsupported'] = 'Błąd: Baza danych {DATABASE} nie jest obecnie obsługiwana';
$string['error_db_cannot_connect'] = 'Błąd: Nie może sie połączyc z serwerem bazy danych';
$string['error_db_cannot_select_db'] = 'Błąd: Nie może otworzyć bazy danych';
$string['error_invalid_db_prefix'] = 'Błąd: {FIELD}: musi się zaczynać od litery i może zawierać tylko litery, cyfry lub podkreslenia';
$string['error_db_prefix_in_use'] = 'Błąd: {FIELD}: już w użytku: {PREFIX}';
$string['error_time_out'] = 'Błąd krytyczny: przekroczony limit czasowy';
$string['error_db_parameter_empty'] = 'Błąd: puste parametry w bazie danych są niedozwolone';
$string['error_db_forbidden_name'] = 'Błąd: {FIELD}: ta nazwa jest niedozwolona: {NAME}';
$string['error_too_short'] = 'Błąd: {FIELD}: tekst za krótki (minimum = {MIN})';
$string['error_too_long'] = 'Błąd: {FIELD}: tekst za długi (maksymum = {MAX})';
$string['error_invalid'] = 'Błąd: {FIELD}: nieważna wartość';
$string['error_bad_password'] = 'Błąd: {FIELD}: wartoś niedozwolona; minimalne wymagania: cyfr: {MIN_DIGIT}, małych liter: {MIN_LOWER}, dużych liter: {MIN_UPPER}';
$string['error_bad_data'] = '{MENU_ITEM}: znaleziono błędy, należy je najpierw usunąć (via  menu)';
$string['error_file_not_found'] = 'Błąd: nie może znaleźć pliku: {FILENAME}';
$string['error_create_table'] = 'Błąd: nie może utworzyć tabeli: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_insert_into_table'] = 'Błąd: nie może wpisac danych do tabeli: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_update_config'] = 'Błąd: nie może zaktualizować konfiguracji: {CONFIG} ({ERRNO}/{ERROR})';
$string['warning_no_manifest'] = 'Uwaga: pusty lub nieistniejący manifest dla {ITEM}';
$string['error_install_demodata'] = 'Błąd: nie może zainstalować danych dla demonstracji';
$string['error_directory_exists'] = 'Błąd: {FIELD}: folder już istnieje: {DIRECTORY}';
$string['error_nameclash'] = 'Błąd: {FIELD}: zmień login użytkownika {USERNAME}; już w użytku przy demonstracji';
$string['warning_mysql_obsolete'] = 'Uwaga: wersja \'{VERSION}\' bazy danych MySQL jest przestarzała i nie obsługuje UTF-8. Zaktualizuj MySQL';
?>