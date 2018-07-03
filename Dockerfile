FROM richarvey/nginx-php-fpm:latest
#FROM ubuntu:latest

# needed to pull tarball
#ENV TINE20_VERSION 2018.02.2

WORKDIR /tine

###############################################################################

# push configs
#ADD tine /tine
#RUN chown nginx:nginx /tine/conf/tine20/config.inc.php

# set nginx config
#RUN rm /etc/nginx/sites-enabled/default.conf
#RUN cp /tine/conf/nginx/nginx-site.conf /etc/nginx/sites-enabled/default.conf

###########################################

RUN mkdir -p /tine/customers
RUN mkdir -p /tine/tine20


###############################################################################

# create directory structure for tine20
RUN mkdir cache files logs tmp
RUN chown nginx:nginx cache files logs tmp

# add deps and compile php-redis
RUN apk update
RUN apk add autoconf gcc musl-dev make
RUN pecl install igbinary
RUN echo -e "extension=igbinary.so\nigbinary.compact_strings=On" > /usr/local/etc/php/conf.d/docker-php-ext-igbinary.ini
RUN echo "yes" | pecl install redis
RUN echo "extension=redis.so" > /usr/local/etc/php/conf.d/docker-php-ext-redis.ini

# xdebug
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN echo -e "#xdebug.default_enable=on\n#xdebug.remote_enable=on\nxdebug.remote_handler=dbgp\n#xdebug.remote_port=9001\n#xdebug.remote_host=127.0.0.1\n#xdebug.remote_autostart=on" >> /usr/local/etc/php/conf.d/xdebug.ini

# finalize deps installation
RUN docker-php-source delete
RUN apk del autoconf g++ make

#######
# dev #
#######
#RUN cd /tine/tine20/tine20 && composer install --no-interaction --ignore-platform-reqs
#RUN cd /tine/tine20/tine20 && COMPOSER_PROCESS_TIMEOUT=2000 composer install --no-interaction --ignore-platform-reqs

# add nodejs
#RUN apk add nodejs         # does not work for the latest image
RUN apk add nodejs-current-npm

# add startup config for webpack
RUN mkdir -p tine20
RUN mkdir -p /etc/supervisor/conf.d/
RUN echo -e "[program:webpack]\ncommand=/usr/bin/npm start\ndirectory=/tine/tine20/Tinebase/js/\nautostart=true\nautorestart=true\npriority=10\nstdout_events_enabled=true\nstderr_events_enabled=true\nstdout_logfile=/dev/stdout\nstdout_logfile_maxbytes=0\nstderr_logfile=/dev/stderr\nstderr_logfile_maxbytes=0" > /etc/supervisor/conf.d/webpack.conf

# fix php-fpm startup config
RUN sed -i 's#/usr/local/etc/php-fpm.d/www.conf#/usr/local/etc/php-fpm.conf#g' /etc/supervisord.conf

#RUN wget http://packages.tine20.org/source/${TINE20_VERSION}/tine20-allinone_${TINE20_VERSION}.tar.bz2
#RUN tar -xjf tine20-allinone_${TINE20_VERSION}.tar.bz2 -C tine20
#RUN rm tine20-allinone_${TINE20_VERSION}.tar.bz2
