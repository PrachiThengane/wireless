#!/bin/bash
#Backup Magento Directories
#Created on 19-05-2014
#Author: Chris Williams
#Version 1.0

#START

DSTDIR="/home/magento/app/design/frontend/default/w1lightinthebox/template/cms"			# Destination of replacement file
SRCDIR="/home/magento/app/design/frontend/default/w1lightinthebox/template/cms/mainbanner"	# Source of replacement file

REPFILE="home-quick-sand.phtml"

ONEINDEX="home-quick-sand-01.phtml"	# Replacement file to set one banners
TWOINDEX="home-quick-sand-02.phtml"	# Replacement file to set two banners
THREEINDEX="home-quick-sand-03.phtml"	# Replacement file to set three banners
FOURINDEX="home-quick-sand-04.phtml"	# Replacement file to set four banners
FIVEINDEX="home-quick-sand-05.phtml"	# Replacement file to set five banners
SIXINDEX="home-quick-sand-06.phtml"	# Replacement file to set six banners
SEVENINDEX="home-quick-sand-07.phtml"	# Replacement file to set seven banners
EIGHTINDEX="home-quick-sand-08.phtml"	# Replacement file to set eight banners
NINEINDEX="home-quick-sand-09.phtml"	# Replacement file to set nine banners
TENINDEX="home-quick-sand-10.phtml"	# Replacement file to set ten banners

echo "So you want to add banners to Magento?"

echo

read -p "How many banners in total do you want to have? (Enter a number between 1-10)" INDEX
echo

if [ $INDEX = 1 ]; then
cp $SRCDIR/$ONEINDEX $DSTDIR/$REPFILE
echo "Banner index of 1 set."
elif [ $INDEX = 2 ]; then
cp $SRCDIR/$TWOINDEX $DSTDIR/$REPFILE
echo "Banner index of 2 set."
elif [ $INDEX = 3 ]; then
cp $SRCDIR/$THREEINDEX $DSTDIR/$REPFILE
echo "Banner index of 3 set."
elif [ $INDEX = 4 ]; then
cp $SRCDIR/$FOURINDEX $DSTDIR/$REPFILE
echo "Banner index of 4 set."
elif [ $INDEX = 5 ]; then
cp $SRCDIR/$FIVEINDEX $DSTDIR/$REPFILE
echo "Banner index of 5 set."
elif [ $INDEX = 6 ]; then
cp $SRCDIR/$SIXINDEX $DSTDIR/$REPFILE
echo "Banner index of 6 set."
elif [ $INDEX = 7 ]; then
cp $SRCDIR/$SEVENINDEX $DSTDIR/$REPFILE
echo "Banner index of 7 set."
elif [ $INDEX = 8 ]; then
cp $SRCDIR/$EIGHTINDEX $DSTDIR/$REPFILE
echo "Banner index of 8 set."
elif [ $INDEX = 9 ]; then
cp $SRCDIR/$NINEINDEX $DSTDIR/$REPFILE
echo "Banner index of 9 set."
elif [ $INDEX = 10 ]; then
cp $SRCDIR/$TENINDEX $DSTDIR/$REPFILE
echo "Banner index of 10 set."
else
echo "Invalid Entry."
fi

echo "Have a nice day!"

chown -R www-data:www-data $DSTDIR

#END
