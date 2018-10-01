#!/bin/bash

DIR=$(dirname $0)
cd ${DIR}
source ./includes

echo -e "Stopping ${DOCKER_FILE}..."

docker-compose -f ${DOCKER_FILE} down --remove-orphans

echo -e ""
