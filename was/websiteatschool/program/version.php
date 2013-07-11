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

/** 'version.php' defines internal and external version numbers
 *
 * The following constants are defined in this file:
 *
 *  - WAS_VERSION - the internal version number, e.g. 2008020100
 *  - WAS_RELEASE - the external version number, e.g. 1.0 or 1.0.0
 *  - WAS_RELEASE_DATE - the date that the distribution files were generated
 *  - WAS_ORIGINAL - indicates the original (TRUE) or a modified version (FALSE) of this program
 *
 * WAS_VERSION is used to see if the database version matches the
 * program version. A difference between the two versions indicates
 * an incomplete update. The version number is of the form yyyymmddxx
 * where yyyymmdd is a date and the number xx is an auxiliary number
 * that may or may not carry an extra meaning. WAS_VERSION is always
 * greater than WAS_VERSION in a previous release of Website@School.
 *
 * WAS_RELEASE is a free-format human-readable string indicating the
 * the version of the program. It could take the form major.minor or
 * major.minor.patchlevel.
 *
 * WAS_RELEASE_DATE is the date on which the distribution package
 * was generated. This date is set by editing this file version.php
 * 'on the fly' from the makedist.sh script (see /devel/tools/makedist.sh).
 *
 * WAS_ORIGINAL is a flag which indicates the original version (value TRUE)
 * or a modified version (value FALSE) of the program.
 * The License Agreement for Website@School states:
 * 
 * "In accordance with section 7(c) modified versions of the Program must
 * clearly be marked in reasonable ways as different from the original
 * version without misrepresenting the origin of the Program. This must be
 * done by adding the phrase "Based on Website@School" to the Appropriate
 * Legal Notices."
 *
 * By defining WAS_ORIGINAL to FALSE, the phrase 'Powered by Website@School'
 * in the interactive user interfaces will morph into 'Based on Website@School'
 * automagically. The file '/program/about.html' should still be edited, though.
 *
 * @author Peter Fokker <peter@berestijn.nl>
 * @copyright Copyright (C) 2008-2013 Ingenieursbureau PSD/Peter Fokker
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package wascore
 * @version $Id: version.php,v 1.16 2013/07/11 10:40:31 pfokker Exp $
 */


/** The internal version number, like 2008012873 or 2008020100 (31 bits will work until the year 2147) */
define('WAS_VERSION',2013071100);


/** The external version number, like 1.0 or 1.0.0 */
define('WAS_RELEASE','0.90.5');


/** Date of distribution file generation in ISO 8601 format: yyyy-mm-dd OR yyyy-mm-ddThh:mm:ss+0000 */
define('WAS_RELEASE_DATE','2013-07-11');


/** A boolean flag indicating this is either the original (TRUE) or a modified (FALSE) version of Website@School */
define('WAS_ORIGINAL',TRUE);


?>