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
        ->addClass('col-sm-2 control-label');
    // Inputs containers with
    $forms->find('.input-container')
        ->addClass('col-sm-10');
    // Inputs TB dedicated attributes
    $forms->find('.input')
        ->addClass('form-control');
    // Submit button specific stuff
    $forms->find('.form-group.submit .input-container')
        ->addClass('col-sm-offset-2 col-sm-10');
    $forms->find('.submit-button')
        ->addClass('btn btn-default');
};

$hooks['html.notifications_display'] = function (\QueryPath\DOMQuery $html) {
    //TODO: handle it in a more generic way
    $html->find('.notifications-to-display .notification-success')
        ->addClass('alert alert-success');
    $html->find('.notifications-to-display .notification-error')
        ->addClass('alert alert-danger');
};

$hooks['html.user_profile_display'] = function (\QueryPath\DOMQuery $html) {
    $html->find('.logged-user-display')
        ->prepend('<span class="glyphicon glyphicon-user"></span>');
};

