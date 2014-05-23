#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

core-plugins:
  forum-base:
    breadcrumb:
      forum: Forum "%name%"
      topic: Topic "%name%"
      new_post: New Post
    home:
      title: Welcome to <span class="site-title">%site-title%</span>
    topics-list:
      no-topic: There is no Topic here for the moment.
      create-new-topic: Create a new Topic
    posts-list:
      no-post: There is no Post here for the moment.
      create-new-post: Answer to this Topic
    new-post:
      form:
        title: Title
        content: Message content
        submit: Post this!
      topic-posts-summary:
        intro: >
          Topic summary:
        first-post: First Post
        last-posts: Last Posts
        single-post-only: There is no other Post in this Topic. Be the first to answer! :-)
      alerts:
        new-post-successful: Your Post has been successfully created!
    acl:
      must_be_authenticated:
        for-new-topic: >
          You must be authenticated to create a new Topic.<br>
          <a href="%sign-in-url%" class="sign-in ajax-link">Sign in</a> or
          <a href="%sign-up-url%" class="sign-up ajax-link">sign up</a> to create a new Topic.
        for-new-post: >
          You must be authenticated to create a new Post.<br>
          <a href="%sign-in-url%" class="sign-in ajax-link">Sign in</a> or
          <a href="%sign-up-url%" class="sign-up ajax-link">sign up</a> to answer to this Topic.
