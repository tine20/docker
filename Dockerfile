FROM richarvey/nginx-php-fpm:1.10.3

WORKDIR /tine

###########################################

RUN mkdir -p /tine/config
RUN mkdir -p /tine/tine20

###############################################################################

# create directory structure for tine20
RUN mkdir cache files logs tmp
RUN chown nginx:nginx cache files logs tmp

# add deps and compile php-redis
RUN apk update
RUN apk add --no-cache --virtual .build-deps autoconf gcc musl-dev make

# compile php-redis
RUN pecl install igbinary
RUN echo -e "extension=igbinary.so\nigbinary.compact_strings=On" > /usr/local/etc/php/conf.d/docker-php-ext-igbinary.ini
RUN echo "yes" | pecl install redis
RUN echo "extension=redis.so" > /usr/local/etc/php/conf.d/docker-php-ext-redis.ini

# xdebug
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN echo -e "zend_extension=xdebug.so\nxdebug.default_enable=on\nxdebug.remote_enable=on\nxdebug.remote_handler=dbgp\nxdebug.remote_port=9001\nxdebug.remote_host=172.18.0.1\nxdebug.remote_autostart=on" >> /usr/local/etc/php/conf.d/xdebug.ini

# php config: no memory limit
# TODO maybe we should add a limit to php-fpm/nginx config
RUN sed -i 's/memory_limit = 128M//g' /usr/local/etc/php/conf.d/docker-vars.ini

# finalize deps installation
RUN apk del --purge .build-deps
RUN docker-php-source delete
RUN apk del autoconf g++ make

#######
# dev / nodejs / webpack
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
