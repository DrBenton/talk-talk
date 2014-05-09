define(function (require, exports, module) {

  var $ = require("jquery");

  var History = require("history");
  var purl = require("purl");
  var moment = require("moment");
  var logger = require("logger");
  var varsRegistry = require("app-modules/core/vars-registry");

  var myDebug = !false;

  var nbUsersToImport = 0;
  var nbUsersCreatedPerBatch = 100;
  var nbBatchesForUsersImport = 0;
  var nbUsersImported = 0;
  var currentUsersImportBatchIndex = 0;
  
  myDebug && logger.debug(module.id, "on the bridge, captain!");

  // Exports
  exports.createWidget = createWidget;
  
  function importNextUsersBatch () {
    $.ajax({
      url: '/phpbb/import/importing/import-users/' + currentUsersImportBatchIndex,
      dataType: 'json'
    })
      .done(function (createdUsersData) {
        nbUsersImported += createdUsersData.created;
        var percentageDone = parseInt(nbUsersImported / nbUsersToImport * 100);
        var $phpBbUsersImportDisplay = $('#phpbb-users-import-display');
        $phpBbUsersImportDisplay.find('progress').attr('value', percentageDone);
        $phpBbUsersImportDisplay.find('.percentage').text(percentageDone);
        if (!createdUsersData.done) {
          currentUsersImportBatchIndex++;
          setTimeout(importNextUsersBatch, 100);
        }
      })
      .fail(function () {
        
      })
  }

  function createWidget($widgetNode) {
    myDebug && logger.debug(module.id, "#createWidget() ; $widgetNode=", $widgetNode);

    nbUsersToImport = parseInt($widgetNode.data('phpbb-nb-users'));
    nbBatchesForUsersImport = Math.ceil(nbUsersToImport / nbUsersCreatedPerBatch);

    var $startImportButton = $widgetNode.find('.start-import');
    $startImportButton.click(function () {
      $startImportButton.off();
      importNextUsersBatch();
    });
  }

});
