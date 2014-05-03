define(function (require, exports, module) {

  var $ = require("jquery");

  var registry = {};

  // Some pre-defined values...
  registry.$document = $(document);
  registry.$mainContentContainer = $("#main-content-container");
  registry.$debugInfoContainer = $("#debug-info-container");


  module.exports = registry;

});
