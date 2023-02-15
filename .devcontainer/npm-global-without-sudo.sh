#!/usr/bin/env bash
# https://github.com/sindresorhus/guides/blob/main/npm-global-without-sudo.md

mkdir "${HOME}/.npm-packages"

npm config set prefix "${HOME}/.npm-packages"

tee --append "${HOME}/.bashrc" > /dev/null <<EOT

NPM_PACKAGES="\${HOME}/.npm-packages"

export PATH="\$PATH:\$NPM_PACKAGES/bin"
EOT
