#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

@general:
  id: theme/twbootstrap
  # since we are a theme plugin, we want to be triggered *after* all other plugins:
  htmlHooksPriority: -100

@assets:
  stylesheets:
    - "%pluginUrl%/assets/components/bootstrap/dist/css/bootstrap.css"
    - "%pluginUrl%/assets/css/theme-twbootstrap.css"
  javascripts:

@hooks:
  - html.site_container
  - html.main_content_container 
  - html.header
  - html.breadcrumb
  - html.page.home
  - html.form
  - html.signup_form
  - html.alerts_display
  - html.user_profile_display
  - html.page.forum
  - html.forums_display
  - html.forum_display
  - html.page.topic
  - html.topics_display
  - html.topic_display
  - html.posts_display
  - html.post_display
  - html.pagination
  - html.authentication_required_msg
  - html.post_new_topic_link
