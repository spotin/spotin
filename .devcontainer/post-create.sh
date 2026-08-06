#!/usr/bin/env bash

## Configure Bash
# Aliases for common commands
tee -a ~/.bash_aliases > /dev/null <<"EOF"
alias tree='tree --dirsfirst -A -F'
EOF

## Install required packages
# Update packages list
sudo apt update

# Install common packages
sudo apt install --yes neovim

# Install network utilities (dig, nslookup, ping, ncat)
sudo apt install --yes dnsutils iputils-ping ncat

# Install packages to interact with SQLite databases
sudo apt install --yes sqlite3 php-sqlite3

# Install PHP and Composer
sudo apt install --yes php-common php-cli php-mysql php-xml php-bcmath composer

## Install Laravel Installer
composer global require laravel/installer

# Add Composer global bin to PATH
tee -a ~/.bashrc > /dev/null <<"EOF"
export PATH="${HOME}/.composer/vendor/bin:${PATH}"
EOF

## Git configuration
# Set pull to rebase by default
git config pull.rebase true
