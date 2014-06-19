define(function (require, exports, module) {
  'use strict';

  // Exports: mixin definition
  module.exports = withAppData;

  var appData = module.config();

  function withAppData() {

    this.getAppData = function () {
      return appData;
    };

  }

});