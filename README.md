# Spot in®

Spot in® is a place for registering spots. Spots can take place in the virtual or
in the physical world. They capture the context and the circumstances that form
the setting for an event. Each spot receives a stable Uniform Resource
Identifier (URI) that can be used for referencing purposes. Spots can contain
arbitrary metadata describing a context and circumstances. Additionally, spots
can redirect the user to the original source of information. Once created, spots
are searchable by places and by keywords.

## Prerequisites

The following prerequisites must be filled to run this service:

- [Docker](https://docs.docker.com/get-docker/) must be installed.
- [Docker Compose](https://docs.docker.com/compose/install/) must be installed
  (it should be installed by default with Docker in most cases).
- [Visual Studio Code](https://code.visualstudio.com/download) must be
  installed.

## Start the application for local development

Open this folder in Visual Studio Code, and open it in a dev container. In a
terminal, run the following commands:

```bash
# Install dependencies
# `--legacy-peer-deps` is used until `nunjucks` is updated.
npm install --legacy-peer-deps

# Copy the default environment variables file
cp .env.defaults .env

# Start the database and smtp server for local development
npm run dev:up

# Run the database migrations
npm run prisma:migrate

# Seed the database with some data
npm run prisma:seed

# Start the application in watch mode (changes to the code will be automatically reloaded)
npm run dev
```

The application should start and be accessible at <http://localhost:3000>. The
API documentation is accessible at <http://localhost:3000/api>. You can log in
with the credentials defined in the `.env` file (`SPOT_IN_ADMIN_EMAIL` and
`SPOT_IN_ADMIN_PASSWORD`). The mail server interface is accessible at <http://localhost:8025>.

## Try out the application in development with Docker

This is not recommended for development, but you can try out the application in
development with Docker.

Do not forget to set the environment variables as described in the previous
section.

```bash
# Build the application with Docker
docker compose build spotin

# Edit the .env file to your needs

# Start the application with Docker
docker compose up spotin

# Optional: seed the database with some data
docker compose exec spotin npm run prisma:seed
```
