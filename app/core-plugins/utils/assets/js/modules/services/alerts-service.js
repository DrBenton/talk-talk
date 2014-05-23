define(function (require, exports, module) {

  var $ = require("jquery");
  var varsRegistry = require("app-modules/core/vars-registry");

  // Exports
  exports.clearAlerts = clearAlerts;
  exports.addAlert = addAlert;


  function getAlertsContainer() {
    return varsRegistry.$document.find("#alerts-container");
  }

  function clearAlerts() {
    getAlertsContainer().empty();
  }

  function addAlert(msgTranslationKey, msgVars, type) {
    msgVars = msgVars || {};
    type = type || 'info';

    var sentAlertsData = [
      {transKey: msgTranslationKey, vars: msgVars, type: type}
    ];

    $.ajax({
      url: '/utils/get-ajax-alerts-display',
      data: { alerts: sentAlertsData },
      type: 'POST',
      dataType: 'text'
    })
      .done(function (alertsJavascriptManagementCode) {
        getAlertsContainer().after(alertsJavascriptManagementCode);
      });
  }

});