(function () {

  var debug = true;//TODO: handle it cleanly somewhere else
  var minExt = (debug) ? "" : ".min" ;

  require.config({
    baseUrl: "/",
    paths: {
      // App core Plugins assets paths aliases
      "app-modules/core": "app/core-plugins/core/assets/js/modules",
      "app-modules/ajax-nav": "app/core-plugins/ajax-navigation/assets/js/modules",
      "app-modules/utils": "app/core-plugins/utils/assets/js/modules",
      "app-modules/forum-base": "app/core-plugins/forum-base/assets/js/modules",
      "app-modules/phpbb": "app/core-plugins/phpbb/assets/js/modules",
      // Third-party libraries aliases
      "jquery": "vendor/js/jquery/dist/jquery" + minExt,
      "lodash": "vendor/js/lodash/dist/lodash" + minExt,
      "flight": "app/core-plugins/core/assets/js/modules/flight-wrapper",
      "logger": "vendor/js/console-polyfill/index",
      "history": "vendor/js/history.js/scripts/bundled/html4+html5/native.history",
      "locache": "vendor/js/locache/build/locache" + minExt,
      "purl": "vendor/js/purl/purl",
      "moment": "vendor/js/momentjs/min/moment" + minExt,
      "jquery-form": "vendor/js/jquery-form/jquery.form",
      "q": "vendor/js/q/q"
    },
    shim: {
      "logger": {
        exports: "console"
      },
      "history": {
        exports: "History"
      },
      "locache": {
        exports: "locache"
      }
    },
    map: {
      '*': {
        "css": 'vendor/js/require-css/css'
      }
    },
    waitSeconds: 1,
    //urlArgs: "dev-bust=" + (new Date()).getTime() //TODO: remove this later :-)
    lastItem: true
  });


})();