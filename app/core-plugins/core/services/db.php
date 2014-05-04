<?php

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;
use TalkTalk\CorePlugins\Core\Database\ConnectionResolver;

$connectionResolver = null;
$db = null;

//TODO: make it easy to configure
$app['db.settings'] = array(
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'talk-talk',
    'username' => 'talk-talk',
    'password' => 'talk-talk',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => ''
);

$app['db.init'] = $app->protect(
    function () use ($app, &$db) {
        if (null !== $db) {
            return $db;
        }

        $manager = new Manager();
        $manager->addConnection($app['db.settings']);
        $manager->bootEloquent();

        $db = $manager->getConnection();

        return $db;
    }
);

$app['db'] = $app->share(
    function () use ($app, &$db) {
        if (null === $db) {
            $db = $app['db.init']();
        }

        return $db;
    }
);

// Wires our Silex app to the Eloquent system
$connectionResolver = new ConnectionResolver();
$connectionResolver->addConnectionClosureInit('core', $app['db.init']);
$connectionResolver->setDefaultConnection('core');
Model::setConnectionResolver($connectionResolver);

$app['db.connection_resolver'] = $connectionResolver;
