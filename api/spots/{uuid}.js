const utils = require("../../utils/utils");
const { Spot } = require("../../models");

module.exports = function () {
  let operations = {
    parameters: [
      {
        name: "uuid",
        in: "path",
        type: "string",
        required: true,
        description: "The spot uuid.",
      },
    ],
    get,
    put,
    del,
  };

  async function get(req, res) {
    let spot = Spot.findByPk(req.params.id);
    res.status(200).json(spot);
  }

  get.apiDoc = {
    summary: "Read a spot by id.",
    operationId: "readSpots",
    responses: {
      200: {
        description: "The spot.",
        schema: {
          type: "object",
          $ref: "#/definitions/Spot",
        },
      },
    },
  };

  async function put(req, res, next) {
    let coordinates;
    if (req.body.longitude || req.body.latitude) {
      coordinates = {
        type: "Point",
        coordinates: [req.body.longitude, req.body.latitude],
      };
    }
    let spot = await Spot.update(
      {
        title: req.body.title,
        description: req.body.description,
        coordinates: coordinates,
        timestamp: req.body.timestamp,
        redirection: req.body.redirection,
        referenced: req.body.referenced,
      },
      {
        where: {
          uuid: req.params.spotUuid,
          // TODO: user_username: req.user.username,
        },
      }
    );
    res.status(200).json(spot);
  }

  put.apiDoc = {
    summary: "Update a spot by id.",
    operationId: "updateSpot",
    responses: {
      200: {
        description: "The list of spots.",
        schema: {
          type: "array",
          items: {
            $ref: "#/definitions/Spot",
          },
        },
      },
    },
  };

  function del(req, res, next) {
    res.status(200).json("");
  }

  del.apiDoc = {
    summary: "Delete a spot by id.",
    operationId: "deleteSpot",
    responses: {
      200: {
        description: "The list of spots.",
        schema: {
          type: "array",
          items: {
            $ref: "#/definitions/Spot",
          },
        },
      },
    },
  };

  return operations;
};
