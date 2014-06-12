<?php

$action = function () use ($app) {
    return $app->json(
        $app->exec('phpbb.import.posts.metadata')
    );
};

return $action;
