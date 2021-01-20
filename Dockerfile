FROM php:8.0-alpine
COPY . /var/www/tdd-improving
WORKDIR /var/www/tdd-improving
COPY ./docker/php.ini $PHP_INI_DIR/php.ini
RUN apk --no-cache add pcre-dev ${PHPIZE_DEPS} \ 
  && pecl install xdebug-3.0.2 \
  && docker-php-ext-enable xdebug \
  && apk del pcre-dev ${PHPIZE_DEPS}
RUN wget https://getcomposer.org/composer.phar -O /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer
ENTRYPOINT ["tail", "-f", "/dev/null"]
