define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var withAlertsCapabilities = require("app-modules/core/mixins/data/with-alerts-capabilities");
  var varsRegistry = require("app-modules/core/vars-registry");
  var _ = require("lodash");
  var logger = require("logger");

  require("jquery-form");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(ajaxLinksHandler, withAlertsCapabilities);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function ajaxLinksHandler() {

    this.onMainContentUpdate = function (ev, data) {
      if (this.$currentAjaxForms && this.$currentAjaxForms.length > 0) {
        this.unbindPreviousAjaxForms();
      }

      this.searchAndHandleAjaxForms();
    };

    this.onFormSubmit = function (ev, data) {

      myDebug && logger.debug(module.id, "onFormSubmit()");

      ev.preventDefault();

      var $form = $(ev.currentTarget);

      $form.ajaxSubmit({
        headers: {'X-Requested-With': "XMLHttpRequest"},
        success: _.bind(this.onFormSendingSuccess, this, new Date),
        error: _.bind(this.onFormSendingError, this)
      });

      // Let's remove previous page Alerts
      this.clearAlerts();

      return false;
    };

    this.onFormSendingSuccess = function(loadingStartDate, response, status, xhr) {
      myDebug && console.log('onFormSendingSuccess() ; args=', arguments);
      varsRegistry.$mainContent.html(response);
      this.trigger("mainContentUpdate");
    };

    this.onFormSendingError = function(jqXHR, textStatus, err) {
      myDebug && logger.debug(module.id, "Ajax form loading failed!");
      this.displayAlert(
        "core-plugins.ajax-navigation.alerts.form-error",
        {},
        "error"
      );
    };

    this.searchAndHandleAjaxForms = function () {
      this.$currentAjaxForms = this.$node.find("form.ajax-form");

      myDebug && logger.debug(module.id, this.$currentAjaxForms.length + " Ajax forms.");

      this.on(this.$currentAjaxForms, "submit", this.onFormSubmit);
    };

    this.unbindPreviousAjaxForms = function () {

      var ajaxFormsToUnbind = [];
      for (var i = 0, j = this.$currentAjaxForms.length; i < j; i++) {
        var form = this.$currentAjaxForms.get(i);
        if ($.contains(this.$node, form))
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
      this.searchAndHandleAjaxForms();
      this.on(document, "mainContentUpdate", this.onMainContentUpdate);
    });
  }

});