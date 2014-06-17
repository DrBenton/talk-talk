define(function (require, exports, module) {
  'use strict';

  var $ = require('jquery');
  var Q = require('q');

  // Exports: mixin definition
  module.exports = withAjax;


  function withAjax() {

    this.ajax = function (settings) {
      settings.context = this;
      return $.ajax(settings);
    };

    this.ajaxPromise = function (settings) {
      return Q(this.ajax(settings));
    };

  }

});