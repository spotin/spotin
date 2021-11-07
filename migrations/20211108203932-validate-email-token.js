"use strict";

module.exports = {
  up: async (queryInterface, Sequelize) => {
    await queryInterface.addColumn("users", "validate_email", {
      type: Sequelize.STRING,
      allowNull: true,
    });
    queryInterface.addIndex("users", ["validate_email"]);
    await queryInterface.addColumn("users", "reset_password", {
      type: Sequelize.STRING,
      allowNull: true,
    });
    queryInterface.addIndex("users", ["reset_password"]);
  },

  down: async (queryInterface, Sequelize) => {
    await queryInterface.removeColumn("users", "validate_email");
    await queryInterface.removeColumn("users", "reset_password");
  },
};
