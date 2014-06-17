define(function (require, exports, module) {
  'use strict';

  var _ = require('lodash');
  var Q = require('q');

  // Exports: mixin definition
  module.exports = withCssLoadingCapabilities;


  function withCssLoadingCapabilities() {

    this.loadStylesheets = function(styleSheetsUrls) {
      var deferred = Q.defer();

      var requiredAssetsUrls = [];
      _.forEach(styleSheetsUrls, function(cssUrl) {
        requiredAssetsUrls.push('css!' + cssUrl);
      });

      require(requiredAssetsUrls, function () {
        deferred.resolve();
      });

      return deferred.promise;
    };

  }

});