<?php

use Symfony\Component\HttpFoundation\Response;

$html_hooks = array();

$app['plugins.html_hooks.add'] = $app->protect(
    function ($hooksNamesArgs) use (&$html_hooks) {
        $hooksNames = func_get_args();
        foreach ($hooksNames as $hookName) {
            $html_hooks[] = 'html.' . $hookName;
        }
    }
);

$app['plugins.html_hooks.trigger_hooks'] = $app->protect(
    function (Response &$appResponse) use ($app, &$html_hooks) {

        $rawView = $appResponse->getContent();

        if (
            0 === count($html_hooks) ||
            $rawView === null ||
            false === strpos($appResponse->headers->get('Content-Type'), 'text/html')
        ) {
            return;
        }

        libxml_use_internal_errors(true); //disable warnings...
        $domView = QueryPath::withHTML($rawView);
        foreach ($html_hooks as $hookName) {
            $app['plugins.manager']->triggerHook($hookName, array(&$domView));
        }
        libxml_use_internal_errors(false); //...and enable it again!

        if ($app['isAjax']) {
            // Notifications are handled in the Ajax Layout.
            // Let's remove them from the session!
            $app['session.flash.clear']();
            // QueryPath has created a DOMDocument.
            // We have to return only the <body> inner HTML
            $modifiedHtml = $domView->find('body')->innerHtml();
        } else {
            // Let's return the plain HTML string
            $modifiedHtml = $domView->html();
        }
        $appResponse->setContent($modifiedHtml);
    }
);
