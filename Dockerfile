FROM ubuntu:18.04

ARG DEBIAN_FRONTEND=noninteractive

RUN mkdir /opt/keops

COPY . /opt/keops

RUN echo "Europe/Madrid" > /etc/timezone

RUN apt-get update -q --fix-missing && \
    apt-get -y upgrade && \
    apt-get -y install  tzdata \	
	php7.2 \
	php7.2-pgsql \
	php7.2-fpm \
	php7.2-mbstring \
	php7.2-memcached \
	php7.2-memcache \
	php7.2-zip \
	memcached \
	nginx \ 
	ca-certificates \
	python \
	python-psycopg2 \
	python-pip \
    python3-pip \
	postgresql-client \
	sudo && \
    apt-get autoremove -y && \
    apt-get autoclean && \
	pip install sqlalchemy alembic bcrypt && \
    pip3 install bcrypt

RUN /opt/keops/configure-keops.sh

EXPOSE 80

RUN rm -r /opt/keops/.git || :

CMD ./opt/keops/docker-entrypoint.sh
