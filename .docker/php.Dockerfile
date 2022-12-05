FROM php:8.1-fpm-alpine3.16
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
#todo: cleanup maybe. a lot of useless build artifacts
RUN docker-php-ext-install pdo pdo_mysql
