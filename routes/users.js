let express = require("express");
let passport = require("passport");
let utils = require("../utils/utils");
let errors = require("../utils/errors");
let middlewares = require("../utils/middlewares");
let router = express.Router();
let { User, Spot } = require("../models");

router.get("/login", function (req, res) {
  res.render("users/login", { title: "Login" });
});

router.post(
  "/login",
  passport.authenticate("local", {
    successRedirect: "/spots/list",
    successFlash: `Welcome!`,
    failureRedirect: "/users/login",
    failureFlash: "Invalid username or password.",
  })
);

router.get("/signup", function (req, res) {
  res.render("users/signup", {
    title: "Sign up",
    values: {},
    errors: {},
  });
});

router.post("/signup", async function (req, res) {
  try {
    let token = utils.createTokenHex();
    await User.create({
      username: req.body.username,
      email: req.body.email,
      password: req.body.password,
      validate_email: utils.hashToken(token),
    });
    utils.validateEmail(req.body.email, token);
    await req.flash(
      "success",
      "Welcome to SpotIn! Please, check your email and confirm your address before loggin in."
    );
    res.redirect("/users/login");
  } catch (error) {
    res.render("users/signup", {
      title: "Sign up",
      values: req.body,
      errors: errors.asErrorMap(error.errors),
    });
  }
});

router.get("/validate/:token", async function (req, res) {
  try {
    await User.update(
      {
        confirmed: true,
        validate_email: null,
      },
      {
        where: {
          validate_email: utils.hashToken(req.params.token),
        },
      }
    );
    await req.flash(
      "success",
      "Congratulations! Your account has been activated."
    );
    res.redirect("/users/login");
  } catch (error) {
    await req.flash("error", "A problem occured while creating your account.");
    res.redirect("/users/signup");
  }
});

router.get("/profile", middlewares.isAuthenticated, function (req, res) {
  res.render("users/profile", { title: "Login" });
});

router.get("/logout", function (req, res) {
  req.logout();
  res.redirect("/");
});

module.exports = router;
