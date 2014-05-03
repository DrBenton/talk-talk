#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

general:

classes:
  -
    prefix: TalkTalk\CorePlugins\Hooks\
    paths: ${pluginPath}/classes/TalkTalk/CorePlugins/Hooks

services:
  - hooks
  - html-hooks

twig-extensions:
  - func.enable-html-hook
  - func.get-plugins-stylesheets
  - func.get-plugins-javascripts
