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

/** /program/install/languages/hu/install.php
 *
 * Language: hu (Magyar)
 * Release:  0.90.3 / 2012041700 (2012-04-17)
 *
 * @author Erika Swiderski, Gergely Sipos <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2012 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_hu
 * @version $Id: install.php,v 1.1 2012/04/17 15:20:31 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Telepítés';
$string['translatetool_description'] = 'Ebben a fájlban a telepítőprogram fordításai vannak';
$string['websiteatschool_install'] = 'A Website@School telepítése';
$string['websiteatschool_logo'] = 'Website@School logó';
$string['help_name'] = 'Segítség';
$string['help_description'] = 'Segítség (új ablakban nyílik)';
$string['next'] = 'Következő';
$string['next_accesskey'] = 'K';
$string['next_title'] = 'Használja az [Alt-K] vagy a [Cmnd-K] kombinációt gyorslinkként';
$string['previous'] = 'Előző';
$string['previous_accesskey'] = 'E';
$string['previous_title'] = 'Használja az [Alt-E] vagy a [Cmnd-E] kombinációt gyorslinkként';
$string['cancel'] = 'Törlés';
$string['cancel_accesskey'] = 'T';
$string['cancel_title'] = 'Használja az [Alt-T] vagy a [Cmnd-T] kombinációt gyorslinkként';
$string['ok'] = 'OK';
$string['ok_accesskey'] = 'K';
$string['ok_title'] = 'Használja az [Alt-K] vagy a [Cmnd-K] kombinációt gyorslinkként';
$string['yes'] = 'Igen';
$string['no'] = 'Nem';
$string['language_name'] = 'Magyar';
$string['dialog_language'] = 'Nyelv';
$string['dialog_language_title'] = 'Válassza ki a telepítés nyelvét';
$string['dialog_language_explanation'] = 'Válassza ki melyik nyelvet szeretné használni telepítés közben';
$string['language_label'] = 'Nyelv';
$string['language_help'] = '';
$string['dialog_installtype'] = 'A telepítés típusa';
$string['dialog_installtype_title'] = 'Válasszon sztenderd és a haladó telepítés közül';
$string['dialog_installtype_explanation'] = 'Válassza ki a telepítés jellegét';
$string['installtype_label'] = 'Telepítés jellege';
$string['installtype_help'] = 'Válasszon sztenderd és a haladó telepítés közül.<br/><strong>A sztenderd</strong> egyszerű telepítést jelent, kevés kérdessel,<br/><strong>a haladó</strong> esetén a telepítés részletei szabályozhatók.';
$string['installtype_option_standard'] = 'Sztenderd';
$string['installtype_option_custom'] = 'Haladó';
$string['high_visibility_label'] = 'Nagy láthatóság';
$string['high_visibility_help'] = 'Jelölje be a négyzetet, ha a text-only felületet szeretné használni telepítéskor.';
$string['dialog_license'] = 'Felhasználási szabályok';
$string['dialog_license_title'] = 'Olvassa el és fogadja el a szoftver felhasználási szabályait';
$string['dialog_license_explanation'] = 'Ez a szoftver csak akkor használható, ha elolvassa a felhasználási feltételeket és egyet ért azokkal. Vegye figyelembe, hogy a feltételek angol nyelvű változata az érvényes.';
$string['dialog_license_i_agree'] = 'Elfogadom a feltételeket';
$string['dialog_license_you_must_accept'] = 'A feltételek elfogadásához ezt kell begépelnie a fenti helyre <b>{IAGREE}</b>';
$string['dialog_database'] = 'Adatbázis';
$string['dialog_database_title'] = 'Adja meg az adatbázis-szerverrel kapcsolatos információkat';
$string['dialog_database_explanation'] = 'Adja meg az adatbázis-szerver tulajdonságait';
$string['db_type_label'] = 'Típus';
$string['db_type_help'] = 'Válassza ki az egyik adatbázis-típust.';
$string['db_type_option_mysql'] = 'MySQL';
$string['db_server_label'] = 'Szerver';
$string['db_server_help'] = 'Ez az adatbázis-szerver címe, általában <strong>localhost</strong>. Más példák: <strong>mysql.pelda.org</strong> vagz <strong>pelda.dbserver.szolgaltato.net:3306</strong>.';
$string['db_username_label'] = 'Felhasználónév';
$string['db_username_help'] = 'Az adatbázis-szerver eléréséhez egy megfelelő felhasználónév és jelszó kombinációjára van szükség. Ne használja a gyökérfiókot, hanem helyette egy kevésbé kiemeltet, pl. <strong>wasfelhasználó</strong> vagy <strong>példa_wwwa</strong>.';
$string['db_password_label'] = 'Jelszó';
$string['db_password_help'] = 'Az adatbázis-szerver eléréséhez egy megfelelő felhasználónév és jelszó kombinációjára van szükség.';
$string['db_name_label'] = 'Adatbázis-név';
$string['db_name_help'] = 'Ez a használni kívánt adatbázis neve. Egy korábban létrehozott adatbázisra van szükség, ugyanis ez a telepítőprogram nem alkalmas adatbázis létrehozására biztonsági okoból. Például: <strong>www</strong> vagy <strong>pelda_www</strong>.';
$string['db_prefix_label'] = 'Előtag';
$string['db_prefix_help'] = 'Az adatbázis minden táblázatneve ezzel az előtaggal kezdődik. Így tartozhat több fordítás egyetlen adatbázishoz. Minden előtag egy betűvel kell, hogy kezdődjön. Példák: <strong>was_</strong> ou <strong>cms2_</strong>.';
$string['dialog_cms'] = 'Honlap';
$string['dialog_cms_title'] = 'Adja meg a honlappal kapcsolatos információkat';
$string['dialog_cms_explanation'] = 'Adja meg az adatbázis-szerverrel kapcsolatos információkat az alábbi helyen.';
$string['cms_title_label'] = 'A honlap címe';
$string['cms_title_help'] = 'A honlap neve';
$string['cms_website_from_address_label'] = 'Küldési cím';
$string['cms_website_from_address_help'] = 'Ez az e-mail cím használatos a kimenő levelekhez, pl. figyelmeztetések és emlékeztetők';
$string['cms_website_replyto_address_label'] = 'Válaszcím';
$string['cms_website_replyto_address_help'] = 'Ez az e-mailcím jelenik meg a kimenő leveleknél, ez az a fiók, amit rendszeresen olvas személyesen.';
$string['cms_dir_label'] = 'Honlapkönyvtár';
$string['cms_dir_help'] = 'Ez az elérési útvonal az index.php és az config.php könyvtáraihoz, pl. <strong>/home/httpd/htdocs</strong> vagy <strong>C:\Program Files\Apache Group\Apache\htdocs</strong>.';
$string['cms_www_label'] = 'A honlap URL-je';
$string['cms_www_help'] = 'Ez a honlap fő URL-je, ahol az index.php is elérhető. Példák: <strong>http://www.pelda.org</strong> vagy <strong>https://pelda.org:443/iskolaioldal</strong>.';
$string['cms_progdir_label'] = 'Programkönyvtár';
$string['cms_progdir_help'] = 'Ez a Website@School programfájlokat tartalmazó könyvtár elérési útja (általában a <strong>program</strong>  alkönyvtára a honlap-könyvtárnak).Példák: <strong>/home/httpd/htdocs/program</strong> vagy <strong>C:\Program Files\Apache Group\Apache\htdocs\program</strong>.';
$string['cms_progwww_label'] = 'Program URL';
$string['cms_progwww_help'] = 'Ez az URL a programkönyvtárhoz tartozik (általában a weblap URL-je után <strong>/program</strong>). Példák : <strong>http://www.pelda.org/program</strong> vagy <strong>https://pelda.org:443/iskolaioldal/program</strong>.';
$string['cms_datadir_label'] = 'Adatkönyvtár';
$string['cms_datadir_help'] = 'Ez a könyvtár tartalmazza a feltöltött fájlokat és egyéb adatfájlokat. Fontos,hogy ne legyen elérhető a böngészőből, hanem a dokumentum-gyökéren kívül helyezkedjen el. Például: <strong>/home/httpd/wasdata</strong> vagy <strong>C:\Program Files\Apache Group\Apache\wasdata</strong>.';
$string['cms_demodata_label'] = 'Adatbázis feltöltése';
$string['cms_demodata_help'] = 'Jelölje be a négyzetet, ha el akar indítani új weblapját demoadatok használatával.';
$string['cms_demodata_password_label'] = 'Demo jelszó';
$string['cms_demodata_password_help'] = 'Ugyanaz a jelszó tartozik <em>minden</em> felhasználói fiókhoz a demoadatoknál. Válasszon egy komplex jelszót, legalább 8 karakterrel, számokkal, kis-és nagybetűkkel. Üresen hagyhatja ezt a részt, ha nem választotta ki az Adatbázis feltöltése lehetőségét.';
$string['dialog_user'] = 'Felhasználói fiók';
$string['dialog_user_title'] = 'Az első fiók létrehozása';
$string['dialog_user_explanation'] = 'Adja meg az első felhasználói fiókot a weblaphoz. Ez a fiók teljes adminisztrációs jogkörrel rendelkezik majd.';
$string['user_full_name_label'] = 'Teljes név';
$string['user_full_name_help'] = 'Adja meg a saját nevét, vagy egy más kitalált nevet, például <strong>Eötvös József</strong> vagy <strong>Webmester</strong>.';
$string['user_username_label'] = 'Felhasználónév';
$string['user_username_help'] = 'Adja meg a felhasználónevét. Például: <strong>ejozsef</strong> vagy <strong>webmester</strong>.';
$string['user_password_label'] = 'Jelszó';
$string['user_password_help'] = 'Válasszon egy komplex jelszót: legyen minimum 8 karakter, kis- és nagybetű is legyen benne, számok és speciális karakterek (#, %, &). Ne adja meg a jelszót másoknak.';
$string['user_email_label'] = 'E-mail cím';
$string['user_email_help'] = 'Adja meg e-mail címét. Szüksége lesz rá minden új jelszó igénylesekor. Ne hasznaljon senkivel együtt közös címet. Például: <strong>eotvos.jozsef@pelda.org</strong> ou <strong>webmester@exemple.org</strong>';
$string['dialog_compatibility'] = 'Kompatibilitás';
$string['dialog_compatibility_title'] = 'Kompatibilitás ellenőrzése';
$string['dialog_compatibility_explanation'] = 'Alább találhatók az elvárt és ajánlott beállítások. Mielőtt folytatja, kérem győzödjön meg róla, teljesíti a feltételeket.';
$string['compatibility_label'] = 'Teszt';
$string['compatibility_value'] = 'Érték';
$string['compatibility_result'] = 'Eredmény';
$string['compatibility_ok'] = 'OK';
$string['compatibility_warning'] = 'FIGYELMEZTETÉS';
$string['compatibility_websiteatschool_version_label'] = 'Website@School';
$string['compatibility_websiteatschool_version_check'] = '(ellenőrzés)';
$string['compatibility_websiteatschool_version_value'] = 'verzió {RELEASE} ({VERSION}) {RELEASE_DATE}';
$string['compatibility_websiteatschool_version_check_title'] = 'A Website@School későbbi verzióinak ellenőrzése';
$string['compatibility_phpversion_label'] = 'PHP verzió';
$string['compatibility_phpversion_obsolete'] = 'Elavult PHP verzió';
$string['compatibility_phpversion_too_old'] = 'A PHP verziója túl régi. Minimális verziószám: {MIN_VERSION}';
$string['compatibility_php_safemode_label'] = 'PHP biztonsági mód';
$string['compatibility_php_safemode_warning'] = 'Biztonsági mód be van kapcsolva. Kérem kapcsolja ki a php.ini fájlban';
$string['compatibility_webserver_label'] = 'Web szerver';
$string['compatibility_autostart_session_label'] = 'Automatikus munkamenet indítás';
$string['compatibility_autostart_session_fail'] = 'Automatikus munkamenet indítás be van kapcsolva. Kérem kapcsolja ki a php.ini fájlban';
$string['compatibility_file_uploads_label'] = 'Fájl feltöltések';
$string['compatibility_file_uploads_fail'] = 'Fájl feltöltések ki vannak kapcsolva. Kérem kapcsolja be a php.ini fájlban';
$string['compatibility_database_label'] = 'Adatbázis szerver';
$string['compatibility_clamscan_label'] = 'Clamscan anti-vírus';
$string['compatibility_clamscan_not_available'] = '(nem elérhető)';
$string['compatibility_gd_support_label'] = 'GD támogatás';
$string['compatibility_gd_support_none'] = 'Nem támogatott GD';
$string['compatibility_gd_support_gif_readonly'] = 'Csak olvasható';
$string['compatibility_gd_support_details'] = '{VERSION} (GIF: {GIF}, JPG: {JPG}, PNG: {PNG})';
$string['dialog_confirm'] = 'Megerősítés';
$string['dialog_confirm_title'] = 'A beállítások megerősítése';
$string['dialog_confirm_explanation'] = 'Új weblap telepítésére készül. Kérem körültekintően ellenőrizze az alább látható beállításokat és amennyiben helyesek a [Következő] gombra kattintva indtsa el az telepítést. Egy ideig eltart a folyamat.';
$string['dialog_confirm_printme'] = 'Tipp: nyomtassa ki ezt az oldalt, hogy később referenciaként bármikor rendelkezésre álljon.';
$string['dialog_cancelled'] = 'Törölve';
$string['dialog_cancelled_title'] = '';
$string['dialog_cancelled_explanation'] = 'A Website@School telepítése megszakításra került. Nyomja meg a lenti gombot a telepítés újbóli elindítéséhoz, vagy a Súgó gombot a dokumentáció megtekintéséhez.';
$string['dialog_finish'] = 'Befejezés';
$string['dialog_finish_title'] = 'A telepítés befejezése';
$string['dialog_finish_explanation_0'] = 'A Website@School {VERSION} telepítése majdnem kész. <p> Két dolog maradt hátra: <ol><li>{AHREF}Le kell töltenie{A} a config.php fájlt és <li>be kell másolnia azt a <tt><strong>{CMS_DIR}</strong></tt> könyvtárba. </ol>Miután a config.php a megfelelő helyre került bezárhatja a telepítőt a lenti [OK] gombbal.';
$string['dialog_finish_explanation_1'] = 'A Website@School {VERSION} telepítése befejeződött. <p>A lenti [OK] gombbal bezárhatja a telepítőt.';
$string['dialog_finish_check_for_updates'] = 'Ha kívánja, az alábbi linket követve frissítéseket kereshet (a link új ablakban kerül megnyitásra).';
$string['dialog_finish_check_for_updates_anchor'] = 'Nézze meg a Website@School fissítéseit';
$string['dialog_finish_check_for_updates_title'] = 'A Website@School verziójának megtekintése';
$string['jump_label'] = 'Ugorjon ide';
$string['jump_help'] = 'Adja meg, hogy hova akar ugrani az [OK] gomb megnyomása után.';
$string['dialog_download'] = 'config.php fájl letöltése';
$string['dialog_download_title'] = 'config.php fájl letöltése a számítógépre';
$string['dialog_unknown'] = 'Ismeretlen';
$string['error_already_installed'] = 'Hiba: A Website@School már telepítve van';
$string['error_wrong_version'] = 'Hiba : rossz verziószám, Letöltötte az új verziót_';
$string['error_fatal'] = 'Végzetes hiba {ERROR} : kérem kérjen segítséget a <{EMAIL}> email címen';
$string['error_php_obsolete'] = 'Hiba: a PHP verziója túl régi';
$string['error_php_too_old'] = 'Hiba: a PHP ({VERSION}) túl régi: használjon legalább {MIN_VERSION} verziót';
$string['error_not_dir'] = 'Hiba: {FIELD}: könyvtár nem létezik: {DIRECTORY}';
$string['warning_switch_to_custom'] = 'Figyelmeztetés: Egyéni telepítési módra kapcsolva, hogy az esetleges hibák javíthatók legyenek';
$string['error_not_create_dir'] = 'Hiba: {FIELD}: A könyvtár nem hozható létre: {DIRECTORY}';
$string['error_db_unsupported'] = 'Hiba: az adatbázis {DATABASE} jelenleg nem támogatott';
$string['error_db_cannot_connect'] = 'Hiba: nem lehet az adatbázis szerverrel kapcsolatot létesíteni';
$string['error_db_cannot_select_db'] = 'Hiba: nem lehet az adatbázis szervert nem lehet megnyitni';
$string['error_invalid_db_prefix'] = 'Hiba: {FIELD}: betűvel kell kezdődnie, csak betűket, számokat és aláhúzás karaktereket tartalmazhat';
$string['error_db_prefix_in_use'] = 'Hiba: {FIELD}: már használatban: {PREFIX}';
$string['error_time_out'] = 'Végzetes hiba: időtullépés';
$string['error_db_parameter_empty'] = 'Hiba: üres adatbázis paraméterek nem megengedettek';
$string['error_db_forbidden_name'] = 'Hiba: {FIELD}: a név nem megengedett: {NAME}';
$string['error_too_short'] = 'Hiba: {FIELD}: túl rövid szöveg (mimimum = {MIN})';
$string['error_too_long'] = 'Hiba:  {FIELD} : túl hosszú szöveg (maximum = {MAX})';
$string['error_invalid'] = 'Hiba: {FIELD} : nem megfelelő érték';
$string['error_bad_password'] = 'Hiba: {FIELD}: rossz érték; minimális követelmények: hossz: {MIN_DIGIT}, kisbetűs: {MIN_LOWER}, nagybetűs: {MIN_UPPER}';
$string['error_bad_data'] = '{MENU_ITEM}: a rendszer hibákat észlelt, kérem először ezeket javítsa ki (a menün keresztül)';
$string['error_file_not_found'] = 'Hiba: Nem található a fájl: {FILENAME}';
$string['error_create_table'] = 'Hiba: táblát nem sikerült létrehozni: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_insert_into_table'] = 'Hiba: nem sikerült adatot szúrni a táblába: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_update_config'] = 'Hiba: konfiguráció nem frissíthető: {CONFIG} ({ERRNO}/{ERROR})';
$string['warning_no_manifest'] = 'Figyelmeztetés: üres, vagy hiányzó manifest fájl a {ITEM} elemhez';
$string['error_install_demodata'] = 'Hiba: nem lehet a demonstrációs adatot telepíteni';
$string['error_directory_exists'] = 'Hiba: {FIELD}: a könyvtár már létezik: {DIRECTORY}';
$string['error_nameclash'] = 'Hiba: {FIELD}: kérem vátoztassa meg a felhasználói nevet {USERNAME}, az ugyanis demonstrációs célokra már fenntartott';
$string['warning_mysql_obsolete'] = 'Figyelmeztetés: a \'{VERSION}\' MySQL verzió már elavult és nem támogatja az UTF-8 karakterkódolást. Kérem frissítse a MySQL-t';
?>