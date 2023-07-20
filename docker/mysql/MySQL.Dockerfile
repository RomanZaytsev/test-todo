FROM mysql:5.7

COPY docker/mysql/docker-entrypoint /usr/local/bin/

# Add UID '1000' to www-data
RUN usermod -u 1000 www-data
RUN chown -R www-data:www-data /docker-entrypoint-initdb.d
RUN chmod -R u=rwx,g=rwx,o=rx  docker/mysql/init

ENTRYPOINT ["docker-entrypoint"]
