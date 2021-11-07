const { assert } = require("chai");
const { createToken, hashToken } = require("./utils");

describe("utils.tokens", function () {
  describe("#createToken()", function () {
    it("should create a random token", function () {
      assert.equal(128, createToken().length);
    });
  });
  describe("#hashToken()", function () {
    it("should create a random token", function () {
      assert.equal(64, hashToken("token").length);
    });
  });
});
