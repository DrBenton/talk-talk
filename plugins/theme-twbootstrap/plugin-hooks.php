<?php

$hooks['html.site_container'] = function (\QueryPath\DOMQuery $html) {
    // Just the standard TB container class...
    $html->find('#site-container')
        ->addClass('container');
};

$hooks['html.main_content_container'] = function (\QueryPath\DOMQuery $html) {
    // A simple TB container class...
    $html->find('#main-content-container')
        ->addClass('well');
};

$hooks['html.header'] = function (\QueryPath\DOMQuery $html) {
    // We're going to work on the <header>...
    $header = $html->find('header');
    // General header TB styles
    $header->addClass('page-header');

    // Nav stuff
    $nav = $header->find('nav');
    $nav->find('ul')
        ->addClass('nav nav-pills');
};

$hooks['html.form'] = function (\QueryPath\DOMQuery $html) {
    // We're going to work on the #signup-form...
    $forms = $html->find('form');
    // General form TB styles
    $forms->addClass('form-horizontal');
    // Labels with + TB dedicated attributes
    $forms->find('label')
        ->addClass('col-sm-4 control-label');
    // Inputs containers with
    $forms->find('.input-container')
        ->addClass('col-sm-8');
    // Inputs TB dedicated attributes
    $forms->find('.input')
        ->addClass('form-control');
    // Submit button specific stuff
    $forms->find('.form-group.submit .input-container')
        ->addClass('col-sm-offset-4 col-sm-8');
    $forms->find('.submit-button')
        ->addClass('btn btn-default');
    // Form errors
    $forms->find('.form-group.form-error')
        ->addClass('has-error');
};

$hooks['html.alerts_display'] = function (\QueryPath\DOMQuery $html) {
    $transforms = array(
        'info' => 'info',
        'success' => 'success',
        'error' => 'danger',
    );
    foreach($transforms as $alertType => $TWBootstrapNotificationType) {
        $html->find(".alerts-to-display .alert-$alertType")
            ->addClass("alert alert-$TWBootstrapNotificationType");
    }
};

$hooks['html.user_profile_display'] = function (\QueryPath\DOMQuery $html) {
    $html->find('.logged-user-display')
        ->prepend('<span class="glyphicon glyphicon-user"></span>');
};

$hooks['html.forums_display'] = function (\QueryPath\DOMQuery $html) {
    $rootForums = $html->find('.forum-container.level-0');
    $rootForums->addClass('col-sm-5');
    $rootForums->find('.forum')->addClass('panel panel-default');
    $rootForums->even()->after('<div class="clearfix"></div>');
    $rootForumsTitles = $rootForums->find('.title');
    $rootForumsTitles->wrap('<div class="panel-heading"></div>')->addClass('panel-title');
    $rootForumsContent = $rootForums->find('.content');
    $rootForumsContent->addClass('panel-body');
};

