FROM nginx:alpine

WORKDIR /var/www/todo-php

RUN apk update \
    && apk upgrade \
    && apk add --no-cache logrotate openssl bash shadow curl

RUN set -x ; \
    addgroup -g 82 -S www-data ; \
    adduser -u 82 -D -S -G www-data www-data && exit 0 ; exit 1

ADD docker/nginx/conf.d /etc/nginx/conf.d

ADD . /var/www/todo-php
RUN chown -R www-data:www-data /var/www/todo-php
