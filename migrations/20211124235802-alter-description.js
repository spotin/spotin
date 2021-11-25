'use strict';

module.exports = {
  up: async (queryInterface, Sequelize) => {
    await queryInterface.changeColumn('spots', 'description', {
        type: Sequelize.TEXT,
        allowNull: true,
    });
  },

  down: async (queryInterface, Sequelize) => {
    // Nothing to do
  }
};
