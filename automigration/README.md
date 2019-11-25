# Automigration files

This folder contains the necessary files for automigration. This means that the KEOPS container will detect if a new version of the database is
available and, then, update it.

The migration files should be attached in this folder, under the
`alembic/versions` directory. Note that these migration files should also be
contained in a zip file under the `/migration` directory, for manual update. 

## Automigration method

When you run the KEOPS container, it will try to update the database schema
automatically. It expects a database called **keopsdb** to be set up in the
PostgreSQL server provided by the environment variables in
`docker-compose.yml`:

```
    environment: 
      - KEOPS_DB_NAME=keopsdb
      - KEOPS_DB_HOST=keopsdb
      - KEOPS_DB_USER=keopsdb
      - KEOPS_DB_PASS=PASSWORD_FOR_USER_KEOPS
      - KEOPS_DB_PORT=5432
      - POSTGRESPASSWORD=PASSWORD_FOR_POSTGRES
```

This is what could happen when updating the database:

* KEOPS container is running but the database is offline
  * The container keeps running without attempting an update
* KEOPS container is running but cannot authenticate in the database server
  * The container keeps running without attempting an update
* The database is running but it does not contain any alembic version
  * If no alembic version is provided, there is not a certain way of knowing the state of the database schema. In this case, we assume the database has an updated schema and set up the alembic version to the latest one.
* The database is running and has an alembic version, which is the latest one
  * The KEOPS container runs normally, without updating anything, but fixes the database permissions anyway.
* The database is running and has an alembic version, which is **not** the latest one
  * The KEOPS container launches an update of the database, and fixes the database permissions afterwards.

Fixing database permissions is necessary because an update could create new tables. Since these tables are created with the `postgres` users, they could not be accessed by the `keopsdb` users unless we grant those permissions. The exact commands run for this matter can be reviewed in <a href="fix-permissions.sql">fix-permissions.sql</a>