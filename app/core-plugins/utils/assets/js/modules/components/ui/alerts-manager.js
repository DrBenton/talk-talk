define(function (require, exports, module) {
  "use strict";

  var defineComponent = require('flight').component;
  var $ = require("jquery");
  var logger = require("logger");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(alertsManager);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function alertsManager() {

    this.loadAlertDisplay = function (ev, data) {
      data.msgVars = data.msgVars || {};
      data.type = data.type || 'info';

      var sentAlertsData = [
        {transKey: data.msgTranslationKey, vars: data.msgVars, type: data.type}
      ];

      $.ajax({
        url: '/utils/get-ajax-alerts-display',
        data: { alerts: sentAlertsData },
        type: 'POST',
        dataType: 'text'
      })
        .then(
          _.bind(this.onAlertDisplayLoadSuccess, this),
          _.bind(this.onAlertDisplayLoadError, this)
        );
    };

    this.onAlertDisplayLoadSuccess = function (alertsJavascriptManagementCode) {
      // We create a temporary HTML container for our alerts JS management code
      var $alertsJsManagementCodeContainer = $("<div></div>");
      // We add this container after our $node...
      this.$node.after($alertsJsManagementCodeContainer);
      // ...We put the JS management code in the container...
      $alertsJsManagementCodeContainer.html(alertsJavascriptManagementCode);
      // ...And we will clear this temporary container in a moment!
      _.delay(function() {
        $alertsJsManagementCodeContainer.remove();
      }, 1000);
    };

    this.onAlertDisplayLoadError = function () {
      logger.warn("Alert display loading failed!");
    };

    this.clearAlerts = function () {
      this.$node.empty();
    };

    // Component initialization
    this.after('initialize', function() {
      this.on(document, 'alertDisplayRequested', this.loadAlertDisplay);
      this.on(document, 'alertsClearingRequested', this.clearAlerts);
      this.on(document, 'mainContentUpdate', this.clearAlerts);
    });
  }

});