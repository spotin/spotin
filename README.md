# Spot in®

Spot in® is a place for registering spots. Spots can take place in the virtual
or in the physical world. They capture the context and the circumstances that
form the setting for an event. Each spot receives a stable Uniform Resource
Identifier (URI) that can be used for referencing purposes. Spots can contain
arbitrary metadata describing a context and circumstances. Additionally, spots
can redirect the user to the original source of information. Once created, spots
are searchable by places and by keywords.

## Prerequisites

The following prerequisites must be filled to run this service:

- [Docker](https://docs.docker.com/get-docker/) must be installed.
- [Visual Studio Code](https://code.visualstudio.com/download) must be
  installed.
- [Visual Studio Code Remote - Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)
  extension must be installed.

## Start the application for local development

Open this folder in Visual Studio Code, and open it in a dev container. In a
terminal, run the following commands:

```bash
# Start the database and SMTP server for local development
docker compose up --detach mailpit

# Install the dependencies with npm and Composer
npm install && npm run build
composer install

# Copy the .env.example file to .env
cp .env.example .env

# Modify the environment variables if necessary (optional)

# Generate the Laravel application key
php artisan key:generate

# Create the symbolic link for uploaded files
php artisan storage:link

# Create the database and run migrations
php artisan migrate

# If you need to reset the database, use:
php artisan migrate:reset
php artisan migrate

# Optional: seed the database with fake data
php artisan db:seed

# Start the Laravel development server
composer run dev
```

The application is accessible at <http://localhost:8000>. The Vite development
server is accessible at <http://localhost:5173>. The mail server interface is
accessible at <http://localhost:8025>.
