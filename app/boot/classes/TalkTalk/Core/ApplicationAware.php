<?php

namespace TalkTalk\Core;

abstract class ApplicationAware implements ApplicationAwareInterface
{

    /**
     * @var \TalkTalk\Core\Application
     */
    protected $app;

    public function setApplication(ApplicationInterface $app)
    {
        $this->app = $app;
    }

}