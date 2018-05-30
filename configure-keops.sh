#!/bin/bash 

echo "START CONFIG"

cp /opt/keops/keops.conf /etc/nginx/sites-available/keops.conf && cd /etc/nginx/sites-enabled && rm default && sudo ln -s /etc/nginx/sites-available/keops.conf && cd /opt/keops

echo "extension=pdo_pgsql.so" >> /etc/php/7.2/fpm/php.ini
echo "extension=pgsql.so" >> /etc/php/7.2/fpm/php.ini

sudo -u postgres service postgresql start && sudo -u postgres createdb keopsdb && sudo -u postgres psql keopsdb < keopsdb_init.sql 

service postgresql restart && service php7.2-fpm restart && service nginx restart

echo "CONFIG COMPLETE"


