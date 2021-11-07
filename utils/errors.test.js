const { assert } = require("chai");
const { asErrorMap } = require("./errors");

describe("utils.errors", function () {
  describe("#asErrorMap(errorList)", function () {
    it("should create a map of errors ({}) from an list of errors ([])", function () {
      const errorList = [
        {
          message: "Validation notEmpty on hash failed",
          type: "Validation error",
          path: "hash",
          value: "",
          origin: "FUNCTION",
          validatorKey: "notEmpty",
          validatorName: "notEmpty",
        },
        {
          message: "Validation notEmpty on name failed",
          type: "Validation error 1",
          path: "name",
          value: "",
          origin: "FUNCTION",
          validatorKey: "notEmpty",
          validatorName: "notEmpty",
        },
        {
          message: "Validation notEmpty on name failed",
          type: "Validation error 2",
          path: "name",
          value: "",
          origin: "FUNCTION",
          validatorKey: "notEmpty",
          validatorName: "notEmpty",
        },
      ];
      const errorMap = asErrorMap(errorList);
      assert.equal(errorMap.hash[0].path, "hash");
      assert.equal(errorMap.hash[0].type, "Validation error");
      assert.equal(errorMap.name[0].path, "name");
      assert.equal(errorMap.name[0].type, "Validation error 1");
      assert.equal(errorMap.name[1].path, "name");
      assert.equal(errorMap.name[1].type, "Validation error 2");
    });
  });
});
