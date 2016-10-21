#!/bin/bash -x
#
# Converts an ODT file to PDF
#

set -o errexit
set -o nounset

f=$(readlink -f "$0")
d=$(dirname $(dirname "$f"))

export HOME="$d/var"

source_path=$(readlink -f "$1")
target_path=$(readlink -f "$2")

if [[ ! -e "$source_path" ]]
then
	echo "Source does not exist"
	exit 1
fi

tmp=$(mktemp -d)
cd "$tmp"

trap "cd /; rm -fr $tmp" EXIT

cp "$source_path" ./source.odt

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
	source.odt

mv source.pdf "$target_path"
