FROM richarvey/nginx-php-fpm:1.5.7

# needed to pull tarball
#ENV TINE20_VERSION 2018.02.2

WORKDIR /tine

###########################################
RUN mkdir -p /tine/customers
RUN mkdir -p /tine/tine20
RUN mkdir -p /tine/tests
RUN mkdir -p /etc/supervisor/conf.d/
RUN mkdir -p /tine/conf.d/

###############################################################################

# create directory structure for tine20
RUN mkdir cache files logs tmp
RUN chown nginx:nginx cache files logs tmp
RUN chmod a+w tine20 tests

#######
# install packages/modules
#######

# add deps and compile php-redis
RUN apk update
RUN apk add --no-cache --virtual .build-deps autoconf gcc musl-dev make

# compile php-redis
RUN pecl install igbinary
RUN echo -e "extension=igbinary.so\nigbinary.compact_strings=On" > /usr/local/etc/php/conf.d/docker-php-ext-igbinary.ini
RUN echo "yes" | pecl install redis-4.3.0
RUN echo "extension=redis.so" > /usr/local/etc/php/conf.d/docker-php-ext-redis.ini

# xdebug
RUN pecl install xdebug
RUN echo -e "zend_extension=xdebug.so\nxdebug.default_enable=on\nxdebug.remote_enable=on\nxdebug.remote_handler=dbgp\nxdebug.remote_port=9001\nxdebug.remote_host=172.118.0.1\nxdebug.remote_autostart=on" >> /usr/local/etc/php/conf.d/xdebug.ini

# yaml
RUN apk add yaml-dev
RUN pecl channel-update pecl.php.net
RUN pecl install yaml-2.0.0 && docker-php-ext-enable yaml

# ldap
RUN apk add openldap-dev

# install some more php modules
RUN docker-php-ext-install sockets pcntl intl bcmath ldap

# install supervisor
RUN apk add supervisor

# add nodejs
RUN apk add nodejs-current-npm

# add rsync
RUN apk add rsync

# ytnef (for extracting winmail.dat files)
RUN apk add ytnef

# cleanup
RUN apk del --purge .build-deps
RUN docker-php-source delete
RUN apk del autoconf g++ make

#######
# fix configs
#######

# fix php-fpm startup config
RUN sed -i 's#/usr/local/etc/php-fpm.d/www.conf#/usr/local/etc/php-fpm.conf#g' /etc/supervisord.conf

# php config: increase memory limit
# TODO maybe we should add a limit to php-fpm/nginx config
RUN sed -i 's/memory_limit = 128M/memory_limit = 512M/g' /usr/local/etc/php/conf.d/docker-vars.ini

# tika
RUN wget https://packages.tine20.org/tika/tika-app-1.14.jar -o /usr/share/tika.jar
RUN chmod +x /usr/share/tika.jar

#######
# dev
#######
#RUN cd /tine/tine20/tine20 && composer install --no-interaction --ignore-platform-reqs
#RUN cd /tine/tine20/tine20 && COMPOSER_PROCESS_TIMEOUT=2000 composer install --no-interaction --ignore-platform-reqs

#######
# prod
#######

#RUN wget http://packages.tine20.org/source/${TINE20_VERSION}/tine20-allinone_${TINE20_VERSION}.tar.bz2
#RUN tar -xjf tine20-allinone_${TINE20_VERSION}.tar.bz2 -C tine20
#RUN rm tine20-allinone_${TINE20_VERSION}.tar.bz2
