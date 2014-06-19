define(function (require, exports, module) {
  'use strict';

  var compose = require('flight').compose;
  var withAjax = require('app/core/mixins/data/with-ajax');
  var _ = require('lodash');
  var Q = require('q');
  var logger = require('logger');

  // Exports: mixin definition
  module.exports = withForumsData;

  // Forums data is kept in cache
  var sharedForumsDataCache;

  function withForumsData() {

    //mix withAjax into withForumsData
    compose.mixin(this, [withAjax]);

    this.forumsData = null;

    this.getForumsData = function () {

      if (sharedForumsDataCache) {
        // Forums data have already been retrieved, and kept in a central cache; let's trop right now!
        return Q.resolve(sharedForumsDataCache);
      }

      return this.ajaxPromise({
        url: '/api/forums'
      })
        .then(_.bind(function (data) {
          sharedForumsDataCache = data;
          this.forumsData = data;
          return data;
        }, this))
        .fail(_.bind(function (err) {
          logger.error('Error on forums data retrieval!');
          throw err;
        }, this));
    };

  }

});