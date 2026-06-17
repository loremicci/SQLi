FROM php:7.4-apache

RUN docker-php-ext-install pdo_mysql && docker-php-ext-enable pdo_mysql

# Copiamo i file del nostro sito
COPY . /var/www/html/
