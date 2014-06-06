<?php

namespace TalkTalk\CorePlugin\Utils\Plates\Extension;

use TalkTalk\CorePlugin\Core\Plates\Extension\BaseExtension;

class Utils extends BaseExtension
{

    public function getFunctions()
    {
        return array(
            'utils' => 'getUtilsObject'
        );
    }

    public function getUtilsObject()
    {
        return $this;
    }

    public function getCurrentPath()
    {
        return $this->app->get('slim')->request->getPathInfo();
    }

}
