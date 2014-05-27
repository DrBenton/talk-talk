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
    $nav->find('.sign-up a')
        ->prepend('<span class="glyphicon glyphicon-asterisk"></span> ');
    $nav->find('.sign-in a')
        ->prepend('<span class="glyphicon glyphicon-log-in"></span> ');
    $nav->find('.sign-out a')
        ->prepend('<span class="glyphicon glyphicon-log-out"></span> ');
};

$hooks['html.breadcrumb'] = function (DOMQuery $html) {
    $breadcrumb = $html->find('.breadcrumb');
    $breadcrumb->find('li.home a')
        ->prepend('<span class="glyphicon glyphicon-home"></span>');
};

$hooks['html.page.home'] = function (DOMQuery $html) use ($app) {
    $homeHeading = $html->find('#home-heading');
    $homeHeadingTitle = $homeHeading->find('h1');
    $homeHeading->addClass('jumbotron');

    // Site settings
    $siteTitleFontSize = $app['settings']->get('app.site-title.font-size');
    if (null !== $siteTitleFontSize) {
        $homeHeadingTitle->attr('style', $homeHeadingTitle->attr('style') . "font-size: ${siteTitleFontSize};");
    }
    $siteTitleColor = $app['settings']->get('app.site-title.color');
    if (null !== $siteTitleColor) {
        $homeHeadingTitle->attr('style', $homeHeadingTitle->attr('style') . "color: ${siteTitleColor};");
    }
    $siteTitleShadow = $app['settings']->get('app.site-title.shadow');
    if (null !== $siteTitleShadow) {
        $homeHeadingTitle->attr('style', $homeHeadingTitle->attr('style') . "text-shadow: ${siteTitleShadow};");
    }
    $siteImageUrl = $app['settings']->get('app.site-image.url');
    if (null !== $siteImageUrl) {
        $homeHeading->addClass('with-image');
        $homeHeading->attr('style', "background-image: url(\"$siteImageUrl\");");
        $siteImageBgColor = $app['settings']->get('app.site-image.bgcolor');
        if (null !== $siteImageBgColor) {
            $homeHeading->attr('style', $homeHeading->attr('style') . "background-color: ${siteImageBgColor};");
        }
        $siteImageSize = $app['settings']->get('app.site-image.size');
        if (null !== $siteImageSize) {
            list($width, $height) = explode('x', $siteImageSize);
            $homeHeading->attr('style', $homeHeading->attr('style') . "height: ${height}px;");
        }
    }
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

$hooks['html.signup_form'] = function (DOMQuery $html) {
    $html->find('.already-have-account')
        ->addClass('help-block');
};

$hooks['html.component.alert'] = function (DOMQuery $html) {
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

$hooks['html.user_profile'] = function (DOMQuery $html) {
    $html->find('.logged-user-display')
        ->addClass('pull-right')
        ->prepend('<span class="glyphicon glyphicon-user"></span>')
        ->after('<div class="clearfix"></div>');
};

$hooks['html.forums_list'] = function (DOMQuery $html) {
    $allForumsDisplayContainer = $html->find('.forums-list-container');
    $allForumsDisplayContainer->addClass('clearfix');
    // "root" forums styling
    $rootForums = $allForumsDisplayContainer->find('> .forum-container');
    $rootForums->addClass('col-sm-6');
    $rootForums->find('.forum')->addClass('panel panel-default');
    // We want these forums to be displayed 2 on a row: let's add a "clearfix" every 2 forums
    $rootForums->even()->after('<div class="clearfix"></div>');
    // Forums title: we wrap them in a <div> with the TWB "panel-heading" class, and add a "panel-title" class
    $rootForumsTitles = $rootForums->find('.forum-title');
    $rootForumsTitles
        ->wrap('<div class="panel-heading"></div>')
        ->addClass('panel-title');
    // Forums content: we add a TWB "panel-body" class
    $rootForumsContent = $rootForums->find('.content');
    $rootForumsContent->addClass('panel-body');
    // List desc
    $html->find('.list-desc')->addClass('lead');
};

$hooks['html.page.forum'] = function (DOMQuery $html) {
    $parentForum = $html->find('.parent-forum');
    // We move the Forum title in a TWB "jumbotron"...
    $forumTitle = $parentForum->find('.forum-title');
    $forumTitle->before('<div class="jumbotron forum-heading"></div>');
    $jumbotron = $parentForum->find('.jumbotron.forum-heading');
    $jumbotron->append($forumTitle);
    $forumTitle->remove();
    // ...and move the forum description in this jumbotron too
    $forumDesc = $parentForum->find('.forum-desc');
    $jumbotron->append($forumDesc);
    $forumDesc->remove();
    // Forum bg image management
    $forumId = (int) $parentForum->attr('data-forum-id');
    $forum = \TalkTalk\Model\Forum::find($forumId);
    $forumBgImg = isset($forum->metadata) && isset($forum->metadata['bgImg'])
        ? $forum->metadata['bgImg']
        : null ;
    if (null !== $forumBgImg) {
        $jumbotron
            ->addClass('with-image')
            ->attr('style', "background-image: url(\"$forumBgImg\");");
    }
};

$hooks['html.topics_list'] = function (DOMQuery $html) {
    $topicsListContainers = $html->find('.topics-list-container');
    $topicsListContainers->addClass('clearfix');
    // Topics styling
    $topics = $html->find('.topic');
    $topics->addClass('panel panel-default clearfix');
    $topics->find('.topic-name')
        ->wrap('<div class="panel-heading"></div>')
        ->addClass('panel-title');
    $topics->find('.topic-info')->addClass('panel-body');
    // List desc
    $html->find('.list-desc')->addClass('lead');
};

$hooks['html.topic'] = function (DOMQuery $html) {
};

$hooks['html.page.topic'] = function (DOMQuery $html) {
    // We move the Topic title in a TWB "jumbotron"...
    $topicTitle = $html->find('.topic-title');
    $topicTitle->before('<div class="jumbotron topic-heading"></div>');
    $jumbotron = $html->find('.jumbotron.topic-heading');
    $jumbotron->append($topicTitle);
    $topicTitle->remove();
    // ...and move the topic description in this jumbotron too
    $topicDesc = $html->find('.topic-desc');
    $jumbotron->append($topicDesc);
    $topicDesc->remove();
};

$hooks['html.posts_list'] = function (DOMQuery $html) {
    $postsListContainers = $html->find('.posts-list-container');
    $postsListContainers->addClass('clearfix');
};

$hooks['html.post'] = function (DOMQuery $html) {

    $postsContainers = $html->find('.post-container');

    // "post-author" management: we have to extract it from each Post display
    $postsContainers->each(
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
    $postsList = $postsContainers->find('.post');
    $postsList->addClass('panel panel-default');
    $postsList->find('.post-title')
        ->wrap('<div class="panel-heading"></div>')
        ->addClass('panel-title');
    $postsList->find('.post-content')->addClass('panel-body');
    $postsList->find('.post-info')->addClass('panel-footer');

};

$hooks['html.component.pagination'] = function (DOMQuery $html) {
    $pagination = $html->find('.pagination');
    $pagination->addClass('pull-right');
    $pagination->after('<div class="clearfix"></div>');
};

$hooks['html.authentication_required_msg'] = function (DOMQuery $html) {
    $msg = $html->find('.authentication-required-msg');
    $msg->addClass('help-block');
};

$hooks['html.create_new_topic_link'] = function (DOMQuery $html) {
    $createNewTopicLink = $html->find('.create-new-topic-link');
    $createNewTopicLink
        ->prepend('<span class="glyphicon glyphicon-comment"></span>')
        ->addClass('btn btn-primary')
        ->attr('role', 'button');
};

$hooks['html.create_new_post_link'] = function (DOMQuery $html) {
    $createNewPostLink = $html->find('.create-new-post-link');
    $createNewPostLink
        ->prepend('<span class="glyphicon glyphicon-comment"></span>')
        ->addClass('btn btn-primary')
        ->attr('role', 'button');
};

$hooks['html.page.new_post_form'] = function (DOMQuery $html) {
    // List desc
    $html->find('.list-desc')->addClass('lead');
};

$hooks['html.component.progress'] = function (DOMQuery $html) use ($app, $myComponentsUrl) {
    // Let's replace our <progress> markups with a TWB "progress bar" CSS component
    $html->find('.progress-component')
        ->replaceWith('
        <div class="progress-component progress">
          <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
            0%
          </div>
        </div>
        ');
    // Now that we have replaced all the <progress> with our custom TWB components, let's add
    // a JS behaviour on them!
    $twbProgressComponents = $html->find('.progress-component');
    $app['html-components.add_component'](
        $twbProgressComponents,
        $myComponentsUrl . 'ui/twb-progress'
    );
};

