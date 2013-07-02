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
 * @version $Id: mailpage.php,v 1.3 2013/07/02 18:13:04 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['title'] = 'E-mail';
$string['description'] = 'Via deze module kunnen bezoekers e-mail-berichten versturen';
$string['translatetool_title'] = 'E-mail';
$string['translatetool_description'] = 'Dit bestand bevat de vertalingen bij de E-mail-module';
$string['mailpage_content_header'] = 'Configuratie E-mail-module';
$string['mailpage_content_explanation'] = 'Hier kunt u de E-mail-module configureren.
Gebruik onderstaande links om nieuwe bestemmingen
toe te voegen of om bestaande bestemmngen te wijzigen
of te verwijderen. U MOET minimaal één bestemming
configureren voor een goede werking van de module.
<p>U kunt een optionele titel en een optionele
introductietekst toevoegen aan de E-mail-pagina. Bovendien
is het mogelijk om alvast een initieel bericht in te
vullen onder \'Standaard-bericht\'. De bezoeker zal deze
tekst vooringevuld zien in het bericht-veld. Dit is een
eenvoudig manier om een bezoeker een aantal vragen tegelijk te
laten beantwoorden, in één enkel bericht.';
$string['add_new_address_label'] = 'Bestemming toevoegen';
$string['add_new_address_title'] = 'Gebruik deze link om een bestemming toe te voegen aan de lijst.';
$string['edit_address_label'] = '{NAME} ({SORT_ORDER})';
$string['edit_address_title'] = 'Wijzig of verwijder bestemming {ADDRESS_ID}: <{EMAIL}>';
$string['header_label'] = '~Titel';
$string['header_title'] = 'Titel voor de e-mail-pagina';
$string['introduction_label'] = '~Introductie';
$string['introduction_title'] = 'Introductietekst voor de e-mail-pagina';
$string['default_message_label'] = '~Standaard-bericht';
$string['default_message_title'] = 'Initiële tekst voor het bericht van de bezoeker';
$string['mailpage_add_address_header'] = 'Nieuwe bestemming toevoegen';
$string['mailpage_add_address_explanation'] = 'Hier kunt u de details van de nieuwe bestemming invullen. U MOET minimaal één bestemming toevoegen om de module goed te laten werken.';
$string['mailpage_edit_address_header'] = 'Bestemming wijzigen of verwijderen';
$string['mailpage_edit_address_explanation'] = 'Hier kunt u de details van een bestaande bestemming aanpassen of een bestaande bestemming wissen. U MOET minimaal één bestemming configureren om de module goed te laten werken.';
$string['address_name_label'] = '~Naam';
$string['address_name_title'] = 'De naam van deze betemming';
$string['address_email_label'] = '~E-mail-adres';
$string['address_email_title'] = 'Het e-mail-adres van deze bestemming';
$string['address_description_label'] = 'O~mschrijving';
$string['address_description_title'] = 'Deze tekst wordt weergegeven als de bezoeker deze bestemming kiest uit de lijst';
$string['address_thankyou_label'] = '~Bedankt-tekst';
$string['address_thankyou_title'] = 'Deze tekst wordt weergegeven nadat het bericht van de bezoeker is verzonden';
$string['address_sort_order_label'] = '~Sorteervolgorde';
$string['address_sort_order_title'] = 'De volgorde van bestemmingen wordt bepaald door dit getal.';
$string['error_saving_data'] = 'Fout bij opslaan gegevens';
$string['error_deleting_data'] = 'Fout bij het verwijderen van gegevens';
$string['error_retrieving_config'] = 'Fout: kan de configuratiegegevens niet inlezen';
$string['error_retrieving_addresses'] = 'Fout: geen bestemming voor pagina {NODE}';
$string['error_retrieving_data'] = 'Fout: kan de gegevens niet inlezen';
$string['error_token_expired'] = 'Fout: het e-mail-formulier is verlopen, probeer het alstublieft nogmaals het formulier in te vullen';
$string['error_storing_data'] = 'Fout: kon gegevens niet opslaan';
$string['error_too_fast'] = 'Fout: de server is kan uw bericht momenteel niet verwerken. Probeer het nog een keer over een minuutje';
$string['error_sending_message'] = 'Fout: bericht kon niet verzonden worden, probeer het alstublieft nog een keer';
$string['error_creating_token'] = 'Fout: geen \'token\' beschikbaar voor pagina {NODE}';
$string['destination_label'] = 'Be~stemming';
$string['destination_title'] = 'Selecteer de bestemming voor uw bericht';
$string['fullname_label'] = '~Naam (verplicht)';
$string['fullname_title'] = 'Vult u alstublieft hier uw naam in';
$string['email_label'] = '~E-mail-adres (verplicht)';
$string['email_title'] = 'Vult u alstublieft hier uw e-mail-adres in';
$string['subject_label'] = '~Onderwerp';
$string['subject_title'] = 'U kunt hier een onderwerp voor uw bericht invullen';
$string['message_label'] = '~Bericht (verplicht)';
$string['message_title'] = 'Type hier uw bericht in';
$string['button_preview'] = '~Voorbeeld';
$string['button_send'] = '~Verzenden';
$string['cancelled'] = 'Geannuleerd';
$string['preview_header'] = 'Voorbeeld';
$string['from'] = 'Van';
$string['to'] = 'Aan';
$string['subject'] = 'Onderwerp';
$string['message'] = 'Bericht';
$string['date'] = 'Datum';
$string['ip_addr'] = 'IP-adres';
$string['subject_line'] = '[{NODE}] Bericht van {IP_ADDR}: {SUBJECT}';
$string['thankyou_header'] = 'Bericht is verzonden';
$string['here_is_a_copy'] = 'Hier is een afschrift van uw bericht.';
?>