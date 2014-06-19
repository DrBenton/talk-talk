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
    public function getJsData()
    {
        return $this->app->get('hooks')->getHookFlattenedResult('layout.js.get_data');
    }

    /**
     * @return array
     */
    public function getCss(array $opts = array())
    {
        $cssFiles = $this->app->get('hooks')->getHookFlattenedResult('layout.css.get_head_scripts', $opts);

        $arrayUtils = $this->app->get('utils.array');
        return array_map(
            function ($cssFile) use ($arrayUtils) {
                return $arrayUtils->getArray($cssFile, 'url');
            },
            $cssFiles
        );
    }

    /**
     * @return array
     */
    public function getHeadJs(array $opts = array())
    {
        $jsScripts = $this->app->get('hooks')->getHookFlattenedResult('layout.js.get_head_scripts', $opts);

        $arrayUtils = $this->app->get('utils.array');
        return array_map(
            function ($jsScript) use ($arrayUtils) {
                return $arrayUtils->getArray($jsScript, 'url');
            },
            $jsScripts
        );
    }

    /**
     * @return array
     */
    public function getEndOfBodyJs(array $opts = array())
    {
        $jsScripts = $this->app->get('hooks')->getHookFlattenedResult('layout.js.get_scripts', $opts);

        $arrayUtils = $this->app->get('utils.array');
        return array_map(
            function ($jsScript) use ($arrayUtils) {
                return $arrayUtils->getArray($jsScript, 'url');
            },
            $jsScripts
        );
    }

    /**
     * @return array
     */
    public function getJsModulesToCompile()
    {
        return $this->app->get('hooks')->getHookFlattenedResult('layout.js.get_modules_to_compile');
    }

}
