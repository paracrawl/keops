FROM ubuntu:24.04

ARG DEBIAN_FRONTEND=noninteractive
ARG PHP_VERSION=8.4

# 1. Configuración de sistema (Timezone) - Rara vez cambia
RUN ln -snf /usr/share/zoneinfo/Europe/Madrid /etc/localtime && \
    echo "Europe/Madrid" > /etc/timezone

# 2. Instalación de Dependencias de Sistema y última PHP 8 parcheada
RUN apt-get update -q --fix-missing && \
    apt-get -y upgrade && \
    apt-get -y install --no-install-recommends \
        tzdata \
        ca-certificates \
        software-properties-common \
        gnupg \
        curl \
        unzip \
        git \
        memcached \
        nginx \
        python3 \
        python3-pip \
        python3-psycopg2 \
        postgresql-client \
        sudo && \
    add-apt-repository ppa:ondrej/php -y && \
    apt-get update -q && \
    apt-get -y install --no-install-recommends \
        php${PHP_VERSION} \
        php${PHP_VERSION}-cli \
        php${PHP_VERSION}-pgsql \
        php${PHP_VERSION}-fpm \
        php${PHP_VERSION}-mbstring \
        php${PHP_VERSION}-memcached \
        php${PHP_VERSION}-memcache \
        php${PHP_VERSION}-zip \
        php${PHP_VERSION}-xml \
        php${PHP_VERSION}-curl && \
    apt-get autoremove -y && \
    apt-get autoclean && \
    rm -rf /var/lib/apt/lists/* && \
    pip3 install --break-system-packages "sqlalchemy<2.0" alembic bcrypt

# 3. Preparación del directorio de la aplicación
RUN mkdir -p /opt/keops /run/php

# 4. Copia del Código Fuente - Cambia frecuentemente
# Al estar aquí, los cambios en el código no invalidan la caché de los pasos anteriores (apt-get/pip)
COPY . /opt/keops

# 5. Configuración de la aplicación
# Se ejecuta después de copiar el código porque necesita scripts y archivos de configuración presentes
RUN /opt/keops/configure-keops.sh

# Limpieza
RUN rm -rf /opt/keops/.git || :

EXPOSE 80

CMD ["/opt/keops/docker-entrypoint.sh"]
