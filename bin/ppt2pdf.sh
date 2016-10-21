#!/bin/bash -x
#
# Converts a PPT and PPTX file to PDF

set -o errexit
set -o nounset

export HOME="/var/www/any2web.io/var"

source_path=$(readlink -f "$1")
target_path="$2"

if [[ ! -e "$source_path" ]]
then
	echo "Source does not exist"
	exit 1
fi

tmp=$(mktemp -d)
cd "$tmp"

trap "cd /; rm -fr $tmp" EXIT

cp "$source_path" ./work.pptx

#
# Always outputs to a pdf file with the same name
soffice \
	--headless \
	--invisible \
	--nocrashreport \
	--nodefault \
	--nofirststartwizard \
	--nolockcheck \
	--nologo \
	--norestore \
	--convert-to pdf \
	--outdir . \
	"work.pptx"

mv -v work.pdf "$target_path"
