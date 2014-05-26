define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var withAlertsCapabilities = require("app-modules/utils/mixins/ui/with-alerts-capabilities");
  var withDateUtils = require("app-modules/utils/mixins/data/with-date-utils");
  var _ = require("lodash");
  var logger = require("logger");

  require("jquery-form");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(ajaxFormsHandler, withAlertsCapabilities, withDateUtils);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function ajaxFormsHandler() {

    this.defaultAttrs({
      formsSelector: "form.ajax-form",
      targetContentContainerSelector: null
    });

    this.onContentUpdate = function (ev, data) {
      var $target = $(data.target);
      if (this.$currentAjaxForms && this.$currentAjaxForms.length > 0) {
        this.unbindPreviousAjaxForms($target);
      }

      this.searchAndHandleAjaxForms($target);
    };

    this.onFormSubmit = function (ev, data) {

      myDebug && logger.debug(module.id, "onFormSubmit()");

      ev.preventDefault();

      var $form = $(ev.currentTarget);
      var formUrl = $form.attr("action");

      this.trigger("ajaxContentLoadingStart", {
        url: formUrl,
        target: this.attr.targetContentContainerSelector
      });

      $form.ajaxSubmit({
        headers: {"X-Requested-With": "XMLHttpRequest"},
        success: _.bind(this.onFormSendingSuccess, this, formUrl, new Date),
        error: _.bind(this.onFormSendingError, this, formUrl)
      });

      // Let's remove previous page Alerts
      this.clearAlerts();

      return false;
    };

    this.onFormSendingSuccess = function(url, loadingStartDate, response, status, xhr) {
      myDebug && console.log('onFormSendingSuccess() ; args=', arguments);

      // "ajaxContentLoadingDone" event dispatch...
      this.trigger("ajaxContentLoadingDone", {
        url: url,
        target: this.attr.targetContentContainerSelector,
        duration: this.getDuration(loadingStartDate)
      });

      // ...and UI content update request!
      var eventPayload = {
        fromUrl: url,
        target: this.attr.targetContentContainerSelector,
        content: response
      };
      this.trigger("uiNeedsContentUpdate", eventPayload);
    };

    this.onFormSendingError = function(url, jqXHR, textStatus, err) {
      myDebug && logger.debug(module.id, "Ajax form loading failed!");
      this.trigger("ajaxContentLoadingError", {
        url: url,
        target: this.attr.targetContentContainerSelector
      });
      this.displayAlert(
        "core-plugins.ajax-navigation.alerts.form-error",
        {},
        "error"
      );
    };

    this.searchAndHandleAjaxForms = function ($target) {
      this.$currentAjaxForms = $target.find(this.attr.formsSelector);

      myDebug && logger.debug(module.id, this.$currentAjaxForms.length + " Ajax forms found.");

      this.off(this.$currentAjaxForms, "submit.ajax-form");
      this.on(this.$currentAjaxForms, "submit.ajax-form", this.onFormSubmit);
    };

    this.unbindPreviousAjaxForms = function ($target) {
      var ajaxFormsToUnbind = [];
      for (var i = 0, j = this.$currentAjaxForms.length; i < j; i++) {
        var form = this.$currentAjaxForms.get(i);
        if ($.contains($target, form))
          ajaxFormsToUnbind.push(form);
      }

      myDebug && logger.debug(module.id, ajaxFormsToUnbind.length +
        " Ajax forms to unbind among " + this.$currentAjaxForms.length);

      this.$currentAjaxForms = this.$currentAjaxForms.filter(function () {
        if (_.indexOf(ajaxFormsToUnbind, this) > -1) {
          // We have to unbind this previous ajax form and
          // remove it from our $currentAjaxForms jQuery collection
          var $formToUnbind = $(this);
          $formToUnbind.off();
          return false;
        }
        return true;
      });
    };

    // Component initialization
    this.after("initialize", function() {
      this.searchAndHandleAjaxForms(this.$node);
      this.on(document, "uiContentUpdated", this.onContentUpdate);
    });
  }

});