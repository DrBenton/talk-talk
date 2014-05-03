#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

general:

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
  - logger
  - session
  - db
  - crypt
  - url-generator
  - twig
  - translator
  - validator
  
assets:
  stylesheets:
  javascripts:
    - ${vendorsUrl}/jquery/dist/jquery.min.js
    - ${vendorsUrl}/requirejs/require.js
    - ${pluginUrl}/assets/js/requirejs-config.js
    - ${pluginUrl}/assets/js/main.js

twig-extensions:
  - func.get-flashes
