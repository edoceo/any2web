#!/bin/bash
#
# @see http://phantomjs.org/download.html

set -o errexit
set -o nounset

f=$(readlink -f $0)
d=$(dirname "$f")

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
	"$1" \
	"$2"

# 	' . $kind;
