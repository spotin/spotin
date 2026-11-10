[Spot in® Documentation](../README.md) > How-to guides

# How-to guides

This document provides a series of how-to guides to assist you in performing
specific tasks and achieving particular goals within the project.

> [!WARNING]
>
> This is a work in progress.

## Table of contents

- [Table of contents](#table-of-contents)
- [Set up a development environment on your local machine](#set-up-a-development-environment-on-your-local-machine)
  - [Prerequisites](#prerequisites)
  - [Clone the repository](#clone-the-repository)
  - [Open the project in a Dev Container in Visual Studio Code](#open-the-project-in-a-dev-container-in-visual-studio-code)
  - [Start the application for local development](#start-the-application-for-local-development)
  - [Update the application](#update-the-application)
- [Set up a production environment on the Infomaniak server](#set-up-a-production-environment-on-the-infomaniak-server)
  - [Order a new Infomaniak web hosting plan](#order-a-new-infomaniak-web-hosting-plan)
  - [Create a new site](#create-a-new-site)
  - [Change the site default directory](#change-the-site-default-directory)
  - [Create a new SSH user](#create-a-new-ssh-user)
  - [Access the Infomaniak hosting via SSH](#access-the-infomaniak-hosting-via-ssh)
  - [Install Node.js](#install-nodejs)
  - [Delete the site default files](#delete-the-site-default-files)
  - [Checklist](#checklist)
- [Deploy the application to the Infomaniak server](#deploy-the-application-to-the-infomaniak-server)
  - [Clone the repository](#clone-the-repository-1)
  - [Set up the application](#set-up-the-application)
  - [Access the application](#access-the-application)
  - [Checklist](#checklist-1)
- [Set up GitHub Actions for automatic deployment](#set-up-github-actions-for-automatic-deployment)
  - [Create an environment in the GitHub repository](#create-an-environment-in-the-github-repository)
  - [Run the deployment workflow](#run-the-deployment-workflow)
- [Migrate the data of the previous application](#migrate-the-data-of-the-previous-application)
  - [Export the tables of the previous application](#export-the-tables-of-the-previous-application)
  - [Map the users to their organizations](#map-the-users-to-their-organizations)
  - [Rehearse the import](#rehearse-the-import)
  - [Import the data](#import-the-data)
  - [Checklist](#checklist-2)

## Set up a development environment on your local machine

### Prerequisites

The following prerequisites must be filled to run this project:

- [Git](https://git-scm.com/downloads) must be installed.
- [Docker](https://docs.docker.com/get-docker/) must be installed.
- [Visual Studio Code](https://code.visualstudio.com/download) must be
  installed.
- [Visual Studio Code Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)
  extension must be installed.

### Clone the repository

1. Open a terminal on your local machine.
2. Navigate to the directory where you want to clone the repository.
3. Clone the repository using the following command:

   ```bash
   git clone <repository-url>
   ```

   Replace `<repository-url>` with the URL of your Git repository. This will
   create a new directory with the repository name and clone the repository into
   it.

### Open the project in a Dev Container in Visual Studio Code

1. Open Visual Studio Code.
2. Click on "File" > "Open Folder..." and select the folder where you cloned the
   repository.
3. Click on the green "><" icon in the bottom-left corner of the Visual Studio
   Code window and select "Dev Containers: Reopen in Container". This will open
   the project in a development container, which provides a consistent
   development environment with all the necessary dependencies and tools.

### Start the application for local development

1. Open the project in a terminal within the Visual Studio Code Dev Container
   (see the previous section for instructions).
2. Run the following commands to set up and start the application:

   ```bash
   # Start the services: MariaDB, as in production, the mail server and
   # phpMyAdmin
   docker compose up --detach

   # Setup the development environment
   composer setup

   # Start the Laravel development server
   composer dev
   ```

3. The application is accessible at <http://localhost:8000>. The Vite
   development server is accessible at <http://localhost:5173>. The mail server
   interface is accessible at <http://localhost:8025>. phpMyAdmin is accessible
   at <http://localhost:9090>.

The application uses the `my_database` database of MariaDB, and the tests the
`testing` one, created with the MariaDB volume (see `compose.yaml`), so that
they do not empty the data of the development.

### Update the application

To update the application, run the following commands:

```bash
# Check for outdated dependencies with npm
npx npm-check-updates

# Update the dependencies with npm
npx npm-check-updates -u

# Update the dependencies with Composer
composer update
```

## Set up a production environment on the Infomaniak server

### Order a new Infomaniak web hosting plan

1. Visit the Infomaniak website: <https://www.infomaniak.com/>.
2. Choose a web hosting plan that suits your needs and click on "Order" or "Get
   started".
3. Follow the on-screen instructions to complete the order process, providing
   the necessary information and making the payment.

### Create a new site

1. Log in to your Infomaniak account: <https://manager.infomaniak.com/>.
2. Navigate to "Web & Domains" > Select your web hosting.
3. Click on "Add site".
4. Select "Create a blank project".
5. Select "Apache/PHP" as the technology stack. In the "Advanced options"
   section, choose the latest stable version of PHP (e.g., PHP 8.5).
6. Link a domain/subdomain to the new site.
7. Create the site. You will be redirected to the site management page.

### Change the site default directory

1. Access the Infomaniak Manager: <https://manager.infomaniak.com/>.
2. Navigate to "Web & Domains" > Select your web hosting > Select your site.
3. Select "Manage advanced settings".
4. Create a new directory named `public` in the site's root directory (if it
   doesn't already exist).
5. Change the site's default directory to the newly created `public` directory.
6. Save the changes. The site will now serve content from the `public`
   directory.

### Create a new SSH user

1. Access the Infomaniak Manager: <https://manager.infomaniak.com/>.
2. Navigate to "Web & Domains" > Select your web hosting > "FTP / SSH".
3. Click on "Add a user".
4. Fill in the required information (username and password). Make sure to select
   "SSH access".
5. Save the new user. You will now have an SSH user that can access your web
   hosting environment via SSH.

### Access the Infomaniak hosting via SSH

1. Identify the Infomaniak hostname for your web hosting. You can find this
   information in the Infomaniak Manager under "Web & Domains" > Select your web
   hosting > "FTP / SSH". The hostname is usually in the format
   `ftp.infomaniak.com` or similar (yes, it is the same as the FTP hostname).
2. Open a terminal on your local machine.
3. Use the following command to connect to the Infomaniak server via SSH:

   ```bash
    ssh username@hostname
   ```

   Replace `username` with the SSH username you created in the previous step and
   `hostname` with the Infomaniak hostname you identified in step 1.

4. If this is your first time connecting to the server, you may be prompted to
   accept the server's fingerprint. Type "yes" and press Enter.
5. Enter the password for the SSH user when prompted and press Enter.
6. You should now be connected to the Infomaniak server via SSH. You can execute
   commands and manage your web hosting environment as needed. The sites are
   hosted in the `~/sites` directory.

### Install Node.js

1. Connect to the Infomaniak server via SSH (see the previous section for
   instructions).
2. Create a `.bashrc` file in the home directory (if it doesn't already exist).
   This will allow to add the Node.js binary to the PATH environment variable
   for the SSH user. You can create the file using the following command:

   ```bash
   touch ~/.bashrc
   ```

3. Install Node.js using the instructions provided by the official Node.js
   website: <https://nodejs.org/en/download>.
4. Verify the installation by running the following command:

   ```bash
   node -v
   ```

   This should display the installed Node.js version.

### Delete the site default files

1. Connect to the Infomaniak server via SSH (see the previous section for
   instructions).
2. Navigate to the `~/sites` directory:

   ```bash
   cd ~/sites
   ```

3. Delete the default files and directories that were created when you set up
   the new site. For example, if you have create a new site named
   `myproject.example.com`, you can delete the default files and directories
   using the following command:

   ```bash
   rm -rf myproject.example.com
   ```

   **Note**: be cautious when using the `rm -rf` command, as it will permanently
   delete files and directories. Make sure you are in the correct directory and
   that you have specified the correct site name to avoid accidentally deleting
   important files.

### Checklist

- [x] Order a new Infomaniak web hosting plan.
- [x] Create a new site.
- [x] Change the site default directory.
- [x] Create a new SSH user.
- [x] Access the Infomaniak hosting via SSH.
- [x] Install Node.js.
- [x] Delete the site default files.

You have now set up a production environment on the Infomaniak server and are
ready to deploy the application.

## Deploy the application to the Infomaniak server

### Clone the repository

1. Connect to the Infomaniak server via SSH (see the previous section for
   instructions).
2. Navigate to the `~/sites` directory:

   ```bash
   cd ~/sites
   ```

3. Clone the repository using the following command:

   ```bash
   git clone <repository-url> <project-directory>
   ```

   Replace `<repository-url>` with the URL of your Git repository and
   `<project-directory>` with the initial directory name you want to use for
   your project (see the previous section for an example, e.g.,
   `myproject.example.com`). This will create a new directory with the specified
   name and clone the repository into it.

   If you need to store the Git credentials for future use (e.g., because you
   are cloning a private repository using a Personal Access Token (PAT)), you
   can use the following command to store them:

   ```bash
   git config credential.helper 'store'
   ```

   **Note**: this will store the credentials in a plain text file in your home
   directory (`~/.git-credentials`). Be cautious when using this option, as it
   may pose a security risk if others have access to your account or machine.

### Set up the application

1. Connect to the Infomaniak server via SSH (see the previous section for
   instructions).
2. Navigate to the project directory (the directory where you cloned the
   repository):

   ```bash
   cd ~/sites/<project-directory>
   ```

3. Run the following command to set up the application:

   ```bash
   composer setup-prod
   ```

4. Copy the example `.env.example` file to create a new `.env` file:

   ```bash
   cp .env.example .env
   ```

5. Invalid the application cache by running the following command:

   ```bash
   php artisan optimize:clear
   ```

6. Generate a new application key by running the following command:

   ```bash
   php artisan key:generate
   ```

7. Set up the environment variables.

   **Note**: make sure to update the `.env` file with the correct values for
   your production environment, such as database credentials, mail server
   settings, and any other necessary configurations. Do not forget to set the
   `APP_ENV` variable to `production` and the `APP_DEBUG` variable to `false` in
   the `.env` file.

8. Run the following command to migrate the database:

   ```bash
   php artisan migrate
   ```

9. Optional: if you need to seed the database with initial data, run the
   following command:

   ```bash
   php artisan db:seed
   ```

10. Run the following command to optimize the application for production:

    ```bash
    php artisan optimize
    ```

11. Reload any running services to apply the changes:

    ```bash
    php artisan reload
    ```

### Access the application

1. Open a web browser and navigate to the domain or subdomain you linked to the
   site during the setup process (e.g., `https://myproject.example.com`).
2. You should see the application running in the production environment. If you
   encounter any issues, check the server logs and the application logs for
   troubleshooting.

### Checklist

- [x] Clone the repository.
- [x] Set up the application.
- [x] Test the application to ensure it is running correctly in the production
      environment.

## Set up GitHub Actions for automatic deployment

### Create an environment in the GitHub repository

1. Go to your GitHub repository and click on the "Settings" tab.
2. In the left sidebar, click on "Environments".
3. Click on "New environment" and give it a name (e.g., `production`).
4. Set the environment protection rules:
   - Required reviewers: add the users or teams that must approve deployments to
     this environment (recommended for production environments, optional for
     development environments).
   - Wait time: set a wait time before the deployment can proceed (recommended
     for production environments, optional for development environments).
5. Set the deployment branches and tags:
   - Deployment branches: specify the branches that are allowed to deploy to
     this environment (`main` for production, `*` for development).
6. Add the following secrets to the environment:
   - `SSH_HOST`: The hostname of your Infomaniak server.
   - `SSH_USERNAME`: The SSH username you created for accessing the Infomaniak
     server.
   - `SSH_PASSWORD`: The password for the SSH user.
7. Add the following variables to the environment:
   - `APP_DOMAIN`: The domain or subdomain linked to your Infomaniak site (e.g.,
     `myproject.example.com`).

### Run the deployment workflow

The deployment is triggered automatically by the workflow defined in the
`.github/workflows/main.yaml` file, once the tests have run.

Two deployment jobs are defined in this workflow:

1. The `deploy-development` job is triggered by pull requests. It runs
   `composer setup`, which recreates the database from scratch and seeds it with
   the development data: all the existing data is lost at each deployment. The
   `deploy-development` job is intended for development and testing purposes
   only.
2. The `deploy-production` job is triggered when you push changes to the `main`
   branch. It runs `composer setup-prod` and applies the pending migrations
   (`php artisan migrate --force`). The database is not seeded.

## Migrate the data of the previous application

The previous Spot in® application, a NestJS one on PostgreSQL (last commit
`d463ba11e814f3d3fca1a0b9f99916609870d2cf`), had users and spots, but no
organizations. The `spotin:import-legacy` command imports its users and spots,
with the organizations you give them in a mapping.

The spots keep their UUID, which their printed QR codes hold: the QR codes keep
working, as long as the application is served at the same address as the
previous one (`APP_URL` must be the former `SPOT_IN_BASE_URL`). The deleted
spots are imported as deleted: their QR codes tell they are no longer in use.

The API tokens are not imported: their applications have to use the new API with
new tokens anyway.

### Export the tables of the previous application

Export the `users` and `spots` tables as JSON, in a new directory:

```sh
mkdir legacy

psql "$SPOT_IN_DATABASE_URL" -At -c "SELECT COALESCE(json_agg(t), '[]') FROM users t" > legacy/users.json
psql "$SPOT_IN_DATABASE_URL" -At -c "SELECT COALESCE(json_agg(t), '[]') FROM spots t" > legacy/spots.json
```

These files hold the hashes of the passwords: keep them private, and delete them
once the import is done and checked.

### Map the users to their organizations

Create the `legacy/organizations.csv` file, with one line per user and
organization:

```csv
email,organization,role

# Museum
alice@example.com,Museum,responsible
bob@example.com,Museum,member
former@example.com,Museum,skip-user

# Library
bob@example.com,Library,responsible

# Administrators
root@example.com,,

# Accounts created by bots
spam@example.com,,skip
```

- Every user of the previous application must be in it: the import stops
  otherwise, and lists the missing ones.
- The `role` is `responsible` or `member`. Each organization needs at least one
  responsible user.
- The spots of a user go to their first organization in the file.
- A user without spots, such as an administrator, can have no organization:
  leave `organization` and `role` empty.
- A user can be left out of the import:
  - with the role `skip` and no organization, with their spots, if any, such as
    an account created by a bot through the former open registration. The QR
    codes of their spots lead to a page not found: only skip spots that were
    never printed, such as tests;
  - with the role `skip-user` and the organization of their spots, which are
    imported without a creator, such as someone who left an institution whose
    spots are still in use. Their QR codes keep working, and the members of the
    organization manage them.
- The organizations are created by their name, or found if one already has it.
- Blank lines and lines starting with `#` are ignored: use them to split the
  file into parts. The errors tell the line of the file they are about.

### Rehearse the import

Run the import without importing anything, on a copy of the production database
first:

```sh
php artisan spotin:import-legacy legacy --dry-run
```

It lists the problems that stop the import (users missing from the mapping,
email addresses that are the same once lowercased, organizations without a
responsible user), or tells what it would create, with what to check afterwards:

- the disabled and deleted users of the previous application, who are imported
  and can log in;
- the payloads of the spots that are not JSON objects, left out (the payloads
  become the custom data of the spots, in the API only);
- the spots that have the name of another spot of their organization: the names
  must be unique in an organization, so they are numbered, such as
  `Fitnessraum (1)` and `Fitnessraum (2)`, in the order they were created. Their
  members rename them to tell what they are;
- the spots that had a single coordinate, such as a latitude without a
  longitude, imported without a location: their members can set it on the map;
- the websites without a scheme, such as `www.example.com`, which get
  `https://`: their QR codes would lead to a page of this application otherwise;
- the spots set up without a name, shown as unnamed: their form asks for one.

### Import the data

1. Stop the previous application, or make it read-only, so that nothing changes
   after the export.
2. Export the tables again, as above.
3. Import them:

   ```sh
   php artisan spotin:import-legacy legacy
   ```

   The import can be run again: what already exists is left as it is (users
   found by their UUID or email address, organizations by their name, spots by
   their UUID). An existing account, such as the administrator of the production
   seeder, keeps its password and becomes an administrator if the previous one
   was.

4. Point the domain of the previous application to this one.

The users log in with their former email address and password. Their password is
hashed again with the options of this application at their first login.

### Checklist

- [ ] The tables are exported after the previous application was stopped.
- [ ] The dry run reports no problem, and its warnings are understood.
- [ ] `APP_URL` is the former `SPOT_IN_BASE_URL`, with the same scheme.
- [ ] A few printed QR codes lead where they did, including the one of a deleted
      spot and the one of a spot not set up yet.
- [ ] A former user logs in with their former password.
- [ ] The exported files are deleted.
