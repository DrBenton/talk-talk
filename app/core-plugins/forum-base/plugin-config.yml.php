#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

@general:
  id: forum-base

@actions:
  -
    # GET / => actions/home.php
    url: /
    target: home
    # we override the default homepage!
    name: core/home
    priority: 10
  -
    # GET /forum/ID => actions/forum-display.php
    url: /forum/{forum}
    target: forum-display
    name: forum-base/forum
    converters:
      forum: forum-id
  -
    # GET /topic/ID => actions/topic-display.php
    url: /topic/{topic}
    target: topic-display
    name: forum-base/topic
    converters:
      topic: topic-id
  -
    # GET /topic/ID/posts/new => actions/new-post-form.php
    # (authentication required)
    url: /topic/{topic}/posts/new
    target: new-post-form
    name: forum-base/new-post-form
    converters:
      topic: topic-id
    before:
      - auth.middleware.is-authenticated
  -
    # POST /topic/ID/posts => actions/new-post-target.php
    # (authentication required)
    url: /topic/{topic}/posts
    target: new-post-target
    name: forum-base/new-post-form/target
    method: POST
    converters:
      topic: topic-id
    before:
      - auth.middleware.is-authenticated

@actions-variables-converters:
  - forum-id
  - topic-id

@classes:
  -
    prefix: TalkTalk\Model\
    paths: %pluginPath%/classes/TalkTalk/Model

@services:
  - forums-data
  - forum-markup-manager

@locales:
  - en

@twig-extensions:
  - filter.bbcode-to-html

@hooks:
  - html.new_post_form
