FROM ubuntu:18.04

ARG DEBIAN_FRONTEND=noninteractive

RUN mkdir /opt/keops

COPY . /opt/keops

RUN echo "Europe/Madrid" > /etc/timezone

RUN apt-get update -q --fix-missing && \
    apt-get -y upgrade && \
    apt-get -y install  tzdata \			
			postgresql \
			php7.2 \
			php7.2-pgsql \
			php7.2-fpm \
			php7.2-mbstring \
			nginx \ 
			ca-certificates \
			sudo && \
    apt-get autoremove -y && \
    apt-get autoclean

RUN /opt/keops/configure-keops.sh

EXPOSE 80


CMD ./opt/keops/docker-entrypoint.sh
