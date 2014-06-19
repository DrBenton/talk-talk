define(function (require, exports, module) {
  'use strict';

  var console = window.console || {};
  var methods = 'debug,log, warn, error'.split(',');
  var noop = function() {};
  for (var i = 0, j = methods.length; i < j; i++) {
    var method = methods[i];
    if (!console[method]) {
      console[method] = noop;
    }
  }

  return console;
});
