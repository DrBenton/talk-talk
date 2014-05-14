#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

@general:
  id: forum-base

@actions:
  -
    # GET / => actions/forums-display.php
    url: /
    target: forums-display
    # we override the default homepage!
    name: core/home
    priority: 10

@classes:
  -
    prefix: TalkTalk\Model\
    paths: ${pluginPath}/classes/TalkTalk/Model

@services:
  - forums-data