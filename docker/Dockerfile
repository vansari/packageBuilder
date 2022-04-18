ARG PHP_VERSION="8.1.4"
ARG COMPOSER_VERSION="2.3.5"

FROM php:${PHP_VERSION}-cli-alpine as php

FROM composer:${COMPOSER_VERSION} as composer

FROM php as composer-php
COPY --from=composer /usr/bin/composer /usr/bin/composer
