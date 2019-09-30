#!/bin/bash
raw=$1
hash=`docker exec keops php -r 'echo password_hash($argv[1],  PASSWORD_DEFAULT);' $raw`
docker exec keopsdb sudo -u postgres psql -d keopsdb -c "update keopsdb.users set password = '$hash' where role='root'"
