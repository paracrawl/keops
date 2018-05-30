#!/bin/bash 

echo "START CONFIG"

cp /opt/keops/keops.conf /etc/nginx/sites-available/keops.conf && cd /etc/nginx/sites-enabled && rm default && ln -s /etc/nginx/sites-available/keops.conf && cd /opt/keops

echo "extension=pdo_pgsql.so" >> /etc/php/7.2/fpm/php.ini
echo "extension=pgsql.so" >> /etc/php/7.2/fpm/php.ini

su postgres &&  service postgresql start && createdb keopsdb && psql keopsdb < keopsdb_init.sql && exit

service postgresql restart && service php7.2-fpm restart && service nginx restart

echo "CONFIG COMPLETE"


