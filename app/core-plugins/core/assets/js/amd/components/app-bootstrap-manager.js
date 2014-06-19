define(function (require, exports, module) {
  'use strict';

  var defineComponent = require('flight').component;
  var withAppData = require('app/core/mixins/data/with-app-data');
  var withModulesLoading = require('app/core/mixins/data/with-modules-loading');
  var withComponentsAttachment = require('app/core/mixins/ui/with-components-attachment');
  var varsRegistry = require('app/core/vars-registry');
  var $ = require('jquery');
  var _ = require('lodash');
  var logger = require('logger');

  var myDebug = !false;

// Exports: component definition
  module.exports = defineComponent(appBootstrapManagement,
    withAppData, withModulesLoading, withComponentsAttachment);

  myDebug && logger.debug(module.id, 'Component on the bridge, captain!');

  function appBootstrapManagement() {

    this.bootstrapModulesIds = [];
    this.bootstrappedModulesIds = [];

    this.initAppBootstrapProcess = function () {

      this.bootstrapModulesIds = this.getAppData()['bootstrapModulesIds'];

      this.loadModules(this.bootstrapModulesIds)
        .then(_.bind(this.onAppBootstrapModulesLoadingDone, this))
        .fail(_.bind(this.onAppBootstrapModulesLoadingError, this));
    };

    this.onAppBootstrapComponentReady = function (ev, data) {

      var bootstrappedComponentModuleId = data.moduleId;
      if (!_.contains(this.bootstrappedModulesIds, bootstrappedComponentModuleId)) {
        this.bootstrappedModulesIds.push(bootstrappedComponentModuleId);
        myDebug && logger.debug(module.id, 'App bootstrap Component "'+bootstrappedComponentModuleId+'" done! '+(this.bootstrapModulesIds.length - this.bootstrappedModulesIds.length)+' remaining... ('+_.difference(this.bootstrapModulesIds, this.bootstrappedModulesIds).join(', ')+')');
      }

      if (this.bootstrapModulesIds.length === this.bootstrappedModulesIds.length) {
        this.onAllAppBootstrapComponentsReady();
      }
    };

    this.onAppBootstrapComponentAddition = function (ev, data) {
      // A new incoming bootstrap Component has arrived!
      // It has probably been loade by one of our first bootstrap Components, and
      // claims a async init process that we'll have to wait for...
      var newBootstrapComponentModuleId = data.moduleId;
      this.bootstrapModulesIds.push(newBootstrapComponentModuleId);
    };

    this.onAppBootstrapModulesLoadingDone = function (loadedBootstrapComponents) {
      myDebug && logger.debug(loadedBootstrapComponents.length + ' App bootstrap Components loaded.');

      this.on(document, 'appBootstrapComponentReady', this.onAppBootstrapComponentReady);
      this.on(document, 'appBootstrapComponentAddition', this.onAppBootstrapComponentAddition);

      var $bootstrapComponentsTargetNode = $(document);
      _.forEach(loadedBootstrapComponents, _.bind(function (bootstrapComponent) {
        this.attachComponentTo(bootstrapComponent, $bootstrapComponentsTargetNode);
      }, this));
    };

    this.onAppBootstrapModulesLoadingError = function (err) {
      debugger;
      logger.warn('App bootstrap Components loading failed!');
      this.trigger(document, 'appBootstrapError');
      throw err;
    };

    this.onAllAppBootstrapComponentsReady = function () {
      myDebug && logger.debug(module.id, 'App bootstrap done in '+((new Date()).getTime() - varsRegistry.appStartTime)+'ms.');
      // Let's wait 1 frame before sending the start signal to everyone...
      _.defer(_.bind(function () {
        this.trigger(document, 'appBootstrapDone');
        // Job's done!
        this.teardown();
      }, this));
    };

    this.after('initialize', function () {

      if (this.getAppData()['debug']) {
        require(['vendor/js/flight/lib/debug'], function (flightDebug) {
          flightDebug.enable(true);
          window.DEBUG && window.DEBUG.events.logAll();
        });
      }

      this.initAppBootstrapProcess();
    });
  }

});