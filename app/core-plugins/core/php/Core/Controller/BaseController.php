<?php

namespace TalkTalk\CorePlugin\Core\Controller;

use TalkTalk\Kernel\ApplicationInterface;

abstract class BaseController
{

    /**
     * @var \TalkTalk\Kernel\ApplicationInterface
     */
    protected static $sharedApp;
    /**
     * @var \TalkTalk\Kernel\ApplicationInterface
     */
    protected $app;
    /**
     * @var bool
     */
    protected $isAjax;

    public static function setApplication(ApplicationInterface $app)
    {
        self::$sharedApp = $app;
    }

    public function __construct()
    {
        $this->app = &self::$sharedApp;
        $this->isAjax = $this->app->vars['isAjax'];
    }

    protected function get($serviceId)
    {
        return $this->app->get($serviceId);
    }

}