require([
  'jquery',
  'logger',
  'flight',
  'app/data',
  'app/core/components/data/components-factory',
  'app/core/csrf-handler'
], function ($, logger, flight, appData, componentsFactory, csrfHandler) {
  'use strict';

  logger.debug('App Main loaded!');

  if (appData.debug) {
    require(['vendor/js/flight/lib/debug'], function (flightDebug) {
      flightDebug.enable(true);
      window.DEBUG && window.DEBUG.events.logAll();
    });
  }

  // CSRF token global management
  csrfHandler.init();

  // Boot modules init
  var bootModules = appData['bootModules'];
  require(bootModules, function() {
    // Our boot Modules are loaded, let's proced to Flight Components initialization!

    // Core Components init
    componentsFactory.attachTo(document);

    // Widgets creation request!
    $(document).trigger('widgetsSearchRequested');
  });


});