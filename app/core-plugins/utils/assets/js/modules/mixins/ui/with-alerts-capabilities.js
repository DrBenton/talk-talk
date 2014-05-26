define(function (require, exports, module) {
  "use strict";

  // Exports: mixin definition
  module.exports = withAlertsCapabilities;


  function withAlertsCapabilities() {

    this.displayAlert = function(msgTranslationKey, msgVars, type) {
      this.trigger(document, "alertDisplayRequested", {
        msgTranslationKey: msgTranslationKey,
        msgVars: msgVars,
        type: type
      });
    };

    this.clearAlerts = function() {
      this.trigger(document, "alertsClearingRequested");
    };

  }

});