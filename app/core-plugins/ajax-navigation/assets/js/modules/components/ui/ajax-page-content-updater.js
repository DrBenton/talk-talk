define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var withAlertsCapabilities = require("app-modules/utils/mixins/ui/with-alerts-capabilities");
  var $ = require("jquery");
  var logger = require("logger");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(ajaxPageContentUpdater, withAlertsCapabilities);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function ajaxPageContentUpdater() {

    this.onContentUpdateRequest = function (ev, data) {

      var $targetContentContainer = $(data.target);
      $targetContentContainer.html(data.content);


      this.trigger(document, "uiContentUpdated", data);

      if (!data.dontScrollToTop) {
        $("html, body").animate({scrollTop:0}, "fast");
      }
      if (!data.keepAlerts) {
        this.clearAlerts();
      }
    };

    // Component initialization
    this.after("initialize", function() {
      this.on(document, "uiNeedsContentUpdate", this.onContentUpdateRequest);
    });
  }

});