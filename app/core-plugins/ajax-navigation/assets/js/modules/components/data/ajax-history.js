define(function (require, exports, module) {
  "use strict";

  var defineComponent = require('flight').component;
  var withUrlNormalization = require("app-modules/core/mixins/data/with-url-normalization");
  var withAlertsCapabilities = require("app-modules/core/mixins/data/with-alerts-capabilities");
  var History = require("history");
  var _ = require("lodash");
  var logger = require("logger");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(ajaxHistory, withUrlNormalization, withAlertsCapabilities);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function ajaxHistory() {

    this.data = {};

    this.initHistory = function() {
      History.Adapter.bind(window, 'statechange', _.bind(this.onHistoryStateChange, this));
    };

    this.onHistoryStateRequested = function (ev, data) {
      myDebug && logger.debug(module.id, "this.onHistoryStateRequested() : url=", data.url);
      this.setHistoryUrl(data.url);
    };

    this.setHistoryUrl = function (url) {
      url = this.normalizeUrl(url);
      History.pushState(null, null, url);
      this.trigger(document, 'historyState', {url: url});
    };

    this.onHistoryStateChange = function() {
      var state = History.getState();
      myDebug && logger.debug(module.id, "state=", state);
      this.setHistoryUrl(state.url);
    };

    // Component initialization
    this.after('initialize', function() {
      this.initHistory();
      this.on(document, 'historyStateRequested', this.onHistoryStateRequested);
    });
  }

});