# See https://hub.docker.com/_/php/
FROM php:7.2-apache

ARG DOCKER_USER
ARG SERVER_TIME_ZONE=UTC

RUN apt-get update && apt-get install zip unzip

RUN docker-php-ext-install pdo_mysql bcmath opcache && \
    docker-php-ext-enable pdo_mysql bcmath opcache

RUN pecl install apcu && \
    docker-php-ext-enable apcu

#RUN rm -rf /etc/localtime && \
#    ln -s /usr/share/zoneinfo/$SERVER_TIME_ZONE /etc/localtime && \
#    echo "$SERVER_TIME_ZONE" > /etc/timezone

RUN printf "[PHP]\ndate.timezone = '$SERVER_TIME_ZONE'\n" > /usr/local/etc/php/conf.d/tzone.ini

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

RUN useradd -m -u 1000 -U $DOCKER_USER

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && php -r "unlink('composer-setup.php');"

WORKDIR /var/www/html
