function asErrorMap(errorList) {
  if (!errorList) {
    return {};
  }
  return errorList.reduce((object, error) => {
    if (!object.hasOwnProperty(error.path)) {
      object[error.path] = [];
    }
    object[error.path].push(error);
    return object;
  }, {});
}

module.exports = {
  asErrorMap,
};
