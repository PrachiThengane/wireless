#!/bin/bash
#Losslessly compress JPEG and PNG images
#Created on 30-05-2014
#Author: Chris Williams
#Version 1.0

#START

MDAIMGDIR="/home/magento/skin/frontend/default/w1lightinthebox/images/"		# W1lightinthebox Skin Images Directory
SKNIMGDIR="/home/magento/media/catalog/"					# Product Images Directory

echo "Starting Lossless Image Optimization"

echo
echo

echo "Execute Product Images Directory JPEG Optimization"

echo
echo

find $MDAIMGDIR -type f -name "*.jpg" -exec jpegoptim --strip-all {} \;

echo
echo

echo "Execute Skin Images Directory JPEG Optimization"

echo
echo

find $SKNIMGDIR -type f -name "*.jpg" -exec jpegoptim --strip-all {} \;

echo
echo

echo "Execute Product Images Directory PNG Optimization"

echo
echo

find $MDAIMGDIR -type f -name "*.png" -exec optipng -o5 {} \;

echo
echo

echo "Execute Skin Images Directory PNG Optimization"

echo
echo

find $SKNIMGDIR -type f -name "*.png" -exec optipng -o5 {} \;

echo
echo

echo "Lossless Image Compression Finished!"

#END
