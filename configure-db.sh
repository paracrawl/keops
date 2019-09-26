#!/bin/bash 

echo "STARTING CONFIG..."

echo "host  all all 0.0.0.0/0   md5" >> /etc/postgresql/10/main/pg_hba.conf
echo "listen_addresses = '*'" >> /etc/postgresql/10/main/postgresql.conf

service postgresql start
sudo -u postgres createdb keopsdb
sudo -u postgres psql keopsdb < /opt/keopsdb_init.sql
service postgresql stop

echo "CONFIG COMPLETE!"