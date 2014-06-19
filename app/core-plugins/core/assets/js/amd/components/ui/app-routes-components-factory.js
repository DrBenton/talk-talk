define(function (require, exports, module) {
  'use strict';

  var defineComponent = require('flight').component;
  var withModulesLoading = require('app/core/mixins/data/with-modules-loading');
  var withComponentsAttachment = require('app/core/mixins/ui/with-components-attachment');
  var _ = require('lodash');
  var $ = require('jquery');
  var logger = require('logger');

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(appRoutesComponentsFactory, withModulesLoading, withComponentsAttachment);

  myDebug && logger.debug(module.id, 'Component on the bridge, captain!');


  function appRoutesComponentsFactory() {

    this.defaultAttrs({
    });

    this.onRouteTriggered = function (ev, data) {
      var targetComponentModuleId = data.targetComponentModuleId;

      this.loadModules([targetComponentModuleId])
        .then(_.bind(this.onRouteTargetComponentLoaded,this, data))
        .fail(_.bind(this.onRouteTargetComponentLoadingError, this, data));
    };

    this.onRouteTargetComponentLoaded = function (routeData, loadedComponents) {
      var targetComponentModule = loadedComponents[0];
      var $targetComponentNode = $(routeData.targetComponentNodeSelector);
      this.attachComponentTo(targetComponentModule, $targetComponentNode);
      this.trigger(document, 'triggeredRouteModuleAttached', routeData);
    };

    this.onRouteTargetComponentLoadingError = function (routeData, err) {
      logger.error('Route target Component "'+routeData.targetComponentModuleId+'" loading failed!');
      throw err;
    };

    // Component initialization
    this.after('initialize', function() {
      this.on(document, 'routeTrigger', this.onRouteTriggered);
    });
  }

});