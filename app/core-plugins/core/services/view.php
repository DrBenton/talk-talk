<?php

use TalkTalk\CorePlugin\Core\Service\View;

$app->vars['view.templates_ext'] = 'tpl.php';

$app->defineService(
    'view',
    function () use ($app) {
        $service = new View();
        $service->setTemplatesFilesExtension($app->vars['view.templates_ext']);

        return $service;
    }
);
