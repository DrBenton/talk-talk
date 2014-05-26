define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var _ = require("lodash");
  var logger = require("logger");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(ajaxLinksHandler);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function ajaxLinksHandler() {

    this.defaultAttrs({
      targetContentContainerSelector: null
    });

    this.onAjaxLinkClick = function (ev, data) {
      ev.preventDefault();
      var $clickedLink = $(ev.currentTarget);
      this.trigger(document, "uiNeedsContentAjaxLoading", {
        url: $clickedLink.attr("href"),
        target: this.attr.targetContentContainerSelector
      });

      return false;
    };

    // Component initialization
    this.after("initialize", function() {
      // Let's use jQuery events proxy mechanism!
      // This way, we won't have to unbind/bind events for each Ajax page content update
      this.$node.on("click", 'a.ajax-link', _.bind(this.onAjaxLinkClick, this));
    });
  }

});