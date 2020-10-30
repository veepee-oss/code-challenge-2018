#!/bin/bash

DIR=$(dirname $0)
cd ${DIR}
source ./includes.sh

enabled=$( docker ps --format "{{.Names}}" | grep -i "${DOCKER_MYSQL}" )
if [ "$enabled" == "" ]
then
    echo -e "\033[31mContainer \033[33m${DOCKER_MYSQL}\033[31m not started!\033[0m\n";
    exit 1;
fi;

DOCKER_MYSQL_BACKUP_TABLES="";
if [ "$1" == "--all" ]; then
    echo -e "Backing up all the database to $DOCKER_MYSQL_BACKUP_FILE...\n";
else
    echo -e "Backing up the contests to $DOCKER_MYSQL_BACKUP_FILE...";
    echo -e "Add --all option to backup all the database.\n"
    DOCKER_MYSQL_BACKUP_TABLES="contest competitor round match";
fi;

docker exec -it ${DOCKER_MYSQL} \
    mysqldump \
        -u$DOCKER_MYSQL_USER \
        -p$DOCKER_MYSQL_PASSWORD \
        --add-drop-table \
        --complete-insert \
        --result-file=$DOCKER_MYSQL_BACKUP_FILE \
        $DOCKER_MYSQL_DATABASE \
        $DOCKER_MYSQL_BACKUP_TABLES

if [ $? -ne 0 ]; then
    echo -e "\033[31mAn error occurred doing the backup!\033[0m\n";
    exit;
fi;

docker exec -it ${DOCKER_MYSQL} \
    cat $DOCKER_MYSQL_BACKUP_FILE > $DOCKER_MYSQL_BACKUP_FILE

echo -e "Backup complete!\n";
echo -e ""
