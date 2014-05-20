<?php

$hooks['html.site_container'] = function (\QueryPath\DOMQuery $html) {
    // Just the standard TWitter Bootstrap ("TWB") container class...
    $html->find('#site-container')
        ->addClass('container');
};

$hooks['html.main_content_container'] = function (\QueryPath\DOMQuery $html) {
    // A simple TWB "panel" class...
    $mainContentContainer = $html->find('#main-content-container');
    $mainContentContainer->addClass('panel panel-default');
    $mainContentContainer->find('#main-content')->addClass('panel-body');
};

$hooks['html.header'] = function (\QueryPath\DOMQuery $html) {
    // We're going to work on the <header>...
    $header = $html->find('header');
    // General header TWB styles
    $header->addClass('page-header');

    // Nav stuff
    $nav = $header->find('nav');
    $nav->find('ul')
        ->addClass('nav nav-pills');
};

$hooks['html.form'] = function (\QueryPath\DOMQuery $html) {
    // We're going to work on <form>s...
    $forms = $html->find('form');
    // General form TWB styles
    $forms->addClass('form-horizontal');
    // Labels with + TWB dedicated attributes
    $forms->find('label')
        ->addClass('col-sm-4 control-label');
    // Inputs containers with
    $forms->find('.input-container')
        ->addClass('col-sm-8');
    // Inputs TWB dedicated attributes
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

$hooks['html.all_forums_display'] = function (\QueryPath\DOMQuery $html) {
    $allForumsDisplayContainer = $html->find('.all-forums-display-container');
    // "root" forums styling
    $rootForums = $allForumsDisplayContainer->find('.forum-container.level-0');
    $rootForums->addClass('col-sm-5');
    $rootForums->find('.forum')->addClass('panel panel-default');
    // We want these forums to be displayed 2 on a row: let's add a "clearfix" every 2 forums
    $rootForums->even()->after('<div class="clearfix"></div>');
    // Forums title: we wrap them in a <div> with the TWB "panel-heading" class, and add a "panel-title" class
    $rootForumsTitles = $rootForums->find('.title');
    $rootForumsTitles->wrap('<div class="panel-heading"></div>')->addClass('panel-title');
    // Forums content: we add a TWB "panel-body" class
    $rootForumsContent = $rootForums->find('.content');
    $rootForumsContent->addClass('panel-body');
};

$hooks['html.topic_display'] = function (\QueryPath\DOMQuery $html) {
    $topicsDisplays = $html->find('.topic-display');
    $topicsDisplays->addClass('panel panel-default');
    $topicsDisplays->find('.topic-name')->wrap('<div class="panel-heading"></div>')->addClass('panel-title');
    $topicsDisplays->find('.topic-info')->addClass('panel-body');
};

$hooks['html.pagination'] = function (\QueryPath\DOMQuery $html) {
    $pagination = $html->find('.pagination');
    $pagination->addClass('pull-right');
    $pagination->after('<div class="clearfix"></div>');
};

