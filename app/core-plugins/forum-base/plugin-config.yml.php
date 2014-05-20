#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

@general:
  id: forum-base

@actions:
  -
    # GET / => actions/all-forums-display.php
    url: /
    target: all-forums-display
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

@classes:
  -
    prefix: TalkTalk\Model\
    paths: ${pluginPath}/classes/TalkTalk/Model

@services:
  - forums-data

@locales:
  - en
