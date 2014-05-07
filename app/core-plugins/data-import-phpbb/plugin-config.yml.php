#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

@general:
  id: import-phpbb
  actionsUrlsPrefix: /import/phpbb
  actionsBefore:
   - auth.middleware.is-authenticated

@actions:
  -
    # GET /import/phpbb/start => actions/import-start-page.php
    url: /start
    target: import-start-page
    name: data-import/phpbb/start
  -
    # POST /import/phpbb/start => actions/import-start-page-target.php
    url: /start
    method: POST
    target: import-start-page-target
    name: data-import/phpbb/start/target
  -
    # GET /import/phpbb/importing => actions/import-importing-page.php
    url: /importing
    target: import-importing-page
    name: data-import/phpbb/importing
    before: data-import-phpbb.middleware.has-phpbb-settings-in-session

@classes:
  -
    prefix: TalkTalk\CorePlugins\DataImport\PhpBb\
    paths: ${pluginPath}/classes/TalkTalk/CorePlugins/DataImport/PhpBb

@services:
  - before-middlewares
  - phpbb-db

@events:
  - before.set-phpbb-db-settings-from-session

@locales:
  - en

@twig-extensions:
