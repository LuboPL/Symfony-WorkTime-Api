FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libicu-dev \
    && docker-php-ext-install pdo pdo_mysql intl \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY conf.d/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www/project/app