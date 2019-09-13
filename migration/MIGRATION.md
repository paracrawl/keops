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

    sqlalchemy.url = postgres+psycopg2://keopsdb:@localhost/keopsdb

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

You must also add `include_schemas=True` every time the context is configured. This happens near lines 46 and 68.

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

Set the reference point of Alembic:

    alembic stamp head

Then, you can download this [set of migrations](migrations.zip) to migrate from the previous version of KEOPS to the current one. The compressed file contains a folder called _versions_. The Python files inside it must be extracted to the _versions_ folder created by Alembic.

If you run `alembic history`, you should get something like this:

    9a12a31e8cc2 -> 55f3d231b630 (head), Remove languages from Projects
    8fe0f67a2fad -> 9a12a31e8cc2, Move language data to tasks
    2313323396dd -> 8fe0f67a2fad, Add language columns to task
    0c404977b0b9 -> 2313323396dd, Add sentences vectors
    b3501b4c1a59 -> 0c404977b0b9, Fill comments table
    ce2dd22be7de -> b3501b4c1a59, Add and fill comments table
    <base> -> ce2dd22be7de, Init

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