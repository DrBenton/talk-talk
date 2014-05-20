#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

@general:
  id: utils
  actionsUrlsPrefix: /utils
@actions:
  -
    # GET /utils/phpinfo => actions/phpinfo.php (only when $app['debug'] === true)
    url: /phpinfo
    target: phpinfo
    onlyForDebug: true
  -
    # POST /utils/get-alerts-display => actions/get-alerts-display.php
    url: /get-alerts-display
    target: get-alerts-display
    method: POST

@classes:
  -
    prefix: TalkTalk\CorePlugins\Utils\
    paths: ${pluginPath}/classes/TalkTalk/CorePlugins/Utils

@services:
  - utils-html
  - perfs

@hooks:
  -
    name: html.header
    # this hook will be triggered *after* the other plugins "html.header" hooks:
    priority: -10
  - html.site_container

@locales:
  - en

@twig-extensions:
  - func.display-app-debug-info
