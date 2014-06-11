define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var withAutomaticTeardownOnContentUpdate = require("app-modules/utils/mixins/ui/with-automatic-teardown-on-content-update");
  var withAjax = require("app-modules/utils/mixins/data/with-ajax");
  var varsRegistry = require("app-modules/core/vars-registry");
  var appConfig = require("app/config");
  var $ = require("jquery");
  var logger = require("logger");

  // CSS dependencies
  require("css!../../../../css/ajax-writing-frame.css");


  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(ajaxPostWriting, withAutomaticTeardownOnContentUpdate, withAjax);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function ajaxPostWriting() {

    this.defaultAttrs({
    });

    this.createAjaxWritingWidget = function() {
      myDebug && logger.debug(module.id, "Ajax writing widget creation.");
      varsRegistry.$siteContainer.after("<div id='ajax-writing-widget'></div>");
      this.$ajaxWritingWidget = $("#ajax-writing-widget");
    };

    this.onNewPostOrTopicWriting = function (ev, data) {
      ev.preventDefault();

      this.$ajaxWritingWidget || this.createAjaxWritingWidget();

      this.$node.css("opacity", 0.5);
      this.loadNewTopicWidgetContent(this.$node.data("forum-id"));

      return false;
    };

    this.loadNewTopicWidgetContent = function (forumId) {
      this.$ajaxWritingWidget.addClass("ajax-loading");
      this.ajaxPromise({
        url: appConfig["base_url"] + "/ajax-post-writing/forum/"+forumId+"/new-topic/widget"
      })
        .then(
          _.bind(function (data) {
            this.$ajaxWritingWidget.removeClass("ajax-loading");
            this.$ajaxWritingWidget.html(data);
            this.trigger(document, "widgetsSearchRequested", {selector: "#ajax-writing-widget"});
          }, this)
        );
    };


    // Component initialization
    this.after("initialize", function() {
      this.on("click", this.onNewPostOrTopicWriting);
    });
  }

});