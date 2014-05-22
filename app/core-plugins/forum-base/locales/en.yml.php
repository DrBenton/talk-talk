#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

core-plugins:
  forum-base:
    breadcrumb:
      forum: Forum "%name%"
      topic: Topic "%name%"
    topics-display:
      no-topic: There is not topic here for the moment.
      post-new-topic: Post a new topic
    acl:
      must_be_authenticated:
        for_new_topic: >
          You must be authenticated to post a new topic.<br>
          <a href="%sign-in-url%" class="sign-in ajax-link">Sign in</a> or
          <a href="%sign-up-url%" class="sign-up ajax-link">sign up</a> to post a new Topic.