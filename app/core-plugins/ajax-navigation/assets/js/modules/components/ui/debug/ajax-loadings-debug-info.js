define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var _ = require("lodash");
  var logger = require("logger");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(ajaxLoadingsDebugInfo);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function ajaxLoadingsDebugInfo() {

    this.onAjaxContentLoadingDone = function(ev, data) {
      this.nbActionsLoadedFromAjax++;
      var duration = data.duration * 1000;
      this.loadingsDurations.push(duration);

      this.$node.find(".current-action-loading-duration").text(duration);
      this.$node.find(".nb-actions-loaded").text(this.nbActionsLoadedFromAjax);
      this.$node.find(".current-action-has-been-loaded-from-cache").addClass("hidden");
      this.updateAverageActionsLoadingDuration();
    };

    this.onAjaxContentDisplayFromCache = function(ev, data) {
      this.nbActionsLoadedFromCache++;

      this.$node.find(".nb-actions-loaded-from-cache").text(this.nbActionsLoadedFromCache);
      this.$node.find(".current-action-has-been-loaded-from-cache").removeClass("hidden");
      this.updateAverageActionsLoadingDuration();
    };

    this.onUiContentUpdated = function(ev, data) {
      this.$node.find(".current-action-url").text(data.fromUrl);
      this.$node.removeClass("hidden");
    };

    this.updateAverageActionsLoadingDuration = function() {
      this.$node.find(".average-actions-loading-duration")
        .text(this.getAverageActionsLoadingDuration());
      this.$node.find(".average-actions-loading-duration-with-cache")
        .text(this.getAverageActionsLoadingDuration(true));
    };

    this.getAverageActionsLoadingDuration = function(withLoadingsFromCache) {
      var sum = _.reduce(this.loadingsDurations, function(sum, num) {
        return sum + num;
      });
      var nbActions = this.nbActionsLoadedFromAjax;
      nbActions += (withLoadingsFromCache) ? this.nbActionsLoadedFromCache : 0 ;
      return sum / nbActions;
    };

    // Component initialization
    this.after("initialize", function() {
      this.nbActionsLoadedFromAjax = 0;
      this.nbActionsLoadedFromCache = 0;
      this.loadingsDurations = [];

      this.on(document, "ajaxContentLoadingDone", this.onAjaxContentLoadingDone);
      this.on(document, "uiContentUpdated", this.onUiContentUpdated);
      this.on(document, "ajaxContentDisplayFromCache", this.onAjaxContentDisplayFromCache);
    });
  }

});