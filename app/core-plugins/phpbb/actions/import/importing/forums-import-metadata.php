<?php

$action = function () use ($app) {
    return $app->json(
        $app->exec('phpbb.import.forums.metadata')
    );
};

return $action;
