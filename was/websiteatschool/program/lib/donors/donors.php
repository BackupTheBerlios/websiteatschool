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

/** /program/lib/donors/donors.php - a list of benefactors (people and organisations)
 *
 * This file provides a list of people and organisations
 * that contributed to Website@School by donating money, commissioning specific
 * features, etc. It is included and called from /program/main_admin.php.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2012 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: donors.php,v 1.1 2012/04/12 20:22:39 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

/** output the logos of zero, one or more of the Website@School benefactors
 *
 * @param object &$output collects the html output
 * @param bool $text_only if TRUE do not show a graphical image
 * @param int $num the number of benefactors to show this time around
 * @param string $m margin for increased readability
 * @return void output added to output
 */
function show_benefactor_logo(&$output, $text_only=FALSE, $num=10, $m='') {
    global $CFG;
    static $benefactors = array(
        array(
            'width'  => 160,
            'height' => 160,
            'img'    => 'anonymousdonor.gif',
            'url'    => 'http://websiteatschool.eu',
            'alt'    => 'Anonymous Donor',
            'title'  => 'Anonymous Donor'
            ),
        array(
            'width'  => 160,
            'height' => 40,
            'img'    => 'blindenpenning.gif',
            'url'    => 'http://www.blinden-penning.nl',
            'alt'    => 'Stichting Blinden-Penning',
            'title'  => 'Stichting Blinden-Penning - Fonds & recreatieve activiteiten voor slechtzienden en blinden'
            ),
        array(
            'width'  => 160,
            'height' => 170,
            'img'    => 'enablement.gif',
            'url'    => 'http://www.enablement.nl',
            'alt'    => 'Enablement',
            'title'  => 'Enablement'
            ),
        array(
            'width'  => 160,
            'height' => 40,
            'img'    => 'europeesplatform.gif',
            'url'    => 'http://www.europeesplatform.nl',
            'alt'    => 'Europees Platform',
            'title'  => 'Europees Platform - internationaliseren in onderwijs'
            ),
        array(
            'width'  => 160,
            'height' => 100,
            'img'    => 'mijnco2spoor.gif',
            'url'    => 'http://www.mijnco2spoor.nl',
            'alt'    => 'Stichting Mijn CO2 Spoor',
            'title'  => 'Stichting Mijn CO2 Spoor - Burgers voor een veilig en rechtvaardig klimaat'
            ),
        array(
            'width'  => 160,
            'height' => 80,
            'img'    => 'nvbs.gif',
            'url'    => 'http://www.nvbs.nl',
            'alt'    => 'Nederlandse Vereniging van Blinden en Slechtzienden',
            'title'  => 'Nederlandse Vereniging van Blinden en Slechtzienden - Oog voor U'
            ),
        array(
            'width'  => 160,
            'height' => 40,
            'img'    => 'rosaboekdrukker.gif',
            'url'    => 'http://rosaboekdrukker.net',
            'alt'    => 'Openbare Basisschool Rosa Boekdrukker',
            'title'  => 'Openbare Basisschool Rosa Boekdrukker'
            ),
        array(
            'width'  => 160,
            'height' => 72,
            'img'    => 'stkba.gif',
            'url'    => 'http://www.stkba.nl',
            'alt'    => 'Stichting KBA Nieuw West',
            'title'  => 'Stichting KBA Nieuw West'
            ),
        array(
            'width'  => 160,
            'height' => 98,
            'img'    => 'lemstratechniek.jpg',
            'url'    => 'http://www.lemstratechniek.nl',
            'alt'    => 'Lemstra Techniek',
            'title'  => 'Lemstra Techniek - elektrische schuifdeurkozijen en elektrisch schuifdeurbeslag'
            )
        );
    // 0 -- work to do at all?
    if ($num < 1) {
        return;
    }

    // 1 -- make sure we have shuffled array the first time around
    if ((!isset($_SESSION['donor_index'])) || (!isset($_SESSION['donor_array']))) {
        $donor_array = array_keys($benefactors);
        for ($j=sizeof($donor_array) - 1; ($j > 1); $j--) {
            $i = mt_rand(0,$j-1);
            $k = $donor_array[$j];
            $donor_array[$j] = $donor_array[$i];
            $donor_array[$i] = $k;
        }
        $_SESSION['donor_array'] = $donor_array;
        $_SESSION['donor_index'] = 0;
        unset($donor_array);
    }

    $output->add_menu($m.'<h2>'.t('donors','admin').'</h2>');
    if ($text_only) {
        $output->add_menu($m.'<ul>');
        $li = '  <li>';
    } else {
        $li = '';
    }
    for ($i=0; $i<$num; ++$i) {
        $index = $_SESSION['donor_array'][$_SESSION['donor_index']++];
        $benefactor = $benefactors[$index];
        $_SESSION['donor_index'] = $_SESSION['donor_index'] % sizeof($_SESSION['donor_array']);
        $title = $benefactor['title'];
        if ($text_only) {
            $anchor = $benefactor['alt'];
        } else {
            $img_attr = array('height' => $benefactor['height'],
                              'width' => $benefactor['width'],
                              'alt' => $benefactor['alt'],
                              'title' => $title);
            $anchor = html_img($CFG->progwww_short.'/lib/donors/'.$benefactor['img'],$img_attr);
        }
        $a_params = array('target' => '_blank', 'title' => $title);
        $output->add_menu($m.$li.html_a($benefactor['url'],NULL,$a_params,$anchor));;
    }
    if ($text_only) {
        $output->add_menu($m.'</ul>');
    }
} // show_benefactor_logo()

?>