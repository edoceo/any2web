#!/bin/bash
#
# Clean the Directories
#

f=$(readlink -f $0)
d=$(dirname $(dirname $(dirname "$f")))

cd "$d"

find ./var/job -type f -mtime +5 -exec rm -v {} \;
find ./var/job -type d -empty    -exec rm -frv {} \;
