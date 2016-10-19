#!/bin/bash -x
#
# @see http://phantomjs.org/download.html
#

set -o errexit
set -o nounset

f=$(readlink -f $0)
d=$(dirname "$f")

source_path=$(readlink -f "$1")
target_path=$(readlink -f "$2")

# $kind = strtolower($_GET['b']);
# switch ($kind) {
# case 'android':
# case 'chrome':
# case 'firefox':
# case 'ipad':
# case 'iphone':
# 	$kind = strtolower($_GET['b']);
# 	break;
# default:
# 	$kind = 'null';
# }

phantomjs \
	--web-security=true \
	"$d/uri2pdf-phantom.js" \
	"$source_path" \
	"$target_path"
