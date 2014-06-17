define(function (require, exports, module) {
  'use strict';

  var defineComponent = require('flight').component;
  var varsRegistry = require('app/core/vars-registry');
  var logger = require('logger');

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(layoutInitHandler);

  myDebug && logger.debug(module.id, 'Component on the bridge, captain!');


  function layoutInitHandler() {

    this.nbBootModulesInitialized = 0;

    this.defaultAttrs({
      nbBootModules: null
    });

    this.onBootModuleInitialization = function (ev, data)
    {
      this.nbBootModulesInitialized++;

      if (this.attr.nbBootModules === this.nbBootModulesInitialized) {
        this.$node.removeClass('waiting-initialization');
        myDebug && logger.debug(module.id, 'App initialized in '+((new Date()).getTime() - varsRegistry.appStartTime)+'ms.');

        // Work complete!
        this.teardown();
      }
    };

    // Component initialization
    this.after('initialize', function() {
      this.on(document, 'bootModuleInitialized', this.onBootModuleInitialization);
    });
  }

});