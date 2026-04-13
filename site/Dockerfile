FROM php:8.2-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/site/www/tcsonrw-gomel.by

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        default-mysql-client \
    && docker-php-ext-install mysqli \
    && a2enmod rewrite headers \
    && sed -ri "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/000-default.conf /etc/apache2/apache2.conf \
    && rm -rf /var/lib/apt/lists/*

COPY .docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/site/www/tcsonrw-gomel.by
