#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

general:


assets:
  stylesheets:
    - "${pluginUrl}/assets/components/bootstrap/dist/css/bootstrap.css"
    - "${pluginUrl}/assets/css/theme-twbootstrap.css"
  javascripts:

hooks:
  - html.site_container
  - html.main_content_container 
  - html.header
  - html.form
  - html.notifications_display
  - html.user_profile_display
