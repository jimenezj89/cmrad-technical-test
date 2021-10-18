FROM php:7.4-fpm-alpine

ARG UID=1000
ARG GID=1000

RUN apk update
RUN apk add --no-cache \
    $PHPIZE_DEPS \
    composer

RUN pecl install xdebug-3.0.4
RUN docker-php-ext-enable xdebug
RUN echo 'xdebug.mode=coverage' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN apk del --purge $PHPIZE_DEPS

RUN addgroup -S alpine -g $GID && adduser -S alpine -u $UID -G alpine

RUN touch /var/log/app.log && chown 1000:1000 /var/log/app.log

WORKDIR /app

USER alpine
