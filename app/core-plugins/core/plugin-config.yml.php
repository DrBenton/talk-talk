#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

@general:
  id: core

@actions:
  -
    # GET / => actions/home.php
    url: /
    target: home
    name: core/home

@classes:
  -
    prefix: TalkTalk\CorePlugin\Core\
    path: %plugin-path%/classes/TalkTalk/CorePlugin/Core
  -
    prefix: TalkTalk\Model\
    path: %plugin-path%/classes/TalkTalk/Model

@pluginsPackers:
  - TalkTalk\CorePlugin\Core\Plugin\PackingBehaviour\TemplatesPacker
  - TalkTalk\CorePlugin\Core\Plugin\PackingBehaviour\TemplatesExtensionsPacker
  - TalkTalk\CorePlugin\Core\Plugin\PackingBehaviour\AppAssetsPacker

@services:
  - view
  - session
  - session-flash
  - translator
  - db
  - crypto
  - uuid
  - validator
#  - settings
#  - csrf
#  - html-escape

#@events:
#  - error.app-error
#  - before.check-csrf

@translations:
  - validation-en

#@hooks:
#  - define_javascript_app_config

@assets:
  javascripts:
    - %vendors-url%/requirejs/require.js
    - %plugin-url%/assets/js/requirejs-config.js
    - %plugin-url%/assets/js/main.js
    -
      url: %vendors-url%/html5shiv/dist/html5shiv.min.js
      head: true
      ieCondition: lt IE 9

@templates-extensions:
  - app
  - app-assets
  - translation
#  - func.get-app-javascript-config
