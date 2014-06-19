require([
  'logger',
  'flight',
  'app/core/components/app-bootstrap-manager'
], function (logger, flight, appBootstrapManager) {
  'use strict';

  logger.debug('App Main loaded!');

  appBootstrapManager.attachTo(document);

});