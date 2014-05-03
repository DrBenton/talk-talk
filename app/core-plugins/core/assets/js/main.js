require([
  "logger",
  "locache",
  "app-modules/core/widgets-factory"
], function (logger, locache, widgetsFactory) {

  logger.debug("App Main loaded!");

  // Let"s start with an empty data cache for the moment...
  locache.flush();

  // Widgets creation
  widgetsFactory.findAndTriggerWidgets();

});