define(function (require, exports, module) {

  var $ = require("jquery");
  var Q = require("q");
  var _ = require("lodash");
  var moment = require("moment");
  var logger = require("logger");
  var alertsService = require("app-modules/utils/services/alerts-service");

  var myDebug = !false;

  var currentItemsImport = {
    type: '',
    nbBatchesRequired: 0,
    nbItemsPerBatch: 0,
    nbItemsToImport: 0,
    nbItemsImported: 0,
    currentBatchIndex: 0,
    startTime: null
  };

  myDebug && logger.debug(module.id, "on the bridge, captain!");

  var itemsToImportTypes = null;
  var currentImportedItemTypeIndex = 0;
  var $importsContainer, $startImportButton;

  //TODO: clean that code! :-)

  // Exports
  exports.createWidget = createWidget;

  function clearPreviousImports() {
    myDebug && logger.debug(module.id, "clearPreviousImports()");

    var serviceUrl = '/phpbb/import/importing/clear-previous-imports';

    return Q($.ajax({
      url: serviceUrl,
      dataType: 'json',
      type: 'POST' //more REST-y, and gives us CSRF protection :-)
    }))
      .fail(function (e) {
        displayImportError(e, serviceUrl);
        resetState();
      });
  }

  function startNextItemsTypeImport() {
    currentItemsImport.type = itemsToImportTypes[currentImportedItemTypeIndex];
    myDebug && logger.debug(module.id, "startNextItemsTypeImport() ; currentItemsImport.type=", currentItemsImport.type);
    fetchNextItemTypesImportMetadata()
      .then(function () {
        currentItemsImport.startTime = new Date();
        importNextBatch();
      });
  }

  function fetchNextItemTypesImportMetadata() {
    myDebug && logger.debug(module.id, "fetchNextItemTypesImportMetadata()");

    var serviceUrl = '/phpbb/import/importing/import-' + currentItemsImport.type + '/metadata';

    return Q($.ajax({
      url: serviceUrl,
      dataType: 'json',
      type: 'POST' //more REST-y, and gives us CSRF protection :-)
    }))
      .then(function (currentItemTypeMetada) {
        myDebug && logger.debug(module.id, "-> currentItemTypeMetada=", currentItemTypeMetada);
        currentItemsImport.nbItemsPerBatch = currentItemTypeMetada.nbItemsPerBatch;
        currentItemsImport.nbItemsToImport = currentItemTypeMetada.nbItemsToImport;
        currentItemsImport.nbBatchesRequired = currentItemTypeMetada.nbBatchesRequired;
        currentItemsImport.currentBatchIndex = 0;
        currentItemsImport.nbItemsImported = 0;

        var $phpBbItemsImportDisplay = getCurrentItemTypeDisplay();
        var $nbItemsToImportDisplay = $phpBbItemsImportDisplay.find('.nb-items-to-import');
        $nbItemsToImportDisplay.find('.number').text(currentItemsImport.nbItemsToImport);
        $nbItemsToImportDisplay.removeClass('hidden').show();
      })
      .fail(function (e) {
        displayImportError(e, serviceUrl);
        resetState();
      });
  }

  function importNextBatch() {
    myDebug && logger.debug(module.id, "importNextBatch()");

    var serviceUrl = '/phpbb/import/importing/import-' + currentItemsImport.type + '/batch/' + currentItemsImport.currentBatchIndex;

    Q($.ajax({
      url: serviceUrl,
      dataType: 'json',
      type: 'POST' //more REST-y, and gives us CSRF protection :-)
    }))
      .then(onBatchEnd)
      .fail(function (e) {
        displayImportError(e, serviceUrl);
        resetState();
      });
  }

  function onBatchEnd(createdItemsData) {
    myDebug && logger.debug(module.id, "-> createdItemsData=", createdItemsData);
    currentItemsImport.nbItemsImported += createdItemsData.created;

    // Progress display
    var percentageDone = parseInt(currentItemsImport.nbItemsImported / currentItemsImport.nbItemsToImport * 100);
    var $phpBbItemsImportDisplay = getCurrentItemTypeDisplay();
    $phpBbItemsImportDisplay.find('progress').attr('value', percentageDone);
    $phpBbItemsImportDisplay.find('.percentage').text(percentageDone);

    if (!createdItemsData.done) {
      // We still have batches to process for this phpBb items type
      currentItemsImport.currentBatchIndex++;
      setTimeout(importNextBatch, 0);
    } else {
      // No more items to process for this phpBb items type

      // Duration display
      var $doneDisplay = $phpBbItemsImportDisplay.find('.done');
      $doneDisplay.find('.duration').text(moment().diff(currentItemsImport.startTime, 'seconds'));
      $doneDisplay.removeClass('hidden').show();

      currentImportedItemTypeIndex++;
      if (currentImportedItemTypeIndex === itemsToImportTypes.length) {
        // Hey, it seems that we have imported all the phpBb items type!
        endImport();
      } else {
        // We have other phpBb items type to import. Let's roll!
        setTimeout(startNextItemsTypeImport, 0);
      }
    }
  }

  function endImport() {
    myDebug && logger.debug(module.id, "endImport()");

    var serviceUrl = '/phpbb/import/importing/finish-import';

    Q($.ajax({
      url: serviceUrl,
      dataType: 'json',
      type: 'POST' //more REST-y, and gives us CSRF protection :-)
    }))
      .then(function () {
        $importsContainer.find('.please-wait').hide();
      })
      .fail(function (e) {
        displayImportError(e, serviceUrl);
        resetState();
      });
  }

  function getCurrentItemTypeDisplay() {
    return $('#phpbb-' + currentItemsImport.type + '-import-display');
  }

  function displayImportError(e, serviceUrl) {
    alertsService.addAlert(
      "core-plugins.phpbb.import.alerts.import-error",
      {'%importUrl%': serviceUrl},
      "error"
    );
  }

  function startImport() {
    $startImportButton.hide();
    $importsContainer.find('.please-wait').removeClass('hidden').show();
    clearPreviousImports()
      .then(
        startNextItemsTypeImport,
        function(e) {
          myDebug && console.log('Previous imports deletion failed!');
        }
      );
  }

  function resetState() {
    $startImportButton.show();
    $importsContainer.find('.please-wait').hide();
  }

  function createWidget($widgetNode) {
    myDebug && logger.debug(module.id, "#createWidget() ; $widgetNode=", $widgetNode);

    //TODO: listen to "main content Ajax loading" events, and clean events binding when it happens
    $importsContainer = $widgetNode;

    itemsToImportTypes = _.keys($importsContainer.data('items-types'));
    myDebug && logger.debug(module.id, "itemsToImportTypes=", itemsToImportTypes);

    $startImportButton = $importsContainer.find('.start-import');
    $startImportButton.click(startImport);
  }

});
