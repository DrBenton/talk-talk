#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

general:
  id: import-phpbb
  actionsUrlsPrefix: /import/phpbb

actions:
  -
    url: /start
    target: actions/import-start-page.php
    name: data-import/phpbb/start

classes:
  -
    prefix: TalkTalk\CorePlugins\DataImport\PhpBb\
    paths: ${pluginPath}/classes/TalkTalk/CorePlugins/DataImport/PhpBb

services:

locales:
  - en

twig-extensions:
