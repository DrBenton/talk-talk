<?php

call_user_func(
  function () use ($app)
  {
        $mainConfigFilePath = $app->vars['app.path'] . '/app/config/main.ini.php';

      $app->vars['config'] = parse_ini_file($mainConfigFilePath, true);

  }
);