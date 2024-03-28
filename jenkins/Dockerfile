FROM jenkins/jenkins

# Install Docker
USER root
RUN groupadd docker --gid 1001
RUN apt-get update 

# Install Docker Compose
RUN apt-get install -y docker-compose

# Install php-xml
RUN apt-get install -y php-cli php-xml

# Install mbstring for PHPunit
RUN apt-get install -y php-mbstring

# Install php-mysql
RUN apt-get install php-mysqlnd

RUN usermod -aG docker jenkins

# Set user
USER jenkins