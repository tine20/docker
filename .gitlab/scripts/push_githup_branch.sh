#!/bin/sh 
set -e

apk add git curl

git add	.
git config --global user.name "ci process"
git config --global user.email "tine20@metaways.de"
git commit -m "replaced internal docker registry with dockerhub"

git remote add gitlab $CI_REPOSITORY_URL_WITH_BASIC_AUTH_GITLAB
git push -f gitlab HEAD:github

git remote add github $CI_REPOSITORY_URL_WITH_BASIC_AUTH_GITHUB
git push -f github HEAD:github
