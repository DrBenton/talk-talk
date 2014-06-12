define(function (require, exports, module) {
  "use strict";

  var defineComponent = require("flight").component;
  var withAjax = require("app-modules/utils/mixins/data/with-ajax");
  var _ = require("lodash");
  var Q = require("q");
  var logger = require("logger");
  var appConfig = require("app/config");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(postContentEditor, withAjax);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");

  // This var is defined at "AMD Module level" instead of Component level:
  // That way, we share the WYSIWYG library load state across our Component instances.
  var wysiwygLibraryLoaded = false;
  // Same thing for the smileys data...
  var smileysData;

  function postContentEditor() {

    this.defaultAttrs({
      wysiwygLibrary: "sceditor"
    });

    this.initWysiwyg = function () {
      var settings = {};

      switch (this.attr.wysiwygLibrary) {

        case "sceditor":
          var scEditorBase = appConfig.base_url + "/vendor/js/SCEditor/";
          var scEditor = this.$node.sceditor({
            plugins: "bbcode",
            style: scEditorBase + "minified/jquery.sceditor.default.min.css",
            toolbar: "bold,italic,underline|link,unlink|quote|bulletlist,orderedlist|emoticon|maximize|source",
            emoticonsRoot: smileysData.smileysRootPath + "/",
            emoticons: {
              dropdown: this.getSmiliesAsHash(0, 10),
              more: this.getSmiliesAsHash(10, 20)
            },
            width: "95%",
            autoUpdate: true
          });
          scEditor.focus();
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

    this.loadSmileysData = function () {
      myDebug && logger.debug(module.id, "loadSmileysData()");

      if (smileysData) {
        myDebug && logger.debug(module.id, "[smileys data already loaded --> immediate return]");
        return Q();//we already have our smileys data; let's return a fullfilled Promise right now!
      }

      return this.ajaxPromise({
        url: appConfig["base_url"] + "/smileys"
      })
        .then(
          _.bind(this.onSmileysDataRetrieved, this)
        );
    };

    this.onSmileysDataRetrieved = function (data) {
      myDebug && logger.debug(module.id, "Smileys data loaded - ", data.smileys.length, " smileys found.");
      smileysData = data;
    };

    this.loadWysiwygLibrary = function () {
      myDebug && logger.debug(module.id, "loadWysiwygLibrary()");

      if (wysiwygLibraryLoaded) {
        myDebug && logger.debug(module.id, "[WYSIWYG library already loaded --> immediate return]");
        return Q();//we already have our WYSIWYG assets; let's return a fullfilled Promise right now!
      }

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

    this.getSmiliesAsHash = function (from, limit) {
      var hash = {};
      for (var i = from, j = from + limit; i < j; i++ ) {
        var currentSmiley = smileysData.smileys[i];
        hash[currentSmiley.code] = currentSmiley.url;
      }
      return hash;
    };


    // Component initialization
    this.after("initialize", function() {

      this.loadSmileysData()
        .then(_.bind(this.loadWysiwygLibrary, this))
        .then(_.bind(this.initWysiwyg, this))
        .fail(function (e) {
          throw e;
        });

    });
  }

});