<?php

namespace TalkTalk\Core\Services;

use TalkTalk\Core\Application;

abstract class ServiceBase implements ServiceInterface
{
    /**
     * @var \TalkTalk\Core\Application
     */
    protected $app;

    /**
     * @inheritdoc
     */
    public function setApplication(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @inheritdoc
     */
    public function onActivation()
    {
    }

}