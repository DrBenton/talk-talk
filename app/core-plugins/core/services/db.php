<?php

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;
use TalkTalk\CorePlugins\Core\Database\ConnectionResolver;

$connectionsManager = new Manager();
$connectionResolver = new ConnectionResolver();

$DEFAULT_CONNECTION_NAME = 'talk-talk';

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
    function (array $connectionSettings, $connectionName) use ($app, $connectionsManager, $connectionResolver) {

        $connectionsManager->addConnection($connectionSettings, $connectionName);
        $connectionsManager->bootEloquent();

        $db = $connectionsManager->getConnection();

        if (null !== $connectionName) {
            // This is it not the default "talk-talk" DB connection,
            // already handled at the end of this file.
            // --> Let's add it to the ConnectionResolver!
            $connectionResolver->addConnection($connectionName, $db);
        }

        return $db;
    }
);

$app['db'] = $app->share(
    function () use ($app, $DEFAULT_CONNECTION_NAME) {
        return $app['db.connection.factory']($app['db.settings'], $DEFAULT_CONNECTION_NAME);
    }
);

// Wires our Silex app to the Eloquent system
$app['db.connections.resolver.default.init'] = $app->protect(
    function () use ($app) {
        return $app['db'];
    }
);

$connectionsManager->manager->setDefaultConnection($DEFAULT_CONNECTION_NAME);

$connectionResolver->addConnectionInitCallable($DEFAULT_CONNECTION_NAME, $app['db.connections.resolver.default.init']);
$connectionResolver->setDefaultConnection($DEFAULT_CONNECTION_NAME);
Model::setConnectionResolver($connectionResolver);

$app['db.connections.manager'] = $connectionsManager;
$app['db.connections.resolver'] = $connectionResolver;
