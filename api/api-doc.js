const apiDoc = {
  swagger: "2.0",
  basePath: "/api",
  info: {
    description: "Documentation of the Spotin API",
    title: "Spotin API",
    version: "0.0.1",
  },
  definitions: {
    Spot: {
      type: "object",
      title: "A spot",
      properties: {
        description: {
          type: "string",
          description: "Description",
        },
        latitude: {
          type: "number",
          format: "float",
          description: "Latitude",
        },
        longitude: {
          type: "number",
          format: "float",
          description: "Longitude",
        },
        redirect: {
          type: "string",
          description: "Redirect",
        },
        referenced: {
          type: "boolean",
          description: "Referenced",
        },
        timestamp: {
          type: "string",
          format: "date-time",
          description: "Timestamp",
        },
        title: {
          type: "string",
          description: "Title",
        },
        uuid: {
          type: "string",
          description: "Uuid",
        },
      },
      "x-go-package": "main/src/internal/models",
    },
  },
  securityDefinitions: {
    apiKey: {
      type: "apiKey",
      name: "x-api-key",
      in: "header",
    },
  },
  security: [{ apiKey: [] }],
  paths: {},
};

module.exports = apiDoc;
