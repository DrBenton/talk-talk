#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

general:
  id: import-phpbb
  actionsUrlsPrefix: /import/phpbb
  actionsBefore:
   - auth.middleware.is-authenticated

actions:
  -
    url: /start
    target: actions/import-start-page.php
    name: data-import/phpbb/start
  -
    url: /start
    method: POST
    target: actions/import-start-page-target.php
    name: data-import/phpbb/start/target
  -
    url: /importing
    target: actions/import-importing-page.php
    name: data-import/phpbb/importing
    before: data-import-phpbb.middleware.has-phpbb-settings-in-session

classes:
  -
    prefix: TalkTalk\CorePlugins\DataImport\PhpBb\
    paths: ${pluginPath}/classes/TalkTalk/CorePlugins/DataImport/PhpBb

services:
  - before-middlewares

locales:
  - en

twig-extensions:
