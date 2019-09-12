FROM alpine:3.10

WORKDIR /root

RUN apk add mysql-client

COPY files /root
COPY scripts /usr/bin

ENV MYSQL_ROOT_PASSWORD="r00t"
ENV DOVECOT_DATABASE="dovecot"
ENV POSTFIX_DATABASE="postfix"