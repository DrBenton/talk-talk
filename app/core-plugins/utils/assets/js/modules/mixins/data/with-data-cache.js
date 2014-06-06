define(function (require, exports, module) {
  "use strict";

  var locache = require("locache");
  var _ = require("lodash");

  // Exports: mixin definition
  module.exports = withDataCache;


  function withDataCache() {

    this.getCacheData = function (key) {
      return locache.get(key);
    };

    this.clearCacheData = function (key) {
      return locache.remove(key);
    };

    this.clearCacheDataForPrefix = function (keysPrefix) {
      var keysToClear = _.filter(locache.keys(), function(key) {
        return 0 === key.indexOf(keysPrefix);
      });
      locache.removeMany(keysToClear);
    };

    this.clearAllCacheData = function () {
      locache.flush();
    };

    this.clearExpiredData = function () {
      locache.cleanup();
    };

    this.setCacheData = function (key, data, duration) {
      locache.set(
        key,
        data,
        duration
      );
    };

  }

});