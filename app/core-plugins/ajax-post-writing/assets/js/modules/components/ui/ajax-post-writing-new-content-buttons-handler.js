define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var withAutomaticTeardownOnContentUpdate = require("app-modules/utils/mixins/ui/with-automatic-teardown-on-content-update");
  var ajaxPostWritingWidget = require("./ajax-post-writing-widget");
  var varsRegistry = require("app-modules/core/vars-registry");
  var logger = require("logger");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(ajaxPostWriting, withAutomaticTeardownOnContentUpdate);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");

  ajaxPostWriting.ajaxWritingWidgetInitialized = false;

  function ajaxPostWriting() {

    this.defaultAttrs({
    });

    this.onNewPostOrTopicWritingButtonClick = function (ev, data) {
      ev.preventDefault();

      ajaxPostWriting.ajaxWritingWidgetInitialized || this.createAjaxWritingWidget();

      this.$node.css("opacity", 0.5);
      this.trigger(document, "uiNeedsAjaxTopicWriting", {
        forumId: parseInt(this.$node.data("forum-id"), 10)
      });

      return false;
    };

    this.createAjaxWritingWidget = function() {
      myDebug && logger.debug(module.id, "Ajax writing widget creation.");
      varsRegistry.$siteContainer.after("<div id='ajax-writing-widget'></div>");
      ajaxPostWritingWidget.attachTo(document.getElementById("ajax-writing-widget"));
      ajaxPostWriting.ajaxWritingWidgetInitialized = true;
    };


    // Component initialization
    this.after("initialize", function() {
      this.on("click", this.onNewPostOrTopicWritingButtonClick);
    });
  }

});