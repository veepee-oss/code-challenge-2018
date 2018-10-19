# See https://hub.docker.com/_/php/
FROM php:7.2-apache

RUN apt-get update && apt-get install zip unzip

RUN docker-php-ext-install pdo_mysql bcmath opcache && \
    docker-php-ext-enable pdo_mysql bcmath opcache

RUN pecl install apcu && \
    docker-php-ext-enable apcu

RUN useradd -m -u 1000 -U david

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && php -r "unlink('composer-setup.php');"

WORKDIR /var/www/html
