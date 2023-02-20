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
$ npm install
```

## Running the app

```bash
# development
$ npm run start

# watch mode
$ npm run start:dev

# production mode
$ npm run start:prod
```

## Test

```bash
# unit tests
$ npm run test

# e2e tests
$ npm run test:e2e

# test coverage
$ npm run test:cov
```

```sh
curl -X 'POST' \
  'http://localhost:3000/api/spots' \
  -H 'accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{
  "description": "string",
  "latitude": 0,
  "longitude": 0,
  "redirect": "string",
  "referenced": true,
  "timestamp": "2023-02-20T14:31:10.811Z",
  "title": "string",
  "uuid": "string"
}'

curl \
	http://localhost:3000/api/spots \
	-X GET \
	-v

curl \
http://localhost:3000/api/spots/<uuid> \
-X GET \
-v
```
