/**
 * A simple wrapper around locache
 */
define(function (require, exports, module) {

  var locache = require("locache");

  exports.getCacheData = function (key) {
    return locache.get(key);
  };

  exports.addCacheData = function (key, data, duration) {
    locache.set(
      key,
      data,
      duration
    );
  };

});
