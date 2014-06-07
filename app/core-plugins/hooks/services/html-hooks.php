<?php

use Symfony\Component\HttpFoundation\Response;

$html_hooks = array();

$app->defineFunction(
    'hooks.html.add',
    function ($hooksNamesArgs) use (&$html_hooks) {
        $hooksNames = func_get_args();
        foreach ($hooksNames as $hookName) {
            $html_hooks[] = 'html.' . $hookName;
        }
    }
);

$app->defineFunction(
    'hooks.html.trigger_hooks',
    function (Response $response) use ($app, &$html_hooks) {

        if (isset($app->vars['app.error'])) {
            return; //don't modify any HTML code if we have an error
        }

        $rawView = $response->getContent();

        if (
            0 === count($html_hooks) ||
            $rawView === null ||
            false === strpos($response->headers->get('Content-Type'), 'text/html')
        ) {
            return;
        }

        $startTime = microtime(true);

        libxml_use_internal_errors(true); //disable warnings...
        $domView = QueryPath::withHTML($rawView);
        $html_hooks = array_unique($html_hooks);
        // Ok, let's trigger Plugins Hooks with a reference to the DOM View!!!
        foreach ($html_hooks as $hookName) {
            try {
                $app->get('hooks')
                    ->triggerPluginsHook($hookName, array(&$domView));
            } catch (\ErrorException $e) {
                ;// We won't handle DOM handling Exceptions for the moment...
            }
        }
        libxml_use_internal_errors(false); //...and enable it again!

        $app->vars['perfs.querypath.duration'] = round(microtime(true) - $startTime, 3);

        if ($app->vars['isAjax']) {
            // Notifications are handled in the Ajax Layout.
            // Let's remove them from the session!
            //$app['session.flash.clear']();//TODO
            // QueryPath has created a DOMDocument.
            // We have to return only the <body> inner HTML
            $modifiedHtml = $domView->find('body')->innerHtml();
        } else {
            // Let's return the plain HTML string
            $modifiedHtml = $domView->html();
        }
        $response->setContent($modifiedHtml);
    }
);
