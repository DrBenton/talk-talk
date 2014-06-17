define(function (require, exports, module) {
  'use strict';

  var _ = require('lodash');

  // Exports: mixin definition
  module.exports = withUniqueClass;


  function withUniqueClass() {

    this.addUniqueClass = function ($jqElement) {
      var uuid = _.uniqueId('uclass-');
      $jqElement.addClass(uuid);
      return uuid;
    };

  }

});