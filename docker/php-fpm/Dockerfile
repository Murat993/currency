FROM php:8.2-fpm
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/install-php-extensions
RUN install-php-extensions sockets && docker-php-ext-enable sockets
RUN apt-get update && apt-get install -y git unzip && \
    php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');" && \
    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    apt-get clean

VOLUME /var/www
WORKDIR /var/www
