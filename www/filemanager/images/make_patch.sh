#!/bin/bash

if [ -f "$1" ];then
	SAVEIFS=$IFS
	IFS=$(echo -en "\n\b")

	for ITEM in *.svg;do
		patch "$ITEM" < $1
	done
	IFS=$SAVEIFS
	exit 0

else
	echo "$1 is not a patch file"
	exit 1
fi

