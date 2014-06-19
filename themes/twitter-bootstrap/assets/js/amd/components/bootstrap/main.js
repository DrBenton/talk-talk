define(function (require, exports, module) {
  'use strict';

  var defineComponent = require('flight').component;
  var _ = require('lodash');
  var logger = require('logger');
  // My Plugin bootstrap components
  var layoutInit = require('./layout-init');
  var templatesInit = require('./templates-init');

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(themeTWBBootstrapEntryPoint);

  myDebug && logger.debug(module.id, 'Component on the bridge, captain!');


  function themeTWBBootstrapEntryPoint() {

    this.initPluginBootstrap = function () {

      layoutInit.attachTo(this.$node);

      templatesInit.attachTo(this.$node);

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