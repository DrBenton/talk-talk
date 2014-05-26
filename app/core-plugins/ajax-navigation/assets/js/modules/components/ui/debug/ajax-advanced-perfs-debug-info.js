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

    this.onAjaxContentLoadingDone = function(ev, data) {
      this.$node.find(".current-action-url").text(data.url);
    };

    this.onjQueryAjaxSuccess = function(ev, data) {
      // Lets handle our specific "X-Perfs-*" headers!

      var headersDomSelectorsMaping = {
        "X-Perfs-Duration": ".perfs-script-duration",
        "X-Perfs-Bootstrap-Duration": ".perfs-bootstrap-duration",
        "X-Perfs-Plugins-Init-Duration": ".perfs-plugins-init.duration",
        "X-Perfs-Script-Nb-Included-Files": ".perfs-script-nb-included-files",
        "X-Perfs-Bootstrap-Nb-Included-Files": ".perfs-bootstrap-nb-included-files",
        "X-Perfs-Plugins-Init-Nb-Included-Files": ".perfs-plugins-init-nb-included-files",
        "X-Perfs-Session-Content": ".session-content",
        "X-Perfs-Nb-Plugins": ".nb-plugins",
        "X-Perfs-Nb-Plugins-Permanently-Disabled": ".nb-plugins-permanently-disabled",
        "X-Perfs-Nb-Plugins-Disabled-For-Current-URL": ".nb-plugins-disabled-for-current-url",
        "X-Perfs-Nb-Actions-Registered": ".nb-actions-registered"
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
      this.on(document, "ajaxContentLoadingDone", this.onAjaxContentLoadingDone);
      this.on(document, "ajaxSuccess", this.onjQueryAjaxSuccess);
    });
  }

});