const express = require("express");
const router = express.Router();
const { Token } = require("../models");
const errors = require("../utils/errors");
const middlewares = require("../utils/middlewares");
const utils = require("../utils/utils");

router.get("/list", async function (req, res, next) {
  const tokens = await Token.findAll({
    where: {
      UserUsername: req.user.username,
    },
    order: [["name", "ASC"]],
  });
  res.render("tokens/list", {
    title: "API keys",
    tokens: tokens,
  });
});

router.get(
  "/create",
  middlewares.isAuthenticated,
  async function (req, res, next) {
    res.render("tokens/form", {
      title: "Create API key",
      values: {},
      errors: {},
    });
  }
);

router.post(
  "/create",
  middlewares.isAuthenticated,
  async function (req, res, next) {
    try {
      let value = utils.createToken();
      let hash = utils.hashToken(value);
      const token = Token.build({
        uuid: utils.createUuid(),
        name: req.body.name,
        hash: hash,
        UserUsername: req.user.username,
      });
      await token.save();
      req.flash(
        "success",
        "Store the value of the API key somewhere safe, it won't show up again!"
      );
      req.flash("token", value);
      res.redirect(`/tokens/${token.uuid}`);
    } catch (error) {
      res.render("tokens/form", {
        title: "Create API key",
        values: req.body,
        errors: errors.asErrorMap(error.errors),
      });
    }
  }
);

router.get(
  "/:tokenUuid/delete",
  middlewares.isAuthenticated,
  async function (req, res, next) {
    try {
      await Token.destroy({
        where: {
          uuid: req.params.tokenUuid,
          UserUsername: req.user.username,
        },
      });
      res.redirect("/tokens/list");
    } catch (error) {
      next(error);
    }
  }
);

router.get(
  "/:tokenUuid",
  middlewares.isAuthenticated,
  async function (req, res, next) {
    try {
      const token = await Token.findOne({
        where: {
          uuid: req.params.tokenUuid,
          UserUsername: req.user.username,
        },
      });
      token.value = req.flash("token");
      res.render("tokens/view", {
        title: "View API key",
        token: token,
      });
    } catch (error) {
      next(error);
    }
  }
);

module.exports = router;
