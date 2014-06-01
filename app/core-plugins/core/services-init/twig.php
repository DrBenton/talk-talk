<?php

use TalkTalk\CorePlugins\Core\Services\TwigProxy;

$app->vars['twig.options'] = array(
    'cache' => $app->config('debug') ? null : $app->vars['app.var.cache.path'] . '/twig',
    'strict_variables' => true,
);

return $app->servicesManager->registerServiceClass(
    'TalkTalk\CorePlugins\Core\Services\TwigProxy',
    function (TwigProxy $serviceInstance) use ($app) {
        $twig = new \Twig_Environment(null, $app->vars['twig.options']);
        $serviceInstance->setProxyTarget($twig);
    }
);
