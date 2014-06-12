define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var withAutomaticTeardownOnContentUpdate = require("app-modules/utils/mixins/ui/with-automatic-teardown-on-content-update");
  var withAjax = require("app-modules/utils/mixins/data/with-ajax");
  var varsRegistry = require("app-modules/core/vars-registry");
  var appConfig = require("app/config");
  var _ = require("lodash");
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
      closeBtSelector: ".frame-tools .bt-close",
      minimizeBtSelector: ".frame-tools .bt-minimize",
      cancelMinimizeBtSelector: ".frame-tools .bt-cancel-minimize",
      fullscreenBtSelector: ".frame-tools .bt-fullscreen",
      cancelFullscreenBtSelector: ".frame-tools .bt-cancel-fullscreen"
    });

    this.statesClasses = "state-normal state-minimized state-fullscreen";

    this.onAjaxTopicWritingRequest = function (ev, data)
    {
      var parentForumId = data.forumId;
      myDebug && logger.debug(module.id, "Ajax Topic writing requested for Forum n°", parentForumId);
      this.$node.empty().show();
      this.loadNewTopicWidgetContent(parentForumId);
    };

    this.onCloseBtClick = function (ev, data)
    {
      this.$node.fadeOut("fast");
    };

    this.onMinimizeBtClick = function (ev, data)
    {
      this.$node.removeClass(this.statesClasses).addClass("state-minimized");
    };

    this.onCancelMinimizeBtClick = function (ev, data)
    {
      this.$node.removeClass(this.statesClasses).addClass("state-normal");
    };

    this.onFullscreenBtClick = function (ev, data)
    {
      this.$node.removeClass(this.statesClasses).addClass("state-fullscreen");
    };

    this.onCancelFullscreenBtClick = function (ev, data)
    {
      this.$node.removeClass(this.statesClasses).addClass("state-normal");
    };

    this.loadNewTopicWidgetContent = function (forumId) {
      this.$node.addClass("ajax-loading");
      this.clearEventsBindings();
      this.ajaxPromise({
        url: appConfig["base_url"] + "/ajax-post-writing/forum/"+forumId+"/new-topic/widget"
      })
        .then(
          _.bind(this.onWidgetContentRetrieved, this)
        );
    };

    this.onWidgetContentRetrieved = function (data) {
        this.$node.removeClass("ajax-loading");
        this.$node.html(data);
        this.trigger(document, "widgetsSearchRequested", {selector: "#" + this.$node.attr("id")});
        this.initEventsBindings();
    };

    this.initEventsBindings = function () {
        this.on(this.select("minimizeBtSelector"), "click", this.onMinimizeBtClick);
        this.on(this.select("cancelMinimizeBtSelector"), "click", this.onCancelMinimizeBtClick);
        this.on(this.select("fullscreenBtSelector"), "click", this.onFullscreenBtClick);
        this.on(this.select("cancelFullscreenBtSelector"), "click", this.onCancelFullscreenBtClick);
        this.on(this.select("closeBtSelector"), "click", this.onCloseBtClick);
    };

    this.clearEventsBindings = function () {
        this.off(this.select("minimizeBtSelector"), "click", this.onMinimizeBtClick);
        this.off(this.select("cancelMinimizeBtSelector"), "click", this.onCancelMinimizeBtClick);
        this.off(this.select("fullscreenBtSelector"), "click", this.onFullscreenBtClick);
        this.off(this.select("cancelFullscreenBtSelector"), "click", this.onCancelFullscreenBtClick);
        this.off(this.select("closeBtSelector"), "click", this.onCloseBtClick);
    };

    // Component initialization
    this.after("initialize", function() {
      this.$node.addClass("state-normal flight-component flight-component-attached");
      this.on(document, "uiNeedsAjaxTopicWriting", this.onAjaxTopicWritingRequest);
    });
  }

});