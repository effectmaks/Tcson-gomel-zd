FROM php:8.0-apache

RUN docker-php-ext-install mysqli

COPY .docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
