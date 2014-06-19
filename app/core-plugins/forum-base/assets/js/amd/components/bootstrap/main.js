define(function (require, exports, module) {
  'use strict';

  var defineComponent = require('flight').component;
  var logger = require('logger');
  // My Plugin bootstrap components
  var routesInit = require('./routes-init');

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(corePluginForumBaseBootstrapEntryPoint);

  myDebug && logger.debug(module.id, 'Component on the bridge, captain!');


  function corePluginForumBaseBootstrapEntryPoint() {

    this.initPluginBootstrap = function () {

      var myPluginComponentsIdPrefix = module.id.replace(/\/bootstrap\/[^/]+$/, '');

      routesInit.attachTo(this.$node, {
        myPluginComponentsIdPrefix: myPluginComponentsIdPrefix
      });

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