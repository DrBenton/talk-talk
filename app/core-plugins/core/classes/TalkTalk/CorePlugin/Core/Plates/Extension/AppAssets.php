<?php

namespace TalkTalk\CorePlugin\Core\Plates\Extension;

class AppAssets extends BaseExtension
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

    public function getJavascriptConfigData()
    {
        $jsConfigData = $this->app->get('hooks')->triggerPluginsHook('define_javascript_app_config');
        if (empty($jsConfigData)) {
            return array();
        }

        $jsConfigDataFlattened = call_user_func_array('array_merge', $jsConfigData);

        return $jsConfigDataFlattened;
    }

}
