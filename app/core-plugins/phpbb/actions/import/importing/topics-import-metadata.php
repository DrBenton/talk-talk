<?php

$action = function () use ($app) {
    return $app->json(
        $app->exec('phpbb.import.topics.metadata')
    );
};

return $action;