<?php

use TalkTalk\Core\ApplicationInterface;

$app->error(
    function (\Exception $e) use ($app) {

        // We just inform the others app components that we have an error
        $app->vars['app.error'] = $e;
        $app->vars['app.http_status_code'] = 500;

        return;

    },
    ApplicationInterface::EARLY_EVENT
);
