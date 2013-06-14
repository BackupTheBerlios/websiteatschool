<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam <info@websiteatschool.eu>
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

/** /program/languages/fr/fr_manifest.php - description of the French translation
 *
 * This file defines the French language package ('fr'). 
 * This file is used when this package is installed.
 *
 * @author Jean Peyratout <jean.peyratout@abul.org> Marjolaine Audoux <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam <info@websiteatschool.eu>
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_fr
 * @version $Id: fr_manifest.php,v 1.5 2013/06/14 19:59:55 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }

$manifests['fr'] = array(
    'language_key'        => 'fr',
    'language_name'       => 'Français',
    'parent_language_key' => '',
    'description'         => 'This is the French translation based on Website@School release 0.90.4',
    'author'              => 'Jean Peyratout, Marjolaine Audoux',
    'author_email'        => 'jean.peyratout@abul.org',
    'version'             => 2013061400,
    'release'             => '0.90.4',
    'release_date'        => '2013-06-14',
    'is_core'             => FALSE
   );

?>