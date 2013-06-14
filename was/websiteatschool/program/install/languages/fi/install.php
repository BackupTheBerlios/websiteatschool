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

/** /program/install/languages/fi/install.php
 *
 * Language: fi (Suomi)
 * Release:  0.90.4 / 2013061400 (2013-06-14)
 *
 * @author Laura Råman <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_fi
 * @version $Id: install.php,v 1.1 2013/06/14 20:00:30 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Asenna';
$string['translatetool_description'] = 'Tämä tiedosto sisältää asennuksen käännökset';
$string['websiteatschool_install'] = 'Website@School Asenna';
$string['websiteatschool_logo'] = 'Website@School logo';
$string['help_name'] = 'Apua';
$string['help_description'] = 'Apua (avautuu uuteen ikkunaan)';
$string['next'] = 'Seuraava';
$string['next_accesskey'] = 'S';
$string['next_title'] = 'Käytä [Alt-S] tai [Cmnd-S] pikanäppäimenä';
$string['previous'] = 'Edellinen';
$string['previous_accesskey'] = 'E';
$string['previous_title'] = 'Käytä [Alt-E] tai [Cmnd-E] pikanäppäimenä';
$string['cancel'] = 'Peruuta';
$string['cancel_accesskey'] = 'P';
$string['cancel_title'] = 'Käytä [Alt-P] tai [Cmnd-P] pikanäppäimenä';
$string['ok'] = 'OK';
$string['ok_accesskey'] = 'K';
$string['ok_title'] = 'Käytä [Alt-K] tai [Cmnd-K] pikanäppäimenä';
$string['yes'] = 'Kyllä';
$string['no'] = 'Ei';
$string['language_name'] = 'Suomi';
$string['dialog_language'] = 'Kieli';
$string['dialog_language_title'] = 'Valitse asennuskieli';
$string['dialog_language_explanation'] = 'Ole hyvä ja valitse käytettävä kieli asennuksen ajaksi.';
$string['language_label'] = 'Kieli';
$string['language_help'] = '';
$string['dialog_installtype'] = 'Asennustyyppi';
$string['dialog_installtype_title'] = 'Valitse Standardi tai Mukautettu asennus';
$string['dialog_installtype_explanation'] = 'Ole hyvä ja valitse asennustapa alla olevasta listasta';
$string['installtype_label'] = 'Asennustapa';
$string['installtype_help'] = 'Ole hyvä ja valitse sopiva asennustapa.<br><strong>Standardi</strong> tarkoittaa suoraviivaista asennusta ilman turhia lisäkysymyksiä,<br><strong>Mukautettu</strong> antaa mahdollisuuden hallita asennusvaihtoehtoja.';
$string['installtype_option_standard'] = 'Standardi';
$string['installtype_option_custom'] = 'Mukautettu';
$string['high_visibility_label'] = 'Korkea näkyvyys';
$string['high_visibility_help'] = 'Laita rasti ruutuun käyttääksesi tekstiversiota asennuksen aikana';
$string['dialog_license'] = 'Lisenssi';
$string['dialog_license_title'] = 'Lue ja hyväksy tämän ohjelman lisenssi.';
$string['dialog_license_explanation'] = 'Tämän ohjelman lisenssin saa ainoastaan jos lukkee, ymmärtää ja hyväksyy seuraavat ehdot. Huomaa, että englanninkielinen versio lisenssistä pätee vaikka ohjelman asennus tapahtuisi toisella kielellä';
$string['dialog_license_i_agree'] = 'Hyväksyn';
$string['dialog_license_you_must_accept'] = 'Sinun täytyy hyväksyä lisenssin sopimus kirjoittamalla &quot;<b>{IAGREE}</b>&quot; (ilman heittomerkkejä) alla olevaan kenttään.';
$string['dialog_database'] = 'Tietokanta';
$string['dialog_database_title'] = 'Syötä tietoa tietokannan serveristä.';
$string['dialog_database_explanation'] = 'Ole hyvä ja syötä tietokannan serverien ominaisuudet alla oleviin kenttiin.';
$string['db_type_label'] = 'Tyyppi';
$string['db_type_help'] = 'Valitse jokin käytössä olevista tietokantatyypeistä.';
$string['db_type_option_mysql'] = 'MySQL';
$string['db_server_label'] = 'Server';
$string['db_server_help'] = 'Tämä on tietokannan serverin osoite, yleensä <strong>localhost</strong>. Muita esimerkkejä: <strong>mysql.example.org</strong> tai <strong>example.dbserver.provider.net:3306</strong>.';
$string['db_username_label'] = 'Käyttäjänimi';
$string['db_username_help'] = 'Voimassa oleva käyttäjänimi/salasana-yhdistelmä vaaditaan yhteyden saamiseksi tietokannan serveriin. Älä käytä tietokannan serverin juuritason tiliä vaan jotain, jolla on vähemmän etuoikeuksia, esim. <strong>wasuser</strong> tai <strong>example_wwwa</strong>.';
$string['db_password_label'] = 'Salasana';
$string['db_password_help'] = 'Voimassa oleva käyttäjänimi/salasana-yhdistelmä vaaditaan yhteyden saamiseksi tietokannan serveriin.';
$string['db_name_label'] = 'Tietokannan nimi';
$string['db_name_help'] = 'Tämä on tietokannan käytössä oleva nimi. Huomaa, että tietokanta tulee olla jo olemassa oleva; tätä asennusohjelmaa ei ole suunniteltu luomaan tietokantoja (turvallisuussyistä). Esimerkkejä: <strong>www</strong> tai <strong>example_www</strong>.';
$string['db_prefix_label'] = 'Etuliite';
$string['db_prefix_help'] = 'Kaikki taulukoidennimet tietokannassa alkaa tällä etuliitteellä. Tämä sallii useamman asennuksen saman tietokannan sisällä. Huomaa, että etuliitteen tulee alkaa kirjaimella. Esimerkkejä: <strong>was_</strong> tai <strong>cms2_</strong>.';
$string['dialog_cms'] = 'Sivusto';
$string['dialog_cms_title'] = 'Anna tarvittavat tiedot sivustosta';
$string['dialog_cms_explanation'] = 'Syötä tarvittavat tiedot sivustosta alla oleviin kenttiin';
$string['cms_title_label'] = 'Sivuston otsikko';
$string['cms_title_help'] = 'Sivuston nimi.';
$string['cms_website_from_address_label'] = 'Lähettäjä: osoite';
$string['cms_website_from_address_help'] = 'Tätä sähköpostiosoitetta käytetään lähteviin viesteihin, esim. varoitukset ja salasana muistutukset.';
$string['cms_website_replyto_address_label'] = 'Vastaa: osoite';
$string['cms_website_replyto_address_help'] = 'Tämä sähköpostiosoite liitetään lähteviin viesteihin. Tähän osoitteeseen voi lähettää takaisintulevaa sähköpostia, joka on tarkoitettu (sinun) luettavaksi, eikä (webserverin ohjelman) tuhottavaksi.';
$string['cms_dir_label'] = 'Sivuston hakemisto';
$string['cms_dir_help'] = 'Tämä on hakemiston polku, joka pitää sisällään index.php ja config.php, esim. <strong>/home/httpd/htdocs</strong> tai <strong>C:\Program Files\Apache Group\Apache\htdocs</strong>.';
$string['cms_www_label'] = 'Sivuston URL';
$string['cms_www_help'] = 'Tämä on emo URL, joka johtaa sivustollesi eli sinne missä voi vierailla index.php:ssä. Esimerkkejä: <strong>http://www.example.org</strong> tai <strong>https://example.org:443/schoolsite</strong>.';
$string['cms_progdir_label'] = 'Ohjelmisto hakemisto';
$string['cms_progdir_help'] = 'Tämä on hakemiston polku, joka sisältää Website@School ohjelmistotiedostot (useimmiten sivuston hakemiston alahakemisto <strong>program</strong>). Esimerkkejä: <strong>/home/httpd/htdocs/program</strong> tai <strong>C:\Program Files\Apache Group\Apache\htdocs\program</strong>.';
$string['cms_progwww_label'] = 'Ohjelmiston URL';
$string['cms_progwww_help'] = 'Tämä URL johtaa ohjelman hakemistoon (yleensä sivuston URL, jota seuraa <strong>/program</strong>). Esimerkkejä: <strong>http://www.example.org/program</strong> tai <strong>https://example.org:443/schoolsite/program</strong>.';
$string['cms_datadir_label'] = 'Data hakemisto';
$string['cms_datadir_help'] = 'Tämä hakemisto sisältää lähetetyt tiedostot ja muita datatiedostoja. On tärkeää, että tämä hakemisto sijoitetaan dokumentin juuren ulkopuolelle, ettei siihen saa yhteyttä suoraan selaimella. Huomaa, että verkkopalvelimen täytyy omata riittävästi oikeuksia, jotta tiedostoja voidaan lukea, luoda ja kirjoittaa täällä. Esimerkkejä: <strong>/home/httpd/wasdata</strong> tai <strong>C:\Program Files\Apache Group\Apache\wasdata</strong>.';
$string['cms_demodata_label'] = 'Asuta tietokanta';
$string['cms_demodata_help'] = 'Laita rasti ruutuun jos haluat aloittaa uuden sivuston käytön käyttämällä demo-dataa.';
$string['cms_demodata_password_label'] = 'Demonstration salasana';
$string['cms_demodata_password_help'] = 'Sama demonstration salasana myönnetään <em>kaikille</em> demonstration käyttäjätileille. Ole hyvä ja valitse salasana: valitse vähintään 8 merkkiä sisältävä isoista ja pienistä kirjaimista ja numeroista koostuva salasana. Voit jättää kentän tyhjäksi mikäli et laittanut rastia ruutuun ylläolevassa kohdassa \'asuta tietokanta\'.';
$string['dialog_user'] = 'Käyttäjätili';
$string['dialog_user_title'] = 'Luo ensimmäinen tili';
$string['dialog_user_explanation'] = 'Ole hyvä ja lisää tämän sivuston ensimmäisen käyttäjätilin tiedot. Huomaa, että tämä tili omaa täydet hallintaetuoikeudet ja kaikki mahdoliset luvat, joten kuka vaan kenellä on pääsy tämän tilin tietoihin voi tehdä mitä vaan.';
$string['user_full_name_label'] = 'Koko nimi';
$string['user_full_name_help'] = 'Ole hyvä ja anna oma nimesi tai joku muu (toimiva) nimi, esim. <strong>Lars Stenbäck</strong> tai <strong>Master Webbi</strong>.';
$string['user_username_label'] = 'Käyttäjänimi';
$string['user_username_help'] = 'Ole hyvä ja anna login nimi, jota haluat käyttää tälle tilille. Tämä nimi tulee antaa aina sisäänkirjautuessa. Esimeskkejä: <strong>lstenb</strong> tai <strong>webmaster</strong>.';
$string['user_password_label'] = 'Salasana';
$string['user_password_help'] = 'Valitse hyvä salasana: sisältää vähintään 8 merkkiä, joissa isoja ja pieniä kirjaimia, numeroita ja erikoismerkkejä kuten % (prosentti), = (on yhtä kuin), / (kauttaviiva) ja . (piste). Älä jaa salasanaa kenenkään kanssa, vaan luo omat käyttäjätilit muille käyttäjille.';
$string['user_email_label'] = 'Sähköpostiosoite';
$string['user_email_help'] = 'Ole hyvä ja anna sähköpostiosoitteesi. Tarvitset tätä osoitetta mikäli sinun tulee anoa uusi salasana. Varmista, että vain sinulla on pääsy  tähän sähköpostitiliin (älä käytä jaettua sähköpostia). Esimerkkejä: <strong>lars.stenback@example.org</strong> tai <strong>webmaster@example.org</strong>.';
$string['dialog_compatibility'] = 'Yhteensopivuus';
$string['dialog_compatibility_title'] = 'Tarkista yhteensopivuus';
$string['dialog_compatibility_explanation'] = 'Alla on yleisnäkymä vaadituista ja toivotuista asetuksista. Tulee varmistaa, että vaatimukset tyydytetään ennen kuin voi jatkaa.';
$string['compatibility_label'] = 'Testi';
$string['compatibility_value'] = 'Arvo';
$string['compatibility_result'] = 'Tulos';
$string['compatibility_ok'] = 'OK';
$string['compatibility_warning'] = 'VAROITUS';
$string['compatibility_websiteatschool_version_label'] = 'Website@School';
$string['compatibility_websiteatschool_version_check'] = '(tarkistus)';
$string['compatibility_websiteatschool_version_value'] = 'versio {RELEASE} ({VERSION}) {RELEASE_DATE}';
$string['compatibility_websiteatschool_version_check_title'] = 'Tarkista Website@School viimeisin versio';
$string['compatibility_phpversion_label'] = 'PHP versio';
$string['compatibility_phpversion_obsolete'] = 'PHP version on vanhentunut';
$string['compatibility_phpversion_too_old'] = 'PHP versio on vanhentunut: minimi {MIN_VERSION}';
$string['compatibility_php_safemode_label'] = 'PHP Safe Mode';
$string['compatibility_php_safemode_warning'] = 'Safe Mode on päällä. Ole hyvä ja laita pois päältä php.ini -tilassa.';
$string['compatibility_webserver_label'] = 'Webserver';
$string['compatibility_autostart_session_label'] = 'Automaattinen istunnon aloitus';
$string['compatibility_autostart_session_fail'] = 'Automaattinen istunnon aloitus on päällä. Ole hyvä ja laita pois päältä php.ini -tilassa.';
$string['compatibility_file_uploads_label'] = 'Tiedoston siirto';
$string['compatibility_file_uploads_fail'] = 'Tiedoston siirto on päällä. Ole hyvä ja laita pois päältä php.ini -tilassa';
$string['compatibility_database_label'] = 'Tietokantaserveri';
$string['compatibility_clamscan_label'] = 'Clamscan anti-virus';
$string['compatibility_clamscan_not_available'] = '(ei saatavissa)';
$string['compatibility_gd_support_label'] = 'GD Tuki';
$string['compatibility_gd_support_none'] = 'GD:tä ei tueta';
$string['compatibility_gd_support_gif_readonly'] = 'Readonly';
$string['compatibility_gd_support_details'] = '{VERSION} (GIF: {GIF}, JPG: {JPG}, PNG: {PNG})';
$string['dialog_confirm'] = 'Vahvistus';
$string['dialog_confirm_title'] = 'Vahvista asetukset';
$string['dialog_confirm_explanation'] = 'Asennat juuri uutta sivustoa. Tarkista alla olevat konfiguraatio asetukset ja paina [Seuraava] aloittaaksesi asennusprosessin. Asennus voi kestää jonkin aikaa.';
$string['dialog_confirm_printme'] = 'Vinkki: tulosta sivu ja säilytä kopio tulevaisuuden varalle.';
$string['dialog_cancelled'] = 'Peruutettu';
$string['dialog_cancelled_title'] = '';
$string['dialog_cancelled_explanation'] = 'Website@School asennus on peruttu. Paina alla olevaa painiketta koittaaksesi uudelleen tai paina \'apua\'-painikkeesta lukeaksesi ohjeet.';
$string['dialog_finish'] = 'Lopeta';
$string['dialog_finish_title'] = 'Lopeta asennus';
$string['dialog_finish_explanation_0'] = 'Website@School {VERSION} asennus on melkein valmis.<p>Kaksi asiaa pitää vielä tapahtua:<ol><li>Nyt täytyy ladata {AHREF}download{A} tiedosto config.php, ja <li>tiedosto config.php täytyy siirtää tänne <tt><strong>{CMS_DIR}</strong></tt>.</ol>Kun config.php on paikallaan, voit sulkea asennuksen painamalla [OK] -painiketta.';
$string['dialog_finish_explanation_1'] = 'Website@School {VERSION} asennus on nyt valmis.<p>Voit sulkea asennuksen painamalla [OK] -painikkeesta.';
$string['dialog_finish_check_for_updates'] = 'Halutessasi voit seurata alla olevaa linkkiä tarkistaaksesi päivitysten tilan (linkki avautuu uuteen ikkunaan).';
$string['dialog_finish_check_for_updates_anchor'] = 'Tarkista Website@School päivitykset.';
$string['dialog_finish_check_for_updates_title'] = 'Tarkista Website@School versiosi tila';
$string['jump_label'] = 'Hyppää kohtaan';
$string['jump_help'] = 'Valitse päämäärä mihin haluat mennä kun olet painanut [OK] -painiketta.';
$string['dialog_download'] = 'Lataa config.php';
$string['dialog_download_title'] = 'Lataa config.php tietokoneellesi';
$string['dialog_unknown'] = 'Tuntematon';
$string['error_already_installed'] = 'Virhe: Website@School on jo asennettu';
$string['error_wrong_version'] = 'Virhe: väärä versionumero. Latasitko uuden version asennuksen aikana?';
$string['error_fatal'] = 'Fatal error {ERROR}: ole hyvä ota yhteyttä &lt;{EMAIL}&gt; neuvojen saamiseksi.';
$string['error_php_obsolete'] = 'Virhe: PHP versio on liian vanha';
$string['error_php_too_old'] = 'Virhe: PHP versio ({VERSION}) on liian vanha: käytä vähintään versiota {MIN_VERSION}';
$string['error_not_dir'] = 'Virhe: {FIELD}: seuraavaa hakemistoa ei ole olemassa: {DIRECTORY}';
$string['warning_switch_to_custom'] = 'Varoitus: siirtyy mukautettuun asennukseen, jotta virheet voidaan korjata.';
$string['error_not_create_dir'] = 'Virhe: {FIELD}: seuraavaa hakemistoa ei pystytä luomaan: {DIRECTORY}';
$string['error_db_unsupported'] = 'Virhe: tietokantaa {DATABASE} ei tueta';
$string['error_db_cannot_connect'] = 'Virhe: ei saa yhteyttä tietokannan palvelimen kanssa';
$string['error_db_cannot_select_db'] = 'Virhe: ei voi avata tietokantaa';
$string['error_invalid_db_prefix'] = 'Virhe: {FIELD}: täytyy alkaa kirjaimella, voi sisältää vain kirjaimia, numeroita tai alaviivoja';
$string['error_db_prefix_in_use'] = 'Virhe: {FIELD}: seuraava on jo käytössä: {PREFIX}';
$string['error_time_out'] = 'Fatal error: time-out';
$string['error_db_parameter_empty'] = 'Virhe: tyhjiä tietokannan parametrejä ei hyväksytä';
$string['error_db_forbidden_name'] = 'Virhe: {FIELD}: tätä nimeä ei hyväksytä: {NAME}';
$string['error_too_short'] = 'Virhe: {FIELD}: ketju on liian lyhyt (minimi = {MIN})';
$string['error_too_long'] = 'Virhe: {FIELD}: ketju on lian pitkä (maksimi = {MAX})';
$string['error_invalid'] = 'Virhe: {FIELD}: virheellinen arvo';
$string['error_bad_password'] = 'Virhe: {FIELD}: arvoa ei voida hyväksyä; vähimmäisvaatimukset ovat: numeroita: {MIN_DIGIT}, pieniä kirjaimia: {MIN_LOWER}, isoja kirjaimia: {MIN_UPPER}';
$string['error_bad_data'] = '{MENU_ITEM}: esiintyi virheitä, ole hyvä ja korjaa ne ensin (valikon kautta)';
$string['error_file_not_found'] = 'Virhe: seuraavaa tiedostoa ei löydy: {FILENAME}';
$string['error_create_table'] = 'Virhe: seuraavaa taulukkoa ei voitu luoda: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_insert_into_table'] = 'Virhe: tietoa ei voida syöttää taulukkoon: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_update_config'] = 'Virhe: konfiguraatiota ei pystytä päivittämään: {CONFIG} ({ERRNO}/{ERROR})';
$string['warning_no_manifest'] = 'Varoitus: tyhjä luettelo tai ei luetteloa osiolle {ITEM}';
$string['error_install_demodata'] = 'Virhe: demo dataa ei voida asentaa';
$string['error_directory_exists'] = 'Virhe: {FIELD}: seuraava hakemisto on jo olemassa: {DIRECTORY}';
$string['error_nameclash'] = 'Virhe: {FIELD}: ole hyvä ja muuta nimi {USERNAME}; se on jo käytössä demonstration käyttäjätilinä';
$string['warning_mysql_obsolete'] = 'Varoitus: MySQL versio \'{VERSION}\' on vanhentunut eikä tue UTF-8. Ole hyvä ja päivitä MySQL';
?>