FROM php:8.2-fpm

# Arguments defined in docker-compose.yml
ARG USERNAME=www-data
ENV uid=1000

# Update & upgrade apt
RUN apt-get update && apt-get install -y
RUN apt install -y apt-utils

# Install dependencies
RUN apt install -y libicu-dev vim redis-tools git libxml2-dev wget ca-certificates zip unzip mc htop nano && \
    pecl channel-update pecl.php.net
RUN apt-get update -y && apt-get install -y libwebp-dev libjpeg-dev libjpeg62-turbo-dev libpng-dev libxpm-dev \
    libfreetype6-dev zlib1g-dev libzip-dev mariadb-client

# Install php extensions
RUN docker-php-ext-install \
bcmath \
intl \
gd \
opcache \
mysqli \
pdo \
pdo_mysql \
soap \
zip

RUN pecl install redis && docker-php-ext-enable redis
RUN apt-get update && apt-get install -y libicu-dev && docker-php-ext-install intl
RUN docker-php-ext-configure gd --with-jpeg --with-webp

ADD ./conf.d/  /usr/local/etc/php/conf.d/

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY ./conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN usermod -G www-data,root -u $uid -d /home/$USERNAME $USERNAME
RUN mkdir -p /home/$USERNAME/.composer && \
    chown -R $USERNAME:$USERNAME /home/$USERNAME

# [Optional] Add sudo support. Omit if you don't need to install software after connecting.
RUN apt-get install -y sudo \
    && echo $user ALL=\(root\) NOPASSWD:ALL > /etc/sudoers.d/$USERNAME \
    && chmod 0440 /etc/sudoers.d/$USERNAME

# Set enviroment vars
ENV APP_DIR=/var/www/crm/

WORKDIR ${APP_DIR}

# Chage permissions
RUN chown www-data:www-data /var/www/crm/ -R


ENV COMPOSER_ALLOW_SUPERUSER=1
#RUN composer install

