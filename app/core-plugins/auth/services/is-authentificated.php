<?php

$app['isAuthenticated'] = $app->share(function () use ($app) {
  return (null !== $app['session']->get('user'));
});