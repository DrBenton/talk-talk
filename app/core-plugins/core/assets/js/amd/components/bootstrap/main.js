define(function (require, exports, module) {
  'use strict';

  var defineComponent = require('flight').component;
  var _ = require('lodash');
  var logger = require('logger');
  // My Plugin bootstrap components
  var layoutInit = require('./layout-init');
  var csrfHandler = require('app/core/csrf-handler');
  var appRouter = require('../data/app-router');
  var appRoutesComponentsFactory = require('../ui/app-routes-components-factory');
  var componentsFactory = require('../ui/components-factory');
  var historyManager = require('../data/history-manager');

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(corePluginCoreBootstrapEntryPoint);

  myDebug && logger.debug(module.id, 'Component on the bridge, captain!');


  function corePluginCoreBootstrapEntryPoint() {

    this.initPluginBootstrap = function () {

      // CSRF token global management
      csrfHandler.init();

      // App router init
      appRouter.attachTo(this.$node);

      // App routes Components factory init
      appRoutesComponentsFactory.attachTo(this.$node);

      // "adress bar" history management init
      historyManager.attachTo(this.$node, {
        contentContainerToListenSelector: '#site-container'
      });

      // Layout init
      layoutInit.attachTo(this.$node);

      // Components factory init
      componentsFactory.attachTo(this.$node);

      // Since we have no async process here, we can trigger the "appBootstrapComponentReady" right now
      this.trigger(this.$node, 'appBootstrapComponentReady', {
        moduleId: module.id
      });

      // Duty accomplished!
      this.teardown();
    };


    // Component initialization
    this.after('initialize', function() {
      this.initPluginBootstrap();
    });
  }

});