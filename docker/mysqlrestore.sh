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

echo -e "Restoring the database from $DOCKER_MYSQL_BACKUP_FILE...\n";

docker exec -i ${DOCKER_MYSQL} \
  mysql \
    -u$DOCKER_MYSQL_USER \
    -p$DOCKER_MYSQL_PASSWORD \
    $DOCKER_MYSQL_DATABASE < $DOCKER_MYSQL_BACKUP_FILE

if [ $? -ne 0 ]; then
    echo -e "\033[31mAn error occurred restoring the database!\033[0m\n";
    exit;
fi;

echo -e "Restore complete!\n";
echo -e ""
