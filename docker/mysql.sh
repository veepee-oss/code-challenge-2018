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

ARGS=""
while [[ $# -ge 1 ]]; do
    ARGS="${ARGS} $1"
    shift
done;

if [ "${ARGS}" == "" ]; then
    docker exec -it ${DOCKER_MYSQL} mysql -u$DOCKER_MYSQL_USER -p$DOCKER_MYSQL_PASSWORD
else
    docker exec -it ${DOCKER_MYSQL} ${ARGS}
fi

echo -e ""
