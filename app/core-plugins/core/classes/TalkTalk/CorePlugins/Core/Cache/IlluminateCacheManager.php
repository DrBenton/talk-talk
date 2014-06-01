<?php

namespace TalkTalk\CorePlugins\Core\Cache;

use Illuminate\Cache\CacheManager;
use TalkTalk\Core\Application;

class IlluminateCacheManager extends CacheManager
{
    /**
     * @var \TalkTalk\Core\Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Returns an instance of the Doctrine cache wrapper.
     *
     * @return \TalkTalk\CorePlugins\Core\Cache\DoctrineCacheWrappingStore
     */
    protected function createDoctrineDriver()
    {
        return $this->repository(new DoctrineCacheWrappingStore($this->app));
    }

    /**
     * Get the default cache driver name.
     *
     * @return string
     */
    protected function getDefaultDriver()
    {
        return 'doctrine';
    }
}
