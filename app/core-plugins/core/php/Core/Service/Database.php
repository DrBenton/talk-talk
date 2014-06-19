<?php

namespace TalkTalk\CorePlugin\Core\Service;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;
use TalkTalk\CorePlugin\Core\Database\ConnectionResolver;
use TalkTalk\CorePlugin\Core\Cache\IlluminateCacheManager;
use TalkTalk\Kernel\ApplicationInterface;
use TalkTalk\Kernel\Service\BaseService;

class Database extends BaseService
{

    const DEFAULT_DB_CONNECTION_NAME = 'talk-talk';

    /**
     * @var \Illuminate\Database\Capsule\Manager
     */
    protected $capsule;
    /**
     * @var \TalkTalk\CorePlugin\Core\Database\ConnectionResolver
     */
    protected $connectionResolver;
    /**
     * @var bool
     */
    protected $defaultConnectionInitialized = false;

    public function setApplication(ApplicationInterface $app)
    {
        parent::setApplication($app);

        $this->app->vars['db.settings'] = array_merge(
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

        $this->initDbCapsule();
        $this->linkConnectionToIlluminateModels();
    }

    public function addConnection(array $connectionSettings, $connectionName)
    {
        $this->capsule->addConnection($connectionSettings, $connectionName);
        $connexion = $this->capsule->getConnection($connectionName);

        if (!empty($this->app->vars['config']['debug']['perfs.tracking.enabled'])) {
            $connexion->enableQueryLog();
        }

        if (self::DEFAULT_DB_CONNECTION_NAME !== $connectionName) {
            // We only add the connection if this is not the default one
            // (default connection is already initialized in "initDbCapsule()")
            $this->connectionResolver->addConnection($connectionName, $connexion);
        }
    }

    public function getConnection($connectionName = null)
    {
        if (!$this->defaultConnectionInitialized) {
            $this->initDefaultConnection();
        }

        return $this->capsule->getConnection($connectionName);
    }

    protected function initDbCapsule()
    {
        $this->capsule = new Capsule;
        $this->connectionResolver = new ConnectionResolver();
    }

    protected function linkConnectionToIlluminateModels()
    {
        $this->capsule->getDatabaseManager()->setDefaultConnection(self::DEFAULT_DB_CONNECTION_NAME);

        $this->connectionResolver->addConnectionInitCallable(
            self::DEFAULT_DB_CONNECTION_NAME,
            array($this, 'getConnection')
        );
        $this->connectionResolver->setDefaultConnection(self::DEFAULT_DB_CONNECTION_NAME);
        Model::setConnectionResolver($this->connectionResolver);
    }

    protected function initDefaultConnection()
    {
        $this->addConnection($this->app->vars['db.settings'], self::DEFAULT_DB_CONNECTION_NAME);

        $cacheManager = new IlluminateCacheManager();
        $cacheManager->setApplication($this->app);
        $this->capsule->setCacheManager($cacheManager);

        $this->capsule->bootEloquent();

        $this->defaultConnectionInitialized = true;
    }


}
