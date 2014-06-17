define(function (require, exports, module) {
  'use strict';

  var defineComponent = require('flight').component;
  var withCssLoadingCapabilities = require('app/utils/mixins/ui/with-css-loading-capabilities');
  var appData = require('app/data');
  var _ = require('lodash');
  var logger = require('logger');

  var myDebug = false;

  // Exports: component definition
  module.exports = defineComponent(twBootstrapLayout, withCssLoadingCapabilities);

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

      this.loadStylesheets([twBootstrapRawCssUrl, myTwBootstrapThemeCssUrl])
        .then(_.bind(function () {
          this.select('siteContainerSelector').addClass('container');
          this.trigger('bootModuleInitialized', {id: module.id});
        }, this));
    };

    // Component initialization
    this.after('initialize', function() {
      this.applyTWBootstrapLookNFeel();
    });
  }

});