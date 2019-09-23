CREATE USER keopsdb PASSWORD 'PASSWORD_FOR_USER_KEOPS';
CREATE SCHEMA keopsdb;

CREATE TYPE keopsdb.role AS ENUM ('ADMIN', 'PM', 'USER');
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
    EVALMODE keopsdb.evalmode NOT NULL DEFAULT 'VAL'
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

/* No stopwords 
update pg_catalog.pg_ts_dict set dictinitoption = regexp_replace(dictinitoption, ', stopwords = ''(.*)''', ''); */
CREATE TEXT SEARCH DICTIONARY public.simple_dict ( TEMPLATE = pg_catalog.simple );

alter role keopsdb set search_path to keopsdb, public;
set search_path = keopsdb, public;