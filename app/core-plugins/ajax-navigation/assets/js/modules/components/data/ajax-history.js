define(function (require, exports, module) {
  "use strict";

  var defineComponent = require('flight').component;
  var withUrlNormalization = require("app-modules/core/mixins/data/with-url-normalization");
  var History = require("history");
  var _ = require("lodash");
  var logger = require("logger");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(ajaxHistory, withUrlNormalization);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function ajaxHistory() {

    this.data = {};

    this.initHistory = function() {
      History.Adapter.bind(window, 'statechange', _.bind(this.onHistoryStateChange, this));
    };

    this.onHistoryStateRequested = function (ev, data) {
      this.setHistoryUrl(data.url);
    };

    this.setHistoryUrl = function (url) {
      url = this.normalizeUrl(url);
      History.pushState(null, null, url);
    };

    this.triggerHistoryUrl = function (url) {
      url = this.normalizeUrl(url);
      this.trigger(document, 'historyState', {url: url});
      // Let's remove previous page Alerts on History change
      this.trigger(document, 'alertsClearingRequested');
    };

    this.onHistoryStateChange = function() {
      var state = History.getState();
      myDebug && logger.debug(module.id, "state=", state);
      this.triggerHistoryUrl(state.url);
    };

    // Component initialization
    this.after('initialize', function() {
      this.initHistory();
      this.on(document, 'historyStateRequested', this.onHistoryStateRequested);
    });
  }

});