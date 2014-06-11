define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var withAjax = require("app-modules/utils/mixins/data/with-ajax");
  var withStringUtils = require("app-modules/utils/mixins/data/with-string-utils");
  var withDateUtils = require("app-modules/utils/mixins/data/with-date-utils");
  var _ = require("lodash");
  var logger = require("logger");

  var myDebug = false;

  // Exports: component definition
  module.exports = defineComponent(phpBbImportsHandler,
    withAjax, withStringUtils, withDateUtils);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function phpBbImportsHandler() {

    this.defaultAttrs({
      clearPreviousImportsServiceUrl: "/phpbb/import/importing/clear-previous-imports",
      itemsTypeMetadataServiceUrl: "/phpbb/import/importing/import-%item-type%/metadata",
      itemsTypeImportBatchServiceUrl: "/phpbb/import/importing/import-%item-type%/batch/%batch-index%",
      finishImportProcessServiceUrl: "/phpbb/import/importing/finish-import"
    });

    this.onStartImportRequest = function(ev, data) {
      this.currentImportedItemTypeIndex = 0;
      this.itemsToImportTypes = data.itemsToImportTypes;
      this.startImport();
    };

    this.startImport = function() {
      this.trigger(document, "dataPhpBbImportProcessStart");

      this.currentItemsImport = {
        type: "",
        nbBatchesRequired: 0,
        nbItemsPerBatch: 0,
        nbItemsToImport: 0,
        nbItemsImported: 0,
        currentBatchIndex: 0,
        startTime: null
      };

      this.clearPreviousImports()
        .then(
          _.bind(this.prepareNextItemsTypeImport, this)
        );
    };

    this.prepareNextItemsTypeImport = function() {
      var nextItemsType = this.itemsToImportTypes[this.currentImportedItemTypeIndex];
      myDebug && logger.debug(module.id, "prepareNextItemsTypeImport(); nextItemsType=", nextItemsType);

      this.fetchItemsTypeImportMetadata(nextItemsType)
        .then(
          _.bind(this.startItemsTypeImport, this, nextItemsType)
        );
    };

    this.fetchItemsTypeImportMetadata = function(itemsType) {
      myDebug && logger.debug(module.id, "fetchItemsTypeImportMetadata()");

      this.trigger(document, "dataPhpBbItemsTypeImportMetadataStart", {
        itemsType: itemsType
      });

      var serviceUrl = this.handleStringVars(
        this.attr.itemsTypeMetadataServiceUrl,
        {
          "%item-type%": itemsType
        }
      );

      return this.ajaxPromise({
        url: serviceUrl,
        dataType: "json",
        type: "GET"
      })
        .then(
          _.bind(this.onItemsTypeImportMetadataSuccess, this, itemsType),
          _.bind(function(err) {
            this.triggerImportErrorEvent(err, serviceUrl);
            this.resetState();
            throw new Error("Metadata retrieval of '"+itemsType+"' items failed.");
          }, this)
        );
    };

    this.onItemsTypeImportMetadataSuccess = function (itemsType, itemsTypeMetadata) {
      myDebug && logger.debug(module.id, "-> itemsTypeMetadata=", itemsTypeMetadata);

      this.currentItemsImport.nbItemsPerBatch = itemsTypeMetadata.nbItemsPerBatch;
      this.currentItemsImport.nbItemsToImport = itemsTypeMetadata.nbItemsToImport;
      this.currentItemsImport.nbBatchesRequired = itemsTypeMetadata.nbBatchesRequired;
      this.currentItemsImport.currentBatchIndex = 0;
      this.currentItemsImport.nbItemsImported = 0;

      var eventPayload = _.extend({}, itemsTypeMetadata, {itemsType: itemsType});
      this.trigger(document, "dataPhpBbItemsTypeImportMetadataDone", eventPayload);
    };

    this.startItemsTypeImport = function (itemsType) {
      // "items type import start" event dispatch
      this.trigger(document, "dataPhpBbItemsTypeImportStart", {
        itemsType: itemsType
      });
      // Let's start the first imports batch!
      this.currentItemsImport.type = itemsType;
      this.currentItemsImport.startTime = new Date();
      this.startNextItemsTypeImportBatch();
    };

    this.startNextItemsTypeImportBatch = function () {
      myDebug && logger.debug(module.id, "startNextItemsTypeImportBatch()");

      this.trigger(document, "dataPhpBbImportBatchStart", {
        itemsType: this.currentItemsImport.type,
        batchIndex: this.currentItemsImport.currentBatchIndex,
        nbBatchesRequired: this.currentItemsImport.nbBatchesRequired
      });

      var serviceUrl = this.handleStringVars(
        this.attr.itemsTypeImportBatchServiceUrl,
        {
          "%item-type%": this.currentItemsImport.type,
          "%batch-index%": this.currentItemsImport.currentBatchIndex
        }
      );

      return this.ajaxPromise({
        url: serviceUrl,
        dataType: "json",
        type: "POST"
      })
        .then(
          _.bind(this.onNextItemsTypeImportBatchEnd, this),
          _.bind(function(err) {
            this.triggerImportErrorEvent(err, serviceUrl);
            this.resetState();
            throw new Error("Failed to execute batch n°"+this.currentItemsImport.currentBatchIndex+" of '"+this.currentItemsImport.type+"' items.");
          }, this)
        );
    };

    this.onNextItemsTypeImportBatchEnd = function (createdItemsData) {
      myDebug && logger.debug(module.id, "-> createdItemsData=", createdItemsData);

      this.currentItemsImport.nbItemsImported += createdItemsData.created;
      var wasLastBatch = createdItemsData.done;

      // Event dispatch
      this.trigger(document, "dataPhpBbImportBatchDone", {
        itemsType: this.currentItemsImport.type,
        batchIndex: this.currentItemsImport.currentBatchIndex,
        nbBatchesRequired: this.currentItemsImport.nbBatchesRequired,
        nbItemsImportedInBatch: createdItemsData.created,
        nbItemsImportedTotal: this.currentItemsImport.nbItemsImported,
        nbItemsToImport: this.currentItemsImport.nbItemsToImport,
        wasLastBatch: wasLastBatch
      });

      myDebug && logger.debug(module.id, "Was it the last batch for this items type?", wasLastBatch);
      if (wasLastBatch) {

        // No more items to process for this phpBb items type

        // "items type import done" event dispatch
        this.trigger(document, "dataPhpBbItemsTypeImportDone", {
          itemsType: this.currentItemsImport.type,
          duration: this.getDuration(this.currentItemsImport.startTime)
        });

        this.currentImportedItemTypeIndex++;
        if (this.currentImportedItemTypeIndex === this.itemsToImportTypes.length) {
          // Hey, it seems that we have imported all the phpBb items type!
          this.finishImportProcess();
        } else {
          // We have other phpBb items type to import. Let's roll!
          _.defer(
            _.bind(this.prepareNextItemsTypeImport, this)
          );
        }

      } else {

        // We still have batches to process for this phpBb items type
        // --> launch another batch!
        this.currentItemsImport.currentBatchIndex++;
        _.defer(_.bind(this.startNextItemsTypeImportBatch, this));

      }
    };

    this.clearPreviousImports = function() {
      myDebug && logger.debug(module.id, "clearPreviousImports()");

      var serviceUrl = this.attr.clearPreviousImportsServiceUrl;
      this.trigger(document, "dataPhpBbPreviousImportClearingStart");

      return this.ajaxPromise({
        url: serviceUrl,
        dataType: "json",
        type: "POST"
      })
        .then(
          _.bind(function (data) {
            this.trigger(document, "dataPhpBbPreviousImportClearingDone", data);
            this.resetState();
          }, this),
          _.bind(function (err) {
            this.triggerImportErrorEvent(err, serviceUrl);
            this.resetState();
            throw new Error("Previous imports clearing failed.");
          }, this)
        );
    };

    this.finishImportProcess = function () {
      myDebug && logger.debug(module.id, "endImport()");

      var serviceUrl = this.attr.finishImportProcessServiceUrl;

      this.ajaxPromise({
        url: serviceUrl,
        dataType: "json",
        type: "POST"
      })
        .then(
        _.bind(function () {
            this.trigger(document, "dataPhpBbImportProcessDone");
          }, this),
        _.bind(function (err) {
            this.triggerImportErrorEvent(err, serviceUrl);
            this.resetState();
          }, this)
        );
    };

    this.triggerImportErrorEvent = function(err, serviceUrl) {
      this.trigger(document, "dataPhpBbImportServiceError", {
        errorMsg: err.message,
        serviceUrl: serviceUrl
      });
    };

    this.resetState = function () {
      this.currentImportedItemTypeIndex = 0;
    };

    this.getStartButton = function () {
      return this.select("startButtonSelector");
    };

    // Component initialization
    this.after("initialize", function() {
      this.on(document, "uiNeedsPhpBbImportStart", this.onStartImportRequest);
    });
  }

});