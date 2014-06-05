#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

@language: en

core-plugins:
  auth:
    header_links:
      sign-up: Sign up
      sign-in: Sign in
      sign-out: Sign out
    sign-up:
      breadcrumb:
        - Sign up
      form:
        already-have-account: >
          Already have an account?
          <a href="%sign-in-url%" class="sign-in-link">Sign in</a> instead.
        login: Login
        email: Email address
        password: Password
        password_confirmation: Password confirmation
        submit: Sign up
      notifications:
        success: Welcome %login%!
    sign-in:
      breadcrumb:
        - Sign in
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
    middlewares:
      authentication-required: Authentication required. Please sign in.