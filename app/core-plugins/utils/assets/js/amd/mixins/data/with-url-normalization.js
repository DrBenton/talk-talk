define(function (require, exports, module) {
  'use strict';

  var purl = require('purl');

  // Exports: mixin definition
  module.exports = withUrlNormalization;


  function withUrlNormalization() {

    this.normalizeUrl = function(rawUrl) {
      var url = purl(rawUrl);
      var returnedUrl = url.attr('path');
      var query = url.attr('query');
      returnedUrl += query ? '?' + query : '' ;
      return returnedUrl;
    };

  }

});