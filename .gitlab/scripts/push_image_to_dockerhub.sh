#!/bin/bash
set -e

function push() {
  docker pull $1
  docker tag $1 $2
  docker push $2
}

push dockerregistry.metaways.net/tine20/docker/mailstackcontrol:1.0.4 tine20/devsetup-mailstackcontrol:1.0.4
push dockerregistry.metaways.net/tine20/docker/dovecot:1.0.1 tine20/devsetup-dovecot:1.0.1
push dockerregistry.metaways.net/tine20/docker/postfix:1.0.1 tine20/devsetup-postfix:1.0.1
push dockerregistry.metaways.net/tine20/tine20/puppeteer:1.0.2 tine20/devsetup-puppeteer:1.0.2
push dockerregistry.metaways.net/tine20/docker/docservice:1.0.0 tine20/devsetup-docservice:1.0.0
