<?php

namespace TalkTalk\CorePlugin\Core;

/**
 * Actions
 */

$app->addAction('/', function () use ($app) {
    return $app->get('view')
        ->render('core::home');
})
    ->method('GET')
    ->bind('core/home');


/**
 * Classes init
 */

$app->get('autoloader')->add('TalkTalk\CorePlugin\Core', __DIR__ . '/classes');


/**
 * App Services & Functions definition
 */

// "view" Service
$app->vars['view.templates_ext'] = 'tpl.php';
// This array will be used by our View Service to init Views folders
$app->vars['view.folders'] = array();
// And that one this will be used by our View Service to init Views Extensions
$app->vars['view.extensions'] = array();

$app->defineService(
    'view',
    function () use ($app) {
        $service = new Service\View();
        $service->setTemplatesFilesExtension($app->vars['view.templates_ext']);

        // Let's init the Plugins Views folders!
        $pluginsFinder = $app->get('plugins.finder');
        foreach($pluginsFinder->getPlugins() as $plugin) {
            $viewsPath = $plugin->path . '/templates';
            if (is_dir($viewsPath)) {
                $app->vars['view.folders'][] = array(
                    'namespace' => $plugin->id,
                    'path' => $viewsPath
                );
            }
        }

        return $service;
    }
);

// "session" Service
$app->defineService(
    'session',
    function () use ($app) {
        $service = new Service\Session();

        return $service;
    }
);

// "session flashes" Service
$app->defineService(
    'flash',
    function () use ($app) {
        $service = new Service\SessionFlash();

        return $service;
    }
);

// "csrf" Service
$app->defineService(
    'csrf',
    function () use ($app) {
        $service = new Service\Csrf();

        return $service;
    }
);

// "app assets" vars
$app->vars['app.assets.css'] = array();
$app->vars['app.assets.js.head'] = array();
$app->vars['app.assets.js.endOfBody'] = array();


/**
 * Views extensions
 */

$app->vars['view.extensions'][] = 'TalkTalk\CorePlugin\Core\Plates\Extension\App';
$app->vars['view.extensions'][] = 'TalkTalk\CorePlugin\Core\Plates\Extension\AppAssets';
$app->vars['view.extensions'][] = 'TalkTalk\CorePlugin\Core\Plates\Extension\Translation';
