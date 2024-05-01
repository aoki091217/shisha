FROM php:8.0-apache-bullseye

RUN apt-get update \
  && apt-get install -y git unzip libpng-dev libjpeg62-turbo-dev \
  && a2enmod rewrite speling \
  && docker-php-ext-configure gd --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd pdo_mysql

COPY --from=composer /usr/bin/composer /usr/bin/composer

ENV APACHE_DOCUMENT_ROOT /var/www/app/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

ENV TZ Asia/Tokyo
WORKDIR /var/www/app
