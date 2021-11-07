"use strict";

const utils = require("../utils/utils");
const { Model } = require("sequelize");

module.exports = (sequelize, DataTypes) => {
  class Spot extends Model {
    static associate(models) {
      this.belongsTo(models.User);
    }
  }
  Spot.init(
    {
      uuid: {
        type: DataTypes.UUID,
        primaryKey: true,
      },
      title: {
        type: DataTypes.STRING,
        allowNull: false,
        validate: {
          notEmpty: true,
        },
      },
      description: {
        type: DataTypes.STRING,
        allowNull: true,
      },
      coordinates: {
        type: DataTypes.GEOMETRY("POINT", 4326),
        allowNull: true,
        validate: {
          isValid(value) {
            if (value && (!value.coordinates[0] || !value.coordinates[1])) {
              throw new Error("Coordinates must be valid.");
            }
          },
          isInBounds(value) {
            if (
              value &&
              (value.coordinates[0] < -180 ||
                value.coordinates[0] > 180 ||
                value.coordinates[1] < -90 ||
                value.coordinates[1] > 90)
            ) {
              throw new Error("Coordinates must be in bounds.");
            }
          },
        },
      },
      timestamp: {
        type: DataTypes.DATE,
        allowNull: true,
      },
      redirection: {
        type: DataTypes.STRING,
        allowNull: true,
        validate: {
          isUrl: true,
        },
      },
      referenced: {
        type: DataTypes.BOOLEAN,
        allowNull: false,
        defaultValue: false,
      },
    },
    {
      sequelize,
      modelName: "Spot",
      paranoid: true,
      underscored: true,
    }
  );
  return Spot;
};
