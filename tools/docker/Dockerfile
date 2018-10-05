FROM php:apache-stretch

RUN apt-get update && apt-get -y -f install imagemagick libmagickwand-dev git ffmpeg
RUN pecl install imagick && docker-php-ext-enable imagick

WORKDIR /var/www/html
RUN git clone https://github.com/hauspie/rfgallery.git . && cp config.php.sample config.php

VOLUME ["/var/www/html/photos","/var/www/html/thumbs"]
