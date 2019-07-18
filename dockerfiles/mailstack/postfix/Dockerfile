FROM alpine:latest

WORKDIR /root

RUN apk add postfix postfix-mysql

RUN wget https://github.com/kelseyhightower/confd/releases/download/v0.16.0/confd-0.16.0-linux-amd64 &&\
    mv confd-0.16.0-linux-amd64 /usr/bin/confd &&\
    chmod +x /usr/bin/confd &&\
    mkdir /etc/confd &&\
    mkdir /etc/confd/conf.d &&\
    mkdir /etc/confd/templates

RUN mkdir /etc/postfix/sql

COPY confd /etc/confd/conf.d
COPY templates /etc/confd/templates
COPY files/main.cf /etc/postfix/main.cf
COPY files/master.cf /etc/postfix/master.cf
COPY files/docker-entrypoint.sh /root/docker-entrypoint.sh

ENV MYSQL_HOST=255.255.255.255 MYSQL_DB=postfix MYSQL_USER=postfix MYSQL_PASSWORD=notTheCat

CMD ["/bin/sh", "/root/docker-entrypoint.sh"]
