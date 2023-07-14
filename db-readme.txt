To apply changes in db for 20230714 new tasks:

sudo docker exec -it keopsdb /bin/bash
sudo -u postgres psql keopsdb
INSERT INTO keopsdb.langs (langcode, langname) values ('bo', 'Bosnian'), ('me', 'Montenegrin'), ('mk', 'Macedonian'), ('sq', 'Albanian'), ('sr', 'Serbian'), ('tk', 'Turkish');
CREATE TYPE keopsdb.mac_label AS ENUM ('WL', 'ML',  'MC', 'RC','MA', 'LQT', 'CBT', 'RT');
ALTER TYPE keopsdb.evalmode ADD VALUE  IF NOT EXISTS 'MONO';
ALTER TYPE keopsdb.evalmode ADD VALUE IF NOT EXISTS  'VAL_MAC';