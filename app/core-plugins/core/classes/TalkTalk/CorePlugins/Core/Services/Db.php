<?php

namespace TalkTalk\CorePlugins\Core\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;
use TalkTalk\CorePlugins\Core\Database\ConnectionResolver;
use TalkTalk\CorePlugins\Core\Cache\IlluminateCacheManager;
use TalkTalk\Core\Services\ServiceBase;

/**
 * Class Db
 * @package TalkTalk\CorePlugins\Core\Service
 */
class Db extends ServiceBase
{
    const DEFAULT_DB_CONNECTION_NAME = 'talk-talk';

    /**
     * @var \Illuminate\Database\Capsule\Manager
     */
    public $capsule;
    /**
     * @var \TalkTalk\CorePlugins\Core\Database\ConnectionResolver
     */
    public $connectionResolver;

    protected $eloquentBooted = false;

    /**
     * @inheritdoc
     */
    public static function getServiceName()
    {
        return 'db';
    }

    public function __construct()
    {
        $this->capsule = new Capsule();
        $this->connectionResolver = new ConnectionResolver();
    }

    public function onActivation()
    {
        $this->bootEloquent();
    }

    public function getDefaultConnection()
    {
        $this->bootEloquent();

        return $this->capsule->getConnection();
    }

    public function addConnection(array $connectionSettings, $connectionName)
    {
        $this->capsule->addConnection($connectionSettings, $connectionName);
        $connexion = $this->capsule->getConnection($connectionName);

        if (
            $this->app->vars['config']['debug']['perfs.tracking.enabled'] &&
            $this->app->vars['config']['debug']['perfs.tracking.sql_queries.enabled']
        ) {
            $connexion->enableQueryLog();
        }

        if (self::DEFAULT_DB_CONNECTION_NAME !== $connectionName) {
            // We only add the connection if this is not the default one
            // (default connection is already initialized in "initIlluminateEnvironment()")
            $this->connectionResolver->addConnection($connectionName, $connexion);
        }
    }

    public function initIlluminateEnvironment()
    {
        $this->capsule->manager->setDefaultConnection(self::DEFAULT_DB_CONNECTION_NAME);

        $this->connectionResolver->addConnectionInitCallable(
            self::DEFAULT_DB_CONNECTION_NAME,
            array($this, 'getDefaultConnection')
        );
        $this->connectionResolver->setDefaultConnection(self::DEFAULT_DB_CONNECTION_NAME);
        Model::setConnectionResolver($this->connectionResolver);
    }

    protected function bootEloquent()
    {
        if ($this->eloquentBooted) {
            return;
        }

        $this->addConnection($this->getDbSettings(), self::DEFAULT_DB_CONNECTION_NAME);
        $this->capsule->setCacheManager(new IlluminateCacheManager($this->app));
        $this->capsule->bootEloquent();

        $this->eloquentBooted = true;
    }

    protected function getDbSettings() {
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
            $this->app->vars['config']['db']
        );
    }

}