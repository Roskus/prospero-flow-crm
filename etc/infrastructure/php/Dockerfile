FROM php:8.3-fpm

# Arguments defined in docker-compose.yml
#ARG USERNAME=www-data
#ENV uid=1000

# Update & upgrade apt Install dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    apt-utils libicu-dev vim redis-tools git libxml2-dev wget ca-certificates zip unzip mc htop nano curl \
    mariadb-client libpq-dev \
    && apt-get clean
RUN pecl channel-update pecl.php.net
RUN apt-get update -y && apt-get install -y --no-install-recommends libwebp-dev libjpeg-dev libjpeg62-turbo-dev  \
    libpng-dev libxpm-dev libfreetype6-dev zlib1g-dev libzip-dev libicu-dev \
    && apt-get clean

# Install php extensions
RUN docker-php-ext-install \
bcmath \
intl \
gd \
opcache \
mysqli \
pdo \
pdo_mysql \
pdo_pgsql \
pgsql \
soap \
zip \
intl

RUN pecl install redis && docker-php-ext-enable redis
RUN docker-php-ext-configure gd --with-jpeg --with-webp

ADD ./conf.d/  /usr/local/etc/php/conf.d/

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY ./conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Install msodbcsql17 and tools
RUN apt-get update && apt-get install -y gnupg2 && \
    curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - && \
    curl https://packages.microsoft.com/config/debian/10/prod.list > /etc/apt/sources.list.d/mssql-release.list && \
    apt-get update && ACCEPT_EULA=Y apt-get install -y unixodbc-dev msodbcsql17

# Install PHP extensions for SQL Server
RUN pecl install pdo_sqlsrv sqlsrv && docker-php-ext-enable pdo_sqlsrv sqlsrv

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
#RUN usermod -G www-data,root -u $uid -d /home/$USERNAME $USERNAME
#RUN mkdir -p /home/$USERNAME/.composer && \
#    chown -R $USERNAME:$USERNAME /home/$USERNAME

# [Optional] Add sudo support. Omit if you don't need to install software after connecting.
#RUN apt-get install -y sudo \
#    && echo $user ALL=\(root\) NOPASSWD:ALL > /etc/sudoers.d/$USERNAME \
#    && chmod 0440 /etc/sudoers.d/$USERNAME

ADD . /var/www/crm/
# Chage permissions
RUN chown www-data:www-data /var/www/crm/ -R
RUN chmod 775 /var/www/crm/ -R

#ENV COMPOSER_ALLOW_SUPERUSER=1
#RUN composer install

