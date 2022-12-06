FROM php:8.1-fpm-alpine3.16
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN cd /usr/local/etc/php && cp -a php.ini-development php.ini
# not sure it's useful, experiment
RUN echo mysqli.allow_local_infile=1 >> /usr/local/etc/php/php.ini
# todo: cleanup maybe. a lot of useless build artifacts
RUN docker-php-ext-install pdo pdo_mysql \
