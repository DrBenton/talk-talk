#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

@general:
  id: phpbb
  actionsUrlsPrefix: /phpbb
  enabledOnlyForUrl:
    - ^/phpbb
    - ^/sign-in
    - ^/utils/get-alerts-display
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
    # (requires "phpbb-settings" in Session, previously initialized in "/import/start")
    url: /import/importing
    target: import-importing-page
    name: phpbb/import/importing
    before: phpbb.middleware.require-phpbb-connection-settings
  -
    # POST /phpbb/import/importing/import-XXX/metadata => actions/data-import/XXX-import-metadata.php
    # (requires "phpbb-settings" in Session, previously initialized in "/import/start")
    url: /import/importing/import-{itemType}/metadata
    method: POST
    target: data-import/{itemType}-import-metadata
    before: phpbb.middleware.require-phpbb-connection-settings
    requirements:
     itemType: (users|forums|topics|posts)
  -
    # POST /phpbb/import/importing/import-XXX/batch/N => actions/data-import/XXX-import-batch.php
    # (requires "phpbb-settings" in Session, previously initialized in "/import/start")
    url: /import/importing/import-{itemType}/batch/{batchIndex}
    method: POST
    target: data-import/{itemType}-import-batch
    before: phpbb.middleware.require-phpbb-connection-settings
    requirements:
     itemType: (users|forums|topics|posts)
     batchIndex: \d+ #{batchIndex} must be an integer
  -
    # POST /phpbb/import/importing/finish-import => actions/data-import/finish-import.php
    # (requires "phpbb-settings" in Session, previously initialized in "/import/start")
    url: /import/importing/finish-import
    method: POST
    target: data-import/finish-import
    before: phpbb.middleware.require-phpbb-connection-settings

@classes:
  -
    prefix: TalkTalk\CorePlugins\PhpBb\
    paths: ${pluginPath}/classes/TalkTalk/CorePlugins/PhpBb

@services:
  - before-middlewares
  - phpbb-db
  - phpbb-import-users
  - phpbb-import-forums
  - phpbb-import-topics
  - phpbb-import-posts

@events:
  - before.set-phpbb-db-settings-from-session

@hooks:
  - auth.user.check-signin-credentials

@locales:
  - en

@twig-extensions:
