<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;
use TalkTalk\CorePlugin\Core\Database\ConnectionResolver;

$DEFAULT_DB_CONNECTION_NAME = 'talk-talk';

$capsule = new Capsule;
$connectionResolver = new ConnectionResolver();

$app->vars['db.settings'] = array_merge(
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
    $app->vars['config']['db']
);

$app->defineFunction(
    'db.connection.add',
    function (array $connectionSettings, $connectionName) use (
        $app,
        $capsule,
        $connectionResolver,
        $DEFAULT_DB_CONNECTION_NAME
    ) {
        $capsule->addConnection($connectionSettings, $connectionName);
        $connexion = $capsule->getConnection($connectionName);

        if (
            $app->vars['config']['debug']['perfs.tracking.enabled'] &&
            $app->vars['config']['debug']['perfs.tracking.sql_queries.enabled']
        ) {
            $connexion->enableQueryLog();
        }

        if ($DEFAULT_DB_CONNECTION_NAME !== $connectionName) {
            // We only add the connection if this is not the default one
            // (default connection is already initialized at the end of this file)
            $connectionResolver->addConnection($connectionName, $connexion);
        }
    }
);

$app->defineService(
    'db',
    function () use ($app, $capsule, $DEFAULT_DB_CONNECTION_NAME) {
        $app->execFunction('db.connection.add', $app->vars['db.settings'], $DEFAULT_DB_CONNECTION_NAME);
        //$capsule->setCacheManager(new IlluminateCacheManager($app));
        $capsule->bootEloquent();

        return $capsule;
    }
);

// Wires our app to the Eloquent system
$app->defineFunction(
    'db.connections.resolver.default.init',
    function () use ($app) {
        return $app->get('db')->getConnection();
    }
);

$capsule->getDatabaseManager()->setDefaultConnection($DEFAULT_DB_CONNECTION_NAME);

$connectionResolver->addConnectionInitCallable(
    $DEFAULT_DB_CONNECTION_NAME,
    $app->getFunction('db.connections.resolver.default.init')
);
$connectionResolver->setDefaultConnection($DEFAULT_DB_CONNECTION_NAME);
Model::setConnectionResolver($connectionResolver);

$app->defineService(
    'db.connections.resolver',
    $connectionResolver
);
