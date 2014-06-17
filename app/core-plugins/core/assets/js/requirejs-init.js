(function () {
  'use strict';

  var appData = JSON.parse(
    document.getElementById('app-data').getAttribute('data-config')
  );

  var debug = appData['debug'];
  var minExt = (debug) ? '' : '.min' ;
  var vendorsRootUrl = appData['vendorsRootUrl'];
  var vendorsModulesRootUrl = vendorsRootUrl.replace(/^\//, '');

  // let's expose our app config data as a Module
  define('app/data', [], appData);

  // Hard-coded RequireJS config
  require.config({
    baseUrl: appData['rootUrl'] + '/',
    paths: {
      // App core Plugins assets paths aliases
      // (Other Plugins Modules will probably use some Modules from these Plugins)
      'app/core': 'app/core-plugins/core/assets/js/amd',
      'app/utils': 'app/core-plugins/utils/assets/js/amd',
      // Third-party libraries aliases
      'jquery': vendorsRootUrl + '/jquery/dist/jquery' + minExt,
      'lodash': vendorsRootUrl + '/lodash/dist/lodash' + minExt,
      'flight': 'app/core-plugins/core/assets/js/amd/flight-wrapper',
      'logger': vendorsRootUrl + '/console-polyfill/index',
      'history': vendorsRootUrl + '/history.js/scripts/bundled/html4+html5/native.history',
      'locache': vendorsRootUrl + '/locache/build/locache' + minExt,
      'purl': vendorsRootUrl + '/purl/purl',
      'moment': vendorsRootUrl + '/momentjs/min/moment' + minExt,
      'jquery-form': vendorsRootUrl + '/jquery-form/jquery.form',
      'q': vendorsRootUrl + '/q/q'
    },
    shim: {
      'logger': {
        exports: 'console'
      },
      'history': {
        exports: 'History'
      },
      'locache': {
        exports: 'locache'
      }
    },
    map: {
      '*': {
        'css': vendorsModulesRootUrl + '/require-css/css'
      }
    },
    waitSeconds: 2,
    //urlArgs: 'dev-bust=' + (new Date()).getTime() //TODO: remove this later :-)
    lastItem: true
  });

  // Given-by-PHP RequireJS config
  require.config(appData['requireJsConfig']);

})();