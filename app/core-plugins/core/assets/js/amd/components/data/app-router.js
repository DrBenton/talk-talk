define(function (require, exports, module) {
  'use strict';

  var defineComponent = require('flight').component;
  var withUrlUtils = require('app/utils/mixins/data/with-url-utils');
  var _ = require('lodash');
  var logger = require('logger');

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(appRouter, withUrlUtils);

  myDebug && logger.debug(module.id, 'Component on the bridge, captain!');


  function appRouter() {

    this.routesRegistrations = {};

    this.defaultAttrs({
    });


    this.onAppBootstrapDone = function (ev, data) {
      // We ask our Modules to declare their app routes!
      this.trigger(document, 'appRoutesDeclarationRequest');

      myDebug && logger.debug(module.id, 'App routes registered:', this.routesRegistrations);

      // Okay, let's start the routing system!
      this.trigger(document, 'appRouteRequest', {
        routeUrl: window.location.pathname
      });
    };

    this.onAppRouteRegistrationRequest = function (ev, data) {
      var routePattern = data.routePattern;

      if (!this.routesRegistrations[routePattern]) {
        this.routesRegistrations[routePattern] = [];
      }

      // Routes are sorted by priority
      data.priority = data.priority || 0;
      var targetSortedIndex = _.sortedIndex(this.routesRegistrations[routePattern], data, 'priority');
      this.routesRegistrations[routePattern].splice(targetSortedIndex, 0, data);
    };

    this.onAppRouteRequest = function (ev, data) {
      var routeUrl = data.routeUrl;

      // Let's look for a route pattern matching this route URL...
      var routeRegistrations = null;
      var routeRegExpMatches;
      _.forEach(this.routesRegistrations, function (registrations, routePattern) {
        if (
            (_.isRegExp(routePattern) && (routeRegExpMatches = routeUrl.match(routePattern))) ||
            routeUrl === routePattern
          ) {
          routeRegistrations = registrations;
          return false;
        }
      });

      if (null === routeRegistrations) {

        // No route match
        this.trigger(document, 'routeUnmatched', data);

      } else {

        // Successful route matching!
        var targetHandler = routeRegistrations[0];
        var routeToTriggerData = _.extend({routeUrl: routeUrl}, targetHandler);
        this.trigger(document, 'routeTrigger', routeToTriggerData);

        if (myDebug && routeRegistrations.length > 1) {
          logger.debug(module.id, (routeRegistrations.length - 1) + ' route(s) not triggered because of routes priorities system.');
        }
      }
    };

    // Component initialization
    this.after('initialize', function() {
      this.on(document, 'appBootstrapDone', this.onAppBootstrapDone);
      this.on(document, 'appRouteRegistrationRequest', this.onAppRouteRegistrationRequest);
      this.on(document, 'appRouteRequest', this.onAppRouteRequest);
    });
  }

});