<?php

namespace TalkTalk\CorePlugin\Core\Service;

use TalkTalk\Core\ApplicationInterface;
use TalkTalk\Core\Service\BaseService;

class SessionFlash extends BaseService
{

    const FLASHES_SESSION_NS = 'talktalk.flash';

    public function __construct()
    {
        if (!isset($_COOKIE['PHPSESSID'])) {
            @session_start();
        }
    }

    public function flash($flashKey, $flashValue)
    {
        $_SESSION[self::FLASHES_SESSION_NS][$flashKey] = $flashValue;
    }

    public function flashTranslated($flashKey, $flashValueTranslationKey, $flashValueTranslationParams = array())
    {
        $this->flash(
            $flashKey,
            $this->translateFlash($flashValueTranslationKey, $flashValueTranslationParams)
        );
    }

    public function keepFlashes()
    {
        //TODO
    }

    public function getFlashes($flashesKeyPrefix = null)
    {
        if (!isset($_SESSION[self::FLASHES_SESSION_NS])) {
            return array();
        }

        $flashes = $_SESSION[self::FLASHES_SESSION_NS];

        if (null === $flashesKeyPrefix) {
            return $flashes;
        }

        //echo '$flashes=<pre>'.print_r($flashes, true).'</pre>';
        $filteredFlashes = array();
        array_walk(
            $flashes,
            function ($flashValue, $flashKey) use (&$filteredFlashes, $flashesKeyPrefix) {
                if (0 === strpos($flashKey, $flashesKeyPrefix)) {
                    $filteredFlashes[$flashKey] = $flashValue;
                }
            }
        );

        //TODO: make it cleaner, or use Symfony Session & FlashBag
        unset($_SESSION[self::FLASHES_SESSION_NS]);

        //echo '$filteredFlashes=<pre>'.print_r($filteredFlashes, true).'</pre>';
        return $filteredFlashes;
    }

    protected function translateFlash($flashValueTranslationKey, array $flashValueTranslationParams)
    {
        return $this->app->get('translator')->trans($flashValueTranslationKey, $flashValueTranslationParams);
    }

}
