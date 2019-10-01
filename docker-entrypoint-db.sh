#!/bin/bash 
echo "HELLO KEOPSDB!"

echo "STARTING POSTGRES..."

service postgresql stop && service postgresql start
echo "ALTER USER postgres WITH PASSWORD '$1';" | sudo -u postgres psql keopsdb

pip3 install bcrypt

hash=`python3 -c 'import bcrypt, sys; hash = bcrypt.hashpw(sys.argv[1].encode("utf-8"), bcrypt.gensalt()); print(hash.decode("ascii"))' $2`
echo "INSERT INTO keopsdb.users(id, name, email, creation_date, role, password, active) VALUES (-1, 'root', 'root', now(), 'root', '$hash', true) on conflict (id) do update set password = '$hash';" | sudo -u postgres psql keopsdb

echo "SERVICES STARTED!"

tail -f /var/log/postgresql/*.log