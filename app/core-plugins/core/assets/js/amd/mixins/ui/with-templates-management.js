define(function (require, exports, module) {
  'use strict';

  var _ = require('lodash');
  var Q = require('q');
  var Handlebars = require('handlebars');
  // Templates will be registered in our app vars registry
  var varsRegistry = require('app/core/vars-registry');

  // Exports: mixin definition
  module.exports = withTemplatesManagement;

  // Vars registry "theme" structure init
  varsRegistry.theme = varsRegistry.theme || {};
  varsRegistry.theme.templates = varsRegistry.theme.templates || {};

  function withTemplatesManagement() {

    this.registerCompiledTemplate = function(templateId, compiledTemplateFunc) {
      if (!_.isFunction(compiledTemplateFunc)) {
        throw new Error('Compiled template "'+templateId+'" is not a Handlebars compiled template function!');
      }
      varsRegistry.theme.templates[templateId] = compiledTemplateFunc;
    };

    this.registerPartial = function(partialId, compiledTemplateFunc) {
      Handlebars.registerPartial(partialId, compiledTemplateFunc);
    };

    this.loadTemplates = function(templatesHash) {
      var deferred = Q.defer();

      var requiredTemplatesIds = [];
      var requiredTemplatesUrls = [];
      _.forEach(templatesHash, function(templateUrl, templateId) {
        requiredTemplatesIds.push(templateId);
        requiredTemplatesUrls.push('hbs!' + templateUrl);
      });

      require(
        requiredTemplatesUrls,
        _.bind(function () {
          _.forEach(arguments, _.bind(function (compiledTemplateFunc, i) {
            var templateId = requiredTemplatesIds[i];
            this.registerCompiledTemplate(templateId, compiledTemplateFunc);
          }, this));
          deferred.resolve();
        }, this),
        function (err) {
          deferred.reject(new Error('Template(s) loading failed: ' + err.requireModules.join(', ')));
        }
      );

      return deferred.promise;
    };

    this.renderTemplate = function (templateId, data)
    {
      if (!varsRegistry.theme.templates[templateId]) {
        throw new Error('Template "'+templateId+'" is unknown!');
      }

      return varsRegistry.theme.templates[templateId](data);
    };

  }

});