define(function (require, exports, module) {
  'use strict';

  var defineComponent = require('flight').component;
  var withAjax = require('app/core/mixins/data/with-ajax');
  var varsRegistry = require('app/core/vars-registry');
  var $ = require('jquery');
  var _ = require('lodash');
  var logger = require('logger');

  var myDebug = !false;

  // Exports: component definition
  module.exports = defineComponent(appJsCoreCompilationHandler, withAjax);

  myDebug && logger.debug(module.id, 'Component on the bridge, captain!');


  function appJsCoreCompilationHandler() {

    this.defaultAttrs({
      jsFilesDataSelector: '#app-js-core-files-to-compile',
      startBuildBtSelector: '#build',
      buildMessagesSelector: '#buildMessages',
      buildOutputSelector: '#output',
      saveBtSelector: '#save'
    });

    this.jsFilesToCompile = [];

    this.startCompilation = function () {

      //@see https://groups.google.com/forum/#!topic/requirejs/Hf-qNmM0ceI
      var requireJsConfig = requirejs.s.contexts._.config;

      this.select('buildMessagesSelector').val('Compilation starts. Please wait...');
      var startTime = new Date();

      _.defer(_.bind(function () {

        requirejs.optimize({
            baseUrl: requireJsConfig.baseUrl,
            paths: requireJsConfig.paths,
            shim: requireJsConfig.shim,
            map: requireJsConfig.map,
            packages: requireJsConfig.packages,
            inlineText: true,
            include: this.jsFilesToCompile,
            findNestedDependencies: true,
            stubModules: [],
            wrapShim: true,
            optimize: 'none',//TODO: allow customization of this param
            waitSeconds: 15,
            out: _.bind(function (text) {
              this.select('buildOutputSelector').val(text);
              this.select('saveBtSelector').removeClass('hidden').show();
            }, this)
          },
          _.bind(function (buildText) {
            this.select('buildMessagesSelector').val('Finished! ('+Math.round(((new Date()).getTime() - startTime.getTime()) / 1000)+'s.)\n\n' + buildText);
          }, this)
        );

      }, this));

    };

    this.saveCompilationResult = function () {
      this.ajaxPromise({
        url: '/utils/js-app-compilation',
        type: 'POST',
        data: {
          jsContent: this.select('buildOutputSelector').val()
        }
      })
        .then(function (data) {

        });
    };

    // Component initialization
    this.after('initialize', function() {

      var $jsFilesDataHolder = this.select('jsFilesDataSelector');
      this.jsFilesToCompile = $jsFilesDataHolder.data('files');
      //myDebug && logger.debug(module.id, 'JS files to compile:', this.jsFilesToCompile);

      this.select('startBuildBtSelector').removeClass('hidden').show();

      this.select('buildMessagesSelector').val(this.jsFilesToCompile.length+' JS entry points modules defined:\n * ' + this.jsFilesToCompile.join('\n * '));

      this.on(this.attr.startBuildBtSelector, 'click', this.startCompilation);
      this.on(this.attr.saveBtSelector, 'click', this.saveCompilationResult);
    });
  }

});