<?php

namespace TalkTalk\CorePlugin\Core\Plates\Extension;

use League\Plates\Extension\ExtensionInterface;
use TalkTalk\Core\ApplicationAware;

class App extends ApplicationAware implements ExtensionInterface
{

    public function getFunctions()
    {
        return array(
            'app' => 'getAppObject'
        );
    }

    public function getAppObject()
    {
        return $this->app;
    }

}
