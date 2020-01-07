# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/xenial64"
  # config.vm.box_check_update = false
  # config.vm.network "forwarded_port", guest: 80, host: 8080, host_ip: "127.0.0.1"

  config.vm.network "private_network", ip: "192.168.33.11"

  # config.vm.network "public_network"
  # config.vm.synced_folder ".", "/vagrant_data"

  config.vm.provision "shell", path: "./scripts/install.sh"
  config.vm.provision "shell", path: "./scripts/php74.sh"
  config.vm.provision "shell", path: "./scripts/mariadb.sh"
end
