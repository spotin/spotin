#!/usr/bin/env sh

## MkDocs dependencies
pip3 install \
    cairosvg \
    mkdocs-git-revision-date-localized-plugin \
    mkdocs-glightbox \
    mkdocs-material \
    mkdocs-minify-plugin \
    pillow

## npm dependencies
npm install --global @nestjs/cli

npm install
