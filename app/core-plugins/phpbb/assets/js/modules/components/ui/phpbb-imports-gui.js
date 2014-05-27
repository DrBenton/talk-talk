define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var withAlertsCapabilities = require("app-modules/utils/mixins/ui/with-alerts-capabilities");
  var _ = require("lodash");
  var logger = require("logger");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(phpBbImportsHandler, withAlertsCapabilities);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function phpBbImportsHandler() {

    this.defaultAttrs({
      startButtonSelector: ".start-import"
    });

    this.onStartButtonClick = function(ev, data) {
      var eventPayload = {
        itemsToImportTypes: _.keys(this.$node.data("items-types"))
      };
      this.trigger(document, "uiNeedsPhpBbImportStart", eventPayload);
    };

    this.onImportProcessStart = function(ev, data) {
      this.getStartButton().hide();
      this.$node.find(".please-wait").removeClass("hidden").show();
    };

    this.onItemsTypeImportMetadataStart = function (ev, data) {
      myDebug && logger.debug(module.id, "onItemsTypeImportMetadata() ; data=", data);

      var $phpBbItemsImportDisplay = this.getItemsTypeDisplay(data.itemsType);

      var $preparationDisplay = $phpBbItemsImportDisplay.find(".import-preparation");
      $preparationDisplay.removeClass("hidden").show();
    };

    this.onItemsTypeImportMetadataDone = function (ev, data) {
      myDebug && logger.debug(module.id, "onItemsTypeImportMetadata() ; data=", data);

      var $phpBbItemsImportDisplay = this.getItemsTypeDisplay(data.itemsType);
      var $preparationDisplay = $phpBbItemsImportDisplay.find(".import-preparation");
      $preparationDisplay.hide();
      var $nbItemsToImportDisplay = $phpBbItemsImportDisplay.find(".nb-items-to-import");
      $nbItemsToImportDisplay.find(".number").text(data.nbItemsToImport);
      $nbItemsToImportDisplay.removeClass("hidden").show();
    };

    this.onItemsTypeImportStart = function (ev, data) {
      myDebug && logger.debug(module.id, "onItemsTypeImportStart() ; data=", data);

      var $phpBbItemsImportDisplay = this.getItemsTypeDisplay(data.itemsType);
      var $inProgressDisplay = $phpBbItemsImportDisplay.find(".import-in-progress");
      $inProgressDisplay.removeClass("hidden").show();
    };

    this.onItemsTypeImportBatchDone = function (ev, data) {
      myDebug && logger.debug(module.id, "onItemsTypeImportBatchDone() ; data=", data);

      // Progress display
      var percentageDone = parseInt(data.nbItemsImportedTotal / data.nbItemsToImport * 100, 10);
      var $phpBbItemsImportDisplay = this.getItemsTypeDisplay(data.itemsType);
      var $progressComponent = $phpBbItemsImportDisplay.find(".progress-component");

      // Percentage display update
      $phpBbItemsImportDisplay.find(".progress-percentage").text(percentageDone);

      // Do we have a "setProgress" function attached to this DOM node ?
      var progressComponentNode = $progressComponent.get(0);
      if (progressComponentNode.setProgress && progressComponentNode.setProgress instanceof Function) {
        // Yes, it seems that a custom component has been put here; let"s trigger its "setProgress" function
        progressComponentNode.setProgress(percentageDone);
      } else {
        // No, our "<progress>" markup has been kept untouched; let"s simply update its "value" attribute
        $progressComponent.attr("value", percentageDone);
      }
    };

    this.onItemsTypeImportDone= function (ev, data) {
      myDebug && logger.debug(module.id, "onItemsTypeImportDone() ; data=", data);

      var $phpBbItemsImportDisplay = this.getItemsTypeDisplay(data.itemsType);
      var $inProgressDisplay = $phpBbItemsImportDisplay.find(".import-in-progress");
      $inProgressDisplay.hide();
    };

    this.onServiceError = function (ev, data) {
      this.displayTranslatedAlert(
        "core-plugins.phpbb.import.alerts.import-error",
        {"%importUrl%": data.serviceUrl},
        "error"
      );
    };

    this.getItemsTypeDisplay = function(itemsType) {
      return this.$node.find("#phpbb-" + itemsType + "-import-container");
    };

    this.getStartButton = function () {
      return this.select("startButtonSelector");
    };

    // Component initialization
    this.after("initialize", function() {
      this.on(this.getStartButton(), "click", this.onStartButtonClick);
      this.on(document, "dataPhpBbImportProcessStart", this.onImportProcessStart);
      this.on(document, "dataPhpBbItemsTypeImportMetadataStart", this.onItemsTypeImportMetadataStart);
      this.on(document, "dataPhpBbItemsTypeImportMetadataDone", this.onItemsTypeImportMetadataDone);
      this.on(document, "dataPhpBbItemsTypeImportStart", this.onItemsTypeImportStart);
      this.on(document, "dataPhpBbImportBatchDone", this.onItemsTypeImportBatchDone);
      this.on(document, "dataPhpBbItemsTypeImportDone", this.onItemsTypeImportDone);
      this.on(document, "dataPhpBbImportServiceError", this.onServiceError);
    });
  }

});