const utils = require("../../utils/utils");
const { Spot } = require("../../models");

module.exports = function () {
  let operations = {
    get,
    post,
  };

  async function get(req, res, next) {
    let spots = await Spot.findAll({
      order: [["created_at", "DESC"]],
      limit: 100,
    });
    res.status(200).json(spots);
  }

  get.apiDoc = {
    summary: "List spots.",
    operationId: "listSpots",
    responses: {
      200: {
        description: "The spots.",
        schema: {
          type: "array",
          items: {
            $ref: "#/definitions/Spot",
          },
        },
      },
    },
  };

  async function post(req, res, next) {
    let uuid = utils.createUuid();
    let coordinates;
    if (req.body.longitude || req.body.latitude) {
      coordinates = {
        type: "Point",
        coordinates: [req.body.longitude, req.body.latitude],
      };
    }
    let spot = Spot.create({
      uuid: uuid,
      title: req.body.title,
      description: req.body.description,
      coordinates: coordinates,
      timestamp: req.body.timestamp,
      redirection: req.body.redirection,
      referenced: req.body.referenced,
      // TODO: UserUsername: req.user.username,
    });
    spot = await spot.save();
    res.status(200).json(spot);
  }

  post.apiDoc = {
    summary: "Create a new spot",
    operationId: "createSpot",
    consumes: ["application/json"],
    parameters: [
      {
        in: "body",
        name: "spot",
        schema: {
          $ref: "#/definitions/Spot",
        },
      },
    ],
    responses: {
      200: {
        description: "The list of spot.",
        schema: {
          type: "object",
          $ref: "#/definitions/Spot",
        },
      },
    },
  };

  return operations;
};
