require([
  "jquery",
  "logger",
  "flight",
  "app-modules/core/components/data/components-factory",
  "app-modules/core/csrf-handler"
], function ($, logger, flight, componentsFactory, csrfHandler) {
  "use strict";

  logger.debug("App Main loaded!");

  if (true) {//to be replaced with a "debug" flag
    require(["vendor/js/flight/lib/debug"], function (flightDebug) {
      flightDebug.enable(true);
      window.DEBUG && window.DEBUG.events.logAll();
    });
  }

  // CSRF token global management
  csrfHandler.init();

  // Core Components init
  componentsFactory.attachTo(document);

  // Widgets creation request!
  $(document).trigger("widgetsSearchRequested");

});