tine20-docker
----

install and setup docker
-----

# install docker and docker-compose

    sudo apt install docker docker-compose

# add yourself to the docker group (to work without sudo)

_relogin required!!!!!!!!!!!!_

    sudo usermod -a -G docker <your userid>

init repos and images
-------

# clone this repo

    git clone git@gitlab.metaways.net:r.jerger/tine20-docker.git

# link your tine20 repo

    ln -s /path/to/tine/repo tine20

# docker build (only needed if you do not want to use the ready image)

note: obselete when we have a docker registry

    docker build . -t tine20


run tine20 dockerized
------

TODO: add docker registry stuff when we have it

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
