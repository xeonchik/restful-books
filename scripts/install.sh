#!/usr/bin/env bash

apt-get remove apache2
apt-get install -y nginx composer redis-server

#rm -f /etc/nginx/sites-enabled/*
#rm -f /etc/nginx/sites-available/*

block="server {
    listen 80;
    server_name restful-phonebook.test;
    root \"/home/vagrant/code/public\";

    index index.html index.htm index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    access_log off;
    error_log  /var/log/nginx/restful-phonebook_error.log error;

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

echo "$block" > "/etc/nginx/sites-available/restful-phonebook.conf"
ln -fs "/etc/nginx/sites-available/restful-phonebook.conf" "/etc/nginx/sites-enabled/restful-phonebook.conf"
service nginx restart

# Install redis
echo "" | pecl install redis
echo "extension=redis.so" > "/etc/php/7.4/mods-available/redis.ini"
ln -s /etc/php/7.4/mods-available/redis.ini /etc/php/7.4/fpm/conf.d/20-redis.ini
ln -s /etc/php/7.4/mods-available/redis.ini /etc/php/7.4/cli/conf.d/20-redis.ini
service php7.4-fpm restart

# Install app
rm -rf /home/vagrant/code
cp -R /vagrant/ /home/vagrant/code
chown -R vagrant:vagrant /home/vagrant/code
cd /home/vagrant/code
php composer.phar install
chmod +x /home/vagrant/code/vendor/bin/*
chmod -R 0777 /home/vagrant/code/var


# Load dump into DB
mysql --user="root" phonebook < /vagrant/db/dump.sql
