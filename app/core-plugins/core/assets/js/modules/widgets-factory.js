/**
 * @see http://paceyourself.net/2011/05/14/managing-client-side-javascript-with-requirejs/
 */
define(function (require, exports, module) {

  var logger = require("logger");
  var varsRegistry = require("app-modules/core/vars-registry");

  var myDebug = !false;

  myDebug && logger.debug(module.id, "on the bridge, captain!");

  var loadModule = function ($jqElement) {
    var moduleName = $jqElement.data("widget")

    require([moduleName], function (module) {
      module.createWidget($jqElement);
    });
  };

  exports.findAndTriggerWidgets = function ($widgetsContainer) {
    $widgetsContainer = $widgetsContainer || varsRegistry.$document;
    var $dataModules = $widgetsContainer.find(".requirejs-widget");
    var nbDataModules = $dataModules.length;

    myDebug && logger.debug(module.id, nbDataModules + " widgets found.");

    for (var i = 0; i < nbDataModules; i++) {
      loadModule($dataModules.eq(i));
    }
  };

});
