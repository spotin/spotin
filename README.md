docker run \
 --name spotin \
 --publish 5432:5432 \
 -e POSTGRES_DB=spotin \
 -e POSTGRES_USER=spotin \
 -e POSTGRES_PASSWORD=spotin \
 -d postgis/postgis:latest

npm install
npx sequelize-cli db:migrate
npx sequelize-cli db:migrate:undo
npx sequelize-cli db:migrate:undo:all
npx sequelize-cli db:seed:all
