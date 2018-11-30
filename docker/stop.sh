#!/bin/bash

DIR=$(dirname $0)
cd ${DIR}
source ./includes.sh

echo -e "Stopping ${DOCKER_COMPOSE_FILE}..."

docker-compose ${DOCKER_COMPOSE_FILE} down --remove-orphans

echo -e ""
