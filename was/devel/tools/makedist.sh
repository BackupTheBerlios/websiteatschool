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

# makedist.sh -- tool to generate Website@School distribution files
#
# Peter Fokker -- 2008-01-31
#
# $Id: makedist.sh,v 1.5 2013/06/11 15:15:57 pfokker Exp $
#
# History:
# 2013-06-11/PF: added support for CREW-module
#
# Usage:
#
# makedist.sh [ -f ] [ -h ] [ -m mod ] [ -p ] [ -r ] [ -s ] distversion
#
#  -f     | --full       generate both the 'distribution' and 'developer' files
#  -h     | --help       show this help message
#  -m mod | --module mod export only CVS-module 'mod'
#  -p     | --partial    generate only the 'distribution' files (opposite of --full)
#  -r     | --release    generate an official release using the appropriate tag in CVS
#  -s     | --snapshot   generate snapshot version using the latest version in CVS (opposite of --release)
#  distversion           either a version number like v.r.p (for --release) or another identifier,
#                        e.g. a date yyyymmdd (for --snapshot).
#
# defaults: $PROG --full --snapshot --module was
#
# This script performs the following actions.
# - a revision Website@School is exported from cvs (using either HEAD or another tag)
# - the various exported directory subtrees are moved to the correct place
# - a distribution package (both in .tar.gz and .zip file) is generated
# - maybe the developer documentation is generated from the exported source tree
# - maybe a developer/docs package is generated (also .tar.gz and .zip)
# - the corresponding md5sum()s are calculated and stored in a file
#
# The names of the generated files all have a version number embedded.
# Suppose the version number is 1.0.2a. That would yield the following files
#
# websiteatschool-1.0.2a.tar.gz
# websiteatschool-1.0.2a.zip
# websiteatschool-devel-1.0.2a.tar.gz
# websiteatschool-devel-1.0.2a.zip
# websiteatschool-1.0.2a.md5sums.txt
#
# Typical use:
#
# Scenario 1: create a quick snapshot of the code (distribution files only)
# makedist.sh --partial --snapshot yyyymmdd
# Result: 3 files: websiteatschool-yyyymmdd.{zip,tar.gz,md5sums.txt}
#
# Scenario 2: create an official version 0.90.0 (distribution and developer files w/ documentation)
# makedist.sh --full --release 0.90.0
# Result: 5 files: websiteatschool-devel-yyyymmdd.{zip,tar.gz} and
#                  websiteatschool-yyyymmdd.{zip,tar.gz,md5sums.txt}
#
# Scenario 3: create a quick snapshot of the current Spanish (es) translation
# makedist.sh --partial --snapshot yyyymmdd --module was/languages/es
# Result: 3 files: websiteatschool-languages-es-yyyymmdd.{zip,tar.gz,md5sums.txt}
#
# Note 1:
# Creating an official release (with --release) for version 'v.r.p' requires that a revision tag
# 'release-v_r_p' exists in CVS. If that does not exist, no files are generated.
#
# Note 2:
# All distribution files unpack into the current working directory and no directory
# names have the version number embedded. It is very well possible to unpack a
# languages file and copy over an existing installation, e.g.
# # cd /home/httpd/htdocs
# # unzip websiteatschool-0.90.0.zip
# # unzip -o websiteatschool-languages-es-yyyymmdd.zip
#
# Note 3:
# All developer versions do unpack in a subdirectory which reflects the version number, e.g.
# # tar zxf websiteatschool-devel-0.90.0.tar.gz
# yields a directory websiteatschool-0.90.0/ with subdirectories
# devel/ and /websiteatschool/. The latter contains the same files as the distribution package.
# That is: the distribution is basically a subtree of the development version.
#
# Note 4:
# This script is based on the assumption that the usual suspects are available in the $PATH
# (mv, rm, mkdir, ls, zip, tar). Generating the documentation requires phpdoc, maybe with
# an explicit path (see definition of $PHPDOC below).

function tmlog() {  # echo w/ timestamp
  echo "$(date '+%H%M') $PROG: $*"
} # tmlog()

function usage() {
  if [ -n "$1" ]; then
    tmlog "$1"
  fi
  echo "usage is:

$PROG [ -f ] [ -h ] [ -m mod ] [ -p ] [ -r ] [ -s ] distversion

 -f     | --full       generate both the 'distribution' and 'developer' files
 -h     | --help       show this help message
 -m mod | --module mod export only CVS-module 'mod'
 -p     | --partial    generate only the 'distribution' files (opposite of --full)
 -r     | --release    generate an official release using the appropriate tag in CVS
 -s     | --snapshot   generate a snapshot version using the latest version in CVS (opposite of --release)
 distversion           either a version number like v.r.p (for --release) or another identifier,
                       e.g. a date yyyymmdd (for --snapshot).

defaults: $PROG --full --snapshot --module was
"
} # usage()


#
# 0 -- setup essentials
#
PROG="$(basename "$0")"
PHPDOC="phpdoc"         # if necessary add an explicit path to the executable
TARGETDIRECTORY="$(pwd)"

#
# 1 -- setup shop
#

# 1A -- set defaults
ARG_FULL="full"         # "partial" or "full"
ARG_RELEASE="snapshot"  # "snapshot" or "release"
ARG_MODULE="was"
ARG_VERSION=""

# 1B -- process command line
while [ -n "$1" ]; do
  case "$1" in
  -h|--help)
    usage
    exit 0
    ;;
  -f|--full)
    ARG_FULL="full"
    ;;
  -p|--partial)
    ARG_FULL="partial"
    ;;
  -r|--release)
    ARG_RELEASE="release"
    ;;
  -s|--snapshot)
    ARG_RELEASE="snapshot"
    ;;
  -m|--module)
    shift
    ARG_MODULE="$(echo "$1" | tr -c -d '[a-xA-Z0-9./]')" # alphanumeric or dot or slash; always start with 'was'
    if [ "$ARG_MODULE" != "was" ]; then
        ARG_MODULE="$(echo "$ARG_MODULE" | grep '^was/')"
    fi
    ;;
  *)
    ARG_VERSION="$(echo "$1" | tr -c -d '[a-zA-Z0-9.]')" # alphanumeric or dot
    ;;
  esac
  shift
done

# 1C -- sanity checks
if [ -z "$ARG_VERSION" ]; then
  usage "no distribution version specified"
  exit 1;
elif [ -z "$ARG_MODULE" ]; then
  usage "invalid or no cvs module specified"
  exit 1;
fi

# 1D -- initialise important variables
if [ "$ARG_MODULE" == "was" ]; then
  PACKAGENAME="websiteatschool"
else
  # 'was/modules' becomes 'websiteatschool-modules'
  PACKAGENAME="$(echo "$ARG_MODULE" | tr '/' '-' | sed -e "s/^was/websiteatschool/g")"
  if [ "$ARG_FULL" != "partial" ]; then
    tmlog "warning: forcing partial $ARG_RELEASE for module $ARG_MODULE"
    ARG_FULL="partial"
  fi
fi
PACKAGEDEVELNAME="${PACKAGENAME}-devel"
tmlog "creating $ARG_FULL $ARG_RELEASE ${PACKAGENAME}-$ARG_VERSION from cvsmodule '$ARG_MODULE'"

# 1E -- build a comfortable working environment
WORKINGDIRECTORY="$(mktemp -d "$TARGETDIRECTORY/tmp-XXXXXX")"
if [ $? -ne 0 ]; then
    tmlog "cannot create working directory, bailing out"
    exit 1
fi
cd "$WORKINGDIRECTORY"

#
# 2 -- get the files from cvs
#
if [ "$ARG_RELEASE" == "release" ]; then
  REVISION_TAG="release-$(echo "$ARG_VERSION" | tr '.' '_')"
else # snapshot
  REVISION_TAG="HEAD"
fi
tmlog "exporting module '$ARG_MODULE' from CVS using revision tag '$REVISION_TAG'"
cvs -q export -r "$REVISION_TAG" "$ARG_MODULE" >/dev/null 2>&1
if [ $? -ne 0 ]; then # fatal: error with CVS
  tmlog "fatal: could not export module '$ARG_MODULE' from CVS using revision tag '$REVISION_TAG'; bailing out"
  cd $TARGETDIRECTORY
  rm -r "${WORKINGDIRECTORY}/"
  exit 1
fi
# always reflect the version number in the top level directory to allow different versions to coexist
mv was "websiteatschool-${ARG_VERSION}"
cd "websiteatschool-${ARG_VERSION}"

# if we check out the complete project, make sure any empty but essential dirs are created nonetheless
if [ "$ARG_MODULE" == "was" ]; then
  for d in devel addons languages manuals modules themes; do
    if [ ! -d "$d" ]; then
      tmlog "creating empty directory '$d' for completeness"
      mkdir -p "$d"
    fi
  done
fi


#
# 3 -- massage the exported files
#

# the program expects to see these 5 different directories in the program directory
for d in addons languages manuals modules themes; do
  if [ -d "$d" ]; then
    tmlog "moving '$d' to websiteatschool/program/"
    mkdir -p websiteatschool/program/
    mv "$d" websiteatschool/program/
  fi
done

# prepare a .ZIP-file containing the CREW-server (added 2013-06-11)
tmlog "creating a compilation of the crew server files"
cd websiteatschool/program/modules/crew/server
mkdir -p -m 0755 graphics
cp -a ../../../graphics/waslogo-567x142.png graphics/
cp -a ../../../lib/utf8lib.php .
cp -a ../../../lib/zip.class.php .
cp -a ../../../lib/license.html .
cp -a ../../../lib/about.html .
zip -9 -r ../crewserver.zip *
unzip -v ../crewserver.zip
cd ../../../../..

# maybe plugin a quasi-version number in version.php when generating a (daily) snapshot
if [ -f websiteatschool/program/version.php ]; then
  cd websiteatschool/program
  if [ "$ARG_RELEASE" == "snapshot" ]; then
    WAS_RELEASE_DATE="$(date -u --iso-8601=seconds)"
    WAS_RELEASE="$(date -u +%Y.%m.%d)"
    tmlog "updating version.php: WAS_RELEASE='$WAS_RELEASE' and WAS_RELEASE_DATE='$WAS_RELEASE_DATE'"
    sed -e "s/^define('WAS_RELEASE'.*$/define('WAS_RELEASE','${WAS_RELEASE}');/g" \
        -e "s/^define('WAS_RELEASE_DATE'.*$/define('WAS_RELEASE_DATE','${WAS_RELEASE_DATE}');/g" \
        -i version.php
  else
    WAS_RELEASE_DATE="$(grep "^define('WAS_RELEASE_DATE'" version.php  | \
                        sed -e "s/^define('WAS_RELEASE_DATE'.*'\(.*\)');/\1/")"
    WAS_RELEASE="$(grep "^define('WAS_RELEASE'" version.php  | \
                   sed -e "s/^define('WAS_RELEASE'.*'\(.*\)');/\1/")"
    tmlog "keeping version.php as-is: WAS_RELEASE='$WAS_RELEASE' and WAS_RELEASE_DATE='$WAS_RELEASE_DATE'"
  fi
  cd ../..
fi

# generate documentation (only for full release/snapshot)
if [ -d devel -a "$ARG_FULL" == "full" ]; then
  tmlog "generating documentation with $PHPDOC (please be patient; this takes a long time...)"
  SOURCEDIR="websiteatschool"
  TARGETDIR="devel/docs"
  IGNORE="CVS/,config.php"
  TITLE="Website@School Developer Documentation ${ARG_VERSION}"
  OUTPUTS="HTML:frames:earthli,PDF:default:default"
  METAFILES="CHANGES,CREDITS,FAQ,HISTORY,INSTALL,LICENSE,README,TODO"
  mkdir -p "$TARGETDIR"
  # allow phpdoc to find the meta-files and strip any carriage returns in the process
  for f in $(echo "$METAFILES" | tr ',' ' '); do
      tr -d '\r' <"websiteatschool/program/$f.txt" >"websiteatschool/$f"
  done

  # generate docs (also from meta-files)
  sleep 20; tmlog "DEBUG:" \
  "$PHPDOC" \
  -q \
  -d "${SOURCEDIR}" \
  -t "${TARGETDIR}" \
  -i "${IGNORE}" \
  -ti "${TITLE}" \
  -o "${OUTPUTS}" \
  -ric "${METAFILES}"

  # get rid of the translated metafiles; we've got them in devel/docs now
  for f in $(echo "$METAFILES" | tr ',' ' '); do
      rm "websiteatschool/$f"
  done
  tmlog "done generating documentation with '$PHPDOC'"
else
  tmlog "no developer documentation generated"
fi

#
# 4 -- maybe generate developer packages (ie. with documenation)
#
FILENAMES=""
if [ "$ARG_FULL" == "full" ]; then
  cd "$WORKINGDIRECTORY"
  FILENAME="${PACKAGEDEVELNAME}-${ARG_VERSION}"
  tmlog "generating developer package '${PACKAGEDEVELNAME}-${ARG_VERSION}.tar.gz'"
  tar cf - "websiteatschool-${ARG_VERSION}/" | gzip -9 >"${TARGETDIRECTORY}/${FILENAME}.tar.gz"
  if [ -f "${TARGETDIRECTORY}/${FILENAME}.zip" ]; then
    tmlog "removing existing file '${FILENAME}.zip'"
    rm "${TARGETDIRECTORY}/${FILENAME}.zip"
  fi
  tmlog "generating developer package '${PACKAGEDEVELNAME}-${ARG_VERSION}.zip'"
  zip -9 -r "${TARGETDIRECTORY}/${FILENAME}.zip" "websiteatschool-${ARG_VERSION}/" >/dev/null
  FILENAMES="${FILENAME}.tar.gz ${FILENAME}.zip "
fi

#
# 5 -- generate the installation packages
#
tmlog "removing superfluous files from 'distribution' files"
cd "${WORKINGDIRECTORY}/websiteatschool-${ARG_VERSION}"
if [ -d websiteatschool ]; then
  cd websiteatschool
fi
# Get rid of 'hidden' .cvsignore etc.
find . -type f -name \.\* -exec rm '{}' ';'

# Get rid of devel version of CREW-server but keep the .ZIP (added 2013-06-11)
rm -r program/modules/crew/server/
ls -l program/modules/crew/crewserver.zip

FILENAME="${PACKAGENAME}-${ARG_VERSION}"
tmlog "generating installation package '${PACKAGENAME}-${ARG_VERSION}.tar.gz'"
tar cf - * | gzip -9 >"${TARGETDIRECTORY}/${FILENAME}.tar.gz"
if [ -f "${TARGETDIRECTORY}/${FILENAME}.zip" ]; then
  tmlog "removing existing file '${FILENAME}.zip'"
  rm "${TARGETDIRECTORY}/${FILENAME}.zip"
fi
tmlog "generating installation package '${PACKAGENAME}-${ARG_VERSION}.zip'"
zip -9 -r "${TARGETDIRECTORY}/${FILENAME}.zip" * >/dev/null
FILENAMES="${FILENAMES}${FILENAME}.tar.gz ${FILENAME}.zip"

#
# 6 -- calc md5sums
#
tmlog "generating md5sums"
cd $TARGETDIRECTORY
md5sum -b $FILENAMES >${PACKAGENAME}-${ARG_VERSION}.md5sums.txt

#
# 7 -- cleanup
#
tmlog "cleaning up and showing results"
rm -r "${WORKINGDIRECTORY}/"
ls -l $FILENAMES ${PACKAGENAME}-${ARG_VERSION}.md5sums.txt
exit 0

# eof
