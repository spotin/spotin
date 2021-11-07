let { Token } = require("../models");
let { hashToken } = require("../utils/utils");

// A middleware for cleaning empty parameters from request bodies.
async function cleanPostParameters(req, res, next) {
  if (req.body) {
    for (let key in req.body) {
      if (req.body[key] === "") {
        delete req.body[key];
      }
    }
  }
  next();
}

// A middleware for checking  the access to a route.
async function isAuthenticated(req, res, next) {
  if (req.user) {
    next();
  } else {
    res.redirect("/users/login");
  }
}

// A middleware  for validating the provided token.
async function validateToken(req, res, next) {
  let value = req.headers["x-api-key"];
  if (!value) {
    res.status(401).send("Unauthorized");
    return;
  }
  let token = await Token.findOne({
    where: {
      hash: hashToken(value),
    },
  });
  if (!token) {
    res.status(401).send("Unauthorized");
    return;
  }
  req.user = {
    username: token.UserUsername,
  };
  next();
}

// A middleware for setting variables commonly reused in response templates.
async function setResponseVariables(req, res, next) {
  res.locals.user = req.user;
  res.locals.flash = {
    info: req.flash("info"),
    success: req.flash("success"),
    error: req.flash("error"),
  };
  next();
}

module.exports = {
  cleanPostParameters,
  isAuthenticated,
  setResponseVariables,
  validateToken,
};
