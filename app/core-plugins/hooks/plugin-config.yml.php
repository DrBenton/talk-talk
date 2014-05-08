#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

@general:
  id: hooks

@classes:
  -
    prefix: TalkTalk\CorePlugins\Hooks\
    paths: ${pluginPath}/classes/TalkTalk/CorePlugins/Hooks

@services:
  - hooks
  - html-hooks

@events:
  - before.init-hooks-manager
  - after.trigger-html-hooks

@twig-extensions:
  - func.enable-html-hooks
  - func.get-plugins-stylesheets
  - func.get-plugins-javascripts
