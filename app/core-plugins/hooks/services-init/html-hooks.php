<?php

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
    function () use ($app, &$html_hooks) {

        /*
         //TODO
        if (null !== $app['app.error']) {
            return; //don't modify any HTML code if we have an error
        }
         */

        $response = &$app->getResponse();
        $rawView = $response->getBody();

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
        foreach ($html_hooks as $hookName) {
            $app->getService('hooks')
                ->triggerPluginsHook($hookName, array(&$domView));
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
        $response->setBody($modifiedHtml);
    }
);
