#!/usr/bin/env bash

## Configure Bash
# Aliases for common commands
tee -a ~/.bash_aliases > /dev/null <<"EOF"
alias tree='tree --dirsfirst -A -F'
EOF

## Enable globstar option for recursive globbing
tee -a ~/.bashrc > /dev/null <<"EOF"
shopt -s globstar nullglob
EOF

## Install required packages
# Update packages list
sudo apt update

# Install common packages
sudo apt install --yes neovim

# Install network utilities (dig, nslookup, ping, ncat)
sudo apt install --yes dnsutils iputils-ping ncat

# Install packages to interact with SQLite databases
sudo apt install --yes sqlite3

# Install missing PHP extensions (PHP is from the Docker official image)
sudo apt install --yes default-libmysqlclient-dev
sudo docker-php-ext-install bcmath pdo_mysql
sudo docker-php-ext-install pcntl

# docker-php-ext-enable is broken in this container; create the ini file manually
if [ ! -f /usr/local/etc/php/conf.d/docker-php-ext-pcntl.ini ]; then
    echo "extension=pcntl.so" | sudo tee /usr/local/etc/php/conf.d/docker-php-ext-pcntl.ini
fi

## Install Laravel Installer
composer global require laravel/installer

# Add Composer global bin to PATH
tee -a ~/.bashrc > /dev/null <<"EOF"
export PATH="${HOME}/.composer/vendor/bin:${PATH}"
EOF

composer setup
