define(function (require, exports, module) {

  var $ = require("jquery");
  var _ = require("lodash");
  var logger = require("logger");
  var purl = require("purl");
  var varsRegistry = require("app-modules/core/vars-registry");
  var ajaxData = require("app-modules/ajax-nav/ajax-data");
  var alertsService = require("app-modules/utils/services/alerts-service");

  require("jquery-form");

  var myDebug = !false;

  var logStats = true;
  var $currentAjaxForms;

  // Exports
  exports.findAndHandleAjaxForms = findAndHandleAjaxForms;


  function onFormSubmit(e) {

    e.preventDefault();

    var $form = $(this);

    $form.ajaxSubmit({
      headers: {'X-Requested-With': 'XMLHttpRequest'},
      success: _.bind(onFormSendingSuccess, new Date),
      error: onFormSendingError
    });

    return false;
  }

  function onFormSendingSuccess(response, status, xhr, loadingStartDate) {
    myDebug && console.log('onFormSendingSuccess() ; args=', arguments);
    varsRegistry.$mainContent.html(response);

    if (logStats) {
      var url = purl(xhr.url).attr('path');
      var formSendingDuration = parseFloat((((new Date).getTime() - loadingStartDate) / 1000).toPrecision(3));
      ajaxData.addStat(url, {
        loadingDuration: formSendingDuration
      });
      varsRegistry.$debugInfoContainer.html("This form has been sent and loaded through Ajax.<br>");
      ajaxData.displayStat(url);
    }
  }

  function onFormSendingError(jqXHR, textStatus, err) {
    myDebug && logger.debug(module.id, "Ajax form loading failed!");
    alertsService.addAlert(
      "core-plugins.ajax-navigation.alerts.form-error",
      {},
      "error"
    );
  }

  function unbindPreviousAjaxForms($formsContainer) {
    var ajaxFormsToUnbind = [];
    for (var i = 0, j = $currentAjaxForms.length; i < j; i++) {
      var form = $currentAjaxForms.get(i);
      if ($.contains($formsContainer, form))
        ajaxFormsToUnbind.push(form);
    }

    myDebug && logger.debug(module.id, ajaxFormsToUnbind.length +
      " Ajax forms to unbind among " + $currentAjaxForms.length);

    $currentAjaxForms = $currentAjaxForms.filter(function () {
      if (_.indexOf(ajaxFormsToUnbind, this) > -1) {
        // We have to unbind this previous ajax form and
        // remove it from our $currentAjaxForms jQuery collection
        var $formToUnbind = $(this);
        $formToUnbind.off();
        return false;
      }
      return true;
    });
  }

  function findAndHandleAjaxForms($formsContainer) {
    $formsContainer = $formsContainer || varsRegistry.$document;
    $currentAjaxForms = $formsContainer.find("form.ajax-form");

    myDebug && logger.debug(module.id, $currentAjaxForms.length + " Ajax forms.");

    if ($currentAjaxForms) {
      unbindPreviousAjaxForms($formsContainer);
    }

    $currentAjaxForms.submit(onFormSubmit);
  }

});
