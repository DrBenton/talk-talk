#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

@general:
  id: phpbb
  actionsUrlsPrefix: /phpbb
  enabledOnlyForUrl:
    - ^/phpbb
    - ^/sign-in$
    - ^/utils/get-ajax-alerts-display$
  firewalls:
   - auth.middleware.is-authenticated

@actions:
  -
    # GET /phpbb/import/start => actions/import-start-page.php
    url: /import/start
    target: import/start-page
    name: phpbb/import/start
  -
    # POST /phpbb/import/start => actions/import-start-page-target.php
    url: /import/start
    method: POST
    target: import/start-page-target
    name: phpbb/import/start/target
  -
    # GET /phpbb/import/importing => actions/import/importing-page.php
    # (requires "phpbb-settings" in Session, previously initialized in "/import/start")
    url: /import/importing
    target: import/importing-page
    name: phpbb/import/importing
    firewalls:
      - phpbb-connection-settings-in-session-required
  -
    # GET /phpbb/import/importing/import-XXX/metadata => actions/import/importing/XXX-import-metadata.php
    # (requires "phpbb-settings" in Session, previously initialized in "/import/start")
    url: /import/importing/import-{itemType}/metadata
    target: import/importing/{itemType}-import-metadata
    firewalls:
      - phpbb-connection-settings-in-session-required
    requirements:
     itemType: (users|forums|topics|posts)
  -
    # POST /phpbb/import/importing/clear-previous-imports => actions/import/importing/clear-previous-imports.php
    # (requires "phpbb-settings" in Session, previously initialized in "/import/start")
    url: /import/importing/clear-previous-imports
    method: POST
    target: import/importing/clear-previous-imports
    firewalls:
      - phpbb-connection-settings-in-session-required
  -
    # POST /phpbb/import/importing/import-XXX/batch/N => actions/import/importing/XXX-import-batch.php
    # (requires "phpbb-settings" in Session, previously initialized in "/import/start")
    url: /import/importing/import-{itemType}/batch/{batchIndex}
    method: POST
    target: import/importing/{itemType}-import-batch
    firewalls:
      - phpbb-connection-settings-in-session-required
    requirements:
     itemType: (users|forums|topics|posts)
     batchIndex: \d+ #{batchIndex} must be an integer
  -
    # POST /phpbb/import/importing/finish-import => actions/import/importing/finish-import.php
    # (requires "phpbb-settings" in Session, previously initialized in "/import/start")
    url: /import/importing/finish-import
    method: POST
    target: import/importing/finish-import
    firewalls:
      - phpbb-connection-settings-in-session-required

@classes:
  -
    prefix: TalkTalk\CorePlugins\PhpBb\
    path: %plugin-path%/classes/TalkTalk/CorePlugins/PhpBb

@services:
#  - before-middlewares
  - phpbb-db
#  - phpbb-data
#  - phpbb-import-users
#  - phpbb-import-forums
#  - phpbb-import-topics
#  - phpbb-import-posts

@firewalls:
  - phpbb-connection-settings-in-session-required

#@events:
#  - before.set-phpbb-db-settings-from-session

#@hooks:
#  - auth.user.check-signin-credentials

@translations:
  - en