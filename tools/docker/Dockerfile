FROM php:apache

RUN apt-get update && apt-get -y -f install imagemagick libmagickwand-dev git ffmpeg && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install opcache
RUN pecl install apcu && docker-php-ext-enable apcu
RUN pecl install imagick && docker-php-ext-enable imagick


WORKDIR /var/www/html
RUN git clone --depth 1 https://github.com/hauspie/rfgallery.git . && cp config.php.sample config.php

WORKDIR /var/www/html/thumbs
RUN chown www-data . -R

WORKDIR /var/www/html

VOLUME ["/var/www/html/photos","/var/www/html/thumbs"]
