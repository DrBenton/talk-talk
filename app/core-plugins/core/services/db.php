<?php

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;
use TalkTalk\CorePlugins\Core\Database\ConnectionResolver;

$app['db.settings'] = $app->share(
    function () use ($app) {
        return array_merge(
            array(
                'driver' => 'mysql',
                'host' => 'localhost',
                'database' => 'talk-talk',
                'username' => 'talk-talk',
                'password' => 'talk-talk',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => ''
            ),
            $app['config']['db']
        );
    }
);

$app['db.connection.factory'] = $app->protect(
    function (array $connectionSettings) use ($app) {
        $manager = new Manager();
        $manager->addConnection($connectionSettings);
        $manager->bootEloquent();

        $db = $manager->getConnection();

        return $db;
    }
);

$app['db'] = $app->share(
    function () use ($app) {
        return $app['db.connection.factory']($app['db.settings']);
    }
);

// Wires our Silex app to the Eloquent system
$app['db.connection_resolver.init'] = $app->protect(function () use ($app) {
    return $app['db'];
});
$connectionResolver = new ConnectionResolver();
$connectionResolver->addConnectionInitCallable('core', $app['db.connection_resolver.init']);
$connectionResolver->setDefaultConnection('core');
Model::setConnectionResolver($connectionResolver);

$app['db.connection_resolver'] = $connectionResolver;
