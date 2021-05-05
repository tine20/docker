#!/bin/sh
set -e

cmds="
s#dockerregistry.metaways.net/tine20/tine20/dev:2020.11-7.4#tine20/dev:2020.11#g
s#dockerregistry.metaways.net/tine20/docker/mailstackcontrol:1.0.4#tine20/devsetup-mailstackcontrol:1.0.4#g
s#dockerregistry.metaways.net/tine20/docker/dovecot:1.0.1#tine20/devsetup-dovecot:1.0.1#g
s#dockerregistry.metaways.net/tine20/docker/postfix:1.0.1#tine20/devsetup-postfix:1.0.1#g
s#dockerregistry.metaways.net/tine20/tine20/puppeteer:1.0.2#tine20/devsetup-puppeteer:1.0.2#g
s#dockerregistry.metaways.net/tine20/docker/docservice:1.0.0#tine20/devsetup-docservice:1.0.0#g
"

for file in compose/*.yml docker-compose.yml; do
  echo $file:
  for cmd in $cmds; do
    sed -i $cmd $file
  done
  if cat $file | grep "dockerregistry.metaways.net"; then
    echo "$file contains dockerregistry.metaways.net. This should probably be replaced for the github version."
    echo "Add a sed line to $0"
  fi
done
