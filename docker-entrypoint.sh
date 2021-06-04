#!/bin/bash 
#ROOT=$(readlink -f $(dirname $(readlink -f $0))/..)

#$ROOT/scripts/startup.sh


#[ -d $ROOT/logs ] || mkdir $ROOT/logs

#touch $ROOT/logs/celery-worker.log $ROOT/logs/errors.log $ROOT/logs/redis.log
#tail -f $ROOT/logs/celery-worker.log $ROOT/logs/errors.log $ROOT/logs/redis.log
echo "HELLO KEOPS!"
echo "STARTING SERVICES..."

cat <<-ENDOFMESSAGE |
<?php
class keopsdb extends PDO{  
 private \$dbname = "$KEOPS_DB_NAME";
 private \$host = "$KEOPS_DB_HOST";
 private \$user = "$KEOPS_DB_USER";
 private \$pass = "$KEOPS_DB_PASS";
 private \$port = $KEOPS_DB_PORT;
 private \$dbh;
 
 public function __construct(){
     
    try {
      \$this->dbh = parent::__construct("pgsql:host=\$this->host;port=\$this->port;dbname=\$this->dbname;user=\$this->user;password=\$this->pass");
    } catch (PDOException \$ex) {
      throw new Exception("DB connection error: " . \$ex->getMessage());
    }
  }

  public function close_conn() {
    \$this->dbh = null;
  }

}
ENDOFMESSAGE
cat >/opt/keops/resources/db/keopsdb.class.php

echo "STARTING PHP..."
service php7.2-fpm stop && sudo -E /etc/init.d/php7.2-fpm start

echo "STARTING NGINX..."
service nginx stop && service nginx start

echo "STARTING MEMCACHED..."
nohup memcached -u memcached &

echo "SERVICES STARTED!"

if ! { [ -z "$KEOPS_DB_HOST" ] || [ -z "$KEOPS_DB_PORT" ] || [ -z "$POSTGRESPASSWORD" ] || [ -z "$KEOPS_DB_NAME" ]; };
then
  >&2 echo "APPLYING DATABASE MIGRATION..."

  i=0
  max=10
  until PGPASSWORD=$POSTGRESPASSWORD psql -h "$KEOPS_DB_HOST" -p "$KEOPS_DB_PORT" -U "postgres" -c '\q'; do
    >&2 echo "Waiting for Postgres... ($((i+1))/$max)"
    ((i++))
    sleep 5

    if [ "$i" -eq "$max" ]
    then
      >&2 echo "Could not connect to database. Server running anyway..."
      tail -f /var/log/nginx/error.log /var/log/nginx/access.log
    fi
  done

  cd /opt/keops/automigration/
  cp alembic.ini.template alembic.ini
  echo "sqlalchemy.url = postgresql+psycopg2://postgres:$POSTGRESPASSWORD@$KEOPS_DB_HOST:$KEOPS_DB_PORT/$KEOPS_DB_NAME" >> alembic.ini
  current=`alembic current`
  if [ -z "$current" ]
  then
    >&2 echo "NO ALEMBIC VERSION DETECTED. SETTING UP ALEMBIC HEAD TO LATEST UPDATE"
    alembic stamp head
    >&2 echo "HEAD IS NOW: $(alembic heads)"
    >&2 echo "IF YOUR DATABASE SCHEMA IS NOT UPDATED, YOU SHOULD DO THAT MANUALLY"
  else
    >&2 echo "ALEMBIC VERSION DETECTED. UPGRADING..."
    alembic upgrade head
    PGPASSWORD=$POSTGRESPASSWORD psql -h "$KEOPS_DB_HOST" -p "$KEOPS_DB_PORT" -U "postgres" -f fix-permissions.sql keopsdb
  fi

  cd /
fi

tail -f /var/log/nginx/error.log /var/log/nginx/access.log 
