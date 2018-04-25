tine20-docker
----

install and setup docker
-----

# install docker and docker-compose

    sudo apt install docker docker-compose

# add yourself to the docker group
# in order not to use sudo
# relogin required!!!!!!!!!!!!

    sudo usermod -a -G docker <your userid>

clone tine20
-------

# clone this repo

    git clone

# link your tine20 repo

    ln -s /path/to/tine/repo tine20

# docker build (only needed if you do not want to use the ready image)

note: wird obsolet, wenn wir die registry haben

    docker build . -t tine20


run tine20 dockerized
------

TODO: add docker registry info



# docker-compose up

    docker-compose up

# show containers

    docker ps

# run bash in container

     docker exec -it tine20 /bin/bash


other useful functions
--------

# containers

    docker ls
    docker ls -a            # shows all (not running only) containers
    docker rm <container id/name>

# images

    docker image ls
    docker image ls -a      # shows all containers, as well as deps
    docker rm <image id/name>
