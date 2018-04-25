tine20-docker
----

# install docker

    sudo apt install docker docker-compose

# clone this repo

    git clone

# link your tine20 repo

    ln -s /path/to/tine/repo tine20

# docker build

note: wird obsolet, wenn wir die registry haben 

    docker build . -t tine20 

# docker-compose up

    docker-compose up

# show containers

    docker ps
    
# run bash in container

     docker exec -it tine20 /bin/bash

