#!/bin/bash 

echo "STARTING CONFIG..."

cp /opt/keops/keops.conf /etc/nginx/sites-available/keops.conf && cd /etc/nginx/sites-enabled && rm default && sudo ln -s /etc/nginx/sites-available/keops.conf && cd /opt/keops

echo "extension=pdo_pgsql" >> /etc/php/7.2/fpm/php.ini
echo "extension=pgsql" >> /etc/php/7.2/fpm/php.ini

cd /opt/keops
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

php composer.phar install

groupadd memcached
useradd -g memcached -d /dev/null -s /sbin/nologin memcached

echo "CONFIG COMPLETE!"


