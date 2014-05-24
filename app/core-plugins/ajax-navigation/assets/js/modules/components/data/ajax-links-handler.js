define(function (require, exports, module) {
  "use strict";

  var defineComponent = require('flight').component;
  var withUniqueClass = require("app-modules/core/mixins/data/with-unique-class");
  var _ = require("lodash");
  var logger = require("logger");

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(ajaxLinksHandler, withUniqueClass);

  myDebug && logger.debug(module.id, "Component on the bridge, captain!");


  function ajaxLinksHandler() {

    this.onAjaxLinkClick = function (ev) {
      ev.preventDefault();
      var $clickedLink = $(ev.currentTarget);
      this.trigger(document, 'historyStateRequested', {
        url: $clickedLink.attr('href'),
        source: '.' + this.nodeUniqueClass
      });
    };

    // Component initialization
    this.after('initialize', function() {
      // We give a unique class to our node
      this.nodeUniqueClass = this.addUniqueClass(this.$node);
      // Let's use jQuery events proxy mechanism!
      // This way, we won't have to unbind events for each Ajax page content update
      this.$node.on('click', 'a.ajax-link', _.bind(this.onAjaxLinkClick, this));
    });
  }

});