#!/bin/bash
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

# langdiff.sh -- tool to generate a list of new or modified English translations
#
# Peter Fokker -- 2011-01-12
#
# $Id: langdiff.sh,v 1.5 2013/06/11 11:25:08 pfokker Exp $
#
# This script performs the following actions.
# - the Website@Schoolversion of date "$1 23:59" is extracted from cvs
# - the current version of Website@School is exported from cvs
# - a php-script is executed to find differences in the English language files;
#   results are written to an HTML-page in the current directory
# - temporary files are removed
#
# The name of the generated report is based on the specified 'old' date
# and the 'new' date (the latter defaults to today).
#
# Usage: langdiff.sh yyyy-mm-dd [yyyy-mm-dd]

#
# 0 -- setup
#
PROG="$(basename "$0")"
PROGDIR="$(dirname "$(readlink -f "$0")")"

TARGETDIRECTORY="$(pwd)"
OLD_DATE="$(echo "$1" | tr -c -d '[0-9-]' | egrep "^[0-9]{4}-[0-9]{2}-[0-9]{2}$")"
if [ -z "$OLD_DATE" ]; then
  echo "$PROG: unrecognised date '$1', usage is: $PROG yyyy-mm-dd [yyyy-mm-dd]"
  exit 1;
fi
if [ -n "$2" ]; then
  NEW_DATE="$(echo "$2" | tr -c -d '[0-9-]' | egrep "^[0-9]{4}-[0-9]{2}-[0-9]{2}$")"
  if [ -z "$NEW_DATE" ]; then
    echo "$PROG: unrecognised date '$2', usage is: $PROG yyyy-mm-dd [yyyy-mm-dd]"
    exit 1;
  fi
else
  NEW_DATE="$(date +%Y-%m-%d)"
fi

WORKINGDIRECTORY="$(mktemp -d "$TARGETDIRECTORY/tmp-XXXXXX")"
if [ $? -ne 0 ]; then
    echo "$PROG: cannot create working directory, bailing out"
    exit 1
fi

cd "$WORKINGDIRECTORY"

#
# 1 -- get the files from cvs and re-arrange subtrees
#
echo "$PROG: Export version of $OLD_DATE from CVS ($CVSROOT)"
cvs export -D "$OLD_DATE 23:59" -d old was >/dev/null 2>&1
echo "$PROG: Export version of $NEW_DATE from CVS ($CVSROOT)"
cvs export -D "$NEW_DATE 23:59" -d new was >/dev/null 2>&1

OLD_RELEASE="$(grep "'WAS_RELEASE'" old/websiteatschool/program/version.php | tr -c -d '[0-9.]')"
NEW_RELEASE="$(grep "'WAS_RELEASE'" new/websiteatschool/program/version.php | tr -c -d '[0-9.]')"

if [ "$OLD_RELEASE" == "$NEW_RELEASE" ]; then
    NEW_RELEASE=CVS
    echo "$PROG: warning: OLD and NEW release were the same; using $NEW_RELEASE as NEW"
fi

#
# 2 -- use a PHP-script to list the changes in a structured way
#
OUTFILE="$TARGETDIRECTORY/langdiff-${OLD_RELEASE}-${NEW_RELEASE}.html"
echo "$PROG: Calculating differences"
php "$PROGDIR/langdiff.php" "$WORKINGDIRECTORY/old" \
                            "$WORKINGDIRECTORY/new" \
                            "$OLD_RELEASE ($OLD_DATE)" \
                            "$NEW_RELEASE ($NEW_DATE)" \
                            >$OUTFILE
#
# 3 -- clean up
#
cd $TARGETDIRECTORY
ls -l "$OUTFILE"
rm -r "$WORKINGDIRECTORY"
echo "$PROG: Done"

# eof
