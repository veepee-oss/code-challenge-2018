#!/bin/bash

DIR=$(dirname $0)
cd ${DIR}
source ./includes

echo -e "Starting ${DOCKER_FILE}..."

docker-compose -f ${DOCKER_FILE} build
if [ $? -ne 0 ]; then
    exit 1
fi

docker-compose -f ${DOCKER_FILE} up -d --remove-orphans

echo -e ""
