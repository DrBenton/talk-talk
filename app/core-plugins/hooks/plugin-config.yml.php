#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

@general:
  id: hooks

@classes:
  -
    prefix: TalkTalk\CorePlugin\Hooks\
    path: %plugin-path%/classes/TalkTalk/CorePlugin/Hooks

@pluginsPackers:
  - TalkTalk\CorePlugin\Hooks\Plugin\PackingBehaviour\HooksPacker

@services:
  - hooks
  - html-hooks

@events:
  - after.trigger-html-hooks

@templates-extensions:
  - html-hooks
