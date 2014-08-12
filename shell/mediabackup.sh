#!/bin/bash
#Backup Magento Media Directory
#Created on 19-05-2014
#Author: Chris Williams
#Version 1.0

#START

TIME=$(date +%F)				# Sets date in backup file name
DSTDIR="/home/backup/files/w1-media-$TIME"	# Destination of backup file

FILENAMEMEDIA="mediabackup-$TIME.tar.bz2"	# Sets backup file name format
SRCDIRMEDIA="/home/magento/media"		# Source directory of backup

echo "Backup of Media Directory is only meant to be executed when required."
echo "DO NOT RUN IN CRONTAB!"

echo

read -p "Press any key to continue..."

echo

echo "Attempting to create backup directory."

echo

mkdir $DSTDIR
if [ $? -eq 0 ]; then
echo "Backup directory created successfully!"
else
echo "Backup directory creation failed or directory already exists!"
fi

echo

echo "Starting Media Directory Backup!"

echo

tar -cpjf $DSTDIR/$FILENAMEMEDIA $SRCDIRMEDIA
if [ $? -eq 0 ]; then
echo
echo "Backup of /home/magento/media completed successfully!"
echo "Backup located at "$DSTDIR"/"$FILENAMEMEDIA"."
else
echo
echo "Backup of /home/magento/media failed!"
fi

echo

echo "Have a nice day!"

#END
