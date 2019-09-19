# Migrating to a new version of KEOPS

This document provides a guide to migrate from a previous version of KEOPS to a new one. We use [SQLAlchemy](https://sqlalchemy-migrate.readthedocs.io/en/latest/) and [Alembic](https://alembic.sqlalchemy.org/en/latest/), which are capable of generating migrations automatically and managing their deployment.

## Getting started

In this section, we will get SQLAlchemy and Alembic ready for migrating to a new version of KEOPS.

### Installing required software
Both SQLAlchemy and Alembic run on Python. They can be installed easily via `pip`:

    pip install sqlalchemy
    pip intall alembic

### Configuring Alembic
Alembic manages migrations, letting us deploy them and revert them using the command line. To start using Alembic, we first have to initialize it:

    alembic init alembic

This command will generate the following structure in the folder you run it:

* alembic.ini
* alembic/
    * env.py
    * versions/

In order for Alembic to connect to your database, you must change this line in `alembic.ini`

    sqlalchemy.url = driver://user:pass@host:port/dbname

Change it to a valid URL which points to your database. You can read about valid URLs [here](https://docs.sqlalchemy.org/en/13/core/engines.html#database-urls). For example, this should work for a local deployment of KEOPS:

    sqlalchemy.url = postgres+psycopg2://keopsdb:PASWORD@localhost/keopsdb

Now, we must configure Alembic to recognize the structure of our database. You need to [download a model of the database](models.py). Place it in the root of the directory where you initilized Alembic. Now, you should have the following file structure:

* alembic.ini
* **models.py**
* alembic/
    * env.py
    * versions/

Then, replace the following line in `env.py`:

    target_metadata = None

With this code:

    import sys, os
    sys.path.insert(0, os.path.dirname(os.path.dirname(__file__)))
    from models import Base
    target_metadata = Base.metadata

You must also add `include_schemas=True` every time the context is configured (the function `context.configure` is called). This happens near lines 46 and 68.

Near line 46:

    context.configure(
        url=url, target_metadata=target_metadata,
        literal_binds=True,
        include_schemas=True
    )

Near line 68:

    context.configure(
        connection=connection,
        target_metadata=target_metadata,
        include_schemas=True
    )

We are ready to go! In the following steps, we will apply the corresponding migrations.

## Migrating
Migrations are Python files which contain two functions: `upgrade()` and `downgrade()`. As you could guess, the first function performs actions when the database is upgraded to a new version, and the second one performs actions when the database is downgraded to a previous version.

First, set the reference point of Alembic:

    alembic stamp head

Then, you can download this [set of migrations](migrations.zip) to migrate from the previous version of KEOPS to the current one. The compressed file contains a folder called _versions_. The Python files inside it must be extracted to the _versions_ folder created by Alembic.

If you run `alembic history`, you should get something like this:

    64d08233eefa -> 84c03f89212d (head), Add feedback table
    4674a545b1b0 -> 64d08233eefa, Set null evalmodes to VAL
    ee294d2ae5e3 -> 4674a545b1b0, Delete comments
    7aef4d0260ec -> ee294d2ae5e3, Adjust null values
    c0f84decbdac -> 7aef4d0260ec, Change evaluation to varchar
    b8bec0892316 -> c0f84decbdac, Add time column
    1ec316af4a8f -> b8bec0892316, Add corpora information
    241a1d4de4e1 -> 1ec316af4a8f, Add tasks information
    bea5d762a752 -> 241a1d4de4e1, Add sentence information
    552fb76000a5 -> bea5d762a752, Add vector
    c2116e8b1149 -> 552fb76000a5, Drop target text column
    f5ba8fc1c5da -> c2116e8b1149, Create sentences_pairing table
    9e93ad2ab9f9 -> f5ba8fc1c5da, Move languages to Tasks
    39f0aae0582b -> 9e93ad2ab9f9, Add Comments table
    <base> -> 39f0aae0582b, The begining

Finally, you migrate to the new version of KEOPS with:

    alembic upgrade HEAD

Please note that this only upgrades the database. You must download the new code from the repository in order to have a functional deployment.

### ⚠️ Permission issues
If you have any issues related to permissions after migrating, these lines restore the corresponding permissions to the database:

    REVOKE CONNECT ON DATABASE keopsdb FROM PUBLIC;
    GRANT CONNECT ON DATABASE keopsdb TO keopsdb;
    ALTER DEFAULT PRIVILEGES FOR USER keopsdb IN SCHEMA keopsdb GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES TO keopsdb;
    GRANT USAGE ON SCHEMA keopsdb TO keopsdb;
    ALTER DEFAULT PRIVILEGES GRANT ALL ON SEQUENCES TO keopsdb;
    GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA keopsdb TO keopsdb; 
    GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA keopsdb TO keopsdb;