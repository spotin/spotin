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
# TODO: Use `--legacy-peer-deps` until `nunjucks` is updated.
npm install --legacy-peer-deps
