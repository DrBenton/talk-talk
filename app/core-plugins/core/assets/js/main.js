require([
  'jquery',
  'logger',
  'flight',
  'app/data',
  'app/core/vars-registry',
  'app/core/csrf-handler',
  'app/core/components/data/components-factory',
  'app/core/components/ui/layout-init-handler'
], function ($, logger, flight, appData, varsRegistry, csrfHandler, componentsFactory, layoutInitHandler) {
  'use strict';

  logger.debug('App Main loaded!');

  var bootModules = appData['bootModules'];

  if (appData.debug) {
    require(['vendor/js/flight/lib/debug'], function (flightDebug) {
      flightDebug.enable(true);
      window.DEBUG && window.DEBUG.events.logAll();
    });
  }

  // CSRF token global management
  csrfHandler.init();

  // Core Components init
  layoutInitHandler.attachTo(varsRegistry.$body, {
    nbBootModules: bootModules.length
  });
  componentsFactory.attachTo(document);

  // Boot modules init
  require(bootModules, function() {
    // Our boot Modules are loaded, let's roll!

    // First widgets creation request
    $(document).trigger('widgetsSearchRequested');
  });


});