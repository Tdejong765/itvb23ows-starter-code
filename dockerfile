FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    wget \
    unzip

# Install Docker
RUN wget https://download.docker.com/linux/static/stable/x86_64/docker-20.10.11.tgz \
    && tar -xvf docker-20.10.11.tgz \
    && mv docker/* /usr/bin/ \
    && rm -rf docker-20.10.11.tgz docker

# Install Docker Compose
RUN wget https://github.com/docker/compose/releases/download/1.29.2/docker-compose-Linux-x86_64 -O /usr/local/bin/docker-compose \
    && chmod +x /usr/local/bin/docker-compose

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHPUnit
RUN wget https://phar.phpunit.de/phpunit.phar && \
    chmod +x phpunit.phar && \
    mv phpunit.phar /usr/local/bin/phpunit

RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli

COPY . /var/www/html/

EXPOSE 3306/tcp