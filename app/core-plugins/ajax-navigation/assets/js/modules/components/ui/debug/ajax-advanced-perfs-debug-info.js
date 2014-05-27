define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var _ = require("lodash");
  var logger = require("logger");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(ajaxAdvancedPerfsDebugInfo);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function ajaxAdvancedPerfsDebugInfo() {

    this.onjQueryAjaxSuccess = function(ev, data, jqXHR) {

      this.$node.find(".current-action-url").text(jqXHR.type + " " + jqXHR.url);

      // Lets handle our specific "X-Perfs-*" headers!

      var headersDomSelectorsMaping = {
        "X-Perfs-Elapsed-Time-Now": ".perfs-elapsed-time-now",
        "X-Perfs-Elapsed-Time-Bootstrap": ".perfs-elapsed-time-bootstrap",
        "X-Perfs-Elapsed-Time-Plugins-Init": ".perfs-elapsed-time-plugins-init",
        "X-Perfs-Nb-Included-Files-Now": ".perfs-nb-included-files-now",
        "X-Perfs-Nb-Included-Files-Bootstrap": ".perfs-nb-included-files-bootstrap",
        "X-Perfs-Nb-Included-Files-Plugins-Init": ".perfs-nb-included-files-plugins-init",
        "X-Perfs-Nb-Plugins": ".nb-plugins",
        "X-Perfs-Nb-Plugins-Permanently-Disabled": ".nb-plugins-permanently-disabled",
        "X-Perfs-Nb-Plugins-Disabled-For-Current-URL": ".nb-plugins-disabled-for-current-url",
        "X-Perfs-Nb-Actions-Registered": ".nb-actions-registered",
        "X-Perfs-Session-Content": ".session-content"
      };

      _.forEach(headersDomSelectorsMaping, _.bind(function(domSelector, headerName) {
        this.$node.find(domSelector).text(
          data.getResponseHeader(headerName)
        );
      }, this));

      // X-Perfs-QueryPath-Duration
      var queryPathDuration = data.getResponseHeader("X-Perfs-QueryPath-Duration");
      if (queryPathDuration) {
        this.$node.find(".query-path-duration")
          .text(queryPathDuration)
          .closest("li").removeClass("hidden");
      }
    };

    // Component initialization
    this.after("initialize", function() {
      this.on(document, "ajaxSuccess", this.onjQueryAjaxSuccess);
    });
  }

});