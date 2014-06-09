<?php

$action = function () use ($app) {

    // We clear the imported DB entities ids mappings hashes
    $app->exec('phpbb.import.clear_imports_ids_mappings');

    return $app->json(
        array(
            'success' => true,
        )
    );
};

return $action;