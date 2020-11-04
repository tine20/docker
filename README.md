tine20-docker
----
# pullup

## install

prerequisite: git, docker, docker-compose, php, composer, npm, your user is in the docker group. If not see [below](install docker-io and docker-compose)

1. clone this git and open it `git clone git@gitlab.metaways.net:tine20/docker.git tine20-docker` and `cd tine20-docker`
2. link your tine20 source `ln -s /path/to/tine/repo tine20` or just wait for pullup to clone it for you
3. link docservice source `ln -s /path/to/docservice/repo docservice` or just wait for pullup to clone it for you
4. login to the tine docker registry, with your gitlab credentials `docker login dockerregistry.metaways.net` ([gitlab docu, for MFA](https://docs.gitlab.com/ee/user/packages/container_registry/#authenticating-to-the-gitlab-container-registry))
5. checkout your branch and install tine20 dependencies `cd tine20/tine20 && composer install --ignore-platform-reqs` and `cd tine20/tine20/Tinebase/js && npm install`
6. install docservice dependencies, if pullup has cloned it you dont need to do anything `cd docservice && composer install --ignore-platform-reqs`

## start
7. start tine20-docker setup `./pullup docker up`, if you have not done install 2 or 3 answer y to clone repos
8. install tine `./pullup tine install`
9. visit localhost:4000, login as tine20admin pw: tine20admin 
10. strg+c to stop

## Man

+ `./pullup docker up` start docker setup.  pulls/builds images, creates containers, starts containers and shows logs
+ `./pullup docker start` start docker setup. pulls/builds images, creates containers, starts containers
+ `./pullup docker stop` stops docker, if you used `up` you can stop with strg+c
+ `./pullup docker logs` displays logs (interactive)
+ `./pullup docker down` destroys docker setup.  stops containers, removes containers and networks, volumes will persist
+ `./pullup docker cli <service name>` start shell in service name eg. db or web for tine20
+ `./pullup docker build` build docker containers locally, build needs to be enabled
+ `./pullup docker pull` pull docker images
+ `./pullup docker push` push updates to docker images

+ `./pullup tine install` install tine
+ `./pullup tine uninstall` uninstall tine
+ `./pullup tine update` update tine: executes setup.php --update
+ `./pullup tine demodata` creates demodata
+ `./pullup tine test <path>` starts test eg `./pullup tine test AllTests`
+ `./pullup tine cli <command>` executes tine20.php with command, dont use the --config option

+ missing a command > issue tracker

## pullup.json

to override default settings copy .pullup.json to pullup.json

+ composeFiles: dockerset up "modules". Take a look at ./compose (Do not include the ones with -build.yml)
+ build: if true images will be build locally
+ more: any wishes? > issue tracker 


# linksammlung

* https://linuxize.com/post/how-to-remove-docker-images-containers-volumes-and-networks/

# install and setup docker

## install docker-io and docker-compose

    sudo apt install docker.io docker-compose

For macOS you can simply use homebrew:

    brew cask install docker
    
some hints to performance with mac osx:

>  docker-edge performs better using macos. but it wipes all your images and so on when upgrading. first need to uninstall brew cask remove docker, then go for brew cask install docker-edge. start docker -> reset -> reset disk image. but data is gone even if NO reset occurs. reset disk image just ensures, that the latest disk image format is used
  and .. docker chokes with suspend on macos, restart docker each time to speed it up
  if possible use cached volumes, that helps a lot

## add yourself to the docker group (to work without sudo) - no need for macOS

_relogin required!_

    sudo usermod -a -G docker <your userid>

# init repos

## clone this repo

    git clone git@gitlab.metaways.net:tine20/docker.git tine20-docker

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

## get docker image

Our dev image `tine20/dev:2020.11-7.3-fpm-alpine` is on docker hub. 
You can also use an image from our registry. 

`dockerregistry.metaways.net/tine20/tine20/dev:<tag>`

[Here you can find all the available tags](https://gitlab.metaways.net/tine20/tine20/container_registry/eyJuYW1lIjoidGluZTIwL3RpbmUyMC9kZXYiLCJ0YWdzX3BhdGgiOiIvdGluZTIwL3RpbmUyMC9yZWdpc3RyeS9yZXBvc2l0b3J5LzU0L3RhZ3M%2FZm9ybWF0PWpzb24iLCJpZCI6NTR9)

Tags:
    2020.11-7.3-fpm-alpine
    <branch name>-<php version>-fpm-alpine
    The branch name dose not need to match the tine20 branch, you are developing on.

## start tine20

    docker-compose -f docker-compose.yml -f compose/webpack.yml up

or
    
    php /scripts/docker.php
    
or

    ./scripts/Docker-start.bash

## show containers

    docker ps

## run bash in container

    docker exec -it tine20 /bin/sh

## open tine20 in browser

[localhost:4000](http://localhost:4000/) - nginx

[localhost:4000](http://localhost:4000/) - webpack served

[localhost:4000/setup.php](http://localhost:4000/setup.php) - webpack served setup

[localhost:4002](http://localhost:4002) Phpmyadmin Username:tine20 Password:tine20pw

## install tine

Also configures the mailserver & system account.

    docker exec --user nginx tine20 sh -c "cd /tine/tine20/ && php setup.php --install --config /tine/customers/localhost/config.inc.php \
      -- adminLoginName=test adminPassword=test adminEmailAddress=test@example.org acceptedTermsVersion=1000 \
      imap=\"backend:standard,host:mail,port:143,useSystemAccount:1,verifyPeer:0,ssl:tls,domain:example.org\" \
      smtp=\"backend:standard,hostname:mail,port:25,auth:none,primarydomain:example.org,ssl:tls,from:test@example.org\""

or via phing:

    docker exec --user nginx tine20 sh -c "cd /tine/tine20/ && vendor/bin/phing -D configdir=/tine/customers/localhost tine-install"


## uninstall tine

    docker exec --user nginx tine20 sh -c "cd /tine/tine20/ && php setup.php --uninstall --config /tine/customers/localhost/config.inc.php"

or via phing:

    docker exec --user nginx tine20 sh -c "cd /tine/tine20/ && vendor/bin/phing -D configdir=/tine/customers/localhost tine-uninstall"

## update tine

    docker exec --user nginx tine20 sh -c "cd /tine/tine20/ && php setup.php --update --config /tine/customers/localhost/config.inc.php"

## create demodata tine

    docker exec --user nginx tine20 sh -c "cd /tine/tine20/ && php tine20.php --config /tine/customers/localhost/config.inc.php --method Tinebase.createAllDemoData  --username=test --password=test"

## run unittests

NOTE #1: php://stdout is not working for logging ServerTests ...
NOTE #2: maybe you need to increase the php memory_limit (i.e. -d memory_limit=512M - or more)

    docker exec --user nginx tine20 sh -c "cd /tine/tests/tine20/ && ../../tine20/vendor/bin/phpunit --color --stop-on-failure --debug AllTests"

## table prefix
you can change the table prefix in the config.inc.php file:

        <?php
        $version = 'be'; <- change this


## import handbook

you might to do this first aus we have some BIG pages in the handbook:

    docker exec -it db /bin/sh
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

# ActionQueue / Worker in Docker

## Running a actionQueue

    docker-compose -f docker-compose.myl -f compose/worker.yml up

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

     name: debugger
     server: 
       name: tine20docker
       host: 172.18.0.1 (or tine20docker)
       port: 9001 
       debugger: Xdebug
       path mapping:
         /local/tine  -> /tine (must have)
         /local/tine/tests -> /tine/tests
         /local/tine/tine20 -> /tine/tine20
     
     ide key: serverName=tine20docker

open Xdebug port in PHPSTORM

    File | Settings | Languages & Frameworks | PHP | Debug | Xdebug
    - Debug port : 9001
    - [x] can accept external connections 
    
if you have a different IP, you might need to use the XDEBUG_CONFIG env vars in docker-compose.yml

## troubleshooting

some tips on testing your xdebug/phpstorm setup:

### check connectivity 

on docker host:

    $ netstat -tulpen | grep 9001
    tcp        0      0 0.0.0.0:9001            0.0.0.0:*               LISTEN      1000       2918160    14641/java

in container:

    $ nc -vz 172.118.0.1 9001
    172.118.0.1 (172.118.0.1:9001) open

### check xdebug log

- activate xdebug log in container (add `remote_log=/tine/logs/xdebug.log` in xdebug.yml)
- look into log (default path: /tine/logs/xdebug.log)

### allow iptables access from container -> host

    sudo iptables -I INPUT 1 -i <docker-bridge-interface> -j ACCEPT
    
<docker-bridge-interface> is something like "br-3ff4120010e5" which has ip:172.118.0.1 (visible with ifconfig)

# running a tine20 container with ...

## webpack

    docker-compose -f docker-compose.yml -f compose/webpack.yml up

## php-cli only container

    docker-compose -f docker-compose.yml up

## mail (dovecot/postfix) container

    docker-compose -f docker-compose.yml -f compose/mail.yml up

## phpmyadmin container

    docker-compose -f docker-compose.yml -f compose/pma.yml up

## mailstack (dovecot postfix sieve userdb)

    docker-compose -f docker-compose.yml -f compose/mailstack.yml up
  
+ before installing tine20 you musst initialise the mail db (mailstack containers must be started)
`./scripts/cli.php mailstack init` or
`docker-compose -f docker-compose.yml -f compose/mailstack.yml run mailstack init`

+ when you are reinstalling tine20 you should reset the mail db
`./scripts/cli.php mailstack reset` or
`docker-compose -f docker-compose.yml -f compose/mailstack.yml run mailstack reset`

+ after mailstack containers have changed eg. git pull, rebuild images
`./scripts/cli.php mailstack build` or
`docker-compose -f docker-compose.yml -f compose/mailstack.yml build`

##### install.properties
imap and pop: 

+ host: dovecot
+ ssl: none / starttls / tls
+ auth: plain
+ db_host: db
+ db: dovecot
+ dovecot_uid:vmail
+ dovecot_gid:vmail
+ dovecot_home:/var/vmail/%d/%u

smtp:
+ host: postfix
+ ssl: none
+ auth: none
+ db_host: db
+ db: dovecot

sieve:
+ hostname dovecot
+ port: 4190
+ ssl: none

eg:
```
imap="active:true,host:dovecot,port:143,useSystemAccount:1,ssl:tls,verifyPeer:0,backend:dovecot_imap,domain:mail.test,instanceName:tine.test,dovecot_host:db,dovecot_dbname:dovecot,dovecot_username:tine20,dovecot_password:tine20pw,dovecot_uid:vmail,dovecot_gid:vmail,dovecot_home:/var/vmail/%d/%u,dovecot_scheme:SSHA256"
smtp="active:true,backend:postfix,hostname:postfix,port:25,ssl:none,auth:none,name:postfix,primarydomain:mail.test,instanceName:tine.test,postfix_host:db,postfix_dbname:postfix,postfix_username:tine20,postfix_password:tine20pw"
sieve="hostname:dovecot,port:4190,ssl:none"
```

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

# add document service

NOTE: some fonts are not available on the minimal docker image ... so don't
 wonder about strange looking texts ... ;) 

## clone, initialize and link repo

    git clone git@gitlab.metaways.net:tine20/documentPreview.git
    cd documentPreview
    composer install
    cd /path/to/tine20-docker
    ln -s /patch/to/docservice docservice
    
## configure

note: this only works with tine20.com/2018.11* branches

```php
'filesystem' => array(
    'createPreviews' => true,
    'previewServiceVersion' => 2,
    'previewServiceUrl' => 'http://docservice/v2/documentPreviewService',
),
```

## clear tine20 cache

    docker exec --user nginx tine20 sh -c "cd /tine/tine20/ && php tine20.php --method=Tinebase.clearCache --username test --password test"
    
## TODO phing aufrufe ergänzen

## restart webpack-dev-server

im tine container:

    ps aux | grep webpack
    kill [PID]

## use ramdisk for sql storage

davor muss der alte db container gelöscht werden, sonst greift das mount nicht:

    docker rm db

ramdisk erzeugen:

    sudo mkdir /mnt/ramdisk
    sudo mount -t tmpfs -o size=512m tmpfs /mnt/ramdisk
    
wenn man mag, kann das mount in die /etc/fstab geschoben werden.
    
    tmpfs   /mnt/ramdisk tmpfs   nosuid,size=512M   0 0

docker-compose:

    # start docker with ramdisk & webpack
    php scripts/docker.php webpack ramdisk

achtung: man verliert natürlich seine db nach dem reboot!

achtung 2: man darf sonst nichts in die ramdisk legen, sonst meckert mysql/maria

## SENTRY

you need to add sentry to your /etc/hosts file (because of CSRF):

    127.0.0.1       localhost sentry

First boot:

    ./pullup docker up sentry [...]
    docker exec -it sentry bash
    ./entrypoint.sh sentry upgrade

## use mysql instead of mariadb

add "mysql" to your pullup.json!
