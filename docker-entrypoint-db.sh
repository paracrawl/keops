#!/bin/bash 
echo "HELLO KEOPSDB!"

echo "STARTING POSTGRES..."

service postgresql stop && service postgresql start
echo "ALTER USER postgres WITH PASSWORD '$1';" | sudo -u postgres psql keopsdb

echo "INSERT INTO keopsdb.users(id, name, email, creation_date, role, password, active) VALUES (-1, 'root', 'root', now(), 'root', $2, true);" | sudo -u postgres psql keopsdb

echo "SERVICES STARTED!"

tail -f /var/log/postgresql/*.log