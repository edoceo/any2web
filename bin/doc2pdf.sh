#!/bin/bash -x
#
# Converts a DOCX file to PDF

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

cp "$source_path" ./source.docx

#
# Always outputs to a pdf file with the same name
/usr/lib64/libreoffice/program/soffice.bin \
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
	"source.docx"

mv source.pdf "$target_path"