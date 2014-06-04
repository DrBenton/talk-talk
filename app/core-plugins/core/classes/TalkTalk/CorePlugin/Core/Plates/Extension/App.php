<?php

namespace TalkTalk\CorePlugin\Core\Plates\Extension;

class App extends BaseExtension
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
