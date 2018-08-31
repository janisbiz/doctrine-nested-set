FROM php:7.1

# Setup the Composer installer
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
    && curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
    && php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }"

# Install Composer
RUN php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --snapshot && rm -rf /tmp/composer-setup.php

WORKDIR "/var/www"

ARG LOCAL_USER_ID=1000
ARG LOCAL_GROUP_ID=1000

RUN pecl install xdebug

RUN set -xe && \
  groupadd -g $LOCAL_GROUP_ID -o -f user && \
  useradd --shell /bin/bash -u $LOCAL_USER_ID -g user -o -c "" -m user

RUN apt-get update
RUN apt-get install -y git zip

RUN docker-php-ext-install pdo_mysql
