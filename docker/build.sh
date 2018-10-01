#!/bin/bash

DIR=$(dirname $0)
cd ${DIR}
source ./includes

echo -e "Building ${DOCKER_FILE}..."

docker-compose -f ${DOCKER_FILE} build --pull --force --no-cache

echo -e ""
