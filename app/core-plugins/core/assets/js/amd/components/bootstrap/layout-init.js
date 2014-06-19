define(function (require, exports, module) {
  'use strict';

  var defineComponent = require('flight').component;
  var logger = require('logger');

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(layoutInitHandler);

  myDebug && logger.debug(module.id, 'Component on the bridge, captain!');


  function layoutInitHandler() {

    this.defaultAttrs({
      appInitializationRootSelector: 'body'
    });

    this.onAppBootstrapDone = function (ev, data)
    {
      this.select('appInitializationRootSelector').removeClass('waiting-initialization');
      // Work complete!
      this.teardown();
    };

    // Component initialization
    this.after('initialize', function() {
      this.on(document, 'appBootstrapDone', this.onAppBootstrapDone);
    });
  }

});