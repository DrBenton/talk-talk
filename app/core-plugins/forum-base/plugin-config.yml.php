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
    params-converters:
      forum: forum-id
  -
    # GET /forum/ID/topics/new => actions/new-topic-form.php
    # (authentication required)
    url: /forum/{forum}/topics/new
    target: new-topic-form
    name: forum-base/new-topic-form
    params-converters:
      forum: forum-id
    firewalls:
      - authentication-required
  -
    # POST /forum/ID/topics => actions/new-topic-target.php
    # (authentication required)
    url: /topic/{forum}/topics
    target: new-topic-target
    name: forum-base/new-topic-form/target
    method: POST
    params-converters:
      forum: forum-id
    firewalls:
      - authentication-required
  -
    # GET /topic/ID => actions/topic-display.php
    url: /topic/{topic}
    target: topic-display
    params-converters:
      topic: topic-id
    name: forum-base/topic
  -
    # GET /topic/ID/posts/new => actions/new-post-form.php
    # (authentication required)
    url: /topic/{topic}/posts/new
    target: new-post-form
    name: forum-base/new-post-form
    params-converters:
      topic: topic-id
    firewalls:
      - authentication-required
  -
    # POST /topic/ID/posts => actions/new-post-target.php
    # (authentication required)
    url: /topic/{topic}/posts
    target: new-post-target
    name: forum-base/new-post-form/target
    method: POST
    params-converters:
      topic: topic-id
    firewalls:
      - authentication-required
  -
    # GET /smileys => actions/get-smileys.php
    url: /smileys
    target: get-smileys
    name: forum-base/get-smileys

@actions-params-converters:
  - forum-id
  - topic-id

@classes:
  -
    prefix: TalkTalk\Model\
    path: %plugin-path%/classes/TalkTalk/Model
  -
    prefix: TalkTalk\Decoda\
    path: %plugin-path%/classes/TalkTalk/Decoda
  -
    prefix: TalkTalk\CorePlugin\ForumBase\
    path: %plugin-path%/classes/TalkTalk/CorePlugin/ForumBase

@services:
  - forums-data
  - forum-markup-manager

@translations:
  - en

@templates-extensions:
  - forum-utils

@hooks:
  - html.component.post_content_editor
