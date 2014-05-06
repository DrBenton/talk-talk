#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

general:
  id: core

actions:
  -
    url: /
    target: actions/home.php
    name: core/home
  -
    url: /phpinfo
    target: actions/phpinfo.php

classes:
  -
    prefix: TalkTalk\CorePlugins\Core\
    paths: ${pluginPath}/classes/TalkTalk/CorePlugins/Core

services:
  - session
  - db
  - crypt
  - url-generator
  - twig
  - translator
  - validator
  - uuid
  - csrf
  
events:
  - error.app-error
  - before.check-csrf

locales:
  -
    file: validation-en
    language: en

assets:
  stylesheets:
  javascripts:
    - ${vendorsUrl}/requirejs/require.js
    - ${pluginUrl}/assets/js/requirejs-config.js
    - ${pluginUrl}/assets/js/main.js

twig-extensions:
  - func.get-flashes
