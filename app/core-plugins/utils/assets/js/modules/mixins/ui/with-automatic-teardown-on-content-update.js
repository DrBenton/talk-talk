define(function (require, exports, module) {
  "use strict";

  var $ = require("jquery");
  var logger = require("logger");

  // Exports: mixin definition
  module.exports = withAutomaticTeardownOnContentUpdate;

  var myDebug = !false;


  function withAutomaticTeardownOnContentUpdate() {

    this._onUiContentAboutToBeUpdated = function (ev, data) {
      var targetContentContainer = $(data.target).get(0);
      if ($.contains(targetContentContainer, this.node)) {
        myDebug && logger.debug(module.id, "My node is a child of an about-to-be-updated node: let's do seppuku! :: this.node.class=", this.$node.attr("class"));
        this.teardown();
      }
    };

    this.after("initialize", function() {
      this.on(document, "uiContentAboutToBeUpdated", this._onUiContentAboutToBeUpdated);
    });

  }

});