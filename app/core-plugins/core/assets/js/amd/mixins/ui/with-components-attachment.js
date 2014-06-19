define(function (require, exports, module) {
  'use strict';

  var $ = require('jquery');
  var _ = require('lodash');
  var Q = require('q');

  // Exports: mixin definition
  module.exports = withComponentsAttachment;


  function withComponentsAttachment() {

    this.attachComponentTo = function(component, $node) {
      var deferred = Q.defer();

      if (!component || !component.attachTo) {

        deferred.promise.reject(new Error('Invalid component "'+component+'" spotted!'));

      } else {

        if (!$node.jquery) {
          $node = $($node);
        }

        component.attachTo($node);
        $node.addClass('flight-component-attached');

        deferred.resolve();

        this.trigger(document, 'componentAttached', {component: component.toString()});

      }

      return deferred.promise;
    };

  }

});