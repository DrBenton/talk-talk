#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

@general:
  id: utils

@classes:
  -
    prefix: TalkTalk\CorePlugins\Utils\
    paths: ${pluginPath}/classes/TalkTalk/CorePlugins/Utils

@services:
  - utils-html

@hooks:
  -
    name: html.header
    # this hook will be triggered *after* the other plugins "html.header" hooks:
    priority: -10
  - html.site_container

@twig-extensions:
