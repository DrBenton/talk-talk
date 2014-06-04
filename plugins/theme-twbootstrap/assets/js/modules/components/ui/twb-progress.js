define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var _ = require("lodash");
  var logger = require("logger");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(twitterBootstrapProgress);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function twitterBootstrapProgress() {

    this.defaultAttrs({
      progressBarSelector: ".progress-bar"
    });

    this.setProgress = function (percentage) {
      percentage = parseInt(percentage, 10);

      this.select("progressBarSelector")
        .attr("aria-valuenow", percentage)
        .css("width", percentage + "%")
        .text(percentage + "%");
    };

    // Component initialization
    this.after("initialize", function() {
      this.node.setProgress = _.bind(this.setProgress, this);
    });

    this.after("teardown", function() {
      delete this.$node.setProgress;
    });
  }

});