#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

@general:
  id: theme-twbootstrap
  # since we are a theme plugin, we want to be triggered *after* all other plugins:
  htmlHooksPriority: -100

@assets:
  stylesheets:
    - "%plugin-url%/assets/components/bootstrap/dist/css/bootstrap.css"
    - "%plugin-url%/assets/css/theme-twbootstrap.css"
  javascripts:

@hooks:
  - html.site_container
  - html.main_content_container 
  - html.header
  - html.breadcrumb
  - html.page.home
  - html.form
  - html.signup_form
  - html.component.alert
  - html.user_profile
  - html.page.forum
  - html.forums_list
  - html.page.topic
  - html.topics_list
  - html.topic
  - html.posts_list
  - html.post
  - html.component.pagination
  - html.authentication_required_msg
  - html.create_new_topic_link
  - html.create_new_post_link
  - html.page.new_topic_form
  - html.page.new_post_form
  - html.component.progress
  - html.phpbb_db_settings_form
  - html.ajax_topic_writing_widget

