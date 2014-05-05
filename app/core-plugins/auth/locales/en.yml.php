#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

core-plugins:
  auth:
    header_links:
      sign-up: Sign up
      sign-in: Sign in
      sign-out: Sign out
    sign-up:
      form:
        login: Login
        email: Email address
        password: Password
        password_confirmation: Password confirmation
        submit: Sign up
      notifications:
        success: Welcome %login%!
    sign-in:
      form:
        login: Login
        password: Password
        submit: Sign in
      notifications:
        success: Welcome back %login%!
        error: No User found for this login or password.
    sign-out:
      notifications:
        success: Bye! Hope to see you soon!
