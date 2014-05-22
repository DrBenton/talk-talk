#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

core-plugins:
  forum-base:
    breadcrumb:
      forum: Forum "%name%"
      topic: Topic "%name%"
    all-forums-display:
    forum-display:
    acl:
      must_be_authenticated:
        for_new_topic: >
          You must be authenticated to post a new topic.<br>
          <a href="%sign-in-url%" class="ajax-link">Sign in</a> or
          <a href="%sign-up-url%" class="ajax-link">sign up</a> to post a new Topic.