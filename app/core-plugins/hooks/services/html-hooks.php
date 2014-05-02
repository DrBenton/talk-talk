<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$html_hooks = array();

$app['plugins.html_hooks.add'] = $app->protect(
    function ($hookName) use (&$html_hooks) {
        $html_hooks[] = 'html.' . $hookName;
    }
);

$app['plugins.html_hooks.trigger_hooks'] = $app->protect(
    function (Response &$appResponse) use ($app, &$html_hooks) {

        $rawView = $appResponse->getContent();

        libxml_use_internal_errors(true); //disable warnings...
        $domView = QueryPath::withHTML($rawView);
        foreach ($html_hooks as $hookName) {
            $app['plugins.manager']->triggerHook($hookName, array($domView));
        }
        libxml_use_internal_errors(false); //...and enable it again!

        $appResponse->setContent($domView->html());
    }
);

// HTML hooks will be triggered just before the Response sending
$app->after(function (Request $request, Response $response) use ($app) {
    $app['plugins.html_hooks.trigger_hooks']($response);
});
