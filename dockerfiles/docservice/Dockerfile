FROM richarvey/nginx-php-fpm:1.5.7

WORKDIR /document-preview

RUN mkdir -p /document-preview/document-preview/
RUN mkdir -p /etc/documentPreviewService/2.0/
RUN mkdir -p /var/log/documentPreviewService/
RUN mkdir -p /var/lib/documentPreviewService/

RUN chown nginx:nginx /document-preview/document-preview/ /etc/documentPreviewService/2.0/ /var/log/documentPreviewService/ /var/lib/documentPreviewService/

RUN apk update

# plec
RUN apk add --no-cache --virtual .build-deps autoconf gcc musl-dev make

# xdebug
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN echo -e "zend_extension=xdebug.so\nxdebug.default_enable=on\nxdebug.remote_enable=on\nxdebug.remote_handler=dbgp\nxdebug.remote_port=9001\nxdebug.remote_host=172.18.0.1\nxdebug.remote_autostart=on" >> /usr/local/etc/php/conf.d/xdebug.ini

# document-preview dependencies
RUN apk add --no-cache unzip
RUN apk add --no-cache ghostscript
RUN apk add --no-cache graphicsmagick


# libreoffice
RUN apk add --no-cache libreoffice
