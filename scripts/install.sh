#!/usr/bin/env bash

apt-get remove apache2
apt-get install -y nginx composer

#rm -f /etc/nginx/sites-enabled/*
#rm -f /etc/nginx/sites-available/*

block="server {
    listen 80;
    server_name restful-books.dev restful-books.test;
    root \"/vagrant/public\";

    index index.html index.htm index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    access_log off;
    error_log  /var/log/nginx/restful-books_error.log error;

    sendfile off;

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;

        fastcgi_intercept_errors off;
        fastcgi_buffer_size 16k;
        fastcgi_buffers 4 16k;
        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
        fastcgi_read_timeout 300;
    }

    location ~ /\.ht {
        deny all;
    }
}
"

echo "$block" > "/etc/nginx/sites-available/restful-books.conf"
ln -fs "/etc/nginx/sites-available/restful-books.conf" "/etc/nginx/sites-enabled/restful-books.conf"
service nginx restart

cd /vagrant/
composer install
