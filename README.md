# Spot in

## Introduction

SpotIn is a place for registering spots. Spots can take place in the virtual or in the physical world. They capture the context and the circumstances that form the setting for an event. Each spot receives a stable Uniform Resource Identifier (URI) that can be used for referencing purposes. Spots can contain arbitrary metadata describing a context and circumstances. Additionally, spots can redirect the user to the original source of information. Once created, spots are searchable by places and by keywords.

## Prerequisites

The following prerequisites must be filled to run this service:

[Node.js](https://nodejs.org/) must be installed.

[Docker](https://docs.docker.com/get-docker/) must be installed.

## Set up the service

```sh
# Create a dev file
cp .env.defaults .env

# Start a Postgres server with Docker
docker-compose up -d

# Install the dependencies
npm install

# Run the migrations
npx sequelize-cli db:migrate

# Seed the database for development purpose
npx sequelize-cli db:seed:all
```

## Start the service in development mode

```sh
npm run watch
```

## Start the service in production mode

```sh
npm start
```

## Stop the database server

```sh
docker-compose down
```