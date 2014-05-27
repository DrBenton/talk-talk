define(function (require, exports, module) {
  "use strict";

  var _ = require("lodash");

  // Exports: mixin definition
  module.exports = withStringUtils;


  function withStringUtils() {

    this.handleStringVars = function (string, varsHash) {
      _.forEach(varsHash, function (value, key) {
        string = string.replace(new RegExp(key, "g"), value);
      });
      return string;
    };

  }

});