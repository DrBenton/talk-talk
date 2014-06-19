define(function (require, exports, module) {
  'use strict';

  var defineComponent = require('flight').component;
  var withModulesLoading = require('app/core/mixins/data/with-modules-loading');
  var withComponentsAttachment = require('app/core/mixins/ui/with-components-attachment');
  var $ = require('jquery');
  var _ = require('lodash');
  var logger = require('logger');

  var myDebug = false;

  // Exports: component definition
  module.exports = defineComponent(componentsFactory, withModulesLoading, withComponentsAttachment);

  myDebug && logger.debug(module.id, 'Component on the bridge, captain!');


  function componentsFactory() {

    this.onComponentsSearchRequest = function (ev, data) {
      var $targetComponentsContainer = (data && data.selector)
        ? $(data.selector)
        : this.$node;
      this.searchAndAttachComponents($targetComponentsContainer);
    };

    this.loadComponents = function ($jqElement) {
      var componentsName = $jqElement.data('component');

      myDebug && logger.debug(module.id, 'loadComponents(' + componentsName + ')');

      var componentsToAttach = componentsName.split(',');
      componentsToAttach = _.map(componentsToAttach, function(componentName) {
        return componentName.replace(/^\//, '', componentName);
      });

      this.loadModules(componentsToAttach)
        .then(_.bind(function (loadedComponents) {
          _.forEach(loadedComponents, _.bind(function (component, i) {
            this.attachComponentTo(component, $jqElement);
          }, this));
        }, this));
    };

    this.searchAndAttachComponents = function ($componentsContainer) {
      var $dataModules = $componentsContainer
        .find('.flight-component')
        .not('.flight-component-attached');
      var nbDataModules = $dataModules.length;

      myDebug && logger.debug(module.id, nbDataModules + ' pending Flight components found in container ', $componentsContainer);

      for (var i = 0; i < nbDataModules; i++) {
        this.loadComponents($dataModules.eq(i));
      }
    };

    // Component initialization
    this.after('initialize', function() {
      this.on(document, 'appBootstrapDone', this.onComponentsSearchRequest);
      this.on(document, 'componentsSearchRequested', this.onComponentsSearchRequest);
      this.on(document, 'uiContentUpdated', this.onComponentsSearchRequest);
    });
  }

});