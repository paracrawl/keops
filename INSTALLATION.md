# KEOPS (Keen Evaluator Of Parallel Sentences)

KEOPS (Keen Evaluation Of Parallel Sentences) provides a complete tool for manual evaluation of parallel sentences.

It can be run inside a Docker or manually installed.

## Get KEOPS ##

Whether you use KEOPS with Docker or you install it locally, you will need the latest version of KEOPS:

```bash
git clone http://github.com/paracrawl/keops.git
```

## Dockerized version ##

All the needed files to dockerize KEOPS are provided. First, install Docker:

```
sudo apt-get install docker
```

KEOPS notifies evaluators about invitations and tasks via email. Please, provide credentials for the email account you are going to use to send messages to users. You also must provide the domain name of the mail server. You can set them in docker-compose.yaml:

```
keops:
    environment:
        - KEOPS_HELPER_HOST=[mail.example.com]
        - KEOPS_HELPER_EMAIL=[noreply@example.com]
        - KEOPS_HELPER_PASSWORD=[examplepassword]
```

The system administrator has the responsibility of deploying and mantaining the email address. KEOPS will not provide any.

Whereas Admins can view and manage their own projects and tasks, KEOPS provides a `root` user which is capable of displaying all the projects, tasks, users (and so on) saved on KEOPS. Please, do not confuse this `root` with the Linux `root`. KEOPS creates this user __only in the application context__, not at system level.

The default password for `root` on KEOPS is `root`. Before launching KEOPS, please change it in `docker-compose.yml`:

    keopsdb:
        environment:
            - KEOPS_ROOT_PASSWORD=[PASWORD]

__Replace [PASSWORD] with your new password__

Now, launch KEOPS:

```
cd keops
docker-compose up -d
```

This will run, on the background, two containers:
* __keops__ contains the KEOPS server
* __keopsdb__ contains the PostgreSQL Database used by KEOPS

Once the containers are running, KEOPS is available on __port 8080__.

If you already have a running database and you want to use it with KEOPS, use the file `docker-compose.yml` to indicate your database. That is, set the following environment variables in the service `keops`:

```
KEOPS_DB_NAME
KEOPS_DB_HOST
KEOPS_DB_USER
KEOPS_DB_PASS
KEOPS_DB_PORT
```
And launch KEOPS __without the default database__:

```
docker-compose up -d --no-deps keops
```

Please note that if you use a custom database, you will need to set it up manually. The file `keopsdb_init.sql` performs all the necessary operations to have a functional database with KEOPS. It will create a schema called `keops` in the database you provided and a user called `keopsdb`. It also performs user privilege operations, but __only on the _keops_ schema__.

Alternatively, you can build and run the container manually:

```
cd keops
sudo docker network create keops
sudo docker build -t keopsdocker .
```

Once built, run it:

```
sudo docker run -d --network=keops -p OUT_PORT:80 --name keops keopsdocker:latest
```

With "OUT_PORT" being the port where Keops is going to be reachable (usually, 80)

This KEOPS container does not provide a database. You can build the database container manually too:

```
cd keops
sudo docker build -f Dockerfile-db -t keopsdb .
```

And run it:

```
sudo docker run -d --name keopsdb --network=keops keopsdb:latest
```

## Local installation ##
Instead of running a Docker container, you can deploy KEOPS locally.

### Requirements ###

To install KEOPS locally in your machine, the following packages are needed.  They can be installed with ```sudo apt-get install```:

* postgresql-10
* php7.2
* php7.2-pgsql
* php7.2-fpm 
* nginx
* ca-certificates

### Install and configure PHP on nginx ###

Create a config file for Keops:

```bash
sudo joe /etc/nginx/sites-available/keops.conf
```

In this file, insert the following modifying the path to the root folder
(the path where you git-cloned Keops):

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
                fastcgi_pass unix:/run/php/php7.2-fpm.sock;
        }
}

 ```
 
 Then, make available the site for nginx and restart the server:

```bash
cd /etc/nginx/sites-enabled
sudo rm default
sudo ln -s /etc/nginx/sites-available/keops.conf
sudo service nginx restart
```

and you will see the result in http://localhost:80
(Please note that if the port is already in use, you may need to change it or stop other services using it)

Also, for PostgreSQL pdo, add the following lines to ``` /etc/php/7.2/fpm/php.ini ```:

```
extension=pdo_pgsql
extension=pgsql
```

Access and error logs are located in ```/var/log/nginx/ ```:

```bash
tail -f /var/log/nginx/error.log
```

### Install and configure PostgreSQL ###

After installation,  start the PostgreSQL service and connect with the ```postgres`` ` user to create a new DB and then access into it:

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

CREATE TYPE keopsdb.role AS ENUM ('ADMIN', 'PM', 'USER', 'root');
CREATE TYPE keopsdb.taskstatus AS ENUM ('PENDING', 'STARTED', 'DONE');
CREATE TYPE keopsdb.label AS ENUM ('P','V','L','A','T','MT','E','F');
CREATE TYPE keopsdb.evalmode AS ENUM ('VAL', 'ADE', 'FLU', 'RAN');

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
    DESCRIPTION varchar(500),
    CREATION_DATE timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ACTIVE boolean NOT NULL DEFAULT TRUE
);



CREATE TABLE keopsdb.CORPORA(
    ID serial PRIMARY KEY,
    NAME varchar(100) NOT NULL,
    SOURCE_LANG integer REFERENCES keopsdb.LANGS(ID),
    TARGET_LANG integer NOT NULL REFERENCES keopsdb.LANGS(ID),
    LINES integer,
    CREATION_DATE timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ACTIVE boolean NOT NULL DEFAULT TRUE,
    EVALMODE keopsdb.evalmode NOT NULL DEFAULT 'VAL',
    added_by integer references keopsdb.users(id)
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
    COMPLETED_DATE timestamp,
    SOURCE_LANG VARCHAR(5) REFERENCES keopsdb.LANGS(langcode),
    TARGET_LANG VARCHAR(5) NOT NULL REFERENCES keopsdb.LANGS(langcode),
    EVALMODE keopsdb.evalmode NOT NULL DEFAULT 'VAL',
    SCORE NUMERIC
);

CREATE TABLE keopsdb.SENTENCES(
    ID serial PRIMARY KEY,
    CORPUS_ID integer NOT NULL REFERENCES keopsdb.CORPORA(ID),
    SOURCE_TEXT varchar (5000) NOT NULL,
    SOURCE_TEXT_VECTOR tsvector NOT NULL,
    TYPE varchar(140),
    IS_SOURCE boolean,
    SYSTEM VARCHAR(140)
);

CREATE TABLE keopsdb.SENTENCES_TASKS(
    ID serial PRIMARY KEY,
    TASK_ID integer NOT NULL REFERENCES keopsdb.TASKS(ID),
    SENTENCE_ID integer NOT NULL REFERENCES keopsdb.SENTENCES(ID),
    EVALUATION varchar(140) NOT NULL DEFAULT 'P',
    CREATION_DATE timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    COMPLETED_DATE timestamp,
    TIME numeric
);

CREATE TABLE keopsdb.SENTENCES_PAIRING (
    id_1 integer NOT NULL REFERENCES keopsdb.sentences(id),
    id_2 integer NOT NULL REFERENCES keopsdb.sentences(id),
    PRIMARY KEY (id_1, id_2)
);

create table keopsdb.COMMENTS (
	pair integer references keopsdb.SENTENCES_TASKS(id),
	name varchar (140),
	value varchar (255),
	primary key (pair, name)
);

CREATE TABLE keopsdb.FEEDBACK (
	id serial NOT NULL,
	score integer NOT NULL,
	"comments" varchar(240) NULL,
	created timestamp NOT NULL DEFAULT NOW(),
	task_id integer NOT NULL references keopsdb.TASKS(id),
	user_id integer NOT NULL references keopsdb.USERS(id),
	PRIMARY KEY (id)
);

create table keopsdb.password_renew (
	token varchar(512) primary key,
	user_id integer unique not null references keopsdb.users(id),
	created_time timestamp not null DEFAULT CURRENT_TIMESTAMP
);

insert INTO keopsdb.langs (langcode, langname) values ('bg','Bulgarian'), ('cs', 'Czech'), ('ca', 'Catalan'),  ('da', 'Danish'), ('de', 'German'), 
('el', 'Greek'), ('en', 'English'), ('es', 'Spanish'), ('et', 'Estonian'), ('fi', 'Finnish'), ('fr', 'French'), ('ga', 'Irish'), ('gl', 'Galician'), 
('hr', 'Croatian'), ('hu', 'Hungarian'), ('is', 'Icelandic'), ('it', 'Italian'),  ('lt', 'Lithuanian'), ('lv', 'Latvian'), ('mt', 'Maltese'), 
('nl', 'Dutch'), ('nn', 'Norwegian - nynorsk'), ('no', 'Norwegian - bokmal'), ('pl', 'Polish'), ('pt', 'Portuguese'),  ('ro', 'Romanian'), 
('sk', 'Slovak'), ('sl', 'Slovenian'), ('sv', 'Swedish');


REVOKE CONNECT ON DATABASE keopsdb FROM PUBLIC;
GRANT CONNECT ON DATABASE keopsdb TO keopsdb;
ALTER DEFAULT PRIVILEGES FOR USER keopsdb IN SCHEMA keopsdb GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES TO keopsdb;
GRANT USAGE ON SCHEMA keopsdb TO keopsdb;
ALTER DEFAULT PRIVILEGES GRANT ALL ON SEQUENCES TO keopsdb;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA keopsdb TO keopsdb; 
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA keopsdb TO keopsdb;


insert into keopsdb.users (name, email, role, password) values ('admin', 'admin@admin.com', 'ADMIN', '$2y$10$dbba8ArdKTe9Uxt7rkGwKOrfX5EpI8SO2VheEnnfoYu4kmVFtQjW2');
insert into keopsdb.users (name, email, role, password) values ('root', 'root', 'root', '$2y$10$rUSV39GMx2fy9XTFT.CdVOlKuNpGxDKazJyzqRS8n3D0Z9mKgBdRi');

/* No stopwords 
update pg_catalog.pg_ts_dict set dictinitoption = regexp_replace(dictinitoption, ', stopwords = ''(.*)''', ''); */
CREATE TEXT SEARCH DICTIONARY public.simple_dict ( TEMPLATE = pg_catalog.simple );

CREATE OR REPLACE FUNCTION lower_if_text(e anyelement) RETURNS anyelement AS $$
    BEGIN
        IF pg_typeof(e) = 'character varying'::regtype then
            return lower(e);
        else
            return e;
        end if;
    END
$$ LANGUAGE plpgsql;

alter role keopsdb set search_path to keopsdb, public;
set search_path = keopsdb, public;
```

At this point, you should be able to log into Keops with user "admin@admin.com" and password "admin".
(As pointed below, it's adviced to log as this user the first time to create a new administrator user, and then remove the default "admin" user for security.)

## Notes ##

Please note that a default user "admin@admin.com" with password "admin" and ADMIN privileges is created.

Is adviced to log as this user the first time to create a new administrator user, and then remove the default "admin" user for security.

Is also recommended to change the PostgreSQL user's password from "PASSWORD_FOR_USER_KEOPS" to a secure one, both in your DB and in ```WORKDIR/keops/resources/db/keopsdb.class.php```

Nginx log can be read, when Keops running in Docker, with the command:

``` 
sudo docker logs -f keops
```

### CAPTCHA API Keys ###
API Keys for the CAPTCHA present in the contact form can be managed using
the following Google acccount:

* Email: `keops.prompsit@gmail.com`
* Password: DE7UsQVyVUhV#t*N 
