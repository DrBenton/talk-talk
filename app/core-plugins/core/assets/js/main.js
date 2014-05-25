require([
  "logger",
  "locache",
  "flight",
  "app-modules/core/components/data/components-factory",
  "app-modules/core/csrf-handler"
], function (logger, locache, flight, componentsFactory, csrfHandler) {
  "use strict";

  logger.debug("App Main loaded!");

  if (true) {//to be replaced with a "debug" flag
    require(["vendor/js/flight/lib/debug"], function (flightDebug) {
      flightDebug.enable(true);
      DEBUG.events.logAll();
    })
  }

  // CSRF token global management
  csrfHandler.init();

  // Let"s start with an empty data cache for the moment...
  locache.flush();

  // Core Components init
  componentsFactory.attachTo(document);

  // Widgets creation request!
  $(document).trigger("widgetsSearchRequested");

});