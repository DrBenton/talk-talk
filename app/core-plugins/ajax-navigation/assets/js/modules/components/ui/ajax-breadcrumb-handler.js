define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var withUrlNormalization = require("app-modules/utils/mixins/data/with-url-normalization");
  var withHttpStatusManagement = require("app-modules/utils/mixins/data/with-http-status-management");
  var $ = require("jquery");
  var logger = require("logger");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(ajaxBreadcrumbHandler, withUrlNormalization, withHttpStatusManagement);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function ajaxBreadcrumbHandler() {

    this.onBreadcrumbUpdateRequested = function(ev, data) {
      var newBreadcrumbContent;

      if (data.fromSelector) {
        var $newBreadcrumbContainer = $(data.fromSelector);
        newBreadcrumbContent = $newBreadcrumbContainer.html();
        $newBreadcrumbContainer.remove();
      } else if (data.content) {
        newBreadcrumbContent = data.content;
      }

      this.$node.html(newBreadcrumbContent);
    };

    this.onUiContentUpdated = function (ev, data) {
      // If this is the first page content, we restore its breadcrumb content
      if (this.firstPageUrl && this.firstPageBreadcrumb && data.url === this.firstPageUrl) {
        this.$node.html(this.firstPageBreadcrumb);
        myDebug && logger.debug(module.id, "First page breadcrumb restored.");
      }
    };

    this.storeInitialBradcrumb = function () {
      if (this.isCurrentPageAnError()) {
        // Don't handle anything if the current page is a error page
        return;
      }

      this.firstPageUrl = this.normalizeUrl(document.location);

      myDebug && logger.debug(module.id, "First page breadcrumb is stored for later use.");
      this.firstPageBreadcrumb = this.$node.html();
    };

    // Component initialization
    this.after("initialize", function() {
      this.storeInitialBradcrumb();
      this.on(document, "uiNeedsBreadcrumbUpdate", this.onBreadcrumbUpdateRequested);
      this.on(document, "uiContentUpdated", this.onUiContentUpdated);
    });
  }

});