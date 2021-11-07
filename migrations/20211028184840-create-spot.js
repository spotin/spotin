"use strict";
module.exports = {
  up: async (queryInterface, Sequelize) => {
    await queryInterface.createTable("spots", {
      uuid: {
        type: Sequelize.UUID,
        allowNull: false,
        primaryKey: true,
      },
      title: {
        type: Sequelize.STRING,
      },
      description: {
        type: Sequelize.STRING,
      },
      coordinates: {
        type: Sequelize.GEOMETRY,
      },
      timestamp: {
        type: Sequelize.DATE,
      },
      redirection: {
        type: Sequelize.STRING,
      },
      referenced: {
        type: Sequelize.BOOLEAN,
      },
      user_username: {
        type: Sequelize.STRING,
        references: {
          model: "users",
          key: "username",
        },
      },
      created_at: {
        allowNull: false,
        type: Sequelize.DATE,
      },
      updated_at: {
        allowNull: false,
        type: Sequelize.DATE,
      },
      deleted_at: {
        allowNull: true,
        type: Sequelize.DATE,
      },
    });
  },
  down: async (queryInterface, Sequelize) => {
    await queryInterface.dropTable("spots");
  },
};
