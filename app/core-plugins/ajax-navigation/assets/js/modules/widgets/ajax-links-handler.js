define(function (require, exports, module) {

  var $ = require("jquery");

  var History = require("history");
  var purl = require("purl");
  var moment = require("moment");
  var logger = require("logger");
  var varsRegistry = require("app-modules/core/vars-registry");
  var ajaxData = require("app-modules/ajax-nav/ajax-data");
  var dataStore = require("app-modules/core/data-store");
  var ajaxFormsHandler = require("app-modules/ajax-nav/ajax-forms-handler");
  var alertsService = require("app-modules/utils/services/alerts-service");
  var widgetsFactory = require("app-modules/core/widgets-factory");

  var myDebug = !false;
  var logStats = true;

  var LOCACHE_AJAX_CONTENT_KEYS_PREFIX = "main-content-ajax-data---";

  var ajaxLoadingsCounter = 0;

  myDebug && logger.debug(module.id, "on the bridge, captain!");

  // Exports
  exports.createWidget = createWidget;


  function initHistory() {
    History.Adapter.bind(window, 'statechange', onHistoryStateChange);
  }

  function onHistoryStateChange() {

    var state = History.getState();
    myDebug && logger.debug(module.id, "state=", state);

    var requestedUrl = state.url;
    requestedUrl = normalizeUrl(requestedUrl);

    // Scroll to top
    $('html, body').animate({scrollTop:0}, 'fast');

    // Do we have cached data for this Ajax main content URL?
    var ajaxContentCachedData = getCachedAjaxMainContent(requestedUrl);
    if (ajaxContentCachedData !== null) {
      // Yes we do!
      myDebug && logger.debug(module.id, "This URL Ajax content is already in our data cache.");
      handleAjaxLoadedMainContent(ajaxContentCachedData, requestedUrl);
      if (logStats) {
        varsRegistry.$debugInfoContainer.html("This content has been displayed from a previously cached data.<br>");
        ajaxData.displayStat(requestedUrl);
      }
    } else {
      // We don't ; let's load it!
      loadAjaxMainContent(requestedUrl);
    }
  }

  function loadAjaxMainContent(contentUrl) {

    contentUrl = normalizeUrl(contentUrl);

    myDebug && logger.debug(module.id, "loadAjaxMainContent(" + contentUrl + ")");

    (function (ajaxCounterForThisLoading, startTime) {

      showAjaxLoading(true);

      $.ajax({
        url: contentUrl,
        dataType: 'text'
      })
        .done(function (data, textStatus, jqXHR) {

          myDebug && logger.debug(module.id, "Ajax link n°" + ajaxCounterForThisLoading +
            "/" + ajaxLoadingsCounter + " (" + contentUrl + ") loading done.");

          if (ajaxCounterForThisLoading < ajaxLoadingsCounter) {
            myDebug && logger.debug(module.id, "It seems that another Ajax main content loading has been " +
              "initiated afterwards. Let's ignore this one.");
            return;
          }

          showAjaxLoading(false);
          var loadingDuration = parseFloat((((new Date).getTime() - startTime) / 1000).toPrecision(3));
          handleAjaxLoadedMainContent(data, contentUrl, loadingDuration);

          if (logStats) {
            ajaxData.displayStat(contentUrl);
          }

        })
        .fail(function (jqXHR, textStatus, err) {
          myDebug && logger.debug(module.id, "Ajax link '" + contentUrl + "' loading failed!");
          alertsService.addAlert(
            "core-plugins.ajax-navigation.alerts.loading-error",
            {'%contentUrl%': contentUrl},
            "error"
          );
        });

    })(++ajaxLoadingsCounter, (new Date()).getTime());

  }

  function handleAjaxLoadedMainContent(htmlContent, loadedUrl, loadingDuration) {

    // Main content container update!
    varsRegistry.$mainContent.html(htmlContent);

    // ...and widgets update in the main container area!
    widgetsFactory.findAndTriggerWidgets(varsRegistry.$mainContent);

    // Ajax forms?
    ajaxFormsHandler.findAndHandleAjaxForms();

    // Does this main content expose ajax loading data?
    checkForAjaxLoadingDataOnAjaxLoadedMainContent(loadedUrl, loadingDuration);
  }

  function checkForAjaxLoadingDataOnAjaxLoadedMainContent(loadedUrl, loadingDuration) {

    if (getCachedAjaxMainContent(loadedUrl) !== null) {
      // We already have cached content for this URL. We have to wait for its expiration...
      return;
    }

    var $ajaxLoadingDataPlaceholder = varsRegistry.$mainContent.find('.ajax-loading-data');
    if ($ajaxLoadingDataPlaceholder.length === 0)
      return;//no loading data cache

    var ajaxCacheInstructions = $ajaxLoadingDataPlaceholder.data("ajax-cache");
    myDebug && logger.debug(module.id, "Ajax loading data=", ajaxCacheInstructions);

    if (!!ajaxCacheInstructions.duration) {

      myDebug && logger.debug(module.id, "Ajax content will be kept in cache " +
        "during " + ajaxCacheInstructions.duration + " seconds.");

      dataStore.addCacheData(
        getAjaxMainContentCacheKey(loadedUrl),
        varsRegistry.$mainContent.html(),
        ajaxCacheInstructions.duration
      );

      if (logStats) {
        var debugMsg;
        if (!loadingDuration) {
          debugMsg = "This page main content will be stored in a cache for " + ajaxCacheInstructions.duration + "s.<br>";
        } else {
          debugMsg = "This page main content has been loaded through Ajax.<br>";
        }
        varsRegistry.$debugInfoContainer.html(debugMsg);
        ajaxData.addStat(loadedUrl, {
          keptInCache: true,
          cacheDuration: ajaxCacheInstructions.duration,
          loadingDuration: loadingDuration
        });
      }
    }

  }

  function clearAlerts() {
    alertsService.clearAlerts();
  }

  function normalizeUrl(rawUrl) {
    var url = purl(rawUrl);
    var returnedUrl = url.attr('path');
    var query = url.attr('query');
    returnedUrl += query ? '?' + query : '' ;
    return returnedUrl;
  }

  function getAjaxMainContentCacheKey(url) {
    return LOCACHE_AJAX_CONTENT_KEYS_PREFIX + url;
  }

  function getCachedAjaxMainContent(url) {
    return dataStore.getCacheData(getAjaxMainContentCacheKey(url));
  }

  function onAjaxLinkClick(e) {
    e.preventDefault();

    var $clickedLink = $(this);
    myDebug && logger.debug(module.id, "Ajax link clicked :", $clickedLink);
    var targetUrl = $clickedLink.attr('href');

    // Let's remove previous page Alerts
    clearAlerts();

    myDebug && logger.debug(module.id, "History.pushState(" + targetUrl + ")");
    History.pushState(null, null, targetUrl);

    return false;
  }

  function showAjaxLoading(showItOrNot) {
    if (showItOrNot)
      $("header h1 a").addClass("ajax-loader");
    else
      $("header h1 a").removeClass("ajax-loader");
  }

  function handleInitialMainContentCache() {
    myDebug && logger.debug(module.id, "Let's check if the initial content has cache information...");
    checkForAjaxLoadingDataOnAjaxLoadedMainContent(purl().attr('path'));
  }

  function createWidget($widgetNode) {
    myDebug && logger.debug(module.id, "#createWidget() ; $widgetNode=", $widgetNode);

    varsRegistry.$document.on('click', 'a.ajax-link', onAjaxLinkClick);

    myDebug && logger.debug(module.id, "Initializing HTML5 History binding...");
    initHistory();

    handleInitialMainContentCache();

    // Ajax forms?
    ajaxFormsHandler.findAndHandleAjaxForms();
  }

});
