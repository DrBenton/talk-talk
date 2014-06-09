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
        $silexApp = $this->app->getService('silex');
        $request = $this->app->getRequest();

        return $silexApp['url_generator']->generate(
            $request->attributes->get('_route'),
            $request->attributes->get('_route_params')
        );
    }

}
