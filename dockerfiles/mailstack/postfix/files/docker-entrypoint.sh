#!/usr/bin/env bash
confd -onetime -backend env
postfix start-fg # start postfix as forground process