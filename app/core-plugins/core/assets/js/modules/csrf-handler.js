define(function (require, exports, module) {

  var varsRegistry = require("app-modules/core/vars-registry");
  var logger = require("logger");

  var myDebug = !false;
  var CSRF_PROTECTED_METHODS = ["POST", "PUT", "DELETE"];

  // Exports
  exports.init = init;

  myDebug && logger.debug(module.id, "on the bridge, captain!");

  function getToken() {
    return varsRegistry.$head.find("meta[name='csrf-token']").attr('content');
  }

  function onjQueryAjaxSend(event, jqXHR, ajaxOptions) {
    if (CSRF_PROTECTED_METHODS.indexOf(ajaxOptions.type) !== -1) {
      var csrfToken = getToken();
      myDebug && logger.debug(module.id, "CSRF token '" + csrfToken + "' attached to jQuery Ajax request.");
      jqXHR.setRequestHeader("X-CSRF-Token", csrfToken);
    }
  }

  function init() {
    varsRegistry.$document.bind("ajaxSend", onjQueryAjaxSend);
  }

});

