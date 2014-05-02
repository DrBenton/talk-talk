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
    prefix: TalkTalk\Core\
    paths: ${pluginPath}/classes/TalkTalk/Core

services:
  - logger
  - session
  - url-generator
  - twig

twig-extensions:
