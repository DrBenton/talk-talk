#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

general:

actions:
  -
    url: /sign-up
    target: actions/sign-up-form.php
    name: auth/signup
  -
    url: /sign-up
    target: actions/sign-up-target.php
    name: auth/signup-submit
    method: POST

classes:
  -
    prefix: TalkTalk\Model\
    paths: ${pluginPath}/classes/TalkTalk/Model

services:
  - auth-helpers 
  

twig-extensions:
