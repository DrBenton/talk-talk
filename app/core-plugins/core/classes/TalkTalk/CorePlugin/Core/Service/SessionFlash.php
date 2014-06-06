<?php

namespace TalkTalk\CorePlugin\Core\Service;

use TalkTalk\Core\ApplicationInterface;
use TalkTalk\Core\Service\BaseService;

class SessionFlash extends BaseService
{

    const SLIM_FLASHES_SESSION_NS = 'slim.flash';

    /**
     * @var \Slim\Middleware\Flash
     */
    protected $slimFlash;

    public function __construct()
    {
        if (!isset($_COOKIE['PHPSESSID'])) {
            @session_start();
        }
    }

    public function setApplication(ApplicationInterface $app)
    {
        parent::setApplication($app);

        $slimEnv = $this->app->get('slim')->environment();
        $this->slimFlash = $slimEnv[self::SLIM_FLASHES_SESSION_NS];
    }

    public function flash($flashKey, $flashValue)
    {
        $this->slimFlash->set($flashKey, $flashValue);
    }

    public function flashNow($flashKey, $flashValue)
    {
        $this->slimFlash->now($flashKey, $flashValue);
    }

    public function flashTranslated($flashKey, $flashValueTranslationKey, $flashValueTranslationParams = array())
    {
        $this->flash(
            $flashKey,
            $this->translateFlash($flashValueTranslationKey, $flashValueTranslationParams)
        );
    }

    public function flashTranslatedNow($flashKey, $flashValueTranslationKey, $flashValueTranslationParams = array())
    {
        $this->flashNow(
            $flashKey,
            $this->translateFlash($flashValueTranslationKey, $flashValueTranslationParams)
        );
    }

    public function keepFlashes()
    {
        $this->slimFlash->keep();
    }

    public function getFlashes($flashesKeyPrefix = null)
    {
        $nextMessages = (isset($_SESSION[self::SLIM_FLASHES_SESSION_NS]) && is_array($_SESSION[self::SLIM_FLASHES_SESSION_NS]))
            ? $_SESSION[self::SLIM_FLASHES_SESSION_NS]
            : array();
        $flashes = $nextMessages + $this->slimFlash->getMessages();

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
        //echo '$filteredFlashes=<pre>'.print_r($filteredFlashes, true).'</pre>';
        return $filteredFlashes;
    }

    protected function translateFlash($flashValueTranslationKey, array $flashValueTranslationParams)
    {
        return $this->app->get('translator')->trans($flashValueTranslationKey, $flashValueTranslationParams);
    }

}
