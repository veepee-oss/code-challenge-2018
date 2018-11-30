#!/bin/bash

DIR=$(dirname $0)
cd ${DIR}
source ./includes.sh

echo -e "Starting ${DOCKER_COMPOSE_FILE}..."

docker-compose ${DOCKER_COMPOSE_FILE} build
if [ $? -ne 0 ]; then
    exit 1
fi

docker-compose ${DOCKER_COMPOSE_FILE} up -d

echo -e ""
