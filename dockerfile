FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    wget \
    unzip

#install docker-compose
RUN apt-get install -y docker-compose

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHPUnit
RUN wget https://phar.phpunit.de/phpunit.phar && \
    chmod +x phpunit.phar && \
    mv phpunit.phar /usr/local/bin/phpunit

RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli

COPY /src /var/www/html/

EXPOSE 3306/tcp