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
  - html.alerts
  - html.user_profile
  - html.page.forum
  - html.forums_list
  - html.page.topic
  - html.topics_list
  - html.topic
  - html.posts_list
  - html.post
  - html.pagination
  - html.authentication_required_msg
  - html.create_new_topic_link
  - html.create_new_post_link
  - html.page.new_post_form
