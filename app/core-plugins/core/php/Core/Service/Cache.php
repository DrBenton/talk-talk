<?php

namespace TalkTalk\CorePlugin\Core\Service;

use Doctrine\Common\Cache\Cache as CacheInterface;
//for dev:
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\PhpFileCache;

//for serious usages:
//TODO: handle real Cache Providers
//use Doctrine\Common\Cache\ApcCache;
//use Doctrine\Common\Cache\RedisCache;

use TalkTalk\Kernel\ApplicationInterface;
use TalkTalk\Kernel\Service\BaseService;

class Cache extends BaseService implements CacheInterface
{

    const CACHE_PREFIX = 'talk-talk';

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $doctrineCache;

    public function setApplication(ApplicationInterface $app)
    {
        parent::setApplication($app);

        if (empty($app->vars['config']['data-cache']['enabled'])) {
            // The app data cache is disabled: let's use the ArrayCache!
            $this->doctrineCache = new ArrayCache();
        } else {
            //TODO: allow full data cache customization through the app "main.ini.php" file
            $cachePath = $app->vars['app.cache_path'] . '/data-cache';
            $this->doctrineCache = new PhpFileCache($cachePath);
        }

    }

    /**
     * Fetches an entry from the cache.
     *
     * @param string $id The id of the cache entry to fetch.
     *
     * @return mixed The cached data or FALSE, if no cache entry exists for the given id.
     */
    function fetch($id)
    {
        return $this->doctrineCache->fetch($id);
    }

    /**
     * Tests if an entry exists in the cache.
     *
     * @param string $id The cache id of the entry to check for.
     *
     * @return boolean TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    function contains($id)
    {
        return $this->doctrineCache->contains($id);
    }

    /**
     * Puts data into the cache.
     *
     * @param string $id The cache id.
     * @param mixed $data The cache entry/data.
     * @param int $lifeTime The cache lifetime.
     *                         If != 0, sets a specific lifetime for this cache entry (0 => infinite lifeTime).
     *
     * @return boolean TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    function save($id, $data, $lifeTime = 0)
    {
        return $this->doctrineCache->save($id, $data, $lifeTime);
    }

    /**
     * Deletes a cache entry.
     *
     * @param string $id The cache id.
     *
     * @return boolean TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    function delete($id)
    {
        return $this->doctrineCache->delete($id);
    }

    /**
     * Retrieves cached information from the data store.
     *
     * The server's statistics array has the following values:
     *
     * - <b>hits</b>
     * Number of keys that have been requested and found present.
     *
     * - <b>misses</b>
     * Number of items that have been requested and not found.
     *
     * - <b>uptime</b>
     * Time that the server is running.
     *
     * - <b>memory_usage</b>
     * Memory used by this server to store items.
     *
     * - <b>memory_available</b>
     * Memory allowed to use for storage.
     *
     * @since 2.2
     *
     * @return array|null An associative array with server's statistics if available, NULL otherwise.
     */
    function getStats()
    {
        return $this->doctrineCache->getStats();
    }
}
