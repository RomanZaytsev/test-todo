FROM nginx:alpine

WORKDIR /var/www/test-todo

RUN apk update \
    && apk upgrade \
    && apk add --no-cache logrotate openssl bash shadow curl

RUN set -x ; \
    addgroup -g 82 -S www-data ; \
    adduser -u 82 -D -S -G www-data www-data && exit 0 ; exit 1

ADD docker/nginx/conf.d /etc/nginx/conf.d

ADD . /var/www/test-todo
RUN chown -R www-data:www-data /var/www/test-todo
