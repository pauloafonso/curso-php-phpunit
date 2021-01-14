FROM php:8.0-alpine
COPY . /var/www/tdd-improving
WORKDIR /var/www/tdd-improving
RUN wget https://getcomposer.org/composer.phar -O /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer