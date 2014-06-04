<?php

// HTML hooks actions will be triggered just before the Response sending
$app->after(
    function () use ($app) {
        $app->execFunction('hooks.html.trigger_hooks');
    }
);