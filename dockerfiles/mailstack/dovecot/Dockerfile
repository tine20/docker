FROM alpine:latest

WORKDIR /root

RUN apk add dovecot dovecot-mysql dovecot-pop3d dovecot-lmtpd dovecot-pigeonhole-plugin

RUN wget https://github.com/kelseyhightower/confd/releases/download/v0.16.0/confd-0.16.0-linux-amd64 &&\
    mv confd-0.16.0-linux-amd64 /usr/bin/confd &&\
    chmod +x /usr/bin/confd &&\
    mkdir /etc/confd &&\
    mkdir /etc/confd/conf.d &&\
    mkdir /etc/confd/templates

RUN adduser -H -D vmail

RUN mkdir /var/vmail && chown vmail:vmail /var/vmail

COPY files/docker-entrypoint.sh /root
COPY conf.d /etc/dovecot/conf.d
COPY confd /etc/confd/conf.d
COPY templates /etc/confd/templates

ENV MYSQL_HOST=255.255.255.255 MYSQL_DB=dovecot MYSQL_USER=dovecot MYSQL_PASSWORD=doofecat

CMD ["/bin/sh", "/root/docker-entrypoint.sh"]