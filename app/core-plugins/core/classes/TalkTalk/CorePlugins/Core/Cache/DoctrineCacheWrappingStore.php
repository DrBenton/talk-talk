<?php

namespace TalkTalk\CorePlugins\Core\Cache;

use Illuminate\Cache\StoreInterface;
use Silex\Application;

class DoctrineCacheWrappingStore implements StoreInterface
{
    /**
     * @var \Silex\Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        $value = $this->getDoctrineCache()->fetch($key);
        if ($value !== false){
			return $value;
		}
    }

    /**
     * Store an item in the cache for a given number of minutes.
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  int    $minutes
     * @return void
     */
    public function put($key, $value, $minutes)
    {
        return $this->getDoctrineCache()->save($key, $value, $minutes * 60);
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function increment($key, $value = 1)
    {
        throw new \LogicException("Increment operations not supported by this driver.");
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function decrement($key, $value = 1)
    {
        throw new \LogicException("Increment operations not supported by this driver.");
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function forever($key, $value)
    {
        return $this->getDoctrineCache()->save($key, $value, 0);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string $key
     * @return void
     */
    public function forget($key)
    {
        return $this->getDoctrineCache()->delete($key);
    }

    /**
     * Remove all items from the cache.
     *
     * @return void
     */
    public function flush()
    {
        return $this->getDoctrineCache()->deleteAll();
    }

    /**
     * Get the cache key prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->app['cache.prefix'];
    }

    /**
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    protected function getDoctrineCache()
    {
        return $this->app['cache'];
    }
}
