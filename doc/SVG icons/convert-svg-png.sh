#!/bin/bash
# SVG to png convertor
# export files in two sizes 


SIZE_BIG=33
#SIZE_SMALL=28

mkdir $SIZE_BIG 2>/dev/null
#mkdir $SIZE_SMALL 2>/dev/null

SAVEIFS=$IFS
IFS=$(echo -en "\n\b")

for ITEM in *.svg;do
	ITEM=`echo "$ITEM" | sed "s/\.svg$//"`
	inkscape "$ITEM.svg" --export-png="$SIZE_BIG/$ITEM.png" --export-width=$SIZE_BIG --export-height=$SIZE_BIG
#	inkscape "$ITEM.svg" --export-png="$SIZE_SMALL/$ITEM.png" --export-width=$SIZE_SMALL --export-height=$SIZE_SMALL
done

IFS=$SAVEIFS

exit 0
