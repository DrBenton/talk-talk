define(function (require, exports, module) {
  'use strict';

  var compose = require('flight').compose;
  var withAppData = require('app/core/mixins/data/with-app-data');
  var purl = require('purl');

  // Exports: mixin definition
  module.exports = withUrlUtils;


  function withUrlUtils() {

    //mix withAppData into withUrlUtils
    compose.mixin(this, [withAppData]);

    this.normalizeUrl = function(rawUrl) {
      var url = purl(rawUrl);
      var returnedUrl = url.attr('path');
      var query = url.attr('query');
      returnedUrl += query ? '?' + query : '' ;
      return returnedUrl;
    };

    this.getAppUrl = function (url) {
      if (url.match(/^(https?:)?\/\//)) {
        // We don't modify absolute URLs...
        return url;
      }
      // ... but we prepend the app root URL to other URLs :-)
      return this.getAppData()['rootUrl'] + url;
    };

  }

});