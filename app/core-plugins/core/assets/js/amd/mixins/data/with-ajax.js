define(function (require, exports, module) {
  'use strict';

  var compose = require('flight').compose;
  var withUrlUtils = require('app/utils/mixins/data/with-url-utils');
  var $ = require('jquery');
  var Q = require('q');

  // Exports: mixin definition
  module.exports = withAjax;


  function withAjax() {

    //mix withUrlUtils into withAjax
    compose.mixin(this, [withUrlUtils]);

    this.ajax = function (settings) {
      settings.context = this;
      settings.url = this.getAppUrl(settings.url);
      return $.ajax(settings);
    };

    this.ajaxPromise = function (settings) {
      return Q(this.ajax(settings));
    };

  }

});