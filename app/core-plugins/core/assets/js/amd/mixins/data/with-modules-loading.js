define(function (require, exports, module) {
  'use strict';

  var _ = require('lodash');
  var Q = require('q');
  var logger = require('logger');

  // Exports: mixin definition
  module.exports = withModulesLoading;


  function withModulesLoading() {

    this.loadModules = function(modulesIds) {
      var deferred = Q.defer();

      require(
        modulesIds,
        function () {
          var loadedModules = arguments;
          loadedModules = Array.prototype.slice.call(loadedModules, 0);//arguments -> Array
          deferred.resolve.call(null, loadedModules);
        },
        function (err) {
          logger.error('Modules loading failed: ' + err.requireModules.join(', '));
          deferred.reject(err);
        }
      );

      return deferred.promise;
    };

  }

});