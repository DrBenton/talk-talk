<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;
use TalkTalk\CorePlugins\Core\Database\ConnectionResolver;
use TalkTalk\CorePlugins\Core\Cache\IlluminateCacheManager;

$DEFAULT_DB_CONNECTION_NAME = 'talk-talk';

$capsule = new Capsule;
$connectionResolver = new ConnectionResolver();

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

$app['db.connection.add'] = $app->protect(
    function (array $connectionSettings, $connectionName) use (
        $app,
        $capsule,
        $connectionResolver,
        $DEFAULT_DB_CONNECTION_NAME
    ) {
        $capsule->addConnection($connectionSettings, $connectionName);
        $connexion = $capsule->getConnection($connectionName);

        if ($app['config']['debug']['perfs.tracking']) {
            $connexion->enableQueryLog();
        }

        if ($DEFAULT_DB_CONNECTION_NAME !== $connectionName) {
            // We only add the connection if this is not the default one
            // (default connection is already initialized at the end of this file)
            $connectionResolver->addConnection($connectionName, $connexion);
        }
    }
);

$app['db'] = $app->share(
    function () use ($app, $capsule, $DEFAULT_DB_CONNECTION_NAME) {
        $app['db.connection.add']($app['db.settings'], $DEFAULT_DB_CONNECTION_NAME);
        $capsule->setCacheManager(new IlluminateCacheManager($app));
        $capsule->bootEloquent();

        return $capsule;
    }
);

// Wires our Silex app to the Eloquent system
$app['db.connections.resolver.default.init'] = $app->protect(
    function () use ($app) {
        return $app['db']->getConnection();
    }
);

$capsule->manager->setDefaultConnection($DEFAULT_DB_CONNECTION_NAME);

$connectionResolver->addConnectionInitCallable(
    $DEFAULT_DB_CONNECTION_NAME,
    $app['db.connections.resolver.default.init']
);
$connectionResolver->setDefaultConnection($DEFAULT_DB_CONNECTION_NAME);
Model::setConnectionResolver($connectionResolver);

$app['db.connections.resolver'] = $connectionResolver;
