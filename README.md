tine20-docker
----

# install and setup docker

## install docker and docker-compose

    sudo apt install docker docker-compose

For macOS you can simply use homebrew:

    brew cask install docker

## add yourself to the docker group (to work without sudo) - no need for macOS

_relogin required!_

    sudo usermod -a -G docker <your userid>

# init repos

## clone this repo

    git clone git@gitlab.metaways.net:tine20/docker.git

## link your tine20 repo

    cd tine20-docker
    ln -s /path/to/tine/repo tine20

# build image

## npm install

    cd tine20-docker/tine20/tine20/Tinebase/js
    npm install

## composer install

    cd tine20-docker/tine20/tine20/
    composer install --ignore-platform-reqs

## docker build (only needed if you do not want to use the ready image)

note: obsolete when we have a docker registry

    docker build . -t tine20

## build for other versions of tine20

note: does not work yet as we only have php 7.2 (which does not work with 2017.11)

for example 2017.11 (which has a different webpack start command):

    git checkout 2017.11
    docker build . -t tine20:2017.11

# run tine20 dockerized

TODO: add docker registry stuff when we have it

## docker-compose up

    docker-compose up

## show containers

    docker ps

## run bash in container

    docker exec -it tine20 /bin/bash

## open tine20 in browser

[localhost:4000](http://localhost:4000/) - nginx

[localhost:4001](http://localhost:4001/) - webpack served

[localhost:4001/setup.php](http://localhost:4001/setup.php) - webpack served setup

[localhost:4002](http://localhost:4002) Phpmyadmin Username:tine20 Password:tine20pw

## install tine

Also configures the mailserver & system account.

    docker exec --user nginx tine20 sh -c "cd /tine/tine20/ && php setup.php --install --config /tine/customers/localhost/config.inc.php \
      -- adminLoginName=test adminPassword=test adminEmailAdress=test@example.org acceptedTermsVersion=1000 \
      imap=\"backend:standard,host:mail,port:143,useSystemAccount:1,verifyPeer:0,ssl:tls,domain:example.org\" \
      smtp=\"backend:standard,hostname:mail,port:25,auth:none,primarydomain:example.org,ssl:tls,from:test@example.org\""

## uninstall tine

    docker exec --user nginx tine20 sh -c "cd /tine/tine20/ && php setup.php --uninstall --config /tine/customers/localhost/config.inc.php"

## update tine

    docker exec --user nginx tine20 sh -c "cd /tine/tine20/ && php setup.php --update --config /tine/customers/localhost/config.inc.php"

## create demodata tine

    docker exec --user nginx tine20 sh -c "cd /tine/tine20/ && php tine20.php --config /tine/customers/localhost/config.inc.php --method Tinebase.createAllDemoData  --username=test --password=test"

## run unittests

NOTE #1: php://stdout is not working for logging ServerTests ...
NOTE #2: maybe you need to increase the php memory_limit (i.e. -d memory_limit=512M - or more)

    docker exec --user nginx tine20 sh -c "cd /tine/tests/tine20/ && ../../tine20/vendor/bin/phpunit --color --stop-on-failure --debug AllTests"

## import handbook

you might to do this first aus we have some BIG pages in the handbook:

    docker exec -it db /bin/bash
    mysql -u root
    MariaDB [tine20]> SET GLOBAL max_allowed_packet=1073741824;

for example the ebhh handbook version:

    docker exec --user nginx tine20 sh -c "cd /tine/tine20/ && php -d memory_limit=512M tine20.php --method UserManual.importHandbookBuild  --username=test --password=test https://erzbistum:****@packages.tine20.com/erzbistum/tine20-handbook_html_chunked_build-369_commit-1debfeda9e3831feccd7ca66f8fa7cae89cda150.zip"

# other useful functions

## containers

    docker ps
    docker ps -a            # shows all (not running only) containers
    docker rm <container id/name>

## images

    docker image ls
    docker image ls -a      # shows all containers, as well as deps
    docker image rm <image id/name>

# debugging with PHPSTORM

this is the default xdebug.ini:

    zend_extension=xdebug.so
    xdebug.default_enable=on
    xdebug.remote_enable=on
    xdebug.remote_handler=dbgp
    xdebug.remote_port=9001
    xdebug.remote_host=172.18.0.1
    xdebug.remote_autostart=on

you need to define a "PHP remote debug" server in PHPSTORM:

     name: tine20docker
     ide key: serverName=tine20docker
     port: 9001 (xdebug)
     host: 172.18.0.1
     path mapping:
       /local/tine  -> /tine
       /local/tine/tests -> /tine/tests
       /local/tine/tine20 -> /tine/tine20

if you have a different IP, you might need to use the XDEBUG_CONFIG env vars in docker-compose.yml

# building and running a php-cli only container

    docker build -f Dockerfile-cli . -t tine20-cli
    docker-compose -f docker-compose-cli.yml up

# debug / test stuff with fake previews

sometimes you don't have a working doc service but need to test files with previews.

## copy some images to container:

    docker cp ~/Pictures/image1.png tine20:/tine/files
    docker cp ~/Pictures/image2.png tine20:/tine/files
    
## patch tine20/Tinebase/FileSystem/Preview/ServiceV1.php

```php
     public function getPreviewsForFile($_filePath, array $_config)
     {
        // just for testing
        $blob1 = file_get_contents('/tine/files/ssh_password.png');
        $blob2 = file_get_contents('/tine/files/tine20_datenbanken.png');
        return array('thumbnail' => array('blob'), 'previews' => array($blob1, $blob2));
        // [...]
     }
```
## configure previews (config.inc.php)

```php
'filesystem' => array(
    'createPreviews' => true,
    'previewServiceVersion' => 1,
),
```

## create previews for files

     docker exec --user nginx tine20 sh -c "cd /tine/tine20/ && php tine20.php  --method Tinebase.fileSystemCheckPreviews  --username=test --password=test"
