define(function (require, exports, module) {
  'use strict';

  var defineComponent = require('flight').component;
  var _ = require('lodash');
  var logger = require('logger');

  var myDebug = false;

  // Exports: component definition
  module.exports = defineComponent(themeTWBBootstrapLayoutInit);

  myDebug && logger.debug(module.id, 'Component on the bridge, captain!');


  function themeTWBBootstrapLayoutInit() {

    this.defaultAttrs({
      siteContainerSelector: '#site-container',
      headerSelector: 'header'
    });

    this.applyTWBootstrapLookNFeel = function () {

      this.select('siteContainerSelector').addClass('container');

    };

    // Component initialization
    this.after('initialize', function() {
      this.applyTWBootstrapLookNFeel();
    });
  }

});