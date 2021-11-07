const express = require("express");
const sequelize = require("sequelize");
const router = express.Router();
const qrcode = require("qrcode");
const errors = require("../utils/errors");
const middlewares = require("../utils/middlewares");
const utils = require("../utils/utils");
const { Spot } = require("../models");

router.get("/latest", async function (req, res, next) {
  const spots = await Spot.findAll({
    where: {
      referenced: true,
    },
    order: [["created_at", "DESC"]],
    limit: 100,
  });
  res.render("spots/latest", {
    title: "Spots",
    spots: spots,
  });
});

router.get(
  "/list",
  middlewares.isAuthenticated,
  async function (req, res, next) {
    const spots = await Spot.findAll({
      where: {
        UserUsername: req.user.username,
      },
      order: [["created_at", "DESC"]],
    });
    res.render("spots/list", {
      title: "Spots",
      spots: spots,
    });
  }
);

router.get(
  "/create",
  middlewares.isAuthenticated,
  async function (req, res, next) {
    res.render("spots/form", {
      title: "Create spot",
      action: "/spots/create",
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
      let uuid = utils.createUuid();
      let coordinates;
      if (req.body.longitude || req.body.latitude) {
        coordinates = {
          type: "Point",
          coordinates: [req.body.longitude, req.body.latitude],
        };
      }
      let spot = await Spot.create({
        uuid: uuid,
        title: req.body.title,
        description: req.body.description,
        coordinates: coordinates,
        timestamp: req.body.timestamp,
        redirection: req.body.redirection,
        referenced: req.body.referenced,
        UserUsername: req.user.username,
      });
      req.flash("success", "The spot has been created");
      res.redirect(`/spots/${spot.uuid}`);
    } catch (error) {
      res.render("spots/form", {
        title: "Create spot",
        action: "/spots/create",
        values: req.body,
        errors: errors.asErrorMap(error.errors),
      });
    }
  }
);

router.get(
  "/:spotUuid/edit",
  middlewares.isAuthenticated,
  async function (req, res, next) {
    try {
      const spot = await Spot.findByPk(req.params.spotUuid);
      res.render("spots/form", {
        title: "Edit spot",
        action: `/spots/${req.params.spotUuid}/edit`,
        values: Object.assign(spot, {
          longitude: spot.coordinates.coordinates[0],
          latitude: spot.coordinates.coordinates[1],
        }),
        errors: {},
      });
    } catch (error) {
      next(error);
    }
  }
);

router.post(
  "/:spotUuid/edit",
  middlewares.isAuthenticated,
  async function (req, res, next) {
    try {
      let coordinates;
      if (req.body.longitude || req.body.latitude) {
        coordinates = {
          type: "Point",
          coordinates: [req.body.longitude, req.body.latitude],
        };
      }
      await Spot.update(
        {
          title: req.body.title,
          description: req.body.description ? req.body.description : null,
          coordinates: coordinates,
          timestamp: req.body.timestamp,
          redirection: req.body.redirection ? req.body.redirection : null,
          referenced: req.body.referenced,
        },
        {
          where: {
            uuid: req.params.spotUuid,
            UserUsername: req.user.username,
          },
        }
      );
      res.redirect("/spots/list");
    } catch (error) {
      res.render("spots/form", {
        title: "Edit spot",
        action: `/spots/${req.params.spotUuid}/edit`,
        values: req.body,
        errors: errors.asErrorMap(error.errors),
      });
    }
  }
);

router.get(
  "/:spotUuid/delete",
  middlewares.isAuthenticated,
  async function (req, res, next) {
    try {
      await Spot.destroy({
        where: {
          uuid: req.params.spotUuid,
          UserUsername: req.user.username,
        },
      });
      res.redirect("/spots/list");
    } catch (error) {
      next(error);
    }
  }
);

router.get("/:spotUuid/redirect", async function (req, res, next) {
  try {
    const spot = await Spot.findByPk(req.params.spotUuid, {
      paranoid: false,
    });
    if (spot.redirection) {
      res.redirect(spot.redirection);
    } else {
      res.redirect(`https://www.spotin.ch/spots/${spot.uuid}`);
    }
  } catch (error) {
    next(error);
  }
});

router.get("/:spotUuid", async function (req, res, next) {
  try {
    const spot = await Spot.findByPk(req.params.spotUuid);
    const spotUrl = `https://www.spotin.ch/spots/${spot.uuid}/redirect`;
    qrcode.toDataURL(
      spotUrl,
      { width: 128, height: 128, margin: 0 },
      function (err, qrcodeUrl) {
        if (err == null) {
          res.render("spots/view", {
            title: "Spot",
            spot: spot,
            qrcode: qrcodeUrl,
          });
        } else {
          next(err);
        }
      }
    );
  } catch (error) {
    next(error);
  }
});

module.exports = router;
