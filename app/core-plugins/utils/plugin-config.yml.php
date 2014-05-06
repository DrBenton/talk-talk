#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

general:
  id: utils

services:
  - utils-html

hooks:
  -
    name: html.header
    # this hook will be triggered *after* the other plugins "html.header" hooks:
    priority: -10
  - html.site_container
  
twig-extensions:
