define(function (require, exports, module) {
  'use strict';

  var layoutComponent = require('./components/ui/layout');
  var varsRegistry = require('app/core/vars-registry');
  var logger = require('logger');

  var myDebug = true;

  // Exports

  myDebug && logger.debug(module.id, 'on the bridge, captain!');

//  varsRegistry.$document.trigger('');

  layoutComponent.attachTo(varsRegistry.$document, {
    themeConfig: module.config()
  });

});

