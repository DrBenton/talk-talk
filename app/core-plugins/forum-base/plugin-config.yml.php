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
    url: /forum/{forumId}
    target: forum-display
    name: forum-base/forum
    requirements:
      forumId: \d+ #{forumId} must be an integer
  -
    # GET /topic/ID => actions/topic-display.php
    url: /topic/{topicId}
    target: topic-display
    name: forum-base/topic
    requirements:
      topicId: \d+ #{topicId} must be an integer

@classes:
  -
    prefix: TalkTalk\Model\
    paths: %pluginPath%/classes/TalkTalk/Model

@services:
  - forums-data
  - forum-markup-manager

@locales:
  - en

@hooks:
  - post.handle_content
