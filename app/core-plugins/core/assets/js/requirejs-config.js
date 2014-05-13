(function () {

  require.config({
    baseUrl: "/",
    paths: {
      // App core Plugins assets paths aliases
      "app-modules/core": "app/core-plugins/core/assets/js/modules",
      "app-modules/ajax-nav": "app/core-plugins/ajax-navigation/assets/js/modules",
      "app-modules/phpbb": "app/core-plugins/phpbb/assets/js/modules",
      "app-modules/utils": "app/core-plugins/utils/assets/js/modules",
      // Third-party libraries aliases
      "jquery": "vendor/js/jquery/dist/jquery.min",
      "lodash": "vendor/js/lodash/dist/lodash.min",
      "logger": "vendor/js/console-polyfill/index",
      "history": "vendor/js/history.js/scripts/bundled/html4+html5/native.history",
      "locache": "vendor/js/locache/build/locache.min",
      "purl": "vendor/js/purl/purl",
      "moment": "vendor/js/momentjs/min/moment.min",
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
    waitSeconds: 1,
    //urlArgs: "dev-bust=" + (new Date()).getTime() //TODO: remove this later :-)
    lastItem: true
  });


})();