#!/bin/bash

for FILE in *.less; do
	IN=$FILE
	OUT=`echo "$FILE"|sed "s/less$/css/"`
	echo "working on $IN > $OUT"
	`lessp.pl "$IN" >"$OUT"`
done
