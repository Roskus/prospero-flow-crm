FROM nginx:stable
RUN apt-get update && apt-get install -y --no-install-recommends \
    vim git wget zip unzip mc htop nano \
    && rm -rf /var/lib/apt/lists/* /var/cache/apt/archives/*
