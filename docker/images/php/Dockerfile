FROM php:7-stretch
MAINTAINER Ivan Petrunko <ivan@petrunko.com>

RUN apt-get update && apt-get install -y \
        curl \
        wget \
        git \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
        libzip-dev \
        libsqlite3-dev \
    && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip json pdo_sqlite fileinfo \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && pecl install mcrypt-1.0.2 \
    && docker-php-ext-enable mcrypt

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir -p /var/log/app && chmod -R 777 /var/log/app

ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini
WORKDIR /var/www

EXPOSE 80

CMD ["php", "-S", "0.0.0.0:80", "-t", "./public", "./public/index.php"]
