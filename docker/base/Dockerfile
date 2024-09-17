FROM php:8.2.1-apache-bullseye as chatgpt-base

WORKDIR /var/www/chatgpt/

RUN a2enmod rewrite && \
    apt-get -y update && \
    apt-get -y upgrade && \
    apt-get -y install git bash libzip-dev unzip curl libpq-dev libpng-dev && \
    docker-php-ext-install opcache zip pdo pdo_pgsql gd

COPY --from=composer:2.5.8 /usr/bin/composer /usr/bin/composer
