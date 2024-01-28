FROM php:7.4-apache

RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli

COPY /src /var/www/html/

EXPOSE 3306/tcp