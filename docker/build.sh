#!/bin/bash

DIR=$(dirname $0)
cd ${DIR}
source ./includes.sh

echo -e "Building ${DOCKER_COMPOSE_FILE}..."

COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1 docker-compose ${DOCKER_COMPOSE_FILE} build --pull --force --no-cache

echo -e ""
