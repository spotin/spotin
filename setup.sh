echo "Starting a Postgres server with Docker ..."

docker run \
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

cp .env.defaults .env

echo "Starting the server with:"
echo "  npm start"
echo "Access the application at: http://localhost:3000"

npm start
