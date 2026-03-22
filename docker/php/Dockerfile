FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libzip-dev \
    libicu-dev \
    librabbitmq-dev \
    libssl-dev \
    libwebp-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip \
    intl \
    opcache \
    bcmath \
    sockets \
    gd \
    && pecl install amqp redis \
    && docker-php-ext-enable amqp redis

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

WORKDIR /var/www/api