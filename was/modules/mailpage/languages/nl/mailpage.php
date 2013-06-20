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

/** /program/modules/mailpage/languages/nl/mailpage.php - translated messages for module (Dutch)
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_mailpage
 * @version $Id: mailpage.php,v 1.1 2013/06/20 14:41:35 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['title'] = 'E-mail';
$string['description'] = 'Via deze module kunnen bezoekers e-mail-berichten versturen';
$string['translatetool_title'] = 'E-mail';
$string['translatetool_description'] = 'Dit bestand bevat de vertalingen bij de E-mail-module';

$string['mailpage_content_header'] = 'Configuratie E-mail-module';
$string['mailpage_content_explanation'] = 'Hier kunt u de E-mail-module configureren.
U kunt een optionele titel en een optionele introductietekst toevoegen aan de E-mail-pagina.
De velden \'Naam\', \'E-mail-adres\', \'Beschrijving\', \'Bedank-tekst\' and \'Sorteervolgorde\'
beschrijven een bestemming voor een bericht van een bezoeker. U MOET minimaal één bestemming
opgeven. Het is mogelijk om meer bestemmingen toe te voegen door de gegevens in te vullen in de
lege velden onderaan. U kunt de sorteervolgorde aanpassen door de getallen \'Sorteervolgorde\'
aan te passen. Een bestemming kan verwijderd worden door het veld \'Naam\' leeg te laten en
de configuratie op te slaan. Tot slot is het mogelijk om alvast een initieel bericht in te
vullen onder \'Standaard-bericht\'. De bezoeker zal deze tekst vooringevuld zien in het
bericht-veld. Dit is een eenvoudig manier om een bezoeker een aantal vragen tegelijk te
laten beantwoorden in één enkel bericht.';

$string['header_label'] = '~Titel';
$string['header_title'] = 'Titel voor de e-mail-pagina';
$string['introduction_label'] = '~Introductie';
$string['introduction_title'] = 'Introductietekst voor de e-mail-pagina';
$string['name_label'] = 'Naam ~{INDEX}';
$string['name_title'] = 'De naam van deze bestemming';
$string['sort_order_label'] = 'Sorteervolgorde {INDEX}';
$string['sort_order_title'] = 'Bestemmingen worden weergegeven in de volgorde bepaald door dit getal';
$string['email_label'] = 'E-mail-adres {INDEX}';
$string['email_title'] = 'Het e-mail-adres van deze bestemming';
$string['description_label'] = 'Beschrijving {INDEX}';
$string['description_title'] = 'Deze tekst wordt weergegeven als de bezoeker deze bestemming kiest';
$string['thankyou_label'] = 'Bedank-tekst {INDEX}';
$string['thankyou_title'] = 'Deze tekst wordt getoond nadat de bezoeker een bericht heeft verzonden naar deze bestemming';
$string['message_label'] = '~Standaard-bericht';
$string['message_title'] = 'Initiële tekst voor het bericht van de bezoeker';
$string['error_saving_data'] = 'Fout bij opslaan gegevens';

?>