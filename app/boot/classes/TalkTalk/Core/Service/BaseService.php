<?php

namespace TalkTalk\Core\Service;

use TalkTalk\Core\Application;
use TalkTalk\Core\ApplicationAwareInterface;

abstract class BaseService implements ApplicationAwareInterface
{

    /**
     * @var \TalkTalk\Core\Application
     */
    protected $app;

    public function setApplication(Application $app)
    {
        $this->app = $app;
    }

}