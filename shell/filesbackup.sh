#!/bin/bash
#Backup Magento Directories
#Created on 19-05-2014
#Author: Chris Williams
#Version 1.0

#START

TIME=$(date +%F)				# Sets date in backup file name
DSTDIR="/home/backup/files/wireless1-$TIME"	# Destination of backup file

FILENAMEAPP="appbackup-$TIME.tar.bz2"		# Sets backup file name format
SRCDIRAPP="/home/magento/app"			# Source directory of backup

FILENAMESKIN="skinbackup-$TIME.tar.bz2"		# Sets backup file name format
SRCDIRSKIN="/home/magento/skin"			# Source directory of backup

FILENAMEJS="jsbackup-$TIME.tar.bz2"		# Sets backup file name format
SRCDIRJS="/home/magento/js"			# Source directory of backup

FILENAMESHELL="shellbackup-$TIME.tar.bz2"	# Sets backup file name format
SRCDIRSHELL="/home/magento/shell"		# Source directory of backup

echo "Attempting to create backup directory."

echo

mkdir $DSTDIR
if [ $? -eq 0 ]; then
echo "Backup directory created successfully!"
else
echo "Backup directory creation failed or directory already exists!"
fi

echo

echo "Starting Backup!"

echo

tar -cpjf $DSTDIR/$FILENAMEAPP $SRCDIRAPP
if [ $? -eq 0 ]; then
echo
echo "Backup of /home/magento/app completed successfully!"
echo "Backup located at "$DSTDIR"/"$FILENAMEAPP"."
else
echo
echo "Backup of /home/magento/app failed!"
fi

echo

tar -cpjf $DSTDIR/$FILENAMESKIN $SRCDIRSKIN
if [ $? -eq 0 ]; then
echo
echo "Backup of /home/magento/skin complete successfully!"
echo "Backup located at "$DSTDIR"/"$FILENAMESKIN"."
else
echo
echo "Backup of /home/magento/skin failed!"
fi

echo

tar -cpjf $DSTDIR/$FILENAMEJS $SRCDIRJS
if [ $? -eq 0 ]; then
echo
echo "Backup of /home/magento/js complete successfully!"
echo "Backup located at "$DSTDIR"/"$FILENAMEJS"."
else
echo
echo "Backup of /home/magento/js failed!"
fi

echo

tar -cpjf $DSTDIR/$FILENAMESHELL $SRCDIRSHELL
if [ $? -eq 0 ]; then
echo
echo "Backup of /home/magento/shell complete successfully!"
echo "Backup located at "$DSTDIR"/"$FILENAMESHELL"."
else
echo
echo "Backup of /home/magento/shell failed!"
fi

echo

echo "All file backups completed!"

#END
