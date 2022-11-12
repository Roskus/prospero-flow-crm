FROM php:8.1-apache

RUN docker-php-ext-install pdo_mysql mysqli

RUN a2enmod rewrite;

RUN APACHE_DOCUMENT_ROOT=/var/www/html/public \
    && sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" $APACHE_CONFDIR/sites-available/*.conf \
    && sed -ri -e "s!/var/www/!${APACHE_DOCUMENT_ROOT}!g" $APACHE_CONFDIR/apache2.conf $APACHE_CONFDIR/conf-available/*.conf
