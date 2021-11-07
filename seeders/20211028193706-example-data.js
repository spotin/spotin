"use strict";

const helpers = require("../utils/utils");

module.exports = {
  up: async (queryInterface, Sequelize) => {
    let username = "johndoe";
    await queryInterface.bulkInsert(
      "users",
      [
        {
          username: username,
          email: "john@doe.com",
          password: helpers.hashPassword("password"),
          confirmed: true,
          created_at: new Date(),
          updated_at: new Date(),
        },
      ],
      {}
    );
    await queryInterface.bulkInsert(
      "tokens",
      [
        {
          uuid: helpers.createUuid(),
          hash: helpers.hashToken("token1"),
          name: "Token 1",
          user_username: username,
          created_at: new Date(),
          updated_at: new Date(),
        },
        {
          uuid: helpers.createUuid(),
          hash: helpers.hashToken("token2"),
          name: "Token 2",
          user_username: username,
          created_at: new Date(),
          updated_at: new Date(),
        },
      ],
      {}
    );
    await queryInterface.bulkInsert(
      "spots",
      [
        {
          uuid: helpers.createUuid(),
          title: "Lausanne",
          description: "Ville de Lausanne",
          coordinates: Sequelize.fn("ST_GeomFromText", "POINT(6.6323 46.5197)"),
          redirection: "https://www.lausanne.ch/",
          referenced: true,
          timestamp: new Date(),
          user_username: username,
          created_at: new Date(Date.UTC(2021, 1, 1)),
          updated_at: new Date(Date.UTC(2021, 1, 2)),
        },
        {
          uuid: helpers.createUuid(),
          title: "Yverdon",
          description: "Ville de Yverdon-les-bains",
          coordinates: Sequelize.fn("ST_GeomFromText", "POINT(6.6412 46.7785)"),
          redirection: "https://www.yverdon-les-bains.ch//",
          referenced: true,
          timestamp: new Date(),
          user_username: username,
          created_at: new Date(Date.UTC(2021, 1, 3)),
          updated_at: new Date(Date.UTC(2021, 1, 4)),
        },
      ],
      {}
    );
  },
  down: async (queryInterface, Sequelize) => {
    await queryInterface.bulkDelete("spots", null, {});
    await queryInterface.bulkDelete("tokens", null, {});
    await queryInterface.bulkDelete("users", null, {});
  },
};
