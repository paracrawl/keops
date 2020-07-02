#!/bin/bash

function usage {
    echo "USAGE: set-password user_email new_password"
    exit
}

if [ "$#" -ne 2 ]; then
    usage
fi

email=$1
new_password=$2

hash=`python3 -c 'import bcrypt, sys; hash = bcrypt.hashpw(sys.argv[1].encode("utf-8"), bcrypt.gensalt()); print(hash.decode("ascii"))' $new_password`

sudo -u postgres psql -d keopsdb -c "update keopsdb.users set password='$hash' where email='$email'"
