<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker <peter@berestijn.nl>
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

/** /program/modules/aggregator/languages/nl/aggregator.php - translated messages for module (Dutch)
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_aggregator
 * @version $Id: aggregator.php,v 1.1 2012/07/01 18:45:40 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['title'] = 'Aggregator';
$string['description'] = 'Deze module is bedoeld voor het aggregeren pagina\'s en secties';
$string['translatetool_title'] = 'Aggregator';
$string['translatetool_description'] = 'Dit bestand bevat de vertalingen voor de aggregator-module';

$string['aggregator_content_header'] = 'Aggregator-configuratie';
$string['aggregator_content_explanation'] = '
Hier kunt u de aggregator-module configureren.
U kunt een optionele titel en een optionele introductietekst voor deze aggregator opgeven.
U kunt een komma-gescheiden lijst van pagina- en sectienummers opgeven. De opgegeven
pagina\'s zullen door de aggregator-module worden samengevoegd. Als u een sectienummer
opgeeft dan zullen alle pagina\'s uit die sectie worden geaggregeerd, in de natuurlijke
volgorde of juist in omgekeerde volgorde.
<p>
Als een opgegeven pagina gekoppeld is aan de kiekjes-module, dan zal het hieronder
opgegeven aantal afbeeldingen tegelijk worden weergegeven. De volgende afbeelding
wordt na een instelbare pauze weergegeven. Als het gaat om een gewone HTML-pagina,
dan zullen de eerste paar alineas worden weergegeven.
<p>
Let op!<br>
Pagina\'s die gekoppeld zijn aan een niet-herkende module zullen niet weergegeven worden.';
$string['header_label'] = '~Titel';
$string['header_title'] = 'Titel voor de aggregator';
$string['introduction_label'] = '~Introductie';
$string['introduction_title'] = 'Introductietekst voor deze aggregator';

$string['node_list_label'] = '~Lijst van pagina\'s en secties';
$string['node_list_title'] = 'Een komma-gescheiden lijst van pagina- and sectienummers';

$string['items_label'] = 'Aa~ntal te aggregeren pagina\'s';
$string['items_title'] = 'Dit is het maximum aantal weer te geven pagina\'s';
$string['reverse_order_check'] = 'O~mgekeerde sorteervolgorde';
$string['reverse_order_label'] = '';
$string['reverse_order_title'] = 'Vink het vakje aan om pagina\'s binnen een sectie in omgekeerde volgorde weer te geven';
$string['htmlpage_length_label'] = 'Te~kstlengte in alineas (htmlpagina)';
$string['htmlpage_length_title'] = 'De lengte van de te extraheren tekst (in alineas)';
$string['snapshots_width_label'] = '~Breedte in pixels (kiekjes)';
$string['snapshots_width_title'] = 'De totaal beschikbare breedte voor het weergeven van kiekjes';
$string['snapshots_height_label'] = 'Hoo~gte in pixels (kiekjes)';
$string['snapshots_height_title'] = 'De beschikbare hoogte voor het weergeven van kiekjes';
$string['snapshots_visible_label'] = '~Zichtbare afbeeldingen (kiekjes)';
$string['snapshots_visible_title'] = 'Het aantal weer te geven kiekjes';
$string['snapshots_showtime_label'] = '~Pauze tussen de afbeeldingen (kiekjes)';
$string['snapshots_showtime_title'] = 'De tijd (in seconden) tot de volgende afbeelding zichtbaar wordt';

$string['invalid_node'] = '{FIELD}: ongeldig pagina/sectienummer \'{VALUE}\'';
?>