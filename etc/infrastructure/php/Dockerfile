FROM php:8.1-fpm
RUN apt-get update && apt-get install -y \
        libicu-dev vim redis-tools git libxml2-dev wget zip unzip mc htop nano
RUN apt-get update -y && apt-get install -y libwebp-dev libjpeg-dev libjpeg62-turbo-dev libpng-dev libxpm-dev \
    libfreetype6-dev zlib1g-dev libzip-dev mariadb-client \
    && docker-php-ext-install \
        mysqli pdo pdo_mysql \
    && docker-php-ext-install \
        soap zip
RUN pecl install redis && docker-php-ext-enable redis
RUN docker-php-ext-configure gd --with-jpeg --with-webp
RUN docker-php-ext-install gd
ADD ./conf.d/  /usr/local/etc/php/conf.d/
# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# Chage permissions
RUN chown -R www-data:www-data /var/www
#WORKDIR .
#RUN composer install
