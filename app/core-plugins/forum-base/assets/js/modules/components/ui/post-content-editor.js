define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var withAlertsCapabilities = require("app-modules/utils/mixins/ui/with-alerts-capabilities");
  var _ = require("lodash");
  var Q = require("q");
  var logger = require("logger");
  var appConfig = require("app/config");

  var myDebug = false;

  // Exports: component definition
  module.exports = defineComponent(postContentEditor, withAlertsCapabilities);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");

  // This var is defined at "AMD Module level" instead of Component level:
  // That way, we share the WYSIWYG library load state across our Component instances.
  var wysiwygLibraryLoaded = false;

  function postContentEditor() {

    this.defaultAttrs({
      wysiwygLibrary: "sceditor"
    });

    this.initWysiwyg = function () {
      var settings = {};

      switch (this.attr.wysiwygLibrary) {

        case "sceditor":
          this.$node.focus();
          var scEditorBase = appConfig.base_url + "/vendor/js/SCEditor/";
          this.$node.sceditor({
            plugins: "bbcode",
            style: scEditorBase + "minified/jquery.sceditor.default.min.css",
            toolbar: "bold,italic,underline|link,unlink|quote|bulletlist,orderedlist|emoticon|maximize|source",
            emoticonsRoot: scEditorBase
          });
          break;

        case "wysibb":
          //this.$node.focus();
          this.$node.wysibb(settings);
          break;

        case "markitup":
          this.$node.markItUp(settings);
          break;

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
        case "sceditor":
          return [
            "vendor/js/SCEditor/minified/jquery.sceditor.bbcode.min",
            "css!vendor/js/SCEditor/minified/themes/default.min.css"
          ];
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