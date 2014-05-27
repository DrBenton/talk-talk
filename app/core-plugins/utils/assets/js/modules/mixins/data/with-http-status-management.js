define(function (require, exports, module) {
  "use strict";

  var $ = require("jquery");

  // Exports: mixin definition
  module.exports = withHttpStatusManagement;

  var SUCCESS_HTTP_STATUS_CODE = 200;

  function withHttpStatusManagement() {

    this.getCurrentPageHttpStatusCode = function() {
      return parseInt($("#app-http-status-code").data("code"), 10);
    };

    this.isCurrentPageAnError = function() {
      return SUCCESS_HTTP_STATUS_CODE !== this.getCurrentPageHttpStatusCode();
    };

  }

});