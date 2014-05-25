/**
 * This is just a single entry point for our other Ajax navigation components
 */
define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var ajaxLinksHandler = require("./ajax-links-handler");
  var ajaxContentLoader = require("./ajax-content-loader");
  var ajaxFormsHandler = require("./ajax-forms-handler");
  var ajaxHistory = require("./ajax-history");
  var logger = require("logger");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(ajaxNavigation);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function ajaxNavigation() {

    // Component initialization
    this.after("initialize", function() {
      ajaxLinksHandler.attachTo(this.$node);
      ajaxContentLoader.attachTo(this.$node);
      ajaxFormsHandler.attachTo(this.$node);
      ajaxHistory.attachTo(this.$node);
    });
  }

});