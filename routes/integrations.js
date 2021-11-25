let express = require("express");
let passport = require("passport");
let utils = require("../utils/utils");
let errors = require("../utils/errors");
let middlewares = require("../utils/middlewares");
let router = express.Router();
let { User, Spot } = require("../models");
let { Op } = require("sequelize");

router.get("/",
  middlewares.isAuthenticated,
  async function (req, res, next) {
    res.render("integrations/index", {
      title: "Integrate spots",
    });
  }
);

router.get("/:username/map.html", async function (req, res, next) {
  try {
    res.render("integrations/map", {
      layout: false,
    });
  } catch (error) {
    next(error);
  }
});

router.get(
  "/:username/spots.geojson",
  async function (req, res, next) {
    try {
      const spots = await Spot.findAll({
        where: {
          UserUsername: req.params.username,
        },
      });
      const features = spots.map((spot) => {
        return {
          type: "Feature",
          properties: {
            uuid: spot.uuid,
            title: spot.title,
            redirection: spot.redirection,
            description: spot.description,
            timestamp: spot.timestamp,
            color: spot.timestamp == null ? '#8FDE0D' : '#00C3FF'
          },
          geometry: {
            type: "Point",
            coordinates: [
              spot.coordinates.coordinates[0],
              spot.coordinates.coordinates[1],
            ],
          },
        };
      });
      res.json({
        type: "geojson",
        data: {
          type: "FeatureCollection",
          features: features,
        }
      });
    } catch (error) {
      next(error);
    }
  }
);

router.get("/:username/permanent.html", async function (req, res, next) {
  try {
    const spots = await Spot.findAll({
      where: {
        UserUsername: req.params.username,
        timestamp: { [Op.eq]: null }
      },
      order: [["created_at", "ASC"]],
    });
    res.render("integrations/list", {
      layout: false,
      spots: spots,
    });
  } catch (error) {
    next(error);
  }
});


router.get("/:username/ponctual.html", async function (req, res, next) {
  try {
    const spots = await Spot.findAll({
      where: {
        UserUsername: req.params.username,
        timestamp: { [Op.not]: null }
      },
      order: [["timestamp", "ASC"]],
    });
    res.render("integrations/list", {
      layout: false,
      spots: spots,
    });
  } catch (error) {
    next(error);
  }
});

module.exports = router;
