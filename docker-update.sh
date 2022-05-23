#!/bin/bash

docker rm -f freenom && \
docker pull luolongfei/freenom && \
docker run -d \
        --name freenom \
        --restart always \
        -v $(pwd):/conf \
        -v $(pwd)/logs:/app/logs \
        luolongfei/freenom