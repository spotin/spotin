#!/usr/bin/env sh

# ## MkDocs dependencies
# pip3 install \
#     cairosvg \
#     mkdocs-git-revision-date-localized-plugin \
#     mkdocs-glightbox \
#     mkdocs-material \
#     mkdocs-minify-plugin \
#     pillow

## Node.js

# Global
npm install --global @nestjs/cli

# Project
# `--legacy-peer-deps` is used until `nunjucks` is updated.
npm install --legacy-peer-deps
