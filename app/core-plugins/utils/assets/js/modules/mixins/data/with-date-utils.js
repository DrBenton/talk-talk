define(function (require, exports, module) {
  "use strict";

  // Exports: mixin definition
  module.exports = withDateUtils;


  function withDateUtils() {

    this.getDuration = function(startDate) {
      var durationMs = (new Date).getTime() - startDate.getTime();
      return durationMs / 1000;
    };

  }

});