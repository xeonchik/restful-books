#!/usr/bin/env bash

#if [ -f /home/vagrant/.homestead-features/php74 ]
#then
#    echo "PHP already installed."
#    exit 0
#fi

# php 7.4 install
apt-get -y install software-properties-common
add-apt-repository -y ppa:ondrej/php
apt-get update
apt-get -y install php7.4 php7.4-fpm php7.4-mysql php7.4-curl php7.4-dev
