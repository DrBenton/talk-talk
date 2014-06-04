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
#  - session
#  - db
#  - crypto
#  - config
#  - settings
#  - url-generator
#  - translator
#  - validator
#  - uuid
#  - csrf
#  - html-escape

#@events:
#  - error.app-error
#  - before.check-csrf

#@locales:
#  -
#    file: validation-en
#    language: en

#@hooks:
#  - define_javascript_app_config

@assets:
  stylesheets:
    - %plugin-url%/assets/css/404.css
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
#  - func.get-app-javascript-config
