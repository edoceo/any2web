#!/bin/bash -x
#
# https://github.com/thnew/Pdf2Png/blob/master/lib/pdf2png.js
#
# $1 Source PDF File
# $2 Output Base filename
# $3 Format - Optional, vertical, horizontal or in a ZIP

set -o errexit
set -o nounset

#export HOME="/var/www/any2web.io/var"
f=$(readlink -f $0)
d=$(dirname $(dirname "$f"))
export HOME="$d/var"

source="$1"
output="$2"
output="${output%.png}"

format="${3:-zip}"

tmp=$(mktemp -d)
cd "$tmp"

trap "cd /; rm -fr $tmp" EXIT

cp "$source" ./source.pdf


page_max=$(pdftk ./source.pdf dump_data | awk '/NumberOfPages/ { print $2 }');

for page_idx in $(seq 1 $page_max)
do
	page_idx=$(printf "%03u" $page_idx)

	gs \
		-dQUIET \
		-dPARANOIDSAFER \
		-dBATCH \
		-dNOPAUSE \
		-dNOPROMPT \
		-sDEVICE=png16m \
		-dTextAlphaBits=4 \
		-dGraphicsAlphaBits=4 \
		-r100 \
		-dFirstPage=$page_idx \
		-dLastPage=$page_idx \
		-sOutputFile="output-$page_idx.png" \
		./source.pdf
done

# Tall Images, Wide Images, Individual Zips, Specific Page
case "$format" in
"png"|"png-v")

	# Pack all the images into a very tall file
	convert \
		"output-*.png" \
		-append \
		-gravity Center \
		"output.png"

	mv output.png "$output.png"

	;;
"png-h")

	# Pack all the images into a wide file
	convert \
		"output-*.png" \
		+append \
		-gravity Center \
		"output.png"

	mv output.png "$output.png"

	;;

"zip")
	# Pack all the pages into a ZIP file
	for page_idx in $(seq 1 $page_max)
	do
		page_idx=$(printf "%03u" $page_idx)
		mv "output-$page_idx.png" "$output-p$page_idx.png"
		zip --junk-paths --no-dir-entries "output.zip" "$output-p$page_idx.png"
	done
	mv "output.zip" "$output.zip"
	;;
esac
