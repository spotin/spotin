"use strict";

const { query } = require("express");

module.exports = {
  up: async (queryInterface, Sequelize) => {
    await queryInterface.sequelize.query(
      "CREATE EXTENSION IF NOT EXISTS postgis;"
    );
  },
  down: async (queryInterface, Sequelize) => {
    await queryInterface.sequelize.query("DROP EXTENSION IF EXISTS postgis;");
  },
};
