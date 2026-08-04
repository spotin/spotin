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

## Git configuration
# Set pull to rebase by default
git config pull.rebase true
