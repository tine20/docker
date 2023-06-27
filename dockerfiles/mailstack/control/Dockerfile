FROM alpine:3.10

WORKDIR /root

RUN apk add mysql-client

# TODO mount SQL from tine repo (no longer copy files dir)
COPY files /root

COPY scripts /usr/bin

ENV MYSQL_ROOT_PASSWORD="root"
ENV DOVECOT_DATABASE="dovecot"
ENV POSTFIX_DATABASE="postfix"