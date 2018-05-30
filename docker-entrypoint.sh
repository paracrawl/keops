#!/bin/bash 
#ROOT=$(readlink -f $(dirname $(readlink -f $0))/..)

#$ROOT/scripts/startup.sh


#[ -d $ROOT/logs ] || mkdir $ROOT/logs

#touch $ROOT/logs/celery-worker.log $ROOT/logs/errors.log $ROOT/logs/redis.log
#tail -f $ROOT/logs/celery-worker.log $ROOT/logs/errors.log $ROOT/logs/redis.log
echo "HELLO KEOPS!"
echo "STARTING SERVICES..."


echo "STARTING POSTGRESQL..."
service postgresql restart

echo "STARTING PHP..."
service php7.2-fpm stop && service php7.2-fpm start

echo "STARTING NGINX..."
service nginx stop && service nginx start

echo "SERVICES STARTED!"

tail -f /var/log/nginx/error.log /var/log/nginx/access.log
/bin/bash