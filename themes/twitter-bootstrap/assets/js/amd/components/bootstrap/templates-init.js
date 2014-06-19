define(function (require, exports, module) {
  'use strict';

  var defineComponent = require('flight').component;
  var withTemplatesManagement = require('app/core/mixins/ui/with-templates-management');
  var $ = require('jquery');
  var logger = require('logger');

  var myDebug = true;

  // Exports: component definition
  module.exports = defineComponent(themeTWBBootstrapTemplatesRegistration, withTemplatesManagement);

  myDebug && logger.debug(module.id, 'on the bridge, captain!');

  var templatesUrlsPrefix = 'themes/twitter-bootstrap/assets/js/templates';
  var templatesToRegister = {
    'inc/forums-display': templatesUrlsPrefix + '/inc/forum-display'
  };

  function themeTWBBootstrapTemplatesRegistration() {

    this.loadThemeTemplates = function () {

      var templatesLoadingPromise = this.loadTemplates(templatesToRegister);

      // Since we have an async bootstrap process, we have to ask to the App Bootstrap Manager
      // to wait for us...
      $(document).trigger('appBootstrapComponentAddition', {
        moduleId: module.id
      });

      templatesLoadingPromise.then(_.bind(function () {
        $(document).trigger('appBootstrapComponentReady', {
          moduleId: module.id
        });
        // Job's done!
        this.teardown();
      }, this));

    };

    this.after('initialize', function () {
      this.loadThemeTemplates();
    });
  }

});

