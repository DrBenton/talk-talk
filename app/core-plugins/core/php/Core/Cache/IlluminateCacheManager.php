<?php

namespace TalkTalk\CorePlugin\Core\Cache;

use Illuminate\Cache\CacheManager;
use TalkTalk\Kernel\ApplicationInterface;
use TalkTalk\Kernel\ApplicationAwareInterface;

class IlluminateCacheManager extends CacheManager implements ApplicationAwareInterface
{

    public function __construct()
    {
        parent::__construct(null);
    }

    /**
     * @var \TalkTalk\Kernel\Application
     */
    protected $app;

    public function setApplication(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    /**
     * Returns an instance of the Doctrine cache wrapper.
     *
     * @return \TalkTalk\CorePlugin\Core\Cache\DoctrineCacheWrappingIlluminateStore
     */
    protected function createDoctrineDriver()
    {
        $store = new DoctrineCacheWrappingIlluminateStore();
        $store->setApplication($this->app);

        return $store;
    }

    /**
     * Get the default cache driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return 'doctrine';
    }
}
