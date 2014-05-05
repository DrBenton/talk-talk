#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

general:
  id: auth

actions:
  -
    url: /sign-up
    target: actions/sign-up-form.php
    name: auth/sign-up
  -
    url: /sign-up
    method: POST
    target: actions/sign-up-target.php
    name: auth/sign-up/submit
  -
    url: /sign-in
    target: actions/sign-in-form.php
    name: auth/sign-in
  -
    url: /sign-in
    method: POST
    target: actions/sign-in-target.php
    name: auth/sign-in/submit
  -
    url: /sign-out
    target: actions/sign-out.php
    name: auth/sign-out

classes:
  -
    prefix: TalkTalk\Model\
    paths: ${pluginPath}/classes/TalkTalk/Model

services:
  - is-authentificated

locales:
  - en

hooks:
  - html.header

twig-extensions:
