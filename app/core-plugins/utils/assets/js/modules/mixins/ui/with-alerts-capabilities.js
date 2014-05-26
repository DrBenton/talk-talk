define(function (require, exports, module) {
  "use strict";

  // Exports: mixin definition
  module.exports = withAlertsCapabilities;


  function withAlertsCapabilities() {

    this.displayTranslatedAlert = function(msgTranslationKey, msgVars, type) {
      this.trigger(document, "uiNeedsTranslatedAlertDisplay", {
        msgTranslationKey: msgTranslationKey,
        msgVars: msgVars,
        type: type
      });
    };

    this.clearAlerts = function() {
      this.trigger(document, "uiNeedsAlertClearing");
    };

  }

});