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
# Install dependencies, copy the environment variables file, generate the
# application key, and run the database migrations
composer setup

# Start the application in watch mode (changes to the code will be automatically reloaded)
php artisan serve
```

The application should start and be accessible at <http://localhost:8000>. You
can log in with the credentials defined in the `.env` file (`MAIL_FROM_ADDRESS`
and the password set during setup).
