<?php

use TalkTalk\Core\Plugins\Manager\Behaviour\HooksManager;

$app->before(function () use ($app) {
    $app['plugins.manager']->addBehaviour(new HooksManager());
});
