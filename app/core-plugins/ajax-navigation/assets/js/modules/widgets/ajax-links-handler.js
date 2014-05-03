define(function (require, exports, module) {

  var $ = require("jquery");
  var _ = require("lodash");

  var History = require("history");
  var purl = require("purl");
  var moment = require("moment");
  var logger = require("logger");
  var varsRegistry = require("app-modules/core/vars-registry");
  var ajaxData = require("app-modules/ajax-nav/ajax-data");
  var dataStore = require("app-modules/core/data-store");
  var ajaxFormsHandler = require("app-modules/ajax-nav/ajax-forms-handler");
  //var notifyService = require("js/services/notify-srv");
  var widgetsFactory = require("app-modules/core/widgets-factory");

  var myDebug = !false;
  var logStats = true;

  var LOCACHE_AJAX_CONTENT_KEYS_PREFIX = "main-content-ajax-data---";

  var $currentAjaxLinks;
  var ajaxLoadingsCounter = 0;

  myDebug && logger.debug(module.id, "on the bridge, captain!");
  
  // Exports
  exports.createWidget = createWidget;

  
  function initHistory () {
    History.Adapter.bind(window, 'statechange', onHistoryStateChange);
  }

  function onHistoryStateChange () {

    var state = History.getState();
    myDebug && logger.debug(module.id, "state=", state);

    var requestedUrl = state.url;
    requestedUrl = normalizeUrl(requestedUrl);

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

  function loadAjaxMainContent (contentUrl) {

    contentUrl = normalizeUrl(contentUrl);

    myDebug && logger.debug(module.id, "loadAjaxMainContent(" + contentUrl + ")");

    (function (ajaxCounterForThisLoading, startTime) {

      showAjaxLoading(true);

      $.get(contentUrl)
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
          notifyService.notify("Error while ajax-loading '" + contentUrl + "' ! (" + err + ")", "error");

        });

    })(++ajaxLoadingsCounter, (new Date()).getTime());

  }

  function handleAjaxLoadedMainContent (htmlContent, loadedUrl, loadingDuration) {
    // Previous Ajax links event cleaning (for those who where in the main container)
    unbindPreviousMainContentAjaxLinks();

    // Main content container update!
    varsRegistry.$mainContentContainer.html(htmlContent);

    // ...and widgets update in the main container area!
    widgetsFactory.findAndTriggerWidgets(varsRegistry.$mainContentContainer);

    // Ajax forms?
    ajaxFormsHandler.findAndHandleAjaxForms();

    // Does this main content expose ajax loading data?
    checkForAjaxLoadingDataOnAjaxLoadedMainContent(loadedUrl, loadingDuration);
  }

  //TODO: refactor this so that we can use it for other containers
  function unbindPreviousMainContentAjaxLinks () {
    var ajaxLinksToUnbind = [];
    for (var i = 0, j = $currentAjaxLinks.length; i < j; i++) {
      var link = $currentAjaxLinks.get(i);
      if ($.contains(varsRegistry.$mainContentContainer, link))
        ajaxLinksToUnbind.push(link);
    }

    myDebug && logger.debug(module.id, ajaxLinksToUnbind.length +
      " Ajax links to unbind among " + $currentAjaxLinks.length);

    $currentAjaxLinks = $currentAjaxLinks.filter(function () {
      if (_.indexOf(ajaxLinksToUnbind, this) > -1) {
        // We have to unbind this previous "main content" link and
        // remove it from our $currentAjaxLinks jQuery collection
        var $linkToUnbind = $(this);
        $linkToUnbind.off();
        return false;
      }
      return true;
    });
  }

  function checkForAjaxLoadingDataOnAjaxLoadedMainContent (loadedUrl, loadingDuration) {

    if (getCachedAjaxMainContent(loadedUrl) !== null) {
      // We already have cached content for this URL. We have to wait for its expiration...
      return;
    }

    var $ajaxLoadingDataPlaceholder = varsRegistry.$mainContentContainer.find('.ajax-loading-data');
    if ($ajaxLoadingDataPlaceholder.length === 0)
      return;//no loading data cache

    var ajaxCacheInstructions = $ajaxLoadingDataPlaceholder.data("ajax-cache");
    myDebug && logger.debug(module.id, "Ajax loading data=", ajaxCacheInstructions);

    if (!!ajaxCacheInstructions.duration) {

      myDebug && logger.debug(module.id, "Ajax content will be kept in cache " +
        "during " + ajaxCacheInstructions.duration + " seconds.");

      dataStore.addCacheData(
        getAjaxMainContentCacheKey(loadedUrl),
        varsRegistry.$mainContentContainer.html(),
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

  function normalizeUrl (rawUrl) {
    return purl(rawUrl).attr('path');
  }

  function getAjaxMainContentCacheKey (url) {
    return LOCACHE_AJAX_CONTENT_KEYS_PREFIX + url;
  }

  function getCachedAjaxMainContent (url) {
    return dataStore.getCacheData(getAjaxMainContentCacheKey(url));
  }

  function onAjaxLinkClick (e) {
    e.preventDefault();

    var $clickedLink = $(this);
    var targetUrl = $clickedLink.attr('href');

    myDebug && logger.debug(module.id, "History.pushState(" + targetUrl + ")");
    History.pushState(null, null, targetUrl);

    return false;
  }

  function showAjaxLoading (showItOrNot) {
    if (showItOrNot)
      $("header h1 a").addClass("ajax-loader");
    else
      $("header h1 a").removeClass("ajax-loader");
  }

  function handleInitialMainContentCache () {
    myDebug && logger.debug(module.id, "Let's check if the initial content has cache information...");
    checkForAjaxLoadingDataOnAjaxLoadedMainContent(purl().attr('path'));
  }

  function createWidget ($widgetNode) {
    myDebug && logger.debug(module.id, "#createWidget() ; $widgetNode=", $widgetNode);
    
    $currentAjaxLinks = $widgetNode.find("a.ajax-link");

    myDebug && logger.debug(module.id, $currentAjaxLinks.length + " Ajax links.");
    
    $currentAjaxLinks.click(onAjaxLinkClick);

    myDebug && logger.debug(module.id, "Initializing HTML5 History binding...");
    initHistory();
    
    handleInitialMainContentCache();
  }

});
