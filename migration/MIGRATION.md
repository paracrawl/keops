# Migrating to a new version of KEOPS

This document provides a guide to migrate from a previous version of KEOPS to a new one. We use [SQLAlchemy](https://sqlalchemy-migrate.readthedocs.io/en/latest/) and [Alembic](https://alembic.sqlalchemy.org/en/latest/), which are capable of generating migrations automatically and managing their deployment.

## 1. Installing required software
First, download the new version of KEOPS. 

```bash
git clone http://gitlab.prompsit.com/paracrawl/keops.git
```

Once downloaded, the simplest way of running KEOPS is using Docker:

    docker-compose up -d

<small>The [Installation guide](/INSTALLATION.md) provides detailed instructions to deploy KEOPS.</small>

That command will launch two containers: `keopsdb` and `keops`. The first one hosts the database and the second one hosts KEOPS.

Both SQLAlchemy and Alembic run on Python. They can be installed easily via `pip`. You will also need `psycopg2` as a driver to connect to the database. Install them on the `keops` container:

    apt install python python-psycopg2 python-pip
    pip install sqlalchemy alembic

## 2. Getting ready for migrating

All the necessary files for migrating are available in `/opt/keops/migration`, inside of the `keops` container:

* migrations.zip
* models.py

Access the command line of the container:
```shell
# docker exec -it keopsdb bash
```

Unzip the contents of the compressed file in the migration folder:

```shell
# cd /opt/keops/migration
# unzip migration.zip
```

Now, your migration folder should look like this:

* migrations.zip
* models.py
* alembic.ini
* alembic/
    * env.py
    * versions/

Edit `alembic.ini` to match the credentials of your actual database (line 38):

    sqlalchemy.url = postgres+psycopg2://postgres:PASSWORD_FOR_POSTGRES@keopsdb/keopsdb

Because of the way KEOPS is initialized, the user `postgres` is the owner of the tables and other entities related to KEOPS on the database. We advise you to use this user in Alembic. Otherwise, you may run into permission errors.

For the following step, quit the container terminal:
```shell
# exit
```

## 3. Safety first
Before performing a migration, back up your current database. First, dump your data from your previous database into a file. 

Run this command wherever your previous database is deployed. You should get a file which contains the backup of the database (`keopsdb.bak.sql`).

```shell
# sudo -u postgres pg_dump keopsdb > keopsdb.bak.sql
```

Copy the generated file in your home folder. Now, you can restore the data to the database present in the container `keopsdb`.

```shell
# docker exec keopsdb sudo -u postgres dropdb keopsdb

# docker exec keopsdb sudo -u postgres createdb keopsdb

# docker cp ~/keopsdb.bak.sql keopsdb:/opt/keopsdb.bak.sql

# docker exec keopsdb sudo -u postgres psql -d keopsdb -f /opt/keopsdb.bak.sql
```

This replicates your previous database in the new KEOPS container. In the next step, we will migrate your data to the new version of KEOPS.

## 4. Migrating

Set the database to the beginning of the migration:

    alembic stamp 39f0aae0582b

And, finally, migrate:

    alembic upgrade head

A set of operations will run sequentially until the end of the migration. Then, the database is ready to be used with the new version of KEOPS.

KEOPS provides a __root__ user which has access to all data. Please, once
you migrate your database, __update root user password__. This user is
stored in the _USERS_ table. A new password can be generated using PHP:

	php -r 'echo password_hash("[NEW PASSWORD]", "PASSWORD_DEFAULT");'

Or using Python (`pip install bcrypt` required):

	python3 -c 'import bcrypt, sys; hash = bcrypt.hashpw("[NEW PASSWORD]".encode("utf-8"), bcrypt.gensalt()); print(hash.decode("ascii"))'

In both cases, replace _[NEW PASSWORD]_ with your actual new password.

Since the migration modifies and creates tables, you need to grant the corresponding permissions again to the user `keopsdb`:

```sql
REVOKE CONNECT ON DATABASE keopsdb FROM PUBLIC;
GRANT CONNECT ON DATABASE keopsdb TO keopsdb;
ALTER DEFAULT PRIVILEGES FOR USER keopsdb IN SCHEMA keopsdb GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES TO keopsdb;
GRANT USAGE ON SCHEMA keopsdb TO keopsdb;
ALTER DEFAULT PRIVILEGES GRANT ALL ON SEQUENCES TO keopsdb;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA keopsdb TO keopsdb; 
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA keopsdb TO keopsdb;
```
