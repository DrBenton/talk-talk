define(function (require, exports, module) {
  'use strict';

  var $ = require('jquery');

  var registry = {};

  // Some pre-defined values...
  registry.$document = $(document);
  registry.$head = $('head');
  registry.$body = $('body');
  registry.$siteContainer = $('#site-container');
  registry.$mainContentContainer = $('#main-content-container');
  registry.$mainContent = $('#main-content');
  registry.$breadcrumb = $('#breadcrumb');
  registry.$debugInfoContainer = $('#debug-info-container');


  module.exports = registry;

});
