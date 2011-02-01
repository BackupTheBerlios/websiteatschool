#!/bin/bash
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2011 Ingenieursbureau PSD/Peter Fokker <peter@berestijn.nl>
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
# along with this program. If not, see http://websiteatschool.org/license.html

# makedoc.sh -- quick and dirty generator of documentation (without metafiles README/INSTALL/CHANGES)
# Peter Fokker -- 2008-01-31
#
# $Id: makedoc.sh,v 1.1 2011/02/01 13:01:04 pfokker Exp $

# Use an explicit path if phpdoc cannot be found via your $PATH
PHPDOC="phpdoc"

# WASDIR is the toplevel directory; try to calculate from location of this script
WASDIR="$(dirname "$(readlink -f "$0")")/../.."

SOURCEDIR="websiteatschool,addons,languages,manuals,modules,themes"
TARGETDIR="devel/docs"
IGNORE="CVS/,config.php"
TITLE="Website@School Documentation (CVS-version)"
OUTPUTS="HTML:frames:earthli,PDF:default:default"

cd "$WASDIR"

# echo "DEBUG:" \
"$PHPDOC" \
-q \
-d "${SOURCEDIR}" \
-t "${TARGETDIR}" \
-i "${IGNORE}" \
-ti "${TITLE}" \
-o "${OUTPUTS}"
