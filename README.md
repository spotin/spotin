# Spot in

## Resources

- [PostGIS - PRisma](https://freddydumont.com/blog/prisma-postgis#installing-postgis)
- [Prisma - convert query parameter](https://www.prisma.io/docs/guides/upgrade-guides/upgrading-versions/upgrading-to-prisma-4#upgrade-path-7)

## Introduction

SpotIn is a place for registering spots. Spots can take place in the virtual or in the physical world. They capture the context and the circumstances that form the setting for an event. Each spot receives a stable Uniform Resource Identifier (URI) that can be used for referencing purposes. Spots can contain arbitrary metadata describing a context and circumstances. Additionally, spots can redirect the user to the original source of information. Once created, spots are searchable by places and by keywords.

## Prerequisites

The following prerequisites must be filled to run this service:

[Docker](https://docs.docker.com/get-docker/) must be installed.

[Visual Studio Code](https://code.visualstudio.com/download) must be installed

## Installation

Open this folder in Visual Studio Code, and open it in a dev contianer

```bash
npm install

cp .env.default .env

docker-compose up database -d
```

## Running the app

```bash
# development
npm run start

# watch mode
npm run start:dev

# production mode
npm run start:prod
```

## Access the documentation

The API documentation is on <http://localhost:3000/api>.

## Test

```bash
# unit tests
npm run test

# e2e tests
npm run test:e2e

# test coverage
npm run test:cov
```
