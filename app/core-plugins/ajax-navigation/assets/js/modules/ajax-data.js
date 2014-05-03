define(function (require, exports, module) {

  var _ = require("lodash");
  var logger = require("logger");
  var varsRegistry = require("app-modules/core/vars-registry");

  var myDebug = !false;

  var statsData = {};

  var statsDefaults = {
    keptInCache: false,
    cacheDuration: 0,
    date: null,
    nbLoadings: 0,
    loadingDuration: 0
  }

  // Exports
  exports.getStat = getStat;
  exports.addStat = addStat;
  exports.displayStat = displayStat;
  

  function getStat(url) {
    return statsData[url];
  }

  function addStat(url, stats) {

    _.defaults(stats, statsDefaults);

    if (statsData[url] && statsData[url].nbLoadings > 0) {
      // We already have a loading stat for this URL
      // --> we have to compute the average loading duration
      stats.nbLoadings = statsData[url].nbLoadings + 1;
      stats.loadingDuration = ((statsData[url].loadingDuration / statsData[url].nbLoadings) + stats.loadingDuration) / stats.nbLoadings;
    } else {
      // This is the first loading stat for this URL
      stats.nbLoadings = (stats.loadingDuration > 0) ? 1 : 0;
      stats.loadingDuration = isNaN(stats.loadingDuration) ? 0 : stats.loadingDuration;
    }

    stats.date = new Date();
    statsData[url] = stats;
  }

  function displayStat(url) {
    myDebug && logger.log('statsData=', statsData);
    var urlStats = getStat(url);

    if (!urlStats) {
      return;
    }

    var ajaxLoadingsStats = computeAjaxStats();
    var debugMsg = "<br>";
    debugMsg += "This <code>" + url + "</code> content has been loaded in " + urlStats.loadingDuration + "s." +
      moment(urlStats.date).fromNow() + "<br>";
    if (urlStats.keptInCache) {
      debugMsg += "It is kept in the data store during " + moment.duration(urlStats.cacheDuration, "seconds").humanize() + ".<br>";
    } else {
      debugMsg += "It isn't kept in the data store.<br>";
    }
    debugMsg += "<p>Average Ajax loadings duration : " + ajaxLoadingsStats.averageDuration +
      "s. for " + ajaxLoadingsStats.nbRequests + " request(s).</p>";
    varsRegistry.$debugInfoContainer.append(debugMsg);
  }

  var computeAjaxStats = exports.computeAjaxStats = function () {
    var nbAjaxStats = _.size(statsData);
    if (nbAjaxStats === 0) {
      return { averageDuration: 0, nbRequests: 0 };
    }

    var loadingsDurationsSum = 0;
    var nbInstantLoadings = 0;
    var nbRequests = 0;
    _.forEach(statsData, function (stats, url) {
      nbRequests += stats.nbLoadings;
      if (stats.loadingDuration === 0 || isNaN(stats.loadingDuration)) {
        nbInstantLoadings++;
      } else {
        loadingsDurationsSum += stats.loadingDuration;
      }
    });

    var nbStatsToCompute = nbAjaxStats - nbInstantLoadings;

    var computedData = {
      averageDuration: parseFloat((loadingsDurationsSum / nbStatsToCompute).toPrecision(3)),
      nbRequests: nbRequests
    }
    myDebug && logger.log('computeAjaxStats ; computedData=', computedData);

    return computedData;
  }

});