<?php

namespace TalkTalk\CorePlugin\Core\Plates\Extension;

use League\Plates\Extension\ExtensionInterface;
use TalkTalk\Core\ApplicationAware;

class AppAssets extends ApplicationAware implements ExtensionInterface
{

    public function getFunctions()
    {
        return array(
            'appAssets' => 'getAssetsObject'
        );
    }

    public function getAssetsObject()
    {
        return $this;
    }

    /**
     * @return array
     */
    public function getCss()
    {
        return $this->app->vars['app.assets.css'];
    }

    /**
     * @return array
     */
    public function getHeadJs()
    {
        return $this->app->vars['app.assets.js.head'];
    }

    /**
     * @return array
     */
    public function getEndOfBodyJs()
    {
        return $this->app->vars['app.assets.js.endOfBody'];
    }

}