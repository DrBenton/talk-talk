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
    url: /forum/:forumId
    target: forum-display
    name: forum-base/forum
  -
    # GET /forum/ID/topics/new => actions/new-topic-form.php
    # (authentication required)
    url: /forum/:forumId/topics/new
    target: new-topic-form
    name: forum-base/new-topic-form
    before:
      - auth.middleware.is-authenticated
  -
    # POST /forum/ID/topics => actions/new-topic-target.php
    # (authentication required)
    url: /topic/:forumId/topics
    target: new-topic-target
    name: forum-base/new-topic-form/target
    method: POST
    before:
      - auth.middleware.is-authenticated
  -
    # GET /topic/ID => actions/topic-display.php
    url: /topic/:topicId
    target: topic-display
    name: forum-base/topic
  -
    # GET /topic/ID/posts/new => actions/new-post-form.php
    # (authentication required)
    url: /topic/:topicId/posts/new
    target: new-post-form
    name: forum-base/new-post-form
    before:
      - auth.middleware.is-authenticated
  -
    # POST /topic/ID/posts => actions/new-post-target.php
    # (authentication required)
    url: /topic/:topicId/posts
    target: new-post-target
    name: forum-base/new-post-form/target
    method: POST
    before:
      - auth.middleware.is-authenticated

@classes:
  -
    prefix: TalkTalk\Model\
    path: %plugin-path%/classes/TalkTalk/Model
#  -
#    prefix: TalkTalk\Decoda\
#    path: %plugin-path%/classes/TalkTalk/Decoda

@services:
  - forums-data
#  - forum-markup-manager

@translations:
  - en

#@twig-extensions:
#  - filter.bbcode-to-html

#@hooks:
#  - html.new_topic_form
#  - html.new_post_form