#!/bin/bash
#Author absolutely.xu@gmail.com
MAXIMUM_BACKUP_FILES=30
BACKUP_FOLDERNAME="/home/backup/db/wireless_v1_mysql"
DB_HOSTNAME="localhost"
DB_USERNAME="root"
DB_PASSWORD="A8n8c8e"
DATABASES=(
"wireless_v1" 
"mysql"
)
#=========
echo "Bash Database Backup Tool"
#CURRENT_DATE=$(date +%F)
CURRENT_DATE=$(date +%F)
BACKUP_FOLDER="${BACKUP_FOLDERNAME}_${CURRENT_DATE}"
mkdir $BACKUP_FOLDER
#Count the database.
count=0
while [ "x${DATABASES[count]}" != "x" ];do
    count=$(( count + 1 ))
done
echo "[+] ${count} databases will be backuped..."
# Iterate over the database list and dump (in SQL) the content of echo one.
for DATABASE in ${DATABASES[@]};do
    echo "[+] Mysql-Dumping: ${DATABASE}"
    echo -n "   Began:  ";echo $(date)
    if $(mysqldump -h ${DB_HOSTNAME} -u${DB_USERNAME} -p${DB_PASSWORD} ${DATABASE} > "${BACKUP_FOLDER}/${DATABASE}.sql");then
        echo "  Dumped successfully!"
    else
        echo "  Failed dumping this database!"
    fi
        echo -n "   Finished: ";echo $(date)
done
echo
echo "[+] Packaging and compressing the backup folder..."
tar -cv ${BACKUP_FOLDER} | bzip2 > ${BACKUP_FOLDER}.tar.bz2 && rm -rf $BACKUP_FOLDER
BACKUP_FILES_MADE=$(ls -l ${BACKUP_FOLDERNAME}*.tar.bz2 | wc -l)
BACKUP_FILES_MADE=$(( $BACKUP_FILES_MADE - 0 )) ###Convert into integer number.
echo
echo "[+] There are ${BACKUP_FILES_MADE} backup files actually."
if [ $BACKUP_FILES_MADE -gt $MAXIMUM_BACKUP_FILES ];then
    REMOVE_FILES=$(( $BACKUP_FILES_MADE - $MAXIMUM_BACKUP_FILES ))
echo "[+] Remove ${REMOVE_FILES} old backup files."
    ALL_BACKUP_FILES=($(ls -t ${BACKUP_FOLDERNAME}*.tar.bz2))
    SAFE_BACKUP_FILES=("${ALL_BACKUP_FILES[@]:0:${MAXIMUM_BACKUP_FILES}}")
echo "[+] Safeting the newest backup files and removing old files..."
    FOLDER_SAFETY="_safety"
if [ ! -d $FOLDER_SAFETY ]
then mkdir $FOLDER_SAFETY
                                                                                                                    
fi
for FILE in ${SAFE_BACKUP_FILES[@]};do
                                                                                                                      
    mv -i  ${FILE}  ${FOLDER_SAFETY}
done
    rm -rf ${BACKUP_FOLDERNAME}*.tar.bz2
    mv  -i ${FOLDER_SAFETY}/* /home/backup/
    rm -rf ${FOLDER_SAFETY}
CHAR=''
for ((i=0;$i<=100;i+=10))
do  printf "Removing:[%-50s]%d%%\r" $CHAR $i
        sleep 0.1
CHAR=#$CHAR
done
    echo
fi
