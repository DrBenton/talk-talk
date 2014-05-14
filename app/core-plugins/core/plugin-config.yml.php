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
    paths: ${pluginPath}/classes/TalkTalk/CorePlugins/Core

@services:
  - session
  - db
  - crypto
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

@assets:
  stylesheets:
  javascripts:
    - ${vendorsUrl}/requirejs/require.js
    - ${pluginUrl}/assets/js/requirejs-config.js
    - ${pluginUrl}/assets/js/main.js

@twig-extensions:
  - func.get-flashes
