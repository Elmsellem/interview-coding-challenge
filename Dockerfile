FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    && docker-php-ext-install intl \
    && docker-php-ext-install bcmath \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

RUN echo "xdebug.mode=debug,coverage" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini;

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN git config --global --add safe.directory /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
