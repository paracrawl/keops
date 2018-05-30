FROM ubuntu:18.04

MKDIR /opt/keops

COPY . /opt/keops

RUN apt-get update -q --fix-missing && \
    apt-get -y upgrade && \
    apt-get -y install  postgresql \
			php7.2 \
			php7.2-pgsql \
			php7.2-fpm \
			nginx \
			git	 && \
    apt-get autoremove -y && \
    apt-get autoclean

EXPOSE 8080

CMD /opt/keops/docker-entrypoint.sh