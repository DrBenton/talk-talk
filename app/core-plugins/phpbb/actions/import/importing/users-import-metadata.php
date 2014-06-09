<?php

$action = function () use ($app) {
    return $app->json(
        $app->exec('phpbb.import.users.metadata')
    );
};

return $action;