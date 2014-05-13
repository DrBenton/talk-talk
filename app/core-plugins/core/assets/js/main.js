require([
  "logger",
  "locache",
  "app-modules/core/widgets-factory",
  "app-modules/core/csrf-handler"
], function (logger, locache, widgetsFactory, csrfHandler) {

  logger.debug("App Main loaded!");

  // CSRF token global management
  csrfHandler.init();

  // Let"s start with an empty data cache for the moment...
  locache.flush();

  // Widgets creation
  widgetsFactory.findAndTriggerWidgets();

});