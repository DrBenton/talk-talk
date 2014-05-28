define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var $ = require("jquery");
  var logger = require("logger");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(ajaxContentLoadingDisplay);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function ajaxContentLoadingDisplay() {

    this.defaultAttrs({
      ajaxLoadingClassTargetSelector: "#main-content-container",
      ajaxLoadingInProgressClassName: "ajax-loading-in-progress"
    });

    this.onAjaxContentLoadingStart = function (ev, data) {
      $(this.attr.ajaxLoadingClassTargetSelector)
        .addClass(this.attr.ajaxLoadingInProgressClassName);
    };

    this.onAjaxContentLoadingDone = function (ev, data) {
      $(this.attr.ajaxLoadingClassTargetSelector)
        .removeClass(this.attr.ajaxLoadingInProgressClassName);
    };

    // Component initialization
    this.after("initialize", function() {
      this.on("ajaxContentLoadingStart", this.onAjaxContentLoadingStart);
      this.on("ajaxContentLoadingDone", this.onAjaxContentLoadingDone);
    });
  }

});