#!/bin/bash

DIR=$(dirname $0)
cd ${DIR}
source ./includes.sh

echo -e "Building ${DOCKER_COMPOSE_FILE}..."

docker-compose ${DOCKER_COMPOSE_FILE} build --pull --force --no-cache

echo -e ""
