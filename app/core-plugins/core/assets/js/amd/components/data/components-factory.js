define(function (require, exports, module) {
  'use strict';

  var defineComponent = require('flight').component;
  var $ = require('jquery');
  var _ = require('lodash');
  var logger = require('logger');

  var myDebug = false;

  // Exports: component definition
  module.exports = defineComponent(componentsFactory);

  myDebug && logger.debug(module.id, 'Component on the bridge, captain!');


  function componentsFactory() {

    this.onWidgetsSearchRequest = function (ev, data) {
      var $targetComponentsContainer = (data &&data.selector)
        ? $(data.selector)
        : this.$node;
      this.searchAndTriggerWidgets($targetComponentsContainer);
    };

    this.loadComponents = function ($jqElement) {
      var componentsName = $jqElement.data('component');

      myDebug && logger.debug(module.id, 'loadComponents(' + componentsName + ')');

      var componentsToAttach = componentsName.split(',');
      componentsToAttach = _.map(componentsToAttach, function(componentName) {
        return componentName.replace(/^\//, '', componentName);
      });

      require(componentsToAttach, function () {

        _.forEach(arguments, function (component, i) {
          if (!component || !component.attachTo) {
            myDebug && logger.warn(module.id, 'Invalid component "'+componentsToAttach[i]+'" spotted!');
            return;
          }
          component.attachTo($jqElement);
          $jqElement.addClass('flight-component-attached');
        });

      });
    };

    this.searchAndTriggerWidgets = function ($componentsContainer) {
      var $dataModules = $componentsContainer.find('.flight-component')
        .not('.flight-component-attached');
      var nbDataModules = $dataModules.length;

      myDebug && logger.debug(module.id, nbDataModules + ' pending Flight components found in container ', $componentsContainer);

      for (var i = 0; i < nbDataModules; i++) {
        this.loadComponents($dataModules.eq(i));
      }
    };

    // Component initialization
    this.after('initialize', function() {
      this.on(document, 'widgetsSearchRequested', this.onWidgetsSearchRequest);
      this.on(document, 'uiContentUpdated', this.onWidgetsSearchRequest);
    });
  }

});