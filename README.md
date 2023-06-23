# Spot in

Spot in is a place for registering spots. Spots can take place in the virtual or in the physical world. They capture the context and the circumstances that form the setting for an event. Each spot receives a stable Uniform Resource Identifier (URI) that can be used for referencing purposes. Spots can contain arbitrary metadata describing a context and circumstances. Additionally, spots can redirect the user to the original source of information. Once created, spots are searchable by places and by keywords.

## Prerequisites

The following prerequisites must be filled to run this service:

- [Docker](https://docs.docker.com/get-docker/) must be installed.
- [Docker Compose](https://docs.docker.com/compose/install/) must be installed (it should be installed by default with Docker in most cases).
- [Visual Studio Code](https://code.visualstudio.com/download) must be installed.

## Start the application for local development

Open this folder in Visual Studio Code, and open it in a dev container. In a terminal, run the following commands:

```bash
# Install dependencies
npm install

# Copy the default environment variables file
cp .env.defaults .env

# Start the database for local development
npm run dev:database

# Run the database migrations
npm run prisma:migrate

# Seed the database with some data
npm run prisma:seed

# Start the application in watch mode (changes to the code will be automatically reloaded)
npm run dev
```

The application should start and be accessible at <http://localhost:3000>. The API documentation is accessible at <http://localhost:3000/api>. You can log in with the credentials defined in the `.env` file (`SPOT_IN_ADMIN_EMAIL` and `SPOT_IN_ADMIN_PASSWORD`).

## Run the application in production with Docker

In a terminal, run the following commands:

```bash
# Copy the default environment variables file
cp .env.defaults .env

# Edit the .env file to your needs, especially the following variables:
# - SPOT_IN_FQDN
# - SPOT_IN_ADMIN_EMAIL
# - SPOT_IN_ADMIN_PASSWORD
# - SPOT_IN_SESSION_SECRET
# - SPOT_IN_JWT_SECRET
# - SPOT_IN_JWT_EXPIRATION_TIME
# - SPOT_IN_DATABASE_URL

# Build the application with Docker
docker compose build --no-cache

# Start the application with Docker
docker compose up --detach

# Optional: seed the database with some data
docker compose exec spotin npm run prisma:seed
```
