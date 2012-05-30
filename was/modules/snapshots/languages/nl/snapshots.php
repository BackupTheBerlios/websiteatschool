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

/** /program/modules/snapshots/languages/nl/snapshots.php - translated messages for module (Dutch)
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wasmod_snapshots
 * @version $Id: snapshots.php,v 1.1 2012/05/30 12:47:23 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$string['title'] = 'Kiekjes';
$string['description'] = 'Deze module is bedoeld voor het snel zichtbaar maken van fotoseries';
$string['translatetool_title'] = 'Kiekjes';
$string['translatetool_description'] = 'Dit bestand bevat de vertalingen voor de kiekjes-module';

$string['snapshots_content_header'] = 'Kiekjes-configuratie';
$string['snapshots_content_explanation'] = 'Hier kunt u de kiekjes-module configureren. U kunt optionele titel en een optionele introductie voor de fotoserie opgeven. U kunt ook de initiële weergave van de fotoserie instellen. Gebruik daarvoor één van de volgende opties:
\'mini-afbeeldingen\' om te beginnen met de introductietekst en mini-afbeeldingen van de hele fotoserie,
\'eerste\' om te beginnen met de eerste foto in de serie, of
\'diashow\' voor een automatische diashow (op javascript gebaseerd).';
$string['header_label'] = '~Titel';
$string['header_title'] = 'Titel voor de fotoserie';
$string['introduction_label'] = '~Introductie';
$string['introduction_title'] = 'Introductietekst voor deze fotoserie';
$string['snapshots_path_label'] = '~Locatie';
$string['snapshots_path_title'] = 'Datamap waar de afbeeldingen zijn opgeslagen';
$string['variant_label'] = 'Kies de ~variant voor de initiële weergave van de fotoserie';
$string['variant_title'] = 'Selecteer één van de mogelijkheden uit de lijst';
$string['variant_thumbs_label'] = '~Mini-afbeeldingen';
$string['variant_thumbs_title'] = 'Begin met de mini-afbeeldingen';
$string['variant_first_label'] = '~Eerste';
$string['variant_first_title'] = 'Start met de eerste foto in de serie';
$string['variant_slideshow_label'] = '~Diashow';
$string['variant_slideshow_title'] = 'Vertoon alle afbeeldingen één voor één';
$string['dimension_label'] = '~Grootte van de box (in pixels)';
$string['dimension_title'] = 'Voer de grootte van de box in waarbinnen de afbeeldingen worden weergegeven';
$string['no_snapshots_available'] = 'Geen kiekjes beschikbaar';
$string['warning_no_such_snapshot'] = 'Waarschuwing: kan afbeelding \'{SNAPSHOT}\' niet vinden';
$string['move_first_title'] = 'Eerste';
$string['move_first_alt'] = 'eerste';
$string['move_prev_title'] = 'Vorige';
$string['move_prev_alt'] = 'vorige';
$string['move_up_title'] = 'Overzicht';
$string['move_up_alt'] = 'mini-afbeeldingen';
$string['move_next_title'] = 'Volgende';
$string['move_next_alt'] = 'volgende';
$string['move_last_title'] = 'Laatste';
$string['move_last_alt'] = 'laatste';
$string['move_current_title'] = 'Huidige';
$string['move_current_alt'] = 'huidige';
$string['slideshow_title'] = 'Diavoorstelling (opent in een nieuw pop-up venster)';
$string['slideshow_alt'] = 'diavoorstelling';
$string['snapshot_status'] = '{SNAPSHOT}/{SNAPSHOTS} - {CAPTION}';
$string['warning_different_area'] = 'Waarschuwing: u selecteerde afbeeldingen uit een ander gebied ({AREANAME})';
$string['warning_personal_directory'] = 'Waarschuwing: u selecteerde afbeeldingen uit uw persoonlijke map';
$string['js_loading'] = 'laden...';
$string['js_no_images'] = 'Geen kiekjes om te laten zien';
$string['snapshots0_title'] = 'Deze sectie bevat het algemene fotoalbum van de school';
$string['snapshots0_link_text'] = 'Fotoalbum';
$string['snapshots1_title'] = 'Foto\'s van de excursie naar de Hortus Botanicus';
$string['snapshots1_link_text'] = 'Excursie {LAST_WEEK}';
$string['snapshots1_header'] = 'Excursie naar de Hortus Botanicus ({LAST_WEEK})';
$string['snapshots1_introduction'] = 'Hier zijn de foto\'s van de excursie naar de Hortus Botanicus die de leerlingen van de bovenbouw maakten op {LAST_WEEK}.<p>{LOREM} {IPSUM} {DOLOR}.';
?>