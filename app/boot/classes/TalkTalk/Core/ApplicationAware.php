<?php

namespace TalkTalk\Core;

abstract class ApplicationAware implements ApplicationAwareInterface
{

    /**
     * @var \TalkTalk\Core\ApplicationInterface
     */
    protected $app;

    public function setApplication(ApplicationInterface $app)
    {
        $this->app = $app;
    }

}