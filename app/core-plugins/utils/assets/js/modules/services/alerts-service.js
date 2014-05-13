define(function (require, exports, module) {

  var $ = require("jquery");
  var varsRegistry = require("app-modules/core/vars-registry");

  var $notificationsContainer = varsRegistry.$document.find("#notifications-container");

  exports.clearAlerts = function () {
    $notificationsContainer.empty();
  };

  exports.addAlert = function (msgTranslationKey, msgVars, type) {
    msgVars = msgVars || {};
    type = type || 'info';

    var sentAlertsData = [
      {transKey: msgTranslationKey, vars: msgVars, type: type}
    ];

    $.ajax({
      url: '/utils/get-alerts-display',
      data: { alerts: sentAlertsData },
      type: 'POST',
      dataType: 'text'
    })
      .done(function (alertsJavascriptManagementCode) {
        varsRegistry.$mainContentContainer.append(alertsJavascriptManagementCode);
      });
  };

});