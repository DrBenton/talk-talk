<?php

$action = function (\Silex\Application $app) {
  return $app['twig']->render('core/index.twig');
};

return $action;
