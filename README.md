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

    git clone git@github.com:tine20/docker.git

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

## docker build

    docker build . -t tine20

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

## install tine

    docker exec --user nginx tine20 sh -c "cd /tine/tine20/ && php setup.php --install --config /tine/config/config.inc.php \
      -- adminLoginName=test adminPassword=test adminEmailAdress=test@example.org acceptedTermsVersion=1000"

## uninstall tine

    docker exec --user nginx tine20 sh -c "cd /tine/tine20/ && php setup.php --uninstall --config /tine/config/config.inc.php"

## update tine

    docker exec --user nginx tine20 sh -c "cd /tine/tine20/ && php setup.php --update --config /tine/config/config.inc.php"

## create demodata tine

    docker exec --user nginx tine20 sh -c "cd /tine/tine20/ && php tine20.php --config /tine/config/config.inc.php --method Tinebase.createAllDemoData  --username=test --password=test"

## run unittests

NOTE #1: php://stdout is not working for logging ServerTests ...
NOTE #2: maybe you need to increase the php memory_limit (i.e. -d memory_limit=512M - or more)

    docker exec --user nginx tine20 sh -c "cd /tine/tests/tine20/ && ../../tine20/vendor/bin/phpunit --color --stop-on-failure --debug AllTests"

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
