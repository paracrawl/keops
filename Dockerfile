FROM ubuntu:18.04

ARG DEBIAN_FRONTEND=noninteractive

# 1. Configuración de sistema (Timezone) - Rara vez cambia
RUN echo "Europe/Madrid" > /etc/timezone

# 2. Instalación de Dependencias de Sistema - Pesado, se cacheará
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
    pip3 install --upgrade pip &&\
    pip3 install setuptools sqlalchemy alembic setuptools-rust bcrypt && \
    pip3 install bcrypt

# 3. Preparación del directorio de la aplicación
RUN mkdir -p /opt/keops

# 4. Copia del Código Fuente - Cambia frecuentemente
# Al estar aquí, los cambios en el código no invalidan la caché de los pasos anteriores (apt-get/pip)
COPY . /opt/keops

# 5. Configuración de la aplicación
# Se ejecuta después de copiar el código porque necesita scripts y archivos de configuración presentes
RUN /opt/keops/configure-keops.sh

# Limpieza
RUN rm -r /opt/keops/.git || :

EXPOSE 80

CMD ["/opt/keops/docker-entrypoint.sh"]