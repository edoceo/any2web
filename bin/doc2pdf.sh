#!/bin/bash -x
#
# Converts a DOCX file to PDF
# 
# @param $1 Source path of doc file.
# @param $2 Path directory of where the new file is to be put.
# The output file will be written to: $2/source.pdf
#
# Example: 	./bin/doc2pdf.sh in.doc /home/me/my_awesome_document/
#			./bin/doc2pdf.sh in.doc $(pwd)
#

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
/usr/bin/soffice \
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