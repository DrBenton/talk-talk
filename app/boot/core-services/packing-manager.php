<?php

use TalkTalk\Core\Service\PackingManager;

$app->defineService(
    'packing-manager',
    function () use ($app) {
        $service = new PackingManager();
        $service->setPacksDir($app->vars['app.php_packs_path']);

        $whiteSpacesStripping = !empty($app->vars['config']['packing']['strip_white_spaces']);
        $service->setWhiteSpacesStripping($whiteSpacesStripping);

        return $service;
    }
);
