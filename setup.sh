echo "Starting a Postgres server with Docker ..."

docker run \
  --name spotin \
  --publish 5432:5432 \
  -e POSTGRES_DB=spotin \
  -e POSTGRES_USER=spotin \
  -e POSTGRES_PASSWORD=spotin \
  -d postgis/postgis:latest

echo "Installing nodejs requirements ..."

npm install 

npx \
  sequelize-cli \
  db:migrate \
  db:migrate:undo \
  db:migrate:undo:all \
  db:seed:all

echo "Writing a default .env file ..."

touch .env
echo "APP_SESSION_SECRET=ch@ng3me!plz" >> .env

echo "Starting the server with:"
echo "  npm start"
echo "Access the application at: http://localhost:3000"

npm start
