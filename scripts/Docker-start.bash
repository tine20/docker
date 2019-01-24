#!/bin/bash
PS3='Your choice: '
options=("Webpack" "Xdebug" "phpMyAdmin" "Mail" "Docservice" "Confroom" "Clamav" "Worker" "Run" "Quit")
docker='docker-compose -f ../docker-compose.yml '
select opt in "${options[@]}"
  do
        case $opt in
        "Webpack")
            docker+='-f ../compose/webpack.yml '
            ;;
        "Xdebug")
            docker+='-f ../compose/xdebug.yml '
            ;;
        "phpMyAdmin")
            docker+='-f ../compose/pma.yml '
            ;;
        "Mail")
            docker+='-f ../compose/mail.yml '
            ;;
        "Docservice")
            docker+='-f ../compose/docservice.yml '
            ;;
        "Confroom")
            docker+='-f ../compose/confroom.yml '
            ;;   
        "Clamav")
            docker+='-f ../compose/clamav.yml '
            ;;       
        "Worker")
            docker+='-f ../compose/worker.yml '
            ;;                            
        "Run")
            docker+=' up'
            $docker
            break
            ;;
        "Quit")
            break
            ;;            
        *) echo "invalid option $REPLY ";;
        esac
        PS3+=$opt' '
    done
