define(function (require, exports, module) {
  "use strict";

  var $ = require("jquery");

  // Exports: mixin definition
  module.exports = withAjax;


  function withAjax() {

    this.ajax = function (settings) {
      settings.context = this;
      return $.ajax(settings);
    };

  }

});