#!/bin/bash 
set -e

echo "STARTING CONFIG..."

PHP_VER=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')

cp /opt/keops/keops.conf /etc/nginx/sites-available/keops.conf && \
    cd /etc/nginx/sites-enabled && \
    rm -f default keops.conf && \
    ln -s /etc/nginx/sites-available/keops.conf && \
    cd /opt/keops

echo "extension=pdo_pgsql" >> /etc/php/${PHP_VER}/fpm/php.ini
echo "extension=pgsql" >> /etc/php/${PHP_VER}/fpm/php.ini
echo "variables_order=EGPCS" >> /etc/php/${PHP_VER}/fpm/php.ini
echo "error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT" >> /etc/php/${PHP_VER}/fpm/php.ini
echo "display_errors = Off" >> /etc/php/${PHP_VER}/fpm/php.ini
echo "log_errors = On" >> /etc/php/${PHP_VER}/fpm/php.ini

sed -i 's|^listen = .*|listen = /run/php/php-fpm.sock|' /etc/php/${PHP_VER}/fpm/pool.d/www.conf
echo "clear_env=no" >> /etc/php/${PHP_VER}/fpm/pool.d/www.conf

cd /opt/keops
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

php composer.phar update --no-interaction --no-dev

groupadd -f memcached
id -u memcached &>/dev/null || useradd -g memcached -d /dev/null -s /sbin/nologin memcached

echo "CONFIG COMPLETE!"



