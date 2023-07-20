#!/bin/bash

function getContainerHealth {
  docker inspect --format "{{.State.Health.Status}}" $1
}
function waitContainer {
  while STATUS=$(getContainerHealth $1); [ "$STATUS" != "healthy" ]; do
    if [ "$STATUS" == "unhealthy" ]; then
      echo "Failed!"
      exit -1
    fi
    printf .
    lf=$'\n'
    sleep 1
  done
  if [ "$STATUS" == "healthy" ]; then
    echo "OK!"
  fi
  printf "$lf"
}
printf "Wait "
printf $(docker ps --format "{{.Names}}" --filter "name=$1")
printf ": "
waitContainer $(docker ps --no-trunc -aq --filter "name=$1")
