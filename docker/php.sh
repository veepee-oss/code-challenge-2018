#!/bin/bash

DIR=$(dirname $0)
cd ${DIR}
source ./includes.sh

enabled=$( docker ps --format "{{.Names}}" | grep -i "${DOCKER_SERVER}" )
if [ "$enabled" == "" ]
then
    echo -e "\033[31mContainer \033[33m${DOCKER_SERVER}\033[31m not started!\033[0m\n";
    exit 1;
fi;

docker exec -it -u ${DOCKER_SERVER_USER} ${DOCKER_SERVER} ${APP_PHP} "$@"

echo -e ""

