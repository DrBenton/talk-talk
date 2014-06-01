<?php

namespace TalkTalk\Core\Services;

use TalkTalk\Core\Application;

interface ServiceInterface
{

    /**
     * @return string
     */
    public static function getServiceName();

    /**
     * Triggered when an instance has been created by the ServicesManager.
     */
    public function setApplication(Application $app);

    /**
     * Triggered when an instance has been created by the ServicesManager,
     * after "setApplication()" and the optional init Closure.
     */
    public function onActivation();

}