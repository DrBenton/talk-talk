<?php

use QueryPath\DOMQuery;

$hooks['html.site_container'] = function (DOMQuery $html) {
    // Just the standard TWitter Bootstrap ("TWB") container class...
    $html->find('#site-container')
        ->addClass('container');
};

$hooks['html.main_content_container'] = function (DOMQuery $html) {
    // A simple TWB "panel" class...
    $mainContentContainer = $html->find('#main-content-container');
    $mainContentContainer->addClass('panel panel-default');
    $mainContentContainer->find('#main-content')->addClass('panel-body');
};

$hooks['html.header'] = function (DOMQuery $html) {
    // We're going to work on the <header>...
    $header = $html->find('header');
    // General header TWB styles
    $header->addClass('page-header');

    // Nav stuff
    $nav = $header->find('nav');
    $nav->find('ul')
        ->addClass('nav nav-pills');
};

$hooks['html.form'] = function (DOMQuery $html) {
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

$hooks['html.alerts_display'] = function (DOMQuery $html) {
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

$hooks['html.user_profile_display'] = function (DOMQuery $html) {
    $html->find('.logged-user-display')
        ->prepend('<span class="glyphicon glyphicon-user"></span>');
};

$hooks['html.forums_display'] = function (DOMQuery $html) {
    $allForumsDisplayContainer = $html->find('.forums-display-container');
    $allForumsDisplayContainer->addClass('clearfix');
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

$hooks['html.forum_display'] = function (DOMQuery $html) {
    // We move the Forum title in a TWB "jumbotron"...
    $forumTitle = $html->find('.forum-title');
    $forumTitle->before('<div class="jumbotron forum-heading"></div>');
    $jumbotron = $html->find('.jumbotron.forum-heading');
    $jumbotron->append($forumTitle);
    $forumTitle->remove();
    // ...and move the forum description in this jumbotron too
    $forumDesc = $html->find('.forum-desc');
    $jumbotron->append($forumDesc);
    $forumDesc->remove();
};

$hooks['html.topics_display'] = function (DOMQuery $html) {
    $topicsDisplaysContainers = $html->find('.topics-display-container');
    $topicsDisplaysContainers->addClass('clearfix');
};

$hooks['html.topic_display'] = function (DOMQuery $html) {
    $topicsDisplays = $html->find('.topic-display');
    $topicsDisplays->addClass('panel panel-default clearfix');
    $topicsDisplays->find('.topic-name')->wrap('<div class="panel-heading"></div>')->addClass('panel-title');
    $topicsDisplays->find('.topic-info')->addClass('panel-body');
};

$hooks['html.posts_display'] = function (DOMQuery $html) {
    $postsDisplaysContainers = $html->find('.posts-display-container');
    $postsDisplaysContainers->addClass('clearfix');
};

$hooks['html.post_display'] = function (DOMQuery $html) {

    $postsDisplaysContainers = $html->find('.post-display-container');

    // "post-author" management: we have to extract it from each Post display
    $postsDisplaysContainers->each(
        function ($i, \DOMElement $postDisplayContainerElement) {
            $postDisplayContainerHtml = new DOMQuery($postDisplayContainerElement);
            $authorDisplay = $postDisplayContainerHtml->find('.post-author');
            // If this is a Post of the Topic'author, we add this same CSS class to the author display
            if ($postDisplayContainerHtml->hasClass('topic-author')) {
                $authorDisplay->addClass('topic-author');
            }
            // We put the "post-author"  *after* the container, in a "clearfix" container,
            // and remove it from its container
            $authorDisplayNewParent = new DOMQuery('<div class="author-display-container clearfix"></div>');
            $authorDisplay->appendTo($authorDisplayNewParent);
            $postDisplayContainerHtml->after($authorDisplayNewParent);
            $authorDisplay->remove();
        }
    );

    // Standard TWB "panel" styling
    $postsDisplays = $postsDisplaysContainers->find('.post-display');
    $postsDisplays->addClass('panel panel-default');
    $postsDisplays->find('.post-title')->wrap('<div class="panel-heading"></div>')->addClass('panel-title');
    $postsDisplays->find('.post-content')->addClass('panel-body');
    $postsDisplays->find('.post-info')->addClass('panel-footer');

};

$hooks['html.pagination'] = function (DOMQuery $html) {
    $pagination = $html->find('.pagination');
    $pagination->addClass('pull-right');
    $pagination->after('<div class="clearfix"></div>');
};

$hooks['html.authentication_required_msg'] = function (DOMQuery $html) {
    $msg = $html->find('.authentication-required-msg');
    $msg->addClass('help-block');
};

