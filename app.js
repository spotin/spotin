let dotenv = require("dotenv");
dotenv.config();

let createError = require("http-errors");
let express = require("express");
let partials = require("express-partials");
let session = require("express-session");
let flash = require("connect-flash");

let path = require("path");
let cors = require("cors");
let cookieParser = require("cookie-parser");
let logger = require("morgan");
let openapi = require("express-openapi");
let swaggerUi = require("swagger-ui-express");
let bcrypt = require("bcrypt");
let passport = require("passport");
let LocalStrategy = require("passport-local").Strategy;
var GitHubStrategy = require("passport-github2").Strategy;

let middlewares = require("./utils/middlewares");
let indexRouter = require("./routes/index");
let usersRouter = require("./routes/users");
let tokensRouter = require("./routes/tokens");
let spotsRouter = require("./routes/spots");
let integrationsRouter = require("./routes/integrations");
let { User } = require("./models");

let app = express();

// view engine setup
app.set("views", path.join(__dirname, "views"));
app.set("view engine", "ejs");

app.use(partials());
app.use(flash());
app.use(logger("dev"));
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: false }));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, "public")));

app.use(middlewares.cleanPostParameters);
//app.use(middlewares.setResponseVariables);

// passport authentication
passport.use(
  new LocalStrategy(
    {
      usernameField: "username",
      passwordField: "password",
    },
    async function (username, password, done) {
      try {
        let user = await User.findOne({
          where: { username: username, confirmed: true },
        });
        if (user.comparePassword(password)) {
          return done(null, user);
        } else {
          return done(null, false, {
            message: "Incorrect username or password.",
          });
        }
      } catch (error) {
        return done(null, false, {
          message: "Incorrect username or password.",
        });
      }
    }
  )
);

passport.serializeUser(function (user, done) {
  delete user.password;
  done(null, JSON.stringify(user));
});

passport.deserializeUser(function (user, done) {
  done(null, JSON.parse(user));
});

// Github Auth
passport.use(
  new GitHubStrategy(
    {
      clientID: process.env.GITHUB_CLIENT_ID, //"clientID",
      clientSecret: process.env.GITHUB_CLIENT_SECRET, //"clientSecret",
      callbackURL: process.env.GITHUB_CALLBACK_URL, //"http://127.0.0.1:3000/auth/github/callback"
    },
    function (accessToken, refreshToken, profile, done) {
      done(null, profile);
    }
  )
);

/*
app.get(
  "/auth/github",
  passport.authenticate("github", { scope: ["user:email"] })
);

app.get(
  "/auth/github/callback",
  passport.authenticate("github", { failureRedirect: "/login" }),
  function (req, res) {
    // Successful authentication, redirect home.
    res.redirect("/");
  }
);
*/

app.use(
  session({
    secret: process.env.APP_SESSION_SECRET,
    resave: false,
    saveUninitialized: false,
    rolling: true,
    name: "sid",
    cookie: {
      httpOnly: true,
      maxAge: 60 * 60 * 1000,
      // TODO: secure: true,
    },
  })
);

app.use(passport.initialize());
app.use(passport.session());

app.use((req, res, next) => {
  res.locals.user = req.user;
  res.locals.flash = {
    info: req.flash("info"),
    success: req.flash("success"),
    error: req.flash("error"),
  };
  next();
});

// token authentication
app.use("/api/spots", middlewares.validateToken);

// initialize openapi
openapi.initialize({
  apiDoc: require("./api/api-doc"),
  app: app,
  dependencies: {},
  paths: [
    { path: "/spots", module: require("./api/spots") },
    { path: "/spots/{uuid}", module: require("./api/spots/{uuid}") },
  ],
});

// initialize swagger ui
app.use(
  "/swagger-ui",
  swaggerUi.serve,
  swaggerUi.setup(null, {
    swaggerOptions: {
      url: "/api/api-docs",
    },
  })
);

// router setup
app.use("/", indexRouter);
app.use("/users", usersRouter);
app.use("/tokens", tokensRouter);
app.use("/spots", spotsRouter);
app.use("/integrations", integrationsRouter);

// catch 404 and forward to error handler
app.use(function (req, res, next) {
  next(createError(404));
});

// error handler
app.use(function (err, req, res, next) {
  // set locals, only providing error in development
  res.locals.message = err.message;
  res.locals.error = req.app.get("env") === "development" ? err : {};

  // render the error page
  res.status(err.status || 500);
  res.render("error", { title: "Error" });
});

module.exports = app;
