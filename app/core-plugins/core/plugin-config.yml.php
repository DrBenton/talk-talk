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
    prefix: TalkTalk\CorePlugins\Core\
    paths: %pluginPath%/classes/TalkTalk/CorePlugins/Core
  -
    prefix: TalkTalk\Model\
    paths: %pluginPath%/classes/TalkTalk/Model

@services:
  - session
  - db
  - crypto
  - config
  - settings
  - url-generator
  - twig
  - translator
  - validator
  - uuid
  - csrf
  - html-escape

@events:
  - error.app-error
  - before.check-csrf

@locales:
  -
    file: validation-en
    language: en

@hooks:
  - define_javascript_app_config

@assets:
  stylesheets:
  javascripts:
    - %vendorsUrl%/requirejs/require.js
    - %pluginUrl%/assets/js/requirejs-config.js
    - %pluginUrl%/assets/js/main.js
    -
      url: %vendorsUrl%/html5shiv/dist/html5shiv.min.js
      head: true
      ieCondition: lt IE 9

@twig-extensions:
  - func.get-flashes
  - func.get-app-javascript-config
