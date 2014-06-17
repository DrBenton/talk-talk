define(function (require, exports, module) {
  'use strict';

  var defineComponent = require('flight').component;
  var appData = require('app/data');
  var _ = require('lodash');
  var logger = require('logger');

  var myDebug = false;

  // Exports: component definition
  module.exports = defineComponent(twBootstrapLayout);

  myDebug && logger.debug(module.id, 'Component on the bridge, captain!');


  function twBootstrapLayout() {

    this.defaultAttrs({
      siteContainerSelector: '#site-container',
      headerSelector: 'header',
      themeConfig: null
    });

    this.applyTWBootstrapLookNFeel = function () {
      var twBootstrapRawCssUrl = this.attr.themeConfig.twBootstrapDistBaseUrl + '/css/bootstrap.min.css';
      var myTwBootstrapThemeCssUrl = this.attr.themeConfig.myAssetsBaseUrl + '/css/theme-twbootstrap.css';
      require(['css!' + twBootstrapRawCssUrl, 'css!'+myTwBootstrapThemeCssUrl], _.bind(function () {
        this.select('siteContainerSelector').addClass('container');
      }, this));
    };

    // Component initialization
    this.after('initialize', function() {
      this.applyTWBootstrapLookNFeel();
    });
  }

});