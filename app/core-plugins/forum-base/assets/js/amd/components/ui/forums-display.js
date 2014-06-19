define(function (require, exports, module) {
  'use strict';

  var defineComponent = require('flight').component;
  var withForumsData = require('app/core-plugins/forum-base/assets/js/amd/mixins/data/with-forums-data');
  var withTemplatesManagement = require('app/core/mixins/ui/with-templates-management');
  var _ = require('lodash');
  var logger = require('logger');

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(forumsDisplay, withForumsData, withTemplatesManagement);

  myDebug && logger.debug(module.id, 'Component on the bridge, captain!');


  function forumsDisplay() {

    this.defaultAttrs({
    });

    this.displayForums = function ()
    {
      this.$node.html(
        this.renderTemplate('inc/forums-display', {forums: this.forumsData})
      );
    };

    // Component initialization
    this.after('initialize', function() {
      this.getForumsData()
        .then(_.bind(this.displayForums, this));
    });
  }

});