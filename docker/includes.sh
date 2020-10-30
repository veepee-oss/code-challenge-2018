#!/bin/bash

DOCKER_COMPOSE_FILE="-f docker-compose.yml -f docker-compose.dev.yml"
DOCKER_SERVER_USER="david"
DOCKER_SERVER="docker_cc18_server"
DOCKER_MYSQL="docker_cc18_mysql"
DOCKER_MYSQL_USER="cc18_user"
DOCKER_MYSQL_PASSWORD="cc18_pass"
DOCKER_MYSQL_DATABASE="cc18_db"
DOCKER_MYSQL_BACKUP_FILE="${DOCKER_MYSQL_DATABASE}.bak.sql"

APP_PHP="/usr/local/bin/php"
APP_COMPOSER="/usr/local/bin/composer"
APP_CONSOLE="bin/console"
APP_PHPUNIT="bin/phpunit"

if [ "$HOME" == "/home/ubuntu" ]; then
    DOCKER_COMPOSE_FILE="-f docker-compose.yml -f docker-compose.prod.yml"
    DOCKER_SERVER_USER="ubuntu"
fi;
