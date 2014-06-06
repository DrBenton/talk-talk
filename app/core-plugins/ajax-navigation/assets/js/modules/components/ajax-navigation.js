/**
 * This is just a single entry point for our other Ajax navigation components
 */
define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var withUrlNormalization = require("app-modules/utils/mixins/data/with-url-normalization");
  var withHttpStatusManagement = require("app-modules/utils/mixins/data/with-http-status-management");
  var varsRegistry = require("app-modules/core/vars-registry");
  var logger = require("logger");
  // Sub components
  var ajaxLinksHandler = require("./ui/ajax-links-handler");
  var ajaxFormsHandler = require("./ui/ajax-forms-handler");
  var ajaxContentUpdater = require("./ui/ajax-page-content-updater");
  var ajaxBreadcrumbHandler = require("./ui/ajax-breadcrumb-handler");
  var ajaxContentLoader = require("./data/ajax-content-loader");
  var ajaxHistory = require("./data/ajax-history");
  var ajaxContentLoadingDisplay = require("./ui/ajax-content-loading-display");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(ajaxNavigation, withUrlNormalization, withHttpStatusManagement);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function ajaxNavigation() {

    this.createSubComponents = function() {

      // The "Ajax links handler" will track "a.ajax-link" user clicks in the site container,
      // and will request content updates for the site main content container.
      ajaxLinksHandler.attachTo(varsRegistry.$siteContainer, {
        targetContentContainerSelector: this.mainContentContainerSelector
      });

      // The "Ajax forms handler" will track "form.ajax-form" user submissions in the site container,
      // and will request content updates for the site main content container too.
      ajaxFormsHandler.attachTo(varsRegistry.$siteContainer, {
        targetContentContainerSelector: this.mainContentContainerSelector
      });

      // The "Ajax breadcrumb handler" will just update the breadcrumb when it is requested.
      ajaxBreadcrumbHandler.attachTo(varsRegistry.$breadcrumb);

      // The "Ajax content updater" is "node agnostic", so we attach it to the document.
      // It will update page content parts following the events "target" instructions.
      ajaxContentUpdater.attachTo(varsRegistry.$document);

      // Idem for "Ajax content loader"...
      ajaxContentLoader.attachTo(varsRegistry.$document);

      // The "Ajax content updater" is "node agnostic", so we attach it to the document.
      // It will update page History only when the updated content part will be the main content container.
      ajaxHistory.attachTo(varsRegistry.$document, {
        contentContainerToListenSelector: this.mainContentContainerSelector
      });

      // The "Ajax content loading display" "node agnostic" too.
      ajaxContentLoadingDisplay.attachTo(varsRegistry.$document);
    };

    this.handleInitialMainContent = function() {

      myDebug && logger.debug(module.id, "Let's check if the initial content has cache information...");

      if (this.isCurrentPageAnError()) {
        // Don't handle anything if the current page is a error page
        return;
      }

      this.trigger("uiNeedsContentAjaxInstructionsCheck", {
        url: this.normalizeUrl(document.location),
        target: this.mainContentContainerSelector
      });
    };

    // Component initialization
    this.after("initialize", function() {
      this.mainContentContainerSelector = "#" + varsRegistry.$mainContent.attr("id");
      this.createSubComponents();
      this.handleInitialMainContent();
    });
  }

});