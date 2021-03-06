#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

@language: en

core-plugins:
  forum-base:
    breadcrumb:
      forum: Forum "%title%"
      topic: Topic "%title%"
      new_topic: New Topic
      new_post: New Post
    home:
      title: Welcome to <span class="site-title">%site-title%</span>
    forums-list:
      no-forum: There is no Forum for the moment.
    topics-list:
      no-topic: There is no Topic here for the moment.
      create-new-topic: Create a new Topic
    posts-list:
      no-post: There is no Post here for the moment.
      create-new-post: Answer to this Topic
    new-topic:
      form:
        title: Title
        content: Topic message content
        submit: Post this new Topic!
      alerts:
        new-topic-successful: Your Topic has been successfully created!
    new-post:
      form:
        title: Title
        content: Message content
        submit: Post this!
        title-default-content: Re: %topic-title%
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
