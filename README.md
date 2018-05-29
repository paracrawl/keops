# KEOPS (Keen Evaluator Of Parallel Sentences)

KEOPS (Keen Evaluation Of Parallel Sentences) project provides a complete tool for manual evaluation of parallel sentences.

## Requirements ##

The following packages are needed.  They can be installed with ```sudo apt-get install```:

* postgresql
* php7.0
* php7.0-pgsql
* php7.0-fpm
* nginx

##  Preparing environment ##

### Install and configure PHP on nginx ###

Create a config file for Keops:

```bash
joe /etc/nginx/sites-available/keops.conf
```

In this file, insert the following modifying the path to the root folder:

```
## Main "keops" server block.
server {
        listen         80;
        server_name    keops.com;
        root           /PATH/TO/dev/keops/;
        index          index.php;

        ### send all requests to Wildfly
        location ~ \.php$ {
                client_max_body_size    100m;
                client_body_buffer_size 1m;

                #If a file isn’t found, 404
                try_files $uri =404; 

                #Include Nginx’s fastcgi configuration
                include /etc/nginx/fastcgi.conf;

                #Look for the FastCGI Process Manager at this location 
                fastcgi_pass unix:/run/php/php7.0-fpm.sock;
        }
}

 ```
 
 Then, make available the site for nginx and restart the server:

```bash
cd /etc/nginx/sites-enabled
rm default
sudo ln -s /etc/nginx/sites-available/keops.conf
sudo service nginx restart
```

Create the file index.php in /PATH/TO/dev/keops/ and you will see the result in http://localhost

Also, for PostgreSQL pdo, add the following lines to ``` /etc/php/7.0/fpm/php.ini ```:

```
extension=pdo_pgsql.so
extension=pgsql.so
```

Access and error logs are located in ```/var/log/nginx/ ```

### Install and configure PostgreSQL ### 

After installing with ```sudo apt-get install postgresql```,  start the PostgreSQL service and connect with the ```postgres`` ` user to create a new DB and then access into it:

```bash
 sudo -i -u postgres
 service postgresql start
 createdb keopsdb
 psql keopsdb
 ```
 
 Create a user for Keops:
 
 ```sql
 CREATE USER keopsdb PASSWORD 'PASSWORD_FOR_USER_KEOPS';
 ```
 
 Create types, tables, relations... for Keops:
 
 ```sql
CREATE SCHEMA keopsdb;

CREATE TYPE keopsdb.role AS ENUM ('ADMIN', 'STAFF', 'USER');
CREATE TYPE keopsdb.taskstatus AS ENUM ('PENDING', 'STARTED', 'DONE');
CREATE TYPE keopsdb.label AS ENUM ('P','V','L','A','T','MT','E','F');

CREATE TABLE keopsdb.USERS (
    ID serial PRIMARY KEY,
    NAME varchar (200) NOT NULL,
    EMAIL varchar (200) UNIQUE NOT NULL,
    CREATION_DATE timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ROLE keopsdb.role NOT NULL DEFAULT 'USER',
    PASSWORD varchar (200) NOT NULL,
    ACTIVE boolean NOT NULL DEFAULT TRUE
 );

CREATE TABLE keopsdb.TOKENS (
    ID serial PRIMARY KEY,
    ADMIN integer NOT NULL REFERENCES keopsdb.USERS (ID),
    TOKEN varchar (200) UNIQUE NOT NULL,
    EMAIL varchar (200) UNIQUE NOT NULL ,
    DATE_SENT timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    DATE_USED timestamp
);


CREATE TABLE keopsdb.LANGS (
    ID serial PRIMARY KEY,
    LANGCODE varchar (5) UNIQUE NOT NULL,
    LANGNAME varchar (50) UNIQUE NOT NULL
);

CREATE TABLE keopsdb.USER_LANGS (
    ID serial PRIMARY KEY,
    USER_ID integer  NOT NULL REFERENCES keopsdb.USERS (ID),
    LANG_ID integer  REFERENCES keopsdb.LANGS (ID)
);

CREATE TABLE keopsdb.PROJECTS(
    ID serial PRIMARY KEY,
    OWNER integer NOT NULL REFERENCES keopsdb.USERS(ID),
    NAME varchar(100) NOT NULL,
    SOURCE_LANG integer NOT NULL REFERENCES keopsdb.LANGS(ID),
    TARGET_LANG integer NOT NULL REFERENCES keopsdb.LANGS(ID),
    DESCRIPTION varchar(500),
    CREATION_DATE timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ACTIVE boolean NOT NULL DEFAULT TRUE
);

CREATE TABLE keopsdb.PROJECTS_USERS(
    ID serial PRIMARY KEY,
    EMAIL varchar(200) NOT NULL,
    USER_ID integer REFERENCES keopsdb.USERS(ID)
);

CREATE TABLE keopsdb.CORPORA(
    ID serial PRIMARY KEY,
    NAME varchar(100) NOT NULL,
    SOURCE_LANG integer NOT NULL REFERENCES keopsdb.LANGS(ID),
    TARGET_LANG integer NOT NULL REFERENCES keopsdb.LANGS(ID),
    LINES integer,
    CREATION_DATE timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ACTIVE boolean NOT NULL DEFAULT TRUE
);


CREATE TABLE keopsdb.TASKS(
    ID serial PRIMARY KEY,
    PROJECT_ID integer NOT NULL REFERENCES keopsdb.PROJECTS,
    ASSIGNED_USER integer REFERENCES keopsdb.USERS(ID),
    CORPUS_ID integer NOT NULL REFERENCES keopsdb.corpora,
    SIZE integer,
    STATUS keopsdb.taskstatus NOT NULL DEFAULT 'PENDING',
    CREATION_DATE timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ASSIGNED_DATE timestamp,
    COMPLETED_DATE timestamp
);


CREATE TABLE keopsdb.SENTENCES(
    ID serial PRIMARY KEY,
    CORPUS_ID integer NOT NULL REFERENCES keopsdb.CORPORA(ID),
    SOURCE_TEXT varchar (5000) NOT NULL,
    TARGET_TEXT varchar (5000) NOT NULL
);

CREATE TABLE keopsdb.SENTENCES_TASKS(
    ID serial PRIMARY KEY,
    TASK_ID integer NOT NULL REFERENCES keopsdb.TASKS(ID),
    SENTENCE_ID integer NOT NULL REFERENCES keopsdb.SENTENCES(ID),
    EVALUATION keopsdb.label NOT NULL DEFAULT 'P',
    CREATION_DATE timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    COMPLETED_DATE timestamp,
    COMMENTS varchar(1000)
);

insert INTO keopsdb.langs (langcode, langname) values
('de', 'German'), ('fr', 'French'), ('es', 'Spanish'), ('en', 'English'), ('it', 'Italian'), ('pt', 'Portuguese'), ('nl', 'Dutch'), ('pl', 'Polish'), ('cs', 'Czech'), ('ro', 'Romanian'), ('fi', 'Finnish'), ('lv', 'Latvian');

REVOKE CONNECT ON DATABASE keopsdb FROM PUBLIC;
GRANT CONNECT ON DATABASE keopsdb TO keopsdb;
ALTER DEFAULT PRIVILEGES FOR USER keopsdb IN SCHEMA keopsdb GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES TO keopsdb;
GRANT USAGE ON SCHEMA keopsdb TO keopsdb;
ALTER DEFAULT PRIVILEGES GRANT ALL ON SEQUENCES TO keopsdb;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA keopsdb TO keopsdb; 
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA keopsdb TO keopsdb;


insert into keopsdb.users (name, email, role, password) values ('admin', 'admin@admin.com', 'ADMIN', '$2y$10$dbba8ArdKTe9Uxt7rkGwKOrfX5EpI8SO2VheEnnfoYu4kmVFtQjW2');

```

Please note that a default user "admin" with password "admin" and ADMIN privileges is created.

Is adviced to log as this user the first time to create a new administrator user, and then remove the default "admin" user for security.

Is also recommended to change the PostgreSQL user's password from "PASSWORD_FOR_USER_KEOPS" to a secure one, both in your DB and in ```WORKDIR/keops/resources/db/keopsdb.class.php```
 