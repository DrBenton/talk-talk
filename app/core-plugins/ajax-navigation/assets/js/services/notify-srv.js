define(function (require, exports, module) {

  var varsRegistry = require("js/core/vars-registry");

  $notificationsContainer = varsRegistry.$document.find("#notifications-container");

  exports.clear = function () {
    $notificationsContainer.empty();
  };

  exports.notify = function (msg, type) {
    type = type || 'info';
    $notificationsContainer.append('<div class="alert alert-' + type + '">' + msg + '</div>');
  };

}); 