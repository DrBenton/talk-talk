<?php

namespace TalkTalk\Core\Services;

use Composer\Autoload\ClassLoader;

/**
 * Class AutoloaderProxy
 * Just a proxy class for Composer ClassLoader.
 * Most used functions are implemented, the others are handled with PHP magic methods.
 *
 * @package TalkTalk\Core\Services
 */
class AutoloaderProxy extends ServiceProxyBase
{

    /**
     * @var \Composer\Autoload\ClassLoader
     */
    protected $proxyTarget;

    /**
     * @inheritdoc
     */
    public static function getServiceName()
    {
        return 'autoloader';
    }

    /**
     * @param $prefix
     * @param $paths
     * @param bool $prepend
     */
    public function addPsr4($prefix, $paths, $prepend = false)
    {
        return $this->proxyTarget->addPsr4($prefix, $paths, $prepend);
    }

    /**
     * @param array $classMap Class to filename map
     */
    public function addClassMap(array $classMap)
    {
        return $this->proxyTarget->addClassMap($classMap);
    }

    /**
     * Registers a set of PSR-0 directories for a given prefix, either
     * appending or prepending to the ones previously set for this prefix.
     *
     * @param string       $prefix  The prefix
     * @param array|string $paths   The PSR-0 root directories
     * @param bool         $prepend Whether to prepend the directories
     */
    public function add($prefix, $paths, $prepend = false)
    {
        return $this->proxyTarget->add($prefix, $paths, $prepend);
    }

}