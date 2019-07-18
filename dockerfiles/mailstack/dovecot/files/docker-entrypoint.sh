#!/usr/bin/env bash
confd -onetime -backend env
/usr/sbin/dovecot -F # start dovecot as forground process