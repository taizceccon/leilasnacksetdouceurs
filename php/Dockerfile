FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y build-essential curl zlib1g-dev g++ git libicu-dev zip libzip-dev libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev libcurl4-openssl-dev pkg-config libssl-dev ca-certificates telnet \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

RUN docker-php-ext-configure gd --with-freetype --with-webp --with-jpeg \
    && docker-php-ext-install  gd \
    && docker-php-ext-install exif \
    # Installation de MongoDB
    && pecl install mongodb-2.1.1 \
    && docker-php-ext-enable mongodb
RUN a2enmod rewrite && a2enmod ssl && a2enmod socache_shmcb

WORKDIR /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Install Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Configure Xdebug for coverage
COPY conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
