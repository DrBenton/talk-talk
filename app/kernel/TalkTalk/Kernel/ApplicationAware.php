<?php

namespace TalkTalk\Kernel;

abstract class ApplicationAware implements ApplicationAwareInterface
{

    /**
     * @var \TalkTalk\Kernel\ApplicationInterface
     */
    protected $app;

    public function setApplication(ApplicationInterface $app)
    {
        $this->app = $app;
    }

}
