#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

@general:
  id: ajax-post-writing
  disabled: false

@actions:
  -
    # GET /ajax-post-writing/get-widget => actions/get-ajax-post-writing-widget.php
    url: /ajax-post-writing/forum/{forum}/new-topic/widget
    target: get-new-topic-widget
    params-converters:
      forum: forum-id

@translations:
  - en

@hooks:
  - html.create_new_post_link
  - html.create_new_topic_link
