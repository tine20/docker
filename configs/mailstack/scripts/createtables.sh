#!/usr/bin/env sh

mysql -hdb -uroot -p"$MYSQL_ROOT_PASSWORD" "$POSTFIX_DATABASE" < /scripts/postfix_init_virtual_domains.sql