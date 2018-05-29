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

### Install PHP on nginx ###

Create a config file for Keops:

```
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

```
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

### Install PostgreSQL ### 

After installing with ```sudo apt-get install postgresql```,  connect with the ```postgres`` ` user to create a new DB and then access into it:

```
 sudo -i -u postgres
 createdb keopsdb
 psql keopsdb
 ```
 
 