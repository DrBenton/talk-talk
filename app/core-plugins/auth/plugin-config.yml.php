#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

@general:
  id: auth

@actions:
  -
    # GET /sign-up => actions/sign-up-form.php
    url: /sign-up
    target: sign-up-form
    name: auth/sign-up
  -
    # POST /sign-up => actions/sign-up-target.php
    url: /sign-up
    method: POST
    target: sign-up-target
    name: auth/sign-up/target
  -
    # GET /sign-in => actions/sign-in-form.php
    url: /sign-in
    target: sign-in-form
    name: auth/sign-in
  -
    # POST /sign-in => actions/sign-in-form-target.php
    url: /sign-in
    method: POST
    target: sign-in-target
    name: auth/sign-in/target
  -
    # GET /sign-out => actions/sign-out.php
    url: /sign-out
    target: sign-out
    name: auth/sign-out

@classes:
  -
    prefix: TalkTalk\Model\
    path: %plugin-path%/classes/TalkTalk/Model

@services:
#  - is-authenticated
#  - before-middlewares
#  - user

@translations:
  - en

#@hooks:
#  - auth.user.check-signin-credentials
#  - html.header

