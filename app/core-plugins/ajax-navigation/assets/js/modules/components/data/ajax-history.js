define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var withUrlNormalization = require("app-modules/utils/mixins/data/with-url-normalization");
  var withAlertsCapabilities = require("app-modules/utils/mixins/ui/with-alerts-capabilities");
  var History = require("history");
  var _ = require("lodash");
  var $ = require("jquery");
  var logger = require("logger");

  var myDebug = false;

  // Exports: component definition
  module.exports = defineComponent(ajaxHistory, withUrlNormalization, withAlertsCapabilities);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function ajaxHistory() {

    this.defaultAttrs({
      contentContainerToListenSelector: null
    });

    this.initHistory = function() {
      this.loadAjaxContentOnHistoryChange = true;
      History.Adapter.bind(window, "statechange", _.bind(this.onHistoryStateChange, this));
    };

    this.onContentUpdated = function (ev, data) {
      myDebug && logger.debug(module.id, "this.onContentUpdated() : fromUrl=", data.fromUrl);
      if (data.target === this.attr.contentContainerToListenSelector) {
        // Hey, it seems that the container we are listening to has been
        // updated via Ajax! Let's update the History accordingly...
        this.setHistoryUrl(data.fromUrl, "ajaxContentUpdate");
      }
    };

    this.setHistoryUrl = function (url, source) {

      url = this.normalizeUrl(url);

      // Browser history manual update
      // We don't want to trigger any ajax content loading through this history update.
      this.loadAjaxContentOnHistoryChange = false;
      History.pushState({source: source}, null, url);
      // Ok, back to normal behaviour
      this.loadAjaxContentOnHistoryChange = true;

      // Components event broadcasting
      this.trigger(document, "historyState", {
        url: url,
        source: source
      });
    };

    this.onHistoryStateChange = function() {
      var state = History.getState();
      myDebug && logger.debug(module.id, "state=", state);

      // Request a Ajax content loading, unless explicitly requested not to do so
      if (this.loadAjaxContentOnHistoryChange) {
        $(document).trigger("uiNeedsContentAjaxLoading", {
          url: this.normalizeUrl(state.url),
          target: this.attr.contentContainerToListenSelector
        });
      }
    };

    // Component initialization
    this.after("initialize", function() {
      this.initHistory();
      this.on(document, "uiContentUpdated", this.onContentUpdated);
    });
  }

});