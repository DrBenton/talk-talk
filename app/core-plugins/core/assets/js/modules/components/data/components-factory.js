define(function (require, exports, module) {
  "use strict";

  var defineComponent = require('flight').component;
  var _ = require("lodash");
  var logger = require("logger");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(componentsFactory);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function componentsFactory() {

    this.loadComponents = function ($jqElement) {
      var componentsName = $jqElement.data("component")

      myDebug && logger.debug(module.id, "loadComponents(" + componentsName + ")");
      var componentsToAttach = componentsName.split(',');
      require(componentsToAttach, function () {
        _.forEach(arguments, function (component, i) {
          if (!component || !component.attachTo) {
            myDebug && logger.warn(module.id, "Invalid component \""+componentsToAttach[i]+"\" spotted!");
            return;
          }
          component.attachTo($jqElement);
        });
      });
    };

    this.searchAndTriggerWidgets = function () {
      var $dataModules = this.$node.find(".flight-component");
      var nbDataModules = $dataModules.length;

      myDebug && logger.debug(module.id, nbDataModules + " pending Flight components found.");

      for (var i = 0; i < nbDataModules; i++) {
        this.loadComponents($dataModules.eq(i));
      }
    };

    // Component initialization
    this.after('initialize', function() {
      this.on('widgetsSearchRequested', this.searchAndTriggerWidgets);
    });
  }

});