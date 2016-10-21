#!/bin/bash -x
#
# Converts any Image file to GIF, JPEG or PNG
#
# @param $1 Source File
# @param $2 Output File
# @param $3 Format gif,jpeg,png*
#
# @see http://www.imagemagick.org/script/convert.php
# @see http://www.imagemagick.org/Usage/masking/
# 	-alpha activate \
# 	-background none \
# 	-transparent white \

set -o errexit
set -o nounset

#export HOME="/var/www/any2web.io/var"
f=$(readlink -f $0)
d=$(dirname $(dirname "$f"))
export HOME="$d/var"

source="$1"
output="$2"
format="${3:=png}"


case "$3" in
"gif")

	/usr/bin/convert \
		"$source" \
		-background '#ffffff' \
		-strip \
		"$output"

	;;

"jpeg")

	/usr/bin/convert \
		"$source" \
		-background '#ffffff' \
		-strip \
		-interlace "line" \
		"$output"

	;;

"png")

	/usr/bin/convert \
		"$source" \
		-strip \
		-interlace "PNG" \
		"$output"

	;;

esac