let express = require("express");
let passport = require("passport");
let utils = require("../utils/utils");
let errors = require("../utils/errors");
let middlewares = require("../utils/middlewares");
let router = express.Router();
let { User, Spot } = require("../models");

router.get(
  "/integration",
  middlewares.isAuthenticated,
  async function (req, res, next) {
    res.render("maps/integration", {
      title: "Integrate spots",
    });
  }
);

router.get("/users/:username/features", async function (req, res, next) {
  try {
    res.render("maps/map", {
      layout: false,
    });
  } catch (error) {
    next(error);
  }
});

router.get(
  "/users/:username/features.geojson",
  async function (req, res, next) {
    try {
      const spots = await Spot.findAll({
        where: {
          UserUsername: req.params.username,
          referenced: true,
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
            "marker-symbol": "venue-map-icon-blue",
            colour: "black",
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
        type: "FeatureCollection",
        features: features,
      });
    } catch (error) {
      next(error);
    }
  }
);

router.get("/spots/:spotUuid/features", async function (req, res, next) {
  try {
    res.render("maps/map", {
      layout: false,
    });
  } catch (error) {
    next(error);
  }
});

router.get(
  "/spots/:spotUuid/features.geojson",
  async function (req, res, next) {
    try {
      const spot = await Spot.findByPk(req.params.spotUuid);
      res.json({
        type: "FeatureCollection",
        features: [
          {
            type: "Feature",
            properties: {
              uuid: spot.uuid,
              title: spot.title,
              redirection: spot.redirection,
              description: spot.description,
              "marker-symbol": "venue-map-icon-blue",
              colour: "black",
            },
            geometry: {
              type: "Point",
              coordinates: [
                spot.coordinates.coordinates[0],
                spot.coordinates.coordinates[1],
              ],
            },
          },
        ],
      });
    } catch (error) {
      next(error);
    }
  }
);

module.exports = router;
