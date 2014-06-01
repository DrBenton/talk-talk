<?php

namespace TalkTalk\Core\Services;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;

/**
 * Class CacheProxy
 * Just a proxy class for Doctrine CacheProvider.
 * Most used functions are implemented, the others are handled with PHP magic methods.
 *
 * @package TalkTalk\Core\Services
 */
class CacheProxy extends ServiceProxyBase implements Cache
{

    /**
     * @var \Doctrine\Common\Cache\CacheProvider
     */
    protected $proxyTarget;

    /**
     * @inheritdoc
     */
    public static function getServiceName()
    {
        return 'cache';
    }

    public function fetch($id)
    {
        return $this->proxyTarget->fetch($id);
    }

    public function contains($id)
    {
        return $this->proxyTarget->contains($id);
    }

    public function save($id, $data, $lifeTime = 0)
    {
        return $this->proxyTarget->save($id, $data, $lifeTime);
    }

    public function delete($id)
    {
        return $this->proxyTarget->delete($id);
    }

    public function getStats()
    {
        return $this->proxyTarget->getStats();
    }

    public function flushAll()
    {
        return $this->proxyTarget->flushAll();
    }

    public function deleteAll()
    {
        return $this->proxyTarget->deleteAll();
    }

}