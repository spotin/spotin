"use strict";

const { Model } = require("sequelize");
const bcrypt = require("bcrypt");

module.exports = (sequelize, DataTypes) => {
  class User extends Model {
    static associate(models) {
      this.hasMany(models.Token);
      this.hasMany(models.Spot);
    }
    static hashPassword(password) {
      return bcrypt.hashSync(password, bcrypt.genSaltSync(10));
    }
    comparePassword(password) {
      return bcrypt.compareSync(password, this.password);
    }
  }
  User.init(
    {
      username: {
        type: DataTypes.STRING,
        primaryKey: true,
        allowNull: false,
        validate: {
          isAlpha: true,
          isLowercase: true,
          len: {
            args: [3],
          },
        },
      },
      email: {
        type: DataTypes.STRING,
        allowNull: false,
        validate: {
          isEmail: true,
        },
      },
      password: {
        type: DataTypes.STRING,
        allowNull: false,
        validate: {
          len: {
            args: [8],
          },
          notEmpty: {
            args: [true],
          },
          notNull: {
            args: [true],
          },
        },
      },
      confirmed: {
        type: DataTypes.BOOLEAN,
        defaultValue: false,
      },
      validate_email: {
        type: DataTypes.STRING,
        allowNull: true,
      },
      reset_password: {
        type: DataTypes.STRING,
        allowNull: true,
      },
    },
    {
      sequelize,
      modelName: "User",
      paranoid: true,
      underscored: true,
      hooks: {
        beforeCreate: (user) => {
          let salt = bcrypt.genSaltSync(10);
          let hash = bcrypt.hashSync(user.password, salt);
          user.password = hash;
        },
      },
    }
  );
  return User;
};
