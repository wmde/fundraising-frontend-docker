#!/bin/bash

VARIANTS="latest dev xdebug composer stan"
IMAGE_NAME="wikimediade/fundraising-frontend"

while [[ $# -gt 0 ]]
do
  key="$1"
  case $key in
      -t|--tag)
      TAG="$2"
      shift # past argument
      shift # past value
      ;;
      -p|--push)
      PUSH_TO_DOCKER_HUB=YES
      shift # past argument
      ;;
  esac
done


VCS_REF=`git rev-parse HEAD`
BUILD_DATE=`date -u +'%Y-%m-%dT%H:%M:%SZ'`

for variant in $VARIANTS
do
  if [ -z "$TAG" ]; then
    BUILD_VERSION="$variant"
  else
    BUILD_VERSION="$variant-$TAG"
  fi
  set -e
  docker build \
    --build-arg VCS_REF=$VCS_REF \
    --build-arg BUILD_VERSION=$BUILD_VERSION \
    --build-arg BUILD_DATE=$BUILD_DATE \
    --tag "$IMAGE_NAME:$BUILD_VERSION" \
    $variant
  set +e
  if [ ! -z "$PUSH_TO_DOCKER_HUB" ]; then
    docker push "$IMAGE_NAME:$BUILD_VERSION"
  fi
done
