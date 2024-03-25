FROM php:8.2-apache
FROM jenkins/jenkins

# Install Docker
USER root
RUN groupadd docker --gid 1001
RUN apt-get update 

# Install Docker Compose
RUN apt-get install -y docker-compose

# Install php-xml
RUN apt-get install -y php-cli php-xml
RUN usermod -aG docker jenkin

# Set user
USER jenkins

RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli

COPY /src /var/www/html/

EXPOSE 3306/tcp