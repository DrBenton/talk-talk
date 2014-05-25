define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var varsRegistry = require("app-modules/core/vars-registry");
  var dataStore = require("app-modules/core/data-store");
  var withAjax = require("app-modules/core/mixins/data/with-ajax");
  var withUrlNormalization = require("app-modules/core/mixins/data/with-url-normalization");
  var withHttpStatusManagement = require("app-modules/core/mixins/data/with-http-status-management");
  var withAlertsCapabilities = require("app-modules/core/mixins/data/with-alerts-capabilities");
  var purl = require("purl");
  var logger = require("logger");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(
    ajaxContentLoader,
    withAjax, withUrlNormalization, withHttpStatusManagement, withAlertsCapabilities
  );

  var AJAX_CONTENT_CACHE_KEYS_PREFIX = "ajax-content-data---";

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");

  function ajaxContentLoader() {

    this.defaultAttrs({
      $ajaxContentContainer: varsRegistry.$mainContent
    });

    this.onAjaxContentLoadRequest = function (ev, data) {
      this.getAjaxContent(data.url);
    };

    this.getAjaxContent = function (contentUrl) {
      var url = this.normalizeUrl(contentUrl);
      var contentFromCache = dataStore.getCacheData(this.getAjaxContentCacheKey(url));
      if (null === contentFromCache) {
        // No content in the data store for this URL:
        // --> let's load it!
        this.loadAjaxContent(contentUrl);
      } else {
        // Easy-peasy, we already have this content in the data store
        this.displayAjaxContent(contentUrl, contentFromCache);
      }
    };

    this.loadAjaxContent = function (contentUrl) {
      var url = this.normalizeUrl(contentUrl);
      this.trigger("ajaxContentLoadingStart", {url: url});
      this.ajax({
        url: url,
        dataType: "text"
      }).then(
        _.partial(this.onAjaxLoadSuccess, url),
        _.partial(this.onAjaxLoadError, url)
      );
    };

    this.onHistoryState = function(ev, data) {
      this.getAjaxContent(data.url);
    };

    this.displayAjaxContent = function(url, htmlContent) {
      this.attr.$ajaxContentContainer.html(htmlContent);
      this.trigger("mainContentUpdate");
      // If this is the first page content, we restore its breadcrumb content
      if (this.firstPageUrl && this.firstPageBreadcumb && url === this.firstPageUrl) {
        varsRegistry.$breadcrumb.html(this.firstPageBreadcumb);
      }
    };

    this.getAjaxContentCacheKey = function(url) {
      return AJAX_CONTENT_CACHE_KEYS_PREFIX + url;
    };

    this.getCachedAjaxContent = function(url) {
      return dataStore.getCacheData(this.getAjaxContentCacheKey(url));
    };

    this.onAjaxLoadSuccess = function (url, content) {
      this.trigger("ajaxContentLoadingDone", {url: url});
      this.displayAjaxContent(url, content);
      this.checkForAjaxDataInstructionsInContent(url);
    };

    this.checkForAjaxDataInstructionsInContent = function(contentSourceUrl) {

      if (this.isCurrentPageAnError()) {
        // Don't handle anything if the current page is a error page
        return;
      }

      if (this.getCachedAjaxContent(contentSourceUrl) !== null) {
        // We already have cached content for this URL. We have to wait for its expiration...
        return;
      }

      var $ajaxLoadingDataPlaceholder = this.attr.$ajaxContentContainer.find('.ajax-loading-data');
      if ($ajaxLoadingDataPlaceholder.length === 0)
        return;//no loading data cache

      var ajaxCacheInstructions = $ajaxLoadingDataPlaceholder.data("ajax-cache");
      myDebug && logger.debug(module.id, "Ajax loading data=", ajaxCacheInstructions);

      if (!!ajaxCacheInstructions.duration) {

        myDebug && logger.debug(module.id, "Ajax content will be kept in cache " +
          "during " + ajaxCacheInstructions.duration + " seconds.");

        dataStore.setCacheData(
          this.getAjaxContentCacheKey(contentSourceUrl),
          this.attr.$ajaxContentContainer.html(),
          ajaxCacheInstructions.duration
        );
      }
    };

    this.onAjaxLoadError = function (url, jqXHR, textStatus, err) {
      myDebug && logger.debug(module.id, "Ajax link '" + url + "' loading failed!");
      this.trigger("ajaxContentLoadingFailed");
      // Let's display a error alert
      this.displayAlert(
        "core-plugins.ajax-navigation.alerts.loading-error",
        {"%contentUrl%": url},
        "error"
      );
    };


    this.handleInitialMainContentCache = function() {
      myDebug && logger.debug(module.id, "Let's check if the initial content has cache information...");
      if (this.isCurrentPageAnError()) {
        // Don't handle anything if the current page is a error page
        return;
      }

      this.firstPageUrl = this.normalizeUrl(document.location);
      this.checkForAjaxDataInstructionsInContent(this.firstPageUrl);
      this.firstPageBreadcumb = varsRegistry.$breadcrumb.html();
    };

    // Component initialization
    this.after("initialize", function() {
      this.handleInitialMainContentCache();
      this.on(document, "ajaxContentLoadRequested", this.onAjaxContentLoadRequest);
      this.on(document, "historyState", this.onHistoryState);
    });
  }

});