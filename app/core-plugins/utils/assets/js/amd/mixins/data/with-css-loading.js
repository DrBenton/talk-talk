define(function (require, exports, module) {
  'use strict';

  var _ = require('lodash');
  var Q = require('q');

  // Exports: mixin definition
  module.exports = withCssLoading;


  function withCssLoading() {

    this.loadStylesheets = function(styleSheetsUrls) {
      var deferred = Q.defer();

      var requiredAssetsUrls = [];
      _.forEach(styleSheetsUrls, function(cssUrl) {
        requiredAssetsUrls.push('css!' + cssUrl);
      });

      require(
        requiredAssetsUrls,
        function () {
          deferred.resolve(styleSheetsUrls);
        },
        function (err) {
          deferred.reject(new Error('CSS loading failed: ' + err.requireModules.join(', ')));
        }
      );

      return deferred.promise;
    };

  }

});