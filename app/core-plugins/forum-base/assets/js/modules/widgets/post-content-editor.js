define(function (require, exports, module) {

  var Q = require("q");
  var logger = require("logger");

  // Exports
  exports.createWidget = createWidget;

  var myDebug = !false;
  var wysiwygEditorLibraryLoaded = false;

  function loadWysiwygEditorLibrary() {
    var deferred = Q.defer();

    myDebug && logger.debug(module.id, "loadWysiwygEditorLibrary()");
    require(
      [
        "vendor/js/jqjquery-wysibb/jquery.wysibb.min",
        "css!vendor/js/jqjquery-wysibb/theme/default/wbbtheme.css"
        /*
        "vendor/js/markitup-markitup/markitup/jquery.markitup",
        "vendor/js/markitup-markitup/markitup/sets/bbcode/set",
        "css!vendor/js/markitup-markitup/markitup/skins/markitup/style.css",
        "css!vendor/js/markitup-markitup/markitup/sets/bbcode/style.css"
        */
      ],
      function() {
        myDebug && logger.debug(module.id, "WYSIWYG editor library successfully loaded.");
        wysiwygEditorLibraryLoaded = true;
        deferred.resolve();
      }
    );

    return deferred.promise;
  }

  function initWysiwyg($target) {

    myDebug && logger.debug(module.id, "initWysiwyg() ; $target=", $target);

    if (!wysiwygEditorLibraryLoaded) {
      // Load WYSIWYG library, then re-run this function with the same args
      loadWysiwygEditorLibrary()
        .then(function () {
          initWysiwyg($target);
        });
      return;
    }

    var settings = {
    };

    $target.focus();
    $target.wysibb(settings);
    //$target.markItUp(settings);
  }

  function createWidget($widgetNode) {
    myDebug && logger.debug(module.id, "#createWidget() ; $widgetNode=", $widgetNode);

    initWysiwyg($widgetNode);
  }

});