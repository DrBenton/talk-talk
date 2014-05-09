#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

@general:
  id: phpbb
  actionsUrlsPrefix: /phpbb
  enabledOnlyForUrl:
    - ^/phpbb
    - ^/sign-in
  actionsBefore:
   - auth.middleware.is-authenticated

@actions:
  -
    # GET /phpbb/import/start => actions/import-start-page.php
    url: /import/start
    target: import-start-page
    name: phpbb/import/start
  -
    # POST /phpbb/import/start => actions/import-start-page-target.php
    url: /import/start
    method: POST
    target: import-start-page-target
    name: phpbb/import/start/target
  -
    # GET /phpbb/import/importing => actions/import-importing-page.php
    # (needs "phpbb-settings" in Session, previously initialized in "/import/start")
    url: /import/importing
    target: import-importing-page
    name: phpbb/import/importing
    before: phpbb.middleware.require-phpbb-connection-settings
  -
    # GET /phpbb/import/importing/import-users/X => actions/data-import/users-import.php
    # (needs "phpbb-settings" in Session, previously initialized in "/import/start")
    url: /import/importing/import-users/{batchIndex}
    target: data-import/users-import
    before: phpbb.middleware.require-phpbb-connection-settings
    requirements:
     batchIndex: \d+ #{batchIndex} must be an integer

@classes:
  -
    prefix: TalkTalk\CorePlugins\PhpBb\
    paths: ${pluginPath}/classes/TalkTalk/CorePlugins/PhpBb

@services:
  - before-middlewares
  - phpbb-db
  - phpbb-users-import

@events:
  - before.set-phpbb-db-settings-from-session

@hooks:
  - auth.user.check-signin-credentials

@locales:
  - en

@twig-extensions:
