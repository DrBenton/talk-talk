define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var withAlertsCapabilities = require("app-modules/utils/mixins/ui/with-alerts-capabilities");
  var _ = require("lodash");
  var Q = require("q");
  var logger = require("logger");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(postContentEditor, withAlertsCapabilities);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");

  // This var is defined at "AMD Module level" instead of Component level:
  // That way, we share the WYSIWYG library load state across our Component instances.
  var wysiwygLibraryLoaded = false;

  function postContentEditor() {

    this.defaultAttrs({
      wysiwygLibrary: "wysibb"
    });

    this.initWysiwyg = function () {
      var settings = {};

      switch (this.attr.wysiwygLibrary) {

        case "wysibb":
          this.$node.focus();
          this.$node.wysibb(settings);

        case "markitup":
          this.$node.markItUp(settings);

      }
    };

    this.loadWysiwygLibrary = function () {
      myDebug && logger.debug(module.id, "loadWysiwygLibrary()");

      var deferred = Q.defer();
      var wysiwygLibraryAssetsToLoad = this.getWysiwygLibraryAssetsToLoad();

      require(
        wysiwygLibraryAssetsToLoad,
        function() {
          myDebug && logger.debug(module.id, "WYSIWYG editor library successfully loaded.");
          wysiwygLibraryLoaded = true;
          deferred.resolve();
        }
      );

      return deferred.promise;
    };

    this.getWysiwygLibraryAssetsToLoad = function () {
      switch (this.attr.wysiwygLibrary) {
        case "wysibb":
          return [
            "vendor/js/jqjquery-wysibb/jquery.wysibb.min",
            "css!vendor/js/jqjquery-wysibb/theme/default/wbbtheme.css"
          ];
        case "markitup":
          return [
            "vendor/js/markitup-markitup/markitup/jquery.markitup",
            "vendor/js/markitup-markitup/markitup/sets/bbcode/set",
            "css!vendor/js/markitup-markitup/markitup/skins/markitup/style.css",
            "css!vendor/js/markitup-markitup/markitup/sets/bbcode/style.css"
          ];
      }
    };


    // Component initialization
    this.after("initialize", function() {

      if (wysiwygLibraryLoaded) {
        // The WYSIWYG library is already loaded: let's init the WYSIWYG editor right now!
        this.initWysiwyg();
      } else {
        // The WYSIWYG library has not been loaded yet: let's load it, then init the WYSIWYG editor
        this.loadWysiwygLibrary()
          .then(
            _.bind(this.initWysiwyg, this)
          );
      }

    });
  }

});