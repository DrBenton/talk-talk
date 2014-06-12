#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

@general:
  id: ajax-post-writing
  disabled: false

@actions:
  -
    # GET /ajax-post-writing/get-widget => actions/get-new-topic-widget.php
    url: /ajax-post-writing/forum/{forum}/new-topic/widget
    target: get-new-topic-widget
    params-converters:
      forum: forum-id
  -
    # GET /ajax-post-writing/get-widget => actions/get-new-post-widget.php
    url: /ajax-post-writing/topic/{topic}/new-post/widget
    target: get-new-post-widget
    params-converters:
      topic: topic-id

@translations:
  - en

@hooks:
  - html.create_new_post_link
  - html.create_new_topic_link
