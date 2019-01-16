#!/bin/bash 
#ROOT=$(readlink -f $(dirname $(readlink -f $0))/..)

#$ROOT/scripts/startup.sh


#[ -d $ROOT/logs ] || mkdir $ROOT/logs

#touch $ROOT/logs/celery-worker.log $ROOT/logs/errors.log $ROOT/logs/redis.log
#tail -f $ROOT/logs/celery-worker.log $ROOT/logs/errors.log $ROOT/logs/redis.log
echo "HELLO KEOPS!"
echo "STARTING SERVICES..."



if [[ -z "${DB_REMOTE}" ]]; then
  echo "STARTING POSTGRESQL..."
  #service postgresql restart
  nohup bash -c 'sudo -u postgres /usr/lib/postgresql/10/bin/postgres -D /var/lib/postgresql/10/main -c "config_file=/etc/postgresql/10/main/postgresql.conf" &'
else
{  cat <<-ENDOFMESSAGE
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
} | cat >/opt/keops/resources/db/keopsdb.class.php

fi

echo "STARTING PHP..."
service php7.2-fpm stop && service php7.2-fpm start

echo "STARTING NGINX..."
service nginx stop && service nginx start

echo "SERVICES STARTED!"

tail -f /var/log/nginx/error.log /var/log/nginx/access.log 
