define(function (require, exports, module) {
  'use strict';

  var defineComponent = require('flight').component;
  var _ = require('lodash');
  var logger = require('logger');

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(forumBaseRouting);

  myDebug && logger.debug(module.id, 'Component on the bridge, captain!');


  function forumBaseRouting() {

    this.defaultAttrs({
      myPluginComponentsIdPrefix: null
    });


    this.declareRoutes = function (ev, data) {

      // Let's give our Routes data to the App Router
      this.trigger(document, 'appRouteRegistrationRequest', {
        routePattern: '/',
        targetComponentModuleId: this.attr.myPluginComponentsIdPrefix + '/ui/forums-display',
        targetComponentNodeSelector: '#main-content',
        routeName: 'forum-base/forums'
      });

      // Job's done!
      this.teardown();
    };


    // Component initialization
    this.after('initialize', function() {
      this.on(document, 'appRoutesDeclarationRequest', this.declareRoutes);
    });
  }

});