#!/bin/sh
mysql -hdb -uroot -p"$MYSQL_ROOT_PASSWORD" "$DOVECOT_DATABASE" < /scripts/dovecot_users.sql
mysql -hdb -uroot -p"$MYSQL_ROOT_PASSWORD" "$POSTFIX_DATABASE" < /scripts/postfix_users.sql