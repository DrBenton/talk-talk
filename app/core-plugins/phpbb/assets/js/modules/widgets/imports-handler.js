define(function (require, exports, module) {

  var $ = require("jquery");
  var Q = require("q");

  var purl = require("purl");
  var moment = require("moment");
  var logger = require("logger");

  var myDebug = !false;

  var currentItemsImport = {
    type: '',
    nbBatchesRequired: 0,
    nbItemsPerBatch: 0,
    nbItemsToImport: 0,
    nbItemsImported: 0,
    currentBatchIndex: 0
  };

  myDebug && logger.debug(module.id, "on the bridge, captain!");

  var itemsToImportTypes = [
    'users',
    'forums'
  ];
  var currentImportedItemTypeIndex = 0;

  // Exports
  exports.createWidget = createWidget;

  function startNextItemsTypeImport() {
    currentItemsImport.type = itemsToImportTypes[currentImportedItemTypeIndex];
    myDebug && logger.debug(module.id, "startNextItemsTypeImport() ; currentItemsImport.type=", currentItemsImport.type);
    fetchNextItemTypesImportMetadata()
      .then(function () {
        importNextBatch();
      });
  }

  function fetchNextItemTypesImportMetadata() {
    myDebug && logger.debug(module.id, "fetchNextItemTypesImportMetadata()");
    return Q($.ajax({
      url: '/phpbb/import/importing/import-' + currentItemsImport.type + '/metadata',
      dataType: 'json'
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
      .fail(function () {

      })
  }

  function importNextBatch() {
    myDebug && logger.debug(module.id, "importNextBatch()");
    Q($.ajax({
      url: '/phpbb/import/importing/import-' + currentItemsImport.type + '/batch/' + currentItemsImport.currentBatchIndex,
      dataType: 'json'
    }))
      .then(function (createdItemsData) {
        myDebug && logger.debug(module.id, "-> createdItemsData=", createdItemsData);
        currentItemsImport.nbItemsImported += createdItemsData.created;
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
          currentImportedItemTypeIndex++;
          if (currentImportedItemTypeIndex === itemsToImportTypes.length) {
            // Hey, it seems that we have imported all the phpBb items type!
          } else {
            // We have other phpBb items type to import. Let's roll!
            setTimeout(startNextItemsTypeImport, 0);
          }
        }
      })
      .fail(function () {

      })
  }

  function getCurrentItemTypeDisplay() {
    return $('#phpbb-' + currentItemsImport.type + '-import-display');
  }

  function createWidget($widgetNode) {
    myDebug && logger.debug(module.id, "#createWidget() ; $widgetNode=", $widgetNode);

    var $startImportButton = $widgetNode.find('.start-import');
    $startImportButton.click(function () {
      $startImportButton.off().remove();
      $widgetNode.find('.please-wait').removeClass('hidden').show();
      startNextItemsTypeImport();
    });
  }

});
