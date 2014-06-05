#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

@general:
  id: utils
  actionsUrlsPrefix: /utils

@actions:
  -
    # GET /utils/phpinfo => actions/phpinfo.php (only when $app->vars['debug'] === true)
    url: /phpinfo
    target: phpinfo
    onlyForDebug: true
  -
    # POST /utils/get-ajax-alerts-display => actions/get-alerts-display.php
    url: /get-ajax-alerts-display
    target: ajax-alerts-display
    method: POST

@classes:
  -
    prefix: TalkTalk\CorePlugins\Utils\
    path: %plugin-path%/classes/TalkTalk/CorePlugins/Utils

@services:
  - utils-html
#  - perfs

@events:
#  - after.perfs-info-headers

@hooks:
  -
    name: html.header
    # this hook will be triggered *after* the other plugins "html.header" hooks:
    priority: -10
  - html.site_container
  - html.alerts_container

@translations:
  - en

#@twig-extensions:
#  - func.current-path
#  - filter.from-now