define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var withUrlNormalization = require("app-modules/utils/mixins/data/with-url-normalization");
  var withAlertsCapabilities = require("app-modules/utils/mixins/ui/with-alerts-capabilities");
  var History = require("history");
  var _ = require("lodash");
  var logger = require("logger");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(ajaxHistory, withUrlNormalization, withAlertsCapabilities);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function ajaxHistory() {

    this.defaultAttrs({
      contentContainerToListenSelector: null
    });

    this.initHistory = function() {
      History.Adapter.bind(window, "statechange", _.bind(this.onHistoryStateChange, this));
    };

    this.onAjaxContentLoaded = function (ev, data) {
      myDebug && logger.debug(module.id, "this.onAjaxContentLoaded() : url=", data.url);
      if (data.target === this.attr.contentContainerToListenSelector) {
        // Hey, it seems that the container we are listening to has been
        // updated via Ajax! Let's update the History accordingly...
        this.setHistoryUrl(data.url, "ajaxContentUpdate");
      }
    };

    this.setHistoryUrl = function (url, source) {

      url = this.normalizeUrl(url);

      // Browser history update
      History.pushState(null, null, url);

      // Components event broadcasting
      this.trigger(document, "historyState", {
        url: url,
        source: source
      });

      // Request a Ajax content loading if the event source is a user action with the browser
      if ("browserHistory" === source) {
        $(document).trigger("uiNeedsContentAjaxLoading", {
          url: url,
          target: this.attr.contentContainerToListenSelector
        });
      }

    };

    this.onHistoryStateChange = function() {
      var state = History.getState();
      myDebug && logger.debug(module.id, "state=", state);
      this.setHistoryUrl(state.url, "browserHistory");
    };

    // Component initialization
    this.after("initialize", function() {
      this.initHistory();
      this.on(document, "ajaxContentLoadingDone", this.onAjaxContentLoaded);
    });
  }

});