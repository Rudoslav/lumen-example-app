FROM php:7.4-fpm-alpine

RUN apk update
RUN apk add oniguruma-dev

# lumen/laravel packages
RUN docker-php-ext-install mbstring tokenizer mysqli pdo_mysql

# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer