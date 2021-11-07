const crypto = require("crypto");
const uuid = require("uuid");
const bcrypt = require("bcrypt");
const aws = require("aws-sdk");
aws.config.update({ region: "eu-central-1" });

// Creates a random uuid without dashes.
function createUuid() {
  return uuid.v4();
}

// Hashes and salts the provided password.
function hashPassword(password) {
  let salt = bcrypt.genSaltSync(10);
  let hash = bcrypt.hashSync(password, salt);
  return hash;
}

// Validates the provided password.
function validatePassword(password) {
  let re = /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[a-zA-Z0-9]{8,64}$/;
  return re.test(password);
}

// Generates a random token and returns its value encoded in base64.
function createToken() {
  return crypto.randomBytes(64).toString("base64");
}

// Generates a random token and returns its value encoded in hex.
function createTokenHex() {
  return crypto.randomBytes(64).toString("hex");
}

// Hashes the provided token and returns its value encoded in hexadecimal.
function hashToken(token) {
  return crypto.createHash("sha256").update(token).digest("hex");
}

async function validateEmail(email, token) {
  try {
    let params = {
      Destination: {
        ToAddresses: [email],
      },
      Message: {
        Subject: {
          Charset: "UTF-8",
          Data: "Spotin: almost there!",
        },
        Body: {
          Html: {
            Charset: "UTF-8",
            Data: `<p>Please, confirm your <a href='https://www.spotin.ch/users/validate/${token}'>email</a>.</p>`,
          },
        },
      },
      Source: "noreply@spotin.ch",
      ReplyToAddresses: ["noreply@spotin.ch"],
    };
    await new aws.SES({ apiVersion: "2010-12-01" }).sendEmail(params).promise();
  } catch (error) {
    console.log(error);
  }
}

module.exports = {
  createUuid,
  hashPassword,
  validatePassword,
  createToken,
  createTokenHex,
  hashToken,
  validateEmail,
};
